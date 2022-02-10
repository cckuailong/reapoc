<?php

require_once 'swpm_handle_subsc_ipn.php';

class swpm_paypal_ipn_handler { // phpcs:ignore

	public $ipn_log = false;                    // bool: log IPN results to text file?
	public $ipn_log_file;               // filename of the IPN log
	public $ipn_response;               // holds the IPN response from paypal
	public $ipn_data     = array();         // array contains the POST values for IPN
	public $fields       = array();           // array holds the fields to submit to paypal
	public $sandbox_mode = false;

	public function __construct() {
		$this->paypal_url   = 'https://www.paypal.com/cgi-bin/webscr';
		$this->ipn_log_file = 'ipn_handle_debug_swpm.log';
		$this->ipn_response = '';
	}

	public function swpm_validate_and_create_membership() {
		// Check Product Name , Price , Currency , Receivers email ,
		$error_msg = '';

		// Read the IPN and validate
		$gross_total      = $this->ipn_data['mc_gross'];
		$transaction_type = $this->ipn_data['txn_type'];
		$txn_id           = $this->ipn_data['txn_id'];
		$payment_status   = $this->ipn_data['payment_status'];

		// Check payment status
		if ( ! empty( $payment_status ) ) {
			if ( 'Denied' == $payment_status ) {
				$this->debug_log( 'Payment status for this transaction is DENIED. You denied the transaction... most likely a cancellation of an eCheque. Nothing to do here.', false );
				return false;
			}
			if ( 'Canceled_Reversal' === $payment_status ) {
				$this->debug_log( 'This is a dispute closed notification in your favour. The plugin will not do anyting.', false );
				return true;
			}
			if ( 'Completed' !== $payment_status && 'Processed' !== $payment_status && 'Refunded' !== $payment_status && 'Reversed' !== $payment_status ) {
				$error_msg .= 'Funds have not been cleared yet. Transaction will be processed when the funds clear!';
				$this->debug_log( $error_msg, false );
				return false;
			}
		}

		// Check txn type
		if ( 'new_case' === $transaction_type ) {
			$this->debug_log( 'This is a dispute case. Nothing to do here.', true );
			return true;
		}

		$custom                   = urldecode( $this->ipn_data['custom'] );
		$this->ipn_data['custom'] = $custom;
		$customvariables          = SwpmTransactions::parse_custom_var( $custom );

		// Handle refunds
		if ( $gross_total < 0 ) {
			// This is a refund or reversal
			$this->debug_log( 'This is a refund notification. Refund amount: ' . $gross_total, true );
			swpm_handle_subsc_cancel_stand_alone( $this->ipn_data, true );
			return true;
		}
		if ( isset( $this->ipn_data['reason_code'] ) && 'refund' === $this->ipn_data['reason_code'] ) {
			$this->debug_log( 'This is a refund notification. Refund amount: ' . $gross_total, true );
			swpm_handle_subsc_cancel_stand_alone( $this->ipn_data, true );
			return true;
		}

		if ( ( 'subscr_signup' === $transaction_type ) ) {
			$this->debug_log( 'Subscription signup IPN received... (handled by the subscription IPN handler)', true );
			// Code to handle the signup IPN for subscription
			$subsc_ref = $customvariables['subsc_ref'];

			if ( ! empty( $subsc_ref ) ) {
				$this->debug_log( 'Found a membership level ID. Creating member account...', true );
				$swpm_id = $customvariables['swpm_id'];
				swpm_handle_subsc_signup_stand_alone( $this->ipn_data, $subsc_ref, $this->ipn_data['subscr_id'], $swpm_id );
				// Handle customized subscription signup
			}
			return true;
		} elseif ( ( $transaction_type == 'subscr_cancel' ) || ( $transaction_type == 'subscr_eot' ) || ( $transaction_type == 'subscr_failed' ) ) {
			// Code to handle the IPN for subscription cancellation
			$this->debug_log( 'Subscription cancellation IPN received... (handled by the subscription IPN handler)', true );
			swpm_handle_subsc_cancel_stand_alone( $this->ipn_data );
			return true;
		} else {
			$cart_items = array();
			$this->debug_log( 'Transaction Type: Buy Now/Subscribe', true );
			$item_number = $this->ipn_data['item_number'];
			$item_name   = $this->ipn_data['item_name'];
			$quantity    = $this->ipn_data['quantity'];
			$mc_gross    = $this->ipn_data['mc_gross'];
			$mc_currency = $this->ipn_data['mc_currency'];

			$current_item = array(
				'item_number' => $item_number,
				'item_name'   => $item_name,
				'quantity'    => $quantity,
				'mc_gross'    => $mc_gross,
				'mc_currency' => $mc_currency,
			);

			array_push( $cart_items, $current_item );
		}

                /*** Duplicate IPN check ***/
		// Query the DB to check if we have already processed this transaction or not
		global $wpdb;
		$txn_row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}swpm_payments_tbl WHERE txn_id = %s", $txn_id ), OBJECT );

		// And if we have already processed it, do nothing and return true
		if (!empty($txn_row)) {
			$this->debug_log( "This transaction has already been processed (".$txn_id."). Nothing to do here.", true );
			return true;
		}
                /*** End of duplicate IPN check ***/

		$counter = 0;
		foreach ( $cart_items as $current_cart_item ) {
			$cart_item_data_num      = $current_cart_item['item_number'];
			$cart_item_data_name     = trim( $current_cart_item['item_name'] );
			$cart_item_data_quantity = $current_cart_item['quantity'];
			$cart_item_data_total    = $current_cart_item['mc_gross'];
			$cart_item_data_currency = $current_cart_item['mc_currency'];
			if ( empty( $cart_item_data_quantity ) ) {
				$cart_item_data_quantity = 1;
			}
			$this->debug_log( 'Item Number: ' . $cart_item_data_num, true );
			$this->debug_log( 'Item Name: ' . $cart_item_data_name, true );
			$this->debug_log( 'Item Quantity: ' . $cart_item_data_quantity, true );
			$this->debug_log( 'Item Total: ' . $cart_item_data_total, true );
			$this->debug_log( 'Item Currency: ' . $cart_item_data_currency, true );

			// Get the button id
			$pp_hosted_button    = false;
			$button_id           = $cart_item_data_num;// Button id is the item number.
			$membership_level_id = get_post_meta( $button_id, 'membership_level_id', true );
			if ( ! SwpmUtils::membership_level_id_exists( $membership_level_id ) ) {
				$this->debug_log( 'This payment button was not created in the plugin. This is a paypal hosted button.', true );
				$pp_hosted_button = true;
			}

			// Price check
			$check_price = true;
			$msg         = '';
			$msg         = apply_filters( 'swpm_before_price_check_filter', $msg, $current_cart_item );
			if ( ! empty( $msg ) && $msg == 'price-check-override' ) {// This filter allows an extension to do a customized version of price check (if needed)
				$check_price = false;
				$this->debug_log( 'Price and currency check has been overridden by an addon/extension.', true );
			}
			if ( $check_price && ! $pp_hosted_button ) {
				// Check according to buy now payment or subscription payment.
				$button_type = get_post_meta( $button_id, 'button_type', true );
				if ( $button_type == 'pp_buy_now' ) {// This is a PayPal buy now type button
					$expected_amount = ( get_post_meta( $button_id, 'payment_amount', true ) ) * $cart_item_data_quantity;
					$expected_amount = round( $expected_amount, 2 );
					$expected_amount = apply_filters( 'swpm_payment_amount_filter', $expected_amount, $button_id );
					$received_amount = $cart_item_data_total;

                                        if ( $received_amount < $expected_amount ) {
                                                // Error! amount received is less than expected. This is invalid.
                                                $this->debug_log( 'Expected amount: ' . $expected_amount, true );
                                                $this->debug_log( 'Received amount: ' . $received_amount, true );
                                                $this->debug_log( 'Price check failed. Amount received is less than the amount expected. This payment will not be processed.', false );
                                                return false;
                                        }

				} elseif ( $button_type == 'pp_subscription' ) {// This is a PayPal subscription type button
                                        //This is a "subscr_payment" type payment notification. The "subscr_signup" type gets handled before.
                                        $trial_billing_cycle = get_post_meta( $button_id, 'trial_billing_cycle', true );
					$trial_billing_amount = get_post_meta( $button_id, 'trial_billing_amount', true );
					$billing_amount = get_post_meta( $button_id, 'billing_amount', true );

                                        if ( empty( $trial_billing_cycle ) ){
                                            //No trial billing. Check main billing amount. Only need to check "mc_gross" which should cointain the "amount3" value.
                                            $this->debug_log( 'Trial billing is not enabled for this button.', true );
                                            $expected_amount = round( $billing_amount, 2 );

                                        } else {
                                            //Trial billing is specified for this button
                                            $this->debug_log( 'Trial billing is enabled for this button.', true );
                                            if ( swpm_is_paypal_recurring_payment($this->ipn_data) ){
                                                //This is a recurring payment of a subscription.
                                                $expected_amount = round( $billing_amount, 2 );
                                            } else {
                                                //This is a trial payment of a subscription
                                                $expected_amount = round( $trial_billing_amount, 2 );
                                            }

                                        }
					$received_amount = $cart_item_data_total;

                                        if ( $received_amount < $expected_amount ) {
                                                // Error! amount received is less than expected. This is invalid.
                                                $this->debug_log( 'Expected amount: ' . $expected_amount, true );
                                                $this->debug_log( 'Received amount: ' . $received_amount, true );
                                                $this->debug_log( 'Price check failed. Amount received is less than the amount expected. This payment will not be processed.', false );
                                                return false;
                                        }

				} else {
					$this->debug_log( 'Error! Unexpected button type: ' . $button_type, false );
					return false;
				}
			}

			// *** Handle Membership Payment ***
			// --------------------------------------------------------------------------------------
			// ========= Need to find the (level ID) in the custom variable ============
			$subsc_ref = $customvariables['subsc_ref'];// Membership level ID
			$this->debug_log( 'Membership payment paid for membership level ID: ' . $subsc_ref, true );
			if ( ! empty( $subsc_ref ) ) {
				$swpm_id = '';
				if ( isset( $customvariables['swpm_id'] ) ) {
					$swpm_id = $customvariables['swpm_id'];
				}
				if ( $transaction_type == 'web_accept' ) {
					$this->debug_log( 'Transaction type: web_accept. Creating member account...', true );
					swpm_handle_subsc_signup_stand_alone( $this->ipn_data, $subsc_ref, $this->ipn_data['txn_id'], $swpm_id );
				} elseif ( $transaction_type == 'subscr_payment' ) {
					$this->debug_log( 'Transaction type: subscr_payment. Checking if the member profile needed to be updated', true );
					swpm_update_member_subscription_start_date_if_applicable( $this->ipn_data );
				}
			} else {
				$this->debug_log( 'Membership level ID is missing in the payment notification! Cannot process this notification.', false );
			}
			// == End of Membership payment handling ==
			$counter++;
		}

		/*** Do Post payment operation and cleanup */
		// Save the transaction data
		$this->debug_log( 'Saving transaction data to the database table.', true );
		$this->ipn_data['gateway'] = 'paypal';
		$this->ipn_data['status']  = $this->ipn_data['payment_status'];

		// If the value ipn_data['ip'] is empty, try to detect the customer IP address using the variable custom['user_ip']
		if (empty($this->ipn_data['ip']) && filter_var($customvariables['user_ip'], FILTER_VALIDATE_IP)) {
			$this->ipn_data['ip'] = $customvariables['user_ip'];
		}

		SwpmTransactions::save_txn_record( $this->ipn_data, $cart_items );
		$this->debug_log( 'Transaction data saved.', true );

		// Trigger the PayPal IPN processed action hook (so other plugins can can listen for this event).
		do_action( 'swpm_paypal_ipn_processed', $this->ipn_data );

		do_action( 'swpm_payment_ipn_processed', $this->ipn_data );

		return true;
	}

	public function swpm_validate_ipn() {
		// Generate the post string from the _POST vars aswell as load the _POST vars into an arry
		$post_string = '';
		foreach ( $_POST as $field => $value ) {
			$this->ipn_data[ "$field" ] = $value;
			$post_string               .= $field . '=' . urlencode( stripslashes( $value ) ) . '&';
		}

		$this->post_string = $post_string;
		$this->debug_log( 'Post string : ' . $this->post_string, true );

		// IPN validation check
		if ( $this->validate_ipn_using_remote_post() ) {
			// We can also use an alternative validation using the validate_ipn_using_curl() function
			return true;
		} else {
			return false;
		}

	}

	public function validate_ipn_using_remote_post() {
		$this->debug_log( 'Checking if PayPal IPN response is valid', true );

		// Get received values from post data
		$validate_ipn  = array( 'cmd' => '_notify-validate' );
		$validate_ipn += wp_unslash( $_POST );

		// Send back post vars to paypal
		$params = array(
			'body'        => $validate_ipn,
			'timeout'     => 60,
			'httpversion' => '1.1',
			'compress'    => false,
			'decompress'  => false,
			'user-agent'  => 'Simple Membership Plugin',
		);

		// Post back to get a response.
		$connection_url = $this->sandbox_mode ? 'https://www.sandbox.paypal.com/cgi-bin/webscr' : 'https://www.paypal.com/cgi-bin/webscr';
		$this->debug_log( 'Connecting to: ' . $connection_url, true );
		$response = wp_safe_remote_post( $connection_url, $params );

		// The following two lines can be used for debugging
		// $this->debug_log( 'IPN Request: ' . print_r( $params, true ) , true);
		// $this->debug_log( 'IPN Response: ' . print_r( $response, true ), true);

		// Check to see if the request was valid.
		if ( ! is_wp_error( $response ) && strstr( $response['body'], 'VERIFIED' ) ) {
			$this->debug_log( 'IPN successfully verified.', true );
			return true;
		}

		// Invalid IPN transaction. Check the log for details.
		$this->debug_log( 'IPN validation failed.', false );
		if ( is_wp_error( $response ) ) {
			$this->debug_log( 'Error response: ' . $response->get_error_message(), false );
		}
		return false;
	}

	public function debug_log( $message, $success, $end = false ) {
		SwpmLog::log_simple_debug( $message, $success, $end );
	}
}

// Start of IPN handling (script execution)

$ipn_handler_instance = new swpm_paypal_ipn_handler();

$settings      = SwpmSettings::get_instance();
$debug_enabled = $settings->get_value( 'enable-debug' );
if ( ! empty( $debug_enabled ) ) {
	$debug_log = 'log.txt'; // Debug log file name
	echo esc_html( sprintf( 'Debug logging is enabled. Check the %s file for debug output.', $debug_log ) );
	$ipn_handler_instance->ipn_log      = true;
	$ipn_handler_instance->ipn_log_file = $debug_log;
	if ( empty( $_POST ) ) {
			$ipn_handler_instance->debug_log( 'This debug line was generated because you entered the URL of the ipn handling script in the browser.', true, true );
			exit;
	}
}

$sandbox_enabled = $settings->get_value( 'enable-sandbox-testing' );
if ( ! empty( $sandbox_enabled ) ) {
	$ipn_handler_instance->paypal_url   = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
	$ipn_handler_instance->sandbox_mode = true;
}

$ipn_handler_instance->debug_log( 'Paypal Class Initiated by ' . $_SERVER['REMOTE_ADDR'], true );

// Validate the IPN
if ( $ipn_handler_instance->swpm_validate_ipn() ) {
	$ipn_handler_instance->debug_log( 'Creating product Information to send.', true );

	if ( ! $ipn_handler_instance->swpm_validate_and_create_membership() ) {
		$ipn_handler_instance->debug_log( 'IPN product validation failed.', false );
	}
}
$ipn_handler_instance->debug_log( 'Paypal class finished.', true, true );
