<?php
if ( ! defined( 'myCRED_VERSION' ) ) exit;

/**
 * Register Hook
 * @since 1.6
 * @version 1.1
 */
add_filter( 'mycred_setup_hooks', 'mycred_register_affiliatewp_hook', 10 );
function mycred_register_affiliatewp_hook( $installed ) {

	if ( ! class_exists( 'Affiliate_WP' ) ) return $installed;

	$installed['affiliatewp'] = array(
		'title'         => __( 'AffiliateWP', 'mycred' ),
		'description'   => __( 'Awards %_plural% for affiliate signups, referring visitors and store sale referrals.', 'mycred' ),
		'documentation' => 'http://codex.mycred.me/hooks/affiliatewp-actions/',
		'callback'      => array( 'myCRED_AffiliateWP' )
	);

	return $installed;

}

/**
 * Affiliate WP Hook
 * @since 1.6
 * @version 1.1
 */
add_action( 'mycred_load_hooks', 'mycred_load_affiliatewp_hook', 10 );
function mycred_load_affiliatewp_hook() {

	// If the hook has been replaced or if plugin is not installed, exit now
	if ( class_exists( 'myCRED_AffiliateWP' ) || ! class_exists( 'Affiliate_WP' ) ) return;

	class myCRED_AffiliateWP extends myCRED_Hook {

		public $currency;

		/**
		 * Construct
		 */
		public function __construct( $hook_prefs, $type = MYCRED_DEFAULT_TYPE_KEY ) {

			parent::__construct( array(
				'id'       => 'affiliatewp',
				'defaults' => array(
					'signup' => array(
						'creds'  => 0,
						'log'    => '%plural% for becoming an affiliate'
					),
					'visits' => array(
						'creds'  => 0,
						'log'    => '%plural% for referral of a visitor',
						'limit'  => '0/x'
					),
					'referrals' => array(
						'creds'      => 0,
						'exchange'   => 1,
						'currency'   => 'MYC',
						'log'        => '%plural% for store referral',
						'remove_log' => '%plural% refund for rejected sale',
						'pay'        => 'amount'
					)
				)
			), $hook_prefs, $type );

			$this->currency = affiliate_wp()->settings->get( 'currency', 'USD' );

			// We might want to add a custom currency code
			add_filter( 'affwp_currencies', array( $this, 'add_currency' ) );

			// A custom currency code has been set and is used in AffiliateWP!
			// We need to take over the way currencies are shown in AffiliateWP
			if ( ! empty( $this->prefs['referrals']['currency'] ) && $this->currency == $this->prefs['referrals']['currency'] ) {
				add_filter( 'affwp_format_amount',                                  array( $this, 'amount' ) );
				add_filter( 'affwp_sanitize_amount_decimals',                       array( $this, 'decimals' ) );
				add_filter( 'affwp_' . $this->currency . '_currency_filter_before', array( $this, 'before' ), 10, 3 );
				add_filter( 'affwp_' . $this->currency . '_currency_filter_after',  array( $this, 'after' ), 10, 3 );
			}

		}

		public function add_currency( $currencies ) {

			if ( $this->prefs['referrals']['pay'] == 'currency' && ! empty( $this->prefs['referrals']['currency'] ) && ! array_key_exists( $this->prefs['referrals']['currency'], $currencies ) )
				$currencies[ $this->prefs['referrals']['currency'] ] = $this->core->plural();

			return $currencies;

		}

		public function amount( $amount ) {

			// Format myCRED way
			return $this->core->format_number( $amount );

		}

		public function before( $formatted, $currency, $amount ) {

			// No need to add if empty
			if ( $this->core->before != '' )
				$formatted = $this->core->before . ' ' . $amount;

			// Some might have applied adjustments how points are shown, apply them here as well
			return apply_filters( 'mycred_format_creds', $formatted, $amount, $this->core );

		}

		public function after( $formatted, $currency, $amount ) {

			// No need to add if empty
			if ( $this->core->after != '' )
				$formatted = $amount . ' ' . $this->core->after;

			// Some might have applied adjustments how points are shown, apply them here as well
			return apply_filters( 'mycred_format_creds', $formatted, $amount, $this->core );

		}

		public function decimals( $decimals ) {

			// Get decimal setup
			return absint( $this->core->format['decimals'] );

		}

		/**
		 * Run
		 * @since 1.6
		 * @version 1.0.1
		 */
		public function run() {

			// If we reward affiliate signups
			if ( $this->prefs['signup']['creds'] != 0 )
				add_action( 'affwp_register_user', array( $this, 'affiliate_signup' ), 10, 3 );

			// If we reward visit referrals
			if ( $this->prefs['visits']['creds'] != 0 )
				add_action( 'affwp_post_insert_visit', array( $this, 'new_visit' ), 10, 2 );

			// If we reward referrals
			add_action( 'affwp_set_referral_status', array( $this, 'referral_payouts' ), 10, 3 );

		}

		/**
		 * Affiliate Signup
		 * @since 1.6
		 * @version 1.0
		 */
		public function affiliate_signup( $affiliate_id, $status, $args ) {

			if ( $status == 'pending' ) return;

			// Get user id from affiliate id
			$user_id = affwp_get_affiliate_user_id( $affiliate_id );

			// Check for exclusion
			if ( $this->core->exclude_user( $user_id ) ) return;

			// Execute (if not done so already)
			if ( ! $this->has_entry( 'affiliate_signup', $affiliate_id, $user_id ) )
				$this->core->add_creds(
					'affiliate_signup',
					$user_id,
					$this->prefs['signup']['creds'],
					$this->prefs['signup']['log'],
					$affiliate_id,
					'',
					$this->mycred_type
				);

		}

		/**
		 * New Visit
		 * @since 1.6
		 * @version 1.0.1
		 */
		public function new_visit( $insert_id, $data ) {

			$affiliate_id = absint( $data['affiliate_id'] );
			$user_id      = affwp_get_affiliate_user_id( $affiliate_id );

			// Check for exclusion
			if ( $this->core->exclude_user( $user_id ) ) return;

			// Limit
			if ( $this->over_hook_limit( 'visits', 'affiliate_visit_referral', $user_id ) ) return;

			// Execute
			$this->core->add_creds(
				'affiliate_visit_referral',
				$user_id,
				$this->prefs['visits']['creds'],
				$this->prefs['visits']['log'],
				$insert_id,
				$data,
				$this->mycred_type
			);

		}

		/**
		 * Referral Payout
		 * @since 1.6
		 * @version 1.0
		 */
		public function referral_payouts( $referral_id, $new_status, $old_status ) {

			// If the referral id isn't valid
			if ( ! is_numeric( $referral_id ) ) {
				return;
			}

			// Get the referral object
			$referral = affwp_get_referral( $referral_id );

			// Get the user id
			$user_id  = affwp_get_affiliate_user_id( $referral->affiliate_id );

			// Check for exclusion
			if ( $this->core->exclude_user( $user_id ) ) return;

			$amount   = false;

			// We are paying a set amount for all referrals
			if ( $this->prefs['referrals']['pay'] == 'creds' )
				$amount = $this->prefs['referrals']['creds'];

			// We pay the referral amount (assumes poins are used as the store currency
			elseif ( $this->prefs['referrals']['pay'] == 'currency' )
				$amount = $referral->amount;

			// We apply an exchange rate
			elseif ( $this->prefs['referrals']['pay'] == 'exchange' )
				$amount = $this->core->number( ( $referral->amount * $this->prefs['referrals']['exchange'] ) );

			$amount = apply_filters( 'mycred_affiliatewp_payout', $amount, $referral, $new_status, $old_status, $this );
			if ( $amount === false ) return;

			if ( 'paid' === $new_status ) {

				$this->core->add_creds(
					'affiliate_referral',
					$user_id,
					$amount,
					$this->prefs['referrals']['log'],
					$referral_id,
					array( 'ref_type' => 'post' ),
					$this->mycred_type
				);

			}

			elseif ( 'paid' === $old_status ) {

				if ( $this->core->has_entry( 'affiliate_referral', $referral_id, $user_id, array( 'ref_type' => 'post' ), $this->mycred_type ) )
					$this->core->add_creds(
						'affiliate_referral_refund',
						$user_id,
						0 - $amount,
						$this->prefs['referrals']['remove_log'],
						$referral_id,
						array( 'ref_type' => 'post' ),
						$this->mycred_type
					);

			}

		}

		/**
		 * Preferences
		 * @since 1.6
		 * @version 1.1
		 */
		public function preferences() {

			$prefs = $this->prefs;

?>
<div class="hook-instance">
	<h3><?php _e( 'Affiliate Signup', 'mycred' ); ?></h3>
	<div class="row">
		<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
			<div class="form-group">
				<label for="<?php echo $this->field_id( array( 'signup', 'creds' ) ); ?>"><?php _e( 'Amount', 'mycred' ); ?></label>
				<input type="text" name="<?php echo $this->field_name( array( 'signup', 'creds' ) ); ?>" id="<?php echo $this->field_id( array( 'signup', 'creds' ) ); ?>" value="<?php echo $this->core->number( $prefs['signup']['creds'] ); ?>" class="form-control" />
				<span class="description"><?php _e( 'Use zero to disable.', 'mycred' ); ?></span>
			</div>
		</div>
		<div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
			<div class="form-group">
				<label for="<?php echo $this->field_id( array( 'signup', 'log' ) ); ?>"><?php _e( 'Log template', 'mycred' ); ?></label>
				<input type="text" name="<?php echo $this->field_name( array( 'signup', 'log' ) ); ?>" id="<?php echo $this->field_id( array( 'signup', 'log' ) ); ?>" placeholder="<?php _e( 'required', 'mycred' ); ?>" value="<?php echo esc_attr( $prefs['signup']['log'] ); ?>" class="form-control" />
				<span class="description"><?php echo $this->available_template_tags( array( 'general', 'post' ) ); ?></span>
			</div>
		</div>
	</div>
</div>
<div class="hook-instance">
	<h3><?php _e( 'Referring Visitors', 'mycred' ); ?></h3>
	<div class="row">
		<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
			<div class="form-group">
				<label for="<?php echo $this->field_id( array( 'visits', 'creds' ) ); ?>"><?php _e( 'Amount', 'mycred' ); ?></label>
				<input type="text" name="<?php echo $this->field_name( array( 'visits', 'creds' ) ); ?>" id="<?php echo $this->field_id( array( 'visits', 'creds' ) ); ?>" value="<?php echo $this->core->number( $prefs['visits']['creds'] ); ?>" class="form-control" />
				<span class="description"><?php _e( 'Use zero to disable.', 'mycred' ); ?></span>
			</div>
		</div>
		<div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
			<div class="form-group">
				<label for="<?php echo $this->field_id( array( 'visits', 'limit' ) ); ?>"><?php _e( 'Limit', 'mycred' ); ?></label>
				<?php echo $this->hook_limit_setting( $this->field_name( array( 'visits', 'limit' ) ), $this->field_id( array( 'visits', 'limit' ) ), $prefs['visits']['limit'] ); ?>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
			<div class="form-group">
				<label for="<?php echo $this->field_id( array( 'visits', 'log' ) ); ?>"><?php _e( 'Log template', 'mycred' ); ?></label>
				<input type="text" name="<?php echo $this->field_name( array( 'visits', 'log' ) ); ?>" id="<?php echo $this->field_id( array( 'visits', 'log' ) ); ?>" placeholder="<?php _e( 'required', 'mycred' ); ?>" value="<?php echo esc_attr( $prefs['visits']['log'] ); ?>" class="form-control" />
				<span class="description"><?php echo $this->available_template_tags( array( 'general', 'post' ) ); ?></span>
			</div>
		</div>
	</div>
</div>
<div class="hook-instance">
	<h3><?php _e( 'Referring Sales', 'mycred' ); ?></h3>
	<div class="row">
		<div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
			<div class="form-group">
				<div class="radio">
					<label for="<?php echo $this->field_id( array( 'referrals', 'pay-amount' ) ); ?>"><input type="radio" name="<?php echo $this->field_name( array( 'referrals', 'pay' ) ); ?>" id="<?php echo $this->field_id( array( 'referrals', 'pay-amount' ) ); ?>"<?php checked( $this->prefs['referrals']['pay'], 'creds' ); ?> value="creds" /> <?php _e( 'Pay a set amount', 'mycred' ); ?></label>
				</div>
				<label for="<?php echo $this->field_id( array( 'referrals', 'creds' ) ); ?>"><?php _e( 'Amount', 'mycred' ); ?></label>
				<input type="text" name="<?php echo $this->field_name( array( 'referrals', 'creds' ) ); ?>" id="<?php echo $this->field_id( array( 'referrals', 'creds' ) ); ?>" class="form-control" value="<?php echo $this->core->number( $prefs['referrals']['creds'] ); ?>" />
				<span class="description"><?php _e( 'All referrals will pay the same amount.', 'mycred' ); ?></span>
			</div>
		</div>
		<div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
			<div class="form-group">
				<div class="radio">
					<label for="<?php echo $this->field_id( array( 'referrals', 'pay-store' ) ); ?>"><input type="radio" name="<?php echo $this->field_name( array( 'referrals', 'pay' ) ); ?>" id="<?php echo $this->field_id( array( 'referrals', 'pay-store' ) ); ?>"<?php checked( $this->prefs['referrals']['pay'], 'currency' ); ?> value="currency" /> <?php _e( 'Pay the referral amount', 'mycred' ); ?></label>
				</div>
				<label for="<?php echo $this->field_id( array( 'referrals', 'currency' ) ); ?>"><?php _e( 'Points Currency Code', 'mycred' ); ?></label>
				<input type="text" name="<?php echo $this->field_name( array( 'referrals', 'currency' ) ); ?>" id="<?php echo $this->field_id( array( 'referrals', 'currency' ) ); ?>" class="form-control" value="<?php echo esc_attr( $prefs['referrals']['currency'] ); ?>" />
				<span class="description"><?php _e( 'Requires AffiliateWP and your store to use points as currency.', 'mycred' ); ?></span>
			</div>
		</div>
		<div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
			<div class="form-group">
				<div class="radio">
					<label for="<?php echo $this->field_id( array( 'referrals', 'pay-ex' ) ); ?>"><input type="radio" name="<?php echo $this->field_name( array( 'referrals', 'pay' ) ); ?>"<?php if ( array_key_exists( $this->currency, $this->point_types ) ) echo ' readonly="readonly"'; ?> id="<?php echo $this->field_id( array( 'referrals', 'pay-ex' ) ); ?>"<?php checked( $this->prefs['referrals']['pay'], 'exchange' ); ?> value="exchange" /> <?php _e( 'Apply an exchange rate', 'mycred' ); ?></label>
				</div>
				<label for="<?php echo $this->field_id( array( 'referrals', 'exchange' ) ); ?>"><?php _e( 'Exchange Rate', 'mycred' ); ?></label>
				<input type="text" name="<?php echo $this->field_name( array( 'referrals', 'exchange' ) ); ?>" id="<?php echo $this->field_id( array( 'referrals', 'exchange' ) ); ?>" class="form-control"<?php if ( array_key_exists( $this->currency, $this->point_types ) ) echo ' readonly="readonly"'; ?> value="<?php echo esc_attr( $prefs['referrals']['exchange'] ); ?>" />
				<span class="description"><?php if ( ! array_key_exists( $this->currency, $this->point_types ) ) printf( __( 'How much is 1 %s worth in %s', 'mycred' ), $this->core->plural(), $this->currency ); else _e( 'Disabled', 'mycred' ); ?></span>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
			<div class="form-group">
				<label for="<?php echo $this->field_id( array( 'referrals', 'log' ) ); ?>"><?php _e( 'Log template - Payout', 'mycred' ); ?></label>
				<input type="text" name="<?php echo $this->field_name( array( 'referrals', 'log' ) ); ?>" id="<?php echo $this->field_id( array( 'referrals', 'log' ) ); ?>" placeholder="<?php _e( 'required', 'mycred' ); ?>" value="<?php echo esc_attr( $prefs['referrals']['log'] ); ?>" class="form-control" />
				<span class="description"><?php echo $this->available_template_tags( array( 'general', 'post' ) ); ?></span>
			</div>
		</div>
		<div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
			<div class="form-group">
				<label for="<?php echo $this->field_id( array( 'referrals', 'remove_log' ) ); ?>"><?php _e( 'Log template - Refund', 'mycred' ); ?></label>
				<input type="text" name="<?php echo $this->field_name( array( 'referrals', 'remove_log' ) ); ?>" id="<?php echo $this->field_id( array( 'referrals', 'remove_log' ) ); ?>" placeholder="<?php _e( 'required', 'mycred' ); ?>" value="<?php echo esc_attr( $prefs['referrals']['remove_log'] ); ?>" class="form-control" />
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
		function sanitise_preferences( $data ) {

			if ( isset( $data['visits']['limit'] ) && isset( $data['visits']['limit_by'] ) ) {
				$limit = sanitize_text_field( $data['visits']['limit'] );
				if ( $limit == '' ) $limit = 0;
				$data['visits']['limit'] = $limit . '/' . $data['visits']['limit_by'];
				unset( $data['visits']['limit_by'] );
			}

			return $data;

		}

	}

}
