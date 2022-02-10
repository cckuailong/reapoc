<?php

namespace NotificationX;

/**
 * WPDeveloper Core Install
 */
class CoreInstaller {
    /**
     * Instance of CoreInstallers
     *
     * @var CoreInstaller
     */
    use GetInstance;

    /**
     * Plugin Base Name
     *
     * @var string
     */
    private $plugin_basename;

    /**
     * Instantiate the class
     *
     * @param string $affiliate
     */
    function __construct($plugin_basename = '') {
        $this->plugin_basename = $plugin_basename;
        add_action('init', array($this, 'init_hooks'));
    }

    /**
     * Initialize the hooks
     *
     * @return void
     */
    public function init_hooks() {
        if (!current_user_can('manage_options')) {
            return;
        }
        add_action('wp_ajax_wpdeveloper_upsale_core_install_' . $this->plugin_basename, array($this, 'core_install'));
    }

    /**
     * Fail if plugin installtion/activation fails
     *
     * @param  Object $thing
     *
     * @return void
     */
    public function fail_on_error($thing) {
        if (is_wp_error($thing)) {
            wp_send_json_error($thing->get_error_message());
        }
    }

    /**
     * Install Upsale Plugin
     *
     * @return void
     */
    public function core_install() {
        check_ajax_referer('wpdeveloper_upsale_core_install_' . $this->plugin_basename);
        if (!current_user_can('manage_options')) {
            wp_send_json_error(__('You don\'t have permission to install the plugins', 'notificationx'));
        }

        $this->raise_limits();

        $plugin_slug = (isset($_POST['slug'])) ? sanitize_text_field( $_POST['slug'] ) : '';
        $plugin_file = (isset($_POST['file'])) ? sanitize_file_name( $_POST['file'] ) : '';

        if (empty($plugin_file) || empty($plugin_slug)) {
            wp_send_json_error(__('You don\'t have set any slug and file name to install the plugins', 'notificationx'));
        }

        $plugin_status = $this->install_plugin($plugin_slug, $plugin_file);
        $this->fail_on_error($plugin_status);

        wp_send_json_success();
    }

    /**
     * Install and activate a plugin
     *
     * @param  string $slug
     * @param  string $file
     *
     * @return WP_Error|null
     */
    public function install_plugin($slug, $file) {
        include_once ABSPATH . 'wp-admin/includes/file.php';
        include_once ABSPATH . 'wp-admin/includes/plugin.php';
        include_once ABSPATH . 'wp-admin/includes/plugin-install.php';
        include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';

        $this->raise_limits();

        $plugin_basename = $slug . '/' . $file;

        // if exists and not activated
        if (file_exists(WP_PLUGIN_DIR . '/' . $plugin_basename)) {
            return \activate_plugin($plugin_basename);
        }

        // seems like the plugin doesn't exists. Download and activate it
        $upgrader = new \Plugin_Upgrader(new \WP_Ajax_Upgrader_Skin());

        $api      = plugins_api('plugin_information', array('slug' => $slug, 'fields' => array('sections' => false)));
        $result   = $upgrader->install($api->download_link);

        if (is_wp_error($result)) {
            return $result;
        }

        return activate_plugin($plugin_basename);
    }
    /**
     * Some process take long time to execute
     * for that need to raise the limit.
     */
    public function raise_limits() {
        wp_raise_memory_limit('admin');
        if (wp_is_ini_value_changeable('max_execution_time')) {
            ini_set('max_execution_time', 0);
        }
        @set_time_limit(0);
    }
}
