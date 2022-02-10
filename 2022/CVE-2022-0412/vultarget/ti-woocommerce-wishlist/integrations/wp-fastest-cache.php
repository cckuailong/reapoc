<?php
/**
 * TI WooCommerce Wishlist integration with:
 *
 * @name WP Fastest Cache
 *
 * @version 0.9.1.0
 *
 * @slug wp-fastest-cache
 *
 * @url https://wordpress.org/plugins/wp-fastest-cache/
 *
 */

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
	exit;
}

// Load integration depends on current settings.
global $tinvwl_integrations;

$slug = "wp-fastest-cache";

$name = "WP Fastest Cache";

$available = defined('WPFC_WP_PLUGIN_DIR');

$tinvwl_integrations = is_array( $tinvwl_integrations ) ? $tinvwl_integrations : [];

$tinvwl_integrations[$slug] = array(
	'name' => $name,
	'available' => $available,
);

if (!tinv_get_option('integrations', $slug)) {
	return;
}

if (!$available) {
	return;
}

if (!function_exists('tinvwl_wp_fastest_cache_reject')) {
	/**
	 * Disable cache for WP Fastest Cache
	 */
	function tinvwl_wp_fastest_cache_reject()
	{
		if (defined('WPFC_WP_PLUGIN_DIR')) {
			$rules_json = get_option('WpFastestCacheExclude');
			$ids = array(
				tinv_get_option('page', 'wishlist'),
				tinv_get_option('page', 'manage'),
			);
			$pages = $ids;
			$languages = apply_filters('wpml_active_languages', array(), array(
				'skip_missing' => 0,
				'orderby' => 'code',
			));
			if (!empty($languages)) {
				foreach ($ids as $id) {
					foreach ($languages as $l) {
						$pages[] = apply_filters('wpml_object_id', $id, 'page', true, $l['language_code']);
					}
				}
				$pages = array_unique($pages);
			}
			$pages = array_filter($pages);
			if (!empty($pages)) {
				foreach ($pages as $i => $page) {
					$pages[$i] = preg_replace("/^\//", '', str_replace(get_site_url(), '', get_permalink($page))); // @codingStandardsIgnoreLine Squiz.Strings.DoubleQuoteUsage.NotRequired
				}
			}
			$pages = array_unique($pages);
			$pages = array_filter($pages);

			$rules_std = json_decode($rules_json, true);
			$ex_pages = array();
			$rules_std = is_array($rules_std) ? $rules_std : array();
			foreach ($rules_std as $value) {
				$value['type'] = isset($value['type']) ? $value['type'] : 'page';
				if ('page' === $value['type']) {
					$ex_pages[] = $value['content'];
				}
			}
			$ex_pages = array_unique($ex_pages);
			$ex_pages = array_filter($ex_pages);
			$changed = false;

			foreach ($pages as $page) {
				$page = preg_replace('/\/$/', '', $page);

				if (!in_array($page, $ex_pages)) {
					$changed = true;
					$rules_std[] = array(
						'prefix' => 'startwith',
						'content' => $page,
						'type' => 'page',
					);
				}
			}
			if ($changed) {
				$data = json_encode($rules_std);
				update_option('WpFastestCacheExclude', $data);
			}
		} // End if().
	}

	add_action('admin_init', 'tinvwl_wp_fastest_cache_reject');
} // End if().
