<?php
if ( ! defined( 'myCRED_VERSION' ) ) exit;

/**
 * myCRED_PayPal class
 * PayPal Payments Standard - Payment Gateway
 * @since 0.1
 * @version 1.3
 */
if ( ! class_exists( 'myCRED_PayPal_Standard' ) ) :
	class myCRED_PayPal_Standard extends myCRED_Payment_Gateway {

		/**
		 * Construct
		 */
		public function __construct( $gateway_prefs ) {

			$types            = mycred_get_types();
			$default_exchange = array();
			foreach ( $types as $type => $label )
				$default_exchange[ $type ] = 1;

			parent::__construct( array(
				'id'               => 'paypal-standard',
				'label'            => 'PayPal',
				'gateway_logo_url' => plugins_url( 'assets/images/paypal.png', MYCRED_PURCHASE ),
				'defaults'         => array(
					'sandbox'          => 0,
					'currency'         => '',
					'account'          => '',
					'logo_url'         => '',
					'item_name'        => 'Purchase of myCRED %plural%',
					'exchange'         => $default_exchange
				)
			), $gateway_prefs );

		}

		/**
		 * IPN - Is Valid Call
		 * Replaces the default check
		 * @since 1.4
		 * @version 1.0.1
		 */
		public function IPN_is_valid_call() {

			// PayPal Host
			$host          = 'www.paypal.com';
			if ( $this->sandbox_mode )
				$host = 'www.sandbox.paypal.com';

			$data          = $this->POST_to_data();

			// Prep Respons
			$request       = 'cmd=_notify-validate';
			$get_magic_quotes_exists = false;
			if ( function_exists( 'get_magic_quotes_gpc' ) )
				$get_magic_quotes_exists = true;

			foreach ( $data as $key => $value ) {
				if ( $get_magic_quotes_exists == true && get_magic_quotes_gpc() == 1 )
					$value = urlencode( stripslashes( $value ) );
				else
					$value = urlencode( $value );

				$request .= "&$key=$value";
			}

			// Call PayPal
			$curl_attempts = apply_filters( 'mycred_paypal_standard_max_attempts', 3 );
			$attempt       = 1;
			$result        = '';
			// We will make a x number of curl attempts before finishing with a fsock.
			do {

				$call = curl_init( "https://$host/cgi-bin/webscr" );
				curl_setopt( $call, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1 );
				curl_setopt( $call, CURLOPT_POST, 1 );
				curl_setopt( $call, CURLOPT_RETURNTRANSFER, 1 );
				curl_setopt( $call, CURLOPT_POSTFIELDS, $request );
				curl_setopt( $call, CURLOPT_SSL_VERIFYPEER, 1 );
				curl_setopt( $call, CURLOPT_CAINFO, MYCRED_PURCHASE_DIR . '/cacert.pem' );
				curl_setopt( $call, CURLOPT_SSL_VERIFYHOST, 2 );
				curl_setopt( $call, CURLOPT_FRESH_CONNECT, 1 );
				curl_setopt( $call, CURLOPT_FORBID_REUSE, 1 );
				curl_setopt( $call, CURLOPT_HTTPHEADER, array( 'Connection: Close', 'User-Agent: myCRED' ) );
				$result = curl_exec( $call );

				// End on success
				if ( $result !== false ) {
					curl_close( $call );
					break;
				}

				curl_close( $call );

				// Final try
				if ( $attempt == $curl_attempts ) {
					$header = "POST /cgi-bin/webscr HTTP/1.0\r\n";
					$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
					$header .= "Content-Length: " . strlen( $request ) . "\r\n\r\n";
					$fp = fsockopen( 'ssl://' . $host, 443, $errno, $errstr, 30 );
					if ( $fp ) {
						fputs( $fp, $header . $request );
						while ( ! feof( $fp ) ) {
							$result = fgets( $fp, 1024 );
						}
						fclose( $fp );
					}
				}
				$attempt++;

			} while ( $attempt <= $curl_attempts );

			if ( strcmp( $result, "VERIFIED" ) == 0 )
				return true;

			return false;

		}

		/**
		 * Process Handler
		 * @since 0.1
		 * @version 1.3
		 */
		public function process() {

			// Required fields
			if ( isset( $_POST['custom'] ) && isset( $_POST['txn_id'] ) && isset( $_POST['mc_gross'] ) ) {

				// Get Pending Payment
				$pending_post_id = sanitize_key( $_POST['custom'] );
				$pending_payment = $this->get_pending_payment( $pending_post_id );
				if ( $pending_payment !== false ) {

					// Verify Call with PayPal
					if ( $this->IPN_is_valid_call() ) {

						$errors   = false;
						$new_call = array();

						// Check amount paid
						if ( $_POST['mc_gross'] != $pending_payment->cost ) {
							$new_call[] = sprintf( __( 'Price mismatch. Expected: %s Received: %s', 'mycred' ), $pending_payment->cost, $_POST['mc_gross'] );
							$errors     = true;
						}

						// Check currency
						if ( $_POST['mc_currency'] != $pending_payment->currency ) {
							$new_call[] = sprintf( __( 'Currency mismatch. Expected: %s Received: %s', 'mycred' ), $pending_payment->currency, $_POST['mc_currency'] );
							$errors     = true;
						}

						// Check status
						if ( $_POST['payment_status'] != 'Completed' ) {
							$new_call[] = sprintf( __( 'Payment not completed. Received: %s', 'mycred' ), $_POST['payment_status'] );
							$errors     = true;
						}

						// Credit payment
						if ( $errors === false ) {

							// If account is credited, delete the post and it's comments.
							if ( $this->complete_payment( $pending_payment, $_POST['txn_id'] ) )
								$this->trash_pending_payment( $pending_post_id );
							else
								$new_call[] = __( 'Failed to credit users account.', 'mycred' );

						}

						// Log Call
						if ( ! empty( $new_call ) )
							$this->log_call( $pending_post_id, $new_call );

					}

				}

			}

		}

		/**
		 * Results Handler
		 * @since 0.1
		 * @version 1.0.1
		 */
		public function returning() {

			if ( isset( $_REQUEST['tx'] ) && isset( $_REQUEST['st'] ) && $_REQUEST['st'] == 'Completed' ) {
				$this->get_page_header( __( 'Success', 'mycred' ), $this->get_thankyou() );
				echo '<h1 style="text-align:center;">' . __( 'Thank you for your purchase', 'mycred' ) . '</h1>';
				$this->get_page_footer();
				exit;
			}

		}

		/**
		 * Prep Sale
		 * @since 1.8
		 * @version 1.0
		 */
		public function prep_sale( $new_transaction = false ) {

			// Set currency
			$this->currency        = ( $this->currency == '' ) ? $this->prefs['currency'] : $this->currency;

			// The item name
			$item_name             = str_replace( '%number%', $this->amount, $this->prefs['item_name'] );
			$item_name             = $this->core->template_tags_general( $item_name );

			// This gateway redirects, so we need to populate redirect_to
			$this->redirect_to     = ( $this->sandbox_mode ) ? 'https://www.sandbox.paypal.com/cgi-bin/webscr' : 'https://www.paypal.com/cgi-bin/webscr';

			// Transaction variables that needs to be submitted
			$this->redirect_fields = array(
				'cmd'           => '_xclick',
				'business'      => $this->prefs['account'],
				'item_name'     => $item_name,
				'quantity'      => 1,
				'amount'        => $this->cost,
				'currency_code' => $this->currency,
				'no_shipping'   => 1,
				'no_note'       => 1,
				'custom'        => $this->transaction_id,
				'return'        => $this->get_thankyou(),
				'notify_url'    => $this->callback_url(),
				'rm'            => 2,
				'cbt'           => sprintf( _x( 'Return to %s', 'Return label. %s = Website name', 'mycred' ), get_bloginfo( 'name' ) ),
				'cancel_return' => $this->get_cancelled( $this->transaction_id )
			);

		}

		/**
		 * AJAX Buy Handler
		 * @since 1.8
		 * @version 1.0
		 */
		public function ajax_buy() {

			// Construct the checkout box content
			$content  = $this->checkout_header();
			$content .= $this->checkout_logo();
			$content .= $this->checkout_order();
			$content .= $this->checkout_cancel();
			$content .= $this->checkout_footer();

			// Return a JSON response
			$this->send_json( $content );

		}

		/**
		 * Checkout Page Body
		 * This gateway only uses the checkout body.
		 * @since 1.8
		 * @version 1.0
		 */
		public function checkout_page_body() {

			echo $this->checkout_header();
			echo $this->checkout_logo( false );

			echo $this->checkout_order();
			echo $this->checkout_cancel();

			echo $this->checkout_footer();

		}

		/**
		 * Preferences
		 * @since 0.1
		 * @version 1.0
		 */
		public function preferences() {

			$prefs = $this->prefs;

?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
		<h3><?php _e( 'Details', 'mycred' ); ?></h3>
		<div class="form-group">
			<label for="<?php echo $this->field_id( 'account' ); ?>"><?php _e( 'Account Email', 'mycred' ); ?></label>
			<input type="text" name="<?php echo $this->field_name( 'account' ); ?>" id="<?php echo $this->field_id( 'account' ); ?>" value="<?php echo esc_attr( $prefs['account'] ); ?>" class="form-control" />
		</div>
		<div class="form-group">
			<label for="<?php echo $this->field_id( 'item_name' ); ?>"><?php _e( 'Item Name', 'mycred' ); ?></label>
			<input type="text" name="<?php echo $this->field_name( 'item_name' ); ?>" id="<?php echo $this->field_id( 'item_name' ); ?>" value="<?php echo esc_attr( $prefs['item_name'] ); ?>" class="form-control" />
		</div>
		<div class="form-group">
			<label for="<?php echo $this->field_id( 'logo_url' ); ?>"><?php _e( 'Logo URL', 'mycred' ); ?></label>
			<input type="text" name="<?php echo $this->field_name( 'logo_url' ); ?>" id="<?php echo $this->field_id( 'logo_url' ); ?>" value="<?php echo esc_attr( $prefs['logo_url'] ); ?>" class="form-control" />
		</div>
	</div>
	<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
		<h3><?php _e( 'Setup', 'mycred' ); ?></h3>
		<div class="form-group">
			<label for="<?php echo $this->field_id( 'currency' ); ?>"><?php _e( 'Currency', 'mycred' ); ?></label>

			<?php $this->currencies_dropdown( 'currency', 'mycred-gateway-paypal-standard-currency' ); ?>

		</div>
		<div class="form-group">
			<label><?php _e( 'Exchange Rates', 'mycred' ); ?></label>

			<?php $this->exchange_rate_setup(); ?>

		</div>
	</div>
</div>
<?php

		}

		/**
		 * Sanatize Prefs
		 * @since 0.1
		 * @version 1.3
		 */
		public function sanitise_preferences( $data ) {

			$new_data              = array();

			$new_data['sandbox']   = ( isset( $data['sandbox'] ) ) ? 1 : 0;
			$new_data['currency']  = sanitize_text_field( $data['currency'] );
			$new_data['account']   = sanitize_text_field( $data['account'] );
			$new_data['item_name'] = sanitize_text_field( $data['item_name'] );
			$new_data['logo_url']  = sanitize_text_field( $data['logo_url'] );

			// If exchange is less then 1 we must start with a zero
			if ( isset( $data['exchange'] ) ) {
				foreach ( (array) $data['exchange'] as $type => $rate ) {
					if ( $rate != 1 && in_array( substr( $rate, 0, 1 ), array( '.', ',' ) ) )
						$data['exchange'][ $type ] = (float) '0' . $rate;
				}
			}
			$new_data['exchange']  = $data['exchange'];

			return $new_data;

		}

	}
endif;
