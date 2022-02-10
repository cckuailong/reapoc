<?php
/**
 * TI WooCommerce Wishlist integration with:
 *
 * @name WooCommerce Product Table
 *
 * @version 2.6.3
 *
 * @slug woocommerce-product-table
 *
 * @url https://barn2.co.uk/wordpress-plugins/woocommerce-product-table/
 *
 */

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
	exit;
}

// Load integration depends on current settings.
global $tinvwl_integrations;

$slug = "woocommerce-product-table";

$name = "WooCommerce Product Table";

$available = class_exists('Barn2\Plugin\WC_Product_Table');

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
 * Gets data for the 'wishlist' column to use in the product table.
 *
 * @license   GPL-3.0
 */
class TINVWL_Product_Table_Data_Wishlist extends Abstract_Product_Table_Data
{

	public function get_data()
	{
		return apply_filters('wc_product_table_data_wishlist', do_shortcode('[ti_wishlists_addtowishlist loop="yes"]'), $this->product);
	}

}

add_filter('wc_product_table_custom_table_data_wishlist', function ($data_obj, $product, $args) {
	return new TINVWL_Product_Table_Data_Wishlist($product);
}, 10, 3);
