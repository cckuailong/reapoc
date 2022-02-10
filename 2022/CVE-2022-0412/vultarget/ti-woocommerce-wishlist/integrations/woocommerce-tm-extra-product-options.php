<?php
/**
 * TI WooCommerce Wishlist integration with:
 *
 * @name WooCommerce TM Extra Product Options
 *
 * @version 5.0.12.12
 *
 * @slug woocommerce-tm-extra-product-options
 *
 * @url https://codecanyon.net/item/woocommerce-extra-product-options/7908619
 *
 */

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
	exit;
}

// Load integration depends on current settings.
global $tinvwl_integrations;

$slug = "woocommerce-tm-extra-product-options";

$name = "WooCommerce TM Extra Product Options";

$available = (defined('THEMECOMPLETE_EPO_VERSION') || defined('TM_EPO_VERSION'));

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

if (!function_exists('tinv_wishlist_metasupport_woocommerce_tm_extra_product_options')) {

	/**
	 * Set description for meta WooCommerce TM Extra Product Options
	 *
	 * @param array $meta Meta array.
	 * @param integer $product_id Product ID.
	 * @param integer $variation_id Product variation ID.
	 *
	 * @return array
	 */
	function tinv_wishlist_metasupport_woocommerce_tm_extra_product_options($meta, $product_id, $variation_id)
	{
		if (array_key_exists('tcaddtocart', $meta) && (defined('THEMECOMPLETE_EPO_VERSION') || defined('TM_EPO_VERSION'))) {
			$api = defined('THEMECOMPLETE_EPO_VERSION') ? THEMECOMPLETE_EPO_API() : TM_EPO_API();
			$core = defined('THEMECOMPLETE_EPO_VERSION') ? THEMECOMPLETE_EPO() : TM_EPO();
			$version = defined('THEMECOMPLETE_EPO_VERSION') ? THEMECOMPLETE_EPO_VERSION : TM_EPO_VERSION;
			$cart = defined('THEMECOMPLETE_EPO_VERSION') ? new THEMECOMPLETE_EPO_Cart() : TM_EPO();

			$has_epo = $api->has_options($product_id);
			if ($api->is_valid_options($has_epo)) {
				$post_data = array();
				foreach ($meta as $key => $value) {
					$post_data[$key] = $value['display'];
				}
				$post_data['add-to-cart'] = $meta['tcaddtocart']['display'];
				$post_data['product_id'] = $product_id;
				if ($variation_id) {
					$post_data['variation_id'] = $variation_id;
				}
				$post_data['quantity'] = 1;

				$cart_class = version_compare($version, '4.8.0', '<') ? $core : $cart;

				$cart_item = $cart_class->add_cart_item_data_helper(array(), $product_id, $post_data);
				if ('normal' == $core->tm_epo_hide_options_in_cart && 'advanced' != $core->tm_epo_cart_field_display && !empty($cart_item['tmcartepo'])) {
					$cart_item['quantity'] = 1;
					$cart_item['data'] = wc_get_product($variation_id ? $variation_id : $product_id);
					$cart_item['tm_cart_item_key'] = '';
					$cart_item['product_id'] = $product_id;
					$item_data = $cart_class->get_item_data_array(array(), $cart_item);

					foreach ($item_data as $key => $data) {
						// Set hidden to true to not display meta on cart.
						if (!empty($data['hidden'])) {
							unset($item_data[$key]);
							continue;
						}
						$item_data[$key]['key'] = !empty($data['key']) ? $data['key'] : $data['name'];
						$item_data[$key]['display'] = !empty($data['display']) ? $data['display'] : $data['value'];
					}

					return $item_data;
				}
			}

			return array();
		}

		return $meta;
	}

	add_filter('tinvwl_wishlist_item_meta_post', 'tinv_wishlist_metasupport_woocommerce_tm_extra_product_options', 10, 3);
} // End if().

if (!function_exists('tinvwl_row_woocommerce_tm_extra_product_options')) {

	/**
	 * Add rows for sub product for WooCommerce TM Extra Product Options
	 *
	 * @param array $wl_product Wishlist Product.
	 * @param \WC_Product_Composite $product Woocommerce Product.
	 */
	function tinvwl_row_woocommerce_tm_extra_product_options($wl_product, $product)
	{
		if (isset($wl_product['meta']) && is_array($wl_product['meta']) && array_key_exists('tcaddtocart', $wl_product['meta']) && (defined('THEMECOMPLETE_EPO_VERSION') || defined('TM_EPO_VERSION'))) {
			$api = defined('THEMECOMPLETE_EPO_VERSION') ? THEMECOMPLETE_EPO_API() : TM_EPO_API();
			$core = defined('THEMECOMPLETE_EPO_VERSION') ? THEMECOMPLETE_EPO() : TM_EPO();

			$has_epo = $api->has_options($product->get_id());

			if ($api->is_valid_options($has_epo)) {
				$cpf_price_array = $core->get_product_tm_epos($product->get_id(), TRUE, TRUE, TRUE);
				if (empty($cpf_price_array)) {
					return FALSE;
				}
				$global_price_array = $cpf_price_array['global'];

				foreach ($global_price_array as $priorities) {
					foreach ($priorities as $field) {
						foreach ($field['sections'] as $section_id => $section) {
							if (isset($section['elements'])) {
								foreach ($section['elements'] as $element) {
									if ('product' === $element['type']) {
										$current_tmcp_post_fields = array_intersect_key($wl_product['meta'], array_flip(array($element['name_inc'])));
										foreach ($current_tmcp_post_fields as $attribute => $key) {
											if (isset($wl_product['meta'][$attribute . '_quantity'])) {
												if (empty($wl_product['meta'][$attribute . '_quantity'])) {
													continue;
												}
											}

											$_product = wc_get_product($key);

											$product_url = $_product->get_permalink();
											$product_image = $_product->get_image();
											$product_title = is_callable(array(
												$_product,
												'get_name'
											)) ? $_product->get_name() : $_product->get_title();

											$product_price = '';

											if ($element['priced_individually']) {
												$product_price = $_product->get_price();
											}

											if ($element['discount']) {
												$discount = wc_format_decimal((double)$element['discount'], wc_get_price_decimals());

												if ($product_price && $element['discount']) {

													$price = wc_format_decimal((double)$product_price, wc_get_price_decimals());

													if ($element['discount_type'] == 'fixed') {
														$product_price = max($price - $discount, 0);
													} else {
														$product_price = max($price * ((100 - $discount) / 100), 0);
													}

												}
											}

											$product_price = wc_price($product_price);

											if ($_product->is_visible()) {
												$product_image = sprintf('<a href="%s">%s</a>', esc_url($product_url), $product_image);
												$product_title = sprintf('<a href="%s">%s</a>', esc_url($product_url), $product_title);
											}
											$product_title .= tinv_wishlist_get_item_data($_product, $wl_product);

											$row_string = '<tr>';
											$row_string .= ((!is_user_logged_in() || get_current_user_id() !== $wl_product['author']) ? ((!tinv_get_option('table', 'colm_checkbox')) ? '' : '<td colspan="1"></td>') : '<td colspan="' . ((!tinv_get_option('table', 'colm_checkbox')) ? '1' : '2') . '"></td>') . '&nbsp;';
											$row_string .= ($element['show_image']) ? '<td class="product-thumbnail">%2$s</td>' : '<td class="product-thumbnail">&nbsp;</td>';

											$row_string .= ($element['show_title']) ? '<td class="product-name">%1$s</td>' : '<td class="product-name">&nbsp;</td>';

											if (tinv_get_option('product_table', 'colm_price')) {
												$row_string .= ($product_price && $element['show_price'] && $element['priced_individually']) ? '<td class="product-price">%4$s &times; %6$s</td>' : '<td class="product-price">&times; %6$s</td>';
											}
											if (tinv_get_option('product_table', 'colm_date')) {
												$row_string .= '<td class="product-date">&nbsp;</td>';
											}
											if (tinv_get_option('product_table', 'colm_stock')) {
												$row_string .= '<td class="product-stock">%5$s</td>';
											}
											if (tinv_get_option('product_table', 'colm_quantity')) {
												$row_string .= '<td class="product-quantity">&nbsp;</td>';
											}
											if (tinv_get_option('product_table', 'add_to_cart')) {
												$row_string .= '<td class="product-action">&nbsp;</td>';
											}
											$row_string .= '</tr>';

											echo sprintf($row_string, is_callable(array(
												$_product,
												'get_name'
											)) ? $_product->get_name() : $_product->get_title(), $product_image, $product_title, $product_price, '', $wl_product['meta'][$attribute . '_quantity']); // WPCS: xss ok.

										}
									}
								}
							}
						}
					}
				} // End if().
			}
		}
	}

	add_action('tinvwl_wishlist_row_after', 'tinvwl_row_woocommerce_tm_extra_product_options', 10, 2);
} // End if().

if (!function_exists('tinvwl_item_price_woocommerce_tm_extra_product_options')) {

	/**
	 * Modify price for WooCommerce TM Extra Product Options
	 *
	 * @param string $price Returned price.
	 * @param array $wl_product Wishlist Product.
	 * @param \WC_Product $product Woocommerce Product.
	 *
	 * @return string
	 */
	function tinvwl_item_price_woocommerce_tm_extra_product_options($price, $wl_product, $product)
	{
		if (array_key_exists('tcaddtocart', (array)@$wl_product['meta']) && (defined('THEMECOMPLETE_EPO_VERSION') || defined('TM_EPO_VERSION'))) {

			$api = defined('THEMECOMPLETE_EPO_VERSION') ? THEMECOMPLETE_EPO_API() : TM_EPO_API();
			$core = defined('THEMECOMPLETE_EPO_VERSION') ? THEMECOMPLETE_EPO() : TM_EPO();
			$version = defined('THEMECOMPLETE_EPO_VERSION') ? THEMECOMPLETE_EPO_VERSION : TM_EPO_VERSION;
			$cart = defined('THEMECOMPLETE_EPO_VERSION') ? new THEMECOMPLETE_EPO_Cart() : TM_EPO();
			if ($core->tm_epo_hide_options_in_cart == 'normal') {
				$product_id = $wl_product['product_id'];
				$has_epo = $api->has_options($product_id);
				if ($api->is_valid_options($has_epo)) {

					$cart_class = version_compare($version, '4.8.0', '<') ? $core : $cart;

					$cart_item = $cart_class->add_cart_item_data_helper(array(), $product_id, $wl_product['meta']);
					$cart_item['quantity'] = 1;
					$cart_item['data'] = $product;

					$product_price = apply_filters('wc_epo_add_cart_item_original_price', $cart_item['data']->get_price(), $cart_item);
					if (!empty($cart_item['tmcartepo'])) {
						$to_currency = version_compare($version, '4.9.0', '<') ? tc_get_woocommerce_currency() : themecomplete_get_woocommerce_currency();
						foreach ($cart_item['tmcartepo'] as $value) {
							if (isset($value['price_per_currency']) && array_key_exists($to_currency, $value['price_per_currency'])) {
								$value = floatval($value['price_per_currency'][$to_currency]);
								$product_price += $value;
							} else {
								$product_price += floatval($value['price']);
							}
						}
					}

					$price = apply_filters('wc_tm_epo_ac_product_price', apply_filters('woocommerce_cart_item_price', $cart_class->get_price_for_cart($product_price, $cart_item, ''), $cart_item, ''), '', $cart_item, $product, $product_id);
				}
			}
		}

		return $price;
	}

	add_filter('tinvwl_wishlist_item_price', 'tinvwl_item_price_woocommerce_tm_extra_product_options', 10, 3);
} // End if().

add_filter('tinvwl_addtowishlist_prepare_form', 'tinvwl_meta_woocommerce_tm_extra_product_options', 10, 3);

function tinvwl_meta_woocommerce_tm_extra_product_options($meta, $post, $files)
{

	if (defined('THEMECOMPLETE_EPO_VERSION') || defined('TM_EPO_VERSION')) {
		foreach ($files as $name => $file) {

			if (array_key_exists($name, $meta)) {
				$upload = THEMECOMPLETE_EPO()->upload_file($file);
				if (empty($upload['error']) && !empty($upload['file'])) {
					$meta[$name] = wc_clean($upload['url']);
				}
			}
		}
	}

	return $meta;
}

add_filter('tinvwl_product_prepare_meta', 'tinvwl_cart_meta_woocommerce_tm_extra_product_options');

function tinvwl_cart_meta_woocommerce_tm_extra_product_options($meta)
{

	if (defined('THEMECOMPLETE_EPO_VERSION') || defined('TM_EPO_VERSION')) {

		$files = $_FILES;

		foreach ($files as $name => $file) {

			if (!array_key_exists($name, $meta)) {
				$upload = THEMECOMPLETE_EPO()->upload_file($file);
				if (empty($upload['error']) && !empty($upload['file'])) {
					$meta[$name] = wc_clean($upload['url']);
				}
			}
		}
	}

	return $meta;
}

function tinvwl_add_to_wishlist_tm_extra_product_options()
{
	wp_add_inline_script('tinvwl', "
					jQuery('body').on('tinvwl_add_to_wishlist_button_click', function(e, el){
							jQuery(el).closest('form.cart').each(function(){
								if (jQuery(this).find('#tm-extra-product-options').length) {

									jQuery(this).find('.tc-hidden[required], input.use_images[required]').attr('disabled', true);

									if (!jQuery(this)[0].checkValidity()){
										jQuery(el).addClass('disabled-add-wishlist');
										jQuery(this)[0].reportValidity();
									} else {
										jQuery(el).removeClass('disabled-add-wishlist');
									}

									jQuery(this).find('.tc-hidden[required], input.use_images[required]').attr('disabled', false);
								}
							});
					});
			");
}

add_action('wp_enqueue_scripts', 'tinvwl_add_to_wishlist_tm_extra_product_options', 100, 1);
