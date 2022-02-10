<?php
if ( ! defined( 'myCRED_VERSION' ) ) exit;

/**
 * myCRED_Rank class
 * @see http://codex.mycred.me/classes/mycred_rank/
 * @since 1.7
 * @version 1.0
 */
if ( ! class_exists( 'myCRED_Rank' ) ) :
	class myCRED_Rank extends myCRED_Object {

		/**
		 * Rank Post ID
		 */
		public $post_id      = false;

		/**
		 * The rank post object
		 */
		public $post         = false;

		/**
		 * The Rank title
		 */
		public $title        = '';

		/**
		 * Minimum point requirement for this rank
		 */
		public $minimum      = NULL;

		/**
		 * Maximum point requirement for this rank
		 */
		public $maximum      = NULL;

		/**
		 * Total users with this rank
		 */
		public $count        = 0;

		/**
		 * Indicates if the rank has a logo
		 */
		public $has_logo     = false;

		/**
		 * The ranks logo attachment id
		 */
		public $logo_id      = false;

		/**
		 * The ranks logo attachment url
		 */
		public $logo_url     = false;

		/**
		 * The rank image width
		 */
		public $image_width  = false;

		/**
		 * The rank image height
		 */
		public $image_height = false;

		/**
		 * The point type object associated with this rank
		 */
		public $point_type   = false;

		/**
		 * Indicates if this rank is manually assigned or not
		 */
		public $is_manual    = false;

		/**
		 * Construct
		 */
		public function __construct( $rank_id = NULL ) {

			parent::__construct();

			$rank_id = absint( $rank_id );
			if ( $rank_id === 0 ) return;

			if ( mycred_get_post_type( $rank_id ) != MYCRED_RANK_KEY ) return;

			$this->image_width  = MYCRED_RANK_WIDTH;
			$this->image_height = MYCRED_RANK_HEIGHT;

			$this->populate( $rank_id );

		}

		/**
		 * Populate
		 * @since 1.0
		 * @version 1.0
		 */
		protected function populate( $rank_id = NULL ) {

			$this->post_id    = absint( $rank_id );
			$this->post       = mycred_get_post( $this->post_id );
			$this->title      = mycred_get_the_title( $this->post );
			$this->minimum    = mycred_get_post_meta( $this->post_id, 'mycred_rank_min', true );
			$this->maximum    = mycred_get_post_meta( $this->post_id, 'mycred_rank_max', true );
			$this->count      = mycred_count_users_with_rank( $this->post_id );

			$this->has_logo   = mycred_rank_has_logo( $this->post_id );
			$this->logo_id    = get_post_thumbnail_id( $this->post );
			$this->logo_url   = wp_get_attachment_url( $this->logo_id );

			$point_type       = mycred_get_post_meta( $this->post_id, 'ctype', true );
			if ( ! mycred_point_type_exists( $point_type ) )
				$point_type = MYCRED_DEFAULT_TYPE_KEY;

			$this->point_type = new myCRED_Point_Type( $point_type );

			$this->is_manual  = mycred_manual_ranks( $point_type );

		}

		/**
		 * Checks if a user has this rank
		 * @since 1.8
		 * @version 1.0
		 */
		public function user_has_rank( $user_id = false ) {

			if ( $user_id === false || absint( $user_id ) === 0 ) return false;

			$user_id    = absint( $user_id );
			$post_type  = $this->point_type->cred_id;

			$users_rank = mycred_get_user_meta( $user_id, MYCRED_RANK_KEY, ( ( $post_type != MYCRED_DEFAULT_TYPE_KEY ) ? $post_type : '' ), true );

			$has_rank   = false;
			if ( $users_rank != '' && absint( $users_rank ) === $this->post_id )
				$has_rank = true;

			return apply_filters( 'mycred_user_has_rank', $has_rank, $user_id, $this );

		}

		/**
		 * Assign Rank to User
		 * @since 1.8
		 * @since 2.3 Added `mycred_count_users_with_rank` for better counting
		 * @version 1.0
		 */
		public function assign( $user_id = false ) {

			if ( $user_id === false || absint( $user_id ) === 0 ) return false;

			$user_id    = absint( $user_id );
			$post_type  = $this->point_type->cred_id;

			// User already has this rank
			if ( $this->user_has_rank( $user_id ) ) return true;

			$value      = apply_filters( 'mycred_rank_user_value', $this->post_id, $user_id, $this );

			$this->count = mycred_count_users_with_rank( $this->post_id );

			$this->count++;

			mycred_update_post_meta( $this->post_id, 'mycred_rank_users', $this->count );

			return mycred_update_user_meta( $user_id, MYCRED_RANK_KEY, ( ( $post_type != MYCRED_DEFAULT_TYPE_KEY ) ? $post_type : '' ), $value );

		}

		/**
		 * Assign All
		 * Assigns all users that meet the requirements for this rank.
		 * @since 1.8
		 * @version 1.0
		 */
		public function assign_all() {

			if ( $this->is_manual ) return false;

			global $wpdb;

			$post_type      = $this->point_type->cred_id;
			$balance_format = esc_sql( $this->point_type->sql_format );
			$rank_key       = mycred_get_meta_key( MYCRED_RANK_KEY, ( ( $point_type != MYCRED_DEFAULT_TYPE_KEY ) ? $point_type : '' ) );

			$balance_key    = mycred_get_meta_key( $point_type );
			if ( mycred_rank_based_on_total( $point_type ) )
				$balance_key = mycred_get_meta_key( $point_type, '_total' );

			do_action( 'mycred_assign_rank_start', $this );

			$count          = $wpdb->query( $wpdb->prepare( "
				UPDATE {$wpdb->usermeta} ranks 
					INNER JOIN {$wpdb->usermeta} balance ON ( ranks.user_id = balance.user_id AND balance.meta_key = %s )
				SET ranks.meta_value = %d 
				WHERE ranks.meta_key = %s 
					AND balance.meta_value BETWEEN {$balance_format} AND {$balance_format};", $balance_key, $this->post_id, $rank_key, $this->minimum, $this->maximum ) );

			do_action( 'mycred_assign_rank_end', $this );

			$this->count    = ( $count === NULL ) ? 0 : absint( $count );

			mycred_update_post_meta( $this->post_id, 'mycred_rank_users', $this->count );

		}

		/**
		 * Divest User
		 * @since 1.8
		 * @since 2.3 Added `mycred_count_users_with_rank` for better counting
		 * @version 1.0
		 */
		public function divest( $user_id = false ) {

			if ( $user_id === false || absint( $user_id ) === 0 ) return false;

			$user_id    = absint( $user_id );
			$post_type  = $this->point_type->cred_id;

			$results    = true;
			$users_rank = mycred_get_user_meta( $user_id, MYCRED_RANK_KEY, ( ( $post_type != MYCRED_DEFAULT_TYPE_KEY ) ? $post_type : '' ), true );

			if ( $users_rank != '' ) {

				$results = mycred_delete_user_meta( $user_id, MYCRED_RANK_KEY, ( ( $post_type != MYCRED_DEFAULT_TYPE_KEY ) ? $post_type : '' ) );

				$this->count = mycred_count_users_with_rank( $this->post_id );

				$this->count--;

				mycred_update_post_meta( $this->post_id, 'mycred_rank_users', $this->count );

			}

			return $results;

		}

		/**
		 * Divest All Users
		 * @since 1.8
		 * @version 1.0
		 */
		public function divest_all() {

			if ( $this->post_id === false ) return false;

			global $wpdb;

			$post_type = $this->point_type->cred_id;

			// Delete connections
			$wpdb->delete(
				$wpdb->usermeta,
				array( 'meta_key' => mycred_get_meta_key( MYCRED_RANK_KEY, ( ( $post_type != MYCRED_DEFAULT_TYPE_KEY ) ? $post_type : '' ) ), 'meta_value' => $this->post_id ),
				array( '%s', '%d' )
			);

			$this->count = 0;

			mycred_update_post_meta( $this->post_id, 'mycred_rank_users', 0 );

			return true;

		}

		/**
		 * Delete Rank
		 * @since 1.8
		 * @version 1.0
		 */
		public function delete( $delete_post = true ) {

			if ( $this->post_id === false ) return false;

			$this->divest_all();

			if ( $delete_post )
				mycred_delete_post( $this->post_id, true );

			return true;

		}

		/**
		 * Get Image
		 * @since 1.0
		 * @version 1.0
		 */
		public function get_image( $image = 'logo' ) {

			if ( $image === 'logo' && $this->has_logo )
				return '<img src="' . esc_url( $this->logo_url ) . '" class="rank-logo" alt="' . esc_attr( $this->title ) . '" width="' . $this->image_width . '" height="' . $this->image_height . '" />';

			return '';

		}

	}
endif;
