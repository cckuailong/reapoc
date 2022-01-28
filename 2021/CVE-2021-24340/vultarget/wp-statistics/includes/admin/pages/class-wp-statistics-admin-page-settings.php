<?php

namespace WP_STATISTICS;

class settings_page
{

    public function __construct()
    {

        // Save Setting Action
        add_action('admin_init', array($this, 'save'));

        // Admin Notice
        add_action('admin_notices', array($this, 'notice'));

        // Check Access Level
        if (Menus::in_page('settings') and !User::Access('manage')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }
    }

    /**
     * Show Setting Page Html
     */
    public static function view()
    {

        // Check admin notices.
        if (Option::get('admin_notices') == true) {
            Option::update('disable_donation_nag', false);
            Option::update('disable_suggestion_nag', false);
        }

        // Add Class inf
        $args['class'] = 'wp-statistics-settings';

        // Check User Access To Save Setting
        $args['wps_admin'] = false;
        if (User::Access('manage')) {
            $args['wps_admin'] = true;
        }
        if ($args['wps_admin'] === false) {
            $args['wps_admin'] = 0;
        }

        // Get Search List
        $args['selist'] = SearchEngine::getList(true);

        // Get Permalink Structure
        $args['permalink']                    = get_option('permalink_structure');
        $args['disable_strip_uri_parameters'] = false;
        if ($args['permalink'] == '' || strpos($args['permalink'], '?') !== false) {
            $args['disable_strip_uri_parameters'] = true;
        }

        // Get List All Options
        $args['wp_statistics_options'] = Option::getOptions();

        // Load Template
        Admin_Template::get_template(array('layout/header', 'layout/title', 'settings', 'layout/footer'), $args);
    }

    /**
     * Save Setting
     */
    public function save()
    {

        // Check Form Nonce
        if (isset($_POST['wp-statistics-nonce']) and wp_verify_nonce($_POST['wp-statistics-nonce'], 'update-options')) {

            // Check Reset Option Wp-Statistics
            self::reset_wp_statistics_options();

            // Get All List Options
            $wp_statistics_options = Option::getOptions();

            // Run Update Option
            $method_list = array(
                'general',
                'visitor_ip',
                'access_level',
                'exclusion',
                'external',
                'wp_cli',
                'maintenance',
                'notification',
                'dashboard',
                'privacy'
            );
            foreach ($method_list as $method) {
                $wp_statistics_options = self::{'save_' . $method . '_option'}($wp_statistics_options);
            }

            // Save Option
            Option::save_options($wp_statistics_options);

            // Redirect User To Save Setting
            wp_redirect(add_query_arg(array('save_setting' => 'yes'), Menus::admin_url('settings')));

            // die
            exit;
        }
    }

    /**
     * Convert input name to Option
     *
     * @param $name
     * @return mixed
     */
    public static function input_name_to_option($name)
    {
        return str_replace("wps_", "", $name);
    }

    /**
     * Save Privacy Option
     *
     * @param $wp_statistics_options
     * @return mixed
     */
    public static function save_privacy_option($wp_statistics_options)
    {
        $wps_option_list = array(
            'wps_anonymize_ips',
            'wps_hash_ips',
            'wps_store_ua',
            'wps_all_online',
        );

        // If the IP hash's are enabled, disable storing the complete user agent.
        if (array_key_exists('wps_hash_ips', $_POST)) {
            $_POST['wps_store_ua'] = '';
        }

        foreach ($wps_option_list as $option) {
            $wp_statistics_options[self::input_name_to_option($option)] = (isset($_POST[$option]) ? $_POST[$option] : '');
        }

        return $wp_statistics_options;
    }

    /**
     * Save Notification
     *
     * @param $wp_statistics_options
     * @return mixed
     */
    public static function save_notification_option($wp_statistics_options)
    {

        if (isset($_POST['wps_time_report'])) {
            if (Option::get('time_report') != $_POST['wps_time_report']) {

                if (wp_next_scheduled('wp_statistics_report_hook')) {
                    wp_unschedule_event(wp_next_scheduled('wp_statistics_report_hook'), 'wp_statistics_report_hook');
                }

                wp_schedule_event(time(), $_POST['wps_time_report'], 'wp_statistics_report_hook');
            }
        }

        $wps_option_list = array(
            "wps_stats_report",
            "wps_time_report",
            "wps_send_report",
            "wps_content_report",
            "wps_email_list",
            "wps_geoip_report",
            "wps_prune_report",
            "wps_upgrade_report",
            "wps_admin_notices",
        );

        foreach ($wps_option_list as $option) {
            $wp_statistics_options[self::input_name_to_option($option)] = (isset($_POST[$option]) ? stripslashes($_POST[$option]) : '');
        }

        return $wp_statistics_options;
    }

    /**
     * Save Dashboard Option
     *
     * @param $wp_statistics_options
     * @return mixed
     */
    public static function save_dashboard_option($wp_statistics_options)
    {
        $wps_option_list = array('wps_disable_map', 'wps_disable_dashboard', 'wps_disable_editor');
        foreach ($wps_option_list as $option) {
            $wp_statistics_options[self::input_name_to_option($option)] = (isset($_POST[$option]) ? $_POST[$option] : '');
        }

        return $wp_statistics_options;
    }

    /**
     * Save maintenance Option
     *
     * @param $wp_statistics_options
     * @return mixed
     */
    public static function save_maintenance_option($wp_statistics_options)
    {
        $wps_option_list = array(
            'wps_schedule_dbmaint',
            'wps_schedule_dbmaint_days',
            'wps_schedule_dbmaint_visitor',
            'wps_schedule_dbmaint_visitor_hits',
        );
        foreach ($wps_option_list as $option) {
            $wp_statistics_options[self::input_name_to_option($option)] = (isset($_POST[$option]) ? $_POST[$option] : '');
        }

        return $wp_statistics_options;
    }

    /**
     * Save External Option
     *
     * @param $wp_statistics_options
     * @return mixed
     */
    public static function save_external_option($wp_statistics_options)
    {

        $wps_option_list = array(
            'wps_geoip',
            'wps_update_geoip',
            'wps_schedule_geoip',
            'wps_geoip_city',
            'wps_auto_pop',
            'wps_private_country_code',
            'wps_referrerspam',
            'wps_schedule_referrerspam'
        );

        // For country codes we always use upper case, otherwise default to 000 which is 'unknown'.
        if (array_key_exists('wps_private_country_code', $_POST)) {
            $_POST['wps_private_country_code'] = trim(strtoupper($_POST['wps_private_country_code']));
        } else {
            $_POST['wps_private_country_code'] = GeoIP::$private_country;
        }

        if ($_POST['wps_private_country_code'] == '') {
            $_POST['wps_private_country_code'] = GeoIP::$private_country;
        }

        foreach ($wps_option_list as $option) {
            $wp_statistics_options[self::input_name_to_option($option)] = (isset($_POST[$option]) ? $_POST[$option] : '');
        }

        // Check Is Checked GEO-IP and Download
        foreach (array("geoip" => "country", "geoip_city" => "city") as $geo_opt => $geo_name) {
            if (!isset($_POST['update_geoip']) and isset($_POST['wps_' . $geo_opt])) {

                //Check File Not Exist
                $file = GeoIP::get_geo_ip_path($geo_name);
                if (!file_exists($file)) {
                    $result = GeoIP::download($geo_name);
                    if (isset($result['status']) and $result['status'] === false) {
                        $wp_statistics_options[$geo_opt] = '';
                    }
                }
            }
        }

        // Check Update Referrer Spam List
        if (isset($_POST['wps_referrerspam'])) {
            $status = Referred::download_referrer_spam();
            if (is_bool($status) and $status === false) {
                $wp_statistics_options['referrerspam'] = '';
            }
        }

        return $wp_statistics_options;
    }

    /**
     * Save WP CLI Option
     *
     * @param $wp_statistics_options
     * @return mixed
     */
    public static function save_wp_cli_option($wp_statistics_options)
    {

        // Save Exclusion
        $wps_option_list = array(
            'wps_wp_cli',
            'wps_wp_cli_summary',
            'wps_wp_cli_user_online',
            'wps_wp_cli_visitors'
        );

        foreach ($wps_option_list as $option) {
            $wp_statistics_options[self::input_name_to_option($option)] = (isset($_POST[$option]) ? $_POST[$option] : '');
        }

        return $wp_statistics_options;
    }

    /**
     * Save Exclude Option
     *
     * @param $wp_statistics_options
     * @return mixed
     */
    public static function save_exclusion_option($wp_statistics_options)
    {

        // Save Exclude Role
        foreach (User::get_role_list() as $role) {
            $role_post                                                     = 'wps_exclude_' . str_replace(" ", "_", strtolower($role));
            $wp_statistics_options[self::input_name_to_option($role_post)] = (isset($_POST[$role_post]) ? $_POST[$role_post] : '');
        }

        // Save HoneyPot
        if (isset($_POST['wps_create_honeypot'])) {
            $my_post                      = array(
                'post_type'    => 'page',
                'post_title'   => __('WP Statistics Honey Pot Page', 'wp-statistics') . ' [' . TimeZone::getCurrentDate() . ']',
                'post_content' => __('This is the Honey Pot for WP Statistics to use, do not delete.', 'wp-statistics'),
                'post_status'  => 'publish',
                'post_author'  => 1,
            );
            $_POST['wps_honeypot_postid'] = wp_insert_post($my_post);
        }

        // Save Exclusion
        $wps_option_list = array(
            'wps_record_exclusions',
            'wps_robotlist',
            'wps_exclude_ip',
            'wps_exclude_loginpage',
            'wps_force_robot_update',
            'wps_excluded_countries',
            'wps_included_countries',
            'wps_excluded_hosts',
            'wps_robot_threshold',
            'wps_use_honeypot',
            'wps_honeypot_postid',
            'wps_exclude_feeds',
            'wps_excluded_urls',
            'wps_exclude_404s',
            'wps_corrupt_browser_info',
        );

        foreach ($wps_option_list as $option) {
            $wp_statistics_options[self::input_name_to_option($option)] = (isset($_POST[$option]) ? $_POST[$option] : '');
        }

        return $wp_statistics_options;
    }

    /**
     * Save Access Level Option
     *
     * @param $wp_statistics_options
     * @return mixed
     */
    public static function save_access_level_option($wp_statistics_options)
    {
        $wps_option_list = array('wps_read_capability', 'wps_manage_capability');
        foreach ($wps_option_list as $option) {
            $wp_statistics_options[self::input_name_to_option($option)] = (isset($_POST[$option]) ? $_POST[$option] : '');
        }

        return $wp_statistics_options;
    }

    /**
     * Save Visitor IP Option
     *
     * @param $wp_statistics_options
     * @return mixed
     */
    public static function save_visitor_ip_option($wp_statistics_options)
    {

        $value = IP::$default_ip_method;
        if (isset($_POST['ip_method']) and !empty($_POST['ip_method'])) {

            // Check Custom Header
            if ($_POST['ip_method'] == "CUSTOM_HEADER") {
                if (trim($_POST['user_custom_header_ip_method']) != "") {
                    $value = $_POST['user_custom_header_ip_method'];
                }
            } else {
                $value = $_POST['ip_method'];
            }
        }

        $wp_statistics_options['ip_method'] = $value;
        return $wp_statistics_options;
    }

    /**
     * Save General Options
     *
     * @param $wp_statistics_options
     * @return mixed
     */
    public static function save_general_option($wp_statistics_options)
    {

        $selist                       = SearchEngine::getList(true);
        $permalink                    = get_option('permalink_structure');
        $disable_strip_uri_parameters = false;

        if ($permalink == '' || strpos($permalink, '?') !== false) {
            $disable_strip_uri_parameters = true;
        }
        foreach ($selist as $se) {
            $se_post = 'wps_disable_se_' . $se['tag'];

            $wp_statistics_options[self::input_name_to_option($se_post)] = (isset($_POST[$se_post]) ? $_POST[$se_post] : '');
        }

        $wps_option_list = array(
            'wps_useronline',
            'wps_visits',
            'wps_visitors',
            'wps_visitors_log',
            'wps_enable_user_column',
            'wps_pages',
            'wps_track_all_pages',
            'wps_use_cache_plugin',
            'wps_disable_column',
            'wps_hit_post_metabox',
            'wps_show_hits',
            'wps_display_hits_position',
            'wps_check_online',
            'wps_menu_bar',
            'wps_coefficient',
            'wps_chart_totals',
            'wps_hide_notices',
            'wps_all_online',
            'wps_strip_uri_parameters',
            'wps_addsearchwords',
        );

        // We need to check the permalink format for the strip_uri_parameters option
        if ($disable_strip_uri_parameters) {
            $_POST['wps_strip_uri_parameters'] = '';
        }

        foreach ($wps_option_list as $option) {
            $wp_statistics_options[self::input_name_to_option($option)] = (isset($_POST[$option]) ? $_POST[$option] : '');
        }

        //Add Visitor RelationShip Table
        if (isset($_POST['wps_visitors_log']) and $_POST['wps_visitors_log'] == 1) {
            Install::create_visitor_relationship_table();
        }

        //Flush Rewrite Use Cache Plugin
        if (isset($_POST['wps_use_cache_plugin'])) {
            flush_rewrite_rules();
        }

        return $wp_statistics_options;
    }

    /**
     * Reset Wp-Statistics Option
     */
    public static function reset_wp_statistics_options()
    {

        if (isset($_POST['wps_reset_plugin'])) {

            if (is_multisite()) {
                $sites = Helper::get_wp_sites_list();
                foreach ($sites as $blog_id) {
                    switch_to_blog($blog_id);
                    self::reset_option();
                    restore_current_blog();
                }
            } else {
                self::reset_option();
            }

            wp_redirect(add_query_arg(array('reset_settings' => 'yes'), Menus::admin_url('settings')));
            exit;
        }
    }

    /**
     * Reset WP-Statistics Option
     */
    public static function reset_option()
    {
        global $wpdb;

        // Get Default Option
        $default_options = Option::defaultOption();

        // Delete the wp_statistics option.
        update_option(Option::$opt_name, array());

        // Delete the user options.
        $wpdb->query("DELETE FROM {$wpdb->prefix}usermeta WHERE meta_key LIKE 'wp_statistics%'");

        // Update Option
        update_option(Option::$opt_name, $default_options);
    }

    /**
     * Admin Notice
     */
    public function notice()
    {

        // Update Referrer Spam
        if (isset($_GET['update-referrer-spam'])) {
            $status = Referred::download_referrer_spam();
            if (is_bool($status)) {
                if ($status === false) {
                    Helper::wp_admin_notice(__("Error Updating Referrer Spam Blacklist.", "wp-statistics"), "error");
                } else {
                    Helper::wp_admin_notice(__("Updated Referrer Spam Blacklist.", "wp-statistics"), "success");
                }
                return;
            }
        }

        // Update GEO IP
        if (Option::get('geoip') and isset($_POST['update_geoip']) and isset($_POST['geoip_name'])) {

            //Check Geo ip Exist in Database
            if (isset(GeoIP::$library[$_POST['geoip_name']])) {
                $result = GeoIP::download($_POST['geoip_name'], "update");
                if (is_array($result) and isset($result['status'])) {
                    Helper::wp_admin_notice($result['notice'], ($result['status'] === false ? "error" : "success"));
                    return;
                }
            }
        }

        // Save Setting
        if (isset($_GET['save_setting'])) {
            Helper::wp_admin_notice(__("Saved Settings.", "wp-statistics"), "success");
        }

        // Reset Setting
        if (isset($_GET['reset_settings'])) {
            Helper::wp_admin_notice(__("All settings reset.", "wp-statistics"), "success");
        }

    }
}

new settings_page;