<?php
/**
 * TI WooCommerce Wishlist integration with:
 *
 * @name Quick Buy Now Button for WooCommerce
 *
 * @version 1.3.6
 *
 * @slug buy-now-button-for-woocommerce
 *
 * @url https://woocommerce.com/products/quick-buy-now-button-for-woocommerce/
 *
 */

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
	exit;
}

// Load integration depends on current settings.
global $tinvwl_integrations;

$slug = "buy-now-button-for-woocommerce";

$name = "Quick Buy Now Button for WooCommerce";

$available = class_exists('Class_Addify_Quick_Buy');

$tinvwl_integrations = is_array($tinvwl_integrations) ? $tinvwl_integrations : [];

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

/**
 * Set description for meta Quick Buy Now Button for WooCommerce
 *
 * @param array $meta Meta array.
 * @param array $wl_product Wishlist Product.
 * @param \WC_Product $product Woocommerce Product.
 *
 * @return array
 */

function tinv_wishlist_item_meta_apbgp($item_data, $product_id, $variation_id)
{
	foreach (array_keys($item_data) as $key) {
		if (strpos($key, 'aqbp') === 0 || strpos($key, 'afqb') === 0) {
			unset($item_data[$key]);
		}
	}

	return $item_data;
}

add_filter('tinvwl_wishlist_item_meta_post', 'tinv_wishlist_item_meta_apbgp', 10, 3);
