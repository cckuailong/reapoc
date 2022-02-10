<?php
/**
 * TI WooCommerce Wishlist integration with:
 *
 * @name Woocommerce Variations Table - Grid
 *
 * @version 1.3.10
 *
 * @slug woo-variations-table-grid
 *
 * @url http://codecanyon.net/item/woocommerce-variations-to-table-grid/10494620
 *
 */

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
	exit;
}

// Load integration depends on current settings.
global $tinvwl_integrations;

$slug = "woo-variations-table-grid";

$name = "Woocommerce Variations Table - Grid";

$available = function_exists('vartable_activate');

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

if (!function_exists('tinvwl_vartable_force_current_product')) {

	/**
	 * Force current variation as global product object
	 *
	 * @param int $product_id product ID.
	 * @param array $values array of vartable values
	 *
	 */
	function tinvwl_vartable_force_current_product($product_id, $values)
	{
		if (!empty($values['variation_id'])) {
			$_product = wc_get_product($values['variation_id']);
			if ($_product) {
				global $vartable_product;
				$vartable_product = $_product;

				add_action('woocommerce_before_add_to_cart_button', 'tinvwl_vartable_set_product', 19);
				add_action('woocommerce_before_add_to_cart_button', 'tinvwl_vartable_reset_product', 21);

				add_action('woocommerce_after_add_to_cart_button', 'tinvwl_vartable_set_product', -1);
				add_action('woocommerce_after_add_to_cart_button', 'tinvwl_vartable_reset_product', 1);

			}
		}

	}

	add_action('vartable_inside_add_to_cart_form', 'tinvwl_vartable_force_current_product', 10, 2);
}

/**
 *
 */
function tinvwl_vartable_set_product()
{
	global $product, $vartable_product, $_product_tmp;
	// store global product data.
	$_product_tmp = $product;
	// store global post data.
	$product = $vartable_product;
}

/**
 *
 */
function tinvwl_vartable_reset_product()
{
	global $product, $_product_tmp;
	// store global post data.
	$product = $_product_tmp;
}

if (!function_exists('tinv_wishlist_meta_support_vartable')) {

	/**
	 * Clear custom meta
	 *
	 * @param array $meta Meta array.
	 *
	 * @return array
	 */
	function tinv_wishlist_meta_support_vartable($meta)
	{

		if (function_exists('vartable_activate')) {

			foreach ($meta as $k => $v) {
				$prefix = 'form_vartable';
				if (0 === strpos($k, $prefix)) {
					unset($meta[$k]);
				}
			}
		}

		return $meta;
	}

	add_filter('tinvwl_wishlist_item_meta_post', 'tinv_wishlist_meta_support_vartable');
} // End if().
