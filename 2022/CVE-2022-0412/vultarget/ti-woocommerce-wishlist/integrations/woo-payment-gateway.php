<?php
/**
 * TI WooCommerce Wishlist integration with:
 *
 * @name Braintree For WooCommerce
 *
 * @version 3.1.8
 *
 * @slug woo-payment-gateway
 *
 * @url https://wordpress.org/plugins/woo-payment-gateway/
 *
 */

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
	exit;
}

// Load integration depends on current settings.
global $tinvwl_integrations;

$slug = "woo-payment-gateway";

$name = "Braintree For WooCommerce";

$available = defined('WC_BRAINTREE_PLUGIN_NAME');

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

if (!function_exists('tinv_wishlist_item_meta_woo_payment_gateway')) {

	/**
	 * Set description for meta Braintree For WooCommerce
	 *
	 * @param array $meta Meta array.
	 * @param array $wl_product Wishlist Product.
	 * @param \WC_Product $product Woocommerce Product.
	 *
	 * @return array
	 */

	function tinv_wishlist_item_meta_woo_payment_gateway($item_data, $product_id, $variation_id)
	{
		if (defined('WC_BRAINTREE_PLUGIN_NAME')) {
			foreach (array_keys($item_data) as $key) {
				if (strpos($key, 'billing_') === 0) {
					unset($item_data[$key]);
				}
				if (strpos($key, 'shipping_') === 0) {
					unset($item_data[$key]);
				}
				if (strpos($key, 'wc_braintree_') === 0) {
					unset($item_data[$key]);
				}
			}
		}

		return $item_data;
	}

	add_filter('tinvwl_wishlist_item_meta_post', 'tinv_wishlist_item_meta_woo_payment_gateway', 10, 3);
}
// End if().
