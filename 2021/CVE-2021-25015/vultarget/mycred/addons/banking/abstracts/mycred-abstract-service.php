<?php
if ( ! defined( 'myCRED_VERSION' ) ) exit;

/**
 * myCRED_Service class
 * @see http://codex.mycred.me/classes/mycred_service/
 * @since 1.2
 * @version 1.1
 */
if ( ! class_exists( 'myCRED_Service' ) ) :
	abstract class myCRED_Service {

		// Service ID
		public $id;

		// myCRED_Settings Class
		public $core;

		// Multipoint types
		public $is_main_type = true;
		public $mycred_type  = MYCRED_DEFAULT_TYPE_KEY;

		// Service Prefs
		public $prefs        = array();
		public $now          = 0;

		/**
		 * Construct
		 */
		function __construct( $args = array(), $service_prefs = NULL, $type = MYCRED_DEFAULT_TYPE_KEY ) {

			if ( ! empty( $args ) ) {
				foreach ( $args as $key => $value ) {
					$this->$key = $value;
				}
			}

			// Grab myCRED Settings
			$this->core        = mycred( $type );
			$this->mycred_type = $type;

			if ( $this->mycred_type != MYCRED_DEFAULT_TYPE_KEY )
				$this->is_main_type = false;

			// Prep settings
			if ( ! empty( $this->defaults ) )
				$this->prefs = $this->defaults;

			if ( $service_prefs !== NULL && array_key_exists( $this->id, $service_prefs ) )
				$this->prefs = $service_prefs[ $this->id ];

			$this->prefs      = wp_parse_args( $this->prefs, $this->defaults );
			$this->now        = current_time( 'timestamp', 1 );

		}

		/**
		 * Run
		 * Must be over-ridden by sub-class!
		 * @since 1.2
		 * @version 1.0
		 */
		public function run() {
			wp_die( 'function myCRED_Service::run() must be over-ridden in a sub-class.' );
		}

		/**
		 * Preferences
		 * @since 0.1
		 * @version 1.0
		 */
		public function preferences() { }

		/**
		 * Sanitise Preference
		 * @since 1.2
		 * @version 1.0
		 */
		public function sanitise_preferences( $post ) {

			return $post;

		}

		/**
		 * Activate
		 * @since 1.5.2
		 * @version 1.0
		 */
		public function activate() { }

		/**
		 * Deactivate
		 * @since 1.2
		 * @version 1.0
		 */
		public function deactivate() { }

		/**
		 * Ajax Handler
		 * @since 1.7
		 * @version 1.0
		 */
		public function ajax_handler() { }

		/**
		 * User Screen
		 * @since 1.7
		 * @version 1.0
		 */
		public function user_screen( $user ) { }

		/**
		 * Get Field Name
		 * Returns the field name for the current service
		 * @since 1.2
		 * @version 1.1
		 */
		public function field_name( $field = '' ) {

			if ( is_array( $field ) ) {
				$array = array();
				foreach ( $field as $parent => $child ) {
					if ( ! is_numeric( $parent ) )
						$array[] = $parent;

					if ( ! empty( $child ) && !is_array( $child ) )
						$array[] = $child;
				}
				$field = '[' . implode( '][', $array ) . ']';
			}
			else {
				$field = '[' . $field . ']';
			}

			$option_id = 'mycred_pref_bank';
			if ( ! $this->is_main_type )
				$option_id = $option_id . '_' . $this->mycred_type;

			return $option_id . '[service_prefs][' . $this->id . ']' . $field;

		}

		/**
		 * Get Field ID
		 * Returns the field id for the current service
		 * @since 1.2
		 * @version 1.1
		 */
		public function field_id( $field = '' ) {

			if ( is_array( $field ) ) {
				$array = array();
				foreach ( $field as $parent => $child ) {
					if ( ! is_numeric( $parent ) )
						$array[] = str_replace( '_', '-', $parent );

					if ( ! empty( $child ) && !is_array( $child ) )
						$array[] = str_replace( '_', '-', $child );
				}
				$field = implode( '-', $array );
			}
			else {
				$field = str_replace( '_', '-', $field );
			}

			$option_id = 'mycred_pref_bank';
			if ( ! $this->is_main_type )
				$option_id = $option_id . '_' . $this->mycred_type;

			$option_id = str_replace( '_', '-', $option_id );
			return $option_id . '-' . str_replace( '_', '-', $this->id ) . '-' . $field;

		}

		/**
		 * Get Days in Year
		 * @since 1.2
		 * @version 1.0.2
		 */
		public function get_days_in_year() {

			if ( date( 'L' ) )
				$days = 366;
			else
				$days = 365;

			return apply_filters( 'mycred_banking_days_in_year', $days, $this );

		}

		/**
		 * Is Large Site?
		 * @since 1.7
		 * @version 1.0.1
		 */
		public function is_large_site() {

			$is_large = false;
			if ( is_multisite() && wp_is_large_network() )
				$is_large = true;

			else {

				$users = count_users();
				if ( $users['total_users'] > 3000 )
					$is_large = true;

				else {

					global $wpdb, $mycred_log_table;

					$total = $wpdb->get_var( "SELECT COUNT(*) FROM {$mycred_log_table};" );
					if ( $total !== NULL && $total > 100000 )
						$is_large = true;

				}

			}

			return $is_large;

		}

		/**
		 * Get Excluded User IDs
		 * @since 1.7
		 * @version 1.0
		 */
		public function get_excluded_user_ids() {

			global $wpdb;

			$exclude_ids = array();

			if ( strlen( $this->prefs['exclude_ids'] ) > 0 ) {

				foreach ( explode( ',', $this->prefs['exclude_ids'] ) as $user_id ) {
					$user_id       = absint( preg_replace( "/[^0-9]/", "", $user_id ) );
					if ( $user_id === 0 ) continue;
					$exclude_ids[] = $user_id;
				}

			}

			if ( ! empty( $this->prefs['exclude_roles'] ) ) {

				$args             = array();
				$args['role__in'] = $this->prefs['exclude_roles'];
				$args['fields']   = 'ID';
				$user_query       = new WP_User_Query( $args );
				$user_ids         = $user_query->get_results();

				if ( ! empty( $user_ids ) ) {
					foreach ( $user_ids as $user_id ) {
						if ( in_array( $user_id, $exclude_ids ) ) continue;
						$exclude_ids[] = absint( $user_id );
					}
				}

			}

			if ( $this->core->exclude['list'] != '' ) {

				foreach ( explode( ',', $this->core->exclude['list'] ) as $user_id ) {
					$user_id       = absint( preg_replace( "/[^0-9]/", "", $user_id ) );
					if ( $user_id === 0 || in_array( $user_id, $exclude_ids ) ) continue;
					$exclude_ids[] = $user_id;
				}

			}

			if ( $this->prefs['min_balance'] != '' && $this->core->number( $this->prefs['min_balance'] ) > $this->core->zero() ) {

				$format = '%d';
				if ( $this->core->format['decimals'] > 0 )
					$format = '%f';

				$user_ids = $wpdb->get_col( $wpdb->prepare( "SELECT user_id FROM {$wpdb->usermeta} WHERE meta_key = %s AND meta_value < {$format};", $this->mycred_type, $this->prefs['min_balance'] ) );
				if ( ! empty( $user_ids ) ) {
					foreach ( $user_ids as $user_id ) {
						if ( in_array( $user_id, $exclude_ids ) ) continue;
						$exclude_ids[] = absint( $user_id );
					}
				}

			}

			return $exclude_ids;

		}

		/**
		 * Convert Date to GMT Timestamp
		 * @since 1.7
		 * @version 1.0
		 */
		public function date_to_timestamp( $string = '' ) {

			return mycred_date_to_gmt_timestamp( $string );

		}

		/**
		 * Convert GMT Timestamp to Date
		 * @since 1.7
		 * @version 1.0
		 */
		public function timestamp_to_date( $string = '' ) {

			return mycred_gmt_timestamp_to_local( $string );

		}

		/**
		 * Time Select
		 * @since 1.7
		 * @version 1.0
		 */
		public function time_select( $name = '', $id = '', $date = NULL ) {

			$date    = ( ( $date !== false && strlen( $date ) > 0 ) ? date( 'H:i', $date ) : '' );
			$options = mycred_banking_get_time_options();

			$element = '<select name="' . $name . '" id="' . $id . '" class="form-control">';
			foreach ( $options as $time => $label ) {
				$element .= '<option value="' . $time . '"';
				if ( $date == $time ) $element .= ' selected="selected"';
				$element .= '>' . $label . '</option>';
			}
			$element .= '</select>';

			return $element;

		}

		/**
		 * Timeframe Dropdown
		 * @since 1.2
		 * @version 1.0
		 */
		public function timeframe_dropdown( $pref_id = '', $use_select = true, $hourly = true ) {

			$timeframes = mycred_banking_get_timeframes();
			if ( ! $hourly )
				unset( $timeframes['hourly'] );

			echo '<select name="' . $this->field_name( $pref_id ) . '" id="' . $this->field_id( $pref_id ) . '" class="form-control">';

			if ( $use_select )
				echo '<option value="">' . __( 'Select', 'mycred' ) . '</option>';

			$settings = '';
			if ( is_array( $pref_id ) ) {
				reset( $pref_id );
				$key = key( $pref_id );
				$settings = $this->prefs[ $key ][ $pref_id[ $key ] ];
			}
			elseif ( isset( $this->prefs[ $pref_id ] ) ) {
				$settings = $this->prefs[ $pref_id ];
			}

			foreach ( $timeframes as $value => $details ) {
				echo '<option value="' . $value . '"';
				if ( $settings == $value ) echo ' selected="selected"';
				echo '>' . $details['label'] . '</option>';
			}
			echo '</select>';

		}

	}
endif;
