<?php

namespace ProfilePress\Core\Admin\SettingsPages\EmailSettings;

trait CustomizerTrait
{
    public function modify_customizer_publish_button()
    {
        add_filter('gettext', function ($translations, $text, $domain) {
            if ($domain == 'default' && $text == 'Publish') {
                $translations = __('Save Changes', 'wp-user-avatar');
            }
            if ($domain == 'default' && $text == 'Published') {
                $translations = __('Saved', 'wp-user-avatar');
            }

            return $translations;
        }, 10, 3);
    }

    public function clean_up_customizer()
    {
        add_action('customize_preview_init', function () {
            remove_all_actions('customize_preview_init');
        }, -1);

        // this should never change from init to say admin_init in future because it will
        // cause wp_enqueue_scripts filter from taking effect cos its used in frontend.
        add_action('init', function () {

            remove_all_actions('admin_print_footer_scripts');

            // remove all custom media button added by plugins and core.
            remove_all_actions('media_buttons');
            remove_all_filters('media_buttons_context');
            remove_all_filters('mce_buttons', 10);
            remove_all_filters('mce_external_plugins', 10);
            remove_all_actions('after_wp_tiny_mce');

            remove_all_actions('wp_head');
            remove_all_actions('wp_print_styles');
            remove_all_actions('wp_print_head_scripts');
            remove_all_actions('wp_footer');

            // Handle `wp_head`
            add_action('wp_head', 'wp_enqueue_scripts', 1);
            add_action('wp_head', 'wp_print_styles', 8);
            add_action('wp_head', 'wp_print_head_scripts', 9);
            add_action('wp_head', 'wp_site_icon');

            // Handle `wp_footer`
            add_action('wp_footer', 'wp_print_footer_scripts', 20);

            // add core media button back.
            add_action('media_buttons', 'media_buttons');

            if (class_exists('Astra_Customizer') && method_exists('Astra_Customizer', 'print_footer_scripts')) {
                remove_action('customize_controls_print_footer_scripts', [\Astra_Customizer::get_instance(), 'print_footer_scripts']);
            }

            add_action('customize_controls_enqueue_scripts', function () {
                global $wp_scripts;
                // important in fixing: Uncaught TypeError: Cannot set property '_value' of undefined
                // from /wp-admin/js/customize-nav-menus.min.js
                unset($wp_scripts->registered['customize-nav-menus']);
            });

            // flatbase theme compat
            add_filter('nice_scripts', '__return_false');

            if (function_exists('td_customize_js')) {
                remove_action('customize_controls_print_footer_scripts', 'td_customize_js');
            }

            // compatibility with easy google font plugin
            if (class_exists('EGF_Customize_Manager')) {
                remove_action('customize_controls_enqueue_scripts', [\EGF_Customize_Manager::get_instance(), 'easy-google-fonts-customize-controls-js']);
                remove_action('customize_register', [\EGF_Customize_Manager::get_instance(), 'register_font_control_type']);
            }

        }, 9999999999999);
    }
}