<?php

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Class that handles database queries for the Events
 *
 */
class WPBS_Object_DB_Events extends WPBS_Object_DB
{

    /**
     * Construct
     *
     */
    public function __construct()
    {

        global $wpdb;

        $this->table_name = $wpdb->prefix . 'wpbs_events';
        $this->primary_key = 'id';
        $this->context = 'event';
        $this->query_object_type = 'WPBS_Event';

    }

    /**
     * Return the table columns
     *
     */
    public function get_columns()
    {

        return array(
            'id' => '%d',
            'date_year' => '%d',
            'date_month' => '%d',
            'date_day' => '%d',
            'booking_id' => '%d',
            'calendar_id' => '%d',
            'legend_item_id' => '%d',
            'description' => '%s',
            'tooltip' => '%s',
            'price' => '%s',
        );

    }

    /**
     * Returns an array of WPBS_Event objects from the database
     *
     * @param array $args
     * @param bool  $count - whether to return just the count for the query or not
     *
     * @return mixed array|int
     *
     */
    public function get_events($args = array(), $count = false)
    {

        $defaults = array(
            'number' => -1,
            'offset' => 0,
            'orderby' => 'id',
            'order' => 'ASC',
            'date_year' => array(),
            'date_month' => array(),
            'date_day' => array(),
            'calendar_id' => 0,
            'legend_item_id' => 0,
        );

        $args = wp_parse_args($args, $defaults);

        // Number args
        if ($args['number'] < 1) {
            $args['number'] = 999999;
        }

        // Where clause
        $where = '';

        // Calendar ID where clause
        if (!empty($args['calendar_id'])) {

            $calendar_id = absint($args['calendar_id']);
            $where .= "WHERE calendar_id = {$calendar_id}";

        }

        // Legend Item ID where clause
        if (!empty($args['legend_item_id'])) {

            $legend_item_id = absint($args['legend_item_id']);
            $where .= " AND legend_item_id = {$legend_item_id}";

        }

        // Date Year Include
        if (!empty($args['date_year'])) {

            if (is_array($args['date_year'])) {

                $date_year = implode(',', array_map('absint', $args['date_year']));
                $where .= " AND date_year IN ( {$date_year} )";

            } else {

                $date_year = absint($args['date_year']);
                $where .= " AND date_year = {$date_year}";

            }

        }

        // Date Month Include
        if (!empty($args['date_month'])) {

            if (is_array($args['date_month'])) {

                $date_month = implode(',', array_map('absint', $args['date_month']));
                $where .= " AND date_month IN ( {$date_month} )";

            } else {

                $date_month = absint($args['date_month']);
                $where .= " AND date_month = {$date_month}";

            }

        }

        // Date Month Include
        if (!empty($args['date_day'])) {

            if (is_array($args['date_day'])) {

                $date_day = implode(',', array_map('absint', $args['date_day']));
                $where .= " AND date_day IN ( {$date_day} )";

            } else {

                $date_day = absint($args['date_day']);
                $where .= " AND date_day = {$date_day}";

            }

        }

        // Orderby
        $orderby = sanitize_text_field($args['orderby']);

        // Order
        $order = ('DESC' === strtoupper($args['order']) ? 'DESC' : 'ASC');

        // Merge clauses
        $clauses = compact('where', 'orderby', 'order', 'count');

        // Get results
        $results = $this->get_results($clauses, $args, 'wpbs_get_event');

        return $results;

    }

    /**
     * Creates and updates the database table for the legend items
     *
     */
    public function create_table()
    {
        global $wpdb;

        $table_name = $this->table_name;
        $charset_collate = $wpdb->get_charset_collate();

        $query = "CREATE TABLE {$table_name} (
			id bigint(10) NOT NULL AUTO_INCREMENT,
			date_year smallint(4) NOT NULL,
			date_month smallint(2) NOT NULL,
			date_day smallint(2) NOT NULL,
			calendar_id bigint(10) NOT NULL,
			booking_id bigint(10) NOT NULL,
			legend_item_id bigint(10) NOT NULL,
			description longtext NOT NULL,
			tooltip longtext NOT NULL,
            price varchar(100) NOT NULL,
			PRIMARY KEY  id (id)
		) {$charset_collate};";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($query);

    }

}
