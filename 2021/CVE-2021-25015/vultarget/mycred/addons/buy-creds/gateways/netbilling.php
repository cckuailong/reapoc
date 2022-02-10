<?php
if ( ! defined( 'myCRED_VERSION' ) ) exit;

/**
 * myCRED_NETbilling class
 * NETbilling Payment Gateway
 * @see http://secure.netbilling.com/public/docs/merchant/public/directmode/directmode3protocol.html
 * @since 0.1
 * @version 1.3
 */
if ( ! class_exists( 'myCRED_NETbilling' ) ) :
	class myCRED_NETbilling extends myCRED_Payment_Gateway {

		protected $http_code = '';

		/**
		 * Construct
		 */
		public function __construct( $gateway_prefs ) {

			global $netbilling_errors;

			$types            = mycred_get_types();
			$default_exchange = array();
			foreach ( $types as $type => $label )
				$default_exchange[ $type ] = 1;

			parent::__construct( array(
				'id'               => 'netbilling',
				'label'            => 'NETbilling',
				'gateway_logo_url' => plugins_url( 'assets/images/netbilling.png', MYCRED_PURCHASE ),
				'defaults'         => array(
					'sandbox'          => 0,
					'account'          => '',
					'site_tag'         => '',
					'item_name'        => 'Purchase of myCRED %plural%',
					'exchange'         => $default_exchange,
					'logo_url'         => '',
					'cryptokey'        => '',
					'currency'         => 'USD'
				)
			), $gateway_prefs );

		}

		/**
		 * IPN - Is Valid Call
		 * Replaces the default check
		 * @since 1.4
		 * @version 1.0
		 */
		public function IPN_is_valid_call() {

			$result  = true;

			// Accounts Match
			$account = explode( ':', $_REQUEST['Ecom_Ezic_AccountAndSitetag'] );
			if ( $account[0] != $this->prefs['account'] || $account[1] != $this->prefs['site_tag'] )
				$result = false;

			// Crypto Check
			$crypto_check = md5( $this->prefs['cryptokey'] . $_REQUEST['Ecom_Cost_Total'] . $_REQUEST['Ecom_Receipt_Description'] );
			if ( $crypto_check != $_REQUEST['Ecom_Ezic_Security_HashValue_MD5'] )
				$result = false;

			return $result;

		}

		/**
		 * Process
		 * @since 0.1
		 * @version 1.2
		 */
		public function process() {

			// Required fields
			if ( isset( $_REQUEST['Ecom_UserData_salesdata'] ) && isset( $_REQUEST['Ecom_Ezic_Response_TransactionID'] ) && isset( $_REQUEST['Ecom_Cost_Total'] ) ) {

				// Get Pending Payment
				$pending_post_id = sanitize_key( $_REQUEST['Ecom_UserData_salesdata'] );
				$pending_payment = $this->get_pending_payment( $pending_post_id );
				if ( $pending_payment !== false ) {

					// Verify Call with PayPal
					if ( $this->IPN_is_valid_call() ) {

						$errors   = false;
						$new_call = array();

						// Check amount paid
						if ( $_REQUEST['Ecom_Cost_Total'] != $pending_payment->cost ) {
							$new_call[] = sprintf( __( 'Price mismatch. Expected: %s Received: %s', 'mycred' ), $pending_payment->cost, $_REQUEST['Ecom_Cost_Total'] );
							$errors     = true;
						}

						// Check status
						if ( $_REQUEST['Ecom_Ezic_Response_StatusCode'] != 1 ) {
							$new_call[] = sprintf( __( 'Payment not completed. Received: %s', 'mycred' ), $_REQUEST['Ecom_Ezic_Response_StatusCode'] );
							$errors     = true;
						}

						// Credit payment
						if ( $errors === false ) {

							// If account is credited, delete the post and it's comments.
							if ( $this->complete_payment( $pending_payment, $_REQUEST['Ecom_Ezic_Response_TransactionID'] ) )
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
		 * Returns
		 * @since 0.1
		 * @version 1.1
		 */
		public function returning() {

			if ( isset( $_REQUEST['Ecom_Ezic_AccountAndSitetag'] ) && isset( $_REQUEST['Ecom_UserData_salesdata'] ) )
				$this->process();

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
			$this->redirect_to     = 'https://secure.netbilling.com/gw/native/interactive2.2';

			// Transaction variables that needs to be submitted
			$this->redirect_fields = array(
				'Ecom_Ezic_AccountAndSitetag'         => $this->prefs['account'] . ':' . $this->prefs['site_tag'],
				'Ecom_Ezic_Payment_AuthorizationType' => 'SALE',
				'Ecom_Receipt_Description'            => $item_name,
				'Ecom_Ezic_Fulfillment_ReturnMethod'  => 'POST',
				'Ecom_Cost_Total'                     => $this->cost,
				'Ecom_UserData_salesdata'             => $this->transaction_id,
				'Ecom_Ezic_Fulfillment_ReturnURL'     => $this->get_thankyou(),
				'Ecom_Ezic_Fulfillment_GiveUpURL'     => $this->get_cancelled( $this->transaction_id ),
				'Ecom_Ezic_Security_HashValue_MD5'    => md5( $this->prefs['cryptokey'] . $this->cost . $item_name ),
				'Ecom_Ezic_Security_HashFields'       => 'Ecom_Cost_Total Ecom_Receipt_Description'
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
		 * @version 1.1
		 */
		public function preferences() {

			$prefs = $this->prefs;

?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
		<h3><?php _e( 'Details', 'mycred' ); ?></h3>
		<div class="form-group">
			<label for="<?php echo $this->field_id( 'account' ); ?>"><?php _e( 'Account ID', 'mycred' ); ?></label>
			<input type="text" name="<?php echo $this->field_name( 'account' ); ?>" id="<?php echo $this->field_id( 'account' ); ?>" value="<?php echo esc_attr( $prefs['account'] ); ?>" class="form-control" />
		</div>
		<div class="form-group">
			<label for="<?php echo $this->field_id( 'site_tag' ); ?>"><?php _e( 'Site Tag', 'mycred' ); ?></label>
			<input type="text" name="<?php echo $this->field_name( 'site_tag' ); ?>" id="<?php echo $this->field_id( 'site_tag' ); ?>" value="<?php echo esc_attr( $prefs['site_tag'] ); ?>" class="form-control" />
		</div>
		<div class="form-group">
			<label for="<?php echo $this->field_id( 'cryptokey' ); ?>"><?php _e( 'Order Integrity Key', 'mycred' ); ?></label>
			<input type="password" name="<?php echo $this->field_name( 'cryptokey' ); ?>" id="<?php echo $this->field_id( 'cryptokey' ); ?>" value="<?php echo esc_attr( $prefs['cryptokey'] ); ?>" class="form-control" />
			<p><span class="description"><?php _e( 'Found under Step 12 on the Fraud Defense page.', 'mycred' ); ?></span></p>
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
			<label><?php _e( 'Currency', 'mycred' ); ?></label>
			<input type="text" readonly="readonly" class="form-control" name="<?php echo $this->field_name( 'currency' ); ?>" value="USD" />
		</div>
		<div class="form-group">
			<label><?php _e( 'Exchange Rates', 'mycred' ); ?></label>

			<?php $this->exchange_rate_setup(); ?>

		</div>
		<div class="form-group">
			<label><?php _e( 'Postback CGI URL', 'mycred' ); ?></label>
			<code style="padding: 12px;display:block;"><?php echo $this->callback_url(); ?></code>
			<p><?php _e( 'For this gateway to work, you must login to your NETbilling account and edit your site. Under "Default payment form settings" make sure the Postback CGI URL is set to the above address and "Return method" is set to POST.', 'mycred' ); ?></p>
		</div>
	</div>
</div>
<?php

		}

		/**
		 * Sanatize Prefs
		 * @since 0.1
		 * @version 1.2
		 */
		public function sanitise_preferences( $data ) {

			$new_data              = array();

			$new_data['sandbox']   = ( isset( $data['sandbox'] ) ) ? 1 : 0;
			$new_data['account']   = sanitize_text_field( $data['account'] );
			$new_data['currency']  = sanitize_text_field( $data['currency'] );
			$new_data['site_tag']  = sanitize_text_field( $data['site_tag'] );
			$new_data['cryptokey'] = sanitize_text_field( $data['cryptokey'] );
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
