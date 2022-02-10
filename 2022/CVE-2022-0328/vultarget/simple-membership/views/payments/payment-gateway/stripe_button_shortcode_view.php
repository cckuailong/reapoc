<?php

/* * ************************************************
 * Stripe Buy Now button shortcode handler
 * *********************************************** */
add_filter( 'swpm_payment_button_shortcode_for_stripe_buy_now', 'swpm_render_stripe_buy_now_button_sc_output', 10, 2 );

function swpm_render_stripe_buy_now_button_sc_output( $button_code, $args ) {

	$button_id = isset( $args['id'] ) ? $args['id'] : '';
	if ( empty( $button_id ) ) {
		return '<p class="swpm-red-box">Error! swpm_render_stripe_buy_now_button_sc_output() function requires the button ID value to be passed to it.</p>';
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
	if ( ! SwpmUtils::membership_level_id_exists( $membership_level_id ) ) {
		return '<p class="swpm-red-box">Error! The membership level specified in this button does not exist. You may have deleted this membership level. Edit the button and use the correct membership level.</p>';
	}

	//Payment amount and currency
	$payment_amount = get_post_meta( $button_id, 'payment_amount', true );
	if ( ! is_numeric( $payment_amount ) ) {
		return '<p class="swpm-red-box">Error! The payment amount value of the button must be a numeric number. Example: 49.50 </p>';
	}
	$payment_currency = get_post_meta( $button_id, 'payment_currency', true );
	$payment_amount   = round( $payment_amount, 2 ); //round the amount to 2 decimal place.
	$zeroCents        = unserialize( SIMPLE_WP_MEMBERSHIP_STRIPE_ZERO_CENTS );
	if ( in_array( $payment_currency, $zeroCents ) ) {
		//this is zero-cents currency, amount shouldn't be multiplied by 100
		$price_in_cents = $payment_amount;
	} else {
		$price_in_cents = $payment_amount * 100; //The amount (in cents). This value is passed to Stripe API.
	}
	$payment_amount_formatted = SwpmMiscUtils::format_money( $payment_amount, $payment_currency );
	//Return, cancel, notifiy URLs
	$notify_url = SIMPLE_WP_MEMBERSHIP_SITE_HOME_URL . '/?swpm_process_stripe_buy_now=1'; //We are going to use it to do post payment processing.
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

	//prefill member email
	$prefill_member_email = $settings->get_value( 'stripe-prefill-member-email' );

	if ( $prefill_member_email ) {
		$auth         = SwpmAuth::get_instance();
		$member_email = $auth->get( 'email' );
	}

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

	$uniqid = uniqid();

	/* === Stripe Buy Now Button Form === */
	$output  = '';
	$output .= '<div class="swpm-button-wrapper swpm-stripe-buy-now-wrapper">';
	$output .= "<form id='swpm-stripe-payment-form-" . $uniqid . "' action='" . $notify_url . "' METHOD='POST'> ";
	$output .= "<div style='display: none !important'>";
	$output .= "<script src='https://checkout.stripe.com/checkout.js' class='stripe-button'
        data-key='" . $api_keys['public'] . "'
        data-panel-label='Pay'
        data-amount='{$price_in_cents}'
		data-name='{$item_name}'";
	$output .= isset( $member_email ) ? sprintf( 'data-email="%s"', $member_email ) : '';
	$output .= "data-description='{$payment_amount_formatted}'";
	$output .= "data-locale='auto'";
	$output .= "data-label='{$button_text}'"; //Stripe doesn't currenty support button image for their standard checkout.
	$output .= "data-currency='{$payment_currency}'";
	if ( ! empty( $item_logo ) ) {//Show item logo/thumbnail in the stripe payment window
		$output .= "data-image='{$item_logo}'";
	}
	if ( ! empty( $billing_address ) ) {//Show billing address in the stipe payment window
		$output .= "data-billing-address='true'";
	}
	$output .= apply_filters( 'swpm_stripe_additional_checkout_data_parameters', '' ); //Filter to allow the addition of extra data parameters for stripe checkout.
	$output .= '></script>';
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

	$output .= wp_nonce_field( 'stripe_payments', '_wpnonce', true, false );
	$output .= '<input type="hidden" name="item_number" value="' . $button_id . '" />';
	$output .= "<input type='hidden' value='{$item_name}' name='item_name' />";
	$output .= "<input type='hidden' value='{$payment_amount}' name='item_price' />";
	$output .= "<input type='hidden' value='{$payment_currency}' name='currency_code' />";
	$output .= "<input type='hidden' value='{$custom_field_value}' name='custom' />";

	//Filter to add additional payment input fields to the form.
	$output .= apply_filters( 'swpm_stripe_payment_form_additional_fields', '' );

	$output .= '</form>';
	$output .= '</div>'; //End .swpm_button_wrapper

	return $output;
}

add_filter( 'swpm_payment_button_shortcode_for_stripe_subscription', 'swpm_render_stripe_subscription_button_sc_output', 10, 2 );

function swpm_render_stripe_subscription_button_sc_output( $button_code, $args ) {

	$button_id = isset( $args['id'] ) ? $args['id'] : '';
	if ( empty( $button_id ) ) {
		return '<p class="swpm-red-box">Error! swpm_render_stripe_buy_now_button_sc_output() function requires the button ID value to be passed to it.</p>';
	}

	//Get class option for button styling, set Stripe's default if none specified
	$class = isset( $args['class'] ) ? $args['class'] : 'stripe-button-el';

	//Check new_window parameter
	$window_target = isset( $args['new_window'] ) ? 'target="_blank"' : '';
	$button_text   = ( isset( $args['button_text'] ) ) ? esc_attr( $args['button_text'] ) : SwpmUtils::_( 'Buy Now' );
	$item_logo     = ''; //Can be used to show an item logo or thumbnail in the checkout form.

	$settings   = SwpmSettings::get_instance();
	$button_cpt = get_post( $button_id ); //Retrieve the CPT for this button
	$item_name  = htmlspecialchars( $button_cpt->post_title );

	$membership_level_id = get_post_meta( $button_id, 'membership_level_id', true );
	//Verify that this membership level exists (to prevent user paying for a level that has been deleted)
	if ( ! SwpmUtils::membership_level_id_exists( $membership_level_id ) ) {
		return '<p class="swpm-red-box">Error! The membership level specified in this button does not exist. You may have deleted this membership level. Edit the button and use the correct membership level.</p>';
	}

	//Return, cancel, notifiy URLs
	$notify_url = SIMPLE_WP_MEMBERSHIP_SITE_HOME_URL . '/?swpm_process_stripe_subscription=1'; //We are going to use it to do post payment processing.
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

	//prefill member email
	$prefill_member_email = $settings->get_value( 'stripe-prefill-member-email' );

	if ( $prefill_member_email ) {
		$auth         = SwpmAuth::get_instance();
		$member_email = $auth->get( 'email' );
	}

	$plan_id = get_post_meta( $button_id, 'stripe_plan_id', true );

	$plan_data = get_post_meta( $button_id, 'stripe_plan_data', true );

	if ( empty( $plan_data ) ) {
		//no plan data available, let's try to request one

		if ( version_compare( PHP_VERSION, '5.4.0', '<' ) ) {
			//This server's PHP version can't handle the library.
			$error_msg  = '<div class="swpm-red-box">';
			$error_msg .= '<p>The Stripe Subscription payment gateway library requires at least PHP 5.4.0. Your server is using a very old version of PHP.</p>';
			$error_msg .= '<p>Request your hosting provider to upgrade your PHP to a more recent version then you will be able to use the Stripe Subscription.<p>';
			$error_msg .= '</div>';
			return $error_msg;
		}

		require_once SIMPLE_WP_MEMBERSHIP_PATH . 'lib/stripe-util-functions.php';
		$result = StripeUtilFunctions::get_stripe_plan_info( $api_keys['secret'], $plan_id );
		if ( $result['success'] === false ) {
			// some error occurred, let's display it and stop processing the shortcode further
			return '<p class="swpm-red-box">Stripe error occurred: ' . $result['error_msg'] . '</p>';
		} else {
			// plan data has been successfully retreived
			$plan_data = $result['plan_data'];
			// Let's update post_meta in order to not re-request the data again on each button display
			update_post_meta( $button_id, 'stripe_plan_data', $plan_data );
		}
	}

	//let's set some vars
	$price_in_cents   = $plan_data['amount'];
	$payment_currency = strtoupper( $plan_data['currency'] );
	$zeroCents        = unserialize( SIMPLE_WP_MEMBERSHIP_STRIPE_ZERO_CENTS );
	if ( in_array( $payment_currency, $zeroCents ) ) {
		//this is zero-cents currency, amount shouldn't be devided by 100
		$payment_amount = $price_in_cents;
	} else {
		$payment_amount = $price_in_cents / 100;
	}
	$interval_count = $plan_data['interval_count'];
	$interval       = $plan_data['interval'];
	$trial          = $plan_data['trial_period_days'];
	$plan_name      = $plan_data['name'];
	$description    = $payment_amount . ' ' . $payment_currency;
	if ( $interval_count == 1 ) {
		$description .= ' / ' . $interval;
	} else {
		$description .= ' every ' . $plan_data['interval_count'] . ' ' . $plan_data['interval'] . 's';
	}
	// this should add info on trial period if available, but Stripe strips too long strings, so we leave it commented out for now.
	//        if ($trial != NULL) {
	//            $description .= '. '.$trial . ' days FREE trial.';
	//        }
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

	/* === Stripe Buy Now Button Form === */
	$output  = '';
	$output .= '<div class="swpm-button-wrapper swpm-stripe-buy-now-wrapper">';
	$output .= "<form action='" . $notify_url . "' METHOD='POST'> ";
	$output .= "<div style='display: none !important'>";
	$output .= "<script src='https://checkout.stripe.com/checkout.js' class='stripe-button'
        data-key='" . $api_keys['public'] . "'
        data-panel-label='Sign Me Up!'
		data-name='{$item_name}'";
	$output .= isset( $member_email ) ? sprintf( 'data-email="%s"', $member_email ) : '';
	$output .= "data-description='{$description}'";
	$output .= "data-locale='auto'";
	$output .= "data-label='{$button_text}'"; //Stripe doesn't currenty support button image for their standard checkout.
	$output .= "data-currency='{$payment_currency}'";
	if ( ! empty( $item_logo ) ) {//Show item logo/thumbnail in the stripe payment window
		$output .= "data-image='{$item_logo}'";
	}
	if ( ! empty( $billing_address ) ) {//Show billing address in the stipe payment window
		$output .= "data-billing-address='true'";
	}
	$output .= apply_filters( 'swpm_stripe_additional_checkout_data_parameters', '' ); //Filter to allow the addition of extra data parameters for stripe checkout.
	$output .= '></script>';
	$output .= '</div>';

	$button_image_url = get_post_meta( $button_id, 'button_image_url', true );
	if ( ! empty( $button_image_url ) ) {
		$output .= '<input type="image" src="' . $button_image_url . '" class="' . $class . '" alt="' . $button_text . '" title="' . $button_text . '" />';
	} else {
		$output .= "<button id='{$button_id}' type='submit' class='{$class}'><span>{$button_text}</span></button>";
	}

	$output .= wp_nonce_field( 'stripe_payments', '_wpnonce', true, false );
	$output .= '<input type="hidden" name="item_number" value="' . $button_id . '" />';
	$output .= "<input type='hidden' value='{$item_name}' name='item_name' />";
	$output .= "<input type='hidden' value='{$payment_amount}' name='item_price' />";
	$output .= "<input type='hidden' value='{$payment_currency}' name='currency_code' />";
	$output .= "<input type='hidden' value='{$custom_field_value}' name='custom' />";

	//Filter to add additional payment input fields to the form.
	$output .= apply_filters( 'swpm_stripe_payment_form_additional_fields', '' );

	$output .= '</form>';
	$output .= '</div>'; //End .swpm_button_wrapper

	return $output;
}
