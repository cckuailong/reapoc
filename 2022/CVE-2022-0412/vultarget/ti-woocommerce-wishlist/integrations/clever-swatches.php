<?php
/**
 * TI WooCommerce Wishlist integration with:
 *
 * @name Clever Swatches
 *
 * @version 2.1.6
 *
 * @slug clever-swatches
 *
 * @url https://codecanyon.net/item/cleverswatches-woocommerce-color-or-image-variation-swatches/20594889
 *
 */

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
	exit;
}

// Load integration depends on current settings.
global $tinvwl_integrations;

$slug = "clever-swatches";

$name = "Clever Swatches";

$available = class_exists('Zoo_Clever_Swatch_Install');

$tinvwl_integrations = is_array($tinvwl_integrations) ? $tinvwl_integrations : [];

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

if (!function_exists('tinv_wishlist_meta_support_clever_swatches')) {

	/**
	 * Set description for meta Improved Product Options for WooCommerce
	 *
	 * @param array $meta Meta array.
	 *
	 * @return array
	 */
	function tinv_wishlist_meta_support_clever_swatches($meta)
	{

		if (class_exists('Zoo_Clever_Swatch_Install')) {
			if (!empty($meta['old_variation_id'])) {
				unset($meta['old_variation_id']);
			}
		}

		return $meta;
	}

	add_filter('tinvwl_wishlist_item_meta_post', 'tinv_wishlist_meta_support_clever_swatches');
} // End if().

function tinv_add_to_wishlist_clever_swatches()
{
	if (class_exists('Zoo_Clever_Swatch_Install')) {

		wp_add_inline_script('tinvwl', "
		jQuery(document).ready(function($){
			  $(document).on('cleverswatch_update_gallery cleverswatch_update_cw_gallery',function (e, data) {
					if (data.product_id === data.variation_id){
						$(data.form_add_to_cart).trigger('hide_variation');
					} else {
						$(data.form_add_to_cart).trigger('show_variation', data, true);
					}
			  });
			  $(document).on('tinvwl_wishlist_button_clicked', function (e, el, data) {
			        var button = $(el);

			        var wrapper = button.closest('div.tinv-wraper');

			        if (wrapper.hasClass('tinvwl-loop-button-wrapper')){

			            var container = wrapper.closest('*.product');

			            if (container.find('a.add_to_cart_button').length > 0){
		                     data.form.variation_id = container.find('a.add_to_cart_button').data('variation_id');
			            }
			        }
			  });
        });
        ");
	}
}

add_action('wp_enqueue_scripts', 'tinv_add_to_wishlist_clever_swatches', 100, 1);
