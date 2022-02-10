<?php
/**
 * TI WooCommerce Wishlist integration with:
 *
 * @name LiteSpeed Cache
 *
 * @version 3.5.2
 *
 * @slug litespeed-cache
 *
 * @url https://www.litespeedtech.com/products/cache-plugins/wordpress-acceleration
 *
 */

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
	exit;
}

// Load integration depends on current settings.
global $tinvwl_integrations;

$slug = "litespeed-cache";

$name = "LiteSpeed Cache";

$available = defined('LSWCP_PLUGIN_URL');

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

if (defined('LSWCP_PLUGIN_URL')) {


	// Force ESI nonce.
	if (!function_exists('tinvwl_litespeed_conf_esi_nonce')) {
		add_action('wp_enqueue_scripts', 'tinvwl_litespeed_conf_esi_nonce', 9);

		function tinvwl_litespeed_conf_esi_nonce()
		{
			do_action('litespeed_nonce', 'wp_rest');
		}

	}

	// Force exclude URL
	if (!function_exists('tinvwl_litespeed_conf_exc_uri')) {
		add_action('init', 'tinvwl_litespeed_conf_exc_uri');

		function tinvwl_litespeed_conf_exc_uri()
		{
			$val = apply_filters('litespeed_conf', 'cache-exc');

			$ids = array(
				tinv_get_option('page', 'wishlist'),
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
					$pages[$i] = preg_replace("/^\//", '', rtrim(str_replace(get_site_url(), '', get_permalink(absint($page))), '/')); // @codingStandardsIgnoreLine Squiz.Strings.DoubleQuoteUsage.NotRequired
				}
			}
			$pages = array_unique($pages);
			$pages = array_filter($pages);

			$val = array_unique(array_merge($val, $pages));
			do_action('litespeed_conf_force', 'cache-exc', $val);
		}
	}
}
