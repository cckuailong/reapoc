<?php
	//include pmprogateway
	require_once(dirname(__FILE__) . "/class.pmprogateway.php");

	//load classes init method
	add_action('init', array('PMProGateway_paypalstandard', 'init'));

	class PMProGateway_paypalstandard extends PMProGateway
	{
		/**
		 * PMProGateway_paypalstandard constructor.
		 *
		 * @param null|string $gateway
         *
         * return string
		 */
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
			//make sure PayPal Express is a gateway option
			add_filter('pmpro_gateways', array('PMProGateway_paypalstandard', 'pmpro_gateways'));

			//add fields to payment settings
			add_filter('pmpro_payment_options', array('PMProGateway_paypalstandard', 'pmpro_payment_options'));

			/*
				This code is the same for PayPal Website Payments Pro, PayPal Express, and PayPal Standard
				So we only load it if we haven't already.
			*/
			global $pmpro_payment_option_fields_for_paypal;
			if(empty($pmpro_payment_option_fields_for_paypal))
			{
				add_filter('pmpro_payment_option_fields', array('PMProGateway_paypalstandard', 'pmpro_payment_option_fields'), 10, 2);
				$pmpro_payment_option_fields_for_paypal = true;
			}

			//code to add at checkout
			$gateway = pmpro_getGateway();
			if($gateway == "paypalstandard")
			{
				add_filter('pmpro_include_billing_address_fields', '__return_false');
				add_filter('pmpro_include_payment_information_fields', '__return_false');
				add_filter('pmpro_required_billing_fields', array('PMProGateway_paypalstandard', 'pmpro_required_billing_fields'));
				add_filter('pmpro_checkout_default_submit_button', array('PMProGateway_paypalstandard', 'pmpro_checkout_default_submit_button'));
				add_filter('pmpro_checkout_before_change_membership_level', array('PMProGateway_paypalstandard', 'pmpro_checkout_before_change_membership_level'), 10, 2);
			}
		}

		/**
		 * Make sure this gateway is in the gateways list
		 *
		 * @param array $gateways - Array of recognized gateway identifiers
         *
         * @return array
         *
		 * @since 1.8
		 */
		static function pmpro_gateways($gateways)
		{
			if(empty($gateways['paypalstandard']))
				$gateways['paypalstandard'] = __('PayPal Standard', 'paid-memberships-pro' );

			return $gateways;
		}

		/**
		 * Get a list of payment options that the this gateway needs/supports.
		 *
		 * @return array
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
				'currency',
				'use_ssl',
				'tax_state',
				'tax_rate',
			);

			return $options;
		}

		/**
		 * Set payment options for payment settings page.
		 *
		 * @param array $options
         *
         * @return array
         *
		 * @since 1.8
		 */
		static function pmpro_payment_options($options)
		{
			//get stripe options
			$paypal_options = PMProGateway_paypalexpress::getGatewayOptions();

			//merge with others.
			$options = array_merge($paypal_options, $options);

			return $options;
		}

		/**
		 * Display fields for this gateway's options.
		 *
         * @param array     $values
         * @param string    $gateway
         *
		 * @since 1.8
		 */
		static function pmpro_payment_option_fields($values, $gateway)
		{
		?>
		<tr class="pmpro_settings_divider gateway gateway_paypal gateway_paypalexpress gateway_paypalstandard" <?php if($gateway != "paypal" && $gateway != "paypalexpress" && $gateway != "paypalstandard") { ?>style="display: none;"<?php } ?>>
			<td colspan="2">
				<hr />
				<h3><?php _e('PayPal Settings', 'paid-memberships-pro' ); ?></h3>
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
				<input type="text" id="gateway_email" name="gateway_email" size="60" value="<?php echo esc_attr($values['gateway_email'])?>" />
			</td>
		</tr>
		<tr class="gateway gateway_paypal gateway_paypalexpress" <?php if($gateway != "paypal" && $gateway != "paypalexpress") { ?>style="display: none;"<?php } ?>>
			<th scope="row" valign="top">
				<label for="apiusername"><?php _e('API Username', 'paid-memberships-pro' );?>:</label>
			</th>
			<td>
				<input type="text" id="apiusername" name="apiusername" size="60" value="<?php echo esc_attr($values['apiusername'])?>" />
			</td>
		</tr>
		<tr class="gateway gateway_paypal gateway_paypalexpress" <?php if($gateway != "paypal" && $gateway != "paypalexpress") { ?>style="display: none;"<?php } ?>>
			<th scope="row" valign="top">
				<label for="apipassword"><?php _e('API Password', 'paid-memberships-pro' );?>:</label>
			</th>
			<td>
				<input type="text" id="apipassword" name="apipassword" size="60" value="<?php echo esc_attr($values['apipassword'])?>" autocomplete="off" class="regular-text code pmpro-admin-secure-key" />
			</td>
		</tr>
		<tr class="gateway gateway_paypal gateway_paypalexpress" <?php if($gateway != "paypal" && $gateway != "paypalexpress") { ?>style="display: none;"<?php } ?>>
			<th scope="row" valign="top">
				<label for="apisignature"><?php _e('API Signature', 'paid-memberships-pro' );?>:</label>
			</th>
			<td>
				<input type="text" id="apisignature" name="apisignature" size="60" value="<?php echo esc_attr($values['apisignature'])?>" />
			</td>
		</tr>
		<tr class="gateway gateway_paypal gateway_paypalexpress gateway_paypalstandard" <?php if($gateway != "paypal" && $gateway != "paypalexpress" && $gateway != "paypalstandard") { ?>style="display: none;"<?php } ?>>
			<th scope="row" valign="top">
				<label><?php _e('IPN Handler URL', 'paid-memberships-pro' );?>:</label>
			</th>
			<td>
				<p><?php _e('Here is your IPN URL for reference. You SHOULD NOT set this in your PayPal settings.', 'paid-memberships-pro' );?> <pre><?php echo add_query_arg( 'action', 'ipnhandler', admin_url('admin-ajax.php') );?></pre></p>
			</td>
		</tr>
		<?php
		}

		/**
		 * Remove required billing fields
		 *
		 * @param array $fields
         *
         * @return array
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
         * @param bool $show
         *
         * @return bool
         *
		 * @since 1.8
		 */
		static function pmpro_checkout_default_submit_button($show)
		{
			global $gateway, $pmpro_requirebilling;

			//show our submit buttons
			?>
			<span id="pmpro_paypalexpress_checkout" <?php if(($gateway != "paypalexpress" && $gateway != "paypalstandard") || !$pmpro_requirebilling) { ?>style="display: none;"<?php } ?>>
				<input type="hidden" name="submit-checkout" value="1" />
				<input type="image" value="<?php _e('Check Out with PayPal', 'paid-memberships-pro' );?> &raquo;" src="<?php echo apply_filters("pmpro_paypal_button_image", "https://www.paypalobjects.com/webstatic/en_US/i/buttons/checkout-logo-medium.png");?>" />
			</span>

			<span id="pmpro_submit_span" <?php if(($gateway == "paypalexpress" || $gateway == "paypalstandard") && $pmpro_requirebilling) { ?>style="display: none;"<?php } ?>>
				<input type="hidden" name="submit-checkout" value="1" />
				<input type="submit" class="<?php echo pmpro_get_element_class( 'pmpro_btn pmpro_btn-submit-checkout', 'pmpro_btn-submit-checkout' ); ?>" value="<?php if($pmpro_requirebilling) { _e('Submit and Check Out', 'paid-memberships-pro' ); } else { _e('Submit and Confirm', 'paid-memberships-pro' );}?> &raquo;" />
			</span>
			<?php

			//don't show the default
			return false;
		}

		/**
		 * Instead of change membership levels, send users to PayPal to pay.
		 *
         * @param int           $user_id
         * @param \MemberOrder  $morder
         *
		 * @since 1.8
		 */
		static function pmpro_checkout_before_change_membership_level($user_id, $morder)
		{
			global $discount_code_id, $wpdb;

			//if no order, no need to pay
			if(empty($morder))
				return;

			$morder->user_id = $user_id;
			$morder->saveOrder();

			//save discount code use
			if(!empty($discount_code_id))
				$wpdb->query("INSERT INTO $wpdb->pmpro_discount_codes_uses (code_id, user_id, order_id, timestamp) VALUES('" . $discount_code_id . "', '" . $user_id . "', '" . $morder->id . "', now())");

			do_action("pmpro_before_send_to_paypal_standard", $user_id, $morder);

			$morder->Gateway->sendToPayPal($morder);
		}

		/**
		 * Process checkout.
		 *
		 * @param \MemberOrder $order
		 *
         * @return bool
		 */
		function process(&$order)
		{
			if(empty($order->code))
				$order->code = $order->getRandomCode();

			//clean up a couple values
			$order->payment_type = "PayPal Standard";
			$order->CardType = "";
			$order->cardtype = "";

			//just save, the user will go to PayPal to pay
			$order->status = "review";
			$order->saveOrder();

			return true;
		}

		/**
         * Send the data/order to PayPal.com's server
         *
		 * @param \MemberOrder $order
		 */
		function sendToPayPal(&$order)
		{
			global $pmpro_currency;

			//taxes on initial amount
			$initial_payment = $order->InitialPayment;
			$initial_payment_tax = $order->getTaxForPrice($initial_payment);
			$initial_payment = pmpro_round_price((float)$initial_payment + (float)$initial_payment_tax);

			//taxes on the amount
			$amount = $order->PaymentAmount;
			$amount_tax = $order->getTaxForPrice($amount);
			$amount = pmpro_round_price((float)$amount + (float)$amount_tax);

			//build PayPal Redirect	URL
			$environment = pmpro_getOption("gateway_environment");

			if("sandbox" === $environment || "beta-sandbox" === $environment) {
				$paypal_url = "https://www.sandbox.paypal.com/cgi-bin/webscr";
			} else {
				$paypal_url = "https://www.paypal.com/cgi-bin/webscr";
			}

			if(pmpro_isLevelRecurring($order->membership_level))
			{
				//convert billing period
				if($order->BillingPeriod == "Day")
					$period = "D";
				elseif($order->BillingPeriod == "Week")
					$period = "W";
				elseif($order->BillingPeriod == "Month")
					$period = "M";
				elseif($order->BillingPeriod == "Year")
					$period = "Y";
				else
				{
					$order->error = "Invalid billing period: " . $order->BillingPeriod;
					$order->shorterror = "Invalid billing period: " . $order->BillingPeriod;
					return false;
				}

				//other args
				$paypal_args = array(
                    'business'      => pmpro_getOption("gateway_email"),
					'cmd'           => '_xclick-subscriptions',
					'a1'			=> number_format($initial_payment, 2, '.', ''),
					'p1'			=> $order->BillingFrequency,
					't1'			=> $period,
					'a3'			=> number_format($amount, 2, '.', ''),
					'p3'			=> $order->BillingFrequency,
					't3'			=> $period,
					'item_name'     => apply_filters( 'pmpro_paypal_level_description', substr($order->membership_level->name . " at " . get_bloginfo("name"), 0, 127), $order->membership_level->name, $order, get_bloginfo("name") ),
					'email'         => $order->Email,
					'no_shipping'   => '1',
					'shipping'      => '0',
					'no_note'       => '1',
					'currency_code' => $pmpro_currency,
					'item_number'   => $order->code,
					'charset'       => get_bloginfo( 'charset' ),
					'rm'            => '2',
					'return'        => add_query_arg( 'level', $order->membership_level->id, pmpro_url("confirmation" ) ),
					'notify_url'    => add_query_arg( 'action', 'ipnhandler', admin_url("admin-ajax.php") ),
					'src'			=> '1',
					'sra'			=> '1',
					'bn'			=> PAYPAL_BN_CODE,
					'MAXFAILEDPAYMENTS' => 1
				);

				//trial?
				/*
					Note here that the TrialBillingCycles value is being ignored. PayPal Standard only offers 1 payment during each trial period.
				*/
				if(!empty($order->TrialBillingPeriod))
				{
					//if a1 and a2 are 0, let's just combine them. PayPal doesn't like a2 = 0.
					if($paypal_args['a1'] == 0 && $order->TrialAmount == 0)
					{
						$paypal_args['p1'] = $paypal_args['p1'] + $order->TrialBillingFrequency;
					}
					else
					{
						$trial_amount = $order->TrialAmount;
						$trial_tax = $order->getTaxForPrice($trial_amount);
						$trial_amount = pmpro_round_price((float)$trial_amount + (float)$trial_tax);

						$paypal_args['a2'] = $trial_amount;
						$paypal_args['p2'] = $order->TrialBillingFrequency;
						$paypal_args['t2'] = $period;
					}
				}
				else
				{
					//we can try to work in any change in ProfileStartDate
					$psd = sprintf( '%1$sT0:0:0',
                        date_i18n(
                                "Y-m-d",
                                strtotime(
                                        sprintf(
                                                "+ %s %s",
                                                $order->BillingFrequency,
                                                $order->BillingPeriod
                                            ),
                                        current_time("timestamp" )
                                )
                        )
                    );

					$adjusted_psd = apply_filters("pmpro_profile_start_date", $psd, $order);

					if($psd != $adjusted_psd)
					{
						//someone is trying to push the start date back
						$adjusted_psd_time = strtotime($adjusted_psd, current_time("timestamp"));
						$seconds_til_psd = $adjusted_psd_time - current_time('timestamp');
						$days_til_psd = floor($seconds_til_psd/(60*60*24));

						//push back trial one by days_til_psd
						if($days_til_psd > 90)
						{
							//we need to convert to weeks, because PayPal limits t1 to 90 days
							$weeks_til_psd = round($days_til_psd / 7);
							$paypal_args['p1'] = $weeks_til_psd;
							$paypal_args['t1'] = "W";
						}
						elseif($days_til_psd > 0)
						{
							//use days
							$paypal_args['p1'] = $days_til_psd;
							$paypal_args['t1'] = "D";
						}
					}
				}

				//billing limit?
				if(!empty($order->TotalBillingCycles))
				{
					if(!empty($trial_amount))
					{

						$srt = intval($order->TotalBillingCycles) - 1;	//subtract one for the trial period
					}
					else
					{
						$srt = intval($order->TotalBillingCycles);
					}

					//srt must be at least 2 or the subscription is not "recurring" according to paypal
					if($srt > 1)
						$paypal_args['srt'] = $srt;
					else
						$paypal_args['src'] = '0';
				}
				else
					$paypal_args['srt'] = '0';	//indefinite subscription
			}
			else
			{
				//other args
				$paypal_args = array(
					'business'      => pmpro_getOption("gateway_email"),
					'cmd'           => '_xclick',
					'amount'        => number_format($initial_payment, 2, '.', ''),
					'item_name'     => apply_filters( 'pmpro_paypal_level_description', substr($order->membership_level->name . " at " . get_bloginfo("name"), 0, 127), $order->membership_level->name, $order, get_bloginfo("name") ),
					'email'         => $order->Email,
					'no_shipping'   => '1',
					'shipping'      => '0',
					'no_note'       => '1',
					'currency_code' => $pmpro_currency,
					'item_number'   => $order->code,
					'charset'       => get_bloginfo( 'charset' ),
					'rm'            => '2',
					'return'        => add_query_arg( 'level', $order->membership_level->id, pmpro_url("confirmation" ) ),
					'notify_url'    => add_query_arg( 'action', 'ipnhandler', admin_url("admin-ajax.php") ),
					'bn'		    => PAYPAL_BN_CODE
				 );
			}

			//anything modders might add
			$additional_parameters = apply_filters("pmpro_paypal_express_return_url_parameters", array());

			foreach( $additional_parameters as $key => $value ) {

				$paypal_args[$key] = $value;
 			}

			$nvpStr = "";

			$account_optional = apply_filters('pmpro_paypal_account_optional', true);

			if ($account_optional) {
				$paypal_args['SOLUTIONTYPE'] = "Sole";
				$paypal_args['LANDINGPAGE'] = "Billing";
			}

			$nvpStr = http_build_query( $paypal_args );
			$nvpStr = apply_filters("pmpro_paypal_standard_nvpstr", $nvpStr, $order);

			//Build complete URI for paypal redirect
			$paypal_url = "{$paypal_url}?{$nvpStr}";

			//wp_die(str_replace("&", "<br />", $paypal_url));
			wp_redirect($paypal_url);
			exit;
		}

		/**
         * Cancel the member order at the payment gateway (if possible),
         * or redirect the user (via the PayPal Sign-on page) to the
         * specific "Subscription details" page for this order on PayPal.com
         *
		 * @param \MemberOrder $order
		 *
		 * @return bool
		 */
		function cancel(&$order)
		{
			$nvp_args    = array();

			$gateway     = pmpro_getGateway();
            $environment = pmpro_getOption("gateway_environment");
			$signature   = pmpro_getOption("apisignature");

			if("sandbox" === $environment || "beta-sandbox" === $environment) {
				$paypal_url = "https://www.sandbox.paypal.com/cgi-bin/webscr";
			} else {
				$paypal_url = "https://www.paypal.com/cgi-bin/webscr";
			}

			if ( empty( $order->membership_level ) ) {
			    $order->membership_level = pmpro_getMembershipLevelForUser( $order->user_id );
            }

			// Send user to PayPal Recurring Billing page.
			if ( "paypalstandard" == $gateway && empty( $signature ) && true === pmpro_isLevelRecurring($order->membership_level ) ) {

			    // Required arguments for the Subscription detail page of this order
			    $nvp_args['cmd'] = '_profile-recurring-payments';
			    $nvp_args['return_to'] = 'txn_details';
			    $nvp_args['encrypted_profile_id'] = $order->subscription_transaction_id;

			    // Building URI for Recurring Billing Plan cancellation page
			    $cancel_url = add_query_arg( $nvp_args, $paypal_url );

                // Select sign-in page based on environment
				if("sandbox" === $environment || "beta-sandbox" === $environment) {
					$paypal_signin = "https://www.sandbox.paypal.com/signin";
				} else {
					$paypal_signin = "https://www.paypal.com/signin";
				}

				// Build URI to cancellation page for their plan via the PayPal Sign-in page.
				$paypal_signin = add_query_arg( 'returnUri', urlencode( $cancel_url ), $paypal_signin );

				// Send them to the PayPal sign-in page with a redirect to the subscription plan cancellation page
			    wp_redirect( $paypal_signin );
			    exit;
            }

			// PayPal profile info for if/when PayPal Express credentials happen to exist in our settings
            $nvp_args['PROFILEID'] = $order->subscription_transaction_id;
			$nvp_args['ACTION'] = 'Cancel';
			$nvp_args['NOTE'] = __( "User requested cancellation", "paid-memberships-pro" );

			// Encode the query
			$nvpStr = http_build_query( $nvp_args );

			$this->httpParsedResponseAr = $this->PPHttpPost('ManageRecurringPaymentsProfileStatus', $nvpStr);

			if("SUCCESS" == strtoupper($this->httpParsedResponseAr["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($this->httpParsedResponseAr["ACK"]))
			{
				$order->updateStatus("cancelled");
				return true;
			}
			else
			{
				$order->status = "error";
				$order->errorcode = $this->httpParsedResponseAr['L_ERRORCODE0'];
				$order->error = urldecode($this->httpParsedResponseAr['L_LONGMESSAGE0']) . ". " . __("Please contact the site owner or cancel your subscription from within PayPal to make sure you are not charged going forward.", 'paid-memberships-pro' );
				$order->shorterror = urldecode($this->httpParsedResponseAr['L_SHORTMESSAGE0']);

				return false;
			}
		}

		/**
		 * PAYPAL Function
		 * Send HTTP POST Request
		 *
		 * @param	string	$methodName_ The API method name
		 * @param	string	$nvpStr_ The POST Message fields in &name=value pair format
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

            $nvp_args = array(
                'METHOD' => $methodName_,
                'VERSION' => '72.0',
                'bn' => PAYPAL_BN_CODE,
            );

			if ( ! empty( $API_UserName ) ) {
			    $nvp_args['USER'] = $API_UserName;
            }

			if ( !empty( $API_Password ) ) {
			    $nvp_args['PWD'] = $API_Password;
            }

            if ( !empty( $API_Signature ) ) {
			    $nvp_args['SIGNATURE'] = $API_Signature;
            }

			$nvpreq = http_build_query( $nvp_args );
            $nvpreq = "{$nvpStr_}&{$nvpreq}";

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
			   die( "{$methodName_} failed: $error_message" );
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
