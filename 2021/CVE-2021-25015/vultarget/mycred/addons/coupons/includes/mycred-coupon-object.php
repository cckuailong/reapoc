<?php
if ( ! defined( 'myCRED_VERSION' ) ) exit;

/**
 * myCRED_Coupon class
 * @see http://codex.mycred.me/classes/mycred_coupon/
 * @since 1.8
 * @version 1.0
 */
if ( ! class_exists( 'myCRED_Coupon' ) ) :
	class myCRED_Coupon extends myCRED_Object {

		/**
		 * Coupon post ID
		 */
		public $post_id           = false;

		/**
		 * Coupon code
		 */
		public $coupon_code       = false;
		public $code              = false;

		/**
		 * Coupon Value
		 */
		public $value             = 0;

		/**
		 * Coupon Point Type
		 */
		public $point_type        = '';

		/**
		 * Maximum usage in total
		 */
		public $max_global        = 0;

		/**
		 * Maximum usage per user
		 */
		public $max_user          = 0;

		/**
		 * Minimum balance requirement
		 */
		public $requires_min      = 0;

		/**
		 * Minimum balance requirement type
		 */
		public $requires_min_type = '';

		/**
		 * Maximum balance requirement
		 */
		public $requires_max      = 0;

		/**
		 * Maximum balance requriement type
		 */
		public $requires_max_type = '';

		/**
		 * Usage count
		 */
		public $used              = 0;

		/**
		 * If coupon expires
		 */
		public $expires           = false;

		/**
		 * Expiraion UNIX timestamp
		 */
		public $expires_unix      = 0;

		/**
		 * Construct
		 */
		function __construct( $coupon_id = NULL ) {

			parent::__construct();

			$coupon_code = '';

			// If we provide the coupon code
			if ( ! is_numeric( $coupon_id ) ) {

				$coupon_id = 0;
				$coupon    = mycred_get_coupon_post( $coupon_id );
				if ( $coupon !== NULL && $coupon->post_type == MYCRED_COUPON_KEY ) {
					$coupon_id   = $coupon->ID;
					$coupon_code = $coupon->post_title;
				}

			}

			// If we provide the post ID
			else {

				$coupon_id = absint( $coupon_id );
				if ( mycred_get_post_type( $coupon_id ) != MYCRED_COUPON_KEY )
					$coupon_id = 0;

			}

			if ( $coupon_id === 0 ) return false;

			$this->populate( $coupon_id, $coupon_code );

		}

		/**
		 * Populate
		 * @since 1.0
		 * @version 1.0
		 */
		protected function populate( $coupon_id = NULL, $code = '' ) {

			$this->post_id           = absint( $coupon_id );
			$this->coupon_code       = ( $code == '' ) ? mycred_get_the_title( $this->post_id ) : $code;
			$this->code              = $this->coupon_code;

			$this->value             = mycred_get_coupon_value( $this->post_id );
			$this->point_type        = mycred_get_post_meta( $this->post_id, 'type', true );
			$this->max_global        = mycred_get_coupon_global_max( $this->post_id );
			$this->max_user          = mycred_get_coupon_user_max( $this->post_id );

			$this->requires_min      = mycred_get_coupon_min_balance( $this->post_id );
			$this->requires_min_type = $this->requires_min['type'];

			$this->requires_max      = mycred_get_coupon_max_balance( $this->post_id );
			$this->requires_max_type = $this->requires_max['type'];

			$this->used              = $this->get_usage_count();

			if ( ! mycred_point_type_exists( $this->point_type ) )
				$this->point_type        = MYCRED_DEFAULT_TYPE_KEY;

			if ( ! mycred_point_type_exists( $this->requires_min_type ) )
				$this->requires_min_type = MYCRED_DEFAULT_TYPE_KEY;

			if ( ! mycred_point_type_exists( $this->requires_max_type ) )
				$this->requires_max_type = MYCRED_DEFAULT_TYPE_KEY;

			$this->expires           = mycred_get_coupon_expire_date( $this->post_id );
			$this->expires_unix      = false;

			// If there is an expiration date
			if ( $this->expires !== false ) {

				$this->expires_unix = ( strtotime( $this->expires . ' midnight' ) + ( DAY_IN_SECONDS - 1 ) );

				// Ill formatted expiration date. Not using a format strtotime() understands
				// Prevent expiration and warn user when editing the coupon
				if ( $this->expires_unix <= 0 || $this->expires_unix === false ) {

					$this->expires = false;

					mycred_update_post_meta( $this->post_id, '_warning_bad_expiration', $this->expires );
					mycred_delete_post_meta( $this->post_id, 'expires' );

				}

			}

			$this->settings = shortcode_atts( array(
				'log'         => 'Coupon redemption',
				'invalid'     => 'This is not a valid coupon',
				'expired'     => 'This coupon has expired',
				'user_limit'  => 'You have already used this coupon',
				'min'         => 'A minimum of %amount% is required to use this coupon',
				'max'         => 'A maximum of %amount% is required to use this coupon',
				'excluded'    => 'You can not use coupons.',
				'success'     => '%amount% successfully deposited into your account'
			), (array) mycred_get_addon_settings( 'coupons' ) );

		}

		/**
		 * Get Usage Count
		 * @since 1.0
		 * @version 1.0
		 */
		public function get_usage_count() {

			$count = mycred_get_post_meta( $this->post_id, 'usage-count', true );
			if ( $count == '' ) {

				global $wpdb, $mycred_log_table;

				$count = $wpdb->get_var( $wpdb->prepare( "
					SELECT COUNT(*) 
					FROM {$mycred_log_table} 
					WHERE ref = 'coupon' AND ref_id = %d AND ctype = %s;", $this->post_id, $this->point_type ) );

				if ( $count === NULL ) $count = 0;

				mycred_update_post_meta( $this->post_id, 'usage-count', $count );

			}

			return apply_filters( 'mycred_get_global_coupon_count', $count, $this->post_id, $this );

		}

		/**
		 * Update Usage Count
		 * @since 1.0
		 * @version 1.0
		 */
		public function update_usage_count() {

			$this->used ++;

			mycred_update_post_meta( $this->post_id, 'usage-count', $this->used );

		}

		/**
		 * Use Coupon
		 * @since 1.0
		 * @version 1.0
		 */
		public function use_coupon( $user_id = false ) {

			if ( $this->post_id === false || $user_id === false ) return 'invalid';

			$can_use       = true;
			$now           = current_time( 'timestamp' );

			// Check Expiration
			if ( $this->expires !== false && $this->expires_unix <= $now )
				$can_use = 'expired';

			// Get Global Count
			if ( $can_use === true ) {

				if ( $this->used >= $this->max_global )
					$can_use = 'expired';

			}

			// Get User max
			if ( $can_use === true ) {

				$user_count = mycred_get_users_coupon_count( $this->code, $user_id );
				if ( $user_count >= $this->max_user )
					$can_use = 'user_limit';

			}

			$mycred        = mycred( $this->point_type );
			if ( $mycred->exclude_user( $user_id ) ) return 'excluded';

			$users_balance = $mycred->get_users_balance( $user_id, $this->point_type );

			if ( $can_use === true ) {

				// Min balance requirement
				if ( $this->requires_min_type != $this->point_type ) {

					$mycred_min        = mycred( $this->requires_min_type );
					$users_balance = $mycred_min->get_users_balance( $user_id, $this->requires_min_type );

				}

				if ( $mycred->number( $this->requires_min['value'] ) > $mycred->zero() && $users_balance < $mycred->number( $this->requires_min['value'] ) )
					$can_use = 'min';

				// Max balance requirement
				if ( $can_use === true ) {

					if ( $this->requires_max_type != $this->point_type ) {

						$mycred_max        = mycred( $this->requires_max_type );
						$users_balance = $mycred_max->get_users_balance( $user_id, $this->requires_max_type );

					}

					if ( $mycred->number( $this->requires_max['value'] ) > $mycred->zero() && $users_balance >= $mycred->number( $this->requires_max['value'] ) )
						$can_use = 'max';

				}

			}

			// Let other play and change the value of $can_use
			$can_use       = apply_filters( 'mycred_can_use_coupon', $can_use, $this->code, $user_id, $this );

			// Ready to use coupon!
			if ( $can_use === true ) {

				$this->settings['log'] = str_replace( '%coupon_code%', $this->code, $this->settings['log'] );

				// Apply Coupon
				$mycred->add_creds(
					'coupon',
					$user_id,
					$this->value,
					$this->settings['log'],
					$this->post_id,
					$this->code,
					$this->point_type
				);

				do_action( 'mycred_use_coupon', $user_id, $this );

				// Increment global counter
				$this->update_usage_count();

				// If the updated counter reaches the max, trash the coupon now
				if ( $this->used >= $this->max_global )
					mycred_trash_post( $this->post_id );

				// This should resolves issues where caching prevents the new global count from being loaded.
				else {

					clean_post_cache( $this->post_id );

				}

				return $mycred->number( $users_balance + $this->value );

			}

			// Trash expired coupons to preent further usage
			elseif ( $can_use == 'expired' ) {

				mycred_trash_post( $this->post_id );

			}

			return $can_use;

		}

	}
endif;
