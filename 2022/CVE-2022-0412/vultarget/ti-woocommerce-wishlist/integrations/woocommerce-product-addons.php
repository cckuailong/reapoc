<?php
/**
 * TI WooCommerce Wishlist integration with:
 *
 * @name WooCommerce Product Add-ons
 *
 * @version 3.3.1
 *
 * @slug woocommerce-product-addons
 *
 * @url https://woocommerce.com/products/product-add-ons/
 *
 */

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
	exit;
}

// Load integration depends on current settings.
global $tinvwl_integrations;

$slug = "woocommerce-product-addons";

$name = "WooCommerce Product Add-ons";

$available = class_exists('WC_Product_Addons');

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

if (!function_exists('tinv_wishlist_item_meta_woocommerce_product_addons')) {

	/**
	 * Set description for meta  WooCommerce Product Addons
	 *
	 * @param array $item_data Meta array.
	 * @param int $product_id Wishlist Product.
	 * @param int $variation_id Woocommerce Product.
	 *
	 * @return array
	 */
	function tinv_wishlist_item_meta_woocommerce_product_addons($item_data, $product_id, $variation_id)
	{

		if (class_exists('WC_Product_Addons')) {

			$id = ($variation_id) ? $variation_id : $product_id;

			if (function_exists('get_product_addons')) {
				$product_addons = get_product_addons($id);
			} else {
				$product_addons = WC_Product_Addons_Helper::get_product_addons($id);
			}

			if ($product_addons) {

				$field = null;

				foreach ($product_addons as $addon) {
					foreach ($addon['options'] as $option) {
						$original_data = 'addon-' . $addon['field_name'];

						$value = isset($item_data[$original_data]) ? $item_data[$original_data]['display'] : '';

						if ($value == '') {
							continue;
						}

						if (is_array($value)) {
							$value = array_map('stripslashes', $value);
						} else {
							$value = stripslashes($value);
						}
						include_once(WP_PLUGIN_DIR . '/woocommerce-product-addons/includes/fields/abstract-wc-product-addons-field.php');
						switch ($addon['type']) {
							case 'checkbox':
								$value = json_decode($value, true);
								include_once(WP_PLUGIN_DIR . '/woocommerce-product-addons/includes/fields/class-wc-product-addons-field-list.php');
								$field = new WC_Product_Addons_Field_List($addon, $value);
								break;
							case 'multiple_choice':
								switch ($addon['display']) {
									case 'radiobutton':
										$value = json_decode($value, true);
										include_once(WP_PLUGIN_DIR . '/woocommerce-product-addons/includes/fields/class-wc-product-addons-field-list.php');
										$field = new WC_Product_Addons_Field_List($addon, $value);
										break;
									case 'images':
									case 'select':
										include_once(WP_PLUGIN_DIR . '/woocommerce-product-addons/includes/fields/class-wc-product-addons-field-select.php');
										$field = new WC_Product_Addons_Field_Select($addon, $value);
										break;
								}
								break;
							case 'custom_text':
							case 'custom_textarea':
							case 'custom_price':
							case 'input_multiplier':
								include_once(WP_PLUGIN_DIR . '/woocommerce-product-addons/includes/fields/class-wc-product-addons-field-custom.php');
								$field = new WC_Product_Addons_Field_Custom($addon, $value);
								break;
							case 'file_upload':
								include_once(WP_PLUGIN_DIR . '/woocommerce-product-addons/includes/fields/class-wc-product-addons-field-file-upload.php');
								$field = new WC_Product_Addons_Field_File_Upload($addon, $value);
								if ($field && isset($field->value)) {
									$field->value = basename($field->value);
								}
								break;
							default:
								// Continue to the next field in case the type is not recognized (instead of causing a fatal error)
								break;
						}

						if ($field) {
							$data = $field->get_cart_item_data();
							unset($item_data[$original_data]);
							foreach ($data as $opt) {
								$name = $opt['name'];

								if ($opt['price'] && apply_filters('woocommerce_addons_add_price_to_name', '__return_true')) {
									$name .= ' (' . wc_price(WC_Product_Addons_Helper::get_product_addon_price_for_display($opt['price'])) . ')';
								}

								$item_data[] = array(
									'key' => $name,
									'display' => $opt['value'],
								);
							}
						}
					}
				}
			}
		}

		return $item_data;
	}

	add_filter('tinvwl_wishlist_item_meta_post', 'tinv_wishlist_item_meta_woocommerce_product_addons', 10, 3);
} // End if().

if (!function_exists('tinvwl_item_price_woocommerce_product_addons')) {

	/**
	 * Modify price for  WooCommerce Product Addons.
	 *
	 * @param string $price Returned price.
	 * @param array $wl_product Wishlist Product.
	 * @param WC_Product $product Woocommerce Product.
	 *
	 * @return string
	 */
	function tinvwl_item_price_woocommerce_product_addons($price, $wl_product, $product)
	{

		if (class_exists('WC_Product_Addons')) {

			if (function_exists('get_product_addons')) {
				$product_addons = get_product_addons($product->get_id());
			} else {
				$product_addons = WC_Product_Addons_Helper::get_product_addons($product->get_id());
			}

			if ($product_addons) {

				$price = 0;
				$field = null;

				foreach ($product_addons as $addon) {

					$original_data = 'addon-' . $addon['field_name'];

					$value = isset($wl_product['meta'][$original_data]) ? $wl_product['meta'][$original_data] : '';
					if ($value == '') {
						continue;
					}

					if (is_array($value)) {
						$value = array_map('stripslashes', $value);
					} else {
						$value = stripslashes($value);
					}
					include_once(WP_PLUGIN_DIR . '/woocommerce-product-addons/includes/fields/abstract-wc-product-addons-field.php');
					switch ($addon['type']) {
						case 'checkbox':
							$value = json_decode($value, true);
							include_once(WP_PLUGIN_DIR . '/woocommerce-product-addons/includes/fields/class-wc-product-addons-field-list.php');
							$field = new WC_Product_Addons_Field_List($addon, $value);
							break;
						case 'multiple_choice':
							switch ($addon['display']) {
								case 'radiobutton':
									$value = json_decode($value, true);
									include_once(WP_PLUGIN_DIR . '/woocommerce-product-addons/includes/fields/class-wc-product-addons-field-list.php');
									$field = new WC_Product_Addons_Field_List($addon, $value);
									break;
								case 'images':
								case 'select':
									include_once(WP_PLUGIN_DIR . '/woocommerce-product-addons/includes/fields/class-wc-product-addons-field-select.php');
									$field = new WC_Product_Addons_Field_Select($addon, $value);
									break;
							}
							break;
						case 'custom_text':
						case 'custom_textarea':
						case 'custom_price':
						case 'input_multiplier':
							include_once(WP_PLUGIN_DIR . '/woocommerce-product-addons/includes/fields/class-wc-product-addons-field-custom.php');
							$field = new WC_Product_Addons_Field_Custom($addon, $value);
							break;
						case 'file_upload':
							include_once(WP_PLUGIN_DIR . '/woocommerce-product-addons/includes/fields/class-wc-product-addons-field-file-upload.php');
							$field = new WC_Product_Addons_Field_File_Upload($addon, $value);
							break;
						default:
							// Continue to the next field in case the type is not recognized (instead of causing a fatal error)
							break;
					}

					if ($field) {
						$data = $field->get_cart_item_data();
						foreach ($data as $option) {
							if ($option['price']) {
								$price += (float)$option['price'];
							}
						}
					}

				}

				$price = wc_price((float)$product->get_price() + (float)$price);
			}
		}

		return $price;
	}

	add_filter('tinvwl_wishlist_item_price', 'tinvwl_item_price_woocommerce_product_addons', 10, 3);
} // End if().

add_filter('tinvwl_addtowishlist_prepare_form', 'tinvwl_meta_woocommerce_product_addons', 10, 3);

function tinvwl_meta_woocommerce_product_addons($meta, $post, $files)
{

	if (class_exists('WC_Product_Addons') && !empty($files)) {
		include_once(WP_PLUGIN_DIR . '/woocommerce-product-addons/includes/fields/abstract-wc-product-addons-field.php');
		include_once(WP_PLUGIN_DIR . '/woocommerce-product-addons/includes/fields/class-wc-product-addons-field-file-upload.php');
		$field = new WC_Product_Addons_Field_File_Upload(array());
		foreach ($files as $name => $file) {

			if (array_key_exists($name, $meta)) {
				$upload = $field->handle_upload($file);
				if (empty($upload['error']) && !empty($upload['file'])) {
					$meta[$name] = wc_clean($upload['url']);
				}
			}
		}
	}

	return $meta;
}

add_filter('tinvwl_product_prepare_meta', 'tinvwl_cart_meta_woocommerce_product_addons');

function tinvwl_cart_meta_woocommerce_product_addons($meta)
{

	if (class_exists('WC_Product_Addons') && !empty($files)) {
		include_once(WP_PLUGIN_DIR . '/woocommerce-product-addons/includes/fields/abstract-wc-product-addons-field.php');
		include_once(WP_PLUGIN_DIR . '/woocommerce-product-addons/includes/fields/class-wc-product-addons-field-file-upload.php');
		$field = new WC_Product_Addons_Field_File_Upload(array());

		$files = $_FILES;

		foreach ($files as $name => $file) {

			if (!array_key_exists($name, $meta)) {
				$upload = $field->handle_upload($file);
				if (empty($upload['error']) && !empty($upload['file'])) {
					$meta[$name] = wc_clean($upload['url']);
				}
			}
		}
	}

	return $meta;
}


add_filter('tinvwl_addproduct_tocart', 'tinvwl_add_to_cart_meta_woocommerce_product_addons');

function tinvwl_add_to_cart_meta_woocommerce_product_addons($wl_product)
{
	if (class_exists('WC_Product_Addons')) {

		$id = ($wl_product['variation_id']) ? $wl_product['variation_id'] : $wl_product['product_id'];

		if (function_exists('get_product_addons')) {
			$product_addons = get_product_addons($id);
		} else {
			$product_addons = WC_Product_Addons_Helper::get_product_addons($id);
		}

		if ($product_addons) {

			$field = null;

			foreach ($product_addons as $addon) {
				foreach ($addon['options'] as $option) {
					$original_data = 'addon-' . $addon['field_name'];

					$value = isset($wl_product['meta'][$original_data]) ? $wl_product['meta'][$original_data] : '';

					if ($value == '') {
						continue;
					}

					if (is_array($value)) {
						$value = array_map('stripslashes', $value);
					} else {
						$value = stripslashes($value);
					}
					include_once(WP_PLUGIN_DIR . '/woocommerce-product-addons/includes/fields/abstract-wc-product-addons-field.php');
					switch ($addon['type']) {
						case 'checkbox':
							if (!is_array($value)) {
								$wl_product['meta'][$original_data] = json_decode($value, true);
							}
							break;
						case 'multiple_choice':
							switch ($addon['display']) {
								case 'radiobutton':
									if (!is_array($value)) {
										$wl_product['meta'][$original_data] = json_decode($value, true);
									}
									break;
							}
							break;
						default:

							break;
					}
				}
			}
		}
	}

	return $wl_product;
}
