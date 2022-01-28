<?php namespace ProfilePress\Core\Classes;

namespace ProfilePress\Core\Classes;

class Miscellaneous
{
    public function __construct()
    {
        $basename = plugin_basename(PROFILEPRESS_SYSTEM_FILE_PATH);
        $prefix   = is_network_admin() ? 'network_admin_' : '';
        add_filter("{$prefix}plugin_action_links_$basename", [$this, 'action_links'], 10, 4);
    }

    public function action_links($actions, $plugin_file, $plugin_data, $context)
    {
        $custom_actions = array(
            'settings' => sprintf('<a href="%s">%s</a>', PPRESS_SETTINGS_SETTING_PAGE, esc_html__('Settings', 'wp-user-avatar')),
        );

        // add the links to the front of the actions list
        return array_merge($custom_actions, $actions);
    }

    public static function get_instance()
    {
        static $instance = null;

        if (is_null($instance)) {
            $instance = new self();
        }

        return $instance;
    }
}