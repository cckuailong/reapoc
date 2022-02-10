<?php
/**
 * TI WooCommerce Wishlist integration with:
 *
 * @name WP Multilang
 *
 * @version 2.3.0
 *
 * @slug wp-multilang
 *
 * @url https://wordpress.org/plugins/wp-multilang/
 *
 */

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
	exit;
}

// Load integration depends on current settings.
global $tinvwl_integrations;

$slug = "wp-multilang";

$name = "WP Multilang";

$available = function_exists('wpm_translate_string');

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

// WP Multilang string translations.
if (function_exists('wpm_translate_string')) {

	add_filter('tinvwl_default_wishlist_title', 'wpm_translate_string');
	add_filter('tinvwl_view_wishlist_text', 'wpm_translate_string');
	add_filter('tinvwl_added_to_wishlist_text', 'wpm_translate_string');
	add_filter('tinvwl_already_in_wishlist_text', 'wpm_translate_string');
	add_filter('tinvwl_removed_from_wishlist_text', 'wpm_translate_string');
	add_filter('tinvwl_remove_from_wishlist_text', 'wpm_translate_string');

	add_filter('tinvwl_added_to_wishlist_text_loop', 'wpm_translate_string');
	add_filter('tinvwl_remove_from_wishlist_text_loop', 'wpm_translate_string');

	add_filter('tinvwl_add_to_cart_text', 'wpm_translate_string');

	add_filter('tinvwl_add_selected_to_cart_text', 'wpm_translate_string');
	add_filter('tinvwl_add_all_to_cart_text', 'wpm_translate_string');

	add_filter('tinvwl_share_on_text', 'wpm_translate_string');

	add_filter('tinvwl_wishlist_products_counter_text', 'wpm_translate_string');

} // End if().
