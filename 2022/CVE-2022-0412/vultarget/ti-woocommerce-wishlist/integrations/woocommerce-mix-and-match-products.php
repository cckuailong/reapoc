<?php
/**
 * TI WooCommerce Wishlist integration with:
 *
 * @name WooCommerce Mix and Match
 *
 * @version 1.6.0
 *
 * @slug woocommerce-mix-and-match-products
 *
 * @url https://woocommerce.com/products/woocommerce-mix-and-match-products/
 *
 */

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
	exit;
}

// Load integration depends on current settings.
global $tinvwl_integrations;

$slug = "woocommerce-mix-and-match-products";

$name = "WooCommerce Mix and Match";

$available = class_exists('WC_Mix_and_Match');

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

if (!function_exists('tinv_wishlist_metasupport_woocommerce_mix_and_match_products')) {

	/**
	 * Set description for meta WooCommerce Mix and Match
	 *
	 * @param array $meta Meta array.
	 * @param integer $product_id Product ID.
	 *
	 * @return array
	 */
	function tinv_wishlist_metasupport_woocommerce_mix_and_match_products($meta, $product_id)
	{
		if (array_key_exists('mnm_quantity', $meta)) {
			$product = wc_get_product($product_id);
			if (is_object($product) && $product->is_type('mix-and-match')) {
				$meta = array();
			}
		}

		return $meta;
	}

	add_filter('tinvwl_wishlist_item_meta_post', 'tinv_wishlist_metasupport_woocommerce_mix_and_match_products', 10, 2);
} // End if().

if (!function_exists('tinvwl_row_woocommerce_mix_and_match_products')) {

	/**
	 * Add rows for sub product for WooCommerce Mix and Match
	 *
	 * @param array $wl_product Wishlist Product.
	 * @param \WC_Product $product Woocommerce Product.
	 */
	function tinvwl_row_woocommerce_mix_and_match_products($wl_product, $product)
	{
		if (is_object($product) && $product->is_type('mix-and-match') && array_key_exists('mnm_quantity', $wl_product['meta'])) {
			$product_quantity = $product->is_sold_individually() ? 1 : $wl_product['quantity'];
			$mnm_items = $product->get_children();
			if (!empty($mnm_items)) {
				foreach ($mnm_items as $id => $mnm_item) {
					$item_quantity = 0;
					if (array_key_exists($id, $wl_product['meta']['mnm_quantity'])) {
						$item_quantity = absint($wl_product['meta']['mnm_quantity'][$id]);
					}
					if (0 >= $item_quantity) {
						continue;
					}

					$product_url = $mnm_item->get_permalink();
					$product_image = $mnm_item->get_image();
					$product_title = is_callable(array(
						$mnm_item,
						'get_name'
					)) ? $mnm_item->get_name() : $mnm_item->get_title();
					$product_price = $mnm_item->get_price_html();
					if ($mnm_item->is_visible()) {
						$product_image = sprintf('<a href="%s">%s</a>', esc_url($product_url), $product_image);
						$product_title = sprintf('<a href="%s">%s</a>', esc_url($product_url), $product_title);
					}
					$product_title .= tinv_wishlist_get_item_data($mnm_item, $wl_product);

					$availability = (array)$mnm_item->get_availability();
					if (!array_key_exists('availability', $availability)) {
						$availability['availability'] = '';
					}
					if (!array_key_exists('class', $availability)) {
						$availability['class'] = '';
					}
					$availability_html = empty($availability['availability']) ? '<p class="stock ' . esc_attr($availability['class']) . '"><span><i class="ftinvwl ftinvwl-check"></i></span><span class="tinvwl-txt">' . esc_html__('In stock', 'ti-woocommerce-wishlist') . '</span></p>' : '<p class="stock ' . esc_attr($availability['class']) . '"><span><i class="ftinvwl ftinvwl-times"></i></span><span>' . esc_html($availability['availability']) . '</span></p>';
					$row_string = '<tr>';
					$row_string .= '<td colspan="2">&nbsp;</td><td class="product-thumbnail">%1$s</td><td class="product-name">%2$s</td>';
					if (tinv_get_option('product_table', 'colm_price')) {
						$row_string .= '<td class="product-price">%3$s &times; %5$s</td>';
					}
					if (tinv_get_option('product_table', 'colm_date')) {
						$row_string .= '<td class="product-date">&nbsp;</td>';
					}
					if (tinv_get_option('product_table', 'colm_stock')) {
						$row_string .= '<td class="product-stock">%4$s</td>';
					}
					if (tinv_get_option('product_table', 'add_to_cart')) {
						$row_string .= '<td class="product-action">&nbsp;</td>';
					}
					$row_string .= '</tr>';

					echo sprintf($row_string, $product_image, $product_title, $product_price, $availability_html, $item_quantity * $product_quantity); // WPCS: xss ok.
				} // End foreach().
			} // End if().
		} // End if().
	}

	add_action('tinvwl_wishlist_row_after', 'tinvwl_row_woocommerce_mix_and_match_products', 10, 2);
} // End if().

if (!function_exists('tinvwl_item_price_woocommerce_mix_and_match_products')) {

	/**
	 * Modify price for WooCommerce Mix and Match
	 *
	 * @param string $price Returned price.
	 * @param array $wl_product Wishlist Product.
	 * @param \WC_Product $product Woocommerce Product.
	 *
	 * @return string
	 */
	function tinvwl_item_price_woocommerce_mix_and_match_products($price, $wl_product, $product)
	{
		if (is_object($product) && $product->is_type('mix-and-match') && $product->is_priced_per_product()) {
			$mnm_items = $product->get_children();
			if (!empty($mnm_items)) {
				$_price = 0;
				foreach ($mnm_items as $id => $mnm_item) {
					$item_quantity = 0;
					if (array_key_exists($id, $wl_product['meta']['mnm_quantity'])) {
						$item_quantity = absint($wl_product['meta']['mnm_quantity'][$id]);
					}
					if (0 >= $item_quantity) {
						continue;
					}
					$_price += wc_get_price_to_display($mnm_item, array('qty' => $item_quantity));
				}
				if (0 < $_price) {
					if ($product->is_on_sale()) {
						$price = wc_format_sale_price($_price + wc_get_price_to_display($product, array('price' => $product->get_regular_price())), $_price + wc_get_price_to_display($product)) . $product->get_price_suffix();
					} else {
						$price = wc_price($_price + wc_get_price_to_display($product)) . $product->get_price_suffix();
					}
					$price = apply_filters('woocommerce_get_price_html', $price, $product);
				}
			}
		}

		return $price;
	}

	add_filter('tinvwl_wishlist_item_price', 'tinvwl_item_price_woocommerce_mix_and_match_products', 10, 3);
} // End if().

if (!function_exists('tinvwl_add_form_woocommerce_mix_and_match_products')) {

	/**
	 * Remove empty meta for WooCommerce Mix and Match
	 *
	 * @param array $form Post form data.
	 *
	 * @return array
	 */
	function tinvwl_add_form_woocommerce_mix_and_match_products($form = array())
	{
		if (array_key_exists('mnm_quantity', $form)) {
			if (is_array($form['mnm_quantity']) && !empty($form['mnm_quantity'])) {
				foreach ($form['mnm_quantity'] as $key => $value) {
					$value = absint($value);
					if (empty($value)) {
						unset($form['mnm_quantity'][$key]);
					}
				}
				if (empty($form['mnm_quantity'])) {
					unset($form['mnm_quantity']);
				}
			}
		}

		return $form;
	}

	add_filter('tinvwl_addtowishlist_add_form', 'tinvwl_add_form_woocommerce_mix_and_match_products');
} // End if().
