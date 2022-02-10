<?php

namespace OXI_IMAGE_HOVER_PLUGINS\Classes;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Description of Support_Recommended
 *
 * @author $biplob018
 */
class Support_Recommended {

    /**
     * Revoke this function when the object is created.
     *
     */
    const GET_LOCAL_PLUGINS = 'get_all_oxilab_plugins';
    const PLUGINS = 'https://www.oxilab.org/wp-json/oxilabplugins/v2/all_plugins';

    public $get_plugins = [];
    public $current_plugins = 'image-hover-effects-ultimate/index.php';

    public function __construct() {
        require_once(ABSPATH . 'wp-admin/includes/screen.php');
        $screen = get_current_screen();
        if (isset($screen->parent_file) && 'plugins.php' === $screen->parent_file && 'update' === $screen->id) {
            return;
        }
        $this->extension();
        add_action('admin_notices', array($this, 'first_install'));
        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
        add_action('admin_notices', array($this, 'dismiss_button_scripts'));
    }

    public function extension() {
        $response = get_transient(self::GET_LOCAL_PLUGINS);
        if (!$response || !is_array($response)) {
            $URL = self::PLUGINS;
            $request = wp_remote_request($URL);
            if (!is_wp_error($request)) {
                $response = json_decode(wp_remote_retrieve_body($request), true);
                set_transient(self::GET_LOCAL_PLUGINS, $response, 10 * DAY_IN_SECONDS);
            } else {
                $response = $request->get_error_message();
            }
        }
        $this->get_plugins = $response;
    }

    /**
     * First Installation Track
     * @return void
     */
    public function first_install() {
        $installed_plugins = get_plugins();

        $plugin = [];
        $i = 1;

        foreach ($this->get_plugins as $key => $value) {
            if (!isset($installed_plugins[$value['modules-path']])) :
                $plugin[$i] = $value;
                $i++;
            endif;
        }


        $recommend = [];

        for ($p = 1; $p < 100; $p++) :
            if (isset($plugin[$p]) && count($recommend) < 3) :
                if (isset($plugin[$p]['dependency']) && $plugin[$p]['dependency'] != '') :
                    if (isset($installed_plugins[$plugin[$p]['dependency']])) :
                        $recommend = $plugin[$p];
                        $p = 100;
                    endif;
                elseif ($plugin[$p]['modules-path'] != $this->current_plugins) :

                    $recommend = $plugin[$p];
                    $p = 100;
                endif;

            else :
                $p = 100;
            endif;
        endfor;

        if (count($recommend) > 2 && $recommend['modules-path'] != '') :
            $plugin = explode('/', $recommend['modules-path'])[0];
            $massage = '<p>Thank you for using our Image Hover Effects Ultimate. ' . $recommend['modules-massage'] . '</p>';

            $install_url = wp_nonce_url(add_query_arg(array('action' => 'install-plugin', 'plugin' => $plugin), admin_url('update.php')), 'install-plugin' . '_' . $plugin);
            echo '<div class="oxi-addons-admin-notifications" style=" width: auto;">
                        <h3>
                            <span class="dashicons dashicons-flag"></span>
                            Notifications
                        </h3>
                        <p></p>
                        <div class="oxi-addons-admin-notifications-holder">
                            <div class="oxi-addons-admin-notifications-alert">
                                ' . $massage . '
                                <p>' . sprintf('<a href="%s" class="button button-large button-primary">%s</a>', $install_url, __('Install Now', OXI_IMAGE_HOVER_TEXTDOMAIN)) . ' &nbsp;&nbsp;<a href="#" class="button button-large button-secondary oxi-image-admin-recommended-dismiss">No, Thanks</a></p>
                            </div>
                        </div>
                        <p></p>
                    </div>';
        endif;
    }

    /**
     * Admin Notice JS file loader
     * @return void
     */
    public function dismiss_button_scripts() {
        wp_enqueue_script('oxi-image-admin-recommended', OXI_IMAGE_HOVER_URL . '/assets/backend/js/admin-recommended.js', false, OXI_IMAGE_HOVER_PLUGIN_VERSION);
        wp_localize_script('oxi-image-admin-recommended', 'ImageHoverUltimate', array(
            'root' => esc_url_raw(rest_url()),
            'nonce' => wp_create_nonce('wp_rest')
        ));
    }

    /**
     * Admin Notice CSS file loader
     * @return void
     */
    public function admin_enqueue_scripts() {
        wp_enqueue_script("jquery");
        wp_enqueue_style('oxi-image-admin-notice-css', OXI_IMAGE_HOVER_URL . '/assets/backend/css/notice.css', false, OXI_IMAGE_HOVER_PLUGIN_VERSION);
        $this->dismiss_button_scripts();
    }

}
