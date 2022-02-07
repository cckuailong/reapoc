<?php

if (!defined('UPDRAFTCENTRAL_CLIENT_DIR')) die('No access.');

class UpdraftCentral_Comments_Commands extends UpdraftCentral_Commands {

	/**
	 * The _search_comments function searches all available comments based
	 * on the following query parameters (type, status, search)
	 *
	 * Search Parameters/Filters:
	 * type - comment types can be 'comment', 'trackback' and 'pingback', defaults to 'comment'
	 * status - comment status can be 'hold' or unapprove, 'approve', 'spam', 'trash'
	 * search - user generated content or keyword
	 *
	 * @param  array $query The query to search comments
	 * @return array
	 */
	private function _search_comments($query) {
		
		// Basic parameters to the query and should display
		// the results in descending order (latest comments) first
		// based on their generated IDs
		
		$args = array(
			'orderby' => 'ID',
			'order' => 'DESC',
			'type' => $query['type'],
			'status' => $query['status'],
			'search' => esc_attr($query['search']),
		);
		
		$query = new WP_Comment_Query;
		$found_comments = $query->query($args);

		$comments = array();
		foreach ($found_comments as $comment) {
			
			// We're returning a collection of comment in an array,
			// in sync with the originator of the request on the ui side
			// so, we're pulling it one by one into the array before
			// returning it.
			
			if (!in_array($comment, $comments)) {
				array_push($comments, $comment);
			}
		}
		
		return $comments;
	}

	/**
	 * The _calculate_pages function generates and builds the pagination links
	 * based on the current search parameters/filters. Please see _search_comments
	 * for the breakdown of these parameters.
	 *
	 * @param  array $query Query to generate pagination links
	 * @return array
	 */
	private function _calculate_pages($query) {
		$per_page_options = array(10, 20, 30, 40, 50);

		if (!empty($query)) {
			if (!empty($query['search'])) {
				return array(
					'page_count' => 1,
					'page_no' => 1
				);
			}
			
			$pages = array();
			$page_query = new WP_Comment_Query;
			
			// Here, we're pulling the comments based on the
			// two parameters namely type and status.
			//
			// The number of results/comments found will then
			// be use to compute for the number of pages to be
			// displayed as navigation links when browsing all
			// comments from the frontend.
			
			$comments = $page_query->query(array(
				'type' => $query['type'],
				'status' => $query['status']
			));
			
			$total_comments = count($comments);
			$page_count = ceil($total_comments / $query['per_page']);
			
			if ($page_count > 1) {
				for ($i = 0; $i < $page_count; $i++) {
					if ($i + 1 == $query['page_no']) {
						$paginator_item = array(
							'value' => $i+1,
							'setting' => 'disabled'
						);
					} else {
						$paginator_item = array(
							'value' => $i+1
						);
					}
					array_push($pages, $paginator_item);
				}

				if ($query['page_no'] >= $page_count) {
					$page_next = array(
						'value' => $page_count,
						'setting' => 'disabled'
					);
				} else {
					$page_next = array(
						'value' => $query['page_no'] + 1
					);
				}
				
				if (1 === $query['page_no']) {
					$page_prev = array(
						'value' => 1,
						'setting' => 'disabled'
					);
				} else {
					$page_prev = array(
						'value' => $query['page_no'] - 1
					);
				}

				return array(
					'page_no' => $query['page_no'],
					'per_page' => $query['per_page'],
					'page_count' => $page_count,
					'pages' => $pages,
					'page_next' => $page_next,
					'page_prev' => $page_prev,
					'total_results' => $total_comments,
					'per_page_options' => $per_page_options
				);

			} else {
				return array(
					'page_no' => $query['page_no'],
					'per_page' => $query['per_page'],
					'page_count' => $page_count,
					'total_results' => $total_comments,
					'per_page_options' => $per_page_options
				);
			}
		} else {
			return array(
				'per_page_options' => $per_page_options
			);
		}
	}
	
	/**
	 * The get_blog_sites function pulls blog sites available for the current WP instance.
	 * If Multisite is enabled on the server, then sites under the network will be pulled, otherwise, it will return an empty array.
	 *
	 * @return array
	 */
	private function get_blog_sites() {
		
		if (!is_multisite()) return array();
		
		// Initialize array container
		$sites = $network_sites = array();
		
		// Check to see if latest get_sites (available on WP version >= 4.6) function is
		// available to pull any available sites from the current WP instance. If not, then
		// we're going to use the fallback function wp_get_sites (for older version).
		
		if (function_exists('get_sites') && class_exists('WP_Site_Query')) {
			$network_sites = get_sites();
		} else {
			if (function_exists('wp_get_sites')) {
				$network_sites = wp_get_sites();
			}
		}
		
		// We only process if sites array is not empty, otherwise, bypass
		// the next block.
		
		if (!empty($network_sites)) {
			foreach ($network_sites as $site) {
				
				// Here we're checking if the site type is an array, because
				// we're pulling the blog_id property based on the type of
				// site returned.
				// get_sites returns an array of object, whereas the wp_get_sites
				// function returns an array of array.
				
				$blog_id = (is_array($site)) ? $site['blog_id'] : $site->blog_id;
				
				
				// We're saving the blog_id and blog name as an associative item
				// into the sites array, that will be used as "Sites" option in
				// the frontend.
				
				$sites[$blog_id] = get_blog_details($blog_id)->blogname;
			}
		}
		
		return $sites;
	}
	
	/**
	 * The get_wp_option function pulls current blog options
	 * from the database using either following functions:
	 * - get_blog_option (for multisite)
	 * - get_option (for ordinary blog)
	 *
	 * @param  array $blog_id This is the specific blog ID
	 * @param  array $setting specifies settings
	 * @return array
	 */
	private function _get_wp_option($blog_id, $setting) {
		return is_multisite() ? get_blog_option($blog_id, $setting) : get_option($setting);
	}
	
	/**
	 * The get_comments function pull all the comments from the database
	 * based on the current search parameters/filters. Please see _search_comments
	 * for the breakdown of these parameters.
	 *
	 * @param  array $query Specific query to pull comments
	 * @return array
	 */
	public function get_comments($query) {
		
		// Here, we're getting the current blog id. If blog id
		// is passed along with the parameters then we override
		// that current (default) value with the parameter blog id value.
		
		$blog_id = get_current_blog_id();
		if (isset($query['blog_id'])) $blog_id = $query['blog_id'];
		
		
		// Here, we're switching to the actual blog that we need
		// to pull comments from.
		
		$switched = false;
		if (function_exists('switch_to_blog')) {
			$switched = switch_to_blog($blog_id);
		}
		
		if (!empty($query['search'])) {
			// If a search keyword is present, then we'll call the _search_comments
			// function to process the query.
			
			$comments = $this->_search_comments($query);
		} else {
			// Set default parameter values if the designated
			// parameters are empty.
			
			if (empty($query['per_page'])) {
				$query['per_page'] = 10;
			}
			if (empty($query['page_no'])) {
				$query['page_no'] = 1;
			}
			if (empty($query['type'])) {
				$query['type'] = '';
			}
			if (empty($query['status'])) {
				$query['status'] = '';
			}
			
			// Since WP_Comment_Query parameters doesn't have a "page" attribute, we
			// need to compute for the offset to get the exact content based on the
			// current page and the number of items per page.
			
			$offset = ((int) $query['page_no'] - 1) * (int) $query['per_page'];
			$args = array(
				'orderby' => 'ID',
				'order' => 'DESC',
				'number' => $query['per_page'],
				'offset' => $offset,
				'type' => $query['type'],
				'status' => $query['status']
			);

			$comments_query = new WP_Comment_Query;
			$comments = $comments_query->query($args);
		}

		// If no comments are found based on the current query then
		// we return with error.
		
		if (empty($comments)) {
			$result = array('message' => 'comments_not_found');
			return $this->_response($result);
		}
		
		// Otherwise, we're going to process each comment
		// before we return it to the one issuing the request.
		//
		// Process in the sense that we add additional related info
		// such as the post tile where the comment belongs to, the
		// comment status, a formatted date field, and to which parent comment
		// does the comment was intended to be as a reply.
		
		foreach ($comments as &$comment) {
			$comment = get_comment($comment->comment_ID, ARRAY_A);
			if ($comment) {
				$post = get_post($comment['comment_post_ID']);
				
				if ($post) $comment['in_response_to'] = $post->post_title;
				if (!empty($comment['comment_parent'])) {
					$parent_comment = get_comment($comment['comment_parent'], ARRAY_A);
					if ($parent_comment) $comment['in_reply_to'] = $parent_comment['comment_author'];
				}
				
				// We're formatting the comment_date to be exactly the same
				// with that of WP Comments table (e.g. 2016/12/21 at 10:30 PM)
				
				$comment['comment_date'] = date('Y/m/d \a\t g:i a', strtotime($comment['comment_date']));
				
				$status = wp_get_comment_status($comment['comment_ID']);
				if ($status) {
					$comment['comment_status'] = $status;
				}
			}
		}
		
		// We return the following to the one issuing
		// the request.
		
		$result = array(
			'comments' => $comments,
			'paging' => $this->_calculate_pages($query)
		);
		
		
		// Here, we're restoring to the current (default) blog before we
		// do the switched.
		
		if (function_exists('restore_current_blog') && $switched) {
			restore_current_blog();
		}
		
		return $this->_response($result);
	}
	
	/**
	 * The get_comment_filters function builds a array of options
	 * to be use as filters for the search function on the frontend.
	 */
	public function get_comment_filters() {
		// Options for comment_types field
		$comment_types = apply_filters('admin_comment_types_dropdown', array(
			'comment' => __('Comments'),
			'pings' => __('Pings'),
		));
				
		// Options for comment_status field
		$comment_statuses = array(
			'approve' => __('Approve'),
			'hold' => __('Hold or Unapprove'),
			'trash' => __('Trash'),
			'spam' => __('Spam'),
		);
		
		// Pull sites options if available.
		$sites = $this->get_blog_sites();

		$result = array(
			'sites' => $sites,
			'types' => $comment_types,
			'statuses' => $comment_statuses,
			'paging' => $this->_calculate_pages(null),
		);
		
		return $this->_response($result);
	}
	
	/**
	 * The get_settings function pulls the current discussion settings
	 * option values.
	 *
	 * @param  array $params Passing specific params for getting current discussion settings
	 * @return array
	 */
	public function get_settings($params) {
		global $updraftcentral_main;
		
		// Here, we're getting the current blog id. If blog id
		// is passed along with the parameters then we override
		// that current (default) value with the parameter blog id value.
		
		$blog_id = get_current_blog_id();
		if (isset($params['blog_id'])) $blog_id = $params['blog_id'];
		
		
		// If user does not have sufficient privileges to manage and edit
		// WP options then we return with error.
		
		if (!current_user_can_for_blog($blog_id, 'manage_options')) {
			$result = array('error' => true, 'message' => 'insufficient_permission');
			return $this->_response($result);
		}
		
		// Pull sites options if available.
		$sites = $this->get_blog_sites();
		
		// Wrap current discussion settings values into an array item
		// named settings.
		
		$result = array(
			'settings' => array(
				'default_pingback_flag' => $this->_get_wp_option($blog_id, 'default_pingback_flag'),
				'default_ping_status' => $this->_get_wp_option($blog_id, 'default_ping_status'),
				'default_comment_status' => $this->_get_wp_option($blog_id, 'default_comment_status'),
				'require_name_email' => $this->_get_wp_option($blog_id, 'require_name_email'),
				'comment_registration' => $this->_get_wp_option($blog_id, 'comment_registration'),
				'close_comments_for_old_posts' => $this->_get_wp_option($blog_id, 'close_comments_for_old_posts'),
				'close_comments_days_old' => $this->_get_wp_option($blog_id, 'close_comments_days_old'),
				'thread_comments' => $this->_get_wp_option($blog_id, 'thread_comments'),
				'thread_comments_depth' => $this->_get_wp_option($blog_id, 'thread_comments_depth'),
				'page_comments' => $this->_get_wp_option($blog_id, 'page_comments'),
				'comments_per_page' => $this->_get_wp_option($blog_id, 'comments_per_page'),
				'default_comments_page' => $this->_get_wp_option($blog_id, 'default_comments_page'),
				'comment_order' => $this->_get_wp_option($blog_id, 'comment_order'),
				'comments_notify' => $this->_get_wp_option($blog_id, 'comments_notify'),
				'moderation_notify' => $this->_get_wp_option($blog_id, 'moderation_notify'),
				'comment_moderation' => $this->_get_wp_option($blog_id, 'comment_moderation'),
				'comment_max_links' => $this->_get_wp_option($blog_id, 'comment_max_links'),
				'moderation_keys' => $this->_get_wp_option($blog_id, 'moderation_keys'),
			),
			'sites' => $sites,
		);
		
		$wp_version = $updraftcentral_main->get_wordpress_version();
		if (version_compare($wp_version, '5.5.0', '<')) {
			$result['settings']['comment_whitelist'] = $this->_get_wp_option($blog_id, 'comment_whitelist');
			$result['settings']['blacklist_keys'] = $this->_get_wp_option($blog_id, 'blacklist_keys');
		} else {
			$result['settings']['comment_previously_approved'] = $this->_get_wp_option($blog_id, 'comment_previously_approved');
			$result['settings']['disallowed_keys'] = $this->_get_wp_option($blog_id, 'disallowed_keys');
		}

		return $this->_response($result);
	}
	
	/**
	 * The update_settings function updates the discussion settings
	 * basing on the user generated content/option from the frontend
	 * form.
	 *
	 * @param  array $params Specific params to update settings based on discussion
	 * @return array
	 */
	public function update_settings($params) {
		
		// Extract settings values from passed parameters.
		$settings = $params['settings'];
		
		// Here, we're getting the current blog id. If blog id
		// is passed along with the parameters then we override
		// that current (default) value with the parameter blog id value.
		
		$blog_id = get_current_blog_id();
		if (isset($params['blog_id'])) $blog_id = $params['blog_id'];
		
		
		// If user does not have sufficient privileges to manage and edit
		// WP options then we return with error.
		
		if (!current_user_can_for_blog($blog_id, 'manage_options')) {
			$result = array('error' => true, 'message' => 'insufficient_permission');
			return $this->_response($result);
		}

		// Here, we're sanitizing the input fields before we save them to the database
		// for safety and security reason. The "explode" and "implode" functions are meant
		// to maintain the line breaks associated with a textarea input/value.
		
		foreach ($settings as $key => $value) {
			
			// We're using update_blog_option and update_option altogether to update the current
			// discussion settings.
			
			if (is_multisite()) {
				update_blog_option($blog_id, $key, implode("\n", array_map('sanitize_text_field', explode("\n", $value))));
			} else {
				update_option($key, implode("\n", array_map('sanitize_text_field', explode("\n", $value))));
			}
		}
		
		// We're not checking for errors here, but instead we're directly returning a success (error = false)
		// status always, because WP's update_option will return fail if values were not changed, meaning
		// previous values were not changed by the user's current request, not an actual exception thrown.
		// Thus, giving a false positive message or report to the frontend.
		
		$result = array('error' => false, 'message' => 'settings_updated', 'values' => array());
		return $this->_response($result);
	}
	
	/**
	 * The get_comment function pulls a single comment based
	 * on a comment ID.
	 *
	 * @param  array $params Specific params for getting a single comment
	 * @return array
	 */
	public function get_comment($params) {
		
		// Here, we're getting the current blog id. If blog id
		// is passed along with the parameters then we override
		// that current (default) value with the parameter blog id value.
		
		$blog_id = get_current_blog_id();
		if (isset($params['blog_id'])) $blog_id = $params['blog_id'];
		
		
		// If user does not have sufficient privileges to moderate or edit
		// a comment then we return with error.
		
		if (!current_user_can_for_blog($blog_id, 'moderate_comments')) {
			$result = array('error' => true, 'message' => 'insufficient_permission');
			return $this->_response($result);
		}
		
		// Here, we're switching to the actual blog that we need
		// to pull comments from.

		$switched = false;
		if (function_exists('switch_to_blog')) {
			$switched = switch_to_blog($blog_id);
		}
		
		// Get comment by comment_ID parameter and return result as an array.
		$result = array(
			'comment' => get_comment($params['comment_id'], ARRAY_A)
		);
		
		
		// Here, we're restoring to the current (default) blog before we
		// do the switched.
		
		if (function_exists('restore_current_blog') && $switched) {
			restore_current_blog();
		}
		
		return $this->_response($result);
	}
	
	/**
	 * The reply_comment function creates a new comment as a reply
	 * to a certain/selected comment.
	 *
	 * @param  array $params Specific params to create a new comment reply
	 * @return array
	 */
	public function reply_comment($params) {
		
		// Extract reply info from the passed parameters
		$reply = $params['comment'];
		
		// Here, we're getting the current blog id. If blog id
		// is passed along with the parameters then we override
		// that current (default) value with the parameter blog id value.

		$blog_id = get_current_blog_id();
		if (isset($params['blog_id'])) $blog_id = $params['blog_id'];
		
		
		// If user does not have sufficient privileges to moderate or edit
		// a comment then we return with error.
		
		if (!current_user_can_for_blog($blog_id, 'moderate_comments')) {
			$result = array('error' => true, 'message' => 'comment_reply_no_permission');
			return $this->_response($result);
		}
		
		// Here, we're switching to the actual blog that we need
		// to apply our changes.

		$switched = false;
		if (function_exists('switch_to_blog')) {
			$switched = switch_to_blog($blog_id);
		}
		
		
		// Get comment by comment_ID parameter.
		$comment = get_comment($reply['comment_id']);
		if ($comment) {
			
			// Get the currently logged in user
			$user = wp_get_current_user();
			
			// If the current comment was not approved yet then
			// we need to approve it before we create a reply to
			// to the comment, mimicking exactly the WP behaviour
			// in terms of creating a reply to a comment.
			
			if (empty($comment->comment_approved)) {
				$update_data = array(
					'comment_ID' => $reply['comment_id'],
					'comment_approved' => 1
				);
				wp_update_comment($update_data);
			}
			
			// Build new comment parameters based on current user info and
			// the target comment for the reply.
			$data = array(
				'comment_post_ID' => $comment->comment_post_ID,
				'comment_author' => $user->display_name,
				'comment_author_email' => $user->user_email,
				'comment_author_url' => $user->user_url,
				'comment_content' => $reply['message'],
				'comment_parent' => $reply['comment_id'],
				'user_id' => $user->ID,
				'comment_date' => current_time('mysql'),
				'comment_approved' => 1
			);
			
			// Create new comment based on the parameters above, and return
			// the status accordingly.
			
			if (wp_insert_comment($data)) {
				$result = array('error' => false, 'message' => 'comment_replied_with_comment_author', 'values' => array($comment->comment_author));
			} else {
				$result = array('error' => true, 'message' => 'comment_reply_failed_with_error', 'values' => array($comment->comment_ID));
			}
		} else {
			$result = array('error' => true, 'message' => 'comment_does_not_exists_error', 'values' => array($reply['comment_id']));
		}
		
		
		// Here, we're restoring to the current (default) blog before we
		// do the switched.

		if (function_exists('restore_current_blog') && $switched) {
			restore_current_blog();
		}
		
		return $this->_response($result);
	}
	
	/**
	 * The edit_comment function saves new information for the
	 * currently selected comment.
	 *
	 * @param  array $params Specific params for editing a coment
	 * @return array
	 */
	public function edit_comment($params) {
		
		// Extract new comment info from the passed parameters
		$comment = $params['comment'];
		
		// Here, we're getting the current blog id. If blog id
		// is passed along with the parameters then we override
		// that current (default) value with the parameter blog id value.

		$blog_id = get_current_blog_id();
		if (isset($params['blog_id'])) $blog_id = $params['blog_id'];

		
		// If user does not have sufficient privileges to moderate or edit
		// a comment then we return with error.
		
		if (!current_user_can_for_blog($blog_id, 'moderate_comments')) {
			$result = array('error' => true, 'message' => 'comment_edit_no_permission');
			return $this->_response($result);
		}
		
		// Here, we're switching to the actual blog that we need
		// to apply our changes.

		$switched = false;
		if (function_exists('switch_to_blog')) {
			$switched = switch_to_blog($blog_id);
		}
		
		
		// Get current comment details
		$original_comment = get_comment($comment['comment_id']);
		if ($original_comment) {
			$data = array();
			
			// Replace "comment_id" with "comment_ID" since WP does not recognize
			// the small case "id".
			$comment['comment_ID'] = $original_comment->comment_ID;
			unset($comment['comment_id']);
			
			// Here, we're sanitizing the input fields before we save them to the database
			// for safety and security reason. The "explode" and "implode" functions are meant
			// to maintain the line breaks associated with a textarea input/value.
		
			foreach ($comment as $key => $value) {
				$data[$key] = implode("\n", array_map('sanitize_text_field', explode("\n", $value)));
			}
			
			// Update existing comment based on the passed parameter fields and
			// return the status accordingly.
			
			if (wp_update_comment($data)) {
				$result = array('error' => false, 'message' => 'comment_edited_with_comment_author', 'values' => array($original_comment->comment_author));
			} else {
				$result = array('error' => true, 'message' => 'comment_edit_failed_with_error', 'values' => array($original_comment->comment_ID));
			}
		} else {
			$result = array('error' => true, 'message' => 'comment_does_not_exists_error', 'values' => array($comment['comment_id']));
		}
		
		// Here, we're restoring to the current (default) blog before we
		// do the switched.

		if (function_exists('restore_current_blog') && $switched) {
			restore_current_blog();
		}

		return $this->_response($result);
	}
	
	/**
	 * The update_comment_status function is a generic handler for the following
	 * comment actions:
	 *
	 * - approve comment
	 * - unapprove comment
	 * - set comment as spam
	 * - move commment to trash
	 * - delete comment permanently
	 * - unset comment as spam
	 * - restore comment
	 *
	 * @param  array $params Specific params to update comment status
	 * @return array
	 */
	public function update_comment_status($params) {
		
		// Here, we're getting the current blog id. If blog id
		// is passed along with the parameters then we override
		// that current (default) value with the parameter blog id value.

		$blog_id = get_current_blog_id();
		if (isset($params['blog_id'])) $blog_id = $params['blog_id'];
		
		
		// If user does not have sufficient privileges to moderate or edit
		// a comment then we return with error.
		
		if (!current_user_can_for_blog($blog_id, 'moderate_comments')) {
			$result = array('error' => true, 'message' => 'comment_change_status_no_permission');
			return $this->_response($result);
		}
		
		// Here, we're switching to the actual blog that we need
		// to apply our changes.

		$switched = false;
		if (function_exists('switch_to_blog')) {
			$switched = switch_to_blog($blog_id);
		}

		
		// We make sure that we still have a valid comment from the server
		// before we apply the currently selected action.
		
		$comment = get_comment($params['comment_id']);
		if ($comment) {
			$post = get_post($comment->comment_post_ID);

			if ($post) $comment->in_response_to = $post->post_title;
			if (!empty($comment->comment_parent)) {
				$parent_comment = get_comment($comment->comment_parent);
				if ($parent_comment) $comment->in_reply_to = $parent_comment->comment_author;
			}

			// We're formatting the comment_date to be exactly the same
			// with that of WP Comments table (e.g. 2016/12/21 at 10:30 PM)

			$comment->comment_date = date('Y/m/d \a\t g:i a', strtotime($comment->comment_date));

			$status = wp_get_comment_status($comment->comment_ID);
			if ($status) {
				$comment->comment_status = $status;
			}

			$succeeded = false;
			$message = '';
			
			// Here, we're using WP's wp_set_comment_status function to change the state
			// of the selected comment based on the current action, except for the "delete" action
			// where we use the wp_delete_comment to delete the comment permanently by passing
			// "true" to the second argument.
			
			switch ($params['action']) {
				case 'approve':
				$succeeded = wp_set_comment_status($params['comment_id'], 'approve');
				$message = 'comment_approve_with_comment_author';
					break;
				case 'unapprove':
				$succeeded = wp_set_comment_status($params['comment_id'], 'hold');
				$message = 'comment_unapprove_with_comment_author';
					break;
				case 'spam':
				$succeeded = wp_set_comment_status($params['comment_id'], 'spam');
				$message = 'comment_spam_with_comment_author';
					break;
				case 'trash':
				$succeeded = wp_set_comment_status($params['comment_id'], 'trash');
				$message = 'comment_trash_with_comment_author';
					break;
				case 'delete':
				$succeeded = wp_delete_comment($params['comment_id'], true);
				$message = 'comment_delete_with_comment_author';
					break;
				case 'notspam':
				$succeeded = wp_set_comment_status($params['comment_id'], 'hold');
				$message = 'comment_not_spam_with_comment_author';
					break;
				case 'restore':
				$succeeded = wp_set_comment_status($params['comment_id'], 'hold');
				$message = 'comment_restore_with_comment_author';
					break;
			}
			
			// If the current action succeeded, then we return a success message, otherwise,
			// we return an error message to the user issuing the request.
			
			if ($succeeded) {
				$result = array('error' => false, 'message' => $message, 'values' => array($comment->comment_author), 'status' => $comment->comment_status, 'approved' => $comment->comment_approved);
			} else {
				$result = array('error' => true, 'message' => 'comment_change_status_failed_with_error', 'values' => array($comment->comment_ID));
			}
		} else {
			$result = array('error' => true, 'message' => 'comment_does_not_exists_error', 'values' => array($params['comment_id']));
		}
		
		// Here, we're restoring to the current (default) blog before we
		// do the switched.

		if (function_exists('restore_current_blog') && $switched) {
			restore_current_blog();
		}
		
		return $this->_response($result);
	}
}
