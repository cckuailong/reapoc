<?php
/**
 * TI WooCommerce Wishlist integration with:
 *
 * @name WooCommerce Product Add-Ons Ultimate
 *
 * @version 3.9.4
 *
 * @slug product-extras-for-woocommerce
 *
 * @url https://pluginrepublic.com/wordpress-plugins/woocommerce-product-add-ons-ultimate/
 *
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Load integration depends on current settings.
global $tinvwl_integrations;

$slug = "product-extras-for-woocommerce";

$name = "WooCommerce Product Add-Ons Ultimate";

$available = defined( 'PEWC_FILE' );

$tinvwl_integrations = is_array( $tinvwl_integrations ) ? $tinvwl_integrations : [];

$tinvwl_integrations[ $slug ] = array(
	'name'      => $name,
	'available' => $available,
);

if ( ! tinv_get_option( 'integrations', $slug ) ) {
	return;
}

if ( ! $available ) {
	return;
}

if ( ! function_exists( 'tinv_wishlist_item_meta_pewc' ) ) {

	/**
	 * Set description for meta WooCommerce Product Add-Ons Ultimate
	 *
	 * @param array $meta Meta array.
	 * @param array $wl_product Wishlist Product.
	 * @param \WC_Product $product Woocommerce Product.
	 *
	 * @return array
	 */

	function tinv_wishlist_item_meta_pewc( $item_data, $product_id, $variation_id ) {

		if ( defined( 'PEWC_FILE' ) ) {
			// Check for product_extra groups
			$product_extra_groups = pewc_get_extra_fields( $product_id );
			$hidden_group_types   = apply_filters( 'pewc_hidden_field_types_in_cart', array() );

			$posted_data = array();

			foreach ( $item_data as $values ) {
				$posted_data[ $values['key'] ] = $values['display'];
			}

			if ( $product_extra_groups ) {

				foreach ( $product_extra_groups as $group ) {

					if ( isset( $group['items'] ) ) {

						foreach ( $group['items'] as $item ) {

							if ( in_array( $item['field_type'], $hidden_group_types ) ) {
								// Don't add this to the cart if it's a hidden field type
								continue;
							}

							$is_visible = pewc_get_conditional_field_visibility( $item['id'], $item, $group['items'], $product_id, $posted_data, $variation_id );

							if ( ! $is_visible ) {
								continue;
							}

							// Don't display hidden fields
							if ( ! empty( $item['hidden_calculation'] ) ) {
								continue;
							}


							// Added in 3.5.3 to allow us to link parent products with children in cart
							$display_product_meta = apply_filters( 'pewc_display_child_product_meta', false, $item );

							if ( isset( $item['field_type'] ) ) {

								if ( $item['field_type'] == 'products' && ! $display_product_meta ) {
									continue;
								}

								$hide_zero   = get_option( 'pewc_hide_zero', 'no' );
								$show_prices = apply_filters( 'pewc_show_field_prices_in_cart', true, $item );

								// Calculate price
								if ( isset( $item['field_price'] ) ) {

									if ( ( $hide_zero == 'yes' && $item['field_price'] == '0.00' ) || ! $show_prices ) {

										// If price is zero and hide_zero is set, hide the price
										$price = '';

									} else {

										/**
										 * Removed in 3.7.1 because tax was getting doubled
										 */
										// $product_id = $cart_item['data']->get_id();
										// $product = wc_get_product( $product_id );
										// $price = pewc_maybe_include_tax( $product, $item['price'] );
										$price = ' ' . wc_price( $item['field_price'] );

									}

								}

								if ( ! empty( $item['field_flatrate'] ) ) {
									$price = '<span class="pewc-flat-rate-cart-label">(' . __( 'Flat rate cost', 'ti-woocommerce-wishlist' ) . ')</span>';
								}

								$price = apply_filters( 'pewc_filter_cart_item_price', $price, $item );

								$item['label'] = ! empty( $item['field_label'] ) ? sanitize_text_field( $item['field_label'] ) : '';

								$value = ( isset( $item_data[ $item['id'] ] ) && isset( $item_data[ $item['id'] ]['display'] ) ) ? $item_data[ $item['id'] ]['display'] : '';

								if ( $item['field_type'] == 'textarea' ) {
									$value = sanitize_textarea_field( stripslashes( $value ) );
								} else if ( in_array( $item['field_type'], array(
									'image_swatch',
									'radio',
									'upload'
								) ) ) {
									$value = isset( $value[0] ) ? wp_kses_post( stripslashes( $value[0] ) ) : '';
								} else {
									$value = sanitize_text_field( stripslashes( $value ) );
								}

								$item['value'] = $value;

								if ( $item['field_type'] == 'upload' || $item['field_type'] == 'information' ) {
									continue;
								}

								if ( $item['field_type'] == 'checkbox' ) {
									$display = '';
									if ( pewc_show_field_prices_in_cart( $item ) ) {
										$display = '<span class="pewc-price pewc-cart-item-price">' . sanitize_text_field( $price ) . '</span>';
									}

									$item_data[] = array(
										'key'     => sanitize_text_field( $item['label'] ),
										'display' => $display,
									);
								} else if ( $item['field_type'] == 'checkbox_group' ) {

									$display = str_replace( ' | ', '<br>', $item['value'] );
									if ( pewc_show_field_prices_in_cart( $item ) ) {
										$display .= '<span class="pewc-price pewc-cart-item-price">' . sanitize_text_field( $price ) . '</span>';
									}

									$item_data[] = array(
										'key'     => sanitize_text_field( $item['label'] ),
										'display' => $display,
									);
								} else if ( $item['field_type'] == 'name_price' ) {
									$value       = wc_price( $item['value'] );
									$item_data[] = array(
										'key'     => sanitize_text_field( $item['label'] ),
										'display' => sanitize_text_field( $value ),
									);
								} else if ( $item['field_type'] == 'group_heading' ) {
									$item_data[] = array(
										'key'     => '<span class="pewc-cart-group-heading">' . sanitize_text_field( $item['label'] ) . '</span>',
										'display' => '',
									);
								} else {

									$show_field_prices_in_cart = pewc_show_field_prices_in_cart( $item );
									$display                   = wp_kses_post( apply_filters( 'pewc_filter_item_value_in_cart', $item['value'], $item ) );

									if ( $show_field_prices_in_cart ) {
										$display .= '<span class="pewc-cart-item-price">' . $price . '</span>';
									}

									$item_data[] = array(
										'key'     => sanitize_text_field( $item['label'] ),
										'display' => $display,
									);
								}
							}
						}
					}
				}
			}
		}

		foreach ( array_keys( $item_data ) as $key ) {
			if ( strpos( $key, 'pewc' ) === 0 ) {
				unset( $item_data[ $key ] );
			}
		}

		return $item_data;
	}

	add_filter( 'tinvwl_wishlist_item_meta_post', 'tinv_wishlist_item_meta_pewc', 10, 3 );
}

if ( ! function_exists( 'tinvwl_item_price_pewc' ) ) {

	/**
	 * Modify price for WooCommerce Product Add-Ons Ultimate
	 *
	 * @param string $price Returned price.
	 * @param array $wl_product Wishlist Product.
	 * @param \WC_Product $product Woocommerce Product.
	 *
	 * @return string
	 */
	function tinvwl_item_price_pewc( $price, $wl_product, $product ) {

		if ( defined( 'PEWC_FILE' ) ) {

			$product_price = $product->get_price();
			$product_id    = $product->get_id();
			$extra_price   = 0;

			$product_extra_groups = pewc_get_extra_fields( $product_id );

			if ( $product_extra_groups ) {

				foreach ( $product_extra_groups as $group ) {

					if ( isset( $group['items'] ) ) {

						foreach ( $group['items'] as $item ) {

							$show_option_prices_in_cart = pewc_show_option_prices_in_cart( $item );


							$group_id   = $item['group_id'];
							$field_id   = $item['field_id'];
							$field_type = $item['field_type'];

							if ( isset( $item['field_type'] ) && $item['field_type'] != 'upload' && $item['field_type'] != 'products' ) {

								$id    = $item['id'];
								$price = 0;
								$value = isset( $wl_product['meta'][ $id ] ) ? $wl_product['meta'][ $id ] : '';


								// If an extra is flat rate, it's not charged per product
								// It's a one-off fee that's added separately in the cart
								$is_flat_rate = ! empty( $item['field_flatrate'] ) ? true : false;

								$is_percentage = ! empty( $item['field_percentage'] ) ? true : false;

								// Only add item if it's visible
								if ( ! empty( $wl_product['meta'][ $id ] ) ) {

									$field_price = pewc_get_field_price( $item, $product );

									// Add the value of the field (not including the value of options)
									if ( ! $is_flat_rate ) {
										$price = floatval( $field_price );
									}

									// Check for Name Your Price
									if ( $field_type == 'name_price' ) {
										if ( ! $is_flat_rate ) {
											$price = $value;
										}
									}

									// Check for Calculation fields
									if ( $field_type == 'calculation' ) {

										if ( isset( $item['formula_action'] ) && $item['formula_action'] == 'cost' ) {

											if ( ! $is_flat_rate ) {
												$price = $value;
											}

										}

									}

									// Calculate price for per character fields
									if ( ! empty( $item['per_character'] ) && ( $field_type == 'text' || $field_type == 'textarea' ) ) {
										$remove_line_breaks = preg_replace( "/\r|\n/", "", $value );
										$str_length         = mb_strlen( str_replace( ' ', '', $remove_line_breaks ) );
										if ( ! empty( $item['field_freechars'] ) ) {
											$str_length -= absint( $item['field_freechars'] );
											$str_length = max( 0, $str_length );
										}
										if ( ! $is_flat_rate ) {
											$price = $str_length * $price;
										}
									}

									// Calculate price for multiply fields
									if ( ! empty( $item['multiply'] ) && ( $field_type == 'number' || $field_type == 'name_price' ) ) {
										if ( ! $is_flat_rate ) {
											$price = $value * $price;
										}
									}

									// Calculate price for percentage fields
									if ( $is_percentage && $field_type != 'calculation' ) {
										if ( ! $is_flat_rate ) {
											$price = pewc_calculate_percentage_price( $field_price, $product );
											// $price = $value * $price;
										}
									}

									// Filtered by Bookings to include per unit cost for extras
									$price = apply_filters( 'pewc_filter_cart_item_data_price', $price, array(), $item, $group_id, $field_id );

									// Find any additional cost for options and select fields
									if ( ! empty( $item['field_options'] ) ) {

										// Record checkbox group values differently
										$checkbox_group_values = array();
										// Radio buttons are arrays, select are simple values
										if ( $field_type == 'radio' || ( $field_type == 'image_swatch' && empty( $item['allow_multiple'] ) ) ) {
											$option_value = $value[0];
										} else {
											$option_value = $value;
										}


										foreach ( $item['field_options'] as $option ) {

											// If it's a checkbox group, we need to total all selected options
											if ( $field_type == 'checkbox_group' || ( $field_type == 'image_swatch' && ! empty( $item['allow_multiple'] ) ) ) {

												if ( ! empty( $option['price'] ) && in_array( $option['value'], $option_value ) ) {
													$option_price = $option['price'];
													if ( $is_percentage ) {
														$option_price = pewc_calculate_percentage_price( $option_price, $product );
													}

													if ( ! $is_flat_rate ) {
														$price                   += floatval( $option_price );
														$option_price            = pewc_maybe_include_tax( $product, $option_price );
														$checkbox_group_values[] = $show_option_prices_in_cart === true ? $option['value'] . ' (' . wc_price( $option_price ) . ')' : $option['value'];
													}
												}

											} else if ( ! empty( $option['price'] ) && $option['value'] == $option_value ) {
												$option_price = $option['price'];
												if ( $is_percentage ) {
													$option_price = pewc_calculate_percentage_price( $option_price, $product );
												}

												if ( ! $is_flat_rate ) {
													$price += floatval( $option_price );
													break;
												}
											}

										}


									}

									// Filter the price of the product extra
									$price = apply_filters( 'pewc_add_cart_item_data_price', $price, $item, $product_id );

									$extra_price += floatval( $price );
								}
							}
						}

						// Ensure price can't be less than 0
						$new_price = floatval( $product_price ) + floatval( $extra_price );
						if ( $new_price < 0 ) {
							$new_price = 0;
						}

						return wc_price( $new_price );
					}
				}
			}
		}

		return $price;
	}

	add_filter( 'tinvwl_wishlist_item_price', 'tinvwl_item_price_pewc', 10, 3 );
} // End if().
