<?php

/**
 * Extension Factory
 *
 * @package NotificationX\Extensions
 */

namespace NotificationX\Admin;

use NotificationX\Core\Database;
use NotificationX\Core\Modules;
use NotificationX\Core\REST;
use NotificationX\Core\Rules;
use NotificationX\Extensions\GlobalFields;
use NotificationX\GetInstance;
use NotificationX\NotificationX;
use UsabilityDynamics\Settings as UsabilityDynamicsSettings;

/**
 * ExtensionFactory Class
 */
class Settings extends UsabilityDynamicsSettings {
    /**
     * Instance of Settings
     *
     * @var Settings
     */
    use GetInstance;

    protected $wpdb;
    protected $defaults = [];

    /**
     * Initially Invoked when initialized.
     *
     * @hook init
     */
    public function __construct( $args ) {
        global $wpdb;
        $this->wpdb = $wpdb;
        parent::__construct( $args );
        add_action( 'init', [ $this, 'init' ] );
        // add_filter('user_has_cap', array($this, 'allow_admin'), 10, 4);
    }

    /**
     * Called from Admin::init();
     * NotificationX
     *
     * @return void
     */
    public function init() {
        // add_action('admin_enqueue_scripts', [$this, 'admin_enqueue_scripts']);
        add_action( 'admin_menu', [ $this, 'menu' ], 25 );
        add_filter( 'nx_branding_url', array( $this, 'nx_branding_url' ), 12 );
    }

    /**
     * This method is responsible for Admin Menu of
     * NotificationX
     *
     * @return void
     */
    public function menu() {
        add_submenu_page( 'nx-admin', 'Settings', 'Settings', 'edit_notificationx_settings', 'nx-settings', [ Admin::get_instance(), 'views' ], 3 );
    }

    /**
     * Register scripts and styles.
     *
     * @param string $hook
     * @return void
     */
    function get_form_data() {
        $data                 = NotificationX::get_instance()->normalize( $this->settings_form() );
        $data['current_page'] = 'settings';
        $data['rest']         = REST::get_instance()->rest_data();
        $data['savedValues']  = self::get_instance()->get( 'settings', false );
        $data['values']       = self::get_instance()->get( 'settings', false );
        $data['assets']       = [
            'admin'  => NOTIFICATIONX_ADMIN_URL,
            'public' => NOTIFICATIONX_PUBLIC_URL,
        ];
        return $data;
    }

    /**
     * Admin Views
     *
     * @return void
     */
    public function settings_page() {
        include_once NOTIFICATIONX_INCLUDES . 'Admin/views/settings.views.php';
    }


    public function settings_form( $nx_id = 0 ) {
        do_action( 'nx_before_settings_fields' );
        $wp_roles  = GlobalFields::get_instance()->normalize_fields( $this->get_roles() );
        $site_name = get_bloginfo( 'name' );
        $settings  = [
            'id'            => 'notificationx_metabox_wrapper',
            'title'         => __( 'NotificationX', 'notificationx' ),
            'object_types'  => array( 'notificationx' ),
            'context'       => 'normal',
            'priority'      => 'high',
            'show_header'   => false,
            'tabnumber'     => true,
            'layout'        => 'horizontal',
            'is_pro_active' => NotificationX::get_instance()->is_pro(),
            'config'        => [
                'active'  => 'tab-general',
                'sidebar' => false,
                'title'   => false,
            ],
            'submit'        => [
                'show'  => true,
                'label' => __( 'Save Settings', 'notificationx' ),
                'class' => 'save-settings',
            ],
            'tabs'          => apply_filters('nx_settings_tab', [
                'tab-general'                => apply_filters('nx_settings_tab_general', [
                    'id'       => 'tab-general',
                    'label'    => __( 'General', 'notificationx' ),
                    'classes'  => 'tab-general',
                    'priority' => 10,
                    'fields'   => [
                        'section-modules' => [
                            'label'  => __( 'Modules', 'notificationx' ),
                            'name'   => 'section-modules',
                            'type'   => 'section',
                            'fields' => [
                                'modules' => [
                                    // 'label'   => "Modules",
                                    'name'     => 'modules',
                                    'type'     => 'toggle',
                                    'multiple' => true,
                                    'default'  => true,
                                    'style'    => [
                                        'type'   => 'card',
                                        'column' => 3,
                                    ],
                                    'options'  => array_values( Modules::get_instance()->get_all() ),
                                ],
                            ],
                        ],
                    ],
                    ]
                ),
                'advanced-settings-tab'      => apply_filters('nx_settings_tab_advanced', [
                    'id'       => 'tab-advanced-settings',
                    'label'    => __( 'Advanced Settings', 'notificationx' ),
                    'classes'  => 'tab-advanced-settings',
                    'priority' => 20,
                    'fields'   => [
                        'powered_by'      => [
                            'name'     => 'powered_by',
                            'label'    => __( 'Powered By', 'notificationx' ),
                            'type'     => 'section',
                            'priority' => 15,
                            'fields'   => [
                                'disable_powered_by' => [
                                    'type'        => 'checkbox',
                                    'label'       => __( 'Disable Powered By', 'notificationx' ),
                                    'name'        => 'disable_powered_by',
                                    'default'     => 0,
                                    'priority'    => 10,
                                    'description' => __( 'Click, if you want to disable powered by text from notification', 'notificationx' ),
                                ],
                            ],
                        ],
                        'role_management' => array(
                            'name'     => 'role_management',
                            'type'     => 'section',
                            'label'    => __( 'Role Management', 'notificationx' ),
                            'priority' => 30,
                            'fields'   => array(
                                'notification_view_roles' => array(
                                    'name'     => 'notification_view_roles',
                                    'type'     => 'select',
                                    'label'    => __( 'Who Can View Notification?', 'notificationx' ),
                                    'priority' => 1,
                                    'multiple' => true,
                                    'is_pro'   => true,
                                    'disable'  => true,
                                    'default'  => [ 'administrator' ],
                                    'options'  => $wp_roles,
                                ),
                                'notification_roles'      => array(
                                    'name'     => 'notification_roles',
                                    'type'     => 'select',
                                    'label'    => __( 'Who Can Create Notification?', 'notificationx' ),
                                    'priority' => 1,
                                    'multiple' => true,
                                    'is_pro'   => true,
                                    'disable'  => true,
                                    'default'  => [ 'administrator' ],
                                    'options'  => $wp_roles,
                                ),
                                'settings_roles'          => array(
                                    'name'     => 'settings_roles',
                                    'type'     => 'select',
                                    'label'    => __( 'Who Can Edit Settings?', 'notificationx' ),
                                    'priority' => 2,
                                    'multiple' => true,
                                    'is_pro'   => true,
                                    'disable'  => true,
                                    'default'  => [ 'administrator' ],
                                    'options'  => $wp_roles,
                                ),
                                'analytics_roles'         => array(
                                    'name'     => 'analytics_roles',
                                    'type'     => 'select',
                                    'label'    => __( 'Who Can Check Analytics?', 'notificationx' ),
                                    'priority' => 3,
                                    'multiple' => true,
                                    'is_pro'   => true,
                                    'disable'  => true,
                                    'default'  => [ 'administrator' ],
                                    'options'  => $wp_roles,
                                ),
                            ),
                        ),
                    ],
                    ]
                ),
                'email-analytics-reporting'  => apply_filters('nx_settings_tab_email_analytics', [
                    'label'    => __( 'Analytics & Reporting', 'notificationx' ),
                    'id'       => 'email-analytics-reporting',
                    'classes'  => 'tab-advanced-settings',
                    'priority' => 30,
                    'fields'   => [
                        'analytics'       => array(
                            'name'     => 'analytics',
                            'priority' => 10,
                            'type'     => 'section',
                            'label'    => __( 'Analytics', 'notificationx' ),
                            'fields'   => array(
                                'enable_analytics'         => array(
                                    'name'     => 'enable_analytics',
                                    'type'     => 'checkbox',
                                    'label'    => __( 'Enable Analytics', 'notificationx' ),
                                    'default'  => true,
                                    'priority' => 0,
                                ),
                                'disable_dashboard_widget' => array(
                                    'name'        => 'disable_dashboard_widget',
                                    'type'        => 'checkbox',
                                    'label'       => __( 'Disable Dashboard Widget', 'notificationx' ),
                                    'default'     => false,
                                    'priority'    => 5,
                                    'description' => __( 'Click, if you want to disable dashboard widget of analytics only.', 'notificationx' ),
                                    'rules'       => Rules::is( 'enable_analytics', true ),
                                ),
                                'analytics_from'           => array(
                                    'name'     => 'analytics_from',
                                    'type'     => 'select',
                                    'label'    => __( 'Analytics From', 'notificationx' ),
                                    'options'  => GlobalFields::get_instance()->normalize_fields(array(
                                        'everyone'         => __( 'Everyone', 'notificationx' ),
                                        'guests'           => __( 'Guests Only', 'notificationx' ),
                                        'registered_users' => __( 'Registered Users Only', 'notificationx' ),
                                        )
                                    ),
                                    'default'  => 'everyone',
                                    'priority' => 10,
                                    'rules'    => Rules::is( 'enable_analytics', true ),
                                ),
                                'exclude_bot_analytics'    => array(
                                    'name'        => 'exclude_bot_analytics',
                                    'type'        => 'checkbox',
                                    'label'       => __( 'Exclude Bot Analytics', 'notificationx' ),
                                    'default'     => true,
                                    'priority'    => 15,
                                    'description' => __( 'Select if you want to exclude bot analytics.', 'notificationx' ),
                                    'rules'       => Rules::is( 'enable_analytics', true ),
                                ),
                            ),
                        ),
                        'email_reporting' => array(
                            'name'     => 'email_reporting',
                            'priority' => 20,
                            'type'     => 'section',
                            'label'    => __( 'Reporting', 'notificationx' ),
                            'rules'    => Rules::is( 'enable_analytics', true ),
                            'fields'   => array(
                                'disable_reporting'   => array(
                                    'name'     => 'disable_reporting',
                                    'label'    => __( 'Disable Reporting', 'notificationx' ),
                                    'type'     => 'checkbox',
                                    'priority' => 0,
                                    'default'  => 0,
                                ),
                                'reporting_frequency' => array(
                                    'name'     => 'reporting_frequency',
                                    'type'     => 'select',
                                    'label'    => __( 'Reporting Frequency', 'notificationx' ),
                                    'default'  => 'nx_weekly',
                                    'is_pro'   => true,
                                    'priority' => 1,
                                    'disable'  => true,
                                    'options'  => GlobalFields::get_instance()->normalize_fields(array(
                                        'nx_daily'   => __( 'Once Daily', 'notificationx' ),
                                        'nx_weekly'  => __( 'Once Weekly', 'notificationx' ),
                                        'nx_monthly' => __( 'Once Monthly', 'notificationx' ),
                                        )
                                    ),
                                    'rules'    => Rules::is( 'disable_reporting', false ),
                                ),
                                'reporting_monthly_help_text' => array(
                                    'name'     => 'reporting_monthly_help_text',
                                    'type'     => 'message',
                                    'class'    => 'nx-warning',
                                    'priority' => 1.5,
                                    'message'  => __( 'It will be triggered on the first day of next month.', 'notificationx' ),
                                    'rules'    => Rules::is( 'reporting_frequency', 'nx_monthly' ),
                                ),
                                'reporting_day'       => array(
                                    'name'        => 'reporting_day',
                                    'type'        => 'select',
                                    'label'       => __( 'Select Reporting Day', 'notificationx' ),
                                    'default'     => 'monday',
                                    'priority'    => 2,
                                    'options'     => GlobalFields::get_instance()->normalize_fields(array(
                                        'sunday'    => __( 'Sunday', 'notificationx' ),
                                        'monday'    => __( 'Monday', 'notificationx' ),
                                        'tuesday'   => __( 'Tuesday', 'notificationx' ),
                                        'wednesday' => __( 'Wednesday', 'notificationx' ),
                                        'thursday'  => __( 'Thursday', 'notificationx' ),
                                        'friday'    => __( 'Friday', 'notificationx' ),
                                        )
                                    ),
                                    'description' => __( 'Select a Day for Email Report.', 'notificationx' ),
                                    'rules'       => Rules::logicalRule([
                                        Rules::is( 'reporting_frequency', 'nx_weekly' ),
                                        Rules::is( 'disable_reporting', false ),
                                        ]
                                    ),
                                ),
                                'reporting_email'     => array(
                                    'name'     => 'reporting_email',
                                    'type'     => 'text',
                                    'label'    => __( 'Reporting Email', 'notificationx' ),
                                    'default'  => get_option( 'admin_email' ),
                                    'priority' => 3,
                                    'rules'    => Rules::is( 'disable_reporting', false ),
                                ),
                                'reporting_subject'   => array(
                                    'name'     => 'reporting_subject',
                                    'type'     => 'text',
                                    'label'    => __( 'Reporting Email Subject', 'notificationx' ),
                                    'default'  => __( "Weekly Engagement Summary of ‘{$site_name}’", 'notificationx' ),
                                    'priority' => 4,
                                    'disable'  => true,
                                    'rules'    => Rules::is( 'disable_reporting', false ),
                                ),
                                'test_report'         => array(
                                    'name'     => 'test_report',
                                    'label'    => __( 'Reporting Test', 'notificationx' ),
                                    'text'     => __( 'Test Report', 'notificationx' ),
                                    'type'     => 'button',
                                    'priority' => 5,
                                    'rules'    => Rules::is( 'disable_reporting', false ),
                                    'ajax'     => [
                                        'on'   => 'click',
                                        'api'  => '/notificationx/v1/reporting-test',
                                        'data' => [
                                            'disable_reporting'   => '@disable_reporting',
                                            'reporting_subject'   => '@reporting_subject',
                                            'reporting_email'     => '@reporting_email',
                                            'reporting_day'       => '@reporting_day',
                                            'reporting_frequency' => '@reporting_frequency',
                                        ],
                                        'swal' => [
                                            'text'      => __( 'Successfully Sent a Test Report in Your Email.', 'notificationx' ),
                                            'icon'      => 'success',
                                            'autoClose' => 2000,
                                        ],
                                    ],
                                ),
                            ),
                        ),
                    ],
                    ]
                ),
                'cache_settings_tab'         => apply_filters('nx_settings_tab_cache', [
                    'id'       => 'tab-cache-settings',
                    'label'    => __( 'Cache Settings', 'notificationx' ),
                    'priority' => 40,
                    'fields'   => [
                        'cache_settings' => array(
                            'name'     => 'cache_settings',
                            'type'     => 'section',
                            'label'    => __( 'Cache Settings', 'notificationx' ),
                            'priority' => 5,
                            'fields'   => array(
                                'cache_limit'            => array(
                                    'name'        => 'cache_limit',
                                    'type'        => 'text',
                                    'label'       => __( 'Cache Limit', 'notificationx' ),
                                    'description' => __( 'Number of Notification Data to be saved in Database.', 'notificationx' ),
                                    'default'     => '100',
                                    'priority'    => 1,
                                ),
                                'download_stats_cache_duration' => array(
                                    'name'        => 'download_stats_cache_duration',
                                    'type'        => 'text',
                                    'label'       => __( 'Download Stats Cache Duration', 'notificationx' ),
                                    'description' => __( 'Minutes (Schedule Duration to fetch new data).', 'notificationx' ),
                                    'default'     => 3,
                                    'priority'    => 2,
                                ),
                                'reviews_cache_duration' => array(
                                    'name'        => 'reviews_cache_duration',
                                    'type'        => 'text',
                                    'label'       => __( 'Reviews Cache Duration', 'notificationx' ),
                                    'description' => __( 'Minutes (Schedule Duration to fetch new data).', 'notificationx' ),
                                    'default'     => '3',
                                    'priority'    => 3,
                                ),
                            ),
                        ),
                    ],
                    ]
                ),
                'tab-miscellaneous-settings' => apply_filters('nx_settings_tab_miscellaneous', [
                    'id'       => 'tab-miscellaneous-settings',
                    'label'    => __( 'Miscellaneous', 'notificationx' ),
                    'priority' => 50,
                    'fields'   => [],
                    ]
                ),
                ]
            ),
        ];

        if ( defined( 'NX_DEBUG' ) && NX_DEBUG ) {
            $settings['tabs']['tab-miscellaneous-settings']['fields'][] = array(
                'name'     => 'danger-zone',
                'type'     => 'section',
                'label'    => __( 'Danger Zone', 'notificationx' ),
                'priority' => 200,
                'fields'   => array(
                    'delete-settings' => array(
                        'name'     => 'delete-settings',
                        'label'    => __( 'Delete Settings', 'notificationx' ),
                        'text'     => __( 'Delete Settings', 'notificationx' ),
                        'type'     => 'button',
                        'priority' => 15,
                        'ajax'     => [
                            'on'     => 'click',
                            'reload' => true,
                            'api'    => '/notificationx/v1/settings',
                            'data'   => [
                                'delete_settings' => true,
                            ],
                            'swal'   => [
                                'text'      => __( 'Successfully deleted Settings.', 'notificationx' ),
                                'icon'      => 'deleted',
                                'autoClose' => 2000,
                            ],
                        ],
                    ),
                ),
            );
        }

        $settings = apply_filters( 'nx_settings_configs', $settings );
        return $settings;
    }

    /**
     * Get All Roles
     * dynamically
     *
     * @return array
     */
    public function get_roles() {
        $roles = wp_roles()->role_names;
        unset( $roles['subscriber'] );
        return $roles;
    }
    /**
     * Get All Roles
     * dynamically
     * user_has_cap
     *
     * @return array
     */
    public function allow_admin( $allcaps, $caps, $args, $user ) {
        $nx_roles = [
            'read_notificationx',
            'edit_notificationx',
            'edit_notificationx_settings',
            'read_notificationx_analytics',
        ];
        if ( ! empty( $caps[0] ) && array_key_exists( 'administrator', $allcaps ) && ! array_key_exists( $caps[0], $allcaps ) && in_array( $caps[0], $nx_roles ) ) {
            $role = get_role( 'administrator' );
            $role->add_cap( $caps[0] );
            $allcaps[ $caps[0] ] = true;
        }
        return $allcaps;
    }

    public function save_settings( $settings ) {
        if ( ! current_user_can( 'edit_notificationx_settings' ) ) {
            return false;
        }
        $remove_before_save = [
            'empty',
        ];

        $roles    = $this->get_selected_roles( $settings );
        $settings = array_merge( $settings, $roles );

        $settings = apply_filters( 'nx_settings', $settings );

        foreach ( $remove_before_save as $key ) {
            if ( isset( $settings[ $key ] ) ) {
                unset( $settings[ $key ] );
            }
        }
        // need this to ensure saved value don't return empty array instead of object.
        if ( ! empty( $settings['delete_settings'] ) ) {
            $settings = [ 'empty' => true ];
        }

        $this->set( 'settings', $settings );
        delete_transient( 'nx_get_field_names' );
        do_action( 'nx_settings_saved', $settings );
        return true;
    }

    public function get_role_map( $settings = [] ) {
        if ( empty( $settings ) || count( $settings ) == 1 ) {
            $settings = $this->get_selected_roles();
        }
        return [
            'read_notificationx'           => [
                'roles' => $settings['notification_view_roles'],
                'map'   => [],
            ],
            'edit_notificationx'           => [
                'roles' => $settings['notification_roles'],
                'map'   => [ 'read_notificationx' ],
            ],
            'edit_notificationx_settings'  => [
                'roles' => $settings['settings_roles'],
                'map'   => [ 'read_notificationx' ],
            ],
            'read_notificationx_analytics' => [
                'roles' => $settings['analytics_roles'],
                'map'   => [ 'read_notificationx' ],
            ],
        ];
    }

    public function get_selected_roles( $settings = [] ) {
        $notification_view_roles = isset( $settings['notification_view_roles'] ) ? $settings['notification_view_roles'] : self::get_instance()->get( 'settings.notification_view_roles', [] );
        $notification_roles      = isset( $settings['notification_roles'] ) ? $settings['notification_roles'] : self::get_instance()->get( 'settings.notification_roles', [] );
        $settings_roles          = isset( $settings['settings_roles'] ) ? $settings['settings_roles'] : self::get_instance()->get( 'settings.settings_roles', [] );
        $analytics_roles         = isset( $settings['analytics_roles'] ) ? $settings['analytics_roles'] : self::get_instance()->get( 'settings.analytics_roles', [] );

        if ( ! is_array( $notification_view_roles ) ) {
            $notification_view_roles = [ $notification_view_roles ];
        }
        if ( ! is_array( $notification_roles ) ) {
            $notification_roles = [ $notification_roles ];
        }
        if ( ! is_array( $settings_roles ) ) {
            $settings_roles = [ $settings_roles ];
        }
        if ( ! is_array( $analytics_roles ) ) {
            $analytics_roles = [ $analytics_roles ];
        }

        return apply_filters('nx_role_management', [
            'notification_view_roles' => array_values( array_unique( array_merge( [ 'administrator' ], $notification_view_roles ) ) ),
            'notification_roles'      => array_values( array_unique( array_merge( [ 'administrator' ], $notification_roles ) ) ),
            'settings_roles'          => array_values( array_unique( array_merge( [ 'administrator' ], $settings_roles ) ) ),
            'analytics_roles'         => array_values( array_unique( array_merge( [ 'administrator' ], $analytics_roles ) ) ),
        ]
        );
    }

    /**
     * Getter for options
     *
     * @param bool|\UsabilityDynamics\type $key
     *
     * @param bool                         $default
     *
     * @return type
     */
    public function Xget( $key = false, $default = false ) {
        if ( empty( $default ) && strpos( $key, 'settings.' ) === 0 ) {
            // getting default value from settings form.
            $_key = str_replace( 'settings.', '', $key );
            if ( empty( $default ) ) {
                $defaults = $this->get_defaults();
                $default  = ! empty( $defaults[ $_key ] ) ? $defaults[ $_key ]['default'] : $default;
            }
        }
        return parent::get( $key, $default );
    }

    /**
     * Setter for options
     *
     * @param string|\UsabilityDynamics\type $key
     * @param bool|\UsabilityDynamics\type   $value
     * @param bool                           $bypass_validation
     *
     * @internal param bool|\UsabilityDynamics\type $force_save
     *
     * @return \UsabilityDynamics\Settings
     */
    public function set( $key = '', $value = false, $bypass_validation = false ) {
        if ( strpos( $key, '.' ) === false ) {
            parent::flush( false, $key );
        }
        return parent::set( $key, $value, $bypass_validation );
    }

    public function nx_branding_url( $link ) {
        $affiliate_link = $this->get( 'settings.affiliate_link' );
        if ( ! empty( $affiliate_link ) ) {
            $link = $affiliate_link;
        }
        return $link;
    }

    // @todo maybe remove
    public function get_defaults( $key = null ) {
        if ( empty( $this->defaults ) ) {
            $tabs           = $this->settings_form();
            $this->defaults = NotificationX::get_instance()->get_field_names( $tabs['tabs'] );
        }
        return $this->defaults;
    }
}
