<?php
/**
 * TI WooCommerce Wishlist integration with:
 *
 * @name PPOM for WooCommerce
 *
 * @version 21.2
 *
 * @slug woocommerce-product-addon
 *
 * @url https://wordpress.org/plugins/woocommerce-product-addon/
 *
 */

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
	exit;
}

// Load integration depends on current settings.
global $tinvwl_integrations;

$slug = "woocommerce-product-addon";

$name = "PPOM for WooCommerce";

$available = defined('PPOM_VERSION');

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

if (!function_exists('tinv_wishlist_metasupport_woocommerce_product_add_on')) {

	/**
	 * Set description for meta WooCommerce Product Add-on
	 *
	 * @param array $meta Meta array.
	 * @param integer $product_id Product ID.
	 *
	 * @return array
	 */
	function tinv_wishlist_metasupport_woocommerce_product_add_on($meta, $product_id)
	{
		if (isset($meta['ppom'])) {
			$meta = array();
		}

		return $meta;
	}

	add_filter('tinvwl_wishlist_item_meta_post', 'tinv_wishlist_metasupport_woocommerce_product_add_on', 10, 2);
} // End if().

if (!function_exists('tinv_wishlist_item_meta_woocommerce_product_add_on')) {

	/**
	 * Set description for meta WooCommerce Product Add-on
	 *
	 * @param array $meta Meta array.
	 * @param array $wl_product Wishlist Product.
	 * @param \WC_Product $product Woocommerce Product.
	 *
	 * @return array
	 */
	function tinv_wishlist_item_meta_woocommerce_product_add_on($meta, $wl_product, $product)
	{
		if (isset($wl_product['meta']) && isset($wl_product['meta']['ppom']) && class_exists('NM_PersonalizedProduct')) {

			$ppom = json_decode($wl_product['meta']['ppom']);
			$product_meta = $ppom->fields ? $ppom->fields : '';

			$item_meta = array();

			if ($product_meta) {

				foreach ($product_meta as $key => $value) {

					if (empty($value)) {
						continue;
					}

					$product_id = $wl_product['product_id'];
					$field_meta = ppom_get_field_meta_by_dataname($product_id, $key);

					if (empty($field_meta)) {
						continue;
					}

					$field_type = $field_meta['type'];
					$field_title = $field_meta['title'];


					switch ($field_type) {
						case 'quantities':
							$total_qty = 0;
							foreach ($value as $label => $qty) {
								if (!empty($qty)) {
									$item_meta[] = array(
										'key' => $label,
										'display' => $qty,
									);
									$total_qty += $qty;
								}
							}
							break;

						case 'file':
							$file_thumbs_html = '';
							foreach ($value as $file_id => $file_uploaded) {
								$file_name = $file_uploaded['org'];
								$file_thumbs_html .= ppom_show_file_thumb($file_name);
							}
							$item_meta[] = array(
								'key' => $field_title,
								'display' => $file_thumbs_html,
							);

							break;

						case 'cropper':
							$file_thumbs_html = '';
							foreach ($value as $file_id => $file_cropped) {

								$file_name = $file_cropped['org'];
								$file_thumbs_html .= ppom_show_file_thumb($file_name, true);
							}
							$item_meta[] = array(
								'key' => $field_title,
								'display' => $file_thumbs_html,
							);
							break;

						case 'image':
							if ($value) {
								foreach ($value as $id => $images_meta) {
									$images_meta = json_decode(stripslashes($images_meta), true);
									$image_url = stripslashes($images_meta['link']);
									$image_html = '<img class="img-thumbnail" style="width:' . esc_attr(ppom_get_thumbs_size()) . '" src="' . esc_url($image_url) . '" title="' . esc_attr($images_meta['title']) . '">';
									$meta_key = $field_title . '(' . $images_meta['title'] . ')';
									$item_meta[] = array(
										'key' => $meta_key,
										'display' => $image_html,
									);
								}
							}
							break;

						case 'audio':
							if ($value) {
								$ppom_file_count = 1;
								foreach ($value as $id => $audio_meta) {
									$audio_meta = json_decode(stripslashes($audio_meta), true);
									$audio_url = stripslashes($audio_meta['link']);
									$audio_html = '<a href="' . esc_url($audio_url) . '" title="' . esc_attr($audio_meta['title']) . '">' . $audio_meta['title'] . '</a>';
									$meta_key = $field_title . ': ' . $ppom_file_count++;
									$item_meta[] = array(
										'key' => $meta_key,
										'display' => $audio_html,
									);
								}
							}
							break;

						case 'bulkquantity':
							$item_meta[] = array(
								'key' => $key,
								'display' => $value['option'] . ' (' . $value['qty'] . ')',
							);
							break;

						default:
							$value = is_object($value) ? implode(",", (array)$value) : $value;
							$item_meta[] = array(
								'key' => $field_title,
								'display' => stripcslashes($value),
							);
							break;
					}

				} // End foreach().
			} // End if().

			if (0 < count($item_meta)) {
				ob_start();
				tinv_wishlist_template('ti-wishlist-item-data.php', array('item_data' => $item_meta));
				$meta .= ob_get_clean();
			}
		} // End if().

		return $meta;
	}

	add_filter('tinvwl_wishlist_item_meta_data', 'tinv_wishlist_item_meta_woocommerce_product_add_on', 10, 3);
} // End if().
