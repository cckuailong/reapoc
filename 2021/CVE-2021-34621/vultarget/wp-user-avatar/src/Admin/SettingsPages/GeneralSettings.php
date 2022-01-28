<?php

namespace ProfilePress\Core\Admin\SettingsPages;

use ProfilePress\Core\Admin\SettingsPages\EmailSettings\EmailSettingsPage;
use ProfilePress\Core\Classes\FormRepository;
use ProfilePress\Custom_Settings_Page_Api;

class GeneralSettings extends AbstractSettingsPage
{
    public function __construct()
    {
        $this->init_menu();
        add_action('admin_menu', array($this, 'register_settings_page'));

        // flush rewrite rule on save/persistence
        add_action('wp_cspa_persist_settings', function () {
            flush_rewrite_rules();
        });
    }

    public function register_settings_page()
    {
        $hook = add_submenu_page(
            PPRESS_SETTINGS_SLUG,
            apply_filters('ppress_general_settings_admin_page_title', esc_html__('Settings', 'wp-user-avatar')) . ' - ProfilePress',
            esc_html__('Settings', 'wp-user-avatar'),
            'manage_options',
            PPRESS_SETTINGS_SLUG,
            array($this, 'settings_admin_page_callback')
        );

        add_action("load-$hook", [$this, 'screen_option']);
    }

    public function screen_option()
    {
        do_action('ppress_settings_page_screen_option');
    }

    public function settings_admin_page_callback()
    {
        if (isset($_GET['view']) && $_GET['view'] == 'email') {
            return EmailSettingsPage::get_instance()->admin_page();
        }

        if (isset($_GET['view']) && $_GET['view'] == 'tools') {
            return ToolsSettingsPage::get_instance()->admin_page();
        }

        $custom_page = apply_filters('ppress_general_settings_admin_page_short_circuit', false);

        if (false !== $custom_page) return $custom_page;

        $edit_profile_forms = array_reduce(FormRepository::get_forms(FormRepository::EDIT_PROFILE_TYPE),
            function ($carry, $item) {
                $carry[$item['form_id']] = $item['name'];

                return $carry;
            }, ['default' => esc_html__('My Account edit profile form (default)', 'wp-user-avatar')]);

        $args = [
            'global_settings'           => apply_filters('ppress_global_settings_page', [
                'tab_title' => esc_html__('Global', 'wp-user-avatar'),
                'dashicon'  => 'dashicons-admin-site-alt',
                [
                    'section_title'         => esc_html__('Global Settings', 'wp-user-avatar'),
                    'set_lost_password_url' => [
                        'type'        => 'custom_field_block',
                        'label'       => esc_html__('Password-reset Page', 'wp-user-avatar'),
                        'data'        => $this->page_dropdown('set_lost_password_url'),
                        'description' => sprintf(
                            esc_html__('Select the page you wish to make WordPress default "Lost Password page". %3$s This should be the page that contains a %1$spassword reset form shortcode%2$s.', 'wp-user-avatar'),
                            '<a href="' . add_query_arg('form-type', 'password-reset', PPRESS_FORMS_SETTINGS_PAGE) . '"><strong>', '</strong></a>', '<br/>'),
                    ],
                    'set_login_url'         => [
                        'type'        => 'custom_field_block',
                        'label'       => esc_html__('Login Page', 'wp-user-avatar'),
                        'data'        => $this->page_dropdown('set_login_url'),
                        'description' => sprintf(
                            esc_html__('Select the page you wish to make WordPress default Login page. %3$s This should be the page that contains a %1$slogin form shortcode%2$s.', 'wp-user-avatar'),
                            '<a href="' . add_query_arg('form-type', 'login', PPRESS_FORMS_SETTINGS_PAGE) . '"><strong>', '</strong></a>', '<br/>'),
                    ],
                    'set_registration_url'  => [
                        'type'        => 'custom_field_block',
                        'label'       => esc_html__('Registration Page', 'wp-user-avatar'),
                        'data'        => $this->page_dropdown('set_registration_url'),
                        'description' => sprintf(
                            esc_html__('Select the page you wish to make WordPress default Registration page. %3$s This should be the page that contains a %1$sregistration form shortcode%2$s.', 'wp-user-avatar'),
                            '<a href="' . add_query_arg('form-type', 'registration', PPRESS_FORMS_SETTINGS_PAGE) . '"><strong>', '</strong></a>', '<br/>'),
                    ],
                    'edit_user_profile_url' => [
                        'type'        => 'custom_field_block',
                        'label'       => esc_html__('My Account Page', 'wp-user-avatar'),
                        'data'        => $this->page_dropdown('edit_user_profile_url'),
                        'description' => sprintf(
                            esc_html__('Select a page that contains %3$s shortcode. You can also use an %1$sedit profile shortcode%2$s on the My Account page in case you want something custom.', 'wp-user-avatar'),
                            '<a href="' . add_query_arg('form-type', 'edit-profile', PPRESS_FORMS_SETTINGS_PAGE) . '"><strong>', '</strong></a>', '<code>[profilepress-my-account]</code>'),
                    ],
                    'disable_ajax_mode'     => [
                        'type'           => 'checkbox',
                        'label'          => esc_html__('Disable Ajax Mode', 'wp-user-avatar'),
                        'value'          => 'yes',
                        'checkbox_label' => esc_html__('Disable', 'wp-user-avatar'),
                        'description'    => esc_html__('Check this box to disable ajax behaviour(whereby forms do not require page reload when submitted) in forms.', 'wp-user-avatar'),
                    ],
                    'remove_plugin_data'    => [
                        'type'           => 'checkbox',
                        'value'          => 'yes',
                        'label'          => esc_html__('Remove Data on Uninstall?', 'wp-user-avatar'),
                        'checkbox_label' => esc_html__('Delete', 'wp-user-avatar'),
                        'description'    => esc_html__('Check this box if you would like ProfilePress to completely remove all of its data when the plugin is deleted.', 'wp-user-avatar'),
                    ]
                ]
            ]),
            'registration_settings'     => apply_filters('ppress_registration_settings_page', [
                'tab_title' => esc_html__('Registration', 'wp-user-avatar'),
                'dashicon'  => 'dashicons-welcome-learn-more',
                [
                    'section_title'            => esc_html__('Registration Settings', 'wp-user-avatar'),
                    'set_auto_login_after_reg' => [
                        'type'           => 'checkbox',
                        'label'          => esc_html__('Auto-login after registration', 'wp-user-avatar'),
                        'checkbox_label' => esc_html__('Enable auto-login', 'wp-user-avatar'),
                        'value'          => 'on',
                        'description'    => esc_html__('Check this option to automatically login users after successful registration.', 'wp-user-avatar')
                    ]
                ]
            ]),
            'login_settings'            => apply_filters('ppress_login_settings_page', [
                'tab_title' => esc_html__('Login', 'wp-user-avatar'),
                'dashicon'  => 'dashicons-universal-access-alt',
                [
                    'section_title'                 => esc_html__('Login Settings', 'wp-user-avatar'),
                    'login_username_email_restrict' => [
                        'type'        => 'select',
                        'options'     => [
                            'both'     => esc_html__('Email Address and Username (default)', 'wp-user-avatar'),
                            'email'    => esc_html__('Email Address Only', 'wp-user-avatar'),
                            'username' => esc_html__('Username Only', 'wp-user-avatar')
                        ],
                        'value'       => '',
                        'label'       => esc_html__('Login with Email or Username', 'wp-user-avatar'),
                        'description' => esc_html__('By default, WordPress allows users to log in using either an email address or username. This setting allows you to restrict logins to only accept email addresses or usernames.', 'wp-user-avatar')
                    ],
                ]
            ]),
            'my_account_settings'       => apply_filters('ppress_my_account_settings_page', [
                'tab_title'                               => esc_html__('My Account', 'wp-user-avatar'),
                'section_title'                           => esc_html__('My Account Settings', 'wp-user-avatar'),
                'dashicon'                                => 'dashicons-dashboard',
                'redirect_default_edit_profile_to_custom' => [
                    'type'           => 'checkbox',
                    'label'          => esc_html__('Redirect Default Edit Profile', 'wp-user-avatar'),
                    'checkbox_label' => esc_html__('Activate', 'wp-user-avatar'),
                    'value'          => 'yes',
                    'description'    => sprintf(
                        __('Redirect <a target="_blank" href="%s">default WordPress profile</a> to My Account page.', 'wp-user-avatar'),
                        admin_url('profile.php')
                    )
                ],
                'myac_edit_account_endpoint'              => [
                    'type'        => 'text',
                    'value'       => 'edit-profile',
                    'label'       => esc_html__('Edit Account Endpoint', 'wp-user-avatar'),
                    'description' => __('Endpoint for the "My Account → Account Details" page.', 'wp-user-avatar'),
                ],
                'myac_change_password_endpoint'           => [
                    'type'        => 'text',
                    'value'       => 'change-password',
                    'label'       => esc_html__('Change Password Endpoint', 'wp-user-avatar'),
                    'description' => __('Endpoint for the "My Account → Change Password" page.', 'wp-user-avatar'),
                ],
                'myac_account_details_form'               => [
                    'type'        => 'select',
                    'options'     => $edit_profile_forms,
                    'label'       => esc_html__('Account Details Form', 'wp-user-avatar'),
                    'description' => esc_html__('Do you want to replace the default form in "My Account → Account Details" page? select an Edit Profile form that will replace it.', 'wp-user-avatar')
                ],
            ]),
            'frontend_profile_settings' => apply_filters('ppress_frontend_profile_settings_page', [
                    'tab_title'                         => esc_html__('Frontend Profile', 'wp-user-avatar'),
                    'section_title'                     => esc_html__('Frontend Profile Settings', 'wp-user-avatar'),
                    'dashicon'                          => 'dashicons-admin-users',
                    'set_user_profile_shortcode'        => [
                        'type'        => 'custom_field_block',
                        'label'       => esc_html__('Page with Profile Shortcode', 'wp-user-avatar'),
                        'data'        => $this->page_dropdown('set_user_profile_shortcode'),
                        'description' => sprintf(__('Select the page that contains your <a href="%s">Frontend user profile shortcode</a>.', 'wp-user-avatar'), PPRESS_USER_PROFILES_SETTINGS_PAGE),
                    ],
                    'set_user_profile_slug'             => [
                        'type'        => 'text',
                        'value'       => 'profile',
                        'label'       => esc_html__('Profile Slug', 'wp-user-avatar'),
                        'description' => sprintf(__('Enter your preferred profile URL slug. Default to "profile" if empty. If slug is "profile", URL becomes %s where "john" is a user\'s username.', 'wp-user-avatar'), '<strong>' . home_url() . '/profile/john</strong>'),
                    ],
                    'disable_guests_can_view_profiles'  => [
                        'type'        => 'checkbox',
                        'label'       => esc_html__('Disable Guests from Viewing Profiles', 'wp-user-avatar'),
                        'description' => esc_html__('Enable this option to stop disable guests or non-registered users from viewing users profiles.', 'wp-user-avatar'),
                        'value'       => 'on'
                    ],
                    'disable_members_can_view_profiles' => [
                        'type'        => 'checkbox',
                        'label'       => esc_html__('Disable Members from Viewing Profiles', 'wp-user-avatar'),
                        'description' => esc_html__('Enable this option to stop members from viewing other users profiles. If enabled, users can only see their own profile.', 'wp-user-avatar'),
                        'value'       => 'on'
                    ],
                    'comment_author_url_to_profile'     => [
                        'type'           => 'checkbox',
                        'label'          => esc_html__('Comment Author URL to Profile', 'wp-user-avatar'),
                        'checkbox_label' => esc_html__('Enable option', 'wp-user-avatar'),
                        'value'          => 'on',
                        'description'    => sprintf(__("Change URL of comment authors to their ProfilePress front-end profile.", 'wp-user-avatar'))
                    ],
                    'author_slug_to_profile'            => [
                        'type'           => 'checkbox',
                        'label'          => esc_html__('Authors Page to Profile', 'wp-user-avatar'),
                        'checkbox_label' => esc_html__('Enable option', 'wp-user-avatar'),
                        'value'          => 'on',
                        'description'    => sprintf(__("Change and redirect authors pages %s to their front-end profiles %s.", 'wp-user-avatar'), '<strong>(' . home_url() . '/author/admin)</strong>', '<strong>(' . home_url() . '/' . ppress_get_profile_slug() . '/admin)</strong>')
                    ],
                ]
            ),
            'redirection_settings'      => apply_filters('ppress_redirection_settings_page', [
                'tab_title'                   => esc_html__('Redirection', 'wp-user-avatar'),
                'section_title'               => esc_html__('Redirection Settings', 'wp-user-avatar'),
                'dashicon'                    => 'dashicons-redo',
                'set_log_out_url'             => [
                    'type'        => 'custom_field_block',
                    'label'       => esc_html__('Log out', 'wp-user-avatar'),
                    'data'        => $this->page_dropdown('set_log_out_url',
                            [
                                ['key' => 'default', 'label' => esc_html__('Select...', 'wp-user-avatar')],
                                ['key' => 'current_view_page', 'label' => esc_html__('Currently viewed page', 'wp-user-avatar')]
                            ]
                        ) . $this->custom_text_input('custom_url_log_out'),
                    'description' => sprintf(
                        esc_html__('Select the page users will be redirected to after logout. To redirect to a custom URL instead of a selected page, enter the URL in input field directly above this description.', 'wp-user-avatar') . '%s' .
                        esc_html__('Leave the "custom URL" field empty to fallback to the selected page.', 'wp-user-avatar'),
                        '<br/>'
                    )
                ],
                'set_login_redirect'          => [
                    'type'        => 'custom_field_block',
                    'label'       => esc_html__('Login', 'wp-user-avatar'),
                    'data'        => $this->page_dropdown(
                            'set_login_redirect',
                            [
                                ['key' => 'current_page', 'label' => esc_html__('Currently viewed page', 'wp-user-avatar')],
                                ['key' => 'dashboard', 'label' => esc_html__('WordPress Dashboard', 'wp-user-avatar')]
                            ]
                        )
                                     . $this->custom_text_input('custom_url_login_redirect'),
                    'description' => sprintf(
                        esc_html__('Select the page or custom URL users will be redirected to after login. To redirect to a custom URL instead of a selected page, enter the URL in input field directly above this description', 'wp-user-avatar') . '%s' .
                        esc_html__('Leave the "custom URL" field empty to fallback to the selected page.', 'wp-user-avatar'),
                        '<br/>'
                    )
                ],
                'set_password_reset_redirect' => [
                    'type'        => 'custom_field_block',
                    'label'       => esc_html__('Password Reset', 'wp-user-avatar'),
                    'data'        => $this->page_dropdown(
                            'set_password_reset_redirect',
                            [],
                            [
                                'show_option_none'  => esc_html__('Default..', 'wp-user-avatar'),
                                'option_none_value' => 'no_redirect',
                            ]
                        ) . $this->custom_text_input('custom_url_password_reset_redirect'),
                    'description' => sprintf(
                        esc_html__('Select the page or custom URL users will be redirected to after they successfully reset or change their password. To redirect to a custom URL instead of a selected page, enter the URL in input field directly above this description.', 'wp-user-avatar') . '%s' .
                        esc_html__('Leave the "custom URL" field empty to fallback to the selected page.', 'wp-user-avatar'),
                        '<br/>'
                    )
                ]
            ]),
            'access_settings'           => apply_filters('ppress_access_settings_page', [
                'tab_title'                         => esc_html__('Access', 'wp-user-avatar'),
                'section_title'                     => esc_html__('Access Settings', 'wp-user-avatar'),
                'dashicon'                          => 'dashicons-products',
                'global_site_access_notice'         => [
                    'type'        => 'arbitrary',
                    'data'        => '',
                    'description' => sprintf(
                        '<div class="ppress-settings-page-notice">' . esc_html__('%sNote:%s Access setting takes precedence over %sContent Protection rules%s.', 'wp-user-avatar'),
                        '<strong>', '</strong>', '<a target="_blank" href="' . PPRESS_CONTENT_PROTECTION_SETTINGS_PAGE . '">', '</a>'
                    )
                ],
                'global_site_access'                => [
                    'type'    => 'select',
                    'label'   => esc_html__('Global Site Access', 'wp-user-avatar'),
                    'options' => [
                        'everyone' => esc_html__('Accessible to Everyone', 'wp-user-avatar'),
                        'login'    => esc_html__('Accessible to Logged-in Users', 'wp-user-avatar')
                    ]
                ],
                'global_site_access_redirect_page'  => [
                    'type'        => 'custom_field_block',
                    'label'       => esc_html__('Redirect Page', 'wp-user-avatar'),
                    'data'        => $this->page_dropdown('global_site_access_redirect_page') . $this->custom_text_input('global_site_access_custom_redirect_page'),
                    'description' => esc_html__('Select the page or custom URL to redirect users that are not logged in to.', 'wp-user-avatar')
                ],
                'global_site_access_exclude_pages'  => [
                    'type'        => 'select2',
                    'label'       => esc_html__('Pages to Exclude', 'wp-user-avatar'),
                    'options'     => array_reduce(get_pages(), function ($carry, $item) {
                        $carry[$item->ID] = $item->post_title;

                        return $carry;
                    }, []),
                    'description' => esc_html__('Select the pages to exclude beside the redirect page that will be accessible by everyone.', 'wp-user-avatar')
                ],
                'global_site_access_allow_homepage' => [
                    'type'           => 'checkbox',
                    'value'          => 'yes',
                    'checkbox_label' => esc_html__('Enable', 'wp-user-avatar'),
                    'label'          => esc_html__('Accessible Homepage', 'wp-user-avatar'),
                    'description'    => esc_html__('Check to allow homepage to be accessible by everyone.', 'wp-user-avatar')
                ],
                'global_restricted_access_message'  => [
                    'type'        => 'wp_editor',
                    'value'       => esc_html__('You are unauthorized to view this page.', 'wp-user-avatar'),
                    'label'       => esc_html__('Global Restricted Access Message', 'wp-user-avatar'),
                    'description' => esc_html__('This is the message shown to users that do not have permission to view the content.', 'wp-user-avatar')
                ],
            ])
        ];

        if (class_exists('\BuddyPress')) {
            $args['buddypress_settings'] = apply_filters('ppress_buddypress_settings_page', [
                    'tab_title'                     => esc_html__('BuddyPress', 'wp-user-avatar'),
                    'section_title'                 => esc_html__('BuddyPress Settings', 'wp-user-avatar'),
                    'dashicon'                      => 'dashicons-buddicons-buddypress-logo',
                    'redirect_bp_registration_page' => [
                        'type'           => 'checkbox',
                        'value'          => 'yes',
                        'label'          => esc_html__('Registration Page', 'wp-user-avatar'),
                        'checkbox_label' => esc_html__('Check to enable', 'wp-user-avatar'),
                        'description'    => sprintf(__('Check to redirect BuddyPress registration page to your selected %s', 'wp-user-avatar'), '<a href="#global_settings?set_registration_url_row">custom registration page</a>')
                    ],
                    'override_bp_avatar'            => [
                        'type'           => 'checkbox',
                        'label'          => esc_html__('Override Avatar', 'wp-user-avatar'),
                        'value'          => 'yes',
                        'checkbox_label' => esc_html__('Check to enable', 'wp-user-avatar'),
                        'description'    => esc_html__('Check to override BuddyPress users uploaded avatars with that of ProfilePress.', 'wp-user-avatar')
                    ],
                    'override_bp_profile_url'       => [
                        'type'           => 'checkbox',
                        'value'          => 'yes',
                        'label'          => esc_html__('Override Profile URL', 'wp-user-avatar'),
                        'checkbox_label' => esc_html__('Check to enable', 'wp-user-avatar'),
                        'description'    => esc_html__('Check to change the profile URL of BuddyPress users to ProfilePress front-end profile.', 'wp-user-avatar')
                    ]
                ]
            );
        }

        if (class_exists('\bbPress')) {
            $args['bbpress_settings'] = apply_filters('ppress_bbpress_settings_page', [
                    'tab_title'                => esc_html__('bbPress', 'wp-user-avatar'),
                    'section_title'            => esc_html__('bbPress Settings', 'wp-user-avatar'),
                    'dashicon'                 => 'dashicons-buddicons-bbpress-logo',
                    'override_bbp_profile_url' => [
                        'type'           => 'checkbox',
                        'value'          => 'yes',
                        'label'          => esc_html__('Override Profile URL', 'wp-user-avatar'),
                        'checkbox_label' => esc_html__('Check to enable', 'wp-user-avatar'),
                        'description'    => esc_html__('Check to change bbPress profile URL to ProfilePress front-end profile.', 'wp-user-avatar')
                    ]
                ]
            );
        }

        $settings_args = apply_filters('ppress_settings_page_args', $args);
        $instance      = Custom_Settings_Page_Api::instance($settings_args, PPRESS_SETTINGS_DB_OPTION_NAME, esc_html__('General', 'wp-user-avatar'));
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