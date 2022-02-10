<?php
/**
 * Extension Abstract
 *
 * @package NotificationX\Extensions
 */

namespace NotificationX\Types;

use NotificationX\Core\Rules;
use NotificationX\Extensions\GlobalFields;
use NotificationX\GetInstance;
use NotificationX\Modules;
use NotificationX\NotificationX;

/**
 * Extension Abstract for all Extension.
 */
class ELearning extends Types {
    /**
     * Instance of Admin
     *
     * @var Admin
     */
    use GetInstance;

    public $priority = 25;
    public $themes = [];
    public $module = [
        'modules_tutor',
        'modules_learndash',
    ];
    public $default_source    = 'tutor';
    public $default_theme = 'elearning_theme-one';


    /**
     * Initially Invoked when initialized.
     */
    public function __construct(){
        $this->id = 'elearning';
        $this->title = __('eLearning', 'notificationx');
        $this->themes = [
            'theme-one'   => [
                'source' => NOTIFICATIONX_ADMIN_URL . 'images/extensions/themes/elearning/elearning-theme-1.jpg',
                'image_shape' => 'circle',
                'template'  => [
                    'first_param'         => 'tag_name',
                    'custom_first_param'  => __('Someone' , 'notificationx'),
                    'second_param'        => __('just enrolled', 'notificationx'),
                    'third_param'         => 'tag_course_title',
                    'custom_third_param'  => __('Anonymous Course' , 'notificationx'),
                    'fourth_param'        => 'tag_time',
                    'custom_fourth_param' => __( 'Some time ago', 'notificationx' ),
                ],
            ],
            'theme-two'   => [
                'source' => NOTIFICATIONX_ADMIN_URL . 'images/extensions/themes/elearning/elearning-theme-2.jpg',
                'image_shape' => 'circle',
                'template'  => [
                    'first_param'         => 'tag_name',
                    'custom_first_param'  => __('Someone' , 'notificationx'),
                    'second_param'        => __('recently enrolled' , 'notificationx'),
                    'third_param'         => 'tag_course_title',
                    'custom_third_param'  => __('Anonymous Course' , 'notificationx'),
                    'fourth_param'        => 'tag_time',
                    'custom_fourth_param' => __( 'Some time ago', 'notificationx' ),
                ],
            ],
            'theme-three' => [
                'source'   => NOTIFICATIONX_ADMIN_URL . 'images/extensions/themes/elearning/elearning-theme-3.jpg',
                'image_shape' => 'square',
                'template' => [
                    'first_param'         => 'tag_name',
                    'custom_first_param'  => __('Someone' , 'notificationx'),
                    'second_param'        => __('recently enrolled' , 'notificationx'),
                    'third_param'         => 'tag_course_title',
                    'custom_third_param'  => __('Anonymous Course' , 'notificationx'),
                    'fourth_param'        => 'tag_time',
                    'custom_fourth_param' => __( 'Some time ago', 'notificationx' ),
                ],
            ],
            'theme-four' => array(
                'is_pro' => true,
                'source' => NOTIFICATIONX_ADMIN_URL . 'images/extensions/themes/elearning/elearning-theme-4.png',
                'image_shape' => 'circle',
            ),
            'theme-five' => array(
                'is_pro' => true,
                'source' => NOTIFICATIONX_ADMIN_URL . 'images/extensions/themes/elearning/elearning-theme-5.png',
                'image_shape' => 'circle',
            ),
            'conv-theme-six' => array(
                'is_pro' => true,
                'source' => NOTIFICATIONX_ADMIN_URL . 'images/extensions/themes/elearning/elearning-theme-6.png',
                'image_shape' => 'circle',
            ),
            'conv-theme-seven' => array(
                'is_pro' => true,
                'source' => NOTIFICATIONX_ADMIN_URL . 'images/extensions/themes/elearning/elearning-theme-7.png',
                'image_shape' => 'rounded',
            ),
            'conv-theme-eight' => array(
                'is_pro' => true,
                'source' => NOTIFICATIONX_ADMIN_URL . 'images/extensions/themes/elearning/elearning-theme-8.png',
                'image_shape' => 'circle',
            ),
            'conv-theme-nine' => array(
                'is_pro' => true,
                'source' => NOTIFICATIONX_ADMIN_URL . 'images/extensions/themes/elearning/elearning-theme-9.png',
                'image_shape' => 'circle',
            ),
            'maps_theme' => array(
                'is_pro' => true,
                'source' => NOTIFICATIONX_ADMIN_URL . 'images/extensions/themes/elearning/maps-theme.png',
                'image_shape' => 'square',
                'show_notification_image' => 'maps_image',
            ),
        ];
        $this->templates = [
            'elearning_template_new' => [
                'first_param' => GlobalFields::get_instance()->common_name_fields(),
                'third_param' => [
                    'tag_course_title'       => __('Course Title', 'notificationx'),
                ],
                'fourth_param' => [
                    'tag_time'       => __('Definite Time', 'notificationx'),
                ],
                '_themes' => [
                    'elearning_theme-one',
                    'elearning_theme-two',
                    'elearning_theme-three',
                    'elearning_theme-four',
                    'elearning_theme-five',
                ]
            ],
            'elearning_template_sales_count' => [
                'first_param' => GlobalFields::get_instance()->common_name_fields(),
                'third_param' => [
                    'tag_course_title' => __('Course Title', 'notificationx'),
                ],
                'fourth_param' => [
                    // 'tag_time' => __('Definite Time', 'notificationx'),
                ],
                '_themes' => [
                    'elearning_conv-theme-seven',
                    'elearning_conv-theme-eight',
                    'elearning_conv-theme-nine',
                ]
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
        add_filter('nx_link_types', [$this, 'link_types']);
        add_filter('nx_content_fields', [$this, 'content_fields']);
        add_filter('nx_type_trigger', [$this, 'type_trigger'], 20);
    }

    /**
     * Get themes for the extension.
     *
     *
     * @param array $args Settings arguments.
     * @return mixed
     */
    public function type_trigger($triggers) {
        $triggers[$this->id]['link_type'] = "@link_type:course_page";
        return $triggers;
    }

    /**
     * Needed content fields
     * @return array
     */
    public function content_fields($fields) {
        $content_fields = &$fields['content']['fields'];
        $content_fields['ld_product_control'] = array(
            'name'     => 'ld_product_control',
            'label'    => __('Show Notification Of', 'notificationx'),
            'type'     => 'select',
            'priority' => 200,
            'default'  => 'none',
            'options'  => GlobalFields::get_instance()->normalize_fields(array(
                'none'      => __('All', 'notificationx'),
                'ld_course' => __('By Course', 'notificationx'),
            )),
            'rules'       => Rules::is('type', $this->id),
        );

        $content_fields['ld_course_list'] = array(
            'name'     => 'ld_course_list',
            'label'    => __('Select Course', 'notificationx'),
            'type'     => 'select',
            'multiple' => true,
            'priority' => 201,
            'options'  => apply_filters('nx_elearning_course_list', []),
            'rules'       => Rules::logicalRule([
                Rules::is('type', $this->id),
                Rules::is('ld_product_control', 'ld_course'),
            ]),
        );

        return $fields;
    }


    /**
     * Adds option to Link Type field in Content tab.
     *
     * @param array $options
     * @return array
     */
    public function link_types($options) {
        $_options = GlobalFields::get_instance()->normalize_fields([
            'course_page' => __('Course Page', 'notificationx'),
        ], 'type', $this->id);

        $this->has_link_types = true;
        return array_merge($options, $_options);
    }

}
