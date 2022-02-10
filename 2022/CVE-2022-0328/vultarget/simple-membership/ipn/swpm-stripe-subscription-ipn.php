<?php

require SIMPLE_WP_MEMBERSHIP_PATH . 'ipn/swpm_handle_subsc_ipn.php';

class SwpmStripeSubscriptionIpnHandler {

	public function __construct() {

		$this->handle_stripe_ipn();
	}

	public function handle_stripe_ipn() {

		/*
		* [Imp] This comment explains how this script handles both the first time HTTP Post after payment and the webhooks.
		* If the "hook" query arg is set then that means it is a webhook notification. It will be used for certain actions like (update, cancel, refund, etc). Others will be ignored.
		* The first time payment in browser is handled via HTTP POST (when the "hook" query arg is not set).
		*/

		if ( isset( $_GET['hook'] ) ) {
			// This is Webhook notification from Stripe.
						// This webhook is used for all recurring payment notification (Legacy and SCA ones).

			// TODO: add Webhook Signing Secret verification
			// To do this, we need to get customer ID, retreive its details from Stripe, get button_id from metadata
			// and see if the button has Signing Secret option set. If it is - we need to check signatures
			// More details here: https://stripe.com/docs/webhooks#signatures

			$input = @file_get_contents( 'php://input' );
			if ( empty( $input ) ) {
				SwpmLog::log_simple_debug( 'Stripe subscription webhook sent empty data or page was accessed directly. Aborting.', false );
				echo 'Empty Webhook data received.';
				die;
			}
			// SwpmLog::log_simple_debug($input, true);
			$event_json = json_decode( $input );

			$type = $event_json->type;
			SwpmLog::log_simple_debug( sprintf( 'Stripe subscription webhook received: %s. Checking if we need to handle this webhook.', $type ), true );

			if ( 'customer.subscription.deleted' === $type || 'charge.refunded' === $type ) {
				// Subscription expired or refunded event
				//SwpmLog::log_simple_debug( sprintf( 'Stripe Subscription Webhook %s received. Processing request...', $type ), true );

				// Let's form minimal ipn_data array for swpm_handle_subsc_cancel_stand_alone
				$customer                  = $event_json->data->object->customer;
				$subscr_id                 = $event_json->data->object->id;
				$ipn_data                  = array();
				$ipn_data['subscr_id']     = $subscr_id;
				$ipn_data['parent_txn_id'] = $customer;

				swpm_handle_subsc_cancel_stand_alone( $ipn_data );
			}

			if ( $type == 'customer.subscription.updated' ) {
				// Subscription updated webhook

				// Let's form minimal ipn_data array
				$customer                  = $event_json->data->object->customer;
				$subscr_id                 = $event_json->data->object->id;
				$ipn_data                  = array();
				$ipn_data['subscr_id']     = $subscr_id;
				$ipn_data['parent_txn_id'] = $customer;

				swpm_update_member_subscription_start_date_if_applicable( $ipn_data );
			}

			if ( $type === 'invoice.payment_succeeded' ) {

				$billing_reason = isset( $event_json->data->object->billing_reason ) ? $event_json->data->object->billing_reason : '';
				if ( $billing_reason == 'subscription_cycle' ) {
					//This is recurring/subscription payment invoice
					SwpmLog::log_simple_debug( sprintf( 'Stripe invoice.payment_succeeded webhook for subscription_cycle. This is a successful subscription charge. Capturing payment data.' ), true );

					$sub_id = $event_json->data->object->subscription;
					//$cust_id = $event_json->data->object->billing_reason;
					//$date = $event_json->data->object->date;
					$price_in_cents = $event_json->data->object->amount_paid; //amount in cents
					$currency_code  = $event_json->data->object->currency;

					$zero_cents = unserialize( SIMPLE_WP_MEMBERSHIP_STRIPE_ZERO_CENTS );
					if ( in_array( $currency_code, $zero_cents, true ) ) {
							$payment_amount = $price_in_cents;
					} else {
							$payment_amount = $price_in_cents / 100;// The amount (in cents). This value is used in Stripe API.
					}
					$payment_amount = floatval( $payment_amount );

					// Let's try to get first_name and last_name from full name
					$full_name   = $event_json->data->object->customer_name;
					$name_pieces = explode( ' ', $full_name, 2 );
					$first_name  = $name_pieces[0];
					if ( ! empty( $name_pieces[1] ) ) {
						$last_name = $name_pieces[1];
					}

					//Retrieve the member record for this subscription
					$member_record = SwpmMemberUtils::get_user_by_subsriber_id( $sub_id );
					if ( $member_record ) {
									// Found a member record
									$member_id           = $member_record->member_id;
									$membership_level_id = $member_record->membership_level;
						if ( empty( $first_name ) ) {
							$first_name = $member_record->first_name;
						}
						if ( empty( $last_name ) ) {
							$last_name = $member_record->last_name;
						}
					} else {
									SwpmLog::log_simple_debug( 'Could not find an existing member record for the given subscriber ID: ' . $sub_id . '. This user profile may have been deleted.', false );
									$member_id           = '';
									$membership_level_id = '';
					}

					//Create the custom field
					$custom_field_value  = 'subsc_ref=' . $membership_level_id;
					$custom_field_value .= '&swpm_id=' . $member_id;

					// Create the $ipn_data array.
					$ipn_data                     = array();
					$ipn_data['mc_gross']         = $payment_amount;
					$ipn_data['first_name']       = $first_name;
					$ipn_data['last_name']        = $last_name;
					$ipn_data['payer_email']      = $event_json->data->object->customer_email;
					$ipn_data['membership_level'] = $membership_level_id;
					$ipn_data['txn_id']           = $event_json->data->object->charge;
					$ipn_data['subscr_id']        = $sub_id;
					$ipn_data['swpm_id']          = $member_id;
					$ipn_data['ip']               = '';
					$ipn_data['custom']           = $custom_field_value;
					$ipn_data['gateway']          = 'stripe-sca-subs';
					$ipn_data['status']           = 'subscription';

					//TODO - Maybe handle the user access start date updating here (instead of "customer.subscription.updated" hook).
					//swpm_update_member_subscription_start_date_if_applicable( $ipn_data );

					// Save the transaction record
					SwpmTransactions::save_txn_record( $ipn_data );
					SwpmLog::log_simple_debug( 'Transaction data saved for Stripe subscription notification.', true );
				}
			}

			//End of the webhook notification execution.
						//Give 200 status then exit out.
			http_response_code( 200 ); // Tells Stripe we received this notification
			return;
		}

		//The following will get executed only for DIRECT post (not webhooks). So it is executed at the time of payment in the browser (via HTTP POST). When the "hook" query arg is not set.

		SwpmLog::log_simple_debug( 'Stripe subscription IPN received. Processing request...', true );
		// SwpmLog::log_simple_debug(print_r($_REQUEST, true), true);//Useful for debugging purpose
		// Include the Stripe library.
		SwpmMiscUtils::load_stripe_lib();
		// Read and sanitize the request parameters.
		$button_id    = sanitize_text_field( $_REQUEST['item_number'] );
		$button_id    = absint( $button_id );
		$button_title = sanitize_text_field( $_REQUEST['item_name'] );

		$stripe_token      = filter_input( INPUT_POST, 'stripeToken', FILTER_SANITIZE_STRING );
		$stripe_token_type = filter_input( INPUT_POST, 'stripeTokenType', FILTER_SANITIZE_STRING );
		$stripe_email      = filter_input( INPUT_POST, 'stripeEmail', FILTER_SANITIZE_EMAIL );

		// Retrieve the CPT for this button
		$button_cpt = get_post( $button_id );
		if ( ! $button_cpt ) {
			// Fatal error. Could not find this payment button post object.
			SwpmLog::log_simple_debug( 'Fatal Error! Failed to retrieve the payment button post object for the given button ID: ' . $button_id, false );
			wp_die( esc_html( sprintf( 'Fatal Error! Payment button (ID: %d) does not exist. This request will fail.', $button_id ) ) );
		}

		$plan_id = get_post_meta( $button_id, 'stripe_plan_id', true );
		$descr   = 'Subscription to "' . $plan_id . '" plan';

		$membership_level_id = get_post_meta( $button_id, 'membership_level_id', true );

		// Validate and verify some of the main values.
		// Validation passed. Go ahead with the charge.
		// Sandbox and other settings
		$settings        = SwpmSettings::get_instance();
		$sandbox_enabled = $settings->get_value( 'enable-sandbox-testing' );

		//API keys
		$api_keys = SwpmMiscUtils::get_stripe_api_keys_from_payment_button( $button_id, ! $sandbox_enabled );

		// Set secret API key in the Stripe library
		\Stripe\Stripe::setApiKey( $api_keys['secret'] );

		// Get the credit card details submitted by the form
		$token = $stripe_token;

		// Create the charge on Stripe's servers - this will charge the user's card
		try {
			$customer = \Stripe\Customer::create(
				array(
					'description'     => $descr,
					'email'           => $stripe_email,
					'source'          => $token,
					'plan'            => $plan_id,
					'trial_from_plan' => 'true',
				)
			);
		} catch ( Exception $e ) {
			SwpmLog::log_simple_debug( 'Error occurred during Stripe Subscribe. ' . $e->getMessage(), false );
			$body         = $e->getJsonBody();
			$error        = $body['error'];
			$error_string = wp_json_encode( $error );
			SwpmLog::log_simple_debug( 'Error details: ' . $error_string, false );
			wp_die( esc_html( 'Stripe subscription Error! ' . $e->getMessage() . $error_string ) );
		}

		// Everything went ahead smoothly with the charge.
		SwpmLog::log_simple_debug( 'Stripe subscription successful.', true );

		// let's add button_id to metadata
		$customer->metadata = array( 'button_id' => $button_id );
		try {
			$customer->save();
		} catch ( Exception $e ) {
			SwpmLog::log_simple_debug( 'Error occurred during Stripe customer metadata update. ' . $e->getMessage(), false );
			$body = $e->getJsonBody();
			SwpmLog::log_simple_debug( 'Error details: ' . $error_string, false );
		}

		// Grab customer ID and set it as the transaction ID.
		$txn_id = $customer->id; // $charge->balance_transaction;
		// Grab subscription ID
		$subscr_id  = $customer->subscriptions->data[0]->id;
		$custom     = sanitize_text_field( $_REQUEST['custom'] );
		$custom_var = SwpmTransactions::parse_custom_var( $custom );
		$swpm_id    = isset( $custom_var['swpm_id'] ) ? $custom_var['swpm_id'] : '';

		$payment_amount = $customer->subscriptions->data[0]->plan->amount / 100;

		// Create the $ipn_data array.
		$ipn_data                     = array();
		$ipn_data['mc_gross']         = $payment_amount;
		$ipn_data['first_name']       = '';
		$ipn_data['last_name']        = '';
		$ipn_data['payer_email']      = $stripe_email;
		$ipn_data['membership_level'] = $membership_level_id;
		$ipn_data['txn_id']           = $txn_id;
		$ipn_data['subscr_id']        = $subscr_id;
		$ipn_data['swpm_id']          = $swpm_id;
		$ipn_data['ip']               = $custom_var['user_ip'];
		$ipn_data['custom']           = $custom;
		$ipn_data['gateway']          = 'stripe';
		$ipn_data['status']           = 'completed';

		$ipn_data['address_street']  = '';
		$ipn_data['address_city']    = '';
		$ipn_data['address_state']   = '';
		$ipn_data['address_zipcode'] = '';
		$ipn_data['country']         = '';

		$ipn_data['payment_button_id'] = $button_id;
		$ipn_data['is_live']           = ! $sandbox_enabled;

		// Handle the membership signup related tasks.
		swpm_handle_subsc_signup_stand_alone( $ipn_data, $membership_level_id, $txn_id, $swpm_id );

		// Save the transaction record
		SwpmTransactions::save_txn_record( $ipn_data );
		SwpmLog::log_simple_debug( 'Transaction data saved.', true );

		// Trigger the stripe IPN processed action hook (so other plugins can can listen for this event).
		do_action( 'swpm_stripe_ipn_processed', $ipn_data );

		do_action( 'swpm_payment_ipn_processed', $ipn_data );

		// Redirect the user to the return URL (or to the homepage if a return URL is not specified for this payment button).
		$return_url = get_post_meta( $button_id, 'return_url', true );
		if ( empty( $return_url ) ) {
			$return_url = SIMPLE_WP_MEMBERSHIP_SITE_HOME_URL;
		}
		SwpmLog::log_simple_debug( 'Redirecting customer to: ' . $return_url, true );
		SwpmLog::log_simple_debug( 'End of Stripe subscription IPN processing.', true, true );
		SwpmMiscUtils::redirect_to_url( $return_url );
	}

}

$swpm_stripe_subscription_ipn = new SwpmStripeSubscriptionIpnHandler();
