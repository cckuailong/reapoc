<?php

/* * ************************************************
 * Stripe Buy Now button shortcode handler
 * *********************************************** */
add_filter( 'swpm_payment_button_shortcode_for_stripe_sca_buy_now', 'swpm_render_stripe_sca_buy_now_button_sc_output', 10, 2 );

function swpm_render_stripe_sca_buy_now_button_sc_output( $button_code, $args ) {

	$button_id = isset( $args['id'] ) ? $args['id'] : '';
	if ( empty( $button_id ) ) {
		return '<p class="swpm-red-box">Error! swpm_render_stripe_sca_buy_now_button_sc_output() function requires the button ID value to be passed to it.</p>';
	}

	//Get class option for button styling, set Stripe's default if none specified
	$class = isset( $args['class'] ) ? $args['class'] : 'stripe-button-el';

	//Check new_window parameter
	$window_target = isset( $args['new_window'] ) ? 'target="_blank"' : '';
	$button_text   = ( isset( $args['button_text'] ) ) ? esc_attr( $args['button_text'] ) : SwpmUtils::_( 'Buy Now' );

	$item_logo = ''; //Can be used to show an item logo or thumbnail in the checkout form.

	$settings   = SwpmSettings::get_instance();
	$button_cpt = get_post( $button_id ); //Retrieve the CPT for this button
	$item_name  = htmlspecialchars( $button_cpt->post_title );

	$membership_level_id = get_post_meta( $button_id, 'membership_level_id', true );
	//Verify that this membership level exists (to prevent user paying for a level that has been deleted)
	if ( ! \SwpmUtils::membership_level_id_exists( $membership_level_id ) ) {
		return '<p class="swpm-red-box">Error! The membership level specified in this button does not exist. You may have deleted this membership level. Edit the button and use the correct membership level.</p>';
	}

	//Payment amount and currency
	$payment_amount = get_post_meta( $button_id, 'payment_amount', true );
	if ( ! is_numeric( $payment_amount ) ) {
		return '<p class="swpm-red-box">Error! The payment amount value of the button must be a numeric number. Example: 49.50 </p>';
	}
	$payment_currency = get_post_meta( $button_id, 'payment_currency', true );
	$payment_amount   = round( $payment_amount, 2 ); //round the amount to 2 decimal place.
	$zero_cents       = unserialize( SIMPLE_WP_MEMBERSHIP_STRIPE_ZERO_CENTS );
	if ( in_array( $payment_currency, $zero_cents ) ) {
		//this is zero-cents currency, amount shouldn't be multiplied by 100
		$price_in_cents = $payment_amount;
	} else {
		$price_in_cents = $payment_amount * 100; //The amount (in cents). This value is passed to Stripe API.
	}
	$payment_amount_formatted = SwpmMiscUtils::format_money( $payment_amount, $payment_currency );

	//$button_image_url = get_post_meta($button_id, 'button_image_url', true);//Stripe doesn't currenty support button image for their standard checkout.
	//User's IP address
	$user_ip                                     = SwpmUtils::get_user_ip_address();
	$_SESSION['swpm_payment_button_interaction'] = $user_ip;

	//Sandbox settings
	$sandbox_enabled = $settings->get_value( 'enable-sandbox-testing' );

	//API keys
	$api_keys = SwpmMiscUtils::get_stripe_api_keys_from_payment_button( $button_id, ! $sandbox_enabled );

	$uniqid = md5( uniqid() );
	$ref_id = 'swpm_' . $uniqid . '|' . $button_id;

	//Return, cancel, notifiy URLs
	$notify_url = SIMPLE_WP_MEMBERSHIP_SITE_HOME_URL . '/?swpm_process_stripe_sca_buy_now=1&ref_id=' . $ref_id; //We are going to use it to do post payment processing.

	$current_url = ( isset( $_SERVER['HTTPS'] ) ? 'https' : 'http' ) . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

	/* === Stripe Buy Now Button Form === */
	$output  = '';
	$output .= '<div class="swpm-button-wrapper swpm-stripe-buy-now-wrapper">';
	$output .= "<form id='swpm-stripe-payment-form-" . $uniqid . "' action='" . $notify_url . "' METHOD='POST'> ";
	$output .= "<div style='display: none !important'>";
	$output .= SwpmMiscUtils::output_stripe_sca_frontend_scripts_once();
	ob_start();
	?>
	<script>
		var stripe = Stripe('<?php echo esc_js( $api_keys['public'] ); ?>');
		jQuery('#swpm-stripe-payment-form-<?php echo esc_js( $uniqid ); ?>').on('submit',function(e) {
			e.preventDefault();
			var btn = jQuery(this).find('button').attr('disabled', true);
			jQuery.post('<?php echo esc_js( admin_url( 'admin-ajax.php' ) ); ?>', {
				'action': 'swpm_stripe_sca_create_checkout_session',
				'swpm_button_id': <?php echo esc_js( $button_id ); ?>,
				'swpm_page_url': '<?php echo esc_js( $current_url ); ?>',
				'swpm_uniqid': '<?php echo esc_js( $uniqid ); ?>'
				}).done(function (response) {
					if (!response.error) {
						stripe.redirectToCheckout({sessionId: response.session_id}).then(function (result) {
					});			
					} else {
						alert(response.error);
						btn.attr('disabled', false);
						return false;
					}
			}).fail(function(e) {
				alert("HTTP error occurred during AJAX request. Error code: "+e.status);
				btn.attr('disabled', false);
				return false;
			});
		});
	</script>
	<?php
	$output .= ob_get_clean();
	$output .= '</div>';

	//apply filter to output additional form fields
	$coupon_input = '';
	$coupon_input = apply_filters( 'swpm_payment_form_additional_fields', $coupon_input, $button_id, $uniqid );
	if ( ! empty( $coupon_input ) ) {
		$output .= $coupon_input;
	}

	$button_image_url = get_post_meta( $button_id, 'button_image_url', true );
	if ( ! empty( $button_image_url ) ) {
		$output .= '<input type="image" src="' . $button_image_url . '" class="' . $class . '" alt="' . $button_text . '" title="' . $button_text . '" />';
	} else {
		$output .= "<button id='{$button_id}' type='submit' class='{$class}'><span>{$button_text}</span></button>";
	}

	//Filter to add additional payment input fields to the form.
	$output .= apply_filters( 'swpm_stripe_payment_form_additional_fields', '' );

	$output .= '</form>';
	$output .= '</div>'; //End .swpm_button_wrapper

	return $output;
}

add_filter( 'swpm_payment_button_shortcode_for_stripe_sca_subscription', 'swpm_render_stripe_sca_subscription_button_sc_output', 10, 2 );

function swpm_render_stripe_sca_subscription_button_sc_output( $button_code, $args ) {

	$button_id = isset( $args['id'] ) ? $args['id'] : '';
	if ( empty( $button_id ) ) {
		return '<p class="swpm-red-box">Error! swpm_render_stripe_sca_buy_now_button_sc_output() function requires the button ID value to be passed to it.</p>';
	}

	//Get class option for button styling, set Stripe's default if none specified
	$class = isset( $args['class'] ) ? $args['class'] : 'stripe-button-el';

	//Check new_window parameter
	$window_target = isset( $args['new_window'] ) ? 'target="_blank"' : '';
	$button_text   = ( isset( $args['button_text'] ) ) ? esc_attr( $args['button_text'] ) : SwpmUtils::_( 'Buy Now' );

	$item_logo = ''; //Can be used to show an item logo or thumbnail in the checkout form.

	$settings   = SwpmSettings::get_instance();
	$button_cpt = get_post( $button_id ); //Retrieve the CPT for this button
	$item_name  = htmlspecialchars( $button_cpt->post_title );

	$membership_level_id = get_post_meta( $button_id, 'membership_level_id', true );
	//Verify that this membership level exists (to prevent user paying for a level that has been deleted)
	if ( ! \SwpmUtils::membership_level_id_exists( $membership_level_id ) ) {
		return '<p class="swpm-red-box">Error! The membership level specified in this button does not exist. You may have deleted this membership level. Edit the button and use the correct membership level.</p>';
	}

	//$button_image_url = get_post_meta($button_id, 'button_image_url', true);//Stripe doesn't currenty support button image for their standard checkout.
	//User's IP address
	$user_ip                                     = SwpmUtils::get_user_ip_address();
	$_SESSION['swpm_payment_button_interaction'] = $user_ip;

	//Custom field data
	$custom_field_value  = 'subsc_ref=' . $membership_level_id;
	$custom_field_value .= '&user_ip=' . $user_ip;
	if ( SwpmMemberUtils::is_member_logged_in() ) {
		$custom_field_value .= '&swpm_id=' . SwpmMemberUtils::get_logged_in_members_id();
	}
	$custom_field_value = apply_filters( 'swpm_custom_field_value_filter', $custom_field_value );

	//Sandbox settings
	$sandbox_enabled = $settings->get_value( 'enable-sandbox-testing' );

	//API keys
	$api_keys = SwpmMiscUtils::get_stripe_api_keys_from_payment_button( $button_id, ! $sandbox_enabled );

	//Billing address
	$billing_address = isset( $args['billing_address'] ) ? '1' : '';
	//By default don't show the billing address in the checkout form.
	//if billing_address parameter is not present in the shortcode, let's check button option
	if ( $billing_address === '' ) {
		$collect_address = get_post_meta( $button_id, 'stripe_collect_address', true );
		if ( $collect_address === '1' ) {
			//Collect Address enabled in button settings
			$billing_address = 1;
		}
	}

	$uniqid = md5( uniqid() );
	$ref_id = 'swpm_' . $uniqid . '|' . $button_id;

	//Return, cancel, notifiy URLs
	$notify_url = SIMPLE_WP_MEMBERSHIP_SITE_HOME_URL . '/?swpm_process_stripe_sca_subscription=1&ref_id=' . $ref_id; //We are going to use it to do post payment processing.

	$current_url = ( isset( $_SERVER['HTTPS'] ) ? 'https' : 'http' ) . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

	$plan_id = get_post_meta( $button_id, 'stripe_plan_id', true );

	/* === Stripe SCA Subscription Button Form === */
	$output  = '';
	$output .= '<div class="swpm-button-wrapper swpm-stripe-buy-now-wrapper">';
	$output .= "<form id='swpm-stripe-payment-form-" . $uniqid . "' action='" . $notify_url . "' METHOD='POST'> ";
	$output .= "<div style='display: none !important'>";
	$output .= SwpmMiscUtils::output_stripe_sca_frontend_scripts_once();
	ob_start();
	?>
	<script>
		var stripe = Stripe('<?php echo esc_js( $api_keys['public'] ); ?>');
		jQuery('#swpm-stripe-payment-form-<?php echo esc_js( $uniqid ); ?>').on('submit',function(e) {
			e.preventDefault();
			var btn = jQuery(this).find('button').attr('disabled', true);
			jQuery.post('<?php echo esc_js( admin_url( 'admin-ajax.php' ) ); ?>', {
				'action': 'swpm_stripe_sca_create_checkout_session',
				'swpm_button_id': <?php echo esc_js( $button_id ); ?>,
				'swpm_page_url': '<?php echo esc_js( $current_url ); ?>',
				'swpm_uniqid': '<?php echo esc_js( $uniqid ); ?>'
				}).done(function (response) {
					if (!response.error) {
						stripe.redirectToCheckout({sessionId: response.session_id}).then(function (result) {
					});			
					} else {
						alert(response.error);
						btn.attr('disabled', false);
						return false;
					}
			}).fail(function(e) {
				alert("HTTP error occurred during AJAX request. Error code: "+e.status);
				btn.attr('disabled', false);
				return false;
			});
		});
	</script>
	<?php
	$output .= ob_get_clean();
	$output .= '</div>';

	//apply filter to output additional form fields
	$coupon_input = '';
	$coupon_input = apply_filters( 'swpm_payment_form_additional_fields', $coupon_input, $button_id, $uniqid );
	if ( ! empty( $coupon_input ) ) {
		$output .= $coupon_input;
	}

	$button_image_url = get_post_meta( $button_id, 'button_image_url', true );
	if ( ! empty( $button_image_url ) ) {
		$output .= '<input type="image" src="' . $button_image_url . '" class="' . $class . '" alt="' . $button_text . '" title="' . $button_text . '" />';
	} else {
		$output .= "<button id='{$button_id}' type='submit' class='{$class}'><span>{$button_text}</span></button>";
	}

	//Filter to add additional payment input fields to the form.
	$output .= apply_filters( 'swpm_stripe_payment_form_additional_fields', '' );

	$output .= '</form>';
	$output .= '</div>'; //End .swpm_button_wrapper

	return $output;}
