<?php
if ( ! defined( 'myCRED_VERSION' ) ) exit;

/**
 * myCRED_Hook class
 * @see http://codex.mycred.me/classes/mycred_hook/
 * @since 0.1
 * @version 1.3.2
 */
if ( ! class_exists( 'myCRED_Hook' ) ) :
	abstract class myCRED_Hook {

		/**
		 * Unique Hook ID
		 */
		public $id           = false;

		/**
		 * The Hooks settings
		 */
		public $prefs        = false;

		/**
		 * The current point type key
		 */
		public $mycred_type  = MYCRED_DEFAULT_TYPE_KEY;

		/**
		 * The myCRED object for the current type
		 */
		public $core         = false;

		/**
		 * Array of all existing point types
		 */
		public $point_types  = array();

		/**
		 * Indicates if the current instance is for the main point type or not
		 */
		public $is_main_type = true;

		/**
		 * Construct
		 */
		public function __construct( $args = array(), $hook_prefs = NULL, $type = MYCRED_DEFAULT_TYPE_KEY ) {

			if ( ! empty( $args ) ) {
				foreach ( $args as $key => $value ) {
					$this->$key = $value;
				}
			}

			// Grab myCRED Settings
			$this->core        = mycred( $type );
			$this->point_types = mycred_get_types();

			if ( $type != '' ) {
				$this->core->cred_id = sanitize_text_field( $type );
				$this->mycred_type   = $this->core->cred_id;
			}

			if ( $this->mycred_type != MYCRED_DEFAULT_TYPE_KEY )
				$this->is_main_type = false;

			// Grab settings
			if ( $hook_prefs !== NULL ) {

				// Assign prefs if set
				if ( isset( $hook_prefs[ $this->id ] ) )
					$this->prefs = $hook_prefs[ $this->id ];

				// Defaults must be set
				if ( ! isset( $this->defaults ) )
					$this->defaults = array();

			}

			// Apply default settings if needed
			if ( ! empty( $this->defaults ) )
				$this->prefs = mycred_apply_defaults( $this->defaults, $this->prefs );

		}

		/**
		 * Run
		 * Must be over-ridden by sub-class!
		 * @since 0.1
		 * @version 1.0
		 */
		public function run() {

			wp_die( 'function myCRED_Hook::run() must be over-ridden in a sub-class.' );

		}

		/**
		 * Preferences
		 * @since 0.1
		 * @version 1.0
		 */
		public function preferences() {

			echo '<p>' . __( 'This Hook has no settings', 'mycred' ) . '</p>';

		}

		/**
		 * Sanitise Preference
		 * @since 0.1
		 * @version 1.0
		 */
		public function sanitise_preferences( $data ) {

			return $data;

		}

		/**
		 * Get Field Name
		 * Returns the field name for the current hook
		 * @since 0.1
		 * @version 1.1
		 */
		public function field_name( $field = '' ) {

			if ( is_array( $field ) ) {

				$array = array();
				foreach ( $field as $parent => $child ) {

					if ( ! is_numeric( $parent ) )
						$array[] = $parent;

					if ( ! empty( $child ) && ! is_array( $child ) )
						$array[] = $child;

				}
				$field = '[' . implode( '][', $array ) . ']';

			}
			else {

				$field = '[' . $field . ']';

			}

			$option_id = apply_filters( 'mycred_option_id', 'mycred_pref_hooks' );
			if ( ! $this->is_main_type )
				$option_id = $option_id . '_' . $this->mycred_type;

			return $option_id . '[hook_prefs][' . $this->id . ']' . $field;

		}

		/**
		 * Get Field ID
		 * Returns the field id for the current hook
		 * @since 0.1
		 * @version 1.2
		 */
		public function field_id( $field = '' ) {

			global $mycred_field_id;

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

			$option_id = 'mycred_pref_hooks';
			if ( ! $this->is_main_type )
				$option_id = $option_id . '_' . $this->mycred_type;

			$option_id = str_replace( '_', '-', $option_id );

			// $mycred_field_id - This little trick is used when widgets are not in a sidebar
			// Adding __i__ to IDs will prevent duplicate IDs ever existing on the same page, causing
			// scripts or HTML structures from working, like having a checkbox/radio selected when you click on
			// the label and not on the input field.

			return $option_id . '-' . str_replace( '_', '-', $this->id ) . '-' . $field . $mycred_field_id;

		}

		/**
		 * Check Limit
		 * @since 1.6
		 * @version 1.3
		 */
		public function over_hook_limit( $instance = '', $reference = '', $user_id = NULL, $ref_id = NULL ) {

			// If logging is disabled, we cant use this feature
			if ( ! MYCRED_ENABLE_LOGGING ) return false;

			// Enforce limit if this function is used incorrectly
			if ( ! isset( $this->prefs[ $instance ] ) && $instance != '' )
				return true;

			global $wpdb, $mycred_log_table;

			// Prep
			$wheres = array();
			$now    = current_time( 'timestamp' );

			// If hook uses multiple instances
			if ( isset( $this->prefs[ $instance ]['limit'] ) )
				$prefs = $this->prefs[ $instance ]['limit'];

			// Else if hook uses single instance
			elseif ( isset( $this->prefs['limit'] ) )
				$prefs = $this->prefs['limit'];

			// no support for limits
			else {
				return false;
			}

			// If the user ID is not set use the current one
			if ( $user_id === NULL )
				$user_id = get_current_user_id();

			// If this an existance check or just a regular limit check?
			$exists_check = false;
			if ( $ref_id !== NULL && strlen( $ref_id ) > 0 )
				$exists_check = true;

			if ( count( explode( '/', $prefs ) ) != 2 )
				$prefs = '0/x';

			// Set to "no limit"
			if ( ! $exists_check && $prefs === '0/x' ) return false;

			// Prep settings
			list ( $amount, $period ) = explode( '/', $prefs );
			$amount   = (int) $amount;

			// We start constructing the query.
			$wheres[] = $wpdb->prepare( "user_id = %d", $user_id );
			$wheres[] = $wpdb->prepare( "ref = %s", $reference );
			$wheres[] = $wpdb->prepare( "ctype = %s", $this->mycred_type );

			if ( $exists_check )
				$wheres[] = $wpdb->prepare( "ref_id = %d", $ref_id );

			// If check is based on time
			if ( ! in_array( $period, array( 't', 'x' ) ) ) {

				// Per day
				if ( $period == 'd' )
					$from = mktime( 0, 0, 0, date( 'n', $now ), date( 'j', $now ), date( 'Y', $now ) );

				// Per week
				elseif ( $period == 'w' )
					$from = mktime( 0, 0, 0, date( "n", $now ), date( "j", $now ) - date( "N", $now ) + 1 );

				// Per Month
				elseif ( $period == 'm' )
					$from = mktime( 0, 0, 0, date( "n", $now ), 1, date( 'Y', $now ) );

				$wheres[] = $wpdb->prepare( "time BETWEEN %d AND %d", $from, $now );

			}

			$over_limit = false;

			if ( ! empty( $wheres ) ) {

				// Put all wheres together into one string
				$wheres   = implode( " AND ", $wheres );

				$query = "SELECT COUNT(*) FROM {$mycred_log_table} WHERE {$wheres};";

				//Lets play for others
				$query = apply_filters( 'mycred_hook_limit_query', $query, $instance, $reference, $user_id, $ref_id, $wheres, $this );

				// Count
				$count = $wpdb->get_var( $query );
				if ( $count === NULL ) $count = 0;

				// Existence check has first priority
				if ( $count > 0 && $exists_check )
					$over_limit = true;

				// Limit check is second priority
				elseif ( $period != 'x' && $count >= $amount )
					$over_limit = true;

			}

			return apply_filters( 'mycred_over_hook_limit', $over_limit, $instance, $reference, $user_id, $ref_id, $this );

		}

		/**
		 * Get Limit Types
		 * @since 1.6
		 * @version 1.0
		 */
		public function get_limit_types() {

			return apply_filters( 'mycred_hook_limits', array(
				'x' => __( 'No limit', 'mycred' ),
				'd' => __( '/ Day', 'mycred' ),
				'w' => __( '/ Week', 'mycred' ),
				'm' => __( '/ Month', 'mycred' ),
				't' => __( 'in Total', 'mycred' )
			), $this );

		}

		/**
		 * Select Limit
		 * @since 1.6
		 * @version 1.0
		 */
		public function hook_limit_setting( $name = '', $id = '', $selected = '' ) {

			// Convert string value into an array
			$check   = explode( '/', $selected );
			$count   = count( $check );

			if ( $count == 0 || ( $count == 1 && $check[0] == 0 ) )
				$selected = array( 0, 'x' );

			elseif ( $count == 1 && $check[0] != '' && is_numeric( $check[0] ) )
				$selected = array( (int) $check[0], 'd' );

			else
				$selected = $check;

			// Hide value field if no limit is set
			$hide    = 'text';
			if ( $selected[1] == 'x' )
				$hide = 'hidden';

			// The limit value field
			$output  = '<div class="h2"><input type="' . $hide . '" size="8" class="mini" name="' . $name . '" id="' . $id . '" value="' . $selected[0] . '" />';

			// Get limit options
			$options = $this->get_limit_types();

			// Adjust the field name
			$name    = str_replace( '[limit]', '[limit_by]', $name );
			$name    = str_replace( '[alimit]', '[alimit_by]', $name );
			$name    = apply_filters( 'mycred_hook_limit_name_by', $name, $this );

			// Adjust the field id
			$id      = str_replace( 'limit', 'limit-by', $id );
			$id      = str_replace( 'alimit', 'alimit-by', $id );
			$id      = apply_filters( 'mycred_hook_limit_id_by', $id, $this );

			// Generate dropdown menu
			$output .= '<select name="' . $name . '" id="' . $id . '" class="limit-toggle">';
			foreach ( $options as $value => $label ) {
				$output .= '<option value="' . $value . '"';
				if ( $selected[1] == $value ) $output .= ' selected="selected"';
				$output .= '>' . $label . '</option>';
			}
			$output .= '</select></div>';

			return $output;

		}

		/**
		 * Impose Limits Dropdown
		 * @since 0.1
		 * @version 1.3
		 */
		public function impose_limits_dropdown( $pref_id = '', $use_select = true ) {

			$settings = '';
			$limits   = array(
				''           => __( 'No limit', 'mycred' ),
				'twentyfour' => __( 'Once every 24 hours', 'mycred' ),
				'sevendays'  => __( 'Once every 7 days', 'mycred' ),
				'daily'      => __( 'Once per day (reset at midnight)', 'mycred' )
			);
			$limits   = apply_filters( 'mycred_hook_impose_limits', $limits, $this );

			echo '<select name="' . $this->field_name( $pref_id ) . '" id="' . $this->field_id( $pref_id ) . '" class="form-control">';

			if ( $use_select )
				echo '<option value="">' . __( 'Select', 'mycred' ) . '</option>';

			if ( is_array( $pref_id ) ) {

				reset( $pref_id );
				$key = key( $pref_id );
				$settings = $this->prefs[ $key ][ $pref_id[ $key ] ];

			}
			elseif ( isset( $this->prefs[ $pref_id ] ) ) {

				$settings = $this->prefs[ $pref_id ];

			}

			foreach ( $limits as $value => $description ) {
				echo '<option value="' . $value . '"';
				if ( $settings == $value ) echo ' selected="selected"';
				echo '>' . $description . '</option>';
			}
			echo '</select>';

		}

		/**
		 * Has Entry
		 * Moved to myCRED_Settings
		 * @since 0.1
		 * @version 1.3
		 */
		public function has_entry( $action = '', $ref_id = '', $user_id = '', $data = '', $point_type = '' ) {

			// If logging is disabled, we cant use this feature
			if ( ! MYCRED_ENABLE_LOGGING ) return false;

			if ( $point_type == '' )
				$point_type = $this->mycred_type;

			return $this->core->has_entry( $action, $ref_id, $user_id, $data, $point_type );

		}

		/**
		 * Available Template Tags
		 * @since 1.4
		 * @version 1.0
		 */
		public function available_template_tags( $available = array(), $custom = '' ) {

			return $this->core->available_template_tags( $available, $custom );

		}

		/**
		 * Over Daily Limit
		 * @since 1.0
		 * @version 1.1.1
		 */
		public function is_over_daily_limit( $ref = '', $user_id = 0, $max = 0, $ref_id = NULL ) {

			// If logging is disabled, we cant use this feature
			if ( ! MYCRED_ENABLE_LOGGING ) return false;

			// Prep
			$reply = true;

			// DB Query
			$total = $this->limit_query( $ref, $user_id, strtotime( 'today midnight', $this->now ), $this->now, $ref_id );

			if ( $total < $max )
				$reply = false;

			return apply_filters( 'mycred_hook_over_daily_limit', $reply, $ref, $user_id, $max );

		}

		/**
		 * Include Post Type
		 * Checks if a given post type should be excluded
		 * @since 0.1
		 * @version 1.1
		 */
		public function include_post_type( $post_type ) {

			// Exclude Core
			$excludes = array( 'post', 'page' );
			if ( in_array( $post_type, apply_filters( 'mycred_post_type_excludes', $excludes ) ) ) return false;

			return true;

		}

		/**
		 * Limit Query
		 * Queries the myCRED log for the number of occurances of the specified
		 * refernece and optional reference id for a specific user between two dates.
		 * @param $ref (string) reference to search for, required
		 * @param $user_id (int) user id to search for, required
		 * @param $start (int) unix timestamp for start date, required
		 * @param $end (int) unix timestamp for the end date, required
		 * @param $ref_id (int) optional reference id to include in search
		 * @returns number of entries found (int) or NULL if required params are missing
		 * @since 1.4
		 * @version 1.2
		 */
		public function limit_query( $ref = '', $user_id = 0, $start = 0, $end = 0, $ref_id = NULL ) {

			// If logging is disabled, we cant use this feature
			if ( ! MYCRED_ENABLE_LOGGING ) return 0;

			// Minimum requirements
			if ( empty( $ref ) || $user_id == 0 || $start == 0 || $end == 0 )
				return NULL;

			global $wpdb, $mycred_log_table;

			// Prep
			$reply    = true;
			$wheres   = array();

			$wheres[] = $wpdb->prepare( "ref = %s", $ref );
			$wheres[] = $wpdb->prepare( "user_id = %d", $user_id );
			$wheres[] = $wpdb->prepare( "time BETWEEN %d AND %d", $start, $end );
			$wheres[] = $wpdb->prepare( "ctype = %s", $this->mycred_type );

			if ( $ref_id !== NULL )
				$wheres[] = $wpdb->prepare( "ref_id = %d", $ref_id );

			$wheres   = implode( " AND ", $wheres );

			// DB Query
			$total = $wpdb->get_var( "SELECT COUNT(*) FROM {$mycred_log_table} WHERE {$wheres};" );
			if ( $total === NULL ) $total = 0;

			return apply_filters( 'mycred_hook_limit_query', $total, $ref, $user_id, $ref_id, $start, $end );

		}

	}
endif;
