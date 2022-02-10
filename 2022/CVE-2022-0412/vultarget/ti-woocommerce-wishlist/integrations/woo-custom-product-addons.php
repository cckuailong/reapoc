<?php
/**
 * TI WooCommerce Wishlist integration with:
 *
 * @name WooCommerce Custom Product Addons
 *
 * @version 2.4.8
 *
 * @slug woo-custom-product-addons
 *
 * @url https://wordpress.org/plugins/woo-custom-product-addons/
 *
 */

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
	exit;
}

// Load integration depends on current settings.
global $tinvwl_integrations;

$slug = "woo-custom-product-addons";

$name = "WooCommerce Custom Product Addons";

$available = defined('WCPA_POST_TYPE');

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

if (!function_exists('tinv_wishlist_item_meta_woocommerce_custom_product_addons')) {

	/**
	 * Set description for meta WooCommerce Custom Product Addons
	 *
	 * @param array $meta Meta array.
	 * @param array $wl_product Wishlist Product.
	 * @param \WC_Product $product Woocommerce Product.
	 *
	 * @return array
	 */

	function tinv_wishlist_item_meta_woocommerce_custom_product_addons($item_data, $product_id, $variation_id)
	{
		if (defined('WCPA_POST_TYPE') && class_exists('WCPA_Form') && class_exists('WCPA_Front_End')) {
			$form = new WCPA_Form();
			$frontend = new WCPA_Front_End();
			$data = array();
			$post_ids = $form->get_form_ids($product_id);

			if (isset($item_data['wcpa_field_key_checker'])) {
				unset($item_data['wcpa_field_key_checker']);
			}

			if (wcpa_get_option('form_loading_order_by_date') === true) {
				if (is_array($post_ids) && count($post_ids)) {
					$post_ids = get_posts(array(
						'posts_per_page' => -1,
						'include' => $post_ids,
						'fields' => 'ids',
						'post_type' => WCPA_POST_TYPE,
						'posts_per_page' => -1,
					));
				}
			}
			foreach ($post_ids as $id) {
				if (get_post_status($id) == 'publish') {
					$json_string = get_post_meta($id, WCPA_FORM_META_KEY, true);
					$json_encoded = json_decode($json_string);
					if ($json_encoded && is_array($json_encoded)) {
						$data = array_merge($data, $json_encoded);
					}
				}
			}

			foreach ($data as $v) {
				$form_data = clone $v;
				unset($form_data->values); //avoid saving large number of data
				unset($form_data->className); //avoid saving no use data
				if (!in_array($v->type, array('header', 'paragraph'))) {
					if (isset($item_data[$v->name])) {

						if (!is_object($v)) {
							$value = sanitize_text_field($v);
						} else if ((isset($v->name))) {
							if (is_array($item_data[$v->name])) {

								$_values = $item_data[$v->name];
								array_walk($_values, function (&$a) {
									sanitize_text_field($a);
								}); // using this array_wal method to preserve the keys
								$value = $_values;
							} else if ($v->type == 'textarea') {
								$value = sanitize_textarea_field(wp_unslash($item_data[$v->name]));
							} else {
								$value = sanitize_text_field(wp_unslash($item_data[$v->name]));
							}
						}
						$item_data[$v->name]['key'] = (isset($v->label)) ? $v->label : '';
						$item_data[$v->name]['display'] = $frontend->cart_display(array(
							'type' => $v->type,
							'name' => $v->name,
							'label' => (isset($v->label)) ? $v->label : '',
							'value' => $value['display'],
							'price' => (isset($v->price)) ? $v->price : false,
							'form_data' => $form_data,
						), wc_get_product($product_id));
					}
				}
			}
		}

		return $item_data;
	}

	add_filter('tinvwl_wishlist_item_meta_post', 'tinv_wishlist_item_meta_woocommerce_custom_product_addons', 10, 3);
}

if (!function_exists('tinvwl_item_price_woocommerce_custom_product_addons')) {

	/**
	 * Modify price for  WooCommerce Custom Product Addons.
	 *
	 * @param string $price Returned price.
	 * @param array $wl_product Wishlist Product.
	 * @param \WC_Product $product Woocommerce Product.
	 *
	 * @return string
	 */
	function tinvwl_item_price_woocommerce_custom_product_addons($price, $wl_product, $product)
	{
		if (defined('WCPA_ITEM_ID') && class_exists('WCPA_Form') && class_exists('WCPA_MC')) {

			$price = 0;
			$form = new WCPA_Form();
			$data = array();
			$post_ids = $form->get_form_ids($wl_product['product_id']);

			if (wcpa_get_option('form_loading_order_by_date') === true) {
				if (is_array($post_ids) && count($post_ids)) {
					$post_ids = get_posts(array(
						'posts_per_page' => -1,
						'include' => $post_ids,
						'fields' => 'ids',
						'post_type' => WCPA_POST_TYPE,
						'posts_per_page' => -1,
					));
				}
			}
			foreach ($post_ids as $id) {
				if (get_post_status($id) == 'publish') {
					$json_string = get_post_meta($id, WCPA_FORM_META_KEY, true);
					$json_encoded = json_decode($json_string);
					if ($json_encoded && is_array($json_encoded)) {
						$data = array_merge($data, $json_encoded);
					}
				}
			}

			foreach ($data as $v) {
				$form_data = clone $v;
				unset($form_data->values); //avoid saving large number of data
				unset($form_data->className); //avoid saving no use data
				if (!in_array($v->type, array('header', 'paragraph'))) {
					if (isset($wl_product['meta'][$v->name])) {
						if (!is_object($v)) {
							continue;
						} else if ((isset($v->name))) {
							if ((!isset($v->is_fee) || $v->is_fee === false) && (!isset($v->is_show_price) || $v->is_show_price === false)) {
								if (isset($v->price) && is_array($v->price)) {
									foreach ($v->price as $p) {
										$price += $p;
									}
								} else if (isset($v->price) && $v->price) {
									$price += $v->price;
								}
							}
						}
					}
				}
			}

			$mc = new WCPA_MC();
			$price = $mc->mayBeConvert($price) + $product->get_price('edit');

			$price = wc_price($price);

		}

		return $price;
	}

	add_filter('tinvwl_wishlist_item_price', 'tinvwl_item_price_woocommerce_custom_product_addons', 10, 3);
} // End if().

if (!function_exists('tinvwl_item_price_woocommerce_custom_product_addons_text_button')) {

	/**
	 * Change text for button add to cart
	 *
	 * @param string $text_add_to_cart Text "Add to cart".
	 * @param array $wl_product Wishlist product.
	 * @param object $product WooCommerce Product.
	 *
	 * @return string
	 */
	function tinvwl_item_price_woocommerce_custom_product_addons_text_button($text_add_to_cart, $wl_product, $product)
	{

		if (defined('WCPA_POST_TYPE') && class_exists('WCPA_Form') && class_exists('WCPA_Front_End')) {
			$product_id = $product->get_id();
			$form = new WCPA_Form();
			$post_ids = $form->get_form_ids($product_id);
			$data = array();
			if (wcpa_get_option('form_loading_order_by_date') === true) {
				if (is_array($post_ids) && count($post_ids)) {
					$post_ids = get_posts(array(
						'posts_per_page' => -1,
						'include' => $post_ids,
						'fields' => 'ids',
						'post_type' => WCPA_POST_TYPE,
						'posts_per_page' => -1
					));
				}
			}
			foreach ($post_ids as $id) {
				if (get_post_status($id) == 'publish') {
					$json_string = get_post_meta($id, WCPA_FORM_META_KEY, true);
					$json_encoded = json_decode($json_string);
					if ($json_encoded && is_array($json_encoded)) {
						$data = array_merge($data, $json_encoded);
					}
				}
			}

			$status = true;

			foreach ($data as $v) {
				if ($v->type != 'file' && isset($v->required) && $v->required && (!isset($wl_product['meta'][$v->name]) || empty($wl_product['meta'][$v->name]))) {
					$status = false;
				}
			}

			if (!$status) {

				$WCPA = new WCPA_Front_End();

				return $WCPA->add_to_cart_text($text_add_to_cart, $product);
			}
		}

		return $text_add_to_cart;

	}

	add_filter('tinvwl_wishlist_item_add_to_cart', 'tinvwl_item_price_woocommerce_custom_product_addons_text_button', 10, 3);
}
