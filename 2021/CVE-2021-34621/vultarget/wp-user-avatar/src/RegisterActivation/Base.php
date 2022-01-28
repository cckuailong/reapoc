<?php

namespace ProfilePress\Core\RegisterActivation;

use ProfilePress\Core\Classes\FormRepository as FR;

class Base
{
    public static function run_install($networkwide = false)
    {
        if (is_multisite() && $networkwide) {

            $site_ids = get_sites(['fields' => 'ids', 'number' => 0]);

            foreach ($site_ids as $site_id) {
                switch_to_blog($site_id);
                self::pp_install();
                restore_current_blog();
            }
        } else {
            self::pp_install();
        }

        flush_rewrite_rules();
    }

    /**
     * Run plugin install / activation action when new blog is created in multisite setup.
     *
     * @param int $blog_id
     */
    public static function multisite_new_blog_install($blog_id)
    {
        if ( ! function_exists('is_plugin_active_for_network')) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }

        if (is_plugin_active_for_network('wp-user-avatar/wp-user-avatar.php')) {
            switch_to_blog($blog_id);
            self::pp_install();
            restore_current_blog();
        }
    }

    /**
     * Perform plugin activation / installation.
     */
    public static function pp_install()
    {
        // set flag for those migrating from wp user avatar
        if ( ! get_option('ppress_plugin_activated') && '1' == get_option('wp_user_avatar_default_avatar_updated')) {
            add_option('ppress_is_from_wp_user_avatar', 'true');
        }

        if ( ! current_user_can('activate_plugins') || get_option('ppress_plugin_activated') == 'true') return;

        CreateDBTables::make();
        self::default_settings();
        self::create_default_forms();

        add_option('ppress_install_date', current_time('mysql'));
        add_option('ppress_plugin_activated', 'true');

        flush_rewrite_rules();
    }

    public static function default_settings()
    {
        $settings = [
            'login_username_email_restrict'    => 'both',
            'myac_edit_account_endpoint'       => 'edit-profile',
            'myac_change_password_endpoint'    => 'change-password',
            'set_user_profile_slug'            => 'profile',
            'set_login_redirect'               => 'dashboard',
            'global_site_access'               => 'everyone',
            'global_restricted_access_message' => '<p>You are unauthorized to view this page.</p>',

            'admin_email_addresses' => ppress_admin_email(),
            'email_sender_name'     => ppress_site_title(),
            'email_sender_email'    => 'wordpress@' . ppress_site_url_without_scheme(),
            'email_content_type'    => 'text/html',
            'email_template_type'   => 'default',

            'password_reset_email_enabled' => 'on',
            'password_reset_email_subject' => sprintf(__('[%s] Password Reset'), ppress_site_title()),
            'password_reset_email_content' => ppress_password_reset_content_default(),

            'new_user_admin_email_email_enabled' => 'on',
            'new_user_admin_email_email_subject' => sprintf(__('[%s] New User Registration'), ppress_site_title()),
            'new_user_admin_email_email_content' => ppress_new_user_admin_notification_message_default(),
        ];

        foreach ($settings as $key => $value) {
            ppress_update_settings($key, $value);
        }
    }

    public static function create_default_forms()
    {

        FR::add_form(
            esc_html__('Default Registration', 'wp-user-avatar'),
            FR::REGISTRATION_TYPE,
            'Tulip',
            FR::DRAG_DROP_BUILDER_TYPE
        );

        FR::add_form(
            esc_html__('Default Login', 'wp-user-avatar'),
            FR::LOGIN_TYPE,
            'Tulip',
            FR::DRAG_DROP_BUILDER_TYPE
        );

        FR::add_form(
            esc_html__('Default Password Reset', 'wp-user-avatar'),
            FR::PASSWORD_RESET_TYPE,
            'Tulip',
            FR::DRAG_DROP_BUILDER_TYPE
        );

        FR::add_form(
            esc_html__('Default Edit Profile', 'wp-user-avatar'),
            FR::EDIT_PROFILE_TYPE,
            'Tulip',
            FR::DRAG_DROP_BUILDER_TYPE
        );

        FR::add_form(
            esc_html__('Default User Profile', 'wp-user-avatar'),
            FR::USER_PROFILE_TYPE,
            'DefaultTemplate',
            FR::DRAG_DROP_BUILDER_TYPE
        );

        FR::add_form(
            esc_html__('Default Member Directory', 'wp-user-avatar'),
            FR::MEMBERS_DIRECTORY_TYPE,
            'DefaultTemplate',
            FR::DRAG_DROP_BUILDER_TYPE
        );

        FR::add_form(
            esc_html__('Lucid Tab Widget', 'wp-user-avatar'),
            FR::MELANGE_TYPE,
            'Lucid',
            FR::SHORTCODE_BUILDER_TYPE
        );
    }

    public static function create_pages()
    {
        $pages = [
            'set_login_url'              => [
                'post_title'   => esc_html__('Log In', 'wp-user-avatar'),
                'post_content' => sprintf('[profilepress-login id="%s"]', FR::get_form_first_id(FR::LOGIN_TYPE)),
            ],
            'set_registration_url'       => [
                'post_title'   => esc_html__('Sign Up', 'wp-user-avatar'),
                'post_content' => sprintf('[profilepress-registration id="%s"]', FR::get_form_first_id(FR::REGISTRATION_TYPE)),
            ],
            'set_lost_password_url'      => [
                'post_title'   => esc_html__('Reset Password', 'wp-user-avatar'),
                'post_content' => sprintf('[profilepress-password-reset id="%s"]', FR::get_form_first_id(FR::PASSWORD_RESET_TYPE)),
            ],
            'edit_user_profile_url'      => [
                'post_title'   => esc_html__('My Account', 'wp-user-avatar'),
                'post_content' => '[profilepress-my-account]',
                'post_name'    => 'account'
            ],
            'set_user_profile_shortcode' => [
                'post_title'   => esc_html__('My Profile', 'wp-user-avatar'),
                'post_content' => sprintf('[profilepress-user-profile id="%s"]', FR::get_form_first_id(FR::USER_PROFILE_TYPE))
            ],
            'member_directory'           => [
                'post_title'   => esc_html__('Member Directory', 'wp-user-avatar'),
                'post_content' => sprintf('[profilepress-member-directory id="%s"]', FR::get_form_first_id(FR::MEMBERS_DIRECTORY_TYPE))
            ]
        ];

        foreach ($pages as $key => $page) {

            $insert = wp_insert_post(
                array_merge(
                    ['post_status' => 'publish', 'post_type' => 'page'],
                    $page
                ),
                true
            );

            if ($insert && ! is_wp_error($insert)) {
                ppress_update_settings($key, $insert);
            }
        }
    }

}