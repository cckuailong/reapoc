<?php

if (!defined('UPDRAFTCENTRAL_CLIENT_DIR')) die('No access.');

/**
 * Handles Media Commands
 */
class UpdraftCentral_Media_Commands extends UpdraftCentral_Commands {

	private $switched = false;

	/**
	 * Function that gets called before every action
	 *
	 * @param string $command    a string that corresponds to UDC command to call a certain method for this class.
	 * @param array  $data       an array of data post or get fields
	 * @param array  $extra_info extrainfo use in the udrpc_action, e.g. user_id
	 *
	 * link to udrpc_action main function in class UpdraftCentral_Listener
	 */
	public function _pre_action($command, $data, $extra_info) {// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable -- This function is called from listener.php and $extra_info is being sent.
		// Here we assign the current blog_id to a variable $blog_id
		$blog_id = get_current_blog_id();
		if (!empty($data['site_id'])) $blog_id = $data['site_id'];
	
		if (function_exists('switch_to_blog') && is_multisite() && $blog_id) {
			$this->switched = switch_to_blog($blog_id);
		}
	}
	
	/**
	 * Function that gets called after every action
	 *
	 * @param string $command    a string that corresponds to UDC command to call a certain method for this class.
	 * @param array  $data       an array of data post or get fields
	 * @param array  $extra_info extrainfo use in the udrpc_action, e.g. user_id
	 *
	 * link to udrpc_action main function in class UpdraftCentral_Listener
	 */
	public function _post_action($command, $data, $extra_info) {// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
		// Here, we're restoring to the current (default) blog before we switched
		if ($this->switched) restore_current_blog();
	}

	/**
	 * Fetch and retrieves posts based from the submitted parameters
	 *
	 * @param array $params Containing all the needed information to filter the results of the current request
	 * @return array
	 */
	public function get_media_items($params) {
		$error = $this->_validate_capabilities(array('upload_files', 'edit_posts'));
		if (!empty($error)) return $error;

		// check paged parameter; if empty set to defaults
		$paged = !empty($params['paged']) ? (int) $params['paged'] : 1;
		$numberposts = !empty($params['numberposts']) ? (int) $params['numberposts'] : 10;
		$offset = ($paged - 1) * $numberposts;

		$args = array(
			'posts_per_page' => $numberposts,
			'paged' => $paged,
			'offset' => $offset,
			'post_type' => 'attachment',
			'post_status' => 'inherit',
		);

		if (!empty($params['keyword'])) {
			$args['s'] = $params['keyword'];
		}

		if (!empty($params['category'])) {
			if (in_array($params['category'], array('detached', 'unattached'))) {
				$attachment_ids = $this->get_unattached_ids();
			} else {
				$attachment_ids = $this->get_type_ids($params['category']);
			}

			$args['post__in'] = $attachment_ids;
		}

		if (!empty($params['date'])) {
			list($monthnum, $year) = explode(':', $params['date']);

			$args['monthnum'] = $monthnum;
			$args['year'] = $year;
		}

		$query = new WP_Query($args);
		$result = $query->posts;

		$count_posts = (int) $query->found_posts;
		$page_count = 0;
		
		if ($count_posts > 0) {
			$page_count = absint($count_posts / $numberposts);
			$remainder = absint($count_posts % $numberposts);
			$page_count = ($remainder > 0) ? ++$page_count : $page_count;
		}
		
		$info = array(
			'page' => $paged,
			'pages' => $page_count,
			'results' => $count_posts,
			'items_from' => (($paged * $numberposts) - $numberposts) + 1,
			'items_to' => ($paged == $page_count) ? $count_posts : $paged * $numberposts,
		);

		$media_items = array();
		if (!empty($result)) {
			foreach ($result as $item) {
				$media = $this->get_media_item($item, null, true);
				if (!empty($media)) {
					array_push($media_items, $media);
				}
			}
		}

		$response = array(
			'items' => $media_items,
			'has_image_editor' => $this->has_image_editor(isset($media_items[0]) ? $media_items[0] : null),
			'info' => $info,
			'options' => array(
				'date' => $this->get_date_options(),
				'type' => $this->get_type_options()
			)
		);

		return $this->_response($response);
	}

	/**
	 * Check whether we have an image editor (e.g. GD, Imagick, etc.) set in place to handle the basic editing
	 * functions such as rotate, crop, etc. If not, then we hide that feature in UpdraftCentral
	 *
	 * @param object $media The media item/object to check
	 * @return boolean
	 */
	private function has_image_editor($media) {
		// Most of the time image library are enabled by default in the php.ini but there's a possbility that users don't
		// enable them as they have no need for them at the moment or for some other reasons. Thus, we need to confirm
		// that here through the wp_get_image_editor method.
		$has_image_editor = true;
		if (!empty($media)) {
			if (!function_exists('wp_get_image_editor')) {
				require_once(ABSPATH.'wp-includes/media.php');
			}

			if (!function_exists('_load_image_to_edit_path')) {
				require_once(ABSPATH.'wp-admin/includes/image.php');
			}

			$image_editor = wp_get_image_editor(_load_image_to_edit_path($media->ID));
			if (is_wp_error($image_editor)) {
				$has_image_editor = false;
			}
		}

		return $has_image_editor;
	}

	/**
	 * Fetch a single media item information
	 *
	 * @param array      $params     Containing all the needed information to filter the results of the current request
	 * @param array|null $extra_info Additional information from the current request
	 * @param boolean    $raw        If set, returns the result of the fetch process unwrapped by the response array
	 * @return array
	 */
	public function get_media_item($params, $extra_info = null, $raw = false) {
		$error = $this->_validate_capabilities(array('upload_files', 'edit_posts'));
		if (!empty($error)) return $error;

		// Raw means that we need to return the result without wrapping it
		// with the "$this->_response" function which indicates that the call
		// was done locally (within the class) and not directly from UpdraftCentral.
		if ($raw && is_object($params) && isset($params->ID)) {
			$media = $params;
		} elseif (is_array($params) && !empty($params['id'])) {
			$media = get_post($params['id']);
		}

		if (!function_exists('get_post_mime_types')) {
			global $updraftcentral_main;

			// For a much later version of WP the "get_post_mime_types" is located
			// in a different folder. So, we make sure that we have it loaded before
			// actually using it.
			if (version_compare($updraftcentral_main->get_wordpress_version(), '3.5', '>=')) {
				require_once(ABSPATH.WPINC.'/post.php');
			} else {
				// For WP 3.4, the "get_post_mime_types" is located in the location provided below.
				require_once(ABSPATH.'wp-admin/includes/post.php');
			}
		}

		if (!function_exists('wp_image_editor')) {
			require_once(ABSPATH.'wp-admin/includes/image-edit.php');
		}

		if (!function_exists('get_media_item')) {
			require_once(ABSPATH.'wp-admin/includes/template.php');
			require_once(ABSPATH.'wp-admin/includes/media.php');
		}


		if ($media) {
			$thumb = wp_get_attachment_image_src($media->ID, 'thumbnail', true);
			if (!empty($thumb)) $media->thumb_url = $thumb[0];

			$media->url = wp_get_attachment_url($media->ID);
			$media->parent_post_title = get_the_title($media->post_parent);
			$media->author = get_the_author_meta('display_name', $media->post_author);
			$media->filename = basename($media->url);
			$media->date = date('Y/m/d', strtotime($media->post_date));
			$media->upload_date = mysql2date(get_option('date_format'), $media->post_date);

			$media->filesize = 0;
			$file = get_attached_file($media->ID);
			if (!empty($file) && file_exists($file)) {
				$media->filesize = size_format(filesize($file));
			}
			
			$media->nonce = wp_create_nonce('image_editor-'.$media->ID);
			if (false !== strpos($media->post_mime_type, 'image/')) {
				$meta = wp_get_attachment_metadata($media->ID);

				$thumb = image_get_intermediate_size($media->ID, 'thumbnail');
				$sub_sizes = isset($meta['sizes']) && is_array($meta['sizes']);

				// Pulling details
				$sizer = 1;
				if (isset($meta['width'], $meta['height'])) {
					$big = max($meta['width'], $meta['height']);
					$sizer = $big > 400 ? 400 / $big : 1;
				}

				$constrained_dims = array();
				if ($thumb && $sub_sizes) {
					$constrained_dims = wp_constrain_dimensions($thumb['width'], $thumb['height'], 160, 120);
				}

				$rotate_supported = false;
				if (function_exists('imagerotate') || wp_image_editor_supports(array('mime_type' => get_post_mime_type($media->ID), 'methods' => array('rotate')))) {
					$rotate_supported = true;
				}

				// Check for alternative text if present
				$alt = get_post_meta($media->ID, '_wp_attachment_image_alt', true);
				$media->alt = !empty($alt) ? $alt : '';

				// Check whether edited images are restorable
				$backup_sizes = get_post_meta($media->ID, '_wp_attachment_backup_sizes', true);
				$can_restore = !empty($backup_sizes) && isset($backup_sizes['full-orig']) && basename($meta['file']) != $backup_sizes['full-orig']['file'];

				$image_edit_overwrite = (!defined('IMAGE_EDIT_OVERWRITE') || !IMAGE_EDIT_OVERWRITE) ? 0 : 1;
				$media->misc = array(
					'sizer' => $sizer,
					'rand' => rand(1, 99999),
					'constrained_dims' => $constrained_dims,
					'rotate_supported' => (int) $rotate_supported,
					'thumb' => $thumb,
					'meta' => $meta,
					'alt_text' => $alt,
					'can_restore' => $can_restore,
					'image_edit_overwrite' => $image_edit_overwrite
				);
			}
		}

		return $raw ? $media : $this->_response(array('item' => $media));
	}

	/**
	 * Fetch and retrieves posts based from the submitted parameters
	 *
	 * @param array $params Containing all the needed information to filter the results of the current request
	 * @return array
	 */
	public function get_posts($params) {
		$error = $this->_validate_capabilities(array('edit_posts'));
		if (!empty($error)) return $error;

		// check paged parameter; if empty set to defaults
		$paged = !empty($params['paged']) ? (int) $params['paged'] : 1;
		$numberposts = !empty($params['numberposts']) ? (int) $params['numberposts'] : 10;
		$offset = ($paged - 1) * $numberposts;

		$args = array(
			'posts_per_page' => $numberposts,
			'paged' => $paged,
			'offset' => $offset,
			'post_type' => 'post',
			'post_status' => 'publish,private,draft,pending,future',
		);

		if (!empty($params['keyword'])) {
			$args['s'] = $params['keyword'];
		}

		$query = new WP_Query($args);
		$result = $query->posts;

		$count_posts = (int) $query->found_posts;
		$page_count = 0;
		
		if ($count_posts > 0) {
			$page_count = absint($count_posts / $numberposts);
			$remainder = absint($count_posts % $numberposts);
			$page_count = ($remainder > 0) ? ++$page_count : $page_count;
		}
		
		$info = array(
			'page' => $paged,
			'pages' => $page_count,
			'results' => $count_posts,
			'items_from' => (($paged * $numberposts) - $numberposts) + 1,
			'items_to' => ($paged == $page_count) ? $count_posts : $paged * $numberposts,
		);

		$posts = array();
		if (!empty($result)) {
			foreach ($result as $post) {
				array_push($posts, array('ID' => $post->ID, 'title' => $post->post_title));
			}
		}

		$response = array(
			'posts' => $posts,
			'info' => $info
		);
		return $this->_response($response);
	}

	/**
	 * Saves media changes from UpdraftCentral
	 *
	 * @param array $params Containing all the needed information to filter the results of the current request
	 * @return array
	 */
	public function save_media_item($params) {
		$error = $this->_validate_capabilities(array('upload_files', 'edit_posts'));
		if (!empty($error)) return $error;

		$args = array(
			'post_title' => $params['image_title'],
			'post_excerpt' => $params['image_caption'],
			'post_content' => $params['image_description']
		);

		if (!empty($params['new'])) {
			$args['post_type'] = 'attachment';
			$media_id = wp_insert_post($args, true);
		} else {
			$args['ID'] = $params['id'];
			$args['post_modified'] = date('Y-m-d H:i:s');
			$args['post_modified_gmt'] = gmdate('Y-m-d H:i:s');

			$media_id = wp_update_post($args, true);
		}

		if (!empty($media_id)) {
			// Update alternative text if not empty
			if (!empty($params['image_alternative_text'])) {
				update_post_meta($media_id, '_wp_attachment_image_alt', $params['image_alternative_text']);
			}

			$result = array(
				'status' => 'success',
				'item' => $this->get_media_item(array('id' => $media_id), null, true)
			);
		} else {
			$result = array('status' => 'failed');
		}

		return $this->_response($result);
	}

	/**
	 * Executes media action (e.g. attach, detach and delete)
	 *
	 * @param array $params Containing all the needed information to filter the results of the current request
	 * @return array
	 */
	public function execute_media_action($params) {
		global $updraftcentral_host_plugin;

		$error = $this->_validate_capabilities(array('upload_files', 'edit_posts'));
		if (!empty($error)) return $error;

		$result = array();
		switch ($params['do']) {
			case 'attach':
				global $wpdb;
				$query_result = $wpdb->query($wpdb->prepare("UPDATE {$wpdb->posts} SET `post_parent` = %d WHERE `post_type` = 'attachment' AND ID = %d", $params['parent_id'], $params['id']));

				if (false === $query_result) {
					$result['error'] = $updraftcentral_host_plugin->retrieve_show_message('failed_to_attach_media');
				} else {
					$result['msg'] = $updraftcentral_host_plugin->retrieve_show_message('media_attached');
				}
				break;
			case 'detach':
				global $wpdb;
				$query_result = $wpdb->query($wpdb->prepare("UPDATE {$wpdb->posts} SET `post_parent` = 0 WHERE `post_type` = 'attachment' AND ID = %d", $params['id']));

				if (false === $query_result) {
					$result['error'] = $updraftcentral_host_plugin->retrieve_show_message('failed_to_detach_media');
				} else {
					$result['msg'] = $updraftcentral_host_plugin->retrieve_show_message('media_detached');
				}
				break;
			case 'delete':
				$failed_items = array();
				foreach ($params['ids'] as $id) {
					// Delete permanently
					if (false === wp_delete_attachment($id, true)) {
						$failed_items[] = $id;
					}
				}

				if (!empty($failed_items)) {
					$result['error'] = $updraftcentral_host_plugin->retrieve_show_message('failed_to_delete_media');
					$result['items'] = $failed_items;
				} else {
					$result['msg'] = $updraftcentral_host_plugin->retrieve_show_message('selected_media_deleted');
				}
				break;
			default:
				break;
		}

		return $this->_response($result);
	}

	/**
	 * Retrieves a collection of formatted dates found for the given post statuses.
	 * It will be used as options for the date filter when managing the media items in UpdraftCentral.
	 *
	 * @return array
	 */
	private function get_date_options() {
		global $wpdb;
		$options = array();

		$date_options = $wpdb->get_col("SELECT DATE_FORMAT(`post_date`, '%M %Y') as `formatted_post_date` FROM {$wpdb->posts} WHERE `post_type` = 'attachment' AND `post_status` = 'inherit' GROUP BY `formatted_post_date` ORDER BY `post_date` DESC");

		if (!empty($date_options)) {
			foreach ($date_options as $monthyear) {
				$timestr = strtotime($monthyear);
				$options[] = array('label' => date('F Y', $timestr), 'value' => date('n:Y', $timestr));
			}
		}

		return $options;
	}

	/**
	 * Retrieves mime types that will be use as filter option in UpdraftCentral
	 *
	 * @return array
	 */
	private function get_type_options() {
		global $wpdb, $updraftcentral_host_plugin, $updraftcentral_main;

		$options = array();
		if (!function_exists('get_post_mime_types')) {
			// For a much later version of WP the "get_post_mime_types" is located
			// in a different folder. So, we make sure that we have it loaded before
			// actually using it.
			if (version_compare($updraftcentral_main->get_wordpress_version(), '3.5', '>=')) {
				require_once(ABSPATH.WPINC.'/post.php');
			} else {
				// For WP 3.4, the "get_post_mime_types" is located in the location provided below.
				require_once(ABSPATH.'wp-admin/includes/post.php');
			}
		}

		$post_mime_types = get_post_mime_types();
		$type_options = $wpdb->get_col("SELECT `post_mime_type` FROM {$wpdb->posts} WHERE `post_type` = 'attachment' AND `post_status` = 'inherit' GROUP BY `post_mime_type` ORDER BY `post_mime_type` DESC");

		foreach ($post_mime_types as $mime_type => $label) {
			if (!wp_match_mime_types($mime_type, $type_options)) continue;
			$options[] = array('label' => $label[0], 'value' => esc_attr($mime_type));
		}

		$options[] = array('label' => $updraftcentral_host_plugin->retrieve_show_message('unattached'), 'value' => 'detached');
		return $options;
	}

	/**
	 * Retrieves media items that haven't been attached to any posts
	 *
	 * @return array
	 */
	private function get_unattached_ids() {
		global $wpdb;
		return $wpdb->get_col("SELECT `ID` FROM {$wpdb->posts} WHERE `post_type` = 'attachment' AND `post_status` = 'inherit' AND `post_parent` = '0'");
	}

	/**
	 * Retrieves IDs of media items that has the given mime type
	 *
	 * @param string $type The mime type to search for
	 * @return array
	 */
	private function get_type_ids($type) {
		global $wpdb;
		return $wpdb->get_col($wpdb->prepare("SELECT `ID` FROM {$wpdb->posts} WHERE `post_type` = 'attachment' AND `post_status` = 'inherit' AND `post_mime_type` LIKE '%s/%%'", $type));
	}

	/**
	 * Checks whether we have the required fields submitted and the user has
	 * the capabilities to execute the requested action
	 *
	 * @param array $capabilities The capabilities to check and validate
	 *
	 * @return array|void
	 */
	private function _validate_capabilities($capabilities) {
		foreach ($capabilities as $capability) {
			if (!current_user_can($capability)) {
				return $this->_generic_error_response('insufficient_permission');
			}
		}
	}

	/**
	 * Populates the $_REQUEST global variable with the submitted data
	 *
	 * @param array $params Submitted data received from UpdraftCentral
	 * @return array
	 */
	private function populate_request($params) {
		if (!empty($params)) {
			foreach ($params as $key => $value) {
				$_REQUEST[$key] = $value;
			}
		}
	}

	/**
	 * Handles image editing requests coming from UpdraftCentral
	 *
	 * @param array $params Containing all the needed information to filter the results of the current request
	 * @return array
	 */
	public function image_editor($params) {
		$error = $this->_validate_capabilities(array('edit_posts'));
		if (!empty($error)) return $error;

		$attachment_id = (int) $params['postid'];
		$this->populate_request($params);

		if (!function_exists('load_image_to_edit')) {
			require_once(ABSPATH.'wp-admin/includes/image.php');
		}

		include_once(ABSPATH.'wp-admin/includes/image-edit.php');
		$msg = false;
		switch ($params['do']) {
			case 'save':
			case 'scale':
				$msg = wp_save_image($attachment_id);
				break;
			case 'restore':
				$msg = wp_restore_image($attachment_id);
				break;
		}

		$msg = (false !== $msg) ? json_encode($msg) : $msg;
		return $this->_response(array('content' => $msg));
	}

	/**
	 * Handles image preview requests coming from UpdraftCentral
	 *
	 * @param array $params Containing all the needed information to filter the results of the current request
	 * @return array
	 */
	public function image_preview($params) {
		$error = $this->_validate_capabilities(array('edit_posts'));
		if (!empty($error)) return $error;

		if (!function_exists('load_image_to_edit')) {
			require_once(ABSPATH.'wp-admin/includes/image.php');
		}

		include_once(ABSPATH.'wp-admin/includes/image-edit.php');
		$this->populate_request($params);
		$post_id = (int) $params['postid'];

		ob_start();
		stream_preview_image($post_id);
		$content = ob_get_contents();
		ob_end_clean();

		return $this->_response(array('content' => base64_encode($content)));
	}
}
