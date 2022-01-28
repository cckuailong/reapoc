<?php

namespace ProfilePress\Core\Admin\SettingsPages;

use ProfilePress\Custom_Settings_Page_Api;

class ToolsSettingsPage extends AbstractSettingsPage
{
    public function __construct()
    {
        add_action('admin_init', [$this, 'clear_error_log']);

        add_filter('ppress_general_settings_admin_page_title', function ($title) {
            if (isset($_GET['view']) && $_GET['view'] == 'tools') {
                $title = esc_html__('Tools', 'wp-user-avatar');
            }

            return $title;
        });
    }

    public function clear_error_log()
    {
        if ( ! isset($_GET['ppress-delete-log']) || ! current_user_can('manage_options') || ! ppress_verify_nonce()) return;

        if ( ! in_array($_GET['ppress-delete-log'], ['social-login', 'debug'])) return;

        ppress_clear_error_log($_GET['ppress-delete-log']);
        wp_safe_redirect(add_query_arg('view', 'tools', PPRESS_SETTINGS_SETTING_PAGE));
        exit;
    }

    public function admin_page()
    {
        $debug_log_content           = ppress_get_error_log();
        $delete_debug_log_url        = esc_url_raw(add_query_arg(['ppress-delete-log' => 'debug', '_wpnonce' => ppress_create_nonce()]));

        $settings = [
            'logs' => apply_filters('ppress_error_log_settings', [
                'tab_title' => esc_html__('Logs', 'wp-user-avatar'),
                'dashicon'  => '',
                [
                    'section_title'         => esc_html__('Debug Error Log', 'wp-user-avatar'),
                    'disable_submit_button' => true,
                    'debug_log_content'     => [
                        'type'        => 'arbitrary',
                        'data'        => sprintf(
                            '<textarea class="ppress-error-log-textarea" disabled>%s</textarea>',
                            $debug_log_content
                        ),
                        'description' => sprintf(
                            '<div style="margin-top: 10px"><a class="button pp-confirm-delete" href="%s">%s</a></div>', $delete_debug_log_url,
                            esc_html__('Delete Log', 'wp-user-avatar')
                        )
                    ]
                ]
            ])
        ];

        $instance = Custom_Settings_Page_Api::instance($settings, 'ppress_tools', esc_html__('Tools', 'wp-user-avatar'));
        $this->register_core_settings($instance, true);
        $instance->tab($this->settings_tab_args());
        $instance->build_sidebar_tab_style();
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