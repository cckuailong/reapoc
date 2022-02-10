<?php
if ( ! defined( 'myCRED_VERSION' ) ) exit;

/**
 * Load BitPay PHP Library
 * @since 1.8
 * @version 1.0
 */
if ( ! class_exists( 'WC_Gateway_Bitpay' ) ) {

	$autoloader_param = __DIR__ . '/Bitpay/Autoloader.php';

	// Load up the BitPay library
	if ( true === file_exists( $autoloader_param ) && true === is_readable( $autoloader_param ) ) {

		require_once $autoloader_param;
		\Bitpay\Autoloader::register();

	}

	// Exist for quirks in object serialization...
	if ( false === class_exists( 'PrivateKey' ) ) {
		include_once( __DIR__ . '/Bitpay/PrivateKey.php' );
	}

	if ( false === class_exists( 'PublicKey' ) ) {
		include_once( __DIR__ . '/Bitpay/PublicKey.php' );
	}

	if ( false === class_exists( 'Token' ) ) {
		include_once( __DIR__ . '/Bitpay/Token.php' );
	}

}

/**
 * myCRED_Bitpay class
 * BitPay (Bitcoins) - Payment Gateway
 * @since 1.4
 * @version 1.2
 */
if ( ! class_exists( 'myCRED_Bitpay' ) ) :
	class myCRED_Bitpay extends myCRED_Payment_Gateway {

		/**
		 * Construct
		 */
		public function __construct( $gateway_prefs ) {

			$types            = mycred_get_types();
			$default_exchange = array();
			foreach ( $types as $type => $label )
				$default_exchange[ $type ] = 1;

			parent::__construct( array(
				'id'               => 'bitpay',
				'label'            => 'Bitpay',
				'gateway_logo_url' => plugins_url( 'assets/images/bitpay.png', MYCRED_PURCHASE ),
				'defaults'         => array(
					'sandbox'          => 0,
					'api_public'       => '',
					'api_secret'       => '',
					'api_sign'         => '',
					'api_token'        => '',
					'api_label'        => '',
					'currency'         => 'USD',
					'exchange'         => $default_exchange,
					'item_name'        => 'Purchase of myCRED %plural%',
					'logo_url'         => '',
					'speed'            => 'high',
					'notifications'    => 1
				)
			), $gateway_prefs );

			$this->is_ready = false;
			if ( isset( $this->prefs['api_public'] ) && ! empty( $this->prefs['api_public'] ) && isset( $this->prefs['api_secret'] ) && ! empty( $this->prefs['api_secret'] ) )
				$this->is_ready = true;

		}

		/**
		 * Process
		 * @since 1.4
		 * @version 1.2
		 */
		public function process() {

			$post = file_get_contents( "php://input" );
			if ( ! empty( $post ) ) {

				$new_call = array();
				$json     = json_decode( $post, true );
				if ( ! empty( $json ) && array_key_exists( 'id', $json ) && array_key_exists( 'url', $json ) ) {

					try {

						$client      = new \Bitpay\Client\Client();
						if ( false === strpos( $json['url'], 'test' ) )
							$network = new \Bitpay\Network\Livenet();
						else
							$network = new \Bitpay\Network\Testnet();

						$client->setNetwork( $network );
						$curlAdapter = new \Bitpay\Client\Adapter\CurlAdapter();
						$client->setAdapter( $curlAdapter );

						$client->setPrivateKey( buycred_bitpay_decrypt( $this->prefs['api_secret'] ) );
						$client->setPublicKey( buycred_bitpay_decrypt( $this->prefs['api_public'] ) );
						$client->setToken( buycred_bitpay_decrypt( $this->prefs['api_token'] ) );

						$invoice     = $client->getInvoice( $json['id'] );

					} catch ( \Exception $e ) {

						$new_call[] = $e->getMessage();

					}

					if ( empty( $new_call ) ) {

						$transaction_id  = $invoice->getOrderId();
						$pending_post_id = buycred_get_pending_payment_id( $transaction_id );
						$pending_payment = $this->get_pending_payment( $pending_post_id );

						if ( $pending_payment !== false ) {

							// If account is credited, delete the post and it's comments.
							if ( $this->complete_payment( $pending_payment, $json['id'] ) )
								$this->trash_pending_payment( $pending_post_id );
							else
								$new_call[] = __( 'Failed to credit users account.', 'mycred' );

							// Log Call
							if ( ! empty( $new_call ) )
								$this->log_call( $pending_post_id, $new_call );

						}

					}

				}

			}

		}

		/**
		 * Returning
		 * @since 1.4
		 * @version 1.0
		 */
		public function returning() { }

		/**
		 * Admin Init Handler
		 * @since 1.8
		 * @version 1.0
		 */
		public function admin_init() {

			add_action( 'wp_ajax_buycred-bitpay-pairing', array( $this, 'ajax_pair' ) );

		}

		/**
		 * AJAX: Pair with bitPay
		 * @since 1.8
		 * @version 1.0
		 */
		public function ajax_pair() {

			check_ajax_referer( 'buycred-pair-bitpay', 'token' );

			$pairing_code = sanitize_text_field( $_POST['code'] );
			$network      = sanitize_text_field( $_POST['network'] );

			try {

				$key          = new \Bitpay\PrivateKey();
				$key->generate();

				$pub          = new \Bitpay\PublicKey();
				$pub->setPrivateKey( $key );
				$pub->generate();

				$sin          = new \Bitpay\SinKey();
				$sin->setPublicKey( $pub );
				$sin->generate();

				$client       = new \Bitpay\Client\Client();

				if ( $network === 'live' )
					$client->setNetwork( new \Bitpay\Network\Livenet() );
				else
					$client->setNetwork( new \Bitpay\Network\Testnet() );

				$curlAdapter  = new \Bitpay\Client\Adapter\CurlAdapter();

				$client->setAdapter( $curlAdapter );
				$client->setPrivateKey( $key );
				$client->setPublicKey( $pub );

			} catch ( \Exception $e ) {
				wp_send_json_error( $e->getMessage() );
			}

			$label        = preg_replace( '/[^a-zA-Z0-9 \-\_\.]/', '', get_bloginfo() );
			$label        = substr( 'buyCRED - ' . $label, 0, 59 );

			try {

				$token = $client->createToken(
					array(
						'id'          => (string) $sin,
						'pairingCode' => $pairing_code,
						'label'       => $label,
					)
				);

			} catch ( \Exception $e ) {

				wp_send_json_error( $e->getMessage() );

			}

			if ( $network !== 'live' )
				$label .= ' (Testnet)';

			wp_send_json_success( array(
				'api_secret' => '<input type="hidden" name="' . $this->field_name( 'api_secret' ) . '" value="' . buycred_bitpay_encrypt( $key ) . '" />',
				'api_public' => '<input type="hidden" name="' . $this->field_name( 'api_public' ) . '" value="' . buycred_bitpay_encrypt( $pub ) . '" />',
				'api_sign'   => '<input type="hidden" name="' . $this->field_name( 'api_sign' ) . '" value="' . (string) $sin . '" />',
				'api_token'  => '<input type="hidden" name="' . $this->field_name( 'api_token' ) . '" value="' . buycred_bitpay_encrypt( $token ) . '" />',
				'label'      => '<input type="hidden" name="' . $this->field_name( 'api_label' ) . '" value="' . $label . '" /><p class="form-control-static">' . $label . '</p>'
			) );

		}

		/**
		 * Prep Sale
		 * @since 1.8
		 * @version 1.0
		 */
		public function prep_sale( $new_transaction = false ) {

			// Set currency
			$this->currency = ( $this->currency == '' ) ? $this->prefs['currency'] : $this->currency;

			//Set Cost in raw format 
			$this->cost = $this->get_cost( $this->amount, $this->point_type, true );

			// Item Name
			$item_name      = str_replace( '%number%', $this->amount, $this->prefs['item_name'] );
			$item_name      = $this->core->template_tags_general( $item_name );

			$user           = get_userdata( $this->buyer_id );

			// Based on the "BitPay for WooCommerce" plugin issued by Bitpay
			try {

				// Currency
				$currency    = new \Bitpay\Currency( $this->currency );

				// First, we set the client
				$client      = new \Bitpay\Client\Client();

				if ( ! $this->sandbox_mode )
					$client->setNetwork( new \Bitpay\Network\Livenet() );
				else
					$client->setNetwork( new \Bitpay\Network\Testnet() );

				$curlAdapter = new \Bitpay\Client\Adapter\CurlAdapter();
				$client->setAdapter($curlAdapter);

				$client->setPrivateKey( buycred_bitpay_decrypt( $this->prefs['api_secret'] ) );
				$client->setPublicKey( buycred_bitpay_decrypt( $this->prefs['api_public'] ) );
				$client->setToken( buycred_bitpay_decrypt( $this->prefs['api_token'] ) );

				// Next, we create an invoice object
				$invoice     = new \Bitpay\Invoice();
				$invoice->setOrderId( (string) $this->transaction_id );
				$invoice->setCurrency( $currency ) ;
				$invoice->setFullNotifications( ( ( $this->prefs['notifications'] ) ? true : false ) );

				// Next, we set the invoice item
				$item        = new \Bitpay\Item();
				$item->setPrice( $this->cost );
				$item->setDescription( $item_name );

				// This includes setting the buyer
				$buyer       = new \Bitpay\Buyer();
				$buyer->setEmail( $user->user_email );

				$invoice->setBuyer( $buyer );
				$invoice->setItem( $item );

				// Append extras
				$invoice->setRedirectUrl( $this->get_thankyou() );
				$invoice->setNotificationUrl( $this->callback_url() );
				$invoice->setTransactionSpeed( $this->prefs['speed'] );

				// Create an invoice
				$invoice     = $client->createInvoice( $invoice );

			} catch ( \Exception $e ) {

				$this->errors[] = $e->getMessage();

			}

			if ( empty( $this->errors ) ) {

				$this->redirect_to = $invoice->getUrl();

			}

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
		 * Gateway Prefs
		 * @since 1.4
		 * @version 1.0
		 */
		function preferences() {

			$prefs = $this->prefs;

?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
		<h3><?php _e( 'Details', 'mycred' ); ?></h3>

		<?php if ( ! $this->is_ready ) : ?>

		<div class="form-group">
			<label><?php _e( 'API Token', 'mycred' ); ?></label>
			<div class="form-inline" id="bitpay-pairing-wrapper">
				<input type="text" id="bitpay-pair-code" class="form-control" placeholder="Pairing Code" value="" /> 
				<select id="bitpay-pair-network" class="form-control">
					<option value="live">Livenet</option>
					<option value="test">Testnet</option>
				</select> 
				<button type="button" id="sync-bitpay-pairing-code" class="button button-secondary">Sync</button>
			</div>
			<p class="description bitpay-link" id="bitpay-link-live"><span>Get a pairing code: <a href="https://bitpay.com/api-tokens" target="_blank">https://bitpay.com/api-tokens</a></span></p>
			<p class="description bitpay-link" id="bitpay-link-test" style="display: none;"><span>Get a pairing code: <a href="https://test.bitpay.com/api-tokens" target="_blank">https://test.bitpay.com/api-tokens</a></span></p>
		</div>
<script type="text/javascript">
jQuery(function($){

	$( '#sync-bitpay-pairing-code' ).click(function(e){

		e.preventDefault();

		var pairwrapper = $( '#bitpay-pairing-wrapper' );

		$.ajax({
			type     : "POST",
			data     : {
				action  : 'buycred-bitpay-pairing',
				token   : '<?php echo wp_create_nonce( 'buycred-pair-bitpay' ); ?>',
				code    : $( '#bitpay-pair-code' ).val(),
				network : $( '#bitpay-pair-network' ).find( ':selected' ).val(),
			},
			dataType : "JSON",
			url      : '<?php echo admin_url( 'admin-ajax.php' ); ?>',
			beforeSend : function() {

				$( '#sync-bitpay-pairing-code' ).attr( 'disabled', 'disabled' );

			},
			success  : function( response ) {

				if ( response.success ) {

					pairwrapper.slideUp(function(){
						pairwrapper.empty();
						$.each( response.data, function(index,element){
							pairwrapper.append( element );
						});
						pairwrapper.slideDown();
					});

				}
				else {

					alert( response.data );
					$( '#sync-bitpay-pairing-code' ).removeAttr( 'disabled' );

				}

			},
			error    : function() {
				alert( 'Communications Error' );
			}
		});

	});

	$( '#bitpay-pair-network' ).change(function(){

		$( '.bitpay-link' ).hide();
		var selectedmode = $(this).find( ':selected' );
		$( '#bitpay-link-' + selectedmode.val() ).show();

	});

});
</script>

		<?php else : ?>

		<div class="form-group">
			<label><?php _e( 'API Token', 'mycred' ); ?></label>
			<p class="form-control-static"><?php echo esc_attr( $prefs['api_label'] ); ?></p>
			<button type="button" id="bitpay-cancel-pair" class="button button-secondary">Revoke Token</button>
			<input type="hidden" class="reset-api" name="<?php echo $this->field_name( 'api_secret' ); ?>" value="<?php echo esc_attr( $prefs['api_secret'] ); ?>" />
			<input type="hidden" class="reset-api" name="<?php echo $this->field_name( 'api_public' ); ?>" value="<?php echo esc_attr( $prefs['api_public'] ); ?>" />
			<input type="hidden" class="reset-api" name="<?php echo $this->field_name( 'api_sign' ); ?>" value="<?php echo esc_attr( $prefs['api_sign'] ); ?>" />
			<input type="hidden" class="reset-api" name="<?php echo $this->field_name( 'api_token' ); ?>" value="<?php echo esc_attr( $prefs['api_token'] ); ?>" />
			<input type="hidden" class="reset-api" name="<?php echo $this->field_name( 'api_label' ); ?>" value="<?php echo esc_attr( $prefs['api_label'] ); ?>" />
		</div>
<script type="text/javascript">
jQuery(function($){

	$( '#bitpay-cancel-pair' ).click(function(e){

		e.preventDefault();

		if ( confirm( '<?php echo esc_js( esc_attr( __( 'Are you sure you want to do this?', 'mycred' ) ) ); ?>' ) ) {

			$( 'input.reset-api' ).val( '' );
			$(this).before().html( '<?php echo esc_js( __( 'Removed - Remember to save your changes.', 'mycred' ) ); ?>' );

		}

	});

});
</script>

		<?php endif; ?>

		<div class="form-group">
			<label for="<?php echo $this->field_id( 'item_name' ); ?>"><?php _e( 'Item Name', 'mycred' ); ?></label>
			<input type="text" name="<?php echo $this->field_name( 'item_name' ); ?>" id="<?php echo $this->field_id( 'item_name' ); ?>" value="<?php echo esc_attr( $prefs['item_name'] ); ?>" class="form-control" />
		</div>
		<div class="form-group">
			<label for="<?php echo $this->field_id( 'logo_url' ); ?>"><?php _e( 'Logo URL', 'mycred' ); ?></label>
			<input type="text" name="<?php echo $this->field_name( 'logo_url' ); ?>" id="<?php echo $this->field_id( 'logo_url' ); ?>" value="<?php echo esc_attr( $prefs['logo_url'] ); ?>" class="form-control" />
		</div>
		<div class="row">
			<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
				<div class="form-group">
					<label for="<?php echo $this->field_id( 'speed' ); ?>"><?php _e( 'Transaction Speed', 'mycred' ); ?></label>
					<select name="<?php echo $this->field_name( 'speed' ); ?>" id="<?php echo $this->field_id( 'speed' ); ?>" class="form-control">
<?php

			$options = array(
				'high'   => __( 'High', 'mycred' ),
				'medium' => __( 'Medium', 'mycred' ),
				'low'    => __( 'Low', 'mycred' )
			);
			foreach ( $options as $value => $label ) {
				echo '<option value="' . $value . '"';
				if ( $prefs['speed'] == $value ) echo ' selected="selected"';
				echo '>' . $label . '</option>';
			}

?>

					</select>
				</div>
			</div>
			<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
				<div class="form-group">
					<label for="<?php echo $this->field_id( 'notifications' ); ?>"><?php _e( 'Full Notifications', 'mycred' ); ?></label>
					<select name="<?php echo $this->field_name( 'notifications' ); ?>" id="<?php echo $this->field_id( 'notifications' ); ?>" class="form-control">
<?php

			$options = array(
				0 => __( 'No', 'mycred' ),
				1 => __( 'Yes', 'mycred' )
			);
			foreach ( $options as $value => $label ) {
				echo '<option value="' . $value . '"';
				if ( $prefs['notifications'] == $value ) echo ' selected="selected"';
				echo '>' . $label . '</option>';
			}

?>

					</select>
				</div>
			</div>
		</div>
	</div>
	<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
		<h3><?php _e( 'Setup', 'mycred' ); ?></h3>
		<div class="form-group">
			<label for="<?php echo $this->field_id( 'currency' ); ?>"><?php _e( 'Currency', 'mycred' ); ?></label>
			<input type="text" name="<?php echo $this->field_name( 'currency' ); ?>" id="<?php echo $this->field_id( 'currency' ); ?>" value="<?php echo $prefs['currency']; ?>" class="form-control" maxlength="3" placeholder="<?php _e( 'Currency Code', 'mycred' ); ?>" />

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
		 * @since 1.4
		 * @version 1.2
		 */
		public function sanitise_preferences( $data ) {

			$new_data                  = array();

			if ( array_key_exists( 'api_secret', $data ) ) {
				$new_data['api_secret']    = sanitize_text_field( $data['api_secret'] );
				$new_data['api_public']    = sanitize_text_field( $data['api_public'] );
				$new_data['api_sign']      = sanitize_text_field( $data['api_sign'] );
				$new_data['api_token']     = sanitize_text_field( $data['api_token'] );
				$new_data['api_label']     = sanitize_text_field( $data['api_label'] );
			}
			else {
				$new_data['api_secret']    = '';
				$new_data['api_public']    = '';
				$new_data['api_sign']      = '';
				$new_data['api_token']     = '';
				$new_data['api_label']     = '';
			}

			$new_data['sandbox']       = ( isset( $data['sandbox'] ) ) ? 1 : 0;
			$new_data['currency']      = sanitize_text_field( $data['currency'] );
			$new_data['item_name']     = sanitize_text_field( $data['item_name'] );
			$new_data['logo_url']      = sanitize_text_field( $data['logo_url'] );
			$new_data['speed']         = sanitize_text_field( $data['speed'] );
			$new_data['notifications'] = sanitize_text_field( $data['notifications'] );

			// If exchange is less then 1 we must start with a zero
			if ( isset( $data['exchange'] ) ) {
				foreach ( (array) $data['exchange'] as $type => $rate ) {
					if ( $rate != 1 && in_array( substr( $rate, 0, 1 ), array( '.', ',' ) ) )
						$data['exchange'][ $type ] = (float) '0' . $rate;
				}
			}
			$new_data['exchange']      = $data['exchange'];

			return $new_data;

		}

	}
endif;

/**
 * Bitpay Encrypt
 * @since 1.8
 * @version 1.0
 */
if ( ! function_exists( 'buycred_bitpay_encrypt' ) ) :
	function buycred_bitpay_encrypt( $data ) {

        if (false === isset($data) || true === empty($data)) {
            throw new \Exception('The Bitpay payment plugin was called to encrypt data but no data was passed!');
        }

        $openssl_ext = new \Bitpay\Crypto\OpenSSLExtension();
        $fingerprint = sha1(sha1(__DIR__));

        if (true === isset($fingerprint) &&
            true === isset($openssl_ext)  &&
            strlen($fingerprint) > 24)
        {
            $fingerprint = substr($fingerprint, 0, 24);

            if (false === isset($fingerprint) || true === empty($fingerprint)) {
                throw new \Exception('The Bitpay payment plugin was called to encrypt data but could not generate a fingerprint parameter!');
            }

            $encrypted = $openssl_ext->encrypt(base64_encode(serialize($data)), $fingerprint, '1234567890123456');

            if (true === empty($encrypted)) {
                throw new \Exception('The Bitpay payment plugin was called to serialize an encrypted object and failed!');
            }

            return $encrypted;
        } else {
            wp_die('Invalid server fingerprint generated');
        }

	}
endif;

/**
 * Bitpay Decrypt
 * @since 1.8
 * @version 1.0
 */
if ( ! function_exists( 'buycred_bitpay_decrypt' ) ) :
    function buycred_bitpay_decrypt( $encrypted ) {

        if (false === isset($encrypted) || true === empty($encrypted)) {
            throw new \Exception('The Bitpay payment plugin was called to decrypt data but no data was passed!');
        }
        $openssl_ext = new \Bitpay\Crypto\OpenSSLExtension();
       
        $fingerprint = sha1(sha1(__DIR__));

        if (true === isset($fingerprint) &&
            true === isset($openssl_ext)  &&
            strlen($fingerprint) > 24)
        {
            $fingerprint = substr($fingerprint, 0, 24);

            if (false === isset($fingerprint) || true === empty($fingerprint)) {
                throw new \Exception('The Bitpay payment plugin was called to decrypt data but could not generate a fingerprint parameter!');
            }

            $decrypted = base64_decode($openssl_ext->decrypt($encrypted, $fingerprint, '1234567890123456'));

            // Strict base64 char check
            if (false === base64_decode($decrypted, true)) {
                $error_string = '    [Warning] In bitpay_decrypt: data appears to have already been decrypted. Strict base64 check failed.';
            } else {
                $decrypted = base64_decode($decrypted);
            }

            if (true === empty($decrypted)) {
                throw new \Exception('The Bitpay payment plugin was called to unserialize a decrypted object and failed! The decrypt function was called with "' . $encrypted . '"');
            }

            return unserialize($decrypted);
        } else {
            wp_die('Invalid server fingerprint generated');
        }

	}
endif;
