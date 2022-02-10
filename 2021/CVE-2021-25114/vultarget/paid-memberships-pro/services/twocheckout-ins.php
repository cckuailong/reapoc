<?php
	//set this in your wp-config.php for debugging
	//define('PMPRO_INS_DEBUG', true);

	//in case the file is loaded directly
	if(!defined("ABSPATH"))
	{
		global $isapage;
		$isapage = true;

		define('WP_USE_THEMES', false);
		require_once(dirname(__FILE__) . '/../../../../wp-load.php');
	}

	// Require TwoCheckout class
	if(!class_exists("Twocheckout"))
		require_once(PMPRO_DIR . "/includes/lib/Twocheckout/Twocheckout.php");

	//some globals
	global $wpdb, $gateway_environment, $logstr;
	$logstr = "";	//will put debug info here and write to inslog.txt

	// Sets the PMPRO_DOING_WEBHOOK constant and fires the pmpro_doing_webhook action.
	pmpro_doing_webhook( 'twocheckout', true );

	//validate?
	if( ! pmpro_twocheckoutValidate() ) {

		inslog("(!!FAILED VALIDATION!!)");

		//validation failed
		pmpro_twocheckoutExit();
	}

	//assign posted variables to local variables
	$message_type = pmpro_getParam( 'message_type', 'REQUEST' );
	$md5_hash = pmpro_getParam( 'md5_hash', 'REQUEST' );
	$txn_id = pmpro_getParam( 'sale_id', 'REQUEST' );
	$recurring = pmpro_getParam( 'recurring', 'REQUEST' );
	$order_id = pmpro_getParam( 'merchant_order_id', 'REQUEST' );
	$order_number = pmpro_getParam( 'order_number', 'REQUEST' );
	if(empty($order_id))
		$order_id = pmpro_getParam( 'vendor_order_id', 'REQUEST' );
	$product_id = pmpro_getParam( 'item_id_1', 'REQUEST' ); // Should be item 0 or 1?
	if(empty($order_id))
		$order_id = $product_id;
	$invoice_status = pmpro_getParam( 'invoice_status', 'REQUEST' ); // On single we need to check for deposited
	$fraud_status = pmpro_getParam( 'fraud_status', 'REQUEST' ); // Check fraud status?
	$invoice_list_amount = pmpro_getParam( 'invoice_list_amount', 'REQUEST' ); // Price paid by customer in seller currency code
	$customer_email = pmpro_getParam( 'customer_email', 'REQUEST', '', 'sanitize_email' );

	// No message = return processing
	if( empty($message_type) ) {
		//initial payment, get the order
		$morder = new MemberOrder( $order_id );
		$morder->getMembershipLevel();
		$morder->getUser();

		inslog("NO MESSAGE: ORDER: " . var_export($morder, true) . "\n---\n");

		//update membership
		if( ! empty ( $morder ) && ! empty ( $morder->status ) && $morder->status === 'success' ) {
			inslog( "Checkout was already processed (" . $morder->code . "). Ignoring this request." );
		}
		elseif (pmpro_insChangeMembershipLevel( $order_number, $morder ) ) {
			inslog( "Checkout processed (" . $morder->code . ") success!" );
		}
		else {
			inslog( "ERROR: Couldn't change level for order (" . $morder->code . ")." );
		}

		pmpro_twocheckoutExit(pmpro_url("confirmation", "?level=" . $morder->membership_level->id));
	}

	// First Payment (checkout) (Will probably want to update order, but not send another email/etc)
	if( $message_type == 'ORDER_CREATED' ) {
		//initial payment, get the order
		$morder = new MemberOrder( $order_id );
		$morder->getMembershipLevel();
		$morder->getUser();

		inslog("ORDER_CREATED: ORDER: " . var_export($morder, true) . "\n---\n");

		//update membership
		if( ! empty ( $morder ) && ! empty ( $morder->status ) && $morder->status === 'success' ) {
			inslog( "Checkout was already processed (" . $morder->code . "). Ignoring this request." );
		}
		elseif (pmpro_insChangeMembershipLevel( $txn_id, $morder ) ) {
			inslog( "Checkout processed (" . $morder->code . ") success!" );

		}
		else {
			inslog( "ERROR: Couldn't change level for order (" . $morder->code . ")." );
		}

		pmpro_twocheckoutExit(pmpro_url("confirmation", "?level=" . $morder->membership_level->id));
	}

	// Recurring Payment Success (recurring installment success and recurring is true)
	if( $message_type == 'RECURRING_INSTALLMENT_SUCCESS' ) {
		//is this a first payment?
		$last_subscr_order = new MemberOrder();
		if( $last_subscr_order->getLastMemberOrderBySubscriptionTransactionID( $txn_id ) == false) {
			//first payment, get order
			$morder = new MemberOrder( $order_id );
			$morder->getMembershipLevel();
			$morder->getUser();

			//update membership
			if( pmpro_insChangeMembershipLevel( $txn_id, $morder ) ) {
				inslog( "Checkout processed (" . $morder->code . ") success!" );

				//hook for successful subscription payments
				do_action("pmpro_subscription_payment_completed", $morder);

			}
			else {
				inslog( "ERROR: Couldn't change level for order (" . $morder->code . ")." );
			}
		}
		else {
			pmpro_insSaveOrder( $txn_id, $last_subscr_order );
		}

		pmpro_twocheckoutExit();
	}

	// Recurring Payment Failed (recurring installment failed and recurring is true)
	if( $message_type == 'RECURRING_INSTALLMENT_FAILED' && $recurring ) {
		//is this a first payment?
		$last_subscr_order = new MemberOrder();
		$last_subscr_order->getLastMemberOrderBySubscriptionTransactionID( $txn_id );
		pmpro_insFailedPayment( $last_subscr_order );

		pmpro_twocheckoutExit();
	}


	/*
	// Recurring Payment Stopped (recurring stopped and recurring is true)
	if( $message_type == 'RECURRING_STOPPED' && $recurring ) {
		//initial payment, get the order
		$morder = new MemberOrder( $product_id );
		$morder->getMembershipLevel();
		$morder->getUser();

		// stop membership
		if ( pmpro_insRecurringStopped( $morder ) ) {
			inslog( "Recurring stopped for order (" . $morder->code . ")!" );
		}
		else {
			inslog( "Recurring NOT stopped for order (" . $morder->code . ")!" );
		}

		pmpro_twocheckoutExit();
	}


	// Recurring Payment Restarted (recurring restarted and recurring is true)
	if( $message_type == 'RECURRING_RESTART' && $recurring ) {
		//initial payment, get the order
		$morder = new MemberOrder( $product_id );
		$morder->getMembershipLevel();
		$morder->getUser();

		// stop membership
		if ( pmpro_insRecurringRestarted( $morder ) ) {
			inslog( "Recurring restarted for order (" . $morder->code . ")!" );
		}
		else {
			inslog( "Recurring NOT restarted for order (" . $morder->code . ")!" );
		}

		pmpro_twocheckoutExit();
	}
	*/

	//Other
	//if we got here, this is a different kind of txn
	inslog("The PMPro INS handler does not process this type of message. message_type = " . $message_type);
	pmpro_twocheckoutExit();

	/*
		Add message to inslog string
	*/
	function inslog( $s )
	{
		global $logstr;
		$logstr .= "\t" . $s . "\n";
	}

	/*
		Output inslog and exit;
	*/
	function pmpro_twocheckoutExit($redirect = false)
	{
		global $logstr;
		//echo esc_html( $logstr );

		$logstr = var_export($_REQUEST, true) . "Logged On: " . date_i18n("m/d/Y H:i:s") . "\n" . $logstr . "\n-------------\n";

		//log in file or email?
		if(defined('PMPRO_INS_DEBUG') && PMPRO_INS_DEBUG === "log")
		{
			//file
			$loghandle = fopen(dirname(__FILE__) . "/../logs/ipn.txt", "a+");
			fwrite($loghandle, $logstr);
			fclose($loghandle);
		}
		elseif(defined('PMPRO_INS_DEBUG'))
		{
			//email
			if(strpos(PMPRO_INS_DEBUG, "@"))
				$log_email = PMPRO_INS_DEBUG;	//constant defines a specific email address
			else
				$log_email = get_option("admin_email");

			wp_mail( $log_email, get_option( "blogname" ) . " 2Checkout INS Log", nl2br( esc_html( $logstr ) ) );
		}

		if(!empty($redirect))
			wp_redirect($redirect);

		exit;
	}

	/*
		Validate the $_POST with TwoCheckout
	*/
	function pmpro_twocheckoutValidate() {
		$params = array();
		foreach ( $_REQUEST as $k => $v )
			$params[$k] = $v;

		//2Checkout uses an order number of 1 in the hash for demo orders for some reason
		if(!empty($params['demo']) && $params['demo'] == 'Y')
			$params['order_number'] = 1;

		//is this a return call or notification
		if(empty($params['message_type']))
			$check = Twocheckout_Return::check( $params, pmpro_getOption( 'twocheckout_secretword' ) );
		else
			$check = Twocheckout_Notification::check( $params, pmpro_getOption( 'twocheckout_secretword' ) );

		if( empty ( $check ) )
			$r = false;	//HTTP failure
		else if( empty ( $check['response_code'] ) )
			$r = false;	//Invalid response
		else
			$r = $check['response_code'] === 'Success';

		/**
		 * Filter if an twocheckout request is valid or not.
		 *
		 * @since 1.8.6.3
		 *
		 * @param bool $r true or false if the request is valid
		 * @param mixed $check remote post object from request to Twocheckout
		 */
		$r = apply_filters('pmpro_twocheckout_validate', $r, $check);

		return $r;
	}

	/*
		Change the membership level. We also update the membership order to include filtered valus.
	*/
	function pmpro_insChangeMembershipLevel($txn_id, &$morder)
	{
		global $wpdb;
		$recurring = pmpro_getParam( 'recurring', 'POST' );

		// Get discount code.
		$morder->getDiscountCode();
		if ( ! empty( $morder->discount_code ) ) {
			// Update membership level
			$morder->getMembershipLevel(true);
			$discount_code_id = $morder->discount_code->id;
		} else {
			$discount_code_id = "";
		}

		// If this is an initial payment...
		if ( empty( pmpro_getParam( 'message_type', 'REQUEST' ) ) || pmpro_getParam( 'message_type', 'REQUEST' ) === 'ORDER_CREATED' ) {
			// Apply discount code level changes.
			if ( ! empty( $discount_code_id ) ) {
				$sqlQuery                 = "SELECT l.id, cl.*, l.name, l.description, l.allow_signups, l.confirmation FROM $wpdb->pmpro_discount_codes_levels cl LEFT JOIN $wpdb->pmpro_membership_levels l ON cl.level_id = l.id LEFT JOIN $wpdb->pmpro_discount_codes dc ON dc.id = cl.code_id WHERE dc.id = '" . esc_sql( $discount_code_id ) . "' AND cl.level_id = '" . esc_sql( $morder->membership_level->level_id ) . "' LIMIT 1";
				$morder->membership_level = $wpdb->get_row( $sqlQuery );
			}
	
			// Extend membership if renewal.
			// Added manually because pmpro_checkout_level filter is not run.
			$morder->membership_level = pmpro_checkout_level_extend_memberships( $morder->membership_level );
		}

		//filter for level
		$morder->membership_level = apply_filters("pmpro_inshandler_level", $morder->membership_level, $morder->user_id);

		//set the start date to current_time('mysql') but allow filters (documented in preheaders/checkout.php)
		$startdate = apply_filters("pmpro_checkout_start_date", "'" . current_time('mysql') . "'", $morder->user_id, $morder->membership_level);
		
		//fix expiration date
		if(!empty($morder->membership_level->expiration_number))
		{
			$enddate = "'" . date_i18n("Y-m-d", strtotime("+ " . $morder->membership_level->expiration_number . " " . $morder->membership_level->expiration_period, current_time("timestamp"))) . "'";
		}
		else
		{
			$enddate = "NULL";
		}

		//filter the enddate (documented in preheaders/checkout.php)
		$enddate = apply_filters("pmpro_checkout_end_date", $enddate, $morder->user_id, $morder->membership_level, $startdate);

		//custom level to change user to
		$custom_level = array(
			'user_id' => $morder->user_id,
			'membership_id' => $morder->membership_level->id,
			'code_id' => $discount_code_id,
			'initial_payment' => $morder->membership_level->initial_payment,
			'billing_amount' => $morder->membership_level->billing_amount,
			'cycle_number' => $morder->membership_level->cycle_number,
			'cycle_period' => $morder->membership_level->cycle_period,
			'billing_limit' => $morder->membership_level->billing_limit,
			'trial_amount' => $morder->membership_level->trial_amount,
			'trial_limit' => $morder->membership_level->trial_limit,
			'startdate' => $startdate,
			'enddate' => $enddate);

		global $pmpro_error;
		if(!empty($pmpro_error))
		{
			echo esc_html( $pmpro_error );
			inslog($pmpro_error);
		}

		if( pmpro_changeMembershipLevel($custom_level, $morder->user_id) !== false ) {
			//update order status and transaction ids
			$morder->status = "success";
			$morder->payment_transaction_id = $txn_id;
			if( $recurring )
				$morder->subscription_transaction_id = $txn_id;
			else
				$morder->subscription_transaction_id = '';
			$morder->saveOrder();

			//add discount code use
			if(!empty($discount_code) && !empty($use_discount_code))
			{
				$wpdb->query("INSERT INTO $wpdb->pmpro_discount_codes_uses (code_id, user_id, order_id, timestamp) VALUES('" . esc_sql( $discount_code_id ) . "', '" . esc_sql( $morder->user_id ) . "', '" . esc_sql( $morder->id ) . "', '" . current_time('mysql') . "')");
			}

			//save first and last name fields
			if(!empty($_POST['first_name']))
			{
				$old_firstname = get_user_meta($morder->user_id, "first_name", true);
				if(!empty($old_firstname))
					update_user_meta($morder->user_id, "first_name", sanitize_text_field($_POST['first_name']));
			}
			if(!empty($_POST['last_name']))
			{
				$old_lastname = get_user_meta($morder->user_id, "last_name", true);
				if(!empty($old_lastname))
					update_user_meta($morder->user_id, "last_name", sanitize_text_field($_POST['last_name']));
			}

			//hook
			do_action("pmpro_after_checkout", $morder->user_id, $morder);

			//setup some values for the emails
			if(!empty($morder))
				$invoice = new MemberOrder($morder->id);
			else
				$invoice = NULL;

			inslog("CHANGEMEMBERSHIPLEVEL: ORDER: " . var_export($morder, true) . "\n---\n");

			$user = get_userdata($morder->user_id);
			if(empty($user))
				return false;

			$user->membership_level = $morder->membership_level;		//make sure they have the right level info

			//send email to member
			$pmproemail = new PMProEmail();
			$pmproemail->sendCheckoutEmail($user, $invoice);

			//send email to admin
			$pmproemail = new PMProEmail();
			$pmproemail->sendCheckoutAdminEmail($user, $invoice);

			return true;
		}
		else
			return false;
	}

	/*
		Send an email RE a failed payment.
		$last_order passed in is the previous order for this subscription.
	*/
	function pmpro_insFailedPayment( $last_order ) {
		//hook to do other stuff when payments fail
		do_action("pmpro_subscription_payment_failed", $last_order);

		//create a blank order for the email
		$morder = new MemberOrder();
		$morder->user_id = $last_order->user_id;

		// Email the user and ask them to update their credit card information
		$pmproemail = new PMProEmail();
		$pmproemail->sendBillingFailureEmail($user, $morder);

		// Email admin so they are aware of the failure
		$pmproemail = new PMProEmail();
		$pmproemail->sendBillingFailureAdminEmail(get_bloginfo("admin_email"), $morder);

		inslog("Payment failed. Emails sent to " . $user->user_email . " and " . get_bloginfo("admin_email") . ".");

		return true;
	}

	/*
		Save a new order from IPN info.
		$last_order passed in is the previous order for this subscription.
	*/
	function pmpro_insSaveOrder( $txn_id, $last_order ) {
		global $wpdb;

		//check that txn_id has not been previously processed
		$old_txn = $wpdb->get_var("SELECT payment_transaction_id FROM $wpdb->pmpro_membership_orders WHERE payment_transaction_id = '" . esc_sql( $txn_id ) . "' LIMIT 1");

		if( empty( $old_txn ) ) {

			//save order
			$morder = new MemberOrder();
			$morder->user_id = $last_order->user_id;
			$morder->membership_id = $last_order->membership_id;
			$morder->payment_transaction_id = $txn_id;
			$morder->subscription_transaction_id = $last_order->subscription_transaction_id;
			$morder->InitialPayment = sanitize_text_field($_POST['item_list_amount_1']);	//not the initial payment, but the class is expecting that
			$morder->PaymentAmount = sanitize_text_field($_POST['item_list_amount_1']);
			$morder->datetime = sanitize_text_field($_POST['timestamp']);

			//Assume no tax for now. Add ons will handle it later.
			$morder->tax = 0;

			$morder->FirstName = sanitize_text_field($_POST['customer_first_name']);
			$morder->LastName = sanitize_text_field($_POST['customer_last_name']);
			$morder->Email = sanitize_text_field($_POST['customer_email']);

			$morder->gateway = $last_order->gateway;
			$morder->gateway_environment = $last_order->gateway_environment;

			//save
			$morder->saveOrder();
			$morder->getMemberOrderByID( $morder->id );

			//email the user their invoice
			$pmproemail = new PMProEmail();
			$pmproemail->sendInvoiceEmail( get_userdata( $last_order->user_id ), $morder );

			inslog( "New order (" . $morder->code . ") created." );

			return true;
		}
		else {
			inslog( "Duplicate Transaction ID: " . $txn_id );
			return false;
		}
	}


	/*
		Cancel a subscription and send an email RE a recurring.
		$morder passed in is the previous order for this subscription.
	*/
	function pmpro_insRecurringStopped( $morder ) {
		global $pmpro_error;
		//hook to do other stuff when payments stop		
		do_action( 'pmpro_subscription_recurring_stopped', $morder );
    do_action( 'pmpro_subscription_recuring_stopped', $morder );    // Keeping the mispelled version in case. Will deprecate.
    
		$worked = pmpro_changeMembershipLevel( false, $morder->user->ID , 'inactive');
		if( $worked === true ) {
			//$pmpro_msg = __("Your membership has been cancelled.", 'paid-memberships-pro' );
			//$pmpro_msgt = "pmpro_success";

			//send an email to the member
			$myemail = new PMProEmail();
			$myemail->sendCancelEmail();

			//send an email to the admin
			$myemail = new PMProEmail();
			$myemail->sendCancelAdminEmail( $morder->user, $morder->membership_level->id );

			inslog("Subscription cancelled due to 'recurring stopped' INS notification.");

			return true;
		}
		else {
			return false;
		}
	}


	/*
		Restart a subscription and send an email RE a recurring.
		$morder passed in is the previous order for this subscription.
	*/
	function pmpro_insRecurringRestarted( $morder ) {
		global $pmpro_error;
		//hook to do other stuff when payments restart
		do_action( 'pmpro_subscription_recuring_restarted', $morder);
		do_action( 'pmpro_subscription_recurring_restarted', $morder);

		$worked = pmpro_changeMembershipLevel( $morder->membership_level->id, $morder->user->ID );
		if( $worked === true ) {
			//$pmpro_msg = __("Your membership has been cancelled.", 'paid-memberships-pro' );
			//$pmpro_msgt = "pmpro_success";

			//send an email to the member
			$pmproemail = new PMProEmail();
			$pmproemail->sendCheckoutEmail( $morder->user, $morder );

			//send email to admin
			$pmproemail = new PMProEmail();
			$pmproemail->sendCheckoutAdminEmail( $morder->user, $morder );

			inslog("Subscription restarted due to 'recurring restarted' INS notification.");

			return true;
		}
		else {
			return false;
		}
	}
