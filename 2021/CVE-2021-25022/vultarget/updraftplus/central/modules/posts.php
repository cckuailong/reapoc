<?php

if (!defined('UPDRAFTCENTRAL_CLIENT_DIR')) die('No access.');

/**
 * Handles Posts Commands
 */
class UpdraftCentral_Posts_Commands extends UpdraftCentral_Commands {

	protected $switched = false;

	protected $post_type = 'post';

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
	public function _post_action($command, $data, $extra_info) {// phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found, VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
		// Here, we're restoring to the current (default) blog before we switched
		if ($this->switched) restore_current_blog();
	}

	/**
	 * Returns the keys and fields names that are associated to a particular module type
	 *
	 * @param string $type The type of the module that the current request is processing
	 *
	 * @return array
	 */
	private function get_state_fields_by_type($type) {
		$state_fields = array(
			'post' => array(
				'validation_fields' => array('publish_posts', 'edit_posts', 'delete_posts'),
				'items_key' => 'posts',
				'count_key' => 'posts_count',
				'list_key' => 'posts',
				'result_key' => 'get',
				'error_key' => 'post_state_change_failed'
			),
			'page' => array(
				'validation_fields' => array('publish_pages', 'edit_pages', 'delete_pages'),
				'items_key' => 'pages',
				'count_key' => 'pages_count',
				'list_key' => 'pages',
				'result_key' => 'get',
				'error_key' => 'page_state_change_failed'
			)
		);

		if (!isset($state_fields[$type])) return array();
		return $state_fields[$type];
	}

	/**
	 * Fetch and retrieves posts based from the submitted parameters
	 *
	 * @param array $params Containing all the needed information to filter the results of the current request
	 * @return array
	 */
	public function get($params) {

		$state_fields = $this->get_state_fields_by_type($this->post_type);
		if (empty($state_fields)) return $this->_generic_error_response('unsupported_type_on_get_posts');

		$error = $this->_validate_capabilities($state_fields['validation_fields']);
		if (!empty($error)) return $error;

		// check paged parameter; if empty set to defaults
		$paged = !empty($params['paged']) ? (int) $params['paged'] : 1;
		$numberposts = !empty($params['numberposts']) ? (int) $params['numberposts'] : 10;
		$offset = ($paged - 1) * $numberposts;

		$args = array(
			'posts_per_page' => $numberposts,
			'paged' => $paged,
			'offset' => $offset,
			'post_type' => $this->post_type,
			'post_status' => 'publish,private,draft,pending,future',
		);

		if (!empty($params['keyword'])) {
			$args['s'] = $params['keyword'];
		}

		if ('post' == $this->post_type) {
			if (!empty($params['category'])) {
				$args['cat'] = (int) $params['category'];
			}
		}

		if (!empty($params['date'])) {
			list($monthnum, $year) = explode(':', $params['date']);

			$args['monthnum'] = $monthnum;
			$args['year'] = $year;
		}

		if (!empty($params['status']) && 'all' !== $params['status']) {
			$args['post_status'] = $params['status'];
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
				// Pulling any other relevant and additional information regarding
				// the post before returning it in the response.
				$postdata = $this->get_postdata($post, false);
				if (!empty($postdata)) {
					array_push($posts, $postdata);
				}
			}
		}

		$response = array(
			$state_fields['items_key'] => $posts,
			'options' => $this->get_options($this->post_type),
			'info' => $info,
			$state_fields['count_key'] => $this->get_post_status_counts($this->post_type)
		);

		// Load any additional information if preload parameter is set. Will only be
		// requested on initial load of items in UpdraftCentral.
		if (isset($params['preload']) && $params['preload']) {
			$timeout = !empty($params['timeout']) ? $params['timeout'] : 30;
			$response = array_merge($response, $this->get_preload_data($timeout, $this->post_type));
		}

		return $this->_response($response);
	}

	/**
	 * Extracts public properties from complex object and return a simple
	 * object (stdClass) that contains the public properties of the original object.
	 *
	 * @param object $obj Any type of complex objects that needs converting (e.g. WP_Taxonomy, WP_Term or WP_User)
	 * @return stdClass
	 */
	protected function trim_object($obj) {
		// To preserve the object's accessibility through its properties we recreate
		// the object using the stdClass and fill it with the public properties
		// that will be extracted from the original object ($obj).
		$newObj = new stdClass();

		if (is_object($obj)) {
			// Making sure that we only extract those publicly accessible properties excluding
			// the private, protected, static ones and methods.
			$props = get_object_vars($obj);
			if (!empty($props)) {
				foreach ($props as $key => $value) {
					$newObj->{$key} = $value;
				}
			}
		}

		return $newObj;
	}

	/**
	 * Retrieves information that will be preloaded in UC for quick and easy access
	 * when editing a certain page or post
	 *
	 * @param int    $timeout The user-defined timeout from UpdraftCentral
	 * @param string $type    The type of the module that the current request is processing
	 *
	 * @return array
	 */
	protected function get_preload_data($timeout, $type = 'post') {
		global $updraftcentral_host_plugin, $updraftcentral_main;

		if (!function_exists('get_page_templates')) {
			require_once(ABSPATH.'wp-admin/includes/theme.php');
		}

		$templates = ('post' == $type) ? get_page_templates(null, 'post') : get_page_templates();
		if (!empty($templates)) {
			$templates = array_flip($templates);
			if (!isset($templates['default'])) {
				$templates['default'] = $updraftcentral_host_plugin->retrieve_show_message('default_template');
			}
		}

		// Preloading elements saves time and avoid unnecessary round trips to fetch
		// these information individually.
		$authors = $this->get_authors();
		$parent_pages = $this->get_parent_pages();

		$data = array(
			'authors' => $authors['data']['authors'],
			'parent_pages' => $parent_pages['data']['pages'],
			'templates' => $templates,
			'editor_styles' => $this->get_editor_styles($timeout),
			'wp_version' => $updraftcentral_main->get_wordpress_version()
		);

		if ('post' == $type) {
			$categories = $this->get_categories();
			$tags = $this->get_tags();

			$data['taxonomies'] = $this->get_taxonomies();
			$data['categories'] = $categories['data'];
			$data['tags'] = $tags['data'];
		}

		return array(
			'preloaded' => json_encode($data)
		);
	}

	/**
	 * Extract content from the given css path
	 *
	 * @param string $style   CSS file path
	 * @param int    $timeout The user-defined timeout from UpdraftCentral
	 * @return string
	 */
	protected function extract_css_content($style, $timeout) {

		$content = '';
		if (1 === preg_match('~^(https?:)?//~i', $style)) {
			$response = wp_remote_get($style, array('timeout' => $timeout));
			if (!is_wp_error($response)) {
				$result = trim(wp_remote_retrieve_body($response));
				if (!empty($result)) $content = $result;
			}
		} else {
			// Editor styles that resides in "css/dist"
			if (false !== ($pos = stripos($style, 'css/dist'))) {
				$file = ABSPATH.WPINC.substr_replace($style, '/', 0, $pos);
			} else {
				// Styles that resides in "wp-content/themes" (coming from $editor_styles global var)
				$file = get_theme_file_path($style);
			}

			$is_valid = (function_exists('is_file')) ? is_file($file) : file_exists($file);
			if ($is_valid) {
				$result = trim(file_get_contents($file));
				if (!empty($result)) $content = $result;
			}
		}

		return $this->filter_url($content);
	}

	/**
	 * Convert URL entries contained in the CSS content to absolute URLs
	 *
	 * @param string $content The content of the CSS file
	 * @return string
	 */
	protected function filter_url($content) {

		// Replace with valid URL (absolute)
		preg_match_all('~url\((.+?)\)~i', $content, $all_matches);
		if (!empty($all_matches) && isset($all_matches[1])) {
			$urls = array_unique($all_matches[1]);
			foreach ($urls as $url) {
				$url = str_replace('"', '', $url);
				if (false !== strpos($url, 'data:')) continue;

				if (1 !== preg_match('~^(https?:)?//~i', $url)) {
					if (1 === preg_match('~(plugins|themes)~i', $url, $matches)) {
						if (false !== ($pos = stripos($url, $matches[1]))) {
							if (!function_exists('content_url')) {
								require_once ABSPATH.WPINC.'/link-template.php';
							}

							$absolute_url = rtrim(content_url(), '/').substr_replace($url, '/', 0, $pos);
							$content = str_replace($url, $absolute_url, $content);
						}
					} else {
						$path = preg_replace('~(\.+\/)~', '', $url);
						$dirpath = trailingslashit(get_stylesheet_directory());
						if (!file_exists($dirpath.$url)) $path = $this->resolve_path($path);

						$absolute_url = (!empty($path)) ? trailingslashit(get_stylesheet_directory_uri()).ltrim($path, '/') : '';
						$content = str_replace($url, $absolute_url, $content);
					}
				}
			}
		}

		return $content;
	}

	/**
	 * Resolve URL to its actual absolute path
	 *
	 * @param string $path Some relative path to check
	 * @return string
	 */
	protected function resolve_path($path) {
		$dir = trailingslashit(get_stylesheet_directory());
		// Some relative paths declared within the css file (e.g. only has '../fonts/etc/', called deep down from a subfolder) where parent
		// subfolder is not articulated needs to be resolve further to get its actual absolute path. Using glob will pinpoint its actual location
		// rather than iterating through a series of sublevels just to find the actual file.
		$result = str_replace($dir, '', glob($dir.'{,*/}{'.$path.'}', GLOB_BRACE));
		
		if (!empty($result)) return $result[0];
		return false;
	}

	/**
	 * Retrieve the editor styles/assets to be use by UpdraftCentral when editing a post
	 *
	 * @param int $timeout The user-defined timeout from UpdraftCentral
	 * @return array()
	 */
	protected function get_editor_styles($timeout) {
		global $editor_styles, $wp_styles;
		$editing_styles = $loaded = array();

		$required = array('css/dist/editor/style.css', 'css/dist/block-library/style.css', 'css/dist/block-library/theme.css');
		foreach ($required as $style) {
			$editing_styles[] = array('css' => $this->extract_css_content($style, $timeout), 'inline' => '');
		};

		do_action('enqueue_block_editor_assets');
		do_action('enqueue_block_assets');

		// Checking for editor styles support since styles make vary from theme to theme
		if ($editor_styles) {
			foreach ($editor_styles as $style) {
				if (false !== array_search($style, $loaded)) continue;

				$editing_styles[] = array('css' => $this->extract_css_content($style, $timeout), 'inline' => '');
				$loaded[] = $style;
			}
		}

		if ($wp_styles) {
			foreach ($wp_styles->queue as $handle) {
				$style = $wp_styles->registered[$handle]->src;
				if (false !== array_search($style, $loaded)) continue;
	
				$inline = $wp_styles->print_inline_style($handle, false);
				$editing_styles[] = array(
					'css' => $this->extract_css_content($style, $timeout),
					'inline' => (!$inline) ? '' : $inline
				);
				$loaded[] = $style;
			}
		}

		$editing_styles[] = array('css' => $this->extract_css_content('/style.css', $timeout), 'inline' => '');
		return $editing_styles;
	}

	/**
	 * Retrieves the total number of items found under each post statuses
	 *
	 * @param string $type The type of the module that the current request is processing
	 *
	 * @return array
	 */
	protected function get_post_status_counts($type = 'post') {
		$posts = wp_count_posts($type);

		$publish = (int) $posts->publish;
		$private = (int) $posts->private;
		$draft = (int) $posts->draft;
		$pending = (int) $posts->pending;
		$future = (int) $posts->future;
		$trash = (int) $posts->trash;

		// We exclude "trash" from the overall total as WP doesn't actually
		// consider or include it in the total count.
		$all = $publish + $private + $draft + $pending + $future;

		return array(
			'all' => $all,
			'publish' => $publish,
			'private' => $private,
			'draft' => $draft,
			'pending' => $pending,
			'future' => $future,
			'trash' => $trash,
		);
	}

	/**
	 * Retrieves a collection of formatted dates found for the given post statuses.
	 * It will be used as options for the date filter when managing the posts in UpdraftCentral.
	 *
	 * @param string $type The type of the module that the current request is processing
	 *
	 * @return array
	 */
	protected function get_date_options($type = 'post') {
		global $wpdb;

		$date_options = $wpdb->get_col("SELECT DATE_FORMAT(`post_date`, '%M %Y') as `formatted_post_date` FROM {$wpdb->posts} WHERE `post_type` = '{$type}' AND `post_status` IN ('publish', 'private', 'draft', 'pending', 'future') GROUP BY `formatted_post_date` ORDER BY `post_date` DESC");

		return $date_options;
	}

	/**
	 * Make sure that we have the required fields to use in UpdraftCentral for
	 * displaying the categories and tags sections. Add if missing.
	 *
	 * @param object $item Taxonomy item to check
	 * @return object
	 */
	protected function map_tax($item) {
		$taxs = array('category' => 'categories', 'post_tag' => 'tags');
		if (array_key_exists($item->name, $taxs)) {
			if (!isset($item->show_in_rest)) $item->show_in_rest = true;
			if (!isset($item->rest_base)) $item->rest_base = $taxs[$item->name];
		}

		return $item;
	}

	/**
	 * Fetch and retrieves available taxonomies for this site and some capabilities specific
	 * to tags and categories when managing them.
	 *
	 * @return array
	 */
	protected function get_taxonomies() {
		$taxonomies = get_taxonomies(array(), 'objects');
		$taxonomies = array_map(array($this, 'map_tax'), $taxonomies);

		$response = array(
			'taxonomies' => $taxonomies,
			'current_user_cap' => array(
				'manage_categories' => current_user_can('manage_categories'),
				'edit_categories' => current_user_can('edit_categories'),
				'delete_categories' => current_user_can('delete_categories'),
				'assign_categories' => current_user_can('assign_categories'),
				'manage_post_tags' => current_user_can('manage_post_tags'),
				'edit_post_tags' => current_user_can('edit_post_tags'),
				'delete_post_tags' => current_user_can('delete_post_tags'),
				'assign_post_tags' => current_user_can('assign_post_tags'),
			)
		);

		return $response;
	}

	/**
	 * Fetch and retrieves categories based from the submitted parameters
	 *
	 * @param array $query Containing all the needed information to filter the results of the current request
	 * @return array
	 */
	public function get_categories($query = array()) {
		$page = !empty($query['page']) ? (int) $query['page'] : 1;
		$items_per_page = !empty($query['per_page']) ? (int) $query['per_page'] : 100;
		$offset = ($page - 1) * $items_per_page;
		$order = !empty($query['order']) ? $query['order'] : 'asc';
		$orderby = !empty($query['orderby']) ? $query['orderby'] : 'name';

		$args = array(
			'hide_empty' => false,
			'orderby' => $orderby,
			'order' => $order,
			'number' => $items_per_page,
			'offset' => $offset
		);

		$categories = get_categories($args);
		$category_options = array();

		if (!empty($categories)) {
			foreach ($categories as $key => $term) {
				$parent_term = get_term((int) $term->parent, $term->taxonomy);
				if (!is_wp_error($parent_term) && !is_null($parent_term)) {
					$parent_term = json_encode($this->trim_object($parent_term));
				} else {
					$parent_term = '';
				}

				$category_options[] = array(
					'id' => $term->term_id,
					'name' => $term->name,
					'parent' => $term->parent
				);

				$categories[$key] = array(
					'term' => json_encode($this->trim_object($term)),
					'misc' => array(
						'link' => get_term_link($term),
						'parent_term' => $parent_term,
						'taxonomy' => $term->taxonomy
					)
				);
			}
		}

		$categorytax = get_taxonomy('category');
		$parent_dropdown_args = array(
			'taxonomy'         => 'category',
			'hide_empty'       => 0,
			'name'             => 'newcategory_parent',
			'orderby'          => 'name',
			'hierarchical'     => 1,
			'show_option_none' => '&mdash; '.$categorytax->labels->parent_item.' &mdash;',
			'echo'			   => false
		);

		$parent_dropdown_args = apply_filters('post_edit_category_parent_dropdown_args', $parent_dropdown_args);
		$parent_dropdown = wp_dropdown_categories($parent_dropdown_args);

		if (!function_exists('wp_popular_terms_checklist')) {
			require_once ABSPATH . 'wp-admin/includes/template.php';
		}

		ob_start();
		wp_popular_terms_checklist('category');
		$popular_terms_checklist = ob_get_contents();
		ob_end_clean();

		return $this->_response(array(
			'terms' => $categories,
			'misc' => array(
				'formatted' => $category_options,
				'raw' => $categories,
				'tax' => json_encode($this->trim_object($categorytax)),
				'popular' => $popular_terms_checklist,
				'parent_dropdown' => $parent_dropdown,
				'capabilities' => array(
					'can_edit_terms' => current_user_can($categorytax->cap->edit_terms)
				)
			)
		));
	}

	/**
	 * Fetch and retrieves tags based from the submitted parameters
	 *
	 * @param array $query Containing all the needed information to filter the results of the current request
	 * @return array
	 */
	public function get_tags($query = array()) {
		$page = !empty($query['page']) ? (int) $query['page'] : 1;
		$items_per_page = !empty($query['per_page']) ? (int) $query['per_page'] : 100;
		$offset = ($page - 1) * $items_per_page;
		$order = !empty($query['order']) ? $query['order'] : 'desc';
		$orderby = !empty($query['orderby']) ? $query['orderby'] : 'count';

		$args = array(
			'hide_empty' => false,
			'orderby' => $orderby,
			'order' => $order,
			'number' => $items_per_page,
			'offset' => $offset
		);

		$tags = get_tags($args);
		$tag_options = array();
		$tag_cloud = '';

		if (!empty($tags)) {
			$tags_for_cloud = array();
			foreach ($tags as $key => $term) {
				if (!isset($term->link)) $term->link = get_tag_link($term->term_id);
				array_push($tags_for_cloud, $term);

				$parent_term = get_term((int) $term->parent, $term->taxonomy);
				if (!is_wp_error($parent_term) && !is_null($parent_term)) {
					$parent_term = json_encode($this->trim_object($parent_term));
				} else {
					$parent_term = '';
				}

				$tag_options[] = array(
					'id' => $term->term_id,
					'name' => $term->name,
				);

				$tags[$key] = array(
					'term' => json_encode($this->trim_object($term)),
					'misc' => array(
						'link' => get_term_link($term),
						'parent_term' => $parent_term,
						'taxonomy' => $term->taxonomy
					)
				);
			}

			add_filter('tag_cloud_sort', array($this, 'sort_tag_cloud'), 9, 2);

			if (!function_exists('wp_generate_tag_cloud')) {
				require_once ABSPATH.WPINC.'/category-template.php';
			}

			$tag_cloud = wp_generate_tag_cloud($tags_for_cloud, array(
				'smallest' => 10,
				'largest' => 22,
				'unit' => 'pt',
				'number' => 10,
				'format' => 'flat',
				'separator' => " ",
				'orderby' => 'count',
				'order' => 'DESC',
				'show_count' => 1,
				'echo' => false
			));
		}

		$tagtax = get_taxonomy('post_tag');
		return $this->_response(array(
			'terms' => $tags,
			'misc' => array(
				'formatted' => $tag_options,
				'raw' => $tags,
				'tax' => json_encode($this->trim_object($tagtax)),
				'tag_cloud' => $tag_cloud,
				'capabilities' => array(
					'can_assign_terms' => current_user_can($tagtax->cap->assign_terms)
				)
			)
		));
	}

	/**
	 * Sorts the tag items that are to be shown within the tag cloud
	 *
	 * @param array $tags The array to be sorted. Contains the tag items
	 * @param array $args Additional parameters needed for the sorting process
	 * @return array
	 */
	public function sort_tag_cloud($tags, $args) {
		uasort($tags, array($this, '_wp_object_count_sort_cb'));
		if ('DESC' === $args['order']) {
			$tags = array_reverse($tags, true);
		}

		return $tags;
	}

	/**
	 * Serves as a callback for comparing objects based on count. Copied from WordPress 5.7
	 * core (wp-includes/category-template.php) and tweaked to return integer instead of boolean
	 * because returning boolean using uasort is now DEPRECATED in PHP 8.
	 *
	 * Used with `uasort()`.
	 *
	 * @since 3.1.0
	 * @access private
	 *
	 * @param object $a The first object to compare.
	 * @param object $b The second object to compare.
	 * @return bool Whether the count value for `$a` is greater than the count value for `$b`.
	 */
	public function _wp_object_count_sort_cb($a, $b) {
		if ($a->count == $b->count) {
			return 0;
		}
		return ( $a->count > $b->count ) ? 1 : -1;
	}

	/**
	 * Fetch all available taxonomies and terms information for the given post object
	 *
	 * @param array $post The "Post" object to use when retrieving the information
	 * @return array
	 */
	protected function get_taxonomies_terms($post) {
		$taxonomies = get_object_taxonomies($post->post_type, 'objects');
		$taxonomies = array_map(array($this, 'map_tax'), $taxonomies);

		$taxonomy_names = array();
		$taxonomy_terms = array();
		$taxonomy_caps = array();

		foreach ($taxonomies as $taxonomy) {
			$terms = get_the_terms($post->ID, $taxonomy->name);
			$terms = !is_array($terms) ? (array) $terms : $terms;

			$taxonomy_terms[$taxonomy->name] = $terms;
			$taxonomy_caps[$taxonomy->name] = array(
				'hierarchical' => is_taxonomy_hierarchical($taxonomy->name),
				'edit_terms' => current_user_can($taxonomy->cap->edit_terms),
				'assign_terms' => current_user_can($taxonomy->cap->assign_terms),
			);
			array_push($taxonomy_names, $taxonomy->name);
		}

		return array(
			'objects' => $taxonomies,
			'names' => $taxonomy_names,
			'terms' => $taxonomy_terms,
			'caps' => $taxonomy_caps,
		);
	}

	/**
	 * Retrieves the underlying data for the given post. Some extra information are
	 * passed along that will be consumed by the editor in UpdraftCentral
	 *
	 * @param int|object $param  Post object or a post ID
	 * @param boolean    $encode True to encode the post object, false otherwise
	 * @return array
	 */
	public function get_postdata($param, $encode = true) {
		$response = array();

		if (is_object($param) && isset($param->ID)) {
			$post = $param;
		} elseif (is_numeric($param)) {
			$post = get_post($param);
		}

		if ($post) {
			$post_type_obj = get_post_type_object($post->post_type);
			
			$is_post_type_viewable = false;
			if (!empty($post_type_obj)) {
				$is_post_type_viewable = $post_type_obj->publicly_queryable || ($post_type_obj->_builtin && $post_type_obj->public);
			}

			if (!function_exists('get_sample_permalink')) {
				require_once ABSPATH.'wp-admin/includes/post.php';
			}

			// Validate template exists on the current theme, otherwise,
			// reset the template to default.
			$template = get_page_template_slug($post->ID);
			if (!empty($template)) {
				$page_templates = wp_get_theme()->get_page_templates($post);
				if ('default' != $template && !isset($page_templates[$template])) {
					update_post_meta($post->ID, '_wp_page_template', 'default');
				}
			}

			$published_date = array(
				'jj' => date('d', strtotime($post->post_date)),
				'mm' => date('m', strtotime($post->post_date)),
				'aa' => date('Y', strtotime($post->post_date)),
				'hh' => date('H', strtotime($post->post_date)),
				'mn' => date('i', strtotime($post->post_date)),
				'ss' => date('s', strtotime($post->post_date))
			);

			$sample_permalink = get_sample_permalink($post->ID, $post->post_title, '');
			$permalink = get_permalink($post->ID);
			$slug = $post->post_name;

			if (!empty($sample_permalink) && !empty($slug)) {
				if (isset($sample_permalink[0])) {
					if (false !== stripos($sample_permalink[0], '%pagename%/') || false !== stripos($sample_permalink[0], '%postname%/')) {
						$token = (false !== stripos($sample_permalink[0], '%pagename%/')) ? '%pagename%/' : '%postname%/';
						$permalink = str_replace($token, '', $sample_permalink[0]).$slug;
					}
				}
			}

			$response = array(
				'post' => $encode ? json_encode($post) : $post,
				'misc' => array(
					'guid_rendered' => apply_filters('get_the_guid', $post->guid, $post->ID),
					'link' => $permalink,
					'slug' => $slug,
					'site_url' => site_url('/'),
					'title_rendered' => get_the_title($post->ID),
					'content_rendered' => apply_filters('the_content', $post->post_content),
					'excerpt' => $post->post_excerpt,
					'featured_media' => 0,
					'sticky' => is_sticky($post->ID),
					'template' => get_page_template_slug($post->ID),
					'permalink_template' => get_permalink($post->ID, true),
					'author_name' => get_the_author_meta('display_name', $post->post_author),
					'publish_month_year' => date('F Y', strtotime($post->post_date)),
					'published_date' => $published_date,
					'format' => get_post_format($post->ID),
					'post_type_name' => $post_type_obj->name,
					'post_type_viewable' => $is_post_type_viewable,
					'post_type_public' => $post_type_obj->public,
					'post_type_hierarchical' => $post_type_obj->hierarchical,
					'sample_permalink' => get_sample_permalink($post->ID, $post->post_title, ''),
					'post_password_required' => post_password_required($post),
					'post_type_supports_authors' => post_type_supports($post->post_type, 'author'),
					'post_type_supports_comments' => post_type_supports($post->post_type, 'comments'),
					'post_type_supports_revisions' => post_type_supports($post->post_type, 'revisions'),
					'post_revisions' => array(),	// N.B. We're not going to allow revisions editing for now
					'post_thumbnail_id' => get_post_thumbnail_id($post->ID),
					'can_publish_posts' => current_user_can($post_type_obj->cap->publish_posts),
					'can_edit_others_posts' => current_user_can($post_type_obj->cap->edit_others_posts),
					'can_unfiltered_html' => current_user_can('unfiltered_html')
				)
			);

			if ('post' == $post->post_type) {
				$taxonomies = $this->get_taxonomies_terms($post);
				$response['misc']['taxonomy_objects'] = $taxonomies['objects'];
				$response['misc']['taxonomy_names'] = $taxonomies['names'];
				$response['misc']['taxonomy_terms'] = $taxonomies['terms'];
				$response['misc']['taxonomy_caps'] = $taxonomies['caps'];

				if (!function_exists('wp_popular_terms_checklist') || !function_exists('get_terms_to_edit')) {
					require_once ABSPATH . 'wp-admin/includes/template.php';
					require_once ABSPATH . 'wp-admin/includes/taxonomy.php';
				}
	
				if (!function_exists('wp_get_post_categories')) {
					require_once(ABSPATH.WPINC.'/post.php');
				}
	
				$categories = wp_get_post_categories($post->ID, array('fields' => 'ids'));
				if (!is_wp_error($categories)) {
					$response['misc']['categories'] = empty($categories) ? array() : $categories;
					$terms_to_edit = get_terms_to_edit($post->ID, 'category');
					if (!empty($terms_to_edit)) {
						$response['misc']['categories_list'] = str_replace(',', ', ', $terms_to_edit);
					}
	
					$popular_ids = wp_popular_terms_checklist('category', 0, 10, false);
					// On WP 3.4 the "wp_terms_checklist" doesn't have an "echo" parameter and will automatically
					// display the rendered checklist. Therefore, we're going to pull the terms so that all
					// versions starting from WP 3.4 will pull the content instead of displaying them.
	
					ob_start();
					// In this call we'll have to set the "echo" parameter to true so that later version of WP
					// will be able to catch and process it.
					wp_terms_checklist($post->ID, array('taxonomy' => 'category', 'popular_cats' => $popular_ids, 'echo' => true));
					$popular_checklist = ob_get_contents();
					ob_end_clean();
	
					$response['misc']['categories_checklist'] = $popular_checklist;
	
					ob_start();
					wp_terms_checklist($post->ID, array('taxonomy' => 'category', 'checked_ontop' => 0, 'echo' => true));
					$quickedit_checklist = ob_get_contents();
					ob_end_clean();
	
					$response['misc']['categories_quickedit_checklist'] = $quickedit_checklist;
				}
	
				$tags = wp_get_post_tags($post->ID, array('fields' => 'ids'));
				if (!is_wp_error($tags)) {
					$response['misc']['tags'] = empty($tags) ? array() : $tags;
					$terms_to_edit = get_terms_to_edit($post->ID, 'post_tag');
					if (!empty($terms_to_edit)) {
						$response['misc']['tags_list'] = str_replace(',', ', ', $terms_to_edit);
					}
				}
			}

			// Naturally, the "featured_media" will suffice when loading the image (media) in
			// UpdraftCentral since the value in this field is the actual image id of the featured
			// media used in UC. If we currently don't have an entry in the "featured_media_updraftcentral" meta,
			// then UC will need to download the featured media (image) for this current post/page
			// using the "featured_media_url" field (below) if not empty.
			$featured_media = get_post_meta($post->ID, 'featured_media_updraftcentral', true);
			if (!empty($featured_media)) {
				$response['misc']['featured_media'] = $featured_media;
			}

			// Retrieve featured media if currently present for the given post/page.
			// If present, we pull the image (media) URL in case there's a need for
			// UpdraftCentral to download the image upon loading the editor (e.g. the featured_media id
			// above no longer exists).
			$media_id = (int) get_post_thumbnail_id($post->ID);
			if (!empty($media_id)) {
				$response['misc']['featured_media_url'] = wp_get_attachment_url($media_id);
			} else {
				// The post/page no longer has a "featured_media" or doesn't have one currently, therefore,
				// we're going to set the "featured_media" and "featured_media_url" fields to both empty to
				// to avoid any further actions (e.g. download media).
				$response['misc']['featured_media'] = 0;
				$response['misc']['featured_media_url'] = '';
			}
		}

		return $response;
	}

	/**
	 * Changes the state/status of the submitted post(s)
	 *
	 * @param array	$params	An array of data that serves as parameters for the given request
	 * @return array
	 */
	public function set_state($params) {

		$state_fields = $this->get_state_fields_by_type($this->post_type);
		if (empty($state_fields)) return $this->_generic_error_response('unsupported_type_on_set_state');

		$error = $this->_validate_capabilities($state_fields['validation_fields']);
		if (!empty($error)) return $error;

		$result = array();
		if (!empty($params['list'])) {
			$posts = array();
			foreach ($params['list'] as $id) {
				$post = $this->apply_state($id, $params['action'], $this->post_type);
				if (!empty($post)) {
					array_push($posts, $post);
				}
			}

			if (!empty($posts)) {
				$result = array($state_fields['list_key'] => $posts);
			}
		} elseif (!empty($params['id'])) {
			$post = $this->apply_state($params['id'], $params['action'], $this->post_type);
			if (!empty($post)) $result = $post;
		}

		if (!empty($result)) {
			$response = $this->get($params);
			if (!empty($response['response']) && 'rpcok' === $response['response']) {
				$result[$state_fields['result_key']] = $response['data'];
			}

			return $this->_response($result);
		} else {
			return $this->_generic_error_response($state_fields['error_key'], array('action' => $params['action']));
		}
	}

	/**
	 * Creates new category
	 *
	 * @param array	  $params	     An array of data that serves as parameters for the given request
	 * @param boolean $wrap_response Indicates whether to wrap the response based on local or UpdraftCentral calls. Default true.
	 * @return array
	 */
	public function add_category($params, $wrap_response = true) {
		$error = $this->_validate_capabilities(array('manage_categories'));
		if (!empty($error)) return $error;

		$name = sanitize_text_field($params['name']);
		$args = array();
		if (!empty($params['parent'])) {
			$args['parent'] = $params['parent'];
		}

		$result = wp_insert_term($name, 'category', $args);
		if (!is_wp_error($result)) {
			$term_id = $result['term_id'];
			$term = get_term($term_id, 'category');

			$data = array();
			if (!is_wp_error($term)) {
				$data = array(
					'id' => $term->term_id,
					'count' => $term->count,
					'description' => $term->description,
					'link' => get_term_link($term->term_id, 'category'),
					'name' => $term->name,
					'slug' => $term->slug,
					'taxonomy' => $term->taxonomy,
					'parent' => $term->parent,
					'meta' => array()
				);

				$categories = $this->get_categories();
				if ($wrap_response) $data['categories'] = json_encode($categories['data']);
			}

			return $wrap_response ? $this->_response($data) : $data;
		} else {
			$error = array(
				'message' => $result->get_error_message()
			);

			return $wrap_response ? $this->_generic_error_response('post_add_category_failed', $error) : $error;
		}
	}

	/**
	 * Assigns categories to a certain post object
	 *
	 * @param int	$post_id	  The ID of the post object
	 * @param array $category_ids A collection of category IDs to assign to the post object
	 * @return void
	 */
	protected function assign_category_to_post($post_id, $category_ids) {
		if (!empty($category_ids)) {
			// Making sure that we have the correct type to use and we
			// don't have any redundant IDs before saving.
			$category_ids = array_unique(array_map('intval', $category_ids));

			// Attach (new) categories to post
			wp_set_object_terms($post_id, $category_ids, 'category');
		} else {
			wp_set_object_terms($post_id, get_option('default_category'), 'category');
		}
	}

	/**
	 * Creates new tag
	 *
	 * @param array	  $params	     An array of data that serves as parameters for the given request
	 * @param boolean $wrap_response Indicates whether to wrap the response based on local or UpdraftCentral calls. Default true.
	 * @return array
	 */
	public function add_tag($params, $wrap_response = true) {
		// N.B. Since the "manage_post_tags" capability does not exist in WP 3.4. We'll use the "manage_categories" instead. Besides, the "manage_post_tags" along with the other tag-related capabilities in the latest versions are actually mapped to the "manage_categories" capability (refer to wp-includes/capabilities.php under the "map_meta_cap" function).
		$error = $this->_validate_capabilities(array('manage_categories'));
		if (!empty($error)) return $error;

		$name = sanitize_text_field($params['name']);
		$result = wp_insert_term($name, 'post_tag');
		if (!is_wp_error($result)) {
			$term_id = $result['term_id'];
			$term = get_term($term_id, 'post_tag');

			$data = array();
			if (!is_wp_error($term)) {
				$data = array(
					'id' => $term->term_id,
					'count' => $term->count,
					'description' => $term->description,
					'link' => get_term_link($term->term_id, 'post_tag'),
					'name' => $term->name,
					'slug' => $term->slug,
					'taxonomy' => $term->taxonomy,
					'meta' => array()
				);

				$tags = $this->get_tags();
				if ($wrap_response) $data['tags'] = json_encode($tags['data']);
			}

			return $wrap_response ? $this->_response($data) : $data;
		} else {
			$error = array(
				'message' => $result->get_error_message()
			);

			return $wrap_response ? $this->_generic_error_response('post_add_tag_failed', $error) : $error;
		}
	}

	/**
	 * Assigns tags to a certain post object
	 *
	 * @param int	$post_id The ID of the post object
	 * @param array $tag_ids A collection of tag IDs to assign to the post object
	 * @return void
	 */
	protected function assign_tag_to_post($post_id, $tag_ids) {
		if (!empty($tag_ids)) {
			// Making sure that we have the correct type to use and we
			// don't have any redundant IDs before saving.
			$tag_ids = array_unique(array_map('intval', $tag_ids));

			// Attach (new) tags to post
			wp_set_object_terms($post_id, $tag_ids, 'post_tag');
		} else {
			wp_set_object_terms($post_id, null, 'post_tag');
		}
	}

	/**
	 * Saves or updates post/page information based from the submitted data
	 *
	 * @param array	$params	An array of data that serves as parameters for the given request
	 * @return array
	 */
	public function save($params) {
		global $updraftcentral_host_plugin;

		$validation_fields = array(
			'post' => array('publish_posts', 'edit_posts', 'delete_posts'),
			'page' => array('publish_pages', 'edit_pages', 'delete_pages')
		);

		if (!isset($validation_fields[$this->post_type])) return $this->_generic_error_response('unsupported_type_on_save_post');

		$error = $this->_validate_capabilities($validation_fields[$this->post_type]);
		if (!empty($error)) return $error;

		if (!empty($params['id']) || !empty($params['new'])) {
			$args = array();

			// post_content
			if (!empty($params['content']))
				$args['post_content'] = $params['content'];

			// post_excerpt
			if (!empty($params['excerpt']))
				$args['post_excerpt'] = $params['excerpt'];

			// menu_order
			if (isset($params['order']))
				$args['menu_order'] = (int) $params['order'];

			// post_parent
			if (isset($params['parent'])) {
				$args['post_parent'] = empty($params['parent']) ? 0 : $params['parent'];
			}

			// post_name
			if (!empty($params['slug']))
				$args['post_name'] = $params['slug'];

			// post_status
			if (!empty($params['status'])) {
				$args['post_status'] = $params['status'];
			}

			// post_title
			if (!empty($params['title']))
				$args['post_title'] = $params['title'];

			// post_author
			if (!empty($params['author']))
				$args['post_author'] = $params['author'];

			// comment_status
			if (!empty($params['comment_status']))
				$args['comment_status'] = $params['comment_status'];

			// ping_status
			if (!empty($params['ping_status']))
				$args['ping_status'] = $params['ping_status'];

			// visibility
			if (!empty($params['visibility'])) {
				switch ($params['visibility']) {
					case 'public':
						$args['post_status'] = 'publish';
						$args['post_password'] = '';
						break;
					case 'password':
						$args['post_status'] = 'publish';
						$args['post_password'] = $params['password'];
						break;
					case 'private':
						$args['post_status'] = 'private';
						$args['post_password'] = '';
						break;
					default:
						break;
				}
			}

			// post/publish date
			if (!empty($params['date'])) {
				$datetime = strtotime($params['date']);
				$post_date = date('Y-m-d H:i:s', $datetime);

				$args['post_date'] = $post_date;
				$args['post_date_gmt'] = gmdate('Y-m-d H:i:s', $datetime);

				// We only change the status to "future" based from the submitted date if the post status
				// is not empty and equal to 'publish' and the date is for the coming future.
				if (!empty($params['status']) && 'publish' == $params['status']) {
					if (strtotime($post_date) > strtotime(date('Y-m-d H:i:s'))) $args['post_status'] = 'future';
				}
			}

			// Make sure we have a slug/post_name generated before insert/update
			if (empty($params['slug']) && !empty($params['title'])) {
				$args['post_name'] = sanitize_title_with_dashes($params['title']);
			}

			if (!empty($params['new'])) {
				$args['post_type'] = $this->post_type;
				$post_id = wp_insert_post($args, true);
			} else {
				$args['ID'] = $params['id'];
				$args['post_modified'] = date('Y-m-d H:i:s');
				$args['post_modified_gmt'] = gmdate('Y-m-d H:i:s');

				$post_id = wp_update_post($args, true);
			}

			// We have successfully created/updated a post at this point, thus, we'll continue
			// with implementing the other requested processes and return the result.
			if (!is_wp_error($post_id)) {
				// sticky post
				if (isset($params['sticky'])) {
					$sticky = (bool) $params['sticky'];
					if ($sticky) {
						stick_post($post_id);
					} else {
						if (is_sticky($post_id)) {
							unstick_post($post_id);
						}
					}
				}

				// template
				if (!empty($params['template'])) {
					update_post_meta($post_id, '_wp_page_template', $params['template']);
				}

				// featured_media
				if (isset($params['featured_media'])) {
					if (!empty($params['featured_media'])) {
						$featured_media = (int) $params['featured_media'];
						$attach_continue = true;
	
						$url = wp_get_attachment_url($featured_media);
						if (!empty($url) && !empty($params['featured_media_url']) && $url == $params['featured_media_url']) {
							set_post_thumbnail($post_id, $featured_media);
							update_post_meta($post_id, 'featured_media_updraftcentral', $params['featured_media']);
							$attach_continue = false;
						}
	
						if ($attach_continue) {
							$featured_media_data = !empty($params['featured_media_data']) ? $params['featured_media_data'] : null;
							$media_id = $this->attach_remote_image($params['featured_media_url'], $featured_media_data, $post_id);
							if (!empty($media_id)) {
								// If we have a successful attachment then add reference to UC's media id
								update_post_meta($post_id, 'featured_media_updraftcentral', $params['featured_media']);
							}
						}
					} else {
						// Remove featured image.
						delete_post_meta($post_id, '_thumbnail_id');
						delete_post_meta($post_id, 'featured_media_updraftcentral');
					}
				}

				// categories
				$categories_updated = false;
				if (!empty($params['categories'])) {
					$term_ids = array();
					foreach ($params['categories'] as $value) {
						$category = sanitize_text_field($value);
						$parent = 0;

						if (false !== strpos($category, ':')) {
							list($parent, $category) = explode(':', $category);
							$result = $this->add_category(array('name' => $category, 'parent' => $parent), false);

							if (!empty($result)) {
								array_push($term_ids, $result['id']);
							}
						} else {
							$term = get_term_by('id', $category, 'category');
							if (!empty($term)) {
								$term_id = $term->term_id;
								array_push($term_ids, $term_id);
							}
						}
					}

					$this->assign_category_to_post($post_id, $term_ids);
					$categories_updated = true;
				}

				// tags
				$tags_updated = false;
				if (!empty($params['tags'])) {
					$term_ids = array();
					foreach ($params['tags'] as $value) {
						$tag = sanitize_text_field($value);
						$field = is_numeric($tag) ? 'id' : 'name';

						$term = get_term_by($field, $tag, 'post_tag');
						if (!empty($term)) {
							$term_id = $term->term_id;
							array_push($term_ids, $term_id);
						} else {
							$result = $this->add_tag(array('name' => $tag), false);
							if (!empty($result)) {
								array_push($term_ids, $result['id']);
							}
						}
					}

					$this->assign_tag_to_post($post_id, $term_ids);
					$tags_updated = true;
				}

				// Pulling any other relevant and additional information regarding
				// the post before returning it in the response.
				$postdata = $this->get_postdata($post_id);

				if (!empty($params['new'])) {
					$timeout = !empty($params['timeout']) ? $params['timeout'] : 30;
					$postdata = array_merge($postdata, $this->get_preload_data($timeout, $this->post_type));
				} else {
					if ($categories_updated || $tags_updated) {
						$categories = $this->get_categories();
						$tags = $this->get_tags();

						$postdata['preloaded'] = json_encode(array(
							'categories' => $categories['data'],
							'tags' => $tags['data']
						));
					}
				}

				$postdata['options'] = $this->get_options($this->post_type);
				return $this->_response($postdata);
			} else {
				// ERROR: error creating or updating post
				return $this->_generic_error_response('post_save_failed', array(
					'message' => $post_id->get_error_message(),
					'args' => $args
				));
			}
		} else {
			// ERROR: no id parameter, invalid request
			return $this->_generic_error_response('post_invalid_request', array('message' => $updraftcentral_host_plugin->retrieve_show_message('parameters_missing')));
		}
	}

	/**
	 * Fetch and retrieves authors based from the submitted parameters
	 *
	 * @param array $params Containing all the needed information to filter the results of the current request
	 * @return array
	 */
	public function get_authors($params = array()) {
		// If expected parameters are empty or does not exists then set them to some default values
		$page = !empty($params['page']) ? (int) $params['page'] : 1;
		$per_page = !empty($params['per_page']) ? (int) $params['per_page'] : 15;
		$offset = ($page - 1) * $per_page;
		$who = !empty($params['who']) ? $params['who'] : 'authors';
		$order = !empty($params['order']) ? strtoupper($params['order']) : 'ASC';
		$orderby = !empty($params['orderby']) ? $params['orderby'] : 'display_name';

		$users = get_users(array(
			'number' => $per_page,
			'paged' => $page,
			'offset' => $offset,
			'who' => $who,
			'order' => $order,
			'orderby' => $orderby,
		));

		$authors = array();
		$locale = get_locale();

		foreach ($users as $user) {
			$data = array(
				'user' => json_encode($this->trim_object($user)),
				'misc' => array(
					'link' => get_author_posts_url($user->ID, $user->user_nicename),
					'locale' => function_exists('get_user_locale') ? get_user_locale($user) : $locale,
					'registered_date' => date('c', strtotime($user->user_registered)),
				)
			);

			array_push($authors, $data);
		}

		return $this->_response(array(
			'authors' => $authors
		));
	}

	/**
	 * Fetch and retrieves parent pages based from the submitted parameters
	 *
	 * @param array $params Containing all the needed information to filter the results of the current request
	 * @return array
	 */
	public function get_parent_pages($params = array()) {
		// If expected parameters are empty or does not exists then set them to some default values
		$page = !empty($params['page']) ? (int) $params['page'] : 1;
		$per_page = !empty($params['per_page']) ? (int) $params['per_page'] : 100;
		$offset = ($page - 1) * $per_page;
		$exclude = !empty($params['exclude']) ? $params['exclude'] : array();
		$order = !empty($params['order']) ? strtoupper($params['order']) : 'ASC';
		$orderby = !empty($params['orderby']) ? $params['orderby'] : 'menu_order';
		$status = !empty($params['status']) ? $params['status'] : 'publish';

		$args = array(
			'posts_per_page' => $per_page,
			'paged' => $page,
			'offset' => $offset,
			'post__not_in' => $exclude,
			'order' => $order,
			'orderby' => $orderby,
			'post_type' => 'page',
			'post_status' => $status,
		);

		$query = new WP_Query($args);
		$posts = $query->posts;

		$pages = array();
		if (!empty($posts)) {
			foreach ($posts as $post) {
				// Get additional information and merge with the response
				$postdata = $this->get_postdata($post, true);
				if (!empty($postdata)) array_push($pages, $this->trim_parent_info($postdata));
			}
		}

		return $this->_response(array(
			'pages' => $pages
		));
	}

	/**
	 * Trim down return data for parent pages
	 *
	 * @param array $postdata The array containing the data to process
	 * @return array
	 */
	protected function trim_parent_info($postdata) {

		if (isset($postdata['post'])) {
			$post = json_decode($postdata['post']);

			$page = new stdClass();
			$page->ID = $post->ID;
			$page->post_title = $post->post_title;
			$page->post_parent = $post->post_parent;
			$page->post_type = $post->post_type;
			$page->post_status = $post->post_status;

			$postdata['post'] = json_encode($page);
		}

		return $postdata;
	}

	/**
	 * Retrieves pages, templates, authors, categories and tags data that will be
	 * used as options when displayed on the editor in UpdraftCentral
	 *
	 * @param string $type The type of the module that the current request is processing
	 *
	 * @return array
	 */
	protected function get_options($type = 'post') {
		// Primarily used for editor consumption so we don't include trash here. Besides,
		// trash posts/pages aren't included as parent options.
		$parent_pages = $this->get_parent_pages();
		$pages = $parent_pages['data']['pages'];

		// Add flexibility by letting users filter the default roles and add their own
		// custom page/post "author" role(s) if need be.
		$author_roles = apply_filters('updraftcentral_author_roles', array('administrator', 'editor', 'author', 'contributor'));
		$authors = get_users(array('role__in' => $author_roles));

		if (!function_exists('get_page_templates')) {
			require_once(ABSPATH.'wp-admin/includes/theme.php');
		}

		$templates = ('post' == $type) ? get_page_templates(null, 'post') : get_page_templates();
		$template_options = array();
		foreach ($templates as $template => $filename) {
			$item = array(
				'filename' => $filename,
				'template' => $template,
			);
			$template_options[] = $item;
		}

		$page_options = array();
		foreach ($pages as $page_item) {
			if (isset($page_item['post'])) {
				$page = json_decode($page_item['post']);
				$item = array(
					'id' => $page->ID,
					'title' => $page->post_title,
					'parent' => $page->post_parent
				);
				$page_options[] = $item;
			}
		}

		$author_options = array();
		foreach ($authors as $user) {
			$item = array(
				'id' => $user->ID,
				'name' => $user->display_name,
			);
			$author_options[] = $item;
		}

		$response = array(
			'page' => $page_options,
			'author' => $author_options,
			'template' => $template_options,
			'date' => $this->get_date_options($type),
		);

		if ('post' == $type) {
			$categories = get_categories(array('hide_empty' => false, 'orderby' => 'name', 'order' => 'ASC'));
			$tags = get_tags(array('hide_empty' => false));

			$category_options = array();
			foreach ($categories as $category) {
				$item = array(
					'id' => $category->term_id,
					'name' => $category->name,
					'parent' => $category->parent
				);
				$category_options[] = $item;
			}
	
			$tag_options = array();
			foreach ($tags as $tag) {
				$item = array(
					'id' => $tag->term_id,
					'name' => $tag->name,
				);
				$tag_options[] = $item;
			}

			$response['category'] = $category_options;
			$response['tag'] = $tag_options;
		}

		return $response;
	}

	/**
	 * Changes the state/status of the given post based from the submitted action/request
	 *
	 * @param int    $id     The ID of the current page to work on
	 * @param string $action The type of change that the current request is going to apply
	 * @param string $type   The type of the module that the current request is processing
	 *
	 * @return array
	 */
	protected function apply_state($id, $action, $type = 'post') {
		if (empty($id)) return false;

		$post = get_post($id);
		if (!empty($post)) {
			$previous_status = $post->post_status;
			$deleted = false;

			switch ($action) {
				case 'draft':
					$args = array('ID' => $id, 'post_status' => 'draft');
					wp_update_post($args);
					break;
				case 'trash':
					wp_trash_post($id);
					break;
				case 'publish':
					$args = array('ID' => $id, 'post_status' => 'publish');
					wp_update_post($args);
					break;
				case 'restore':
					$args = array('ID' => $id, 'post_status' => 'pending');
					wp_update_post($args);
					break;
				case 'delete':
					$result = wp_delete_post($id, true);
					if (!empty($result)) $deleted = true;
					break;
				default:
					break;
			}

			$postdata = $this->get_postdata($post);
			if (!empty($postdata) || $deleted) {
				$data = $deleted ? $id : $postdata;
				$result = array(
					'id' => $id,
					'previous_status' => $previous_status
				);

				$result[$type] = $data;
				return $result;
			}
		}

		return false;
	}

	/**
	 * Imports image from UpdraftCentral's page/post editor
	 *
	 * @param string $image_url  The URL of the image to import
	 * @param string $image_data The image data to save. If empty, image_url will be used to download the image
	 * @param int    $post_id    The ID of the page where this image is to be attached
	 *
	 * @return integer
	 */
	protected function attach_remote_image($image_url, $image_data, $post_id) {
		if (empty($image_url) || empty($post_id)) return;

		$image = pathinfo($image_url);
		$image_name = $image['basename'];
		$upload_dir = wp_upload_dir();

		if (empty($image_data)) {
			$response = wp_remote_get($image_url);
			if (!is_wp_error($response)) {
				$image_data = wp_remote_retrieve_body($response);
			}
		} else {
			$image_data = base64_decode($image_data);
		}

		$media_id = 0;
		if (!empty($image_data)) {
			$unique_file_name = wp_unique_filename($upload_dir['path'], $image_name);
			$filename = basename($unique_file_name);

			if (wp_mkdir_p($upload_dir['path'])) {
				$file = $upload_dir['path'] . '/' . $filename;
			} else {
				$file = $upload_dir['basedir'] . '/' . $filename;
			}

			file_put_contents($file, $image_data);
			$wp_filetype = wp_check_filetype($filename, null);

			$attachment = array(
				'post_mime_type' => $wp_filetype['type'],
				'post_title'     => sanitize_file_name($filename),
				'post_content'   => '',
				'post_status'    => 'inherit'
			);

			$media_id = wp_insert_attachment($attachment, $file, $post_id);
			require_once(ABSPATH . 'wp-admin/includes/image.php');

			$attach_data = wp_generate_attachment_metadata($media_id, $file);
			wp_update_attachment_metadata($media_id, $attach_data);
			set_post_thumbnail($post_id, $media_id);
		}

		return $media_id;
	}

	/**
	 * Checks whether we have the required fields submitted and the user has
	 * the capabilities to execute the requested action
	 *
	 * @param array $capabilities The capabilities to check and validate
	 *
	 * @return array|void
	 */
	protected function _validate_capabilities($capabilities) {
		foreach ($capabilities as $capability) {
			if (!current_user_can($capability)) return $this->_generic_error_response('insufficient_permission');
		}
	}
}
