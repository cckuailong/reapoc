<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class that handles database queries for the Bookings
 *
 */
class WPBS_Object_DB_Bookings extends WPBS_Object_DB
{

    /**
     * Construct
     *
     */
    public function __construct()
    {

        global $wpdb;

        $this->table_name = $wpdb->prefix . 'wpbs_bookings';
        $this->primary_key = 'id';
        $this->context = 'booking';
        $this->query_object_type = 'WPBS_Booking';

    }

    /**
     * Return the table columns
     *
     */
    public function get_columns()
    {

        return array(
            'id' => '%d',
            'calendar_id' => '%d',
            'form_id' => '%d',
            'start_date' => '%s',
            'end_date' => '%s',
            'fields' => '%s',
            'status' => '%s',
            'is_read' => '%d',
            'date_created' => '%s',
            'date_modified' => '%s',
        );
    }

    /**
     * Returns an array of WPBS_Booking objects from the database
     *
     * @param array $args
     * @param bool  $count - whether to return just the count for the query or not
     *
     * @return mixed array|int
     *
     */
    public function get_bookings($args = array(), $count = false)
    {   
        $defaults = array(
            'number' => -1,
            'offset' => 0,
            'orderby' => 'id',
            'order' => 'DESC',
            'include' => array(),
            'search' => '',
            'calendar_id' => '',
            'form_id' => '',
            'status' => array(),
            'is_read' => ''
        );

        $args = wp_parse_args($args, $defaults);

        /**
         * Filter the query arguments just before making the db call
         *
         * @param array $args
         *
         */
        $args = apply_filters('wpbs_get_bookings_args', $args);


        // Where clause
        $where = "WHERE 1=1";

        // Calendar ID where clause
        if (!empty($args['calendar_id'])) {

            $calendar_id = absint($args['calendar_id']);
            $where .= " AND calendar_id = {$calendar_id}";

        }

        // Form ID where clause
        if (!empty($args['form_id'])) {

            $form_id = absint($args['form_id']);
            $where .= " AND form_id = {$form_id}";

        }

        // Is Read where clause
        if (is_int($args['is_read'])) {

            $is_read = absint($args['is_read']);
            $where .= " AND is_read = {$is_read}";

        }

        // Number args
        if ($args['number'] < 1) {
            $args['number'] = 999999;
        }

        // Status where clause
        if (!empty($args['status'])) {
            $status_query = '';
            foreach ($args['status'] as $status) {
                $status = sanitize_text_field($status);
                $status_query .= "status = '{$status}' OR ";
            }
            $status_query = trim($status_query, ' OR ');

            $where .= " AND ({$status_query})";

        }

        // Include where clause
        if (!empty($args['include'])) {

            $include = implode(',', $args['include']);
            $where .= " AND id IN({$include})";

        }

        // Include search
        if (!empty($args['search'])) {

            $search = sanitize_text_field($args['search']);
            $where .= " AND name LIKE '%%{$search}%%'";

        }

        

        // Orderby
        $orderby = sanitize_text_field($args['orderby']);

        // Order
        $order = ('DESC' === strtoupper($args['order']) ? 'DESC' : 'ASC');

        $clauses = compact('where', 'orderby', 'order', 'count');

        $results = $this->get_results($clauses, $args, 'wpbs_get_booking');

        return $results;

    }

    /**
     * Creates and updates the database table for the bookings
     *
     */
    public function create_table()
    {

        global $wpdb;

        $table_name = $this->table_name;
        $charset_collate = $wpdb->get_charset_collate();

        $query = "CREATE TABLE {$table_name} (
			id bigint(10) NOT NULL AUTO_INCREMENT,
			calendar_id bigint(10) NOT NULL,
			form_id bigint(10) NOT NULL,
            start_date datetime NOT NULL,
			end_date datetime NOT NULL,
			fields longtext NOT NULL,
			status text NOT NULL,
            is_read tinyint(1) NOT NULL,
			date_created datetime NOT NULL,
			date_modified datetime NOT NULL,
			PRIMARY KEY  id (id)
		) {$charset_collate};";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($query);

    }

}
