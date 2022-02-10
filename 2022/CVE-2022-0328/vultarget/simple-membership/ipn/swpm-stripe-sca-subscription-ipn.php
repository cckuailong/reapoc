<?php

require SIMPLE_WP_MEMBERSHIP_PATH . 'ipn/swpm_handle_subsc_ipn.php';

class SwpmStripeSCASubscriptionIpnHandler {

	public function __construct() {

		$this->handle_stripe_ipn();
	}

	public function handle_stripe_ipn() {
				//This will get executed only for direct post (not webhooks). So it is executed at the time of payment in the browser (via HTTP POST). When the "hook" query arg is not set.
				//The webhooks are handled by the "swpm-stripe-subscription-ipn.php" script.

		SwpmLog::log_simple_debug( 'Stripe SCA Subscription IPN (HTTP POST) received. Processing request...', true );
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

			$sub_id = $sess->subscription;

			$sub = \Stripe\Subscription::retrieve( $sub_id );
		} catch ( Exception $e ) {
			$error_msg = 'Error occurred: ' . $e->getMessage();
			SwpmLog::log_simple_debug( $error_msg, false );
			wp_die( esc_html( $error_msg ) );
		}

		$pm = \Stripe\PaymentMethod::retrieve( $sub->default_payment_method );

		// Grab the charge ID and set it as the transaction ID.
		$txn_id = $sub->customer;
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

		$price_in_cents = $sub->plan->amount;
		$currency_code  = strtoupper( $sub->plan->currency );

		$zero_cents = unserialize( SIMPLE_WP_MEMBERSHIP_STRIPE_ZERO_CENTS );
		if ( in_array( $currency_code, $zero_cents, true ) ) {
			$payment_amount = $price_in_cents;
		} else {
			$payment_amount = $price_in_cents / 100;// The amount (in cents). This value is used in Stripe API.
		}

		$payment_amount = floatval( $payment_amount );

		$payment_amount = apply_filters( 'swpm_payment_amount_filter', $payment_amount, $button_id );

		$membership_level_id = get_post_meta( $button_id, 'membership_level_id', true );

		// Everything went ahead smoothly with the charge.
		SwpmLog::log_simple_debug( 'Stripe SCA Subscription charge successful.', true );

		$customer = \Stripe\Customer::retrieve( $txn_id );

		$stripe_email = $customer->email;

		$user_ip = SwpmUtils::get_user_ip_address();

		//Custom field data
		$custom_field_value  = 'subsc_ref=' . $membership_level_id;
		$custom_field_value .= '&user_ip=' . $user_ip;
		if ( SwpmMemberUtils::is_member_logged_in() ) {
			$custom_field_value .= '&swpm_id=' . SwpmMemberUtils::get_logged_in_members_id();
		}
		$custom_field_value = apply_filters( 'swpm_custom_field_value_filter', $custom_field_value );

		$custom = $custom_field_value;

                //Override custom value if necessary (for subscription webhooks)
                if ( isset ( $sub_id )){
                    //This is a subscription payment notificataion. Lets check if the original custom field value is already saved in the CPT for this stripe subscription.
                    $orig_custom_value = SwpmTransactions::get_original_custom_value_for_subscription_payment($sub_id);
                    if ( !empty ($orig_custom_value)){
                        //Override the custom field value with the original transactions value that can contain additional cookie and session info.
                        $custom = $orig_custom_value;
                        SwpmLog::log_simple_debug( 'Custom field value overriden with original value. Custom: ' . $custom, true );
                    }
                }

		$custom_var = SwpmTransactions::parse_custom_var( $custom );
		$swpm_id    = isset( $custom_var['swpm_id'] ) ? $custom_var['swpm_id'] : '';

		// Let's try to get first_name and last_name from full name
		$name       = $pm->billing_details->name;
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
		$ipn_data['subscr_id']        = $sub_id;
		$ipn_data['swpm_id']          = $swpm_id;
		$ipn_data['ip']               = $custom_var['user_ip'];
		$ipn_data['custom']           = $custom;
		$ipn_data['gateway']          = 'stripe-sca-subs';
		$ipn_data['status']           = 'completed';

		$bd_addr = $pm->billing_details->address;

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
		SwpmLog::log_simple_debug( 'End of Stripe SCA Subscription IPN processing.', true, true );
		SwpmMiscUtils::redirect_to_url( $return_url );

	}
}

new SwpmStripeSCASubscriptionIpnHandler();
