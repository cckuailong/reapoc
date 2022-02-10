<?php
/**
 * TI WooCommerce Wishlist integration with:
 *
 * @name WooCommerce Rental & Bookings System
 *
 * @version 9.0.5
 *
 * @slug woocommerce-rental-and-booking
 *
 * @url https://codecanyon.net/item/rnb-woocommerce-rental-booking-system/14835145
 *
 */

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
	exit;
}

// Load integration depends on current settings.
global $tinvwl_integrations;

$slug = "woocommerce-rental-and-booking";

$name = "WooCommerce Rental & Bookings System";

$available = class_exists('RedQ_Rental_And_Bookings');

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

if (!function_exists('tinvwl_woocommerce_rental_and_booking_product_type')) {

	/**
	 * Force product type selection.
	 *
	 * @param array $product_types Array of product types.
	 *
	 * @return array
	 */
	function tinvwl_woocommerce_rental_and_booking_product_type($product_types)
	{

		if (class_exists('RedQ_Rental_And_Bookings')) {
			$product_types['redq_rental'] = __('Rental Product', 'ti-woocommerce-wishlist');
		}

		return $product_types;
	}

	add_filter('product_type_selector', 'tinvwl_woocommerce_rental_and_booking_product_type');
}

if (!function_exists('tinv_wishlist_metasupport_woocommerce_rental_and_booking')) {

	/**
	 * Set description for meta WooCommerce Rental & Bookings System
	 *
	 * @param array $meta Meta array.
	 * @param integer $product_id Priduct ID.
	 * @param integer $variation_id Variation Product ID.
	 *
	 * @return array
	 */
	function tinv_wishlist_metasupport_woocommerce_rental_and_booking($meta, $product_id, $variation_id)
	{
		if (!class_exists('RedQ_Rental_And_Bookings')) {
			return $meta;
		}

		$product_type = wc_get_product($product_id)->get_type();

		if (isset($product_type) && $product_type === 'redq_rental') {

			$custom_data = array();

			$options_data = array();
			$options_data['quote_id'] = '';

			$get_labels = redq_rental_get_settings($product_id, 'labels', array(
				'pickup_location',
				'return_location',
				'pickup_date',
				'return_date',
				'resources',
				'categories',
				'person',
				'deposites'
			));
			$labels = $get_labels['labels'];
			$get_displays = redq_rental_get_settings($product_id, 'display');
			$displays = $get_displays['display'];

			$get_conditions = redq_rental_get_settings($product_id, 'conditions');
			$conditional_data = $get_conditions['conditions'];

			$get_general = redq_rental_get_settings($product_id, 'general');
			$general_data = $get_general['general'];

			if (isset($meta['quote_id'])) {
				$custom_data[] = array(
					'key' => $options_data['quote_id'] ? $options_data['quote_id'] : __('Quote Request', 'ti-woocommerce-wishlist'),
					'display' => '#' . $meta['quote_id']['display'],

				);
			}

			if (isset($meta['pickup_location'])) {
				$custom_data[] = array(
					'key' => $labels['pickup_location'],
					'display' => $meta['pickup_location']['display']['address'],

				);
			}

			if (isset($meta['pickup_location']) && !empty($meta['pickup_location']['cost'])) {
				$custom_data[] = array(
					'key' => $labels['pickup_location'] . __(' Cost', 'ti-woocommerce-wishlist'),
					'display' => wc_price($meta['pickup_location']['display']['cost']),

				);
			}

			if (isset($meta['dropoff_location'])) {
				$custom_data[] = array(
					'key' => $labels['return_location'],
					'display' => $meta['dropoff_location']['display']['address'],

				);
			}

			if (isset($meta['dropoff_location']) && !empty($meta['dropoff_location']['cost'])) {
				$custom_data[] = array(
					'key' => $labels['return_location'] . __(' Cost', 'ti-woocommerce-wishlist'),
					'display' => wc_price($meta['dropoff_location']['display']['cost']),

				);
			}

			if (isset($meta['location_cost'])) {
				$custom_data[] = array(
					'key' => esc_html__('Location Cost', 'ti-woocommerce-wishlist'),
					'display' => wc_price($meta['location_cost']['display']),

				);
			}

			if (isset($meta['payable_cat'])) {
				$cat_name = '';
				foreach ($meta['payable_cat']['display'] as $key => $value) {
					if ($value['multiply'] === 'per_day') {
						$cat_name .= $value['key'] . '×' . $value['quantity'] . ' ( ' . wc_price($value['cost']) . ' - ' . __('Per Day', 'ti-woocommerce-wishlist') . ' )' . ' , <br> ';
					} else {
						$cat_name .= $value['key'] . '×' . $value['quantity'] . ' ( ' . wc_price($value['cost']) . ' - ' . __('One Time', 'ti-woocommerce-wishlist') . ' )' . ' , <br> ';
					}
				}
				$custom_data[] = array(
					'key' => $labels['categories'],
					'display' => $cat_name,

				);
			}

			if (isset($meta['payable_resource'])) {
				$resource_name = '';
				foreach ($meta['payable_resource']['display'] as $key => $value) {
					if ($value['cost_multiply'] === 'per_day') {
						$resource_name .= $value['resource_name'] . ' ( ' . wc_price($value['resource_cost']) . ' - ' . __('Per Day', 'ti-woocommerce-wishlist') . ' )' . ' , <br> ';
					} else {
						$resource_name .= $value['resource_name'] . ' ( ' . wc_price($value['resource_cost']) . ' - ' . __('One Time', 'ti-woocommerce-wishlist') . ' )' . ' , <br> ';
					}
				}
				$custom_data[] = array(
					'key' => $labels['resource'],
					'display' => $resource_name,

				);
			}

			if (isset($meta['payable_security_deposites'])) {
				$security_deposite_name = '';
				foreach ($meta['payable_security_deposites']['display'] as $key => $value) {
					if ($value['cost_multiply'] === 'per_day') {
						$security_deposite_name .= $value['security_deposite_name'] . ' ( ' . wc_price($value['security_deposite_cost']) . ' - ' . __('Per Day', 'ti-woocommerce-wishlist') . ' )' . ' , <br> ';
					} else {
						$security_deposite_name .= $value['security_deposite_name'] . ' ( ' . wc_price($value['security_deposite_cost']) . ' - ' . __('One Time', 'ti-woocommerce-wishlist') . ' )' . ' , <br> ';
					}
				}
				$custom_data[] = array(
					'key' => $labels['deposite'],
					'display' => $security_deposite_name,

				);
			}

			if (isset($meta['adults_info'])) {
				$custom_data[] = array(
					'key' => $labels['adults'],
					'display' => $meta['adults_info']['display']['person_count'],

				);
			}

			if (isset($meta['childs_info'])) {
				$custom_data[] = array(
					'key' => $labels['childs'],
					'display' => $meta['childs_info']['display']['person_count'],

				);
			}


			if (isset($meta['pickup_date']) && $displays['pickup_date'] === 'open') {

				$pickup_date_time = convert_to_output_format($meta['pickup_date']['display'], $conditional_data['date_format']);

				if (isset($meta['pickup_time'])) {
					$pickup_date_time = $pickup_date_time . ' ' . esc_html__('at', 'ti-woocommerce-wishlist') . ' ' . $meta['pickup_time']['display'];
				}
				$custom_data[] = array(
					'key' => $labels['pickup_datetime'],
					'display' => $pickup_date_time,

				);
			}

			if (isset($meta['dropoff_date']) && $displays['return_date'] === 'open') {

				$return_date_time = convert_to_output_format($meta['dropoff_date']['display'], $conditional_data['date_format']);

				if (isset($meta['dropoff_time'])) {
					$return_date_time = $return_date_time . ' ' . esc_html__('at', 'ti-woocommerce-wishlist') . ' ' . $meta['dropoff_time']['display'];
				}

				$custom_data[] = array(
					'key' => $labels['return_datetime'],
					'display' => $return_date_time,

				);
			}

			if (isset($meta['rental_days_and_costs'])) {
				if ($meta['rental_days_and_costs']['display']['days'] > 0) {
					$custom_data[] = array(
						'key' => $general_data['total_days'] ? $general_data['total_days'] : esc_html__('Total Days', 'ti-woocommerce-wishlist'),
						'display' => $meta['rental_days_and_costs']['display']['days'],

					);
				} else {
					$custom_data[] = array(
						'key' => $general_data['total_hours'] ? $general_data['total_hours'] : esc_html__('Total Hours', 'ti-woocommerce-wishlist'),
						'display' => $meta['rental_days_and_costs']['display']['hours'],

					);
				}

				if (!empty($meta['rental_days_and_costs']['due_payment'])) {
					$custom_data[] = array(
						'key' => $general_data['payment_due'] ? $general_data['payment_due'] : esc_html__('Due Payment', 'ti-woocommerce-wishlist'),
						'display' => wc_price($meta['rental_days_and_costs']['display']['due_payment']),

					);
				}
			}

			return $custom_data;
		}

		return $meta;
	}

	add_filter('tinvwl_wishlist_item_meta_post', 'tinv_wishlist_metasupport_woocommerce_rental_and_booking', 20, 3);
} // End if().

if (!function_exists('tinvwl_item_price_woocommerce_rental_and_booking')) {

	/**
	 * Modify price for WooCommerce Rental & Bookings System
	 *
	 * @param string $price Returned price.
	 * @param array $wl_product Wishlist Product.
	 * @param \WC_Product $product Woocommerce Product.
	 *
	 * @return string
	 */
	function tinvwl_item_price_woocommerce_rental_and_booking($price, $wl_product, $product)
	{
		if (!class_exists('RedQ_Rental_And_Bookings')) {
			return $price;
		}

		$product_type = $product->get_type();

		if (isset($product_type) && $product_type === 'redq_rental') {
			$meta = $wl_product['meta'];

			if (!empty($meta['quote_price'])) {

				$price = $meta['quote_price'];

				return wc_price($price);
			}
		}


		return $price;
	}

	add_filter('tinvwl_wishlist_item_price', 'tinvwl_item_price_woocommerce_rental_and_booking', 20, 3);
} // End if().
