<?php
/**
 * Plugin Name: WP Booking System
 * Plugin URI: https://www.wpbookingsystem.com/
 * Description: A set-and-forget booking calendar for your rental business.
 * Version: 2.0.0
 * Author: Veribo, Roland Murg
 * Author URI: https://www.wpbookingsystem.com/
 * Text Domain: wp-booking-system
 * License: GPL2
 *
 * == Copyright ==
 * Copyright 2019 WP Booking System (www.wpbookingsystem.com)
 *
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Main plugin class
 *
 */
class WP_Booking_System
{

    /**
     * The current instance of the object
     *
     * @access private
     * @var    WP_Booking_System
     *
     */
    private static $instance;

    /**
     * A list with the objects that handle database requests
     *
     * @access public
     * @var    array
     *
     */
    public $db = array();

    /**
     * A list with the objects that handle submenu pages
     *
     * @access public
     * @var    array
     *
     */
    public $submenu_pages = array();

    /**
     * Constructor
     *
     */
    public function __construct()
    {

        // Defining constants
        define('WPBS_VERSION', '2.0.0');
        define('WPBS_FILE', __FILE__);
        define('WPBS_BASENAME', plugin_basename(__FILE__));
        define('WPBS_PLUGIN_DIR', plugin_dir_path(__FILE__));
        define('WPBS_PLUGIN_DIR_URL', plugin_dir_url(__FILE__));

        $this->include_files();
        $this->load_db_layer();

        define('WPBS_TRANSLATION_DIR', WPBS_PLUGIN_DIR . '/translations');
        define('WPBS_TRANSLATION_TEXTDOMAIN', 'wp-booking-system');

        // Check if just updated
        add_action('plugins_loaded', array($this, 'update_check'), 20);

        // Load the textdomain and the translation folders
        add_action('plugins_loaded', array($this, 'load_text_domain'), 30);

        // Update the database tables
        add_action('wpbs_update_check', array($this, 'update_database_tables'));

        // Add and remove main plugin page
        add_action('admin_menu', array($this, 'add_main_menu_page'), 10);
        add_action('admin_menu', array($this, 'remove_main_menu_page'), 11);

        // Add submenu pages
        add_action('wp_loaded', array($this, 'load_admin_submenu_pages'), 11);

        // Admin scripts
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));

        // Front-end scripts
        add_action('wp_enqueue_scripts', array($this, 'enqueue_front_end_scripts'));

        // Remove plugin query args from the URL
        add_filter('removable_query_args', array($this, 'removable_query_args'));

        // Add a 5 star review call to action to admin footer text
        add_filter('admin_footer_text', array($this, 'admin_footer_text'));

        register_activation_hook(__FILE__, array($this, 'set_cron_jobs'));
        register_deactivation_hook(__FILE__, array($this, 'unset_cron_jobs'));

        /**
         * Plugin initialized
         *
         */
        do_action('wpbs_initialized');

    }

    /**
     * Returns an instance of the plugin object
     *
     * @return WP_Booking_System
     *
     */
    public static function instance()
    {

        if (!isset(self::$instance) && !(self::$instance instanceof WP_Booking_System)) {
            self::$instance = new WP_Booking_System;
        }

        return self::$instance;

    }

    /**
     * Add the main menu page
     *
     */
    public function add_main_menu_page()
    {

        add_menu_page('WP Booking System', 'WP Booking<br /> System', apply_filters('wpbs_menu_page_capability', 'manage_options'), 'wp-booking-system', '', 'dashicons-calendar-alt');

    }

    /**
     * Remove the main menu page as we will rely only on submenu pages
     *
     */
    public function remove_main_menu_page()
    {

        remove_submenu_page('wp-booking-system', 'wp-booking-system');

    }

    /**
     * Checks to see if the current version of the plugin matches the version
     * saved in the database
     *
     * @return void
     *
     */
    public function update_check()
    {

        $db_version = get_option('wpbs_version', '');
        $do_update = false;

        // If current version number differs from saved version number
        if ($db_version != WPBS_VERSION) {

            $do_update = true;

            // Update the version number in the db
            update_option('wpbs_version', WPBS_VERSION);

            // Add first activation time
            if (get_option('wpbs_first_activation', '') == '') {
                update_option('wpbs_first_activation', time());
            }

        }

        if ($do_update) {

            // Hook for fresh update
            do_action('wpbs_update_check', $db_version);

            // Trigger set cron jobs
            $this->set_cron_jobs();

        }

    }

    /**
     * Creates and updates the database tables
     *
     * @return void
     *
     */
    public function update_database_tables()
    {

        foreach ($this->db as $db_class) {

            $db_class->create_table();

        }

    }

    /**
     * Loads plugin text domain
     *
     */
    public function load_text_domain()
    {

        $current_theme = wp_get_theme();

        if (!empty($current_theme->stylesheet) && file_exists(get_theme_root() . '/' . $current_theme->stylesheet . '/wpbs-translations')) {
            load_plugin_textdomain(WPBS_TRANSLATION_TEXTDOMAIN, false, plugin_basename(dirname(__FILE__)) . '/../../themes/' . $current_theme->stylesheet . '/wpbs-translations');
        } else {
            load_plugin_textdomain(WPBS_TRANSLATION_TEXTDOMAIN, false, plugin_basename(dirname(__FILE__)) . '/translations');
        }

    }

    /**
     * Sets an action hook for modules to add custom schedules
     *
     */
    public function set_cron_jobs()
    {

        do_action('wpbs_set_cron_jobs');

    }

    /**
     * Sets an action hook for modules to remove custom schedules
     *
     */
    public function unset_cron_jobs()
    {

        do_action('wpbs_unset_cron_jobs');

    }

    /**
     * Include files
     *
     * @return void
     *
     */
    public function include_files()
    {

        /**
         * Include abstract classes
         *
         */
        $abstracts = scandir(WPBS_PLUGIN_DIR . 'includes/abstracts');

        foreach ($abstracts as $abstract) {

            if (false === strpos($abstract, '.php')) {
                continue;
            }

            include WPBS_PLUGIN_DIR . 'includes/abstracts/' . $abstract;

        }

        /**
         * Include all functions.php files from all plugin folders
         *
         */
        $this->_recursively_include_files(WPBS_PLUGIN_DIR . 'includes');

        /**
         * Helper hook to include files early
         *
         */
        do_action('wpbs_include_files');

    }

    /**
     * Recursively includes all functions.php files from the given directory path
     *
     * @param string $dir_path
     *
     */
    protected function _recursively_include_files($dir_path)
    {

        $folders = array_filter(glob($dir_path . '/*'), 'is_dir');

        foreach ($folders as $folder_path) {

            if (file_exists($folder_path . '/functions.php')) {
                include $folder_path . '/functions.php';
            }

            $this->_recursively_include_files($folder_path);

        }

    }

    /**
     * Sets up all objects that handle database related requests and adds them to the
     * $db property of the app
     *
     */
    public function load_db_layer()
    {

        /**
         * Hook to register db class handlers
         * The array element should be 'class_slug' => 'class_name'
         *
         * @param array
         *
         */
        $db_classes = apply_filters('wpbs_register_database_classes', array());

        if (empty($db_classes)) {
            return;
        }

        foreach ($db_classes as $db_class_slug => $db_class_name) {

            $this->db[$db_class_slug] = new $db_class_name;

        }

    }

    /**
     * Sets up all objects that handle submenu pages and adds them to the
     * $submenu_pages property of the app
     *
     */
    public function load_admin_submenu_pages()
    {

        /**
         * Hook to register submenu_pages class handlers
         * The array element should be 'submenu_page_slug' => array( 'class_name' => array(), 'data' => array() )
         *
         * @param array
         *
         */
        $submenu_pages = apply_filters('wpbs_register_submenu_page', array());

        if (empty($submenu_pages)) {
            return;
        }

        foreach ($submenu_pages as $submenu_page_slug => $submenu_page) {

            if (empty($submenu_page['data'])) {
                continue;
            }

            if (empty($submenu_page['data']['page_title']) || empty($submenu_page['data']['menu_title']) || empty($submenu_page['data']['capability']) || empty($submenu_page['data']['menu_slug'])) {
                continue;
            }

            $this->submenu_pages[$submenu_page['data']['menu_slug']] = new $submenu_page['class_name']($submenu_page['data']['page_title'], $submenu_page['data']['menu_title'], $submenu_page['data']['capability'], $submenu_page['data']['menu_slug']);

        }

    }

    /**
     * Enqueue the scripts and style for the admin area
     *
     */
    public function enqueue_admin_scripts($hook)
    {
        if (strpos($hook, 'wpbs') !== false || strpos($hook, 'widgets') !== false || in_array(get_post_type(), array('post', 'page'))) {

            if (!wp_script_is('chosen')) {

                wp_enqueue_script('wpbs-chosen', WPBS_PLUGIN_DIR_URL . 'assets/libs/chosen/chosen.jquery.min.js', array('jquery'), WPBS_VERSION);
                wp_enqueue_style('wpbs-chosen', WPBS_PLUGIN_DIR_URL . 'assets/libs/chosen/chosen.css', array(), WPBS_VERSION);

            }

        }

        if (strpos($hook, 'wpbs') !== false) {

            $settings = get_option('wpbs_settings', array());

            // Edit calendar scripts
            wp_register_script('wpbs-script-edit-calendar', WPBS_PLUGIN_DIR_URL . 'assets/js/script-admin-edit-calendar.js', array('jquery', 'jquery-ui-sortable', 'wp-color-picker', 'jquery-ui-datepicker'), WPBS_VERSION);
            wp_localize_script('wpbs-script-edit-calendar', 'wpbs_plugin_settings', $settings);
            wp_enqueue_script('wpbs-script-edit-calendar');

            // Edit form scripts
            wp_register_script('wpbs-script-edit-form', WPBS_PLUGIN_DIR_URL . 'assets/js/script-admin-edit-form.js', array('jquery', 'jquery-ui-sortable', 'wp-color-picker', 'jquery-ui-datepicker'), WPBS_VERSION);
            wp_enqueue_script('wpbs-script-edit-form');
            wp_localize_script('wpbs-script-edit-form', 'wpbs_localized_data', array('wpbs_plugins_dir_url' => WPBS_PLUGIN_DIR_URL));

            // Edit booking scripts
            wp_register_script('wpbs-script-edit-booking', WPBS_PLUGIN_DIR_URL . 'assets/js/script-admin-edit-booking.js', array('jquery', 'jquery-ui-sortable', 'wp-color-picker', 'jquery-ui-datepicker'), WPBS_VERSION);
            wp_enqueue_script('wpbs-script-edit-booking');
            wp_localize_script('wpbs-script-edit-booking', 'wpbs_localized_data_booking', array(
                'wpbs_plugins_dir_url' => WPBS_PLUGIN_DIR_URL,
                'open_bookings_token' => wp_create_nonce('wpbs_open_booking_details'),
                'email_customer_token' => wp_create_nonce('wpbs_booking_email_customer'),
            ));

            // Color picker
            wp_enqueue_style('jquery-style', WPBS_PLUGIN_DIR_URL . 'assets/css/jquery-ui.css', array(), WPBS_VERSION);
            wp_enqueue_style('wp-color-picker');

            // Datepicker Custom
            wp_enqueue_style('wpbs-datepicker', WPBS_PLUGIN_DIR_URL . 'assets/css/datepicker.css', array(), WPBS_VERSION);

        }

        if (!empty($_GET['page']) && $_GET['page'] == 'wpbs-upgrader') {

            wp_register_script('wpbs-script-upgrader', WPBS_PLUGIN_DIR_URL . 'assets/js/script-admin-upgrader.js', array('jquery'), WPBS_VERSION);
            wp_enqueue_script('wpbs-script-upgrader');

        }

        if (!empty($_GET['page']) && $_GET['page'] == 'wpbs-settings') {

            wp_register_script('wpbs-script-uninstaller', WPBS_PLUGIN_DIR_URL . 'assets/js/script-admin-uninstaller.js', array('jquery'), WPBS_VERSION);
            wp_enqueue_script('wpbs-script-uninstaller');

        }

        // Plugin styles
        wp_register_style('wpbs-admin-style', WPBS_PLUGIN_DIR_URL . 'assets/css/style-admin.css', array(), WPBS_VERSION);
        wp_enqueue_style('wpbs-admin-style');

        // Plugin script
        wp_register_script('wpbs-admin-script', WPBS_PLUGIN_DIR_URL . 'assets/js/script-admin.js', array('jquery', 'jquery-ui-sortable', 'wp-color-picker', 'jquery-ui-datepicker'), WPBS_VERSION);
        wp_enqueue_script('wpbs-admin-script');

        // Plugin styles from the front-end. Needed for the actual calendar
        wp_register_style('wpbs-front-end-style', WPBS_PLUGIN_DIR_URL . 'assets/css/style-front-end.css', array(), WPBS_VERSION);
        wp_enqueue_style('wpbs-front-end-style');

        // Icon Font
        wp_register_style('wpbs-icons-font', WPBS_PLUGIN_DIR_URL . 'assets/css/icons-font.css', array(), WPBS_VERSION);
        wp_enqueue_style('wpbs-icons-font');

        /**
         * Hook to enqueue scripts immediately after the plugin's scripts
         *
         */
        do_action('wpbs_enqueue_admin_scripts');

    }

    /**
     * Enqueue the scripts and style for the front-end part
     *
     */
    public function enqueue_front_end_scripts()
    {

        $settings = get_option('wpbs_settings', array());

        // Plugin styles
        wp_register_style('wpbs-style', WPBS_PLUGIN_DIR_URL . 'assets/css/style-front-end.css', array(), WPBS_VERSION);
        wp_enqueue_style('wpbs-style');

        // Plugin styles
        if (!isset($settings['form_styling']) || $settings['form_styling'] == 'default') {
            wp_register_style('wpbs-style-form', WPBS_PLUGIN_DIR_URL . 'assets/css/style-front-end-form.css', array(), WPBS_VERSION);
            wp_enqueue_style('wpbs-style-form');
        }

        // Plugin script
        wp_register_script('wpbs-script', WPBS_PLUGIN_DIR_URL . 'assets/js/script-front-end.js', array('jquery'), WPBS_VERSION, true);
        wp_localize_script('wpbs-script', 'wpbs_ajax', array(
            'token' => wp_create_nonce('wpbs_form_ajax'),
            'plugin_settings' => $settings,
        ));

        wp_enqueue_script('wpbs-script');

        // Add ajax_url into the front-end
        $ajax_url = "
            var wpbs_ajaxurl = '" . admin_url('admin-ajax.php') . "';
        ";

        wp_add_inline_script('wpbs-script', $ajax_url, 'before');

        // Google reCaptcha V2
        if (isset($settings['recaptcha_v2_site_key']) && !empty($settings['recaptcha_v2_site_key'])) {
            wp_register_script('google-recaptcha', 'https://www.google.com/recaptcha/api.js', array(), WPBS_VERSION, false);
            wp_enqueue_script('google-recaptcha');
        }

        /**
         * Hook to enqueue scripts immediately after the plugin's scripts
         *
         */
        do_action('wpbs_enqueue_front_end_scripts');

    }

    /**
     * Removes the query variables from the URL upon page load
     *
     */
    public function removable_query_args($args = array())
    {

        $args[] = 'wpbs_message';

        return $args;

    }

    /**
     * Replace admin footer text with a rate plugin message
     *
     * @param string $text
     *
     */
    public function admin_footer_text($text)
    {

        if (isset($_GET['page']) && strpos($_GET['page'], 'wpbs') !== false) {
            return sprintf(__('If you enjoy using <strong>WP Booking System</strong>, please <a href="%s" target="_blank">leave us a ★★★★★ rating</a>. Big thank you for this!', 'wp-booking-system'), 'https://wordpress.org/support/plugin/wp-booking-system/reviews/?rate=5#new-post');
        }

        return $text;

    }

}

/**
 * Returns the WP Booking System instanced object
 *
 */
function wp_booking_system()
{

    return WP_Booking_System::instance();

}

// Let's get the party started
wp_booking_system();
