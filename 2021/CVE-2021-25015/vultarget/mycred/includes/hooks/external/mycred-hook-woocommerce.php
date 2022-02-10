<?php
if ( ! defined( 'myCRED_VERSION' ) ) exit;

/**
 * WooCommerce Setup
 * @since 1.5
 * @version 1.1
 */
if ( ! function_exists( 'mycred_load_woocommerce_reward' ) ) :
	function mycred_load_woocommerce_reward() {

		if ( ! class_exists( 'WooCommerce' ) ) return;

		add_filter( 'mycred_comment_gets_cred',                      'mycred_woo_remove_review_from_comments', 10, 2 );
		add_action( 'add_meta_boxes_product',                        'mycred_woo_add_product_metabox' );
		add_action( 'save_post_product',                             'mycred_woo_save_reward_settings' );
		add_action( 'woocommerce_payment_complete',                  'mycred_woo_payout_rewards' );
		add_action( 'woocommerce_order_status_completed',            'mycred_woo_payout_rewards' );
		add_action( 'woocommerce_product_after_variable_attributes', 'mycred_woo_add_product_variation_detail', 10, 3 );
		add_action( 'woocommerce_save_product_variation',            'mycred_woo_save_product_variation_detail' );
		add_filter( 'mycred_run_this',                               'mycred_woo_refund_points' );

	}
endif;
add_action( 'mycred_load_hooks', 'mycred_load_woocommerce_reward', 90 );

/**
 * Remove Reviews from Comment Hook
 * Prevents the comment hook from granting points twice for a review.
 * @since 1.6.3
 * @version 1.0
 */
if ( ! function_exists( 'mycred_woo_remove_review_from_comments' ) ) :
	function mycred_woo_remove_review_from_comments( $reply, $comment ) {

		if ( mycred_get_post_type( $comment->comment_post_ID ) == 'product' ) return false;

		return $reply;

	}
endif;

/**
 * Add Reward Metabox
 * @since 1.5
 * @version 1.0.1
 */
if ( ! function_exists( 'mycred_woo_add_product_metabox' ) ) :
	function mycred_woo_add_product_metabox() {
		$product = wc_get_product( get_the_ID() );
		if( $product->is_type( 'variable' ) != 'variable' ) {	
			add_meta_box(
				'mycred_woo_sales_setup',
				mycred_label(),
				'mycred_woo_product_metabox',
				'product',
				'side',
				'high'
			);
		}

	}
endif;

/**
 * Product Metabox
 * @since 1.5
 * @version 1.1
 */
if ( ! function_exists( 'mycred_woo_product_metabox' ) ) :
	function mycred_woo_product_metabox( $post ) {
		
		$product = wc_get_product( get_the_ID() );
		if( $product->is_type( 'variable' ) != 'variable' ) {	
			if ( ! current_user_can( apply_filters( 'mycred_woo_reward_cap', 'edit_others_posts' ) ) ) return;

			$types = mycred_get_types();
			$prefs = (array) mycred_get_post_meta( $post->ID, 'mycred_reward', true );

			foreach ( $types as $point_type => $point_type_label ) {
				if ( ! array_key_exists( $point_type, $prefs ) )
					$prefs[ $point_type ] = '';
			}

			$count = 0;
			$cui   = get_current_user_id();
			foreach ( $types as $point_type => $point_type_label ) {

				$count ++;
				$mycred = mycred( $point_type );

				if ( ! $mycred->user_is_point_admin( $cui ) ) continue;

				$setup = $prefs[ $point_type ];

	?>
	<p class="<?php if ( $count == 1 ) echo 'first'; ?>"><label for="mycred-reward-purchase-with-<?php echo $point_type; ?>"><input class="toggle-mycred-reward" data-id="<?php echo $point_type; ?>" <?php if ( $setup != '' ) echo 'checked="checked"'; ?> type="checkbox" name="mycred_reward[<?php echo $point_type; ?>][use]" id="mycred-reward-purchase-with-<?php echo $point_type; ?>" value="1" /> <?php echo $mycred->template_tags_general( __( 'Reward with %plural%', 'mycred' ) ); ?></label></p>
	<div class="mycred-woo-wrap" id="reward-<?php echo $point_type; ?>" style="display:<?php if ( $setup == '' ) echo 'none'; else echo 'block'; ?>">
		<label><?php echo $mycred->plural(); ?></label> <input type="text" size="8" name="mycred_reward[<?php echo $point_type; ?>][amount]" value="<?php echo esc_attr( $setup ); ?>" placeholder="<?php echo $mycred->zero(); ?>" />
	</div>
	<?php

			}
		}

?>
<script type="text/javascript">
jQuery(function($) {

	$( '.toggle-mycred-reward' ).click(function(){
		var target = $(this).attr( 'data-id' );
		$( '#reward-' + target ).toggle();
	});

});
</script>
<style type="text/css">
#mycred_woo_sales_setup .inside { margin: 0; padding: 0; }
#mycred_woo_sales_setup .inside > p { padding: 12px; margin: 0; border-top: 1px solid #ddd; }
#mycred_woo_sales_setup .inside > p.first { border-top: none; }
#mycred_woo_sales_setup .inside .mycred-woo-wrap { padding: 6px 12px; line-height: 27px; text-align: right; border-top: 1px solid #ddd; background-color: #F5F5F5; }
#mycred_woo_sales_setup .inside .mycred-woo-wrap label { display: block; font-weight: bold; float: left; }
#mycred_woo_sales_setup .inside .mycred-woo-wrap input { width: 50%; }
#mycred_woo_sales_setup .inside .mycred-woo-wrap p { margin: 0; padding: 0 12px; font-style: italic; text-align: center; }
#mycred_woo_vaiation .box { display: block; float: left; width: 49%; margin-right: 1%; margin-bottom: 12px; }
#mycred_woo_vaiation .box input { display: block; width: 100%; }
</style>
<?php

	}
endif;

/**
 * Add Reward details for Variations
 * @since 1.7.6
 * @version 1.0
 */
if ( ! function_exists( 'mycred_woo_add_product_variation_detail' ) ) :
	function mycred_woo_add_product_variation_detail( $loop, $variation_data, $variation ) {

		$types   = mycred_get_types();
		$user_id = get_current_user_id();
		$prefs   = (array) mycred_get_post_meta( $variation->ID, '_mycred_reward', true );

		foreach ( $types as $point_type => $point_type_label ) {
			if ( ! array_key_exists( $point_type, $prefs ) )
				$prefs[ $point_type ] = '';
		}

?>
<style type="text/css">
#mycred_woo_sales_setup .inside { margin: 0; padding: 0; }
#mycred_woo_sales_setup .inside > p { padding: 12px; margin: 0; border-top: 1px solid #ddd; }
#mycred_woo_sales_setup .inside > p.first { border-top: none; }
#mycred_woo_sales_setup .inside .mycred-woo-wrap { padding: 6px 12px; line-height: 27px; text-align: right; border-top: 1px solid #ddd; background-color: #F5F5F5; }
#mycred_woo_sales_setup .inside .mycred-woo-wrap label { display: block; font-weight: bold; float: left; }
#mycred_woo_sales_setup .inside .mycred-woo-wrap input { width: 50%; }
#mycred_woo_sales_setup .inside .mycred-woo-wrap p { margin: 0; padding: 0 12px; font-style: italic; text-align: center; }
#mycred_woo_vaiation .box { display: block; float: left; width: 49%; margin-right: 1%; margin-bottom: 12px; }
#mycred_woo_vaiation .box input { display: block; width: 100%; }
</style>
<div class="" id="mycred_woo_vaiation">
<?php

		foreach ( $types as $point_type => $point_type_label ) {

			$mycred = mycred( $point_type );

			if ( ! $mycred->user_is_point_admin( $user_id ) ) continue;

			$id = 'mycred-rewards-variation-' . $variation->ID . str_replace( '_', '-', $point_type );

?>
		<div class="box">
			<label for="<?php echo $id; ?>"><?php echo $mycred->template_tags_general( __( 'Reward with %plural%', 'mycred' ) ); ?></label>
			<input type="text" name="_mycred_reward[<?php echo $variation->ID; ?>][<?php echo $point_type; ?>]" id="<?php echo $id; ?>" class="input-text" placeholder="<?php _e( 'Leave empty for no rewards', 'mycred' ); ?>" value="<?php echo esc_attr( $prefs[ $point_type ] ); ?>" />
		</div>
<?php

		}

?>
</div>
<?php

	}
endif;

/**
 * WooCommerce Points Refund
 * @since 1.7.9.8
 * @version 1.0
 */
if ( ! function_exists( 'mycred_woo_refund_points' ) ) :
	function mycred_woo_refund_points( $request ) {

		if( $request['ref'] == 'woocommerce_refund' )
			$request['amount'] = abs($request['amount']);
		
		return $request;
	}
endif;

/**
 * Save Reward Setup
 * @since 1.5
 * @version 2.0.0
 */
if ( ! function_exists( 'mycred_woo_save_reward_settings' ) ) :
	function mycred_woo_save_reward_settings( $post_id ) {
		
		//Works only for multisite
		$override = ( is_multisite() && mycred_override_settings() && ! mycred_is_main_site() );
		
		$post_type = '';
		
		if( $override )
			$post_type = get_post_type( $post_id );
		else
			$post_type = mycred_get_post_type( $post_id );
		
		if ( ! isset( $_POST['mycred_reward'] ) || empty( $_POST['mycred_reward'] ) || $post_type != 'product' ) return;

		$new_setup = array();
		foreach ( $_POST['mycred_reward'] as $point_type => $setup ) {

			if ( empty( $setup ) ) continue;

			$mycred = mycred( $point_type );
			if ( array_key_exists( 'use', $setup ) && $setup['use'] == 1 )
				$new_setup[ $point_type ] = $mycred->number( $setup['amount'] );

		}

		if ( empty( $new_setup ) )
			mycred_delete_post_meta( $post_id, 'mycred_reward' );
		else
			mycred_update_post_meta( $post_id, 'mycred_reward', $new_setup );

	}
endif;

/**
 * Save Reward for Variations
 * @since 1.7.6
 * @version 1.0
 */
if ( ! function_exists( 'mycred_woo_save_product_variation_detail' ) ) :
	function mycred_woo_save_product_variation_detail( $post_id ) {

		if ( ! isset( $_POST['_mycred_reward'] ) || empty( $_POST['_mycred_reward'] ) || ! array_key_exists( $post_id, $_POST['_mycred_reward'] ) ) return;

		$new_setup = array();
		foreach ( $_POST['_mycred_reward'][ $post_id ] as $point_type => $value ) {

			$value  = sanitize_text_field( $value );
			if ( empty( $value ) ) continue;

			$mycred = mycred( $point_type );
			$value  = $mycred->number( $value );
			if ( $value === $mycred->zero() ) continue;

			$new_setup[ $point_type ] = $value;

		}

		if ( empty( $new_setup ) )
			mycred_delete_post_meta( $post_id, '_mycred_reward' );
		else
			mycred_update_post_meta( $post_id, '_mycred_reward', $new_setup );

	}
endif;

/**
 * Register WooCommerce Purchase Reward refrence
 * @since 2.1
 * @version 1.0
 */
function mycred_register_woo_reward_ref( $list ) {

    $list['reward'] = 'WooCommerce Purchase reaward';
    return $list;

}
add_filter( 'mycred_all_references', 'mycred_register_woo_reward_ref' );

/**
 * Payout Rewards
 * @since 1.5
 * @version 1.2
 */
if ( ! function_exists( 'mycred_woo_payout_rewards' ) ) :
	function mycred_woo_payout_rewards( $order_id ) {
	 
		// Get Order
		$order    = wc_get_order( $order_id );

		global $woocommerce;

		$paid_with = ( version_compare( $woocommerce->version, '3.0', '>=' ) ) ? $order->get_payment_method() : $order->payment_method;
		$buyer_id  = ( version_compare( $woocommerce->version, '3.0', '>=' ) ) ? $order->get_user_id() : $order->user_id;

		// If we paid with myCRED we do not award points by default
		if ( $paid_with == 'mycred' && apply_filters( 'mycred_woo_reward_mycred_payment', false, $order ) === false )
			return;

		// Get items
		$items    = $order->get_items();
		$types    = mycred_get_types();

		// Loop through each point type
		foreach ( $types as $point_type => $point_type_label ) {

			// Load type
			$mycred = mycred( $point_type );

			// Check for exclusions
			if ( $mycred->exclude_user( $buyer_id ) ) continue;

			// Calculate reward
			$payout = $mycred->zero();
			foreach ( $items as $item ) {

				// Get the product ID or the variation ID
				$product_id    = absint( $item['product_id'] );
				$variation_id  = absint( $item['variation_id'] );
				$reward_amount = mycred_get_woo_product_reward( $product_id, $variation_id, $point_type );

				// Reward can not be empty or zero
				if ( $reward_amount != '' && $reward_amount != 0 )
					$payout = ( $payout + ( $mycred->number( $reward_amount ) * $item['qty'] ) );

			}

			// We can not payout zero points
			if ( $payout === $mycred->zero() ) continue;

			// Let others play with the reference and log entry
			$reference = apply_filters( 'mycred_woo_reward_reference', 'reward', $order_id, $point_type );
			$log       = apply_filters( 'mycred_woo_reward_log',       '%plural% reward for store purchase', $order_id, $point_type );

			// Make sure we only get points once per order
			if ( ! $mycred->has_entry( $reference, $order_id, $buyer_id ) ) {

				// Execute
				$mycred->add_creds(
					$reference,
					$buyer_id,
					$payout,
					$log,
					$order_id,
					array( 'ref_type' => 'post' ),
					$point_type
				);

			}

		}

	}
endif;

/**
 * Get Product Reward
 * Returns either an array of point types and the reward value set for each or
 * the value set for a given point type. Will check for variable product rewards as well.
 * @since 1.7.6
 * @version 1.0
 */
if ( ! function_exists( 'mycred_get_woo_product_reward' ) ) :
	function mycred_get_woo_product_reward( $product_id = NULL, $variation_id = NULL, $requested_type = false ) {

		$product_id   = absint( $product_id );
		$types        = mycred_get_types();
		$meta_key     = 'mycred_reward';
		$is_variable  = false;
		if ( $product_id === 0 ) return false;

		if ( function_exists( 'wc_get_product' ) ) { 

			$product  = wc_get_product( $product_id );

			// For variations, we need a variation ID
			if ( $product->is_type( 'variable' ) && $variation_id !== NULL && $variation_id > 0 ) {
				$reward_setup        = (array) mycred_get_post_meta( $variation_id, '_mycred_reward', true );
				$parent_reward_setup = (array) mycred_get_post_meta( $product_id, 'mycred_reward', true );
			}
			else {
				$reward_setup        = (array) mycred_get_post_meta( $product_id, 'mycred_reward', true );
				$parent_reward_setup = array();
			}

		}

		// Make sure all point types are populated in a reward setup
		foreach ( $types as $point_type => $point_type_label ) {

			if ( empty( $reward_setup ) || ! array_key_exists( $point_type, $reward_setup ) )
				$reward_setup[ $point_type ]        = '';

			if ( empty( $parent_reward_setup ) || ! array_key_exists( $point_type, $parent_reward_setup ) )
				$parent_reward_setup[ $point_type ] = '';

		}

		// We might want to enforce the parent value for variations
		foreach ( $reward_setup as $point_type => $value ) {

			// If the variation has no value set, but the parent box has a value set, enforce the parent value
			// If the variation is set to zero however, it indicates we do not want to reward that variation
			if ( $value == '' && $parent_reward_setup[ $point_type ] != '' && $parent_reward_setup[ $point_type ] != 0 )
				$reward_setup[ $point_type ] = $parent_reward_setup[ $point_type ];

		}

		// If we are requesting one particular types reward
		if ( $requested_type !== false ) {

			$value = '';
			if ( array_key_exists( $requested_type, $reward_setup ) )
				$value = $reward_setup[ $requested_type ];

			return $value;

		}

		return $reward_setup;

	}
endif;

/**
 * Register Hook
 * @since 1.5
 * @version 1.1
 */
function mycred_register_woocommerce_hook( $installed ) {

	if ( ! class_exists( 'WooCommerce' ) ) return $installed;

	$installed['wooreview'] = array(
		'title'         => __( 'WooCommerce Product Reviews', 'mycred' ),
		'description'   => __( 'Awards %_plural% for users leaving reviews on your WooCommerce products.', 'mycred' ),
		'documentation' => 'http://codex.mycred.me/hooks/product-reviews/',
		'callback'      => array( 'myCRED_Hook_WooCommerce_Reviews' )
	);

	return $installed;

}
add_filter( 'mycred_setup_hooks', 'mycred_register_woocommerce_hook', 95 );

/**
 * WooCommerce Product Review Hook
 * @since 1.5
 * @version 1.1.1
 */
add_action( 'mycred_load_hooks', 'mycred_load_woocommerce_hook', 95 );
function mycred_load_woocommerce_hook() {

	// If the hook has been replaced or if plugin is not installed, exit now
	if ( class_exists( 'myCRED_Hook_WooCommerce_Reviews' ) || ! class_exists( 'WooCommerce' ) ) return;

	class myCRED_Hook_WooCommerce_Reviews extends myCRED_Hook {

		/**
		 * Construct
		 */
		public function __construct( $hook_prefs, $type = MYCRED_DEFAULT_TYPE_KEY ) {

			parent::__construct( array(
				'id'       => 'wooreview',
				'defaults' => array(
					'creds' => 1,
					'log'   => '%plural% for product review',
					'limit' => '0/x'
				)
			), $hook_prefs, $type );

		}

		/**
		 * Run
		 * @since 1.5
		 * @version 1.0
		 */
		public function run() {

			add_action( 'comment_post',              array( $this, 'new_review' ), 99, 2 );
			add_action( 'transition_comment_status', array( $this, 'review_transitions' ), 99, 3 );

		}

		/**
		 * New Review
		 * @since 1.5
		 * @version 1.0
		 */
		public function new_review( $comment_id, $comment_status ) {

			// Approved comment
			if ( $comment_status == '1' )
				$this->review_transitions( 'approved', 'unapproved', $comment_id );

		}

		/**
		 * Review Transitions
		 * @since 1.5
		 * @version 1.2
		 */
		public function review_transitions( $new_status, $old_status, $comment ) {

			// Only approved reviews give points
			if ( $new_status != 'approved' ) return;

			// Passing an integer instead of an object means we need to grab the comment object ourselves
			if ( ! is_object( $comment ) )
				$comment = get_comment( $comment );

			// No comment object so lets bail
			if ( $comment === NULL ) return;

			// Only applicable for reviews
			if ( mycred_get_post_type( $comment->comment_post_ID ) != 'product' ) return;

			// Check for exclusions
			if ( $this->core->exclude_user( $comment->user_id ) ) return;

			// Limit
			if ( $this->over_hook_limit( '', 'product_review', $comment->user_id ) ) return;

			// Execute
			$data = array( 'ref_type' => 'post' );
			if ( ! $this->core->has_entry( 'product_review', $comment->comment_post_ID, $comment->user_id, $data, $this->mycred_type ) )
				$this->core->add_creds(
					'product_review',
					$comment->user_id,
					$this->prefs['creds'],
					$this->prefs['log'],
					$comment->comment_post_ID,
					$data,
					$this->mycred_type
				);

		}

		/**
		 * Preferences for WooCommerce Product Reviews
		 * @since 1.5
		 * @version 1.1
		 */
		public function preferences() {

			$prefs = $this->prefs;

?>
<div class="hook-instance">
	<div class="row">
		<div class="col-lg-2 col-md-6 col-sm-6 col-xs-12">
			<div class="form-group">
				<label for="<?php echo $this->field_id( 'creds' ); ?>"><?php echo $this->core->plural(); ?></label>
				<input type="text" name="<?php echo $this->field_name( 'creds' ); ?>" id="<?php echo $this->field_id( 'creds' ); ?>" value="<?php echo $this->core->number( $prefs['creds'] ); ?>" class="form-control" />
			</div>
		</div>
		<div class="col-lg-4 col-md-6 col-sm-6 col-xs-12">
			<div class="form-group">
				<label for="<?php echo $this->field_id( 'limit' ); ?>"><?php _e( 'Limit', 'mycred' ); ?></label>
				<?php echo $this->hook_limit_setting( $this->field_name( 'limit' ), $this->field_id( 'limit' ), $prefs['limit'] ); ?>
			</div>
		</div>
		<div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
			<div class="form-group">
				<label for="<?php echo $this->field_id( 'log' ); ?>"><?php _e( 'Log Template', 'mycred' ); ?></label>
				<input type="text" name="<?php echo $this->field_name( 'log' ); ?>" id="<?php echo $this->field_id( 'log' ); ?>" placeholder="<?php _e( 'required', 'mycred' ); ?>" value="<?php echo esc_attr( $prefs['log'] ); ?>" class="form-control" />
				<span class="description"><?php echo $this->available_template_tags( array( 'general', 'post' ) ); ?></span>
			</div>
		</div>
	</div>
</div>
<?php

		}

		/**
		 * Sanitise Preferences
		 * @since 1.6
		 * @version 1.0
		 */
		public function sanitise_preferences( $data ) {

			if ( isset( $data['limit'] ) && isset( $data['limit_by'] ) ) {
				$limit = sanitize_text_field( $data['limit'] );
				if ( $limit == '' ) $limit = 0;
				$data['limit'] = $limit . '/' . $data['limit_by'];
				unset( $data['limit_by'] );
			}

			return $data;

		}

	}

}