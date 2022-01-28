<?php

if ( ! defined('ABSPATH')) {
    die('You are not allowed to call this page directly.');
}

/**
 * Let's get started!
 */
class PPRESS_WP_User_Avatar_Setup
{
    public function __construct()
    {
        $this->_define_constants();
        $this->_load_wp_includes();
        $this->_load_wpua();
    }

    /**
     * Define paths
     */
    private function _define_constants()
    {
        define('WPUA_VERSION', PPRESS_VERSION_NUMBER);
        define('WPUA_DIR', plugin_dir_path(PROFILEPRESS_SYSTEM_FILE_PATH));
        define('WPUA_INC', WPUA_DIR . 'deprecated/wp-user-avatar/includes/');
        define('WPUA_URL', plugin_dir_url(PROFILEPRESS_SYSTEM_FILE_PATH) . 'deprecated/wp-user-avatar/');
        define('WPUA_INC_URL', WPUA_URL . 'includes' . '/');
    }

    /**
     * WordPress includes used in plugin
     */
    private function _load_wp_includes()
    {
        if ( ! is_admin()) {
            // wp_handle_upload
            require_once(ABSPATH . 'wp-admin/includes/file.php');
            // wp_generate_attachment_metadata
            require_once(ABSPATH . 'wp-admin/includes/image.php');
            // image_add_caption
            require_once(ABSPATH . 'wp-admin/includes/media.php');
            // submit_button
            require_once(ABSPATH . 'wp-admin/includes/template.php');
        }
        // add_screen_option
        require_once(ABSPATH . 'wp-admin/includes/screen.php');
    }

    /**
     * Load WP User Avatar
     */
    private function _load_wpua()
    {
        require_once(WPUA_INC . 'wpua-globals.php');
        require_once(WPUA_INC . 'wpua-functions.php');
        require_once(WPUA_INC . 'class-wp-user-avatar-admin.php');
        require_once(WPUA_INC . 'class-wp-user-avatar.php');
        require_once(WPUA_INC . 'class-wp-user-avatar-functions.php');
        require_once(WPUA_INC . 'class-wp-user-avatar-shortcode.php');
        require_once(WPUA_INC . 'class-wp-user-avatar-subscriber.php');
        require_once(WPUA_INC . 'class-wp-user-avatar-update.php');
        require_once(WPUA_INC . 'class-wp-user-avatar-widget.php');

        // Load TinyMCE only if enabled
        require_once(WPUA_INC . 'wpua-tinymce.php');

    }
}

/**
 * Initialize
 */
new PPRESS_WP_User_Avatar_Setup();