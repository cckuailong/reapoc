<?php
	class MemberOrder
	{
		/**
		 * Constructor
		 */
		function __construct($id = NULL)
		{
			//set up the gateway
			$this->setGateway(pmpro_getOption("gateway"));

			//get data if an id was passed
			if($id)
			{
				if(is_numeric($id))
					return $this->getMemberOrderByID($id);
				else
					return $this->getMemberOrderByCode($id);
			}
			else
				return $this->getEmptyMemberOrder();	//blank constructor
		}

		/**
		 * Returns an empty (but complete) order object.
		 *
		 * @return stdClass $order - a 'clean' order object
		 *
		 * @since: 1.8.6.8
		 */
		function getEmptyMemberOrder()
		{

			//defaults
			$order = new stdClass();
			$order->code = $this->getRandomCode();
			$order->user_id = "";
			$order->membership_id = "";
			$order->subtotal = "";
			$order->tax = "";
			$order->couponamount = "";
			$order->total = "";
			$order->payment_type = "";
			$order->cardtype = "";
			$order->accountnumber = "";
			$order->expirationmonth = "";
			$order->expirationyear = "";
			$order->status = "success";
			$order->gateway = pmpro_getOption("gateway");
			$order->gateway_environment = pmpro_getOption("gateway_environment");
			$order->payment_transaction_id = "";
			$order->subscription_transaction_id = "";
			$order->affiliate_id = "";
			$order->affiliate_subid = "";
			$order->notes = "";
			$order->checkout_id = 0;

			$order->billing = new stdClass();
			$order->billing->name = "";
			$order->billing->street = "";
			$order->billing->city = "";
			$order->billing->state = "";
			$order->billing->zip = "";
			$order->billing->country = "";
			$order->billing->phone = "";

			return $order;
		}

		/**
		 * Retrieve a member order from the DB by ID
		 */
		function getMemberOrderByID($id)
		{
			global $wpdb;

			if(!$id)
				return false;

			$dbobj = $wpdb->get_row("SELECT * FROM $wpdb->pmpro_membership_orders WHERE id = '$id' LIMIT 1");

			if($dbobj)
			{
				$this->id = $dbobj->id;
				$this->code = $dbobj->code;
				$this->session_id = $dbobj->session_id;
				$this->user_id = $dbobj->user_id;
				$this->membership_id = $dbobj->membership_id;
				$this->paypal_token = $dbobj->paypal_token;
				$this->billing = new stdClass();
				$this->billing->name = $dbobj->billing_name;
				$this->billing->street = $dbobj->billing_street;
				$this->billing->city = $dbobj->billing_city;
				$this->billing->state = $dbobj->billing_state;
				$this->billing->zip = $dbobj->billing_zip;
				$this->billing->country = $dbobj->billing_country;
				$this->billing->phone = $dbobj->billing_phone;

				//split up some values
				$nameparts = pnp_split_full_name($this->billing->name);

				if(!empty($nameparts['fname']))
					$this->FirstName = $nameparts['fname'];
				else
					$this->FirstName = "";
				if(!empty($nameparts['lname']))
					$this->LastName = $nameparts['lname'];
				else
					$this->LastName = "";

				$this->Address1 = $this->billing->street;

				//get email from user_id
				$this->Email = $wpdb->get_var("SELECT user_email FROM $wpdb->users WHERE ID = '" . $this->user_id . "' LIMIT 1");

				$this->subtotal = $dbobj->subtotal;
				$this->tax = $dbobj->tax;
				$this->couponamount = $dbobj->couponamount;
				$this->certificate_id = $dbobj->certificate_id;
				$this->certificateamount = $dbobj->certificateamount;
				$this->total = $dbobj->total;
				$this->payment_type = $dbobj->payment_type;
				$this->cardtype = $dbobj->cardtype;
				$this->accountnumber = trim($dbobj->accountnumber);
				$this->expirationmonth = $dbobj->expirationmonth;
				$this->expirationyear = $dbobj->expirationyear;

				//date formats sometimes useful
				$this->ExpirationDate = $this->expirationmonth . $this->expirationyear;
				$this->ExpirationDate_YdashM = $this->expirationyear . "-" . $this->expirationmonth;

				$this->status = $dbobj->status;
				$this->gateway = $dbobj->gateway;
				$this->gateway_environment = $dbobj->gateway_environment;
				$this->payment_transaction_id = $dbobj->payment_transaction_id;
				$this->subscription_transaction_id = $dbobj->subscription_transaction_id;
				$this->timestamp = strtotime( $dbobj->timestamp );
				$this->affiliate_id = $dbobj->affiliate_id;
				$this->affiliate_subid = $dbobj->affiliate_subid;

				$this->notes = $dbobj->notes;
				$this->checkout_id = $dbobj->checkout_id;

				//reset the gateway
				if(empty($this->nogateway))
					$this->setGateway();

				return $this->id;
			}
			else
				return false;	//didn't find it in the DB
		}

		/**
		 * Get the first order for this subscription.
		 * Useful to find the original order from a recurring order.
		 * @since 2.5
		 * @return mixed Order object if found or false if not.
		 */
		function get_original_subscription_order( $subscription_id = '' ){
			global $wpdb;
			
			// Default to use the subscription ID on this order object.
			if ( empty( $subscription_id ) && ! empty( $this->subscription_transaction_id ) ) {
				$subscription_id = $this->subscription_transaction_id;
			}
			
			// Must have a subscription ID.
			if ( empty( $subscription_id ) ) {
				return false;
			}
			
			// Get some other values from this order to narrow the search.
			if ( ! empty( $this->user_id ) ) {
				$user_id = $this->user_id;
			} else {
				$user_id = '';
			}
			if ( ! empty( $this->gateway ) ) {
				$gateway = $this->gateway;
			} else {
				$gateway = '';
			}
			if ( ! empty( $this->gateway_environment ) ) {
				$gateway_environment = $this->gateway_environment;
			} else {
				$gateway_environment = '';
			}
			
			// Double check for a user_id, gateway and gateway environment.
			$sql = $wpdb->prepare(
				"SELECT ID
				 FROM $wpdb->pmpro_membership_orders
				 WHERE `subscription_transaction_id` = %s
				   AND `user_id` = %d
				   AND `gateway` = %s
				   AND `gateway_environment` = %s
				 ORDER BY id ASC
				 LIMIT 1",
				 array(
					 $subscription_id,
					 $user_id,
					 $gateway,
					 $gateway_environment
				 )
			 );
			
			$order_id = $wpdb->get_var( $sql );
			if ( ! empty( $order_id ) ) {
				return new MemberOrder( $order_id );
			} else {
				return false;
			}
		}

		/**
		 * Set up the Gateway class to use with this order.
		 *
		 * @param string $gateway Name/label for the gateway to set.
		 *
		 */
		function setGateway($gateway = NULL) {
			//set the gateway property
			if(isset($gateway)) {
				$this->gateway = $gateway;
			}

			//which one to load?
			$classname = "PMProGateway";	//default test gateway
			if(!empty($this->gateway) && $this->gateway != "free") {
				$classname .= "_" . $this->gateway;	//adding the gateway suffix
			}

			if(class_exists($classname) && isset($this->gateway)) {
				$this->Gateway = new $classname($this->gateway);
			} else {
				$this->Gateway = null;	//null out any current gateway
				$error = new WP_Error("PMPro1001", "Could not locate the gateway class file with class name = " . $classname . ".");
			}

			if(!empty($this->Gateway)) {
				return $this->Gateway;
			} else {
				//gateway wasn't setup
				return false;
			}
		}

		/**
		 * Get the most recent order for a user.
		 *
		 * @param int $user_id ID of user to find order for.
		 * @param string $status Limit search to only orders with this status. Defaults to "success".
		 * @param int $membership_id Limit search to only orders for this membership level. Defaults to NULL to find orders for any level.
		 *
		 * @return MemberOrder
		 */
		function getLastMemberOrder($user_id = NULL, $status = 'success', $membership_id = NULL, $gateway = NULL, $gateway_environment = NULL)
		{
			global $current_user, $wpdb;
			if(!$user_id)
				$user_id = $current_user->ID;

			if(!$user_id)
				return false;

			//build query
			$this->sqlQuery = "SELECT id FROM $wpdb->pmpro_membership_orders WHERE user_id = '" . $user_id . "' ";
			if(!empty($status) && is_array($status))
				$this->sqlQuery .= "AND status IN('" . implode("','", $status) . "') ";
			elseif(!empty($status))
				$this->sqlQuery .= "AND status = '" . esc_sql($status) . "' ";

			if(!empty($membership_id))
				$this->sqlQuery .= "AND membership_id = '" . $membership_id . "' ";

			if(!empty($gateway))
				$this->sqlQuery .= "AND gateway = '" . esc_sql($gateway) . "' ";

			if(!empty($gateway_environment))
				$this->sqlQuery .= "AND gateway_environment = '" . esc_sql($gateway_environment) . "' ";

			$this->sqlQuery .= "ORDER BY timestamp DESC LIMIT 1";

			//get id
			$id = $wpdb->get_var($this->sqlQuery);

			return $this->getMemberOrderByID($id);
		}

		/*
			Returns the order using the given order code.
		*/
		function getMemberOrderByCode($code)
		{
			global $wpdb;
			$id = $wpdb->get_var("SELECT id FROM $wpdb->pmpro_membership_orders WHERE code = '" . $code . "' LIMIT 1");
			if($id)
				return $this->getMemberOrderByID($id);
			else
				return false;
		}

		/*
			Returns the last order using the given payment_transaction_id.
		*/
		function getMemberOrderByPaymentTransactionID($payment_transaction_id)
		{
			//did they pass a trans id?
			if(empty($payment_transaction_id))
				return false;

			global $wpdb;
			$id = $wpdb->get_var("SELECT id FROM $wpdb->pmpro_membership_orders WHERE payment_transaction_id = '" . esc_sql($payment_transaction_id) . "' LIMIT 1");
			if($id)
				return $this->getMemberOrderByID($id);
			else
				return false;
		}

		/**
		 * Returns the last order using the given subscription_transaction_id.
		 */
		function getLastMemberOrderBySubscriptionTransactionID($subscription_transaction_id)
		{
			//did they pass a sub id?
			if(empty($subscription_transaction_id))
				return false;

			global $wpdb;
			$id = $wpdb->get_var("SELECT id FROM $wpdb->pmpro_membership_orders WHERE subscription_transaction_id = '" . esc_sql($subscription_transaction_id) . "' ORDER BY id DESC LIMIT 1");

			if($id)
				return $this->getMemberOrderByID($id);
			else
				return false;
		}

		/**
		 * Returns the last order using the given paypal token.
		 */
		function getMemberOrderByPayPalToken($token)
		{
			global $wpdb;
			$id = $wpdb->get_var("SELECT id FROM $wpdb->pmpro_membership_orders WHERE paypal_token = '" . $token . "' LIMIT 1");
			if($id)
				return $this->getMemberOrderByID($id);
			else
				return false;
		}

		/**
		 * Get a discount code object for the code used in this order.
		 *
		 * @param bool $force If true, it will query the database again.
		 *
		 */
		function getDiscountCode($force = false)
		{
			if(!empty($this->discount_code) && !$force)
				return $this->discount_code;

			global $wpdb;
			$this->discount_code = $wpdb->get_row("SELECT dc.* FROM $wpdb->pmpro_discount_codes dc LEFT JOIN $wpdb->pmpro_discount_codes_uses dcu ON dc.id = dcu.code_id WHERE dcu.order_id = '" . $this->id . "' LIMIT 1");

			//filter @since v1.7.14
			$this->discount_code = apply_filters("pmpro_order_discount_code", $this->discount_code, $this);

			return $this->discount_code;
		}

		/**
		 * Update the discount code used in this order.
		 *
		 * @param int $discount_code_id The ID of the discount code to update.
		 *
		 */
		function updateDiscountCode( $discount_code_id ) {
			global $wpdb;

			// Assumes one discount code per order
			$sqlQuery = $wpdb->prepare("
				SELECT id FROM $wpdb->pmpro_discount_codes_uses
				WHERE order_id = %d
				LIMIT 1",
				$this->id
			);
			$discount_codes_uses_id = $wpdb->get_var( $sqlQuery );

			// INSTEAD: Delete the code use if found
			if ( empty( $discount_code_id ) ) {
				if ( ! empty( $discount_codes_uses_id ) ) {
					$wpdb->delete(
						$wpdb->pmpro_discount_codes_uses,
						array( 'id' => $discount_codes_uses_id ),
						array( '%d' )
					);
				}
			} else {
				if ( ! empty( $discount_codes_uses_id ) ) {
					// Update existing row
					$wpdb->update(
						$wpdb->pmpro_discount_codes_uses,
						array( 'code_id' => $discount_code_id, 'user_id' => $this->user_id, 'order_id' => $this->id ),
						array( 'id' => $discount_codes_uses_id ),
						array( '%d', '%d', '%d' ),
						array( '%d' )
					);
				} else {
					// Insert a new row
					$wpdb->insert(
						$wpdb->pmpro_discount_codes_uses,
						array( 'code_id' => $discount_code_id, 'user_id' => $this->user_id, 'order_id' => $this->id ),
						array( '%d', '%d', '%d' )
					);
				}
			}

			// Make sure to reset properties on this object
			return $this->getDiscountCode( true );
		}

		/**
		 * Get a user object for the user associated with this order.
		 */
		function getUser()
		{
			global $wpdb;

			if(!empty($this->user))
				return $this->user;

			
			$this->user = $wpdb->get_row("SELECT * FROM $wpdb->users WHERE ID = '" . $this->user_id . "' LIMIT 1");
			
			// Fix the timestamp for local time 
			if ( ! empty( $this->user ) && ! empty( $this->user->user_registered ) ) {
				$this->user->user_registered = strtotime( get_date_from_gmt( $this->user->user_registered, 'Y-m-d H:i:s' ) );
			}

			return $this->user;
		}

		/**
		 * Get a membership level object for the level associated with this order.
		 *
		 * @param bool $force If true, it will query the database again.
		 *
		 */
		function getMembershipLevel($force = false)
		{
			global $wpdb;

			if(!empty($this->membership_level) && empty($force))
				return $this->membership_level;

			//check if there is an entry in memberships_users first
			if(!empty($this->user_id))
			{
				$this->membership_level = $wpdb->get_row("SELECT l.id as level_id, l.name, l.description, l.allow_signups, l.expiration_number, l.expiration_period, mu.*, UNIX_TIMESTAMP(CONVERT_TZ(mu.startdate, '+00:00', @@global.time_zone)) as startdate, UNIX_TIMESTAMP(CONVERT_TZ(mu.enddate, '+00:00', @@global.time_zone)) as enddate, l.name, l.description, l.allow_signups FROM $wpdb->pmpro_membership_levels l LEFT JOIN $wpdb->pmpro_memberships_users mu ON l.id = mu.membership_id WHERE mu.status = 'active' AND l.id = '" . $this->membership_id . "' AND mu.user_id = '" . $this->user_id . "' LIMIT 1");

				//fix the membership level id
				if(!empty($this->membership_level->level_id))
					$this->membership_level->id = $this->membership_level->level_id;
			}

			//okay, do I have a discount code to check? (if there is no membership_level->membership_id value, that means there was no entry in memberships_users)
			if(!empty($this->discount_code) && empty($this->membership_level->membership_id))
			{
				if(!empty($this->discount_code->code))
					$discount_code = $this->discount_code->code;
				else
					$discount_code = $this->discount_code;

				$sqlQuery = "SELECT l.id, cl.*, l.name, l.description, l.allow_signups FROM $wpdb->pmpro_discount_codes_levels cl LEFT JOIN $wpdb->pmpro_membership_levels l ON cl.level_id = l.id LEFT JOIN $wpdb->pmpro_discount_codes dc ON dc.id = cl.code_id WHERE dc.code = '" . $discount_code . "' AND cl.level_id = '" . $this->membership_id . "' LIMIT 1";

				$this->membership_level = $wpdb->get_row($sqlQuery);
			}

			//just get the info from the membership table	(sigh, I really need to standardize the column names for membership_id/level_id) but we're checking if we got the information already or not
			if(empty($this->membership_level->membership_id) && empty($this->membership_level->level_id))
			{
				$this->membership_level = $wpdb->get_row("SELECT l.* FROM $wpdb->pmpro_membership_levels l WHERE l.id = '" . $this->membership_id . "' LIMIT 1");
			}
			
			// Round prices to avoid extra decimals.
			if( ! empty( $this->membership_level ) ) {
				$this->membership_level->initial_payment = pmpro_round_price( $this->membership_level->initial_payment );
				$this->membership_level->billing_amount = pmpro_round_price( $this->membership_level->billing_amount );
				$this->membership_level->trial_amount = pmpro_round_price( $this->membership_level->trial_amount );
			}

			return $this->membership_level;
		}
		
		/**
		 * Get a membership level object at checkout
		 * for the level associated with this order.
		 *
		 * @since 2.0.2
		 * @param bool $force If true, it will reset the property.
		 *
		 */
		function getMembershipLevelAtCheckout($force = false) {
			global $pmpro_level;

			if( ! empty( $this->membership_level ) && empty( $force ) ) {
				return $this->membership_level;
			}
			
			// If for some reason, we haven't setup pmpro_level yet, do that.
			if ( empty( $pmpro_level ) ) {
				$pmpro_level = pmpro_getLevelAtCheckout();
			}
			
			// Set the level to the checkout level global.
			$this->membership_level = $pmpro_level;
			
			// Fix the membership level id.
			if(!empty( $this->membership_level) && !empty($this->membership_level->level_id)) {
				$this->membership_level->id = $this->membership_level->level_id;
			}
			
			// Round prices to avoid extra decimals.
			if( ! empty( $this->membership_level ) ) {
				$this->membership_level->initial_payment = pmpro_round_price( $this->membership_level->initial_payment );
				$this->membership_level->billing_amount = pmpro_round_price( $this->membership_level->billing_amount );
				$this->membership_level->trial_amount = pmpro_round_price( $this->membership_level->trial_amount );
			}
			
			return $this->membership_level;
		}

		/**
		 * Apply tax rules for the price given.
		 */
		function getTaxForPrice($price)
		{
			//get options
			$tax_state = pmpro_getOption("tax_state");
			$tax_rate = pmpro_getOption("tax_rate");

			//default
			$tax = 0;

			//calculate tax
			if($tax_state && $tax_rate)
			{
				//we have values, is this order in the tax state?
				if(!empty($this->billing) && trim(strtoupper($this->billing->state)) == trim(strtoupper($tax_state)))
				{
					//return value, pass through filter
					$tax = round((float)$price * (float)$tax_rate, 2);
				}
			}

			//set values array for filter
			$values = array("price" => $price, "tax_state" => $tax_state, "tax_rate" => $tax_rate);
			if(!empty($this->billing->street))
				$values['billing_street'] = $this->billing->street;
			if(!empty($this->billing->state))
				$values['billing_state'] = $this->billing->state;
			if(!empty($this->billing->city))
				$values['billing_city'] = $this->billing->city;
			if(!empty($this->billing->zip))
				$values['billing_zip'] = $this->billing->zip;
			if(!empty($this->billing->country))
				$values['billing_country'] = $this->billing->country;

			//filter
			$tax = apply_filters("pmpro_tax", $tax, $values, $this);
			return $tax;
		}

		/**
		 * Get the tax amount for this order.
		 */
		function getTax($force = false)
		{
			if(!empty($this->tax) && !$force)
				return $this->tax;

			//reset
			$this->tax = $this->getTaxForPrice($this->subtotal);

			return $this->tax;
		}

		/**
		 * Get the timestamp for this order.
		 *
		 * @param bool $gmt whether to return GMT time or local timestamp.
		 * @return int timestamp.
		 */
		function getTimestamp( $gmt = false ) {
			return $gmt ? $this->timestamp : strtotime( get_date_from_gmt( date( 'Y-m-d H:i:s', $this->timestamp ) ) );
		}

		/**
		 * Change the timestamp of an order by passing in year, month, day, time.
		 *
		 * $time should be adjusted for local timezone.
		 *
		 * NOTE: This function should no longer be used. Instead, set the timestamp
		 * for the order directly and call the MemberOrder->saveOrder() function.
		 * This function is no longer used on the /adminpages/orders.php page.
		 */
		function updateTimestamp($year, $month, $day, $time = NULL)
		{
			if(empty($this->id))
				return false;		//need a saved order

			if ( empty( $time ) ) {
				// Just save the order date.
				$date = $year . '-' . $month . '-' . $day . ' 00:00:00';
			} else {
				$date = get_gmt_from_date( $year . '-' . $month . '-' . $day . ' ' . $time, 'Y-m-d H:i:s' );
			}

			global $wpdb;
			$this->sqlQuery = "UPDATE $wpdb->pmpro_membership_orders SET timestamp = '" . $date . "' WHERE id = '" . $this->id . "' LIMIT 1";

			do_action('pmpro_update_order', $this);
			if($wpdb->query($this->sqlQuery) !== "false") {
				$this->timestamp = strtotime( $date );
				do_action('pmpro_updated_order', $this);
				
				return $this->getMemberOrderByID($this->id);
			} else {
				return false;
			}
		}

		/**
		 * Save/update the values of the order in the database.
		 */
		function saveOrder()
		{
			global $current_user, $wpdb, $pmpro_checkout_id;

			//get a random code to use for the public ID
			if(empty($this->code))
				$this->code = $this->getRandomCode();

			//figure out how much we charged
			if(!empty($this->InitialPayment))
				$amount = $this->InitialPayment;
			elseif(!empty($this->subtotal))
				$amount = $this->subtotal;
			else
				$amount = 0;

			//Todo: Tax?!, Coupons, Certificates, affiliates
			if(empty($this->subtotal))
				$this->subtotal = $amount;
			if(isset($this->tax))
				$tax = $this->tax;
			else
				$tax = $this->getTax(true);
			$this->certificate_id = "";
			$this->certificateamount = "";

			//calculate total
			if(!empty($this->total))
				$total = $this->total;
			elseif ( ! isset( $this->total ) || $this->total === '' ) {
				$total = (float)$amount + (float)$tax;
				$this->total = $total;
			} else {
				$total = 0;
			}
			
			//these fix some warnings/notices
			if(empty($this->billing))
			{
				$this->billing = new stdClass();
				$this->billing->name = $this->billing->street = $this->billing->city = $this->billing->state = $this->billing->zip = $this->billing->country = $this->billing->phone = "";
			}
			if(empty($this->user_id))
				$this->user_id = 0;
			if(empty($this->paypal_token))
				$this->paypal_token = "";
			if(empty($this->couponamount))
				$this->couponamount = "";
			if(empty($this->payment_type))
				$this->payment_type = "";
			if(empty($this->payment_transaction_id))
				$this->payment_transaction_id = "";
			if(empty($this->subscription_transaction_id))
				$this->subscription_transaction_id = "";
			if(empty($this->affiliate_id))
				$this->affiliate_id = "";
			if(empty($this->affiliate_subid))
				$this->affiliate_subid = "";
			if(empty($this->session_id))
				$this->session_id = "";
			if(empty($this->accountnumber))
				$this->accountnumber = "";
			if(empty($this->cardtype))
				$this->cardtype = "";
			if(empty($this->expirationmonth))
				$this->expirationmonth = "";
			if(empty($this->expirationyear))
				$this->expirationyear = "";
			if(empty($this->ExpirationDate))
				$this->ExpirationDate = "";
			if (empty($this->status))
				$this->status = "";

			if(empty($this->gateway))
				$this->gateway = pmpro_getOption("gateway");
			if(empty($this->gateway_environment))
				$this->gateway_environment = pmpro_getOption("gateway_environment");
			
			if(empty($this->datetime) && empty($this->timestamp))
				$this->datetime = date("Y-m-d H:i:s", time());
			elseif(empty($this->datetime) && !empty($this->timestamp) && is_numeric($this->timestamp))
				$this->datetime = date("Y-m-d H:i:s", $this->timestamp);	//get datetime from timestamp
			elseif(empty($this->datetime) && !empty($this->timestamp))
				$this->datetime = $this->timestamp;		//must have a datetime in it

			if(empty($this->notes))
				$this->notes = "";

			if(empty($this->checkout_id) || intval($this->checkout_id)<1) {
				$highestval = $wpdb->get_var("SELECT MAX(checkout_id) FROM $wpdb->pmpro_membership_orders");
				$this->checkout_id = intval($highestval)+1;
				$pmpro_checkout_id = $this->checkout_id;
			}

			//build query
			if(!empty($this->id))
			{
				//set up actions
				$before_action = "pmpro_update_order";
				$after_action = "pmpro_updated_order";
				//update
				$this->sqlQuery = "UPDATE $wpdb->pmpro_membership_orders
									SET `code` = '" . esc_sql( $this->code ) . "',
									`session_id` = '" . esc_sql( $this->session_id ) . "',
									`user_id` = " . intval($this->user_id) . ",
									`membership_id` = " . intval($this->membership_id) . ",
									`paypal_token` = '" . esc_sql( $this->paypal_token ) . "',
									`billing_name` = '" . esc_sql($this->billing->name) . "',
									`billing_street` = '" . esc_sql($this->billing->street) . "',
									`billing_city` = '" . esc_sql($this->billing->city) . "',
									`billing_state` = '" . esc_sql($this->billing->state) . "',
									`billing_zip` = '" . esc_sql($this->billing->zip) . "',
									`billing_country` = '" . esc_sql($this->billing->country) . "',
									`billing_phone` = '" . esc_sql($this->billing->phone) . "',
									`subtotal` = '" . esc_sql( $this->subtotal ) . "',
									`tax` = '" . esc_sql( $this->tax ) . "',
									`couponamount` = '" . esc_sql( $this->couponamount ) . "',
									`certificate_id` = " . intval($this->certificate_id) . ",
									`certificateamount` = '" . esc_sql( $this->certificateamount ) . "',
									`total` = '" . esc_sql( $this->total ) . "',
									`payment_type` = '" . esc_sql( $this->payment_type ) . "',
									`cardtype` = '" . esc_sql( $this->cardtype ) . "',
									`accountnumber` = '" . esc_sql( $this->accountnumber ) . "',
									`expirationmonth` = '" . esc_sql( $this->expirationmonth ) . "',
									`expirationyear` = '" . esc_sql( $this->expirationyear ) . "',
									`status` = '" . esc_sql($this->status) . "',
									`gateway` = '" . esc_sql( $this->gateway ) . "',
									`gateway_environment` = '" . esc_sql( $this->gateway_environment ) . "',
									`payment_transaction_id` = '" . esc_sql($this->payment_transaction_id) . "',
									`subscription_transaction_id` = '" . esc_sql($this->subscription_transaction_id) . "',
									`timestamp` = '" . esc_sql($this->datetime) . "',
									`affiliate_id` = '" . esc_sql($this->affiliate_id) . "',
									`affiliate_subid` = '" . esc_sql($this->affiliate_subid) . "',
									`notes` = '" . esc_sql($this->notes) . "',
									`checkout_id` = " . intval($this->checkout_id) . "
									WHERE id = '" . esc_sql( $this->id ) . "'
									LIMIT 1";
			}
			else
			{
				//set up actions
				$before_action = "pmpro_add_order";
				$after_action = "pmpro_added_order";
				
				//only on inserts, we might want to set the expirationmonth and expirationyear from ExpirationDate
				if( (empty($this->expirationmonth) || empty($this->expirationyear)) && !empty($this->ExpirationDate)) {
					$this->expirationmonth = substr($this->ExpirationDate, 0, 2);
					$this->expirationyear = substr($this->ExpirationDate, 2, 4);
				}
				
				//insert
				$this->sqlQuery = "INSERT INTO $wpdb->pmpro_membership_orders
								(`code`, `session_id`, `user_id`, `membership_id`, `paypal_token`, `billing_name`, `billing_street`, `billing_city`, `billing_state`, `billing_zip`, `billing_country`, `billing_phone`, `subtotal`, `tax`, `couponamount`, `certificate_id`, `certificateamount`, `total`, `payment_type`, `cardtype`, `accountnumber`, `expirationmonth`, `expirationyear`, `status`, `gateway`, `gateway_environment`, `payment_transaction_id`, `subscription_transaction_id`, `timestamp`, `affiliate_id`, `affiliate_subid`, `notes`, `checkout_id`)
								VALUES('" . esc_sql( $this->code ) . "',
									   '" . esc_sql( session_id() ) . "',
									   " . intval($this->user_id) . ",
									   " . intval($this->membership_id) . ",
									   '" . esc_sql( $this->paypal_token ) . "',
									   '" . esc_sql(trim($this->billing->name)) . "',
									   '" . esc_sql(trim($this->billing->street)) . "',
									   '" . esc_sql($this->billing->city) . "',
									   '" . esc_sql($this->billing->state) . "',
									   '" . esc_sql($this->billing->zip) . "',
									   '" . esc_sql($this->billing->country) . "',
									   '" . esc_sql( cleanPhone($this->billing->phone) ) . "',
									   '" . esc_sql( $this->subtotal ) . "',
									   '" . esc_sql( $tax ) . "',
									   '" . esc_sql( $this->couponamount ). "',
									   " . intval($this->certificate_id) . ",
									   '" . esc_sql( $this->certificateamount ) . "',
									   '" . esc_sql( $total ) . "',
									   '" . esc_sql( $this->payment_type ) . "',
									   '" . esc_sql( $this->cardtype ) . "',
									   '" . esc_sql( hideCardNumber($this->accountnumber, false) ) . "',
									   '" . esc_sql( $this->expirationmonth ) . "',
									   '" . esc_sql( $this->expirationyear ) . "',
									   '" . esc_sql($this->status) . "',
									   '" . esc_sql( $this->gateway ) . "',
									   '" . esc_sql( $this->gateway_environment ) . "',
									   '" . esc_sql($this->payment_transaction_id) . "',
									   '" . esc_sql($this->subscription_transaction_id) . "',
									   '" . esc_sql($this->datetime) . "',
									   '" . esc_sql($this->affiliate_id) . "',
									   '" . esc_sql($this->affiliate_subid) . "',
										'" . esc_sql($this->notes) . "',
									    " . intval($this->checkout_id) . "
									   )";
			}

			do_action($before_action, $this);
			if($wpdb->query($this->sqlQuery) !== false)
			{
				if(empty($this->id))
					$this->id = $wpdb->insert_id;
				do_action($after_action, $this);
				return $this->getMemberOrderByID($this->id);
			}
			else
			{
				return false;
			}
		}

		/**
		 * Get a random code to use as the order code.
		 */
		function getRandomCode() {
			global $wpdb;

			// We mix this with the seed to make sure we get unique codes.
			static $count = 0;
			$count++;

			while( empty( $code ) ) {
				$scramble = md5( AUTH_KEY . microtime() . SECURE_AUTH_KEY . $count );
				$code = substr( $scramble, 0, 10 );
				$code = apply_filters( 'pmpro_random_code', $code, $this );	//filter
				$check = $wpdb->get_var( "SELECT id FROM $wpdb->pmpro_membership_orders WHERE code = '$code' LIMIT 1" );
				if( $check || is_numeric( $code ) ) {
					$code = NULL;
				}
			}

			return strtoupper( $code );
		}

		/**
		 * Update the status of the order in the database.
		 */
		function updateStatus($newstatus)
		{
			global $wpdb;

			if(empty($this->id))
				return false;

			$this->sqlQuery = "UPDATE $wpdb->pmpro_membership_orders SET status = '" . esc_sql($newstatus) . "' WHERE id = '" . $this->id . "' LIMIT 1";
			
			do_action('pmpro_update_order', $this);
			if($wpdb->query($this->sqlQuery) !== false){
				$this->status = $newstatus;
				do_action('pmpro_updated_order', $this);
				
				return true;
			}else{
				return false;
			}
		}

		/**
		 * Call the process step of the gateway class.
		 */
		function process()
		{
			if (is_object($this->Gateway)) {
				return $this->Gateway->process($this);
			}
		}

		/**
		 * For offsite gateways with a confirm step.
		 *
		 * @since 1.8
		 */
		function confirm()
		{
			if (is_object($this->Gateway)) {
				return $this->Gateway->confirm($this);
			}
		}

		/**
		 * Cancel an order and call the cancel step of the gateway class if needed.
		 */
		function cancel() {
			global $wpdb;
			
			//only need to cancel on the gateway if there is a subscription id
			if(empty($this->subscription_transaction_id)) {
				//just mark as cancelled
				$this->updateStatus("cancelled");
				return true;
			} else {
				//get some data
				$order_user = get_userdata($this->user_id);

				//cancel orders for the same subscription
				//Note: We do this early to avoid race conditions if and when the
				//gateway send the cancel webhook after cancelling the subscription.				
				$sqlQuery = $wpdb->prepare(
					"UPDATE $wpdb->pmpro_membership_orders 
						SET `status` = 'cancelled' 
						WHERE user_id = %d 
							AND membership_id = %d 
							AND gateway = %s 
							AND gateway_environment = %s 
							AND subscription_transaction_id = %s 
							AND `status` IN('success', '') ",					
					$this->user_id,
					$this->membership_id,
					$this->gateway,
					$this->gateway_environment,
					$this->subscription_transaction_id
				);
				do_action('pmpro_update_order', $this);
				$wpdb->query($sqlQuery);
				do_action('pmpro_updated_order', $this);
				
				//cancel the gateway subscription first
				if (is_object($this->Gateway)) {
					$result = $this->Gateway->cancel( $this );
				} else {
					$result = false;
				}

				if($result == false) {
					//there was an error, but cancel the order no matter what
					$this->updateStatus("cancelled");

					//we should probably notify the admin
					$pmproemail = new PMProEmail();
					$pmproemail->template = "subscription_cancel_error";
					$pmproemail->data = array("body"=>"<p>" . sprintf(__("There was an error canceling the subscription for user with ID=%s. You will want to check your payment gateway to see if their subscription is still active.", 'paid-memberships-pro' ), strval($this->user_id)) . "</p><p>Error: " . $this->error . "</p>");
					$pmproemail->data["body"] .= '<p>' . __('User Email', 'paid-memberships-pro') . ': ' . $order_user->user_email . '</p>';
					$pmproemail->data["body"] .= '<p>' . __('Username', 'paid-memberships-pro') . ': ' . $order_user->user_login . '</p>';
					$pmproemail->data["body"] .= '<p>' . __('User Display Name', 'paid-memberships-pro') . ': ' . $order_user->display_name . '</p>';
					$pmproemail->data["body"] .= '<p>' . __('Order', 'paid-memberships-pro') . ': ' . $this->code . '</p>';
					$pmproemail->data["body"] .= '<p>' . __('Gateway', 'paid-memberships-pro') . ': ' . $this->gateway . '</p>';
					$pmproemail->data["body"] .= '<p>' . __('Subscription Transaction ID', 'paid-memberships-pro') . ': ' . $this->subscription_transaction_id . '</p>';
					$pmproemail->data["body"] .= '<hr />';
					$pmproemail->data["body"] .= '<p>' . __('Edit User', 'paid-memberships-pro') . ': ' . esc_url( add_query_arg( 'user_id', $this->user_id, self_admin_url( 'user-edit.php' ) ) ) . '</p>';
					$pmproemail->data["body"] .= '<p>' . __('Edit Order', 'paid-memberships-pro') . ': ' . esc_url( add_query_arg( array( 'page' => 'pmpro-orders', 'order' => $this->id ), admin_url('admin.php' ) ) ) . '</p>';
					$pmproemail->sendEmail(get_bloginfo("admin_email"));
				} else {
					//Note: status would have been set to cancelled by the gateway class. So we don't have to update it here.

					//remove billing numbers in pmpro_memberships_users if the membership is still active					
					$sqlQuery = "UPDATE $wpdb->pmpro_memberships_users SET initial_payment = 0, billing_amount = 0, cycle_number = 0 WHERE user_id = '" . $this->user_id . "' AND membership_id = '" . $this->membership_id . "' AND status = 'active'";
					$wpdb->query($sqlQuery);
				}
				
				
				
				return $result;
			}
		}

		/**
		 * Call the update method of the gateway class.
		 */
		function updateBilling()
		{
			if (is_object($this->Gateway)) {
				return $this->Gateway->update( $this );
			}
		}

		/**
		 * Call the getSubscriptionStatus method of the gateway class.
		 */
		function getGatewaySubscriptionStatus()
		{
			if (is_object($this->Gateway)) {
				return $this->Gateway->getSubscriptionStatus( $this );
			}
		}

		/**
		 * Call the getTransactionStatus method of the gateway class.
		 */
		function getGatewayTransactionStatus()
		{
			if (is_object($this->Gateway)) {
				return $this->Gateway->getTransactionStatus( $this );
			}
		}

		/** 
		 * Get TOS consent information.
		 * @since  1.9.5
		 */
		function get_tos_consent_log_entry() {
			if ( empty( $this->id ) ) {
				return false;
			}
			
			$consent_log = pmpro_get_consent_log( $this->user_id );
			foreach( $consent_log as $entry ) {
				if( $entry['order_id'] == $this->id ) {
					return $entry;
				}
			}

			return false;
		}

		/**
		 * Sets the billing address fields on the order object.
		 * Checks the last order for the same sub or pulls from user meta.
		 * @since 2.5.5
		 */
		function find_billing_address() {
			global $wpdb;

			if ( empty( $this->billing ) || empty( $this->billing->street ) ) {
				// We do not already have a billing address.
				$last_subscription_order = new MemberOrder();
				$last_subscription_order->getLastMemberOrderBySubscriptionTransactionID( $this->subscription_transaction_id );
				if ( ! empty( $last_subscription_order->billing ) && ! empty( $last_subscription_order->billing->street ) ) {
					// Last order in subscription has biling information. Pull data from there. 
					$this->Address1    = $last_subscription_order->billing->street;
					$this->City        = $last_subscription_order->billing->city;
					$this->State       = $last_subscription_order->billing->state;
					$this->Zip         = $last_subscription_order->billing->zip;
					$this->CountryCode = $last_subscription_order->billing->country;
					$this->PhoneNumber = $last_subscription_order->billing->phone;
					$this->Email       = $wpdb->get_var("SELECT user_email FROM $wpdb->users WHERE ID = '" . $this->user_id . "' LIMIT 1");

					$this->billing          = new stdClass();
					$this->billing->name    = $last_subscription_order->billing->name;
					$this->billing->street  = $last_subscription_order->billing->street;
					$this->billing->city    = $last_subscription_order->billing->city;
					$this->billing->state   = $last_subscription_order->billing->state;
					$this->billing->zip     = $last_subscription_order->billing->zip;
					$this->billing->country = $last_subscription_order->billing->country;
					$this->billing->phone   = $last_subscription_order->billing->phone;
				} else {
					// Last order did not have billing information. Try to pull from usermeta.
					$this->Address1    = get_user_meta( $this->user_id, "pmpro_baddress1", true );
					$this->City        = get_user_meta( $this->user_id, "pmpro_bcity", true );
					$this->State       = get_user_meta( $this->user_id, "pmpro_bstate", true );
					$this->Zip         = get_user_meta( $this->user_id, "pmpro_bzip", true );
					$this->CountryCode = get_user_meta( $this->user_id, "pmpro_bcountry", true );
					$this->PhoneNumber = get_user_meta( $this->user_id, "pmpro_bphone", true );
					$this->Email       = $wpdb->get_var("SELECT user_email FROM $wpdb->users WHERE ID = '" . $this->user_id . "' LIMIT 1");

					$this->billing          = new stdClass();
					$this->billing->name    = get_user_meta( $this->user_id, "pmpro_bfirstname", true ) . " " . get_user_meta( $this->user_id, "pmpro_blastname", true ) ;
					$this->billing->street  = $this->Address1;
					$this->billing->city    = $this->City;
					$this->billing->state   = $this->State;
					$this->billing->zip     = $this->Zip;
					$this->billing->country = $this->CountryCode;
					$this->billing->phone   = $this->PhoneNumber;
				}
			}
		}

		/**
		 * Delete an order and associated data.
		 */
		function deleteMe()
		{
			if(empty($this->id))
				return false;

			global $wpdb;
			$this->sqlQuery = "DELETE FROM $wpdb->pmpro_membership_orders WHERE id = '" . $this->id . "' LIMIT 1";
			if($wpdb->query($this->sqlQuery) !== false)
			{
				do_action("pmpro_delete_order", $this->id, $this);
				return true;
			}
			else
				return false;
		}

		/*
		* Generates a test order on the fly for orders.
		*/
		function get_test_order() {
			global $current_user;

			//$test_order = $this->getEmptyMemberOrder();
			$all_levels = pmpro_getAllLevels();
			
			if ( ! empty( $all_levels ) ) {
				$first_level                = array_shift( $all_levels );
				$this->membership_id  = $first_level->id;
				$this->InitialPayment = $first_level->initial_payment;
			} else {
				$this->membership_id  = 1;
				$this->InitialPayment = 1;
			}
			$this->user_id             = $current_user->ID;
			$this->cardtype            = "Visa";
			$this->accountnumber       = "4111111111111111";
			$this->expirationmonth     = date( 'm', current_time( 'timestamp' ) );
			$this->expirationyear      = ( intval( date( 'Y', current_time( 'timestamp' ) ) ) + 1 );
			$this->ExpirationDate      = $this->expirationmonth . $this->expirationyear;
			$this->CVV2                = '123';
			$this->FirstName           = 'Jane';
			$this->LastName            = 'Doe';
			$this->Address1            = '123 Street';
			$this->billing             = new stdClass();
			$this->billing->name       = 'Jane Doe';
			$this->billing->street     = '123 Street';
			$this->billing->city       = 'City';
			$this->billing->state      = 'ST';
			$this->billing->country    = 'US';
			$this->billing->zip        = '12345';
			$this->billing->phone      = '5558675309';
			$this->gateway_environment = 'sandbox';
			$this->timestamp		   = time();
			$this->notes               = __( 'This is a test order used with the PMPro Email Templates addon.', 'paid-memberships-pro' );

			return apply_filters( 'pmpro_test_order_data', $this );
		}
	} // End of Class