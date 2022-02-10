<?php
namespace PublishPress\Capabilities;

/**
 * PublishPress\Capabilities\WP_REST_Workarounds class
 *
 * @author Kevin Behrens
 * @copyright Copyright (c) 2020, PublishPress
 * @link https://publishpress.com/
 *
 */
class WP_REST_Workarounds
{
	private $post_id = 0;
	private $is_posts_request = false;
	private $is_view_method = false;
	private $params = [];
	private $skip_filtering = false;

	public function __construct() {
		add_filter('rest_pre_dispatch', [$this, 'fltRestPreDispatch'], 10, 3);
		add_filter('user_has_cap', [$this, 'fltPublishCapReplacement'], 5, 3);

		add_filter('wp_insert_post_data', [$this, 'fltInsertPostData'], 10, 2);
		add_filter('edit_post_status', [$this, 'fltPostStatus'], 10, 2);
		add_filter('user_has_cap', [$this, 'fltRegulateUnpublish'], 5, 3);

		add_action('admin_print_styles-post.php', [$this, 'actAdminPrintScripts']);
	}
	
    /**
    * Work around Gutenberg editor enforcing publish_posts capability instead of edit_published_posts.
    * 
    * Allow edit_published capability to satisfy publish capability requirement if:
	*   - The query pertains to a specific post
	*	- The post type and its capabilities are defined and match the current publish capability requirement
	*	- The post is already published with a public status, or scheduled
	*
	* Filter hook: 'user_has_cap'
	*
	* @author Kevin Behrens
	* @link   https://core.trac.wordpress.org/ticket/47443
	* @link   https://github.com/WordPress/gutenberg/issues/13342
    * @param  array  $wp_sitecaps  Array of user capabilities acknowledged for this request.
    * @param  array  $reqd_caps    Capability requirements
    * @param  array  $args         Additional arguments passed into user_has_cap filter
    */
	public function fltPublishCapReplacement($wp_sitecaps, $reqd_caps, $args)
	{
		global $pagenow;

		if ($this->skip_filtering || (!in_array($pagenow, ['post.php', 'post-new.php']) && (!defined('REST_REQUEST') || !constant('REST_REQUEST')))) {
			return $wp_sitecaps;
		}

		$reqd_caps = (array) $reqd_caps;

		if ($reqd_cap = reset($reqd_caps)) {
			// slight compromise for perf: apply this workaround only when cap->publish_posts property for post type follows typical pattern (publish_*)
			if (0 === strpos($reqd_cap, 'publish_')) { 
				if (!empty($wp_sitecaps[$reqd_cap])) {
					return $wp_sitecaps;
				}

				if (!$_post = get_post($this->getPostID())) {
					return $wp_sitecaps;
				}
				
				$type_obj = get_post_type_object($_post->post_type);
				$status_obj = get_post_status_object($_post->post_status);

				if ($type_obj && !empty($type_obj->cap) 
				&& !empty($type_obj->cap->publish_posts) && !empty($type_obj->cap->edit_published_posts)
				&& $type_obj->cap->publish_posts == $reqd_cap
				&& $status_obj && (!empty($status_obj->public) || 'future' == $_post->post_status)
				) {
					if (!empty($wp_sitecaps[$type_obj->cap->edit_published_posts])) {
						$wp_sitecaps[$reqd_cap] = true;
					}
				}
			}
		}

		return $wp_sitecaps;
	}

	/**
	* Work around WordPress allowing user who can "edit_published_posts" but not "publish_posts" to unpublish a post.
	*
	* This is hooked to the edit_post_status filter and also called internally from REST update_item capability check (for Gutenberg)
	* and wp_insert_post_data (for Classic Editor and Quick Edit) 
	*
	* Filter hook: 'edit_post_status'
	*
	* @author Kevin Behrens
    * @param  int  $post_status  Post status being set
    * @param  int  $post_id    	 ID of post being modified
    */
	public function fltPostStatus($post_status, $post_id) {
		global $current_user;

		$new_status_obj = get_post_status_object($post_status);
		if (!$new_status_obj || !empty($new_status_obj->internal)) {
			return $post_status;
		}

		if (!$_post = get_post($post_id)) {
			return $post_status;
		}

		$type_obj = get_post_type_object($_post->post_type);
		$status_obj = get_post_status_object($_post->post_status);

		if ($type_obj && $status_obj && (!empty($status_obj->public) || !empty($status_obj->private) || 'future' == $_post->post_status)) {
			// Apply this workaround only if current user has $type_obj->cap->edit_published_posts
			if (isset($type_obj->cap->edit_published_posts) && !empty($current_user->allcaps[$type_obj->cap->edit_published_posts])) {
				$this->skip_filtering = true;

				if (!current_user_can($type_obj->cap->publish_posts)) {
					$post_status = $_post->post_status;
				}

				$this->skip_filtering = false;
			}
		}

		return $post_status;
	}

	/**
	* Regulate post unpublishing on Classic Editor and Quick Edit updates: 
	* If a user can't publish a post, don't let them unpublish it either.
	*
	* Filter hook: 'wp_insert_post_data'
	*
    * @param  array  $data     Parsed array of Post data being set
    * @param  array  $postarr  ARray of current post data
	*/
	public function fltInsertPostData($data, $postarr) {
		if (!empty($data['post_status']) && !empty($postarr['post_ID'])) {
			$data['post_status'] = $this->fltPostStatus($data['post_status'], $postarr['post_ID']);
		}

		return $data;
	}

	/**
	* Regulate post unpublishing on Gutenberg "Switch to Draft"
	* If a user can't publish a post, don't let them unpublish it either.
	*
	* Filter hook: user_has_cap
	*
	* @param  array  $wp_sitecaps  Array of user capabilities acknowledged for this request.
    * @param  array  $reqd_caps    Capability requirements
    * @param  array  $args         Additional arguments passed into user_has_cap filter
	*/
	public function fltRegulateUnpublish($wp_sitecaps, $reqd_caps, $args)
	{
		if (!defined('REST_REQUEST') || !REST_REQUEST || !$this->is_posts_request || !$this->post_id || $this->skip_filtering) {
			return $wp_sitecaps;
		}
		
		if ($reqd_cap = reset($reqd_caps)) {
			// slight compromise for perf: apply this workaround only when cap->edit_published_posts property for post type follows typical pattern (edit_published_*)
			if (0 === strpos($reqd_cap, 'edit_published_')) { 
				if ($this->params && !empty($this->params['status'])) {
					$set_status = $this->fltPostStatus($this->params['status'], $this->post_id);
					if ($set_status != $this->params['status']) {
						unset($wp_sitecaps[$reqd_cap]);
					}
				}
			}
		}

		return $wp_sitecaps;
	}

	/**
	* If we are blocking Gutenberg "Switch to Draft" by capability filtering, also hide the button
	*
	* Action hook: 'admin_print_styles-post.php'
	*/
	public function actAdminPrintScripts() {
		global $current_user, $post;

		if (empty($post) || !did_action('enqueue_block_editor_assets')) {
			return;
		}

		$status_obj = get_post_status_object($post->post_status);

		if (!$status_obj || (empty($status_obj->public) && empty($status_obj->private))) {
			return;
		}

		$type_obj = get_post_type_object($post->post_type);
		$this->skip_filtering = true;

		if ($type_obj && !current_user_can($type_obj->cap->publish_posts) && current_user_can($type_obj->cap->edit_published_posts)): ?>
			<style type="text/css">button.editor-post-switch-to-draft {display:none;}</style>
		<?php endif;

		$this->skip_filtering = false;
	}
	
	/**
	* Log REST query parameters for possible use by subsequent filters
	*
	* Action hook: 'rest_pre_dispatch'
	*/
	public function fltRestPreDispatch($rest_response, $rest_server, $request)
	{
		$method = $request->get_method();
		$path = $request->get_route();
		
		foreach ($rest_server->get_routes() as $route => $handlers) {
			if (!$match = preg_match( '@^' . $route . '$@i', $path, $matches )) {
				continue;
			}

			$args = [];
			foreach ($matches as $param => $value) {
				if (!is_int($param)) {
					$args[ $param ] = $value;
				}
			}

			foreach ($handlers as $handler) {
				if (is_array($handler['callback']) && isset($handler['callback'][0]) && is_object($handler['callback'][0])
				&& 'WP_REST_Posts_Controller' == get_class($handler['callback'][0])
				) {
					if ( ! $this->post_id = (!empty($args['id'])) ? $args['id'] : 0) {
						$this->post_id = (!empty($this->params['id'])) ? $this->params['id'] : 0;
					}

					$this->is_posts_request = true;
					$this->is_view_method = in_array($method, [\WP_REST_Server::READABLE, 'GET']);
               		$this->params = $request->get_params();

					break 2;
				}
			}
		}

		return $rest_response;
	} 

	/**
	* Determine the Post ID, if any, which this query pertains to
	*/
	private function getPostID()
    {
        global $post;

        if (defined('REST_REQUEST') && REST_REQUEST && $this->is_posts_request) {
            return $this->post_id;
        }

        if (!empty($post) && is_object($post)) {
            return ('auto-draft' == $post->post_status) ? 0 : $post->ID;
		} elseif (isset($_REQUEST['post'])) {
            return (int)$_REQUEST['post'];
        } elseif (isset($_REQUEST['post_ID'])) {
            return (int)$_REQUEST['post_ID'];
        } elseif (isset($_REQUEST['post_id'])) {
            return (int)$_REQUEST['post_id'];
        }
	}
}
