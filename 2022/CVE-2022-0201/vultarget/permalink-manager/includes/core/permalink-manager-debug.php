<?php

/**
* Additional debug functions for "Permalink Manager Pro"
*/
class Permalink_Manager_Debug_Functions extends Permalink_Manager_Class {

	public function __construct() {
		add_action('init', array($this, 'debug_data'), 99);
	}

	public function debug_data() {
		add_filter('permalink_manager_filter_query', array($this, 'debug_query'), 9, 5);
		add_filter('permalink_manager_filter_redirect', array($this, 'debug_redirect'), 9, 3);
		add_filter('wp_redirect', array($this, 'debug_wp_redirect'), 9, 2);

		self::debug_custom_redirects();
		self::debug_custom_fields();
	}

	/**
	 * 1. Used in Permalink_Manager_Core_Functions::detect_post();
	 */
	public function debug_query($query, $old_query = null, $uri_parts = null, $pm_query = null, $content_type = null) {
		global $permalink_manager;

		if(isset($_REQUEST['debug_url'])) {
			$debug_info['uri_parts'] = $uri_parts;
			$debug_info['old_query_vars'] = $old_query;
			$debug_info['new_query_vars'] = $query;
			$debug_info['pm_query'] = (!empty($pm_query['id'])) ? $pm_query['id'] : "-";
			$debug_info['content_type'] = (!empty($content_type)) ? $content_type : "-";

			// License key info
			if(class_exists('Permalink_Manager_Pro_Functions')) {
				$license_key = $permalink_manager->functions['pro-functions']->get_license_key();

				// Mask the license key
				$debug_info['license_key'] = preg_replace('/([^-]+)-([^-]+)-([^-]+)-([^-]+)$/', '***-***-$3', $license_key);
			}

			// Plugin version
			$debug_info['version'] = PERMALINK_MANAGER_VERSION;

			self::display_debug_data($debug_info);
		}

		return $query;
	}

	/**
	 * 2. Used in Permalink_Manager_Core_Functions::new_uri_redirect_and_404();
	 */
	public function debug_redirect($correct_permalink, $redirect_type, $queried_object) {
		global $wp_query;

		if(isset($_REQUEST['debug_redirect'])) {
			$debug_info['query_vars'] = $wp_query->query_vars;
			$debug_info['redirect_url'] = (!empty($correct_permalink)) ? $correct_permalink : '-';
			$debug_info['redirect_type'] = (!empty($redirect_type)) ? $redirect_type : "-";
			$debug_info['queried_object'] = (!empty($queried_object)) ? $queried_object : "-";

			self::display_debug_data($debug_info);
		}

		return $correct_permalink;
	}

	/**
	 * 3. Used to debug wp_redirect() function used in 3rd party plugins
	 */
	public function debug_wp_redirect($url, $status) {
 		if(isset($_GET['debug_wp_redirect'])) {
 			$debug_info['url'] = $url;
 			$debug_info['backtrace'] = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 10);

			self::display_debug_data($debug_info);
 		}

 		return $url;
 	}

	/**
	 * 4. Outputs a list of native & custom redirects
	 */
	public function debug_custom_redirects() {
		global $permalink_manager, $permalink_manager_uris, $permalink_manager_redirects;

		if(isset($_GET['debug_custom_redirects']) && current_user_can('manage_options')) {
			if(!empty($permalink_manager_uris)) {
				$uri_post_class = $permalink_manager->functions['uri-functions-post'];
				$uri_tax_class = $permalink_manager->functions['uri-functions-tax'];
				$home_url = Permalink_Manager_Helper_Functions::get_permalink_base();

				remove_filter('_get_page_link', array($uri_post_class, 'custom_post_permalinks'), 99, 2);
				remove_filter('page_link', array($uri_post_class, 'custom_post_permalinks'), 99, 2);
				remove_filter('post_link', array($uri_post_class, 'custom_post_permalinks'), 99, 2);
				remove_filter('post_type_link', array($uri_post_class, 'custom_post_permalinks'), 99, 2);
				remove_filter('attachment_link', array($uri_post_class, 'custom_post_permalinks'), 99, 2);
				remove_filter('term_link', array($uri_tax_class, 'custom_tax_permalinks'), 999, 2 );
				// remove_filter('category_link', array($uri_tax_class, 'custom_tax_permalinks'), 999, 2 );
				// remove_filter('tag_link', array($uri_tax_class, 'custom_tax_permalinks'), 999, 2 );

				$csv = array();

				// Native redirects
				foreach($permalink_manager_uris as $element_id => $uri) {
					if(is_numeric($element_id)) {
						$original_permalink = user_trailingslashit(get_permalink($element_id));
						$custom_permalink = user_trailingslashit($home_url . "/" . $uri);
					} else {
						$term_id = preg_replace("/[^0-9]/", "", $element_id);
						$term = get_term($term_id);

						if(empty($term->taxonomy)) { continue; }

						$original_permalink = user_trailingslashit(get_term_link($term->term_id, $term->taxonomy));
						$custom_permalink = user_trailingslashit($home_url . "/" . $uri);
					}

					if($original_permalink == $custom_permalink && $original_permalink !== '/') { continue; }

					$csv[$element_id] = array(
						'type' => 'native_redirect',
						'from' => $original_permalink,
						'to' => $custom_permalink
					);
				}
			}

			// Custom redirects
			if($permalink_manager_redirects) {
				foreach($permalink_manager_redirects as $element_id => $redirects) {
					if(empty($permalink_manager_uris[$element_id])) { continue; }
					$custom_permalink = user_trailingslashit($home_url . "/" . $permalink_manager_uris[$element_id]);

					if(is_array($redirects)) {
						$redirects = array_values($redirects);
						$redirects_count = count($redirects);

						foreach($redirects as $index => $redirect) {
							$redirect_url = user_trailingslashit($home_url . "/" . $redirect);

							$csv["extra-redirect-{$index}-{$element_id}"] = array(
								'type' => 'extra_redirect',
								'from' => $redirect_url,
								'to' => $custom_permalink
							);
						}
					}
				}
			}

			echo self::output_csv($csv);
			die();
		}
	}

	public static function debug_custom_fields() {
		global $pagenow, $post;

		if($pagenow == 'post.php' && isset($_GET['debug_custom_fields']) && isset($_GET['post'])) {
			$post_id = intval($_GET['post']);
			$custom_fields = get_post_meta($post_id);

			self::display_debug_data($custom_fields);
		}
	}

	/**
	 * A function used to display the debug data in various functions
	 */
	public static function display_debug_data($debug_info) {
		$debug_txt = sprintf("<pre style=\"display:block;\">%s</pre>", print_r($debug_info, true));
		wp_die($debug_txt);
	}

	public static function output_csv($array, $filename = 'debug.csv') {
		if(count($array) == 0) {
			return null;
		}

		// Disable caching
    $now = gmdate("D, d M Y H:i:s");
  	header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
    header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
    header("Last-Modified: {$now} GMT");

    // Force download
    header("Content-Type: application/force-download");
    header("Content-Type: application/octet-stream");
    header("Content-Type: application/download");
		header('Content-Type: text/csv');

    // Disposition / encoding on response body
    header("Content-Disposition: attachment;filename={$filename}");
    header("Content-Transfer-Encoding: binary");

		ob_start();

		$df = fopen("php://output", 'w');

		fputcsv($df, array_keys(reset($array)));
		foreach ($array as $row) {
			fputcsv($df, $row);
		}
		fclose($df);

		echo ob_get_clean();
		die();
	}

}

?>
