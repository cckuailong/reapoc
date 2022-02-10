<?php
/**
 * The Braintree webhook handler
 *
 * @since 1.9.5 - Various updates to how we log & process requests from Braintree
 */

use Braintree\WebhookNotification as Braintree_WebhookNotification;

// If loading directly, make sure we return a 200 HTTP status
global $isapage;
$isapage = true;

//in case the file is loaded directly
if ( ! defined( "ABSPATH" ) ) {
	define( 'WP_USE_THEMES', false );
	require_once( dirname( __FILE__ ) . '/../../../../wp-load.php' );
}

//globals
global $wpdb;

// Sets the PMPRO_DOING_WEBHOOK constant and fires the pmpro_doing_webhook action.
pmpro_doing_webhook( 'braintree', true );

// Debug log
global $logstr;
$logstr = array( "Logged On: " . date_i18n( "m/d/Y H:i:s", current_time( 'timestamp' ) ) );
$logstr[] = "\nREQUEST:";
$logstr[] = var_export( $_REQUEST, true );
$logstr[] = "\n";

// Don't run this with wrong PHP version
if ( version_compare( PHP_VERSION, '5.4.45', '<' ) ) {
	return;
}

//load Braintree library, gateway class constructor does config
if ( ! class_exists( '\Braintree' ) ) {
	require_once( PMPRO_DIR . "/classes/gateways/class.pmprogateway_braintree.php" );
}

$gateway             = new PMProGateway_braintree();
$webhookNotification = null;

if ( empty( $_REQUEST['bt_payload'] ) ) {
	$logstr[] = "No payload in request?!? " . print_r( $_REQUEST, true );
	pmpro_braintreeWebhookExit();
}

if ( isset( $_POST['bt_signature'] ) && ! isset( $_POST['bt_payload'] ) ) {
	$logstr[] = "No payload and signature included in the request!";
	pmpro_braintreeWebhookExit();
}

//get notification
try {
	/**
	 * @since 1.9.5 - BUG FIX: Unable to identify Braintree Webhook messages
	 * Expecting Braintree library to sanitize signature & payload 
	 * since using sanitize_text_field() breaks Braintree parser
	 */
	$webhookNotification = Braintree_WebhookNotification::parse( $_POST['bt_signature'], $_POST['bt_payload'] );
	
	$logstr[] = "\webhookNotification:";
	$logstr[] = var_export( $webhookNotification, true );
	$logstr[] = "\n";
} catch ( Exception $e ) {
	$logstr[] = "Couldn't extract notification from payload: {$_REQUEST['bt_payload']}";
	$logstr[] = "Error message: " . $e->getMessage();
	
	pmpro_braintreeWebhookExit();
}

/**
 * @since 1.9.5 - ENHANCEMENT: Log if notification object has unexpected format
 */
if ( ! isset( $webhookNotification->kind ) ) {
	$logstr[] = "Unexpected webhook message: " . print_r( $webhookNotification, true ) . "\n";
	pmpro_braintreeWebhookExit();
}

/**
 * Only verifying?
 * @since 1.9.5 - Log Webhook tests with webhook supplied timestamp (verifies there's no caching).
 */
if ( $webhookNotification->kind === Braintree_WebhookNotification::CHECK ) {	
	$when = $webhookNotification->timestamp->format( 'Y-m-d H:i:s.u' );
	
	$logstr[] = "Since you are just testing the URL, check that the timestamp updates on refresh to make sure this isn't being cached.";
	$logstr[] = "Braintree gateway timestamp: {$when}";
	pmpro_braintreeWebhookExit();
}

//subscription charged sucessfully
if ( $webhookNotification->kind === Braintree_WebhookNotification::SUBSCRIPTION_CHARGED_SUCCESSFULLY ) {
	$logstr[] = "The Braintree gateway received payment for a recurring billing plan";
	
	//need a subscription id
	if ( empty( $webhookNotification->subscription->id ) ) {
		$logstr[] = "No subscription ID.";
		pmpro_braintreeWebhookExit();
	}
	
	//figure out which order to attach to
	$old_order = new \MemberOrder();
	$old_order->getLastMemberOrderBySubscriptionTransactionID( $webhookNotification->subscription->id );

	//no order?
	if ( empty( $old_order->id ) ) {
		$logstr[] = "Couldn't find the original subscription with ID={$webhookNotification->subscription->id}.";
		pmpro_braintreeWebhookExit();
	}
	
	//create new order
	$user_id                = $old_order->user_id;
	$user                   = get_userdata( $user_id );
	
	if ( empty( $user ) ) {
		$logstr[] = "Couldn't find the old order's user. Order ID = {$old_order->id}.";
		pmpro_braintreeWebhookExit();
	} else {
		$user->membership_level = pmpro_getMembershipLevelForUser( $user_id );
	}
	
	//data about this transaction
	$transaction = $webhookNotification->subscription->transactions[0];

	//log it for debug email
	$logstr[] = var_export( $transaction );

	//alright. create a new order/invoice
	$morder                              = new \MemberOrder();
	$morder->user_id                     = $old_order->user_id;
	$morder->membership_id               = $old_order->membership_id;
	$morder->InitialPayment              = $transaction->amount;    //not the initial payment, but the order class is expecting this
	$morder->PaymentAmount               = $transaction->amount;
	$morder->payment_transaction_id      = $transaction->id;
	$morder->subscription_transaction_id = $webhookNotification->subscription->id;

	//Assume no tax for now. Add ons will handle it later.
	$morder->tax = 0;
	
	$morder->gateway             = $old_order->gateway;
	$morder->gateway_environment = $old_order->gateway_environment;
	
	$morder->billing = new stdClass();
	
	if (! empty( $transaction->billing_details) ) {
		$morder->FirstName = $transaction->billing_details->first_name;
		$morder->LastName  = $transaction->billing_details->last_name;
		$morder->Email     = $wpdb->get_var( "SELECT user_email FROM $wpdb->users WHERE ID = '" . $old_order->user_id . "' LIMIT 1" );
		$morder->Address1  = $transaction->billing_details->street_address;
		$morder->City      = $transaction->billing_details->locality;
		$morder->State     = $transaction->billing_details->region;
		//$morder->CountryCode = $old_order->billing->city;
		$morder->Zip         = $transaction->billing_details->postal_code;
		
		$morder->billing->name    = trim( $transaction->billing_details->first_name . " " . $transaction->billing_details->last_name );
		$morder->billing->street  = $transaction->billing_details->street_address;
		$morder->billing->city    = $transaction->billing_details->locality;
		$morder->billing->state   = $transaction->billing_details->region;
		$morder->billing->zip     = $transaction->billing_details->postal_code;
		$morder->billing->country = $transaction->billing_details->country_code_alpha2;	
	} else {
		$morder->FirstName = $old_order->FirstName;
		$morder->LastName = $old_order->LastName;
		$morder->Email = $user->user_email;
		$morder->Address1 = $old_order->Address1;
		$morder->City = $old_order->billing->city;
		$morder->State = $old_order->billing->state;
		$morder->Zip = $old_order->billing->zip;
		
		$morder->billing->name    = $old_order->billing->name;
		$morder->billing->street  = $old_order->billing->street;
		$morder->billing->city    = $old_order->billing->city;
		$morder->billing->state   = $old_order->billing->state;
		$morder->billing->zip     = $old_order->billing->zip;
		$morder->billing->country = $old_order->billing->country;
	}
	
	$morder->PhoneNumber = $old_order->billing->phone;
	$morder->billing->phone   = $old_order->billing->phone;
	
	//get CC info that is on file
	$morder->cardtype              = get_user_meta( $user_id, "pmpro_CardType", true );
	$morder->accountnumber         = hideCardNumber( get_user_meta( $user_id, "pmpro_AccountNumber", true ), false );
	$morder->expirationmonth       = get_user_meta( $user_id, "pmpro_ExpirationMonth", true );
	$morder->expirationyear        = get_user_meta( $user_id, "pmpro_ExpirationYear", true );
	$morder->ExpirationDate        = $morder->expirationmonth . $morder->expirationyear;
	$morder->ExpirationDate_YdashM = $morder->expirationyear . "-" . $morder->expirationmonth;
	
	//save
	$morder->status = "success";
	$morder->saveOrder();
	$morder->getMemberOrderByID( $morder->id );
	
	//email the user their invoice
	$pmproemail = new \PMProEmail();
	$pmproemail->sendInvoiceEmail( $user, $morder );
	
	do_action( 'pmpro_subscription_payment_completed', $morder );
	
	$logstr[] = "Triggered pmpro_subscription_payment_completed actions and returned";
	
	/**
	 * @since 1.9.5 - Didn't terminate & save debug loggins for Webhook
	 */
	pmpro_braintreeWebhookExit();
}

/*
	Note here: These next three checks all work the same way and send the same
	"billing failed" email, but kick off different actions based on the kind.
*/

//subscription charged unsuccessfully
if ( $webhookNotification->kind === Braintree_WebhookNotification::SUBSCRIPTION_CHARGED_UNSUCCESSFULLY ) {
	$logstr[] = "The Braintree gateway let us know there's a problem with the payment";
	
	//need a subscription id
	if ( empty( $webhookNotification->subscription->id ) ) {
		$logstr[] = "No subscription ID.";
		pmpro_braintreeWebhookExit();
	}
	
	//figure out which order to attach to
	$old_order = new \MemberOrder();
	$old_order->getLastMemberOrderBySubscriptionTransactionID( $webhookNotification->subscription->id );
	
	if ( empty( $old_order ) ) {
		$logstr[] = "Couldn't find old order for failed payment with subscription id={$webhookNotification->subscription->id}";
		pmpro_braintreeWebhookExit();
	}
	
	$user_id                = $old_order->user_id;
	$user                   = get_userdata( $user_id );
	$user->membership_level = pmpro_getMembershipLevelForUser( $user_id );
	
	//generate billing failure email
	do_action( "pmpro_subscription_payment_failed", $old_order );
	
	$transaction = isset( $webhookNotification->transactions ) && is_array( $webhookNotification->transactions ) ?
		$webhookNotification->transactions[0] :
		null;
	
	if ( empty( $transaction ) || ! isset( $transaction->billing_details ) ) {
		// Get billing address info from either old order or billing meta
		$old_order->billing = pmpro_braintreeAddressInfo( $user_id, $old_order );
	}
	
	//prep this order for the failure emails
	$morder          = new \MemberOrder();
	$morder->user_id = $user_id;
	$morder->membership_id = $old_order->membership_id;
	
	$morder->billing = new stdClass();
	$morder->billing->name = isset( $transaction->billing_details->first_name ) && isset( $transaction->billing_details->last_name ) ?
		trim( $transaction->billing_details->first_name . " " . $transaction->billing_details->first_name ) :
		$old_order->billing->name;
	
	$morder->billing->street = isset( $transaction->billing_details->street_address ) ?
		$transaction->billing_details->street_address :
		$old_order->billing->street;
	
	$morder->billing->city = isset( $transaction->billing_details->locality ) ?
		$transaction->billing_details->locality :
		$old_order->billing->city;
	
	$morder->billing->state = isset( $transaction->billing_details->region ) ?
		$transaction->billing_details->region :
		$old_order->billing->state;
	
	$morder->billing->zip = isset( $transaction->billing_details->postal_code ) ?
		$transaction->billing_details->postal_code :
		$old_order->billing->zip;
	
	$morder->billing->country = isset( $transaction->billing_details->country_code_alpha2 ) ?
		$transaction->billing_details->country_code_alpha2 :
		$old_order->billing->country;
	
	$morder->billing->phone = $old_order->billing->phone;
	
	//get CC info that is on file
	$morder->cardtype        = get_user_meta( $user_id, "pmpro_CardType", true );
	$morder->accountnumber   = hideCardNumber( get_user_meta( $user_id, "pmpro_AccountNumber", true ), false );
	$morder->expirationmonth = get_user_meta( $user_id, "pmpro_ExpirationMonth", true );
	$morder->expirationyear  = get_user_meta( $user_id, "pmpro_ExpirationYear", true );
	
	// Email the user and ask them to update their credit card information
	$pmproemail = new \PMProEmail();
	$pmproemail->sendBillingFailureEmail( $user, $morder );
	
	// Email admin so they are aware of the failure
	$pmproemail = new \PMProEmail();
	$pmproemail->sendBillingFailureAdminEmail( get_bloginfo( "admin_email" ), $morder );
	
	$logstr[] = "Sent email to the member and site admin. Thanks.";
	pmpro_braintreeWebhookExit();
}

//subscription went past due
if ( $webhookNotification->kind === Braintree_WebhookNotification::SUBSCRIPTION_WENT_PAST_DUE ) {
	
	$logstr[] = "The Braintree gateway informed us the subscription payment is past due";
	
	//need a subscription id
	if ( empty( $webhookNotification->subscription->id ) ) {
		$logstr[] = "No subscription ID.";
		pmpro_braintreeWebhookExit();
	}
	
	//figure out which order to attach to
	$old_order = new \MemberOrder();
	$old_order->getLastMemberOrderBySubscriptionTransactionID( $webhookNotification->subscription->id );
	
	if ( empty( $old_order ) ) {
		$logstr[] = "Couldn't find old order for failed payment with subscription id=" . $webhookNotification->subscription->id;
		pmpro_braintreeWebhookExit();
	}
	
	$user_id                = $old_order->user_id;
	$user                   = get_userdata( $user_id );
	$user->membership_level = pmpro_getMembershipLevelForUser( $user_id );
	
	//generate billing failure email
	do_action( "pmpro_subscription_payment_failed", $old_order );
	do_action( "pmpro_subscription_payment_went_past_due", $old_order );
	
	$transaction = isset( $webhookNotification->transactions ) && is_array( $webhookNotification->transactions ) ?
		$webhookNotification->transactions[0] :
		null;
	
	if ( empty( $transaction ) || ! isset( $transaction->billing_details ) ) {
		// Get billing address info from either old order or billing meta
		$old_order->billing = pmpro_braintreeAddressInfo( $user_id, $old_order );
	}
	
	//prep this order for the failure emails
	$morder          = new \MemberOrder();
	$morder->user_id = $user_id;
	$morder->membership_id = $old_order->membership_id;
	
	$morder->billing->name = isset( $transaction->billing_details->first_name ) && isset( $transaction->billing_details->last_name ) ?
		trim( $transaction->billing_details->first_name . " " . $transaction->billing_details->first_name ) :
		$old_order->billing->name;
	
	$morder->billing->street = isset( $transaction->billing_details->street_address ) ?
		$transaction->billing_details->street_address :
		$old_order->billing->street;
	
	$morder->billing->city = isset( $transaction->billing_details->locality ) ?
		$transaction->billing_details->locality :
		$old_order->billing->city;
	
	$morder->billing->state = isset( $transaction->billing_details->region ) ?
		$transaction->billing_details->region :
		$old_order->billing->state;
	
	$morder->billing->zip = isset( $transaction->billing_details->postal_code ) ?
		$transaction->billing_details->postal_code :
		$old_order->billing->zip;
	
	$morder->billing->country = isset( $transaction->billing_details->country_code_alpha2 ) ?
		$transaction->billing_details->country_code_alpha2 :
		$old_order->billing->country;
	
	$morder->billing->phone = $old_order->billing->phone;
	
	//get CC info that is on file
	$morder->cardtype        = get_user_meta( $user_id, "pmpro_CardType", true );
	$morder->accountnumber   = hideCardNumber( get_user_meta( $user_id, "pmpro_AccountNumber", true ), false );
	$morder->expirationmonth = get_user_meta( $user_id, "pmpro_ExpirationMonth", true );
	$morder->expirationyear  = get_user_meta( $user_id, "pmpro_ExpirationYear", true );
	
	// Email the user and ask them to update their credit card information
	$pmproemail = new \PMProEmail();
	$pmproemail->sendBillingFailureEmail( $user, $morder );
	
	// Email admin so they are aware of the failure
	$pmproemail = new \PMProEmail();
	$pmproemail->sendBillingFailureAdminEmail( get_bloginfo( "admin_email" ), $morder );
	
	$logstr[] = "Sent email to the member and site admin. Thanks.";
	pmpro_braintreeWebhookExit();
}

//subscription expired
if ( $webhookNotification->kind === Braintree_WebhookNotification::SUBSCRIPTION_EXPIRED ) {
	$logstr[] = "The Braintree gateway informed us the recurring payment plan has completed its required number of payments";
	
	//need a subscription id
	if ( empty( $webhookNotification->subscription->id ) ) {
		$logstr[] = "No subscription ID.";
		pmpro_braintreeWebhookExit();
	}
	
	//figure out which order to attach to
	$old_order = new \MemberOrder();
	$old_order->getLastMemberOrderBySubscriptionTransactionID( $webhookNotification->subscription->id );
	
	if ( empty( $old_order ) ) {
		$logstr[] = "Couldn't find old order for failed payment with subscription id=" . $webhookNotification->subscription->id;
		pmpro_braintreeWebhookExit();
	}
	
	$user_id                = $old_order->user_id;
	$user                   = get_userdata( $user_id );
	$user->membership_level = pmpro_getMembershipLevelForUser( $user_id );
	
	//generate billing failure email
	do_action( "pmpro_subscription_expired", $old_order );
	
	$transaction = isset( $webhookNotification->transactions ) && is_array( $webhookNotification->transactions ) ?
		$webhookNotification->transactions[0] :
		null;
	
	if ( empty( $transaction ) || ! isset( $transaction->billing_details ) ) {
		// Get billing address info from either old order or billing meta
		$old_order->billing = pmpro_braintreeAddressInfo( $user_id, $old_order );
	}
	
	// We don't currently allow billing limits (number_of_billing_cycles) on Braintree subscriptions.
	// But in case we get here, let's send the correct email to the admin.	
	$myemail = new PMProEmail();
	$body = sprintf( __( "<p>A member's Braintree subscription has expired at your site. This typically happens if you've set up billing limits on your levels.</p><p>We have not removed the user's membership level.</p><p>You can view details on this user here: %s</p>", 'paid-memberships-pro' ), esc_url( admin_url( 'user-edit.php?user_id=' . $user_id ) ) );	
	$myemail->template = 'braintree_subscription_expired';
	$myemail->subject = sprintf( __( "A member's Braintree subscription has expired at %s", 'paid-memberships-pro' ), get_bloginfo( 'name' ) );
	$myemail->data = array( 'body' => $body );
	$myemail->sendEmail( get_bloginfo( 'admin_email' ) );
	
	$logstr[] = "Sent email to the site admin. Thanks.";
	pmpro_braintreeWebhookExit();
}

//subscription cancelled (they used one l canceled)
if ( $webhookNotification->kind === Braintree_WebhookNotification::SUBSCRIPTION_CANCELED ) {
	
	$logstr[] = "The Braintree gateway cancelled the subscription plan";
	
	//need a subscription id
	if ( empty( $webhookNotification->subscription->id ) ) {
		$logstr[] = "No subscription ID.";
		pmpro_braintreeWebhookExit();
	}
	
	//figure out which order to attach to
	$old_order = new \MemberOrder();
	$old_order->getLastMemberOrderBySubscriptionTransactionID( $webhookNotification->subscription->id );
	
	if ( empty( $old_order ) ) {
		$logstr[] = "Couldn't find old order for failed payment with subscription id={$webhookNotification->subscription->id}";
		pmpro_braintreeWebhookExit();
	}
	
	/**
	 * @since v1.9.5+ - BUG FIX: Don't process previously handled subscription cancellation
	 */
	if ( isset( $old_order->status ) && 'cancelled' == $old_order->status ) {
		$logstr[] = "Order for subscription id {$webhookNotification->subscription->id} is cancelled already";
		pmpro_braintreeWebhookExit();
	}
	
	$user_id                = $old_order->user_id;
	$user                   = get_userdata( $user_id );
	$user->membership_level = pmpro_getMembershipLevelForUser( $user_id,true );
	
	/**
	 * @since 1.9.5 - BUG FIX: Erroneously triggering warning email
	 *                Happens when a user cancels their membership (and Braintree) sends a webhook notifying us
	 *                of the fact that the user cancelled their subscription plan (SUBSCRIPTION_CANCELED event).
	 */
	if ( empty( $user->membership_level ) ) {
		
		$logstr[] = "Membership for user (ID: {$user_id}) is cancelled already. Probably a duplicate webhook notification. Exiting!";
		pmpro_braintreeWebhookExit();
	}
	
	// Trigger subscription cancelled action
	do_action( "pmpro_subscription_cancelled", $old_order );
	
	$transaction = isset( $webhookNotification->transactions ) && is_array( $webhookNotification->transactions ) ?
		$webhookNotification->transactions[0] :
		null;
	
	if ( empty( $transaction ) || ! isset( $transaction->billing_details ) ) {
		// Get billing address info from either old order or billing meta
		$old_order->billing = pmpro_braintreeAddressInfo( $user_id, $old_order );
	}
	
	// Cancel the related membership.
	pmpro_cancelMembershipLevel( $old_order->membership_id, $old_order->user_id, 'cancelled' );
	
	$logstr[] = "Cancelled membership for user with id = {$old_order->user_id}. Subscription transaction id = {$old_order->subscription_transaction_id}.\n";
	
	// Send an email to the member.
	$myemail = new PMProEmail();
	$myemail->sendCancelEmail( $user, $old_order->membership_id );
	
	// Send an email to the admin.
	$myemail = new PMProEmail();
	$myemail->sendCancelAdminEmail( $user, $old_order->membership_id );
	
	pmpro_braintreeWebhookExit();
}

/**
 * @since 1.9.5 - BUG FIX: Didn't terminate & save debug log for webhook event
 */
pmpro_braintreeWebhookExit();

/**
 * Fix address info for order/transaction
 *
 * @param int          $user_id
 * @param \MemberOrder $old_order
 *
 * @return \stdClass
 */
function pmpro_braintreeAddressInfo( $user_id, $old_order ) {
	
	
	// Grab billing info from the saved metadata as needed
	
	if ( ! isset( $old_order->billing ) ) {
		$old_order->billing = new \stdClass();
	}
	
	if ( empty ( $old_order->billing->name ) ) {
		$first_name = get_user_meta( $user_id, 'pmpro_bfirstname', true );
		$last_name  = get_user_meta( $user_id, 'pmpro_blastname', true );
		
		if ( ! empty( $first_name ) && ! empty( $last_name ) ) {
			$old_order->billing->name = trim( "{$first_name} {$last_name}" );
		}
	}
	
	if ( empty( $old_order->billing->street ) ) {
		$address1                   = get_user_meta( $user_id, 'pmpro_baddress', true );
		$address2                   = get_user_meta( $user_id, 'pmpro_baddress2', true );
		$old_order->billing->street = ! empty( $address1 ) ? trim( $address1 ) : '';
		$old_order->billing->street .= ! empty( $address2 ) ? "\n" . trim( $address2 ) : '';
	}
	
	if ( empty( $old_order->billing->city ) ) {
		$city                     = get_user_meta( $user_id, 'pmpro_bcity', true );
		$old_order->billing->city = ! empty( $city ) ? trim( $city ) : '';
	}
	
	if ( empty( $old_order->billing->state ) ) {
		$state                     = get_user_meta( $user_id, 'pmpro_bstate', true );
		$old_order->billing->state = ! empty( $state ) ? trim( $state ) : '';
	}
	
	if ( empty( $old_order->billing->zip ) ) {
		$zip                     = get_user_meta( $user_id, 'pmpro_bzipcode', true );
		$old_order->billing->zip = ! empty( $zip ) ? trim( $zip ) : '';
	}
	
	if ( empty( $old_order->billing->country ) ) {
		$country                     = get_user_meta( $user_id, 'pmpro_bcountry', true );
		$old_order->billing->country = ! empty( $country ) ? trim( $country ) : '';
	}
	
	$old_order->updateBilling();
	
	return $old_order->billing;
}

/**
 * Exit the Webhook handler, and save the debug log (if needed)
 */
function pmpro_braintreeWebhookExit() {
	
	global $logstr;
	
	//Log the info (if there is any)
	if ( ! empty( $logstr ) ) {
		
		$logstr[] = "\n-------------\n";
		
		$debuglog = implode( "\n", $logstr );
		
		//log in file or email?
		if ( defined( 'PMPRO_BRAINTREE_WEBHOOK_DEBUG' ) && PMPRO_BRAINTREE_WEBHOOK_DEBUG === "log" ) {
			//file
			$loghandle = fopen( dirname( __FILE__ ) . "/../logs/braintree-webhook.txt", "a+" );
			fwrite( $loghandle, $debuglog );
			fclose( $loghandle );
		} else if ( defined( 'PMPRO_BRAINTREE_WEBHOOK_DEBUG' ) ) {
			/**
			 * @since 1.9.5 - BUG FIX: We specifically care about errors, not strings at position 0
			 */
			//email
			if ( false !== strpos( PMPRO_BRAINTREE_WEBHOOK_DEBUG, "@" ) ) {
				$log_email = PMPRO_BRAINTREE_WEBHOOK_DEBUG;    //constant defines a specific email address
			} else {
				$log_email = get_option( "admin_email" );
			}
			
			wp_mail( $log_email, get_option( "blogname" ) . " Braintree Webhook Log", nl2br( esc_html( $debuglog ) ) );
		}
	}
	
	exit;
}
