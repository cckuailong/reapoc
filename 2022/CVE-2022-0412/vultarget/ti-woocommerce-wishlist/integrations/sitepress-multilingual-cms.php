<?php
/**
 * TI WooCommerce Wishlist integration with:
 *
 * @name WPML Multilingual CMS
 *
 * @version 4.2.7.1
 *
 * @slug sitepress-multilingual-cms
 *
 * @url https://wpml.org/
 *
 */

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
	exit;
}

// Load integration depends on current settings.
global $tinvwl_integrations;

$slug = "sitepress-multilingual-cms";

$name = "WPML Multilingual CMS";

$available = defined('ICL_SITEPRESS_VERSION');

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

if (!function_exists('tinvwl_wpml_product_get')) {

	/**
	 * Change product data if product need translate
	 *
	 * @param array $product Wishlistl product.
	 *
	 * @return array
	 */
	function tinvwl_wpml_product_get($product)
	{
		if (array_key_exists('data', $product)) {
			$_product_id = $product_id = $product['product_id'];
			$_variation_id = $variation_id = $product['variation_id'];
			$_product_id = apply_filters('wpml_object_id', $_product_id, 'product', true);
			if (!empty($_variation_id)) {
				$_variation_id = apply_filters('wpml_object_id', $_variation_id, 'product', true);
			}
			if ($_product_id !== $product_id || $_variation_id !== $variation_id) {
				$product['data'] = wc_get_product($variation_id ? $_variation_id : $_product_id);
			}
		}

		return $product;
	}

	add_filter('tinvwl_wishlist_product_get', 'tinvwl_wpml_product_get');
}

if (!function_exists('tinvwl_wpml_filter_link')) {

	/**
	 * Correct add wishlist key for WPML plugin.
	 *
	 * @param string $full_link Link for page.
	 * @param array $l Language.
	 *
	 * @return string
	 */
	function tinvwl_wpml_filter_link($full_link, $l)
	{
		$share_key = get_query_var('tinvwlID', null);
		if (!empty($share_key)) {
			if (get_option('permalink_structure')) {
				$suffix = '';
				if (preg_match('/([^\?]+)\?*?(.*)/i', $full_link, $_full_link)) {
					$full_link = $_full_link[1];
					$suffix = $_full_link[2];
				}
				if (!preg_match('/\/$/', $full_link)) {
					$full_link .= '/';
				}
				$full_link .= $share_key . '/' . $suffix;
			} else {
				$full_link .= add_query_arg('tinvwlID', $share_key, $full_link);
			}
		}

		return $full_link;
	}

	add_filter('WPML_filter_link', 'tinvwl_wpml_filter_link', 0, 2);
}
