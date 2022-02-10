<?php
/**
 * TI WooCommerce Wishlist integration with:
 *
 * @name OceanWP
 *
 * @version
 *
 * @slug oceanwp
 *
 * @url https://oceanwp.org/
 *
 */

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
	exit;
}

// Load integration depends on current settings.
global $tinvwl_integrations;

$slug = "oceanwp";

$name = "OceanWP Theme";

$available = true;

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

// OceanWP theme compatibility;
if (!function_exists('tinvwl_oceanwp_fix_archive_markup')) {
	add_action('init', 'tinvwl_oceanwp_fix_archive_markup');

	/**
	 * OceanWP theme fix for catalog add to wishlist button position
	 */
	function tinvwl_oceanwp_fix_archive_markup()
	{
		if (class_exists('OceanWP_WooCommerce_Config') && 'above_thumb' === tinv_get_option('add_to_wishlist_catalog', 'position')) {
			remove_action('woocommerce_before_shop_loop_item', 'tinvwl_view_addto_htmlloop', 9);
			add_action('woocommerce_before_shop_loop_item', 'tinvwl_view_addto_htmlloop', 10);
		}
	}
}
