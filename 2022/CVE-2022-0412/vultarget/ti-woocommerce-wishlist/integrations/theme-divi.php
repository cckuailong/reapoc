<?php
/**
 * TI WooCommerce Wishlist integration with:
 *
 * @name Divi
 *
 * @version 4.4.6
 *
 * @slug divi
 *
 * @url http://www.elegantthemes.com/gallery/divi/
 *
 */

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
	exit;
}

// Load integration depends on current settings.
global $tinvwl_integrations;

$slug = "divi";

$name = "Divi Theme";

$available = true;

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

/**
 * Run hooks on page redirect.
 */
function tinvwl_divi_init()
{
	if (class_exists('ET_Builder_Element') && is_product() && version_compare(ET_BUILDER_PRODUCT_VERSION, '4.0.0', '>=')) {
		global $post;
		$product = wc_get_product($post->ID);
		if (!empty($product) && !$product->is_in_stock()) {
			remove_action('woocommerce_single_product_summary', 'tinvwl_view_addto_htmlout', 29);
			remove_action('woocommerce_single_product_summary', 'tinvwl_view_addto_htmlout', 31);
			add_action('woocommerce_' . $product->get_type() . '_add_to_cart', 'tinvwl_divi_single_product_summary', 40);
		}
	}
}

add_action('template_redirect', 'tinvwl_divi_init');

// Add a custom hook for single page.
function tinvwl_divi_single_product_summary()
{
	do_action('tinvwl_single_product_summary');
}
