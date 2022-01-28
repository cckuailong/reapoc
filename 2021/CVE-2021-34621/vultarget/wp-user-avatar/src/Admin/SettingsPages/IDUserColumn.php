<?php

namespace ProfilePress\Core\Admin\SettingsPages;

class IDUserColumn
{
    public function __construct()
    {
        add_filter('manage_users_columns', [$this, 'add_user_id_column'], 999999);
        add_action('manage_users_custom_column', [$this, 'show_user_id'], 10, 3);
    }

    /**
     * @param $columns
     *
     * @return mixed
     */
    public function add_user_id_column($columns)
    {
        $columns['ppress_user_id'] = __('ID', 'wp-user-avatar');

        return $columns;
    }

    /**
     * @param $value
     * @param $column_name
     * @param $user_id
     *
     * @return mixed
     */
    public function show_user_id($value, $column_name, $user_id)
    {
        if ('ppress_user_id' == $column_name) {

            $value = $user_id;
        }

        return $value;
    }

    /**
     * @return self
     */
    public static function get_instance()
    {
        static $instance = null;

        if (is_null($instance)) {
            $instance = new self();
        }

        return $instance;
    }
}