<?php
/**
 * TI WooCommerce Wishlist integration with:
 *
 * @name WooCommerce Advanced Quantity
 *
 * @version 2.4.4
 *
 * @slug woo-advanced-qty
 *
 * @url https://codecanyon.net/item/woocommerce-advanced-quantity/11861326
 *
 */

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
	exit;
}

// Load integration depends on current settings.
global $tinvwl_integrations;

$slug = "woo-advanced-qty";

$name = "WooCommerce Advanced Quantity";

$available = class_exists('Woo_Advanced_QTY_Public');

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

// WooCommerce Advanced Quantity compatibility.
if (!function_exists('tinv_wishlist_qty_woo_advanced_qty')) {

	/**
	 * Force quantity to minimum.
	 *
	 * @param $quantity
	 * @param $product
	 *
	 * @return mixed
	 */
	function tinv_wishlist_qty_woo_advanced_qty($quantity, $product)
	{

		if (class_exists('Woo_Advanced_QTY_Public')) {
			$advanced_qty = new Woo_Advanced_QTY_Public(null, null);

			$args = $advanced_qty->qty_input_args(array(
				'min_value' => 1,
				'max_value' => '',
				'step' => 1,
			), $product);

			$quantity = $args['input_value'];
		}

		return $quantity;
	}

	add_filter('tinvwl_product_add_to_cart_quantity', 'tinv_wishlist_qty_woo_advanced_qty', 10, 2);
}

// WooCommerce Advanced Quantity compatibility.
if (!function_exists('tinv_wishlist_qty_woo_advanced_url')) {

	/**
	 * @param $url
	 * @param $product
	 *
	 * @return string|string[]|null
	 */
	function tinv_wishlist_qty_woo_advanced_url($url, $product)
	{

		if (class_exists('Woo_Advanced_QTY_Public')) {
			if (strpos($url, 'add-to-cart=')) {
				$advanced_qty = new Woo_Advanced_QTY_Public(null, null);
				$args = $advanced_qty->qty_input_args(array(
					'min_value' => 1,
					'max_value' => '',
					'step' => 1,
				), $product);

				$url = preg_replace('/&quantity=[0-9.]*/', '', $url);

				$url .= '&quantity=' . $args['input_value'];
			}
		}

		return $url;
	}

	add_filter('tinvwl_product_add_to_cart_redirect_slug_original', 'tinv_wishlist_qty_woo_advanced_url', 10, 2);
	add_filter('tinvwl_product_add_to_cart_redirect_url_original', 'tinv_wishlist_qty_woo_advanced_url', 10, 2);
	add_filter('tinvwl_product_add_to_cart_redirect_url', 'tinv_wishlist_qty_woo_advanced_url', 10, 2);
}
