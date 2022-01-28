<?php

namespace WP_STATISTICS;

class Admin_User
{
    /**
     * constructor.
     */
    public function __construct()
    {

        // Add Visits Column in All Admin User Wp_List_Table
        if (User::Access('read') and Option::get('enable_user_column')) {
            add_filter('manage_users_columns', array($this, 'add_column_user_table'));
            add_filter('manage_users_custom_column', array($this, 'modify_user_table_row'), 10, 3);
            add_filter('manage_users_sortable_columns', array($this, 'sort_by_custom_field'));
            add_action('pre_user_query', array($this, 'modify_pre_user_query'));
        }

        // Reset User_id as User is deleted.
        add_action('delete_user', array($this, 'modify_delete_user'));
    }

    /**
     * Add Visits Link
     *
     * @param $column
     * @return mixed
     */
    public function add_column_user_table($column)
    {
        $column['visits'] = __("Visits", "wp-statistics");
        return $column;
    }

    /**
     * Modify Users Row
     *
     * @param $val
     * @param $column_name
     * @param $user_id
     * @return mixed
     */
    public function modify_user_table_row($val, $column_name, $user_id)
    {
        switch ($column_name) {
            case 'visits' :
                $count = Visitor::Count(array('key' => 'user_id', 'compare' => '=', 'value' => $user_id));
                return '<a href="' . Menus::admin_url('visitors', array('user_id' => $user_id)) . '" class="wps-text-muted" target="_blank">' . number_format_i18n($count) . '</a>';
            default:
        }
        return $val;
    }

    /**
     * Sort By Users Visit
     *
     * @param $columns
     * @return mixed
     */
    function sort_by_custom_field($columns)
    {
        $columns['visits'] = 'visit';
        return $columns;
    }

    /**
     * Pre User Query Join by visitors
     *
     * @param $user_query
     */
    public function modify_pre_user_query($user_query)
    {
        global $wpdb;

        // Check in Admin
        if (!is_admin()) {
            return;
        }

        // If order-by.
        if (isset($user_query->query_vars['orderby']) and isset($user_query->query_vars['order']) and $user_query->query_vars['orderby'] == 'visit') {
            // Get global Variable
            $order = $user_query->query_vars['order'];

            // Select Field
            $user_query->query_fields .= ", (select Count(*) from " . DB::table("visitor") . " where {$wpdb->users}.ID = " . DB::table("visitor") . ".user_id) as user_visit ";

            // And order by it.
            $user_query->query_orderby = " ORDER BY user_visit $order";
        }

        return $user_query;
    }

    /**
     * Remove User from Visitors Table when user is deleted.
     *
     * @param $user_id
     */
    public function modify_delete_user($user_id)
    {
        global $wpdb;
        $wpdb->update(DB::table("visitor"), array('user_id' => 0), array('user_id' => $user_id), array('%d'), array('%d'));
    }

}

new Admin_User;