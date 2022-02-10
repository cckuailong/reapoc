<?php
	//include pmprogateway
	require_once(dirname(__FILE__) . "/class.pmprogateway.php");

	//load classes init method
	add_action('init', array('PMProGateway_paypal', 'init'));

	class PMProGateway_paypal extends PMProGateway
	{
		function __construct($gateway = NULL)
		{
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
			//make sure PayPal Website Payments Pro is a gateway option
			add_filter('pmpro_gateways', array('PMProGateway_paypal', 'pmpro_gateways'));

			//add fields to payment settings
			add_filter('pmpro_payment_options', array('PMProGateway_paypal', 'pmpro_payment_options'));

			/*
				This code is the same for PayPal Website Payments Pro, PayPal Express, and PayPal Standard
				So we only load it if we haven't already.
			*/
			global $pmpro_payment_option_fields_for_paypal;
			if(empty($pmpro_payment_option_fields_for_paypal))
			{
				add_filter('pmpro_payment_option_fields', array('PMProGateway_paypal', 'pmpro_payment_option_fields'), 10, 2);
				$pmpro_payment_option_fields_for_paypal = true;
			}

			//code to add at checkout
			$gateway = pmpro_getGateway();
			if($gateway == "paypal")
			{
				add_action('pmpro_checkout_preheader', array('PMProGateway_paypal', 'pmpro_checkout_preheader'));
				add_filter('pmpro_checkout_default_submit_button', array('PMProGateway_paypal', 'pmpro_checkout_default_submit_button'));
				add_action('http_api_curl', array('PMProGateway_paypal', 'http_api_curl'), 10, 3);
			}
		}

		/**
		 * Update the SSLVERSION for CURL to support PayPal Express moving to TLS 1.2
		 *
		 * @since 1.8.9.1
		 */
		static function http_api_curl($handle, $r, $url) {
			if(strpos($url, 'paypal.com') !== false)
				curl_setopt( $handle, CURLOPT_SSLVERSION, 6 );
		}

		/**
		 * Make sure this gateway is in the gateways list
		 *
		 * @since 1.8
		 */
		static function pmpro_gateways($gateways)
		{
			if(empty($gateways['paypal']))
				$gateways['paypal'] = __('PayPal Website Payments Pro', 'paid-memberships-pro' );

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
				'gateway_email',
				'apiusername',
				'apipassword',
				'apisignature',
				'currency',
				'use_ssl',
				'tax_state',
				'tax_rate',
				'accepted_credit_cards',
				'paypalexpress_skip_confirmation',
				///'paypal_enable_3dsecure',
				//'paypal_cardinal_apikey',
				//'paypal_cardinal_apiidentifier',
				//'paypal_cardinal_orgunitid',
				//'paypal_cardinal_merchantid',
				//'paypal_cardinal_processorid'
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
			//get options
			$paypal_options = PMProGateway_paypal::getGatewayOptions();

			//merge with others.
			$options = array_merge($paypal_options, $options);

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
		<tr class="pmpro_settings_divider gateway gateway_paypal gateway_paypalexpress gateway_paypalstandard" <?php if($gateway != "paypal" && $gateway != "paypalexpress" && $gateway != "paypalstandard") { ?>style="display: none;"<?php } ?>>
			<td colspan="2">
				<hr />
				<h2 class="title"><?php _e('PayPal Settings', 'paid-memberships-pro' ); ?></h2>
			</td>
		</tr>
		<tr class="gateway gateway_paypalstandard" <?php if($gateway != "paypalstandard") { ?>style="display: none;"<?php } ?>>
			<td colspan="2" style="padding: 0px;">
				<p class="pmpro_message">
				<?php
					$allowed_message_html = array (
						'a' => array (
							'href' => array(),
							'target' => array(),
							'title' => array(),
						),
					);
					echo sprintf( wp_kses( __( 'Note: We do not recommend using PayPal Standard. We suggest using PayPal Express, Website Payments Pro (Legacy), or PayPal Pro (Payflow Pro). <a target="_blank" href="%s" title="More information on why can be found here">More information on why can be found here</a>.', 'paid-memberships-pro' ), $allowed_message_html ), 'https://www.paidmembershipspro.com/read-using-paypal-standard-paid-memberships-pro/?utm_source=plugin&utm_medium=pmpro-paymentsettings&utm_campaign=blog&utm_content=read-using-paypal-standard-paid-memberships-pro' );
				?>
				</p>
			</td>
		</tr>
		<tr class="gateway gateway_paypal gateway_paypalexpress gateway_paypalstandard" <?php if($gateway != "paypal" && $gateway != "paypalexpress" && $gateway != "paypalstandard") { ?>style="display: none;"<?php } ?>>
			<th scope="row" valign="top">
				<label for="gateway_email"><?php _e('Gateway Account Email', 'paid-memberships-pro' );?>:</label>
			</th>
			<td>
				<input type="text" id="gateway_email" name="gateway_email" value="<?php echo esc_attr($values['gateway_email'])?>" class="regular-text code" />
			</td>
		</tr>
		<tr class="gateway gateway_paypal gateway_paypalexpress" <?php if($gateway != "paypal" && $gateway != "paypalexpress") { ?>style="display: none;"<?php } ?>>
			<th scope="row" valign="top">
				<label for="apiusername"><?php _e('API Username', 'paid-memberships-pro' );?>:</label>
			</th>
			<td>
				<input type="text" id="apiusername" name="apiusername" value="<?php echo esc_attr($values['apiusername'])?>" class="regular-text code" />
			</td>
		</tr>
		<tr class="gateway gateway_paypal gateway_paypalexpress" <?php if($gateway != "paypal" && $gateway != "paypalexpress") { ?>style="display: none;"<?php } ?>>
			<th scope="row" valign="top">
				<label for="apipassword"><?php _e('API Password', 'paid-memberships-pro' );?>:</label>
			</th>
			<td>
				<input type="text" id="apipassword" name="apipassword" value="<?php echo esc_attr($values['apipassword'])?>" autocomplete="off" class="regular-text code pmpro-admin-secure-key" />
			</td>
		</tr>
		<tr class="gateway gateway_paypal gateway_paypalexpress" <?php if($gateway != "paypal" && $gateway != "paypalexpress") { ?>style="display: none;"<?php } ?>>
			<th scope="row" valign="top">
				<label for="apisignature"><?php _e('API Signature', 'paid-memberships-pro' );?>:</label>
			</th>
			<td>
				<input type="text" id="apisignature" name="apisignature" value="<?php echo esc_attr($values['apisignature'])?>" class="regular-text code" />
			</td>
		</tr>
		<tr class="gateway gateway_paypal gateway_paypalexpress" <?php if($gateway != "paypal" && $gateway != "paypalexpress") { ?>style="display: none;"<?php } ?>>
			<th scope="row" valign="top">
				<label for="paypalexpress_skip_confirmation"><?php _e('Confirmation Step', 'paid-memberships-pro' );?>:</label>
			</th>
			<td>
				<select id="paypalexpress_skip_confirmation" name="paypalexpress_skip_confirmation">
					<option value="0" <?php selected(pmpro_getOption('paypalexpress_skip_confirmation'), 0);?>>Require an extra confirmation after users return from PayPal Express.</option>
					<option value="1" <?php selected(pmpro_getOption('paypalexpress_skip_confirmation'), 1);?>>Skip the extra confirmation after users return from PayPal Express.</option>
				</select>
			</td>
		</tr>
		<tr class="gateway gateway_paypal gateway_paypalexpress gateway_paypalstandard" <?php if($gateway != "paypal" && $gateway != "paypalexpress" && $gateway != "paypalstandard") { ?>style="display: none;"<?php } ?>>
			<th scope="row" valign="top">
				<label><?php _e('IPN Handler URL', 'paid-memberships-pro' );?>:</label>
			</th>
			<td>
				<p class="description"><?php _e('To fully integrate with PayPal, be sure to set your IPN Handler URL to ', 'paid-memberships-pro' );?></p>
				<p><code><?php echo add_query_arg( 'action', 'ipnhandler', admin_url('admin-ajax.php') );?></code></p>
			</td>
		</tr>
		<?php
		}

		/**
		 * Code added to checkout preheader.
		 *
		 * @since 2.1
		 */
		static function pmpro_checkout_preheader() {
			global $gateway, $gateway_environment, $pmpro_level;
			$default_gateway = pmpro_getOption("gateway");

			if ( $gateway == 'paypal' || $default_gateway == 'paypal' ) {
				$dependencies = array( 'jquery' );
				$paypal_enable_3dsecure = pmpro_getOption( 'paypal_enable_3dsecure' );
				$data = array();

				// Setup 3DSecure if enabled.
				if( pmpro_was_checkout_form_submitted() && $paypal_enable_3dsecure ) {
					if( 'sandbox' === $gateway_environment || 'beta-sandbox' === $gateway_environment ) {
						$songbird_url = 'https://songbirdstag.cardinalcommerce.com/cardinalcruise/v1/songbird.js';
					} else {
						$songbird_url = 'https://songbird.cardinalcommerce.com/edge/v1/songbird.js';
					}
					wp_enqueue_script( 'pmpro_songbird', $songbird_url );
					$dependencies[] = 'pmpro_songbird';
					$data['enable_3dsecure'] = $paypal_enable_3dsecure;
					$data['cardinal_jwt'] = PMProGateway_paypal::get_cardinal_jwt();
					if ( WP_DEBUG ) {
						$data['cardinal_debug'] = 'verbose';
						$data['cardinal_logging'] = 'On';
					} else {
						$data['cardinal_debug'] = '';
						$data['cardinal_logging'] = 'Off';
					}
				}

				wp_register_script( 'pmpro_paypal',
                            plugins_url( 'js/pmpro-paypal.js', PMPRO_BASE_FILE ),
                            $dependencies,
                            PMPRO_VERSION );
				wp_localize_script( 'pmpro_paypal', 'pmpro_paypal', $data );
				wp_enqueue_script( 'pmpro_paypal' );
			}
		}

		static function get_cardinal_jwt() {
			require_once( PMPRO_DIR . '/includes/lib/php-jwt/JWT.php' );

			$key = pmpro_getOption( 'paypal_cardinal_apikey' );
			$now = current_time( 'timestamp' );
			$token = array(
				'jti' => 'JWT' . pmpro_getDiscountCode(),
				'iat' => $now,
				'exp' => $now + 7200,
				'iss' => pmpro_getOption( 'paypal_cardinal_apiidentifier' ),
				'OrgUnitId' => pmpro_getOption( 'paypal_cardinal_orgunitid' ),

			);
			$jwt = \PMPro\Firebase\JWT\JWT::encode($token, $key);

			return $jwt;
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
			<?php if($gateway == "paypal" || $gateway == "paypalexpress" || $gateway == "paypalstandard") { ?>
			<span id="pmpro_paypalexpress_checkout" <?php if(($gateway != "paypalexpress" && $gateway != "paypalstandard") || !$pmpro_requirebilling) { ?>style="display: none;"<?php } ?>>
				<input type="hidden" name="submit-checkout" value="1" />
				<input type="image" id="pmpro_btn-submit-paypal" class="<?php echo pmpro_get_element_class( 'pmpro_btn-submit-checkout' ); ?>" value="<?php _e('Check Out with PayPal', 'paid-memberships-pro' );?> &raquo;" src="<?php echo apply_filters("pmpro_paypal_button_image", "https://www.paypalobjects.com/webstatic/en_US/i/buttons/checkout-logo-medium.png");?>" />
			</span>
			<?php } ?>

			<span id="pmpro_submit_span" <?php if(($gateway == "paypalexpress" || $gateway == "paypalstandard") && $pmpro_requirebilling) { ?>style="display: none;"<?php } ?>>
				<input type="hidden" name="submit-checkout" value="1" />
				<input type="submit" id="pmpro_btn-submit" class="<?php echo pmpro_get_element_class( 'pmpro_btn pmpro_btn-submit-checkout', 'pmpro_btn-submit-checkout' ); ?>" value="<?php if($pmpro_requirebilling) { _e('Submit and Check Out', 'paid-memberships-pro' ); } else { _e('Submit and Confirm', 'paid-memberships-pro' );}?> &raquo;" />
			</span>
			<?php

			//don't show the default
			return false;
		}

		/**
		 * Process checkout.
		 *
		 */
		function process(&$order)
		{
			if(floatval($order->InitialPayment) == 0)
			{
				//auth first, then process
				$authorization_id = $this->authorize($order);
				if($authorization_id)
				{
					$this->void($order, $authorization_id);
					$order->ProfileStartDate = date_i18n("Y-m-d\TH:i:s", strtotime("+ " . $order->BillingFrequency . " " . $order->BillingPeriod, current_time("timestamp")));
					$order->ProfileStartDate = apply_filters("pmpro_profile_start_date", $order->ProfileStartDate, $order);
					return $this->subscribe($order);
				}
				else
				{
					if(empty($order->error))
						$order->error = __("Unknown error: Authorization failed.", 'paid-memberships-pro' );
					return false;
				}
			}
			else
			{
				//charge first payment
				if($this->charge($order))
				{
					//set up recurring billing
					if(pmpro_isLevelRecurring($order->membership_level))
					{
						$order->ProfileStartDate = date_i18n("Y-m-d\TH:i:s", strtotime("+ " . $order->BillingFrequency . " " . $order->BillingPeriod, current_time("timestamp")));
						$order->ProfileStartDate = apply_filters("pmpro_profile_start_date", $order->ProfileStartDate, $order);
						if($this->subscribe($order))
						{
							return true;
						}
						else
						{
							if($this->refund($order, $order->payment_transaction_id))
							{
								if(empty($order->error))
									$order->error = __("Unknown error: Payment failed.", 'paid-memberships-pro' );
							}
							else
							{
								if(empty($order->error))
									$order->error = "Unknown error: Payment failed.";

								$order->error .= " " . __("A partial payment was made that we could not refund. Please contact the site owner immediately to correct this.", 'paid-memberships-pro' );
							}

							return false;
						}
					}
					else
					{
						//only a one time charge
						$order->status = "success";	//saved on checkout page
						$order->saveOrder();
						return true;
					}
				}
			}
		}

		function authorize(&$order)
		{
			if(empty($order->code))
				$order->code = $order->getRandomCode();

			//paypal profile stuff
			$nvpStr = "";
			if(!empty($order->Token))
				$nvpStr .= "&TOKEN=" . $order->Token;
			$nvpStr .="&AMT=1.00&CURRENCYCODE=" . pmpro_getOption("currency");
			$nvpStr .= "&NOTIFYURL=" . urlencode( add_query_arg( 'action', 'ipnhandler', admin_url('admin-ajax.php') ) );
			//$nvpStr .= "&L_BILLINGTYPE0=RecurringPayments&L_BILLINGAGREEMENTDESCRIPTION0=" . $order->PaymentAmount;

			$nvpStr .= "&PAYMENTACTION=Authorization&IPADDRESS=" . $_SERVER['REMOTE_ADDR'] . "&INVNUM=" . $order->code;

			//credit card fields
			if($order->cardtype == "American Express")
				$cardtype = "Amex";
			else
				$cardtype = $order->cardtype;

			if(!empty($cardtype))
				$nvpStr .= "&CREDITCARDTYPE=" . $cardtype . "&ACCT=" . $order->accountnumber . "&EXPDATE=" . $order->ExpirationDate . "&CVV2=" . $order->CVV2;

			//Maestro/Solo card fields. (Who uses these?) :)
			if(!empty($order->StartDate))
				$nvpStr .= "&STARTDATE=" . $order->StartDate . "&ISSUENUMBER=" . $order->IssueNumber;

			// Name and email info
			if ( ! empty( $order->FirstName ) && ! empty( $order->LastName ) && ! empty( $order->Email ) ) {
				$nvpStr .= "&EMAIL=" . $order->Email . "&FIRSTNAME=" . $order->FirstName . "&LASTNAME=" . $order->LastName;
			}

			//billing address, etc
			if(!empty($order->Address1))
			{
				$nvpStr .= "&STREET=" . $order->Address1;

				if($order->Address2)
					$nvpStr .= "&STREET2=" . $order->Address2;

				$nvpStr .= "&CITY=" . $order->billing->city . "&STATE=" . $order->billing->state . "&COUNTRYCODE=" . $order->billing->country . "&ZIP=" . $order->billing->zip . "&SHIPTOPHONENUM=" . $order->billing->phone;
			}

			//for debugging, let's attach this to the class object
			$this->nvpStr = $nvpStr;

			$this->httpParsedResponseAr = $this->PPHttpPost('DoDirectPayment', $nvpStr);

			if("SUCCESS" == strtoupper($this->httpParsedResponseAr["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($this->httpParsedResponseAr["ACK"])) {
				$order->authorization_id = $this->httpParsedResponseAr['TRANSACTIONID'];
				$order->updateStatus("authorized");
				return $order->authorization_id;
			} else  {
				$order->status = "error";
				$order->errorcode = $this->httpParsedResponseAr['L_ERRORCODE0'];
				$order->error = urldecode($this->httpParsedResponseAr['L_LONGMESSAGE0']);
				$order->shorterror = urldecode($this->httpParsedResponseAr['L_SHORTMESSAGE0']);
				return false;
			}
		}

		function void(&$order, $authorization_id = null)
		{
			if(empty($authorization_id))
				return false;

			//paypal profile stuff
			$nvpStr="&AUTHORIZATIONID=" . $authorization_id . "&NOTE=Voiding an authorization for a recurring payment setup.";

			$this->httpParsedResponseAr = $this->PPHttpPost('DoVoid', $nvpStr);

			if("SUCCESS" == strtoupper($this->httpParsedResponseAr["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($this->httpParsedResponseAr["ACK"])) {
				return true;
			} else  {
				$order->status = "error";
				$order->errorcode = $this->httpParsedResponseAr['L_ERRORCODE0'];
				$order->error = urldecode($this->httpParsedResponseAr['L_LONGMESSAGE0']);
				$order->shorterror = urldecode($this->httpParsedResponseAr['L_SHORTMESSAGE0']);
				return false;
			}
		}

		function refund(&$order, $transaction_id)
		{
			if(empty($transaction_id))
				return false;

			//paypal profile stuff
			$nvpStr="&TRANSACTIONID=" . $transaction_id . "&NOTE=Refunding a charge.";

			$this->httpParsedResponseAr = $this->PPHttpPost('RefundTransaction', $nvpStr);

			if("SUCCESS" == strtoupper($this->httpParsedResponseAr["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($this->httpParsedResponseAr["ACK"])) {
				return true;
			} else  {
				$order->status = "error";
				$order->errorcode = $this->httpParsedResponseAr['L_ERRORCODE0'];
				$order->error = urldecode($this->httpParsedResponseAr['L_LONGMESSAGE0']);
				$order->shorterror = urldecode($this->httpParsedResponseAr['L_SHORTMESSAGE0']);
				return false;
			}
		}

		function charge(&$order)
		{
			global $pmpro_currency;

			if(empty($order->code))
				$order->code = $order->getRandomCode();

			//taxes on the amount
			$amount = $order->InitialPayment;
			$amount_tax = $order->getTaxForPrice($amount);
			$order->subtotal = $amount;
			$amount = pmpro_round_price((float)$amount + (float)$amount_tax);

			//paypal profile stuff
			$nvpStr = "";
			if(!empty($order->Token))
				$nvpStr .= "&TOKEN=" . $order->Token;
			$nvpStr .="&AMT=" . $amount . "&ITEMAMT=" . $order->InitialPayment . "&TAXAMT=" . $amount_tax . "&CURRENCYCODE=" . $pmpro_currency;
			$nvpStr .= "&NOTIFYURL=" . urlencode( add_query_arg( 'action', 'ipnhandler', admin_url('admin-ajax.php') ) );
			//$nvpStr .= "&L_BILLINGTYPE0=RecurringPayments&L_BILLINGAGREEMENTDESCRIPTION0=" . $order->PaymentAmount;

			$nvpStr .= "&PAYMENTACTION=Sale&IPADDRESS=" . $_SERVER['REMOTE_ADDR'] . "&INVNUM=" . $order->code;

			//credit card fields
			if($order->cardtype == "American Express")
				$cardtype = "Amex";
			else
				$cardtype = $order->cardtype;

			if(!empty($cardtype))
				$nvpStr .= "&CREDITCARDTYPE=" . $cardtype . "&ACCT=" . $order->accountnumber . "&EXPDATE=" . $order->ExpirationDate . "&CVV2=" . $order->CVV2;

			//Maestro/Solo card fields. (Who uses these?) :)
			if(!empty($order->StartDate))
				$nvpStr .= "&STARTDATE=" . $order->StartDate . "&ISSUENUMBER=" . $order->IssueNumber;

			// Name and email info
			if ( $order->FirstName && $order->LastName && $order->Email ) {
				$nvpStr .= "&EMAIL=" . $order->Email . "&FIRSTNAME=" . $order->FirstName . "&LASTNAME=" . $order->LastName;
			}

			//billing address, etc
			if($order->Address1)
			{
				$nvpStr .= "&STREET=" . $order->Address1;

				if($order->Address2)
					$nvpStr .= "&STREET2=" . $order->Address2;

				$nvpStr .= "&CITY=" . $order->billing->city . "&STATE=" . $order->billing->state . "&COUNTRYCODE=" . $order->billing->country . "&ZIP=" . $order->billing->zip . "&SHIPTOPHONENUM=" . $order->billing->phone;
			}

			$this->httpParsedResponseAr = $this->PPHttpPost('DoDirectPayment', $nvpStr);

			if("SUCCESS" == strtoupper($this->httpParsedResponseAr["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($this->httpParsedResponseAr["ACK"])) {
				$order->payment_transaction_id = $this->httpParsedResponseAr['TRANSACTIONID'];
				$order->updateStatus("success");
				return true;
			} else  {
				$order->status = "error";
				$order->errorcode = $this->httpParsedResponseAr['L_ERRORCODE0'];
				$order->error = urldecode($this->httpParsedResponseAr['L_LONGMESSAGE0']);
				$order->shorterror = urldecode($this->httpParsedResponseAr['L_SHORTMESSAGE0']);
				return false;
			}
		}

		function subscribe(&$order)
		{
			global $pmpro_currency;

			if(empty($order->code))
				$order->code = $order->getRandomCode();

			//filter order before subscription. use with care.
			$order = apply_filters("pmpro_subscribe_order", $order, $this);

			//taxes on the amount
			$amount = $order->PaymentAmount;
			$amount_tax = $order->getTaxForPrice($amount);
			$amount = pmpro_round_price((float)$amount + (float)$amount_tax);

			//paypal profile stuff
			$nvpStr = "";

			if(!empty($order->Token))
				$nvpStr .= "&TOKEN=" . $order->Token;
			$nvpStr .="&AMT=" . $order->PaymentAmount . "&TAXAMT=" . $amount_tax . "&CURRENCYCODE=" . $pmpro_currency . "&PROFILESTARTDATE=" . $order->ProfileStartDate;
			$nvpStr .= "&BILLINGPERIOD=" . $order->BillingPeriod . "&BILLINGFREQUENCY=" . $order->BillingFrequency . "&AUTOBILLOUTAMT=AddToNextBilling";
			$nvpStr .= "&DESC=" . urlencode( apply_filters( 'pmpro_paypal_level_description', substr($order->membership_level->name . " at " . get_bloginfo("name"), 0, 127), $order->membership_level->name, $order, get_bloginfo("name")) );
			$nvpStr .= "&NOTIFYURL=" . urlencode( add_query_arg( 'action', 'ipnhandler', admin_url('admin-ajax.php') ) );
			//$nvpStr .= "&L_BILLINGTYPE0=RecurringPayments&L_BILLINGAGREEMENTDESCRIPTION0=" . $order->PaymentAmount;

			//if billing cycles are defined
			if(!empty($order->TotalBillingCycles))
				$nvpStr .= "&TOTALBILLINGCYCLES=" . $order->TotalBillingCycles;

			//if a trial period is defined
			if(!empty($order->TrialBillingPeriod))
			{
				$trial_amount = $order->TrialAmount;
				$trial_tax = $order->getTaxForPrice($trial_amount);
				$trial_amount = pmpro_round_price((float)$trial_amount + (float)$trial_tax);

				$nvpStr .= "&TRIALBILLINGPERIOD=" . $order->TrialBillingPeriod . "&TRIALBILLINGFREQUENCY=" . $order->TrialBillingFrequency . "&TRIALAMT=" . $trial_amount;
			}
			if(!empty($order->TrialBillingCycles))
				$nvpStr .= "&TRIALTOTALBILLINGCYCLES=" . $order->TrialBillingCycles;

			//credit card fields
			if($order->cardtype == "American Express")
				$cardtype = "Amex";
			else
				$cardtype = $order->cardtype;

			if($cardtype)
				$nvpStr .= "&CREDITCARDTYPE=" . $cardtype . "&ACCT=" . $order->accountnumber . "&EXPDATE=" . $order->ExpirationDate . "&CVV2=" . $order->CVV2;

			//Maestro/Solo card fields. (Who uses these?) :)
			if(!empty($order->StartDate))
				$nvpStr .= "&STARTDATE=" . $order->StartDate . "&ISSUENUMBER=" . $order->IssueNumber;

			// Name and email info
			if ( $order->FirstName && $order->LastName && $order->Email ) {
				$nvpStr .= "&EMAIL=" . $order->Email . "&FIRSTNAME=" . $order->FirstName . "&LASTNAME=" . $order->LastName;
			}

			//billing address, etc
			if($order->Address1)
			{
				$nvpStr .= "&STREET=" . $order->Address1;

				if($order->Address2)
					$nvpStr .= "&STREET2=" . $order->Address2;

				$nvpStr .= "&CITY=" . $order->billing->city . "&STATE=" . $order->billing->state . "&COUNTRYCODE=" . $order->billing->country . "&ZIP=" . $order->billing->zip . "&SHIPTOPHONENUM=" . $order->billing->phone;
			}

			// Set MAXFAILEDPAYMENTS so subscriptions are cancelled after 1 failed payment.
			$nvpStr .= "&MAXFAILEDPAYMENTS=1";
			
			$nvpStr = apply_filters("pmpro_create_recurring_payments_profile_nvpstr", $nvpStr, $order);

			//for debugging let's add this to the class object
			$this->nvpStr = $nvpStr;
			
			///echo str_replace("&", "&<br />", $nvpStr);
			///exit;

			$this->httpParsedResponseAr = $this->PPHttpPost('CreateRecurringPaymentsProfile', $nvpStr);

			if("SUCCESS" == strtoupper($this->httpParsedResponseAr["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($this->httpParsedResponseAr["ACK"])) {
				$order->status = "success";
				$order->subscription_transaction_id = urldecode($this->httpParsedResponseAr['PROFILEID']);
				return true;
				//exit('CreateRecurringPaymentsProfile Completed Successfully: '.print_r($this->httpParsedResponseAr, true));
			} else  {
				$order->status = "error";
				$order->errorcode = $this->httpParsedResponseAr['L_ERRORCODE0'];
				$order->error = urldecode($this->httpParsedResponseAr['L_LONGMESSAGE0']);
				$order->shorterror = urldecode($this->httpParsedResponseAr['L_SHORTMESSAGE0']);
				return false;
				//exit('CreateRecurringPaymentsProfile failed: ' . print_r($httpParsedResponseAr, true));
			}
		}

		function update(&$order)
		{
			//paypal profile stuff
			$nvpStr = "";
			$nvpStr .= "&PROFILEID=" . $order->subscription_transaction_id;

			//credit card fields
			if($order->cardtype == "American Express")
				$cardtype = "Amex";
			else
				$cardtype = $order->cardtype;

			//credit card fields
			if($cardtype)
				$nvpStr .= "&CREDITCARDTYPE=" . $cardtype . "&ACCT=" . $order->accountnumber . "&EXPDATE=" . $order->ExpirationDate . "&CVV2=" . $order->CVV2;

			//Maestro/Solo card fields. (Who uses these?) :)
			if($order->StartDate)
				$nvpStr .= "&STARTDATE=" . $order->StartDate . "&ISSUENUMBER=" . $order->IssueNumber;

				// Name and email info
				if ( $order->FirstName && $order->LastName && $order->Email ) {
					$nvpStr .= "&EMAIL=" . $order->Email . "&FIRSTNAME=" . $order->FirstName . "&LASTNAME=" . $order->LastName;
				}

				//billing address, etc
				if($order->Address1)
				{
					$nvpStr .= "&STREET=" . $order->Address1;

					if($order->Address2)
						$nvpStr .= "&STREET2=" . $order->Address2;

					$nvpStr .= "&CITY=" . $order->billing->city . "&STATE=" . $order->billing->state . "&COUNTRYCODE=" . $order->billing->country . "&ZIP=" . $order->billing->zip . "&SHIPTOPHONENUM=" . $order->billing->phone;
				}

			$this->httpParsedResponseAr = $this->PPHttpPost('UpdateRecurringPaymentsProfile', $nvpStr);

			if("SUCCESS" == strtoupper($this->httpParsedResponseAr["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($this->httpParsedResponseAr["ACK"])) {
				$order->status = "success";
				$order->subscription_transaction_id = urldecode($this->httpParsedResponseAr['PROFILEID']);
				return true;
				//exit('CreateRecurringPaymentsProfile Completed Successfully: '.print_r($this->httpParsedResponseAr, true));
			} else  {
				$order->status = "error";
				$order->errorcode = $this->httpParsedResponseAr['L_ERRORCODE0'];
				$order->error = urldecode($this->httpParsedResponseAr['L_LONGMESSAGE0']);
				$order->shorterror = urldecode($this->httpParsedResponseAr['L_SHORTMESSAGE0']);
				return false;
				//exit('CreateRecurringPaymentsProfile failed: ' . print_r($httpParsedResponseAr, true));
			}
		}		

		function cancel(&$order) {
			// Always cancel the order locally even if PayPal might fail
			$order->updateStatus("cancelled");

			// If we're processing an IPN request for this subscription, it's already cancelled at PayPal.
			if ( ( ! empty( $_POST['subscr_id'] ) && $_POST['subscr_id'] == $order->subscription_transaction_id ) ||
				 ( ! empty( $_POST['recurring_payment_id'] ) && $_POST['recurring_payment_id'] == $order->subscription_transaction_id ) ) {
				// recurring_payment_failed transaction still need to be cancelled
				if ( $_POST['txn_type'] !== 'recurring_payment_failed' ) {
					return true;
				}
			}

			// Cancel at gateway
			return $this->cancelSubscriptionAtGateway($order);
		}

		function cancelSubscriptionAtGateway(&$order) {
			// Build the nvp string for PayPal API
			$nvpStr = "";
			$nvpStr .= "&PROFILEID=" . urlencode($order->subscription_transaction_id) . "&ACTION=Cancel&NOTE=" . urlencode("User requested cancel.");

			$nvpStr = apply_filters("pmpro_manage_recurring_payments_profile_status_nvpstr", $nvpStr, $order);

			$this->httpParsedResponseAr = $this->PPHttpPost('ManageRecurringPaymentsProfileStatus', $nvpStr);

			if("SUCCESS" == strtoupper($this->httpParsedResponseAr["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($this->httpParsedResponseAr["ACK"])) {
				return true;
			} else {
				$order->status = "error";
				$order->errorcode = $this->httpParsedResponseAr['L_ERRORCODE0'];
				$order->error = urldecode($this->httpParsedResponseAr['L_LONGMESSAGE0']) . ". " . __("Please contact the site owner or cancel your subscription from within PayPal to make sure you are not charged going forward.", 'paid-memberships-pro' );
				$order->shorterror = urldecode($this->httpParsedResponseAr['L_SHORTMESSAGE0']);

				return false;
			}
		}

		function getSubscriptionStatus(&$order)
		{
			if(empty($order->subscription_transaction_id))
				return false;

			//paypal profile stuff
			$nvpStr = "";
			$nvpStr .= "&PROFILEID=" . urlencode($order->subscription_transaction_id);

			$nvpStr = apply_filters("pmpro_get_recurring_payments_profile_details_nvpstr", $nvpStr, $order);

			$this->httpParsedResponseAr = $this->PPHttpPost('GetRecurringPaymentsProfileDetails', $nvpStr);

			if("SUCCESS" == strtoupper($this->httpParsedResponseAr["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($this->httpParsedResponseAr["ACK"]))
			{
				return $this->httpParsedResponseAr;
			}
			else
			{
				$order->status = "error";
				$order->errorcode = $this->httpParsedResponseAr['L_ERRORCODE0'];
				$order->error = urldecode($this->httpParsedResponseAr['L_LONGMESSAGE0']);
				$order->shorterror = urldecode($this->httpParsedResponseAr['L_SHORTMESSAGE0']);

				return false;
			}
		}
		
		function getTransactionStatus(&$order) {
			$transaction_details = $order->Gateway->getTransactionDetailsByOrder( $order );
			if( false === $transaction_details ){
				return false;
			}

			if( ! isset( $transaction_details['PAYMENTSTATUS'] ) ){
				return false;
			}

			return $transaction_details['PAYMENTSTATUS'];
		}

		function getTransactionDetailsByOrder(&$order)
		{
			if(empty($order->payment_transaction_id))
				return false;

			if( $order->payment_transaction_id == $order->subscription_transaction_id ){
				/** Initial payment **/
				$nvpStr = "";
				// STARTDATE is Required, even if useless here. Start from 24h before the order timestamp, to avoid timezone related issues.
				$nvpStr .= "&STARTDATE=" . urlencode( gmdate( DATE_W3C, $order->getTimestamp() - DAY_IN_SECONDS ) . 'Z' );
				// filter results by a specific transaction id.
				$nvpStr .= "&TRANSACTIONID=" . urlencode($order->subscription_transaction_id);

				$this->httpParsedResponseAr = $this->PPHttpPost('TransactionSearch', $nvpStr);

				if( ! in_array( strtoupper( $this->httpParsedResponseAr["ACK"] ), [ 'SUCCESS', 'SUCCESSWITHWARNING' ] ) ){
					// since we are using TRANSACTIONID=I-... which is NOT a transaction id,
                    			// paypal is returning an error. but the results are actually filtered by that transaction id, usually.

					// let's double check it.
					if( ! isset( $this->httpParsedResponseAr['L_TRANSACTIONID0'] ) ){
						// really no results? it's a real error.
						return false;
					}
				}

				$transaction_ids = [];
				for( $i = 0; $i < PHP_INT_MAX; $i++ ){
	    				// loop until we have results
					if( ! isset( $this->httpParsedResponseAr["L_TRANSACTIONID$i"] ) ){
						break;
					}

					// ignore I-... results
					if( "I-" === substr( $this->httpParsedResponseAr["L_TRANSACTIONID$i"], 0 ,2 ) ){
						if( $order->subscription_transaction_id != $this->httpParsedResponseAr["L_TRANSACTIONID$i"] ){
							// if we got a result from another I- subscription transaction id,
							// then something changed into paypal responses.
							// var_dump( $this->httpParsedResponseAr, $this->httpParsedResponseAr["L_TRANSACTIONID$i"] );
							throw new Exception();
						}

						continue;
					}

					$transaction_ids[] = $this->httpParsedResponseAr["L_TRANSACTIONID$i"];
				}

				// no payment_transaction_ids in results
				if( empty( $transaction_ids ) ){
					return false;
				}

				// found the payment transaction id, it's the last one (the oldest)
				$payment_transaction_id = end( $transaction_ids );
				return $this->getTransactionDetails( $payment_transaction_id );
			}else{
				/** Recurring payment **/
				return $this->getTransactionDetails( $order->payment_transaction_id );
			}
		}
		
		function getTransactionDetails($payment_transaction_id)
        	{
			$nvpStr = "";
			$nvpStr .= "&TRANSACTIONID=" . urlencode($payment_transaction_id);

			$this->httpParsedResponseAr = $this->PPHttpPost('GetTransactionDetails', $nvpStr);

			if("SUCCESS" == strtoupper($this->httpParsedResponseAr["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($this->httpParsedResponseAr["ACK"]))
			{
				return $this->httpParsedResponseAr;
			}
			else
			{
				// var_dump( $this->httpParsedResponseAr, $this->httpParsedResponseAr["L_TRANSACTIONID$i"] );
				return false;
			}
		}

		/**
		 * PAYPAL Function
		 * Send HTTP POST Request
		 *
		 * @param	string	The API method name
		 * @param	string	The POST Message fields in &name=value pair format
		 * @return	array	Parsed HTTP Response body
		 */
		function PPHttpPost($methodName_, $nvpStr_) {
			global $gateway_environment;
			$environment = $gateway_environment;

			$API_UserName = pmpro_getOption("apiusername");
			$API_Password = pmpro_getOption("apipassword");
			$API_Signature = pmpro_getOption("apisignature");
			$API_Endpoint = "https://api-3t.paypal.com/nvp";
			if("sandbox" === $environment || "beta-sandbox" === $environment) {
				$API_Endpoint = "https://api-3t.$environment.paypal.com/nvp";
			}

			$version = urlencode('72.0');

			// NVPRequest for submitting to server
			$nvpreq = "METHOD=" . urlencode($methodName_) . "&VERSION=" . urlencode($version) . "&PWD=" . urlencode($API_Password) . "&USER=" . urlencode($API_UserName) . "&SIGNATURE=" . urlencode($API_Signature) . "&BUTTONSOURCE=" . urlencode(PAYPAL_BN_CODE) . $nvpStr_;

			//post to PayPal
			$response = wp_remote_post( $API_Endpoint, array(
					'timeout' => 60,
					'sslverify' => FALSE,
					'httpversion' => '1.1',
					'body' => $nvpreq
			    )
			);

			if ( is_wp_error( $response ) ) {
			   $error_message = $response->get_error_message();
			   die( "methodName_ failed: $error_message" );
			} else {
				//extract the response details
				$httpParsedResponseAr = array();
				parse_str(wp_remote_retrieve_body($response), $httpParsedResponseAr);

				//check for valid response
				if((0 == sizeof($httpParsedResponseAr)) || !array_key_exists('ACK', $httpParsedResponseAr)) {
					exit("Invalid HTTP Response for POST request($nvpreq) to $API_Endpoint.");
				}
			}

			return $httpParsedResponseAr;
		}
	}
