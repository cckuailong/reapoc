<?php

use Braintree\WebhookNotification as Braintree_WebhookNotification;

	//include pmprogateway
	require_once(dirname(__FILE__) . "/class.pmprogateway.php");

	//load classes init method
	add_action('init', array('PMProGateway_braintree', 'init'));

	class PMProGateway_braintree extends PMProGateway
	{
		/**
		 * @var bool    Is the Braintree/PHP Library loaded
		 */
		private static $is_loaded = false;

		function __construct($gateway = NULL)
		{
			$this->gateway = $gateway;
			$this->gateway_environment = pmpro_getOption("gateway_environment");

			if( true === $this->dependencies() ) {
				$this->loadBraintreeLibrary();

				//convert to braintree nomenclature
				$environment = $this->gateway_environment;
				if($environment == "live")
					$environment = "production";

				$merch_id = pmpro_getOption( "braintree_merchantid" );
				$pk = pmpro_getOption( "braintree_publickey" );
				$sk = pmpro_getOption( "braintree_privatekey" );

                try {

                    Braintree_Configuration::environment( $environment );
                    Braintree_Configuration::merchantId( $merch_id );
                    Braintree_Configuration::publicKey( $pk );
                    Braintree_Configuration::privateKey( $sk );

                } catch( Exception $exception ) {
                    global $msg;
                    global $msgt;
                    global $pmpro_braintree_error;

                    error_log($exception->getMessage() );

                        $pmpro_braintree_error = true;
                        $msg                   = - 1;
                        $msgt                  = sprintf( __( 'Attempting to load Braintree gateway: %s', 'paid-memberships-pro' ), $exception->getMessage() );
                    return false;
                }

				self::$is_loaded = true;
			}

			return $this->gateway;
		}
		/**
		 * Warn if required extensions aren't loaded.
		 *
		 * @return bool
		 * @since 1.8.6.8.1
		 */
		public static function dependencies()
		{
			global $msg, $msgt, $pmpro_braintree_error;

			if ( version_compare( PHP_VERSION, '5.4.45', '<' )) {

				$msg = -1;
				$msgt = sprintf(__("The Braintree Gateway requires PHP 5.4.45 or greater. We recommend upgrading to PHP %s or greater. Ask your host to upgrade.", "paid-memberships-pro" ), PMPRO_PHP_MIN_VERSION );

				pmpro_setMessage( $msgt, "pmpro_error" );
				return false;
			}

			$modules = array('xmlwriter', 'SimpleXML', 'openssl', 'dom', 'hash', 'curl');

			foreach($modules as $module){
				if(!extension_loaded($module)){

				    if ( false == $pmpro_braintree_error ) {
					    $pmpro_braintree_error = true;
					    $msg                   = - 1;
					    $msgt                  = sprintf( __( "The %s gateway depends on the %s PHP extension. Please enable it, or ask your hosting provider to enable it.", 'paid-memberships-pro' ), 'Braintree', $module );
				    }

					//throw error on checkout page
					if ( ! is_admin() ) {
						pmpro_setMessage( $msgt, 'pmpro_error' );
					}

					return false;
				}
			}

			self::$is_loaded = true;
			return true;
		}

		/**
		 * Load the Braintree API library.
		 *
		 * @since 1.8.1
		 * Moved into a method in version 1.8.1 so we only load it when needed.
		 */
		function loadBraintreeLibrary()
		{
			//load Braintree library if it hasn't been loaded already (usually by another plugin using Braintree)
			if(!class_exists("\Braintree"))
				require_once( PMPRO_DIR . "/includes/lib/Braintree/lib/Braintree.php");
		}

		/**
		 * Get a collection of plans available for this Braintree account.
		 */
		function getPlans($force = false) {
			//check for cache
			$cache_key = 'pmpro_braintree_plans_' . md5($this->gateway_environment . pmpro_getOption("braintree_merchantid") . pmpro_getOption("braintree_publickey") . pmpro_getOption("braintree_privatekey"));

      $plans = wp_cache_get( $cache_key,'pmpro_levels' );
			
			//check Braintree if no transient found
			if($plans === false) {

			    try {
				    $plans = Braintree_Plan::all();

			    } catch( Braintree\Exception $exception ) {

			        global $msg;
			        global $msgt;
				    global $pmpro_braintree_error;

				    if ( false == $pmpro_braintree_error ) {

				        $pmpro_braintree_error = true;
					    $msg                   = - 1;
					    $status = $exception->getMessage();

					    if ( !empty( $status)) {
						    $msgt = sprintf( __( "Problem loading plans: %s", "paid-memberships-pro" ), $status );
					    } else {
					        $msgt = __( "Problem accessing the Braintree Gateway. Please verify your PMPro Payment Settings (Keys, etc).", "paid-memberships-pro");
                        }
				    }

			        return false;
                }

                // Save to local cache
                if ( !empty( $plans ) ) {
	                /**
	                 * @since v1.9.5.4+ - BUG FIX: Didn't expire transient
                     * @since v1.9.5.4+ - ENHANCEMENT: Use wp_cache_*() system over direct transients
	                 */
                    wp_cache_set( $cache_key,$plans,'pmpro_levels',HOUR_IN_SECONDS );
                }
			}

			return $plans;
		}

		/**
         * Clear cached plans when updating membership level
         *
		 * @param $level_id
		 */
		public static function pmpro_save_level_action( $level_id ) {
		    
		    $BT_Gateway = new PMProGateway_braintree();
		    
		    if ( isset( $BT_Gateway->gateway_environment ) ) {
			    $cache_key = 'pmpro_braintree_plans_' . md5($BT_Gateway->gateway_environment . pmpro_getOption("braintree_merchantid") . pmpro_getOption("braintree_publickey") . pmpro_getOption("braintree_privatekey"));
			
			    wp_cache_delete( $cache_key,'pmpro_levels' );
		    }
		}
		
		/**
		 * Search for a plan by id
		 */
		function getPlanByID($id) {
			$plans = $this->getPlans();

			if(!empty($plans)) {
				foreach($plans as $plan) {
					if($plan->id == $id)
						return $plan;
				}
			}

			return false;
		}

		/**
		 * Checks if a level has an associated plan.
		 */
		static function checkLevelForPlan($level_id) {
			$Gateway = new PMProGateway_braintree();

			$plan = $Gateway->getPlanByID( $Gateway->get_plan_id( $level_id ) );

			if(!empty($plan))
				return true;
			else
				return false;
		}

		/**
		 * Run on WP init
		 *
		 * @since 1.8
		 */
		static function init()
		{
			//make sure Braintree Payments is a gateway option
			add_filter('pmpro_gateways', array('PMProGateway_braintree', 'pmpro_gateways'));

			//add fields to payment settings
			add_filter('pmpro_payment_options', array('PMProGateway_braintree', 'pmpro_payment_options'));
			add_filter('pmpro_payment_option_fields', array('PMProGateway_braintree', 'pmpro_payment_option_fields'), 10, 2);

			//code to add at checkout if Braintree is the current gateway
			$default_gateway = pmpro_getOption('gateway');
			$current_gateway = pmpro_getGateway();
			if( ( $default_gateway == "braintree" || $current_gateway == "braintree" && empty($_REQUEST['review'])))	//$_REQUEST['review'] means the PayPal Express review page
			{
			    add_action('pmpro_checkout_preheader', array('PMProGateway_braintree', 'pmpro_checkout_preheader'));
				add_action( 'pmpro_billing_preheader', array( 'PMProGateway_braintree', 'pmpro_checkout_preheader' ) );
				add_action( 'pmpro_save_membership_level', array( 'PMProGateway_braintree', 'pmpro_save_level_action') );
				add_action('pmpro_checkout_before_submit_button', array('PMProGateway_braintree', 'pmpro_checkout_before_submit_button'));
				add_action('pmpro_billing_before_submit_button', array('PMProGateway_braintree', 'pmpro_checkout_before_submit_button'));
				add_filter('pmpro_checkout_order', array('PMProGateway_braintree', 'pmpro_checkout_order'));
				add_filter('pmpro_billing_order', array('PMProGateway_braintree', 'pmpro_checkout_order'));
				add_filter('pmpro_required_billing_fields', array('PMProGateway_braintree', 'pmpro_required_billing_fields'));
				add_filter('pmpro_include_payment_information_fields', array('PMProGateway_braintree', 'pmpro_include_payment_information_fields'));
			}
		}

		/**
		 * Make sure this gateway is in the gateways list
		 *
		 * @since 1.8
		 */
		static function pmpro_gateways($gateways)
		{
			if(empty($gateways['braintree']))
				$gateways['braintree'] = __('Braintree Payments', 'paid-memberships-pro' );

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
				'braintree_merchantid',
				'braintree_publickey',
				'braintree_privatekey',
				'braintree_encryptionkey',
				'currency',
				'use_ssl',
				'tax_state',
				'tax_rate',
				'accepted_credit_cards',
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
			//get Braintree options
			$braintree_options = self::getGatewayOptions();

			//merge with others.
			$options = array_merge($braintree_options, $options);

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
		<tr class="pmpro_settings_divider gateway gateway_braintree" <?php if($gateway != "braintree") { ?>style="display: none;"<?php } ?>>
			<td colspan="2">
				<hr />
				<h2 class="title"><?php esc_html_e( 'Braintree Settings', 'paid-memberships-pro' ); ?></h2>
			</td>
		</tr>
		<tr class="gateway gateway_braintree" <?php if($gateway != "braintree") { ?>style="display: none;"<?php } ?>>
			<th scope="row" valign="top">
				<label for="braintree_merchantid"><?php _e('Merchant ID', 'paid-memberships-pro' );?>:</label>
			</th>
			<td>
				<input type="text" id="braintree_merchantid" name="braintree_merchantid" value="<?php echo esc_attr($values['braintree_merchantid'])?>" class="regular-text code" />
			</td>
		</tr>
		<tr class="gateway gateway_braintree" <?php if($gateway != "braintree") { ?>style="display: none;"<?php } ?>>
			<th scope="row" valign="top">
				<label for="braintree_publickey"><?php _e('Public Key', 'paid-memberships-pro' );?>:</label>
			</th>
			<td>
				<input type="text" id="braintree_publickey" name="braintree_publickey" value="<?php echo esc_attr($values['braintree_publickey'])?>" class="regular-text code" />
			</td>
		</tr>
		<tr class="gateway gateway_braintree" <?php if($gateway != "braintree") { ?>style="display: none;"<?php } ?>>
			<th scope="row" valign="top">
				<label for="braintree_privatekey"><?php _e('Private Key', 'paid-memberships-pro' );?>:</label>
			</th>
			<td>
				<input type="text" id="braintree_privatekey" name="braintree_privatekey" value="<?php echo esc_attr($values['braintree_privatekey'])?>" autocomplete="off" class="regular-text code pmpro-admin-secure-key" />
			</td>
		</tr>
		<tr class="gateway gateway_braintree" <?php if($gateway != "braintree") { ?>style="display: none;"<?php } ?>>
			<th scope="row" valign="top">
				<label for="braintree_encryptionkey"><?php _e('Client-Side Encryption Key', 'paid-memberships-pro' );?>:</label>
			</th>
			<td>
				<textarea id="braintree_encryptionkey" name="braintree_encryptionkey" autocomplete="off" rows="3" cols="50" class="large-text code pmpro-admin-secure-key"><?php echo esc_textarea($values['braintree_encryptionkey'])?></textarea>
			</td>
		</tr>
		<tr class="gateway gateway_braintree" <?php if($gateway != "braintree") { ?>style="display: none;"<?php } ?>>
			<th scope="row" valign="top">
				<label><?php _e('Web Hook URL', 'paid-memberships-pro' );?>:</label>
			</th>
			<td>
				<p><?php _e('To fully integrate with Braintree, be sure to set your Web Hook URL to', 'paid-memberships-pro' );?></p>
				<p><code><?php
						//echo admin_url("admin-ajax.php") . "?action=braintree_webhook";
						echo add_query_arg( 'action', 'braintree_webhook', admin_url( 'admin-ajax.php' ) );
				?></code></p>
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
			global $gateway, $pmpro_level;

			$default_gateway = pmpro_getOption("gateway");

			if(($gateway == "braintree" || $default_gateway == "braintree")) {
				wp_enqueue_script("stripe", "https://js.braintreegateway.com/v1/braintree.js", array(), NULL);
				wp_register_script( 'pmpro_braintree',
                            plugins_url( 'js/pmpro-braintree.js', PMPRO_BASE_FILE ),
                            array( 'jquery' ),
                            PMPRO_VERSION );
				wp_localize_script( 'pmpro_braintree', 'pmpro_braintree', array(
					'encryptionkey' => pmpro_getOption( 'braintree_encryptionkey' )
				));
				wp_enqueue_script( 'pmpro_braintree' );
			}
		}

		/**
		 * Filtering orders at checkout.
		 *
		 * @since 1.8
		 */
		static function pmpro_checkout_order($morder)
		{
			//load up values
			if(isset($_REQUEST['number']))
				$braintree_number = sanitize_text_field($_REQUEST['number']);
			else
				$braintree_number = "";

			if(isset($_REQUEST['expiration_date']))
				$braintree_expiration_date = sanitize_text_field($_REQUEST['expiration_date']);
			else
				$braintree_expiration_date = "";

			if(isset($_REQUEST['cvv']))
				$braintree_cvv = sanitize_text_field($_REQUEST['cvv']);
			else
				$braintree_cvv = "";

			$morder->braintree = new stdClass();
			$morder->braintree->number = $braintree_number;
			$morder->braintree->expiration_date = $braintree_expiration_date;
			$morder->braintree->cvv = $braintree_cvv;

			return $morder;
		}

		/**
		 * Don't require the CVV, but look for cvv (lowercase) that braintree sends
		 *
		 */
		static function pmpro_required_billing_fields($fields)
		{
			unset($fields['CVV']);
			$fields['cvv'] = true;
			return $fields;
		}

		/**
		 * Add some hidden fields and JavaScript to checkout.
		 *
		 */
		static function pmpro_checkout_before_submit_button()
		{
		?>
		<input type='hidden' data-encrypted-name='expiration_date' id='credit_card_exp' />
		<input type='hidden' name='AccountNumber' id='BraintreeAccountNumber' />
		<?php
		}

		/**
		 * Use our own payment fields at checkout. (Remove the name attributes and set some data-encrypted-name attributes.)
		 * @since 1.8
		 */
		static function pmpro_include_payment_information_fields($include)
		{

			//global vars
			global $pmpro_requirebilling, $pmpro_show_discount_code, $discount_code, $CardType, $AccountNumber, $ExpirationMonth, $ExpirationYear;

			//get accepted credit cards
			$pmpro_accepted_credit_cards = pmpro_getOption("accepted_credit_cards");
			$pmpro_accepted_credit_cards = explode(",", $pmpro_accepted_credit_cards);
			$pmpro_accepted_credit_cards_string = pmpro_implodeToEnglish($pmpro_accepted_credit_cards);

			//include ours
			?>
			<div id="pmpro_payment_information_fields" class="<?php echo pmpro_get_element_class( 'pmpro_checkout', 'pmpro_payment_information_fields' ); ?>" <?php if(!$pmpro_requirebilling || apply_filters("pmpro_hide_payment_information_fields", false) ) { ?>style="display: none;"<?php } ?>>
				<h3>
					<span class="<?php echo pmpro_get_element_class( 'pmpro_checkout-h3-name' ); ?>"><?php _e('Payment Information', 'paid-memberships-pro' );?></span>
				<span class="<?php echo pmpro_get_element_class( 'pmpro_checkout-h3-msg' ); ?>"><?php printf(__('We Accept %s', 'paid-memberships-pro' ), $pmpro_accepted_credit_cards_string);?></span>
				</h3>
				<?php $sslseal = pmpro_getOption("sslseal"); ?>
				<?php if(!empty($sslseal)) { ?>
					<div class="<?php echo pmpro_get_element_class( 'pmpro_checkout-fields-display-seal' ); ?>">
				<?php } ?>
				<div class="<?php echo pmpro_get_element_class( 'pmpro_checkout-fields' ); ?>">
					<?php
						$pmpro_include_cardtype_field = apply_filters('pmpro_include_cardtype_field', true);
						if($pmpro_include_cardtype_field) { ?>
						<div class="<?php echo pmpro_get_element_class( 'pmpro_checkout-field pmpro_payment-card-type', 'pmpro_payment-card-type' ); ?>">
							<label for="CardType"><?php _e('Card Type', 'paid-memberships-pro' );?></label>
							<select id="CardType" name="CardType" class="<?php echo pmpro_get_element_class( 'CardType' ); ?>">
								<?php foreach($pmpro_accepted_credit_cards as $cc) { ?>
									<option value="<?php echo $cc?>" <?php if($CardType == $cc) { ?>selected="selected"<?php } ?>><?php echo $cc?></option>
								<?php } ?>
							</select>
						</div>
					<?php } ?>
					<div class="<?php echo pmpro_get_element_class( 'pmpro_checkout-field pmpro_payment-account-number', 'pmpro_payment-account-number' ); ?>">
						<label for="AccountNumber"><?php _e('Card Number', 'paid-memberships-pro' );?></label>
						<input id="AccountNumber" name="AccountNumber" class="<?php echo pmpro_get_element_class( 'input', 'AccountNumber' ); ?>" type="text" size="25" value="<?php echo esc_attr($AccountNumber)?>" data-encrypted-name="number" autocomplete="off" />
					</div>
					<div class="<?php echo pmpro_get_element_class( 'pmpro_checkout-field pmpro_payment-expiration', 'pmpro_payment-expiration' ); ?>">
						<label for="ExpirationMonth"><?php _e('Expiration Date', 'paid-memberships-pro' );?></label>
						<select id="ExpirationMonth" name="ExpirationMonth" class="<?php echo pmpro_get_element_class( 'ExpirationMonth' ); ?>">
							<option value="01" <?php if($ExpirationMonth == "01") { ?>selected="selected"<?php } ?>>01</option>
							<option value="02" <?php if($ExpirationMonth == "02") { ?>selected="selected"<?php } ?>>02</option>
							<option value="03" <?php if($ExpirationMonth == "03") { ?>selected="selected"<?php } ?>>03</option>
							<option value="04" <?php if($ExpirationMonth == "04") { ?>selected="selected"<?php } ?>>04</option>
							<option value="05" <?php if($ExpirationMonth == "05") { ?>selected="selected"<?php } ?>>05</option>
							<option value="06" <?php if($ExpirationMonth == "06") { ?>selected="selected"<?php } ?>>06</option>
							<option value="07" <?php if($ExpirationMonth == "07") { ?>selected="selected"<?php } ?>>07</option>
							<option value="08" <?php if($ExpirationMonth == "08") { ?>selected="selected"<?php } ?>>08</option>
							<option value="09" <?php if($ExpirationMonth == "09") { ?>selected="selected"<?php } ?>>09</option>
							<option value="10" <?php if($ExpirationMonth == "10") { ?>selected="selected"<?php } ?>>10</option>
							<option value="11" <?php if($ExpirationMonth == "11") { ?>selected="selected"<?php } ?>>11</option>
							<option value="12" <?php if($ExpirationMonth == "12") { ?>selected="selected"<?php } ?>>12</option>
						</select>/<select id="ExpirationYear" name="ExpirationYear" class="<?php echo pmpro_get_element_class( 'ExpirationYear' ); ?>">
							<?php for($i = date_i18n("Y"); $i < date_i18n("Y") + 10; $i++) { ?>
								<option value="<?php echo $i?>" <?php if($ExpirationYear == $i) { ?>selected="selected"<?php } ?>><?php echo $i?></option>
							<?php } ?>
						</select>
					</div>
					<?php
						$pmpro_show_cvv = apply_filters("pmpro_show_cvv", true);
						if($pmpro_show_cvv) { ?>
							<div class="<?php echo pmpro_get_element_class( 'pmpro_checkout-field pmpro_payment-cvv', 'pmpro_payment-cvv' ); ?>">
								<label for="CVV"><?php _e('CVV', 'paid-memberships-pro' );?></label>
								<input id="CVV" name="cvv" type="text" size="4" value="<?php if(!empty($_REQUEST['CVV'])) { echo esc_attr(sanitize_text_field($_REQUEST['CVV'])); }?>" class="<?php echo pmpro_get_element_class( 'input', 'CVV' ); ?>" data-encrypted-name="cvv" />  <small>(<a href="javascript:void(0);" onclick="javascript:window.open('<?php echo pmpro_https_filter(PMPRO_URL)?>/pages/popup-cvv.html','cvv','toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=yes, width=600, height=475');"><?php _e("what's this?", 'paid-memberships-pro' );?></a>)</small>
							</div>
					<?php } ?>
					<?php if($pmpro_show_discount_code) { ?>
						<div class="<?php echo pmpro_get_element_class( 'pmpro_checkout-field pmpro_payment-discount-code', 'pmpro_payment-discount-code' ); ?>">
							<label for="discount_code"><?php _e('Discount Code', 'paid-memberships-pro' );?></label>
							<input class="<?php echo pmpro_get_element_class( 'input', 'discount_code' ); ?>" id="discount_code" name="discount_code" type="text" size="20" value="<?php echo esc_attr($discount_code)?>" />
							<input type="button" id="discount_code_button" name="discount_code_button" value="<?php _e('Apply', 'paid-memberships-pro' );?>" />
							<p id="discount_code_message" class="<?php echo pmpro_get_element_class( 'pmpro_message' ); ?>" style="display: none;"></p>
						</div>
					<?php } ?>
				</div> <!-- end pmpro_checkout-fields -->
				<?php if(!empty($sslseal)) { ?>
					<div class="<?php echo pmpro_get_element_class( 'pmpro_checkout-fields-rightcol pmpro_sslseal', 'pmpro_sslseal' ); ?>"><?php echo stripslashes($sslseal); ?></div>
				</div> <!-- end pmpro_checkout-fields-display-seal -->
				<?php } ?>
			</div> <!-- end pmpro_payment_information_fields -->
			<?php

			//don't include the default
			return false;
		}

		/**
		 * Process checkout.
		 *
		 */
		function process(&$order)
		{
			//check for initial payment
			if(floatval($order->InitialPayment) == 0)
			{
				//just subscribe
				return $this->subscribe($order);
			}
			else
			{
				//charge then subscribe
				if($this->charge($order))
				{
					if(pmpro_isLevelRecurring($order->membership_level))
					{
						if($this->subscribe($order))
						{
							//yay!
							return true;
						}
						else
						{
							//try to refund initial charge
							return false;
						}
					}
					else
					{
						//only a one time charge
						$order->status = "success";	//saved on checkout page
						return true;
					}
				}
				else
				{
					if(empty($order->error)) {

					    if ( !self::$is_loaded ) {

					        $order->error = __("Payment error: Please contact the webmaster (braintree-load-error)", "paid-memberships-pro");

                        } else {

						    $order->error = __( "Unknown error: Initial payment failed.", "paid-memberships-pro" );
					    }
                    }

					return false;
				}
			}
		}

		function charge(&$order)
		{
		    if ( ! self::$is_loaded ) {

                $order->error = __("Payment error: Please contact the webmaster (braintree-load-error)", "paid-memberships-pro");
                return false;
            }

			//create a code for the order
			if(empty($order->code))
				$order->code = $order->getRandomCode();

			//what amount to charge?
			$amount = $order->InitialPayment;

			//tax
			$order->subtotal = $amount;
			$tax = $order->getTax(true);
			$amount = pmpro_round_price((float)$order->subtotal + (float)$tax);

			//create a customer
			$this->getCustomer($order);
			if(empty($this->customer) || !empty($order->error))
			{
				//failed to create customer
				return false;
			}

			//charge
			try
			{
				$response = Braintree_Transaction::sale(array(
				  'amount' => $amount,
				  'customerId' => $this->customer->id
				));
			}
			catch (Exception $e)
			{
				//$order->status = "error";
				$order->errorcode = true;
				$order->error = "Error: " . $e->getMessage() . " (" . get_class($e) . ")";
				$order->shorterror = $order->error;
				return false;
			}

			if($response->success)
			{
				//successful charge
				$transaction_id = $response->transaction->id;
				try {
					$response = Braintree_Transaction::submitForSettlement( $transaction_id );
				} catch ( Exception $exception ) {
					$order->errorcode = true;
					$order->error = "Error: " . $exception->getMessage() . " (" . get_class($exception) . ")";
					$order->shorterror = $order->error;
					return false;
                }

				if($response->success)
				{
					$order->payment_transaction_id = $transaction_id;
					$order->updateStatus("success");
					return true;
				}
				else
				{
					$order->errorcode = true;
					$order->error = __("Error during settlement:", 'paid-memberships-pro' ) . " " . $response->message;
					$order->shorterror = $response->message;
					return false;
				}
			}
			else
			{
				//$order->status = "error";
				$order->errorcode = true;
				$order->error = __("Error during charge:", 'paid-memberships-pro' ) . " " . $response->message;
				$order->shorterror = $response->message;
				return false;
			}
		}

		/*
			This function will return a Braintree customer object.
			If $this->customer is set, it returns it.
			It first checks if the order has a subscription_transaction_id. If so, that's the customer id.
			If not, it checks for a user_id on the order and searches for a customer id in the user meta.
			If a customer id is found, it checks for a customer through the Braintree API.
			If a customer is found and there is an AccountNumber on the order passed, it will update the customer.
			If no customer is found and there is an AccountNumber on the order passed, it will create a customer.
		*/
		function getCustomer(&$order, $force = false)
		{
            if ( ! self::$is_loaded ) {
	            $order->error = __("Payment error: Please contact the webmaster (braintree-load-error)", 'paid-memberships-pro');
	            return false;
            }

			global $current_user;

			//already have it?
			if(!empty($this->customer) && !$force)
				return $this->customer;

			//try based on user id
			if(!empty($order->user_id))
				$user_id = $order->user_id;

			//if no id passed, check the current user
			if(empty($user_id) && !empty($current_user->ID))
				$user_id = $current_user->ID;

			//check for a braintree customer id
			if(!empty($user_id))
			{
				$customer_id = get_user_meta($user_id, "pmpro_braintree_customerid", true);
			}

			//check for an existing Braintree customer
			if(!empty($customer_id))
			{
				try
				{
					$this->customer = Braintree_Customer::find($customer_id);

					//update the customer address, description and card
					if( ! empty( $order->braintree ) && ! empty( $order->braintree->number ) ) {
						//put data in array for Braintree API calls
						$update_array = array(
							'firstName' => $order->FirstName,
							'lastName' => $order->LastName,
							'creditCard' => array(
								'number' => $order->braintree->number,
								'expirationDate' => $order->braintree->expiration_date,
								'cvv' => $order->braintree->cvv,
								'cardholderName' => trim($order->FirstName . " " . $order->LastName),
								'options' => array(
									'updateExistingToken' => $this->customer->creditCards[0]->token
								)
							)
						);

						//address too?
						if(!empty($order->billing))
							//make sure Address2 is set
							if(!isset($order->Address2))
								$order->Address2 = '';

							//add billing address to array
							$update_array['creditCard']['billingAddress'] = array(
								'firstName' => $order->FirstName,
								'lastName' => $order->LastName,
								'streetAddress' => $order->Address1,
								'extendedAddress' => $order->Address2,
								'locality' => $order->billing->city,
								'region' => $order->billing->state,
								'postalCode' => $order->billing->zip,
								'countryCodeAlpha2' => $order->billing->country,
								'options' => array(
									'updateExisting' => true
								)
							);

							try {
								//update
								$response = Braintree_Customer::update($customer_id, $update_array);
                            } catch ( Exception $exception ) {
								$order->error = sprintf( __("Failed to update customer: %s", 'paid-memberships-pro' ), $exception->getMessage() );
								$order->shorterror = $order->error;
								return false;
                            }

						if($response->success)
						{
							$this->customer = $response->customer;
							return $this->customer;
						}
						else
						{
							$order->error = __("Failed to update customer.", 'paid-memberships-pro' ) . " " . $response->message;
							$order->shorterror = $order->error;
							return false;
						}
					}

					return $this->customer;
				}
				catch (Exception $e)
				{
					//assume no customer found
				}
			}

			//no customer id, create one
			if(!empty($order->accountnumber))
			{
				try
				{
					$result = Braintree_Customer::create(array(
						'firstName' => $order->FirstName,
						'lastName' => $order->LastName,
						'email' => $order->Email,
						'phone' => $order->billing->phone,
						'creditCard' => array(
							'number' => $order->braintree->number,
							'expirationDate' => $order->braintree->expiration_date,
							'cvv' => $order->braintree->cvv,
							'cardholderName' =>  trim($order->FirstName . " " . $order->LastName),
							'billingAddress' => array(
								'firstName' => $order->FirstName,
								'lastName' => $order->LastName,
								'streetAddress' => $order->Address1,
								'extendedAddress' => $order->Address2,
								'locality' => $order->billing->city,
								'region' => $order->billing->state,
								'postalCode' => $order->billing->zip,
								'countryCodeAlpha2' => $order->billing->country
							)
						)
					));

					if($result->success)
					{
						$this->customer = $result->customer;
					}
					else
					{
						$order->error = __("Failed to create customer.", 'paid-memberships-pro' ) . " " . $result->message;
						$order->shorterror = $order->error;
						return false;
					}
				}
				catch (Exception $e)
				{
					$order->error = __("Error creating customer record with Braintree:", 'paid-memberships-pro' ) . $e->getMessage() . " (" . get_class($e) . ")";
					$order->shorterror = $order->error;
					return false;
				}

				//if we have no user id, we need to set the customer id after the user is created
				if(empty($user_id))
				{
					global $pmpro_braintree_customerid;
					$pmpro_braintree_customerid = $this->customer->id;
					add_action('user_register', array('PMProGateway_braintree','user_register'));
				}
				else
					update_user_meta($user_id, "pmpro_braintree_customerid", $this->customer->id);

				return $this->customer;
			}

			return false;
		}

		/**
         * Create Braintree Subscription
         *
		 * @param \MemberOrder $order
		 *
		 * @return bool
		 */
		function subscribe(&$order)
		{
			if ( ! self::$is_loaded ) {
				$order->error = __("Payment error: Please contact the webmaster (braintree-load-error)", 'paid-memberships-pro');
				return false;
			}

			//create a code for the order
			if(empty($order->code))
				$order->code = $order->getRandomCode();

			//set up customer
			$this->getCustomer($order);
			if(empty($this->customer) || !empty($order->error))
				return false;	//error retrieving customer

			//figure out the amounts
			$amount = $order->PaymentAmount;
			$amount_tax = $order->getTaxForPrice($amount);
			$amount = pmpro_round_price((float)$amount + (float)$amount_tax);

			/*
				There are two parts to the trial. Part 1 is simply the delay until the first payment
				since we are doing the first payment as a separate transaction.
				The second part is the actual "trial" set by the admin.

				Braintree only supports Year or Month for billing periods, but we account for Days and Weeks just in case.
			*/
			//figure out the trial length (first payment handled by initial charge)
			if($order->BillingPeriod == "Year")
				$trial_period_days = $order->BillingFrequency * 365;	//annual
			elseif($order->BillingPeriod == "Day")
				$trial_period_days = $order->BillingFrequency * 1;		//daily
			elseif($order->BillingPeriod == "Week")
				$trial_period_days = $order->BillingFrequency * 7;		//weekly
			else
				$trial_period_days = $order->BillingFrequency * 30;	//assume monthly

			//convert to a profile start date
			$order->ProfileStartDate = date_i18n("Y-m-d\TH:i:s", strtotime("+ " . $trial_period_days . " Day", current_time("timestamp")));

			//filter the start date
			$order->ProfileStartDate = apply_filters("pmpro_profile_start_date", $order->ProfileStartDate, $order);

			$start_ts  = strtotime($order->ProfileStartDate, current_time("timestamp") );
			$now =  strtotime( date('Y-m-d\T00:00:00', current_time('timestamp' ) ), current_time('timestamp' ) );
			
			//convert back to days
			$trial_period_days = ceil(abs( $now - $start_ts ) / 86400);
			
			//now add the actual trial set by the site
			if(!empty($order->TrialBillingCycles))
			{
				$trialOccurrences = (int)$order->TrialBillingCycles;
				if($order->BillingPeriod == "Year")
					$trial_period_days = $trial_period_days + (365 * $order->BillingFrequency * $trialOccurrences);	//annual
				elseif($order->BillingPeriod == "Day")
					$trial_period_days = $trial_period_days + (1 * $order->BillingFrequency * $trialOccurrences);		//daily
				elseif($order->BillingPeriod == "Week")
					$trial_period_days = $trial_period_days + (7 * $order->BillingFrequency * $trialOccurrences);	//weekly
				else
					$trial_period_days = $trial_period_days + (30 * $order->BillingFrequency * $trialOccurrences);	//assume monthly
			}

			//subscribe to the plan
			try
			{
				
				$details = array(
				  'paymentMethodToken' => $this->customer->creditCards[0]->token,
				  'planId' => $this->get_plan_id( $order->membership_id ),
				  'price' => $amount
				);

				if(!empty($trial_period_days))
				{
					$details['trialPeriod'] = true;
					$details['trialDuration'] = $trial_period_days;
					$details['trialDurationUnit'] = "day";
				}

				if(!empty($order->TotalBillingCycles))
					$details['numberOfBillingCycles'] = $order->TotalBillingCycles;

				$result = Braintree_Subscription::create($details);
			}
			catch (Exception $e)
			{
				$order->error = sprint( __("Error subscribing customer to plan with Braintree: %s (%s)", 'paid-memberships-pro' ), $e->getMessage(), get_class($e) );
				//return error
				$order->shorterror = $order->error;
				return false;
			}

			if($result->success)
			{
				//if we got this far, we're all good
				$order->status = "success";
				$order->subscription_transaction_id = $result->subscription->id;
				return true;
			}
			else
			{
				$order->error = sprintf( __("Failed to subscribe with Braintree: %s", 'paid-memberships-pro' ),  $result->message );
				$order->shorterror = $result->message;
				return false;
			}
		}

		function update(&$order)
		{
			if ( ! self::$is_loaded ) {
				$order->error = __("Payment error: Please contact the webmaster (braintree-load-error)", 'paid-memberships-pro');
				return false;
			}

			//we just have to run getCustomer which will look for the customer and update it with the new token
			$this->getCustomer($order, true);

			if(!empty($this->customer) && empty($order->error))
			{
				return true;
			}
			else
			{
				return false;	//couldn't find the customer
			}
		}
		
		/**
      * Cancel order and Braintree Subscription if applicable
      *
		  * @param \MemberOrder $order
		  *
		  * @return bool
		  */
		function cancel(&$order)
		{
			if ( ! self::$is_loaded ) {
				$order->error = __("Payment error: Please contact the webmaster (braintree-load-error)", 'paid-memberships-pro');
				return false;
			}
			
			if ( isset( $_POST['bt_payload']) && isset( $_POST['bt_payload']) ) {
			
				try {
					$webhookNotification = Braintree_WebhookNotification::parse( $_POST['bt_signature'], $_POST['bt_payload'] );
					if ( Braintree_WebhookNotification::SUBSCRIPTION_CANCELED === $webhookNotification->kind ) {
					    // Return, we're already processing the cancellation
					    return true;
		            }
				} catch ( \Exception $e ) {
				    // Don't do anything
				}
			}
			
			// Always cancel, even if Braintree fails
			$order->updateStatus("cancelled" );			
            
			//require a subscription id
			if(empty($order->subscription_transaction_id))
				return false;

			//find the customer
			if(!empty($order->subscription_transaction_id))
			{
				//cancel
				try
				{
					$result = Braintree_Subscription::cancel($order->subscription_transaction_id);
				}
				catch(Exception $e)
				{
					$order->error = sprintf( __("Could not find the subscription. %s", 'paid-memberships-pro' ),  $e->getMessage() );
					$order->shorterror = $order->error;
					return false;	//no subscription found
				}

				if($result->success)
				{
					return true;
				}
				else
				{
					$order->error = sprintf( __("Could not find the subscription. %s", 'paid-memberships-pro' ), $result->message );
					$order->shorterror = $order->error;
					return false;	//no subscription found
				}
			}
			else
			{
				$order->error = __("Could not find the subscription.", 'paid-memberships-pro' );
				$order->shorterror = $order->error;
				return false;	//no customer found
			}
		}

		/*
			Save Braintree customer id after the user is registered.
		*/
		static function user_register($user_id)
		{
			global $pmpro_braintree_customerid;
			if(!empty($pmpro_braintree_customerid))
			{
				update_user_meta($user_id, 'pmpro_braintree_customerid', $pmpro_braintree_customerid);
			}
		}

		/**
		 * Gets the Braintree plan ID for a given level ID
		 * @param  int $level_id level to get plan ID for
		 * @return string        Braintree plan ID
		 */
	static function get_plan_id( $level_id ) {
		/**
			* Filter pmpro_braintree_plan_id
			*
			* Used to change the Braintree plan ID for a given level
			*
			* @since 2.1.0
			*
			* @param string  $plan_id for the given level
			* @param int $level_id the level id to make a plan id for
			*/
			return apply_filters( 'pmpro_braintree_plan_id', 'pmpro_' . $level_id, $level_id );
	}

	function get_subscription( &$order ) {
		// Does order have a subscription?
		if ( empty( $order ) || empty( $order->subscription_transaction_id ) ) {
			return false;
		}

		try {
			$subscription = Braintree_Subscription::find( $order->subscription_transaction_id );
		} catch ( Exception $e ) {
			$order->error      = __( "Error getting subscription with Braintree:", 'paid-memberships-pro' ) . $e->getMessage();
			$order->shorterror = $order->error;
			return false;
		}

		return $subscription;
	}

	/**
	 * Filter pmpro_next_payment to get date via API if possible
	 */
	static function pmpro_next_payment( $timestamp, $user_id, $order_status ) {
		// Check that we have a user ID...
		if ( ! empty( $user_id ) ) {
			// Get last order...
			$order = new MemberOrder();
			$order->getLastMemberOrder( $user_id, $order_status );

			// Check if this is a Braintree order with a subscription transaction id...
			if ( ! empty( $order->id ) && ! empty( $order->subscription_transaction_id ) && $order->gateway == "braintree" ) {
				// Get the subscription and return the next billing date.
				$subscription = $order->Gateway->get_subscription( $order );
				if ( ! empty( $subscription ) ) {
					$timestamp = $subscription->nextBillingDate->getTimestamp();
				}
			}
		}

		return $timestamp;
	}
}
