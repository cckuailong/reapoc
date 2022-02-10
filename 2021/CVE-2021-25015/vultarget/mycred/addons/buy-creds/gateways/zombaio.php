<?php
if ( ! defined( 'myCRED_VERSION' ) ) exit;

/**
 * myCRED_Zombaio class
 * Zombaio Payment Gateway
 * @since 1.1
 * @version 1.2
 */
if ( ! class_exists( 'myCRED_Zombaio' ) ) :
	class myCRED_Zombaio extends myCRED_Payment_Gateway {

		/**
		 * Construct
		 */
		public function __construct( $gateway_prefs ) {

			$types            = mycred_get_types();
			$default_exchange = array();
			foreach ( $types as $type => $label )
				$default_exchange[ $type ] = 1;

			parent::__construct( array(
				'id'               => 'zombaio',
				'label'            => 'Zombaio',
				'gateway_logo_url' => plugins_url( 'assets/images/zombaio.png', MYCRED_PURCHASE ),
				'defaults'         => array(
					'sandbox'          => 0,
					'site_id'          => '',
					'pricing_id'       => '',
					'dynamic'          => 0,
					'currency'         => 'USD',
					'gwpass'           => '',
					'logo_url'         => '',
					'lang'             => 'ZOM',
					'exchange'         => $default_exchange,
					'bypass_ipn'       => 0
				)
			), $gateway_prefs );

		}

		/**
		 * Verify Z-script
		 * @since 1.8
		 * @version 1.0
		 */
		public function returning() {

			// ZOA Validation
			if ( isset( $_REQUEST['wp_zombaio_ips'] ) || isset( $_REQUEST['ZombaioGWPass'] ) && isset( $_GET['username'] ) && substr( $_GET['username'], 0, 4 ) == 'Test' ) {

				if ( ! headers_sent() )
					header( 'HTTP/1.1 200 OK' );

				echo 'OK';
				die;

			}

		}

		/**
		 * Process
		 * @since 1.1
		 * @version 1.0
		 */
		public function process() {

			if ( isset( $_GET['wp_zombaio_ips'] ) && $_GET['wp_zombaio_ips'] == 1 ) {

				if ( isset( $_GET['csv'] ) && $_GET['csv'] == 1 ) {
					echo '<textarea style="width: 270px;" rows="10" readonly="readonly">' . implode( ',', $this->get_zombaio_ips() ) . '</textarea>';
					exit;
				}

				echo '<ul>';
				foreach ( $ips as $ip ) {
					echo '<li><input type="text" readonly="readonly" value="' . $ip . '" size="15" /></li>';
				}
				echo '</ul>';

				exit;

			}

			$this->handle_call();

		}

		/**
		 * Verify IPN IP
		 * @since 1.1
		 * @version 1.1
		 */
		public function verify_zombaio_call() {

			if ( $this->prefs['bypass_ipn'] ) return true;

			$zombaio_ips = $this->get_zombaio_ips();
			if ( empty( $zombaio_ips ) ) return true;

			if ( $_SERVER['REMOTE_ADDR'] != '' ) {

				$remote_addr = explode( '.', $_SERVER['REMOTE_ADDR'] );
				$remote_addr = $remote_addr[0] . '.' . $remote_addr[1] . '.' . $remote_addr[2] . '.';

				if ( in_array( $remote_addr, $zombaio_ips ) ) return true;

			}

			return false;

		}

		/**
		 * IPN - Is Valid Call
		 * Replaces the default check
		 * @since 1.4
		 * @version 1.1
		 */
		public function IPN_is_valid_call() {

			$result = true;

			// Check password
			if ( $_GET['ZombaioGWPass'] != $this->prefs['gwpass'] )
				$result = false;

			// Check IPN
			if ( $result === true && $this->prefs['bypass_ipn'] == 0 ) {

				$zombaio_ips = $this->get_zombaio_ips();
				if ( ! empty( $zombaio_ips ) ) {

					if ( $_SERVER['REMOTE_ADDR'] != '' ) {

						$remote_addr = explode( '.', $_SERVER['REMOTE_ADDR'] );
						$remote_addr = $remote_addr[0] . '.' . $remote_addr[1] . '.' . $remote_addr[2] . '.';

						if ( ! in_array( $remote_addr, $zombaio_ips ) )
							$result = false;

					}

				}

			}

			// Check Site ID
			if ( $result === true && $_GET['SiteID'] != $this->prefs['site_id'] )
				$result = false;

			return $result;

		}

		/**
		 * Handle IPN Call
		 * @since 1.1
		 * @version 1.2.1
		 */
		public function handle_call() {

			$outcome = 'FAILED';

			// ZOA Validation
			if ( isset( $_GET['username'] ) && substr( $_GET['username'], 0, 4 ) == 'Test' ) {
				if ( ! headers_sent() )
					header( 'HTTP/1.1 200 OK' );

				echo 'OK';
				die;
			}

			// Required fields
			if ( isset( $_GET['ZombaioGWPass'] ) && isset( $_GET['SiteID'] ) && isset( $_GET['Action'] ) && isset( $_GET['Credits'] ) && isset( $_GET['TransactionID'] ) && isset( $_GET['Identifier'] ) ) {

				// In case this is a true Zombaio call but for other actions, return now
				// to allow other plugins to take over.
				if ( $_GET['Action'] != 'user.addcredits' )
					return;

				// Get Pending Payment
				$pending_post_id = sanitize_key( $_GET['Identifier'] );
				$pending_payment = $this->get_pending_payment( $pending_post_id );
				if ( $pending_payment !== false ) {

					// Validate call
					if ( $this->IPN_is_valid_call() ) {

						$errors         = false;
						$new_call       = array();
						$transaction_id = sanitize_text_field( $_GET['TransactionID'] );

						// Make sure transaction is unique
						if ( ! $this->transaction_id_is_unique( $transaction_id ) ) {
							$new_call[] = sprintf( __( 'Duplicate transaction. Received: %s', 'mycred' ), $transaction_id );
							$errors     = true;
						}

						// Live transaction during testing
						if ( $this->sandbox_mode && $transaction_id != '0000' ) {
							$new_call[] = sprintf( __( 'Live transaction while debug mode is enabled! Received: %s', 'mycred' ), $transaction_id );
							$errors     = true;
						}

						// Credit payment
						if ( $errors === false ) {

							if ( $this->prefs['dynamic'] == 1 ) {
								$amount = $pending_payment->amount;
								$cost   = $pending_payment->cost;
							}

							else {
								$amount = sanitize_text_field( $_GET['Credits'] );
								$cost   = 0;
							}

							if ( is_numeric( $amount ) && $amount > 0 ) {

								// Type
								$point_type                = $pending_payment->point_type;
								$mycred                    = mycred( $point_type );

								$pending_payment->amount   = $mycred->number( $amount );
								$pending_payment->cost     = $cost;

								// If account is credited, delete the post and it's comments.
								if ( $this->complete_payment( $pending_payment, $transaction_id ) ) {
									$this->trash_pending_payment( $pending_post_id );
									$outcome = 'COMPLETED';
								}
								else
									$new_call[] = __( 'Failed to credit users account.', 'mycred' );

							}

						}

						// Log Call
						if ( ! empty( $new_call ) )
							$this->log_call( $pending_post_id, $new_call );

					}

				}

			}

			if ( $outcome == 'COMPLETED' )
				die( 'OK' );
			else
				die( 'ERROR' );

		}

		/**
		 * Prep Sale
		 * @since 1.8
		 * @version 1.0
		 */
		public function prep_sale( $new_transaction = false ) {

			// Set currency
			$this->currency    = ( $this->currency == '' ) ? $this->prefs['currency'] : $this->currency;

			// Item Name
			$item_name         = str_replace( '%number%', $this->amount, $this->prefs['item_name'] );
			$item_name         = $this->core->template_tags_general( $item_name );

			$this->redirect_to = 'https://secure.zombaio.com/?' . $this->prefs['site_id'] . '.' . $this->prefs['pricing_id'] . '.' . $this->prefs['lang'];

			$redirect_fields                = array();
			$redirect_fields['identifier']  = $this->post_id;
			$redirect_fields['approve_url'] = $this->get_thankyou();
			$redirect_fields['decline_url'] = $this->callback_url();

			if ( $this->prefs['dynamic'] ) {
				$redirect_fields['DynAmount_Value'] = $this->cost;
				$redirect_fields['DynAmount_Hash']  = md5( $this->prefs['gwpass'] . $this->cost );
			}

			$this->redirect_fields = $redirect_fields;

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
		 * @since 1.1
		 * @version 1.0.1
		 */
		function preferences() {

			$prefs = $this->prefs;

?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
		<h3><?php _e( 'Details', 'mycred' ); ?></h3>
		<div class="form-group">
			<label for="<?php echo $this->field_id( 'site_id' ); ?>"><?php _e( 'Site ID', 'mycred' ); ?></label>
			<input type="text" name="<?php echo $this->field_name( 'site_id' ); ?>" id="<?php echo $this->field_id( 'site_id' ); ?>" value="<?php echo esc_attr( $prefs['site_id'] ); ?>" class="form-control" />
		</div>
		<div class="form-group">
			<label for="<?php echo $this->field_id( 'gwpass' ); ?>"><?php _e( 'GW Password', 'mycred' ); ?></label>
			<input type="text" name="<?php echo $this->field_name( 'gwpass' ); ?>" id="<?php echo $this->field_id( 'gwpass' ); ?>" value="<?php echo esc_attr( $prefs['gwpass'] ); ?>" class="form-control" />
		</div>
		<div class="form-group">
			<label for="<?php echo $this->field_id( 'logo_url' ); ?>"><?php _e( 'Logo URL', 'mycred' ); ?></label>
			<input type="text" name="<?php echo $this->field_name( 'logo_url' ); ?>" id="<?php echo $this->field_id( 'logo_url' ); ?>" value="<?php echo esc_attr( $prefs['logo_url'] ); ?>" class="form-control" />
		</div>
		<div class="row">
			<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
				<div class="form-group">
					<label for="<?php echo $this->field_id( 'bypass_ipn' ); ?>"><?php _e( 'IP Verification', 'mycred' ); ?></label>
					<select name="<?php echo $this->field_name( 'bypass_ipn' ); ?>" id="<?php echo $this->field_id( 'bypass_ipn' ); ?>" class="form-control">
<?php

			$options = array(
				0 => __( 'No', 'mycred' ),
				1 => __( 'Yes', 'mycred' )
			);
			foreach ( $options as $value => $label ) {
				echo '<option value="' . $value . '"';
				if ( $prefs['bypass_ipn'] == $value ) echo ' selected="selected"';
				echo '>' . $label . '</option>';
			}

?>
					</select>
				</div>
			</div>
			<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
				<div class="form-group">
					<label for="<?php echo $this->field_id( 'lang' ); ?>"><?php _e( 'Language', 'mycred' ); ?></label>

					<?php $this->lang_dropdown( 'lang' ); ?>

				</div>
			</div>
		</div>
	</div>
	<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
		<h3><?php _e( 'Setup', 'mycred' ); ?></h3>
		<div class="form-group">
			<label for="<?php echo $this->field_id( 'pricing_id' ); ?>"><?php _e( 'Pricing ID', 'mycred' ); ?></label>
			<input type="text" name="<?php echo $this->field_name( 'pricing_id' ); ?>" id="<?php echo $this->field_id( 'pricing_id' ); ?>" value="<?php echo esc_attr( $prefs['pricing_id'] ); ?>" class="form-control" />
		</div>
		<div class="form-group">
			<div class="checkbox">
				<label for="<?php echo $this->field_id( 'dynamic' ); ?>"><input type="checkbox" name="<?php echo $this->field_name( 'dynamic' ); ?>" id="<?php echo $this->field_id( 'dynamic' ); ?>"<?php checked( $prefs['dynamic'], 1 ); ?> value="1" /> <?php _e( 'This pricing ID is a "Dynamic Credits Purchase" in Zombaio.', 'mycred' ); ?></label>
			</div>
		</div>
		<div id="zombaio-dynamic-wrapper" style="display: <?php if ( $prefs['dynamic'] == 1 ) echo 'block'; else echo 'none'; ?>;">
			<div class="form-group">
				<label for="<?php echo $this->field_id( 'currency' ); ?>"><?php _e( 'Currency', 'mycred' ); ?></label>

				<?php $this->currencies_dropdown( 'currency', 'mycred-gateway-zombaio-currency' ); ?>

			</div>
			<div class="form-group">
				<label><?php _e( 'Exchange Rates', 'mycred' ); ?></label>

				<?php $this->exchange_rate_setup(); ?>

			</div>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<h3><?php _e( 'Postback URL (ZScript)', 'mycred' ); ?></h3>
		<code style="padding: 12px;display:block;"><?php echo get_bloginfo( 'url' ); ?></code>
		<p><?php _e( 'For this gateway to work, login to ZOA and set the Postback URL to the above address and click validate.', 'mycred' ); ?></p>
	</div>
</div>
<script type="text/javascript">
jQuery(function($){

	$( '#<?php echo $this->field_id( 'dynamic' ); ?>' ).click(function(){

		if ( $(this).is( ':checked' ) )
			$( '#zombaio-dynamic-wrapper' ).show();
		else
			$( '#zombaio-dynamic-wrapper' ).hide();

	});

});
</script>
<?php

		}

		/**
		 * Sanatize Prefs
		 * @since 1.1
		 * @version 1.2
		 */
		public function sanitise_preferences( $data ) {

			$new_data               = array();

			$new_data['sandbox']    = ( array_key_exists( 'sandbox', $data ) ) ? 1 : 0;
			$new_data['site_id']    = sanitize_text_field( $data['site_id'] );
			$new_data['gwpass']     = sanitize_text_field( $data['gwpass'] );
			$new_data['pricing_id'] = sanitize_text_field( $data['pricing_id'] );
			$new_data['dynamic']    = ( array_key_exists( 'dynamic', $data ) ) ? 1 : 0;
			$new_data['currency']   = sanitize_text_field( $data['currency'] );
			$new_data['logo_url']   = sanitize_text_field( $data['logo_url'] );
			$new_data['bypass_ipn'] = ( array_key_exists( 'bypass_ipn', $data ) ) ? 1 : 0;
			$new_data['lang']       = sanitize_text_field( $data['lang'] );

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

		/**
		 * Currency Dropdown
		 * @since 1.8
		 * @version 1.0
		 */
		public function currencies_dropdown( $name = '', $js = '' ) {

			$currencies = array(
				'USD' => 'US Dollars'
			);
			$currencies = apply_filters( 'mycred_dropdown_currencies_' . $this->id, $currencies );

			if ( $js != '' )
				$js = ' data-update="' . $js . '"';

			echo '<select name="' . $this->field_name( $name ) . '" id="' . $this->field_id( $name ) . '" class="currency form-control"' . $js . '>';
			echo '<option value="">' . __( 'Select', 'mycred' ) . '</option>';
			foreach ( $currencies as $code => $cname ) {
				echo '<option value="' . $code . '"';
				if ( isset( $this->prefs[ $name ] ) && $this->prefs[ $name ] == $code ) echo ' selected="selected"';
				echo '>' . $cname . '</option>';
			}
			echo '</select>';

		}

		/**
		 * Language Dropdown
		 * @since 1.1
		 * @version 1.0
		 */
		public function lang_dropdown( $name ) {

			$languages = array(
				'ZOM' => 'Let Zombaio Detect Language',
				'US'  => 'English',
				'FR'  => 'French',
				'DE'  => 'German',
				'IT'  => 'Italian',
				'JP'  => 'Japanese',
				'ES'  => 'Spanish',
				'SE'  => 'Swedish',
				'KR'  => 'Korean',
				'CH'  => 'Traditional Chinese',
				'HK'  => 'Simplified Chinese'
			);

			echo '<select name="' . $this->field_name( $name ) . '" id="' . $this->field_id( $name ) . '" class="form-control">';
			echo '<option value="">' . __( 'Select', 'mycred' ) . '</option>';
			foreach ( $languages as $code => $cname ) {
				echo '<option value="' . $code . '"';
				if ( isset( $this->prefs[ $name ] ) && $this->prefs[ $name ] == $code ) echo ' selected="selected"';
				echo '>' . $cname . '</option>';
			}
			echo '</select>';

		}

		/**
		 * First Comment
		 * @since 1.7.3
		 * @version 1.0.1
		 */
		public function first_comment( $comment ) {

			return 'New Zombaio purchase confirmation.';

		}

		/**
		 * Load IPN IP List
		 * @since 1.1
		 * @version 1.1
		 */
		public function get_zombaio_ips() {

			$request = new WP_Http();
			$data    = $request->request( 'http://www.zombaio.com/ip_list.txt' );
			$data    = explode( '|', $data['body'] );

			$zombaio_ips = array();
			if ( ! empty( $data ) ) {
				foreach ( $data as $ip_range ) {
					if ( $ip_range != '' )
						$zombaio_ips[] = $ip_range;
				}
			}

			return $zombaio_ips;

		}

	}
endif;
