<?php
if ( ! defined( 'myCRED_VERSION' ) ) exit;

/**
 * Query Log
 * @see http://codex.mycred.me/classes/mycred_query_log/ 
 * @since 0.1
 * @version 1.8
 */
if ( ! class_exists( 'myCRED_Query_Log' ) ) :
	class myCRED_Query_Log {

		public $cache_key      = false;
		public $now            = 0;
		public $render_mode    = true;
		public $args           = array();
		public $request        = '';
		public $num_rows       = 0;
		public $max_num_pages  = 1;
		public $total_rows     = 0;
		public $where          = '';
		public $sortby         = '';

		public $results        = array();

		public $headers        = array();
		public $hidden_headers = array();
		public $core;
		public $is_admin       = false;
		public $references     = array();
		public $types          = array();
		public $refs           = array();

		/**
		 * Construct
		 */
		public function __construct( $args = array(), $array = false ) {

			if ( ! MYCRED_ENABLE_LOGGING ) return false;

			$this->now        = current_time( 'timestamp' );
			$this->references = mycred_get_all_references();

			$this->core                             = mycred();
			$this->types[ MYCRED_DEFAULT_TYPE_KEY ] = $this->core;

			// Parse and validate the given args
			$this->parse_args( $args );

			// Caching of results
			$cache_results = $this->args['cache_results'];
			if ( function_exists( 'is_admin' ) && is_admin() ) $cache_results = false;

			if ( $cache_results ) {

				$cached_results  = $this->get_cache();

				if ( $cached_results !== false ) {

					$cached_results   = maybe_unserialize( $cached_results );

					$this->request    = $cached_results['request'];
					$this->results    = $cached_results['results'];
					$this->num_rows   = $cached_results['num_rows'];
					$this->total_rows = $cached_results['total_rows'];
					$this->max_num_pages = (int)$cached_results['max_num_pages'];

					if ( $this->args['number'] !== NULL && $this->args['number'] < 0 )
						$this->max_num_pages = ceil( $this->num_rows / $this->args['number'] );

					return;

				}

			}

			global $wpdb, $mycred_log_table;

			/**
			 * Results Return Option
			 * Added in 1.7.5, this allows us to use this class to return specific sets of information.
			 * If fields is set to anything but "all", the results table can not be used.
			 *

				 // Everything (default)
				 fields=all
				 ids=0 (depreciated)

				 // Return entry ids
				 fields=ids
				 ids=1 (depreciated)

				 // Column
				 fields=user_id
				 'fields' => array( 'user_id' )

				 // Multiple columns
				 fields=id,user_id,creds
				 'fields' => array( 'id', 'user_id', 'creds' )

			 */
			$select        = '*';
			$get_results   = true;
			$get_column    = false;

			if ( $this->args['fields'] !== NULL && ! empty( $this->args['fields'] ) ) {

				// Convert string queries to array queries
				if ( ! is_array( $this->args['fields'] ) ) {

					$columns = ( ( count( explode( ',', $this->args['fields'] ) ) > 0 ) ? explode( ',', $this->args['fields'] ) : array( $this->args['fields'] ) );

					// Return one specific column
					if ( count( $columns ) == 1 )
						$this->args['fields'] = $columns[0];

					// Return multiple columns
					elseif ( count( $columns ) > 1 )
						$this->args['fields'] = $columns;

				}

				// All - default
				if ( ! is_array( $this->args['fields'] ) && $this->args['fields'] == 'all' ) {

					$select            = '*';
					$this->render_mode = true;
					$get_results       = true;
					$get_column        = false;
				}

				// Single column
				elseif ( ( is_array( $this->args['fields'] ) && count( $this->args['fields'] ) == 1 ) || ! is_array( $this->args['fields'] ) ) {

					$select            = ( ( is_array( $this->args['fields'] ) ) ? esc_sql( $this->args['fields'][0] ) : $this->args['fields'] );
					$this->render_mode = false;
					$get_results       = false;
					$get_column        = true;

				}

				// Multiple columns
				elseif ( is_array( $this->args['fields'] ) ) {

					$select            = implode( ', ', $this->args['fields'] );
					$this->render_mode = false;
					$get_results       = true;
					$get_column        = false;

				}

			}

			/**
			 * Number of results to return
			 *

				 // Everything
				 number=-1

				 // Entries per page
				 number=10

				 // Offset
				 offset=10&number=10

			 */
			$number        = $this->args['number'];
			if ( $number < -1 ) $number = abs( $number );
			elseif ( $number == 0 || $number == -1 ) $number = NULL;

			/**
			 * Set Query Limit
			 */
			
			$limits = '';
			if ( $number !== NULL ) {

				$page = 1;
				if ( $this->args['paged'] !== NULL ) {
					$page = absint( $this->args['paged'] );
					if ( ! $page )
						$page = 1;
				}

				if ( $this->args['offset'] == '' ) {
					$pgstrt = ($page - 1) * $number . ', ';
				}

				else {
					$offset = absint( $this->args['offset'] );
					$pgstrt = $offset . ', ';
				}

				$limits = 'LIMIT ' . $pgstrt . $number;

			}

			$found_rows    = '';
			if ( $limits != '' ) $found_rows = 'SQL_CALC_FOUND_ROWS';

			$this->request = "SELECT {$found_rows} {$select} FROM {$mycred_log_table} {$this->where} {$this->sortby} {$limits};";

			/**
			 * Populate Results
			 * Based on what we selected to return with the "fields" argument.
			 */
			if ( $get_column ) {

				$this->results = $wpdb->get_col( $this->request );

			}

			else {

				$this->results = $wpdb->get_results( $this->request, $array ? ARRAY_A : OBJECT );

			}

			/**
			 * Calculate rows and max number of pages for navigation
			 */
			if ( $this->render_mode ) {

				if ( $limits != '' )
					$this->num_rows = $wpdb->get_var( 'SELECT FOUND_ROWS()' );
				else
					$this->num_rows = count( $this->results );

				if ( $limits != '' )
					$this->max_num_pages = ceil( $this->num_rows / $number );

			}

			$this->total_rows = $wpdb->get_var( "SELECT COUNT(*) FROM {$mycred_log_table}" );

			// Cache results
			if ( $cache_results ) {

				$new_cache = array(
					'request'       => $this->request,
					'results'       => $this->results,
					'num_rows'      => $this->num_rows,
					'total_rows'    => $this->total_rows,
                    'max_num_pages' => $this->max_num_pages
				);

				$this->cache_result( $new_cache );

			}

		}

		/**
		 * Parse Arguments
		 * We have two jobs: Make sure we provide arguments we can understand and
		 * that the arguments we provided are valid.
		 * @since 1.8
		 * @version 1.0
		 */
		public function parse_args( $args = array() ) {

			/**
			 * Populate Query Arguments
			 * @uses mycred_query_log_args
			 * @see http://codex.mycred.me/filters/mycred_query_log_args/
			 */
			$defaults        = array(
				'entry_id'      => NULL,
				'user_id'       => NULL,
				'ctype'         => MYCRED_DEFAULT_TYPE_KEY,
				'time'          => NULL,
				'ref'           => NULL,
				'ref_id'        => NULL,
				'amount'        => NULL,
				's'             => NULL,
				'data'          => NULL,
				'number'        => 25,
				'offset'        => '',
				'orderby'       => 'time',
				'order'         => 'DESC',
				'ids'           => false,  // depreciated as of 1.7.5
				'fields'        => 'all',  // in favor for fields
				'cache_results' => true,
				'paged'         => $this->get_pagenum()
			);
			$this->args      = apply_filters( 'mycred_query_log_args', wp_parse_args( $args, $defaults ), $defaults );

			global $wpdb, $mycred_log_table;

			$select          = $where = $sortby = $limits = '';
			$wheres          = array();

			/**
			 * Setup Point Format
			 * Make sure the core property is loaded for the correct point type as this will
			 * determen how we format point related values. When we have a query for multiple point types,
			 * the default type dictates formating. If the default type does not use decimals, neither does any other type.
			 */
			$format          = '%d';
			if ( isset( $this->core->format['decimals'] ) && $this->core->format['decimals'] > 0 ) {
				$length = 65 - $this->core->format['decimals'];
				$format = 'CAST( %f AS DECIMAL( ' . $length . ', ' . $this->core->format['decimals'] . ' ) )';
			}

			if ( $this->args['ctype'] !== NULL && ! empty( $this->args['ctype'] ) ) {

				// Convert string queries to array queries
				if ( ! is_array( $this->args['ctype'] ) ) {

					$point_types = ( ( count( explode( ',', $this->args['ctype'] ) ) > 0 ) ? explode( ',', $this->args['ctype'] ) : array( $this->args['ctype'] ) );

					// Single point type query
					if ( count( $point_types ) == 1 )
						$this->args['ctype'] = array( 'ids' => $point_types[0], 'compare' => '=' );

					// Multiple point types query
					elseif ( count( $point_types ) > 1 ) {

						$this->args['ctype'] = array( 'ids' => $point_types, 'compare' => 'IN' );

					}

				}

				// Check if this is a single point type query
				// We need to do this to see if the query is for a point type that is not the default one
				// This is to format values correctly based on the point types setup
				if ( ( is_array( $this->args['ctype']['ids'] ) && count( $this->args['ctype']['ids'] ) == 1 ) || ! is_array( $this->args['ctype']['ids'] ) ) {

					if ( ! is_array( $this->args['ctype']['ids'] ) )
						$requested_point_type = $this->args['ctype']['ids'];
					else
						$requested_point_type = $this->args['ctype']['ids'][0];

					// If this is a query for a custom point type, change the balance format now
					if ( $requested_point_type != $this->core->cred_id ) {
						$this->core = mycred( $requested_point_type );
						if ( isset( $this->core->format['decimals'] ) && $this->core->format['decimals'] > 0 ) {
							$length = 65 - $this->core->format['decimals'];
							$format = 'CAST( %f AS DECIMAL( ' . $length . ', ' . $this->core->format['decimals'] . ' ) )';
						}
					}

					if ( ! array_key_exists( $requested_point_type, $this->types ) )
						$this->types[ $requested_point_type ] = $this->core;

				}

				// Indicate that this query uses multiple point types.
				// Mainly used to load the correct label for the point amount column in the table
				elseif ( is_array( $this->args['ctype']['ids'] ) && count( $this->args['ctype']['ids'] ) > 1 ) {

					// Populate the types property with each point type object
					// This is used so the correct point type format is shown in the table for each row
					foreach ( $this->args['ctype']['ids'] as $point_type_key ) {
						if ( ! array_key_exists( $point_type_key, $this->types ) )
							$this->types[ $point_type_key ] = mycred( $point_type_key );
					}

				}

			}

			/**
			 * Entry ID Query
			 *

				 // Singular check
				 entry_id=1
				 'entry_id' => array(
					 'ids'     => 1,
					 'compare' => '='
				 )
				 'entry_id' => array(
					 'ids'     => 1,
					 'compare' => '!='
				 )

				 // Multiple checks
				 entry_id=1,2,3
				 'entry_id' => array(
					 'ids'     => array( 1, 2, 3 )
					 'compare' => 'IN'
				 )

			 */
			if ( $this->args['entry_id'] !== NULL && ! empty( $this->args['entry_id'] ) ) {

				// Convert string queries to array queries
				if ( ! is_array( $this->args['entry_id'] ) ) {

					$entry_ids = ( ( count( explode( ',', $this->args['entry_id'] ) ) > 0 ) ? explode( ',', $this->args['entry_id'] ) : array( $this->args['entry_id'] ) );

					// Single user query
					if ( count( $entry_ids ) == 1 )
						$this->args['entry_id'] = array( 'ids' => $entry_ids[0], 'compare' => '=' );

					// Multiple user query
					elseif ( count( $entry_ids ) > 1 )
						$this->args['entry_id'] = array( 'ids' => $entry_ids, 'compare' => 'IN' );

				}

				// Make sure query is properly formatted
				if ( array_key_exists( 'ids', $this->args['entry_id'] ) && array_key_exists( 'compare', $this->args['entry_id'] ) ) {

					// IN or NOT IN comparisons
					if ( in_array( $this->args['entry_id']['compare'], array( 'IN', 'NOT IN' ) ) && is_array( $this->args['entry_id']['ids'] ) )
						$wheres[] = $wpdb->prepare( "id ".$this->args['entry_id']['compare']." ( %d" . str_repeat( ", %d", ( count( $this->args['entry_id']['ids'] ) - 1 ) ) . " )", $this->args['entry_id']['ids'] );

					// All other supported comparisons
					elseif ( in_array( $this->args['entry_id']['compare'], array( '=', '!=' ) ) && ! is_array( $this->args['entry_id']['ids'] ) ) {

						$compare  = esc_sql( $this->args['entry_id']['compare'] );
						$wheres[] = $wpdb->prepare( "id {$compare} %d", absint( $this->args['entry_id']['ids'] ) );

					}

				}

			}

			/**
			 * Point Type Query
			 *

				 // Singular check
				 ctype=mycred_default
				 'ctype' => array(
					 'ids'     => 'mycred_default',
					 'compare' => '='
				 )
				 'ctype' => array(
					 'ids'     => 'mycred_default',
					 'compare' => '!='
				 )

				 // Multiple checks
				 ctype=mycred_default,custom_point_type
				 'ctype' => array(
					 'ids'     => array( 'mycred_default', 'custom_point_type' )
					 'compare' => 'IN'
				 )

			 */
			if ( $this->args['ctype'] !== NULL && ! empty( $this->args['ctype'] ) ) {

				// Make sure query is properly formatted
				if ( array_key_exists( 'ids', $this->args['ctype'] ) && array_key_exists( 'compare', $this->args['ctype'] ) ) {

					// IN or NOT IN comparisons
					if ( in_array( $this->args['ctype']['compare'], array( 'IN', 'NOT IN' ) ) && is_array( $this->args['ctype']['ids'] ) )
						$wheres[] = $wpdb->prepare( "ctype IN ( %s" . str_repeat( ", %s", ( count( $this->args['ctype']['ids'] ) - 1 ) ) . " )", $this->args['ctype']['ids'] );

					// All other supported comparisons
					elseif ( in_array( $this->args['ctype']['compare'], array( '=', '!=' ) ) && ! is_array( $this->args['ctype']['ids'] ) ) {

						$compare  = esc_sql( $this->args['ctype']['compare'] );
						$wheres[] = $wpdb->prepare( "ctype {$compare} %s", sanitize_key( $this->args['ctype']['ids'] ) );

					}

				}

			}

			/**
			 * User ID Query
			 *

				 // Singular check
				 user_id=1
				 'user_id' => array(
					 'ids'     => 1,
					 'compare' => '='
				 )
				 'user_id' => array(
					 'ids'     => 1,
					 'compare' => '!='
				 )

				 // Multiple checks
				 user_id=1,2,3
				 'user_id' => array(
					 'ids'     => array( 1, 2, 3 )
					 'compare' => 'IN'
				 )

			 */
			if ( $this->args['user_id'] !== NULL && ! empty( $this->args['user_id'] ) ) {

				// Convert string queries to array queries
				if ( ! is_array( $this->args['user_id'] ) ) {

					$user_ids = ( ( count( explode( ',', $this->args['user_id'] ) ) > 0 ) ? explode( ',', $this->args['user_id'] ) : array( $this->args['user_id'] ) );

					// Single user query
					if ( count( $user_ids ) == 1 )
						$this->args['user_id'] = array( 'ids' => $user_ids[0], 'compare' => '=' );

					// Multiple user query
					elseif ( count( $user_ids ) > 1 )
						$this->args['user_id'] = array( 'ids' => $user_ids, 'compare' => 'IN' );

				}

				// Make sure query is properly formatted
				if ( array_key_exists( 'ids', $this->args['user_id'] ) && array_key_exists( 'compare', $this->args['user_id'] ) ) {

					// IN or NOT IN comparisons
					if ( in_array( $this->args['user_id']['compare'], array( 'IN', 'NOT IN' ) ) && is_array( $this->args['user_id']['ids'] ) )
						$wheres[] = $wpdb->prepare( "user_id IN ( %d" . str_repeat( ", %d", ( count( $this->args['user_id']['ids'] ) - 1 ) ) . " )", $this->args['user_id']['ids'] );

					// All other supported comparisons
					elseif ( in_array( $this->args['user_id']['compare'], array( '=', '!=' ) ) && ! is_array( $this->args['user_id']['ids'] ) ) {

						$compare  = esc_sql( $this->args['user_id']['compare'] );
						$wheres[] = $wpdb->prepare( "user_id {$compare} %d", absint( $this->args['user_id']['ids'] ) );

					}

				}

			}

			/**
			 * Reference Query
			 *

				 // Singular check
				 ref=approved_comment
				 'ref' => array(
					 'ids'     => 'approved_comment',
					 'compare' => '='
				 )
				 'ref' => array(
					 'ids'     => 'approved_comment',
					 'compare' => '!='
				 )

				 // Multiple checks
				 ref=approved_comment,published_content
				 'ref' => array(
					 'ids'     => array( 'approved_comment', 'published_content' )
					 'compare' => 'IN'
				 )

			 */
			if ( $this->args['ref'] !== NULL && ! empty( $this->args['ref'] ) ) {

				// Convert string queries to array queries
				if ( ! is_array( $this->args['ref'] ) ) {

					$references = ( ( count( explode( ',', $this->args['ref'] ) ) > 0 ) ? explode( ',', $this->args['ref'] ) : array( $this->args['ref'] ) );

					// Single reference query
					if ( count( $references ) == 1 )
						$this->args['ref'] = array( 'ids' => $references[0], 'compare' => '=' );

					// Multiple reference query
					elseif ( count( $references ) > 1 )
						$this->args['ref'] = array( 'ids' => $references, 'compare' => 'IN' );

				}

				// Make sure query is properly formatted
				if ( array_key_exists( 'ids', $this->args['ref'] ) && array_key_exists( 'compare', $this->args['ref'] ) ) {

					// IN or NOT IN comparisons
					if ( in_array( $this->args['ref']['compare'], array( 'IN', 'NOT IN' ) ) && is_array( $this->args['ref']['ids'] ) )
						$wheres[] = $wpdb->prepare( "ref IN ( %s" . str_repeat( ", %s", ( count( $this->args['ref']['ids'] ) - 1 ) ) . " )", $this->args['ref']['ids'] );

					// All other supported comparisons
					elseif ( in_array( $this->args['ref']['compare'], array( '=', '!=' ) ) && ! is_array( $this->args['ref']['ids'] ) ) {

						$compare  = esc_sql( $this->args['ref']['compare'] );
						$wheres[] = $wpdb->prepare( "ref {$compare} %s", sanitize_key( $this->args['ref']['ids'] ) );

					}

				}

			}

			/**
			 * Reference ID Query
			 *

				 // Singular check
				 ref_id=1
				 'ref_id' => array(
					 'ids'     => 1,
					 'compare' => '='
				 )
				 'ref_id' => array(
					 'ids'     => 1,
					 'compare' => '!='
				 )

				 // Multiple checks
				 ref_id=1,2,3
				 'ref_id' => array(
					 'ids'     => array( 1, 2, 3 )
					 'compare' => 'IN'
				 )

			 */
			if ( $this->args['ref_id'] !== NULL && ! empty( $this->args['ref_id'] ) ) {

				// Convert string queries to array queries
				if ( ! is_array( $this->args['ref_id'] ) ) {

					$reference_ids = ( ( count( explode( ',', $this->args['ref_id'] ) ) > 0 ) ? explode( ',', $this->args['ref_id'] ) : array( $this->args['ref_id'] ) );

					// Single id query
					if ( count( $reference_ids ) == 1 )
						$this->args['ref_id'] = array( 'ids' => $reference_ids[0], 'compare' => '=' );

					// Multiple id query
					elseif ( count( $reference_ids ) > 1 )
						$this->args['ref_id'] = array( 'ids' => $reference_ids, 'compare' => 'IN' );

				}

				// Make sure query is properly formatted
				if ( array_key_exists( 'ids', $this->args['ref_id'] ) && array_key_exists( 'compare', $this->args['ref_id'] ) ) {

					// IN or NOT IN comparisons
					if ( in_array( $this->args['ref_id']['compare'], array( 'IN', 'NOT IN' ) ) && is_array( $this->args['ref_id']['ids'] ) )
						$wheres[] = $wpdb->prepare( "ref_id IN ( %d" . str_repeat( ", %d", ( count( $this->args['ref_id']['ids'] ) - 1 ) ) . " )", $this->args['ref_id']['ids'] );

					// All other supported comparisons
					elseif ( in_array( $this->args['ref_id']['compare'], array( '=', '!=', '>', '>=', '<', '<=' ) ) && ! is_array( $this->args['ref_id']['ids'] ) ) {

						$compare  = esc_sql( $this->args['ref_id']['compare'] );
						$wheres[] = $wpdb->prepare( "ref_id {$compare} %d", absint( $this->args['ref_id']['ids'] ) );

					}

				}

			}

			/**
			 * Amount Query
			 *

				 // Comparisons
				 'amount' => array(
					 'num'     => 10,
					 'compare' => '<'
				 )

				 // Between (Range)
				 'amount' => array(
					 'num'     => array( 1, 10 ),
					 'compare' => 'BETWEEN'
				 )

				 // Specific amount
				 amount=10
				 'amount' => array(
					 'num'     => 10,
					 'compare' => '='
				 )

				 // One of these (list)
				 amount=1,10,12,14
				 'amount' => array(
					 'num'     => array( 1, 10, 12, 14 ),
					 'compare' => 'IN'
				 )

			 */
			if ( $this->args['amount'] !== NULL && ! empty( $this->args['amount'] ) ) {

				// Convert string queries to array queries
				if ( ! is_array( $this->args['amount'] ) ) {

					$point_value = ( ( count( explode( ',', $this->args['amount'] ) ) > 0 ) ? explode( ',', $this->args['amount'] ) : array( $this->args['amount'] ) );

					// Single amount query
					if ( count( $point_value ) == 1 )
						$this->args['amount'] = array( 'num' => $point_value[0], 'compare' => '=' );

					// Multiple amounts query
					elseif ( count( $point_value ) > 1 )
						$this->args['amount'] = array( 'num' => $point_value, 'compare' => 'IN' );

				}

				// Make sure query is properly formatted
				if ( array_key_exists( 'num', $this->args['amount'] ) && array_key_exists( 'compare', $this->args['amount'] ) ) {

					// Between (requires num to be an array)
					if ( in_array( $this->args['amount']['compare'], array( 'BETWEEN', 'NOT BETWEEN' ) ) && is_array( $this->args['amount']['num'] ) ) {

						$between  = esc_sql( $this->args['amount']['compare'] );
						$wheres[] = $wpdb->prepare( "creds {$between} {$format} AND {$format}", $this->args['amount']['num'] );

					}

					// IN or NOT IN comparisons
					elseif ( in_array( $this->args['amount']['compare'], array( 'IN', 'NOT IN' ) ) && is_array( $this->args['amount']['num'] ) )
						$wheres[] = $wpdb->prepare( "creds IN ( {$format}" . str_repeat( ',' . $format, ( count( $this->args['amount']['num'] ) - 1 ) ) . " )", $this->args['amount']['num'] );

					// All other supported comparisons
					elseif ( in_array( $this->args['amount']['compare'], array( '=', '!=', '>', '>=', '<', '<=' ) ) && ! is_array( $this->args['amount']['num'] ) ) {

						$compare  = esc_sql( $this->args['amount']['compare'] );
						$wheres[] = $wpdb->prepare( "creds {$compare} {$format}", $this->args['amount']['num'] );

					}

				}

			}

			/**
			 * Time Query
			 * Supports either YYYY-MM-DD or MM/DD/YYYY date formats or unix timestamps
			 * Dates can use time for more precise queries but it is not required. If no time is set, the dates last second is used
			 * So e.g. 2016-01-01 will become 2016-01-01 23:59:59. If this is not desired, then a time has to be included when using string dates.
			 * Not applicable when providing unix timestamps.
			 * @uses strtotime() http://php.net/manual/en/function.strtotime.php
			 *

				 // Todays entries
				 time=today
				 'time' => array(
					 'dates'   => 'today',
					 'compare' => '='
				 )

				 // Yesterdays entries
				 time=yesterday
				 'time' => array(
					 'dates'   => 'yesterday',
					 'compare' => '='
				 )

				 // This weeks entries
				 time=thisweek
				 'time' => array(
					 'dates'   => 'thisweek',
					 'compare' => '='
				 )

				 // This months entries
				 time=thismonth
				 'time' => array(
					 'dates'   => 'thismonth',
					 'compare' => '='
				 )

				 // Between two dates
				 'time' => array(
					 'dates'   => array( '2016-01-01', '2016-12-31' ),
					 'compare' => 'BETWEEN'
				 )

				 // Specific date
				 time=2016-01-01
				 'time' => array(
					 'dates'   => '2016-01-01',
					 'compare' => '='
				 )

				 // Comparisons
				 'time' => array(
					 'dates'   => '2016-01-01 00:00:00',
					 'compare' => '<='
				 )

			 */
			if ( $this->args['time'] !== NULL && ! empty( $this->args['time'] ) ) {

				// Convert string queries to array queries
				if ( ! is_array( $this->args['time'] ) ) {

					$datetimes = ( ( count( explode( ',', $this->args['time'] ) ) > 0 ) ? explode( ',', $this->args['time'] ) : array( $this->args['time'] ) );
					$dates     = $this->get_timestamps( $datetimes );

					if ( $dates !== false ) {

						// Single time query
						if ( count( $dates ) == 1 )
							$this->args['time'] = array( 'dates' => $dates[0], 'compare' => '=' );

						// Keyword time query or between two dates
						elseif ( count( $dates ) == 2 || in_array( $this->args['time'], array( 'today', 'yesterday', 'thisweek', 'thismonth' ) ) )
							$this->args['time'] = array( 'dates' => $dates, 'compare' => 'BETWEEN' );

						// Multiple time query
						else
							$this->args['time'] = array( 'dates' => $dates, 'compare' => 'IN' );

					}

				}

				// Make sure query is properly formatted
				if ( is_array( $this->args['time'] ) && array_key_exists( 'dates', $this->args['time'] ) && array_key_exists( 'compare', $this->args['time'] ) ) {

					// Between (requires dates to be an array)
					if ( in_array( $this->args['time']['compare'], array( 'BETWEEN', 'NOT BETWEEN' ) ) && is_array( $this->args['time']['dates'] ) ) {

						$between  = esc_sql( $this->args['time']['compare'] );
						$dates    = $this->get_timestamps( $this->args['time']['dates'] );
						if ( $dates !== false )
							$wheres[] = $wpdb->prepare( "time {$between} %d AND %d", $dates );

						$this->args['time']['dates'] = $dates;

					}

					// IN or NOT IN comparisons
					elseif ( in_array( $this->args['time']['compare'], array( 'IN', 'NOT IN' ) ) && is_array( $this->args['time']['dates'] ) ) {

						$dates    = $this->get_timestamps( $this->args['time']['dates'] );
						if ( $dates !== false )
							$wheres[] = $wpdb->prepare( "time IN ( %d" . str_repeat( ",%d", ( count( $this->args['time']['dates'] ) - 1 ) ) . " )", $dates );

					}

					// All other supported comparisons
					elseif ( in_array( $this->args['time']['compare'], array( '=', '!=', '>', '>=', '<', '<=' ) ) && ! is_array( $this->args['time']['dates'] ) ) {

						$compare  = esc_sql( $this->args['time']['compare'] );
						$date     = $this->get_timestamp( $this->args['time']['dates'] );
						if ( $date !== false )
							$wheres[] = $wpdb->prepare( "time {$compare} %d", $date );

					}

				}

			}

			/**
			 * Search Entries Query
			 *

				 // Search
				 s=hello

			 */
			if ( $this->args['s'] !== NULL && ! empty( $this->args['s'] ) ) {
				
				$search_query = sanitize_text_field( $this->args['s'] );
				$plural = mycred_get_point_type_name(isset( $_REQUEST['ctype'] ),false);
				$single = mycred_get_point_type_name(isset( $_REQUEST['ctype'] ),true);
				// Check if we are using wildcards
				$search_query = str_replace( strtolower($plural), '%plural%', strtolower($search_query) );
				$search_query = str_replace( strtolower($single), '%singular%', strtolower($search_query) );
				if ( str_replace( '%', '', $search_query ) != $search_query )
					$wheres[] = $wpdb->prepare( "entry LIKE %s", $search_query );

				else
					$wheres[] = $wpdb->prepare( "entry LIKE %s", '%'.$search_query.'%' );

			}

			/**
			 * Search Data Column Query
			 *

				 // Search
				 data=boo

			 */
			if ( $this->args['data'] !== NULL && ! empty( $this->args['data'] ) ) {

				$data_query = sanitize_text_field( $this->args['data'] );

				// Check if we are using wildcards
				if( str_replace( '!%', '', $data_query ) != $data_query )
					$wheres[] = $wpdb->prepare( "data NOT LIKE %s", str_replace( '!%', '%', $data_query ) );
				else if( str_replace( '!', '', $data_query ) != $data_query )
					$wheres[] = $wpdb->prepare( "data != %s", $data_query );
				else if( str_replace( '%', '', $data_query ) != $data_query )
					$wheres[] = $wpdb->prepare( "data LIKE %s", $data_query );
				else
					$wheres[] = $wpdb->prepare( "data = %s", $data_query );

			}

			/**
			 * Ordering of results
			 * 
			 * Single order
			 * orderby=time&order=ASC
			 * 
			 * Multiple orders
			 * 'orderby' => array( 'time' => 'ASC', 'id' => 'ASC' )
			 */
			$this->sortby    = "ORDER BY time DESC";
			if ( ! empty( $this->args['orderby'] ) ) {

				// Make sure $sortby is valid
				$allowed = apply_filters( 'mycred_allowed_sortby', array( 'id', 'ref', 'ref_id', 'user_id', 'creds', 'ctype', 'entry', 'data', 'time' ) );

				// Convert strings to array
				if ( ! is_array( $this->args['orderby'] ) && ! empty( $this->args['order'] ) ) {

					$this->args['orderby'] = array( $this->args['orderby'] => $this->args['order'] );

				}

				$orders          = array();
				$duplicate_check = array();
				foreach ( $this->args['orderby'] as $orderby => $order ) {

					$orderby           = sanitize_text_field( $orderby );
					if ( ! in_array( $orderby, $allowed ) ) $orderby = 'time';

					// Make sure we ar enot attempting to order by the same column multiple times
					if ( in_array( $orderby, $duplicate_check ) ) continue;

					$order             = sanitize_text_field( $order );
					if ( ! in_array( $order, array( 'ASC', 'DESC' ) ) ) $order = 'DESC';
					$order             = strtoupper( $order );

					$orders[]          = $orderby . ' ' . $order;
					$duplicate_check[] = $orderby;

				}

				$this->sortby          = "ORDER BY " . implode( ', ', $orders );

			}

			// Support will be removed in future version
			if ( (bool) $this->args['ids'] === true )
				$this->args['fields'] = array( 'ids' );

			/**
			 * Construct Query
			 */
			$this->where     = ( ( ! empty( $wheres ) ) ? 'WHERE ' . implode( ' AND ', $wheres ) : '' );

			// Generate a unique ID that identifies the leaderboard we are trying to build
			$this->cache_key             = $this->get_cache_key();
			$this->args['cache_results'] = ( array_key_exists( 'cache_results', $this->args ) ) ? (bool) $this->args['cache_results'] : false;

			if ( ! $this->args['cache_results'] ) return;

			// Save cache key so we can clear it when needed
			$cache_keys      = mycred_get_option( MYCRED_SLUG . '-cache-keys', array() );
			if ( empty( $cache_keys ) || ( ! empty( $cache_keys ) && ! in_array( $this->cache_key, $cache_keys ) ) ) {

				$cache_keys[] = $this->cache_key;

				mycred_update_option( MYCRED_SLUG . '-cache-keys', $cache_keys );

			}

		}

		/**
		 * Get Cached Results
		 * @since 1.8
		 * @version 1.0
		 */
		public function get_cache() {

			$data         = false;
			$key          = $this->cache_key;

			// Object caching we will always do
			$object_cache = wp_cache_get( $key, MYCRED_SLUG );
			if ( $object_cache !== false && is_array( $object_cache ) ) {

				if ( ! $this->args['cache_results'] )
					wp_cache_delete( $key, MYCRED_SLUG );

				else $data = $object_cache;

			}

			return apply_filters( 'mycred_get_cached_log', $data, $this );

		}

		/**
		 * Cache Results
		 * @since 1.8
		 * @version 1.0
		 */
		public function cache_result( $data ) {

			if ( ! $this->args['cache_results'] ) return;

			wp_cache_set( $this->cache_key, $data, MYCRED_SLUG );

			do_action( 'mycred_cache_log', $data, $this );

		}

		/**
		 * Table Headers
		 * Returns all table column headers.
		 * @filter mycred_log_column_headers
		 * @since 0.1
		 * @version 1.1.3
		 */
		public function table_headers() {

			// Headers already set
			if ( ! empty( $this->headers ) || ! $this->render_mode ) return;

			global $mycred_types;

			$columns = array(
				'username' => __( 'User', 'mycred' ),
				'time'     => __( 'Date', 'mycred' ),
				'creds'    => $this->core->plural(),
				'entry'    => __( 'Entry', 'mycred' )
			);

			if ( $this->args['user_id'] !== NULL )
				unset( $columns['username'] );

			if ( $this->is_admin )
				$columns = array(
					'cb'       => '',
					'username' => __( 'User', 'mycred' ),
					'ref'      => __( 'Reference', 'mycred' ),
					'time'     => __( 'Date', 'mycred' ),
					'creds'    => $this->core->plural(),
					'entry'    => __( 'Entry', 'mycred' )
				);

			$headers = $this->headers;
			if ( empty( $this->headers ) )
				$headers = $columns;

			// If we are showing results for multiple point types, the label will not be correct
			// Instead we use a more generic label.
			if ( array_key_exists( 'creds', $headers ) && count( $this->types ) > 1 )
				$headers['creds'] = __( 'Amount', 'mycred' );

			$this->headers = apply_filters( 'mycred_log_column_headers', $headers, $this, $this->is_admin );

		}

		/**
		 * Has Entries
		 * @returns true or false
		 * @since 0.1
		 * @version 1.0
		 */
		public function have_entries() {

			if ( ! empty( $this->results ) ) return true;
			return false;

		}

		/**
		 * No Entries
		 * @since 0.1
		 * @version 1.0
		 */
		public function no_entries() {

			echo $this->get_no_entries();

		}

		/**
		 * Get No Entries
		 * @since 0.1
		 * @version 1.0
		 */
		public function get_no_entries() {

			return __( 'No log entries found', 'mycred' );

		}

		/**
		 * Get Page Number
		 * @since 1.4
		 * @version 1.0.2
		 */
		public function get_pagenum() {

			global $wp;

			if ( isset( $wp->query_vars['pagenum'] ) && $wp->query_vars['pagenum'] != '' )
				$pagenum = absint( $wp->query_vars['pagenum'] );

			elseif ( isset( $_REQUEST['paged'] ) )
				$pagenum = absint( $_REQUEST['paged'] );

			elseif ( isset( $_REQUEST['pagenum'] ) )
				$pagenum = absint( $_REQUEST['pagenum'] );

			else return 1;

			return max( 1, $pagenum );

		}

		/**
		 * Table Nav
		 * @since 0.1
		 * @version 1.1.1
		 */
		public function table_nav( $location = 'top', $is_profile = false ) {

			if ( ! $this->have_entries() || ! $this->render_mode ) return;

			if ( $location == 'top' ) {

				$this->bulk_actions();
				$this->filter_options( $is_profile );
				$this->navigation( $location );

			}
			else {

				$this->navigation( $location );

			}

		}

		/**
		 * Bulk Actions
		 * @since 1.7
		 * @version 1.0.1
		 */
		public function bulk_actions() {

			if ( ! $this->is_admin || ! $this->render_mode ) return;

			$bulk_actions = apply_filters( 'mycred_log_bulk_actions', array(
				'-1'            => __( 'Bulk Actions', 'mycred' ),
				'export-raw'    => __( 'Export Raw', 'mycred' ),
				'export-format' => __( 'Export Formatted', 'mycred' ),
				'delete'        => __( 'Delete', 'mycred' )
			), $this );

			if ( empty( $bulk_actions ) ) return;

?>
<div class="alignleft actions bulkactions">
	<select name="action" id="bulk-action-selector-top">
<?php

	foreach ( $bulk_actions as $action_id => $label )
		echo '<option value="' . $action_id . '">' . $label . '</option>';

?>
	</select>
	<input type="submit" class="button action" id="doaction" value="<?php _e( 'Apply', 'mycred' ); ?>" />
</div>
<?php

		}

		/**
		 * Filter Log options
		 * @since 0.1
		 * @version 1.3.3
		 */
		public function filter_options( $is_profile = false ) {

			if ( ! $this->render_mode ) return;

			echo '<div class="alignleft actions">';
			$show = false;

			// Filter by reference
			if ( ! empty( $this->references ) ) {

				echo '<select name="ref" id="myCRED-reference-filter"><option value="">' . __( 'Show all references', 'mycred' ) . '</option>';
				foreach ( $this->references as $ref_id => $ref_label ) {

					echo '<option value="' . $ref_id . '"';
					if ( isset( $_GET['ref'] ) && $_GET['ref'] == $ref_id ) echo ' selected="selected"';
					echo '>' . esc_html( $ref_label ) . '</option>';

				}
				echo '</select>';
				$show = true;

			}

			// Filter by user
			if ( $this->core->user_is_point_editor() && ! $is_profile && $this->num_rows > 0 ) {

				echo '<input type="text" class="form-control" name="user" id="myCRED-user-filter" size="22" placeholder="' . __( 'User ID, Username, Email or Nicename', 'mycred' ) . '" value="' . ( ( isset( $_GET['user'] ) ) ? esc_attr( $_GET['user'] ) : '' ) . '" /> ';
				$show = true;

			}

			// Filter Order
			if ( $this->num_rows > 0 ) {

				echo '<select name="order" id="myCRED-order-filter"><option value="">' . __( 'Show in order', 'mycred' ) . '</option>';
				foreach ( array( 'ASC' => __( 'Ascending', 'mycred' ), 'DESC' => __( 'Descending', 'mycred' ) ) as $value => $label ) {

					echo '<option value="' . $value . '"';
					if ( ! isset( $_GET['order'] ) && $value == 'DESC' ) echo ' selected="selected"';
					elseif ( isset( $_GET['order'] ) && $_GET['order'] == $value ) echo ' selected="selected"';
					echo '>' . $label . '</option>';

				}
				echo '</select>';
				$show = true;

			}

			// Let others play
			if ( has_action( 'mycred_filter_log_options' ) ) {
				do_action( 'mycred_filter_log_options', $this );
				$show = true;
			}

			if ( $show === true )
				echo '<input type="submit" class="btn btn-default button button-secondary" value="' . __( 'Filter', 'mycred' ) . '" />';

			echo '</div>';

		}

		/**
		 * Front Navigation
		 * Renders navigation with bootstrap support
		 * @since 1.7
		 * @version 1.0.1
		 */
		public function front_navigation( $location = 'top', $pagination = 10 ) {

			if ( ! $this->have_entries() || $this->max_num_pages == 1 || ! $this->render_mode ) return;

?>
<div class="row pagination-<?php echo $location; ?>">
	<div class="col-xs-12">

		<?php $this->front_pagination( $pagination ); ?>

	</div>
</div>
<?php

		}

		/**
		 * Navigation Wrapper
		 * @since 0.1
		 * @version 1.1.1
		 */
		public function navigation( $location = 'top', $id = '' ) {

			if ( ! $this->render_mode ) return;

?>
<h2 class="screen-reader-text sr-only"><?php _e( 'Log entries navigation', 'mycred' ); ?></h2>
<div class="tablenav-pages<?php if ( $this->max_num_pages == 1 ) echo ' one-page'; ?>">

	<?php $this->pagination( $location, $id ); ?>

</div>
<br class="clear" />
<?php

		}

		/**
		 * Front Pagination
		 * @since 1.7
		 * @version 1.0.4
		 */
		public function front_pagination( $pages_to_show = 5 ) {

			if ( ! $this->have_entries() || ! $this->render_mode ) return;

			$page_links           = array();
			$total_pages          = $this->max_num_pages;
			$current              = $this->get_pagenum();

			$removable_query_args = wp_removable_query_args();

			$current_url          = set_url_scheme( 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );
			$current_url          = remove_query_arg( $removable_query_args, $current_url );
			$current_url          = str_replace( '/' . $current . '/', '/', $current_url );
			$current_url          = apply_filters( 'mycred_log_front_nav_url', $current_url, $this );

			$pages_to_show        = absint( $pages_to_show );
			if ( $pages_to_show === 0 ) $pages_to_show = 5;

			// We can not show more pages then whats available
			if ( $pages_to_show > $total_pages )
				$pages_to_show = $total_pages;

			$disable_first        = $disable_last = '';
			if ( $current == 1 )
				$disable_first = ' disabled';

			if ( $current == $total_pages )
				$disable_last = ' disabled';

			if ( $current == 1 )
				$page_links[] = '<li><span aria-hidden="true">&laquo;</span></li>';
			else {
				$page_links[] = sprintf( '<li><a class="%s" href="%s">%s</a></li>',
					'first-page',
					esc_url( remove_query_arg( 'pagenum', $current_url ) ),
					'&laquo;'
				);
			}

			if ( $current == 1 )
				$page_links[] = '<li><span class="tablenav-pages-navspan" aria-hidden="true">&lsaquo;</span></li>';
			else {
				$page_links[] = sprintf( '<li><a class="%s" href="%s">%s</a></li>',
					'prev-page',
					esc_url( add_query_arg( 'pagenum', max( 1, $current-1 ), $current_url ) ),
					'&lsaquo;'
				);
			}

			$start_from           = 1;
			if ( $current > $pages_to_show ) {
				$diff          = (int) ( $current / $pages_to_show );
				$start_from    = $pages_to_show * $diff;
				$pages_to_show = $start_from + $pages_to_show;
			}

			for ( $i = $start_from; $i <= $pages_to_show; $i++ ) {

				if ( $i != $current )
					$page_links[] = sprintf( '<li><a class="%s" href="%s">%s</a></li>',
						'mycred-nav',
						esc_url( add_query_arg( 'pagenum', $i, $current_url ) ),
						$i
					);

				else
					$page_links[] = '<li class="active"><span class="current">' . $current . '</span></li>';

			}

			if ( $current == $total_pages )
				$page_links[] = '<li><span class="tablenav-pages-navspan" aria-hidden="true">&rsaquo;</span></li>';
			else {
				$page_links[] = sprintf( '<li><a class="%s" href="%s">%s</a></li>',
					'next-page' . $disable_last,
					esc_url( add_query_arg( 'pagenum', min( $total_pages, $current+1 ), $current_url ) ),
					'&rsaquo;'
				);
			}

			if ( $current == $total_pages )
				$page_links[] = '<li><span class="tablenav-pages-navspan" aria-hidden="true">&raquo;</span></li>';
			else {
				$page_links[] = sprintf( '<li><a class="%s" href="%s">%s</a></li>',
					'last-page' . $disable_last,
					esc_url( add_query_arg( 'pagenum', $total_pages, $current_url ) ),
					'&raquo;'
				);
			}

			echo '<nav><ul class="pagination">' . implode( '', $page_links ) . '</ul></nav>';

		}

		/**
		 * Pagination
		 * @since 1.4
		 * @version 1.1.2
		 */
		public function pagination( $location = 'top', $id = '' ) {

			if ( ! $this->render_mode ) return;

			$page_links         = array();
			$output             = '';
			$total_pages        = $this->max_num_pages;
			$current            = $this->get_pagenum();
			$current_url        = set_url_scheme( 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );

			if ( ! $this->is_admin )
				$current_url = str_replace( '/page/' . $current . '/', '/', $current_url );

			$current_url        = remove_query_arg( array( 'hotkeys_highlight_last', 'hotkeys_highlight_first' ), $current_url );

			if ( $this->have_entries() )
				$output = '<span class="displaying-num">' . sprintf( _n( '1 entry', '%d entries', $this->num_rows, 'mycred' ), $this->num_rows ) . '</span>';
	
			$total_pages_before = '<span class="paging-input">';
			$total_pages_after  = '</span>';
	
			$disable_first = $disable_last = $disable_prev = $disable_next = false;
	
	 		if ( $current == 1 ) {
				$disable_first = true;
				$disable_prev  = true;
	 		}
			if ( $current == 2 ) {
				$disable_first = true;
			}
	 		if ( $current == $total_pages ) {
				$disable_last = true;
				$disable_next = true;
	 		}
			if ( $current == $total_pages - 1 ) {
				$disable_last = true;
			}
	
			if ( $disable_first ) {
				$page_links[] = '<span class="tablenav-pages-navspan" aria-hidden="true">&laquo;</span>';
			} else {
				$page_links[] = sprintf( "<a class='first-page' href='%s'><span class='screen-reader-text'>%s</span><span aria-hidden='true'>%s</span></a>",
					esc_url( remove_query_arg( 'paged', $current_url ) ),
					__( 'First page' ),
					'&laquo;'
				);
			}
	
			if ( $disable_prev ) {
				$page_links[] = '<span class="tablenav-pages-navspan" aria-hidden="true">&lsaquo;</span>';
			} else {
				$page_links[] = sprintf( "<a class='prev-page' href='%s'><span class='screen-reader-text'>%s</span><span aria-hidden='true'>%s</span></a>",
					esc_url( add_query_arg( 'paged', max( 1, $current-1 ), $current_url ) ),
					__( 'Previous page' ),
					'&lsaquo;'
				);
			}
	
			if ( 'bottom' === $location ) {
				$html_current_page  = $current;
				$total_pages_before = '<span class="screen-reader-text">' . __( 'Current Page' ) . '</span><span id="table-paging" class="paging-input">';
			} else {
				$html_current_page = sprintf( "%s<input class='current-page' id='current-page-selector' type='text' name='paged' value='%s' size='%d' aria-describedby='table-paging' />",
					'<label for="current-page-selector" class="screen-reader-text">' . __( 'Current Page' ) . '</label>',
					$current,
					strlen( $total_pages )
				);
			}
			$html_total_pages = sprintf( "<span class='total-pages'>%s</span>", number_format_i18n( $total_pages ) );
			$page_links[]     = $total_pages_before . sprintf( _x( '%1$s of %2$s', 'paging' ), $html_current_page, $html_total_pages ) . $total_pages_after;
	
			if ( $disable_next ) {
				$page_links[] = '<span class="tablenav-pages-navspan" aria-hidden="true">&rsaquo;</span>';
			} else {
				$page_links[] = sprintf( "<a class='next-page' href='%s'><span class='screen-reader-text'>%s</span><span aria-hidden='true'>%s</span></a>",
					esc_url( add_query_arg( 'paged', min( $total_pages, $current+1 ), $current_url ) ),
					__( 'Next page' ),
					'&rsaquo;'
				);
			}
	
			if ( $disable_last ) {
				$page_links[] = '<span class="tablenav-pages-navspan" aria-hidden="true">&raquo;</span>';
			} else {
				$page_links[] = sprintf( "<a class='last-page' href='%s'><span class='screen-reader-text'>%s</span><span aria-hidden='true'>%s</span></a>",
					esc_url( add_query_arg( 'paged', $total_pages, $current_url ) ),
					__( 'Last page' ),
					'&raquo;'
				);
			}
	
			$pagination_links_class = 'pagination-links';
			if ( ! empty( $infinite_scroll ) ) {
				$pagination_links_class = ' hide-if-js';
			}
			$output .= "\n<span class='$pagination_links_class'>" . join( "\n", $page_links ) . '</span>';
	
			if ( $total_pages ) {
				$page_class = $total_pages < 2 ? ' one-page' : '';
			} else {
				$page_class = ' no-pages';
			}
	
			echo $output;

		}

		/**
		 * Display
		 * @since 0.1
		 * @version 1.0.1
		 */
		public function display() {

			if ( $this->render_mode )
				echo $this->get_display();

		}

		/**
		 * Get Display
		 * Generates a table for our results.
		 * @since 0.1
		 * @version 1.1.2
		 */
		public function get_display() {

			if ( ! $this->render_mode ) return '';

			$this->table_headers();

			$table_class = 'table table-condensed mycred-table';
			if ( $this->is_admin )
				$table_class = 'mycred-table wp-list-table widefat fixed striped users';

			$output  = '';
			if ( ! $this->is_admin )
				$output .= '<div class="table-responsive">';

			$output .= '
<table class="' . apply_filters( 'mycred_log_table_classes', $table_class, $this ) . '" cellspacing="0" cellspacing="0">
	<thead>
		<tr>';

			// Table header
			foreach ( $this->headers as $col_id => $col_title ) {

				$class = '';
				if ( $col_id != 'username' && in_array( $col_id, $this->hidden_headers ) )
					$class = ' hidden';

				if ( $col_id == 'cb' )
					$output .= '<td id="cb" class="manage-column column-cb check-column"><label class="screen-reader-text" for="cb-select-all-1">' . __( 'Select all', 'mycred' ) . '</label><input type="checkbox" id="cb-select-all-1" /></td>';

				else
					$output .= '<th scope="col" id="' . $col_id . '" class="manage-column' . ( ( $col_id == 'username' ) ? ' column-primary' : '' ) . ' column-' . $col_id . $class . '">' . $col_title . '</th>';

			}

			$output .= '
		</tr>
	</thead>
	<tbody id="the-list">';

			// Loop
			if ( $this->have_entries() ) {

				$alt = 0;

				foreach ( $this->results as $log_entry ) {

					$row_class = apply_filters( 'mycred_log_row_classes', array( 'entry-' . $log_entry->id, 'type-log-entry', 'format-standard', 'hentry' ), $log_entry );

					$alt = $alt+1;
					if ( $alt % 2 == 0 )
						$row_class[] = 'alt';

					$output .= '<tr class="' . implode( ' ', $row_class ) . '" id="entry-' . $log_entry->id . '">' . $this->get_the_entry( $log_entry ) . '</tr>';

				}

			}
			// No log entry
			else {

				$output .= '<tr><td colspan="' . count( $this->headers ) . '" class="no-entries">' . $this->get_no_entries() . '</td></tr>';

			}

			$output .= '
	</tbody>
	<tfoot>
		<tr>';

			// Table footer
			foreach ( $this->headers as $col_id => $col_title ) {

				$class = '';
				if ( $col_id != 'username' && in_array( $col_id, $this->hidden_headers ) )
					$class = ' hidden';

				if ( $col_id == 'cb' )
					$output .= '<td class="manage-column column-cb check-column"><label class="screen-reader-text" for="cb-select-all-2">' . __( 'Select all', 'mycred' ) . '</label><input type="checkbox" id="cb-select-all-2" /></td>';

				else
					$output .= '<th scope="col" class="manage-column' . ( ( $col_id == 'username' ) ? ' column-primary' : '' ) . ' column-' . $col_id . $class . '">' . $col_title . '</th>';

			}

			$output .= '
		</tr>
	</tfoot>
</table>';

			if ( ! $this->is_admin )
				$output .= '</div>';

			return $output;

		}

		/**
		 * The Entry
		 * @since 0.1
		 * @version 1.1.1
		 */
		public function the_entry( $log_entry, $wrap = 'td' ) {

			if ( $this->render_mode )
				echo $this->get_the_entry( $log_entry, $wrap );

		}

		/**
		 * Get The Entry
		 * Generated a single entry row depending on the columns used / requested.
		 * @filter mycred_log_date
		 * @since 0.1
		 * @version 1.4.4
		 */
		public function get_the_entry( $log_entry, $wrap = 'td' ) {

			if ( ! $this->render_mode ) return '';

			$date_format = get_option( 'date_format' ) . ' ' . get_option( 'time_format' );
			$entry_data  = '';

			// Run though columns
			foreach ( $this->headers as $column_id => $column_name ) {

				$hidden = '';
				if ( $column_id != 'username' && in_array( $column_id, $this->hidden_headers ) )
					$hidden = ' hidden';

				$content = false;
				$data    = '';

				switch ( $column_id ) {

					// Checkbox column for bulk actions
					case 'cb' :

						$entry_data .= '<th scope="row" class="check-column"><label class="screen-reader-text" for="mycred-log-entry' . $log_entry->id . '">' . __( 'Select entry', 'mycred' ) . '</label><input type="checkbox" name="entry[]" id="mycred-log-entry' . $log_entry->id . '" value="' . $log_entry->id . '" /></th>';

					break;

					// Username Column
					case 'username' :

						$user = get_userdata( $log_entry->user_id );
						$display_name = '<span>' . __( 'User Missing', 'mycred' ) . ' (ID: ' . $log_entry->user_id . ')</span>';
						if ( isset( $user->display_name ) )
							$display_name = $user->display_name;

						if ( ! $this->is_admin )
							$content = '<span>' . $display_name . '</span>';

						else {
							$actions = $this->get_row_actions( $log_entry, $user );
							$content = '<strong>' . $display_name . '</strong>' . $actions;
						}

						if ( $this->is_admin )
							$content .= '<button type="button" class="toggle-row"><span class="screen-reader-text">' . __( 'Show more details', 'mycred' ) . '</span></button>';

						$content = apply_filters( 'mycred_log_username', $content, $log_entry->user_id, $log_entry );

					break;

					// Log Entry Column
					case 'ref' :

						$reference = ucwords( str_replace( array( '-', '_' ), ' ', $log_entry->ref ) );
						if ( array_key_exists( $log_entry->ref, $this->references ) )
							$reference = $this->references[ $log_entry->ref ];

						$content = apply_filters( 'mycred_log_ref', $reference, $log_entry->ref, $log_entry );

					break;

					// Date & Time Column
					case 'time' :

						$content = $time = apply_filters( 'mycred_log_date', date_i18n( $date_format, $log_entry->time ), $log_entry->time, $log_entry );
						$content = '<time>' . $content . '</time>';

						if ( $this->is_admin )
							$content .= '<div class="row-actions"><span class="view"><a href="' . add_query_arg( array( 'page' => $_REQUEST['page'], 'time' => $this->get_time_for_filter( $log_entry->time ) ), admin_url( 'admin.php' ) ) . '">' . __( 'Filter by Date', 'mycred' ) . '</a></span></div>';

					break;

					// Amount Column
					case 'creds' :

						$content = $creds = $this->types[ $log_entry->ctype ]->format_creds( $log_entry->creds );
						$content = apply_filters( 'mycred_log_creds', $content, $log_entry->creds, $log_entry );
						$data    = ' data-raw="' . esc_attr( $log_entry->creds ) . '"';

					break;

					// Log Entry Column
					case 'entry' :

						$content = $this->types[ $log_entry->ctype ]->parse_template_tags( $log_entry->entry, $log_entry );
						$content = apply_filters( 'mycred_log_entry', $content, $log_entry->entry, $log_entry );
						$data    = ' data-raw="' . esc_attr( $log_entry->entry ) . '"';

					break;

					// Let others play
					default :

						$content = apply_filters( 'mycred_log_' . $column_id, false, $log_entry );

					break;

				}

				if ( $content !== false )
					$entry_data .= '<' . $wrap . ' class="' . ( ( $column_id == 'username' ) ? 'column-primary ' : '' ) . 'column-' . $column_id . $hidden . '" data-colname="' . $column_name . '" ' . $data . '>' . $content . '</' . $wrap . '>';

			}

			return $entry_data;

		}

		/**
		 * Row Actions
		 * @since 1.7
		 * @version 1.0.1
		 */
		public function get_row_actions( $entry, $user ) {

			if ( ! $this->is_admin || ! $this->render_mode ) return;

			$filter_label = __( 'Filter by User', 'mycred' );
			if ( $user === false )
				$filter_label = __( 'Filter by ID', 'mycred' );

			$actions = array();

			if ( ! isset( $_REQUEST['user'] ) || $_REQUEST['user'] == '' )
				$actions['view']   = '<a href="' . add_query_arg( array( 'page' => $_REQUEST['page'], 'user' => $entry->user_id ), admin_url( 'admin.php' ) ) . '">' . $filter_label . '</a>';

			$actions['edit']   = '<a href="javascript:void(0);" class="mycred-open-log-entry-editor" data-id="' . $entry->id . '" data-ref="' . $entry->ref . '">' . __( 'Edit', 'mycred' ) . '</a>';
			$actions['delete'] = '<a href="javascript:void(0);" class="mycred-delete-row" data-id="' . $entry->id . '">' . __( 'Delete', 'mycred' ) . '</a>';

			if ( ! empty( $actions ) ) {

				$output  = '';
				$counter = 0;
				$count   = count( $actions );
				foreach ( $actions as $id => $link ) {

					$end = ' | ';
					if ( $counter+1 == $count )
						$end = '';

						$output .= '<span class="' . $id . '">' . $link . $end . '</span>';
						$counter ++;

				}

				return '<div class="row-actions">' . $output . '</div>';

			}

		}

		/**
		 * Exporter
		 * Displays all available export options.
		 * @since 0.1
		 * @version 1.1.1
		 */
		public function exporter( $title = '', $is_profile = false ) {

			// Must be logged in
			if ( ! is_user_logged_in() || ! $this->render_mode ) return;

			// Export options
			$exports     = mycred_get_log_exports();
			$search_args = mycred_get_search_args();

			if ( array_key_exists( 'user', $exports ) && $this->args['user_id'] === NULL )
				unset( $exports['user'] );

?>
<div style="display:none;" class="clear" id="export-log-history">
	<?php if ( ! empty( $title ) ) : ?><h3 class="group-title"><?php echo $title; ?></h3><?php endif; ?>
<?php

			if ( ! empty( $exports ) ) {

				foreach ( (array) $exports as $id => $data ) {

					// Label
					if ( $is_profile )
						$label = $data['my_label'];
					else
						$label = $data['label'];

					$url = mycred_get_export_url( $id );
					if ( $url === false ) continue;

					echo '<a href="" class="' . $data['class'] . '">' . $label . '</a> ';

				}

?>
	<p><span class="description"><?php _e( 'Log entries are exported to a CSV file and depending on the number of entries selected, the process may take a few seconds.', 'mycred' ); ?></span></p>
<?php

			}

			else {

				echo '<p>' . __( 'No export options available.', 'mycred' ) . '</p>';

			}

?>
</div>
<script type="text/javascript">
jQuery(function($) {
	$( '.toggle-exporter' ).click(function(){
		$( '#export-log-history' ).toggle();
	});
});
</script>
<?php

		}

		/**
		 * Log Search
		 * @since 0.1
		 * @version 1.0.5
		 */
		public function search() {

			if ( ! $this->render_mode ) return;

			if ( isset( $_GET['s'] ) && $_GET['s'] != '' )
				$serarch_string = $_GET['s'];
			else
				$serarch_string = '';

?>
<p class="search-box">
	<label class="screen-reader-text"><?php _e( 'Search Log', 'mycred' ); ?>:</label>
	<input type="search" name="s" value="<?php echo esc_attr( $serarch_string ); ?>" placeholder="<?php _e( 'search log entries', 'mycred' ); ?>" />
	<input type="submit" id="search-submit" class="button button-medium button-secondary" value="<?php _e( 'Search Log', 'mycred' ); ?>" />
</p>
<?php

		}

		/**
		 * Filter by Dates
		 * @since 0.1
		 * @version 1.0.1
		 */
		public function filter_dates( $url = '' ) {

			if ( ! $this->render_mode ) return;

			$date_sorting = apply_filters( 'mycred_sort_by_time', array(
				''          => __( 'All', 'mycred' ),
				'today'     => __( 'Today', 'mycred' ),
				'yesterday' => __( 'Yesterday', 'mycred' ),
				'thisweek'  => __( 'This Week', 'mycred' ),
				'thismonth' => __( 'This Month', 'mycred' )
			) );

			if ( ! empty( $date_sorting ) ) {

				$total = count( $date_sorting );
				$count = 0;

				echo '<ul class="subsubsub">';

				foreach ( $date_sorting as $sorting_id => $sorting_name ) {

					$count = $count+1;

					echo '<li class="' . $sorting_id . '"><a href="';

					// Build Query Args
					$url_args = array();
					if ( isset( $_GET['user_id'] ) && $_GET['user_id'] != '' )
						$url_args['user_id'] = $_GET['user_id'];

					if ( isset( $_GET['ref'] ) && $_GET['ref'] != '' )
						$url_args['ref'] = $_GET['ref'];

					if ( isset( $_GET['order'] ) && $_GET['order'] != '' )
						$url_args['order'] = $_GET['order'];

					if ( isset( $_GET['s'] ) && $_GET['s'] != '' )
						$url_args['s'] = $_GET['s'];

					if ( $sorting_id != '' )
						$url_args['show'] = $sorting_id;

					// Build URL
					if ( ! empty( $url_args ) )
						echo esc_url( add_query_arg( $url_args, $url ) );

					else
						echo esc_url( $url );

					echo '"';

					if ( isset( $_GET['show'] ) && $_GET['show'] == $sorting_id ) echo ' class="current"';
					elseif ( ! isset( $_GET['show'] ) && $sorting_id == '' ) echo ' class="current"';

					echo '>' . $sorting_name . '</a>';
					if ( $count != $total ) echo ' | ';
					echo '</li>';

				}
				echo '</ul>';

			}

		}

		/**
		 * Get Time from Filter
		 * @since 0.1
		 * @version 1.0.1
		 */
		protected function get_time_for_filter( $timestamp ) {

			$start = strtotime( date( 'Y-m-d 00:00:00' ), $timestamp );
			$end   = $start + ( DAY_IN_SECONDS - 1 );

			return $start . ',' . $end;

		}

		/**
		 * Get User ID
		 * Converts username, email or userlogin into an ID if possible
		 * @since 1.6.3
		 * @version 1.0
		 */
		protected function get_user_id( $string = '' ) {

			if ( ! is_numeric( $string ) ) {

				$user = get_user_by( 'login', $string );
				if ( ! isset( $user->ID ) ) {

					$user = get_user_by( 'email', $string );
					if ( ! isset( $user->ID ) ) {
						$user = get_user_by( 'slug', $string );
						if ( ! isset( $user->ID ) )
							return false;
					}

				}
				return absint( $user->ID );

			}

			return $string;

		}

		/**
		 * Get Time from Keyword
		 * @since 1.7.5
		 * @version 1.0
		 */
		protected function get_time_from_keyword( $keyword = '' ) {

			$today                 = strtotime( date( 'Y-m-d' ) . ' midnight', $this->now );
			$todays_date           = date( 'd', $this->now );
			$weekday               = date( 'w', $this->now );
			$result                = array();

			$keyword               = strtolower( $keyword );

			// Today
			if ( $keyword === 'today' ) {
				$result[] = $today;
				$result[] = $this->now;
			}

			// Yesterday
			elseif ( $keyword === 'yesterday' ) {
				$result[] = strtotime( '-1 day midnight', $this->now );
				$result[] = strtotime( 'today midnight', $this->now );
			}

			// This week
			elseif ( $keyword === 'thisweek' ) {

				$thisweek  = strtotime( '-' . ( $weekday+1 ) . ' days midnight', $this->now );
				if ( get_option( 'start_of_week' ) == $weekday )
					$thisweek = $today;

				$result[] = $thisweek;
				$result[] = $this->now;

			}

			// This month
			elseif ( $keyword === 'thismonth' ) {
				$result[] = strtotime( date( 'Y-m-01' ) . ' midnight', $this->now );
				$result[] = $this->now;
			}

			return $result;

		}

		/**
		 * Get Timestamp
		 * @since 1.7
		 * @version 1.0
		 */
		protected function get_timestamp( $string = '' ) {

			// Unix timestamp?
			if ( is_numeric( $string ) && strtotime( date( 'd-m-Y H:i:s', $string ) ) === (int) $string )
				return $string;

			$timestamp = strtotime( $string, current_time( 'timestamp' ) );

			if ( $timestamp <= 0 )
				$timestamp = false;

			return $timestamp;

		}

		/**
		 * Get Timestamps
		 * @since 1.7.5
		 * @version 1.0
		 */
		protected function get_timestamps( $value = NULL ) {

			// Can't work with this
			if ( $value === NULL || empty( $value ) ) return false;

			$timestamps  = array();
			$date_values = array();
			foreach ( (array) $value as $date_string ) {

				$date_string = sanitize_text_field( $date_string );

				// Unix timestamp?
				if ( is_numeric( $date_string ) && strtotime( date( 'Y-m-d H:i:s', $date_string ) ) === (int) $date_string )
					$date_values[] = $date_string;

				// Keyword?
				elseif ( in_array( $date_string, array( 'today', 'yesterday', 'thisweek', 'thismonth' ) ) )
					$date_values[] = $date_string;

				// Valid date string?
				elseif ( strtotime( $date_string ) !== false )
					$date_values[] = $date_string;

			}

			if ( ! empty( $date_values ) ) {

				if ( count( $date_values ) == 1 )
					$timestamps = $this->get_time_from_keyword( $date_values[0] );

				else {

					foreach ( $date_values as $value ) {

						if ( is_numeric( $value ) && strtotime( date( 'Y-m-d H:i:s', $value ) ) === (int) $value )
							$timestamps[] = $value;
						else
							$timestamps[] = strtotime( $value, $this->now );

					}

				}

			}

			if ( empty( $timestamps ) ) $timestamps = false;

			return $timestamps;

		}

		/**
		 * Reset Query
		 * @since 1.3
		 * @version 1.0
		 */
		public function reset_query() {

			$this->args          = NULL;
			$this->request       = NULL;
			$this->prep          = NULL;
			$this->num_rows      = NULL;
			$this->max_num_pages = NULL;
			$this->total_rows    = NULL;
			$this->results       = NULL;
			$this->headers       = NULL;

		}

		/**
		 * Get Cache Key
		 * @since 1.8
		 * @version 1.0
		 */
		public function get_cache_key() {

			return 'log-query-' . md5( serialize( $this->args ) );

		}

	}
endif;

/**
 * Get Total Points by Time
 * Counts the total amount of points that has been entered into the log between
 * two given UNIX timestamps. Optionally you can restrict counting to a specific user
 * or specific reference (or both).
 *
 * Will return false if the time stamps are incorrectly formated same for user id (must be int).
 * If you do not want to filter by reference pass NULL and not an empty string or this function will
 * return false. Same goes for the user id!
 *
 * @param $from (int|string) UNIX timestamp from when to start counting. The string 'today' can also
 * be used to start counting from the start of today.
 * @param $to (int|string) UNIX timestamp for when to stop counting. The string 'now' can also be used
 * to count up until now.
 * @param $ref (string) reference to filter by.
 * @param $user_id (int|NULL) user id to filter by.
 * @param $type (string) point type to filer by.
 * @returns total points (int|float) or error message (string)
 * @since 1.1.1
 * @version 1.4.1
 */
if ( ! function_exists( 'mycred_get_total_by_time' ) ) :
	function mycred_get_total_by_time( $from = 'today', $to = 'now', $ref = NULL, $user_id = NULL, $type = MYCRED_DEFAULT_TYPE_KEY ) {

		if ( ! MYCRED_ENABLE_LOGGING ) return 0;

		global $wpdb, $mycred_log_table;

		// Prep
		$mycred = mycred( $type );
		$wheres = array();
		$now    = current_time( 'timestamp' );

		// Reference
		if ( $ref !== NULL && strlen( $ref ) > 0 )
			$wheres[] = $wpdb->prepare( 'ref = %s', $ref );

		// User
		if ( $user_id !== NULL && strlen( $user_id ) > 0 ) {

			// No use to run a calculation if the user is excluded
			if ( $mycred->exclude_user( $user_id ) ) return 0;

			$wheres[] = $wpdb->prepare( 'user_id = %d', $user_id );

		}

		// Default from start of today
		if ( $from == 'today' )
			$from  = strtotime( 'today midnight', $now );

		// From
		else {

			$_from = strtotime( $from, $now );
			if ( $_from === false || $_from < 0 ) return 'Invalid Time ($from)';

			$from = $_from;

		}

		if ( is_numeric( $from ) )
			$wheres[] = $wpdb->prepare( 'time >= %d', $from );

		// Until
		if ( $to == 'now' )
			$to = $now;

		else {

			$_to = strtotime( $to );
			if ( $_to === false || $_to < 0 ) return 'Invalid Time ($to)';

			$to = $_to;

		}

		if ( is_numeric( $to ) )
			$wheres[] = $wpdb->prepare( 'time <= %d', $to );

		if ( mycred_point_type_exists( $type ) )
			$wheres[] = $wpdb->prepare( 'ctype = %s', $type );

		// Construct
		$where = implode( ' AND ', $wheres );

		// Query
		$query = $wpdb->get_var( "
			SELECT SUM( creds ) 
			FROM {$mycred_log_table} 
			WHERE {$where} 
			ORDER BY time;" );

		if ( $query === NULL || $query == 0 )
			return $mycred->zero();

		return $mycred->number( $query );

	}
endif;

/**
 * Get users total creds
 * Returns the users total creds unformated. If no total is fuond,
 * the users current balance is returned instead.
 *
 * @param $user_id (int), required user id
 * @param $type (string), optional cred type to check for
 * @returns zero if user id is not set or if no total were found, else returns creds
 * @since 1.2
 * @version 1.4
 */
if ( ! function_exists( 'mycred_get_users_total' ) ) :
	function mycred_get_users_total( $user_id = NULL, $point_type = MYCRED_DEFAULT_TYPE_KEY ) {

		$total_balance = 0;
		if ( ! MYCRED_ENABLE_TOTAL_BALANCE || $user_id === NULL || absint( $user_id ) === 0 ) return $total_balance;

		$user_id    = absint( $user_id );
		$point_type = sanitize_key( $point_type );

		$mycred     = mycred( $point_type );

		if ( ! $mycred->exclude_user( $user_id ) )
			$total_balance = $mycred->get_users_total_balance( $user_id );

		return $mycred->number( $total_balance );

	}
endif;

/**
 * Query Users Total
 * Queries the database for the users total acculimated points.
 *
 * @param $user_id (int), required user id
 * @param $type (string), required point type
 * @since 1.4.7
 * @version 1.2
 */
if ( ! function_exists( 'mycred_query_users_total' ) ) :
	function mycred_query_users_total( $user_id, $point_type = MYCRED_DEFAULT_TYPE_KEY ) {

		if ( ! MYCRED_ENABLE_LOGGING || ! MYCRED_ENABLE_TOTAL_BALANCE ) return 0;

		if ( ! mycred_point_type_exists( $point_type ) )
			$point_type = MYCRED_DEFAULT_TYPE_KEY;

		global $wpdb, $mycred_log_table;

		$total = $wpdb->get_var( $wpdb->prepare( "
			SELECT meta_value 
			FROM {$wpdb->usermeta} 
			WHERE user_id = %d 
				AND meta_key = %s;", $user_id, mycred_get_meta_key( $point_type, '_total' ) ) );

		if ( $total === NULL ) {

			$total = $wpdb->get_var( $wpdb->prepare( "
				SELECT SUM( creds ) 
				FROM {$mycred_log_table} 
				WHERE user_id = %d
					AND ( ( creds > 0 ) OR ( creds < 0 AND ref = 'manual' ) )
					AND ctype = %s;", $user_id, $point_type ) );

			if ( $total === NULL )
				$total = 0;

		}

		return apply_filters( 'mycred_query_users_total', $total, $user_id, $point_type );

	}
endif;

/**
 * Calculate Users Total
 * Query to get users Total from log.
 *
 * @param $user_id (int), required user id
 * @param $type (string), required point type
 * @since 1.8.14
 * @version 1.0
 */
if ( ! function_exists( 'mycred_calculate_users_total' ) ) :
	function mycred_calculate_users_total( $user_id, $point_type = MYCRED_DEFAULT_TYPE_KEY ) {

		if ( ! MYCRED_ENABLE_LOGGING || ! MYCRED_ENABLE_TOTAL_BALANCE ) return 0;

		if ( ! mycred_point_type_exists( $point_type ) )
			$point_type = MYCRED_DEFAULT_TYPE_KEY;

		global $wpdb, $mycred_log_table;

		$total = $wpdb->get_var( $wpdb->prepare( "
			SELECT SUM( creds ) 
			FROM {$mycred_log_table} 
			WHERE user_id = %d
				AND ( ( creds > 0 ) OR ( creds < 0 AND ref = 'manual' ) )
				AND ctype = %s;", $user_id, $point_type ) );

		if ( $total === NULL )
			$total = 0;

		return apply_filters( 'mycred_calculate_users_total', $total, $user_id, $point_type );

	}
endif;

/**
 * Get All References
 * Returns an array of references currently existing in the log
 * for a particular point type. Will return false if empty.
 * @since 1.5
 * @version 1.3.2
 */
if ( ! function_exists( 'mycred_get_all_references' ) ) :
	function mycred_get_all_references() {

		// Hooks
		$hooks = array(
			'registration'        => __( 'Website Registration', 'mycred' ),
			'site_visit'          => __( 'Website Visit', 'mycred' ),
			'view_content'        => __( 'Viewing Content (Member)', 'mycred' ),
			'view_content_author' => __( 'Viewing Content (Author)', 'mycred' ),
			'logging_in'          => __( 'Logging in', 'mycred' ),
			'publishing_content'  => __( 'Publishing Content', 'mycred' ),
			'approved_comment'    => __( 'Approved Comment', 'mycred' ),
			'unapproved_comment'  => __( 'Unapproved Comment', 'mycred' ),
			'spam_comment'        => __( 'SPAM Comment', 'mycred' ),
			'deleted_comment'     => __( 'Deleted Comment', 'mycred' ),
			'link_click'          => __( 'Link Click', 'mycred' ),
			'watching_video'      => __( 'Watching Video', 'mycred' ),
			'visitor_referral'    => __( 'Visitor Referral', 'mycred' ),
			'signup_referral'     => __( 'Signup Referral', 'mycred' ),
			'anniversary'         => __( 'Anniversary', 'mycred' ),
		);

		if ( class_exists( 'BuddyPress' ) ) {
			$hooks['new_profile_update']     = __( 'New Profile Update', 'mycred' );
			$hooks['deleted_profile_update'] = __( 'Profile Update Removal', 'mycred' );
			$hooks['upload_avatar']          = __( 'Avatar Upload', 'mycred' );
			$hooks['upload_cover']           = __( 'Profile Cover Upload', 'mycred' );
			$hooks['new_friendship']         = __( 'New Friendship', 'mycred' );
			$hooks['ended_friendship']       = __( 'Ended Friendship', 'mycred' );
			$hooks['new_comment']            = __( 'New Profile Comment', 'mycred' );
			$hooks['comment_deletion']       = __( 'Profile Comment Deletion', 'mycred' );
			$hooks['fave_activity']          = __( 'Add Activity to Favorites', 'mycred' );
			$hooks['unfave_activity']        = __( 'Remove Activity from Favorites', 'mycred' );
			$hooks['new_message']            = __( 'New Message', 'mycred' );
			$hooks['sending_gift']           = __( 'Sending Gift', 'mycred' );
			$hooks['creation_of_new_group']  = __( 'New Group', 'mycred' );
			$hooks['deletion_of_group']      = __( 'Deleted Group', 'mycred' );
			$hooks['new_group_forum_topic']  = __( 'New Group Forum Topic', 'mycred' );
			$hooks['edit_group_forum_topic'] = __( 'Edit Group Forum Topic', 'mycred' );
			$hooks['new_group_forum_post']   = __( 'New Group Forum Post', 'mycred' );
			$hooks['edit_group_forum_post']  = __( 'Edit Group Forum Post', 'mycred' );
			$hooks['joining_group']          = __( 'Joining Group', 'mycred' );
			$hooks['leaving_group']          = __( 'Leaving Group', 'mycred' );
			$hooks['upload_group_avatar']    = __( 'New Group Avatar', 'mycred' );
			$hooks['upload_group_cover']     = __( 'New Group Cover', 'mycred' );
			$hooks['new_group_comment']      = __( 'New Group Post', 'mycred' );
		}

		if ( function_exists( 'bpa_init' ) || function_exists( 'bpgpls_init' ) ) {
			$hooks['photo_upload'] = __( 'Photo Upload', 'mycred' );
			$hooks['video_upload'] = __( 'Video Upload', 'mycred' );
			$hooks['music_upload'] = __( 'Music Upload', 'mycred' );
		}

		if ( function_exists( 'bp_links_setup_root_component' ) ) {
			$hooks['new_link']    = __( 'New Link', 'mycred' );
			$hooks['link_voting'] = __( 'Link Voting', 'mycred' );
			$hooks['update_link'] = __( 'Link Update', 'mycred' );
		}

		if ( class_exists( 'bbPress' ) ) {
			$hooks['new_forum'] = __( 'New Forum (bbPress)', 'mycred' );
			$hooks['new_forum_topic'] = __( 'New Forum Topic (bbPress)', 'mycred' );
			$hooks['topic_favorited'] = __( 'Favorited Topic (bbPress)', 'mycred' );
			$hooks['new_forum_reply'] = __( 'New Topic Reply (bbPress)', 'mycred' );
		}

		if ( function_exists( 'wpcf7' ) )
			$hooks['contact_form_submission'] = __( 'Form Submission (Contact Form 7)', 'mycred' );

		if ( class_exists( 'GFForms' ) )
			$hooks['gravity_form_submission'] = __( 'Form Submission (Gravity Form)', 'mycred' );

		if ( defined( 'SFTOPICS' ) ) {
			$hooks['new_forum_topic'] = __( 'New Forum Topic (SimplePress)', 'mycred' );
			$hooks['new_topic_post']  = __( 'New Forum Post (SimplePress)', 'mycred' );
		}

		if ( class_exists( 'Affiliate_WP' ) ) {
			$hooks['affiliate_signup']          = __( 'Affiliate Signup (AffiliateWP)', 'mycred' );
			$hooks['affiliate_visit_referral']  = __( 'Referred Visit (AffiliateWP)', 'mycred' );
			$hooks['affiliate_referral']        = __( 'Affiliate Referral (AffiliateWP)', 'mycred' );
			$hooks['affiliate_referral_refund'] = __( 'Referral Refund (AffiliateWP)', 'mycred' );
		}

		if ( defined( 'WP_POSTRATINGS_VERSION' ) ) {
			$hooks['post_rating']        = __( 'Adding a Rating', 'mycred' );
			$hooks['post_rating_author'] = __( 'Receiving a Rating', 'mycred' );
		}

		if ( function_exists( 'vote_poll' ) )
			$hooks['poll_voting'] = __( 'Poll Voting', 'mycred' );

		if ( function_exists( 'invite_anyone_init' ) ) {
			$hooks['sending_an_invite']   = __( 'Sending an Invite', 'mycred' );
			$hooks['accepting_an_invite'] = __( 'Accepting an Invite', 'mycred' );
		}

		// Addons
		$addons = array();
		if ( class_exists( 'myCRED_Banking_Module' ) ) {
			$addons['interest']  = __( 'Compound Interest', 'mycred' );
			$addons['recurring'] = __( 'Recurring Payout', 'mycred' );
		}

		if ( class_exists( 'myCRED_Badge_Module' ) )
			$hooks['badge_reward'] = __( 'Badge Reward', 'mycred' );

		if ( class_exists( 'myCRED_buyCRED_Module' ) ) {
			$addons['buy_creds_with_paypal_standard'] = __( 'buyCRED Purchase (PayPal Standard)', 'mycred' );
			$addons['buy_creds_with_skrill']          = __( 'buyCRED Purchase (Skrill)', 'mycred' );
			$addons['buy_creds_with_zombaio']         = __( 'buyCRED Purchase (Zombaio)', 'mycred' );
			$addons['buy_creds_with_netbilling']      = __( 'buyCRED Purchase (NETBilling)', 'mycred' );
			$addons['buy_creds_with_bitpay']          = __( 'buyCRED Purchase (BitPay)', 'mycred' );
			$addons['buy_creds_with_bank']            = __( 'buyCRED Purchase (Bank Transfer)', 'mycred' );
			$addons = apply_filters( 'mycred_buycred_refs', $addons );
		}

		if ( class_exists( 'myCRED_Coupons_Module' ) )
			$addons['coupon'] = __( 'Coupon Usage', 'mycred' );

		if ( defined( 'myCRED_GATE' ) ) {
			if ( class_exists( 'WooCommerce' ) ) {
				$addons['woocommerce_payment'] = __( 'Store Purchase (WooCommerce)', 'mycred' );
				$addons['reward']              = __( 'Store Reward (WooCommerce)', 'mycred' );
				$addons['product_review']      = __( 'Product Review (WooCommerce)', 'mycred' );
			}
			if ( class_exists( 'MarketPress' ) ) {
				$addons['marketpress_payment'] = __( 'Store Purchase (MarketPress)', 'mycred' );
				$addons['marketpress_reward']  = __( 'Store Reward (MarketPress)', 'mycred' );
			}
			if ( class_exists( 'wpsc_merchant' ) )
				$addons['wpecom_payment']      = __( 'Store Purchase (WP E-Commerce)', 'mycred' );

			$addons = apply_filters( 'mycred_gateway_refs', $addons );
		}

		if ( defined( 'EVENT_ESPRESSO_VERSION' ) ) {
			$addons['event_payment']   = __( 'Event Payment (Event Espresso)', 'mycred' );
			$addons['event_sale']      = __( 'Event Sale (Event Espresso)', 'mycred' );
		}

		if ( defined( 'EM_VERSION' ) ) {
			$addons['ticket_purchase'] = __( 'Event Payment (Events Manager)', 'mycred' );
			$addons['ticket_sale']     = __( 'Event Sale (Events Manager)', 'mycred' );
		}

		if ( class_exists( 'myCRED_Sell_Content_Module' ) ) {
			$addons['buy_content']  = __( 'Content Purchase', 'mycred' );
			$addons['sell_content'] = __( 'Content Sale', 'mycred' );
		}

		if ( class_exists( 'myCRED_Transfer_Module' ) )
			$addons['transfer'] = __( 'Transfer', 'mycred' );

		$references = array_merge( $hooks, $addons );

		$references['manual'] = __( 'Manual Adjustment by Admin', 'mycred' );

		return apply_filters( 'mycred_all_references', $references );

	}
endif;

/**
 * Get Used References
 * Returns an array of references currently existing in the log
 * for a particular point type. Will return false if empty.
 * @since 1.5
 * @version 1.0.1
 */
if ( ! function_exists( 'mycred_get_used_references' ) ) :
	function mycred_get_used_references( $point_type = array() ) {

		global $wpdb, $mycred_log_table;

		$query      = "SELECT DISTINCT ref FROM {$mycred_log_table} WHERE ref != ''";
		if ( ! empty( $point_type ) )
			$query .= $wpdb->prepare( " AND ctype IN ( %s" . str_repeat( ",%s", ( count( $point_type ) - 1 ) ) . " )", $point_type );

		$references = $wpdb->get_col( $query );

		return apply_filters( 'mycred_used_references', $references, $point_type );

	}
endif;

/**
 * Get Used Log Entry Count
 * @since 1.7
 * @version 1.1
 */
if ( ! function_exists( 'mycred_user_has_log_entries' ) ) :
	function mycred_user_has_log_entries( $user_id = NULL ) {

		$user_id = absint( $user_id );
		if ( $user_id === 0 ) return 0;

		$count = mycred_get_user_meta( $user_id, 'mycred-log-count', '', false );
		if ( empty( $count ) ) {

			global $wpdb, $mycred_log_table;

			$count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$mycred_log_table} WHERE user_id = %d;", $user_id ) );
			if ( $count === NULL ) $count = 0;

			mycred_add_user_meta( $user_id, 'mycred-log-count', '', $count, true );

		}

		return $count;

	}
endif;

/**
 * Count All Reference Instances
 * Counts all the reference instances in the log returning the result
 * in an assosiative array.
 * @see http://codex.mycred.me/functions/mycred_count_all_ref_instances/
 * @param $number (int) number of references to return. Defaults to 5. Use '-1' for all.
 * @param $order (string) order to return ASC or DESC
 * @filter mycred_count_all_refs
 * @since 1.3.3
 * @version 1.1.1
 */
if ( ! function_exists( 'mycred_count_all_ref_instances' ) ) :
	function mycred_count_all_ref_instances( $number = 5, $order = 'DESC', $type = MYCRED_DEFAULT_TYPE_KEY ) {

		global $wpdb, $mycred_log_table;

		$results = array();
		$mycred  = mycred( $type );

		$limit = '';
		if ( $number > 0 )
			$limit = ' LIMIT 0,' . absint( $number );

		if ( ! in_array( $order, array( 'ASC', 'DESC' ) ) )
			$order = 'DESC';

		if ( $type != 'all' )
			$type = $wpdb->prepare( 'WHERE ctype = %s', $mycred->cred_id );

		else
			$type = '';

		$query = $wpdb->get_results( "SELECT ref, COUNT(*) AS count FROM {$mycred_log_table} {$type} GROUP BY ref ORDER BY count {$order} {$limit};" );

		if ( $wpdb->num_rows > 0 ) {

			foreach ( $query as $num => $reference ) {

				$occurrence = $reference->count;
				if ( $reference->ref == 'transfer' )
					$occurrence = $occurrence/2;

				$results[ $reference->ref ] = $occurrence;

			}

			arsort( $results );

		}

		return apply_filters( 'mycred_count_all_refs', $results );

	}
endif;

/**
 * Count Reference Instances
 * Counts the total number of occurrences of a specific reference for a user.
 * @see http://codex.mycred.me/functions/mycred_count_ref_instances/
 * @param $reference (string) required reference to check
 * @param $user_id (int) option to check references for a specific user
 * @uses get_var()
 * @since 1.1
 * @version 1.1
 */
if ( ! function_exists( 'mycred_count_ref_instances' ) ) :
	function mycred_count_ref_instances( $reference = '', $user_id = NULL, $type = MYCRED_DEFAULT_TYPE_KEY ) {

		if ( $reference == '' ) return 0;

		global $wpdb, $mycred_log_table;

		$wheres   = array();
		$wheres[] = $wpdb->prepare( "ref = %s", $reference );

		if ( $user_id !== NULL )
			$wheres[] = $wpdb->prepare( "user_id = %d", $user_id );

		if ( mycred_point_type_exists( $type ) )
			$wheres[] = $wpdb->prepare( "ctype = %s", $type );

		$wheres   = implode( ' AND ', $wheres );

		$count    = $wpdb->get_var( "SELECT COUNT(*) FROM {$mycred_log_table} WHERE {$wheres};" );
		if ( $count === NULL ) $count = 0;

		return $count;

	}
endif;

/**
 * Count Reference ID Instances
 * Counts the total number of occurrences of a specific reference combined with a reference ID for a user.
 * @see http://codex.mycred.me/functions/mycred_count_ref_id_instances/
 * @param $reference (string) required reference to check
 * @param $user_id (int) option to check references for a specific user
 * @uses get_var()
 * @since 1.5.3
 * @version 1.1
 */
if ( ! function_exists( 'mycred_count_ref_id_instances' ) ) :
	function mycred_count_ref_id_instances( $reference = '', $ref_id = NULL, $user_id = NULL, $type = MYCRED_DEFAULT_TYPE_KEY ) {

		if ( $reference == '' || $ref_id === NULL ) return 0;

		global $wpdb, $mycred_log_table;

		$wheres   = array();
		$wheres[] = $wpdb->prepare( "ref = %s",    $reference );
		$wheres[] = $wpdb->prepare( "ref_id = %d", $ref_id );

		if ( $user_id !== NULL )
			$wheres[] = $wpdb->prepare( "user_id = %d", $user_id );

		if ( mycred_point_type_exists( $type ) )
			$wheres[] = $wpdb->prepare( "ctype = %s", $type );

		$wheres   = implode( ' AND ', $wheres );

		$count    = $wpdb->get_var( "SELECT COUNT(*) FROM {$mycred_log_table} WHERE {$wheres};" );
		if ( $count === NULL ) $count = 0;

		return $count;

	}
endif;

/**
 * Get Users Reference Count
 * @since 1.7
 * @version 1.0
 */
if ( ! function_exists( 'mycred_get_users_reference_count' ) ) :
	function mycred_get_users_reference_count( $user_id = NULL, $point_type = MYCRED_DEFAULT_TYPE_KEY ) {

		if ( $user_id === NULL ) return false;

		$references = (array) mycred_get_user_meta( $user_id, 'mycred_ref_counts-' . $point_type, '', true );
		$references = maybe_unserialize( $references );

		if ( empty( $references ) ) {

			global $wpdb, $mycred_log_table;

			$query = $wpdb->get_results( $wpdb->prepare( "SELECT COUNT(*) AS total, ref AS reference FROM {$mycred_log_table} WHERE user_id = %d AND ctype = %s GROUP BY ref ORDER BY total DESC;", $user_id, $point_type ) );
			if ( ! empty( $query ) ) {
				foreach ( $query as $result ) {
					$references[ $result->reference ] = $result->total;
				}
			}

			mycred_update_user_meta( $user_id, 'mycred_ref_counts-' . $point_type, '', $references );

		}

		return $references;

	}
endif;

/**
 * Get Users Reference Sum
 * @since 1.7
 * @version 1.1
 */
if ( ! function_exists( 'mycred_get_users_reference_sum' ) ) :
	function mycred_get_users_reference_sum( $user_id = NULL, $point_type = MYCRED_DEFAULT_TYPE_KEY, $force = false ) {

		if ( $user_id === NULL ) return false;

		$references = (array) mycred_get_user_meta( $user_id, 'mycred_ref_sums-' . $point_type, '', true );
		$references = maybe_unserialize( $references );

		if ( $force || empty( $references ) || empty( $references[0] ) ) {

			global $wpdb, $mycred_log_table;

			$query = $wpdb->get_results( $wpdb->prepare( "SELECT SUM(creds) AS total, ref AS reference FROM {$mycred_log_table} WHERE user_id = %d AND ctype = %s GROUP BY ref ORDER BY total DESC;", $user_id, $point_type ) );
			if ( ! empty( $query ) ) {

				$references = array();
				foreach ( $query as $result ) {
					$references[ $result->reference ] = $result->total;
				}

				mycred_update_user_meta( $user_id, 'mycred_ref_sums-' . $point_type, '', $references );

			}

		}

		return $references;

	}
endif;

/**
 * Get Search Args
 * Converts URL arguments into an array of log query friendly arguments.
 * @since 1.8
 * @since 2.3 Added `fields` in exclude array to prevent SQL Injection
 * @version 1.0.3
 */
if ( ! function_exists( 'mycred_get_search_args' ) ) :
	function mycred_get_search_args( $exclude = NULL ) {

		if ( $exclude === NULL )
			$exclude = array( 'page', 'mycred-export', 'mycred-action', 'action', 'set', '_token', 'fields' );

		$search_args = array();
		if ( ! empty( $_GET ) ) {
			foreach ( $_GET as $key => $value ) {

				$key   = sanitize_key( $key );

				if ( $key === '' || in_array( $key, $exclude ) ) continue;

				if ( in_array( $key, array( 'user_id', 'paged', 'number' ) ) ) {
					$value = absint( $value );
					if ( $value === 0 ) continue;
				}

				elseif ( $key == 'user' )
					$value = mycred_get_user_id( $value );

				elseif ( is_array( $value ) ) {

					$temp = array();
					if ( ! empty( $value ) ) {
						foreach ( $value as $sub_key => $sub_value ) {
							if ( ! is_array( $sub_value ) )
								$temp[ $sub_key ] = sanitize_text_field( $sub_value );
							else
								$temp[ $sub_key ] = $sub_value;
						}
					}
					$value = $temp;

				}

				else {
					$value = sanitize_text_field( $value );
					if ( strlen( $value ) == 0 ) continue;
				}

				if ( $key === 'user' )
					$key = 'user_id';

				elseif ( $key === 'show' )
					$key = 'time';

				$search_args[ $key ] = $value;

			}
		}

		if ( ! empty( $search_args ) ) {

			// Convert comma separated lists
			if ( array_key_exists( 'time', $search_args ) && str_replace( ',', '', $search_args['time'] ) != $search_args['time'] ) {

				$timestamps = explode( ',', $search_args['time'] );
				if ( count( $timestamps ) == 2 )
					$search_args['time'] = array( 'dates' => $timestamps, 'compare' => 'BETWEEN' );
				else
					$search_args['time'] = array( 'dates' => $timestamps, 'compare' => 'IN' );

			}

			// Convert comma separated lists
			if ( array_key_exists( 'ref', $search_args ) && str_replace( ',', '', $search_args['ref'] ) != $search_args['ref'] ) {

				$references = explode( ',', $search_args['ref'] );
				if ( count( $references ) > 1 )
					$search_args['ref'] = array( 'ids' => $references, 'compare' => 'IN' );

			}

			if ( array_key_exists( 'start', $search_args ) && array_key_exists( 'end', $search_args ) ) {
				$search_args['amount'] = array( 'num' => array( $search_args['start'], $search_args['end'] ), 'compare' => 'BETWEEN' );
				unset( $search_args['start'] );
				unset( $search_args['end'] );
			}

			elseif ( array_key_exists( 'num', $search_args ) && array_key_exists( 'compare', $search_args ) ) {
				$search_args['amount'] = array( 'num' => $search_args['num'], 'compare' => urldecode( $search_args['compare'] ) );
				unset( $search_args['num'] );
				unset( $search_args['compare'] );
			}

		}

		return $search_args;

	}
endif;

/**
 * Get Users History Data
 * @since 1.8
 * @version 1.0
 */
if ( ! function_exists( 'mycred_get_users_history' ) ) :
	function mycred_get_users_history( $user_id = false, $point_type = MYCRED_DEFAULT_TYPE_KEY, $orderby = 'time', $order = 'DESC', $force = false ) {

		$history = array();
		if ( ! MYCRED_ENABLE_LOGGING || $user_id === false || absint( $user_id ) === 0 || ! mycred_point_type_exists( $point_type ) ) return $history;

		$history = mycred_get_user_meta( $user_id, $point_type, '_history', true );
		if ( $history == '' || $force ) {

			global $wpdb, $mycred_log_table;

			$history = array();
			if ( ! in_array( $orderby, array( 'rows', 'reference', 'total', 'ref_id' ) ) ) $orderby = 'rows';
			if ( ! in_array( $order, array( 'ASC', 'DESC' ) ) ) $order = 'DESC';

			$query   = $wpdb->get_results( $wpdb->prepare( "SELECT COUNT(*) AS `rows`, ref AS reference, SUM(creds) AS total, MAX(time) AS last_entry FROM {$mycred_log_table} WHERE user_id = %d AND ctype = %s GROUP BY ref ORDER BY `{$orderby}` {$order};", $user_id, $point_type ) );

			if ( ! empty( $query ) ) {
				foreach ( $query as $result ) {

					$extra = $wpdb->get_col( $wpdb->prepare( "SELECT DISTINCT ref_id FROM {$mycred_log_table} WHERE user_id = %d AND ref = %s AND ctype = %s ORDER BY time {$order} LIMIT %d;", $user_id, $result->reference, $point_type, MYCRED_MAX_HISTORY_SIZE ) );

					$result->reference_ids         = $extra;
					$history[ $result->reference ] = $result;

				}
			}

			mycred_update_user_meta( $user_id, $point_type, '_history', $history );

		}

		return apply_filters( 'mycred_get_users_history', $history, $user_id, $point_type, $orderby, $order, $force );

	}
endif;

/**
 * Save Users History Data
 * @since 1.8
 * @version 1.0
 */
if ( ! function_exists( 'mycred_update_users_history' ) ) :
	function mycred_update_users_history( $user_id = NULL, $point_type = MYCRED_DEFAULT_TYPE_KEY, $reference = '', $ref_id = 0, $amount = 0 ) {

		global $mycred_current_account;

		$current     = array();
		$user_id     = absint( $user_id );
		$history     = mycred_get_users_history( $user_id, $point_type );
		$type_object = new myCRED_Point_Type( $point_type );

		if ( empty( $history ) || ! array_key_exists( $reference, $history ) ) {

			$entry                  = new StdClass();
			$entry->rows            = 1;
			$entry->reference       = $reference;
			$entry->total           = $type_object->number( $amount );
			$entry->reference_ids   = array();
			$entry->reference_ids[] = absint( $ref_id );

		}
		else {

			$current                = $history[ $reference ];
			$current_total          = $current->total;

			$current->total         = ( $amount != 0 ) ? $type_object->number( $current_total + $amount ) : $current_total;
			$current->reference_ids = $current->reference_ids;

			if ( ! in_array( $ref_id, $current->reference_ids ) )
				$current->reference_ids[] = absint( $ref_id );

			$entry                  = $current;

		}

		$entry       = apply_filters( 'mycred_update_users_history', $entry, $current, $user_id, $point_type, $reference, $ref_id, $amount, $history );

		if ( $entry !== false ) {

			$history[ $reference ] = $entry;

			mycred_update_user_meta( $user_id, $point_type, '_history', $history );

			if ( mycred_is_current_account( $user_id ) && isset( $mycred_current_account->history ) && in_array( $point_type, $mycred_current_account->history ) ) {

				if ( isset( $mycred_current_account->balance[ $point_type ]->history ) )
					$mycred_current_account->balance[ $point_type ]->history->update_data( $reference, $entry );

			}

		}

	}
endif;
