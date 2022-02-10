<?php
/**
 * TI WooCommerce Wishlist integration with:
 *
 * @name Improved Product Options for WooCommerce
 *
 * @version 5.1.0
 *
 * @slug improved-variable-product-attributes
 *
 * @url https://codecanyon.net/item/improved-variable-product-attributes-for-woocommerce/9981757
 *
 */

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
	exit;
}

// Load integration depends on current settings.
global $tinvwl_integrations;

$slug = "improved-variable-product-attributes";

$name = "Improved Product Options for WooCommerce";

$available = class_exists('XforWC_Improved_Options');

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

if (!function_exists('tinv_wishlist_meta_support_ivpa')) {

	/**
	 * Set description for meta Improved Product Options for WooCommerce
	 *
	 * @param array $meta Meta array.
	 *
	 * @return array
	 */
	function tinv_wishlist_meta_support_ivpa($meta)
	{
		global $product;

		if (class_exists('XforWC_Improved_Options')) {

			$curr_customizations = XforWC_Improved_Options_Frontend::get_settings();

			foreach ($meta as $k => $v) {

				$prefix = 'ivpac_';
				$k_ivpac = (0 === strpos($k, $prefix)) ? substr($k, strlen($prefix)) : $k;

				$prefix = 'attribute_';
				$k_ivpac = (0 === strpos($k, $prefix)) ? substr($k, strlen($prefix)) : $k_ivpac;
				$local_attribute = (0 === strpos($k, $prefix)) ? true : false;
				$v = is_array($v['display']) ? implode(', ', $v['display']) : $v['display'];

				if (isset($curr_customizations['ivpa_attr'][$k_ivpac])) {
					if ($curr_customizations['ivpa_attr'][$k_ivpac] == 'ivpa_custom') {
						$meta[$k] = array(
							'key' => $curr_customizations['ivpa_title'][$k_ivpac],
							'display' => $v,
						);
					}
				}

				if (in_array($k_ivpac, $curr_customizations['ivpa_attr'])) {

					$attributes = $product->get_attributes();
					$attribute = sanitize_title($k_ivpac);

					$term_slug = '';

					if (isset($attributes[$attribute])) {
						$term_slug = $attributes[$attribute];
					} elseif (isset($attributes['pa_' . $attribute])) {
						$term_slug = $attributes['pa_' . $attribute];
					}

					if ($product->is_type('variation') && $term_slug === $v) {
						unset($meta[$k]);
					} else {
						$meta[$k] = array(
							'key' => wc_attribute_label($k_ivpac),
							'display' => $v,
						);
					}
				} elseif (wc_attribute_label($k_ivpac) && $local_attribute) {
					$meta[$k] = array(
						'key' => wc_attribute_label($k_ivpac),
						'display' => $v,
					);
				}
			}
		}

		return $meta;
	}

	add_filter('tinvwl_wishlist_item_meta_post', 'tinv_wishlist_meta_support_ivpa');
} // End if().


function tinv_add_to_wishlist_ivpa()
{
	if (class_exists('XforWC_Improved_Options')) {

		wp_add_inline_script('tinvwl', "
		jQuery(document).ready(function($){
		    $(document).on('tinvwl_wishlist_button_clicked', function (e, el, data) {
				if (typeof ivpa === 'undefined' || !ivpa) {
					return false;
				}
				var button = $(el);
				var container = button.closest(ivpa.settings.archive_selector);
				var find = button.closest('.summary').length > 0 ? '#ivpa-content' : '.ivpa-content';

				if ( container.find(find).length > 0 ) {
					var var_id = container.find(find).attr('data-selected');

					if ( typeof var_id == 'undefined' || var_id == '' ) {
						var_id = container.find('[name=\"variation_id\"]').val();
					}

					if ( typeof var_id == 'undefined' || var_id == '' ) {
						var_id = container.find(find).attr('data-id');
					}

					var item = {};

					container.find(find+' .ivpa_attribute').each( function() {
						var attribute = $(this).attr('data-attribute');
						var attribute_value = $(this).find('.ivpa_term.ivpa_clicked').attr('data-term');

					data.form['attribute_' + attribute] = attribute_value;
					});

					var ivpac = container.find(find+' .ivpa_custom_option').length>0 ? container.find(find+' .ivpa_custom_option [name^=\"ivpac_\"]').serialize() : '';

					var ivpac_fields = container.find(find + ' .ivpa_custom_option').length > 0 ? container.find(find + ' .ivpa_custom_option [name^=\"ivpac_\"]') : '';

					if(ivpac_fields){

						ivpac_fields.each(function () {

							var name = $(this).attr('name').replace(/\[.*\]/g, '');

							if ($(this).is(':checkbox')) {

								if (!$(this).is(':checked')) return true;

								if (data.form.hasOwnProperty(name) && data.form[name].length) {
									data.form[name] = (data.form[name] + ', ' + $(this).val()).replace(/^, /, '');
								} else {
									data.form[name] = $(this).val();
								}
							} else {
								data.form[name] = $(this).val();
							}
						});
					}

					data.form.variation_id = var_id;
					data.product_variation = var_id;
					data.ivpac = ivpac;
				}
			});
        });
        ");
	}
}

add_action('wp_enqueue_scripts', 'tinv_add_to_wishlist_ivpa', 100, 1);
