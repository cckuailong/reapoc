<?php
	//include pmprogateway
	require_once(dirname(__FILE__) . "/class.pmprogateway.php");

	//load classes init method
	add_action('init', array('PMProGateway_Twocheckout', 'init'));

	class PMProGateway_Twocheckout extends PMProGateway
	{
		function __construct($gateway = NULL)
		{
			if(!class_exists("Twocheckout"))
				require_once(dirname(__FILE__) . "/../../includes/lib/Twocheckout/Twocheckout.php");

			//set API connection vars
			Twocheckout::sellerId(pmpro_getOption('twocheckout_accountnumber'));
			Twocheckout::username(pmpro_getOption('twocheckout_apiusername'));
			Twocheckout::password(pmpro_getOption('twocheckout_apipassword'));
			Twocheckout::$verifySSL = false;

			$this->gateway = $gateway;
			return $this->gateway;
		}

		/**
		 * Run on WP init
		 *
		 * @since 1.8
		 */
		static function init()
		{
			//make sure PayPal Express is a gateway option
			add_filter('pmpro_gateways', array('PMProGateway_Twocheckout', 'pmpro_gateways'));

			//add fields to payment settings
			add_filter('pmpro_payment_options', array('PMProGateway_Twocheckout', 'pmpro_payment_options'));
			add_filter('pmpro_payment_option_fields', array('PMProGateway_Twocheckout', 'pmpro_payment_option_fields'), 10, 2);

			//code to add at checkout
			$gateway = pmpro_getGateway();
			if($gateway == "twocheckout")
			{
				add_filter('pmpro_include_billing_address_fields', '__return_false');
				add_filter('pmpro_include_payment_information_fields', '__return_false');
				add_filter('pmpro_required_billing_fields', array('PMProGateway_Twocheckout', 'pmpro_required_billing_fields'));
				add_filter('pmpro_checkout_default_submit_button', array('PMProGateway_Twocheckout', 'pmpro_checkout_default_submit_button'));
				add_filter('pmpro_checkout_before_change_membership_level', array('PMProGateway_Twocheckout', 'pmpro_checkout_before_change_membership_level'), 10, 2);
			}
		}

		/**
		 * Make sure this gateway is in the gateways list
		 *
		 * @since 1.8
		 */
		static function pmpro_gateways($gateways)
		{
			if(empty($gateways['twocheckout']))
				$gateways['twocheckout'] = __('2Checkout', 'paid-memberships-pro' );

			return $gateways;
		}

		/**
		 * Get a list of payment options that the this gateway needs/supports.
		 *
		 * @since 1.8
		 */
		static function getGatewayOptions()
		{
			$options = array(
				'sslseal',
				'nuclear_HTTPS',
				'gateway_environment',
				'twocheckout_apiusername',
				'twocheckout_apipassword',
				'twocheckout_accountnumber',
				'twocheckout_secretword',
				'currency',
				'use_ssl',
				'tax_state',
				'tax_rate'
			);

			return $options;
		}

		/**
		 * Set payment options for payment settings page.
		 *
		 * @since 1.8
		 */
		static function pmpro_payment_options($options)
		{
			//get stripe options
			$twocheckout_options = PMProGateway_Twocheckout::getGatewayOptions();

			//merge with others.
			$options = array_merge($twocheckout_options, $options);

			return $options;
		}

		/**
		 * Display fields for this gateway's options.
		 *
		 * @since 1.8
		 */
		static function pmpro_payment_option_fields($values, $gateway)
		{
		?>
		<tr class="pmpro_settings_divider gateway gateway_twocheckout" <?php if($gateway != "twocheckout") { ?>style="display: none;"<?php } ?>>
			<td colspan="2">
				<hr />
				<h2 class="title"><?php esc_html_e( '2Checkout Settings', 'paid-memberships-pro' ); ?></h2>
			</td>
		</tr>
		<tr class="gateway gateway_twocheckout" <?php if($gateway != "twocheckout") { ?>style="display: none;"<?php } ?>>
			<th scope="row" valign="top">
				<label for="twocheckout_apiusername"><?php _e('API Username', 'paid-memberships-pro' );?>:</label>
			</th>
			<td>
				<input type="text" id="twocheckout_apiusername" name="twocheckout_apiusername" value="<?php echo esc_attr($values['twocheckout_apiusername'])?>" class="regular-text code" />
				<p class="description"><?php _e('Go to Account &raquo; User Management in 2Checkout and create a user with API Access and API Updating.');?></p>
			</td>
		</tr>
		<tr class="gateway gateway_twocheckout" <?php if($gateway != "twocheckout") { ?>style="display: none;"<?php } ?>>
			<th scope="row" valign="top">
				<label for="twocheckout_apipassword"><?php _e('API Password', 'paid-memberships-pro' );?>:</label>
			</th>
			<td>
				<input type="text" id="twocheckout_apipassword" name="twocheckout_apipassword" value="<?php echo esc_attr($values['twocheckout_apipassword'])?>" autocomplete="off" class="regular-text code pmpro-admin-secure-key" />
				<p class="description"><?php esc_html_e( 'Password for the API user created.', 'paid-memberships-pro' ); ?></p>
			</td>
		</tr>
		<tr class="gateway gateway_twocheckout" <?php if($gateway != "twocheckout") { ?>style="display: none;"<?php } ?>>
			<th scope="row" valign="top">
				<label for="twocheckout_accountnumber"><?php _e('Account Number', 'paid-memberships-pro' );?>:</label>
			</th>
			<td>
				<input type="text" name="twocheckout_accountnumber" value="<?php echo $values['twocheckout_accountnumber']?>" class="regular-text code" />
				<p class="description"><?php esc_html_e( 'Click on the profile icon in 2Checkout to find your Account Number.', 'paid-memberships-pro' ); ?></p>
			</td>
		</tr>
		<tr class="gateway gateway_twocheckout" <?php if($gateway != "twocheckout") { ?>style="display: none;"<?php } ?>>
			<th scope="row" valign="top">
				<label for="twocheckout_secretword"><?php _e('Secret Word', 'paid-memberships-pro' );?>:</label>
			</th>
			<td>
				<input type="text" name="twocheckout_secretword" size="60" value="<?php echo $values['twocheckout_secretword']?>" />
				<p class="description"><?php _e('Go to Account &raquo; Site Management. Look under Checkout Options to find the Secret Word.', 'paid-memberships-pro' ); ?></p>
			</td>
		</tr>
		<tr class="gateway gateway_twocheckout" <?php if($gateway != "twocheckout") { ?>style="display: none;"<?php } ?>>
			<th scope="row" valign="top">
				<label><?php _e('TwoCheckout INS URL', 'paid-memberships-pro' );?>:</label>
			</th>
			<td>
				<p><?php _e('To fully integrate with 2Checkout, be sure to use the following for your INS URL and Approved URL', 'paid-memberships-pro' );?></p>
				<p><code><?php echo admin_url("admin-ajax.php") . "?action=twocheckout-ins";?></code></p>

			</td>
		</tr>
		<?php
		}

		/**
		 * Remove required billing fields
		 *
		 * @since 1.8
		 */
		static function pmpro_required_billing_fields($fields)
		{
			unset($fields['bfirstname']);
			unset($fields['blastname']);
			unset($fields['baddress1']);
			unset($fields['bcity']);
			unset($fields['bstate']);
			unset($fields['bzipcode']);
			unset($fields['bphone']);
			unset($fields['bemail']);
			unset($fields['bcountry']);
			unset($fields['CardType']);
			unset($fields['AccountNumber']);
			unset($fields['ExpirationMonth']);
			unset($fields['ExpirationYear']);
			unset($fields['CVV']);

			return $fields;
		}

		/**
		 * Swap in our submit buttons.
		 *
		 * @since 1.8
		 */
		static function pmpro_checkout_default_submit_button($show)
		{
			global $gateway, $pmpro_requirebilling;

			//show our submit buttons
			?>
			<span id="pmpro_submit_span">
				<input type="hidden" name="submit-checkout" value="1" />
				<input type="submit" class="<?php echo pmpro_get_element_class( 'pmpro_btn pmpro_btn-submit-checkout', 'pmpro_btn-submit-checkout' ); ?>" value="<?php if($pmpro_requirebilling) { _e('Check Out with 2Checkout', 'paid-memberships-pro' ); } else { _e('Submit and Confirm', 'paid-memberships-pro' );}?> &raquo;" />
			</span>
			<?php

			//don't show the default
			return false;
		}

		/**
		 * Instead of change membership levels, send users to 2Checkout to pay.
		 *
		 * @since 1.8
		 */
		static function pmpro_checkout_before_change_membership_level($user_id, $morder)
		{
			global $wpdb;

			//if no order, no need to pay
			if(empty($morder))
				return;

			$morder->user_id = $user_id;
			$morder->saveOrder();

			//save discount code use
			if(isset($morder->membership_level) && !empty($morder->membership_level->code_id))
			{
				$discount_code_id = (int)$morder->membership_level->code_id;
				$wpdb->query("INSERT INTO $wpdb->pmpro_discount_codes_uses (code_id, user_id, order_id, timestamp) VALUES('" . $discount_code_id . "', '" . $user_id . "', '" . $morder->id . "', now())");
			}
			do_action("pmpro_before_send_to_twocheckout", $user_id, $morder);

			$morder->Gateway->sendToTwocheckout($morder);
		}

		/**
		 * Process checkout.
		 *
		 */
		function process(&$order)
		{
			if(empty($order->code))
				$order->code = $order->getRandomCode();

			//clean up a couple values
			$order->payment_type = "2CheckOut";
			$order->CardType = "";
			$order->cardtype = "";

			//just save, the user will go to 2checkout to pay
			$order->status = "review";
			$order->saveOrder();

			return true;
		}

		function sendToTwocheckout(&$order)
		{
			global $pmpro_currency;

			$tco_args = array(
				'sid' => pmpro_getOption("twocheckout_accountnumber"),
				'mode' => '2CO', // will always be 2CO according to docs (@see https://www.2checkout.com/documentation/checkout/parameter-sets/pass-through-products/)
				'li_0_type' => 'product',
				'li_0_name' => substr($order->membership_level->name . " at " . get_bloginfo("name"), 0, 127),
				'li_0_quantity' => 1,
				'li_0_tangible' => 'N',
				'li_0_product_id' => $order->code,
				'merchant_order_id' => $order->code,
				'currency_code' => $pmpro_currency,
				'pay_method' => 'CC',
				'purchase_step' => 'billing-information',
				'x_receipt_link_url' => admin_url("admin-ajax.php") . "?action=twocheckout-ins" //pmpro_url("confirmation", "?level=" . $order->membership_level->id)
			);

			//taxes on initial amount
			$initial_payment = $order->InitialPayment;
			$initial_payment_tax = $order->getTaxForPrice($initial_payment);
			$initial_payment = pmpro_round_price((float)$initial_payment + (float)$initial_payment_tax);

			//taxes on the amount (NOT CURRENTLY USED)
			$amount = $order->PaymentAmount;
			$amount_tax = $order->getTaxForPrice($amount);
			$amount = pmpro_round_price((float)$amount + (float)$amount_tax);

			// Recurring membership
			if( pmpro_isLevelRecurring( $order->membership_level ) ) {
				$tco_args['li_0_startup_fee'] = number_format($initial_payment - $amount, 2, ".", "");		//negative amount for lower initial payments
				$recurring_payment = number_format($order->membership_level->billing_amount, 2, ".", "");
				$recurring_payment_tax = number_format($order->getTaxForPrice($recurring_payment), 2, ".", "");
				$recurring_payment = number_format(pmpro_round_price((float)$recurring_payment + (float)$recurring_payment_tax), 2, ".", "");
				$tco_args['li_0_price'] = number_format($recurring_payment, 2, ".", "");

				$tco_args['li_0_recurrence'] = ( $order->BillingFrequency == 1 ) ? $order->BillingFrequency . ' ' . $order->BillingPeriod : $order->BillingFrequency . ' ' . $order->BillingPeriod;

				if( property_exists( $order, 'TotalBillingCycles' ) )
					$tco_args['li_0_duration'] = ($order->BillingFrequency * $order->TotalBillingCycles ) . ' ' . $order->BillingPeriod;
				else
					$tco_args['li_0_duration'] = 'Forever';
			}
			// Non-recurring membership
			else {
				$tco_args['li_0_price'] = number_format($initial_payment, 2, ".", "");
			}

			// Demo mode?
			if(empty($order->gateway_environment))
				$gateway_environment = pmpro_getOption("gateway_environment");
			else
				$gateway_environment = $order->gateway_environment;
			if("sandbox" === $gateway_environment || "beta-sandbox" === $gateway_environment)
			{
				Twocheckout::sandbox(true);
				$tco_args['demo'] = 'Y';
			}
			else
				Twocheckout::sandbox(false);

			// Trial?
			//li_#_startup_fee	Any start up fees for the product or service. Can be negative to provide discounted first installment pricing, but cannot equal or surpass the product price.
			if(!empty($order->TrialBillingPeriod)) {
				$trial_amount = $order->TrialAmount;
				$trial_tax = $order->getTaxForPrice($trial_amount);
				$trial_amount = pmpro_formatPrice(pmpro_round_price((float)$trial_amount + (float)$trial_tax), false, false);
				$tco_args['li_0_startup_fee'] = $trial_amount; // Negative trial amount
			}

			$ptpStr = '';
			foreach( $tco_args as $key => $value ) {
				reset( $tco_args ); // Used to verify whether or not we're on the first argument
				$ptpStr .= ( $key == key($tco_args) ) ? '?' . $key . '=' . urlencode( $value ) : '&' . $key . '=' . urlencode( $value );
			}

			//anything modders might add
			$additional_parameters = apply_filters( 'pmpro_twocheckout_return_url_parameters', array() );
			if( ! empty( $additional_parameters ) )
				foreach( $additional_parameters as $key => $value )
					$ptpStr .= "&" . urlencode($key) . "=" . urlencode($value);

			$ptpStr = apply_filters( 'pmpro_twocheckout_ptpstr', $ptpStr, $order );

			///useful for debugging
			///echo str_replace("&", "&<br />", $ptpStr);
			///exit;

			$tco_url = 'https://www.2checkout.com/checkout/purchase' . $ptpStr;

			//redirect to 2checkout
			wp_redirect( $tco_url );
			exit;
		}

		function cancel(&$order) {
			//no matter what happens below, we're going to cancel the order in our system
			$order->updateStatus("cancelled");

			//require a payment transaction id
			if(empty($order->payment_transaction_id))
				return false;

			//build api params
			$params = array();
			$params['sale_id'] = $order->payment_transaction_id;

			// Demo mode?
			if(empty($order->gateway_environment))
				$gateway_environment = pmpro_getOption("gateway_environment");
			else
				$gateway_environment = $order->gateway_environment;

			if("sandbox" === $gateway_environment || "beta-sandbox" === $gateway_environment)
			{
				Twocheckout::sandbox(true);
				$params['demo'] = 'Y';
			}
			else
				Twocheckout::sandbox(false);

			$result = Twocheckout_Sale::stop( $params ); // Stop the recurring billing

			// Successfully cancelled
			if (isset($result['response_code']) && $result['response_code'] === 'OK') {
				$order->updateStatus("cancelled");
				return true;
			}
			// Failed
			else {
				$order->status = "error";
				$order->errorcode = $result->getCode();
				$order->error = $result->getMessage();

				return false;
			}

			return $order;
		}
	}
