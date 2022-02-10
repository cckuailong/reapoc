<?php
/**
 * TI WooCommerce Wishlist integration with:
 *
 * @name WooCommerce – Gift Cards
 *
 * @version 2.6.5
 *
 * @slug gift-cards-for-woocommerce
 *
 * @url https://wordpress.org/plugins/gift-cards-for-woocommerce/
 *
 */

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
	exit;
}

// Load integration depends on current settings.
global $tinvwl_integrations;

$slug = "gift-cards-for-woocommerce";

$name = "WooCommerce – Gift Cards";

$available = class_exists('KODIAK_GIFTCARDS');

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

if (!function_exists('tinvwl_gift_card_add')) {

	/**
	 * Support WooCommerce - Gift Cards
	 * Redirect to page gift card, if requires that customers enter a name and email when purchasing a Gift Card.
	 *
	 * @param boolean $redirect Default value to redirect.
	 * @param \WC_Product $product Product data.
	 *
	 * @return boolean
	 */
	function tinvwl_gift_card_add($redirect, $product)
	{
		if (class_exists('KODIAK_GIFTCARDS')) {
			$is_required_field_giftcard = get_option('woocommerce_enable_giftcard_info_requirements');

			if ('yes' == $is_required_field_giftcard) { // WPCS: loose comparison ok.
				$is_giftcard = get_post_meta($product->get_id(), '_giftcard', true);
				if ('yes' == $is_giftcard) { // WPCS: loose comparison ok.
					return true;
				}
			}
		}

		return $redirect;
	}

	add_filter('tinvwl_product_add_to_cart_need_redirect', 'tinvwl_gift_card_add', 20, 2);
}

if (!function_exists('tinvwl_gift_card_add_url')) {

	/**
	 * Support WooCommerce - Gift Cards
	 * Redirect to page gift card, if requires that customers enter a name and email when purchasing a Gift Card.
	 *
	 * @param string $redirect_url Default value to redirect.
	 * @param \WC_Product $product Product data.
	 *
	 * @return boolean
	 */
	function tinvwl_gift_card_add_url($redirect_url, $product)
	{
		if (class_exists('KODIAK_GIFTCARDS')) {
			$is_required_field_giftcard = get_option('woocommerce_enable_giftcard_info_requirements');

			if ('yes' == $is_required_field_giftcard) { // WPCS: loose comparison ok.
				$is_giftcard = get_post_meta($product->get_id(), '_giftcard', true);
				if ('yes' == $is_giftcard) { // WPCS: loose comparison ok.
					return $product->get_permalink();
				}
			}
		}

		return $redirect_url;
	}

	add_filter('tinvwl_product_add_to_cart_redirect_url', 'tinvwl_gift_card_add_url', 20, 2);
}

if (!function_exists('tinv_wishlist_meta_support_rpgiftcards')) {

	/**
	 * Set description for meta WooCommerce - Gift Cards
	 *
	 * @param array $meta Meta array.
	 *
	 * @return array
	 */
	function tinv_wishlist_metasupport_rpgiftcards($meta)
	{
		if (class_exists('KODIAK_GIFTCARDS')) {
			foreach ($meta as $key => $data) {
				switch ($data['key']) {
					case 'rpgc_note':
						$meta[$key]['key'] = __('Note', 'ti-woocommerce-wishlist');
						break;
					case 'rpgc_to':
						$meta[$key]['key'] = (get_option('woocommerce_giftcard_to') <> null ? get_option('woocommerce_giftcard_to') : __('To', 'ti-woocommerce-wishlist')); // WPCS: loose comparison ok.
						break;
					case 'rpgc_to_email':
						$meta[$key]['key'] = (get_option('woocommerce_giftcard_toEmail') <> null ? get_option('woocommerce_giftcard_toEmail') : __('To Email', 'ti-woocommerce-wishlist')); // WPCS: loose comparison ok.
						break;
					case 'rpgc_address':
						$meta[$key]['key'] = (get_option('woocommerce_giftcard_address') <> null ? get_option('woocommerce_giftcard_address') : __('Address', 'ti-woocommerce-wishlist')); // WPCS: loose comparison ok.
						break;
					case 'rpgc_reload_card':
						$meta[$key]['key'] = __('Reload existing Gift Card', 'ti-woocommerce-wishlist');
						break;
					case 'rpgc_description':
					case 'rpgc_reload_check':
						unset($meta[$key]);
						break;
				}
			}
		}

		return $meta;
	}

	add_filter('tinvwl_wishlist_item_meta_post', 'tinv_wishlist_metasupport_rpgiftcards');
} // End if().

if (!function_exists('tinv_wishlist_metaprepare_rpgiftcards')) {

	/**
	 * Prepare save meta for WooCommerce - Gift Cards
	 *
	 * @param array $meta Meta array.
	 *
	 * @return array
	 */
	function tinv_wishlist_metaprepare_rpgiftcards($meta)
	{
		if (class_exists('KODIAK_GIFTCARDS')) {
			if (array_key_exists('rpgc_reload_check', $meta)) {
				foreach (array('rpgc_note', 'rpgc_to', 'rpgc_to_email', 'rpgc_address') as $value) {
					if (array_key_exists($value, $meta)) {
						unset($meta[$value]);
					}
				}
			}
		}

		return $meta;
	}

	add_filter('tinvwl_product_prepare_meta', 'tinv_wishlist_metaprepare_rpgiftcards');
}
