<?php
if ( ! defined( 'myCRED_VERSION' ) ) exit;

/**
 * Event Espresso Payment Gateway
 * @since 1.2
 * @version 1.2
 */
if ( ! class_exists( 'myCRED_Espresso_Gateway' ) ) :
	class myCRED_Espresso_Gateway {

		public $label       = '';
		public $core        = NULL;
		public $mycred_type = MYCRED_DEFAULT_TYPE_KEY;
		public $prefs       = array();
		public $update      = false;

		/**
		 * Construct
		 */
		function __construct() {

			$defaults = array(
				'labels'   => array(
					'gateway'  => $this->label,
					'payment'  => $this->label . ' ' . __( 'Payments', 'mycred' ),
					'button'   => __( 'Pay Now', 'mycred' )
				),
				'type'     => MYCRED_DEFAULT_TYPE_KEY,
				'rate'     => 100,
				'share'    => 0,
				'log'      => __( 'Payment for Event Registration', 'mycred' ),
				'messages' => array(
					'solvent'   => 'Click "Pay Now" to pay using your %plural%.',
					'insolvent' => 'Unfortunately you do not have enough %plural% to pay for this event.',
					'visitors'  => 'Payments using %_plural% is only available for registered members.'
				)
			);

			// Settings
			$settings          = get_option( 'mycred_espresso_gateway_prefs' );
			$this->prefs       = mycred_apply_defaults( $defaults, $settings );
			$this->mycred_type = $this->prefs['type'];
			$this->core        = mycred( $this->mycred_type );

		}

		/**
		 * Load Gateway
		 * Hook in on init and setup our gateway.
		 * @since 1.2
		 * @version 1.0
		 */
		public function load() {

			add_action( 'init', array( $this, 'gateway_setup' ) );

		}

		/**
		 * Gateway Active
		 * Checks to see if a given gateway is active.
		 * @since 1.2
		 * @version 1.0
		 */
		public function gateway_active( $id = 'mycred' ) {

			global $active_gateways;

			if ( ! isset( $active_gateways ) || ! is_array( $active_gateways ) )
				$active_gateways = get_option( 'event_espresso_active_gateways', array() );

			if ( array_key_exists( $id, $active_gateways ) ) return true;
			return false;

		}

		/**
		 * Gateway Setup
		 * @since 1.2
		 * @version 1.0
		 */
		public function gateway_setup() {

			// Capture Settings Update
			if ( isset( $_REQUEST['mycred-gateway-action'] ) && isset( $_REQUEST['mycred-gateway-token'] ) )
				$this->update_settings();

			add_filter( 'action_hook_espresso_gateway_formal_name',      array( $this, 'formal_name' ) );
			add_filter( 'action_hook_espresso_gateway_payment_type',     array( $this, 'paymenttype_name' ) );
			add_action( 'action_hook_espresso_display_gateway_settings', array( $this, 'gateway_settings_page' ), 11 );

			// Make sure gateway is enabled
			if ( $this->gateway_active() ) {

				// Hook into Payment Page
				add_action( 'action_hook_espresso_display_onsite_payment_gateway', array( $this, 'payment_page' ) );

				// Capture myCRED Payment Requests
				if ( $this->is_payment() ) {
					add_filter( 'filter_hook_espresso_transactions_get_attendee_id', array( $this, 'set_attendee_id' ) );
					add_filter( 'filter_hook_espresso_thank_you_get_payment_data',   array( $this, 'process_payment' ) );
				}

				add_action( 'action_hook_espresso_display_onsite_payment_header', 'espresso_display_onsite_payment_header' );
				add_action( 'action_hook_espresso_display_onsite_payment_footer', 'espresso_display_onsite_payment_footer' );

			}

		}

		/**
		 * Is Payment Request?
		 * @since 1.2
		 * @version 1.0
		 */
		public function is_payment() {

			if (
				( isset( $_REQUEST['payment_type'] ) && $_REQUEST['payment_type'] == 'mycred' ) &&
				( isset( $_REQUEST['token'] ) && wp_verify_nonce( $_REQUEST['token'], 'pay-with-mycred' ) ) ) return true;

			return false;

		}

		/**
		 * Formal Name
		 * @since 1.2
		 * @version 1.0
		 */
		public function formal_name( $gateway_formal_names ) {

			$gateway_formal_names['mycred'] = $this->prefs['labels']['gateway'];

			return $gateway_formal_names;

		}

		/**
		 * Payment Type
		 * @since 1.2
		 * @version 1.0
		 */
		public function paymenttype_name( $gateway_payment_types ) {

			$gateway_payment_types['mycred'] = $this->prefs['labels']['payment'];

			return $gateway_payment_types;

		}

		/**
		 * Set Attendee ID
		 * @since 1.2
		 * @version 1.0
		 */
		public function set_attendee_id( $attendee_id ) {

			if ( isset( $_REQUEST['id'] ) )
				$attendee_id = $_REQUEST['id'];

			return $attendee_id;

		}

		/**
		 * Process Payment
		 * @since 1.2
		 * @version 1.2
		 */
		public function process_payment( $payment_data ) {

			if ( ! is_user_logged_in() ) return $payment_data;

			// Security
			if ( ! isset( $_REQUEST['token'] ) || ! wp_verify_nonce( $_REQUEST['token'], 'pay-with-mycred' ) ) return $payment_data;

			// Let others play
			do_action( 'mycred_espresso_process', $payment_data, $this->prefs, $this->core );

			// Check if this event does not accept myCRED payments
			if ( isset( $event_meta['mycred_no_sale'] ) ) return;

			$user_id        = get_current_user_id();

			// Make sure this is unique
			if ( $this->core->has_entry( 'event_payment', $payment_data['event_id'], $user_id, $payment_data['registration_id'] ) ) return $payment_data;

			$balance        = $this->core->get_users_balance( $user_id, $this->mycred_type );
			$event_cost     = $this->prefs['rate']*$payment_data['total_cost'];
			$after_purchase = $balance-$event_cost;

			// This should never happen
			if ( $after_purchase < 0 ) return $payment_data;

			$entry          = $this->prefs['log'];

			// Deduct
			$this->core->add_creds(
				'event_payment',
				$user_id,
				0-$event_cost,
				$entry,
				$payment_data['event_id'],
				$payment_data['registration_id'],
				$this->mycred_type
			);

			// Update Payment Data
			$payment_data['payment_status'] = 'Completed';
			$payment_data['txn_type']       = $this->prefs['labels']['payment'];
			$payment_data['txn_id']         = $payment_data['attendee_session'];
			$payment_data['txn_details']    = $this->core->template_tags_general( $entry );

			// Let others play
			do_action( 'mycred_espresso_processed', $payment_data, $this->prefs, $this->core );

			// Profit sharing
			if ( $this->prefs['share'] != 0 ) {

				$event_post = mycred_get_post( (int) $payment_data['event_id'] );

				if ( $event_post !== NULL ) {

					$share = ( $this->prefs['share']/100 ) * $price;

					$this->core->add_creds(
						'event_sale',
						$event_post->post_author,
						$share,
						$this->prefs['log'],
						$payment_data['event_id'],
						$payment_data['registration_id'],
						$this->mycred_type
					);

				}

			}

			return $payment_data;

		}

		/**
		 * Payment Page
		 * @since 1.2
		 * @version 1.1.1
		 */
		public function payment_page( $payment_data ) {

			extract( $payment_data );

			// Check if this event does not accept myCRED payments
			if ( isset( $event_meta['mycred_no_sale'] ) ) return;

			global $org_options;

			$member = $solvent = $user_id = false;

			if ( is_user_logged_in() ) {

				$member         = true;
				$user_id        = get_current_user_id();
				$balance        = $this->core->get_users_balance( $user_id, $this->mycred_type );

				// Calculate Cost
				$event_cost     = $this->prefs['rate']*$event_cost;
				$after_purchase = $balance-$event_cost;

				if ( $after_purchase >= 0 )
					$solvent = true;

				$args = array(
					'page_id'      => $org_options['return_url'],
					'r_id'         => $registration_id,
					'id'           => $attendee_id,
					'payment_type' => 'mycred',
					'token'        => wp_create_nonce( 'pay-with-mycred' )
				);
				$finalize_link = add_query_arg( $args, home_url() );

			}

?>
<div id="mycred-payment-option-dv" class="payment-option-dv">
	<a id="mycred-payment-option-lnk" class="payment-option-lnk algn-vrt display-the-hidden" rel="mycred-payment-option-form" style="display: table-cell">
		<div class="vrt-cell">
			<div><?php echo $this->prefs['labels']['gateway']; ?></div>
		</div>
	</a><br/>
	<div id="mycred-payment-option-form-dv" class="hide-if-js">

	<?php if ( $member && $solvent ) : ?>

		<?php if ( trim( $this->prefs['messages']['solvent'] ) != '' ) : ?>

		<p><?php echo $this->core->template_tags_general( $this->prefs['messages']['solvent'] ); ?></p>

		<?php endif; ?>

		<div class="event-display-boxes">
			<h4 id="mycred_title" class="payment_type_title section-heading"><?php echo $this->prefs['labels']['payment']; ?></h4>
			<table style="width:100%;">
				<tr>
					<td class="info"><?php _e( 'Current Balance', 'mycred' ); ?></td>
					<td class="amount"><?php echo $this->core->format_creds( $balance ); ?></td>
				</tr>
				<tr>
					<td class="info"><?php _e( 'Total Cost', 'mycred' ); ?></td>
					<td class="amount"><?php echo $this->core->format_creds( $event_cost ); ?></td>
				</tr>
				<tr>
					<td class="info"><?php _e( 'Balance After Purchase', 'mycred' ); ?></td>
					<td class="amount"><?php echo $this->core->format_creds( $after_purchase ); ?></td>
				</tr>
			</table>
			<p><a href="<?php echo esc_url( $finalize_link ); ?>" class="button button-large button-primary" style="float:right;"><?php echo $this->prefs['labels']['button']; ?></a></p>
		</div>

	<?php elseif ( $member && ! $solvent ) : ?>

		<div class="event_espresso_attention event-messages ui-state-highlight">
			<span class="ui-icon ui-icon-alert"></span>
			<p><?php echo $this->core->template_tags_general( $this->prefs['messages']['insolvent'] ); ?></p>
		</div>
		<div class="event-display-boxes">
			<h4 id="mycred_title" class="payment_type_title section-heading"><?php echo $this->prefs['labels']['payment']; ?></h4>
			<table style="width:100%;">
				<tr class="current">
					<td class="info"><?php _e( 'Current Balance', 'mycred' ); ?></td>
					<td class="amount"><?php echo $this->core->format_creds( $balance ); ?></td>
				</tr>
				<tr class="cost">
					<td class="info"><?php _e( 'Total Cost', 'mycred' ); ?></td>
					<td class="amount"><?php echo $this->core->format_creds( $event_cost ); ?></td>
				</tr>
				<tr class="after-purchase">
					<td class="info"><?php _e( 'Balance After Purchase', 'mycred' ); ?></td>
					<td class="amount" style="color:red;"><?php echo $this->core->format_creds( $after_purchase ); ?></td>
				</tr>
			</table>
		</div>

<?php else : ?>

		<div class="event_espresso_attention event-messages ui-state-highlight">
			<span class="ui-icon ui-icon-alert"></span>
			<p><?php echo $this->core->template_tags_general( $this->prefs['messages']['visitors'] ); ?></p>
		</div>

<?php endif; ?>

	</div>
</div>
<?php

		}

		/**
		 * Gateway Settings Page
		 * @since 1.2
		 * @version 1.0
		 */
		public function gateway_settings_page( ) {

			global $espresso_premium, $active_gateways, $org_options;

			if ( ! $espresso_premium )
				return;

			// activate
			if ( ! empty( $_REQUEST['activate_mycred_payment'] ) ) {
				$active_gateways['mycred'] = myCRED_GATE_CART_DIR . 'mycred-eventespresso3.php';
				update_option( 'event_espresso_active_gateways', $active_gateways );
			}

			$activate_url    = admin_url( 'admin.php?page=payment_gateways&activate_mycred_payment=true' );
			$activate_text   = sprintf( __( 'Activate %s', 'mycred' ), $this->label );

			// deactivate
			if ( ! empty( $_REQUEST['deactivate_check_payment'] ) ) {
				unset( $active_gateways['mycred'] );
				update_option( 'event_espresso_active_gateways', $active_gateways );
			}

			$deactivate_url  = admin_url( 'admin.php?page=payment_gateways&deactivate_mycred_payment=true' );
			$deactivate_text = sprintf( __( 'Deactivate %s', 'mycred' ), $this->label );

			//Open or close the postbox div
			$postbox_style   = 'closed';
			if ( empty( $_REQUEST['deactivate_mycred_payment'] ) && ( ! empty( $_REQUEST['deactivate_mycred_payment'] ) || array_key_exists( 'mycred', $active_gateways ) ) )
				$postbox_style = '';

?>
<p id="mycred-gate">&nbsp;</p>
<div class="metabox-holder">
	<div class="postbox <?php echo $postbox_style; ?>">
		<div title="Click to toggle" class="handlediv"><br /></div>
		<h3 class="hndle">
			<?php echo $this->label . ' ' . __( 'Gateway Settings', 'mycred' ); ?>
		</h3>
		<div class="inside">
			<div class="padding">
				<ul>
<?php

			if ( array_key_exists( 'mycred', $active_gateways ) ) {

				echo '<li id="deactivate_check" style="width:30%;" onclick="location.href=\'' . $deactivate_url . '\';" class="red_alert pointer"><strong>' . $deactivate_text . '</strong></li>';
				$this->gateway_settings();

			}
			else {

				echo '<li id="activate_check" style="width:30%;" onclick="location.href=\'' . $activate_url . '\';" class="green_alert pointer"><strong>' . $activate_text . '</strong></li>';

			}
			echo '</ul>';

?>
			</div>
		</div>
	</div>
</div>
<?php

		}

		/**
		 * Gateway Settings
		 * Included first when the gateway is activated.
		 * @since 1.2
		 * @version 1.2
		 */
		public function gateway_settings() {

			global $org_options;

			$exchange_message = sprintf(
				__( 'How many %s is 1 %s worth?', 'mycred' ),
				$this->core->plural(),
				$org_options['currency_symbol']
			);

			$mycred_types = mycred_get_types();

?>
<?php if ( $this->update ) : ?>
<h2 style="color: green;"><?php _e( 'Settings Updated', 'mycred' ); ?></h2>
<?php endif; ?>
<form method="post" action="<?php echo $_SERVER['REQUEST_URI'] ?>#mycred-gate">

	<?php do_action( 'mycred_espresso_before_prefs' ); ?>

	<table width="99%" border="0" cellspacing="5" cellpadding="5">
		<tr>
			<td valign="top">
				<h4><?php _e( 'Labels', 'mycred' ); ?></h4>
				<ul>
					<li>
						<label for="mycred-prefs-gateway-labels"><?php _e( 'Gateway Title', 'mycred' ); ?></label>
						<input type="text" name="mycred_prefs[labels][gateway]" id="mycred-prefs-gateway-labels" size="30" value="<?php echo $this->prefs['labels']['gateway']; ?>" /><br />
						<span class="description"><?php _e( 'Title to show on Payment page', 'mycred' ); ?>.</span>
					</li>
					<li>
						<label for="mycred-prefs-payment-labels"><?php _e( 'Payment Type', 'mycred' ); ?></label>
						<input type="text" name="mycred_prefs[labels][payment]" id="mycred-prefs-payment-labels" size="30" value="<?php echo $this->prefs['labels']['payment']; ?>" /><br />
						<span class="description"><?php _e( 'Title to show on receipts and logs', 'mycred' ); ?>.</span>
					</li>
					<li>
						<label for="mycred-prefs-button-labels"><?php _e( 'Button Label', 'mycred' ); ?></label>
						<input type="text" name="mycred_prefs[labels][button]" id="mycred-prefs-button-labels" size="30" value="<?php echo $this->prefs['labels']['button']; ?>" /><br />
						<span class="description"><?php _e( 'Pay Button', 'mycred' ); ?></span>
					</li>
				</ul>

				<?php if ( count( $mycred_types ) > 1 ) : ?>

				<ul>
					<li>
						<label for="mycred-prefs-payment-type"><?php _e( 'Point Type', 'mycred' ); ?></label>
						<?php mycred_types_select_from_dropdown( 'mycred_prefs[type]', 'mycred-prefs-payment-type', $this->prefs['type'] ); ?>

					</li>
				</ul>

				<?php else : ?>

				<input type="hidden" name="mycred_prefs[type]" id="mycred-prefs-payment-type" value="mycred_default" />

				<?php endif; ?>

				<h4><?php _e( 'Price', 'mycred' ); ?></h4>
				<ul>
					<li id="mycred-event-exchange-box">
						<label for="mycred-prefs-price-x-rate"><?php _e( 'Exchange Rate', 'mycred' ); ?></label>
						<input type="text" name="mycred_prefs[rate]" id="mycred-prefs-price-x-rate" size="30" value="<?php echo $this->prefs['rate']; ?>" /><br />
						<span class="description"><?php echo $exchange_message; ?></span>
					</li>
					<li>
						<p><strong><?php _e( 'Important!', 'mycred' ); ?></strong></p>
						<ol>
							<li><?php _e( 'You can disable purchases using this gateway by adding a custom Event Meta: <code>mycred_no_sale</code>', 'mycred' ); ?></li>
							<li><?php _e( 'Users must be logged in to use this gateway!', 'mycred' ); ?></li>
						</ol>
					</li>
					<li id="mycred-event-profit-sharing">
						<label for="mycred-prefs-profit-share"><?php _e( 'Profit Sharing', 'mycred' ); ?></label>
						<input type="text" name="mycred_prefs[share]" id="mycred-prefs-profit-share" size="5" value="<?php echo $this->prefs['share']; ?>" /> %<br />
						<span class="description"><?php _e( 'Option to share sales with the product owner. Use zero to disable.', 'mycred' ); ?></span>
					</li>
				</ul>
				<h4><?php _e( 'Log', 'mycred' ); ?></h4>
				<ul>
					<li>
						<label for="mycred-prefs-log"><?php _e( 'Log Entry', 'mycred' ); ?></label>
						<input type="text" name="mycred_prefs[log]" id="mycred-prefs-log" size="30" value="<?php echo $this->prefs['log']; ?>" /><br />
						<span class="description"><?php echo $this->core->available_template_tags( array( 'general' ) ); ?></span>
					</li>
				</ul>
			</td>
			<td valign="top">
				<h4><?php _e( 'Templates', 'mycred' ); ?></h4>
				<ul>
					<li>
						<label for="mycred-prefs-message-solvent"><?php _e( 'Solvent users', 'mycred' ); ?></label>
						<textarea name="mycred_prefs[messages][solvent]" id="mycred-prefs-message-solvent" style="width: 90%; max-width: 90%; min-height: 90px;"><?php echo stripslashes( $this->prefs['messages']['solvent'] ); ?></textarea><br />
						<span class="description"><?php _e( 'Message to show users on the payment page before they are charged. Leave empty to hide.', 'mycred' ); ?><br /><?php echo $this->core->available_template_tags( array( 'general' ) ); ?></span>
					</li>
					<li>
						<label for="mycred-prefs-message-insolvent"><?php _e( 'Insolvent users', 'mycred' ); ?></label>
						<textarea name="mycred_prefs[messages][insolvent]" id="mycred-prefs-message-solvent" style="width: 90%; max-width: 90%; min-height: 90px;"><?php echo stripslashes( $this->prefs['messages']['insolvent'] ); ?></textarea><br />
						<span class="description"><?php _e( 'Message to show users who do not have enough points to pay.', 'mycred' ); ?><br /><?php echo $this->core->available_template_tags( array( 'general' ) ); ?></span>
					</li>
					<li>
						<label for="mycred-prefs-message-insolvent"><?php _e( 'Visitors', 'mycred' ); ?></label>
						<textarea name="mycred_prefs[messages][visitors]" id="mycred-prefs-message-visitors" style="width: 90%; max-width: 90%; min-height: 90px;"><?php echo stripslashes( $this->prefs['messages']['visitors'] ); ?></textarea><br />
						<span class="description"><?php _e( 'Message to show visitors (users not logged in) on the payment page.', 'mycred' ); ?><br /><?php echo $this->core->available_template_tags( array( 'general' ) ); ?></span>
					</li>
				</ul>
			</td>
		</tr>
	</table>

	<?php do_action( 'mycred_espresso_after_prefs' ); ?>

	<input type="hidden" name="mycred-gateway-action" value="update-settings" />
	<input type="hidden" name="mycred-gateway-token" value="<?php echo wp_create_nonce( 'mycred-espresso-update' ); ?>" />
	<p><input class="button-primary" type="submit" name="Submit" value="<?php _e( 'Update Settings', 'mycred' ); ?>" /></p>
</form>
<?php

		}

		/**
		 * Update Settings
		 * @since 1.2
		 * @version 1.2
		 */
		public function update_settings() {

			// Apply Whitelabeling
			$this->label = mycred_label();

			// Security
			if ( ! wp_verify_nonce( $_REQUEST['mycred-gateway-token'], 'mycred-espresso-update' ) ) return;
			if ( ! $this->core->user_is_point_admin() ) return;

			// Prep
			$new_settings = array();
			$post         = $_POST['mycred_prefs'];

			if ( ! is_array( $post ) || empty( $post ) ) return;

			// Labels
			$new_settings['labels']['gateway'] = strip_tags( $post['labels']['gateway'], '<strong><em><span>' );
			$new_settings['labels']['payment'] = strip_tags( $post['labels']['payment'], '<strong><em><span>' );
			$new_settings['labels']['button']  = sanitize_text_field( $post['labels']['button'] );

			// Point Type
			$new_settings['type']  = sanitize_text_field( $post['type'] );

			// Exchange Rate
			$new_settings['rate']  = sanitize_text_field( $post['rate'] );
			
			// Profit Share
			$new_settings['share'] = abs( $post['share'] );
			
			// Log
			$new_settings['log']   = sanitize_text_field( $post['log'] );
			
			// Messages
			$new_settings['messages']['solvent']   = sanitize_text_field( stripslashes( $post['messages']['solvent'] ) );
			$new_settings['messages']['insolvent'] = sanitize_text_field( stripslashes( $post['messages']['insolvent'] ) );
			$new_settings['messages']['visitors']  = sanitize_text_field( stripslashes( $post['messages']['visitors'] ) );

			// Let others play
			$new_settings = apply_filters( 'mycred_espresso_save_pref', $new_settings );

			// Save new settings
			$current     = $this->prefs;
			$this->prefs = mycred_apply_defaults( $current, $new_settings );
			update_option( 'mycred_espresso_gateway_prefs', $this->prefs );

			// Flag update
			$this->update = true;

		}

	}
endif;
