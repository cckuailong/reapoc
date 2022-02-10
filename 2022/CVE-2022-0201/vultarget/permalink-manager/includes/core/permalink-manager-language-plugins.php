<?php

/**
* Third parties integration
*/
class Permalink_Manager_Language_Plugins extends Permalink_Manager_Class {

	public function __construct() {
		add_action('init', array($this, 'init_hooks'), 99);
	}

	function init_hooks() {
		global $sitepress_settings, $permalink_manager_options, $polylang, $translate_press_settings;

		// 1. WPML, Polylang & TranslatePress
		if($sitepress_settings || !empty($polylang->links_model->options) || class_exists('TRP_Translate_Press')) {
			// Detect Post/Term function
			add_filter('permalink_manager_detected_post_id', array($this, 'fix_language_mismatch'), 9, 3);
			add_filter('permalink_manager_detected_term_id', array($this, 'fix_language_mismatch'), 9, 3);

			// Fix posts page
			// else {
				add_filter('permalink_manager_filter_query', array($this, 'fix_posts_page'), 5, 5);
			// }

			// URI Editor
			add_filter('permalink_manager_uri_editor_extra_info', array($this, 'language_column_uri_editor'), 9, 3);

			// Adjust front page ID
			add_filter('permalink_manager_is_front_page', array($this, 'wpml_is_front_page'), 9, 3);

			// Get translation mode
			$mode = 0;

			// A. WPML
			if(isset($sitepress_settings['language_negotiation_type'])) {
				$url_settings = $sitepress_settings['language_negotiation_type'];

				if(in_array($sitepress_settings['language_negotiation_type'], array(1, 2))) {
					$mode = 'prepend';
				} else if($sitepress_settings['language_negotiation_type'] == 3) {
					$mode = 'append';
				}
			}
			// B. Polylang
			else if(isset($polylang->links_model->options['force_lang'])) {
				$url_settings = $polylang->links_model->options['force_lang'];

				if(in_array($url_settings, array(1, 2, 3))) {
					$mode = 'prepend';
				}
			}
			// C. TranslatePress
			else if(class_exists('TRP_Translate_Press')) {
				$translate_press_settings = get_option('trp_settings');

				$mode = 'prepend';
			}

			if($mode === 'prepend') {
				add_filter('permalink_manager_detect_uri', array($this, 'detect_uri_language'), 9, 3);
				add_filter('permalink_manager_filter_permalink_base', array($this, 'prepend_lang_prefix'), 9, 2);
				add_filter('template_redirect', array($this, 'wpml_redirect'), 0, 998 );
			} else if($mode === 'append') {
				add_filter('permalink_manager_filter_final_post_permalink', array($this, 'append_lang_prefix'), 5, 2);
				add_filter('permalink_manager_filter_final_term_permalink', array($this, 'append_lang_prefix'), 5, 2);
				add_filter('permalink_manager_detect_uri', array($this, 'wpml_ignore_lang_query_parameter'), 9);
			}

			// Translate permastructures
			add_filter('permalink_manager_filter_permastructure', array($this, 'translate_permastructure'), 9, 2);

			// Translate post type slug
			if(class_exists('WPML_Slug_Translation')) {
				add_filter('permalink_manager_filter_post_type_slug', array($this, 'wpml_translate_post_type_slug'), 9, 3);
			}

			// Translate WooCommerce endpoints
			if(class_exists('WCML_Endpoints')) {
				add_filter('request', array($this, 'wpml_translate_wc_endpoints'), 99999);
			}

			// Edit custom URI using WPML Classic Translation Editor
			if(class_exists('WPML_Translation_Editor_UI')) {
				add_filter('wpml_tm_adjust_translation_fields', array($this, 'wpml_translation_edit_uri'), 999, 2);
				add_action('icl_pro_translation_saved', array($this, 'wpml_translation_save_uri'), 999, 3);
				add_filter('wpml_translation_editor_save_job_data', array($this, 'wpml_translation_save_uri'), 999, 2);
			}

			// Generate custom permalink after WPML's Advanced Translation editor is used
			if(!empty($sitepress_settings['translation-management']) && !empty($sitepress_settings['translation-management']['doc_translation_method']) && $sitepress_settings['translation-management']['doc_translation_method'] == 'ATE') {
				add_action('icl_pro_translation_completed', array($this, 'regenerate_uri_after_wpml_translation_completed'), 99, 3);
			}

			add_action('icl_make_duplicate', array($this, 'wpml_duplicate_uri'), 999, 4);

			// Allow canonical redirect for default language if "Hide URL language information for default language" is turned on in Polylang settings
			if(!empty($polylang) && !empty($polylang->links_model) && !empty($polylang->links_model->options['hide_default'])) {
				add_filter('permalink_manager_filter_query', array($this, 'pl_allow_canonical_redirect'), 3, 5);
			}
		}
	}

	/**
	 * WPML/Polylang/TranslatePress filters
	 */
	public static function get_language_code($element) {
		global $TRP_LANGUAGE, $translate_press_settings, $icl_adjust_id_url_filter_off;

		// Disable WPML adjust ID filter
		$icl_adjust_id_url_filter_off = true;

		// Fallback
		if(is_string($element) && strpos($element, 'tax-') !== false) {
			$element_id = intval(preg_replace("/[^0-9]/", "", $element));
			$element = get_term($element_id);
		} else if(is_numeric($element)) {
			$element = get_post($element);
		}

		// A. TranslatePress
		if(!empty($TRP_LANGUAGE)) {
			$lang_code = self::get_translatepress_language_code($TRP_LANGUAGE);
		}
		// B. WPML & Polylang
		else {
			if(isset($element->post_type)) {
				$element_id = $element->ID;
				$element_type = $element->post_type;
			} else if(isset($element->taxonomy)) {
				$element_id = $element->term_taxonomy_id;
				$element_type = $element->taxonomy;
			} else {
				return false;
			}

			$lang_code = apply_filters('wpml_element_language_code', null, array('element_id' => $element_id, 'element_type' => $element_type));
		}

		// Enable WPML adjust ID filter
		$icl_adjust_id_url_filter_off = false;

		// Use default language if nothing detected
		return ($lang_code) ? $lang_code : self::get_default_language();
	}

	public static function get_translatepress_language_code($lang) {
		global $translate_press_settings;

		if(!empty($translate_press_settings['url-slugs'])) {
			$lang_code = (!empty($translate_press_settings['url-slugs'][$lang])) ? $translate_press_settings['url-slugs'][$lang] : '';
		}

		return (!empty($lang_code)) ? $lang_code : false;
	}

	public static function get_default_language() {
		global $sitepress, $translate_press_settings;

		if(function_exists('pll_default_language')) {
			$def_lang = pll_default_language('slug');
		} else if(is_object($sitepress)) {
			$def_lang = $sitepress->get_default_language();
		} else if(!empty($translate_press_settings['default-language'])) {
			$def_lang = self::get_translatepress_language_code($translate_press_settings['default-language']);
		} else {
			$def_lang = '';
		}

		return $def_lang;
	}

	public static function get_all_languages($exclude_default_language = false) {
		global $sitepress, $sitepress_settings, $polylang, $translate_press_settings;

		$languages_array = $active_languages = array();
		$default_language = self::get_default_language();

		if(!empty($sitepress_settings['active_languages'])) {
			$languages_array = $sitepress_settings['active_languages'];
		} elseif(function_exists('pll_languages_list')) {
			$languages_array = pll_languages_list(array('fields' => null));
		} if(!empty($translate_press_settings['url-slugs'])) {
			// $languages_array = $translate_press_settings['url-slugs'];
		}

		// Get native language names as value
		if($languages_array) {
			foreach($languages_array as $val) {
				if(!empty($sitepress)) {
					$lang = $val;
					$lang_details = $sitepress->get_language_details($lang);
					$language_name = $lang_details['native_name'];
				} else if(!empty($val->name)) {
					$lang = $val->slug;
					$language_name = $val->name;
				}

				$active_languages[$lang] = (!empty($language_name)) ? sprintf('%s <span>(%s)</span>', $language_name, $lang) : '-';
			}

			// Exclude default language if needed
			if($exclude_default_language && $default_language && !empty($active_languages[$default_language])) {
				unset($active_languages[$default_language]);
			}
		}

		return (array) $active_languages;
	}

	function fix_language_mismatch($item_id, $uri_parts, $is_term = false) {
		global $wp, $language_code, $permalink_manager_options;

		$mode = (!empty($permalink_manager_options['general']['fix_language_mismatch'])) ? $permalink_manager_options['general']['fix_language_mismatch'] : 0;

		if($is_term) {
			$element = get_term($item_id);
			if(!empty($element) && !is_wp_error($element)) {
				$element_id = $element->term_taxonomy_id;
				$element_type = $element->taxonomy;
			} else {
				return false;
			}
		} else {
			$element = get_post($item_id);

			if(!empty($element->post_type)) {
				$element_id = $item_id;
				$element_type = $element->post_type;
			}
		}

		// Stop if no term or post is detected
		if(empty($element)) { return false; }

		// Get the language code of the found post/term
		$element_language_code = self::get_language_code($element);

		// Get the detected language code
		if(defined('ICL_LANGUAGE_CODE')) {
			$detected_language_code = ICL_LANGUAGE_CODE;
		} else if(!empty($uri_parts['lang'])) {
			$detected_language_code = $uri_parts['lang'];
		} else {
			return $item_id;
		}

		if($detected_language_code !== $element_language_code) {
			// A. Display the content in requested language
			if($mode == 1) {
				$item_id = apply_filters('wpml_object_id', $element_id, $element_type);
			}
			// C. Display "404 error"
			else {
				$item_id = 0;
			}
		}

		return $item_id;
	}

	/**
	 * 5C. Fix for WPML (language switcher on blog page)
	 */
	function fix_posts_page($query, $old_query, $uri_parts, $pm_query, $content_type) {
		if(empty($pm_query['id']) || !is_numeric($pm_query['id'])) {
			return $query;
		}

		$blog_page_id = apply_filters('wpml_object_id', get_option('page_for_posts'), 'page');
		$element_id = apply_filters('wpml_object_id', $pm_query['id'], 'page');

		if(!empty($blog_page_id) && !empty($blog_page_id) && ($blog_page_id == $element_id) && !isset($query['page'])) {
			$query['page'] = '';
		}

		return $query;
	}

	function detect_uri_language($uri_parts, $request_url, $endpoints) {
		global $sitepress, $sitepress_settings, $polylang, $translate_press_settings;

		if(!empty($sitepress_settings['active_languages'])) {
			$languages_list = (array) $sitepress_settings['active_languages'];
		} elseif(function_exists('pll_languages_list')) {
			$languages_array = pll_languages_list();
			$languages_list = (is_array($languages_array)) ? (array) $languages_array : "";
		} elseif($translate_press_settings['url-slugs']) {
			$languages_list = $translate_press_settings['url-slugs'];
		}

		if(is_array($languages_list)) {
			$languages_list = implode("|", $languages_list);
		} else {
			return $uri_parts;
		}

		$default_language = self::get_default_language();

		// Fix for multidomain language configuration
		if((isset($sitepress_settings['language_negotiation_type']) && $sitepress_settings['language_negotiation_type'] == 2) || (!empty($polylang->options['force_lang']) && $polylang->options['force_lang'] == 3)) {
			if(!empty($polylang->options['domains'])) {
				$domains = (array) $polylang->options['domains'];
			} else if(!empty($sitepress_settings['language_domains'])) {
				$domains = (array) $sitepress_settings['language_domains'];
			}

			foreach($domains as &$domain) {
				$domain = preg_replace('/((http(s)?:\/\/(www\.)?)|(www\.))?(.+?)\/?$/', 'http://$6', $domain);
			}

			$request_url = trim(str_replace($domains, "", $request_url), "/");
		}

		if(!empty($languages_list)) {
			//preg_match("/^(?:({$languages_list})\/)?(.+?)(?|\/({$endpoints})[\/$]([^\/]*)|\/()([\d+]))?\/?$/i", $request_url, $regex_parts);
			preg_match("/^(?:({$languages_list})\/)?(.+?)(?|\/({$endpoints})(?|\/(.*)|$)|\/()([\d]+)\/?)?$/i", $request_url, $regex_parts);

			$uri_parts['lang'] = (!empty($regex_parts[1])) ? $regex_parts[1] : $default_language;
			$uri_parts['uri'] = (!empty($regex_parts[2])) ? $regex_parts[2] : "";
			$uri_parts['endpoint'] = (!empty($regex_parts[3])) ? $regex_parts[3] : "";
			$uri_parts['endpoint_value'] = (!empty($regex_parts[4])) ? $regex_parts[4] : "";
		}

		return $uri_parts;
	}

	function prepend_lang_prefix($base, $element) {
		global $sitepress_settings, $polylang, $permalink_manager_uris, $translate_press_settings;

		$language_code = self::get_language_code($element);
		$default_language_code = self::get_default_language();
		$home_url = get_home_url();

		// Hide language code if "Use directory for default language" option is enabled
		$hide_prefix_for_default_lang = ((isset($sitepress_settings['urls']['directory_for_default_language']) && $sitepress_settings['urls']['directory_for_default_language'] != 1) || !empty($polylang->links_model->options['hide_default']) || (!empty($translate_press_settings) && $translate_press_settings['add-subdirectory-to-default-language'] !== 'yes')) ? true : false;

		// Last instance - use language paramater from &_GET array
		if(is_admin()) {
			$language_code = (empty($language_code) && !empty($_GET['lang'])) ? $_GET['lang'] : $language_code;
		}

		// Adjust URL base
		if(!empty($language_code)) {
			// A. Different domain per language
			if((isset($sitepress_settings['language_negotiation_type']) && $sitepress_settings['language_negotiation_type'] == 2) || (!empty($polylang->options['force_lang']) && $polylang->options['force_lang'] == 3)) {

				if(!empty($polylang->options['domains'])) {
					$domains = $polylang->options['domains'];
				} else if(!empty($sitepress_settings['language_domains'])) {
					$domains = $sitepress_settings['language_domains'];
				}

				$is_term = (!empty($element->term_taxonomy_id)) ? true : false;
				$element_id = ($is_term) ? "tax-{$element->term_taxonomy_id}" : $element->ID;

				// Filter only custom permalinks
				if(empty($permalink_manager_uris[$element_id]) || empty($domains)) { return $base; }

				// Replace the domain name
				if(!empty($domains[$language_code])) {
					$base = trim($domains[$language_code], "/");

					// Append URL scheme
					if(!preg_match("~^(?:f|ht)tps?://~i", $base)) {
						$scehme = parse_url($home_url, PHP_URL_SCHEME);
						$base = "{$scehme}://{$base}";
			    }
				}
			}
			// B. Prepend language code
			else if(!empty($polylang->options['force_lang']) && $polylang->options['force_lang'] == 2) {
				if($hide_prefix_for_default_lang && ($default_language_code == $language_code)) {
					return $base;
				} else {
					$base = preg_replace('/(https?:\/\/)/', "$1{$language_code}.", $home_url);
				}
			}
			// C. Append prefix
			else {
				if($hide_prefix_for_default_lang && ($default_language_code == $language_code)) {
					return $base;
				} else {
					$base .= "/{$language_code}";
				}
			}
		}

		return $base;
	}

	function append_lang_prefix($permalink, $element) {
		global $sitepress_settings, $polylang, $permalink_manager_uris;

		$language_code = self::get_language_code($element);
		$default_language_code = self::get_default_language();

		// Last instance - use language paramater from &_GET array
		if(is_admin()) {
			$language_code = (empty($language_code) && !empty($_GET['lang'])) ? $_GET['lang'] : $language_code;
		}

		// B. Append ?lang query parameter
		if(isset($sitepress_settings['language_negotiation_type']) && $sitepress_settings['language_negotiation_type'] == 3) {
			if($default_language_code == $language_code) {
				return $permalink;
			} else if(strpos($permalink, "lang=") === false) {
				$permalink .= "?lang={$language_code}";
			}
		}

		return $permalink;
	}

	function language_column_uri_editor($output, $column, $element) {
		$language_code = self::get_language_code($element);
		$output .= (!empty($language_code)) ? sprintf(" | <span><strong>%s:</strong> %s</span>", __("Language"), $language_code) : "";

		return $output;
	}

	function wpml_is_front_page($bool, $page_id, $front_page_id) {
		$default_language_code = self::get_default_language();
		$page_id = apply_filters('wpml_object_id', $page_id, 'page', true, $default_language_code);

		return (!empty($page_id) && $page_id == $front_page_id) ? true : $bool;
	}

	function wpml_ignore_lang_query_parameter($uri_parts) {
		global $permalink_manager_uris;

		foreach($permalink_manager_uris as &$uri) {
			$uri = trim(strtok($uri, '?'), "/");
		}

		return $uri_parts;
	}

	function wpml_redirect() {
		global $language_code, $wp_query;

		if(!empty($language_code) && defined('ICL_LANGUAGE_CODE') && ICL_LANGUAGE_CODE != $language_code && !empty($wp_query->query['do_not_redirect'])) {
			unset($wp_query->query['do_not_redirect']);
		}
	}

	function translate_permastructure($permastructure, $element) {
		global $permalink_manager_permastructs, $pagenow;;

		// Get element language code
		if(!empty($_REQUEST['data']) && strpos($_REQUEST['data'], "target_lang")) {
			$language_code = preg_replace('/(.*target_lang=)([^=&]+)(.*)/', '$2', $_REQUEST['data']);
		} else if(in_array($pagenow, array('post.php', 'post-new.php')) && !empty($_GET['lang'])) {
			$language_code = $_GET['lang'];
		} else if(!empty($_REQUEST['icl_post_language'])) {
			$language_code = $_REQUEST['icl_post_language'];
		} else if(!empty($_POST['action']) && $_POST['action'] == 'pm_save_permalink' && defined('ICL_LANGUAGE_CODE')) {
			$language_code = ICL_LANGUAGE_CODE;
		} else {
			$language_code = self::get_language_code($element);
		}

		if(!empty($element->ID)) {
			$translated_permastructure = (!empty($permalink_manager_permastructs["post_types"]["{$element->post_type}_{$language_code}"])) ? $permalink_manager_permastructs["post_types"]["{$element->post_type}_{$language_code}"] : '';
		} else if(!empty($element->term_id)) {
			$translated_permastructure = (!empty($permalink_manager_permastructs["taxonomies"]["{$element->taxonomy}_{$language_code}"])) ? $permalink_manager_permastructs["taxonomies"]["{$element->taxonomy}_{$language_code}"] : '';
		}

		return (!empty($translated_permastructure)) ? $translated_permastructure : $permastructure;
	}

	function wpml_translate_post_type_slug($post_type_slug, $element, $post_type) {
		$post = (is_integer($element)) ? get_post($element) : $element;
		$language_code = self::get_language_code($post);

		$post_type_slug = apply_filters('wpml_get_translated_slug', $post_type_slug, $post_type, $language_code);

		// Translate %post_type% tag in custom permastructures
		return $post_type_slug;
	}

	function wpml_translate_wc_endpoints($request) {
		global $woocommerce, $wpdb;

		if(!empty($woocommerce->query->query_vars)) {
			// Get WooCommerce original endpoints
			$endpoints = $woocommerce->query->query_vars;

			// Get all endppoint translations
			$endpoint_translations = $wpdb->get_results("SELECT t.value AS translated_endpoint, t.language, s.value AS endpoint FROM {$wpdb->prefix}icl_string_translations AS t LEFT JOIN {$wpdb->prefix}icl_strings AS s ON t.string_id = s.id WHERE context = 'WP Endpoints'");

			// Replace translate endpoint with its original name
			foreach($endpoint_translations as $endpoint) {
				if(isset($request[$endpoint->translated_endpoint]) && ($endpoint->endpoint !== $endpoint->translated_endpoint)) {
					$request[$endpoint->endpoint] = $request[$endpoint->translated_endpoint];
					unset($request[$endpoint->translated_endpoint]);
				}
			}
		}

		return $request;
	}

	/**
	 * Generate custom permalink after WPML's Advanced Translation editor is used
	 */
	function regenerate_uri_after_wpml_translation_completed($post_id, $postdata, $job) {
		global $permalink_manager_uris;

		// Get the default custom permalink based on a permastructure set with Permalink Manager
		if(empty($permalink_manager_uris[$post_id])) {
			$permalink_manager_uris[$post_id] = Permalink_Manager_URI_Functions_Post::get_default_post_uri($post_id);

			// Save the update
			update_option('permalink-manager-uris', $permalink_manager_uris);
		}
	}

	/**
	 * Edit custom URI using WPML Classic Translation Editor
	 */
	function wpml_translation_edit_uri($fields, $job) {
		global $permalink_manager_uris;

		$element_type = (!empty($job->original_post_type) && strpos($job->original_post_type, 'post_') !== false) ? preg_replace('/^(post_)/', '', $job->original_post_type) : '';

		if(!empty($element_type)) {
			$original_id = $job->original_doc_id;
			$translation_id = apply_filters('wpml_object_id', $original_id, $element_type, false, $job->language_code);

			$original_custom_uri = Permalink_Manager_URI_Functions_Post::get_post_uri($original_id, true);

			if(!empty($translation_id)) {
				$translation_custom_uri = Permalink_Manager_URI_Functions_Post::get_post_uri($translation_id, true);
				$uri_translation_complete = (!empty($permalink_manager_uris[$translation_id])) ? '1' : '0';
			} else {
				$translation_custom_uri = $original_custom_uri;
				$uri_translation_complete = '0';
			}

			$fields[] = array(
				'field_type' => 'pm-custom_uri',
				//'tid' => 9999,
				'field_data' => $original_custom_uri,
				'field_data_translated' => $translation_custom_uri,
				'field_finished' => $uri_translation_complete,
				'field_style' => '0',
				'title' => 'Custom URI',
			);
		}

		return $fields;
	}

	function wpml_translation_save_uri($in = '', $data = '', $job = '') {
		global $permalink_manager_uris;

		// A. Save the URI also when the translation is uncompleted
		if(!empty($in['fields'])) {
			$data = $in['fields'];

			$original_id = $in['job_post_id'];
			$element_type = (strpos($in['job_post_type'], 'post_') !== false) ? preg_replace('/^(post_)/', '', $in['job_post_type']) : '';

			$translation_id = apply_filters('wpml_object_id', $original_id, $element_type, false, $in['target_lang']);
		}
		// B. Save the URI also when the translation is completed
		else if(is_numeric($in)) {
			$translation_id = $in;
		}

		if(isset($data['pm-custom_uri']) && isset($data['pm-custom_uri']['data']) && !empty($translation_id)) {
			$permalink_manager_uris[$translation_id] = (!empty($data['pm-custom_uri']['data'])) ? Permalink_Manager_Helper_Functions::sanitize_title($data['pm-custom_uri']['data'], true) : Permalink_Manager_URI_Functions_Post::get_default_post_uri($translation_id);

			update_option('permalink-manager-uris', $permalink_manager_uris);
		}

		// Return the data when the translation is uncompleted
		if(!empty($in['fields'])) {
			return $in;
		}
	}

	function wpml_duplicate_uri($master_post_id, $lang, $post_array, $id) {
		global $permalink_manager_uris;

		// Trigger the function only if duplicate is created in the metabox
		if(empty($_POST['action']) || $_POST['action'] !== 'make_duplicates') { return; }

		$permalink_manager_uris[$id] = Permalink_Manager_URI_Functions_Post::get_default_post_uri($id);

		update_option('permalink-manager-uris', $permalink_manager_uris);
	}

	function pl_allow_canonical_redirect($query, $old_query, $uri_parts, $pm_query, $content_type) {
		global $polylang;

		// Run only if "Hide URL language information for default language" is turned on in Polylang settings
		if(!empty($pm_query['id']) && !empty($pm_query['lang'])) {
			$url_lang = $polylang->links_model->get_language_from_url();
			$def_lang = pll_default_language('slug');

			// Check if the slug of default language is present in the requested URL
			if($url_lang == $def_lang) {
				// Allow canonical redirect
				unset($query['do_not_redirect']);
			}
		}

		return $query;
	}

}
