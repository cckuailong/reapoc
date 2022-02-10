<?php
if ( ! defined( 'MYCRED_CASHCRED' ) ) exit;

/**
 * Get Gateways
 * @since 1.8
 * @version 1.0
 */
if ( ! function_exists( 'mycred_get_cashcred_gateways' ) ) :
	function mycred_get_cashcred_gateways() {

		$installed = array();

		// Bank Transfers
		$installed['bank'] = array(
			'title'         => __( 'Bank Transfer', 'mycred' ),
			'documentation' => 'http://codex.mycred.me/chapter-iii/buycred/payment-gateways/bank-transfers/',
			'callback'      => array( 'myCRED_cashcred_Bank_Transfer' ),
			'icon'          => 'dashicons-admin-generic',
			'sandbox'       => false,
			'external'      => false,
			'custom_rate'   => true
		);

		return apply_filters( 'mycred_cashcred_setup_gateways', $installed );

	}
endif;

/**
 * Get buyCRED Setup
 * @since 1.8
 * @version 1.0
 */
if ( ! function_exists( 'cashcred_get_requested_gateway_id' ) ) :
	function cashcred_get_requested_gateway_id() {

		$gateway_id = false;
			
		if ( isset( $_REQUEST['cashcred_pay_method'] ) && is_user_logged_in() )
			$gateway_id = trim( $_REQUEST['cashcred_pay_method'] );	

		return apply_filters( 'mycred_gateway_id', $gateway_id );

	}
endif;

if ( ! function_exists( 'cashcred_register_fields' ) ) :
	function cashcred_register_fields($type,$name) {
			
		return  'cashcred_user_settings['.$type.']['.$name.']';
 
	}
endif;

if ( ! function_exists( 'cashcred_get_user_payment_details' ) ) :
	function cashcred_get_user_payment_details() {

		$user_id = get_current_user_id();

		if ( is_admin() ) {
			$post_id = get_the_ID();
			$user_id = check_site_get_post_meta( $post_id, 'from', true );
		}
		return mycred_get_user_meta( $user_id, 'cashcred_user_settings', '', true );
 
	}
endif;

/**
 * Display Messages 
 * @since 1.9
 * @version 1.0
 *
 * $type = error, success
 */
if ( ! function_exists( 'cashcred_display_message' ) ) :
	function cashcred_display_message() {

		$cashcred_notice = mycred_get_user_meta( get_current_user_id(), 'cashcred_notice', '', true ); 
		
		if( ! empty( $cashcred_notice ) ) {?> 
			<p class="cashcred-notice"> 
				<?php echo $cashcred_notice; ?> 
			</p>
			<?php

			if ( ! isset( $_POST['cashcred_withdraw_wpnonce'] ) )
				mycred_delete_user_meta( get_current_user_id(), 'cashcred_notice' );
		}
 
	}
endif;

/**
 * Return list of Gateways.
 * If a $gateway empty return all active gateways.
 * @since 1.9
 * @version 1.0
 */
if ( ! function_exists( 'cashcred_get_usable_gateways' ) ) :
	function cashcred_get_usable_gateways( $gateways ) {

		global $cashcred_instance;

		$gateways_list = array();

		if ( empty( $cashcred_instance->active ) ) $gateways_list;

		if ( empty( $gateways ) ) {

			$gateways_list = $cashcred_instance->active;
			
		}
		else {

			$gateways = explode( ',', $gateways );

			foreach ( $gateways as $gateway_id ) {

				if ( array_key_exists( $gateway_id, $cashcred_instance->active ) ) 
					$gateways_list[ $gateway_id ] = $cashcred_instance->active[ $gateway_id ];

			}

		}

		return $gateways_list;

	}
endif;

/**
 * Return list of usable Point Types for the given user.
 * If a $types empty return all point types.
 * @since 1.9
 * @version 1.0
 */
if ( ! function_exists( 'cashcred_get_point_types' ) ) :
	function cashcred_get_point_types( $types, $user_id ) {

		global $mycred_types;

		$point_types = array();

		$available_types = array_keys( $mycred_types );

		if ( ! empty( $types ) ) {
			
			$types = explode( ',', $types );
			$available_types = array_intersect( $available_types, $types );

		}

		foreach ( $available_types as $type_id ) {
			
			$mycred = mycred( $type_id );
			
			if ( ! $mycred->exclude_user( $user_id ) ) {
				
				$point_types[ $type_id ] = $mycred;

			}

		}

		return $point_types;

	}
endif;

/**
 * Return list of Point Types Which user have balance.
 * If a $types empty return all point types.
 * @since 1.9
 * @version 1.0
 */
if ( ! function_exists( 'cashcred_is_user_have_balances' ) ) :
	function cashcred_is_user_have_balances( $point_types, $user_id ) {

		$usable_point_types = array();

		foreach ( $point_types as $point_type ) {
			if ( $point_type->get_users_balance( $user_id ) > 0 ) {
				$usable_point_types[ $point_type->cred_id ] = $point_type;
			}
		}

		return $usable_point_types;

	}
endif;

/**
 * Get Withdraw requests.
 * $status | Pending, Approved, Cancelled
 * @since 1.9
 * @version 1.0
 */
if ( ! function_exists( 'cashcred_get_withdraw_requests' ) ) :
	function cashcred_get_withdraw_requests( $status = 'Pending' ) {

		$meta_query = array(
		    array(
			   'key'     => 'status',
			   'value'   => $status,
			   'compare' => '='
		    ),
		    array(
			   'key'     => 'from',
			   'value'   => get_current_user_id(),
			   'compare' => '='
		    )
		);	

		if ( $status == 'Pending' ) {
			array_push( 
				$meta_query, 
				array(
				   'key' => 'points',
				   'value' => '',
				   'compare' => '!='
			    )
			);
		}

		$args = array( 
			'post_type' 	 => 'cashcred_withdrawal',
			'post_status'	 => 'publish' ,
			'posts_per_page' => -1,
			'paged' 		 => get_query_var('paged'),
			'meta_query'     => $meta_query		
		);
			
		return get_posts( $args );

	}
endif;

/**
 * Get Pending Payment
 * @since 1.7
 * @version 1.0
 */
if ( ! function_exists( 'cashcred_get_pending_payment_id' ) ) :
	function cashcred_get_pending_payment_id( $payment_id = NULL ) {

		if ( $payment_id === NULL || $payment_id == '' ) return false;

		// In case we are using the transaction ID instead of the post ID.
		$post_id = false;
		if ( ! is_numeric( $payment_id ) ) {

			$post = mycred_get_page_by_title( strtoupper( $payment_id ), OBJECT, 'buycred_payment' );
			if ( $post === NULL ) return false;

			$post_id = $post->ID;

		}
		else {
			$post_id = absint( $payment_id );
		}

		return $post_id;

	}
endif;

/**
 * Get Pending Payment
 * @since 1.7
 * @version 1.0
 */
if ( ! function_exists( 'cashcred_get_payment_settings' ) ) :
	function cashcred_get_payment_settings( $payment_id = NULL ) {
	    
		// Construct fake pending object ( when no pending payment object exists )
		if ( is_array( $payment_id ) ) {

			$pending_payment                 = new StdClass();
			$pending_payment->payment_id     = false;
			$pending_payment->public_id      = $payment_id['public_id'];
			$pending_payment->point_type     = $payment_id['point_type'];
			$pending_payment->points         = $payment_id['points'];
			$pending_payment->cost           = $payment_id['cost'];
			$pending_payment->currency       = $payment_id['currency'];
			$pending_payment->gateway_id     = $payment_id['gateway_id'];
			$pending_payment->transaction_id = $payment_id['transaction_id'];

		}

		else {

			$payment_id = cashcred_get_pending_payment_id( $payment_id );

			if ( $payment_id === false ) return false;

			$pending_payment                 = new StdClass();
			$pending_payment->payment_id     = absint( $payment_id );
			$pending_payment->public_id      = get_the_title( $payment_id );
			$pending_payment->point_type     = check_site_get_post_meta( $payment_id, 'point_type', true );
			$pending_payment->points         = check_site_get_post_meta( $payment_id, 'points', true );
			$pending_payment->cost           = check_site_get_post_meta( $payment_id, 'cost', true );
			$pending_payment->currency       = check_site_get_post_meta( $payment_id, 'currency', true );
			$pending_payment->gateway_id     = check_site_get_post_meta( $payment_id, 'gateway', true );
			$pending_payment->transaction_id = $pending_payment->public_id;

		}

		return apply_filters( 'cashcred_get_payment_settings', $pending_payment, $payment_id );

	}
endif;

/**
 * Add Pending Comment
 * @since 1.7
 * @version 1.1
 */
if ( ! function_exists( 'cashcred_add_comment' ) ) :
	function cashcred_add_comment( $payment_id = NULL, $comment = NULL, $time = null ) {

		if ( ! MYCRED_CASHCRED_PENDING_COMMENTS ) return true;

		$post_id = cashcred_get_pending_payment_id( $payment_id );
		if ( $post_id === false ) return false;

		global $mycred_modules;

		if ( $time === null || $time == 'now' )
			$time = current_time( 'mysql' );

		$author       = 'cashcred';
		$gateway      = mycred_get_post_meta( $post_id, 'gateway', true );
		$gateways     = mycred_get_cashcred_gateways();
		$author_url   = sprintf( 'buyCRED: %s %s', __( 'Unknown Gateway', 'mycred' ), $gateway );
		$author_email = apply_filters( 'mycred_buycred_comment_email', 'buycred-service@mycred.me' );

		if ( array_key_exists( $gateway, $gateways ) )
			$author = sprintf( 'buyCRED: %s %s', $gateways[ $gateway ]['title'], __( 'Gateway', 'mycred' ) );

		return wp_insert_comment( array(
			'comment_post_ID'      => $post_id,
			'comment_author'       => $author,
			'comment_author_email' => $author_email,
			'comment_content'      => $comment,
			'comment_type'         => 'cashcred',
			'comment_author_IP'    => $_SERVER['REMOTE_ADDR'],
			'comment_date'         => $time,
			'comment_approved'     => 1,
			'user_id'              => 0
		) );

	}
endif;

/**
 * buyCRED Gateway Constructor
 * @since 1.7
 * @version 1.1
 */
if ( ! function_exists( 'cashcred_gateway' ) ) :
	function cashcred_gateway( $gateway_id = NULL ) {

		global $cashcred_gateway, $mycred_modules;
	 
		if ( isset( $cashcred_gateway )
			&& ( $cashcred_gateway instanceof myCRED_Cash_Payment_Gateway )
			&& ( $gateway_id === $cashcred_gateway->id )
		) {
			return $cashcred_gateway;
		}

		$cashcred_gateway = false;
		$installed       = $mycred_modules['solo']['cashcred']->get();
		if ( array_key_exists( $gateway_id, $installed ) ) {

			$class   = $installed[ $gateway_id ]['callback'][0];

			// Construct Gateway
			$cashcred_gateway = new $class( $mycred_modules['solo']['cashcred']->gateway_prefs );

		}

		return $cashcred_gateway;

	}
endif;

/**
 * CashCred_Gateway_Fields class
 * @since 2.0
 * @version 1.0
 */
if ( ! class_exists( 'CashCred_Gateway_Fields' ) ) :
	class CashCred_Gateway_Fields {

		public $gateway_name;

		private $gateway_fields;

		/**
		 * Construct
		 * @since 2.0
		 * @version 1.0
		 */
		public function __construct( $name, $fields ) {

			$this->gateway_name   = $name;
			$this->gateway_fields = $fields;

			$this->populate();

		}

		private function populate() {

			$user_payment_details = cashcred_get_user_payment_details();

			foreach( $this->gateway_fields as $gateway_field_id => $gateway_field_data ){

				$field_value = '';

				if ( ! empty( $user_payment_details[ $this->gateway_name ][ $gateway_field_id ] ) ) {
					$field_value = $user_payment_details[ $this->gateway_name ][ $gateway_field_id ];
				}

      			$this->{ $gateway_field_id } = $field_value;
			}

		}

		public function field_name( $field_name ) {

			return "cashcred_user_settings[$this->gateway_name][$field_name]";

		}

		public function generate_form() {

			foreach ( $this->gateway_fields as $gateway_field_id => $gateway_field_data ): ?>

			 	<div class="form-group">  
					<label><?php echo $gateway_field_data['lable']; ?></label>
					<input type="text" name="<?php echo $this->field_name( $gateway_field_id );?>" class="<?php echo $gateway_field_data['classes']; ?>" placeholder="<?php echo $gateway_field_data['placeholder']; ?>" value="<?php echo $this->{$gateway_field_id};?>">
				</div>

			<?php
			endforeach;
		}

	}
endif;

if ( ! function_exists( 'mycred_get_cashcred_settings' ) ) :
	function mycred_get_cashcred_settings() {

		$defaults = array(
			'debugging' => 'disable'
		);

		$settings = mycred_get_addon_settings( 'cashcreds' );
		$settings = wp_parse_args( $settings, $defaults );

		return apply_filters( 'mycred_get_cashcred_settings', $settings );

	}
endif;

/**
* add postmeta by checking multisite and current blog
* @param $post_id post id
* @param $key meta key
* @param bool $unique
* @return mixed
*/
if( !function_exists('check_site_add_post_meta') ):
    function check_site_add_post_meta( $post_id, $meta_key, $meta_value, $unique = false ) {
	    if(is_multisite() AND !is_main_site() AND mycred_override_settings()) {
	        return add_post_meta( $post_id, $meta_key, $meta_value, $unique );
	    }
	    else {
	        return mycred_add_post_meta( $post_id, $meta_key, $meta_value, $unique );
	    }
	}
endif;

/**
* Returns postmeta by checking multisite and current blog
* @param $post_id post id
* @param $key meta key
* @param bool $single
* @return mixed
*/
if( ! function_exists('check_site_get_post_meta') ):
    function check_site_get_post_meta( $post_id, $key, $single = false ) {
	    if( is_multisite() AND !is_main_site() AND mycred_override_settings() ) {
	        return get_post_meta( $post_id, $key, $single );
	    }
	    else {
	        return mycred_get_post_meta( $post_id, $key, $single );
	    }
	}
endif;

/**
* cashCred get user's settings
*/
if(!function_exists('cashcred_get_user_settings')):
    function cashcred_get_user_settings() {
        $check = '';
		$cashcred_user_setting = '';
		
		if( is_multisite() AND !is_main_site() AND mycred_override_settings() ) {
		    $check = true;
		}
		else {
		    $check = false;
	    }

		if( $check ) {
		    return 'cashcred_user_settings_' . get_current_blog_id();
		}
		else {
		    return 'cashcred_user_settings';
		}
    }
endif;

/**
*Checks site is multisite or not and update post meta
* @param $post_id post id
* @param $key meta key
* @param $new_value new meta value
* @return mixed
*/
if( ! function_exists( 'check_site_update_post_meta' ) ):
    function check_site_update_post_meta( $post_id, $meta_key, $new_value ) {
        if(is_multisite() AND !is_main_site() AND mycred_override_settings()) {
	        return update_post_meta( $post_id, $meta_key, $new_value );
	    }
	    else {
	        return mycred_update_post_meta( $post_id, $meta_key, $new_value );
	    }
    }
endif;

/**
* Update payment status
*/
if( ! function_exists( 'mycred_cashcred_update_status' ) ):
    function mycred_cashcred_update_status( $post_id, $meta_key, $meta_value ) {
	    check_site_update_post_meta( $post_id, $meta_key, $meta_value );
	    
        $mycred_pref_cashcreds = mycred_get_option( 'mycred_pref_cashcreds',false  );

		$point_type = check_site_get_post_meta( $post_id, 'point_type', true );
		$points = check_site_get_post_meta( $post_id, 'points', true );
		$cashcred_pay_method = check_site_get_post_meta( $post_id, 'gateway' , true );
		
		$user_id = get_post_field( 'post_author', $post_id );
		$user_balance = mycred_get_users_balance( $user_id, $point_type );
		$payment_withdrawal_request = array(
			'point_type' 			=> $point_type,
			'cashcred_pay_method' 	=> $cashcred_pay_method,
			'points' 				=> $points,
			'user_balance'			=> $user_balance,
			'user_id'				=> $user_id,
			'post_id'				=> $post_id
		);
		
	   do_action( 'mycred_after_payment_request', $payment_withdrawal_request , $meta_value );
	}
endif;