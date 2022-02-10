<?php
/**
 * TI WooCommerce Wishlist integration with:
 *
 * @name YITH WooCommerce Product Add-Ons
 *
 * @version 2.0.3
 *
 * @slug yith-woocommerce-product-add-ons
 *
 * @url https://wordpress.org/plugins/yith-woocommerce-product-add-ons/
 *
 */

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
	exit;
}

// Load integration depends on current settings.
global $tinvwl_integrations;

$slug = "yith-woocommerce-product-add-ons";

$name = "YITH WooCommerce Product Add-Ons";

$available = class_exists('YITH_WAPO');

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

if (!function_exists('tinv_wishlist_item_meta_yith_woocommerce_product_add_on')) {

	/**
	 * Set description for meta YITH WooCommerce Product Add-on
	 *
	 * @param array $meta Meta array.
	 * @param array $wl_product Wishlist Product.
	 * @param \WC_Product $product Woocommerce Product.
	 *
	 * @return array
	 */
	function tinv_wishlist_item_meta_yith_woocommerce_product_add_on($item_data, $product_id, $variation_id)
	{

		if (isset($item_data['yith_wapo_product_id']) && class_exists('YITH_WAPO')) {
			unset($item_data['yith_wapo_product_id']);

			$id = ($variation_id) ? $variation_id : $product_id;

			$base_product = wc_get_product($id);

			if ((is_object($base_product) && get_option('yith_wapo_settings_show_product_price_cart') == 'yes')) {

				$price = yit_get_display_price($base_product);

				$price_html = wc_price($price);

				$item_data[] = array(
					'key' => __('Base price', 'ti-woocommerce-wishlist'),
					'display' => $price_html,
				);

			}
			if (!empty($item_data['yith_wapo'])) {
				// $total_options_price = 0;
				$cart_data_array = array();
				$first_free_options_count = 0;
				foreach (json_decode($item_data['yith_wapo']['display'], true) as $index => $option) {
					foreach ($option as $key => $value) {
						if ($key && $value) {

							$explode = explode('-', $key);
							if (isset($explode[1])) {
								$addon_id = $explode[0];
								$option_id = $explode[1];
							} else {
								$addon_id = $key;
								$option_id = $value;
							}

							$info = yith_wapo_get_option_info($addon_id, $option_id);

							if ($info['price_type'] == 'percentage') {
								$option_percentage = floatval($info['price']);
								$option_percentage_sale = floatval($info['price_sale']);
								$option_price = ($product_price / 100) * $option_percentage;
								$option_price_sale = ($product_price / 100) * $option_percentage_sale;
							} else if ($info['price_type'] == 'multiplied') {
								$option_price = $info['price'] * $value;
								$option_price_sale = $info['price'] * $value;
							} else {
								$option_price = $info['price'];
								$option_price_sale = $info['price_sale'];
							}

							$sign = $info['price_method'] == 'decrease' ? '-' : '+';

							// First X free options check
							if ($info['addon_first_options_selected'] == 'yes' && $first_free_options_count < $info['addon_first_free_options']) {
								$option_price = 0;
								$first_free_options_count++;
							} else {
								$option_price = $option_price_sale > 0 ? $option_price_sale : $option_price;
							}

							$cart_data_name = ((isset($info['addon_label']) && $info['addon_label'] != '') ? $info['addon_label'] : '');

							if (in_array($info['addon_type'], array('checkbox', 'color', 'label', 'radio', 'select'))) {
								$value = $info['label'];
							} else if (in_array($info['addon_type'], array('product'))) {
								$option_product_info = explode('-', $value);
								$option_product_id = $option_product_info[1];
								$option_product_qty = $option_product_info[2];
								$option_product = wc_get_product($option_product_id);
								$value = $option_product->get_title();

								// product prices
								$product_price = $option_product->get_price();
								if ($info['price_method'] == 'product') {
									$option_price = $product_price;
								} else if ($info['price_method'] == 'discount') {
									$option_discount_value = $option_price;
									$option_price = $product_price - $option_discount_value;
									if ($info['price_type'] == 'percentage') {
										$option_price = $product_price - (($product_price / 100) * $option_discount_value);
									}
								}

							} else if (in_array($info['addon_type'], array('file'))) {
								$file_url = explode('/', $value);
								$value = '<a href="' . $value . '" target="_blank">' . end($file_url) . '</a>';
							} else {
								$cart_data_name = $info['label'];
							}

							$option_price = $option_price != '' ? ($option_price + (($option_price / 100) * yith_wapo_get_tax_rate())) : 0;

							if (get_option('yith_wapo_show_options_in_cart') == 'yes') {
								if (!isset($cart_data_array[$cart_data_name])) {
									$cart_data_array[$cart_data_name] = '';
								}
								$cart_data_array[$cart_data_name] .= '<div>' . $value . ($option_price != '' ? ' (' . $sign . wc_price($option_price) . ')' : '') . '</div>';
							}

						}
					}
				}
				foreach ($cart_data_array as $key => $value) {
					$item_data[] = array(
						'key' => $key,
						'display' => $value,
					);
				}
				unset($item_data['yith_wapo']);

			}

		}

		return $item_data;
	}

	add_filter('tinvwl_wishlist_item_meta_post', 'tinv_wishlist_item_meta_yith_woocommerce_product_add_on', 10, 3);
} // End if().

if (!function_exists('tinvwl_item_price_yith_woocommerce_product_add_on')) {

	/**
	 * Modify price for YITH WooCommerce product Addons.
	 *
	 * @param string $price Returned price.
	 * @param array $wl_product Wishlist Product.
	 * @param \WC_Product $product Woocommerce Product.
	 *
	 * @return string
	 */
	function tinvwl_item_price_yith_woocommerce_product_add_on($price, $wl_product, $product)
	{

		if (class_exists('YITH_WAPO')) {

			if (!empty($wl_product['meta']['yith_wapo'])) {
				$total_options_price = 0;
				$first_free_options_count = 0;
				foreach (json_decode($wl_product['meta']['yith_wapo'], true) as $index => $option) {
					foreach ($option as $key => $value) {
						if ($key && $value) {

							$explode = explode('-', $key);
							if (isset($explode[1])) {
								$addon_id = $explode[0];
								$option_id = $explode[1];
							} else {
								$addon_id = $key;
								$option_id = $value;
							}

							$info = yith_wapo_get_option_info($addon_id, $option_id);

							if ($info['price_type'] == 'percentage') {
								$_product = $product;
								// WooCommerce Measurement Price Calculator (compatibility)
								if (isset($cart_item['pricing_item_meta_data']['_price'])) {
									$product_price = $cart_item['pricing_item_meta_data']['_price'];
								} else {
									$product_price = floatval($_product->get_price());
								}
								$option_percentage = floatval($info['price']);
								$option_percentage_sale = floatval($info['price_sale']);
								$option_price = ($product_price / 100) * $option_percentage;
								$option_price_sale = ($product_price / 100) * $option_percentage_sale;
							} else if ($info['price_type'] == 'multiplied') {
								$option_price = $info['price'] * $value;
								$option_price_sale = $info['price'] * $value;
							} else {
								$option_price = $info['price'];
								$option_price_sale = $info['price_sale'];
							}

							// First X free options check
							if ($info['addon_first_options_selected'] == 'yes' && $first_free_options_count < $info['addon_first_free_options']) {
								$first_free_options_count++;
							} else {
								$option_price = $option_price_sale > 0 ? $option_price_sale : $option_price;


								if (in_array($info['addon_type'], array('product')) && ($info['price_method'] == 'product' || $info['price_method'] == 'discount')) {
									$option_product_info = explode('-', $value);
									$option_product_id = $option_product_info[1];
									$option_product_qty = $option_product_info[2];
									$option_product = wc_get_product($option_product_id);
									$value = $option_product->get_title();
									$product_price = $option_product->get_price();
									if ($info['price_method'] == 'product') {
										$option_price = $product_price;
									} else if ($info['price_method'] == 'discount') {
										$option_discount_value = $option_price;
										$option_price = $product_price - $option_discount_value;
										if ($info['price_type'] == 'percentage') {
											$option_price = $product_price - (($product_price / 100) * $option_discount_value);
										}
									}
									$total_options_price += floatval($option_price);

								} else if ($info['price_method'] == 'decrease') {
									$total_options_price -= floatval($option_price);
								} else {
									$total_options_price += floatval($option_price);
								}
							}

						}
					}
				}

				$base_price = $product->get_price();
				$price = wc_price($base_price + $total_options_price);

			}
		}

		return $price;
	}

	add_filter('tinvwl_wishlist_item_price', 'tinvwl_item_price_yith_woocommerce_product_add_on', 10, 3);
} // End if().

if (!function_exists('tinvwl_add_to_cart_meta_yith_woocommerce_product_add_on')) {

	function tinvwl_add_to_cart_meta_yith_woocommerce_product_add_on($wl_product)
	{
		if (class_exists('YITH_WAPO')) {

			if (!empty($wl_product['meta']['yith_wapo'])) {
				$wl_product['meta']['yith_wapo'] = json_decode($wl_product['meta']['yith_wapo'], true);
			}
		}

		return $wl_product;
	}

	add_filter('tinvwl_addproduct_tocart', 'tinvwl_add_to_cart_meta_yith_woocommerce_product_add_on');
} // End if().
