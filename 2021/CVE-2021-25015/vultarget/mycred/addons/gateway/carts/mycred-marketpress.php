<?php
if ( ! defined( 'myCRED_VERSION' ) ) exit;

/**
 * MarketPress Payment Gateway
 * @since 1.1
 * @version 1.3
 */
if ( ! function_exists( 'mycred_init_marketpress_gateway' ) ) {
	add_action( 'mp_load_gateway_plugins', 'mycred_init_marketpress_gateway' );
	function mycred_init_marketpress_gateway()
	{
		if ( ! class_exists( 'MP_Gateway_API' ) ) return;
		
		class MP_Gateway_myCRED extends MP_Gateway_API {

			var $plugin_name = 'mycred';
			var $admin_name = 'myCRED';
			var $public_name = 'myCRED';
			var $mycred_type = 'mycred_default';
			var $method_img_url = '';
			var $method_button_img_url = '';
			var $force_ssl = false;
			var $ipn_url;
			var $skip_form = false;

			/**
			 * Runs when your class is instantiated. Use to setup your plugin instead of __construct()
			 */
			function on_creation() {
				global $mp;
				$settings = get_option( 'mp_settings' );

				//set names here to be able to translate
				$this->admin_name = 'myCRED';

				$this->public_name = mycred_label( true );
				if ( isset( $settings['gateways']['mycred']['name'] ) && ! empty( $settings['gateways']['mycred']['name'] ) )
					$this->public_name = $settings['gateways']['mycred']['name'];

				$this->method_img_url = plugins_url( 'assets/images/cred-icon32.png', myCRED_THIS );
				if ( isset( $settings['gateways']['mycred']['logo'] ) && ! empty( $settings['gateways']['mycred']['logo'] ) )
					$this->method_img_url = $settings['gateways']['mycred']['logo'];

				$this->method_button_img_url = $this->public_name;
				
				if ( ! isset( $settings['gateways']['mycred']['type'] ) )
					$this->mycred_type = 'mycred_default';
				else
					$this->mycred_type = $settings['gateways']['mycred']['type'];

				$this->mycred = mycred( $this->mycred_type );
			}
		
			/**
			 * Use Exchange
			 * Checks to see if exchange is needed.
			 * @since 1.1
			 * @version 1.0
			 */
			function use_exchange() {
				global $mp;

				$settings = get_option( 'mp_settings' );
				if ( $settings['currency'] == 'POINTS' ) return false;
				return true;
			}

			/**
			 * Returns the current carts total.
			 * @since 1.2
			 * @version 1.0
			 */
			function get_cart_total( $cart = NULL ) {
				global $mp;

				// Get total
				$totals = array();
				foreach ( $cart as $product_id => $variations ) {
					foreach ( $variations as $data ) {
						$totals[] = $mp->before_tax_price( $data['price'], $product_id ) * $data['quantity'];
					}
				}
				$total = array_sum( $totals );

				// Apply Coupons
				if ( $coupon = $mp->coupon_value( $mp->get_coupon_code(), $total ) ) {
					$total = $coupon['new_total'];
				}

				// Shipping Cost
				if ( ( $shipping_price = $mp->shipping_price() ) !== false ) {
					$total = $total + $shipping_price;
				}

				// Tax
				if ( ( $tax_price = $mp->tax_price() ) !== false ) {
					$total = $total + $tax_price;
				}
			
				$settings = get_option( 'mp_settings' );
				if ( $this->use_exchange() )
					return $this->mycred->apply_exchange_rate( $total, $settings['gateways']['mycred']['exchange'] );
				else
					return $this->mycred->number( $total );
			}

			/**
			 * Return fields you need to add to the payment screen, like your credit card info fields
			 *
			 * @param array $cart. Contains the cart contents for the current blog, global cart if $mp->global_cart is true
			 * @param array $shipping_info. Contains shipping info and email in case you need it
			 * @since 1.1
			 * @version 1.1
			 */
			function payment_form( $cart, $shipping_info ) {
				global $mp;
			
				$settings = get_option( 'mp_settings' );
			
				if ( ! is_user_logged_in() ) {
					$message = str_replace( '%login_url_here%', wp_login_url( mp_checkout_step_url( 'checkout' ) ), $settings['gateways']['mycred']['visitors'] );
					$message = $this->mycred->template_tags_general( $message );
					return '<div id="mp-mycred-balance">' . $message . '</div>';
				}
			
				$balance = $this->mycred->get_users_cred( get_current_user_id(), $this->mycred_type );
				$total = $this->get_cart_total( $cart );
			
				// Low balance
				if ( $balance-$total < 0 ) {
					$message = $this->mycred->template_tags_user( $settings['gateways']['mycred']['lowfunds'], false, wp_get_current_user() );
					$instructions = '<div id="mp-mycred-balance">' . $message . '</div>';
					$red = ' style="color: red;"';
				}
				else {
					$instructions = $this->mycred->template_tags_general( $settings['gateways']['mycred']['instructions'] );
					$red = '';
				}
			
				// Return Cost
				return '
<div id="mp-mycred-balance">' . $instructions . '</div>
<div id="mp-mycred-cost">
<table style="width:100%;">
	<tr>
		<td class="info">' . __( 'Current Balance', 'mycred' ) . '</td>
		<td class="amount">' . $this->mycred->format_creds( $balance ) . '</td>
	</tr>
	<tr>
		<td class="info">' . __( 'Total Cost', 'mycred' ) . '</td>
		<td class="amount">' . $this->mycred->format_creds( $total ) . '</td>
	</tr>
	<tr>
		<td class="info">' . __( 'Balance After Purchase', 'mycred' ) . '</td>
		<td class="amount"' . $red . '>' . $this->mycred->format_creds( $balance-$total ) . '</td>
	</tr>
</table>
</div>';
			}

			/**
			 * Return the chosen payment details here for final confirmation. You probably don't need
			 * to post anything in the form as it should be in your $_SESSION var already.
			 *
			 * @param array $cart. Contains the cart contents for the current blog, global cart if $mp->global_cart is true
			 * @param array $shipping_info. Contains shipping info and email in case you need it
			 * @since 1.1
			 * @version 1.1
			 */
			function confirm_payment_form( $cart, $shipping_info ) {
				global $mp;

				$settings = get_option( 'mp_settings' );
				$user_id = get_current_user_id();
				$balance = $this->mycred->get_users_cred( get_current_user_id(), $this->mycred_type );
				$total = $this->get_cart_total( $cart );
			
				$table = '<table class="mycred-cart-cost"><thead><tr><th>' . __( 'Payment', 'mycred' ) . '</th></tr></thead>';
				if ( $balance-$total < 0 ) {
					$message = $this->mycred->template_tags_user( $settings['gateways']['mycred']['lowfunds'], false, wp_get_current_user() );
					$table .= '<tr><td id="mp-mycred-cost" style="color: red; font-weight: bold;"><p>' . $message . ' <a href="' . mp_checkout_step_url( 'checkout' ) . '">' . __( 'Go Back', 'mycred' ) . '</a></td></tr>';
				}
				else
					$table .= '<tr><td id="mp-mycred-cost" class="mycred-ok">' . $this->mycred->format_creds( $total ) . ' ' . __( 'will be deducted from your account.', 'mycred' ) . '</td></tr>';
			
				return $table . '</table>';
			}
		
			function process_payment_form( $cart, $shipping_info ) { }

			/**
			 * Use this to do the final payment. Create the order then process the payment. If
			 * you know the payment is successful right away go ahead and change the order status
			 * as well.
			 * Call $mp->cart_checkout_error($msg, $context); to handle errors. If no errors
			 * it will redirect to the next step.
			 *
			 * @param array $cart. Contains the cart contents for the current blog, global cart if $mp->global_cart is true
			 * @param array $shipping_info. Contains shipping info and email in case you need it
			 * @since 1.1
			 * @version 1.2.1
			 */
			function process_payment( $cart, $shipping_info ) {
				global $mp;
			
				$settings = get_option('mp_settings');
				$user_id = get_current_user_id();
				$insolvent = $this->mycred->template_tags_user( $settings['gateways']['mycred']['lowfunds'], false, wp_get_current_user() );
				$timestamp = time();

				// This gateway requires buyer to be logged in
				if ( ! is_user_logged_in() ) {
					$message = str_replace( '%login_url_here%', wp_login_url( mp_checkout_step_url( 'checkout' ) ), $settings['gateways']['mycred']['visitors'] );
					$mp->cart_checkout_error( $this->mycred->template_tags_general( $message ) );
				}

				// Make sure current user is not excluded from using myCRED
				if ( $this->mycred->exclude_user( $user_id ) )
					$mp->cart_checkout_error(
						sprintf( __( 'Sorry, but you can not use this gateway as your account is excluded. Please <a href="%s">select a different payment method</a>.', 'mycred' ), mp_checkout_step_url( 'checkout' ) )
					);

				// Get users balance
				$balance = $this->mycred->get_users_cred( $user_id, $this->mycred_type );
				$total = $this->get_cart_total( $cart );
			
				// Low balance or Insolvent
				if ( $balance <= $this->mycred->zero() || $balance-$total < $this->mycred->zero() ) {
					$mp->cart_checkout_error(
						$insolvent . ' <a href="' . mp_checkout_step_url( 'checkout' ) . '">' . __( 'Go Back', 'mycred' ) . '</a>'
					);
					return;
				}

				// Let others decline a store order
				$decline = apply_filters( 'mycred_decline_store_purchase', false, $cart, $this );
				if ( $decline !== false ) {
					$mp->cart_checkout_error( $decline );
					return;
				}

				// Create MarketPress order
				$order_id = $mp->generate_order_id();
				$payment_info['gateway_public_name'] = $this->public_name;
				$payment_info['gateway_private_name'] = $this->admin_name;
				$payment_info['status'][ $timestamp ] = __( 'Paid', 'mycred' );
				$payment_info['total'] = $total;
				$payment_info['currency'] = $settings['currency'];
				$payment_info['method'] = __( 'myCRED', 'mycred' );
				$payment_info['transaction_id'] = $order_id;
				$paid = true;
				$result = $mp->create_order( $order_id, $cart, $shipping_info, $payment_info, $paid );
				
				$order = get_page_by_title( $result, 'OBJECT', 'mp_order' );

				// Deduct cost
				$this->mycred->add_creds(
					'marketpress_payment',
					$user_id,
					0-$total,
					$settings['gateways']['mycred']['log_template'],
					$order->ID,
					array( 'ref_type' => 'post' ),
					$this->mycred_type
				);
				
				// Profit Sharing
				if ( $settings['gateways']['mycred']['profit_share_percent'] > 0 ) {
					foreach ( $cart as $product_id => $variations ) {
						// Get Product
						$product = get_post( (int) $product_id );
						
						// Continue if product has just been deleted or owner is buyer
						if ( $product === NULL || $product->post_author == $cui ) continue;
						
						foreach ( $variations as $data ) {
							$price = $data['price'];
							$quantity = $data['quantity'];
							$cost = $price*$quantity;

							// Calculate Share
							$share = ( $settings['gateways']['mycred']['profit_share_percent'] / 100 ) * $cost;

							// Payout
							$this->mycred->add_creds(
								'store_sale',
								$product->post_author,
								$share,
								$settings['gateways']['mycred']['profit_share_log'],
								$product->ID,
								array( 'ref_type' => 'post' ),
								$this->mycred_type
							);
						}
					}
				}
			}
		
			function order_confirmation( $order ) { }

			/**
			 * Filters the order confirmation email message body. You may want to append something to
			 * the message. Optional
			 * @since 1.1
			 * @version 1.0
			 */
			function order_confirmation_email( $msg, $order ) {
				global $mp;
				$settings = get_option('mp_settings');

				if ( isset( $settings['gateways']['mycred']['email'] ) )
					$msg = $mp->filter_email( $order, $settings['gateways']['mycred']['email'] );
				else
					$msg = $settings['email']['new_order_txt'];

				return $msg;
			}

			/**
			 * Return any html you want to show on the confirmation screen after checkout. This
			 * should be a payment details box and message.
			 * @since 1.1
			 * @version 1.1
			 */
			function order_confirmation_msg( $content, $order ) {
				global $mp;
				$settings = get_option('mp_settings');

				$mycred = mycred();
				$user_id = get_current_user_id();
			
				return $content . str_replace(
					'TOTAL',
					$mp->format_currency( $order->mp_payment_info['currency'], $order->mp_payment_info['total'] ),
					$mycred->template_tags_user( $settings['gateways']['mycred']['confirmation'], false, wp_get_current_user() )
				);
			}

			/**
			 * myCRED Gateway Settings
			 * @since 1.1
			 * @version 1.3
			 */
			function gateway_settings_box( $settings ) {
				global $mp;
				$settings = get_option( 'mp_settings' );
				$mycred = mycred();
			
				$name = mycred_label( true );
				$settings['gateways']['mycred'] = shortcode_atts( array(
					'name'                 => $name . ' ' . $mycred->template_tags_general( __( '%_singular% Balance', 'mycred' ) ),
					'logo'                 => $this->method_button_img_url,
					'type'                 => 'mycred_default',
					'log_template'         => __( 'Payment for Order: #%order_id%', 'mycred' ),
					'exchange'             => 1,
					'profit_share_percent' => 0,
					'profit_share_log'     => __( 'Product Sale: %post_title%', 'mycred' ),
					'instructions'         => __( 'Pay using your account balance.', 'mycred' ),
					'confirmation'         => __( 'TOTAL amount has been deducted from your account. Your current balance is: %balance_f%', 'mycred' ),
					'lowfunds'             => __( 'Insufficient funds.', 'mycred' ),
					'visitors'             => __( 'You must be logged in to pay with %_plural%. Please <a href="%login_url_here%">login</a>.', 'mycred' ),
					'email'                => $settings['email']['new_order_txt']
				), ( isset( $settings['gateways']['mycred'] ) ) ? $settings['gateways']['mycred'] : array() ); ?>

<div id="mp_mycred_payments" class="postbox mp-pages-msgs">
	<h3 class="handle"><span><?php echo $name . ' ' . __( 'Settings', 'mycred' ); ?></span></h3>
	<div class="inside">
		<span class="description"><?php echo sprintf( __( 'Let your users pay for items in their shopping cart using their %s Account. Note! This gateway requires your users to be logged in when making a purchase!', 'mycred' ), $name ); ?></span>
		<table class="form-table">
			<tr>
				<th scope="row"><label for="mycred-method-name"><?php _e( 'Method Name', 'mycred' ); ?></label></th>
				<td>
					<span class="description"><?php _e( 'Enter a public name for this payment method that is displayed to users - No HTML', 'mycred' ); ?></span>
					<p><input value="<?php echo esc_attr( $settings['gateways']['mycred']['name'] ); ?>" style="width: 100%;" name="mp[gateways][mycred][name]" id="mycred-method-name" type="text" /></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="mycred-method-logo"><?php _e( 'Gateway Logo URL', 'mycred' ); ?></label></th>
				<td>
					<p><input value="<?php echo esc_attr( $settings['gateways']['mycred']['logo'] ); ?>" style="width: 100%;" name="mp[gateways][mycred][logo]" id="mycred-method-logo" type="text" /></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="mycred-method-type"><?php _e( 'Point Type', 'mycred' ); ?></label></th>
				<td>
					<?php mycred_types_select_from_dropdown( 'mp[gateways][mycred][type]', 'mycred-method-type', $settings['gateways']['mycred']['type'] ); ?>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="mycred-log-template"><?php _e( 'Log Template', 'mycred' ); ?></label></th>
				<td>
					<span class="description"><?php echo $mycred->available_template_tags( array( 'general' ), '%order_id%, %order_link%' ); ?></span>
					<p><input value="<?php echo esc_attr( $settings['gateways']['mycred']['log_template'] ); ?>" style="width: 100%;" name="mp[gateways][mycred][log_template]" id="mycred-log-template" type="text" /></p>
				</td>
			</tr>
<?php
				// Exchange rate
				if ( $this->use_exchange() ) :
					$exchange_desc = __( 'How much is 1 %_singular% worth in %currency%?', 'mycred' );
					$exchange_desc = $mycred->template_tags_general( $exchange_desc );
					$exchange_desc = str_replace( '%currency%', $settings['currency'], $exchange_desc ); ?>

			<tr>
				<th scope="row"><label for="mycred-exchange-rate"><?php _e( 'Exchange Rate', 'mycred' ); ?></label></th>
				<td>
					<span class="description"><?php echo $exchange_desc; ?></span>
					<p><input value="<?php echo esc_attr( $settings['gateways']['mycred']['exchange'] ); ?>" size="8" name="mp[gateways][mycred][exchange]" id="mycred-exchange-rate" type="text" /></p>
				</td>
			</tr>
<?php			endif; ?>

			<tr>
				<td colspan="2"><h4><?php _e( 'Profit Sharing', 'mycred' ); ?></h4></td>
			</tr>
			<tr>
				<th scope="row"><label for="mycred-profit-sharing"><?php _e( 'Percentage', 'mycred' ); ?></label></th>
				<td>
					<span class="description"><?php _e( 'Option to share sales with the product owner. Use zero to disable.', 'mycred' ); ?></span>
					<p><input value="<?php echo esc_attr( $settings['gateways']['mycred']['profit_share_percent'] ); ?>" size="8" name="mp[gateways][mycred][profit_share_percent]" id="mycred-profit-sharing" type="text" /> %</p>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="mycred-profit-sharing-log"><?php _e( 'Log Template', 'mycred' ); ?></label></th>
				<td>
					<span class="description"><?php echo $mycred->available_template_tags( array( 'general', 'post' ) ); ?></span>
					<p><input value="<?php echo esc_attr( $settings['gateways']['mycred']['profit_share_log'] ); ?>" style="width: 100%;" name="mp[gateways][mycred][profit_share_log]" id="mycred-profit-sharing-log" type="text" /></p>
				</td>
			</tr>
			<tr>
				<td colspan="2"><h4><?php _e( 'Messages', 'mycred' ); ?></h4></td>
			</tr>
			<tr>
				<th scope="row"><label for="mycred-lowfunds"><?php _e( 'Insufficient Funds', 'mycred' ); ?></label></th>
				<td>
					<span class="description"><?php _e( 'Message to show when the user can not use this gateway.', 'mycred' ); ?></span>
					<p><input type="text" name="mp[gateways][mycred][lowfunds]" id="mycred-lowfunds" style="width: 100%;" value="<?php echo esc_attr( $settings['gateways']['mycred']['lowfunds'] ); ?>"><br />
					<span class="description"><?php echo $mycred->available_template_tags( array( 'general' ) ); ?></span></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="mycred-visitors"><?php _e( 'Visitors', 'mycred' ); ?></label></th>
				<td>
					<span class="description"><?php _e( 'Message to show to buyers that are not logged in.', 'mycred' ); ?></span>
					<p><input type="text" name="mp[gateways][mycred][visitors]" id="mycred-visitors" style="width: 100%;" value="<?php echo esc_attr( $settings['gateways']['mycred']['visitors'] ); ?>"><br />
					<span class="description"><?php echo $mycred->available_template_tags( array( 'general' ) ); ?></span></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="mycred-instructions"><?php _e( 'User Instructions', 'mycred' ); ?></label></th>
				<td>
					<span class="description"><?php _e( 'Information to show users before payment.', 'mycred' ); ?></span>
					<p><?php wp_editor( $settings['gateways']['mycred']['instructions'] , 'mycred-instructions', array( 'textarea_name' => 'mp[gateways][mycred][instructions]' ) ); ?><br />
					<span class="description"><?php echo $mycred->available_template_tags( array( 'general' ), '%balance% or %balance_f%' ); ?></span></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="mycred-confirmation"><?php _e( 'Confirmation Information', 'mycred' ); ?></label></th>
				<td>
					<span class="description"><?php _e( 'Information to display on the order confirmation page. - HTML allowed', 'mycred' ); ?></span>
					<p><?php wp_editor( $settings['gateways']['mycred']['confirmation'], 'mycred-confirmation', array( 'textarea_name' => 'mp[gateways][mycred][confirmation]' ) ); ?><br />
					<span class="description"><?php echo $mycred->available_template_tags( array( 'general' ), '%balance% or %balance_f%' ); ?></span></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="mycred-email"><?php _e( 'Order Confirmation Email', 'mycred' ); ?></label></th>
				<td>
					<span class="description"><?php echo sprintf( __( 'This is the email text to send to those who have made %s checkouts. It overrides the default order checkout email. These codes will be replaced with order details: CUSTOMERNAME, ORDERID, ORDERINFO, SHIPPINGINFO, PAYMENTINFO, TOTAL, TRACKINGURL. No HTML allowed.', 'mycred' ), $name ); ?></span>
					<p><textarea id="mycred-email" name="mp[gateways][mycred][email]" class="mp_emails_txt"><?php echo esc_textarea( $settings['gateways']['mycred']['email'] ); ?></textarea></p>
					<span class="description"><?php echo $mycred->available_template_tags( array( 'general' ), '%balance% or %balance_f%' ); ?></span>
				</td>
			</tr>
		</table>
	</div>
</div>
<?php
			}

			/**
			 * Filter Gateway Settings
			 * @since 1.1
			 * @version 1.3
			 */
			function process_gateway_settings( $settings ) {
				// Name (no html)
				$settings['gateways']['mycred']['name'] = stripslashes( wp_filter_nohtml_kses( $settings['gateways']['mycred']['name'] ) );

				// Log Template (no html)
				$settings['gateways']['mycred']['log_template'] = stripslashes( wp_filter_nohtml_kses( $settings['gateways']['mycred']['log_template'] ) );
				$settings['gateways']['mycred']['type'] = sanitize_text_field( $settings['gateways']['mycred']['type'] );
				$settings['gateways']['mycred']['logo'] = stripslashes( wp_filter_nohtml_kses( $settings['gateways']['mycred']['logo'] ) );

				// Exchange rate (if used)
				if ( $this->use_exchange() ) {
					// Decimals must start with a zero
					if ( $settings['gateways']['mycred']['exchange'] != 1 && substr( $settings['gateways']['mycred']['exchange'], 0, 1 ) != '0' ) {
						$settings['gateways']['mycred']['exchange'] = (float) '0' . $settings['gateways']['mycred']['exchange'];
					}
					// Decimal seperator must be punctuation and not comma
					$settings['gateways']['mycred']['exchange'] = str_replace( ',', '.', $settings['gateways']['mycred']['exchange'] );
				}
				else
					$settings['gateways']['mycred']['exchange'] = 1;
			
				$settings['gateways']['mycred']['profit_share_percent'] = stripslashes( trim( $settings['gateways']['mycred']['profit_share_percent'] ) );
				$settings['gateways']['mycred']['profit_share_log'] = stripslashes( wp_filter_nohtml_kses( $settings['gateways']['mycred']['profit_share_log'] ) );
			
				$settings['gateways']['mycred']['lowfunds'] = stripslashes( wp_filter_post_kses( $settings['gateways']['mycred']['lowfunds'] ) );
				$settings['gateways']['mycred']['visitors'] = stripslashes( wp_filter_post_kses( $settings['gateways']['mycred']['visitors'] ) );
				$settings['gateways']['mycred']['instructions'] = stripslashes( wp_filter_post_kses( $settings['gateways']['mycred']['instructions'] ) );
				$settings['gateways']['mycred']['confirmation'] = stripslashes( wp_filter_post_kses( $settings['gateways']['mycred']['confirmation'] ) );

				// Email (no html)
				$settings['gateways']['mycred']['email'] = stripslashes( wp_filter_nohtml_kses( $settings['gateways']['mycred']['email'] ) );

				return $settings;
			}
		}
		mp_register_gateway_plugin( 'MP_Gateway_myCRED', 'mycred', 'myCRED' );
	}
}

	

/**
 * Filter the myCRED Log
 * Parses the %order_id% and %order_link% template tags.
 * @since 1.1
 * @version 1.1
 */
if ( ! function_exists( 'mycred_marketpress_parse_log' ) ) {
	add_filter( 'mycred_parse_log_entry_marketpress_payment', 'mycred_marketpress_parse_log', 90, 2 );
	function mycred_marketpress_parse_log( $content, $log_entry )
	{
		// Prep
		global $mp;
		$mycred = mycred( $log_entry->ctype );
		$order = get_post( $log_entry->ref_id );
		$order_id = $order->post_title;
		$user_id = get_current_user_id();

		// Order ID
		$content = str_replace( '%order_id%', $order->post_title, $content );

		// Link to order if we can edit plugin or are the user who made the order
		if ( $user_id == $log_entry->user_id || $mycred->can_edit_plugin( $user_id ) ) {
			$track_link = '<a href="' . mp_orderstatus_link( false, true ) . $order_id . '/' . '">#' . $order->post_title . '/' . '</a>';
			$content = str_replace( '%order_link%', $track_link, $content );
		}
		else {
			$content = str_replace( '%order_link%', '#' . $order_id, $content );
		}

		return $content;
	}
}

/**
 * Parse Email Notice
 * @since 1.2.2
 * @version 1.0
 */
if ( ! function_exists( 'mycred_market_parse_email' ) ) {
	add_filter( 'mycred_email_before_send', 'mycred_market_parse_email' );
	function mycred_market_parse_email( $email )
	{
		if ( $email['request']['ref'] == 'marketpress_payment' ) {
			$order = get_post( (int) $email['request']['ref_id'] );
			if ( isset( $order->id ) ) {
				$track_link = '<a href="' . mp_orderstatus_link( false, true ) . $order_id . '/' . '">#' . $order->post_title . '/' . '</a>';

				$content = str_replace( '%order_id%', $order->post_title, $email['request']['entry'] );
				$email['request']['entry'] = str_replace( '%order_link%', $track_link, $content );
			}
		}
		return $email;
	}
}
?>