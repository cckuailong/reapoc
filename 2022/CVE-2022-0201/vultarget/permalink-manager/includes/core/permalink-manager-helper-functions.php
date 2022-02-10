<?php

/**
* Additional functions used in classes and another subclasses
*/
class Permalink_Manager_Helper_Functions extends Permalink_Manager_Class {

	public function __construct() {
		add_action('plugins_loaded', array($this, 'init'), 5);
	}

	public function init() {
		// Replace empty placeholder tags & remove BOM
		add_filter( 'permalink_manager_filter_default_post_uri', array($this, 'replace_empty_placeholder_tags'), 10, 5);
		add_filter( 'permalink_manager_filter_default_term_uri', array($this, 'replace_empty_placeholder_tags'), 10, 5);

		// Clear the final default URIs
		add_filter( 'permalink_manager_filter_default_term_uri', array($this, 'clear_single_uri'), 20);
		add_filter( 'permalink_manager_filter_default_post_uri', array($this, 'clear_single_uri'), 20);

		// Reload the globals when the blog is switched (multisite)
		add_action('switch_blog', array($this, 'reload_globals_in_network'), 9);
	}

	/**
	 * Support for multidimensional arrays - array_map()
	 */
	static function multidimensional_array_map($function, $input) {
		$output = array();

		if(is_array($input)) {
			foreach ($input as $key => $val) {
				$output[$key] = (is_array($val) ? self::multidimensional_array_map($function, $val) : $function($val));
			}
		} else {
			$output = $function($input);
		}

		return $output;
	}

	/**
	 * Get primary term
	 */
	static function get_primary_term($post_id, $taxonomy, $slug_only = true) {
		global $permalink_manager_options;

		$primary_term_enabled = (isset($permalink_manager_options['general']['primary_category'])) ? (bool) $permalink_manager_options['general']['primary_category'] : true;
		$primary_term_enabled = apply_filters('permalink_manager_primary_term', $primary_term_enabled);

		if(!$primary_term_enabled) { return; }

		// A. Yoast SEO
		if(class_exists('WPSEO_Primary_Term')) {
			$primary_term = new WPSEO_Primary_Term($taxonomy, $post_id);
			$primary_term = get_term($primary_term->get_primary_term());
		}
		// B. The SEO Framework
		else if(function_exists('the_seo_framework')) {
			$primary_term = the_seo_framework()->get_primary_term($post_id, $taxonomy);
		}
		// C. RankMath
		else if(class_exists('RankMath')) {
			$primary_cat_id = get_post_meta($post_id, "rank_math_primary_{$taxonomy}", true);
			$primary_term = (!empty($primary_cat_id)) ? get_term($primary_cat_id, $taxonomy) : '';
		}
		// D. SEOPress
		else if(function_exists('seopress_init') && $taxonomy == 'category') {
			$primary_cat_id = get_post_meta($post_id, '_seopress_robots_primary_cat', true);
			$primary_term = (!empty($primary_cat_id)) ? get_term($primary_cat_id, 'category') : '';
		}

		if(!empty($primary_term) && !is_wp_error($primary_term)) {
			return ($slug_only) ? $primary_term->slug : $primary_term;
		} else {
			return;
		}
	}

	/**
	 * Get lowest level term/post
	 */
	static function get_lowest_element($first_element, $elements) {
		if(!empty($elements) && !empty($first_element)) {
			// Get the ID of first element
			if(!empty($first_element->term_id)) {
				$first_element_id = $first_element->term_id;
				$parent_key = 'parent';
			} else if(!empty($first_element->ID)) {
				$first_element_id = $first_element->ID;
				$parent_key = 'post_parent';
			} else if(is_numeric($first_element)) {
				$first_element_id = $first_element;
				$parent_key = 'post_parent';
			} else {
				return false;
			}

			$children = wp_filter_object_list($elements, array($parent_key => $first_element_id));
			if(!empty($children)) {
				// Get the first term
				$child_term = reset($children);
				$first_element = self::get_lowest_element($child_term, $elements);
			}
		}
		return $first_element;
	}

	/**
	 * Get term full slug
	 */
	static function get_term_full_slug($term, $terms, $mode = false, $native_uri = false) {
		global $permalink_manager_uris;

		// Check if term is not empty
		if(empty($term->taxonomy)) { return ''; }

		// Get taxonomy
		$taxonomy = $term->taxonomy;

		// Check if mode is set
		if(empty($mode)) {
			$mode = (is_taxonomy_hierarchical($taxonomy)) ? 2 : 4;
		}

		// A. Inherit the custom permalink from the term
		if($mode == 1) {
			$term_slug = $permalink_manager_uris["tax-{$term->term_id}"];
		}
		// B. Hierarhcical taxonomy base
		else if($mode == 2) {
			$ancestors = get_ancestors($term->term_id, $taxonomy, 'taxonomy');
			$hierarchical_slugs = array();

			foreach((array) $ancestors as $ancestor) {
				$ancestor_term = get_term($ancestor, $taxonomy);
				$hierarchical_slugs[] = ($native_uri) ? $ancestor_term->slug : self::force_custom_slugs($ancestor_term->slug, $ancestor_term);
			}
			$hierarchical_slugs = array_reverse($hierarchical_slugs);
			$term_slug = implode('/', $hierarchical_slugs);

			// Append the term slug now
			$last_term_slug = ($native_uri) ? $term->slug : self::force_custom_slugs($term->slug, $term);
			$term_slug = "{$term_slug}/{$last_term_slug}";
		}
		// C. Force flat taxonomy base - get highest level term (if %taxonomy_top% tag is used)
		else if($mode == 3) {
			if(!empty($term->parent)) {
				$ancestors = get_ancestors($term->term_id, $taxonomy, 'taxonomy');

				if(is_array($ancestors)) {
					$top_ancestor = end($ancestors);
					$top_ancestor_term = get_term($top_ancestor, $taxonomy);
					$single_term = (!empty($top_ancestor_term->slug)) ? $top_ancestor_term : $term;
				}
			}

			$term_slug = (!empty($single_term->slug)) ? self::force_custom_slugs($single_term->slug, $single_term) : $term->slug;
		}
		// D. Force flat taxonomy base - get primary or lowest level term (if term is non-hierarchical or %taxonomy_flat% tag is used)
		else {
			if(!empty($term->slug)) {
				$term_slug = ($native_uri) ? $term->slug : Permalink_Manager_Helper_Functions::force_custom_slugs($term->slug, $term);
			} else {
				foreach($terms as $single_term) {
					if($single_term->parent == 0) {
						$term_slug = self::force_custom_slugs($single_term->slug, $single_term);
						break;
					}
				}
			}
		}

		return (!empty($term_slug)) ? $term_slug : "";
	}

	/**
	 * Allow to disable post types and taxonomies
	 */
	static function get_disabled_post_types($include_user_excluded = true) {
		global $wp_post_types, $permalink_manager_options;

		$disabled_post_types = array(
			'revision', 'nav_menu_item', 'algolia_task', 'fl_builder', 'fl-builder', 'fl-builder-template', 'fl-theme-layout', 'fusion_tb_layout', 'fusion_tb_section', 'fusion_template', 'fusion_element', 'wc_product_tab', 'wc_voucher', 'wc_voucher_template',
			'sliders', 'thirstylink', 'elementor_library', 'elementor_menu_item', 'cms_block', 'nooz_coverage'
		);

		// 1. Disable post types that are not publicly_queryable
		foreach($wp_post_types as $post_type) {
			$is_publicly_queryable = (!empty($post_type->publicly_queryable) || (!empty($post_type->public) && !empty($post_type->rewrite))) ? true : false;

			if(!$is_publicly_queryable && !in_array($post_type->name, array('post', 'page', 'attachment'))) {
				$disabled_post_types[] = $post_type->name;
			}
		}

		// 2. Add post types disabled by user
		if($include_user_excluded) {
			$disabled_post_types = (!empty($permalink_manager_options['general']['partial_disable']['post_types'])) ? array_merge((array) $permalink_manager_options['general']['partial_disable']['post_types'], $disabled_post_types) : $disabled_post_types;
		}

		return apply_filters('permalink_manager_disabled_post_types', $disabled_post_types);
	}

	static function get_disabled_taxonomies($include_user_excluded = true) {
		global $wp_taxonomies, $permalink_manager_options;

		$disabled_taxonomies = array(
			'product_shipping_class', 'post_status', 'fl-builder-template-category', 'post_format', 'nav_menu', 'language'
		);

		// 1. Disable taxonomies that are not publicly_queryable
		foreach($wp_taxonomies as $taxonomy) {
			$is_publicly_queryable = (!empty($taxonomy->publicly_queryable) || (!empty($taxonomy->public) && !empty($taxonomy->rewrite))) ? true : false;

			if(!$is_publicly_queryable && !in_array($taxonomy->name, array('category', 'post_tag'))) {
				$disabled_taxonomies[] = $taxonomy->name;
			}
		}

		// 2. Add taxonomies disabled by user
		if($include_user_excluded) {
			$disabled_taxonomies = (!empty($permalink_manager_options['general']['partial_disable']['taxonomies'])) ? array_merge((array) $permalink_manager_options['general']['partial_disable']['taxonomies'], $disabled_taxonomies) : $disabled_taxonomies;
		}

		return apply_filters('permalink_manager_disabled_taxonomies', $disabled_taxonomies);
	}

	/**
	 * Check if post type should be ignored by Permalink Manager
	 */
	static public function is_post_type_disabled($post_type, $check_if_exists = true) {
		$disabled_post_types = self::get_disabled_post_types();
		$post_type_exists = ($check_if_exists) ? post_type_exists($post_type) : true;
		$out = ((is_array($disabled_post_types) && in_array($post_type, $disabled_post_types)) || empty($post_type_exists)) ? true : false;

		return $out;
	}

	/**
	 * Check if taxonomy should be ignored by Permalink Manager
	 */
	static public function is_taxonomy_disabled($taxonomy, $check_if_exists = true) {
		$disabled_taxonomies = self::get_disabled_taxonomies();
		$taxonomy_exists = ($check_if_exists) ? taxonomy_exists($taxonomy) : true;
		$out = ((is_array($disabled_taxonomies) && in_array($taxonomy, $disabled_taxonomies)) || empty($taxonomy_exists)) ? true : false;

		return $out;
	}

	/**
	 * Check if single post should be ignored by Permalink Manager
	 */
	public static function is_post_excluded($post = null) {
		$post = (is_integer($post)) ? get_post($post) : $post;

		// A. Check if post type is disabled
		if(!empty($post->post_type) && self::is_post_type_disabled($post->post_type)) {
			return true;
		}

		$excluded_post_ids = apply_filters('permalink_manager_excluded_post_ids', array());

		// B. Check if post ID is excluded
		if(is_array($excluded_post_ids) && !empty($post->ID) && in_array($post->ID, $excluded_post_ids)) {
			return true;
		}

		return false;
	}

	/**
	 * Check if single term should be ignored by Permalink Manager
	 */
	public static function is_term_excluded($term = null) {
		$term = (is_numeric($term)) ? get_term($term) : $term;

		// A. Check if post type is disabled
		if(!empty($term->taxonomy) && self::is_taxonomy_disabled($term->taxonomy)) {
			return true;
		}

		$excluded_term_ids = apply_filters('permalink_manager_excluded_term_ids', array());

		// B. Check if post ID is excluded
		if(is_array($excluded_term_ids) && !empty($term->term_id) && in_array($term->term_id, $excluded_term_ids)) {
			return true;
		}

		return false;
	}

	/**
	 * Get all post types supported by Permalink Manager
	 */
	static function get_post_types_array($format = null, $cpt = null, $include_user_excluded = false) {
		global $wp_post_types;

		$post_types_array = array();
		$disabled_post_types = self::get_disabled_post_types(!$include_user_excluded);

		foreach($wp_post_types as $post_type) {
			if($format == 'full') {
				$post_types_array[$post_type->name] = array('label' => $post_type->labels->name, 'name' => $post_type->name);
			} else if($format == 'archive_slug') {
				// Ignore non-public post types
				if(!is_post_type_viewable($post_type) || empty($post_type->has_archive)) {
					continue;
				}

				if($post_type->has_archive != true) {
					$archive_slug = $post_type->has_archive;
				} else if(is_array($post_type->rewrite) && !empty($post_type->rewrite['slug'])) {
					$archive_slug = $post_type->rewrite['slug'];
				} else {
					$archive_slug = $post_type->name;
				}

				$post_types_array[$post_type->name] = $archive_slug;
			} else {
				$post_types_array[$post_type->name] = $post_type->labels->name;
			}
		}

		if(is_array($disabled_post_types)) {
			foreach($disabled_post_types as $post_type) {
				if(!empty($post_types_array[$post_type])) {
					unset($post_types_array[$post_type]);
				}
			}
		}

		return (empty($cpt)) ? $post_types_array : $post_types_array[$cpt];
	}

	/**
	 * Get all taxonomies supported by Permalink Manager
	 */
	static function get_taxonomies_array($format = null, $tax = null, $prefix = false, $include_user_excluded = false, $settings = false) {
		global $wp_taxonomies;

		$taxonomies_array = array();
		$disabled_taxonomies = self::get_disabled_taxonomies(!$include_user_excluded);

		foreach($wp_taxonomies as $taxonomy) {
			$key = ($prefix) ? "tax-{$taxonomy->name}" : $taxonomy->name;
			$taxonomy_name = (!empty($taxonomy->labels->name)) ? $taxonomy->labels->name : '-';

			$taxonomies_array[$taxonomy->name] = ($format == 'full') ? array('label' => $taxonomy->labels->name, 'name' => $taxonomy->name) : $taxonomy_name;
		}

		if(is_array($disabled_taxonomies)) {
			foreach($disabled_taxonomies as $taxonomy) {
				if(!empty($taxonomies_array[$taxonomy])) {
					unset($taxonomies_array[$taxonomy]);
				}
			}
		}

		ksort($taxonomies_array);

		return (empty($tax)) ? $taxonomies_array : $taxonomies_array[$tax];
	}

	/**
	 * Get all post statuses supported by Permalink Manager
	 */
	static function get_post_statuses() {
		$post_statuses = get_post_statuses();

		return apply_filters('permalink_manager_post_statuses', $post_statuses);
	}

	/**
	* Get permastruct
	*/
	static function get_default_permastruct($post_type = 'page', $remove_post_tag = false) {
		global $wp_rewrite;

		// Get default permastruct
		if($post_type == 'page') {
			$permastruct = $wp_rewrite->get_page_permastruct();
		} else if($post_type == 'post') {
			$permastruct = get_option('permalink_structure');
		} else {
			$permastruct = $wp_rewrite->get_extra_permastruct($post_type);
		}

		return ($remove_post_tag) ? trim(str_replace(array("%postname%", "%pagename%", "%{$post_type}%"), "", $permastruct), "/") : $permastruct;
	}

	/**
	 * Get all endpoints
	 */
	static function get_endpoints() {
		global $wp_rewrite;

		$pagination_endpoint = (!empty($wp_rewrite->pagination_base)) ? $wp_rewrite->pagination_base : 'page';

		// Start with default endpoints
		$endpoints = "{$pagination_endpoint}|feed|embed|attachment|trackback|filter";

		if(!empty($wp_rewrite->endpoints)) {
			foreach($wp_rewrite->endpoints as $endpoint) {
				$endpoints .= "|{$endpoint[1]}";
			}
		}

		return apply_filters("permalink_manager_endpoints", str_replace("/", "\/", $endpoints));
	}

	/**
	* Structure Tags & Rewrite functions
	*/
	static function get_all_structure_tags($code = true, $seperator = ', ', $hide_slug_tags = true) {
		global $wp_rewrite;

		$tags = $wp_rewrite->rewritecode;

		// Hide slug tags
		if($hide_slug_tags) {
			$post_types = Permalink_Manager_Helper_Functions::get_post_types_array();
			foreach($post_types as $post_type => $post_type_name) {
				$post_type_tag = Permalink_Manager_Helper_Functions::get_post_tag($post_type);
				// Find key with post type tag from rewritecode
				$key = array_search($post_type_tag, $tags);
				if($key) { unset($tags[$key]); }
			}
		}

		// Extra tags
		$tags[] = '%taxonomy%';
		$tags[] = '%post_type%';
		$tags[] = '%term_id%';
		$tags[] = '%monthname%';

		foreach($tags as &$tag) {
			$tag = ($code) ? "<code>{$tag}</code>" : "{$tag}";
		}
		$output = implode($seperator, $tags);

		return "<span class=\"structure-tags-list\">{$output}</span>";
	}

	/**
	* Get endpoint used to mark the postname or its equivalent for custom post types and pages in permastructures
	*/
	static function get_post_tag($post_type) {
		// Get the post type (with fix for posts & pages)
		if($post_type == 'page') {
			$post_type_tag = '%pagename%';
		} else if ($post_type == 'post') {
			$post_type_tag = '%postname%';
		} else {
			$post_type_tag = "%{$post_type}%";
		}
		return $post_type_tag;
	}

	/**
	* Find taxonomy name using "term_id"
	*/
	static function get_tax_name($term, $term_by = 'id') {
		$term_object = get_term_by($term_by, $term);
		return (isset($term_object->taxonomy)) ? $term_object->taxonomy : '';
	}

	/**
	 * Get permalink base (home URL)
	 */
	public static function get_permalink_base($element = null) {
		return apply_filters('permalink_manager_filter_permalink_base', trim(get_option('home'), "/"), $element);
	}

	/**
	 * Remove post tag from permastructure
	 */
	static function remove_post_tag($permastruct) {
		$post_types = self::get_post_types_array('full');

		// Get all post tags
		$post_tags = array("%postname%", "%pagename%");
		foreach($post_types as $post_type) {
			$post_tags[] = "%{$post_type['name']}%";
		}

		$permastruct = str_replace($post_tags, "", $permastruct);
		return trim($permastruct, "/");
	}

	/**
	 * Get front-page ID
	 */
	static function is_front_page($page_id) {
		$front_page_id = get_option('page_on_front');
		$bool = (!empty($front_page_id) && $page_id == $front_page_id) ? true : false;

		return apply_filters('permalink_manager_is_front_page', $bool, $page_id, $front_page_id);
	}

	/**
	 * Sanitize multidimensional array
	 */
	static function sanitize_array($data = array()) {
		if (!is_array($data) || !count($data)) {
			return array();
		}

		foreach ($data as $k => $v) {
			if (!is_array($v) && !is_object($v)) {
				$data[$k] = htmlspecialchars(trim($v));
			}
			if (is_array($v)) {
				$data[$k] = self::sanitize_array($v);
			}
		}
		return $data;
	}

	/**
	 * Encode URI and keep special characters
	 */
	static function encode_uri($uri) {
		return str_replace(array('%2F', '%2C', '%7C', '%2B'), array('/', ',', '|', '+'), urlencode($uri));
	}

	/**
	 * Slugify function
	 */
	public static function sanitize_title($str, $keep_percent_sign = false, $force_lowercase = null, $sanitize_slugs = null) {
		global $permalink_manager_options;

		// Foree lowercase & hyphens
		$force_lowercase = (!is_null($force_lowercase)) ? $force_lowercase : apply_filters('permalink_manager_force_lowercase_uris', true);

		if(is_null($sanitize_slugs)) {
			$sanitize_slugs = (!empty($permalink_manager_options['general']['disable_slug_sanitization'])) ? false : true;
		}

		// Remove accents & entities
		$clean = (empty($permalink_manager_options['general']['keep_accents'])) ? remove_accents($str) : $str;
		$clean = str_replace(array('&lt', '&gt', '&amp'), '', $clean);

		$percent_sign = ($keep_percent_sign) ? "\%" : "";
		$sanitize_regex = apply_filters("permalink_manager_sanitize_regex", "/[^\p{Xan}a-zA-Z0-9{$percent_sign}\/_\.|+, -]/ui", $percent_sign);
		$clean = preg_replace($sanitize_regex, '', $clean);
		$clean = ($force_lowercase) ? strtolower($clean) : $clean;

		// Remove amperand
		$clean = str_replace(array('%26', '&'), '', $clean);

		// Remove special characters
		if($sanitize_slugs !== false) {
			$clean = preg_replace("/[\s|+-]+/", "-", $clean);
			$clean = preg_replace("/[,]+/", "", $clean);
			$clean = preg_replace('/([\.]+)(?![a-z]{3,4}$)/i', '', $clean);
			$clean = preg_replace('/([-\s+]\/[-\s+])/', '-', $clean);
		} else {
			$clean = preg_replace("/[\s]+/", "-", $clean);
		}

		// Remove widow & duplicated slashes
		$clean = preg_replace('/([-]*[\/]+[-]*)/', '/', $clean);
		$clean = preg_replace('/([\/]+)/', '/', $clean);

		// Trim slashes, dashes and whitespaces
		$clean = trim($clean, " /-");

		return $clean;
	}

	/**
	 * Replace empty placeholder tags & remove BOM
	 */
	public static function replace_empty_placeholder_tags($default_uri, $native_slug = "", $element = "", $slug = "", $native_uri = "") {
		// Do not affect native URIs
		if($native_uri == true) { return $default_uri; }

		// Remove the BOM
		$default_uri = str_replace(array("\xEF\xBB\xBF", "%ef%bb%bf"), '', $default_uri);

		// Encode the URI before placeholders are removed
		$chunks = explode('/', $default_uri);
		foreach ($chunks as &$chunk) {
			if(preg_match("/^(%.+?%)$/", $chunk) == false) {
				$chunk = rawurldecode($chunk);
			}
		}
		$default_uri = implode("/", $chunks);

		$empty_tag_replacement = apply_filters('permalink_manager_empty_tag_replacement', null, $element);
		$default_uri = ($empty_tag_replacement || is_null($empty_tag_replacement)) ? str_replace("//", "/", preg_replace("/%(.+?)%/", $empty_tag_replacement, $default_uri)) : $default_uri;

		return trim($default_uri, "/");
	}

	/**
	 * Clear/Sanitize the URI
	 */
	public static function clear_single_uri($uri) {
		return self::sanitize_title($uri, true);
	}

	/**
	 * Remove all slashes
	 */
	public static function remove_slashes($uri) {
		return preg_replace("/[\/]+/", "", $uri);
	}

	/**
	 * Force custom slugs
	 */
	public static function force_custom_slugs($slug, $object, $flat = false) {
		global $permalink_manager_options, $permalink_manager_uris;

		$force_custom_slugs = (!empty($permalink_manager_options['general']['force_custom_slugs'])) ? $permalink_manager_options['general']['force_custom_slugs'] : false;
		$force_custom_slugs = apply_filters('permalink_manager_force_custom_slugs', $force_custom_slugs, $slug, $object);

		if($force_custom_slugs) {
			// A. Custom slug (title)
			if($force_custom_slugs == 1) {
				$title = (!empty($object->name)) ? $object->name : $object->post_title;
				$title = self::remove_slashes($title);

				$new_slug = self::sanitize_title($title, false, null, null);
			}
			// B. Custom slug (custom permalink)
			else {
				$object_id = (!empty($object->term_id)) ? "tax-{$object->term_id}" : $object->ID;
				$new_slug = (!empty($permalink_manager_uris[$object_id])) ? basename($permalink_manager_uris[$object_id]) : '';
			}

			$slug = (!empty($new_slug)) ? preg_replace('/([^\/]+)$/', $new_slug, $slug) : $slug;
		}

		if($flat) {
			$slug = preg_replace("/([^\/]+)(.*)/", "$1", $slug);
		}

		return $slug;
	}

	public static function element_exists($element_id) {
		global $wpdb;

		if(strpos($element_id, 'tax-') !== false) {
			$term_id = intval(preg_replace("/[^0-9]/", "", $element_id));
			$element_exists = $wpdb->get_var( "SELECT * FROM {$wpdb->prefix}terms WHERE term_id = {$term_id}" );
		} else {
			$element_exists = $wpdb->get_var( "SELECT * FROM {$wpdb->prefix}posts WHERE ID = {$element_id} AND post_status NOT IN ('auto-draft', 'trash') AND post_type != 'nav_menu_item'" );
		}

		return (!empty($element_exists)) ? $element_exists : false;
	}

	/**
	 * Detect duplicates
	 */
	public static function get_all_duplicates($include_custom_uris = true) {
		global $permalink_manager_uris, $permalink_manager_redirects, $permalink_manager_options, $wpdb;

		// Make sure that both variables are arrays
		$all_uris = ($include_custom_uris && is_array($permalink_manager_uris)) ? $permalink_manager_uris : array();
		$permalink_manager_redirects = (is_array($permalink_manager_redirects)) ? $permalink_manager_redirects : array();

		// Convert redirects list, so it can be merged with $permalink_manager_uris
		foreach($permalink_manager_redirects as $element_id => $redirects) {
			if(is_array($redirects)) {
				foreach($redirects as $index => $uri) {
					$all_uris["redirect-{$index}_{$element_id}"] = $uri;
				}
			}
		}

		// Count duplicates
		$duplicates_removed = 0;
		$duplicates_groups = array();
		$duplicates_list = array_count_values($all_uris);
		$duplicates_list = array_filter($duplicates_list, function ($x) { return $x >= 2; });

		// Assign keys to duplicates (group them)
		if(count($duplicates_list) > 0) {
			foreach($duplicates_list as $duplicated_uri => $count) {
				$duplicated_ids = array_keys($all_uris, $duplicated_uri);

				// Ignore duplicates in different langauges
				if(self::is_uri_duplicated($duplicated_uri, $duplicated_ids[0], $duplicated_ids)) {
					$duplicates_groups[$duplicated_uri] = $duplicated_ids;
				}
			}
		}

		return $duplicates_groups;
	}

	/**
	 * Check if a single URI is duplicated
	 */
	public static function is_uri_duplicated($uri, $element_id, $duplicated_ids = array()) {
		global $permalink_manager_uris;

 		if(empty($uri) || empty($element_id) || empty($permalink_manager_uris)) { return false; }

 		$uri = trim(trim(sanitize_text_field($uri)), "/");
 		$element_id = sanitize_text_field($element_id);

 		// Keep the URIs in a separate array just here
		if(!empty($duplicated_ids)) {
			$all_duplicates = $duplicated_ids;
		} else if(in_array($uri, $permalink_manager_uris)) {
			$all_duplicates = (array) array_keys($permalink_manager_uris, $uri);
		}

 		if(!empty($all_duplicates)) {
			// Get the language code of current element
			$this_uri_lang = Permalink_Manager_Language_Plugins::get_language_code($element_id);

			foreach($all_duplicates as $key => $duplicated_id) {
				// Ignore custom redirects
				if(strpos($key, 'redirect-') !== false) {
					unset($all_duplicates[$key]);
					continue;
				}

				if($this_uri_lang) {
					$duplicated_uri_lang = Permalink_Manager_Language_Plugins::get_language_code($duplicated_id);
				}

				// Ignore the URI for requested element and other elements in other languages to prevent the false alert
				if((!empty($duplicated_uri_lang) && $duplicated_uri_lang !== $this_uri_lang) || $element_id == $duplicated_id) {
					unset($all_duplicates[$key]);
				}
			}

			return (count($all_duplicates) > 0) ? true : false;
		} else {
			return false;
		}
 	}

	/**
	 * URI Search
	 */
	public static function search_uri($search_query, $content_type = null) {
		global $permalink_manager_uris;

		$found = array();
		$search_query = preg_quote($search_query, '/');

		foreach($permalink_manager_uris as $id => $uri) {
			if(preg_match("/\b$search_query\b/i", $uri)) {
				if($content_type && $content_type == 'taxonomies' && (strpos($id, 'tax-') !== false)) {
					$found[] = (int) abs(filter_var($id, FILTER_SANITIZE_NUMBER_INT));
				} else if($content_type && $content_type == 'posts' && is_numeric($id)) {
					$found[] = (int) filter_var($id, FILTER_SANITIZE_NUMBER_INT);
				} else {
					$found[] = $id;
				}
			}
		}

		return $found;
	}

	/**
	 * Reload the globals when the blog is switched (multisite)
	 */
	public function reload_globals_in_network($new_blog_id) {
	  global $permalink_manager_uris, $permalink_manager_redirects, $permalink_manager_external_redirects;

		if(function_exists('get_blog_option')) {
		  $permalink_manager_uris = get_blog_option($new_blog_id, 'permalink-manager-uris');
		  $permalink_manager_redirects = get_blog_option($new_blog_id, 'permalink-manager-redirects');
		  $permalink_manager_external_redirects = get_blog_option($new_blog_id, 'permalink-manager-external-redirects');
		}
	}


}
