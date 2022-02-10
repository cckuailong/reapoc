<?php
/**
 * TI WooCommerce Wishlist integration with:
 *
 * @name WooCommerce Square
 *
 * @version 2.3.4
 *
 * @slug woocommerce-square
 *
 * @url https://woocommerce.com/products/square/
 *
 */

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
	exit;
}

// Load integration depends on current settings.
global $tinvwl_integrations;

$slug = "woocommerce-square";

$name = "WooCommerce Square";

$available = class_exists('WooCommerce_Square_Loader');

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

if (!function_exists('tinv_wishlist_metaprepare_woocommerce_square')) {

	/**
	 * Set description for meta WooCommerce Square
	 *
	 * @param array $meta Meta array.
	 * @param integer $product_id Product ID.
	 * @param integer $variation_id Product variation ID.
	 *
	 * @return array
	 */
	function tinv_wishlist_metaprepare_woocommerce_square($meta, $product_id, $variation_id)
	{

		foreach (array_keys($meta) as $key) {
			if (strpos($key, 'nds-pmd') === 0) {
				unset($meta[$key]);
			}
		}

		return $meta;
	}

	add_filter('tinvwl_wishlist_item_meta_post', 'tinv_wishlist_metaprepare_woocommerce_square', 10, 3);
}
