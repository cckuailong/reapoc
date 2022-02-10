<?php
/**
 * TI WooCommerce Wishlist integration with:
 *
 * @name Check Pincode/Zipcode for Shipping Woocommerce
 *
 * @version 1.0
 *
 * @slug check-pincodezipcode-for-shipping-woocommerce
 *
 * @url https://wordpress.org/plugins/check-pincodezipcode-for-shipping-woocommerce/
 *
 */

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
	exit;
}

// Load integration depends on current settings.
global $tinvwl_integrations;

$slug = "check-pincodezipcode-for-shipping-woocommerce";

$name = "Check Pincode/Zipcode for Shipping Woocommerce";

$available = defined('WCZP_PLUGIN_NAME');

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

if (defined('WCZP_PLUGIN_NAME')) {

	/**
	 * Set description for meta Check Pincode/Zipcode for Shipping Woocommerce
	 *
	 * @param array $meta Meta array.
	 * @param array $wl_product Wishlist Product.
	 * @param \WC_Product $product Woocommerce Product.
	 *
	 * @return array
	 */

	function tinv_wishlist_item_meta_wczp($item_data, $product_id, $variation_id)
	{

		foreach (array_keys($item_data) as $key) {
			if (strpos($key, 'wczp') === 0) {
				unset($item_data[$key]);
			}
		}


		return $item_data;
	}

	add_filter('tinvwl_wishlist_item_meta_post', 'tinv_wishlist_item_meta_wczp', 10, 3);
}
