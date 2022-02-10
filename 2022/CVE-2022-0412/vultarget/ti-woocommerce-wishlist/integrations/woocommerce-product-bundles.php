<?php
/**
 * TI WooCommerce Wishlist integration with:
 *
 * @name WooCommerce Product Bundles
 *
 * @version 5.12.0
 *
 * @slug woocommerce-product-bundles
 *
 * @url https://woocommerce.com/products/product-bundles/
 *
 */

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
	exit;
}

// Load integration depends on current settings.
global $tinvwl_integrations;

$slug = "woocommerce-product-bundles";

$name = "WooCommerce Product Bundles";

$available = class_exists('WC_Bundles');

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

if (!function_exists('tinv_wishlist_metasupport_woocommerce_product_bundles')) {

	/**
	 * Set description for meta WooCommerce Product Bundles
	 *
	 * @param array $meta Meta array.
	 * @param integer $product_id Product ID.
	 *
	 * @return array
	 */
	function tinv_wishlist_metasupport_woocommerce_product_bundles($meta, $product_id)
	{
		$product = wc_get_product($product_id);

		if (is_object($product) && $product->is_type('bundle') && empty($meta['bkap_price_charged'])) {
			$meta = array();
		}

		return $meta;
	}

	add_filter('tinvwl_wishlist_item_meta_post', 'tinv_wishlist_metasupport_woocommerce_product_bundles', 10, 2);
} // End if().

if (!function_exists('tinvwl_row_woocommerce_product_bundles')) {

	/**
	 * Add rows for sub product for WooCommerce Product Bundles
	 *
	 * @param array $wl_product Wishlist Product.
	 * @param \WC_Product $product Woocommerce Product.
	 * @param int $discount_extra possible discount on bundle row.
	 */
	function tinvwl_row_woocommerce_product_bundles($wl_product, $product, $discount_extra = 0)
	{
		if (is_object($product) && $product->is_type('bundle')) {

			$product_id = WC_PB_Core_Compatibility::get_id($product);
			$bundled_items = $product->get_bundled_items();
			if (!empty($bundled_items)) {
				foreach ($bundled_items as $bundled_item_id => $bundled_item) {

					$bundled_item_variation_id_request_key = apply_filters('woocommerce_product_bundle_field_prefix', '', $product_id) . 'bundle_variation_id_' . $bundled_item_id;
					$bundled_variation_id = absint(isset($wl_product['meta'][$bundled_item_variation_id_request_key]) ? $wl_product['meta'][$bundled_item_variation_id_request_key] : 0);
					if (!empty($bundled_variation_id)) {
						$bundled_item->product = wc_get_product($bundled_variation_id);
					}

					$is_optional = $bundled_item->is_optional();

					$bundled_item_quantity_request_key = apply_filters('woocommerce_product_bundle_field_prefix', '', $product_id) . 'bundle_quantity_' . $bundled_item_id;
					$bundled_product_qty = isset($wl_product['meta'][$bundled_item_quantity_request_key]) ? absint($wl_product['meta'][$bundled_item_quantity_request_key]) : $bundled_item->get_quantity();

					if ($is_optional) {

						/** Documented in method 'get_posted_bundle_configuration'. */
						$bundled_item_selected_request_key = apply_filters('woocommerce_product_bundle_field_prefix', '', $product_id) . 'bundle_selected_optional_' . $bundled_item_id;

						if (!array_key_exists($bundled_item_selected_request_key, $wl_product['meta'])) {
							$bundled_product_qty = 0;
						}
					}
					if (0 === $bundled_product_qty || 'visible' != $bundled_item->cart_visibility) {
						continue;
					}

					$product_url = $bundled_item->product->get_permalink();
					$product_image = $bundled_item->product->get_image();
					$product_title = $bundled_item->has_title_override() ? is_callable(array(
						$bundled_item,
						'get_name'
					)) ? $bundled_item->get_name() : $bundled_item->get_title() : $bundled_item->get_raw_title();

					$product_price = $bundled_item->product->get_price_html();
					$product_price_raw = $bundled_item->product->get_regular_price();
					$discount = $bundled_item->get_discount();
					$discount = empty($discount) ? $discount_extra : (100 - $discount) / $discount_extra + $discount;
					$product_price = empty($discount) ? $product_price : wc_price(WC_PB_Product_Prices::get_discounted_price($product_price_raw, $discount));

					if ($bundled_item->product->is_visible()) {
						$product_image = sprintf('<a href="%s">%s</a>', esc_url($product_url), $product_image);
						$product_title = sprintf('<a href="%s">%s</a>', esc_url($product_url), $product_title);
					}
					$product_title .= tinv_wishlist_get_item_data($bundled_item->product, $wl_product);

					$availability = (array)$bundled_item->product->get_availability();
					if (!array_key_exists('availability', $availability)) {
						$availability['availability'] = '';
					}
					if (!array_key_exists('class', $availability)) {
						$availability['class'] = '';
					}
					$availability_html = empty($availability['availability']) ? '<p class="stock ' . esc_attr($availability['class']) . '"><span><i class="ftinvwl ftinvwl-check"></i></span><span class="tinvwl-txt">' . esc_html__('In stock', 'ti-woocommerce-wishlist') . '</span></p>' : '<p class="stock ' . esc_attr($availability['class']) . '"><span><i class="ftinvwl ftinvwl-times"></i></span><span>' . esc_html($availability['availability']) . '</span></p>';
					$row_string = '<tr>';
					$row_string .= '<td colspan="2">&nbsp;</td><td class="product-thumbnail">%1$s</td><td class="product-name">%2$s</td>';
					if (tinv_get_option('product_table', 'colm_price') && $bundled_item->is_priced_individually()) {
						$row_string .= '<td class="product-price">%3$s &times; %5$s</td>';
					} elseif (!$bundled_item->is_priced_individually()) {
						$row_string .= '<td class="product-price"></td>';
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

					echo sprintf($row_string, $product_image, $product_title, $product_price, $availability_html, $bundled_product_qty); // WPCS: xss ok.
				} // End foreach().
			} // End if().
		} // End if().
	}

	add_action('tinvwl_wishlist_row_after', 'tinvwl_row_woocommerce_product_bundles', 10, 2);
} // End if().

if (!function_exists('tinvwl_item_price_woocommerce_product_bundles')) {

	/**
	 * Modify price for WooCommerce Product Bundles
	 *
	 * @param string $price Returned price.
	 * @param array $wl_product Wishlist Product.
	 * @param \WC_Product $product Woocommerce Product.
	 *
	 * @return string
	 */
	function tinvwl_item_price_woocommerce_product_bundles($price, $wl_product, $product, $raw = false)
	{
		if (is_object($product) && $product->is_type('bundle')) {

			$bundle_price = $product->get_price();
			$product_id = WC_PB_Core_Compatibility::get_id($product);
			$bundled_items = $product->get_bundled_items();

			if (!empty($bundled_items)) {

				$bundled_items_price = 0.0;

				foreach ($bundled_items as $bundled_item_id => $bundled_item) {

					$bundled_item_variation_id_request_key = apply_filters('woocommerce_product_bundle_field_prefix', '', $product_id) . 'bundle_variation_id_' . $bundled_item_id;
					$bundled_variation_id = absint(isset($wl_product['meta'][$bundled_item_variation_id_request_key]) ? $wl_product['meta'][$bundled_item_variation_id_request_key] : 0);
					if (!empty($bundled_variation_id)) {
						$_bundled_product = wc_get_product($bundled_variation_id);
					} else {
						$_bundled_product = $bundled_item->product;
					}

					$is_optional = $bundled_item->is_optional();

					$bundled_item_quantity_request_key = apply_filters('woocommerce_product_bundle_field_prefix', '', $product_id) . 'bundle_quantity_' . $bundled_item_id;
					$bundled_product_qty = isset($wl_product['meta'][$bundled_item_quantity_request_key]) ? absint($wl_product['meta'][$bundled_item_quantity_request_key]) : $bundled_item->get_quantity();

					if ($is_optional) {

						/** Documented in method 'get_posted_bundle_configuration'. */
						$bundled_item_selected_request_key = apply_filters('woocommerce_product_bundle_field_prefix', '', $product_id) . 'bundle_selected_optional_' . $bundled_item_id;

						if (!array_key_exists($bundled_item_selected_request_key, $wl_product['meta'])) {
							$bundled_product_qty = 0;
						}
					}

					if ($bundled_item->is_priced_individually()) {
						$product_price = $_bundled_product->get_regular_price();

						$discount = $bundled_item->get_discount();
						$product_price = empty($discount) ? $product_price : WC_PB_Product_Prices::get_discounted_price($product_price, $discount);

						$bundled_item_price = (double)$product_price * (int)$bundled_product_qty;

						$bundled_items_price += (double)$bundled_item_price;
					}

				} // End foreach().
				$price = (double)$bundle_price + $bundled_items_price;
				if (!$raw) {
					$price = apply_filters('woocommerce_get_price_html', wc_price($price), $product);
				}
			} // End if().
		} // End if().

		return $price;
	}

	add_filter('tinvwl_wishlist_item_price', 'tinvwl_item_price_woocommerce_product_bundles', 10, 3);
} // End if().
