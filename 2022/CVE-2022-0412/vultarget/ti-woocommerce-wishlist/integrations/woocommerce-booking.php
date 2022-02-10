<?php
/**
 * TI WooCommerce Wishlist integration with:
 *
 * @name Booking & Appointment Plugin for WooCommerce
 *
 * @version 4.14.0
 *
 * @slug woocommerce-booking
 *
 * @url http://www.tychesoftwares.com/store/premium-plugins/woocommerce-booking-plugin
 *
 */

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
	exit;
}

// Load integration depends on current settings.
global $tinvwl_integrations;

$slug = "woocommerce-booking";

$name = "Booking & Appointment Plugin for WooCommerce";

$available = class_exists('woocommerce_booking');

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

if (!function_exists('tinv_wishlist_metasupport_woocommerce_booking')) {

	/**
	 * Set description for meta Booking & Appointment Plugin for WooCommerce
	 *
	 * @param array $meta Meta array.
	 * @param integer $product_id Priduct ID.
	 * @param integer $variation_id Variation Product ID.
	 *
	 * @return array
	 */
	function tinv_wishlist_metasupport_woocommerce_booking($meta, $product_id, $variation_id)
	{
		if (!class_exists('woocommerce_booking')) {
			return $meta;
		}

		if (!empty($meta['bkap_price_charged'])) {

			$item_data = array(
				'bkap_booking' => array(),
				'product_id' => $product_id,
			);

			if (!empty($meta['booking_calender'])) {
				$item_data['bkap_booking'][0]['date'] = $meta['booking_calender']['display'];
			}

			if (!empty($meta['booking_calender_checkout'])) {
				$item_data['bkap_booking'][0]['date_checkout'] = $meta['booking_calender_checkout']['display'];
			}

			if (!empty($meta['time_slot'])) {
				$item_data['bkap_booking'][0]['time_slot'] = $meta['time_slot']['display'];
			}
			if (!empty($meta['bkap_front_resource_selection'])) {
				$item_data['bkap_booking'][0]['resource_id'] = $meta['bkap_front_resource_selection']['display'];
			}

			$custom_meta = bkap_cart::bkap_get_item_data_booking(array(), $item_data);

			foreach ($custom_meta as $key => $item) {
				$custom_meta[$key]['key'] = $item['name'];
				unset($custom_meta[$key]['name']);
			}

			if ($custom_meta) {
				return $custom_meta;
			}
		}

		return $meta;
	}

	add_filter('tinvwl_wishlist_item_meta_post', 'tinv_wishlist_metasupport_woocommerce_booking', 20, 3);
} // End if().

if (!function_exists('tinvwl_item_price_woocommerce_booking')) {

	/**
	 * Modify price for Booking & Appointment Plugin for WooCommerce
	 *
	 * @param string $price Returned price.
	 * @param array $wl_product Wishlist Product.
	 * @param \WC_Product $product Woocommerce Product.
	 *
	 * @return string
	 */
	function tinvwl_item_price_woocommerce_booking($price, $wl_product, $product)
	{
		if (!class_exists('woocommerce_booking')) {
			return $price;
		}

		$meta = $wl_product['meta'];

		if (!empty($meta['bkap_price_charged'])) {

			$price = $meta['bkap_price_charged'];

			return wc_price($price);
		}

		return $price;
	}

	add_filter('tinvwl_wishlist_item_price', 'tinvwl_item_price_woocommerce_booking', 20, 3);
} // End if().
