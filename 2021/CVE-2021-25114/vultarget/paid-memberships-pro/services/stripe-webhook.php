<?php
	if ( version_compare( PHP_VERSION, '5.3.29', '<' )) {
		return;
	}

	// For compatibility with old library (Namespace Alias)
	use Stripe\Invoice as Stripe_Invoice;
	use Stripe\Event as Stripe_Event;
	use Stripe\PaymentIntent as Stripe_PaymentIntent;
	use Stripe\Charge as Stripe_Charge;

	global $isapage;
	$isapage = true;

	global $logstr;
	$logstr = "";

	// Sets the PMPRO_DOING_WEBHOOK constant and fires the pmpro_doing_webhook action.
	pmpro_doing_webhook( 'stripe', true );

	//you can define a different # of seconds (define PMPRO_STRIPE_WEBHOOK_DELAY in your wp-config.php) if you need this webhook to delay more or less
	if(!defined('PMPRO_STRIPE_WEBHOOK_DELAY'))
		define('PMPRO_STRIPE_WEBHOOK_DELAY', 2);

	//in case the file is loaded directly
	if(!defined("ABSPATH"))
	{
		define('WP_USE_THEMES', false);
		require_once(dirname(__FILE__) . '/../../../../wp-load.php');
	}

	if(!class_exists("Stripe\Stripe")) {
		require_once( PMPRO_DIR . "/includes/lib/Stripe/init.php" );
	}

	// retrieve the request's body and parse it as JSON
	if(empty($_REQUEST['event_id']))
	{
		$body = @file_get_contents('php://input');
		$post_event = json_decode($body);

		//get the id
		if ( ! empty( $post_event ) ) {
			$event_id = sanitize_text_field($post_event->id);
			$livemode = ! empty( $post_event->livemode );
		}
	}
	else
	{
		$event_id = sanitize_text_field($_REQUEST['event_id']);
		$livemode = pmpro_getOption( 'gateway_environment' ) === 'live'; // User is testing, so use current environment.
	}

	try {
		if ( PMProGateway_stripe::using_legacy_keys() ) {
			$secret_key = pmpro_getOption( "stripe_secretkey" );
		} elseif ( $livemode ) {
			$secret_key = pmpro_getOption( 'live_stripe_connect_secretkey' );
		} else {
			$secret_key = pmpro_getOption( 'sandbox_stripe_connect_secretkey' );
		}
		Stripe\Stripe::setApiKey( $secret_key );
	} catch ( Exception $e ) {
		$logstr .= "Unable to set API key for Stripe gateway: " . $e->getMessage();
		pmpro_stripeWebhookExit();
	}

	//get the event through the API now
	if(!empty($event_id))
	{
		try
		{
			global $pmpro_stripe_event;
			$pmpro_stripe_event = Stripe_Event::retrieve($event_id);
		}
		catch(Exception $e)
		{
			$logstr .= "Could not find an event with ID #" . $event_id . ". " . $e->getMessage();
			// pmpro_stripeWebhookExit();
			$pmpro_stripe_event = $post_event;			//for testing you may want to assume that the passed in event is legit
		}
	}

	global $wpdb;

	//real event?
	if(!empty($pmpro_stripe_event->id))
	{
		// Send a 200 HTTP response to Stripe to avoid timeout.
		pmpro_send_200_http_response();

		// Log that we have successfully received a webhook from Stripe.
		update_option( 'pmpro_stripe_last_webhook_received_' . ( $livemode ? 'live' : 'sandbox' ), date( 'Y-m-d H:i:s' ) );

		//check what kind of event it is
		if($pmpro_stripe_event->type == "invoice.payment_succeeded")
		{
			if($pmpro_stripe_event->data->object->amount_due > 0)
			{
				//do we have this order yet? (check status too)
				$order = getOrderFromInvoiceEvent($pmpro_stripe_event);

				//no? create it
				if(empty($order->id))
				{				
					//last order for this subscription //getOldOrderFromInvoiceEvent($pmpro_stripe_event);
					$old_order = new MemberOrder();
					$old_order->getLastMemberOrderBySubscriptionTransactionID($pmpro_stripe_event->data->object->subscription);
					
					//lookup by customer id
					if(empty($old_order) || empty($old_order->id))
					{
						$old_order->getLastMemberOrderBySubscriptionTransactionID($pmpro_stripe_event->data->object->customer);
					}
					
					//still can't find the order
					if(empty($old_order) || empty($old_order->id))
					{
						$logstr .= "Couldn't find the original subscription.";
						pmpro_stripeWebhookExit();
					}

					$user_id = $old_order->user_id;
					$user = get_userdata($user_id);
					$user->membership_level = pmpro_getMembershipLevelForUser($user_id);

					if(empty($user))
					{
						$logstr .= "Couldn't find the old order's user. Order ID = " . $old_order->id . ".";
						pmpro_stripeWebhookExit();
					}

					$invoice = $pmpro_stripe_event->data->object;

					//alright. create a new order/invoice
					$morder = new MemberOrder();
					$morder->user_id = $old_order->user_id;
					$morder->membership_id = $old_order->membership_id;
					$morder->timestamp = $invoice->created;
					
					global $pmpro_currency;
					global $pmpro_currencies;
					
					$currency_unit_multiplier = 100; // 100 cents / USD

					//account for zero-decimal currencies like the Japanese Yen
					if(is_array($pmpro_currencies[$pmpro_currency]) && isset($pmpro_currencies[$pmpro_currency]['decimals']) && $pmpro_currencies[$pmpro_currency]['decimals'] == 0)
						$currency_unit_multiplier = 1;
					
					if(isset($invoice->amount))
					{
						$morder->subtotal = $invoice->amount / $currency_unit_multiplier;
						$morder->tax = 0;
					}
					elseif(isset($invoice->subtotal))
					{
						$morder->subtotal = (! empty( $invoice->subtotal ) ? $invoice->subtotal / $currency_unit_multiplier : 0);
						$morder->tax = (! empty($invoice->tax) ? $invoice->tax / $currency_unit_multiplier : 0);
						$morder->total = (! empty($invoice->total) ? $invoice->total / $currency_unit_multiplier : 0);
					}

					$morder->payment_transaction_id = $invoice->id;
					$morder->subscription_transaction_id = $invoice->subscription;

					$morder->gateway = $old_order->gateway;
					$morder->gateway_environment = $old_order->gateway_environment;

					$charge = Stripe_Charge::retrieve( $pmpro_stripe_event->data->object->charge );
					if ( ! empty ( $charge->billing_details->address->line1 ) ) {
						// Get order billing details from Stripe.
						$morder->billing = $charge->billing_details->address;
						$morder->billing->name = $charge->billing_details->name; // Add name.
						$morder->billing->phone = $charge->billing_details->phone; // Add phone.
						$morder->billing->zip = $morder->billing->postal_code; // Fix zip.
						$morder->billing->street = $morder->billing->line1; // Fix street. 

						$nameparts = pnp_split_full_name( $morder->billing->name );
						$morder->FirstName = empty( $nameparts['fname'] ) ? '' : $nameparts['fname'];
						$morder->LastName = empty( $nameparts['lname'] ) ? '' : $nameparts['lname'];
						$morder->Email = $wpdb->get_var("SELECT user_email FROM $wpdb->users WHERE ID = '" . $old_order->user_id . "' LIMIT 1");
						$morder->Address1 = $morder->billing->street;
						$morder->City = $morder->billing->city;
						$morder->State = $morder->billing->state;
						$morder->Zip = $morder->billing->zip;
						$morder->PhoneNumber = $morder->billing->phone;
					} else {
						// Pull from previous order.
						$morder->find_billing_address();
					}

					//get CC info that is on file
					$morder->cardtype = get_user_meta($user_id, "pmpro_CardType", true);
					$morder->accountnumber = hideCardNumber(get_user_meta($user_id, "pmpro_AccountNumber", true), false);
					$morder->expirationmonth = get_user_meta($user_id, "pmpro_ExpirationMonth", true);
					$morder->expirationyear = get_user_meta($user_id, "pmpro_ExpirationYear", true);
					$morder->ExpirationDate = $morder->expirationmonth . $morder->expirationyear;
					$morder->ExpirationDate_YdashM = $morder->expirationyear . "-" . $morder->expirationmonth;

					//save
					$morder->status = "success";
					$morder->saveOrder();
					$morder->getMemberOrderByID($morder->id);

					//email the user their invoice
					$pmproemail = new PMProEmail();
					$pmproemail->sendInvoiceEmail($user, $morder);

					$logstr .= "Created new order with ID #" . $morder->id . ". Event ID #" . $pmpro_stripe_event->id . ".";

					/*
						Checking if there is an update "after next payment" for this user.
					*/
					$user_updates = $user->pmpro_stripe_updates;
					if(!empty($user_updates))
					{
						foreach($user_updates as $key => $update)
						{
							if($update['when'] == 'payment')
							{
								PMProGateway_stripe::updateSubscription($update, $user_id);

								//remove this update
								unset($user_updates[$key]);

								//only process the first next payment update
								break;
							}
						}

						//save updates in case we removed some
						update_user_meta($user_id, "pmpro_stripe_updates", $user_updates);
					}

					do_action('pmpro_subscription_payment_completed', $morder);

					pmpro_stripeWebhookExit();
				}
				else
				{
					$logstr .= "We've already processed this order with ID #" . $order->id . ". Event ID #" . $pmpro_stripe_event->id . ".";
					pmpro_stripeWebhookExit();
				}
			}
			else
			{
				$logstr .= "Ignoring an invoice for $0. Probably for a new subscription just created. Event ID #" . $pmpro_stripe_event->id . ".";
				pmpro_stripeWebhookExit();
			}
		}
		elseif($pmpro_stripe_event->type == "invoice.payment_action_required") {
			// TODO: Test subs with SCA.
			$old_order = getOldOrderFromInvoiceEvent($pmpro_stripe_event);
			$user_id = $old_order->user_id;
			$user = get_userdata($user_id);
			
			// Prep order for emails.
			$morder = new MemberOrder();
			$morder->user_id = $user_id;
			$morder->billing = new stdClass();
			$morder->billing->name = $old_order->billing->name;
			$morder->billing->street = $old_order->billing->street;
			$morder->billing->city = $old_order->billing->city;
			$morder->billing->state = $old_order->billing->state;
			$morder->billing->zip = $old_order->billing->zip;
			$morder->billing->country = $old_order->billing->country;
			$morder->billing->phone = $old_order->billing->phone;

			//get CC info that is on file
			$morder->cardtype = get_user_meta($user_id, "pmpro_CardType", true);
			$morder->accountnumber = hideCardNumber(get_user_meta($user_id, "pmpro_AccountNumber", true), false);
			$morder->expirationmonth = get_user_meta($user_id, "pmpro_ExpirationMonth", true);
			$morder->expirationyear = get_user_meta($user_id, "pmpro_ExpirationYear", true);
			
			// Add invoice link to the order.
			$morder->invoice_url = $pmpro_stripe_event->data->object->hosted_invoice_url;
			
			// Email the user and ask them to authenticate their payment.
			$pmproemail = new PMProEmail();
			$pmproemail->sendPaymentActionRequiredEmail($user, $morder);

			// Email admin so they are aware.
			// TODO: Remove?
			$pmproemail = new PMProEmail();
			$pmproemail->sendPaymentActionRequiredAdminEmail($user, $morder);

			$logstr .= "Subscription payment for order ID #" . $old_order->id . " requires customer authentication. Sent email to the member and site admin.";
			pmpro_stripeWebhookExit();
			
			
		} elseif($pmpro_stripe_event->type == "charge.failed")
		{
			//last order for this subscription
			$old_order = getOldOrderFromInvoiceEvent($pmpro_stripe_event);

			$user_id = $old_order->user_id;
			$user = get_userdata($user_id);

			if(!empty($old_order->id))
			{
				do_action("pmpro_subscription_payment_failed", $old_order);

				//prep this order for the failure emails
				$morder = new MemberOrder();
				$morder->user_id = $user_id;
				$morder->membership_id = $old_order->membership_id;
				
				$morder->billing = new stdClass();
				$morder->billing->name = $old_order->billing->name;
				$morder->billing->street = $old_order->billing->street;
				$morder->billing->city = $old_order->billing->city;
				$morder->billing->state = $old_order->billing->state;
				$morder->billing->zip = $old_order->billing->zip;
				$morder->billing->country = $old_order->billing->country;
				$morder->billing->phone = $old_order->billing->phone;

				//get CC info that is on file
				$morder->cardtype = get_user_meta($user_id, "pmpro_CardType", true);
				$morder->accountnumber = hideCardNumber(get_user_meta($user_id, "pmpro_AccountNumber", true), false);
				$morder->expirationmonth = get_user_meta($user_id, "pmpro_ExpirationMonth", true);
				$morder->expirationyear = get_user_meta($user_id, "pmpro_ExpirationYear", true);

				// Email the user and ask them to update their credit card information
				$pmproemail = new PMProEmail();
				$pmproemail->sendBillingFailureEmail($user, $morder);

				// Email admin so they are aware of the failure
				$pmproemail = new PMProEmail();
				$pmproemail->sendBillingFailureAdminEmail(get_bloginfo("admin_email"), $morder);

				$logstr .= "Subscription payment failed on order ID #" . $old_order->id . ". Sent email to the member and site admin.";
				pmpro_stripeWebhookExit();
			}
			else
			{
				$logstr .= "Could not find the related subscription for event with ID #" . $pmpro_stripe_event->id . ".";
				if(!empty($pmpro_stripe_event->data->object->customer))
					$logstr .= " Customer ID #" . $pmpro_stripe_event->data->object->customer . ".";
				pmpro_stripeWebhookExit();
			}
		}
		elseif($pmpro_stripe_event->type == "customer.subscription.deleted")
		{
			//for one of our users? if they still have a membership for the same level, cancel it
			$old_order = getOldOrderFromInvoiceEvent($pmpro_stripe_event);

			if(!empty($old_order)) {
				$user_id = $old_order->user_id;
				$user = get_userdata($user_id);
								
				/**
				 * Array of Stripe.com subscription IDs and the timestamp when they were configured as 'preservable'
				 */
				$preserve = get_user_meta( $user_id, 'pmpro_stripe_dont_cancel', true );
				
				// Asume we should cancel the membership
				$cancel_membership = true;
				
				// Grab the subscription ID from the webhook
				if ( !empty( $pmpro_stripe_event->data->object ) && 'subscription' == $pmpro_stripe_event->data->object->object ) {
					
					$subscr = $pmpro_stripe_event->data->object;
					
					// Check if there's a sub ID to look at (from the webhook)
					// If it's in the list of preservable subscription IDs, don't delete it
					if ( is_array( $preserve ) && in_array( $subscr->id, array_keys( $preserve ) ) ) {
						
						$logstr       .= "Stripe subscription ({$subscr->id}) has been flagged during Subscription Update (in user profile). Will NOT cancel the membership for {$user->display_name} ({$user->user_email})!\n";
						$cancel_membership = false;
						
					}
				}
				
				if(!empty($user->ID) && true === $cancel_membership ) {
					do_action( "pmpro_stripe_subscription_deleted", $user->ID );
					
					if ( $old_order->status == "cancelled" ) {
						$logstr .= "We've already processed this cancellation. Probably originated from WP/PMPro. (Order #{$old_order->id}, Subscription Transaction ID #{$old_order->subscription_transaction_id})\n";
					} else if ( ! pmpro_hasMembershipLevel( $old_order->membership_id, $user->ID ) ) {
						$logstr .= "This user has a different level than the one associated with this order. Their membership was probably changed by an admin or through an upgrade/downgrade. (Order #{$old_order->id}, Subscription Transaction ID #{$old_order->subscription_transaction_id})\n";
					} else {
						//if the initial payment failed, cancel with status error instead of cancelled					
						pmpro_cancelMembershipLevel( $old_order->membership_id, $old_order->user_id, 'cancelled' );
						
						$logstr .= "Cancelled membership for user with id = {$old_order->user_id}. Subscription transaction id = {$old_order->subscription_transaction_id}.\n";
						
						//send an email to the member
						$myemail = new PMProEmail();
						$myemail->sendCancelEmail( $user, $old_order->membership_id );
						
						//send an email to the admin
						$myemail = new PMProEmail();
						$myemail->sendCancelAdminEmail( $user, $old_order->membership_id );
					}
					
					// Try to delete the usermeta entry as it's (probably) stale
					if ( isset( $preserve[$old_order->subscription_transaction_id])) {
						unset( $preserve[$old_order->subscription_transaction_id]);
						update_user_meta( $user_id, 'pmpro_stripe_dont_cancel', $preserve );
					}
					
					$logstr .= "Subscription deleted for user ID #" . $user->ID . ". Event ID #" . $pmpro_stripe_event->id . ".";
					pmpro_stripeWebhookExit();
				} else {
					$logstr .= "Stripe tells us they deleted the subscription, but for some reason we must ignore it. ";
					
					if ( false === $cancel_membership ) {
						$logstr .= "The subscription has been flagged as one to not delete the user membership for.\n ";
					} else {
						$logstr .= "Perhaps we could not find a user here for that subscription. ";
					}
					
					$logstr .= "Could also be a subscription managed by a different app or plugin. Event ID # {$pmpro_stripe_event->id}.";
					pmpro_stripeWebhookExit();
				}
				
			} else {
				$logstr .= "Stripe tells us a subscription is deleted, but we could not find the order for that subscription. Could be a subscription managed by a different app or plugin. Event ID #" . $pmpro_stripe_event->id . ".";
				pmpro_stripeWebhookExit();
			}
		}
	}
	else
	{
		if(!empty($event_id))
			$logstr .= "Could not find an event with ID #" . $event_id;
		else
			$logstr .= "No event ID given.";
		pmpro_stripeWebhookExit();
	}

	function getUserFromInvoiceEvent($pmpro_stripe_event)
	{
		//pause here to give PMPro a chance to finish checkout
		sleep(PMPRO_STRIPE_WEBHOOK_DELAY);

		global $wpdb;

		$customer_id = $pmpro_stripe_event->data->object->customer;

		//look up the order
		$user_id = $wpdb->get_var("SELECT user_id FROM $wpdb->pmpro_membership_orders WHERE subscription_transaction_id = '" . esc_sql($customer_id) . "' LIMIT 1");

		if(!empty($user_id))
			return get_userdata($user_id);
		else
			return false;
	}

	function getUserFromCustomerEvent($pmpro_stripe_event, $status = false, $checkplan = true)
	{
		//pause here to give PMPro a chance to finish checkout
		sleep(PMPRO_STRIPE_WEBHOOK_DELAY);

		global $wpdb;

		$customer_id = $pmpro_stripe_event->data->object->customer;
		$subscription_id = $pmpro_stripe_event->data->object->id;
		$plan_id = $pmpro_stripe_event->data->object->plan->id;

		//look up the order
		$sqlQuery = "SELECT user_id FROM $wpdb->pmpro_membership_orders WHERE (subscription_transaction_id = '" . esc_sql($customer_id) . "' OR subscription_transaction_id = '"  . esc_sql($subscription_id) . "') ";
		if($status)
			$sqlQuery .= " AND status='" . esc_sql($status) . "' ";
		if($checkplan)
			$sqlQuery .= " AND code='" . esc_sql($plan_id) . "' ";
		$sqlQuery .= " LIMIT 1";

		$user_id = $wpdb->get_var($sqlQuery);

		if(!empty($user_id))
			return get_userdata($user_id);
		else
			return false;
	}

	// TODO Test this
    // TODO docblock
	function getOldOrderFromInvoiceEvent($pmpro_stripe_event)
	{
		//pause here to give PMPro a chance to finish checkout
		sleep(PMPRO_STRIPE_WEBHOOK_DELAY);

		global $wpdb;

		$customer_id = $pmpro_stripe_event->data->object->customer;

		if ( ! empty( $pmpro_stripe_event->data->object->subscription ) ) {
            $subscription_id = $pmpro_stripe_event->data->object->subscription;
        } else {
            $subscription_id = $pmpro_stripe_event->data->object->id;
        }

		// no customer passed? we can't cross reference
		if(empty($customer_id))
			return false;

		// okay, add an invoice. first lookup the user_id from the subscription id passed
		$old_order_id = $wpdb->get_var("SELECT id FROM $wpdb->pmpro_membership_orders WHERE (subscription_transaction_id = '" . $customer_id . "' OR subscription_transaction_id = '"  . esc_sql($subscription_id) . "') AND gateway = 'stripe' ORDER BY timestamp DESC LIMIT 1");

		// since v1.8, PMPro may store the Stripe subscription_id (sub_XXXX) instead of the Stripe customer_id (cus_XXXX)
		// so that last query may turn up an empty result
		if(empty($old_order_id))
		{
			// let's look up the Stripe subscription_id instead
			// unfortunately, the subscription_id is not included in the JSON data from the Stripe event
			// so, we must look up the subscription_id from the invoice_id, which IS included in the JSON data from the Stripe event
			$invoice_id = $pmpro_stripe_event->data->object->invoice;

			try {

				$invoice = Stripe_Invoice::retrieve( $invoice_id );

			} catch (Exception $e) {
				error_log("Unable to fetch Stripe Invoice object: " . $e->getMessage());
				$invoice = null;
			}

			if (isset( $invoice->subscription )) {
				$subscription_id = $invoice->subscription;
				$old_order_id    = $wpdb->get_var( "SELECT id FROM $wpdb->pmpro_membership_orders WHERE (subscription_transaction_id = '" . $subscription_id . "' OR subscription_transaction_id = '"  . esc_sql($subscription_id) . "') AND gateway = 'stripe' ORDER BY timestamp DESC LIMIT 1" );
			}
		}

		if (!empty($old_order_id)) {

			$old_order = new MemberOrder( $old_order_id );

			if(isset($old_order->id) && ! empty($old_order->id))
				return $old_order;
		}

		return false;
	}

	function getOrderFromInvoiceEvent($pmpro_stripe_event)
	{
		//pause here to give PMPro a chance to finish checkout
		sleep(PMPRO_STRIPE_WEBHOOK_DELAY);

		$invoice_id = $pmpro_stripe_event->data->object->id;

		//get order by invoice id
		$order = new MemberOrder();
		$order->getMemberOrderByPaymentTransactionID($invoice_id);		
		
		if(!empty($order->id))
			return $order;
		else
			return false;
	}

	function pmpro_stripeWebhookExit()
	{
		global $logstr;

		//for log
		if($logstr)
		{
			$logstr = "Logged On: " . date_i18n("m/d/Y H:i:s") . "\n" . $logstr . "\n-------------\n";

			echo esc_html( $logstr );

			//log in file or email?
			if(defined('PMPRO_STRIPE_WEBHOOK_DEBUG') && PMPRO_STRIPE_WEBHOOK_DEBUG === "log")
			{
				//file
				$loghandle = fopen(dirname(__FILE__) . "/../logs/stripe-webhook.txt", "a+");
				fwrite($loghandle, $logstr);
				fclose($loghandle);
			}
			elseif(defined('PMPRO_STRIPE_WEBHOOK_DEBUG') && false !== PMPRO_STRIPE_WEBHOOK_DEBUG )
			{
				//email
				if(strpos(PMPRO_STRIPE_WEBHOOK_DEBUG, "@"))
					$log_email = PMPRO_STRIPE_WEBHOOK_DEBUG;	//constant defines a specific email address
				else
					$log_email = get_option("admin_email");

				wp_mail( $log_email, get_option( "blogname" ) . " Stripe Webhook Log", nl2br( esc_html( $logstr ) ) );
			}
		}

		exit;
	}
