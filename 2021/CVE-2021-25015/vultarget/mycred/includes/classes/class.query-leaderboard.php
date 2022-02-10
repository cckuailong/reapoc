<?php
if ( ! defined( 'myCRED_VERSION' ) ) exit;

/**
 * Query Log
 * @see http://codex.mycred.me/classes/mycred_query_leaderboard/ 
 * @since 1.7.9.1
 * @version 1.0.1
 */
if ( ! class_exists( 'myCRED_Query_Leaderboard' ) ) :
	class myCRED_Query_Leaderboard {

		public $cache_key       = false;
		public $now             = 0;
		public $core            = NULL;
		public $user_id         = 0;
		private $max_size       = 250;

		public $args            = array();
		public $based_on        = 'balance';
		public $references      = array();
		public $point_types     = array();
		public $multitype_query = false;
		public $order           = '';
		public $limit           = '';

		public $leaderboard     = false;

		/**
		 * Construct
		 * Preps the class for getting a leaderboard based on the
		 * given arguments. Validates these arguments.
		 * @since 1.0
		 * @version 1.1.1
		 */
		public function __construct( $args = array() ) {

			$this->now      = current_time( 'timestamp' );
			$this->user_id  = get_current_user_id();
			$this->max_size = apply_filters( 'mycred_max_leaderboard_size', 250, $this );

			// Parse and validate the given args
			$this->parse_args( $args );

			// What is the leaderboard based on
			$this->based_on = ( MYCRED_ENABLE_LOGGING ) ? $this->args['based_on'] : 'balance';
			$this->order    = $this->args['order'];

			// Setup limit
			if ( $this->args['number'] > 0 ) {

				$this->limit = 'LIMIT ' . $this->args['number'];
				if ( $this->args['offset'] != 0 )
					$this->limit = 'LIMIT ' . $this->args['offset'] . ', ' . $this->args['number'];

			}

		}

		/**
		 * Apply Defaults
		 * @since 1.0
		 * @version 1.0
		 */
		public function apply_defaults( $data = array() ) {

			$defaults = array(
				'based_on'     => 'balance',
				'number'       => 25,
				'offset'       => 0,
				'type'         => MYCRED_DEFAULT_TYPE_KEY,
				'timeframe'    => '',
				'to'		   => '',
				'now'          => $this->now,
				'order'        => 'DESC',
				'total'        => 0,
				'exclude_zero' => 1,
				'forced'       => 0,
				'exclude'	   => ''
			);

			return apply_filters( 'mycred_query_leaderboard_args', shortcode_atts( $defaults, $data ), $data, $this );

		}

		/**
		 * Parse Arguments
		 * We have two jobs: Make sure we provide arguments we can understand and
		 * that the arguments we provided are valid.
		 * @since 1.0
		 * @version 1.1
		 */
		public function parse_args( $args = array() ) {

			/**
			 * Populate Query Arguments
			 * @uses mycred_query_leaderboard_args
			 * @see http://codex.mycred.me/filters/mycred_query_leaderboard_args/
			 */
			$args                       = $this->apply_defaults( $args );

			// Generate a unique ID that identifies the leaderboard we are trying to build
			$this->cache_key            = $this->get_cache_key( $args );

			// Based on
			$based_on                   = sanitize_text_field( $args['based_on'] );
			if ( ! MYCRED_ENABLE_LOGGING ) $based_on = 'balance';

			if ( $based_on != 'balance' ) {

				$references = array();
				if ( ! empty( $args['based_on'] ) ) {
					foreach ( explode( ',', $based_on ) as $ref ) {

						$ref = sanitize_key( $ref );
						if ( strlen( $ref ) == 0 ) continue;
						$references[] = $ref;

					}
				}
				$this->references = $references;

				$based_on = 'references';

			}

			$this->args['based_on']     = $based_on;

			// Number or leaderboard size
			$number                     = (int) sanitize_key( $args['number'] );
			if ( $number < -1 )
				$number = -1;

			elseif ( ! is_numeric( $number ) )
				$number = 25;

			elseif ( $number > $this->max_size )
				$number = $this->max_size;

			$this->args['number']       = $number;

			// Option to offset
			$offset                     = (int) sanitize_key( $args['offset'] );
			if ( ! is_numeric( $offset ) )
				$offset = 0;

			$this->args['offset']       = $offset;

			// Point Type
			$point_types                = explode( ',', $args['type'] );
			$list_of_types              = array();
			if ( ! empty( $point_types ) ) {
				foreach ( $point_types as $potential_key ) {

					$type_key = sanitize_key( $potential_key );
					if ( mycred_point_type_exists( $type_key ) || ! in_array( $type_key, $list_of_types ) )
						$list_of_types[] = $type_key;

				}
			}
			if ( empty( $list_of_types ) )
				$list_of_types[] = MYCRED_DEFAULT_TYPE_KEY;

			$this->point_types          = $list_of_types;
			$this->multitype_query      = ( count( $list_of_types ) > 1 ) ? true : false;

			$this->core                 = mycred( $this->point_types[0] );

			// Timeframe
			$this->args['timeframe']    = ( MYCRED_ENABLE_LOGGING ) ? sanitize_text_field( $args['timeframe'] ) : '';
			// To

			$this->args['to']    = ( MYCRED_ENABLE_LOGGING ) ? sanitize_text_field( $args['to'] ) : '';
			$this->args['now']          = ( $args['now'] != '' ) ? absint( $args['now'] ) : $this->now;

			// Order
			$order = strtoupper( sanitize_text_field( $args['order'] ) );
			if ( ! in_array( $order, array( 'ASC', 'DESC' ) ) )
				$order = 'DESC';

			$this->args['order']        = $order;

			// Show total balance
			$this->args['total']        = ( MYCRED_ENABLE_TOTAL_BALANCE ) ? (bool) $args['total'] : false;

			// Exclude zero balances
			$this->args['exclude_zero'] = (bool) $args['exclude_zero'];

			// Force a new leaderboard instead of a cached one (if used)
			$this->args['forced']       = (bool) $args['forced'];
			$this->args['exclude']    = ( $args['exclude'] != '' ) ? sanitize_text_field( $args['exclude'] ) : '';

		}

		/**
		 * Get Leaderboard Results
		 * Returns the leaderboard data in an array form or false if the query results in no data.
		 * @since 1.0
		 * @version 1.0
		 */
		public function get_leaderboard_results( $append_current_user = false ) {

			$results = $this->get_cache();
			if ( $results === false ) {

				global $wpdb;

				$results = $wpdb->get_results( $this->get_db_query(), 'ARRAY_A' );
				if ( empty( $results ) )
					$results = false;

				if ( $results !== false )
					$this->cache_result( $results );

			}

			$this->leaderboard = $results;

			if ( $append_current_user )
				$this->append_current_user();

			$results           = $this->leaderboard;
			$this->leaderboard = apply_filters( 'mycred_get_leaderboard_results', $results, $append_current_user, $this );

		}

		/**
		 * Append Current User
		 * Appends the current logged in user to the end of the leaderboard if the user is not in the results.
		 * This is done separatelly since we can not cache a leaderboard for each user that might view the board.
		 * @since 1.0
		 * @version 1.0
		 */
		public function append_current_user( $return = false ) {

			if ( ! is_user_logged_in() || $this->leaderboard === false || $this->core->exclude_user( $this->user_id ) ) return;

			// First we need to check if the user is already in the leaderboard
			if ( $this->user_in_leaderboard() ) return;

			// User is not in the leaderboard so we need to append him/her to the end of the leaderboard array.
			$new_row             = array( 'ID' => $this->user_id );
			$new_row['position'] = $this->get_users_current_position();
			$new_row['cred']     = $this->get_users_current_value();

			if ( $return )
				return $new_row;

			$this->leaderboard[] = $new_row;

		}

		/**
		 * User In Leaderboard
		 * Checks if a given user or the current user is in the leaderboard.
		 * @since 1.0
		 * @version 1.0
		 */
		public function user_in_leaderboard( $user_id = NULL ) {

			$in_leaderboard = false;
			if ( $this->leaderboard !== false && ! empty( $this->leaderboard ) ) {

				if ( $user_id === NULL || absint( $user_id ) === 0 )
					$user_id = $this->user_id;

				$user_id = absint( $user_id );

				foreach ( $this->leaderboard as $position => $user ) {
					if ( absint( $user['ID'] ) === $user_id ) {
						$in_leaderboard = true;
						break;
					}
				}

			}

			return apply_filters( 'mycred_user_in_leaderboard', $in_leaderboard, $user_id, $this );

		}

		/**
		 * Get Database Query
		 * Returns the SQL query required for generating a leaderboard.
		 * @since 1.0
		 * @version 1.0
		 */
		public function get_db_query() {

			if ( $this->based_on == 'balance' )
				$query = $this->get_balance_db_query();
			else
				$query = $this->get_reference_db_query();

			return $query;

		}

		/**
		 * Get Balance Database Query
		 * Returns the SQL query required for generating a leaderboard that is based on balances.
		 * @since 1.0
		 * @version 1.1
		 */
		public function get_balance_db_query() {

			global $wpdb, $mycred_log_table;

			$query             = '';
			$exclude_filter    = $this->get_excludefilter();
			$exclude_user_filter    = $this->get_exclude_userfilter();
			$multisite_check   = $this->get_multisitefilter();

			/**
			 * Total balance with timeframe
			 * For this, we need to query the myCRED log so we can apply the timeframe.
			 */
			if ( MYCRED_ENABLE_LOGGING && $this->args['total'] && $this->args['timeframe'] != '' ) {

				$time_filter       = $this->get_timefilter();
				$point_type_is     = 'l.ctype = %s';
				$point_type_values = $this->point_types[0];

				// For multiple point types
				if ( count( $this->point_types ) > 1 ) {

					$point_type_is     = 'l.ctype IN ( %s' . str_repeat( ', %s', ( count( $this->point_types ) - 1 ) ) . ' )';
					$point_type_values = $this->point_types;

				}

				$query             = $wpdb->prepare( "
					SELECT l.user_id AS ID, SUM( l.creds ) AS cred 
					FROM {$mycred_log_table} l 
					{$multisite_check} 
					WHERE {$point_type_is} AND ( ( l.creds > 0 ) OR ( l.creds < 0 AND l.ref = 'manual' ) ) 
					{$time_filter}
					{$exclude_filter} 
					{$exclude_user_filter}
					GROUP BY l.user_id
					ORDER BY SUM( l.creds ) {$this->order}, l.user_id ASC 
					{$this->limit};", $point_type_values );

			}

			/**
			 * Current or Total Balance
			 * For this, we will query the usermeta table for the meta_key's.
			 */
			else {

				$point_type_is     = 'l.meta_key = %s';
				$point_type_values = mycred_get_meta_key( $this->point_types[0], ( ( $this->args['total'] ) ? '_total' : '' ) );

				// For multiple point types
				if ( count( $this->point_types ) > 1 ) {

					$point_type_is     = 'l.meta_key IN ( %s' . str_repeat( ', %s', ( count( $this->point_types ) - 1 ) ) . ' )';
					$point_type_values = array();

					foreach ( $this->point_types as $type_key )
						$point_type_values[] = mycred_get_meta_key( $type_key, ( ( $this->args['total'] ) ? '_total' : '' ) );

				}

				$query             = $wpdb->prepare( "
					SELECT DISTINCT u.ID, l.meta_value AS cred 
					FROM {$wpdb->users} u 
					INNER JOIN {$wpdb->usermeta} l ON ( u.ID = l.user_id ) 
					{$multisite_check} 
					WHERE {$point_type_is} 
					{$exclude_filter} 
					{$exclude_user_filter}
					ORDER BY l.meta_value+0 {$this->order}, l.user_id ASC
					{$this->limit};", $point_type_values );

			}

			return apply_filters( 'mycred_get_balance_leaderboard_sql', $query, $this );

		}

		/**
		 * Get Reference Database Query
		 * Returns the SQL query required for generating a leaderboard that is based on references.
		 * @since 1.0
		 * @version 1.1
		 */
		public function get_reference_db_query() {

			global $wpdb, $mycred_log_table;

			$time_filter       = $this->get_timefilter();
			$multisite_check   = $this->get_multisitefilter();
			$exclude_user_filter    = $this->get_exclude_userfilter();

			$reference_is      = 'l.ref = %s';
			$reference_values  = $this->references[0];

			if ( count( $this->references ) > 1 ) {

				$reference_is     = 'l.ref IN ( %s' . str_repeat( ', %s', ( count( $this->references ) - 1 ) ) . ' )';
				$reference_values = $this->references;

			}

			$point_type_is     = 'l.ctype = %s';
			$point_type_values = $this->point_types[0];

			if ( count( $this->point_types ) > 1 ) {

				$point_type_is     = 'l.ctype IN ( %s' . str_repeat( ', %s', ( count( $this->point_types ) - 1 ) ) . ' )';
				$point_type_values = $this->point_types;

			}

			/**
			 * Central Logging
			 * When we are not using Multisite or if we do, but enabled "Central Loggign".
			 */
			if ( mycred_centralize_log() ) {

				$query = $wpdb->prepare( "
					SELECT DISTINCT l.user_id AS ID, SUM( l.creds ) AS cred 
					FROM {$mycred_log_table} l 
					WHERE {$reference_is} AND {$point_type_is} 
					{$time_filter} 
					{$exclude_user_filter}
					GROUP BY l.user_id 
					ORDER BY SUM( l.creds ) {$this->order}, l.user_id ASC 
					{$this->limit};", $reference_values, $point_type_values );

			}

			/**
			 * Multisites
			 * When we are on a multisite, we need to query based on our local users.
			 */
			else {

				$query = $wpdb->prepare( "
					SELECT DISTINCT l.user_id AS ID, SUM( l.creds ) AS cred 
					FROM {$mycred_log_table} l 
					{$multisite_check} 
					WHERE {$reference_is} AND {$point_type_is}
					{$time_filter} 
					{$exclude_user_filter}
					GROUP BY l.user_id 
					ORDER BY SUM( l.creds ) {$this->order}, l.user_id ASC
					{$this->limit};", $reference_values, $point_type_values );

			}

			return apply_filters( 'mycred_get_reference_leaderboard_sql', $query, $this );

		}

		/**
		 * Get Users Leaderboard Position
		 * @since 1.0
		 * @version 1.1
		 */
		public function get_users_current_position( $user_id = NULL, $no_position = '' ) {

			$position          = false;

			// Better safe than sorry
			if ( $user_id === NULL && ! is_user_logged_in() ) return $position;

			if ( $user_id === NULL || absint( $user_id ) === 0 )
				$user_id = $this->user_id;

			global $wpdb, $mycred_log_table;

			$time_filter       = $this->get_timefilter();
			$exclude_filter    = $this->get_excludefilter();
			$exclude_user_filter    = $this->get_exclude_userfilter();
			$multisite_check   = $this->get_multisitefilter();

			$point_type_is     = 'l.ctype = %s';
			$point_type_values = $this->point_types[0];

			if ( count( $this->point_types ) > 1 ) {

				$point_type_is     = 'l.ctype IN ( %s' . str_repeat( ', %s', ( count( $this->point_types ) - 1 ) ) . ' )';
				$point_type_values = $this->point_types;

			}

			/**
			 * Balance Query
			 */
			if ( $this->based_on == 'balance' ) {

				/**
				 * Total balance with timeframe
				 * For this, we need to query the myCRED log so we can apply the timeframe.
				 */
				if ( MYCRED_ENABLE_LOGGING && $this->args['total'] && $this->args['timeframe'] != '' ) {

					$position          = $wpdb->get_var( $wpdb->prepare( "
						SELECT position FROM (
							SELECT s.*, @position := @position + 1 position FROM (
								SELECT l.user_id, sum( l.creds ) TotalPoints FROM {$mycred_log_table} l 
								{$multisite_check}
								WHERE {$point_type_is} AND ( ( l.creds > 0 ) OR ( l.creds < 0 AND l.ref = 'manual' ) ) 
								{$time_filter} 
								{$exclude_filter} 
								{$exclude_user_filter}
								GROUP BY l.user_id
								) s, (SELECT @position := 0) init
							ORDER BY TotalPoints DESC, s.user_id ASC 
						) r 
						WHERE user_id = %d", $point_type_values, $user_id ) );

				}

				/**
				 * Current or Total Balance
				 * For this, we will query the usermeta table for the meta_key's.
				 */
				else {

					$point_type_is     = 'l.meta_key = %s';
					$point_type_values = mycred_get_meta_key( $this->point_types[0], ( ( $this->args['total'] ) ? '_total' : '' ) );

					// For multiple point types
					if ( count( $this->point_types ) > 1 ) {

						$point_type_is     = 'l.meta_key IN ( %s' . str_repeat( ', %s', ( count( $this->point_types ) - 1 ) ) . ' )';
						$point_type_values = array();

						foreach ( $this->point_types as $type_key )
							$point_type_values[] = mycred_get_meta_key( $type_key, ( ( $this->args['total'] ) ? '_total' : '' ) );

					}

					$position          = $wpdb->get_var( $wpdb->prepare( "
						SELECT position FROM (
							SELECT s.*, @position := @position + 1 position FROM (
								SELECT l.user_id, l.meta_value AS Balance FROM {$wpdb->usermeta} l 
								{$multisite_check} 
								WHERE {$point_type_is} 
								{$exclude_filter}
								{$exclude_user_filter}
							) s, (SELECT @position := 0) init
							ORDER BY Balance+0 DESC, s.user_id ASC 
						) r 
						WHERE user_id = %d", $point_type_values, $user_id ) );

				}

			}

			/**
			 * Reference Query
			 */
			elseif ( MYCRED_ENABLE_LOGGING ) {

				$reference_is      = 'AND l.ref = %s';
				$reference_values  = $this->references[0];
				if ( count( $this->references ) > 1 ) {
					$reference_is     = 'AND l.ref IN ( %s' . str_repeat( ', %s', ( count( $this->references ) - 1 ) ) . ' )';
					$reference_values = $this->references;
				}

				$position          = $wpdb->get_var( $wpdb->prepare( "
					SELECT position FROM (
						SELECT s.*, @position := @position + 1 position FROM (
							SELECT l.user_id, sum( l.creds ) TotalPoints FROM {$mycred_log_table} l 
							{$multisite_check}
							WHERE {$point_type_is} AND ( ( l.creds > 0 ) OR ( l.creds < 0 AND l.ref = 'manual' ) ) 
							{$reference_is} 
							{$time_filter} 
							{$exclude_filter} 
							{$exclude_user_filter}
							GROUP BY l.user_id
						) s, (SELECT @position := 0) init
						ORDER BY TotalPoints DESC, s.user_id ASC 
					) r 
					WHERE user_id = %d", $point_type_values, $reference_values, $user_id ) );

			}

			if ( $position === NULL )
				$position = $no_position;

			return apply_filters( 'mycred_get_leaderboard_position', $position, $user_id, $no_position, $this );

		}

		/**
		 * Get Users Leaderboard Value
		 * @since 1.0
		 * @version 1.1
		 */
		public function get_users_current_value( $user_id = NULL ) {

			$value             = 0;

			// Better safe than sorry
			if ( $user_id === NULL && ! is_user_logged_in() ) return $value;

			if ( $user_id === NULL || absint( $user_id ) === 0 )
				$user_id = $this->user_id;

			global $wpdb, $mycred_log_table;

			$time_filter       = $this->get_timefilter();
			$exclude_filter    = $this->get_excludefilter();
			$exclude_user_filter    = $this->get_exclude_userfilter();
			$multisite_check   = $this->get_multisitefilter();

			$point_type_is     = 'l.ctype = %s';
			$point_type_values = $this->point_types[0];

			if ( count( $this->point_types ) > 1 ) {

				$point_type_is     = 'l.ctype IN ( %s' . str_repeat( ', %s', ( count( $this->point_types ) - 1 ) ) . ' )';
				$point_type_values = $this->point_types;

			}

			/**
			 * Balance Query
			 */
			if ( $this->based_on == 'balance' ) {

				/**
				 * Total balance with timeframe
				 * For this, we need to query the myCRED log so we can apply the timeframe.
				 */
				if ( MYCRED_ENABLE_LOGGING && $this->args['total'] && $this->args['timeframe'] != '' ) {

					$value             = $wpdb->get_var( $wpdb->prepare( "
						SELECT TotalPoints FROM (
							SELECT s.*, @position := @position + 1 position FROM (
								SELECT l.user_id, sum( l.creds ) TotalPoints FROM {$mycred_log_table} l 
								{$multisite_check}
								WHERE {$point_type_is} AND ( ( l.creds > 0 ) OR ( l.creds < 0 AND l.ref = 'manual' ) ) 
								{$time_filter} 
								{$exclude_filter} 
								{$exclude_user_filter}
								GROUP BY l.user_id
								) s, (SELECT @position := 0) init
							ORDER BY TotalPoints DESC, s.user_id ASC 
						) r 
						WHERE user_id = %d", $point_type_values, $user_id ) );

				}

				/**
				 * Current or Total Balance
				 * For this, we will query the usermeta table for the meta_key's.
				 */
				else {

					$point_type_is     = 'l.meta_key = %s';
					$point_type_values = mycred_get_meta_key( $this->point_types[0], ( ( $this->args['total'] ) ? '_total' : '' ) );

					// For multiple point types
					if ( count( $this->point_types ) > 1 ) {

						$point_type_is     = 'l.meta_key IN ( %s' . str_repeat( ', %s', ( count( $this->point_types ) - 1 ) ) . ' )';
						$point_type_values = array();

						foreach ( $this->point_types as $type_key )
							$point_type_values[] = mycred_get_meta_key( $type_key, ( ( $this->args['total'] ) ? '_total' : '' ) );

					}

					$value             = $wpdb->get_var( $wpdb->prepare( "
						SELECT Balance FROM (
							SELECT s.*, @position := @position + 1 position FROM (
								SELECT l.user_id, l.meta_value AS Balance FROM {$wpdb->usermeta} l 
								{$multisite_check} 
								WHERE {$point_type_is} 
								{$exclude_filter}
								{$exclude_user_filter}
							) s, (SELECT @position := 0) init
							ORDER BY Balance+0 DESC, s.user_id ASC 
						) r 
						WHERE user_id = %d", $point_type_values, $user_id ) );

				}

			}

			/**
			 * Reference Query
			 */
			elseif ( MYCRED_ENABLE_LOGGING ) {

				$reference_is      = 'AND l.ref = %s';
				$reference_values  = $this->references[0];
				if ( count( $this->references ) > 1 ) {
					$reference_is     = 'AND l.ref IN ( %s' . str_repeat( ', %s', ( count( $this->references ) - 1 ) ) . ' )';
					$reference_values = $this->references;
				}

				$value             = $wpdb->get_var( $wpdb->prepare( "
					SELECT TotalPoints FROM (
						SELECT s.*, @position := @position + 1 position FROM (
							SELECT l.user_id, sum( l.creds ) TotalPoints FROM {$mycred_log_table} l 
							{$multisite_check}
							WHERE {$point_type_is} AND ( ( l.creds > 0 ) OR ( l.creds < 0 AND l.ref = 'manual' ) ) 
							{$reference_is} 
							{$time_filter} 
							{$exclude_filter}
							{$exclude_user_filter}
							GROUP BY l.user_id
						) s, (SELECT @position := 0) init
						ORDER BY TotalPoints DESC, s.user_id ASC 
					) r 
					WHERE user_id = %d", $point_type_values, $reference_values, $user_id ) );

			}

			return apply_filters( 'mycred_get_users_leaderboard_value', $value, $user_id, $this );

		}

		/**
		 * Get Time Filter
		 * Generates the required SQL query for filtering results based on time.
		 * Can only be used when the leaderboard is based either on total balance or based on references.
		 * @since 1.0
		 * @version 1.0
		 */
		public function get_timefilter() {

			$query = '';
			if ( $this->args['timeframe'] === NULL || strlen( $this->args['timeframe'] ) == 0 ) return $query;

			global $wpdb;

			// Filter: Daily
			if ( $this->args['timeframe'] == 'today' ) {
				$query = $wpdb->prepare( "AND l.time BETWEEN %d AND %d", strtotime( 'today midnight', $this->now ), $this->args['now'] );
			}
			// Filter: Weekly
			elseif ( $this->args['timeframe'] == 'this-week' ) {

				// Start of the week based of our settings
				$days = array( 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday' );

				$week_starts = get_option( 'start_of_week' );

				if ( $days[ $week_starts ] == date('l') )
					$week_starts = 'today midnight';
				else
					$week_starts = 'last ' . $days[ $week_starts ];

				$query = $wpdb->prepare( "AND l.time BETWEEN %d AND %d", strtotime( $week_starts, $this->now ), $this->args['now'] );
			}
			// Filter: Monthly
			elseif ( $this->args['timeframe'] == 'this-month' ) {
				$query = $wpdb->prepare( "AND l.time BETWEEN %d AND %d", strtotime( date( 'Y-m-01', $this->now ) ), $this->args['now'] );
			}
			else {

				$start_from = strtotime( $this->args['timeframe'], $this->now );
				
				if ( $start_from !== false && $start_from > 0 ) {
					$end_to = $this->args['to'] != '' ? strtotime(date($this->args['to']." 23:59:59")) : 0;
					if ($end_to === false || $end_to <= 0 ) {
						$end_to = $this->args['now'];
					}
					$query = $wpdb->prepare( "AND l.time BETWEEN %d AND %d", $start_from, $end_to );
				}

			}

			return apply_filters( 'mycred_leaderboard_time_filter', $query, $this );

		}

		/**
		 * Get Exclude Filter
		 * Generates the required SQL query for filtering results based on if zero balances should
		 * be part of the leaderboard or not. By default, myCRED will not give a user a balance until they
		 * gain or lose points. A user that has no balance and is not excluded, is considered to have zero balance.
		 * @since 1.0
		 * @version 1.0
		 */
		public function get_excludefilter() {

			global $wpdb;

			// Option to exclude zero balances
			$query = '';
			if ( $this->args['exclude_zero'] ) {

				$balance_format = '%d';
				if ( isset( $this->core->format['decimals'] ) && $this->core->format['decimals'] > 0 ) {
					$length         = absint( 65 - $this->core->format['decimals'] );
					$balance_format = 'CAST( %f AS DECIMAL( ' . $length . ', ' . $this->core->format['decimals'] . ' ) )';
				}

				if ( ! $this->args['total'] )
					$query = $wpdb->prepare( "AND l.meta_value != {$balance_format}", $this->core->zero() );

			}

			return apply_filters( 'mycred_leaderboard_exclude_filter', $query, $this );

		}

		/**
		 * Get Exclude User Filter
		 * Generates the required SQL query for filtering results based on user ids or roles that should
		 * be part of the leaderboard or not. By default, myCRED will not exclude any user.
		 * @since 1.0
		 * @version 1.0
		 */
		public function get_exclude_userfilter() {

			global $wpdb;

			// Option to exclude zero balances
			$query = '';
			$checkIDs='~^\d+(,\d+)?$~';
			$exclude=$this->args['exclude'];

			if (!empty($exclude)) {
				if(preg_match($checkIDs,$exclude)){

					$exclude=$this->args['exclude'];
				}
				else{
					$exclude=mycred_leaderboard_exclude_role($exclude);
				}
				$query = $wpdb->prepare( "AND l.user_id NOT IN (%s) ",$exclude);
			}
			return apply_filters( 'mycred_leaderboard_exclude_user_filter', $query, $this );

		}

		/**
		 * Get Multisite Filter
		 * Generates the required SQL query for filtering results based on our multisite setup.
		 * Will return an empty string if we are not using multisites or if we have centralized the log.
		 * @since 1.0
		 * @version 1.0
		 */
		public function get_multisitefilter() {

			global $wpdb;

			$multisite_check = "";
			if ( ! mycred_centralize_log() ) {

				$blog_id         = absint( $GLOBALS['blog_id'] );
				$multisite_check = "LEFT JOIN {$wpdb->usermeta} cap ON ( l.user_id = cap.user_id AND cap.meta_key = 'cap.wp_{$blog_id}_capabilities' )";

			}

			return apply_filters( 'mycred_leaderboard_musite_filter', $multisite_check, $this );

		}

		/**
		 * Get Cache Key
		 * @since 1.0
		 * @version 1.0
		 */
		public function get_cache_key( $args = array() ) {

			if ( empty( $args ) ) $args = $this->args;
			else $args = $this->apply_defaults( $args );

			unset( $args['now'] );

			return 'leaderboard-' . md5( serialize( $args ) );

		}

		/**
		 * Get Cached Leaderboard
		 * @since 1.0
		 * @version 1.1
		 */
		public function get_cache() {

			$data         = false;
			$key          = $this->get_cache_key();

			// Object caching we will always do
			$object_cache = wp_cache_get( $key, MYCRED_SLUG );
			if ( $object_cache !== false && is_array( $object_cache ) ) {

				if ( $this->args['forced'] )
					wp_cache_delete( $key, MYCRED_SLUG );

				else $data = $object_cache;

			}

			return apply_filters( 'mycred_get_cached_leaderboard', $data, $this );

		}

		/**
		 * Cache Results
		 * @since 1.0
		 * @version 1.1
		 */
		public function cache_result( $data = array() ) {

			if ( $this->args['forced'] ) return;

			$key        = $this->get_cache_key();
			$cache_keys = mycred_get_option( MYCRED_SLUG . '-cache-leaderboard-keys', array() );

			if ( empty( $cache_keys ) || ( ! empty( $cache_keys ) && ! in_array( $key, $cache_keys ) ) ) {

				$cache_keys[] = $key;

				mycred_update_option( MYCRED_SLUG . '-cache-leaderboard-keys', $cache_keys );

			}

			wp_cache_set( $key, $data, MYCRED_SLUG );

			do_action( 'mycred_cache_leaderboard', $data, $this );

		}

		/**
		 * Is Leaderboard
		 * @since 1.0
		 * @version 1.0
		 */
		public function is_leaderboard( $args = array() ) {

			if ( $this->cache_key === false ) return false;

			return ( $this->cache_key == $this->get_cache_key( $args ) );

		}

		/**
		 * Render Leaderboard
		 * @since 1.0
		 * @version 1.0
		 */
		public function render( $args = array(), $content = '' ) {

			extract( shortcode_atts( array(
				'wrap'         => 'li',
				'template'     => '#%position% %user_profile_link% %cred_f%',
				'nothing'      => 'Leaderboard is empty',
			), $args ) );

			$mycred = mycred( $args['type'] );
	
			$output = '';

			// Leaderboard is empty
			if ( $this->leaderboard === false || empty( $this->leaderboard ) ) {

				$output .= '<p class="mycred-leaderboard-none">' . $nothing . '</p>';

			}

			// Got results to show
			else {

				// Wrapper
				if ( $wrap == 'li' )
					$output .= '<ol class="myCRED-leaderboard list-unstyled">';

				// Loop
				foreach ( $this->leaderboard as $position => $user ) {

					// Prep
					$class   = array();
					$row     = $position;

					if ( array_key_exists( 'position', $user ) )
						$position = $user['position'];

					else {

						if ( $this->args['offset'] != '' && $this->args['offset'] > 0 )
							$position = $position + $this->args['offset'];

						$position++;

					}

					// Classes
					$class[] = 'item-' . $row;
					if ( $position == 0 )
						$class[] = 'first-item';

					if ( $this->user_id > 0 && $user['ID'] == $this->user_id )
						$class[] = 'current-user';

					if ( is_numeric( $position ) && $position % 2 != 0 )
						$class[] = 'alt';

					$row_template = $template;
					if ( ! empty( $content ) )
						$row_template = $content;

					// Template Tags
					$layout  = str_replace( array( '%ranking%', '%position%' ), $position, $row_template );

					$layout  = $this->core->template_tags_amount( $layout, $user['cred'] );
					$layout  = $this->core->template_tags_user( $layout, $user['ID'] );

					//Point type Image
					if( $args['image'] && $mycred->image_url )
						$layout = str_replace( '%image%', "<img src='{$mycred->image_url}' style='margin-right: 5px;' class='mycred-my-balance-image-".$args["type"]."' width='20px' />", $layout );
					else
						$layout = str_replace( '%image%', '', $layout );
					
					// Wrapper
					if ( ! empty( $wrap ) )
						$layout = '<' . $wrap . ' class="%classes%">' . $layout . '</' . $wrap . '>';

					$layout  = str_replace( '%classes%', apply_filters( 'mycred_ranking_classes', implode( ' ', $class ), $this ), $layout );
					$layout  = apply_filters( 'mycred_ranking_row', $layout, $template, $user, $position, $this );

					$output .= $layout . "\n";

				}

				if ( $wrap == 'li' )
					$output .= '</ol>';

			}

			return apply_filters( 'mycred_leaderboard', $output, $args, $this );

		}

	}
endif;

/**
 * Get Leaderboard
 * @since 1.7.9.1
 * @version 1.1
 */
if ( ! function_exists( 'mycred_get_leaderboard' ) ) :
	function mycred_get_leaderboard( $args = array() ) {

		global $mycred_leaderboard;

		if ( isset( $mycred_leaderboard )
			&& ( $mycred_leaderboard instanceof myCRED_Query_Leaderboard )
			&& ( $mycred_leaderboard->is_leaderboard( $args ) )
		) {

			return $mycred_leaderboard;

		}

		$mycred_leaderboard = new myCRED_Query_Leaderboard( $args );

		return $mycred_leaderboard;

	}
endif;
