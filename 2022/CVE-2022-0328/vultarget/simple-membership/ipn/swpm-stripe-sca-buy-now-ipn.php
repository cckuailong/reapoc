<?php

class SwpmStripeSCABuyNowIpnHandler {

	public function __construct() {
		//check if this is session create request
		if ( wp_doing_ajax() ) {
			$action = filter_input( INPUT_POST, 'action', FILTER_SANITIZE_STRING );
			if ( 'swpm_stripe_sca_create_checkout_session' === $action ) {
				add_action( 'wp_ajax_swpm_stripe_sca_create_checkout_session', array( $this, 'handle_session_create' ) );
				add_action( 'wp_ajax_nopriv_swpm_stripe_sca_create_checkout_session', array( $this, 'handle_session_create' ) );
			}
			return;
		}

		require_once SIMPLE_WP_MEMBERSHIP_PATH . 'ipn/swpm_handle_subsc_ipn.php';
		$this->handle_stripe_ipn();
	}

	public function handle_stripe_ipn() {
		SwpmLog::log_simple_debug( 'Stripe SCA Buy Now IPN received. Processing request...', true );
		// SwpmLog::log_simple_debug(print_r($_REQUEST, true), true);//Useful for debugging purpose

		// Read and sanitize the request parameters.

		$ref_id = filter_input( INPUT_GET, 'ref_id', FILTER_SANITIZE_STRING );

		if ( empty( $ref_id ) ) {
			//no ref id provided, cannot proceed
			SwpmLog::log_simple_debug( 'Fatal Error! No ref_id provied.', false );
			wp_die( esc_html( 'Fatal Error! No ref_id provied.' ) );

		}

		$trans_info = explode( '|', $ref_id );
		$button_id  = isset( $trans_info[1] ) ? absint( $trans_info[1] ) : false;

		// Retrieve the CPT for this button
		$button_cpt = get_post( $button_id );
		if ( ! $button_cpt ) {
			// Fatal error. Could not find this payment button post object.
			SwpmLog::log_simple_debug( 'Fatal Error! Failed to retrieve the payment button post object for the given button ID: ' . $button_id, false );
			wp_die( esc_html( sprintf( 'Fatal Error! Payment button (ID: %d) does not exist. This request will fail.', $button_id ) ) );
		}

		$settings        = SwpmSettings::get_instance();
		$sandbox_enabled = $settings->get_value( 'enable-sandbox-testing' );

		//API keys
		$api_keys = SwpmMiscUtils::get_stripe_api_keys_from_payment_button( $button_id, ! $sandbox_enabled );

		// Include the Stripe library.
		SwpmMiscUtils::load_stripe_lib();

		try {
			\Stripe\Stripe::setApiKey( $api_keys['secret'] );

			$events = \Stripe\Event::all(
				array(
					'type'    => 'checkout.session.completed',
					'created' => array(
						'gte' => time() - 60 * 60,
					),
				)
			);

			$sess = false;

			foreach ( $events->autoPagingIterator() as $event ) {
				$session = $event->data->object;
				if ( isset( $session->client_reference_id ) && $session->client_reference_id === $ref_id ) {
					$sess = $session;
					break;
				}
			}

			if ( false === $sess ) {
				// Can't find session.
				$error_msg = sprintf( "Fatal error! Payment with ref_id %s can't be found", $ref_id );
				SwpmLog::log_simple_debug( $error_msg, false );
				wp_die( esc_html( $error_msg ) );
			}

			$pi_id = $sess->payment_intent;

			$pi = \Stripe\PaymentIntent::retrieve( $pi_id );
		} catch ( Exception $e ) {
			$error_msg = 'Error occurred: ' . $e->getMessage();
			SwpmLog::log_simple_debug( $error_msg, false );
			wp_die( esc_html( $error_msg ) );
		}

		$charge = $pi->charges;

		// Grab the charge ID and set it as the transaction ID.
		$txn_id = $charge->data[0]->id;
		// The charge ID can be used to retrieve the transaction details using hte following call.
		// \Stripe\Charge::retrieve($charge->$data[0]->id);

		//check if this payment has already been processed
		$payment = get_posts(
			array(
				'meta_key'       => 'txn_id',
				'meta_value'     => $txn_id,
				'posts_per_page' => 1,
				'offset'         => 0,
				'post_type'      => 'swpm_transactions',
			)
		);
		wp_reset_postdata();

		if ( $payment ) {
			//payment has already been processed. Redirecting user to return_url
			$return_url = get_post_meta( $button_id, 'return_url', true );
			if ( empty( $return_url ) ) {
				$return_url = SIMPLE_WP_MEMBERSHIP_SITE_HOME_URL;
			}
			SwpmMiscUtils::redirect_to_url( $return_url );
			return;
		}

		$price_in_cents = floatval( $pi->amount_received );
		$currency_code  = strtoupper( $pi->currency );

		$zero_cents = unserialize( SIMPLE_WP_MEMBERSHIP_STRIPE_ZERO_CENTS );
		if ( in_array( $currency_code, $zero_cents, true ) ) {
			$payment_amount = $price_in_cents;
		} else {
			$payment_amount = $price_in_cents / 100;// The amount (in cents). This value is used in Stripe API.
		}

		$payment_amount = floatval( $payment_amount );

		$stripe_email = $charge->data[0]->billing_details->email;

		$membership_level_id = get_post_meta( $button_id, 'membership_level_id', true );

		// Validate and verify some of the main values.
		$true_payment_amount = get_post_meta( $button_id, 'payment_amount', true );
		$true_payment_amount = apply_filters( 'swpm_payment_amount_filter', $true_payment_amount, $button_id );
		$true_payment_amount = floatval( $true_payment_amount );

		if ( $payment_amount !== $true_payment_amount ) {
			// Fatal error. Payment amount may have been tampered with.
			$error_msg = 'Fatal Error! Received payment amount (' . $payment_amount . ') does not match with the original amount (' . $true_payment_amount . ')';
			SwpmLog::log_simple_debug( $error_msg, false );
			wp_die( esc_html( $error_msg ) );
		}
		$true_currency_code = get_post_meta( $button_id, 'payment_currency', true );
		if ( $currency_code !== $true_currency_code ) {
			// Fatal error. Currency code may have been tampered with.
			$error_msg = 'Fatal Error! Received currency code (' . $currency_code . ') does not match with the original code (' . $true_currency_code . ')';
			SwpmLog::log_simple_debug( $error_msg, false );
			wp_die( esc_html( $error_msg ) );
		}

		// Everything went ahead smoothly with the charge.
		SwpmLog::log_simple_debug( 'Stripe SCA Buy Now charge successful.', true );

		$user_ip = SwpmUtils::get_user_ip_address();

		//Custom field data
		$custom_field_value  = 'subsc_ref=' . $membership_level_id;
		$custom_field_value .= '&user_ip=' . $user_ip;
		if ( SwpmMemberUtils::is_member_logged_in() ) {
			$custom_field_value .= '&swpm_id=' . SwpmMemberUtils::get_logged_in_members_id();
		}
		$custom_field_value = apply_filters( 'swpm_custom_field_value_filter', $custom_field_value );

		$custom = $custom_field_value;

		$custom_var = SwpmTransactions::parse_custom_var( $custom );
		$swpm_id    = isset( $custom_var['swpm_id'] ) ? $custom_var['swpm_id'] : '';

		// Let's try to get first_name and last_name from full name
		$name       = trim( $charge->data[0]->billing_details->name );
		$last_name  = ( strpos( $name, ' ' ) === false ) ? '' : preg_replace( '#.*\s([\w-]*)$#', '$1', $name );
		$first_name = trim( preg_replace( '#' . $last_name . '#', '', $name ) );

		// Create the $ipn_data array.
		$ipn_data                     = array();
		$ipn_data['mc_gross']         = $payment_amount;
		$ipn_data['first_name']       = $first_name;
		$ipn_data['last_name']        = $last_name;
		$ipn_data['payer_email']      = $stripe_email;
		$ipn_data['membership_level'] = $membership_level_id;
		$ipn_data['txn_id']           = $txn_id;
		$ipn_data['subscr_id']        = $txn_id;/* Set the txn_id as subscriber_id so it is similar to PayPal buy now. Also, it can connect to the profile in the "payments" menu. */
		$ipn_data['swpm_id']          = $swpm_id;
		$ipn_data['ip']               = $custom_var['user_ip'];
		$ipn_data['custom']           = $custom;
		$ipn_data['gateway']          = 'stripe-sca';
		$ipn_data['status']           = 'completed';

		$bd_addr = $charge->data[0]->billing_details->address;

		$ipn_data['address_street']  = isset( $bd_addr->line1 ) ? $bd_addr->line1 : '';
		$ipn_data['address_city']    = isset( $bd_addr->city ) ? $bd_addr->city : '';
		$ipn_data['address_state']   = isset( $bd_addr->state ) ? $bd_addr->state : '';
		$ipn_data['address_zipcode'] = isset( $bd_addr->postal_code ) ? $bd_addr->postal_code : '';
		$ipn_data['address_country'] = isset( $bd_addr->country ) ? $bd_addr->country : '';

		$ipn_data['payment_button_id'] = $button_id;
		$ipn_data['is_live']           = ! $sandbox_enabled;

		// Handle the membership signup related tasks.
		swpm_handle_subsc_signup_stand_alone( $ipn_data, $membership_level_id, $txn_id, $swpm_id );

		// Save the transaction record
		SwpmTransactions::save_txn_record( $ipn_data );
		SwpmLog::log_simple_debug( 'Transaction data saved.', true );

		// Trigger the stripe IPN processed action hook (so other plugins can can listen for this event).
		do_action( 'swpm_stripe_sca_ipn_processed', $ipn_data );

		do_action( 'swpm_payment_ipn_processed', $ipn_data );

		// Redirect the user to the return URL (or to the homepage if a return URL is not specified for this payment button).
		$return_url = get_post_meta( $button_id, 'return_url', true );
		if ( empty( $return_url ) ) {
			$return_url = SIMPLE_WP_MEMBERSHIP_SITE_HOME_URL;
		}
		SwpmLog::log_simple_debug( 'Redirecting customer to: ' . $return_url, true );
		SwpmLog::log_simple_debug( 'End of Stripe SCA Buy Now IPN processing.', true, true );
		SwpmMiscUtils::redirect_to_url( $return_url );

	}

	public function handle_session_create() {
		$button_id = filter_input( INPUT_POST, 'swpm_button_id', FILTER_SANITIZE_NUMBER_INT );
		if ( empty( $button_id ) ) {
			wp_send_json( array( 'error' => 'No button ID provided' ) );
		}

		$uniqid = filter_input( INPUT_POST, 'swpm_uniqid', FILTER_SANITIZE_STRING );
		$uniqid = ! empty( $uniqid ) ? $uniqid : '';

		$settings   = SwpmSettings::get_instance();
		$button_cpt = get_post( $button_id ); //Retrieve the CPT for this button
		$item_name  = htmlspecialchars( $button_cpt->post_title );

		$plan_id = get_post_meta( $button_id, 'stripe_plan_id', true );

		if ( empty( $plan_id ) ) {
			//Payment amount and currency
			$payment_amount = get_post_meta( $button_id, 'payment_amount', true );
			if ( ! is_numeric( $payment_amount ) ) {
				wp_send_json( array( 'error' => 'Error! The payment amount value of the button must be a numeric number. Example: 49.50' ) );
			}

			$payment_currency = get_post_meta( $button_id, 'payment_currency', true );
			$payment_amount   = round( $payment_amount, 2 ); //round the amount to 2 decimal place.

			$payment_amount = apply_filters( 'swpm_payment_amount_filter', $payment_amount, $button_id );

			$zero_cents = unserialize( SIMPLE_WP_MEMBERSHIP_STRIPE_ZERO_CENTS );
			if ( in_array( $payment_currency, $zero_cents ) ) {
				//this is zero-cents currency, amount shouldn't be multiplied by 100
				$price_in_cents = $payment_amount;
			} else {
				$price_in_cents = $payment_amount * 100; //The amount (in cents). This value is passed to Stripe API.
			}
			$payment_amount_formatted = SwpmMiscUtils::format_money( $payment_amount, $payment_currency );
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

		$ref_id = 'swpm_' . $uniqid . '|' . $button_id;

		//Return, cancel, notifiy URLs
		if ( empty( $plan_id ) ) {
			$notify_url = sprintf( SIMPLE_WP_MEMBERSHIP_SITE_HOME_URL . '/?swpm_process_stripe_sca_buy_now=1&ref_id=%s', $ref_id );
		} else {
			$notify_url = sprintf( SIMPLE_WP_MEMBERSHIP_SITE_HOME_URL . '/?swpm_process_stripe_sca_subscription=1&ref_id=%s', $ref_id );
		}

		$current_url_posted = filter_input( INPUT_POST, 'swpm_page_url', FILTER_SANITIZE_URL );

		$current_url = ! empty( $current_url_posted ) ? $current_url_posted : SIMPLE_WP_MEMBERSHIP_SITE_HOME_URL;

		//prefill member email
		$prefill_member_email = $settings->get_value( 'stripe-prefill-member-email' );

		if ( $prefill_member_email ) {
			$auth         = SwpmAuth::get_instance();
			$member_email = $auth->get( 'email' );
		}

		SwpmMiscUtils::load_stripe_lib();

		try {
			\Stripe\Stripe::setApiKey( $api_keys['secret'] );

			if ( empty( $plan_id ) ) {
				//this is one-off payment
				$opts = array(
					'payment_method_types'       => array( 'card' ),
					'client_reference_id'        => $ref_id,
					'billing_address_collection' => $billing_address ? 'required' : 'auto',
					'line_items'                 => array(
						array(
							'name'        => $item_name,
							'description' => $payment_amount_formatted,
							'amount'      => $price_in_cents,
							'currency'    => $payment_currency,
							'quantity'    => 1,
						),
					),
					'success_url'                => $notify_url,
					'cancel_url'                 => $current_url,
				);
			} else {
				//this is subscription payment
				$opts = array(
					'payment_method_types'       => array( 'card' ),
					'client_reference_id'        => $ref_id,
					'billing_address_collection' => $billing_address ? 'required' : 'auto',
					'subscription_data'          => array(
						'items' => array( array( 'plan' => $plan_id ) ),
					),
					'success_url'                => $notify_url,
					'cancel_url'                 => $current_url,
				);

				$trial_period = get_post_meta( $button_id, 'stripe_trial_period', true );
				$trial_period = absint( $trial_period );
				if ( $trial_period ) {
					$opts['subscription_data']['trial_period_days'] = $trial_period;
				}
			}

			if ( ! empty( $item_logo ) ) {
				$opts['line_items'][0]['images'] = array( $item_logo );
			}

			if ( ! empty( $member_email ) ) {
				$opts['customer_email'] = $member_email;
			}

			$opts = apply_filters( 'swpm_stripe_sca_session_opts', $opts, $button_id );

			$session = \Stripe\Checkout\Session::create( $opts );
		} catch ( Exception $e ) {
			$err = $e->getMessage();
			wp_send_json( array( 'error' => 'Error occurred: ' . $err ) );
		}
		wp_send_json( array( 'session_id' => $session->id ) );
	}

}

new SwpmStripeSCABuyNowIpnHandler();
