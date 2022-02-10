<?php
// Don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Base class for accessing the database and custom table
 */
class RTEC_Db
{
	/**
	 * @var RTEC_Db
	 *
	 * @since 1.0
	 */
	private static $instance;

	/**
	 * @var string RTEC database table name
	 */
	protected $table_name;

	/**
	 * Construct the necessary data needed to make queries
	 *
	 * Including the WordPress database object and the table name for
	 * registrations is needed to add registrations to the database
	 */
	public function __construct()
	{
		global $wpdb;

		$this->table_name = $wpdb->prefix . RTEC_TABLENAME;
	}

	/**
	 * Get the one true instance of EDD_Register_Meta.
	 *
	 * @since  1.0
	 * @return object $instance
	 */
	static public function instance() {
		if ( !self::$instance ) {
			self::$instance = new RTEC_Db();
		}

		return self::$instance;
	}

	/**
	 * Add a new entry to the custom registrations table
	 *
	 * @since 1.0
	 * @param $data
	 */
	public function insert_entry( $data, $field_attributes, $from_form = true )
	{
		global $wpdb;

		$event_id = isset( $data['event_id'] ) ? $data['event_id'] : '';
		$event_meta = ! empty( $event_id ) ? rtec_get_event_meta( $event_id ) : array( 'venue_id' => '' );
		$now = date( "Y-m-d H:i:s" );
		$registration_date = isset( $data['entry_date'] ) ? $data['entry_date'] : $now;

		if ( isset( $data['last'] ) ) {
			$last = str_replace( "'", '`', $data['last'] );
		} else {
			$last = isset( $data['last_name'] ) ? str_replace( "'", '`', $data['last_name'] ) : '';
		}

		if ( isset( $data['first'] ) ) {
			$first = str_replace( "'", '`', $data['first'] );
		} else {
			$first = isset( $data['first_name'] ) ? str_replace( "'", '`', $data['first_name'] ) : '';
		}

		$email = isset( $data['email'] ) ? $data['email'] : '';
		$venue = ! empty( $data['venue'] ) ? $data['venue'] : $event_meta['venue_id'];
		$phone = isset( $data['phone'] ) ? $data['phone'] : '';
		$other = isset( $data['other'] ) ? str_replace( "'", '`', $data['other'] ) : '';
		$custom = rtec_serialize_custom_data( $data, $field_attributes, $from_form );
		$status = isset( $data['status'] ) ? $data['status'] : 'n';
		$user_id = isset( $data['user_id'] ) ? $data['user_id'] : get_current_user_id();
		$action_key = isset( $data['action_key'] ) ? $data['action_key'] : rtec_generate_action_key();
		$wpdb->query( $wpdb->prepare( "INSERT INTO $this->table_name
          ( event_id, user_id, registration_date, last_name, first_name, email, venue, phone, other, custom, status, action_key ) VALUES ( %d, %d, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s )",
			$event_id, $user_id, $registration_date, $last, $first, $email, $venue, $phone, $other, $custom, $status, $action_key ) );

	}

	/**
	 * Get a hard count of the number of registrations currently
	 * in the database for the give id
	 *
	 * @param $event_id int   post ID for the event
	 *
	 * @return int      number registered
	 * @since 1.0
	 */
	public function get_registration_count( $event_id, $form_id = 1 )
	{
		global $wpdb;

		$num_registered = $wpdb->get_results( $wpdb->prepare( "SELECT event_id, COUNT(*) AS num_registered
            FROM $this->table_name WHERE event_id = %d", $event_id ), ARRAY_A );

		$count = isset( $num_registered[0] ) ? $num_registered[0]['num_registered'] : 0;

		$count = ! is_null( $count ) ? $count : 0;

		return $count;
	}

	/**
	 * Update the number of registrations in event meta directly
	 *
	 * @param int $id
	 * @param int $num
	 * @since 1.0
	 */
	public function update_num_registered_meta( $id, $current, $num )
	{
		$new = (int)$current + (int)$num;
		update_post_meta( $id, '_RTECnumRegistered', $new );
	}

	public function update_num_registered_meta_for_event( $event_id )
	{
		$event_meta = rtec_get_event_meta( $event_id );
		$num_registered_event = $this->get_registration_count( $event_id, $event_meta['form_id'] );

		update_post_meta( $event_id, '_RTECnumRegistered', $num_registered_event );
	}

	/**
	 * Update event meta
	 *
	 * @param int $id
	 * @param array $key_value_meta
	 * @since 1.1
	 */
	public function update_event_meta( $id, $key_value_meta )
	{
		foreach ( $key_value_meta as $key => $value ) {
			update_post_meta( $id, $key, $value );
		}
	}

	/**
	 * One a registration has been seen, status changes from (n)ew to (c)urrent
	 *
	 * @param array $ids    event ids to be updated
	 *
	 * @return bool
	 * @since 1.0
	 * @since 1.1 new parameter allows for specific ids
	 */
	public function update_statuses( $ids = NULL )
	{
		global $wpdb;

		$current = 'c';
		$new = 'n';

		if ( $ids != NULL ) {
			$id_string = implode( ', ', $ids );
			$query = $wpdb->prepare( "UPDATE $this->table_name SET status=%s WHERE status=%s", $current, $new );
			$query .=  "AND event_id IN ( " . $id_string . " )";
			$wpdb->query( $query );
		} else {
			$wpdb->query( $wpdb->prepare( "UPDATE $this->table_name SET status=%s WHERE status=%s", $current, $new ) );
		}

		delete_transient( 'rtec_new_registrations' );

		return true;
	}

	public function dismiss_new() {
		global $wpdb;

		$sql = "UPDATE $this->table_name SET status='c' WHERE status='n';";
		$return = $wpdb->query( $sql );

		delete_transient( 'rtec_new_registrations' );

		return $return;
	}

	/**
	 * Generates the registration form with a shortcode
	 *
	 * @param   $email        string  registrants entered
	 * @param   $event_id     int     post id of the event to compare
	 *
	 * @return  bool  true if email is a duplicate
	 *
	 * @since   1.6
	 */
	function check_for_duplicate_email( $email, $event_id ) {
		global $wpdb;

		$results = $wpdb->get_row( $wpdb->prepare( "SELECT email FROM $this->table_name 
		WHERE event_id=%d AND email=%s;", $event_id, $email), ARRAY_A );

		if ( isset( $results ) ) {
			return 1;
		} else {
			return 0;
		}
	}

	/**
	 * @param $email
	 * @param $event_id
	 *
	 * @return bool
	 *
	 * @since  2.19.2
	 */
	public function is_duplicate_email( $email, $event_id ) {
		global $wpdb;


		$results = $wpdb->get_results( $wpdb->prepare(
			"SELECT email FROM $this->table_name 
				WHERE event_id = %d 
				AND email = %s;",
			$event_id, $email ), ARRAY_A );

		if ( ! empty( $results ) ) {
			return true;
		}

		return false;
	}

	public function maybe_verify_token( $data )
	{
		global $wpdb;

		$result = $wpdb->get_results( $wpdb->prepare (
			"SELECT * FROM $this->table_name WHERE email=%s AND action_key=%s",
			$data['email'], $data['token'] ), ARRAY_A
		);
		$return = isset( $result[0] ) ? $result[0] : false;

		return $return;
	}

	public function remove_record_by_action_key( $action_key ) {
		global $wpdb;

		return $wpdb->query( $wpdb->prepare( "DELETE FROM $this->table_name 
			WHERE action_key=%s LIMIT 1;", $action_key ), ARRAY_A );
	}

	/**
	 * Gets all entries that meet a set of parameters
	 *
	 * @param $data array       parameters for what entries to retrieve
	 * @param $full boolean     whether to return the full custom field
	 * @param $limit string     limit if any for registrations retrieved
	 *
	 * @return mixed bool/array false if no results, registrations if there are
	 * @since 1.0
	 * @since 1.3   expanded to work with custom fields and dynamic entries
	 * @since 1.4   added the ability to limit entries retrieved
	 * @since 1.6   moved to RTEC_Db class for use in the front-end registration display
	 */

	public function retrieve_entries( $data, $full = false, $limit = 'none', $arrange = 'DESC' )
	{
		global $wpdb;

		$fields = $data['fields'];
		if ( ! is_array( $fields ) ) {
			$fields = explode( ',', str_replace( ' ', '', $fields ) );
		}
		$standard_fields = array( 'id', 'event_id', 'registration_date', 'last_name', 'first_name', 'last', 'first', 'email', 'venue', 'other', 'custom', 'phone', 'status', 'action_key', 'guests', 'user_id' );
		$request_fields = array();
		$custom_flag = 0;

		$limit_string = $limit === 'none' ? '' : ' LIMIT ' . (int)$limit;

		foreach ( $fields as $field ) {

			if ( in_array( $field, $standard_fields, true ) ) {
				if ( $field === 'first' || $field === 'last' ) {
					$field .= '_name';
				}
				//if ( isset( $args['join']))
				$request_fields[] = $field;
			} else {
				$custom_flag++;
			}

		}

		if ( $custom_flag > 0 ) {
			$request_fields[] = 'custom';
		}

		$fields = implode( ',' , $request_fields );

		$where_clause = $this->build_escaped_where_clause( $data['where'] );
		$order_by = isset( $data['order_by'] ) ? $data['order_by'] : 'last_name';
		$type = ARRAY_A;

		if ( ! isset( $data['join'] ) ) {
			$sql = sprintf(
				"
                SELECT %s
                FROM %s
				WHERE $where_clause
                ORDER BY %s DESC%s;
                ",
				esc_sql( $fields ),
				esc_sql( $this->table_name ),
				esc_sql( $order_by ),
				esc_sql( $limit_string )
			);
		} else {

			$join_table = esc_sql( $wpdb->prefix . $data['join'][1] );
			$join_type = esc_sql( $data['join'][0] );
			$join_on = "$join_table.".esc_sql( $data['join'][2] ). " = $this->table_name.".esc_sql( $data['join'][2] );
			$sql = sprintf(
				"
                SELECT %s
                FROM %s
                $join_type JOIN $join_table ON $join_on
                ORDER BY %s DESC%s;
                ",
				esc_sql( $fields ),
				esc_sql( $this->table_name ),
				esc_sql( $order_by ),
				esc_sql( $limit_string )
			);
		}

		$results = $wpdb->get_results( $sql, $type );

		if ( $custom_flag > 0 && isset( $results[0] ) ) {
			$i = 0;

			$form = new RTEC_Form();

			$event_id = isset( $results[0]['event_id'] ) ? (int)$results[0]['event_id'] : '';

			if ( $event_id === '' && isset ( $_GET['id'] ) ) {
				$event_id = (int)$_GET['id'];
			}

			$form->build_form( $event_id );
			$fields_atts = $form->get_field_attributes();

			foreach ( $results as $result ) {

				if ( isset( $result['custom'] ) ) {
					if ( rtec_has_deprecated_data_structure( maybe_unserialize( $result['custom'] ) ) ) {
						$results[$i]['custom'] = rtec_get_parsed_custom_field_data( $result['custom'], $fields_atts );
					} else {
						$results[$i]['custom'] = rtec_get_parsed_custom_field_data_full_structure( $result['custom'] );
					}
				}

				$i++;
			}

		}

		return $results;
	}

	/**
	 * retrieves registrations from the database based on what data is being shown
	 *
	 * @param   $event_meta   array  the meta for the event
	 *
	 * @return  array                associative array of all registrations that have been reviewed
	 *
	 * @since   1.6
	 */
	public function get_registrants_data( $event_meta, $attendee_list_fields = array() )
	{
		global $rtec_options;

		if ( empty( $attendee_list_fields ) ) {
			$attendee_list_fields =  array( 'first_name', 'last_name' );
		}

		$args = array(
			'fields' => $attendee_list_fields,
			'where' => array(
				array( 'event_id', $event_meta['post_id'], '=', 'int' )
			),
			'order_by' => 'registration_date'
		);

		if ( isset( $rtec_options['registrants_data_who'] ) && $rtec_options['registrants_data_who'] === 'users_and_confirmed' ) {
			$args['where'][] = array( 'status', '"n"', '!=', 'string' );
		}

		$rtec = RTEC();
		$form = $rtec->form->instance();

		$registrants = $this->retrieve_entries( $args, false, 300, $arrange = 'DESC' );

		if ( isset( $registrants[0] ) ) {

			if ( isset( $registrants[0]['custom'] ) ) {
				$i = 0;
				$custom_label_names = $form->get_custom_fields_label_name_pairs();
				$custom_field_name_label_pairs = array_flip( $custom_label_names );

				foreach ( $registrants as $registrant ) {

					foreach ( $attendee_list_fields as $retrieve_field ) {

						if ( isset( $registrant['custom'][ $retrieve_field ] ) ) {

							if ( isset( $registrant['custom'][ $retrieve_field ]['value'] ) ) {
								$registrants[ $i ][ $retrieve_field ] = $registrant['custom'][ $retrieve_field ]['value'];
							} elseif ( isset( $registrant['custom'][ $custom_field_name_label_pairs[ $retrieve_field ] ] ) ) {
								$registrants[ $i ][ $custom_label_names[ $retrieve_field ] ] = $registrant['custom'][ $custom_field_name_label_pairs[ $retrieve_field ] ];
							}

						} elseif ( isset( $custom_field_name_label_pairs[ $retrieve_field ] ) && isset( $registrant['custom'][ $custom_field_name_label_pairs[ $retrieve_field ] ] ) ) {
							$registrants[ $i ][ $retrieve_field ] = $registrant['custom'][ $custom_field_name_label_pairs[ $retrieve_field ] ];
						}

					}

					unset( $registrants[ $i ]['custom'] );
					$i++;
				}

			}

			return $registrants;
		} else {
			return array();
		}
	}


	public function get_custom_field_label_array( $custom_columns ) {
		global $wpdb;

		$table_name = esc_sql( $wpdb->prefix . RTEC_TABLENAME_FORM_FIELDS );
		$size = count( $custom_columns );
		$i = 1;

		$return_results = array();
		$in_clause = '';

		foreach ( $custom_columns as $column ) {
			$return_results[$column] = array( 'label' => '' );
			$in_clause .= "'" . esc_sql( $column ) . "'";
			if ( $i < $size ) {
				$in_clause .= ',';
			}
			$i++;
		}

		$results = $in_clause !== '' ? $wpdb->get_results( "SELECT label, field_name FROM $table_name WHERE field_name IN ( $in_clause );", ARRAY_A ) : '';

		if ( isset( $results[0] ) ) {

			foreach ( $results as $result ) {
				$return_results[ $result['field_name'] ]['label'] = str_replace( '&#42;', '', $result['label'] );
			}

			return $return_results;
		} else {
			return false;
		}
	}

	/**
	 * Used to build an escaped where clause for special queries
	 *
	 * @param $where    array of arrays of settings for a where clause 'AND'
	 *
	 * @return string   escaped where clause
	 *
	 * @since 1.6
	 */
	protected function build_escaped_where_clause( $where )
	{
		$i = 1;
		$size = count( $where );
		$where_clause = '';
		if ( ! empty( $where ) ) {
			foreach ( $where as $item ) {
				if ( $item[2] === '=' ) {
					if ( $item[3] === 'string' ) {
						$where_clause .= esc_sql( $item[0] ) . ' '. esc_sql( $item[2] ) .' "' . esc_sql( $item[1] ) . '"';
					} else {
						$where_clause .= esc_sql( $item[0] ) . ' '. esc_sql( $item[2] ) .' ' . esc_sql( $item[1] );
					}
				} elseif ( $item[2] === '!=' ) {
					$where_clause .= esc_sql( $item[0] ) . ' NOT IN (' . $item[1] . ')';
				}

				if ( $size > $i ) {
					$where_clause .= ' AND ';
				}
				$i++;
			}
		}

		return $where_clause;
	}
}
RTEC_Db::instance();