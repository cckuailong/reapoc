<?php
/**
 * TI WooCommerce Wishlist integration with:
 *
 * @name WP Rocket
 *
 * @version 3.5.4
 *
 * @slug wp-rocket
 *
 * @url https://wp-rocket.me/
 *
 */

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
	exit;
}

// Load integration depends on current settings.
global $tinvwl_integrations;

$slug = "wp-rocket";

$name = "WP Rocket";

$available = defined('WP_ROCKET_VERSION');

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

if (!function_exists('tinvwl_rocket_cache_dynamic_cookies')) {
	/**
	 * Use dynamic cache with WP Rocket
	 *
	 * @param array $cookies Cookies.
	 *
	 * @return array
	 */
	function tinvwl_rocket_cache_dynamic_cookies($cookies = array())
	{
		$cookies[] = 'tinv_wishlistkey';

		return $cookies;
	}
}

if (!function_exists('tinvwl_rocket_flush')) {
	/**
	 * Use dynamic cache with WP Rocket
	 *
	 * @param array $cookies Cookies.
	 *
	 * @return array
	 */

	function tinvwl_rocket_flush()
	{
		if (!function_exists('flush_rocket_htaccess')
			|| !function_exists('rocket_generate_config_file')) {
			return false;
		}

		// Update WP Rocket .htaccess rules.
		flush_rocket_htaccess();

		// Regenerate WP Rocket config file.
		rocket_generate_config_file();
	}
}

/**
 * Update WP Rocket config.
 *
 */
function tivnwl_wp_rocket()
{
	add_filter('rocket_cache_dynamic_cookies', 'tinvwl_rocket_cache_dynamic_cookies');
	add_filter('rocket_htaccess_mod_rewrite', '__return_false');
	tinvwl_rocket_flush();
}

add_action('tinvwl_flush_rewrite_rules', 'tivnwl_wp_rocket');
add_action('after_rocket_clean_domain', 'tivnwl_wp_rocket');

if (defined('WP_ROCKET_VERSION')) {
	add_action('tinvwl_product_added', 'tinvwl_rocket_clean_dynamic_cache');
	add_action('tinvwl_product_updated', 'tinvwl_rocket_clean_dynamic_cache');
	add_action('tinvwl_product_removed', 'tinvwl_rocket_clean_dynamic_cache');

	/**
	 * Clean dynamic cache on wishlist events.
	 *
	 */
	function tinvwl_rocket_clean_dynamic_cache()
	{

		$key = filter_input(INPUT_COOKIE, 'tinv_wishlistkey', FILTER_VALIDATE_REGEXP, array(
			'options' => array(
				'regexp' => '/^[A-Fa-f0-9]{6}$/',
			),
		));

		if (!$key) {
			return;
		}

		$urls = get_rocket_i18n_uri();

		if (!$urls) {
			return;
		}

		foreach ($urls as $url) {

			$directories = glob(WP_ROCKET_CACHE_PATH . rocket_remove_url_protocol($url), GLOB_NOSORT);

			if ($directories) {
				foreach ($directories as $dir) {
					tinvwl_rocket_remove_dir($dir, $key);
				}
			}
		}
	}


	/**
	 * Clean only dynamic key cache files.
	 *
	 */
	function tinvwl_rocket_remove_dir($dir, $key)
	{
		$dirs = glob($dir . '/*', GLOB_NOSORT);

		if ($dirs) {
			foreach ($dirs as $dir) {
				if (rocket_direct_filesystem()->is_dir($dir)) {
					tinvwl_rocket_remove_dir($dir, $key);
				} elseif (strpos($dir, $key) !== false) {
					rocket_direct_filesystem()->delete($dir);
				}
			}
		}
	}


	add_action('init', 'tinvwl_rocket_empty_cart');

	/**
	 * Prevent cache WooCommerce cart fragments.
	 *
	 */
	function tinvwl_rocket_empty_cart()
	{

		if ((empty($_COOKIE['woocommerce_cart_hash']) || empty($_COOKIE['woocommerce_items_in_cart'])) && apply_filters('tinvwl_rocket_disable_fragmetns_cache', true)) {

			$lang = function_exists('rocket_get_current_language') ? rocket_get_current_language() : false;

			if ($lang) {
				delete_transient('rocket_get_refreshed_fragments_cache_' . $lang);
			}

			delete_transient('rocket_get_refreshed_fragments_cache');
		}
	}

	add_filter('nonce_user_logged_out', 'tinvwl_revert_uid_for_nonce_actions', 100, 2);

	/**
	 * Set $user_id to 0 for certain nonce actions.
	 *
	 * WooCommerce core changes how nonces are used for non-logged customers.
	 * When a user is logged out, but has items in their cart, WC core sets the $uid as a random string customer id.
	 * This is going to mess out nonce validation with WP Rocket and third party plugins which do not bypass WC nonce changes.
	 * WP Rocket caches the page so the nonce $uid will be always different than the session customer $uid.
	 * This function will check the nonce against a UID of 0 because this is how WP Rocket generated the cached page.
	 *
	 *
	 * @param string|int $user_id ID of the nonce-owning user.
	 * @param string|int $action The nonce action.
	 *
	 * @return int $uid      ID of the nonce-owning user.
	 *
	 */
	function tinvwl_revert_uid_for_nonce_actions($user_id, $action)
	{
		// User ID is invalid.
		if (empty($user_id) || 0 === $user_id) {
			return $user_id;
		}

		if (!$action || 'wp_rest' !== $action) {
			return $user_id;
		}

		return 0;
	}
}
