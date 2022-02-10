<?php
/**
 * TI WooCommerce Wishlist integration with:
 *
 * @name WooCommerce Bookings
 *
 * @version 1.15.5
 *
 * @slug woocommerce-bookings
 *
 * @url https://woocommerce.com/products/woocommerce-bookings/
 *
 */

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
	exit;
}

// Load integration depends on current settings.
global $tinvwl_integrations;

$slug = "woocommerce-bookings";

$name = "WooCommerce Bookings";

$available = class_exists('WC_Bookings');

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

if (class_exists('WC_Bookings')) {

	if (!function_exists('tinv_wishlist_metasupport_woocommerce_bookings')) {

		/**
		 * Set description for meta WooCommerce Bookings
		 *
		 * @param array $meta Meta array.
		 * @param integer $product_id Priduct ID.
		 * @param integer $variation_id Variation Product ID.
		 *
		 * @return array
		 */
		function tinv_wishlist_metasupport_woocommerce_bookings($meta, $product_id, $variation_id)
		{
			if (!class_exists('WC_Booking_Form') || !function_exists('is_wc_booking_product')) {
				return $meta;
			}
			$product = wc_get_product($variation_id ? $variation_id : $product_id);
			if (is_wc_booking_product($product)) {
				$booking_form = (version_compare(WC_BOOKINGS_VERSION, '1.15.0', '<')) ? new WC_Booking_Form($product) : new WC_Bookings_Cost_Calculation();
				$post_data = array();
				foreach ($meta as $data) {
					$post_data[$data['key']] = $data['display'];
				}
				$booking_data = (version_compare(WC_BOOKINGS_VERSION, '1.15.0', '<')) ? $booking_form->get_posted_data($post_data) : wc_bookings_get_posted_data($post_data, $product);
				$meta = array();
				foreach ($booking_data as $key => $value) {
					if (!preg_match('/^_/', $key)) {
						$meta[$key] = array(
							'key' => get_wc_booking_data_label($key, $product),
							'display' => $value,
						);
					}
				}
			}

			return $meta;
		}

		add_filter('tinvwl_wishlist_item_meta_post', 'tinv_wishlist_metasupport_woocommerce_bookings', 10, 3);
	} // End if().

	if (!function_exists('tinvwl_item_price_woocommerce_bookings')) {

		/**
		 * Modify price for WooCommerce Bookings
		 *
		 * @param string $price Returned price.
		 * @param array $wl_product Wishlist Product.
		 * @param \WC_Product $product Woocommerce Product.
		 *
		 * @return string
		 */
		function tinvwl_item_price_woocommerce_bookings($price, $wl_product, $product)
		{
			if (!class_exists('WC_Booking_Form') || !function_exists('is_wc_booking_product')) {
				return $price;
			}
			if (is_wc_booking_product($product) && array_key_exists('meta', $wl_product)) {
				$booking_form = (version_compare(WC_BOOKINGS_VERSION, '1.15.0', '<')) ? new WC_Booking_Form($product) : new WC_Bookings_Cost_Calculation();
				$cost = (version_compare(WC_BOOKINGS_VERSION, '1.15.0', '<')) ? $booking_form->calculate_booking_cost($wl_product['meta']) : $booking_form->calculate_booking_cost(wc_bookings_get_posted_data($wl_product['meta'], $product), $product);
				if (is_wp_error($cost)) {
					return $price;
				}

				if ('incl' === get_option('woocommerce_tax_display_shop')) {
					if (function_exists('wc_get_price_excluding_tax')) {
						$display_price = wc_get_price_including_tax($product, array('price' => $cost));
					} else {
						$display_price = $product->get_price_including_tax(1, $cost);
					}
				} else {
					if (function_exists('wc_get_price_excluding_tax')) {
						$display_price = wc_get_price_excluding_tax($product, array('price' => $cost));
					} else {
						$display_price = $product->get_price_excluding_tax(1, $cost);
					}
				}

				if (version_compare(WC_VERSION, '2.4.0', '>=')) {
					$price_suffix = $product->get_price_suffix($cost, 1);
				} else {
					$price_suffix = $product->get_price_suffix();
				}
				$price = wc_price($display_price) . $price_suffix;
			}

			return $price;
		}

		add_filter('tinvwl_wishlist_item_price', 'tinvwl_item_price_woocommerce_bookings', 10, 3);
	} // End if().

	if (!function_exists('tinvwl_item_status_woocommerce_bookings')) {

		/**
		 * Modify availability for WooCommerce Bookings
		 *
		 * @param string $status Status availability.
		 * @param string $availability Default availability.
		 * @param array $wl_product Wishlist Product.
		 * @param \WC_Product $product Woocommerce Product.
		 *
		 * @return type
		 */
		function tinvwl_item_status_woocommerce_bookings($status, $availability, $wl_product, $product)
		{
			if (!class_exists('WC_Booking_Form') || !function_exists('is_wc_booking_product')) {
				return $status;
			}
			if (is_wc_booking_product($product) && array_key_exists('meta', $wl_product)) {
				$booking_form = (version_compare(WC_BOOKINGS_VERSION, '1.15.0', '<')) ? new WC_Booking_Form($product) : new WC_Bookings_Cost_Calculation();
				$cost = (version_compare(WC_BOOKINGS_VERSION, '1.15.0', '<')) ? $booking_form->calculate_booking_cost($wl_product['meta']) : $booking_form->calculate_booking_cost(wc_bookings_get_posted_data($wl_product['meta'], $product), $product);
				if (is_wp_error($cost)) {
					return '<p class="stock out-of-stock"><span><i class="ftinvwl ftinvwl-times"></i></span><span>' . $cost->get_error_message() . '</span></p>';
				}
			}

			return $status;
		}

		add_filter('tinvwl_wishlist_item_status', 'tinvwl_item_status_woocommerce_bookings', 10, 4);
	}

	add_filter('woocommerce_product_object_query_args', 'tinvwl_item_product_type_woocommerce_bookings');

	function tinvwl_item_product_type_woocommerce_bookings($args)
	{
		if (!is_array($args['type'])) {
			$args['type'] = [$args['type']];
		}

		$args['type'][] = 'booking';

		return $args;
	}
}
