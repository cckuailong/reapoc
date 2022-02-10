<?php

/**
 * Extension Abstract
 *
 * @package NotificationX\Extensions
 */

namespace NotificationX\Types;

use NotificationX\Core\Rules;
use NotificationX\Extensions\ExtensionFactory;
use NotificationX\Extensions\GlobalFields;
use NotificationX\GetInstance;
use NotificationX\Modules;

/**
 * Extension Abstract for all Extension.
 */
class ContactForm extends Types {
    /**
     * Instance of Admin
     *
     * @var Admin
     */
    use GetInstance;

    public $priority = 40;
    public $id       = 'form';
    public $module   = [
        'modules_cf7',
        'modules_wpf',
        'modules_njf',
        'modules_grvf',
    ];
    public $default_source = 'cf7';
    public $default_theme  = 'form_theme-one';


    /**
     * Initially Invoked when initialized.
     */
    public function __construct() {
        $this->title = __('Contact Form', 'notificationx');
        $this->themes = [
            'theme-one'   => [
                'source' => NOTIFICATIONX_ADMIN_URL . 'images/extensions/themes/form/cf7-theme-two.jpg',
                'image_shape' => 'circle',
                // Default values for Add New > Content > Notification Template fields
                'template' => [
                    'first_param'         => 'select_a_tag',
                    'custom_first_param'  => __('Someone', 'notificationx'),
                    'second_param'        => __('recently contacted via', 'notificationx'),
                    'third_param'         => 'tag_title',
                    'custom_third_param'  => '',
                    'fourth_param'        => 'tag_time',
                    'custom_fourth_param' => __('Some time ago', 'notificationx'),
                ],
            ],
            'theme-two'   => [
                'source' => NOTIFICATIONX_ADMIN_URL . 'images/extensions/themes/form/cf7-theme-one.jpg',
                'image_shape' => 'circle',
                'template' => [
                    'first_param'         => 'select_a_tag',
                    'custom_first_param'  => __('Someone', 'notificationx'),
                    'second_param'        => __('recently contacted via', 'notificationx'),
                    'third_param'         => 'tag_title',
                    'custom_third_param'  => '',
                    'fourth_param'        => 'tag_time',
                    'custom_fourth_param' => __('Some time ago', 'notificationx'),
                ],
            ],
            'theme-three' => [
                'source' => NOTIFICATIONX_ADMIN_URL . 'images/extensions/themes/form/cf7-theme-three.jpg',
                'image_shape' => 'square',
                'template' => [
                    'first_param'         => 'select_a_tag',
                    'custom_first_param'  => __('Someone', 'notificationx'),
                    'second_param'        => __('recently contacted via', 'notificationx'),
                    'third_param'         => 'tag_title',
                    'custom_third_param'  => '',
                    'fourth_param'        => 'tag_time',
                    'custom_fourth_param' => __('Some time ago', 'notificationx'),
                ],
            ],
        ];
        $this->templates = [
            // Dropdown options for Add New > Content > Notification Template fields
            'form_template_new' => [
                'first_param' => [
                    'select_a_tag' => [
                        'label'    => __('Select A Tag', 'notificationx'),
                        'value'    => 'select_a_tag',
                        'disabled' => true,
                    ],
                ],
                'third_param' => [
                    'tag_title'       => __('Form Title', 'notificationx'),
                    // 'tag_custom_form_title' => __('Custom Title', 'notificationx'),
                ],
                'fourth_param' => [
                    'tag_time'       => __('Definite Time', 'notificationx'),
                ],
                // themes for this template.
                '_themes' => [
                    'form_theme-one',
                    'form_theme-two',
                    'form_theme-three',
                ],
            ],
        ];
        parent::__construct();
    }

    /**
     * Hooked to nx_before_metabox_load action.
     *
     * @return void
     */
    public function init_fields() {
        parent::init_fields();
        add_filter('nx_content_fields', [$this, 'add_form_fields'], 9);
        add_filter('nx_notification_template', [$this, 'notification_template'], 9);
        add_filter('nx_customize_fields', [$this, 'customize_fields'], 20);
    }

    /**
     * Loading forms input field via ajax for first dropdown.
     *
     * @param array $fields
     * @return array
     */
    public function notification_template( $fields ){
        $fields['first_param']['ajax'] = [
            'on'   => 'click',
            'api'  => "/notificationx/v1/get-data",
            'data' => [
                'type'      => "ContactForm",
                'form_type' => "@source",
                'form_id'   => "@form_list",
            ],
            'target' => "notification-template[first_param]",
            'rules'  => [
                'is', 'type', 'form'
            ]
        ];

        return $fields;
    }

    /**
     * Responsible for passing the ajax request to extension.
     *
     * @param array $args
     * @return void
     */
    public static function restResponse( $args ){
        if( ! isset( $args['form_type'] ) || $args['form_type'] === 'undefined' ) {
            return new \WP_Error('something', 'NILL');
        }
        $args['form_id'] = str_replace(trim($args['form_type']) . '_', '', $args['form_id']);
        return ExtensionFactory::get_instance()->get( trim( $args['form_type'] ) )->restResponse( $args );
    }

    /**
     * Adding fields in the metabox.
     *
     * @param array $args Settings arguments.
     * @return array
     */
    public function add_form_fields($fields) {

        $templates = &$fields['content']['fields']['notification-template']['fields'];

        $fields['content']['fields']['form_list'] = [
            'type' => 'select',
            'name' => 'form_list',
            'label' => __('Select a Form', 'notificationx'),
            'options' => apply_filters('nx_form_list', []),
            'priority' => 89,
            'rules' => Rules::includes( 'type', $this->id ),
        ];

        return $fields;
    }

    /**
     * Undocumented function
     *
     * @param array $options
     * @return array
     */
    public function customize_fields($fields) {
        $behaviour = &$fields['behaviour']['fields'];
        $behaviour['link_open'] = Rules::is('type', $this->id, true, $behaviour['link_open']);
        return $fields;
    }
}
