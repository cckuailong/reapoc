<?php
/*
 * all WooCommerce Hooks will be called here
 *
 *
 */

if( ! defined('ABSPATH') ) die('Not Allowed.');

function ppom_woocommerce_show_fields() {
    
    global $product;
    
    $product_id = ppom_get_product_id( $product ); 
    
	ppom_woocommerce_show_fields_on_product($product_id);
}


// for shortcode/direct access purpose
function ppom_woocommerce_show_fields_on_product($product_id, $args=null) {
	
	$product = wc_get_product($product_id);
	
    $product_id = ppom_get_product_id( $product ); 
	$ppom		= new PPOM_Meta( $product_id );

	if( ! $ppom->fields ) return '';
	 
	if( ! $ppom->has_unique_datanames() ) {
		
		printf(__("<div class='error'>Some of your fields has duplicated datanames, please fix it</div>"), "ppom");
		return;
	}
	
    
    $ppom_box_id = is_array($ppom->meta_id) ? implode('-',$ppom->meta_id) : $ppom->meta_id;
    $ppom_html = '<div id="ppom-box-'.esc_attr($ppom_box_id).'" class="ppom-wrapper">';
    
    if( ppom_get_price_table_location() === 'before' ) {
		$ppom_html .= '<div id="ppom-price-container"></div>';
    }
    
    $template_vars = array('ppom_settings'  	=> $ppom->ppom_settings,
    						'product'			=> $product,
    						'ppom_fields_meta'	=> $ppom->fields,
    						'ppom_id'			=> $ppom->meta_id,
    						'args'				=> $args);
    ob_start();
    ppom_load_template ( 'render-fields.php', $template_vars );
    $ppom_html .= ob_get_clean();
    
    if( ppom_get_price_table_location() === 'after' ) {
		$ppom_html .= '<div id="ppom-price-container"></div>';
    }
	
	// Clear fix
	$ppom_html .= '<div style="clear:both"></div>';   // Clear fix
	$ppom_html .= '</div>';   // Ends ppom-wrappper
	
	echo apply_filters('ppom_fields_html', $ppom_html, $product);
}

// Template Base Callback function
function ppom_woocommerce_inputs_template_base() {
    
    global $product;
    
    $product_id = ppom_get_product_id( $product ); 
    
    $args = apply_filters('ppom_rendering_template_args', ['enable_add_to_cart_id' => false], $product);
    
	ppom_woocommerce_template_base_inputs_rendering($product_id, $args);
}

function ppom_woocommerce_template_base_inputs_rendering($product_id, $args=null) {
	
	$product = wc_get_product($product_id);

	// @TODO: have to re-check abou args param for the Form class
	$form_obj = new PPOM_Form( $product, $args );
	
	
	// Check if PPOM fields is empty
	if( ! $form_obj->has_ppom_fields() ) return '';
	 
	$ppom_html = '';
	$template_vars = ['form_obj' => $form_obj];
    
    ob_start();
    	ppom_load_input_templates( 'frontend/ppom-fields.php', $template_vars );
    $ppom_html .= ob_get_clean();
	
	echo apply_filters('ppom_fields_html', $ppom_html, $product);
}

function ppom_woocommerce_load_scripts() {
	
	if( ! is_product() ) return '';
	
	global $post;
	$product = wc_get_product($post->ID);
	
	$ppom		= new PPOM_Meta( $product->get_id() );
	

	if( ! $ppom->fields ) return '';
	
	// Loading all required scripts/css for inputs like datepicker, fileupload etc
    ppom_hooks_load_input_scripts( $product );
    
    do_action('ppom_after_scripts_loaded', $ppom, $product);
}


function ppom_woocommerce_validate_product($passed, $product_id, $qty) {
    
  	$ppom		= new PPOM_Meta( $product_id );
  	if( ! $ppom->ajax_validation_enabled ) {
		$passed = ppom_check_validation($product_id, $_POST);
	}
	
	if( ppom_get_price_mode() == 'legacy' && isset($_POST['ppom']['fields']) ) {
		
		if( ppom_is_price_attached_with_fields($_POST['ppom']['fields']) &&
    		empty($_POST['ppom']['ppom_option_price'])
    	 ) {
    		$error_message = __('Sorry, an error has occurred. Please enable JavaScript or contact site owner.',"ppom");
			ppom_wc_add_notice( $error_message );
			$passed = false;
			return $passed;
    	}
    }
    
    return $passed;
}

function ppom_woocommerce_ajax_validate() {
	
	// ppom_pa($_POST); exit;
	$ppom_nonce = $_REQUEST['ppom_nonce'];
	$validate_nonce_action = "ppom_validating_action";
	if ( ! wp_verify_nonce( $ppom_nonce, $validate_nonce_action ) ) {
		
		$message = sprintf(__('<div class="woocommerce-error" role="alert">%s</div>', "ppom"), 'Error while validating, try again');
		$response = array('status'=>'error', 'message' => $message);
    	wp_send_json( $response );
	}
	
	$errors_found = array();
	
	$product_id = intval($_POST['ppom_product_id']);
	$passed =  ppom_check_validation($product_id, $_POST);
	
	$all_notices = wc_get_notices();
	wc_clear_notices();
	
	$response = array();
	if( ! $passed ) {
		ob_start();
		foreach($all_notices as $type => $message) {
			
			if( $type != 'error' ) continue;
			wc_get_template( "notices/{$type}.php", array(
				'messages' => $message )
			);
		}
		
		$all_notices = wc_kses_notice( ob_get_clean() );
		$response = array('status'=>'error', 'message'=>$all_notices);
	} else {
		$response = array('status'=>'success');
	}
	// $all_notices = '<div class="">'.$all_notices.'</div>';
	// ppom_pa($all_notices);
	
	wp_send_json( $response );
}

function ppom_check_validation($product_id, $post_data, $passed=true) {
	
	$ppom		= new PPOM_Meta( $product_id );
	
	if( ! $ppom->fields ) return $passed;
	
	
	$ppom_posted_fields = isset($post_data['ppom']['fields']) ? $post_data['ppom']['fields'] : null;
	if( ! $ppom_posted_fields ) return $passed;
	
	foreach($ppom->fields as $field) {
		
		// ppom_pa($field);
		
		// Check field Visibility settings
		if( ! ppom_is_field_visible($field) ) continue;
		
		$passed = apply_filters('ppom_before_fields_validation', $passed, $field, $post_data, $product_id);
		
		if( empty($field['data_name']) || empty($field['required']) 
		&& (empty($field['min_checked']) && empty($field['max_checked']) )
		) continue;
		
		$data_name	= sanitize_key($field['data_name']);
		$title		= isset($field['title']) ? $field['title'] : '';
		$type		= isset($field['type']) ? $field['type'] : '';
		
		
		// Check if field is required by hidden by condition
		if( ppom_is_field_hidden_by_condition($data_name) ) continue;
		
		if( ! ppom_has_posted_field_value($ppom_posted_fields, $field) ) {
			
			// Note: Checkbox is being validate by hook: ppom_has_posted_field_value
			// $error_message = isset($field['error_message']) ? $field['error_message'] : '';
			// $error_message = (isset($field['error_message']) && $field['error_message'] != '') ? $title.": ".$field['error_message'] : "{$title} is a required field";
			$error_message = (isset($field['error_message']) && $field['error_message'] != '') ? sprintf(__("%s: %s", 'ppom'), $title, $field['error_message']) : "{$title} ".__("is a required field", 'ppom');
			$error_message = sprintf ( __ ( '%s', 'ppom' ), $error_message );
			$error_message = stripslashes ($error_message);
			ppom_wc_add_notice( $error_message );
			$passed = false;
		}
		
	}
	
	// ppom_pa($post_data); exit;
	
	return $passed;
}


function ppom_woocommerce_add_cart_item_data($cart, $product_id) {
	
	if( ! isset($_POST['ppom']) ) return $cart;
	
	$ppom		= new PPOM_Meta( $product_id );
	if( ! $ppom->ppom_settings ) return $cart;
	
	// ADDED WC BUNDLES COMPATIBILITY
	if ( function_exists('wc_pb_is_bundled_cart_item') && wc_pb_is_bundled_cart_item( $cart )) {
		return $cart;
	}
	
	// PPOM also saving cropped images under this filter.
	$ppom_posted_fields = apply_filters('ppom_add_cart_item_data', $_POST['ppom'], $_POST);
	$cart['ppom'] = $ppom_posted_fields;
	
	// ppom_pa($_POST);
	// exit;
	return $cart;
}

function ppom_woocommerce_update_cart_fees($cart_items, $values) {
	
	if( empty($cart_items) ) return $cart_items;

	if( ! isset( $values['ppom']['ppom_option_price'] ) ) return $cart_items;
	
	$wc_product = $cart_items['data'];
	$product_id = ppom_get_product_id($wc_product);
	
	$ppom_meta_ids = '';	
	// removing id field
	if ( !empty( $values ['ppom'] ['fields']['id'] )) {
		$ppom_meta_ids = $values ['ppom'] ['fields']['id'];
		unset( $values ['ppom'] ['fields']['id']);
	}
	
	// converting back to org price if Currency Switcher is used
	$ppom_item_org_price	= ppom_hooks_convert_price_back($wc_product->get_price());
	// $ppom_item_org_price	= $wc_product->get_price();
	
	$ppom_item_order_qty	= floatval($cart_items['quantity']);
	
	// Getting option price
	$option_prices = json_decode( stripslashes($values['ppom']['ppom_option_price']), true);
	// ppom_pa($option_prices);
	$total_option_price = 0;
	$ppom_matrix_price = 0;
	
	$ppom_quantities_price = 0;
	$ppom_quantities_usebaseprice = false;
	$ppom_quantities_include_base = false;
	
	$ppom_total_quantities = 0;
	$ppom_total_discount = 0;
	$ppon_onetime_cost = 0;
	$ppomm_measures = 1;	// meassure need to be multiple with each so it will be 1
	
	
	// If quantities field found then we need to get total quantity to get correct matrix price
	// if matrix is also used
	
	if($option_prices) {
		foreach($option_prices as $option){
			if( $option['apply'] == 'quantities' ) {
				$ppom_total_quantities += $option['quantity'];
				$ppom_item_order_qty = $ppom_total_quantities;
			}

		}
	}
	

	// Check if price is set by matrix
	$matrix_found = ppom_get_price_matrix_chunk($wc_product, $option_prices, $ppom_item_order_qty);
	// ppom_pa($matrix_found);
	
	// Calculating option prices
	if($option_prices) {
		foreach($option_prices as $option){
			
			// Do not add if option is fixed/onetime
			// if( $option['apply'] != 'variable' ) continue;
			
			// ppom_get_field_option_price
			
			switch ($option['apply']) {

				case 'variable':
					
					$option_price = $option['price'];
					// verify prices from server due to security
					if( isset($option['data_name']) && isset($option['option_id'])) {
						
						$option_price = ppom_get_field_option_price_by_id($option, $wc_product, $ppom_meta_ids);
					}
					
					$total_option_price += wc_format_decimal( $option_price, wc_get_price_decimals());
					break;
				
				case 'onetime':
					
					$option_price = $option['price'];
					// verify prices from server due to security
					if( isset($option['data_name']) && isset($option['option_id'])) {
						
						$option_price = ppom_get_field_option_price_by_id($option, $wc_product, $ppom_meta_ids);
					}
					$ppon_onetime_cost += wc_format_decimal( $option_price, wc_get_price_decimals());
					break;
				
				case 'quantities':
		
					$ppom_quantities_use_option_price = apply_filters('ppom_quantities_use_option_price', true, $option_prices);
					if( $ppom_quantities_use_option_price ) { 
						
						$quantity_price = $option['price'];
						
						// If matrix found now product org price will be set to matrix
						if( !empty($matrix_found) && !isset($matrix_found['discount']) ) {
							
							$quantity_price = $matrix_found['price'];
							
						}
						
						$ppom_quantities_price += wc_format_decimal(( $quantity_price * $option['quantity'] ), wc_get_price_decimals());
						// $ppom_total_quantities += $option['quantity'];
					}
					
					if( !empty($option['include']) && $option['include'] == 'on') {
						$ppom_quantities_include_base = true;
					}
					break;
					
				case 'bulkquantity':
					
					
					// Note: May need to add matrix price like in quantites (above)
					
					$ppom_quantities_price += wc_format_decimal(($option['price'] * $option['quantity']), wc_get_price_decimals());
					$ppom_quantities_price += isset($option['base']) ? $option['base'] : 0;
					
					if(isset($option['usebase_price']) && $option['usebase_price'] == 'yes') {
						$ppom_quantities_usebaseprice = true;
					}
					break;
					
				// Fixed price addon
				case 'fixedprice':
					
					$ppom_item_org_price = $option['unitprice'];
					
					// Well, it should NOT be like this but have to do this. will see later.
					$ppom_item_order_qty = 1;
					break;
					
				case 'measure':
					
					$measer_qty = isset($option['qty']) ? $option['qty'] : 0;
					$option_price = $option['price'];
					
					$ppomm_measures			*= $measer_qty;
					
					
					break;
					
			}
			
			
			/**
			 * @since 15.4: Updating options weight
			 **/
			if( ppom_pro_is_installed() ) {
				$option_weight = ppom_get_field_option_weight_by_id($option, $ppom_meta_ids);
				if( $option_weight > 0 ) {
					$new_weight = $wc_product->get_weight() + $option_weight;
					$wc_product->set_weight($new_weight);
				}
			}
			
		}
	}
	
	
	// ppom_pa($matrix_found);
	if( !empty($matrix_found) ) {
		
		// Check that it's not a discount matrix
		if( ! isset($matrix_found['discount']) ) {
			$ppom_item_org_price = $matrix_found['price'];
		} else {
			
			// Discount matrix found
			if( !empty($matrix_found['percent']) ) {
						
				$total_with_options	= $ppom_item_org_price + $total_option_price + $ppon_onetime_cost;
				
				// Check wheather to apply on Both (Base+Options) or only Base
				if( $matrix_found['discount'] == 'both' ) {
					
					// Also adding quantities price if used
					$total_price_to_be_discount = $total_with_options+$ppom_quantities_price;
					
					$price_after_precent = ppom_get_amount_after_percentage($total_price_to_be_discount, $matrix_found['percent']);
				} elseif( $matrix_found['discount'] == 'base' ) {
					
					$total_price_to_be_discount = $ppom_item_org_price+$ppom_quantities_price;
					$price_after_precent = ppom_get_amount_after_percentage($total_price_to_be_discount, $matrix_found['percent']);
				}
				
				$ppom_total_discount += $price_after_precent;
			} else {
				/**
				 * when discount is in PRICE not Percent then applied to whole price Base+Option)
				 * so need to get per unit discount
				 **/
				 
				 /*
				 ** @since 16.8
				 ** When each variation has own quantity, then cart quantity is disabled only one price is set
				 ** not indivisual
				 **/
				 if( ! $ppom_quantities_usebaseprice ) {
				 	
				 	$ppom_total_discount += $matrix_found['price'];
				 } else {
					$discount_per_unit = $matrix_found['price'] / $ppom_item_order_qty;
					$ppom_total_discount += $discount_per_unit;
				 }
			}
		}
	}
	
	
	if( $ppom_quantities_price > 0 ) {
		
		if( ! $ppom_quantities_include_base ) {
			// $ppom_item_org_price = ($ppom_item_org_price * $ppom_total_quantities);
			$ppom_item_org_price = 0;
			
			// when base price is NOT included the quantity is updated so it must be multiplied by options
			$total_option_price = ($total_option_price * $ppom_total_quantities);
		}
	}
	
	// If measures found, Multiply it with options
	if( $ppomm_measures > 0 ) {
		// $total_option_price = $total_option_price * $ppomm_measures;
		$ppom_item_org_price = $ppom_item_org_price * $ppomm_measures;
	}
	
	
	// var_dump($ppom_total_discount);
	// var_dump($ppom_item_org_price);
	// var_dump($total_option_price);
	// var_dump($ppom_quantities_price);
	
	
	$cart_line_total = ($ppom_item_org_price + $total_option_price + $ppom_quantities_price - $ppom_total_discount);
	
	$cart_line_total	= apply_filters('ppom_cart_line_total', $cart_line_total, $cart_items, $values);
	
	$wc_product -> set_price($cart_line_total);
	
	return $cart_items;
}

function ppom_calculate_totals_from_session( $cart ) {
	$cart->calculate_totals();
}


function ppom_woocommerce_add_fixed_fee( $cart ) {
	
	$fee_no = 1;
	foreach( $cart->get_cart() as $item ){
	
		if( empty($item['ppom']['ppom_option_price']) ) continue;
		
		// Getting option price
		$option_prices = json_decode( stripslashes($item['ppom']['ppom_option_price']), true);
		
		if( $option_prices ) {
			foreach( $option_prices as $fee ) {
				
				if( $fee['apply'] != 'onetime' ) continue;
				
				
				$label = $fee_no.'-'.$fee['product_title'].': '.$fee['label'];
				$label = apply_filters('ppom_fixed_fee_label', $label, $fee, $item);
				
				$taxable = (isset($fee['taxable']) && $fee['taxable'] == 'on') ? true : false;
				$fee_price = $fee['price'];
				
				if( !empty($fee['without_tax']) ) {
					$fee_price = $fee['without_tax'];
				}
				
				// if(  'incl' === get_option( 'woocommerce_tax_display_shop' ) ) {
				// 	$taxable = false;
				// }
				
				$fee_price	= apply_filters('ppom_cart_fixed_fee', $fee_price, $fee, $cart);
				
				if( $fee_price != 0 ) {
					$cart -> add_fee( sprintf(__( "%s", "ppom"), esc_html($label)), $fee_price, $taxable );
					$fee_no++;
				}
			} 
		}
	}
}

// Show fixed fee in mini cart
function ppom_woocommerce_mini_cart_fixed_fee() {
	
	if( ! WC()->cart->get_fees() ) return '';
	
	$fixed_fee_html = '<table>';
	foreach ( WC()->cart->get_fees() as $fee ) {
		
		$item_fee = $fee->amount;
		if(  WC()->cart->display_prices_including_tax() && $fee->taxable ) {
			
			$item_fee = $fee->total + $fee->tax;
		}
		// var_dump($fee);
		$fixed_fee_html .= '<tr>';
			$fixed_fee_html .= '<td class="subtotal-text">'. esc_html( $fee->name );'</td>';
			$fixed_fee_html .= '<td class="subtotal-price">'. wc_price( $item_fee ).'</td>';
		$fixed_fee_html .= '</tr>';
	}
	
	$fixed_fee_html .= '<tr><td colspan="2">'.__("Total will be calculated in the cart", "ppom").'</td></tr>';
	$fixed_fee_html .= '</table>';
	
	echo apply_filters('ppom_mini_cart_fixed_fee', $fixed_fee_html);
}

function ppom_woocommerce_add_item_meta($item_meta, $cart_item) {

	// ppom_pa($item_meta);
	if( ! isset($cart_item['ppom']['fields']) ) return $item_meta;
	
	
	// ADDED WC BUNDLES COMPATIBILITY
	if ( function_exists('wc_pb_is_bundled_cart_item') && wc_pb_is_bundled_cart_item( $cart_item )) {
		return $item_meta;
	}
	
	$ppom_meta = ppom_make_meta_data( $cart_item );
	
	foreach( $ppom_meta as $key => $meta ) {
		
		$hidden 	= isset($meta['hidden']) ? $meta['hidden'] : false;
		$meta_name	= isset($meta['name']) ? $meta['name'] : '';
		$meta_value = isset($meta['value']) ? $meta['value'] : '';
		$display	= isset($meta['display']) ? $meta['display'] : $meta_value;
		if( $key == 'ppom_has_quantities' ) $hidden = true;
		

		// If no value		
		if( ! $display ) continue;
		
		if( !empty( $meta_name ) ) {
	
			if( apply_filters('ppom_show_option_price_cart', false) && isset($meta['price']) ) {
				$meta_value .=' ('.wc_price($meta['price']).')';
			}
			
			$meta_key = stripslashes($meta_name);
			
			// WPML
			$meta_key = ppom_wpml_translate($meta_key, 'PPOM');
			
			$item_meta[] = array('name'	=> wp_strip_all_tags($meta_key), 'value' => $meta_value, 'hidden' => $hidden, 'display'=>$display);
		} else {
			$item_meta[] = array('name'	=> ($key), 'value' => $meta, 'hidden' => $hidden, 'display'=>$display);
		}
		
	}
	
	return $item_meta;
}

// alter price on shop page if price matrix found
function ppom_woocommerce_alter_price($price, $product) {
	
	$product_id = ppom_get_product_id($product);
	
	if (class_exists('sitepress')){
		$default_lang = apply_filters('wpml_default_language', NULL);
		$product = wc_get_product( apply_filters('wpml_object_id', $product->get_id(), 'product', true, $default_lang) );
	}
	
	$price_matrix_found = ppom_has_field_by_type( $product_id, 'pricematrix' );
	if( empty($price_matrix_found) && apply_filters('ppom_hide_product_price_if_zero', true, $product) ) {
		if( $product->get_price() <= 0 ) return '';
	}
	
	if( empty($price_matrix_found) ) return $price;
	
	$from_pice = '';
	$to_price = '';
	
	if (!in_array($product->get_type(), array('variable', 'grouped', 'external'))) {
			
		$price_range = array();
		
		foreach($price_matrix_found as $meta){
			
			// ppom_pa($meta);
			
			if( ! ppom_is_field_visible( $meta ) ) continue;
			
			if($meta['type'] == 'pricematrix'){
				
				$options = $meta['options'];
				$ranges	 = ppom_convert_options_to_key_val($options, $meta, $product);
				// ppom_pa($ranges);	
				
				if( isset($meta['discount']) && $meta['discount'] == 'on' ) {
					
					$last_discount	= end($ranges);
					$least_price	= $last_discount['price'];
					
					if( !empty($last_discount['percent']) ) {
						$max_discount	= $last_discount['percent'];
						$least_price	= ppom_get_amount_after_percentage($product->get_price(), $max_discount);
					}
					
					$least_price	= $product->get_price() - $least_price;
					$least_price	= wc_format_decimal( $least_price, wc_get_price_decimals());
					// var_dump($least_price);
					$price = wc_price($least_price).'-'.$price;
				} else {
					
					foreach($ranges as $range){
						$price_range[] = $range['price'];
					}
					
					if( !empty($price_range) ){
					
						$from_pice = min($price_range);
						$to_price  = max($price_range);
						$price = wc_format_price_range($from_pice, $to_price);
					}
				}
			}
		}
		
	}
	
	return apply_filters('ppom_loop_matrix_price', $price, $from_pice, $to_price);
}

/*function ppom_hide_variation_price_html($show, $parent, $variation) {
	
	$product_id = $parent->get_id();
	$ppom		= new PPOM_Meta( $product_id );
	
	if( $ppom->is_exists && $ppom->price_display != 'hide' ) {
		$show = false;
	}
	
	return $show;
	
}*/

// Set default quantity for price matrix
function ppom_woocommerce_product_default_quantity( $args, $product ) {
	
	if( ! is_product() ) return $args;
	
	$product_id = ppom_get_product_id($product);
	$ppom		= new PPOM_Meta( $product_id );
	if( ! $ppom->is_exists ) return $args;
	
	$ppom_matrix_found = ppom_has_field_by_type( $product_id, 'pricematrix' );
	
	if($ppom_matrix_found){
		
		$price_matrix = reset($ppom_matrix_found);
		// If it is Discount Matrix, do not set min quantity
		// if( isset($meta['discount']) && $meta['discount'] == 'on' ) continue;
		$options		= $price_matrix['options'];
		$ranges			= ppom_convert_options_to_key_val($options, $price_matrix, $product);
		if( !empty($ranges) ) {
			$first_range	= reset($ranges);
			$qty_ranges 	= explode('-', $first_range['raw']);
			$args['input_value']	= $qty_ranges[0];
		}
	}
	
	return $args;
}

// Set min quantity for price matrix
function ppom_woocommerce_set_min_quantity( $min_quantity, $product ) {
	
	$product_id = ppom_get_product_id($product);
	$ppom		= new PPOM_Meta( $product_id );
	if( ! $ppom->is_exists ) return $min_quantity;
	
	$ppom_matrix_found = ppom_has_field_by_type( $product_id, 'pricematrix' );
	if($ppom_matrix_found){
		foreach($ppom_matrix_found as $meta){
			
			// If it is Discount Matrix, do not set min quantity
			// if( isset($meta['discount']) && $meta['discount'] == 'on' ) continue;
			$options		= $meta['options'];
			$ranges			= ppom_convert_options_to_key_val($options, $meta, $product);
			
			if( empty($ranges) ) continue;
			
			$first_range	= reset($ranges);
			$qty_ranges 	= explode('-', $first_range['raw']);
			$min_quantity	= $qty_ranges[0];
		}
	}
	
	return $min_quantity;
}
// Set max quantity for price matrix
function ppom_woocommerce_set_max_quantity( $max_quantity, $product ) {
	
	$product_id = ppom_get_product_id($product);
	$ppom		= new PPOM_Meta( $product_id );
	if( ! $ppom->is_exists ) return $max_quantity;
	
	$last_range = array();
	
	$ppom_matrix_found = ppom_has_field_by_type( $product_id, 'pricematrix' );
	
	if($ppom_matrix_found){
		foreach($ppom_matrix_found as $meta){
			
			// If it is Discount Matrix, do not set max quantity
			if( isset($meta['discount']) && $meta['discount'] == 'on' ) continue;
			
			$options = $meta['options'];
			// ppom_pa($options);
			$ranges	 = ppom_convert_options_to_key_val($options, $meta, $product);
			
			if( empty($ranges) ) continue;
			
			$last_range = end($ranges);
			$qty_ranges = explode('-', $last_range['raw']);
			$max_quantity	= $qty_ranges[1];
		}
	}
	
	return $max_quantity;
}

// Set quantity step for price matrix
function ppom_woocommerce_set_quantity_step( $quantity_step, $product ) {
	
	$product_id = ppom_get_product_id($product);
	$ppom		= new PPOM_Meta( $product_id );
	if( ! $ppom->is_exists ) return $quantity_step;
		
	$last_range = array();
	
	$ppom_matrix_found = ppom_has_field_by_type( $product_id, 'pricematrix' );
	if($ppom_matrix_found){
		foreach($ppom_matrix_found as $meta){
			
			$quantity_step = empty($meta['qty_step']) ? 1 : $meta['qty_step'];
		}
	}
	
	return $quantity_step;
}

// When quantities is used then reset quantity to 1
function ppom_woocommerce_add_to_cart_quantity( $quantity, $product_id ) {
	
	if( ppom_reset_cart_quantity_to_one( $product_id ) ) {
		$quantity = 1;
	}
	
	return $quantity;
}

// It is change cart quantity label
function ppom_woocommerce_control_cart_quantity_legacy($quantity, $cart_item_key) {
	
	$cart_item = WC()->cart->get_cart_item( $cart_item_key );
	
	// ppom_pa($cart_item)
	if( !isset($cart_item['ppom']['ppom_option_price']) &&
		!isset($cart_item['ppom']['ppom_pricematrix']) ) return $quantity;
	
	// Getting option price
	$option_prices = json_decode( stripslashes($cart_item['ppom']['ppom_option_price']), true);
	$ppom_has_quantities = 0;
	// ppom_pa($option_prices);
	
	if( empty($option_prices) ) return $quantity;
	
	foreach($option_prices as $option) {
		
		if( isset($option['include']) && $option['include'] == '') {
			if( isset($option['quantity']) ) {
				$ppom_has_quantities += intval( $option['quantity'] );
			}
		} elseif(isset($option['include']) && $option['include'] == 'on') {
			$ppom_has_quantities = 1;
		}
	}
	
	// var_dump($ppom_has_quantities);
	// If no quantity updated then return default
	$ppom_quantitiles_allow_update_cart = apply_filters('ppom_quantities_allow_cart_update', false, $option_prices);
	if( $ppom_has_quantities != 0 && !$ppom_quantitiles_allow_update_cart) {
		$quantity = '<span class="ppom-cart-quantity">'.$ppom_has_quantities.'</span>';
	}
	
	return $quantity;
}

function ppom_woocommerce_control_cart_quantity($quantity, $cart_item_key) {
	
	$cart_item = WC()->cart->get_cart_item( $cart_item_key );
	
	if( !isset($cart_item['ppom']['fields']) ) return $quantity;
	
	$ppom_fields_post   = $cart_item['ppom']['fields'];
	$product_id			= $cart_item['product_id'];
	
	if( ppom_is_cart_quantity_updatable( $product_id ) ) return $quantity;

	$ppom_has_quantities = ppom_price_get_total_quantities($ppom_fields_post, $product_id);
	
	// var_dump(!$ppom_quantitiles_allow_update_cart);
	// If no quantity updated then return default
	$ppom_quantitiles_allow_update_cart = apply_filters('ppom_quantities_allow_cart_update', false, $ppom_fields_post);
	if( $ppom_has_quantities != 0 && !$ppom_quantitiles_allow_update_cart) {
		$quantity = '<span class="ppom-cart-quantity">'.$ppom_has_quantities.'</span>';
	}
	
	return $quantity;
}

// Control subtotal when quantities input used
/*function ppom_woocommerce_item_subtotal( $item_subtotal, $cart_item, $cart_item_key) {
	
	if( !isset($cart_item['ppom']['ppom_option_price']) ) return $item_subtotal;
	
	// Getting option price
	$option_prices = json_decode( stripslashes($cart_item['ppom']['ppom_option_price']), true);
	if( empty($option_prices) ) return $item_subtotal;
	
	$ppom_has_quantities = 0;
	foreach($option_prices as $option) {
		
		if( isset($option['quantity']) ) {
			$ppom_has_quantities += intval( $option['quantity'] );
		}
	}
	
	// If no quantity updated then return default
	if( $ppom_has_quantities == 0 ) return $item_subtotal;
	
	$_product = $cart_item['data'];
	$item_quantity = 1;
	return WC()->cart->get_product_subtotal( $_product,  $item_quantity);
	
}*/

function ppom_woocommerce_control_checkout_quantity($quantity, $cart_item, $cart_item_key) {
	
	// ppom_pa($cart_item);
	if( !isset($cart_item['ppom']['fields']) ) return $quantity;
	
	$ppom_fields_post   = $cart_item['ppom']['fields'];
	$product_id			= $cart_item['product_id'];
	
	if( ppom_is_cart_quantity_updatable( $product_id ) ) return $quantity;

	$ppom_has_quantities = ppom_price_get_total_quantities($ppom_fields_post, $product_id);
	
	// If no quantity updated then return default
	if( $ppom_has_quantities > 0 ) {
		$quantity = '<strong class="product-quantity">' . sprintf( "&times; %s", $ppom_has_quantities ) . '</strong>';
	}
	
	return $quantity;
}

function ppom_woocommerce_control_oder_item_quantity($quantity, $item) {
	
	$ppom_has_quantities = 0;
	
	$product_id = $item->get_product_id();

	$ppom_fields_post = wc_get_order_item_meta( $item->get_id(), '_ppom_fields');
	if( !isset($ppom_fields_post['fields']) ) return $quantity;
	
	$ppom_fields_post = $ppom_fields_post['fields'];
	
	if( ppom_is_cart_quantity_updatable( $product_id ) ) return $quantity;

	$ppom_has_quantities = ppom_price_get_total_quantities($ppom_fields_post, $product_id);
	
	if( $ppom_has_quantities > 0 ) {
		$quantity = '<strong class="product-quantity">' . sprintf( "&times; %s", $ppom_has_quantities ) . '</strong>';
	}
	
	return $quantity;
}

function ppom_woocommerce_control_email_item_quantity($quantity, $item) {
	
	$ppom_has_quantities = 0;
	
	$product_id = $item->get_product_id();

	$ppom_fields_post = wc_get_order_item_meta( $item->get_id(), '_ppom_fields');
	if( !isset($ppom_fields_post['fields']) ) return $quantity;
	
	$ppom_fields_post = $ppom_fields_post['fields'];
	
	if( ppom_is_cart_quantity_updatable( $product_id ) ) return $quantity;

	$ppom_has_quantities = ppom_price_get_total_quantities($ppom_fields_post, $product_id);
	
	if( $ppom_has_quantities > 0 ) {
		$quantity = '<strong class="product-quantity">' . sprintf( "%s", $ppom_has_quantities ) . '</strong>';
	}
	
	return $quantity;
}

function ppom_woocommerce_control_order_item_quantity($quantity, $item) {
	
	$ppom_has_quantities = 0;
	
	$product_id = $item->get_product_id();

	$ppom_fields_post = wc_get_order_item_meta( $item->get_id(), '_ppom_fields');
	if( !isset($ppom_fields_post['fields']) ) return $quantity;
	
	$ppom_fields_post = $ppom_fields_post['fields'];
	
	if( ppom_is_cart_quantity_updatable( $product_id ) ) return $quantity;

	$ppom_has_quantities = ppom_price_get_total_quantities($ppom_fields_post, $product_id);
	
	if( $ppom_has_quantities > 0 ) {
		$quantity = $ppom_has_quantities;
	}
	
	return $quantity;
}

function ppom_woocommerce_cart_update_validate( $cart_validated, $cart_item_key, $values, $quantity ) {
	
	$max_quantity = ppom_get_cart_item_max_quantity( $values );
	
	if( ! is_null($max_quantity) && $quantity > intval($max_quantity) ) {
		
		$cart_validated = false;
		wc_add_notice( sprintf( __( 'Sorry, maximum quantity is %d.', 'ppom' ), $max_quantity ), 'error' );
	}
	
	return $cart_validated;
}


function ppom_woocommerce_order_item_meta($item, $cart_item_key, $values, $order) {
	
	if ( ! isset ( $values ['ppom']['fields'] )) {
		return;
	}
	// ADDED WC BUNDLES COMPATIBILITY
	if ( function_exists('wc_pb_is_bundled_cart_item') && wc_pb_is_bundled_cart_item( $values )) {
		return;
	}
	
	$ppom_meta = ppom_make_meta_data( $values, 'order' );
	// ppom_pa($item); exit;
	
	foreach( $ppom_meta as $key => $meta ) {
		
		if( !isset($meta['value']) ) continue;
		
		// WPML
		$meta_key = ppom_wpml_translate($key, 'PPOM');
		
		$meta_value = isset($meta['display']) ? $meta['display'] : $meta['value'];
		$item->update_meta_data($key, $meta_value);
	}
	
	// Since 15.2, saving all fields as another meta
	$item->update_meta_data('_ppom_fields', $values ['ppom']);
}

// Changing order item meta key to label
function ppom_woocommerce_order_key( $display_key, $meta, $item ) {
	
	
	if ($item->get_type() != 'line_item') return $display_key;
	
	$field_meta = ppom_get_field_meta_by_dataname( $item->get_product_id(), $display_key );
	if( isset($field_meta['title']) && $field_meta['title'] != '' ) {
		$display_key = stripslashes( $field_meta['title'] );
	}
	
	return $display_key;
}

function ppom_woocommerce_order_value( $display_value, $meta=null, $item=null ) {
	
	if( is_null($item) ) return $display_value;
	
	if ($item->get_type() != 'line_item') return $display_value;
	
	$field_meta = ppom_get_field_meta_by_dataname( $item->get_product_id(), $meta->key );
	
	// if( ! isset($field_meta['type']) ) return $display_value;
	
	$input_type = isset($field_meta['type']) ? $field_meta['type'] : '';
	
	switch( $input_type ) {
		
		case 'file':
		case 'cropper':
			
			/**
			 * File upload and croppers now save only filename in meta
			 * seperated by commas, now here we will build it's html to show thumbs in item orde
			 * @since: 10.10
			 **/
			 $display_value = ppom_generate_html_for_files($meta->value, $input_type, $item);
			 break;
			 
		case 'image':
			$display_value = $meta->value;
			break;
			
		default:
			
			// Important hook: changing order value format using local hooks
			// Also being used for export order lite
			$display_value = apply_filters('ppom_order_display_value', $display_value, $meta, $item);
			break;
	 
	}
	
	return $display_value;
}


// Hiding some ppom meta like ppom_has_quantities
function ppom_woocommerce_hide_order_meta($formatted_meta, $order_item) {
	
	if( empty($formatted_meta) ) return $formatted_meta;
	
	$ppom_meta_searching = $formatted_meta;
	// ppom_has_quantities
	foreach( $ppom_meta_searching as $meta_id => $meta_data ) {
		
		if( $meta_data->key == 'ppom_has_quantities' ) {
			unset( $formatted_meta[$meta_id] );
		}
	}
	
	return $formatted_meta;
}

// When order paid update filename with order number
function ppom_woocommerce_rename_files( $order_id, $posted_data, $order ){
	
	global $woocommerce;

	// getting product id in cart
	$cart = WC()->cart->get_cart();
	
	// ppom_pa($cart); exit;
	
	
	
	// since 8.1, files will be send to email as attachment
	
	//ppom_pa($cart); exit;
	foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item){
		
		// ppom_pa($cart_item); exit;
		if( !isset($cart_item['ppom']['fields']) ) continue;
		
		$product_id = $cart_item['product_id'];
		$all_moved_files = array();
		
		foreach( $cart_item['ppom']['fields'] as $key => $values) {
			
			if( $key == 'id' ) continue;
			
			$field_meta = ppom_get_field_meta_by_dataname( $product_id, $key );
			if( ! $field_meta ) continue;
			
			$field_type = $field_meta['type'];
			$field_label= isset($field_meta['title']) ? $field_meta['title'] : $field_meta['data_name'];
			$moved_files = array();
			
			if( $field_type == 'file' ||  $field_type == 'cropper') {
				
				$base_dir_path 		= ppom_get_dir_path();
				$confirm_dir		= 'confirmed/'.$order_id;
				$confirmed_dir_path = ppom_get_dir_path($confirm_dir);
				$edits_dir_path 	= ppom_get_dir_path('edits');
				
				foreach($values as $file_id => $file_data) {
					
					$file_name		= $file_data['org'];
					$file_cropped	= isset($file_data['cropped']) ? true : false;
					
					$new_filename	= ppom_file_get_name($file_name, $product_id, $cart_item);
					$source_file	= $base_dir_path . $file_name;
					$destination_path	= $confirmed_dir_path . $new_filename;
					
					
					if (file_exists ( $destination_path )) {
						break;
					}
					
					/*$moved_files[] = array('path' => $destination_path,
											'file_name' => $file_name,
											'product_id' => $product_id);*/
																		
					if (file_exists ( $source_file )) {
						
						if (! rename ( $source_file, $destination_path )) {
							die ( 'Error while re-naming order image ' . $source_file );
						}
					}
					
					//renaming edited files
					$source_file_edit = $edits_dir_path . $file_name;
					$destination_path_edit = '';
					
					$file_edited = false;
					if (file_exists ( $source_file_edit )) {
						
						$destination_path_edit = $edits_dir_path . $new_filename;	
						if (! rename ( $source_file_edit, $destination_path_edit )){
							die ( 'Error while re-naming order image ' . $source_file_edit );
						}else{
							$file_edited = true;
						}
					}
					
					$moved_files[] = array(
											'path'		=> $destination_path,
											'file_name' => $file_name,
											'file_label'=> $field_label,
											'file_cropped'=> $file_cropped,
											'file_edited'=>$file_edited,
											'file_edit_path'=>$destination_path_edit,
											'product_id'=> $product_id,
											'field_name'	=> $key);
							
					// $moved_files['file_edited'] = $file_edited;
				}
				
				$all_moved_files[$key] = $moved_files;
			}
		}
		
		do_action('ppom_after_files_moved', $all_moved_files, $order_id, $order);
	}
}