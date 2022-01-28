<?php

namespace ProfilePress\Core\Admin\SettingsPages\EmailSettings;

use ProfilePress\Core\Admin\SettingsPages\AbstractSettingsPage;
use ProfilePress\Core\Classes\SendEmail;
use ProfilePress\Custom_Settings_Page_Api;

class EmailSettingsPage extends AbstractSettingsPage
{
    public $email_notification_list_table;

    public function __construct()
    {
        add_filter('ppress_settings_page_screen_option', [$this, 'screen_option']);
        add_action('admin_init', [$this, 'handle_email_preview']);

        add_action('admin_enqueue_scripts', function ($hook_suffix) {
            if ($hook_suffix == 'toplevel_page_pp-config') {
                wp_enqueue_script('customize-loader');
            }
        });

        add_filter('ppress_general_settings_admin_page_title', function ($title) {
            if (isset($_GET['view']) && $_GET['view'] == 'email') {
                $title = esc_html__('Emails', 'wp-user-avatar');
            }

            return $title;
        });
    }

    public function screen_option()
    {
        if (isset($_GET['view']) && $_GET['view'] == 'email') {
            $this->email_notification_list_table = new WPListTable($this->email_notifications());
        }
    }

    public function email_notifications()
    {
        $site_title = ppress_site_title();

        return apply_filters('ppress_email_notifications', [
            [
                'key'          => 'welcome_message',
                'title'        => esc_html__('Account Welcome Email', 'wp-user-avatar'),
                'subject'      => sprintf(esc_html__('Welcome To %s', 'wp-user-avatar'), $site_title),
                'message'      => ppress_welcome_msg_content_default(),
                'description'  => esc_html__('Email that is sent to the user upon successful registration.', 'wp-user-avatar'),
                'recipient'    => esc_html__('Users', 'wp-user-avatar'),
                'placeholders' => [
                    '{{username}}'            => esc_html__('Username of the registered user.', 'wp-user-avatar'),
                    '{{email}}'               => esc_html__('Email address of the registered user.', 'wp-user-avatar'),
                    '{{password}}'            => esc_html__('Password of the registered user.', 'wp-user-avatar'),
                    '{{site_title}}'          => esc_html__('Website title or name.', 'wp-user-avatar'),
                    '{{first_name}}'          => esc_html__('First Name entered by user on registration.', 'wp-user-avatar'),
                    '{{last_name}}'           => esc_html__('Last Name entered by user on registration.', 'wp-user-avatar'),
                    '{{password_reset_link}}' => esc_html__('URL to reset password.', 'wp-user-avatar'),
                    '{{login_link}}'          => esc_html__('URL to login..', 'wp-user-avatar'),
                ]
            ],
            [
                'key'          => 'password_reset',
                'title'        => esc_html__('Password Reset Email', 'wp-user-avatar'),
                'subject'      => sprintf(esc_html__('[%s] Password Reset', 'wp-user-avatar'), $site_title),
                'message'      => ppress_password_reset_content_default(),
                'description'  => esc_html__('Email that is sent to the user upon password reset request.', 'wp-user-avatar'),
                'recipient'    => esc_html__('Users', 'wp-user-avatar'),
                'placeholders' => [
                    '{{username}}'            => esc_html__('Username of user.', 'wp-user-avatar'),
                    '{{email}}'               => esc_html__('Email address of the registered user.', 'wp-user-avatar'),
                    '{{site_title}}'          => esc_html__('Website title or name.', 'wp-user-avatar'),
                    '{{password_reset_link}}' => esc_html__('URL to reset password.', 'wp-user-avatar'),
                ]
            ],
            [
                'key'          => 'new_user_admin_email',
                'title'        => esc_html__('New User Admin Notification', 'wp-user-avatar'),
                'subject'      => sprintf(esc_html__('[%s] New User Registration', 'wp-user-avatar'), $site_title),
                'message'      => ppress_new_user_admin_notification_message_default(),
                'description'  => esc_html__('Email that is sent to admins when there is a new user registration', 'wp-user-avatar'),
                'recipient'    => esc_html__('Administrators', 'wp-user-avatar'),
                'placeholders' => [
                    '{{username}}'   => esc_html__('Username of the newly registered user.', 'wp-user-avatar'),
                    '{{email}}'      => esc_html__('Email address of the newly registered user.', 'wp-user-avatar'),
                    '{{first_name}}' => esc_html__('First name of the newly registered user.', 'wp-user-avatar'),
                    '{{last_name}}'  => esc_html__('Last name of the newly registered user.', 'wp-user-avatar'),
                    '{{site_title}}' => esc_html__('Website name or name.', 'wp-user-avatar'),
                    '{{field_key}}'  => sprintf(
                        esc_html__('Replace "field_key" with the %scustom field key%s or usermeta key.', 'wp-user-avatar'),
                        '<a href="' . PPRESS_CUSTOM_FIELDS_SETTINGS_PAGE . '" target="_blank">', '</a>'
                    )
                ]
            ]
        ]);
    }

    public function admin_page()
    {
        if ( ! isset($_GET['type'])) {
            add_filter('wp_cspa_main_content_area', function ($content_area) {
                ob_start();
                $this->email_notification_list_table->prepare_items();
                $this->email_notification_list_table->display();

                return ob_get_clean() . $content_area;
            });
        }

        $page_header = esc_html__('Emails', 'wp-user-avatar');
        $instance    = Custom_Settings_Page_Api::instance();

        $email_settings = [
            [
                'admin_email_addresses'      => [
                    'label'       => esc_html__('Admin Email Address(es)', 'wp-user-avatar'),
                    'description' => esc_html__('The Email address to receive admin notifications. Use comma to separate multiple email addresses.', 'wp-user-avatar'),
                    'type'        => 'text',
                    'value'       => ppress_admin_email()
                ],
                'email_sender_name'          => [
                    'label'       => esc_html__('Sender Name', 'wp-user-avatar'),
                    'description' => esc_html__('The name to use as the sender of all ProfilePress emails. Preferably your website name.', 'wp-user-avatar'),
                    'type'        => 'text',
                    'value'       => ppress_site_title()
                ],
                'email_sender_email'         => [
                    'value'       => ppress_admin_email(),
                    'label'       => esc_html__('Sender Email Address', 'wp-user-avatar'),
                    'description' => esc_html__('The email address to use as the sender of all ProfilePress emails.', 'wp-user-avatar'),
                    'type'        => 'text'
                ],
                'email_content_type'         => [
                    'type'        => 'select',
                    'options'     => [
                        'text/html'  => esc_html__('HTML', 'wp-user-avatar'),
                        'text/plain' => esc_html__('Plain Text', 'wp-user-avatar')
                    ],
                    'value'       => 'text/html',
                    'label'       => esc_html__('Content Type', 'wp-user-avatar'),
                    'description' => esc_html__('Choose whether to send ProfilePress emails in HTML or plain text. HTML is recommended.', 'wp-user-avatar')
                ],
                'email_template_type'        => [
                    'type'        => 'select',
                    'options'     => [
                        'default' => esc_html__('Default Template', 'wp-user-avatar'),
                        'custom'  => esc_html__('Custom Email Template', 'wp-user-avatar')
                    ],
                    'value'       => 'default',
                    'label'       => esc_html__('Email Template', 'wp-user-avatar'),
                    'description' => esc_html__('Choose "Custom Email Template" if you want to code your own email template from scratch.', 'wp-user-avatar')
                ],
                'customize_default_template' => [
                    'type' => 'custom_field_block',
                    'data' => sprintf(
                        '<a href="%s" target="_blank" class="button load-customize"><span style="margin-top: 3px;margin-right: 3px;" class="dashicons dashicons-edit"></span> <span>%s</span></a>',
                        add_query_arg(['ppress-preview-template' => 'true'], admin_url('customize.php')),
                        esc_html__('Customize Default Template', 'wp-user-avatar')
                    ),
                ]
            ],
        ];

        if (isset($_GET['type'])) {
            $key  = sanitize_text_field($_GET['type']);
            $data = wp_list_filter($this->email_notifications(), ['key' => $key]);

            $data = array_shift($data);

            if (empty($data)) {
                wp_safe_redirect(PPRESS_SETTINGS_SETTING_PAGE);
                exit;
            }

            $page_header = $data['title'];

            $email_content_field_type = 'email_editor';
            $content_type             = ppress_get_setting('email_content_type', 'text/html');

            if ($content_type == 'text/html' && ppress_get_setting('email_template_type', 'default') == 'default') {
                $email_content_field_type = 'wp_editor';
            }

            if ($content_type == 'text/plain') {
                $email_content_field_type = 'textarea';
            }

            add_action('wp_cspa_media_button', function () use ($key) {
                add_action('media_buttons', function () use ($key) {
                    $url = add_query_arg([
                        'pp_email_preview' => $key,
                        '_wpnonce'         => ppress_create_nonce()
                    ],
                        admin_url()
                    );

                    printf(
                        '<a target="_blank" href="%s" class="button"><span class="wp-media-buttons-icon dashicons dashicons-visibility"></span> %s</a>',
                        $url,
                        esc_html__('Preview Email', 'wp-user-avatar')
                    );
                });
            });

            add_action('wp_cspa_after_wp_editor_field', function () use ($data) {
                if (isset($data['placeholders'])) {
                    $this->placeholder_tags_table($data['placeholders']);
                }
            });

            add_action('wp_cspa_after_email_editor_field', function () use ($data) {
                if (isset($data['placeholders'])) {
                    $this->placeholder_tags_table($data['placeholders']);
                }
            });

            $email_settings = [
                [
                    $key . '_email_enabled' => [
                        'type'           => 'checkbox',
                        'label'          => esc_html__('Enable Notification', 'wp-user-avatar'),
                        'checkbox_label' => esc_html__('Enable', 'wp-user-avatar'),
                        'value'          => 'on',
                        'default_value'  => 'on',
                        'description'    => esc_html__('Check to enable this email notification.', 'wp-user-avatar')
                    ],
                    $key . '_email_subject' => [
                        'type'        => 'text',
                        'value'       => $data['subject'],
                        'label'       => esc_html__('Subject Line', 'wp-user-avatar'),
                        'description' => esc_html__('Enter the subject or title for the welcome message email.', 'wp-user-avatar')
                    ],
                    $key . '_email_content' => [
                        'type'  => $email_content_field_type,
                        'value' => $data['message'],
                        'label' => esc_html__('Message Body', 'wp-user-avatar'),
                    ],
                ]
            ];
        }

        $instance->option_name(PPRESS_SETTINGS_DB_OPTION_NAME);
        $instance->page_header($page_header);
        $this->register_core_settings($instance, true);
        $instance->main_content($email_settings);
        $instance->remove_white_design();
        $instance->header_without_frills();
        $instance->tab($this->settings_tab_args());
        $instance->build(true);

        $this->toggle_field_js_script();
    }

    public function handle_email_preview()
    {
        if ( ! isset($_GET['pp_email_preview']) || empty($_GET['pp_email_preview'])) return;

        if ( ! current_user_can('manage_options')) return;

        ppress_verify_nonce();

        $key = sanitize_text_field($_GET['pp_email_preview']);


        $data = wp_list_filter($this->email_notifications(), ['key' => $key]);

        $data = array_shift($data);

        $subject = ppress_get_setting($key . '_email_subject', $data['subject'], true);
        $content = ppress_get_setting($key . '_email_content', $data['message'], true);
        echo (new SendEmail('', $subject, $content))->templatified_email();
        exit;
    }

    public function toggle_field_js_script()
    {
        ?>
        <script type="text/javascript">
            (function ($) {

                $('#email_template_type').change(function () {
                    var cache = $('#customize_default_template_row');
                    cache.hide();
                    if (this.value === 'default') {
                        cache.show();
                    }
                }).change();

                $('#email_content_type').change(function () {
                    var cache = $('#email_template_type_row, #customize_default_template_row');
                    cache.hide();
                    if (this.value === 'text/html') {
                        cache.show();
                        $('#email_template_type').change();
                    }

                }).change();
            })(jQuery);
        </script>
        <?php
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