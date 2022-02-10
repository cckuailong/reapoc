<?php
/**
 * TI WooCommerce Wishlist integration with:
 *
 * @name Hide Price and Add to Cart Button
 *
 * @version 1.2.1
 *
 * @slug hide-price-add-to-cart-button
 *
 * @url https://woocommerce.com/products/hide-price-add-to-cart-button/
 *
 */

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
	exit;
}

// Load integration depends on current settings.
global $tinvwl_integrations;

$slug = "hide-price-add-to-cart-button";

$name = "Hide Price and Add to Cart Button";

$available = class_exists('Addify_Woo_Hide_Price_Front');

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

if (class_exists('Addify_Woo_Hide_Price_Front')) {

	function tinvwl_check_class_action($tag, $class, $method)
	{
		global $wp_filter;
		if (isset($wp_filter[$tag])) {
			$len = strlen($method);

			foreach ($wp_filter[$tag] as $_priority => $actions) {

				if ($actions) {
					foreach ($actions as $function_key => $data) {

						if ($data) {
							if (substr($function_key, -$len) == $method) {

								if ($class !== '') {
									if (is_string($data['function'][0])) {
										$_class = $data['function'][0];
									} elseif (is_object($data['function'][0])) {
										$_class = get_class($data['function'][0]);
									} else {
										return false;
									}

									if ($_class !== '' && $_class == $class) {
										return true;
									}
								} else {
									return true;
								}

							}
						}
					}
				}
			}
		}

		return false;
	}

	add_action('woocommerce_single_product_summary', 'tinvwl_afwhp_hooks', 31);
	add_action('woocommerce_single_variation', 'tinvwl_afwhp_hooks', 31);

	function tinvwl_afwhp_hooks()
	{

		$simple = tinvwl_check_class_action('woocommerce_single_product_summary', 'Addify_Woo_Hide_Price_Front', 'afwhp_custom_button_replacement');
		if ($simple) {
			add_action('woocommerce_single_product_summary', 'tinvwl_view_addto_html', 32);
			add_action('woocommerce_single_product_summary', 'tinvwl_view_addto_htmlout', 32);
		}

		$variable = tinvwl_check_class_action('woocommerce_single_variation', 'Addify_Woo_Hide_Price_Front', 'afwhp_custom_button_replacement');
		if ($variable) {
			add_action('woocommerce_single_variation', 'tinvwl_view_addto_html', 32);
			add_action('woocommerce_single_variation', 'tinvwl_view_addto_htmlout', 32);

			ob_start(); ?>
			<script>
				(function ($) {
					$(document).ready(function () {
						$(document).on('show_variation', '.variations_form', function (a, b, d) {
							var e = $(this).find('.tinvwl_add_to_wishlist_button');
							if (e.length) {
								e.attr('data-tinv-wl-productvariation', b.variation_id);
							}
							a.preventDefault();
						});
					});
				})(jQuery);
			</script>


			<?php $content = ob_get_clean();
			echo $content;
		}
	}

	//wishlist add to cart button
	add_filter('tinvwl_wishlist_item_action_add_to_cart', 'tinvwl_product_allow_add_to_cart_afwhp', 10, 3);

	/**
	 * Allow show button add to cart
	 *
	 * @param boolean $allow Settings flag.
	 * @param array $wlproduct Wishlist Product.
	 * @param WC_Product $product Product.
	 *
	 * @return boolean
	 */
	function tinvwl_product_allow_add_to_cart_afwhp($allow, $wlproduct, $product)
	{

		$args = array(
				'post_type' => 'addify_whp',
				'post_status' => 'publish',
				'numberposts' => -1,
				'orderby' => 'menu_order',
				'order' => 'ASC'

		);
		$rules = get_posts($args);
		foreach ($rules as $rule) {

			$afwhp_rule_type = get_post_meta(intval($rule->ID), 'afwhp_rule_type', true);
			$afwhp_hide_products = unserialize(get_post_meta(intval($rule->ID), 'afwhp_hide_products', true));
			$afwhp_hide_categories = unserialize(get_post_meta(intval($rule->ID), 'afwhp_hide_categories', true));
			$afwhp_hide_user_role = unserialize(get_post_meta(intval($rule->ID), 'afwhp_hide_user_role', true));
			$afwhp_is_hide_addtocart = get_post_meta(intval($rule->ID), 'afwhp_is_hide_addtocart', true);
			$afwhp_custom_button_text = get_post_meta(intval($rule->ID), 'afwhp_custom_button_text', true);
			$afwhp_custom_button_link = get_post_meta(intval($rule->ID), 'afwhp_custom_button_link', true);
			$afwhp_contact7_form = get_post_meta(intval($rule->ID), 'afwhp_contact7_form', true);
			$afwhp_hide_for_countries = unserialize(get_post_meta(intval($rule->ID), 'afwhp_hide_for_countries', true));

			if (!empty($afwhp_hide_for_countries)) {
				//country
				if (!empty($_SERVER['REMOTE_ADDR'])) {
					$ip = sanitize_meta('', $_SERVER['REMOTE_ADDR'], '');
				} else {
					$ip = '';
				}
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, 'http://www.geoplugin.net/json.gp?ip=' . $ip);
				curl_setopt($ch, CURLOPT_HEADER, 0);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				$ip_data_in = curl_exec($ch); // string
				curl_close($ch);

				$ip_data = json_decode($ip_data_in, true);
				$ip_data = str_replace('&quot;', '"', $ip_data); // for PHP 5.2 see stackoverflow.com/questions/3110487/

				if ($ip_data && null != $ip_data['geoplugin_countryCode']) {
					$country = $ip_data['geoplugin_countryCode'];
				}

				$curr_country = $country;
			} else {
				$curr_country = '';
			}

			$istrue = false;

			if (!empty($afwhp_hide_for_countries) && in_array($curr_country, $afwhp_hide_for_countries)) {

				$iscountry = true;

			} elseif (empty($afwhp_hide_for_countries)) {

				$iscountry = true;

			} else {

				$iscountry = false;
			}

			$applied_on_all_products = get_post_meta($rule->ID, 'afwhp_apply_on_all_products', true);


			//Registered Users
			if ('afwhp_for_registered_users' == $afwhp_rule_type) {

				if (is_user_logged_in()) {

					// get Current User Role
					$curr_user = wp_get_current_user();
					$user_data = get_user_meta($curr_user->ID);
					$curr_user_role = $curr_user->roles[0];

					if ('yes' == $applied_on_all_products && empty($afwhp_hide_user_role)) {
						$istrue = true;
					} elseif ((is_array($afwhp_hide_user_role) && in_array($curr_user_role, $afwhp_hide_user_role)) && 'yes' == $applied_on_all_products) {
						$istrue = true;
					} elseif ((is_array($afwhp_hide_user_role) && in_array($curr_user_role, $afwhp_hide_user_role)) && (is_array($afwhp_hide_products) && in_array($product->get_id(), $afwhp_hide_products))) {
						$istrue = true;
					}


					//Products
					if ($istrue && $iscountry) {

						if ('yes' == $afwhp_is_hide_addtocart) {

							if ('' == $afwhp_custom_button_text) {

								echo '';
							} else {

								if (!empty($afwhp_custom_button_link)) {

									echo '<a href="' . esc_url($afwhp_custom_button_link) . '" rel="nofollow" class="button add_to_cart_button product_type_' . esc_attr($product->get_type()) . '">' . esc_attr($afwhp_custom_button_text) . '</a>';
								} elseif (!empty($afwhp_contact7_form)) {

									$contact7 = get_post($afwhp_contact7_form);

									$form_title = $contact7->post_title;

									?>
									<a href="javascript:void(0)"
									   onclick="showPopForm('<?php echo esc_attr($afwhp_contact7_form) . esc_attr($product->get_id()); ?>')"
									   class="form_popup<?php echo esc_attr($afwhp_contact7_form) . esc_attr($product->get_id()); ?>_open button product_type_simple add_to_cart_button"><?php echo esc_attr($afwhp_custom_button_text); ?></a>
									<div id="form_popup<?php echo esc_attr($afwhp_contact7_form) . esc_attr($product->get_id()); ?>"
										 class="form_popup">

										<button class="form_popup<?php echo esc_attr($afwhp_contact7_form) . esc_attr($product->get_id()); ?>_close form_close_btn btn btn-default">
											X
										</button>

										<?php echo do_shortcode('[contact-form-7 id="' . $afwhp_contact7_form . '" title="' . $form_title . '" ] '); ?>

									</div>

									<?php

								} else {

									echo '<a href="javascript:void(0)" rel="nofollow" class="button add_to_cart_button product_type_' . esc_attr($product->get_type()) . '">' . esc_attr($afwhp_custom_button_text) . '</a>';
								}

							}
						}

					}

					//Categories

					if (!empty($afwhp_hide_categories) && !$istrue && $iscountry) {

						foreach ($afwhp_hide_categories as $cat) {

							if (has_term($cat, 'product_cat', $product->get_id())) {

								if (in_array($curr_user_role, $afwhp_hide_user_role)) {

									if ('yes' == $afwhp_is_hide_addtocart) {

										if ('' == $afwhp_custom_button_text) {

											echo '';
										} else {

											if (!empty($afwhp_custom_button_link)) {

												echo '<a href="' . esc_url($afwhp_custom_button_link) . '" rel="nofollow" class="button add_to_cart_button product_type_' . esc_attr($product->get_type()) . '">' . esc_attr($afwhp_custom_button_text) . '</a>';

												return;
											} elseif (!empty($afwhp_contact7_form)) {

												$contact7 = get_post($afwhp_contact7_form);


												$form_title = $contact7->post_title;

												?>
												<a href="javascript:void(0)"
												   onclick="showPopForm('<?php echo esc_attr($afwhp_contact7_form) . esc_attr($product->get_id()); ?>')"
												   class="form_popup<?php echo esc_attr($afwhp_contact7_form) . esc_attr($product->get_id()); ?>_open button product_type_simple add_to_cart_button"><?php echo esc_attr($afwhp_custom_button_text); ?></a>
												<div id="form_popup<?php echo esc_attr($afwhp_contact7_form) . esc_attr($product->get_id()); ?>"
													 class="form_popup">

													<button class="form_popup<?php echo esc_attr($afwhp_contact7_form) . esc_attr($product->get_id()); ?>_close form_close_btn btn btn-default">
														X
													</button>

													<?php echo do_shortcode('[contact-form-7 id="' . $afwhp_contact7_form . '" title="' . $form_title . '" ] '); ?>

												</div>

												<?php
												return;
											} else {

												echo '<a href="javascript:void(0)" rel="nofollow" class="button add_to_cart_button product_type_' . esc_attr($product->get_type()) . '">' . esc_attr($afwhp_custom_button_text) . '</a>';

												return;
											}

										}
									}

								}

							}

						}
					}


				}

			} else {
				//Guest Users
				if (!is_user_logged_in()) {

					//Products
					if ('yes' == $applied_on_all_products) {
						$istrue = true;
					} elseif (is_array($afwhp_hide_products) && in_array($product->get_id(), $afwhp_hide_products)) {
						$istrue = true;
					}

					if ($istrue && $iscountry) {

						if ('yes' == $afwhp_is_hide_addtocart) {

							if ('' == $afwhp_custom_button_text) {

								echo '';
							} else {

								if (!empty($afwhp_custom_button_link)) {

									echo '<a href="' . esc_url($afwhp_custom_button_link) . '" rel="nofollow" class="button add_to_cart_button product_type_' . esc_attr($product->get_type()) . '">' . esc_attr($afwhp_custom_button_text) . '</a>';
								} elseif (!empty($afwhp_contact7_form)) {

									$contact7 = get_post($afwhp_contact7_form);


									$form_title = $contact7->post_title;

									?>
									<a href="javascript:void(0)"
									   onclick="showPopForm('<?php echo esc_attr($afwhp_contact7_form) . esc_attr($product->get_id()); ?>')"
									   class="form_popup<?php echo esc_attr($afwhp_contact7_form) . esc_attr($product->get_id()); ?>_open button product_type_simple add_to_cart_button"><?php echo esc_attr($afwhp_custom_button_text); ?></a>
									<div id="form_popup<?php echo esc_attr($afwhp_contact7_form) . esc_attr($product->get_id()); ?>"
										 class="form_popup">

										<button class="form_popup<?php echo esc_attr($afwhp_contact7_form) . esc_attr($product->get_id()); ?>_close form_close_btn btn btn-default">
											X
										</button>

										<?php echo do_shortcode('[contact-form-7 id="' . $afwhp_contact7_form . '" title="' . $form_title . '" ] '); ?>

									</div>

									<?php

								} else {

									echo '<a href="javascript:void(0)" rel="nofollow" class="button add_to_cart_button product_type_' . esc_attr($product->get_type()) . '">' . esc_attr($afwhp_custom_button_text) . '</a>';
								}

							}
						}

					}


					//Categories
					if (!empty($afwhp_hide_categories) && !$istrue && $iscountry) {

						foreach ($afwhp_hide_categories as $cat) {

							if (has_term($cat, 'product_cat', $product->get_id())) {

								if ('yes' == $afwhp_is_hide_addtocart) {

									if ('' == $afwhp_custom_button_text) {

										echo '';
									} else {

										if (!empty($afwhp_custom_button_link)) {

											echo '<a href="' . esc_url($afwhp_custom_button_link) . '" rel="nofollow" class="button add_to_cart_button product_type_' . esc_attr($product->get_type()) . '">' . esc_attr($afwhp_custom_button_text) . '</a>';

											return;
										} elseif (!empty($afwhp_contact7_form)) {

											$contact7 = get_post($afwhp_contact7_form);


											$form_title = $contact7->post_title;

											?>
											<a href="javascript:void(0)"
											   onclick="showPopForm('<?php echo esc_attr($afwhp_contact7_form) . esc_attr($product->get_id()); ?>')"
											   class="form_popup<?php echo esc_attr($afwhp_contact7_form) . esc_attr($product->get_id()); ?>_open button product_type_simple add_to_cart_button"><?php echo esc_attr($afwhp_custom_button_text); ?></a>
											<div id="form_popup<?php echo esc_attr($afwhp_contact7_form) . esc_attr($product->get_id()); ?>"
												 class="form_popup">

												<button class="form_popup<?php echo esc_attr($afwhp_contact7_form) . esc_attr($product->get_id()); ?>_close form_close_btn btn btn-default">
													X
												</button>

												<?php echo do_shortcode('[contact-form-7 id="' . $afwhp_contact7_form . '" title="' . $form_title . '" ] '); ?>

											</div>

											<?php
											return;
										} else {

											echo '<a href="javascript:void(0)" rel="nofollow" class="button add_to_cart_button product_type_' . esc_attr($product->get_type()) . '">' . esc_attr($afwhp_custom_button_text) . '</a>';

											return;
										}

									}
								}

							}

						}
					}

				}
			}

		}
	}
}
