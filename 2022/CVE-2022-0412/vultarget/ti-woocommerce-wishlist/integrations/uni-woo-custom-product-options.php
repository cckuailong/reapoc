<?php
/**
 * TI WooCommerce Wishlist integration with:
 *
 * @name Product Options and Price Calculation Formulas for WooCommerce – Uni CPO
 *
 * @version 4.9.9.1
 *
 * @slug uni-woo-custom-product-options
 *
 * @url https://wordpress.org/plugins/uni-woo-custom-product-options/
 *
 */

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
	exit;
}

// Load integration depends on current settings.
global $tinvwl_integrations;

$slug = "uni-woo-custom-product-options";

$name = "Product Options and Price Calculation Formulas for WooCommerce – Uni CPO";

$available = class_exists('Uni_Cpo');

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

if (!function_exists('tinv_wishlist_item_meta_uni_woo_custom_product_options')) {

	/**
	 * Set description for meta Product Options and Price Calculation Formulas for WooCommerce – Uni CPO
	 *
	 * @param array $meta Meta array.
	 * @param array $wl_product Wishlist Product.
	 * @param \WC_Product $product Woocommerce Product.
	 *
	 * @return array
	 */

	function tinv_wishlist_item_meta_uni_woo_custom_product_options($item_data, $product_id, $variation_id)
	{

		if (class_exists('Uni_Cpo')) {
			foreach (array_keys($item_data) as $key) {
				if (strpos($key, 'cpo_') === 0) {
					unset($item_data[$key]);
				}
			}

			if (!empty($item_data)) {
				$options_eval_result = [];
				$form_data = array();
				$formatted_vars = array();
				$variables = array();
				$product_data = Uni_Cpo_Product::get_product_data_by_id($product_id);
				foreach ($item_data as $key => $value) {
					$form_data[$key] = $value['display'];
				}

				array_walk(
					$options_eval_result,
					function ($v) use (&$variables, &$formatted_vars, &$nice_names_vars) {
						foreach ($v as $slug => $value) {
							// prepare $variables for calculation purpose
							$variables['{' . $slug . '}'] = $value['calc'];
							// prepare $formatted_vars for conditional logic purpose
							$formatted_vars[$slug] = $value['cart_meta'];
						}
					}
				);

				// non option variables
				if ('on' === $product_data['nov_data']['nov_enable']
					&& !empty($product_data['nov_data']['nov'])
				) {
					$variables = uni_cpo_process_formula_with_non_option_vars($variables, $product_data, $formatted_vars);
				}

				$filtered_form_data = array_filter($form_data, function ($k) use ($form_data) {
					return false !== strpos($k, UniCpo()->get_var_slug()) && !empty($form_data[$k]);
				}, ARRAY_FILTER_USE_KEY);

				if (!empty($filtered_form_data)) {
					$posts = uni_cpo_get_posts_by_slugs(array_keys($filtered_form_data));

					if (!empty($posts)) {
						$posts_ids = wp_list_pluck($posts, 'ID');
						foreach ($posts_ids as $post_id) {
							$option = uni_cpo_get_option($post_id);
							if (is_object($option)) {
								$calculate_result = $option->calculate($filtered_form_data);

								if (is_array($calculate_result) && isset($calculate_result[$option->get_slug()])) {
									if (is_array($calculate_result[$option->get_slug()]['order_meta'])) {
										$calculate_result[$option->get_slug()]['order_meta'] = array_map(function ($item) {
											if (!is_numeric($item)) {
												return esc_html__($item);
											} else {
												return $item;
											}
										}, $calculate_result[$option->get_slug()]['order_meta']);
										$display_value = implode(', ', $calculate_result[$option->get_slug()]['order_meta']);
									} else {
										if (!is_numeric($calculate_result[$option->get_slug()]['order_meta'])) {
											$display_value = esc_html__($calculate_result[$option->get_slug()]['order_meta']);
										} else {
											$display_value = $calculate_result[$option->get_slug()]['order_meta'];
										}
									}
									$display_key = uni_cpo_sanitize_label($option->cpo_order_label());
									$display_value = uni_cpo_replace_curly(
										$display_value,
										$formatted_vars,
										$product_data,
										$variables
									);
									$display_value = uni_cpo_get_proper_option_label_cart($display_value);
									$display_key = uni_cpo_replace_curly(
										$display_key,
										$formatted_vars,
										$product_data,
										$variables
									);
									$display_key = trim($display_key, ' ');
									$display_key = uni_cpo_get_proper_option_label_cart($display_key);

									$item_data[$option->get_slug()]['display'] = $display_value;
								}
								$item_data[$option->get_slug()]['key'] = $display_key;
							}
						}
					}

				}

			}

		}

		return $item_data;
	}

	add_filter('tinvwl_wishlist_item_meta_post', 'tinv_wishlist_item_meta_uni_woo_custom_product_options', 10, 3);
}

if (!function_exists('tinvwl_item_price_uni_woo_custom_product_options')) {

	/**
	 * Modify price for Product Options and Price Calculation Formulas for WooCommerce – Uni CPO.
	 *
	 * @param string $price Returned price.
	 * @param array $wl_product Wishlist Product.
	 * @param \WC_Product $product Woocommerce Product.
	 *
	 * @return string
	 */
	function tinvwl_item_price_uni_woo_custom_product_options($price, $wl_product, $product)
	{

		if (class_exists('Uni_Cpo')) {
			$product_data = Uni_Cpo_Product::get_product_data_by_id($product->get_id());

			if ('on' === $product_data['settings_data']['calc_enable']) {
				$form_data = $wl_product['meta'];
				$options_eval_result = array();
				$variables = array();
				$is_calc_disabled = false;
				$formatted_vars = array();

				$main_formula = $product_data['formula_data']['main_formula'];
				$filtered_form_data = array_filter($form_data, function ($k) use ($form_data) {
					return false !== strpos($k, UniCpo()->get_var_slug()) && !empty($form_data[$k]);
				}, ARRAY_FILTER_USE_KEY);


				if (!empty($filtered_form_data)) {
					$posts = uni_cpo_get_posts_by_slugs(array_keys($filtered_form_data));

					if (!empty($posts)) {
						$posts_ids = wp_list_pluck($posts, 'ID');
						foreach ($posts_ids as $post_id) {
							$option = uni_cpo_get_option($post_id);

							if (is_object($option)) {
								$calculate_result = $option->calculate($filtered_form_data);
								if (!empty($calculate_result)) {
									$options_eval_result[$option->get_slug()] = $calculate_result;
								}
							}

						}
					}

				}

				array_walk($options_eval_result, function ($v) use (&$variables, &$formatted_vars) {
					foreach ($v as $slug => $value) {
						// prepare $variables for calculation purpose
						$variables['{' . $slug . '}'] = $value['calc'];
						// prepare $formatted_vars for conditional logic purpose
						$formatted_vars[$slug] = $value['cart_meta'];
					}
				});
				$variables['{uni_cpo_price}'] = $product->get_price('edit');
				// non option variables
				if ('on' === $product_data['nov_data']['nov_enable'] && !empty($product_data['nov_data']['nov'])) {
					$variables = uni_cpo_process_formula_with_non_option_vars($variables, $product_data, $formatted_vars);
				}
				// formula conditional logic

				if ('on' === $product_data['formula_data']['rules_enable'] && !empty($product_data['formula_data']['formula_scheme']) && is_array($product_data['formula_data']['formula_scheme'])) {
					$conditional_formula = uni_cpo_process_formula_scheme($formatted_vars, $product_data);
					if ($conditional_formula) {
						$main_formula = $conditional_formula;
					}
				}

				if ('disable' === $main_formula) {
					$is_calc_disabled = true;
				}
				//

				if (!$is_calc_disabled) {
					$main_formula = uni_cpo_process_formula_with_vars($main_formula, $variables);
					// calculates formula
					$price_calculated = uni_cpo_calculate_formula($main_formula);
					$price_min = $product_data['settings_data']['min_price'];
					$price_max = $product_data['settings_data']['max_price'];
					// check for min price
					if ($price_calculated < $price_min) {
						$price_calculated = $price_min;
					}
					// check for max price
					if (!empty($price_max) && $price_calculated >= $price_max) {
						$is_calc_disabled = true;
					}

					if (true !== $is_calc_disabled) {
						// filter, so 3rd party scripts can hook up
						$price_calculated = apply_filters(
							'uni_cpo_in_cart_calculated_price',
							$price_calculated,
							$product,
							$filtered_form_data
						);

						return wc_price($price_calculated);
					} else {
						return wc_price($price_max);
					}

				} else {
					return wc_price(0);
				}
			}

		}

		return $price;
	}

	add_filter('tinvwl_wishlist_item_price', 'tinvwl_item_price_uni_woo_custom_product_options', 10, 3);
} // End if().
