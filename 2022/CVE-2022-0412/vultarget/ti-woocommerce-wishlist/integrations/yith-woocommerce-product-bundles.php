<?php
/**
 * TI WooCommerce Wishlist integration with:
 *
 * @name YITH WooCommerce Product Bundles
 *
 * @version 1.1.15
 *
 * @slug yith-woocommerce-product-bundles
 *
 * @url https://wordpress.org/plugins/yith-woocommerce-product-bundles/
 *
 */

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
	exit;
}

// Load integration depends on current settings.
global $tinvwl_integrations;

$slug = "yith-woocommerce-product-bundles";

$name = "YITH WooCommerce Product Bundles";

$available = defined('YITH_WCPB_VERSION');

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

if (!function_exists('tinv_wishlist_metasupport_yith_woocommerce_product_bundles')) {

	/**
	 * Set description for meta WooCommerce Mix and Match
	 *
	 * @param array $meta Meta array.
	 * @param integer $product_id Product ID.
	 *
	 * @return array
	 */
	function tinv_wishlist_metasupport_yith_woocommerce_product_bundles($meta, $product_id)
	{
		if (array_key_exists('yith_bundle_quantity_1', $meta)) {
			$product = wc_get_product($product_id);
			if (is_object($product) && $product->is_type('yith_bundle')) {
				$meta = array();
			}
		}

		return $meta;
	}

	add_filter('tinvwl_wishlist_item_meta_post', 'tinv_wishlist_metasupport_yith_woocommerce_product_bundles', 10, 2);
} // End if().

if (!function_exists('tinvwl_item_status_yith_woocommerce_product_bundles')) {

	/**
	 * Modify status for YITH WooCommerce Product Bundles
	 *
	 * @param string $availability_html Returned availability status.
	 * @param string $availability Availability status.
	 * @param array $wl_product Wishlist Product.
	 * @param \WC_Product $product Woocommerce Product.
	 *
	 * @return string
	 */
	function tinvwl_item_status_yith_woocommerce_product_bundles($availability_html, $availability, $wl_product, $product)
	{
		if (empty($availability) && is_object($product) && $product->is_type('yith_bundle')) {
			$response = true;
			$bundled_items = $product->get_bundled_items();
			foreach ($bundled_items as $key => $bundled_item) {
				if (method_exists($bundled_item, 'is_optional')) {
					if ($bundled_item->is_optional() && !array_key_exists('yith_bundle_optional_' . $key, $wl_product['meta'])) {
						continue;
					}
				}
				if (!$bundled_item->get_product()->is_in_stock()) {
					$response = false;
				}
			}

			if (!$response) {
				$availability = array(
					'class' => 'out-of-stock',
					'availability' => __('Out of stock', 'ti-woocommerce-wishlist'),
				);
				$availability_html = '<p class="stock ' . esc_attr($availability['class']) . '"><span><i class="ftinvwl ftinvwl-times"></i></span><span>' . esc_html($availability['availability']) . '</span></p>';
			}
		}

		return $availability_html;
	}

	add_filter('tinvwl_wishlist_item_status', 'tinvwl_item_status_yith_woocommerce_product_bundles', 10, 4);
} // End if().

if (!function_exists('tinvwl_row_yith_woocommerce_product_bundles')) {

	/**
	 * Add rows for sub product for YITH WooCommerce Product Bundles
	 *
	 * @param array $wl_product Wishlist Product.
	 * @param \WC_Product $product Woocommerce Product.
	 */
	function tinvwl_row_yith_woocommerce_product_bundles($wl_product, $product)
	{
		if (is_object($product) && $product->is_type('yith_bundle')) {
			$bundled_items = $product->get_bundled_items();
			$product_quantity = $product->is_sold_individually() ? 1 : $wl_product['quantity'];
			if (!empty($bundled_items)) {
				foreach ($bundled_items as $key => $bundled_item) {
					$item_quantity = $bundled_item->get_quantity();
					if (array_key_exists('yith_bundle_quantity_' . $key, $wl_product['meta'])) {
						$item_quantity = absint($wl_product['meta']['yith_bundle_quantity_' . $key]);
					}
					if (method_exists($bundled_item, 'is_optional')) {
						if ($bundled_item->is_optional() && !array_key_exists('yith_bundle_optional_' . $key, $wl_product['meta'])) {
							$item_quantity = 0;
						}
					}
					if (0 >= $item_quantity) {
						continue;
					}

					$product = $bundled_item->get_product();
					if (!is_object($product)) {
						continue;
					}

					$product_url = $product->get_permalink();
					$product_image = $product->get_image();
					$product_title = is_callable(array(
						$product,
						'get_name'
					)) ? $product->get_name() : $product->get_title();
					$product_price = $product->get_price_html();
					if ($product->is_visible()) {
						$product_image = sprintf('<a href="%s">%s</a>', esc_url($product_url), $product_image);
						$product_title = sprintf('<a href="%s">%s</a>', esc_url($product_url), $product_title);
					}
					$product_title .= tinv_wishlist_get_item_data($product, $wl_product);

					$availability = (array)$product->get_availability();
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

	add_action('tinvwl_wishlist_row_after', 'tinvwl_row_yith_woocommerce_product_bundles', 10, 2);
} // End if().
