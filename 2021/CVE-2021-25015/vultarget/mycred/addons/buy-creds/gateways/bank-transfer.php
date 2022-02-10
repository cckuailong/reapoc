<?php
if ( ! defined( 'myCRED_VERSION' ) ) exit;

/**
 * myCRED_Bank_Transfer class
 * Manual payment gateway - bank transfers
 * @since 1.7
 * @version 1.1
 */
if ( ! class_exists( 'myCRED_Bank_Transfer' ) ) :
	class myCRED_Bank_Transfer extends myCRED_Payment_Gateway {

		/**
		 * Construct
		 */
		public function __construct( $gateway_prefs ) {

			$types            = mycred_get_types();
			$default_exchange = array();
			foreach ( $types as $type => $label )
				$default_exchange[ $type ] = 1;

			parent::__construct( array(
				'id'               => 'bank',
				'label'            => 'Bank Transfer',
				'documentation'    => 'http://codex.mycred.me/chapter-iii/buycred/payment-gateways/bank-transfers/',
				'gateway_logo_url' => '',
				'defaults'         => array(
					'title'            => '',
					'account'          => '',
					'logo_url'         => '',
					'currency'         => 'EUR',
					'exchange'         => $default_exchange
				)
			), $gateway_prefs );

		}

		/**
		 * Process Handler
		 * @since 1.0
		 * @version 1.0
		 */
		public function process() { }

		/**
		 * Results Handler
		 * @since 1.0
		 * @version 1.0
		 */
		public function returning() {

			add_filter( 'mycred_setup_gateways', array( $this, 'relable_gateway' ) );

		}

		/**
		 * Admin Init Handler
		 * @since 1.7
		 * @version 1.0
		 */
		public function admin_init() {

			add_filter( 'mycred_setup_gateways', array( $this, 'relable_gateway' ) );

		}

		/**
		 * Results Handler
		 * @since 1.7.6
		 * @version 1.0
		 */
		public function relable_gateway( $installed ) {

			if ( ! empty( $this->prefs['title'] ) && $this->prefs['title'] != $installed['bank']['title'] )
				$installed['bank']['title'] = $this->prefs['title'];

			return $installed;

		}

		/**
		 * AJAX Buy Handler
		 * @since 1.8
		 * @version 1.0
		 */
		public function ajax_buy() {

			$this->toggle_id = 'buycred-checkout-step2';

			$content         = $this->checkout_header();
			$content        .= $this->checkout_logo();

			$content        .= '<div id="buycred-checkout-step1">';

			$content        .= $this->checkout_order();
			$content        .= $this->checkout_cancel();

			$content        .= '</div><div id="buycred-checkout-step2" style="display: none;">';

			$content        .= $this->checkout_transaction_id();
			$content        .= wptexturize( wpautop( $this->prefs['account'] ) );

			$content        .= '</div>';

			$content        .= $this->checkout_footer();

			// Return a JSON response
			$this->send_json( $content );

		}

		/**
		 * Checkout Page Title
		 * @since 1.8
		 * @version 1.0
		 */
		public function checkout_page_title() {

			echo $this->checkout_logo();

		}

		/**
		 * Checkout Page Body
		 * @since 1.8
		 * @version 1.0
		 */
		public function checkout_page_body() {

			$this->toggle_id = 'buycred-checkout-step2';

			echo $this->checkout_header();
			echo $this->checkout_logo( false );

			echo '<div id="buycred-checkout-step1">';

			echo $this->checkout_order();
			echo $this->checkout_cancel();

			echo '</div><div id="buycred-checkout-step2" style="display: none;">';

			echo $this->checkout_transaction_id();
			echo wptexturize( wpautop( $this->prefs['account'] ) );

			echo '</div>';

			echo $this->checkout_footer();

		}

		/**
		 * Preferences
		 * @since 1.0
		 * @version 1.0
		 */
		public function preferences() {

			$prefs = $this->prefs;

?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
		<h3><?php _e( 'Details', 'mycred' ); ?></h3>
		<div class="form-group">
			<label for="<?php echo $this->field_id( 'title' ); ?>"><?php _e( 'Title', 'mycred' ); ?></label>
			<input type="text" name="<?php echo $this->field_name( 'title' ); ?>" id="<?php echo $this->field_id( 'title' ); ?>" value="<?php echo esc_attr( $prefs['title'] ); ?>" class="form-control" />
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
			<input type="text" name="<?php echo $this->field_name( 'currency' ); ?>" id="<?php echo $this->field_id( 'currency' ); ?>" value="<?php echo esc_attr( $prefs['currency'] ); ?>" class="form-control" />
		</div>
		<div class="form-group">
			<label><?php _e( 'Exchange Rates', 'mycred' ); ?></label>

			<?php $this->exchange_rate_setup(); ?>

		</div>
	</div>
</div>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<div class="form-group">
			<label for="buycredbanktransferaccount"><?php _e( 'Bank Account Information', 'mycred' ); ?></label>
			<?php wp_editor( $prefs['account'], 'buycredbanktransferaccount', array( 'textarea_name' => $this->field_name( 'account' ), 'textarea_rows' => 10 ) ); ?>
		</div>
	</div>
</div>
<script type="text/javascript">
jQuery(function($){

	$( '#mycred-gateway-prefs-bank-currency' ).change(function(){
		$( 'span.mycred-gateway-bank-currency' ).text( $(this).val() );
	});

});
</script>
<?php

		}

		/**
		 * Sanatize Prefs
		 * @since 1.0
		 * @version 1.0
		 */
		public function sanitise_preferences( $data ) {

			$new_data = array();

			$new_data['title']    = sanitize_text_field( $data['title'] );
			$new_data['logo_url'] = sanitize_text_field( $data['logo_url'] );
			$new_data['account']  = wp_kses_post( $data['account'] );
			$new_data['currency'] = sanitize_text_field( $data['currency'] );

			// If exchange is less then 1 we must start with a zero
			if ( isset( $data['exchange'] ) ) {
				foreach ( (array) $data['exchange'] as $type => $rate ) {
					if ( $rate != 1 && in_array( substr( $rate, 0, 1 ), array( '.', ',' ) ) )
						$data['exchange'][ $type ] = (float) '0' . $rate;
				}
			}
			$new_data['exchange'] = $data['exchange'];

			return $new_data;

		}

	}
endif;
