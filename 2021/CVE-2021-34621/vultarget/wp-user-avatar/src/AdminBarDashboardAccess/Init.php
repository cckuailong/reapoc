<?php

namespace ProfilePress\Core\AdminBarDashboardAccess;

use ProfilePress\Core\Admin\SettingsPages\AbstractSettingsPage;
use ProfilePress\Custom_Settings_Page_Api;

class Init extends AbstractSettingsPage
{
    public function __construct()
    {
        add_filter('show_admin_bar', [$this, 'admin_bar_control'], 9999999999999999999999999999999999999999);
        add_filter('admin_init', [$this, 'dashboard_access_control'], 9999999999999999999999999999999999999999);

        add_filter('ppress_settings_page_tabs', [$this, 'menu_tab']);

        add_filter('ppress_general_settings_admin_page_short_circuit', [$this, 'settings_page_callback']);
        add_filter('ppress_general_settings_admin_page_title', [$this, 'change_page_title']);
    }

    public function db_options()
    {
        return get_option('ppress_abdc_options', []);
    }

    public function menu_tab($tabs)
    {
        $tabs[50] = [
            'url'   => add_query_arg('view', 'admin-bar-dashboard', PPRESS_SETTINGS_SETTING_PAGE),
            'label' => esc_html__('Admin Bar & Dashboard', 'wp-user-avatar')
        ];

        return $tabs;
    }

    public function change_page_title($title)
    {
        if (isset($_GET['view']) && $_GET['view'] == 'admin-bar-dashboard') {
            $title = esc_html__('Admin Bar & Dashboard Access', 'wp-user-avatar');
        }

        return $title;
    }

    public function settings_page_callback($page)
    {
        if (isset($_GET['view']) && $_GET['view'] == 'admin-bar-dashboard') {

            // call to save the setting options
            self::save_options();

            add_filter('wp_cspa_main_content_area', function () {
                ob_start();
                require dirname(__FILE__) . '/include.settings-page.php';

                return ob_get_clean();
            });

            $instance = Custom_Settings_Page_Api::instance();
            $instance->option_name('ppress_abdc_options');
            $instance->page_header(esc_html__('Admin Bar & Dashboard Access', 'wp-user-avatar'));
            $this->register_core_settings($instance);
            $instance->tab($this->settings_tab_args());
            $instance->build();

            return true;
        }

        return $page;
    }


    public static function save_options()
    {
        if (isset($_POST['settings_submit'])) {

            check_admin_referer('ppress_abc_settings_nonce', '_wpnonce');

            if ( ! current_user_can('manage_options')) return;

            $saved_options = self::sanitize_data($_POST['ppress_abdc_options']);

            update_option('ppress_abdc_options', $saved_options);

            wp_safe_redirect(add_query_arg(['view' => 'admin-bar-dashboard', 'settings-updated' => 'true'], PPRESS_SETTINGS_SETTING_PAGE));
            exit;
        }
    }

    /**
     * Helper function to recursively sanitize POSTed data.
     *
     * @param $data
     *
     * @return string|array
     */
    public static function sanitize_data($data)
    {
        if (is_string($data)) return sanitize_text_field($data);
        $sanitized_data = array();
        foreach ($data as $key => $value) {
            if (is_array($data[$key])) {
                $sanitized_data[$key] = self::sanitize_data($data[$key]);
            } else {
                $sanitized_data[$key] = sanitize_text_field($data[$key]);
            }
        }

        return $sanitized_data;
    }

    /**
     * Callback to disable admin bar.
     *
     * @return bool
     */
    public function admin_bar_control()
    {
        $current_user       = wp_get_current_user();
        $current_user_roles = $current_user->roles;

        $is_admin_bar_disabled   = ppress_var($this->db_options(), 'disable_admin_bar', '', true);
        $disable_admin_bar_roles = ppress_var($this->db_options(), 'disable_admin_bar_roles', [], true);

        // get current user's admin_bar_front preference
        // if value is true, $user_option will has a boolen true value or false otherwise.
        $user_option = get_user_option('show_admin_bar_front', $current_user->ID) == 'true';

        // bail if the disable admin bar checkbox isn't checked.
        if ($is_admin_bar_disabled != 'yes') return $user_option;

        if (is_super_admin($current_user->ID)) return $user_option;

        // if no role is selected, disable for everyone by return false.
        if (empty($disable_admin_bar_roles)) return false;

        $intersect = array_intersect($current_user_roles, $disable_admin_bar_roles);

        return empty($intersect);
    }

    /**
     * Disable dashboard access.
     *
     * @return bool|void
     */
    public function dashboard_access_control()
    {
        $current_user       = wp_get_current_user();
        $current_user_roles = $current_user->roles;

        $is_dashboard_access_disabled   = ppress_var($this->db_options(), 'disable_dashboard_access', '', true);
        $disable_dashboard_access_roles = ppress_var($this->db_options(), 'disable_dashboard_access_roles', [], true);

        if (defined('DOING_AJAX') && DOING_AJAX) return;

        if (basename(sanitize_text_field(wp_unslash($_SERVER['SCRIPT_FILENAME']))) == 'admin-post.php') return;

        if ($is_dashboard_access_disabled != 'yes') return;

        if (is_super_admin($current_user->ID)) return;

        // if no role is selected, disable for everyone by return false.
        if (empty($disable_dashboard_access_roles)) return $this->disable_dashboard_access();

        $intersect = array_intersect($current_user_roles, $disable_dashboard_access_roles);

        if ( ! empty($intersect)) $this->disable_dashboard_access();
    }

    /**
     * Call to disable dashboard access.
     */
    public function disable_dashboard_access()
    {
        $dashboard_redirect_url = ppress_var($this->db_options(), 'dashboard_redirect_url', home_url(), true);

        if (is_admin()) {
            wp_safe_redirect(esc_url_raw($dashboard_redirect_url));
            exit;
        }
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