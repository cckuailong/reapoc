<?php

namespace ProfilePress\Core\Admin\SettingsPages;

use ProfilePress\Core\Classes\ExtensionManager;
use ProfilePress\Custom_Settings_Page_Api;

if ( ! defined('ABSPATH')) {
    exit;
}

abstract class AbstractSettingsPage
{
    protected $option_name;

    public function init_menu()
    {
        add_action('admin_menu', array($this, 'register_core_menu'));
    }

    private function getMenuIcon()
    {
        return 'data:image/svg+xml;base64,' . base64_encode('<?xml version="1.0" encoding="UTF-8"?><!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1//EN" "http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd"><svg xmlns="http://www.w3.org/2000/svg" width="150" height="150" viewBox="0 0 11.71 11.71"  shape-rendering="geometricPrecision" image-rendering="optimizeQuality" fill-rule="evenodd" xmlns:v="https://vecta.io/nano"><path d="M5.85.2c3.13 0 5.66 2.53 5.66 5.65 0 3.13-2.53 5.66-5.66 5.66C2.73 11.51.2 8.98.2 5.85A5.65 5.65 0 0 1 5.85.2zM3.17 5.66l2.98-2.98c.24-.24.62-.24.86 0L8.53 4.2c.24.24.24.63 0 .87L6.12 7.48l.53.54a.64.64 0 0 1 0 .92l-.07.07a.64.64 0 0 1-.92 0l-.54-.53L3.44 6.8l-.27-.28c-.24-.23-.24-.62 0-.86zm3.21-1.22L4.93 5.89c-.12.11-.12.29 0 .4.11.11.28.11.39 0l1.46-1.45c.11-.11.11-.29 0-.4h0c-.11-.11-.29-.11-.4 0zM4.93 6.29h0z" fill="#a6aaad"/></svg>');
    }

    public function register_core_menu()
    {
        add_menu_page(
            esc_html__('ProfilePress - WordPress User Registration & Profile Plugin', 'wp-user-avatar'),
            'ProfilePress',
            'manage_options',
            PPRESS_SETTINGS_SLUG,
            '',
            $this->getMenuIcon(),
            '80.0015'
        );
    }

    /**
     * Register core settings.
     *
     * @param Custom_Settings_Page_Api $instance
     * @param bool $remove_sidebar
     */
    public function register_core_settings(Custom_Settings_Page_Api $instance, $remove_sidebar = false)
    {
        if ( ! $remove_sidebar) {
            $instance->sidebar($this->sidebar_args());
        }
    }

    public function settings_tab_args()
    {
        $tabs = apply_filters('ppress_settings_page_tabs', [
            20 => ['url' => PPRESS_SETTINGS_SETTING_PAGE, 'label' => esc_html__('General', 'wp-user-avatar')],
            40 => ['url' => add_query_arg('view', 'email', PPRESS_SETTINGS_SETTING_PAGE), 'label' => esc_html__('Emails', 'wp-user-avatar')],
            60 => ['url' => add_query_arg('view', 'tools', PPRESS_SETTINGS_SETTING_PAGE), 'label' => esc_html__('Tools', 'wp-user-avatar')],
        ]);

        if ( ! ExtensionManager::is_premium()) {
            $tabs[999] = ['url' => PPRESS_EXTENSIONS_SETTINGS_PAGE, 'label' => esc_html__('Premium Addons', 'wp-user-avatar')];
        }

        ksort($tabs);

        return $tabs;
    }

    public function sidebar_args()
    {
        $sidebar_args = [
            [
                'section_title' => esc_html__('Help / Support', 'wp-user-avatar'),
                'content'       => $this->sidebar_support_docs(),
            ],
            [
                'section_title' => esc_html__('Check out MailOptin', 'wp-user-avatar'),
                'content'       => $this->mailoptin_ad_block(),
            ]
        ];

        return $sidebar_args;
    }

    public function sidebar_support_docs()
    {
        $content = '<p>';
        $content .= sprintf(
            esc_html__('Visit the %s for guidance.', 'wp-user-avatar'),
            '<strong><a href="https://profilepress.net/docs/" target="_blank">' . esc_html__('Documentation', 'wp-user-avatar') . '</a></strong>'
        );

        $content .= '</p>';

        $content .= '<p>';
        $content .= sprintf(
            esc_html__('For support, %sreach out to us%s.', 'wp-user-avatar'),
            '<strong><a href="https://profilepress.net/support/" target="_blank">', '</a></strong>'
        );
        $content .= '</p>';

        return $content;
    }

    public function mailoptin_ad_block()
    {
        $content = '<p>';
        $content .= sprintf(
            esc_html__('Use the coupon code %s10PERCENTOFF%s to save %s off MailOptin.', 'wp-user-avatar'),
            '<code>', '</code>', '10%'
        );

        $content .= '</p>';

        $content .= '<a href="https://mailoptin.io/?utm_source=wp_dashboard&utm_medium=profilepress-admin-sidebar&utm_campaign=mailoptin" target="_blank"><img style="width: 100%" src="' . PPRESS_ASSETS_URL . '/images/admin/mo-pro-upgrade.jpg"></a>';

        return $content;
    }

    protected function placeholder_tags_table($placeholders)
    {
        ?>
        <div class="ppress-placeholder-tags">
            <table class="widefat striped">
                <tbody>
                <tr>
                    <th colspan="2"><?= esc_html__('Available placeholders for subject and message body', 'wp-user-avatar'); ?></th>
                </tr>
                <?php foreach ($placeholders as $tag => $description) : ?>
                    <tr>
                        <td><?= $tag ?></td>
                        <td><?= $description ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php
    }

    protected function page_dropdown($id, $appends = [], $args = [])
    {
        $html = wp_dropdown_pages(
            array_replace(
                [
                    'name'             => PPRESS_SETTINGS_DB_OPTION_NAME . "[$id]",
                    'show_option_none' => esc_html__('Select...', 'wp-user-avatar'),
                    'selected'         => ppress_get_setting($id, ''),
                    'echo'             => false
                ],
                $args
            )
        );

        if ( ! empty($appends)) {
            $addition = '';
            foreach ($appends as $append) {
                $key      = $append['key'];
                $label    = $append['label'];
                $addition .= "<option value=\"$key\"" . selected(ppress_get_setting($id), $key, false) . '>' . $label . '</option>';
            }

            $html = ppress_append_option_to_select($addition, $html);
        }

        return $html;
    }

    protected function custom_text_input($id, $placeholder = '')
    {
        $placeholder = ! empty($placeholder) ? $placeholder : esc_html__('Custom URL Here', 'wp-user-avatar');
        $value       = ppress_get_setting($id, '');

        return "<input placeholder=\"$placeholder\" name=\"" . PPRESS_SETTINGS_DB_OPTION_NAME . "[$id]\" type=\"text\" class=\"regular-text code\" value=\"$value\">";
    }
}