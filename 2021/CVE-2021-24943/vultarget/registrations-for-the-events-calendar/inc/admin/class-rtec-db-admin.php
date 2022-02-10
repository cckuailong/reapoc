<?php
// Don't load directly
if ( ! defined( 'ABSPATH' ) ) {
    die( '-1' );
}

/**
 * Class RTEC_Db_Admin
 * 
 * Contains methods that just apply to the admin area
 * @since 1.0
 */
class RTEC_Db_Admin extends RTEC_Db
{
	/**
	 * Used to create the registrations table on activation
	 *
	 * @since 1.0
	 * @since 1.4   added indices for event_id and status
	 */
    public static function create_table()
    {
        global $wpdb;

	        $table_name = $wpdb->prefix . RTEC_TABLENAME;
	        $charset_collate = $wpdb->get_charset_collate();

	        if ( $wpdb->get_var( "show tables like '$table_name'" ) != $table_name ) {
		        $sql = "CREATE TABLE " . $table_name . " (
                id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
                user_id BIGINT(20) UNSIGNED DEFAULT 0 NOT NULL,
                event_id BIGINT(20) UNSIGNED NOT NULL,
                registration_date DATETIME NOT NULL,
                last_name VARCHAR(1000) NOT NULL,
                first_name VARCHAR(1000) NOT NULL,
                email VARCHAR(1000) NOT NULL,
                venue VARCHAR(1000) NOT NULL,
                phone VARCHAR(40) DEFAULT '' NOT NULL,
                other VARCHAR(1000) DEFAULT '' NOT NULL,
                guests INT(11) UNSIGNED DEFAULT 0 NOT NULL,
                custom LONGTEXT DEFAULT '' NOT NULL,
                status CHAR(1) DEFAULT 'y' NOT NULL,
                action_key VARCHAR(40) DEFAULT '' NOT NULL,
                INDEX event_id (event_id),
                INDEX status (status),
                UNIQUE KEY id (id)
            ) $charset_collate;";
		        $wpdb->query( $sql );

	        add_option( 'rtec_db_version', RTEC_DBVERSION );

        }

	    $db = new RTEC_Db_Admin();
	    $db->maybe_add_index( 'event_id', 'event_id' );
	    $db->maybe_add_index( 'status', 'status' );

    }

    /**
     * Used to make changes to existing registrations
     * 
     * @param $data array           information to be updated
     * @param $custom_data array    custom data to be updated
     * @since 1.0
     */
	public function update_entry( $data, $entry_id = '', $field_atts )
	{
		global $wpdb;

		$set_string = '';

		foreach ( $data as $key => $value ) {

			if ( $key !== 'event_id' || $key !== 'id' ) {

				if ( $key !== 'custom' ) {
					$set_string .= esc_sql( $key ) . "='" . esc_sql( str_replace( "'", '`', $value ) ). "', ";
				} else {
					$custom = $this->get_custom_data( $entry_id );

					$custom = $this->update_custom_data_for_db( $custom, $data['custom'], $field_atts );

					$set_string .= "custom='" . esc_sql( $custom ) . "', ";
				}

			}

		}

		$set_string = substr( $set_string, 0, -2 );
		$esc_table_name = esc_sql( $this->table_name );

		$int_entry_id = (int)$entry_id;

		if ( ! empty( $entry_id ) ) {
			$sql = "UPDATE $esc_table_name
            SET $set_string
            WHERE id=$int_entry_id";
			$wpdb->query( "UPDATE $esc_table_name
            SET $set_string
            WHERE id=$int_entry_id" );
		}
	}

    public function get_custom_data( $id )
    {
    	global $wpdb;

	    $results = $wpdb->get_results( $wpdb->prepare( "SELECT custom FROM $this->table_name
                WHERE id=%d", $id ), ARRAY_A );

	    return maybe_unserialize( $results[0]['custom'] );
    }

	/**
	 * Updates a custom field in the database with serialization
	 *
	 * @param $db_custom
	 * @param $new_custom
	 * @param $field_atts
	 *
	 * @return mixed
	 * @since 2.0
	 */
	public function update_custom_data_for_db( $db_custom, $new_custom, $field_atts )
	{
		if ( ! empty( $new_custom ) ) {
			foreach ( $new_custom as $key => $value ) {
				$db_custom[$key] = array(
					'value' => $value,
					'label' => $field_atts[$key]['label']
				);
			}
		}

		return maybe_serialize( $db_custom );
	}

    /**
     * Removes a set of records from the dashboard
     * 
     * @param $records array    ids or email of records to remove
     *
     * @return bool
     * @since 1.0
     */
    public function remove_records( $records ) {
        global $wpdb;

        $where = 'id';

        if ( is_array( $records ) ) {
            $registrations_to_be_deleted = implode( ', ', $records);
        } else {
            $registrations_to_be_deleted = $records;
        }

	    $table_name = esc_sql( $this->table_name );
	    $registrations_to_be_deleted_string = esc_sql( $registrations_to_be_deleted );

        $wpdb->query( "DELETE FROM $table_name WHERE $where IN( $registrations_to_be_deleted_string )" );

        return true;
    }

    /**
     * Used to create the alert for new registrations
     * 
     * @return false|int    false if no records, otherwise number of new registrations
     * @since 1.0
     */
    public function check_for_new()
    {
        global $wpdb;

        $new = 'n';

        return $wpdb->query( $wpdb->prepare( "SELECT status
        FROM $this->table_name WHERE status=%s", $new ) );
    }

    /**
     * Get a hard count of the number of registrations currently
     * in the database for the give id
     * 
     * @param $id int   post ID for the event
     *
     * @return int      number registered
     * @since 1.0
     */
    public function get_registration_count( $id, $form_id = 1 )
    {
        global $wpdb;

        $result = $wpdb->get_results( $wpdb->prepare( "SELECT event_id, COUNT(*) AS num_registered
        FROM $this->table_name WHERE event_id = %d", $id ), ARRAY_A );

        return $result[0]['num_registered'];
    }

    /**
     * Manually set the number of registrations
     * 
     * @param $id int   post ID
     * @param $num int  new number to set the post meta as
     * @since 1.0
     */
    public function set_num_registered_meta( $id, $num )
    {
        update_post_meta( $id, '_RTECnumRegistered', (int)$num );
    }

    /**
     * Gets all of the post IDs with the Tribe Events post type
     * 
     * @return array    the ids
     * @since 1.0
     */
    public function get_event_post_ids() 
    {
        global $wpdb;

        $query = $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type = %s", RTEC_TRIBE_EVENTS_POST_TYPE );
        $event_ids = $wpdb->get_col( $query );

        return $event_ids;
    }

	/**
	 * Get search results from registrations table
	 *
	 * @param $term     string
	 * @param $columns  string
	 *
	 * @return array|mixed|null|object
	 * @since 2.0
	 */
	public function get_matches( $term, $columns )
	{
		global $wpdb;

		$where_clause = '';
		if ( is_array( $columns ) ) {
			$i = 1;
			$size = count( $columns );

			foreach ( $columns as $column ) {
				$where_clause .= esc_sql( $column ) . ' LIKE "%' . esc_sql( $term ) . '%"';

				if ( $size > $i ) {
					$where_clause .= ' OR ';
				}

				$i++;
			}

		} else {
			$where_clause .= $columns . 'LIKE %' . $term . '%';
		}

		$table_name = esc_sql( $this->table_name );

		$matches = $wpdb->get_results( "SELECT *
        FROM $table_name WHERE $where_clause ORDER BY id DESC LIMIT 200", ARRAY_A );

		return $matches;
	}

	/**
	 * Used to update the database to accommodate new columns added since release
	 *
	 * @param $column string    name of column to add if it doesn't exist
	 * @since 1.1
	 */
    public function maybe_add_column_to_table( $column, $type = 'VARCHAR(40)' )
    {
	    global $wpdb;

	    $table_name = esc_sql( $this->table_name );
	    $column_name = esc_sql( $column );
	    $type_name = esc_sql( $type );

	    $results = $wpdb->query( "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '$table_name' AND column_name = '$column_name'" );

	    if ( $results == 0 ){
		    $wpdb->query( "ALTER TABLE $table_name ADD $column_name $type_name DEFAULT '' NOT NULL" );
	    }
    }

	/**
	 * Used to update the database to accommodate new columns added since release
	 *
	 * @param $column string    name of column to add if it doesn't exist
	 * @since 1.1
	 */
	public function maybe_add_column_to_table_no_string( $column, $type = 'INT(11)' )
	{
		global $wpdb;

		$table_name = esc_sql( $this->table_name );
		$column_name = esc_sql( $column );
		$type_name = esc_sql( $type );

		$results = $wpdb->query( "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '$table_name' AND column_name = '$column_name'" );

		if ( $results == 0 ){
			$wpdb->query( "ALTER TABLE $table_name ADD $column_name $type_name DEFAULT 0 NOT NULL" );
		}
	}

	/**
	 * Used to add indices to registrations table
	 *
	 * @param $index string    name of index to add if it doesn't exist
	 * @param $column string        name of column to add index to
	 * @since 1.3
	 */
    public function maybe_add_index( $index, $column )
    {
	    global $wpdb;

	    $table_name = esc_sql( $this->table_name );
	    $column_name = esc_sql( $column );
	    $index_name = esc_sql( $index );

	    $results = $wpdb->get_results( "SELECT COUNT(1) indexExists FROM INFORMATION_SCHEMA.STATISTICS 
			WHERE table_schema=DATABASE() AND table_name = '$table_name' AND index_name = '$index_name'" );

	    if ( $results[0]->indexExists == '0' ){
		    $wpdb->query( "ALTER TABLE $table_name ADD INDEX $index_name ($column_name)" );
	    }
    }

	/**
	 * Used to add indices to registrations table
	 *
	 * @param $edit string    name of index to add if it doesn't exist
	 * @param $column string        name of column to add index to
	 * @since 1.3
	 */
	public function maybe_update_column( $edit, $column )
	{
		global $wpdb;

		$table_name = esc_sql( $this->table_name );
		$column_name = esc_sql( $column );
		$edit = esc_sql( $edit );

		$results = $wpdb->query( "ALTER TABLE $table_name MODIFY $column_name $edit" );
	}

	/**
	 * @since 2.3
	 */
	public function get_event_ids( $args, $arrange = 'DESC' )
	{
		global $wpdb;

		$where_clause = $this->build_escaped_where_clause( $args['where'] );
		$results = $wpdb->get_col( "SELECT event_id FROM $this->table_name WHERE $where_clause;" );

		return $results;

	}
}