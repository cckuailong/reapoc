<?php

/**
* Core functions
*/
class Permalink_Manager_Core_Functions extends Permalink_Manager_Class {

	public function __construct() {
		add_action( 'init', array($this, 'init_hooks'), 99);
	}

	function init_hooks() {
		global $permalink_manager_options;

		// Trailing slashes
		add_filter( 'permalink_manager_filter_final_term_permalink', array($this, 'control_trailing_slashes'), 9);
		add_filter( 'permalink_manager_filter_final_post_permalink', array($this, 'control_trailing_slashes'), 9);
		add_filter( 'permalink_manager_filter_post_sample_uri', array($this, 'control_trailing_slashes'), 9);
		add_filter( 'wpseo_canonical', array($this, 'control_trailing_slashes'), 9);
		add_filter( 'wpseo_opengraph_url', array($this, 'control_trailing_slashes'), 9);
		add_filter( 'paginate_links', array($this, 'control_trailing_slashes'), 9);

		/**
		 * Detect & canonical URL/redirect functions
		 */
		// Do not trigger in back-end
		if(is_admin()) { return false; }

		// Do not trigger if Customizer is loaded
		if(function_exists('is_customize_preview') && is_customize_preview()) { return false; }

		// Use the URIs set in this plugin
		add_filter( 'request', array($this, 'detect_post'), 0, 1 );

		// Redirect from old URIs to new URIs  + adjust canonical redirect settings
		add_action( 'template_redirect', array($this, 'new_uri_redirect_and_404'), 1);
		add_action( 'wp', array($this, 'adjust_canonical_redirect'), 0, 1);

		// Case insensitive permalinks
		if(!empty($permalink_manager_options['general']['case_insensitive_permalinks'])) {
			add_action( 'parse_request', array($this, 'case_insensitive_permalinks'), 0);
		}
		// Force 404 on non-existing pagination pages
		if(!empty($permalink_manager_options['general']['pagination_redirect'])) {
			add_action( 'wp', array($this, 'fix_pagination_pages'), 0);
		}
	}

	/**
	* The most important Permalink Manager function
	*/
	public static function detect_post($query, $request_url = false, $return_object = false) {
		global $wpdb, $wp, $wp_rewrite, $permalink_manager, $permalink_manager_uris, $wp_filter, $permalink_manager_options, $pm_query;

		// Check if the array with custom URIs is set
		if(!(is_array($permalink_manager_uris))) return $query;

		// Used in debug mode & endpoints
		$old_query = $query;

		/**
		* 1. Prepare URL and check if it is correct (make sure that both requested URL & home_url share the same protoocl and get rid of www prefix)
		*/
		$request_url = (!empty($request_url)) ? parse_url($request_url, PHP_URL_PATH) : $_SERVER['REQUEST_URI'];
		$request_url = strtok($request_url, "?");

		$http_host = (!empty($_SERVER['HTTP_HOST'])) ? $_SERVER['HTTP_HOST'] : preg_replace('/www\./i', '', $_SERVER['SERVER_NAME']);
		$request_url = sprintf("http://%s%s", str_replace("www.", "", $http_host), $request_url);
		$raw_home_url = trim(get_option('home'));
		$home_url = preg_replace("/http(s)?:\/\/(www\.)?(.+?)\/?$/", "http://$3", $raw_home_url);

		if(filter_var($request_url, FILTER_VALIDATE_URL)) {
			// Check if "Deep Detect" is enabled
			$deep_detect_enabled = apply_filters('permalink_manager_deep_uri_detect', $permalink_manager_options['general']['deep_detect']);

			// Sanitize the URL
			// $request_url = filter_var($request_url, FILTER_SANITIZE_URL);

			// Keep only the URI
			$request_url = str_replace($home_url, "", $request_url);

			// Hotfix for language plugins
			if(filter_var($request_url, FILTER_VALIDATE_URL)) {
				$request_url = parse_url($request_url, PHP_URL_PATH);
			}

			$request_url = trim($request_url, "/");

			// Get all the endpoints & pattern
			$endpoints = Permalink_Manager_Helper_Functions::get_endpoints();
			$pattern = "/^(.+?)(?|\/({$endpoints})(?|\/(.*)|$)|\/()([\d]+)\/?)?$/i";

			// Use default REGEX to detect post
			preg_match($pattern, $request_url, $regex_parts);
			$uri_parts['lang'] = false;
			$uri_parts['uri'] = (!empty($regex_parts[1])) ? $regex_parts[1] : "";
			$uri_parts['endpoint'] = (!empty($regex_parts[2])) ? $regex_parts[2] : "";
			$uri_parts['endpoint_value'] = (!empty($regex_parts[3])) ? $regex_parts[3] : "";

			// Allow to filter the results by third-parties + store the URI parts with $pm_query global
			$uri_parts = apply_filters('permalink_manager_detect_uri', $uri_parts, $request_url, $endpoints);

			// Support comment pages
			preg_match("/(.*)\/{$wp_rewrite->comments_pagination_base}-([\d]+)/", $uri_parts['uri'], $regex_parts);
			if(!empty($regex_parts[2])) {
				$uri_parts['uri'] = $regex_parts[1];
				$uri_parts['endpoint'] = 'cpage';
				$uri_parts['endpoint_value'] = $regex_parts[2];
			}

			// Support pagination endpoint
			if($uri_parts['endpoint'] == $wp_rewrite->pagination_base) {
				$uri_parts['endpoint'] = 'page';
			}

			// Stop the function if $uri_parts is empty
			if(empty($uri_parts)) return $query;

			// Store the URI parts in a separate global variable
			$pm_query = $uri_parts;

			// Get the URI parts from REGEX parts
			$lang = $uri_parts['lang'];
			$uri = $uri_parts['uri'];
			$endpoint = $uri_parts['endpoint'];
			$endpoint_value = $uri_parts['endpoint_value'];

			// Trim slashes
			$uri = trim($uri, "/");

			// Ignore URLs with no URI grabbed
			if(empty($uri)) return $query;

			// Store an array with custom permalinks in a separate variable
			$all_uris = $permalink_manager_uris;

			// Check what content type should be loaded in case of duplicate ("posts" or "terms")
			$duplicates_priority = apply_filters('permalink_manager_duplicates_priority', false);
			if($duplicates_priority !== false) {
				$uri_count = array_count_values($all_uris);

				foreach($uri_count as $duplicated_uri => $count) {
					if($count <= 1) { continue; }

					$duplicates_ids = array_keys($all_uris, $duplicated_uri);

					foreach($duplicates_ids as $id) {
						if($duplicates_priority == 'posts' && !is_numeric($id)) {
							unset($all_uris[$id]);
						} else if($duplicates_priority !== 'posts' && is_numeric($id)) {
							unset($all_uris[$id]);
						}
					}
				}
			}

			// Exclude draft posts
			$exclude_drafts = (isset($permalink_manager_options['general']['ignore_drafts'])) ? $permalink_manager_options['general']['ignore_drafts'] : false;
			$exclude_drafts = apply_filters('permalink_manager_exclude_drafts', $exclude_drafts);

			if($exclude_drafts) {
				$post_ids = $wpdb->get_col("SELECT DISTINCT ID FROM {$wpdb->posts} AS p WHERE p.post_status = 'draft' ORDER BY ID DESC");
				if(!empty($post_ids)) {
					foreach($post_ids as $post_id) {
						unset($permalink_manager_uris[$post_id]);
					}
				}
			}

			// Flip array for better performance
			$all_uris = array_flip($all_uris);

			// Attempt 1.
			// Find the element ID
			$element_id = isset($all_uris[$uri]) ? $all_uris[$uri] : false;

			// Atempt 2.
			// Decode both request URI & URIs array & make them lowercase (and save in a separate variable)
			if(empty($element_id)) {
				$uri = strtolower(urldecode($uri));

				foreach($all_uris as $raw_uri => $uri_id) {
					$raw_uri = urldecode($raw_uri);
					$all_uris[$raw_uri] = $uri_id;
				}

				// Convert array keys lowercase
				$all_uris = array_change_key_case($all_uris, CASE_LOWER);

				$element_id = isset($all_uris[$uri]) ? $all_uris[$uri] : $element_id;
			}

			// Atempt 3.
			// Check again in case someone used post/tax IDs instead of slugs
			if($deep_detect_enabled && is_numeric($endpoint_value) && isset($all_uris["{$uri}/{$endpoint_value}"])) {
				$element_id = $all_uris["{$uri}/{$endpoint_value}"];
				$endpoint_value = $endpoint = "";
			}

			// Atempt 4.
			// Check again for attachment custom URIs
			if(empty($element_id) && isset($old_query['attachment'])) {
				$element_id = isset($all_uris["{$uri}/{$endpoint}/{$endpoint_value}"]) ? $all_uris["{$uri}/{$endpoint}/{$endpoint_value}"] : $element_id;

				if($element_id) {
					$endpoint_value = $endpoint = "";
				}
			}

			// Allow to filter the item_id by third-parties after initial detection
			$element_id = apply_filters('permalink_manager_detected_element_id', $element_id, $uri_parts, $request_url);

			// Clear the original query before it is filtered
			$query = ($element_id) ? array() : $query;

			/**
			* 3A. Custom URI assigned to taxonomy
			*/
			if(strpos($element_id, 'tax-') !== false) {
				// Remove the "tax-" prefix
				$term_id = intval(preg_replace("/[^0-9]/", "", $element_id));

				// Filter detected post ID
				$term_id = apply_filters('permalink_manager_detected_term_id', intval($term_id), $uri_parts, true);

				// Get the variables to filter wp_query and double-check if taxonomy exists
				$term = get_term($term_id);
				$term_taxonomy = (!empty($term->taxonomy)) ? $term->taxonomy : false;

				// Check if term is allowed
				$disabled = ($term_taxonomy && Permalink_Manager_Helper_Functions::is_term_excluded($term)) ? true : false;

				// Proceed only if the term is not removed and its taxonomy is not disabled
				if(!$disabled && $term_taxonomy) {
					// Get some term data
					if($term_taxonomy == 'category') {
						$query_parameter = 'category_name';
					} else if($term_taxonomy == 'post_tag') {
						$query_parameter = 'tag';
					} else {
						$query["taxonomy"] = $term_taxonomy;
						$query_parameter = $term_taxonomy;
					}
					$term_ancestors = get_ancestors($term_id, $term_taxonomy);
					$final_uri = $term->slug;

					// Fix for hierarchical terms
					if(!empty($term_ancestors)) {
						foreach ($term_ancestors as $parent_id) {
							$parent = get_term((int) $parent_id, $term_taxonomy);
							if(!empty($parent->slug)) {
								$final_uri = $parent->slug . '/' . $final_uri;
							}
						}
					}

					//$query["term"] = $final_uri;
					$query["term"] = $term->slug;
					//$query[$query_parameter] = $final_uri;
					$query[$query_parameter] = $term->slug;
				} else if($disabled) {
					$broken_uri = true;
					$query = $old_query;
				} else {
					$query = $old_query;
				}
			}
			/**
			* 3B. Custom URI assigned to post/page/cpt item
			*/
			else if(isset($element_id) && is_numeric($element_id)) {
				// Fix for revisions
				$is_revision = wp_is_post_revision($element_id);
				if($is_revision) {
					$revision_id = $element_id;
					$element_id = $is_revision;
				}

				// Filter detected post ID
				$element_id = apply_filters('permalink_manager_detected_post_id', $element_id, $uri_parts);

				$post_to_load = get_post($element_id);
				$final_uri = (!empty($post_to_load->post_name)) ? $post_to_load->post_name : false;
				$post_type = (!empty($post_to_load->post_type)) ? $post_to_load->post_type : false;

				// Check if post is allowed
				$disabled = ($post_type && Permalink_Manager_Helper_Functions::is_post_excluded($post_to_load)) ? true : false;

				// Proceed only if the term is not removed and its taxonomy is not disabled
				if(!$disabled && $post_type) {
					$post_type_object = get_post_type_object($post_type);

					// Fix for hierarchical CPT & pages
					if(!(empty($post_to_load->ancestors)) && !empty($post_type_object->hierarchical)) {
						foreach ($post_to_load->ancestors as $parent) {
							$parent = get_post($parent);
							if($parent && $parent->post_name) {
								$final_uri = $parent->post_name . '/' . $final_uri;
							}
						}
					}

					// Alter the final query array
					if($post_to_load->post_status == 'private' && (!is_user_logged_in() || current_user_can('read_private_posts', $element_id) !== true)) {
						$element_id = null;
						$query = $old_query;
					} else if($post_to_load->post_status == 'draft' || empty($final_uri)) {
						if(is_user_logged_in()) {
							if($post_type == 'page') {
								$query['page_id'] = $element_id;
							} else {
								$query['p'] = $element_id;
							}

							$query['preview'] = true;
							$query['post_type'] = $post_type;
						} else if($post_to_load->post_status == 'draft') {
							$query['pagename'] = '-';
							$query['error'] = '404';

							$element_id = 0;
						} else {
							$query = $old_query;
						}
					} else if($post_type == 'page') {
						$query['pagename'] = $final_uri;
						// $query['post_type'] = $post_type;
					} else if($post_type == 'post') {
						$query['name'] = $final_uri;
					} else if($post_type == 'attachment') {
						$query['attachment'] = $final_uri;
					} else {
						// Get the query var
						$query_var = (!empty($post_type_object->query_var)) ? $post_type_object->query_var : $post_type;

						$query['name'] = $final_uri;
						$query['post_type'] = $post_type;
						$query[$query_var] = $final_uri;
					}
				} else if($disabled) {
					$broken_uri = true;
					$query = $old_query;
				} else {
					$query = $old_query;
				}
			}

			/**
			 * 4. Auto-remove removed term custom URI & redirects (works if enabled in plugin settings)
			 */
			if(!empty($broken_uri) && (!empty($permalink_manager_options['general']['auto_fix_duplicates'])) && $permalink_manager_options['general']['auto_fix_duplicates'] == 1) {
				// Do not trigger if WP Rocket cache plugin is turned on
				if(!defined('WP_ROCKET_VERSION') && is_array($permalink_manager_uris)) {
					$broken_element_id = (!empty($revision_id)) ? $revision_id : $element_id;
					$remove_broken_uri = (!empty($broken_element_id)) ? Permalink_Manager_Actions::force_clear_single_element_uris_and_redirects($broken_element_id) : '';

					// Reload page if success
					if($remove_broken_uri && !headers_sent()) {
						header("Refresh:0");
						exit();
					}
				}
			}

			/**
			 * 5A. Endpoints
			 */
			if(!empty($element_id) && (!empty($endpoint) || !empty($endpoint_value))) {
				if(is_array($endpoint)) {
					foreach($endpoint as $endpoint_name => $endpoint_value) {
						$query[$endpoint_name] = $endpoint_value;
					}
				} else if($endpoint == 'feed') {
					$query[$endpoint] = 'feed';
				} else if($endpoint == 'embed') {
					$query[$endpoint] = true;
				} else if($endpoint == 'page') {
					$endpoint = 'paged';
					if(is_numeric($endpoint_value)) {
						$query[$endpoint] = $endpoint_value;
					} else {
						$query = $old_query;
					}
				} else if($endpoint == 'trackback') {
					$endpoint = 'tb';
					$query[$endpoint] = 1;
				} else if(empty($endpoint) && is_numeric($endpoint_value)) {
					$query['page'] = $endpoint_value;
				} else {
					$query[$endpoint] = $endpoint_value;
				}

				// Fix for attachments
				if(!empty($query['attachment'])) {
					$query = array('attachment' => $query['attachment'], 'do_not_redirect' => 1);
				}
			}

			/**
			 * 5B. Endpoints - check if any endpoint is set with $_GET parameter
			 */
			if(!empty($element_id) && $deep_detect_enabled && !empty($_GET)) {
				$get_endpoints = array_intersect($wp->public_query_vars, array_keys($_GET));

				if(!empty($get_endpoints)) {
					// Append query vars from $_GET parameters
					foreach($get_endpoints as $endpoint) {
						// Numeric endpoints
						$endpoint_value = (in_array($endpoint, array('page', 'paged', 'attachment_id'))) ? filter_var($_GET[$endpoint], FILTER_SANITIZE_NUMBER_INT) : $_GET[$endpoint];

						// Ignore page endpoint if its value is 1
						if(in_array($endpoint, array('page', 'paged')) && $endpoint_value == 1) { continue; }

						$query[$endpoint] = sanitize_text_field($endpoint_value);
					}
				}
			}

			/**
			 * 6. Set global with detected item id
			 */
			if(!empty($element_id)) {
				$pm_query['id'] = $element_id;

				// Make the redirects more clever - see new_uri_redirect_and_404() method
				$query['do_not_redirect'] = 1;
			}
		}

		/**
		 * 7. Debug data
		 */
		if(!empty($term_taxonomy)) {
			$content_type = "Taxonomy: {$term_taxonomy}";
		} else if(!empty($post_type)) {
			$content_type = "Post type: {$post_type}";
		} else {
			$content_type = '';
		}
		$uri_parts = (!empty($uri_parts)) ? $uri_parts : '';
		$query = apply_filters('permalink_manager_filter_query', $query, $old_query, $uri_parts, $pm_query, $content_type);

		if($return_object && !empty($term)) {
			return $term;
		} else if($return_object && !empty($post_to_load)) {
			return $post_to_load;
		} else {
			return $query;
		}
	}

	/**
	 * Trailing slash & remove BOM and double slashes
	 */
	static function control_trailing_slashes($permalink) {
		global $permalink_manager_options;

		// Ignore empty permalinks
		if(empty($permalink)) { return $permalink; }

		// Keep the original permalink in a separate variable
		$original_permalink = $permalink;

		$trailing_slash_setting = (!empty($permalink_manager_options['general']['trailing_slashes'])) ? $permalink_manager_options['general']['trailing_slashes'] : "";

		// Remove trailing slashes from URLs that end with file extension (eg. .html)
		if(preg_match('/(http(?:s)?:\/\/(?:[^\/]+)\/.*\.([a-zA-Z]{3,4}))\/?$/', $permalink)) {
			$permalink = preg_replace('/^(?!http(?:s):\/\/[^\/]+\/$)(.+?)([\/]*)(\[\?\#][^\/]+|$)/', '$1$3', $permalink); // Instead of untrailingslashit()
		} else {
			// Add trailing slashes
			if(in_array($trailing_slash_setting, array(1, 10))) {
				$permalink = preg_replace('/(.+?)([\/]*)(\[\?\#][^\/]+|$)/', '$1/$3', $permalink); // Instead of trailingslashit()
			}
			// Remove trailing slashes
			else if(in_array($trailing_slash_setting, array(2, 20))) {
				$permalink = preg_replace('/(.+?)([\/]*)(\[\?\#][^\/]+|$)/', '$1$3', $permalink); // Instead of untrailingslashit()
			}
			// Default settings
			else {
				$permalink = user_trailingslashit($permalink);
			}
		}

		// Remove double slashes
		$permalink = preg_replace('/([^:])(\/{2,})/', '$1/', $permalink);

		// Remove trailing slashes from URLs that end with query string or anchors
		$permalink = preg_replace('/([\?\#]{1}[^\/]+)([\/]+)$/', '$1', $permalink);

		return apply_filters('permalink_manager_control_trailing_slashes', $permalink, $original_permalink);
	}

	/**
	 * Display 404 if requested page does not exist in pagination
	 */
	function fix_pagination_pages() {
		global $wp_query, $pm_query;

		// 1. Get the queried object
		$post = get_queried_object();

		// 2. Check if post object is defined
		if((!empty($post->post_type) && !empty($post->post_content)) || (!empty($wp_query->max_num_pages))) {
			// 2A. Check if pagination is detected
			$current_page = (!empty($wp_query->query_vars['page'])) ? $wp_query->query_vars['page'] : 1;
			$current_page = (empty($wp_query->query_vars['page']) && !empty($wp_query->query_vars['paged'])) ? $wp_query->query_vars['paged'] : $current_page;

			// 2B. Count post pages
			$post_content = (!empty($post->post_content)) ? $post->post_content : '';
			$num_pages = (is_home() || is_archive()) ? $wp_query->max_num_pages : substr_count(strtolower($post_content), '<!--nextpage-->') + 1;

			$is_404 = ($current_page > 1 && ($current_page > $num_pages)) ? true : false;
		}
		// 3. Force 404 if no posts are loaded
		else if(!empty($wp_query->query['paged']) && $wp_query->post_count == 0) {
			$is_404 = true;
		}
		// 4. Force 404 if endpoint value is not set
		else if(!empty($pm_query['endpoint']) && $pm_query['endpoint'] == 'page' && empty($pm_query['endpoint_value'])) {
			$is_404 = true;
		}

		// 5. Block non-existent pages (Force 404 error)
		if(!empty($is_404)) {
			$wp_query->query = $wp_query->queried_object = $wp_query->queried_object_id = null;
			$wp_query->set_404();
			status_header(404);
			nocache_headers();
			$pm_query = '';
		}
	}

	/**
	 * Redirects
	 */
	function new_uri_redirect_and_404() {
 		global $wp_query, $wp, $wp_rewrite, $wpdb, $permalink_manager_uris, $permalink_manager_redirects, $permalink_manager_external_redirects, $permalink_manager_options, $pm_query;

		// Get the redirection mode & trailing slashes settings
		$redirect_mode = (!empty($permalink_manager_options['general']['redirect'])) ? $permalink_manager_options['general']['redirect'] : false;
		$trailing_slashes_mode = (!empty($permalink_manager_options['general']['trailing_slashes'])) ? $permalink_manager_options['general']['trailing_slashes'] : false;
		$trailing_slashes_redirect = (!empty($permalink_manager_options['general']['trailing_slashes_redirect'])) ? $permalink_manager_options['general']['trailing_slashes_redirect'] : false;
		$extra_redirects = (!empty($permalink_manager_options['general']['extra_redirects'])) ? $permalink_manager_options['general']['extra_redirects'] : false;
		$canonical_redirect = (!empty($permalink_manager_options['general']['canonical_redirect'])) ? $permalink_manager_options['general']['canonical_redirect'] : false;
		$old_slug_redirect = (!empty($permalink_manager_options['general']['old_slug_redirect'])) ? $permalink_manager_options['general']['old_slug_redirect'] : false;
		$endpoint_redirect = (!empty($permalink_manager_options['general']['endpoint_redirect'])) ? $permalink_manager_options['general']['endpoint_redirect'] : false;
		$pagination_redirect = (!empty($permalink_manager_options['general']['pagination_redirect'])) ? $permalink_manager_options['general']['pagination_redirect'] : false;
		$copy_query_redirect = (!empty($permalink_manager_options['general']['copy_query_redirect'])) ? $permalink_manager_options['general']['copy_query_redirect'] : false;
		$redirect_type = '-';

		// Get home URL
		$home_url = rtrim(get_option('home'), "/");
		$home_dir = parse_url($home_url, PHP_URL_PATH);

		// Set up $correct_permalink variable
		$correct_permalink = '';

		// Get query string & URI
		$query_string = ($copy_query_redirect && !empty($_SERVER['QUERY_STRING'])) ? $_SERVER['QUERY_STRING'] : '';
		$old_uri = $_SERVER['REQUEST_URI'];

		// Fix for WP installed in directories (remove the directory name from the URI)
		if(!empty($home_dir)) {
			$home_dir_regex = preg_quote(trim($home_dir), "/");
			$old_uri = preg_replace("/{$home_dir_regex}/", "", $old_uri, 1);
		}

		// Do not use custom redirects on author pages, search & front page
		if(!is_author() && !is_front_page() && !is_home() && !is_feed() && !is_search() && empty($_GET['s'])) {
			// Sometimes $wp_query indicates the wrong object if requested directly
			$queried_object = get_queried_object();

			// Unset 404 if custom URI is detected
			if(isset($pm_query['id']) && (empty($queried_object->post_status) || $queried_object->post_status !== 'private')) {
				$wp_query->is_404 = false;
			}

			/**
			* 1A. External redirect
			*/
			if(!empty($pm_query['id']) && !empty($permalink_manager_external_redirects[$pm_query['id']])) {
				$external_url = $permalink_manager_external_redirects[$pm_query['id']];

				if(filter_var($external_url, FILTER_VALIDATE_URL)) {
					// Allow redirect
					$wp_query->query_vars['do_not_redirect'] = 0;

					wp_redirect($external_url, 301, PERMALINK_MANAGER_PLUGIN_NAME);
					exit();
				}
			}

			/**
			 * 1B. Custom redirects
			 */
			if(empty($wp_query->query_vars['do_not_redirect']) && $extra_redirects && !empty($permalink_manager_redirects) && is_array($permalink_manager_redirects) && !empty($wp->request) && !empty($pm_query['uri'])) {
				$uri = $pm_query['uri'];
				$endpoint_value = $pm_query['endpoint_value'];

				// Make sure that URIs with non-ASCII characters are also detected + Check the URLs that end with number
				$decoded_url = urldecode($uri);
				$endpoint_url = "{$uri}/{$endpoint_value}";

				// Convert to lowercase to make case insensitive
				$force_lowercase = apply_filters('permalink_manager_force_lowercase_uris', true);

				if($force_lowercase) {
					$uri = strtolower($uri);
					$decoded_url = strtolower($decoded_url);
					$endpoint_url = strtolower($endpoint_url);
				}

				// Check if the URI is not assigned to any post/term's redirects
				foreach($permalink_manager_redirects as $element => $redirects) {
					if(!is_array($redirects)) { continue; }

					if(in_array($uri, $redirects) || in_array($decoded_url, $redirects) || (is_numeric($endpoint_value) && in_array($endpoint_url, $redirects))) {
						// Post is detected
						if(is_numeric($element)) {
							$correct_permalink = get_permalink($element);
						}
						// Term is detected
						else {
							$term_id = intval(preg_replace("/[^0-9]/", "", $element));
							$correct_permalink = get_term_link($term_id);
						}

						// The custom redirect is found so there is no need to query the rest of array
						continue;
					}
				}

				$redirect_type = (!empty($correct_permalink)) ? 'custom_redirect' : $redirect_type;
			}

			// Ignore WP-Content links
			if(!empty($_SERVER['REQUEST_URI']) && (strpos($_SERVER['REQUEST_URI'], '/wp-content') !== false)) { return false; }

			/**
			 * 1C. Pagination redirect
			 */
			if($pagination_redirect && ((isset($wp_query->query_vars['paged']) && $wp_query->query_vars['paged'] == 1) || (isset($wp_query->query_vars['page']) && $wp_query->query_vars['page'] == 1 && !empty($pm_query['endpoint_value'])))) {
				$pm_query['endpoint'] = $pm_query['endpoint_value'] = '';
				$wp_query->query_vars['do_not_redirect'] = 0;
			}

			/**
			 * 1D. Enhance native redirect
			 */
			if($canonical_redirect && empty($wp_query->query_vars['do_not_redirect']) && !empty($queried_object) && empty($correct_permalink)) {
				// Affect only posts with custom URI and old URIs
				if(!empty($queried_object->ID) && isset($permalink_manager_uris[$queried_object->ID]) && empty($wp_query->query['preview'])) {
					// Ignore posts with specific statuses
					if(!(empty($queried_object->post_status)) && in_array($queried_object->post_status, array('draft', 'pending', 'auto-draft', 'future'))) {
						return;
					}

					// Check if the post is excluded
					if(Permalink_Manager_Helper_Functions::is_post_excluded($queried_object)) { return; }

					// Get the real URL
					$correct_permalink = get_permalink($queried_object->ID);
				}
				// Affect only terms with custom URI and old URIs
				else if(!empty($queried_object->term_id) && isset($permalink_manager_uris["tax-{$queried_object->term_id}"]) && defined('PERMALINK_MANAGER_PRO')) {
					// Check if the term is excluded
					if(Permalink_Manager_Helper_Functions::is_term_excluded($queried_object)) { return; }

					// Get the real URL
					$correct_permalink = get_term_link($queried_object->term_id, $queried_object->taxonomy);
				}

				$redirect_type = (!empty($correct_permalink)) ? 'native_redirect' : $redirect_type;
			}

			/**
			 * 1E. Old slug redirect
			 */
			if($old_slug_redirect && !empty($pm_query['uri']) && empty($wp_query->query_vars['do_not_redirect']) && is_404() && empty($correct_permalink)) {
				$slug = basename($pm_query['uri']);

				$post_id = $wpdb->get_var($wpdb->prepare("SELECT post_id from {$wpdb->postmeta} WHERE meta_key = '_wp_old_slug' AND meta_value = %s", $slug));
				if(!empty($post_id)) {
					$correct_permalink = get_permalink($post_id);
					$redirect_type = 'old_slug_redirect';
				}
			}

			/**
			 * 2. Check if URL contains duplicated slashes
			 */
			if(!empty($old_uri) && ($old_uri != '/') && preg_match('/\/{2,}/', $old_uri)) {
				$new_uri = ltrim(preg_replace('/([^:])([\/]+)/', '$1/', $old_uri), "/");
				$correct_permalink = "{$home_url}/{$new_uri}";
			}

			/**
			 * 3. Prevent redirect loop
			 */
			if(!empty($correct_permalink) && is_string($correct_permalink) && !empty($wp->request) && !empty($redirect_type) && $redirect_type !== 'slash_redirect') {
				$current_uri = trim($wp->request, "/");
				$redirect_uri = trim(parse_url($correct_permalink, PHP_URL_PATH), "/");

				$correct_permalink = ($redirect_uri == $current_uri) ? null : $correct_permalink;
			}

			/**
			 * 4. Add endpoints to redirect URL
			 */
			if(!empty($correct_permalink) && $endpoint_redirect && ($redirect_type !== 'slash_redirect') && (!empty($pm_query['endpoint_value']) || !empty($pm_query['endpoint']))) {
				$endpoint_value = $pm_query['endpoint_value'];

				if(empty($pm_query['endpoint']) && is_numeric($endpoint_value)) {
					$correct_permalink = sprintf("%s/%d", trim($correct_permalink, "/"), $endpoint_value);
				} else if(isset($pm_query['endpoint']) && !empty($endpoint_value)) {
					if($pm_query['endpoint'] == 'cpage') {
						$correct_permalink = sprintf("%s/%s-%s", trim($correct_permalink, "/"), $wp_rewrite->comments_pagination_base, $endpoint_value);
					} else {
						$correct_permalink = sprintf("%s/%s/%s", trim($correct_permalink, "/"), $pm_query['endpoint'], $endpoint_value);
					}
				} else {
					$correct_permalink = sprintf("%s/%s", trim($correct_permalink, "/"), $pm_query['endpoint']);
				}
			}
		} else {
			$queried_object = '-';
		}

		/**
		 * 5. Check trailing slashes (ignore links with query parameters)
		 */
		if($trailing_slashes_mode && $trailing_slashes_redirect && empty($correct_permalink) && empty($_SERVER['QUERY_STRING']) && !empty($_SERVER['REQUEST_URI']) && !is_front_page()) {
			// Check if $old_uri ends with slash or not
			$ends_with_slash = (substr($old_uri, -1) == "/") ? true : false;
			$trailing_slashes_mode = (preg_match("/.*\.([a-zA-Z]{3,4})\/?$/", $old_uri) && $trailing_slashes_mode == 1) ? 2 : $trailing_slashes_mode;

			// Ignore empty URIs
			if($old_uri != "/") {
				// 2A. Force trailing slashes
				if($trailing_slashes_mode == 1 && $ends_with_slash == false) {
					$correct_permalink = sprintf("%s/%s/", rtrim($home_url, '/'), trim($old_uri, '/'));
				}
				// 2B. Remove trailing slashes
				else if($trailing_slashes_mode == 2 && $ends_with_slash == true) {
					$correct_permalink = sprintf("%s/%s", rtrim($home_url, '/'), trim($old_uri, '/'));
				}
				// 2C. Remove duplicated trailing slashes
				else if($trailing_slashes_mode == 1 && preg_match('/[\/]{2,}$/', $old_uri)) {
					$correct_permalink = sprintf("%s/%s/", rtrim($home_url, '/'), trim($old_uri, '/'));
				}
			}

			$redirect_type = (!empty($correct_permalink)) ? 'slash_redirect' : '-';
		}

		/**
		 * 6. WWW prefix | SSL mismatch redirect
		 */
		if(!empty($permalink_manager_options['general']['sslwww_redirect'])) {
			$home_url_has_www = (strpos($home_url, 'www.') !== false) ? true : false;
			$requested_url_has_www = (strpos($_SERVER['HTTP_HOST'], 'www.') !== false) ? true : false;
			$home_url_has_ssl = (strpos($home_url, 'https') !== false) ? true : false;
			$requested_url_has_ssl = is_ssl();

			if(($home_url_has_www !== $requested_url_has_www) || ($home_url_has_ssl !== $requested_url_has_ssl)) {
				$correct_permalink = "{$home_url}/{$old_uri}";
				$redirect_type = 'www_redirect';
			}
		}

		/**
		 * 7. Debug redirect
		 */
		$correct_permalink = apply_filters('permalink_manager_filter_redirect', $correct_permalink, $redirect_type, $queried_object);

		/**
		 * 8. Ignore default URIs (or do nothing if redirects are disabled)
		 */
		if(!empty($correct_permalink) && is_string($correct_permalink) && !empty($redirect_mode)) {
			// Allow redirect
			$wp_query->query_vars['do_not_redirect'] = 0;

			// Append query string
			$correct_permalink = (!empty($query_string)) ? sprintf("%s?%s", strtok($correct_permalink, "?"), $query_string) : $correct_permalink;

			// Adjust trailing slashes
			$correct_permalink = self::control_trailing_slashes($correct_permalink);

			wp_safe_redirect($correct_permalink, $redirect_mode, PERMALINK_MANAGER_PLUGIN_NAME);
			exit();
		}
 	}

	function adjust_canonical_redirect() {
 		global $permalink_manager_options, $permalink_manager_uris, $wp, $wp_rewrite;

		// Adjust rewrite settings for trailing slashes
		$trailing_slash_setting = (!empty($permalink_manager_options['general']['trailing_slashes'])) ? $permalink_manager_options['general']['trailing_slashes'] : "";
		if(in_array($trailing_slash_setting, array(1, 10))) {
			$wp_rewrite->use_trailing_slashes = true;
		} else if(in_array($trailing_slash_setting, array(2, 20))) {
			$wp_rewrite->use_trailing_slashes = false;
		}

		// Get endpoints
		$endpoints = Permalink_Manager_Helper_Functions::get_endpoints();
		$endpoints_array = ($endpoints) ? explode("|", $endpoints) : array();

		// Check if any endpoint is called (fix for feed and similar endpoints)
		foreach($endpoints_array as $endpoint) {
			if(!empty($wp->query_vars[$endpoint]) && !in_array($endpoint, array('attachment', 'page', 'paged'))) {
				$wp->query_vars['do_not_redirect'] = 1;
				break;
			}
		}

		// Do nothing for posts and terms without custom URIs (when canonical redirect is enabled)
		if(is_singular() || is_tax() || is_category() || is_tag()) {
			$element = get_queried_object();
			if(!empty($element->ID)) {
				$custom_uri = (!empty($permalink_manager_uris[$element->ID])) ? $permalink_manager_uris[$element->ID] : "";
			} else if(!empty($element->term_id)) {
				$custom_uri = (!empty($permalink_manager_uris["tax-{$element->term_id}"])) ? $permalink_manager_uris["tax-{$element->term_id}"] : "";
			}
		}

 		if(empty($permalink_manager_options['general']['canonical_redirect'])) {
 			remove_action('template_redirect', 'redirect_canonical');
 		}

		if(empty($permalink_manager_options['general']['old_slug_redirect'])) {
			remove_action('template_redirect', 'wp_old_slug_redirect');
		}

		if(!empty($wp->query_vars['do_not_redirect'])) {
			// RankMath
			remove_action('template_redirect', 'do_redirection', 11);
			remove_action('wp', 'do_redirection', 11);

			// SEOPress
			remove_action('template_redirect', 'seopress_category_redirect', 1);

			remove_action('template_redirect', 'wp_old_slug_redirect');
			remove_action('template_redirect', 'redirect_canonical');
			add_filter('wpml_is_redirected', '__return_false', 99, 2);
			add_filter('pll_check_canonical_url', '__return_false', 99, 2);
		}
 	}

	/**
	 * Case insensitive permalinks
	 */
	function case_insensitive_permalinks() {
		global $permalink_manager_options, $permalink_manager_uris;

		if(!empty($_SERVER['REQUEST_URI'])) {
			$_SERVER['REQUEST_URI'] = strtolower($_SERVER['REQUEST_URI']);
			$permalink_manager_uris = array_map('strtolower', $permalink_manager_uris);
		}
	}

}
