<?php
/**
 * TI WooCommerce Wishlist integration with:
 *
 * @name WooCommerce Multilingual
 *
 * @version 4.10.2
 *
 * @slug woocommerce-multilingual
 *
 * @url https://wordpress.org/plugins/woocommerce-multilingual/
 *
 */

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
	exit;
}

// Load integration depends on current settings.
global $tinvwl_integrations;

$slug = "woocommerce-multilingual";

$name = "WooCommerce Multilingual";

$available = class_exists('woocommerce_wpml');

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

if (!function_exists('tinvwl_wpml_addtowishlist_prepare')) {

	/**
	 * Change product data if product need translate in WooCommerce Multilingual
	 *
	 * @param array $post_data Data for wishlist.
	 *
	 * @return array
	 */
	function tinvwl_wpml_addtowishlist_prepare($post_data)
	{
		if (class_exists('woocommerce_wpml')) {

			global $woocommerce_wpml, $sitepress, $wpdb;

			// Reload products class.
			if (version_compare(WCML_VERSION, '4.4.0', '<')) {
				$woocommerce_wpml->products = new WCML_Products($woocommerce_wpml, $sitepress, $wpdb);
			} else {
				global $wpml_post_translations;
				if (!($woocommerce_wpml instanceof woocommerce_wpml) || !($sitepress instanceof SitePress) || !($wpml_post_translations instanceof WPML_Post_Translation)) {
					return $post_data;
				}
				$woocommerce_wpml->products = new WCML_Products($woocommerce_wpml, $sitepress, $wpml_post_translations, $wpdb);
			}

			if (array_key_exists('product_id', $post_data) && !empty($post_data['product_id'])) {
				$post_data['product_id'] = $woocommerce_wpml->products->get_original_product_id($post_data['product_id']);
			}
			if (array_key_exists('product_id', $post_data) && !empty($post_data['product_id']) && array_key_exists('product_variation', $post_data) && !empty($post_data['product_variation'])) {
				$original_product_language = $woocommerce_wpml->products->get_original_product_language($post_data['product_id']);
				$post_data['product_variation'] = apply_filters('translate_object_id', $post_data['product_variation'], 'product_variation', true, $original_product_language);
			}
		}

		return $post_data;
	}

	add_filter('tinvwl_addtowishlist_prepare', 'tinvwl_wpml_addtowishlist_prepare');
}

if (!function_exists('tinvwl_wpml_addtowishlist_check_product')) {

	/**
	 * Change product data if product need translate in WooCommerce Multilingual
	 *
	 * @param object $product WC_Product object.
	 *
	 * @return object $product WC_Product object
	 */
	function tinvwl_wpml_addtowishlist_check_product($product)
	{
		if (class_exists('woocommerce_wpml')) {

			global $woocommerce_wpml, $sitepress, $wpdb;

			// Reload products class.
			if (version_compare(WCML_VERSION, '4.4.0', '<')) {
				$woocommerce_wpml->products = new WCML_Products($woocommerce_wpml, $sitepress, $wpdb);
			} else {
				global $wpml_post_translations;
				if (!($woocommerce_wpml instanceof woocommerce_wpml) || !($sitepress instanceof SitePress) || !($wpml_post_translations instanceof WPML_Post_Translation)) {
					return $product;
				}
				$woocommerce_wpml->products = new WCML_Products($woocommerce_wpml, $sitepress, $wpml_post_translations, $wpdb);
			}

			if ($product) {
				$product = wc_get_product($woocommerce_wpml->products->get_original_product_id($product->get_id()));
			}

		}

		return $product;
	}

	add_filter('tinvwl_addtowishlist_check_product', 'tinvwl_wpml_addtowishlist_check_product');
}

if (!function_exists('tinvwl_wpml_addtowishlist_out_prepare')) {

	/**
	 * Change product data if product need translate in WooCommerce Multilingual
	 *
	 * @param array $attr Data for wishlist.
	 *
	 * @return array
	 */
	function tinvwl_wpml_addtowishlist_out_prepare($attr)
	{
		if (class_exists('woocommerce_wpml')) {

			global $woocommerce_wpml, $sitepress, $wpdb;

			// Reload products class.
			if (version_compare(WCML_VERSION, '4.4.0', '<')) {
				$woocommerce_wpml->products = new WCML_Products($woocommerce_wpml, $sitepress, $wpdb);
			} else {
				global $wpml_post_translations;
				if (!($woocommerce_wpml instanceof woocommerce_wpml) || !($sitepress instanceof SitePress) || !($wpml_post_translations instanceof WPML_Post_Translation)) {
					return $attr;
				}
				$woocommerce_wpml->products = new WCML_Products($woocommerce_wpml, $sitepress, $wpml_post_translations, $wpdb);
			}

			if (array_key_exists('product_id', $attr) && !empty($attr['product_id'])) {
				$attr['product_id'] = $woocommerce_wpml->products->get_original_product_id($attr['product_id']);
			}
			if (array_key_exists('product_id', $attr) && !empty($attr['product_id']) && array_key_exists('variation_id', $attr) && !empty($attr['variation_id'])) {
				$original_product_language = $woocommerce_wpml->products->get_original_product_language($attr['product_id']);
				$attr['variation_id'] = apply_filters('translate_object_id', $attr['variation_id'], 'product_variation', true, $original_product_language);
			}
		}

		return $attr;
	}

	add_filter('tinvwl_addtowishlist_out_prepare_attr', 'tinvwl_wpml_addtowishlist_out_prepare');
}

if (!function_exists('tinvwl_wpml_addtowishlist_out_prepare_product')) {

	/**
	 * Change product if product need translate in WooCommerce Multilingual
	 *
	 * @param \WC_Product $product WooCommerce Product.
	 *
	 * @return \WC_Product
	 */
	function tinvwl_wpml_addtowishlist_out_prepare_product($product)
	{
		if (class_exists('woocommerce_wpml') && is_object($product)) {

			global $woocommerce_wpml, $sitepress, $wpdb;

			// Reload products class.
			if (version_compare(WCML_VERSION, '4.4.0', '<')) {
				$woocommerce_wpml->products = new WCML_Products($woocommerce_wpml, $sitepress, $wpdb);
			} else {
				global $wpml_post_translations;
				if (!($woocommerce_wpml instanceof woocommerce_wpml) || !($sitepress instanceof SitePress) || !($wpml_post_translations instanceof WPML_Post_Translation)) {
					return $product;
				}
				$woocommerce_wpml->products = new WCML_Products($woocommerce_wpml, $sitepress, $wpml_post_translations, $wpdb);
			}

			$product_id = $product->is_type('variation') ? $product->get_parent_id() : $product->get_id();
			$variation_id = $product->is_type('variation') ? $product->get_id() : 0;

			if (!empty($product_id)) {
				$product_id = $woocommerce_wpml->products->get_original_product_id($product_id);
			}
			if (!empty($product_id) && !empty($variation_id)) {
				$original_product_language = $woocommerce_wpml->products->get_original_product_language($product_id);
				$variation_id = apply_filters('translate_object_id', $variation_id, 'product_variation', true, $original_product_language);
			}
			if (!empty($product_id)) {
				$product = wc_get_product($variation_id ? $variation_id : $product_id);
			}
		}

		return $product;
	}

	add_filter('tinvwl_addtowishlist_out_prepare_product', 'tinvwl_wpml_addtowishlist_out_prepare_product');
}

if (!function_exists('tinvwl_wpml_addtowishlist_prepare_form')) {

	/**
	 * Change product form data if product need translate in WooCommerce Multilingual
	 *
	 * @param array $post_data Data for wishlist.
	 *
	 * @return array
	 */
	function tinvwl_wpml_addtowishlist_prepare_form($post_data)
	{
		if (class_exists('woocommerce_wpml') && is_array($post_data)) {

			global $woocommerce_wpml, $sitepress, $wpdb;

			// Reload products class.
			if (version_compare(WCML_VERSION, '4.4.0', '<')) {
				$woocommerce_wpml->products = new WCML_Products($woocommerce_wpml, $sitepress, $wpdb);
			} else {
				global $wpml_post_translations;
				if (!($woocommerce_wpml instanceof woocommerce_wpml) || !($sitepress instanceof SitePress) || !($wpml_post_translations instanceof WPML_Post_Translation)) {
					return $post_data;
				}
				$woocommerce_wpml->products = new WCML_Products($woocommerce_wpml, $sitepress, $wpml_post_translations, $wpdb);
			}

			if (array_key_exists('product_id', $post_data) && !empty($post_data['product_id'])) {
				$post_data['product_id'] = $woocommerce_wpml->products->get_original_product_id($post_data['product_id']);
			}
			if (array_key_exists('product_id', $post_data) && !empty($post_data['product_id']) && array_key_exists('variation_id', $post_data) && !empty($post_data['variation_id'])) {
				$original_product_language = $woocommerce_wpml->products->get_original_product_language($post_data['product_id']);
				$post_data['variation_id'] = apply_filters('translate_object_id', $post_data['variation_id'], 'product_variation', true, $original_product_language);
			}
		}

		return $post_data;
	}

	add_filter('tinvwl_addtowishlist_prepare_form', 'tinvwl_wpml_addtowishlist_prepare_form');
}
