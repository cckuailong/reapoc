<?php
/**
 * TI WooCommerce Wishlist integration with:
 *
 * @name WPC Product Bundles for WooCommerce
 *
 * @version 5.3.1
 *
 * @slug woo-product-bundle
 *
 * @url https://wordpress.org/plugins/woo-product-bundle/
 *
 */

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
	exit;
}

// Load integration depends on current settings.
global $tinvwl_integrations;

$slug = "woo-product-bundle";

$name = "WPC Product Bundles for WooCommerce";

$available = defined('WOOSB_VERSION');

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

if (defined('WOOSB_VERSION')) {

	add_action('tinvwl_wishlist_addtowishlist_button', 'tinvwl_woo_product_bundle_loop_data', 10, 2);

	function tinvwl_woo_product_bundle_loop_data($product, $loop)
	{

		if ($loop && 'woosb' === $product->get_type()) {
			$ids_str = '';

			if (get_post_meta($product->get_id(), 'woosb_ids', true)) {
				$ids_str = get_post_meta($product->get_id(), 'woosb_ids', true);
			}

			$ids_str = WPCleverWoosb_Helper::woosb_clean_ids($ids_str);
			?>
			<input name="woosb_ids" class="woosb_ids woosb-ids" type="hidden"
				   value="<?php echo esc_attr($ids_str); ?>"/>
			<?php
		}
	}

	/**
	 * Add rows for sub product for WPC Product Bundles for WooCommerce
	 *
	 * @param array $wl_product Wishlist Product.
	 * @param \WC_Product $product Woocommerce Product.
	 * @param int $discount_extra possible discount on bundle row.
	 */
	function tinvwl_row_woo_product_bundle($wl_product, $product, $discount_extra = 0)
	{
		if (is_object($product) && $product->is_type('woosb') && isset($wl_product['meta']) && isset($wl_product['meta']['woosb_ids'])) {

			$bundle = WPCleverWoosb::instance();

			$bundled_items = $bundle->woosb_get_bundled(0, $wl_product['meta']['woosb_ids']);
			if (!empty($bundled_items)) {
				foreach ($bundled_items as $key => $data) {

					$bundled_item = new stdClass();

					$bundled_item->product = wc_get_product($data['id']);

					if (!$bundled_item->product) {
						continue;
					}

					$bundled_product_qty = $data['qty'];
					$product_url = (get_option('_woosb_bundled_link', 'yes') !== 'no') ? $product->get_permalink() : $bundled_item->product->get_permalink();
					$product_image = $bundled_item->product->get_image();

					$product_title = is_callable(array(
							$bundled_item->product,
							'get_name'
					)) ? $bundled_item->product->get_name() : $bundled_item->product->get_title();

					$product_price = $bundled_item->product->get_price_html();

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
					$row_string .= '<td colspan="2">&nbsp;</td><td class="product-thumbnail">%1$s</td><td class="product-name">&#10149; %2$s</td>';
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

					echo sprintf($row_string, $product_image, $product_title, $product_price, $availability_html, $bundled_product_qty); // WPCS: xss ok.
				} // End foreach().
			} // End if().
		} // End if().
	}

	add_action('tinvwl_wishlist_row_after', 'tinvwl_row_woo_product_bundle', 10, 2);

	/**
	 * Set description for meta WPC Product Bundles for WooCommerce
	 *
	 * @param array $meta Meta array.
	 * @param array $wl_product Wishlist Product.
	 * @param \WC_Product $product Woocommerce Product.
	 *
	 * @return array
	 */

	function tinv_wishlist_item_meta_woo_product_bundle($item_data, $product_id, $variation_id)
	{

		foreach (array_keys($item_data) as $key) {
			if (strpos($key, 'woosb_') === 0) {
				unset($item_data[$key]);
			}
		}


		return $item_data;
	}

	add_filter('tinvwl_wishlist_item_meta_post', 'tinv_wishlist_item_meta_woo_product_bundle', 10, 3);
}
