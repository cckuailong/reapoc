<?php
if ( ! defined( 'myCRED_VERSION' ) ) exit;

/**
 * WooCommerce Payment Gateway
 * Custom Payment Gateway for WooCommerce.
 * @see http://docs.woothemes.com/document/payment-gateway-api/
 * @since 0.1
 * @version 1.5
 */
if ( ! function_exists( 'mycred_init_woo_gateway' ) ) :
	function mycred_init_woo_gateway() {

		if ( ! class_exists( 'WC_Payment_Gateway' ) || class_exists( 'WC_Gateway_myCRED' ) ) return;

		class WC_Gateway_myCRED extends WC_Payment_Gateway {

			public $mycred;

			/**
			 * Constructor
			 */
			public function __construct() {

				$this->id                        = 'mycred';
				$this->icon                      = '';
				$this->has_fields                = true;
				$this->method_title              = mycred_label();
				$this->method_description        = __( 'Let users pay using points.', 'mycred' );

				$gateway_suppots = array(
					'products',
					'refunds'
				);

				$this->supports = apply_filters( 'mycred_woocommerce_gateway_supports', $gateway_suppots );

				if ( ! $this->use_exchange() )
					$this->mycred_type = get_woocommerce_currency();

				else {
					$this->mycred_type = $this->get_option( 'point_type' );
					if ( ! mycred_point_type_exists( $this->mycred_type ) )
						$this->mycred_type = MYCRED_DEFAULT_TYPE_KEY;
				}

				$this->mycred                    = mycred( $this->mycred_type );

				// Load the settings.
				$this->init_form_fields();
				$this->init_settings();

				// Define user set variables
				$this->title                     = $this->get_option( 'title' );
				$this->description               = $this->get_option( 'description' );

				if ( $this->use_exchange() )
					$exchange_rate = (float) $this->get_option( 'exchange_rate' );
				else
					$exchange_rate = 1;

				if ( ! is_numeric( $exchange_rate ) )
					$exchange_rate = 1;

				$this->exchange_rate             = $exchange_rate;
				$this->log_template              = $this->get_option( 'log_template' );
				$this->log_template_refund       = $this->get_option( 'log_template_refund' );
				$this->profit_sharing_refund_log = $this->get_option( 'profit_sharing_refund_log' );

				$this->show_total                = $this->get_option( 'show_total' );
				$this->total_label               = $this->get_option( 'total_label' );
				$this->balance_format            = $this->get_option( 'balance_format' );

				$this->profit_sharing_percent    = $this->get_option( 'profit_sharing_percent' );
				$this->profit_sharing_log        = $this->get_option( 'profit_sharing_log' );

				// Actions
				add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
				add_action( 'woocommerce_thankyou_mycred',                              array( $this, 'thankyou_page' ) );

			}

			/**
			 * Initialise Gateway Settings Form Fields
			 * @since 0.1
			 * @version 1.4.1
			 */
			function init_form_fields() {

				// Fields
				$fields['enabled']             = array(
					'title'   => __( 'Enable/Disable', 'mycred' ),
					'type'    => 'checkbox',
					'label'   => sprintf( __( 'Enable %s Payment', 'mycred' ), $this->method_title ),
					'default' => 'no',
					'description' => __( 'Users who are not logged in or excluded from using myCRED will not have access to this gateway!', 'mycred' )
				);
				$fields['title']               = array(
					'title'       => __( 'Title', 'mycred' ),
					'type'        => 'text',
					'description' => __( 'Title to show for this payment option.', 'mycred' ),
					'default'     => __( 'Pay with myCRED', 'mycred' ),
					'desc_tip'    => true
				);
				$fields['description']         = array(
					'title'       => __( 'Customer Message', 'mycred' ),
					'type'        => 'textarea',
					'default'     => $this->mycred->template_tags_general( 'Deduct the amount from your %_plural% balance.' )
				);
				$fields['log_template']        = array(
					'title'       => __( 'Log Template', 'mycred' ),
					'type'        => 'text',
					'description' => $this->mycred->available_template_tags( array( 'general' ), '%order_id%, %order_link%' ),
					'default'     => 'Payment for Order: #%order_id%'
				);
				$fields['log_template_refund'] = array(
					'title'       => __( 'Refund Log Template', 'mycred' ),
					'type'        => 'text',
					'description' => $this->mycred->available_template_tags( array( 'general' ), '%order_id%, %reason%' ),
					'default'     => 'Payment refund for order #%order_id% Reason: %reason%'
				);

				// Only add exchange rate if the currecy is not set to mycred
				if ( $this->use_exchange() ) {

					$mycred_types = mycred_get_types();
					$fields['point_type']      = array(
						'title'       => __( 'Point Type', 'mycred' ),
						'type'        => 'select',
						'label'       => __( 'Select the point type users can use to pay.', 'mycred' ),
						'options'     => $mycred_types,
						'default'     => MYCRED_DEFAULT_TYPE_KEY
					);

					$exchange_desc = $this->mycred->template_tags_general( __( 'How much is 1 %_singular% worth in %currency%?', 'mycred' ) );
					$exchange_desc = str_replace( '%currency%', get_woocommerce_currency(), $exchange_desc );

					$fields['exchange_rate']   = array(
						'title'       => __( 'Exchange Rate', 'mycred' ),
						'type'        => 'text',
						'description' => $exchange_desc,
						'default'     => 1,
						'desc_tip'    => true
					);
					$fields['show_total']      = array(
						'title'       => __( 'Show Total', 'mycred' ),
						'type'        => 'select',
						'label'       => $this->mycred->template_tags_general( __( 'Show the final price in %_plural% .', 'mycred' ) ),
						'options'     => array(
							''           => __( 'Do not show', 'mycred' ),
							'cart'       => __( 'Show in Cart', 'mycred' ),
							'checkout'   => __( 'Show on Checkout Page', 'mycred' ),
							'all'        => __( 'Show in Cart and on Checkout Page', 'mycred' )
						),
						'default'     => ''
					);
					$fields['total_label']     = array(
						'title'       => __( 'Label', 'mycred' ),
						'type'        => 'text',
						'default'     => $this->mycred->template_tags_general( __( 'Order Total in %_plural%', 'mycred' ) ),
						'desc_tip'    => true
					);

				}

				else {

					$fields['point_type']      = array(
						'title'       => __( 'Point Type', 'mycred' ),
						'type'        => 'currency',
						'description' => $this->mycred->plural(),
						'default'     => $this->mycred_type
					);

				}

				$fields['balance_format']            = array(
					'title'       => __( 'Balance Label', 'mycred' ),
					'type'        => 'text',
					'description' => __( 'The label to use when presenting a user their balance on the checkout / cart pages. Leave empty to hide.', 'mycred' ),
					'default'     => 'Your Balance',
					'desc_tip'    => false
				);
				$fields['profit_sharing_percent']    = array(
					'title'       => __( 'Profit Sharing', 'mycred' ),
					'type'        => 'text',
					'description' => __( 'Option to share sales with the product owner. Use zero to disable.', 'mycred' ),
					'default'     => 0,
					'desc_tip'    => true
				);
				$fields['profit_sharing_log']        = array(
					'title'       => __( 'Log Template', 'mycred' ),
					'type'        => 'text',
					'description' => __( 'Log entry template for profit sharing.', 'mycred' ) . ' ' . $this->mycred->available_template_tags( array( 'general', 'post' ) ),
					'default'     => 'Sale of %post_title%'
				);
				$fields['profit_sharing_refund_log'] = array(
					'title'       => __( 'Refund Log Template', 'mycred' ),
					'type'        => 'text',
					'description' => __( 'Log entry template for refunds of profit shares.', 'mycred' ) . ' ' . $this->mycred->available_template_tags( array( 'general', 'post' ) ),
					'default'     => 'Refund for order #%order_id%'
				);
				
				$this->form_fields = apply_filters( 'mycred_woo_fields', $fields, $this );

			}

			/**
			 * Generate Text Input HTML.
			 * @since 1.7.6
			 * @version 1.0
			 */
			public function generate_text_html( $key, $data ) {

				$field_key = $this->get_field_key( $key );
				$defaults  = array(
					'title'             => '',
					'disabled'          => false,
					'class'             => '',
					'css'               => '',
					'placeholder'       => '',
					'type'              => 'text',
					'desc_tip'          => false,
					'description'       => '',
					'custom_attributes' => array(),
				);

				$data     = wp_parse_args( $data, $defaults );
				$currency = get_woocommerce_currency();

				ob_start();
?>
<tr valign="top">
	<th scope="row" class="titledesc">
		<label for="<?php echo esc_attr( $field_key ); ?>"><?php echo wp_kses_post( $data['title'] ); ?></label>
		<?php echo $this->get_tooltip_html( $data ); ?>
	</th>
	<td class="forminp">
		<?php if ( $data['type'] == 'currency' ) : $mycred = mycred( $currency ); ?>
		<input type="hidden" name="<?php echo esc_attr( $field_key ); ?>" id="<?php echo esc_attr( $field_key ); ?>" value="<?php echo get_woocommerce_currency(); ?>" />
		<p><?php echo $mycred->plural(); ?></p>
		<?php else : ?>
		<fieldset>
			<legend class="screen-reader-text"><span><?php echo wp_kses_post( $data['title'] ); ?></span></legend>
			<input class="input-text regular-input <?php echo esc_attr( $data['class'] ); ?>" type="<?php echo esc_attr( $data['type'] ); ?>" name="<?php echo esc_attr( $field_key ); ?>" id="<?php echo esc_attr( $field_key ); ?>" style="<?php echo esc_attr( $data['css'] ); ?>" value="<?php echo esc_attr( $this->get_option( $key ) ); ?>" placeholder="<?php echo esc_attr( $data['placeholder'] ); ?>" <?php disabled( $data['disabled'], true ); ?> <?php echo $this->get_custom_attribute_html( $data ); ?> />
			<?php echo $this->get_description_html( $data ); ?>
		</fieldset>
		<?php endif; ?>
	</td>
</tr>
<?php

				return ob_get_clean();

			}

			/**
			 * Use Exchange
			 * Checks to see if exchange is needed.
			 * @since 0.1
			 * @version 1.0
			 */
			function use_exchange() {

				$currency = get_woocommerce_currency();
				if ( mycred_point_type_exists( $currency ) || $currency == 'MYC' ) return false;
				return true;

			}

			/**
			 * Admin Panel Options
			 * @since 0.1
			 * @version 1.1
			 */
			public function admin_options() {

?>
		<h3><?php printf( __( '%s Payment', 'mycred' ), mycred_label() ); ?></h3>
		<table class="form-table">
<?php

				// Generate the HTML For the settings form.
				$this->generate_settings_html();

?>
		</table>
<?php

			}

			/**
			 * Process Payment
			 * @since 0.1
			 * @version 1.4.3
			 */
			function process_payment( $order_id ) {

				global $woocommerce;

				// Make sure we are still logged in
				if ( ! is_user_logged_in() ) {
					wc_add_notice( $this->mycred->template_tags_general( __( 'You must be logged in to pay with %_plural%', 'mycred' ) ), 'error' );
					return;
				}

				$user_id = apply_filters('mycred_woo_gateway_user_id', get_current_user_id(), $order_id);

				// Make sure we have not been excluded
				if ( $this->mycred->exclude_user( $user_id ) ) {
					wc_add_notice( $this->mycred->template_tags_general( __( 'You can not use this gateway. Please try a different payment option.', 'mycred' ) ), 'error' );
					return;
				}

				// Grab Order
				$order       = wc_get_order( $order_id );

				$order_total = ( version_compare( $woocommerce->version, '3.0', '>=' ) ) ? $order->get_total() : $order->order_total;

				// Cost
				$cost        = $order_total;
				if ( $this->use_exchange() )
					$cost = $this->mycred->number( ( $order_total / $this->exchange_rate ) );

				$cost        = apply_filters( 'mycred_woo_order_cost', $cost, $order, false, $this );

				// Check funds
				if ( $this->mycred->get_users_balance( $user_id, $this->mycred_type ) < $cost ) {
					$message = apply_filters( 'mycred_woo_error_insufficient_funds', __( 'Insufficient funds.', 'mycred' ) );
					wc_add_notice( $message, 'error' );
					return;
				}

				// Let others decline a store order
				$decline     = apply_filters( 'mycred_decline_store_purchase', false, $order, $this );
				if ( $decline !== false ) {
					wc_add_notice( $decline, 'error' );
					return;
				}

				// Charge
				$this->mycred->add_creds(
					'woocommerce_payment',
					$user_id,
					0 - $cost,
					$this->log_template,
					$order_id,
					array( 'ref_type' => 'post' ),
					$this->mycred_type
				);

				$order->payment_complete();

				// Profit Sharing
				if ( $this->profit_sharing_percent > 0 ) {

					// Get Items
					$items = $order->get_items();

					// Loop though items
					foreach ( $items as $item ) {

						// Get Product
						$product    = mycred_get_post( (int) $item['product_id'] );

						// Continue if product has just been deleted or owner is buyer
						if ( $product === NULL || $product->post_author == $user_id ) continue;

						// Calculate Share
						$percentage = apply_filters( 'mycred_woo_profit_share', $this->profit_sharing_percent, $order, $product, $this );
						if ( $percentage == 0 ) continue;

						$share      = ( $percentage / 100 ) * $item['line_total'];

						// Payout
						$this->mycred->add_creds(
							'store_sale',
							$product->post_author,
							$this->mycred->number( $share ),
							$this->profit_sharing_log,
							$product->ID,
							array( 'ref_type' => 'post' ),
							$this->mycred_type
						);

					}

				}

				// Let others play
				do_action( 'mycred_paid_for_woo', $order, $user_id, $this );

				// Return the good news
				return array(
					'result'   => 'success',
					'redirect' => $this->get_return_url( $order )
				);

			}

			/**
			 * Process Refunds
			 * @since 1.5.4
			 * @version 1.0.4
			 */
			public function process_refund( $order_id, $amount = null, $reason = '' ) {

				$order       = wc_get_order( $order_id );

				$order_total = ( version_compare( $woocommerce->version, '3.0', '>=' ) ) ? $order->get_total() : $order->order_total;
				$user_id     = ( version_compare( $woocommerce->version, '3.0', '>=' ) ) ? $order->get_user_id() : $order->user_id;

				if ( $amount === NULL )
					$amount = $order_total;

				$refund      = $amount;
				if ( $this->use_exchange() )
					$refund = $this->mycred->number( ( $refund / $this->exchange_rate ) );

				$this->mycred->add_creds(
					'woocommerce_refund',
					$user_id,
					$refund,
					$this->log_template_refund,
					$order_id,
					array( 'ref_type' => 'post', 'reason' => $reason ),
					$this->mycred_type
				);

				$order->add_order_note( sprintf( _x( 'Refunded %s', '%s = Point amount formatted', 'mycred' ), $this->mycred->format_creds( $refund ) ) );

				// Profit Sharing
				if ( $this->profit_sharing_percent > 0 ) {

					// Get Items
					$items = $order->get_items();

					// Loop though items
					foreach ( $items as $item ) {

						// Get Product
						$product = mycred_get_post( (int) $item['product_id'] );

						// Continue if product has just been deleted
						if ( $product === NULL ) continue;

						// Calculate Share
						$percentage = apply_filters( 'mycred_woo_profit_share_refund', $this->profit_sharing_percent, $order, $product, $this );
						if ( $percentage == 0 ) continue;

						$share      = ( $percentage / 100 ) * $item['line_total'];

						// Payout
						$this->mycred->add_creds(
							'store_sale_refund',
							$product->post_author,
							0 - $share,
							$this->profit_sharing_refund_log,
							$product->ID,
							array( 'ref_type' => 'post', 'order_id' => $order_id ),
							$this->mycred_type
						);

					}

				}

				// Let others play
				do_action( 'mycred_refunded_for_woo', $order, $amount, $reason, $this );

				return true;

			}

			/**
			 * Thank You Page
			 * @since 0.1
			 * @version 1.0
			 */
			function thankyou_page() {

				echo apply_filters( 'mycred_woo_thank_you_message', '<p>' . __( 'Your account has successfully been charged.', 'mycred' ) . '</p>' );

			}

		}

	}
endif;
add_action( 'after_setup_theme', 'mycred_init_woo_gateway' );

/**
 * Register Gateway
 * @since 0.1
 * @version 1.0
 */
if ( ! function_exists( 'mycred_register_woo_gateway' ) ) :
	function mycred_register_woo_gateway( $methods ) {

		$methods[] = 'WC_Gateway_myCRED';
		return $methods;

	}
endif;
add_filter( 'woocommerce_payment_gateways', 'mycred_register_woo_gateway' );

/**
 * Available Gateways
 * "Removes" this gateway as a payment option if:
 * - User is not logged in
 * - User is excluded
 * - Users balance is too low
 *
 * @since 0.1
 * @since 2.3 Return existing gateways atleast
 * @version 1.4
 */
if ( ! function_exists( 'mycred_woo_available_gateways' ) ) :
	function mycred_woo_available_gateways( $gateways ) {

		if ( ! isset( $gateways['mycred'] ) ) return $gateways;

		// Easy override
		if ( defined( 'SHOW_MYCRED_IN_WOOCOMMERCE' ) && SHOW_MYCRED_IN_WOOCOMMERCE ) return $gateways;

		// Check if we are logged in
		if ( ! is_user_logged_in() ) {

			unset( $gateways['mycred'] );

			return $gateways;

		}

		$point_type = $gateways['mycred']->get_option( 'point_type' );
		if ( ! mycred_point_type_exists( $point_type ) )
			$point_type = MYCRED_DEFAULT_TYPE_KEY;

		// Get myCRED
		$mycred     = mycred( $point_type );
		$user_id    = get_current_user_id();

		// Check if we are excluded from myCRED usage
		if ( $mycred->exclude_user( $user_id ) ) {

			unset( $gateways['mycred'] );

			return $gateways;

		}

		global $woocommerce;

		// Calculate cost in CREDs
		$currency   = get_woocommerce_currency();

		//Return existing gateways atleast
		if( ! is_object( $woocommerce ) || empty( $woocommerce->cart ) )
		{
			unset( $gateways['mycred'] );

			return $gateways;
		}

		$cost       = $woocommerce->cart->total;
		if ( ! mycred_point_type_exists( $currency ) && $currency != 'MYC' )
			$cost = $mycred->number( ( $woocommerce->cart->total / $gateways['mycred']->get_option( 'exchange_rate' ) ) );

		$cost       = apply_filters( 'mycred_woo_order_cost', $cost, $woocommerce->cart, true, $mycred );

		// Check if we have enough points
		if ( $mycred->get_users_balance( $user_id, $point_type ) < $cost ) {
			$gateways['mycred']->enabled = 'no';
		}

		return $gateways;

	}
endif;
add_filter( 'woocommerce_available_payment_gateways', 'mycred_woo_available_gateways' );

/**
 * Add Currency
 * Inserts all registered point types as a currency in WooCommerce.
 * @since 0.1
 * @version 1.2
 */
if ( ! function_exists( 'mycred_woo_add_currency' ) ) :
	function mycred_woo_add_currency( $currencies ) {

		$point_types = mycred_get_types();

		if ( ! empty( $point_types ) ) {
			foreach ( $point_types as $type_id => $label ) {

				if ( $type_id == MYCRED_DEFAULT_TYPE_KEY )
					$type_id = 'MYC';

				$currencies[ $type_id ] = $label;

			}
		}

		return $currencies;

	}
endif;
add_filter( 'woocommerce_currencies', 'mycred_woo_add_currency' );

/**
 * Currency Symbol
 * Appends the myCRED prefix or suffix to the amount.
 * @since 0.1
 * @version 1.2.1
 */
if ( ! function_exists( 'mycred_woo_currency_symbol' ) ) :
	function mycred_woo_currency_symbol( $currency_symbols ) {

		$point_types = mycred_get_types();
		if ( ! empty( $point_types ) ) {
			foreach ( $point_types as $type_id => $label ) {

				$mycred = mycred( $type_id );
				$symbol = '';
				if ( ! empty( $mycred->after ) )
					$symbol = $mycred->after;
				elseif ( ! empty( $mycred->before ) )
					$symbol = $mycred->before;

				if ( $type_id == MYCRED_DEFAULT_TYPE_KEY )
					$type_id = 'MYC';

				$currency_symbols[ $type_id ] = $symbol;

			}
		}

		return $currency_symbols;

	}
endif;
add_filter( 'woocommerce_currency_symbols', 'mycred_woo_currency_symbol' );

/**
 * Log Entry: Payment
 * @since 0.1
 * @version 1.4
 */
if ( ! function_exists( 'mycred_woo_log_entry_payment' ) ) :
	function mycred_woo_log_entry_payment( $content, $log_entry ) {

		$order_id   = absint( $log_entry->ref_id );
		$order_link = '#' . $order_id;

		if ( function_exists( 'wc_get_order' ) ) {

			$order = wc_get_order( $order_id );

			if ( $order !== false && is_object( $order ) )
				$order_link = '<a href="' . esc_url( $order->get_view_order_url() ) . '">#' . $order_id . '</a>';

		}

		$content   = str_replace( '%order_id%',   $order_id, $content );
		$content   = str_replace( '%order_link%', $order_link, $content );

		return $content;

	}
endif;
add_filter( 'mycred_parse_log_entry_woocommerce_payment', 'mycred_woo_log_entry_payment', 90, 2 );

/**
 * Log Entry: Refund
 * @since 1.5.4
 * @version 1.0
 */
if ( ! function_exists( 'mycred_woo_log_entry_refunds' ) ) :
	function mycred_woo_log_entry_refunds( $content, $log_entry ) {

		$content = mycred_woo_log_entry_payment( $content, $log_entry );

		$data    = maybe_unserialize( $log_entry->data );
		$reason  = '-';
		if ( isset( $data['reason'] ) && $data['reason'] != '' )
			$reason = $data['reason'];

		$content = str_replace( '%reason%', $reason, $content );

		return $content;

	}
endif;
add_filter( 'mycred_parse_log_entry_woocommerce_refund', 'mycred_woo_log_entry_refunds', 90, 2 );

/**
 * Log Entry: Profit Share Refund
 * @since 1.5.4
 * @version 1.0
 */
if ( ! function_exists( 'mycred_woo_log_entry_profit_refund' ) ) :
	function mycred_woo_log_entry_profit_refund( $content, $log_entry ) {

		$data     = maybe_unserialize( $log_entry->data );
		$order_id = '';
		if ( isset( $data['order_id'] ) && $data['order_id'] != '' )
			$order_id = '#' . $data['order_id'];

		$content  = str_replace( '%order_id%', $order_id, $content );

		$reason   = '-';
		if ( isset( $data['reason'] ) && $data['reason'] != '' )
			$reason = $data['reason'];

		$content  = str_replace( '%reason%', $reason, $content );

		return $content;

	}
endif;
add_filter( 'mycred_parse_log_entry_store_sale_refund', 'mycred_woo_log_entry_profit_refund', 90, 2 );

/**
 * Parse Email Notice
 * @since 1.2.2
 * @version 1.0.2
 */
if ( ! function_exists( 'mycred_woo_parse_email' ) ) :
	function mycred_woo_parse_email( $email ) {

		if ( $email['request']['ref'] == 'woocommerce_payment' && function_exists( 'woocommerce_get_page_id' ) ) {

			if ( function_exists( 'wc_get_order' ) )
				$order = wc_get_order( (int) $email['request']['ref_id'] );
			else
				$order = new WC_Order( (int) $email['request']['ref_id'] );

			if ( isset( $order->id ) ) {

				$url     = esc_url( add_query_arg( 'order', $order->id, mycred_get_permalink( woocommerce_get_page_id( 'view_order' ) ) ) );
				$content = str_replace( '%order_id%', $order->id, $email['request']['entry'] );

				$email['request']['entry'] = str_replace( '%order_link%', '<a href="' . esc_url( $url ) . '">#' . $order->id . '</a>', $content );

			}

		}

		return $email;

	}
endif;
add_filter( 'mycred_email_before_send', 'mycred_woo_parse_email', 10 );

/**
 * After Order Total
 * Potentially insert the cart cost in points and the users current balance.
 * @since 0.1
 * @version 1.3
 */
if ( ! function_exists( 'mycred_woo_after_order_total' ) ) :
	function mycred_woo_after_order_total() {

		if ( ! is_user_logged_in() ) return;

		// Only available for logged in non-excluded users
		global $woocommerce;

		$available_gateways = $woocommerce->payment_gateways->get_available_payment_gateways();
		if ( ! isset( $available_gateways['mycred'] ) ) return;
		
		$point_type         = $available_gateways['mycred']->get_option( 'point_type' );
		if ( $point_type === NULL )
			$point_type = MYCRED_DEFAULT_TYPE_KEY;

		$mycred             = mycred( $point_type );
		$user_id            = get_current_user_id();

		// Nothing to do if we are excluded
		if ( $mycred->exclude_user( $user_id ) ) return;

		$show_total         = $available_gateways['mycred']->get_option( 'show_total' );
		$balance            = $mycred->get_users_balance( $user_id, $point_type );
		$balance_label      = $available_gateways['mycred']->get_option( 'balance_format' );

		// If we should show the total in points
		if ( ( $show_total == 'both' || $show_total == 'all') 
			|| ( $show_total == 'cart' && is_cart() ) 
			|| ( $show_total == 'checkout' && is_checkout() ) 
		) {

			// Make sure myCRED is not the currency used
			$currency = get_woocommerce_currency();
			if ( ! mycred_point_type_exists( $currency ) && $currency != 'MYC' ) {

				// Apply Exchange Rate
				$cost    = $mycred->number( ( $woocommerce->cart->total / $available_gateways['mycred']->get_option( 'exchange_rate' ) ) );
				$cost    = apply_filters( 'mycred_woo_order_cost', $cost, $woocommerce->cart, true, $mycred );
				

?>
<tr class="total">
	<th><strong><?php echo $mycred->template_tags_general( $available_gateways['mycred']->get_option( 'total_label' ) ); ?></strong></th>
	<td>
		<div class="current-balance order-total-in-points">
			<strong class="<?php if ( $balance < $cost ) echo 'mycred-low-funds'; else echo 'mycred-funds'; ?>"<?php if ( $balance < $cost ) echo ' style="color:red;"'; ?>><?php echo $mycred->format_creds( $cost ); ?></strong> 
		</div>
	</td>
</tr>
<?php

			}

		}

		// If we should show the users balance
		if ( ! empty( $balance_label ) ) {

?>
<tr class="total">
	<th><strong><?php echo $mycred->template_tags_general( $balance_label ); ?></strong></th>
	<td>
		<div class="current-balance">
			<?php echo $mycred->format_creds( $balance ); ?>
		</div>
	</td>
</tr>
<?php

		}

	}
endif;
add_action( 'woocommerce_review_order_after_order_total', 'mycred_woo_after_order_total' );
add_action( 'woocommerce_cart_totals_after_order_total',  'mycred_woo_after_order_total' );
