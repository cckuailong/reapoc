<?php
if ( ! defined( 'myCRED_VERSION' ) ) exit;

/**
 * myCRED_Payment_Gateway class
 * @see http://codex.mycred.me/classes/mycred_payment_gateway/
 * @since 0.1
 * @version 1.3
 */
if ( ! class_exists( 'myCRED_Payment_Gateway' ) ) :
	abstract class myCRED_Payment_Gateway {

		/**
		 * The Gateways Unique ID
		 */
		public $id                = false;

		/**
		 * Gateway Label
		 */
		public $label             = '';

		/**
		 * Indicates if the gateway is operating in sandbox mode or not
		 */
		public $sandbox_mode      = false;

		/**
		 * The gateways logo URL
		 */
		public $gateway_logo_url  = '';

		/**
		 * Gateways Settings
		 */
		public $prefs             = false;

		/**
		 * Main Point Type Settings
		 */
		public $core;

		/**
		 * buyCRED Add-on Settings
		 */
		public $buycred           = false;

		/**
		 * The point type being purchased
		 */
		public $point_type        = '';

		/**
		 * The point amount being purchased
		 */
		public $amount            = 0;

		/**
		 * The buyers ID
		 */
		public $buyer_id          = false;

		/**
		 * The recipients ID
		 */
		public $recipient_id      = false;

		/**
		 * Indicates if this is a gift or not
		 */
		public $gifting           = false;

		/**
		 * Indicates if this is a valid purchase request
		 */
		public $valid_request     = false;

		/**
		 * The current users ID
		 */
		public $current_user_id   = 0;

		/**
		 * Redirect fields
		 */
		public $redirect_fields   = array();

		/**
		 * Redirect URL
		 */
		public $redirect_to       = '';

		public $errors         = array();

		/**
		 * Toggle ID
		 */
		public $toggle_id         = '';

		/**
		 * Limit Setting
		 */
		public $buycred_limit     = array();

		protected $response;
		protected $request;
		protected $status;

		protected $processing_log = NULL;

		/**
		 * Construct
		 */
		public function __construct( $args = array(), $gateway_prefs = NULL ) {

			// Make sure gateway prefs is set
			if ( $gateway_prefs === NULL ) return;

			// Populate
			$this->now              = current_time( 'timestamp' );
			$this->current_user_id  = get_current_user_id();
			$this->core             = mycred();
			$this->buycred          = mycred_get_buycred_settings();

			// Arguments
			if ( ! empty( $args ) ) {
				foreach ( $args as $key => $value ) {
					$this->$key = $value;
				}
			}

			$gateway_settings       = $this->defaults;
			if ( is_array( $gateway_prefs ) && array_key_exists( $this->id, $gateway_prefs ) )
				$gateway_settings = $gateway_prefs[ $this->id ];

			elseif ( is_object( $gateway_prefs ) && array_key_exists( $this->id, $gateway_prefs->gateway_prefs ) )
				$gateway_settings = $gateway_prefs->gateway_prefs[ $this->id ];

			$this->prefs            = shortcode_atts( $this->defaults, $gateway_settings );

			// Sandbox Mode
			$this->sandbox_mode     = ( isset( $this->prefs['sandbox'] ) ) ? (bool) $this->prefs['sandbox'] : false;

			// Decode Log Entries
			add_filter( 'mycred_prep_template_tags',                          array( $this, 'decode_log_entries' ), 10, 2 );
			add_filter( 'mycred_parse_log_entry_buy_creds_with_' . $this->id, array( $this, 'log_entry' ), 10, 2 );

		}

		/**
		 * Request Validator
		 * @since 1.8
		 * @version 1.0
		 */
		public function valid_request() {

			// Step 1 - We need to be logged in to buy
			if ( ! is_user_logged_in() ) return false;

			// Step 2 - We need a valid token to start the request
			if ( ! isset( $_REQUEST['token'] ) || ! wp_verify_nonce( $_REQUEST['token'], 'mycred-buy-creds' ) ) return false;

			$valid                = true;

			$this->point_type     = $this->get_point_type();
			if ( $this->point_type != MYCRED_DEFAULT_TYPE_KEY )
				$this->core = mycred( $this->point_type );

			$this->buycred_limit  = mycred_get_buycred_sale_setup( $this->point_type );

			$this->transaction_id = ( isset( $_REQUEST['revisit'] ) ) ? strtoupper( sanitize_text_field( $_REQUEST['revisit'] ) ) : false;
			$this->post_id        = ( $this->transaction_id !== false ) ? buycred_get_pending_payment_id( $this->transaction_id ) : false;
			$this->buyer_id       = $this->current_user_id;
			$this->recipient_id   = $this->get_recipient_id();
			$this->amount         = $this->get_amount();
			$this->cost           = $this->get_cost( $this->amount, $this->point_type );
			$this->currency       = ( isset( $this->prefs['currency'] ) ) ? $this->prefs['currency'] : '';
			$this->maximum        = -1;

			if ( $this->core->exclude_user( $this->buyer_id ) ){
				$valid = false;
				$this->errors[] = __( 'Buyer is excluded from this point type.', 'mycred' );
			}
			elseif ( $this->core->exclude_user( $this->recipient_id ) ) {
				$valid          = false;
				$this->errors[] = __( 'Recipient is excluded from this point type. ', 'mycred' );
			}

			elseif ( $this->amount === false || $this->amount == 0 ){
				$valid = false;
				$this->errors[] = __( 'An amount value is required.', 'mycred' );
			}

			elseif ( ! empty( $this->buycred_limit['max'] ) && $this->amount > floatval( $this->buycred_limit['max'] ) ){
				$valid = false;
				$this->errors[] = apply_filters( 'buycred_max_amount_error', sprintf( __( 'The amount must be less than %d.', 'mycred' ), $this->buycred_limit['max'] ), $this->buycred_limit['max'], $this );
			}

			elseif ( $this->exceeds_limit() ){
				$valid = false;
				$this->errors[] = __( 'You have exceeded the limit.', 'mycred' );
			}

			if ( $valid )
				$this->populate_transaction();

			if ( ! empty( $this->errors ) ) 
				$valid = false;

			return apply_filters( 'mycred_valid_buycred_request', $valid, $this );

		}

		/**
		 * Populate Transaction
		 * @since 1.8
		 * @since 2.3 @filter added `mycred_buycred_populate_transaction` to avoid pending payments log in some cases.
		 * @version 1.0
		 */
		public function populate_transaction() {

			if( apply_filters( 'mycred_buycred_populate_transaction', false, $this->id ) )
				return;

			// Create a new transaction
			$new_transaction = false;
			if ( $this->transaction_id === false && $this->post_id === false ) {

				$this->post_id        = $this->add_pending_payment( array(
					$this->buyer_id,
					$this->recipient_id,
					$this->amount,
					$this->cost,
					$this->currency,
					$this->point_type
				) );

				$this->transaction_id = get_the_title( $this->post_id );

			}

			// Get existing one
			elseif ( $this->post_id === false ) {

				$transaction = buycred_get_pending_payment( $this->post_id );

				if ( $transaction !== false ) {

					$new_transaction      = true;

					$this->point_type     = $transaction->point_type;
					$this->amount         = $transaction->amount;
					$this->cost           = $transaction->cost;
					$this->currency       = $transaction->currency;
					$this->buyer_id       = $transaction->buyer_id;
					$this->recipient_id   = $transaction->recipient_id;
					$this->transaction_id = $transaction->public_id;

				}

			}

			$this->prep_sale( $new_transaction );

		}

		/**
		 * Prep Sale
		 * @since 1.8
		 * @version 1.0
		 */
		public function prep_sale( $new_transaction = false ) { }

		/**
		 * Send JSON
		 * @since 1.8
		 * @version 1.0
		 */
		public function send_json( $content = '' ) {

			$content = apply_filters( 'mycred_buycred_send_json', $content, $this );

			wp_send_json( $content );

		}

		/**
		 * Request Exceeds Limit Check
		 * Checks if a requested amount of points exceeds the "maximum" limit (if used).
		 * @since 1.8
		 * @version 1.0
		 */
		public function exceeds_limit() {

			$exceeds   = false;
			$remaining = mycred_user_can_buycred( $this->buyer_id, $this->point_type );

			// A maximum limit is enforced and we have maxed out
			if ( $remaining === 0 ) {

				$exceeds          = true;
				$this->maximum  = 0;

			}

			// A maximum limit is used so we need to make sure the amount we want to buy is valid
			elseif ( $remaining !== true ) {

				$this->maximum = $this->core->number( $remaining );

				// The amount remaining is lower than our requested amount
				if ( $remaining > 0 && $remaining < $this->amount )
					$this->amount = $remaining;

				// Make sure the amount does not exceeds our maximum limit, if it does, reject
				else {

					$remaining = $this->core->number( $remaining - $this->amount );

					if ( $remaining < 0 ) {
						$exceeds        = true;
						$this->maximum  = 0;
					}
					else {

						$this->maximum  = $remaining;

					}

				}

			}

			return apply_filters( 'mycred_exceeds_buycred_limit', $exceeds, $remaining, $this );

		}

		/**
		 * Process Purchase
		 * @since 0.1
		 * @version 1.0
		 */
		public function process() { }

		/**
		 * Results Handler
		 * @since 0.1
		 * @version 1.0
		 */
		public function returning() { }

		/**
		 * AJAX Buy Handler
		 * @since 1.8
		 * @version 1.0
		 */
		public function ajax_buy() { }

		/**
		 * Buy Handler
		 * @since 0.1
		 * @version 1.0
		 */
		public function buy() { }

		/**
		 * Admin Init Handler
		 * @since 1.7
		 * @version 1.0
		 */
		public function admin_init() { }

		/**
		 * Preferences
		 * @since 0.1
		 * @version 1.0
		 */
		public function preferences() {

			echo '<p>This Payment Gateway has no settings</p>';

		}

		/**
		 * Sanatize Prefs
		 * @since 0.1
		 * @version 1.0
		 */
		public function sanitise_preferences( $data ) {

			return $data;

		}

		/**
		 * Checkout Header
		 * @since 1.8
		 * @version 1.0
		 */
		public function checkout_header() {

			$content  = '';

			if ( $this->sandbox_mode )
				$content .= '<div class="checkout-header"><div class="warning">' . esc_js( esc_attr( __( 'Test Mode', 'mycred' ) ) )  . '</div></div>';

			$content .= '<div class="checkout-body padded' . ( ( ! $this->sandbox_mode ) ? ' no-header' : '' ) . '">';

			return apply_filters( 'mycred_buycred_checkout_header', $content, $this );

		}

		/**
		 * Checkout Footer
		 * @since 1.8
		 * @version 1.0
		 */
		public function checkout_footer( $button_label = '' ) {

			if ( $button_label == '' )
				$button_label = __( 'Continue', 'mycred' );

			$content  = '';
			if ( ! empty( $this->redirect_fields ) ) {

				$fields = apply_filters( 'mycred_buycred_redirect_fields', $this->redirect_fields, $this );

				if ( ! empty( $fields ) ) {
					foreach ( $fields as $name => $value ) $content .= '<input type="hidden" name="' . $name . '" value="' . $value . '" />';
				}

			}

			$button   = '<button type="button" id="checkout-action-button" data-act="submit" data-value="" class="btn btn-default">' . esc_js( $button_label ) . '</button>';

			// The button
			if ( ! empty( $this->toggle_id ) )
				$button = '<button type="button" id="checkout-action-button" data-act="toggle" data-value="' . esc_attr( $this->toggle_id ) . '" class="btn btn-default">' . esc_js( $button_label ) . '</button>';

			elseif ( ! empty( $this->redirect_to ) )
				$button = '<button type="button" id="checkout-action-button" data-act="redirect" data-value="' . $this->redirect_to . '" class="btn btn-default '. $this->id .'">' . esc_js( $button_label ) . '</button>';

			$button   = apply_filters( 'mycred_buycred_checkout_button', $button, $this );

			$content .= '</div>';

			if ( $button != '' )
				$content .= '<div class="checkout-footer">' . $button . '</div>';

			return apply_filters( 'mycred_buycred_checkout_footer', $content, $this );

		}

		/**
		 * Checkout Logo
		 * @since 1.8
		 * @version 1.0
		 */
		public function checkout_logo( $title = '' ) {

			if ( $title === '' ) {
				if ( isset( $this->prefs['title'] ) ) $title = $this->prefs['title'];
				elseif ( isset( $this->prefs['label'] ) ) $title = $this->prefs['label'];
			}

			if ( isset( $this->prefs['logo'] ) && ! empty( $this->prefs['logo'] ) )
				$content = '<img src="' . $this->prefs['logo'] . '" alt="" />';

			elseif ( isset( $this->prefs['logo_url'] ) && ! empty( $this->prefs['logo_url'] ) )
				$content = '<img src="' . $this->prefs['logo_url'] . '" alt="" />';

			elseif ( isset( $this->gateway_logo_url ) && ! empty( $this->gateway_logo_url ) )
				$content = '<img src="' . $this->gateway_logo_url . '" alt="" />';

			elseif ( $title !== false ) $content = '<h2 class="gateway-title">' . esc_html( $title ) . '</h2>';
			else {
				$content = '';
			}

			return apply_filters( 'mycred_buycred_checkout_logo', $content, $this );

		}

		/**
		 * Checkout: Order
		 * @since 1.8
		 * @version 1.0
		 */
		public function checkout_order() {

			$table_rows   = array();
			$point_type_name = apply_filters( 'mycred_buycred_checkout_order', $this->core->plural(), $this );
			$table_rows[] = '<tr><td class="item">' . esc_html( $point_type_name ) . '</td><td class="cost right">' . $this->amount . '</td></tr>';

			$item_label = apply_filters( 'mycred_buycred_checkout_order', __('Item', 'mycred'), $this );
			$amount_label = apply_filters( 'mycred_buycred_checkout_order', __('Amount', 'mycred'), $this );

			if ( $this->gifting )
				$table_rows[] = '<tr><td colspan="2"><strong>' . esc_js( esc_attr( __( 'Recipient', 'mycred' ) ) ) . ':</strong> ' . esc_html( get_userdata( $this->recipient_id )->display_name ) . '</td></tr>';

			$table_rows[] = '<tr class="total"><td class="item right">' . esc_js( esc_attr( __( 'Cost', 'mycred' ) ) ) . '</td><td class="cost right">' . sprintf( '%s %s', $this->cost, $this->prefs['currency'] ) . '</td></tr>';

			$table_rows   = apply_filters( 'mycred_buycred_order_table_rows', $table_rows, $this );

			if ( ! empty( $table_rows ) )
				$content = '
					<table class="table" cellspacing="0" cellpadding="0">
						<thead>
							<tr>
								<th class="item">' . esc_js( esc_attr($item_label ) ) . '</td>
								<th class="cost right">' . esc_js( esc_attr($amount_label ) ) . '</td>
							</tr>
						</thead>
						<tbody>
							' . implode( '', $table_rows ) . '
						</tbody>
					</table>';

			return apply_filters( 'mycred_buycred_checkout_order', $content, $this );

		}

		/**
		 * Checkout: Transaction ID
		 * @since 1.8
		 * @version 1.0
		 */
		public function checkout_transaction_id() {

			$content = '<h2><span class="text-mutted">' . esc_js( esc_attr( __( 'Transaction ID', 'mycred' ) ) ) . '</span>' . esc_attr( $this->transaction_id ) . '</h2>';

			return apply_filters( 'mycred_buycred_checkout_txtid', $content, $this );

		}

		/**
		 * Checkout: Cancel
		 * @since 1.8
		 * @version 1.0
		 */
		public function checkout_cancel() {

			$content = '<hr /><div class="cancel"><a href="' . $this->get_cancelled( $this->transaction_id ) . '">' . esc_js( esc_attr( __( 'cancel purchase', 'mycred' ) ) )  . '</a></div>';

			return apply_filters( 'mycred_buycred_checkout_cancel', $content, $this );

		}

		/**
		 * Checkout Page Title
		 * @since 1.8
		 * @version 1.0
		 */
		public function checkout_page_title() { }

		/**
		 * Checkout Page Body
		 * @since 1.8
		 * @version 1.0
		 */
		public function checkout_page_body() { }

		/**
		 * Checkout Page Footer
		 * @since 1.8
		 * @version 1.0
		 */
		public function checkout_page_footer() { }

		/**
		 * Exchange Rate Setup
		 * @since 1.5
		 * @version 1.1
		 */
		public function exchange_rate_setup( $default = 'USD' ) {

			if ( ! isset( $this->prefs['exchange'] ) ) return;

			$content     = '';
			$point_types = array( MYCRED_DEFAULT_TYPE_KEY );

			if ( isset( $this->buycred['types'] ) )
				$point_types = (array) $this->buycred['types'];

			foreach ( $point_types as $type_id ) {

				$mycred = mycred( $type_id );

				if ( ! isset( $this->prefs['exchange'][ $type_id ] ) )
					$this->prefs['exchange'][ $type_id ] = 1;

				$content .= '
<table>
	<tr>
		<td style="min-width: 100px;"><div class="form-control-static">1 ' . esc_html( $mycred->singular() ) . '</div></td>
		<td style="width: 10px;"><div class="form-control-static">=</div></td>
		<td><input type="text" name="' . $this->field_name( array( 'exchange' => $type_id ) ) . '" id="' . $this->field_id( array( 'exchange' => $type_id ) ) . '" value="' . esc_attr( $this->prefs['exchange'][ $type_id ] ) . '" size="8" /> ';


		if ( isset( $this->prefs['currency'] ) )
			$content .= '<span class="mycred-gateway-' . $this->id . '-currency">' . ( ( $this->prefs['currency'] == '' ) ? __( 'Select currency', 'mycred' ) : esc_attr( $this->prefs['currency'] ) ) . '</span>';

		else
			$content .= '<span>' . esc_attr( $default ) . '</span>';

		$content .= '</td>
	</tr>
</table>';

			}

			echo apply_filters( 'mycred_buycred_exchange_rate_field', $content, $default, $this );

		}

		/**
		 * Add Pending Payment
		 * @since 1.5
		 * @version 1.1.1
		 */
		public function add_pending_payment( $data ) {

			$post_id = false;
			list ( $to, $from, $amount, $cost, $currency, $point_type ) = $data;

			// Title
			if ( isset( $_REQUEST['transaction_id'] ) )
				$post_title = trim( $_REQUEST['transaction_id'] );
			else
				$post_title = strtoupper( wp_generate_password( 6, false, false ) );

			$check = $this->transaction_exists( $to, $from, $amount, $cost, $currency, $point_type );
			if ( $check !== false ) return $check;

			// Make sure we are not adding more then one pending item
			$check = mycred_get_page_by_title( $post_title, ARRAY_A, 'buycred_payment' );
			if ( $check === NULL || ( isset( $check['post_status'] ) && $check['post_status'] == 'trash' ) ) {

				// Generate new id and trash old request
				if ( isset( $check['post_status'] ) && $check['post_status'] == 'trash' ) {
					buycred_trash_pending_payment( $check['ID'] );
					$post_title = strtoupper( wp_generate_password( 6, false, false ) );
				}

				// Insert post
				$post_id = wp_insert_post( array(
					'post_title'     => $post_title,
					'post_type'      => 'buycred_payment',
					'post_status'    => 'publish',
					'post_author'    => $from,
					'ping_status'    => 'closed',
					'comment_status' => 'open'
				) );

				// Add meta details if insertion was a success
				if ( $post_id !== NULL && ! is_wp_error( $post_id ) ) {

					mycred_add_post_meta( $post_id, 'from',       $to, true );
					mycred_add_post_meta( $post_id, 'to',         $from, true );
					mycred_add_post_meta( $post_id, 'amount',     $amount, true );
					mycred_add_post_meta( $post_id, 'cost',       $cost, true );
					mycred_add_post_meta( $post_id, 'currency',   $currency, true );
					mycred_add_post_meta( $post_id, 'point_type', $point_type, true);
					mycred_add_post_meta( $post_id, 'gateway',    $this->id, true );

					mycred_delete_user_meta( $from, 'buycred_pending_payments' );

					$mycred    = mycred( $point_type );

					$log_entry = $this->first_comment( sprintf( _x( 'Received new request to purchase %s.', '%s is replaced with the point amount and name.', 'mycred' ), $mycred->format_number( $amount ) . ' ' . $mycred->plural() ) );
					$log_entry = apply_filters( 'mycred_new_buycred_request_comment_' . $this->id, $log_entry, $data );

					$this->log_call( $post_id, $log_entry );

				}

			}
			else $post_id = $check['ID'];

			return apply_filters( 'mycred_add_pending_payment', $post_id, $data );

		}

		public function transaction_exists( $to, $from, $amount, $cost, $currency, $point_type ) {

			$post_query = array(
				'post_type'      => 'buycred_payment',
				'post_status'    => 'publish',
				'posts_per_page' => '-1',
				'orderby'        => 'date',
				'order'          => 'DESC',
				'fields'         => 'ids'
			);

			$meta_query = array();

			$meta_query[] = array(
				'key'     => 'to',
				'value'   => $to,
				'compare' => '=',
				'type'    => 'NUMERIC'
			);

			$meta_query[] = array(
				'key'     => 'from',
				'value'   => $from,
				'compare' => '=',
				'type'    => 'NUMERIC'
			);

			$meta_query[] = array(
				'key'     => 'amount',
				'value'   => $amount,
				'compare' => '=',
				'type'    => 'NUMERIC'
			);

			$meta_query[] = array(
				'key'     => 'cost',
				'value'   => $cost,
				'compare' => '=',
				'type'    => 'NUMERIC'
			);

			$meta_query[] = array(
				'key'     => 'currency',
				'value'   => $currency,
				'compare' => '='
			);

			$meta_query[] = array(
				'key'     => 'point_type',
				'value'   => $point_type,
				'compare' => '='
			);

			$meta_query[] = array(
				'key'     => 'gateway',
				'value'   => $this->id,
				'compare' => '='
			);

			$post_query['meta_query'] = $meta_query;

			$post_id = false;
			$pending = new WP_Query( $post_query );
			if ( ! empty( $pending->posts ) ) {

				$post_id = $pending->posts[0];

				wp_reset_postdata();

			}

			return $post_id;

		}

		/**
		 * First Comment
		 * Used to allow a gateway to adjust the first comment with pending payments. 
		 * @since 1.7.3
		 * @version 1.0
		 */
		public function first_comment( $comment ) {

			return $comment;

		}

		/**
		 * Get Pending Payment
		 * @since 1.5
		 * @version 1.1
		 */
		public function get_pending_payment( $post_id = NULL ) {

			$pending_payment = buycred_get_pending_payment( $post_id );

			return apply_filters( 'mycred_get_pending_payment', $pending_payment, $post_id );

		}

		/**
		 * Get Recipient ID
		 * Returns the numeric ID of the user that is nominated to receive the purchased points.
		 * @since 1.8
		 * @version 1.0
		 */
		public function get_recipient_id() {

			$this->gifting = false;
			$recipient_id  = $this->current_user_id;

			// Gift to a user
			if ( $this->buycred['gifting']['members'] == 1 ) {

				if ( isset( $_REQUEST['gift_to'] ) ) {

					$gift_to = absint( $_REQUEST['gift_to'] );
					if ( $gift_to > 0 ) {
						$recipient_id  = $gift_to;
						$this->gifting = true;
					}

				}

			}

			// Gifting author
			if ( $this->buycred['gifting']['authors'] == 1 ) {

				if ( isset( $_REQEST['post_id'] ) ) {

					$post_id = absint( $_REQEST['post_id'] );
					$post    = mycred_get_post( $post_id );
					if ( isset( $post->post_author ) ) {
						$recipient_id  = absint( $post->post_author );
						$this->gifting = true;
					}

				}

			}

			return apply_filters( 'mycred_get_buycred_recipient_id', $recipient_id, $this );

		}
		public function get_to() {

			return $this->get_recipient_id();

		}

		/**
		 * Get Amount
		 * @since 1.8
		 * @version 1.0
		 */
		public function get_amount() {

			$settings           = mycred_get_buycred_sale_setup( $this->point_type );
			$amount             = false;

			// Validate amount ( amount is not zero, higher then minimum required and do not exceed maximum (if set) )
			if ( isset( $_REQUEST['amount'] ) && is_numeric( $_REQUEST['amount'] ) ) {

				$amount  = $this->core->number( $_REQUEST['amount'] );
				$minimum = $this->core->number( $settings['min'] );

				// Enforce minimum
				if ( $amount < $minimum )
					$amount = $minimum;

			}

			return apply_filters( 'mycred_get_buycred_amount', $amount, $this );

		}

		/**
		 * Get Point Type
		 * @since 1.5
		 * @version 1.2
		 */
		public function get_point_type() {

			$point_type = MYCRED_DEFAULT_TYPE_KEY;

			if ( isset( $_REQUEST['ctype'] ) ) {

				$type_id = sanitize_key( $_REQUEST['ctype'] );
				if ( $type_id != '' && mycred_point_type_exists( $type_id ) )
					$point_type = $type_id;

			}

			return $point_type;

		}

		/**
		 * Get Cost
		 * @since 1.3.2
		 * @version 1.2
		 */
		public function get_cost( $amount = 0, $point_type = MYCRED_DEFAULT_TYPE_KEY, $raw = false, $custom_rate = 0 ) {

			if(isset($_REQUEST['er_random']) && !empty($_REQUEST['er_random'])){
				$custom_rate=mycred_buycred_decode($_REQUEST['er_random']);
			}

			$setup = mycred_get_buycred_sale_setup( $point_type );

			// Apply minimum
			if ( $amount < $setup['min'] )
				$amount = $setup['min'];

			// Calculate cost here so we can use any exchange rate
			if ( array_key_exists( $point_type, $this->prefs['exchange'] ) ) {

				// Check for user override
				$override = mycred_get_user_meta( $this->current_user_id, 'mycred_buycred_rates_' . $point_type, '', true );
				if ( isset( $override[ $this->id ] ) && $override[ $this->id ] != '' )
					$rate = $override[ $this->id ];
				else if($custom_rate !=0 )
					$rate = $custom_rate;
				else
					$rate = $this->prefs['exchange'][ $point_type ];

				if ( isfloat( $rate ) )
					$rate = (float) $rate;
				else
					$rate = (int) $rate;

				$cost   = $amount * $rate;

			}
			else
				$cost = $amount;

			// Return a properly formated cost so PayPal is happy
			if ( ! $raw )
				$cost = number_format( $cost, 2, '.', '' );

			return apply_filters( 'mycred_buycred_get_cost', $cost, $amount, $point_type, $this->prefs, $setup );

		}

		/**
		 * Get Thank You Page
		 * @since 0.1
		 * @version 1.1
		 */
		public function get_thankyou() {

			$url = home_url( '/' );

			// Using a page
			if ( $this->buycred['thankyou']['use'] == 'page' ) {

				if ( ! empty( $this->buycred['thankyou']['page'] ) )
					$url = mycred_get_permalink( $this->buycred['thankyou']['page'] );

			}

			// Using a custom url
			elseif ( $this->buycred['thankyou']['use'] == 'custom' ) {

				if ( ! empty( $this->buycred['thankyou']['custom'] ) )
					$url = $this->buycred['thankyou']['custom'];

			}

			$profile_url = mycred_get_users_profile_url( $this->buyer_id );
			$url         = str_replace( '%profile%', $profile_url, $url );

			return apply_filters( 'mycred_buycred_thankyou_url', $url, $this );

		}

		/**
		 * Get Entry
		 * Returns the appropriate log entry template.
		 * @since 0.1
		 * @version 1.1
		 */
		public function get_entry( $recipient_id = false, $buyer_id = false ) {

			if ( $recipient_id === false ) $recipient_id = $this->recipient_id;
			if ( $buyer_id === false ) $buyer_id = $this->buyer_id;

			$log_entry = $this->buycred['log'];

			// Log entry
			if ( $recipient_id != $buyer_id ) {

				if ( $this->buycred['gifting']['members'] == 1 || $this->buycred['gifting']['authors'] == 1 )
					$log_entry = $this->buycred['gifting']['log'];

			}

			return $log_entry;

		}

		/**
		 * Get Cancelled Page
		 * @since 0.1
		 * @version 1.4
		 */
		public function get_cancelled( $transaction_id = NULL ) {

			$url         = buycred_get_cancel_transaction_url( $transaction_id );

			$profile_url = mycred_get_users_profile_url( $this->buyer_id );
			$url         = str_replace( '%profile%', $profile_url, $url );

			return $url;

		}

		/**
		 * Log Gateway Call
		 * @since 1.5
		 * @version 1.2
		 */
		public function log_call( $payment_id, $log ) {

			if ( is_array( $log ) )
				$log = implode( '<br />', $log );

			buycred_add_pending_comment( $payment_id, $log );

		}

		/**
		 * Decode Log Entries
		 * @since 0.1
		 * @version 1.0
		 */
		public function log_entry( $content, $log_entry ) {

			return $this->core->template_tags_user( $content, $log_entry->ref_id );

		}

		/**
		 * Get Log Entry
		 * @since 1.4
		 * @version 1.0.1
		 */
		public function get_log_entry( $from = 0, $to = 0 ) {

			$entry = $this->get_entry( $from, $to );
			if ( isset( $this->label ) )
				$entry = str_replace( '%gateway%', $this->label, $entry );

			if ( $this->sandbox_mode ) $entry = 'TEST ' . $entry;
			
			return apply_filters( 'mycred_buycred_get_log_entry', $entry, $from, $to, $this );

		}

		/**
		 * Get Field Name
		 * Returns the field name for the current gateway
		 * @since 0.1
		 * @version 1.0
		 */
		public function field_name( $field = '' ) {

			if ( is_array( $field ) ) {

				$array = array();
				foreach ( $field as $parent => $child ) {
					if ( ! is_numeric( $parent ) )
						$array[] = str_replace( '-', '_', $parent );

					if ( ! empty( $child ) && ! is_array( $child ) )
						$array[] = str_replace( '-', '_', $child );
				}
				$field = '[' . implode( '][', $array ) . ']';

			}
			else {

				$field = '[' . $field . ']';

			}

			return 'mycred_pref_buycreds[gateway_prefs][' . $this->id . ']' . $field;

		}

		/**
		 * Get Field ID
		 * Returns the field id for the current gateway
		 * @since 0.1
		 * @version 1.0
		 */
		public function field_id( $field = '' ) {

			if ( is_array( $field ) ) {

				$array = array();
				foreach ( $field as $parent => $child ) {
					if ( ! is_numeric( $parent ) )
						$array[] = str_replace( '_', '-', $parent );

					if ( ! empty( $child ) && ! is_array( $child ) )
						$array[] = str_replace( '_', '-', $child );
				}
				$field = implode( '-', $array );

			}
			else {

				$field = str_replace( '_', '-', $field );

			}

			return 'mycred-gateway-prefs-' . str_replace( '_', '-', $this->id ) . '-' . $field;

		}

		/**
		 * Callback URL
		 * @since 0.1
		 * @version 1.2
		 */
		public function callback_url() {

			$url = add_query_arg( 'mycred_call', $this->id, home_url( '/' ) );

			return apply_filters( 'mycred_buycred_callback_url', $url, $this );

		}

		/**
		 * Start Log
		 * @since 1.4
		 * @version 1.0
		 */
		public function start_log() {

			$this->new_log_entry( 'Incoming confirmation call detected' );
			$this->new_log_entry( sprintf( 'Gateway identified itself as "%s"', $this->id ) );
			$this->new_log_entry( 'Verifying caller' );

		}

		/**
		 * New Log Entry
		 * @since 0.1
		 * @version 1.0
		 */
		public function new_log_entry( $entry = '' ) {

			if ( ! isset( $this->processing_log[ $this->id ] ) )
				$this->processing_log[ $this->id ] = array();

			$this->processing_log[ $this->id ][] = $entry;

		}

		/**
		 * Save Log Entry
		 * @since 0.1
		 * @version 1.0
		 */
		public function save_log_entry( $id = '', $outcome = '' ) {

			update_option( 'mycred_buycred_last_call', array(
				'gateway' => $this->id,
				'date'    => time(),
				'outcome' => $outcome,
				'id'      => $id,
				'entries' => serialize( $this->processing_log[ $this->id ] )
			) );

		}

		/**
		 * Payment Page Header
		 * Pre 1.8 setup. Will be removed as of version 1.9
		 * @since 0.1
		 * @version 1.2
		 */
		public function get_page_header( $site_title = '', $reload = false ) {

			// Set Logo
			$logo = '';
			if ( isset( $this->prefs['logo'] ) && ! empty( $this->prefs['logo'] ) )
				$logo = '<img src="' . $this->prefs['logo'] . '" alt="" />';

			elseif ( isset( $this->prefs['logo_url'] ) && ! empty( $this->prefs['logo_url'] ) )
				$logo = '<img src="' . $this->prefs['logo_url'] . '" alt="" />';

			elseif ( isset( $this->gateway_logo_url ) && ! empty( $this->gateway_logo_url ) )
				$logo = '<img src="' . $this->gateway_logo_url . '" alt="" />';

			// Set Title
			if ( $this->sandbox_mode )
				$title = __( 'Test Payment', 'mycred' );

			elseif ( isset( $this->label ) )
				$title = $this->label;

			else
				$title = __( 'Payment', 'mycred' );

			if ( ! isset( $this->transaction_id ) )
				$this->transaction_id = '';

?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
	<meta name="apple-mobile-web-app-capable" content="yes" />
	<title><?php echo $site_title; ?></title>
	<meta name="robots" content="noindex, nofollow" />
	<?php if ( $reload ) echo '<meta http-equiv="refresh" content="2;url=' . $reload . '" />'; ?>

	<link rel="stylesheet" href="<?php echo plugins_url( 'assets/css/gateway.css', MYCRED_PURCHASE ); ?>" type="text/css" media="all" />
	<link rel="stylesheet" href="<?php echo plugins_url( 'assets/css/bootstrap-grid.css', myCRED_THIS ); ?>" type="text/css" media="all" />
	<link rel="stylesheet" href="<?php echo plugins_url( 'assets/css/mycred-forms.css', myCRED_THIS ); ?>" type="text/css" media="all" />
	<?php do_action( 'mycred_buycred_page_header', $title, $reload, $this->id ); ?>

</head>
<body class="mycred-metabox">
	<div class="row">
		<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
			<?php echo $logo; ?>
		</div>
		<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 text-right">
			<h2><?php echo $title; ?></h2>
			<p><a href="<?php echo $this->get_cancelled( $this->transaction_id ); ?>" id="return-where-we-came-from"><?php _e( 'Cancel', 'mycred' ); ?></a></p>
		</div>
	</div>
<?php

			do_action( 'mycred_buycred_page_top', $title, $reload, $this->id );

		}
		public function purchase_header( $title = '', $reload = false ) {
			$this->get_page_header( $title, $reload );
		}

		/**
		 * Payment Page Footer
		 * @since 0.1
		 * @version 1.1
		 */
		public function get_page_footer() {

			do_action( 'mycred_buycred_page_footer', $this->id );

?>
</body> 
</html>
<?php

		}
		public function purchase_footer() {
				$this->get_page_footer();
			}

		/**
		 * Get Billing Address Form
		 * Depreciated as of 1.7. This should be added by the gateway.
		 * @since 1.4
		 * @version 1.0
		 */
		public function get_billing_address_form( $country_dropdown = false ) {

			if ( ! is_user_logged_in() ) return;

			$user = wp_get_current_user();

			// Base
			$user_details = array(
				'first_name' => ( isset( $_POST['billing']['first_name'] ) ) ? $_POST['billing']['first_name'] : $user->first_name,
				'last_name'  => ( isset( $_POST['billing']['last_name'] )  ) ? $_POST['billing']['last_name']  : $user->last_name,
				'address1'   => ( isset( $_POST['billing']['address1'] )   ) ? $_POST['billing']['address1']   : $user->address1,
				'address2'   => ( isset( $_POST['billing']['address2'] )   ) ? $_POST['billing']['address2']   : $user->address2,
				'city'       => ( isset( $_POST['billing']['city'] )       ) ? $_POST['billing']['city']       : $user->city,
				'postcode'   => ( isset( $_POST['billing']['postcode'] )   ) ? $_POST['billing']['postcode']   : $user->postcode,
				'state'      => ( isset( $_POST['billing']['state'] )      ) ? $_POST['billing']['state']      : $user->state,
				'country'    => ( isset( $_POST['billing']['country'] )    ) ? $_POST['billing']['country']    : $user->country
			);

			// Grab WooCommerce User Fields
			if ( ! isset( $_POST['billing']['address1'] ) ) {

				if ( class_exists( 'WC_Customer' ) ) {
					$user_details['first_name'] = get_user_meta( $user->ID, 'billing_first_name', true );
					$user_details['last_name']  = get_user_meta( $user->ID, 'billing_last_name',  true );
					$user_details['address1']   = get_user_meta( $user->ID, 'billing_address_1',  true );
					$user_details['address2']   = get_user_meta( $user->ID, 'billing_address_2',  true );
					$user_details['city']       = get_user_meta( $user->ID, 'billing_city',       true );
					$user_details['postcode']   = get_user_meta( $user->ID, 'billing_postcode',   true );
					$user_details['state']      = get_user_meta( $user->ID, 'billing_state',      true );
				}

				// Else grab MarketPress User Fields
				elseif ( class_exists( 'MarketPress' ) ) {
					$meta = get_user_meta( $user->ID, 'mp_billing_info', true );
					if ( is_array( $meta ) ) {
						$user_details['address1']   = ( isset( $meta['address1'] ) ) ? $meta['address1'] : '';
						$user_details['address2']   = ( isset( $meta['address2'] ) ) ? $meta['address2'] : '';
						$user_details['city']       = ( isset( $meta['city'] ) )     ? $meta['city']     : '';
						$user_details['postcode']   = ( isset( $meta['zip'] ) )      ? $meta['zip']      : '';
						$user_details['state']      = ( isset( $meta['state'] ) )    ? $meta['state']    : '';
						$user_details['country']    = ( isset( $meta['country'] ) )  ? $meta['country']  : '';
					}
				}

			}

			// Let others play
			$user_details = apply_filters( 'mycred_buycred_user_details', $user_details, $this );

			// Required Fields
			$required_fields = apply_filters( 'mycred_buycred_req_fields', array( 'first_name', 'last_name', 'address1', 'city', 'zip', 'state', 'country' ), $this );

			// Show required and optional fields via placeholders
			$required = 'placeholder="required"';
			$optional = 'placeholder="optional"';

?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
		<div class="form-group">
			<label for="billing-first-name"><?php _e( 'First Name', 'mycred' ); ?></label>
			<input type="text" name="billing[first_name]" id="billing-first-name" value="<?php echo $user_details['first_name']; ?>" class="form-control<?php if ( array_key_exists( 'first_name', $this->errors ) ) { echo ' error'; } ?>" <?php if ( in_array( 'first_name', $required_fields ) ) echo $required; else echo $optional; ?> autocomplete="off"  />
		</div>
	</div>
	<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
		<div class="form-group">
			<label for="billing-last-name"><?php _e( 'Last Name', 'mycred' ); ?></label>
			<input type="text" name="billing[last_name]" id="billing-last-name" value="<?php echo $user_details['last_name']; ?>" class="form-control<?php if ( array_key_exists( 'last_name', $this->errors ) ) { echo ' error'; } ?>" <?php if ( in_array( 'last_name', $required_fields ) ) echo $required; else echo $optional; ?>  autocomplete="off"  />
		</div>
	</div>
</div>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<div class="form-group">
			<label for="billing-address1"><?php _e( 'Address Line 1', 'mycred' ); ?></label>
			<input type="text" name="billing[address1]" id="billing-address1" value="<?php echo $user_details['address1']; ?>" class="form-control<?php if ( array_key_exists( 'address1', $this->errors ) ) { echo ' error'; } ?>" <?php if ( in_array( 'address1', $required_fields ) ) echo $required; else echo $optional; ?> autocomplete="off"  />
		</div>
	</div>
</div>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<div class="form-group">
			<label for="billing-address2"><?php _e( 'Address Line 2', 'mycred' ); ?></label>
			<input type="text" name="billing[address2]" id="billing-address2" value="<?php echo $user_details['address2']; ?>" class="form-control<?php if ( array_key_exists( 'address2', $this->errors ) ) { echo ' error'; } ?>" <?php if ( in_array( 'address2', $required_fields ) ) echo $required; else echo $optional; ?> autocomplete="off"  />
		</div>
	</div>
</div>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
		<div class="form-group">
			<label for="billing-city"><?php _e( 'City', 'mycred' ); ?></label>
			<input type="text" name="billing[city]" id="billing-city" value="<?php echo $user_details['city']; ?>" class="form-control<?php if ( array_key_exists( 'city', $this->errors ) ) { echo ' error'; } ?>" <?php if ( in_array( 'city', $required_fields ) ) echo $required; else echo $optional; ?> autocomplete="off"  />
		</div>
	</div>
	<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
		<div class="form-group">
			<label for="billing-zip"><?php _e( 'Zip', 'mycred' ); ?></label>
			<input type="text" name="billing[zip]" id="billing-zip" value="<?php echo $user_details['postcode']; ?>" class="form-control<?php if ( array_key_exists( 'zip', $this->errors ) ) { echo ' error'; } ?>" <?php if ( in_array( 'zip', $required_fields ) ) echo $required; else echo $optional; ?> autocomplete="off"  />
		</div>
	</div>
</div>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<div class="form-group">
			<label for="billing-state"><?php _e( 'State', 'mycred' ); ?></label>
			<input type="text" name="billing[state]" id="billing-state" value="<?php echo $user_details['state']; ?>" class="form-control<?php if ( array_key_exists( 'state', $this->errors ) ) { echo ' error'; } ?>" <?php if ( in_array( 'state', $required_fields ) ) echo $required; else echo $optional; ?> autocomplete="off"  />
		</div>
	</div>
</div>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<div class="form-group">
			<label for="billing-country"><?php _e( 'Country', 'mycred' ); ?></label>

			<?php if ( $country_dropdown !== false ) : ?>

			<select name="billing[country]" id="billing-country" class="form-control">
				<option value=""><?php _e( 'Choose Country', 'mycred' ); ?></option>

				<?php $this->list_option_countries(); ?>

			</select>

			<?php else : ?>

				<input type="text" name="billing[country]" id="billing-country" value="<?php echo $user_details['country']; ?>" class="form-control<?php if ( array_key_exists( 'country', $this->errors ) ) { echo ' error'; } ?>" <?php if ( in_array( 'country', $required_fields ) ) echo $required; else echo $optional; ?> autocomplete="off"  />

			<?php endif; ?>
		</div>
	</div>
</div>
<?php

			do_action( 'mycred_buycred_after_billing_details', $user_details, $this );

		}

		/**
		 * Get Buyers Name
		 * @since 1.6
		 * @version 1.0
		 */
		public function get_buyers_name( $user_id = NULL ) {

			if ( $user_id === NULL ) return '';

			$user = get_userdata( $user_id );
			if ( ! isset( $user->ID ) ) return $user_id;

			if ( ! empty( $user->first_name ) && ! empty( $user->last_name ) )
				$name = $user->first_name . ' ' . $user->last_name;

			elseif ( class_exists( 'WooCommerce' ) )
				$name = get_user_meta( $user_id, 'billing_first_name', true ) . ' ' . get_user_meta( $user_id, 'billing_last_name', true );

			else
				$name = $user->display_name;

			return $name;

		}

		/**
		 * Get Debug
		 * @since 1.0
		 * @version 1.0
		 */
		public function get_debug() {

?>
<h2><?php echo 'Debug'; ?></h2>
<p><span class="description"><?php echo 'Here you can see information that are collected and sent to this gateway. Debug information is only visible for administrators and are intended for troubleshooting / testing of this gateway. Please disable "Sandbox Mode" when you want to take this gateway online.'; ?></span></p>
<table id="gateway-debug">
	<thead>
		<tr>
			<th id="gateway-col-section" class="col-section"><?php echo 'Section'; ?></th>
			<th id="gateway-col-result" class="col-result"><?php echo 'Result'; ?></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td class="col-section"><?php echo 'Payment Status'; ?></td>
			<td class="col-result"><pre><?php print_r( $this->status ); ?></pre></td>
		</tr>
		<tr>
			<td class="col-section"><?php echo 'Request'; ?></td>
			<td class="col-result"><pre><?php print_r( $this->request ); ?></pre></td>
		</tr>
		<tr>
			<td class="col-section"><?php echo 'Gateway Response'; ?></td>
			<td class="col-result"><pre><?php print_r( $this->response ); ?></pre></td>
		</tr>
	</tbody>
</table>
<?php

		}

		/**
		 * Get Errors
		 * @since 1.0
		 * @version 1.0
		 */
		public function get_errors() {

			if ( empty( $this->errors ) ) return;

			$errors = array();
			foreach ( $this->errors as $form_field => $error_message )
				$errors[] = $error_message;

?>
<div class="gateway-error"><?php echo implode( '<br />', $errors ); ?></div>
<?php

		}

		/**
		 * Form Builder with Redirect
		 * Used by gateways that redirects users to a remote processor.
		 * @since 0.1
		 * @version 1.0
		 */
		public function get_page_redirect( $hidden_fields = array(), $location = '' ) {

			$id = str_replace( '-', '_', $this->id );

			// Logo
			if ( empty( $logo_url ) )
				$logo_url = plugins_url( 'images/cred-icon32.png', myCRED_THIS );

			// Hidden Fields
			$hidden_fields = apply_filters( "mycred_{$id}_purchase_fields", $hidden_fields, $this );

?>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<form name="mycred_<?php echo $id; ?>_request" class="form text-center" action="<?php echo $location; ?>" method="post" id="redirect-form">
			<?php foreach ( $hidden_fields as $name => $value ) echo '<input type="hidden" name="' . $name . '" value="' . $value . '" />' . "\n"; ?>
			<img src="<?php echo plugins_url( 'assets/images/loading.gif', MYCRED_PURCHASE ); ?>" alt="Loading" />
			<noscript><input type="submit" name="submit-form" value="<?php printf( __( 'Continue to %s', 'mycred' ), $this->label ); ?>" /></noscript>
			<p id="manual-continue"><a href="javascript:void(0);" onclick="document.mycred_<?php echo $id; ?>_request.submit();return false;"><?php _e( 'Click here if you are not automatically redirected', 'mycred' ); ?></a></p>
		</form>
	</div>
</div>
<script type="text/javascript"><?php if ( $this->sandbox_mode ) echo '//'; ?>setTimeout( "document.mycred_<?php echo $id; ?>_request.submit()",2000 );</script>
<?php

		}
		public function form_with_redirect( $hidden_fields = array(), $location = '', $logo_url = '', $custom_html = '', $sales_data = '' ) {
				$this->get_page_redirect( $hidden_fields, $location, $custom_html, $sales_data );
			}

		/**
		 * POST to data
		 * @since 0.1
		 * @version 1.2
		 */
		public function POST_to_data( $unset = false ) {

			$data = array();
			foreach ( $_POST as $key => $value ) {
				$data[ $key ] = stripslashes( $value );
			}
			if ( $unset )
				unset( $_POST );

			return $data;

		}

		/**
		 * Transaction ID unique
		 * Searches the Log for a given transaction.
		 *
		 * @returns (bool) true if transaction id is unique or false
		 * @since 0.1
		 * @version 1.0.2
		 */
		public function transaction_id_is_unique( $transaction_id = '' ) {

			if ( empty( $transaction_id ) ) return false;

			global $wpdb, $mycred_log_table;

			// Make sure this is a new transaction
			$sql = "
				SELECT * 
				FROM {$mycred_log_table} 
				WHERE ref = %s 
					AND data LIKE %s 
					AND ctype = %s;";

			$gateway = str_replace( '-', '_', $this->id );
			$gateway_id = 'buy_creds_with_' . $gateway;

			$check = $wpdb->get_results( $wpdb->prepare( $sql, $gateway_id, "%:\"" . $transaction_id . "\";%", $this->mycred_type ) );
			if ( $wpdb->num_rows > 0 ) return false;

			return true;

		}

		/**
		 * Create Unique Transaction ID
		 * Returns a unique transaction ID that has no been used by buyCRED yet.
		 * @since 1.4
		 * @version 1.0.1
		 */
		public function create_unique_transaction_id() {

			global $wpdb, $mycred_log_table;

			do {

				$id    = strtoupper( wp_generate_password( 12, false, false ) );
				$query = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$mycred_log_table} WHERE ref LIKE %s AND data LIKE %s;", 'buy_creds_with_%', "%:\"" . $id . "\";%" ) );

			} while ( ! empty( $query ) );
	
			return $id;

		}

		/**
		 * Create Token
		 * Returns a wp nonce
		 * @since 0.1
		 * @version 1.0
		 */
		public function create_token( $user_id = NULL ) {

			return wp_create_nonce( 'mycred-buy-' . $this->id );

		}

		/**
		 * Verify Token
		 * Based on wp_verify_nonce() this function requires the user id used when the token
		 * was created as by default not logged in users would generate different tokens causing us
		 * to fail.
		 * @param $user_id (int) required user id
		 * @param $nonce (string) required nonce to check
		 * @returns true or false
		 * @since 0.1
		 * @version 1.0.1
		 */
		public function verify_token( $user_id, $nonce ) {

			$uid = absint( $user_id );
			$i   = wp_nonce_tick();

			if ( substr( wp_hash( $i . 'mycred-buy-' . $this->id . $uid, 'nonce' ), -12, 10 ) == $nonce )
				return true;
			if ( substr( wp_hash( ( $i - 1 ) . 'mycred-buy-' . $this->id . $uid, 'nonce' ), -12, 10 ) === $nonce )
				return true;

			return false;

		}

		/**
		 * Encode Sales Data
		 * @since 0.1
		 * @version 1.1
		 */
		public function encode_sales_data( $data ) {

			$protect = new myCRED_Protect();
			if ( $protect !== false )
				return $protect->do_encode( $data );

			return $data;

		}

		/**
		 * Decode Sales Data
		 * @since 0.1
		 * @version 1.1
		 */
		public function decode_sales_data( $data ) {

			$protect = new myCRED_Protect();
			if ( $protect !== false )
				return $protect->do_decode( $data );

			return $data;

		}

		/**
		 * Currencies Dropdown
		 * @since 0.1
		 * @version 1.0.2
		 */
		public function currencies_dropdown( $name = '', $js = '' ) {

			$currencies = array(
				'USD' => 'US Dollars',
				'AUD' => 'Australian Dollars',
				'CAD' => 'Canadian Dollars',
				'EUR' => 'Euro',
				'GBP' => 'British Pound Sterling',
				'JPY' => 'Japanese Yen',
				'NZD' => 'New Zealand Dollars',
				'CHF' => 'Swiss Francs',
				'HKD' => 'Hong Kong Dollars',
				'SGD' => 'Singapore Dollars',
				'SEK' => 'Swedish Kronor',
				'DKK' => 'Danish Kroner',
				'PLN' => 'Polish Zloty',
				'NOK' => 'Norwegian Kronor',
				'HUF' => 'Hungarian Forint',
				'CZK' => 'Check Koruna',
				'ILS' => 'Israeli Shekel',
				'MXN' => 'Mexican Peso',
				'BRL' => 'Brazilian Real',
				'MYR' => 'Malaysian Ringgits',
				'PHP' => 'Philippine Pesos',
				'RUB' => 'Russian Ruble',
				'TWD' => 'Taiwan New Dollars',
				'THB' => 'Thai Baht'
			);
			$currencies = apply_filters( 'mycred_dropdown_currencies', $currencies, $this->id );
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
		 * Item Type Dropdown
		 * @since 0.1
		 * @version 1.0
		 */
		public function item_types_dropdown( $name = '' ) {

			$types = array(
				'product'  => 'Product',
				'service'  => 'Service',
				'donation' => 'Donation'
			);
			$types = apply_filters( 'mycred_dropdown_item_types', $types );

			echo '<select name="' . $this->field_name( $name ) . '" id="' . $this->field_id( $name ) . '">';
			echo '<option value="">' . __( 'Select', 'mycred' ) . '</option>';
			foreach ( $types as $code => $cname ) {
				echo '<option value="' . $code . '"';
				if ( isset( $this->prefs[ $name ] ) && $this->prefs[ $name ] == $code ) echo ' selected="selected"';
				echo '>' . $cname . '</option>';
			}
			echo '</select>';

		}

		/**
		 * Countries Dropdown Options
		 * @since 0.1
		 * @version 1.0
		 */
		public function list_option_countries( $selected = '' ) {

			$countries = array (
				"US"  =>  "UNITED STATES",
				"AF"  =>  "AFGHANISTAN",
				"AL"  =>  "ALBANIA",
				"DZ"  =>  "ALGERIA",
				"AS"  =>  "AMERICAN SAMOA",
				"AD"  =>  "ANDORRA",
				"AO"  =>  "ANGOLA",
				"AI"  =>  "ANGUILLA",
				"AQ"  =>  "ANTARCTICA",
				"AG"  =>  "ANTIGUA AND BARBUDA",
				"AR"  =>  "ARGENTINA",
				"AM"  =>  "ARMENIA",
				"AW"  =>  "ARUBA",
				"AU"  =>  "AUSTRALIA",
				"AT"  =>  "AUSTRIA",
				"AZ"  =>  "AZERBAIJAN",
				"BS"  =>  "BAHAMAS",
				"BH"  =>  "BAHRAIN",
				"BD"  =>  "BANGLADESH",
				"BB"  =>  "BARBADOS",
				"BY"  =>  "BELARUS",
				"BE"  =>  "BELGIUM",
				"BZ"  =>  "BELIZE",
				"BJ"  =>  "BENIN",
				"BM"  =>  "BERMUDA",
				"BT"  =>  "BHUTAN",
				"BO"  =>  "BOLIVIA",
				"BA"  =>  "BOSNIA AND HERZEGOVINA",
				"BW"  =>  "BOTSWANA",
				"BV"  =>  "BOUVET ISLAND",
				"BR"  =>  "BRAZIL",
				"IO"  =>  "BRITISH INDIAN OCEAN TERRITORY",
				"BN"  =>  "BRUNEI DARUSSALAM",
				"BG"  =>  "BULGARIA",
				"BF"  =>  "BURKINA FASO",
				"BI"  =>  "BURUNDI",
				"KH"  =>  "CAMBODIA",
				"CM"  =>  "CAMEROON",
				"CA"  =>  "CANADA",
				"CV"  =>  "CAPE VERDE",
				"KY"  =>  "CAYMAN ISLANDS",
				"CF"  =>  "CENTRAL AFRICAN REPUBLIC",
				"TD"  =>  "CHAD",
				"CL"  =>  "CHILE",
				"CN"  =>  "CHINA",
				"CX"  =>  "CHRISTMAS ISLAND",
				"CC"  =>  "COCOS (KEELING) ISLANDS",
				"CO"  =>  "COLOMBIA",
				"KM"  =>  "COMOROS",
				"CG"  =>  "CONGO",
				"CD"  =>  "CONGO, THE DEMOCRATIC REPUBLIC OF THE",
				"CK"  =>  "COOK ISLANDS",
				"CR"  =>  "COSTA RICA",
				"CI"  =>  "COTE D'IVOIRE",
				"HR"  =>  "CROATIA",
				"CU"  =>  "CUBA",
				"CY"  =>  "CYPRUS",
				"CZ"  =>  "CZECH REPUBLIC",
				"DK"  =>  "DENMARK",
				"DJ"  =>  "DJIBOUTI",
				"DM"  =>  "DOMINICA",
				"DO"  =>  "DOMINICAN REPUBLIC",
				"EC"  =>  "ECUADOR",
				"EG"  =>  "EGYPT",
				"SV"  =>  "EL SALVADOR",
				"GQ"  =>  "EQUATORIAL GUINEA",
				"ER"  =>  "ERITREA",
				"EE"  =>  "ESTONIA",
				"ET"  =>  "ETHIOPIA",
				"FK"  =>  "FALKLAND ISLANDS (MALVINAS)",
				"FO"  =>  "FAROE ISLANDS",
				"FJ"  =>  "FIJI",
				"FI"  =>  "FINLAND",
				"FR"  =>  "FRANCE",
				"GF"  =>  "FRENCH GUIANA",
				"PF"  =>  "FRENCH POLYNESIA",
				"TF"  =>  "FRENCH SOUTHERN TERRITORIES",
				"GA"  =>  "GABON",
				"GM"  =>  "GAMBIA",
				"GE"  =>  "GEORGIA",
				"DE"  =>  "GERMANY",
				"GH"  =>  "GHANA",
				"GI"  =>  "GIBRALTAR",
				"GR"  =>  "GREECE",
				"GL"  =>  "GREENLAND",
				"GD"  =>  "GRENADA",
				"GP"  =>  "GUADELOUPE",
				"GU"  =>  "GUAM",
				"GT"  =>  "GUATEMALA",
				"GN"  =>  "GUINEA",
				"GW"  =>  "GUINEA-BISSAU",
				"GY"  =>  "GUYANA",
				"HT"  =>  "HAITI",
				"HM"  =>  "HEARD ISLAND AND MCDONALD ISLANDS",
				"VA"  =>  "HOLY SEE (VATICAN CITY STATE)",
				"HN"  =>  "HONDURAS",
				"HK"  =>  "HONG KONG",
				"HU"  =>  "HUNGARY",
				"IS"  =>  "ICELAND",
				"IN"  =>  "INDIA",
				"ID"  =>  "INDONESIA",
				"IR"  =>  "IRAN, ISLAMIC REPUBLIC OF",
				"IQ"  =>  "IRAQ",
				"IE"  =>  "IRELAND",
				"IL"  =>  "ISRAEL",
				"IT"  =>  "ITALY",
				"JM"  =>  "JAMAICA",
				"JP"  =>  "JAPAN",
				"JO"  =>  "JORDAN",
				"KZ"  =>  "KAZAKHSTAN",
				"KE"  =>  "KENYA",
				"KI"  =>  "KIRIBATI",
				"KP"  =>  "KOREA, DEMOCRATIC PEOPLE'S REPUBLIC OF",
				"KR"  =>  "KOREA, REPUBLIC OF",
				"KW"  =>  "KUWAIT",
				"KG"  =>  "KYRGYZSTAN",
				"LA"  =>  "LAO PEOPLE'S DEMOCRATIC REPUBLIC",
				"LV"  =>  "LATVIA",
				"LB"  =>  "LEBANON",
				"LS"  =>  "LESOTHO",
				"LR"  =>  "LIBERIA",
				"LY"  =>  "LIBYAN ARAB JAMAHIRIYA",
				"LI"  =>  "LIECHTENSTEIN",
				"LT"  =>  "LITHUANIA",
				"LU"  =>  "LUXEMBOURG",
				"MO"  =>  "MACAO",
				"MK"  =>  "MACEDONIA, THE FORMER YUGOSLAV REPUBLIC OF",
				"MG"  =>  "MADAGASCAR",
				"MW"  =>  "MALAWI",
				"MY"  =>  "MALAYSIA",
				"MV"  =>  "MALDIVES",
				"ML"  =>  "MALI",
				"MT"  =>  "MALTA",
				"MH"  =>  "MARSHALL ISLANDS",
				"MQ"  =>  "MARTINIQUE",
				"MR"  =>  "MAURITANIA",
				"MU"  =>  "MAURITIUS",
				"YT"  =>  "MAYOTTE",
				"MX"  =>  "MEXICO",
				"FM"  =>  "MICRONESIA, FEDERATED STATES OF",
				"MD"  =>  "MOLDOVA, REPUBLIC OF",
				"MC"  =>  "MONACO",
				"MN"  =>  "MONGOLIA",
				"MS"  =>  "MONTSERRAT",
				"MA"  =>  "MOROCCO",
				"MZ"  =>  "MOZAMBIQUE",
				"MM"  =>  "MYANMAR",
				"NA"  =>  "NAMIBIA",
				"NR"  =>  "NAURU",
				"NP"  =>  "NEPAL",
				"NL"  =>  "NETHERLANDS",
				"AN"  =>  "NETHERLANDS ANTILLES",
				"NC"  =>  "NEW CALEDONIA",
				"NZ"  =>  "NEW ZEALAND",
				"NI"  =>  "NICARAGUA",
				"NE"  =>  "NIGER",
				"NG"  =>  "NIGERIA",
				"NU"  =>  "NIUE",
				"NF"  =>  "NORFOLK ISLAND",
				"MP"  =>  "NORTHERN MARIANA ISLANDS",
				"NO"  =>  "NORWAY",
				"OM"  =>  "OMAN",
				"PK"  =>  "PAKISTAN",
				"PW"  =>  "PALAU",
				"PS"  =>  "PALESTINIAN TERRITORY, OCCUPIED",
				"PA"  =>  "PANAMA",
				"PG"  =>  "PAPUA NEW GUINEA",
				"PY"  =>  "PARAGUAY",
				"PE"  =>  "PERU",
				"PH"  =>  "PHILIPPINES",
				"PN"  =>  "PITCAIRN",
				"PL"  =>  "POLAND",
				"PT"  =>  "PORTUGAL",
				"PR"  =>  "PUERTO RICO",
				"QA"  =>  "QATAR",
				"RE"  =>  "REUNION",
				"RO"  =>  "ROMANIA",
				"RU"  =>  "RUSSIAN FEDERATION",
				"RW"  =>  "RWANDA",
				"SH"  =>  "SAINT HELENA",
				"KN"  =>  "SAINT KITTS AND NEVIS",
				"LC"  =>  "SAINT LUCIA",
				"PM"  =>  "SAINT PIERRE AND MIQUELON",
				"VC"  =>  "SAINT VINCENT AND THE GRENADINES",
				"WS"  =>  "SAMOA",
				"SM"  =>  "SAN MARINO",
				"ST"  =>  "SAO TOME AND PRINCIPE",
				"SA"  =>  "SAUDI ARABIA",
				"SN"  =>  "SENEGAL",
				"CS"  =>  "SERBIA AND MONTENEGRO",
				"SC"  =>  "SEYCHELLES",
				"SL"  =>  "SIERRA LEONE",
				"SG"  =>  "SINGAPORE",
				"SK"  =>  "SLOVAKIA",
				"SI"  =>  "SLOVENIA",
				"SB"  =>  "SOLOMON ISLANDS",
				"SO"  =>  "SOMALIA",
				"ZA"  =>  "SOUTH AFRICA",
				"GS"  =>  "SOUTH GEORGIA AND THE SOUTH SANDWICH ISLANDS",
				"ES"  =>  "SPAIN",
				"LK"  =>  "SRI LANKA",
				"SD"  =>  "SUDAN",
				"SR"  =>  "SURINAME",
				"SJ"  =>  "SVALBARD AND JAN MAYEN",
				"SZ"  =>  "SWAZILAND",
				"SE"  =>  "SWEDEN",
				"CH"  =>  "SWITZERLAND",
				"SY"  =>  "SYRIAN ARAB REPUBLIC",
				"TW"  =>  "TAIWAN, PROVINCE OF CHINA",
				"TJ"  =>  "TAJIKISTAN",
				"TZ"  =>  "TANZANIA, UNITED REPUBLIC OF",
				"TH"  =>  "THAILAND",
				"TL"  =>  "TIMOR-LESTE",
				"TG"  =>  "TOGO",
				"TK"  =>  "TOKELAU",
				"TO"  =>  "TONGA",
				"TT"  =>  "TRINIDAD AND TOBAGO",
				"TN"  =>  "TUNISIA",
				"TR"  =>  "TURKEY",
				"TM"  =>  "TURKMENISTAN",
				"TC"  =>  "TURKS AND CAICOS ISLANDS",
				"TV"  =>  "TUVALU",
				"UG"  =>  "UGANDA",
				"UA"  =>  "UKRAINE",
				"AE"  =>  "UNITED ARAB EMIRATES",
				"GB"  =>  "UNITED KINGDOM",
				"US"  =>  "UNITED STATES",
				"UM"  =>  "UNITED STATES MINOR OUTLYING ISLANDS",
				"UY"  =>  "URUGUAY",
				"UZ"  =>  "UZBEKISTAN",
				"VU"  =>  "VANUATU",
				"VE"  =>  "VENEZUELA",
				"VN"  =>  "VIET NAM",
				"VG"  =>  "VIRGIN ISLANDS, BRITISH",
				"VI"  =>  "VIRGIN ISLANDS, U.S.",
				"WF"  =>  "WALLIS AND FUTUNA",
				"EH"  =>  "WESTERN SAHARA",
				"YE"  =>  "YEMEN",
				"ZM"  =>  "ZAMBIA",
				"ZW"  =>  "ZIMBABWE"
			);
			$countries = apply_filters( 'mycred_list_option_countries', $countries );

			foreach ( $countries as $code => $cname ) {
				echo '<option value="' . $code . '"';
				if ( $selected == $code ) echo ' selected="selected"';
				echo '>' . $cname . '</option>';
			}

		}

		/**
		 * US States Dropdown Options
		 * @since 0.1
		 * @version 1.0
		 */
		public function list_option_us_states( $selected = '', $non_us = false ) {

			$states = array (
				"AL"  =>  "Alabama",
				"AK"  =>  "Alaska",
				"AZ"  =>  "Arizona",
				"AR"  =>  "Arkansas",
				"CA"  =>  "California",
				"CO"  =>  "Colorado",
				"CT"  =>  "Connecticut",
				"DC"  =>  "D.C.",
				"DE"  =>  "Delaware",
				"FL"  =>  "Florida",
				"GA"  =>  "Georgia",
				"HI"  =>  "Hawaii",
				"ID"  =>  "Idaho",
				"IL"  =>  "Illinois",
				"IN"  =>  "Indiana",
				"IA"  =>  "Iowa",
				"KS"  =>  "Kansas",
				"KY"  =>  "Kentucky",
				"LA"  =>  "Louisiana",
				"ME"  =>  "Maine",
				"MD"  =>  "Maryland",
				"MA"  =>  "Massachusetts",
				"MI"  =>  "Michigan",
				"MN"  =>  "Minnesota",
				"MS"  =>  "Mississippi",
				"MO"  =>  "Missouri",
				"MT"  =>  "Montana",
				"NE"  =>  "Nebraska",
				"NV"  =>  "Nevada",
				"NH"  =>  "New Hampshire",
				"NJ"  =>  "New Jersey",
				"NM"  =>  "New Mexico",
				"NY"  =>  "New York",
				"NC"  =>  "North Carolina",
				"ND"  =>  "North Dakota",
				"OH"  =>  "Ohio",
				"OK"  =>  "Oklahoma",
				"OR"  =>  "Oregon",
				"PA"  =>  "Pennsylvania",
				"RI"  =>  "Rhode Island",
				"SC"  =>  "South Carolina",
				"SD"  =>  "South Dakota",
				"TN"  =>  "Tennessee",
				"TX"  =>  "Texas",
				"UT"  =>  "Utah",
				"VT"  =>  "Vermont",
				"VA"  =>  "Virginia",
				"WA"  =>  "Washington",
				"WV"  =>  "West Virginia",
				"WI"  =>  "Wisconsin",
				"WY"  =>  "Wyoming"
			);
			$states = apply_filters( 'mycred_list_option_us', $states );

			$outside = 'Outside US';
			if ( $non_us == 'top' ) echo '<option value="">' . $outside . '</option>';
			foreach ( $states as $code => $cname ) {
				echo '<option value="' . $code . '"';
				if ( $selected == $code ) echo ' selected="selected"';
				echo '>' . $cname . '</option>';
			}
			if ( $non_us == 'bottom' ) echo '<option value="">' . $outside . '</option>';

		}

		/**
		 * Months Dropdown Options
		 * @since 0.1
		 * @version 1.0
		 */
		public function list_option_months( $selected = '' ) {

			$months = array (
				"01"  =>  __( 'January', 'mycred' ),
				"02"  =>  __( 'February', 'mycred' ),
				"03"  =>  __( 'March', 'mycred' ),
				"04"  =>  __( 'April', 'mycred' ),
				"05"  =>  __( 'May', 'mycred' ),
				"06"  =>  __( 'June', 'mycred' ),
				"07"  =>  __( 'July', 'mycred' ),
				"08"  =>  __( 'August', 'mycred' ),
				"09"  =>  __( 'September', 'mycred' ),
				"10"  =>  __( 'October', 'mycred' ),
				"11"  =>  __( 'November', 'mycred' ),
				"12"  =>  __( 'December', 'mycred' )
			);

			foreach ( $months as $number => $text ) {
				echo '<option value="' . $number . '"';
				if ( $selected == $number ) echo ' selected="selected"';
				echo '>' . $text . '</option>';
			}

		}

		/**
		 * Years Dropdown Options
		 * @since 0.1
		 * @version 1.0
		 */
		public function list_option_card_years( $selected = '', $number = 16 ) {

			$now     = current_time( 'timestamp' );
			$yy      = date( 'y', $now );
			$yyyy    = date( 'Y', $now );
			$count   = 0;
			$options = array();

			while ( $count <= (int) $number ) {
				$count ++;
				if ( $count > 1 ) {
					$yy++;
					$yyyy++;
				}
				$options[ $yy ] = $yyyy;
			}

			foreach ( $options as $key => $value ) {
				echo '<option value="' . $key . '"';
				if ( $selected == $key ) echo ' selected="selected"';
				echo '>' . $value . '</option>';
			}

		}

		/**
		 * IPN - Has Required Fields
		 * @since 1.4
		 * @version 1.0
		 */
		public function IPN_has_required_fields( $required_fields = array(), $method = 'REQUEST' ) {

			$missing = 0;
			foreach ( $required_fields as $field_key ) {
				if ( $method == 'POST' ) {
					if ( ! isset( $_POST[ $field_key ] ) )
						$missing ++;
				}
				elseif ( $method == 'GET' ) {
					if ( ! isset( $_GET[ $field_key ] ) )
						$missing ++;
				}
				elseif ( $method == 'REQUEST' ) {
					if ( ! isset( $_REQUEST[ $field_key ] ) )
						$missing ++;
				}
				else {
					if ( ! isset( $method[ $field_key ] ) )
						$missing ++;
				}
			}

			if ( $missing > 0 )
				$result = false;
			else
				$result = true;

			$result = apply_filters( 'mycred_buycred_IPN_missing', $result, $required_fields, $this->id );

			return $result;

		}

		/**
		 * IPN - Is Valid Call
		 * @since 1.4
		 * @version 1.0
		 */
		public function IPN_is_valid_call() {

			return false;

		}

		/**
		 * IPN - Is Valid Sale
		 * @since 1.4
		 * @version 1.1
		 */
		public function IPN_is_valid_sale( $sales_data_key = '', $cost_key = '', $transactionid_key = '', $method = '' ) {

			if ( $method == 'POST' )
				$post_id = $_POST[ $sales_data_key ];
			elseif ( $method == 'GET' )
				$post_id = $_GET[ $sales_data_key ];
			else
				$post_id = $_REQUEST[ $sales_data_key ];

			$pending_payment = $this->get_pending_payment( $post_id );
			if ( $pending_payment === false ) return false;

			$result = true;

			if ( $method == 'POST' )
				$price = $_POST[ $cost_key ];
			elseif ( $method == 'GET' )
				$price = $_GET[ $cost_key ];
			else
				$price = $_REQUEST[ $cost_key ];

			if ( $result === true && $pending_payment['cost'] != $price ) {
				$result = false;
			}

			if ( $result === true && isset( $this->prefs['currency'] ) && $this->prefs['currency'] != $pending_payment['currency'] ) {
				$result = false;
			}

			if ( $method == 'POST' )
				$transaction_id = $_POST[ $transactionid_key ];
			elseif ( $method == 'GET' )
				$transaction_id = $_GET[ $transactionid_key ];
			else
				$transaction_id = $_REQUEST[ $transactionid_key ];

			if ( $result === true && ! $this->transaction_id_is_unique( $transaction_id ) ) {
				$result = false;
			}

			$result = apply_filters( 'mycred_buycred_valid_sale', $result, $sales_data_key, $cost_key, $transactionid_key, $method, $this );

			if ( $result === true )
				return $decoded_data;
		
			return $result;

		}

		/**
		 * Complete Payment
		 * @since 1.4
		 * @version 1.4
		 */
		public function complete_payment( $pending_payment = NULL, $transaction_id = '' ) {

			if ( $pending_payment === NULL ) return false;

			$reply      = false;
			$mycred     = mycred( $pending_payment->point_type );

			$reference  = 'buy_creds_with_' . str_replace( array( ' ', '-' ), '_', $this->id );
			$sales_data = array(
				'to'       => $pending_payment->recipient_id,
				'from'     => $pending_payment->buyer_id,
				'amount'   => $pending_payment->amount,
				'cost'     => $pending_payment->cost,
				'currency' => $pending_payment->currency,
				'ctype'    => $pending_payment->point_type
			);
			$data       = array( 'ref_type' => 'user', 'txn_id' => $transaction_id, 'sales_data' => implode( '|', $sales_data ) );

			if ( ! $mycred->has_entry( $reference, $pending_payment->buyer_id, $pending_payment->recipient_id, $data, $pending_payment->point_type ) ) {

				add_filter( 'mycred_get_email_events', array( $this, 'email_notice' ), 10, 2 );
				$reply = $mycred->add_creds(
					$reference,
					$pending_payment->recipient_id,
					$pending_payment->amount,
					$this->get_log_entry( $pending_payment->recipient_id, $pending_payment->buyer_id ),
					$pending_payment->buyer_id,
					$data,
					$pending_payment->point_type
				);
				remove_filter( 'mycred_get_email_events', array( $this, 'email_notice' ), 10, 2 );

			}

			return apply_filters( 'mycred_buycred_complete_payment', $reply, $transaction_id, $this );

		}

		/**
		 * Email Notice Add-on Support
		 * @since 1.5.4
		 * @version 1.0
		 */
		public function email_notice( $events, $request ) {

			if ( substr( $request['ref'], 0, 15 ) == 'buy_creds_with_' )
				$events[] = 'buy_creds|positive';

			return $events;

		}

		/**
		 * Trash Pending Payment
		 * @since 1.5.3
		 * @version 1.0.1
		 */
		public function trash_pending_payment( $payment_id ) {

			return buycred_trash_pending_payment( $payment_id );

		}

	}
endif;
