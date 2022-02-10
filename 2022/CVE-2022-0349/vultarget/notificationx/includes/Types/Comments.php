<?php

/**
 * Extension Abstract
 *
 * @package NotificationX\Extensions
 */

namespace NotificationX\Types;

use NotificationX\Extensions\GlobalFields;
use NotificationX\GetInstance;
use NotificationX\Modules;
use NotificationX\NotificationX;

/**
 * Extension Abstract for all Extension.
 */
class Comments extends Types {
    /**
     * Instance of Admin
     *
     * @var Admin
     */
    use GetInstance;
    public $priority       = 10;
    public $module         = ['modules_wordpress'];
    public $id             = 'comments';
    public $default_source = 'wp_comments';
    public $default_theme  = 'comments_theme-one';

    /**
     * Initially Invoked when initialized.
     */
    public function __construct() {
        $this->title = __('Comments', 'notificationx');

        // nx_comment_colored_themes
        $this->themes = [
            'theme-one'        => [
                'source'  => NOTIFICATIONX_ADMIN_URL . 'images/extensions/themes/nx-comment-theme-2.jpg',
                'image_shape' => 'circle',
                'template'  => [
                    'first_param'         => 'tag_name',
                    'custom_first_param'  => __('Someone', 'notificationx'),
                    'second_param'        => __('commented on', 'notificationx'),
                    'third_param'         => 'tag_post_title',
                    'custom_third_param'  => __('Anonymous Post', 'notificationx'),
                    'fourth_param'        => 'tag_time',
                    'custom_fourth_param' => __('Some time ago', 'notificationx'),
                ],
            ],
            'theme-two'        => [
                'source'  => NOTIFICATIONX_ADMIN_URL . 'images/extensions/themes/nx-comment-theme-1.jpg',
                'image_shape' => 'circle',
                'template'  => [
                    'first_param'         => 'tag_name',
                    'custom_first_param'  => __('Someone', 'notificationx'),
                    'second_param'        => __('commented on', 'notificationx'),
                    'third_param'         => 'tag_post_title',
                    'custom_third_param'  => __('Anonymous Post', 'notificationx'),
                    'fourth_param'        => 'tag_time',
                    'custom_fourth_param' => __('Some time ago', 'notificationx'),
                ],
            ],
            'theme-three'      => [
                'source'  => NOTIFICATIONX_ADMIN_URL . 'images/extensions/themes/nx-comment-theme-3.jpg',
                'image_shape' => 'square',
                'template'  => [
                    'first_param'         => 'tag_name',
                    'custom_first_param'  => __('Someone', 'notificationx'),
                    'second_param'        => __('commented on', 'notificationx'),
                    'third_param'         => 'tag_post_title',
                    'custom_third_param'  => __('Anonymous Post', 'notificationx'),
                    'fourth_param'        => 'tag_time',
                    'custom_fourth_param' => __('Some time ago', 'notificationx'),
                ],
            ],
            'theme-six-free'   => [
                'source'  => NOTIFICATIONX_ADMIN_URL . 'images/extensions/themes/nx-comment-theme-4.jpg',
                'image_shape' => 'rounded',
                'template'  => [
                    'first_param'         => 'tag_name',
                    'custom_first_param'  => __('Someone', 'notificationx'),
                    'second_param'        => __('commented on', 'notificationx'),
                    'third_param'         => 'tag_post_comment',
                    'custom_third_param'  => __('Anonymous Post', 'notificationx'),
                    'fourth_param'        => 'tag_time',
                    'custom_fourth_param' => __('Some time ago', 'notificationx'),
                ],
            ],
            'theme-seven-free' => [
                'source'  => NOTIFICATIONX_ADMIN_URL . 'images/extensions/themes/nx-comment-theme-5.jpg',
                'image_shape' => 'circle',
                'template'  => [
                    'first_param'         => 'tag_name',
                    'custom_first_param'  => __('Someone', 'notificationx'),
                    'second_param'        => __('commented on', 'notificationx'),
                    'third_param'         => 'tag_post_comment',
                    'custom_third_param'  => __('Anonymous Post', 'notificationx'),
                    'fourth_param'        => 'tag_time',
                    'custom_fourth_param' => __('Some time ago', 'notificationx'),
                ],
            ],
            'theme-eight-free' => [
                'source'  => NOTIFICATIONX_ADMIN_URL . 'images/extensions/themes/nx-comment-theme-6.jpg',
                'image_shape' => 'circle',
                'template'  => [
                    'first_param'         => 'tag_name',
                    'custom_first_param'  => __('Someone', 'notificationx'),
                    'second_param'        => __('commented on', 'notificationx'),
                    'third_param'         => 'tag_post_comment',
                    'custom_third_param'  => __('Anonymous Post', 'notificationx'),
                    'fourth_param'        => 'tag_time',
                    'custom_fourth_param' => __('Some time ago', 'notificationx'),
                ],
            ],
            'theme-four'       => array(
                'is_pro' => true,
                'source' => NOTIFICATIONX_ADMIN_URL . 'images/extensions/themes/pro/nx-comment-theme-four.png',
                'image_shape' => 'circle',
                'template'  => [
                    'first_param'         => 'tag_name',
                    'custom_first_param'  => __('Someone', 'notificationx'),
                    'second_param'        => __('commented on', 'notificationx'),
                    'third_param'         => 'tag_post_title',
                    'custom_third_param'  => __('Anonymous Post', 'notificationx'),
                    'fourth_param'        => 'tag_time',
                    'custom_fourth_param' => __('Some time ago', 'notificationx'),
                ],
            ),
            'theme-five' => array(
                'is_pro' => true,
                'source' => NOTIFICATIONX_ADMIN_URL . 'images/extensions/themes/pro/nx-comment-theme-five.png',
                'image_shape' => 'circle',
                'template'  => [
                    'first_param'         => 'tag_name',
                    'custom_first_param'  => __('Someone', 'notificationx'),
                    'second_param'        => __('commented on', 'notificationx'),
                    'third_param'         => 'tag_post_title',
                    'custom_third_param'  => __('Anonymous Post', 'notificationx'),
                    'fourth_param'        => 'tag_time',
                    'custom_fourth_param' => __('Some time ago', 'notificationx'),
                ],
            ),
            // @todo pro fix
            'maps_theme' => array(
                'is_pro' => true,
                'source' => NOTIFICATIONX_ADMIN_URL . 'images/extensions/themes/pro/maps-theme-comments.png',
                'image_shape' => 'square',
                'show_notification_image' => 'maps_image',
            ),
        ];
        $this->templates = [
            'comments_template_new' => [
                'first_param' => GlobalFields::get_instance()->common_name_fields(true),
                'third_param' => [
                    'tag_post_title'     => __('Post Title', 'notificationx'),
                    'tag_post_comment'   => __('Post Comment', 'notificationx'),
                ],
                'fourth_param' => [
                    'tag_time' => __('Definite Time', 'notificationx'),
                ],
                '_themes' => [
                    'comments_theme-one',
                    'comments_theme-two',
                    'comments_theme-three',
                    'comments_theme-six-free',
                    'comments_theme-seven-free',
                    'comments_theme-eight-free',
                    'comments_theme-four',
                    'comments_theme-five',
                ],
            ],
        ];
        parent::__construct();

        add_filter('nx_link_types', [$this, 'link_types']);
    }

    /**
     * Hooked to nx_before_metabox_load action.
     *
     * @return void
     */
    public function init_fields() {
        parent::init_fields();
        add_filter('nx_content_trim_length_dependency', [$this, 'content_trim_length_dependency']);
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
        $triggers[$this->id]['link_type'] = "@link_type:comment_url";
        return $triggers;
    }

    /**
     * Adds option to Link Type field in Content tab.
     *
     * @param array $options
     * @return array
     */
    public function link_types($options){
        $_options = GlobalFields::get_instance()->normalize_fields([
            'comment_url'      => __('Comment URL', 'notificationx'),
        ], 'type', $this->id);

        $this->has_link_types = true;
        return array_merge($options, $_options);
    }


    /**
     * This method is an implementable method for All Extension coming forward.
     *
     * @param array $args Settings arguments.
     * @return mixed
     */
    public function content_trim_length_dependency($dependency) {
        $dependency[] = 'comments_theme-six-free';
        $dependency[] = 'comments_theme-seven-free';
        $dependency[] = 'comments_theme-eight-free';
        return $dependency;
    }
}
