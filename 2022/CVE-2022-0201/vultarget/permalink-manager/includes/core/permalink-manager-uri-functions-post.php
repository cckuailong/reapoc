<?php

/**
* Additional functions used in classes and another subclasses
*/
class Permalink_Manager_URI_Functions_Post extends Permalink_Manager_Class {

	public function __construct() {
		add_action( 'admin_init', array($this, 'admin_init'), 99, 3);

		add_filter( '_get_page_link', array($this, 'custom_post_permalinks'), 99, 2);
		add_filter( 'page_link', array($this, 'custom_post_permalinks'), 99, 2);
		add_filter( 'post_link', array($this, 'custom_post_permalinks'), 99, 2);
		add_filter( 'post_type_link', array($this, 'custom_post_permalinks'), 99, 2);
		add_filter( 'attachment_link', array($this, 'custom_post_permalinks'), 99, 2);

		add_filter( 'permalink_manager_uris', array($this, 'exclude_homepage'), 99);

		// Support url_to_postid
		add_filter( 'url_to_postid', array($this, 'url_to_postid'), 999);

		/**
		 * URI Editor
		 */
		add_filter( 'get_sample_permalink_html', array($this, 'edit_uri_box'), 10, 5 );

		add_action( 'save_post', array($this, 'update_post_uri'), 99, 1);
		add_action( 'edit_attachment', array($this, 'update_post_uri'), 99, 1 );
		add_action( 'wp_insert_post', array($this, 'new_post_uri'), 99, 1 );
		add_action( 'add_attachment', array($this, 'new_post_uri'), 99, 1 );
		add_action( 'wp_trash_post', array($this, 'remove_post_uri'), 100, 1 );
		add_action( 'delete_post', array($this, 'remove_post_uri'), 100, 1 );

		add_action( 'quick_edit_custom_box', array($this, 'quick_edit_column_form'), 99, 3);
	}

	/**
	 * Init
	 */
	function admin_init() {
 		$post_types = Permalink_Manager_Helper_Functions::get_post_types_array();

 		// Add "URI Editor" to "Quick Edit" for all post_types
 		foreach($post_types as $post_type => $label) {
 			add_filter( "manage_{$post_type}_posts_columns" , array($this, 'quick_edit_column') );
 			add_filter( "manage_{$post_type}_posts_custom_column" , array($this, 'quick_edit_column_content'), 10, 2 );
 		}
	}

	/**
	* Change permalinks for posts, pages & custom post types
	*/
	static function custom_post_permalinks($permalink, $post) {
		global $wp_rewrite, $permalink_manager_uris, $permalink_manager_options, $permalink_manager_ignore_permalink_filters;

		// Do not filter permalinks in Customizer
		if((function_exists('is_customize_preview') && is_customize_preview()) || !empty($_REQUEST['customize_url'])) { return $permalink; }

		// Do not filter in WPML String Editor
		if(!empty($_REQUEST['icl_ajx_action']) && $_REQUEST['icl_ajx_action'] == 'icl_st_save_translation') { return $permalink; }

		// WPML (prevent duplicated posts)
		if(!empty($_REQUEST['trid']) && !empty($_REQUEST['skip_sitepress_actions'])) { return $permalink; }

		// Do not run when metaboxes are loaded with Gutenberg
		if(!empty($_REQUEST['meta-box-loader']) && empty($_POST['custom_uri'])) { return $permalink; }

		// Do not filter if $permalink_manager_ignore_permalink_filters global is set
		if(!empty($permalink_manager_ignore_permalink_filters)) { return $permalink; }

		$post = (is_integer($post)) ? get_post($post) : $post;

		// Do not run if post object is invalid
		if(empty($post) || empty($post->ID) || empty($post->post_type)) { return $permalink; }

		// Start with homepage URL
		$home_url = Permalink_Manager_Helper_Functions::get_permalink_base($post);

		// Check if the post is excluded
		if(!empty($post->post_type) && Permalink_Manager_Helper_Functions::is_post_excluded($post) && $post->post_type !== 'attachment') { return $permalink; }

		// 2A. Do not change permalink of frontpage
		if(Permalink_Manager_Helper_Functions::is_front_page($post->ID)) {
			return $permalink;
		}
		// 2B. Do not change permalink for drafts and future posts (+ remove trailing slash from them)
		else if(in_array($post->post_status, array('draft', 'pending', 'auto-draft', 'future'))) {
			return $permalink;
		}

		// 3. Save the old permalink to separate variable
		$old_permalink = $permalink;

		// 4. Filter only the posts with custom permalink assigned
		if(isset($permalink_manager_uris[$post->ID])) {
			// Encode URI?
			if(!empty($permalink_manager_options['general']['decode_uris'])) {
				$permalink = "{$home_url}/" . rawurldecode("/{$permalink_manager_uris[$post->ID]}");
			} else {
				$permalink = "{$home_url}/" . Permalink_Manager_Helper_Functions::encode_uri("{$permalink_manager_uris[$post->ID]}");
			}
		} else if($post->post_type == 'attachment' && $post->post_parent > 0 && $post->post_parent != $post->ID && !empty($permalink_manager_uris[$post->post_parent])) {
			$permalink = "{$home_url}/{$permalink_manager_uris[$post->post_parent]}/attachment/{$post->post_name}";
		} else if(!empty($permalink_manager_options['general']['decode_uris'])) {
			$permalink = "{$home_url}/" . rawurldecode("/{$permalink}");
		}

		// 5. Allow to filter (do not filter in Customizer)
		if(!(function_exists('is_customize_preview') && is_customize_preview())) {
			return apply_filters('permalink_manager_filter_final_post_permalink', $permalink, $post, $old_permalink);
		} else {
			return $old_permalink;
		}
	}

	/**
	* Check if the provided slug is unique and then update it with SQL query.
	*/
	static function update_slug_by_id($slug, $id) {
		global $wpdb;

		// Update slug and make it unique
		$slug = (empty($slug)) ? get_the_title($id) : $slug;
		$slug = sanitize_title($slug);

		$new_slug = wp_unique_post_slug($slug, $id, get_post_status($id), get_post_type($id), null);
		$wpdb->query($wpdb->prepare("UPDATE {$wpdb->posts} SET post_name = %s WHERE ID = %d", $new_slug, $id));

		return $new_slug;
	}

	/**
	* Get the active URI
	*/
	public static function get_post_uri($post_id, $native_uri = false, $is_draft = false) {
		global $permalink_manager_uris;

		// Check if input is post object
		$post_id = (isset($post_id->ID)) ? $post_id->ID : $post_id;

		if(!empty($permalink_manager_uris[$post_id])) {
			$final_uri = $permalink_manager_uris[$post_id];
		} else if(!$is_draft) {
			$final_uri = self::get_default_post_uri($post_id, $native_uri);
		} else {
			$final_uri = '';
		}

		return $final_uri;
	}

	/**
	* Get the default (not overwritten by the user) or native URI (unfiltered)
	*/
	public static function get_default_post_uri($post, $native_uri = false, $check_if_disabled = false) {
		global $permalink_manager_options, $permalink_manager_uris, $permalink_manager_permastructs, $wp_post_types, $icl_adjust_id_url_filter_off;

		// Disable WPML adjust ID filter
		$icl_adjust_id_url_filter_off = true;

		// Load all bases & post
		$post = is_object($post) ? $post : get_post($post);

		// Check if post ID is defined (and front page permalinks should be empty)
		if(empty($post->ID) || Permalink_Manager_Helper_Functions::is_front_page($post->ID)) { return ''; }

		$post_id = $post->ID;
		$post_type = $post->post_type;
		$post_name = (empty($post->post_name)) ? Permalink_Manager_Helper_Functions::sanitize_title($post->post_title) : $post->post_name;

		// 1A. Check if post type is allowed
		if($check_if_disabled && Permalink_Manager_Helper_Functions::is_post_type_disabled($post_type)) { return ''; }

		// 1A. Get the native permastructure
		if($post_type == 'attachment') {
			$parent_page = ($post->post_parent > 0 && $post->post_parent != $post->ID) ? get_post($post->post_parent) : false;

			if(!empty($parent_page->ID)) {
				$parent_page_uri = (!empty($permalink_manager_uris[$parent_page->ID])) ? $permalink_manager_uris[$parent_page->ID] : get_page_uri($parent_page->ID);
			} else {
				$parent_page_uri = "";
			}

			$native_permastructure = ($parent_page) ? trim($parent_page_uri, "/") . "/attachment" : "";
		} else {
			$native_permastructure = Permalink_Manager_Helper_Functions::get_default_permastruct($post_type);
		}

		// 1B. Get the permastructure
		if($native_uri || empty($permalink_manager_permastructs['post_types'][$post_type])) {
			$permastructure = $native_permastructure;
		} else {
			$permastructure = apply_filters('permalink_manager_filter_permastructure', $permalink_manager_permastructs['post_types'][$post_type], $post);
		}

		// 1C. Set the permastructure
		$default_base = (!empty($permastructure)) ? trim($permastructure, '/') : "";

		// 2A. Get the date
		$date = explode(" ", date('Y m d H i s', strtotime($post->post_date)));
		$monthname = sanitize_title(date_i18n('F', strtotime($post->post_date)));

		// 2B. Get the author (if needed)
		$author = '';
		if(strpos($default_base, '%author%') !== false) {
			$authordata = get_userdata($post->post_author);
			$author = $authordata->user_nicename;
		}

		// 2C. Get the post type slug
		if(!empty($wp_post_types[$post_type])) {
			if(!empty($wp_post_types[$post_type]->rewrite['slug'])) {
				$post_type_slug = $wp_post_types[$post_type]->rewrite['slug'];
			} else if(is_string($wp_post_types[$post_type]->rewrite)) {
				$post_type_slug = $wp_post_types[$post_type]->rewrite;
			}
		}

		$post_type_slug = (!empty($post_type_slug)) ? $post_type_slug : $post_type;
		$post_type_slug = apply_filters('permalink_manager_filter_post_type_slug', $post_type_slug, $post, $post_type);
		$post_type_slug = preg_replace('/(%([^%]+)%\/?)/', '', $post_type_slug);

		// 3B. Get the full slug
		$post_name = Permalink_Manager_Helper_Functions::remove_slashes($post_name);
		$custom_slug = $full_custom_slug = Permalink_Manager_Helper_Functions::force_custom_slugs($post_name, $post);
		$full_native_slug = $post_name;

		// 3A. Fix for hierarchical CPT (start)
		// $full_slug = (is_post_type_hierarchical($post_type)) ? get_page_uri($post) : $post_name;
		if($post->ancestors && is_post_type_hierarchical($post_type)) {
			foreach($post->ancestors as $parent) {
        $parent = get_post($parent);
        if($parent && $parent->post_name) {
					$full_native_slug = $parent->post_name . '/' . $full_native_slug;
					$full_custom_slug = Permalink_Manager_Helper_Functions::force_custom_slugs($parent->post_name, $parent) . '/' . $full_custom_slug;
        }
    	}
		}

		// 3B. Allow filter the default slug (only custom permalinks)
		if(!$native_uri) {
			$full_slug = apply_filters('permalink_manager_filter_default_post_slug', $full_custom_slug, $post, $post_name);
		} else {
			$full_slug = $full_native_slug;
		}

		$post_type_tag = Permalink_Manager_Helper_Functions::get_post_tag($post_type);

		// 3C. Get the standard tags and replace them with their values
		$tags = array('%year%', '%monthnum%', '%monthname%', '%day%', '%hour%', '%minute%', '%second%', '%post_id%', '%author%', '%post_type%');
		$tags_replacements = array($date[0], $date[1], $monthname, $date[2], $date[3], $date[4], $date[5], $post->ID, $author, $post_type_slug);
		$default_uri = str_replace($tags, $tags_replacements, $default_base);

		// 3D. Get the slug tags
		$slug_tags = array($post_type_tag, "%postname%", "%postname_flat%", "%{$post_type}_flat%", "%native_slug%");
		$slug_tags_replacement = array($full_slug, $full_slug, $custom_slug, $custom_slug, $full_native_slug);

		// 3E. Check if any post tag is present in custom permastructure
		$do_not_append_slug = (!empty($permalink_manager_options['permastructure-settings']['do_not_append_slug']['post_types'][$post_type])) ? true : false;
		$do_not_append_slug = apply_filters("permalink_manager_do_not_append_slug", $do_not_append_slug, $post_type, $post);
		if($do_not_append_slug == false) {
			foreach($slug_tags as $tag) {
				if(strpos($default_uri, $tag) !== false) {
					$do_not_append_slug = true;
					break;
				}
			}
		}

		// 3F. Replace the post tags with slugs or rppend the slug if no post tag is defined
		if(!empty($do_not_append_slug)) {
			$default_uri = str_replace($slug_tags, $slug_tags_replacement, $default_uri);
		} else {
			$default_uri .= "/{$full_slug}";
		}

		// 4. Replace taxonomies
		$taxonomies = get_taxonomies();

		if($taxonomies) {
			foreach($taxonomies as $taxonomy) {
				// 0. Check if taxonomy tag is present
				if(strpos($default_uri, "%{$taxonomy}") === false) { continue; }

				// 1. Reset $replacement variable
				$replacement = $replacement_term = "";
				$terms = wp_get_object_terms($post->ID, $taxonomy);

				// 2. Sort the terms
				if(!empty($terms)) {
					$terms = wp_list_sort(
						$terms,
						array(
							'parent'  => 'DESC',
							'term_id' => 'ASC',
						)
					);
				}

				// 3A. Try to use Yoast SEO Primary Term
				$replacement_term = $primary_term = Permalink_Manager_Helper_Functions::get_primary_term($post->ID, $taxonomy, false);

				// 3B. Get the first assigned term to this taxonomy
				if(empty($replacement_term)) {
					$replacement_term = (!is_wp_error($terms) && !empty($terms) && is_object($terms[0])) ? Permalink_Manager_Helper_Functions::get_lowest_element($terms[0], $terms) : '';
					$replacement_term = apply_filters('permalink_manager_filter_post_terms', $replacement_term, $post, $terms, $taxonomy, $native_uri);
				}

				// 4A. Custom URI as term base
				if(!empty($replacement_term->term_id) && strpos($default_uri, "%{$taxonomy}_custom_uri%") !== false && !empty($permalink_manager_uris["tax-{$replacement_term->term_id}"])) {
					$mode = 1;
				}
				// 4B. Hierarhcical term base
				else if(!empty($replacement_term->term_id) && strpos($default_uri, "%{$taxonomy}_flat%") === false && strpos($default_uri, "%{$taxonomy}_top%") === false && is_taxonomy_hierarchical($taxonomy)) {
					$mode = 2;
				}
				// 4C. Force flat/non-hierarchical term base - get highest level term (if %taxonomy_top% tag is used)
				else if(strpos($default_uri, "%{$taxonomy}_top%") !== false) {
					$mode = 3;
				}
				// 4D. Force flat/non-hierarchical term base - get lowest level term (if %taxonomy_flat% tag is used)
				else {
					$mode = 4;
				}

				// Get the replacement slug (custom + native)
				$replacement = Permalink_Manager_Helper_Functions::get_term_full_slug($replacement_term, $terms, $mode, $native_uri);
				$native_replacement = Permalink_Manager_Helper_Functions::get_term_full_slug($replacement_term, $terms, $mode, true);

				// Trim slashes
				$replacement  = trim($replacement, '/');
				$native_replacement  = trim($native_replacement, '/');

				// Filter final category slug
				$replacement = apply_filters('permalink_manager_filter_term_slug', $replacement, $replacement_term, $post, $terms, $taxonomy, $native_uri);

				// 4. Do the replacement
				$default_uri = (!empty($replacement)) ? str_replace(array("%{$taxonomy}%", "%{$taxonomy}_flat%", "%{$taxonomy}_custom_uri%", "%{$taxonomy}_top%"), $replacement, $default_uri) : $default_uri;
				$default_uri = (!empty($native_replacement)) ? str_replace("%{$taxonomy}_native_slug%", $native_replacement, $default_uri) : $default_uri;
			}
		}

		// Enable WPML adjust ID filter
		$icl_adjust_id_url_filter_off = false;

		return apply_filters('permalink_manager_filter_default_post_uri', $default_uri, $post->post_name, $post, $post_name, $native_uri);
	}

	/**
	* The homepage should not use URI
	*/
	function exclude_homepage($uris) {
		// Find the homepage URI
		$homepage_id = get_option('page_on_front');

		if(is_array($uris) && !empty($uris[$homepage_id])) {
			unset($uris[$homepage_id]);
		}

		return $uris;
	}

	/**
	 * Support url_to_postid
	 */
	public function url_to_postid($url) {
		global $pm_query;

		// Filter only defined URLs
		if(empty($url)) { return $url; }

		// Make sure that $pm_query global is not changed
		$old_pm_query = $pm_query;
		$post = Permalink_Manager_Core_Functions::detect_post(null, $url, true);
		$pm_query = $old_pm_query;

		if(!empty($post->ID)) {
			$native_url = "/?p={$post->ID}";
		}

		return (!empty($native_url)) ? $native_url : $url;
	}

	/**
	 * Bulk tools
	 */
	public static function get_items() {
		global $wpdb;

		// Check if post types & statuses are not empty
		if(empty($_POST['post_types']) || empty($_POST['post_statuses'])) { return false; }

		$post_types_array = ($_POST['post_types']);
		$post_statuses_array = ($_POST['post_statuses']);
		$post_types = implode("', '", $post_types_array);
		$post_statuses = implode("', '", $post_statuses_array);

		// Filter the posts by IDs
		$where = '';
		if(!empty($_POST['ids'])) {
			// Remove whitespaces and prepare array with IDs and/or ranges
			$ids = esc_sql(preg_replace('/\s*/m', '', $_POST['ids']));
			preg_match_all("/([\d]+(?:-?[\d]+)?)/x", $ids, $groups);

			// Prepare the extra ID filters
			$where .= "AND (";
			foreach($groups[0] as $group) {
				$where .= ($group == reset($groups[0])) ? "" : " OR ";
				// A. Single number
				if(is_numeric($group)) {
					$where .= "(ID = {$group})";
				}
				// B. Range
				else if(substr_count($group, '-')) {
					$range_edges = explode("-", $group);
					$where .= "(ID BETWEEN {$range_edges[0]} AND {$range_edges[1]})";
				}
			}
			$where .= ")";
		}

		// Get excluded items
		$excluded_posts_ui = $wpdb->get_col("SELECT post_ID FROM {$wpdb->postmeta} AS pm LEFT JOIN {$wpdb->posts} AS p ON (pm.post_ID = p.ID) WHERE pm.meta_key = 'auto_update_uri' AND pm.meta_value = '-2' AND post_type IN ('{$post_types}')");
		$excluded_posts_hook = (array) apply_filters('permalink_manager_excluded_post_ids', array());
		$excluded_posts = array_merge($excluded_posts_ui, $excluded_posts_hook);

		if(!empty($excluded_posts)) {
			$where .= sprintf(" AND ID NOT IN ('%s') ", implode("', '", $excluded_posts));
		}

		// Support for attachments
		$attachment_support = (in_array('attachment', $post_types_array)) ? " OR (post_type = 'attachment')" : "";

		// Get the rows before they are altered
		return $wpdb->get_results("SELECT post_type, post_title, post_name, ID FROM {$wpdb->posts} WHERE ((post_status IN ('{$post_statuses}') AND post_type IN ('{$post_types}')){$attachment_support}) {$where}", ARRAY_A);
	}

	/**
	* Find & replace (bulk action)
	*/
	public static function find_and_replace($chunk = null, $mode = '', $old_string = '', $new_string = '') {
		global $wpdb, $permalink_manager_uris;

		// Reset variables
		$updated_slugs_count = 0;
		$updated_array = array();
		$alert_type = $alert_content = $errors = '';

		// Get the rows before they are altered
		$posts_to_update = ($chunk) ? $chunk : self::get_items();

		// Now if the array is not empty use IDs from each subarray as a key
		if($posts_to_update && empty($errors)) {
			foreach ($posts_to_update as $row) {
				// Get default & native URL
				$native_uri = self::get_default_post_uri($row['ID'], true);
				$default_uri = self::get_default_post_uri($row['ID']);

				$old_post_name = $old_slug = $row['post_name'];
				$old_uri = (isset($permalink_manager_uris[$row['ID']])) ? $permalink_manager_uris[$row['ID']] : $native_uri;

				// Do replacement on slugs (non-REGEX)
				if(preg_match("/^\/.+\/[a-z]*$/i", $old_string)) {
					$regex = stripslashes(trim(sanitize_text_field($_POST['old_string']), "/"));
					$regex = preg_quote($regex, '~');
					$pattern = "~{$regex}~";

					$new_post_name = ($mode == 'slugs') ? preg_replace($pattern, $new_string, $old_post_name) : $old_post_name;
					$new_uri = ($mode != 'slugs') ? preg_replace($pattern, $new_string, $old_uri) : $old_uri;
				} else {
					$new_post_name = ($mode == 'slugs') ? str_replace($old_string, $new_string, $old_post_name) : $old_post_name; // Post name is changed only in first mode
					$new_uri = ($mode != 'slugs') ? str_replace($old_string, $new_string, $old_uri) : $old_uri;
				}

				// echo "{$old_uri} - {$new_uri} - {$native_uri} - {$default_uri} \n";

				// Check if native slug should be changed
				if(($mode == 'slugs') && ($old_post_name != $new_post_name)) {
					$new_slug = self::update_slug_by_id($new_post_name, $row['ID']);
				}

				if(($old_uri != $new_uri) || ($old_post_name != $new_post_name) && !(empty($new_uri))) {
					$permalink_manager_uris[$row['ID']] = trim($new_uri, '/');
					$updated_array[] = array('item_title' => $row['post_title'], 'ID' => $row['ID'], 'old_uri' => $old_uri, 'new_uri' => $new_uri, 'old_slug' => $old_slug, 'new_slug' => $new_slug);
					$updated_slugs_count++;
				}

				do_action('permalink_manager_updated_post_uri', $row['ID'], $new_uri, $old_uri, $native_uri, $default_uri);
			}

			// Filter array before saving
			if(is_array($permalink_manager_uris)) {
				$permalink_manager_uris = array_filter($permalink_manager_uris);
				update_option('permalink-manager-uris', $permalink_manager_uris);
			}

			$output = array('updated' => $updated_array, 'updated_count' => $updated_slugs_count);
			wp_reset_postdata();
		}

		return ($output) ? $output : "";
	}

	/**
	* Regenerate slugs & bases (bulk action)
	*/
	static function regenerate_all_permalinks($chunk = null, $mode = '') {
		global $wpdb, $permalink_manager_uris;

		// Reset variables
		$updated_slugs_count = 0;
		$updated_array = array();
		$alert_type = $alert_content = $errors = '';

		// Get the rows before they are altered
		$posts_to_update = ($chunk) ? $chunk : self::get_items();

		// Now if the array is not empty use IDs from each subarray as a key
		if($posts_to_update && empty($errors)) {
			foreach ($posts_to_update as $row) {
				// Get default & native URL
				$native_uri = self::get_default_post_uri($row['ID'], true);
				$default_uri = self::get_default_post_uri($row['ID']);
				$old_post_name = $row['post_name'];
				$old_uri = isset($permalink_manager_uris[$row['ID']]) ? trim($permalink_manager_uris[$row['ID']], "/") : '';
				$correct_slug = ($mode == 'slugs') ? sanitize_title($row['post_title']) : Permalink_Manager_Helper_Functions::sanitize_title($row['post_title']);

				// Process URI & slug
				$new_slug = wp_unique_post_slug($correct_slug, $row['ID'], get_post_status($row['ID']), get_post_type($row['ID']), null);
				$new_post_name = ($mode == 'slugs') ? $new_slug : $old_post_name; // Post name is changed only in first mode

				// Prepare the new URI
				if($mode == 'slugs') {
					$new_uri = ($old_uri) ? $old_uri : $native_uri;
				} else if($mode == 'native') {
					$new_uri = $native_uri;
				} else {
					$new_uri = $default_uri;
				}

				//print_r("{$old_uri} - {$new_uri} - {$native_uri} - {$default_uri} / - {$new_slug} - {$new_post_name} \n");

				// Check if native slug should be changed
				if(($mode == 'slugs') && ($old_post_name != $new_post_name)) {
					self::update_slug_by_id($new_post_name, $row['ID']);
					clean_post_cache($row['ID']);
				}

				if(($old_uri != $new_uri) || ($old_post_name != $new_post_name)) {
					$permalink_manager_uris[$row['ID']] = $new_uri;
					$updated_array[] = array('item_title' => $row['post_title'], 'ID' => $row['ID'], 'old_uri' => $old_uri, 'new_uri' => $new_uri, 'old_slug' => $old_post_name, 'new_slug' => $new_post_name);
					$updated_slugs_count++;
				}

				do_action('permalink_manager_updated_post_uri', $row['ID'], $new_uri, $old_uri, $native_uri, $default_uri);
			}

			// Filter array before saving
			if(is_array($permalink_manager_uris)) {
				$permalink_manager_uris = array_filter($permalink_manager_uris);
				update_option('permalink-manager-uris', $permalink_manager_uris);
			}

			$output = array('updated' => $updated_array, 'updated_count' => $updated_slugs_count);
			wp_reset_postdata();
		}

		return (!empty($output)) ? $output : "";
	}

	/**
	* Update all slugs & bases (bulk action)
	*/
	static public function update_all_permalinks() {
		global $permalink_manager_uris;

		// Setup needed variables
		$updated_slugs_count = 0;
		$updated_array = array();

		$old_uris = $permalink_manager_uris;
		$new_uris = isset($_POST['uri']) ? $_POST['uri'] : array();

		// Double check if the slugs and ids are stored in arrays
		if (!is_array($new_uris)) $new_uris = explode(',', $new_uris);

		if (!empty($new_uris)) {
			foreach($new_uris as $id => $new_uri) {
				// Prepare variables
				$this_post = get_post($id);
				$updated = '';

				// Get default & native URL
				$native_uri = self::get_default_post_uri($id, true);
				$default_uri = self::get_default_post_uri($id);

				$old_uri = isset($old_uris[$id]) ? trim($old_uris[$id], "/") : "";

				// Process new values - empty entries will be treated as default values
				$new_uri = Permalink_Manager_Helper_Functions::sanitize_title($new_uri);
				$new_uri = (!empty($new_uri)) ? trim($new_uri, "/") : $default_uri;
				$new_slug = (strpos($new_uri, '/') !== false) ? substr($new_uri, strrpos($new_uri, '/') + 1) : $new_uri;

				//print_r("{$old_uri} - {$new_uri} - {$native_uri} - {$default_uri}\n");

				if($new_uri != $old_uri) {
					$old_uris[$id] = $new_uri;
					$updated_array[] = array('item_title' => get_the_title($id), 'ID' => $id, 'old_uri' => $old_uri, 'new_uri' => $new_uri);
					$updated_slugs_count++;
				}

				do_action('permalink_manager_updated_post_uri', $id, $new_uri, $old_uri, $native_uri, $default_uri);
			}

			// Filter array before saving & append the global
			if(is_array($permalink_manager_uris)) {
				$permalink_manager_uris = array_filter($old_uris);
				update_option('permalink-manager-uris', $permalink_manager_uris);
			}

			$output = array('updated' => $updated_array, 'updated_count' => $updated_slugs_count);
		}

		return ($output) ? $output : "";
	}

	/**
	* Allow to edit URIs from "Edit Post" admin pages
	*/
	function edit_uri_box($html, $id, $new_title, $new_slug, $post) {
		global $permalink_manager_uris, $permalink_manager_options;

		// Detect auto drafts
		$autosave = (!empty($new_title) && empty($new_slug)) ? true : false;

		// Check if the post is excluded
		if(empty($post->post_type) || Permalink_Manager_Helper_Functions::is_post_excluded($post)) { return $html; }

		// Ignore drafts
		if(!empty($permalink_manager_options["general"]["ignore_drafts"]) && !empty($post->post_status) && $post->post_status == 'draft') { return $html; }

		// Stop the hook (if needed)
		$show_uri_editor = apply_filters("permalink_manager_show_uri_editor_post_{$post->post_type}", true, $post);
		if(!$show_uri_editor) { return $html; }

		$new_html = preg_replace("/^(<strong>(.*)<\/strong>)(.*)/is", "$1 ", $html);
		$default_uri = self::get_default_post_uri($id);
		$native_uri = self::get_default_post_uri($id, true);

		// Make sure that home URL ends with slash
		$home_url = Permalink_Manager_Helper_Functions::get_permalink_base($post);

		// A. Display original permalink on front-page editor
		if(Permalink_Manager_Helper_Functions::is_front_page($id)) {
			preg_match('/href="([^"]+)"/mi', $html, $matches);
			$sample_permalink = (!empty($matches[1])) ? $matches[1] : "";
		}
		else {
			// B. Do not change anything if post is not saved yet (display sample permalink instead)
			if($autosave || empty($post->post_status)) {
				$sample_permalink_uri = $default_uri;
			}
			// C. Display custom URI if set
			else {
				$sample_permalink_uri = (!empty($permalink_manager_uris[$id])) ? $permalink_manager_uris[$id] : $native_uri;
			}

			// Decode URI & allow to filter it
			$sample_permalink_uri = apply_filters('permalink_manager_filter_post_sample_uri', rawurldecode($sample_permalink_uri), $post);

			// Prepare the sample & default permalink
			$sample_permalink = sprintf("%s/<span class=\"editable\">%s</span>", $home_url, str_replace("//", "/", $sample_permalink_uri));

			// Allow to filter the sample permalink URL
			// $sample_permalink = apply_filters('permalink_manager_filter_post_sample_permalink', $sample_permalink, $post);
		}

		// Append new HTML output
		$new_html .= sprintf("<span class=\"sample-permalink-span\"><a id=\"sample-permalink\" href=\"%s\">%s</a></span>&nbsp;", strip_tags($sample_permalink), $sample_permalink);
		$new_html .= (!$autosave) ? Permalink_Manager_Admin_Functions::display_uri_box($post) : "";

		// Append hidden field with native slug
		$new_html .= (!empty($post->post_name)) ? "<span id=\"editable-post-name-full\">{$post->post_name}</span>" : "";

		return $new_html;
	}

	/**
	 * "Quick Edit" form
	 */
	function quick_edit_column($columns) {
		global $current_screen;

		// Get post type
		$post_type = (!empty($current_screen->post_type)) ? $current_screen->post_type : false;

		// Check if post type is disabled
		if($post_type && Permalink_Manager_Helper_Functions::is_post_type_disabled($post_type)) { return $columns; }

		return (is_array($columns)) ? array_merge($columns, array('permalink-manager-col' => __( 'Current URI', 'permalink-manager'))) : $columns;
	}

	function quick_edit_column_content($column_name, $post_id) {
		global $permalink_manager_uris, $permalink_manager_options;

		if($column_name == "permalink-manager-col") {
			// Get auto-update settings
			$auto_update_val = get_post_meta($post_id, "auto_update_uri", true);
			$auto_update_uri = (!empty($auto_update_val)) ? $auto_update_val : $permalink_manager_options["general"]["auto_update_uris"];

			$uri = (!empty($permalink_manager_uris[$post_id])) ? rawurldecode($permalink_manager_uris[$post_id]) : self::get_post_uri($post_id, true);
			printf('<span class="permalink-manager-col-uri" data-auto_update="%s">%s</span>', intval($auto_update_uri), $uri);
		}
	}

	function quick_edit_column_form($column_name, $post_type, $taxonomy = '') {
		if(!$taxonomy && $column_name == 'permalink-manager-col') {
			echo Permalink_Manager_Admin_Functions::quick_edit_column_form();
		}
	}

	/**
	 * Update URI when new post is added
	 */
	function new_post_uri($post_id) {
		global $post, $permalink_manager_uris, $permalink_manager_options, $permalink_manager_before_sections_html;

		// Do not trigger if post is a revision or imported via WP All Import (URI should be set after the post meta is added)
		if(wp_is_post_revision($post_id) || (!empty($_REQUEST['page']) && $_REQUEST['page'] == 'pmxi-admin-import')) { return $post_id; }

		// Prevent language mismatch in MultilingualPress plugin
		if(is_admin() && !empty($post->ID) && $post->ID != $post_id) { return $post_id; }

		// Stop when products are imported with WooCommerce importer
		if(!empty($_REQUEST['action']) && $_REQUEST['action'] == 'woocommerce_do_ajax_product_import') { return $post_id; }

		// Do not do anything if post is autosaved
		if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) { return $post_id; }

		// Do not do anything on in "Bulk Edit"
		if(!empty($_REQUEST['bulk_edit'])) { return $post_id; }

		// Hotfix
		if(isset($_POST['custom_uri']) || isset($_POST['permalink-manager-quick-edit']) || isset($permalink_manager_uris[$post_id])) { return $post_id; }

		$post_object = get_post($post_id);

		// Check if post is allowed
		if(empty($post_object->post_type) || Permalink_Manager_Helper_Functions::is_post_excluded($post_object)) { return $post_id; }

		// Exclude drafts
		if(!empty($permalink_manager_options["general"]["ignore_drafts"]) && !empty($post_object->post_status) && $post_object->post_status == 'draft') { return $post_id; }

		// Stop the hook (if needed)
		$allow_new_uri = apply_filters("permalink_manager_allow_new_post_uri", true, $post_object);
		if(!$allow_new_uri) { return $post_id; }

		// Ignore menu items
		if($post_object->post_type == 'nav_menu_item') { return $post_id; }

		// Ignore auto-drafts, revisions, removed posts and posts without title
		if(in_array($post_object->post_status, array('auto-draft', 'trash')) || (strpos($post_object->post_name, 'revision-v1') !== false) || empty($post_object->post_title) || (!empty($post_object->post_name) && $post_object->post_name == 'auto-draft')) { return $post_id; }

		$native_uri = self::get_default_post_uri($post_id, true);
		$new_uri = self::get_default_post_uri($post_id);
		$permalink_manager_uris[$post_object->ID] = $new_uri;

		if(is_array($permalink_manager_uris)) {
			update_option('permalink-manager-uris', $permalink_manager_uris);
		}

		do_action('permalink_manager_new_post_uri', $post_id, $new_uri, $native_uri);
	}

	/**
	 * Update URI from "Edit Post" admin page
	 */
	static public function update_post_uri($post_id) {
		global $permalink_manager_uris, $permalink_manager_options, $permalink_manager_before_sections_html;

		// Verify nonce at first
		if(!isset($_POST['permalink-manager-nonce']) || !wp_verify_nonce($_POST['permalink-manager-nonce'], 'permalink-manager-edit-uri-box')) { return $post_id; }

		// Do not do anything if the field with URI or element ID are not present
		if(!isset($_POST['custom_uri']) || empty($_POST['permalink-manager-edit-uri-element-id'])) { return $post_id; }

		// Hotfix
		if($_POST['permalink-manager-edit-uri-element-id'] != $post_id) { return $post_id; }

		// Do not do anything if post is autosaved
		if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) { return $post_id; }

		// Do not do anything on in "Bulk Edit" or when the post is imported via WP All Import
		if(!empty($_REQUEST['bulk_edit']) || (!empty($_REQUEST['page']) && $_REQUEST['page'] == 'pmxi-admin-import')) { return $post_id; }

		// Fix for revisions
		$is_revision = wp_is_post_revision($post_id);
		$post_id = ($is_revision) ? $is_revision : $post_id;
		$post = get_post($post_id);

		// Check if post is allowed
		if(empty($post->post_type) || Permalink_Manager_Helper_Functions::is_post_excluded($post)) { return $post_id; }

		// Exclude drafts
		if(!empty($permalink_manager_options["general"]["ignore_drafts"]) && !empty($post->post_status) && $post->post_status == 'draft') { return $post_id; }

		// Stop the hook (if needed)
		$allow_update_uri = apply_filters("permalink_manager_allow_update_post_uri", true, $post);
		if(!$allow_update_uri) { return $post_id; }

		// Ignore auto-drafts, removed posts and posts without title
		if(in_array($post->post_status, array('auto-draft', 'trash')) || empty($post->post_title)) { return $post_id; }

		// Get auto-update URI setting (if empty use global setting)
		if(!empty($_POST["auto_update_uri"])) {
			$auto_update_uri_current = intval($_POST["auto_update_uri"]);
		} else if(!empty($_POST["action"]) && $_POST['action'] == 'inline-save') {
			$auto_update_uri_current = get_post_meta($post_id, "auto_update_uri", true);
		}
		$auto_update_uri = (!empty($auto_update_uri_current)) ? $auto_update_uri_current : $permalink_manager_options["general"]["auto_update_uris"];

		// Update the slug (if changed)
		if(isset($_POST['permalink-manager-edit-uri-element-slug']) && isset($_POST['native_slug']) && ($_POST['native_slug'] !== $_POST['permalink-manager-edit-uri-element-slug'])) {

			// Make sure that '_wp_old_slug' is saved
			if(!empty($_POST['post_name'])) {
				$post_before = $post;

				 // Clone the instance of WP_Post object
				$post_after = unserialize(serialize($post));
				$post_after->post_name = sanitize_title($_POST['native_slug']);

				wp_check_for_changed_slugs($post_id, $post_after, $post_before);
			}

			self::update_slug_by_id($_POST['native_slug'], $post_id);
			clean_post_cache($post_id);
		}

		$default_uri = self::get_default_post_uri($post_id);
		$native_uri = self::get_default_post_uri($post_id, true);
		$old_uri = (isset($permalink_manager_uris[$post->ID])) ? $permalink_manager_uris[$post->ID] : $native_uri;

		// Use default URI if URI is cleared by user OR URI should be automatically updated
		$new_uri = (($_POST['custom_uri'] == '') || $auto_update_uri == 1) ? $default_uri : Permalink_Manager_Helper_Functions::sanitize_title($_POST['custom_uri'], true);

		// Save or remove "Auto-update URI" settings
		if(!empty($auto_update_uri_current)) {
			update_post_meta($post_id, "auto_update_uri", $auto_update_uri_current);
		} elseif(isset($_POST['auto_update_uri'])) {
			delete_post_meta($post_id, "auto_update_uri");
		}

		// Save only changed URIs
		if(is_array($permalink_manager_uris)) {
			$permalink_manager_uris[$post_id] = $new_uri;
			update_option('permalink-manager-uris', $permalink_manager_uris);
		}

		do_action('permalink_manager_updated_post_uri', $post_id, $new_uri, $old_uri, $native_uri, $default_uri, $single_update = true);
	}

	/**
	* Remove URI from options array after post is moved to the trash
	*/
	function remove_post_uri($post_id) {
		global $permalink_manager_uris;

		// Check if the custom permalink is assigned to this post
		if(isset($permalink_manager_uris[$post_id])) {
			unset($permalink_manager_uris[$post_id]);
		}

		if(is_array($permalink_manager_uris)) {
			update_option('permalink-manager-uris', $permalink_manager_uris);
		}
	}

}
