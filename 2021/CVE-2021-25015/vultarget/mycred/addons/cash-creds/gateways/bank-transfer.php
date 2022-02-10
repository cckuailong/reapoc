<?php
if ( ! defined( 'myCRED_VERSION' ) ) exit;

/**
 * myCRED_cashcred_Bank_Transfer class
 * Manual payment gateway - bank transfers
 * @since 1.7
 * @version 1.1
 */
if ( ! class_exists( 'myCRED_cashcred_Bank_Transfer' ) ) :
	class myCRED_cashcred_Bank_Transfer extends myCRED_Cash_Payment_Gateway {

		/**
		 * Construct
		 */
		public function __construct( $gateway_prefs ) {

			$types            = mycred_get_types();
			
			$default_exchange = array();

			foreach ( $types as $type => $label ) $default_exchange[ $type ] = 1;

			parent::__construct( array(
				'id'               => 'bank',
				'label'            => 'Bank Transfer',
				'documentation'    => 'http://codex.mycred.me/chapter-iii/buycred/payment-gateways/bank-transfers/',
				'gateway_logo_url' => '',
				'defaults'         => array(
					'enable_additional_notes' => '',
					'additional_notes'  => '',
					'minimum_amount' 	=> '',
					'maximum_amount' 	=> '',
					'currency'          => 'EUR',
					'exchange'          => $default_exchange
				)
			), $gateway_prefs );

		}

		/**
		 * Process Handler
		 * @since 1.0
		 * @version 1.0
		 */
		public function process( $post = false ) {

			$time = current_time( 'mysql' );

			update_post_meta( $post,  'cashcred_payment_transfer_date', $time );

			return array (
				'status' => true , 
				'message' => 'Amount transfer successfully to your bank account', 
				'date' => $time
			);
 
		}

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
			<label for="<?php echo $this->field_id( 'minimum_amount' ); ?>"><?php _e( 'Minimum Points Withdrawal', 'mycred' ); ?></label>
			<input type="number" name="<?php echo $this->field_name( 'minimum_amount' ); ?>" id="<?php echo $this->field_id( 'minimum_amount' ); ?>"  min="1" value="<?php echo esc_attr( $prefs['minimum_amount'] ); ?>" class="form-control" />
		</div>
		 
		<div class="form-group">
			<label for="<?php echo $this->field_id( 'maximum_amount' ); ?>"><?php _e( 'Maximum Points Withdrawal', 'mycred' ); ?></label>
			<input type="number" name="<?php echo $this->field_name( 'maximum_amount' ); ?>" id="<?php echo $this->field_id( 'maximum_amount' ); ?>" value="<?php echo esc_attr( $prefs['maximum_amount'] ); ?>" class="form-control" />
		</div>
		
		<div class="form-group">
			<label for="<?php echo $this->field_id( 'enable_additional_notes' ); ?>"><input type="checkbox" name="<?php echo $this->field_name( 'enable_additional_notes' ); ?>" id="<?php echo $this->field_id( 'enable_additional_notes' ); ?>" value="1"<?php checked( $prefs['enable_additional_notes'], 1 ); ?> /> <?php _e( 'Enable Additional Notes', 'mycred' ); ?></label>
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
 
<div class="row" id="additional_notes_show">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<div class="form-group">
			<label for="cashcredbanktransferaccount"><?php _e( 'Additional notes', 'mycred' ); ?></label>
			<?php wp_editor( $prefs['additional_notes'], 'cashcredbanktransferaccount', array( 'textarea_name' => $this->field_name( 'additional_notes' ), 'textarea_rows' => 10 ) ); ?>
		</div>
	</div>
</div>
<script>
	// on change additional-notes div hide
	jQuery(function() {

		jQuery('#mycred-gateway-prefs-bank-enable-additional-notes').change(function() {
			
			if (jQuery(this).is(':checked')) {
			  
				jQuery('#additional_notes_show').slideDown('fast');
			 
			}
			else {
				  
				jQuery('#additional_notes_show').slideUp(400);
			
			}

		});
    
    });

	// onload additional-notes div hide
	if (jQuery('#mycred-gateway-prefs-bank-enable-additional-notes').is(':checked')) {
	  
		jQuery('#additional_notes_show').slideDown('fast');
	 
	}
	else {
		  
		jQuery('#additional_notes_show').slideUp(400);
	
	} 
</script>
<?php
		}
		
		public function cashcred_payment_settings( $data ) {
			
			$mycred_pref_cashcreds = mycred_get_option( 'mycred_pref_cashcreds' , false );

			$fields    = $this->form_fields();

			$bank_form = new CashCred_Gateway_Fields( $data, $fields );

			?>
			<div id="panel_<?php echo $data;?>" class="cashcred_panel">
					
				<div class="form-group">  
					<label><h3><?php _e( 'Bank account details', 'mycred' )?></h3></label>
				</div>
				
				<?php if( isset( $mycred_pref_cashcreds["gateway_prefs"]["bank"]["enable_additional_notes"] ) ): ?>
				<div class="form-group">  
					<p><?php echo $mycred_pref_cashcreds["gateway_prefs"]["bank"]["additional_notes"]; ?></p>
				</div> 
				<?php endif;?>

				<?php $bank_form->generate_form(); ?>
			 	
			</div>
			
			<?php 
		}
		
		
		/**
		 * Bank Transfer Form Fields
		 * @since 2.0
		 * @version 1.0
		 */
		public function form_fields() {

			$gateway_fields = array(
				'ac_name' => array(
					'type'        => 'text',
					'lable'       => 'Account name',
					'classes'     => 'form-control',
					'placeholder' => 'Account name',
				),
				'ac_number' => array(
					'type'        => 'text',
					'lable'       => 'Account number',
					'classes'     => 'form-control',
					'placeholder' => 'Account number',
				),
				'ac_code' => array(
					'type'        => 'text',
					'lable'       => 'Sort code',
					'classes'     => 'form-control',
					'placeholder' => 'Sort code',
				),
				'ba_name' => array(
					'type'        => 'text',
					'lable'       => 'Bank name',
					'classes'     => 'form-control',
					'placeholder' => 'Bank name',
				),
				'ro_number' => array(
					'type'        => 'text',
					'lable'       => 'Routing number',
					'classes'     => 'form-control',
					'placeholder' => 'Routing number',
				),
				'ib_name' => array(
					'type'        => 'text',
					'lable'       => 'IBAN',
					'classes'     => 'form-control',
					'placeholder' => 'IBAN',
				),
				'sw_code' => array(
					'type'        => 'text',
					'lable'       => 'Swift code',
					'classes'     => 'form-control',
					'placeholder' => 'Swift code',
				)
			);

			return apply_filters( 'mycred_cashcred_bank_transfer_fields', $gateway_fields );

		}
		
		
		/**
		 * Sanatize Prefs
		 * @since 1.0
		 * @version 1.0
		 */
		public function sanitise_preferences( $data ) {

			$new_data = array();

			$new_data['additional_notes']    = sanitize_text_field( $data['additional_notes'] );
			
			if( isset( $data['enable_additional_notes'] ) ) {
			
				$new_data['enable_additional_notes'] = sanitize_text_field( $data['enable_additional_notes'] );
			
			}
			$new_data['minimum_amount'] = sanitize_text_field( $data['minimum_amount'] );
			$new_data['maximum_amount'] = sanitize_text_field( $data['maximum_amount'] );
			$new_data['additional_notes']  = wp_kses_post( $data['additional_notes'] );
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
