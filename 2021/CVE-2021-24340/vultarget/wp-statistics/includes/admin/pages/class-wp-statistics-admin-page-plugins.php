<?php

namespace WP_STATISTICS;

class plugins_page
{

    /**
     * List Of WP-Statistics AddOns API
     *
     * @var string
     */
    public static $addons = 'https://wp-statistics.com/wp-json/plugin/addons';

    /**
     * Get Change Log of Last Version Wp-Statistics
     *
     * @var string
     */
    public static $change_log = 'https://api.github.com/repos/wp-statistics/wp-statistics/releases/latest';

    /**
     * plugins_page constructor.
     */
    public function __construct()
    {

        if (Menus::in_page('plugins')) {
            add_filter('screen_options_show_screen', '__return_false');
        }
    }

    /**
     * Get List WP-Statistics addons
     */
    public static function get_list_addons()
    {
        $response        = wp_remote_get(self::$addons);
        $response_code   = wp_remote_retrieve_response_code($response);
        $error           = null;
        $args['plugins'] = array();

        // Check response
        if (is_wp_error($response)) {
            $args['error'] = $response->get_error_message();
        } else {
            if ($response_code == '200') {
                $args['plugins'] = json_decode($response['body']);
            } else {
                $args['error'] = $response['body'];
            }
        }

        return $args;
    }

    /**
     * Show change log
     */
    public static function show_change_log()
    {

        // Get Change Log From Github Api
        $response = wp_remote_get(self::$change_log);
        if (is_wp_error($response)) {
            return;
        }
        $response_code = wp_remote_retrieve_response_code($response);
        if ($response_code == '200') {

            // Json Data To Array
            $data = json_decode($response['body']);

            // Load ParseDown
            if (!class_exists('\Parsedown')) {
                include(WP_STATISTICS_DIR . "includes/libraries/Parsedown.php");
            }
            $parse = new \Parsedown();

            // convert MarkDown To Html
            echo $parse->text(nl2br($data->body));
        }
    }

    /**
     * This function displays the HTML for the page.
     */
    public static function view()
    {

        // Activate or deactivate the selected plugin
        if (isset($_GET['action'])) {

            if ($_GET['action'] == 'activate') {
                $result = activate_plugin($_GET['plugin'] . '/' . $_GET['plugin'] . '.php');
                if (is_wp_error($result)) {
                    Helper::wp_admin_notice($result->get_error_message(), "error");
                } else {
                    Helper::wp_admin_notice(__('Add-On activated.', 'wp-statistics'), "success");
                }

            }

            if ($_GET['action'] == 'deactivate') {
                $result = deactivate_plugins($_GET['plugin'] . '/' . $_GET['plugin'] . '.php');
                if (is_wp_error($result)) {
                    Helper::wp_admin_notice($result->get_error_message(), "error");
                } else {
                    Helper::wp_admin_notice(__('Add-On deactivated.', 'wp-statistics'), "success");
                }
            }
        }

        Admin_Template::get_template(array('plugins'), self::get_list_addons());
    }

}

new plugins_page;