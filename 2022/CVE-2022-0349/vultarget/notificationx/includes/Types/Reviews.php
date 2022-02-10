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

/**
 * Extension Abstract for all Extension.
 */
class Reviews extends Types {
    /**
     * Instance of Admin
     *
     * @var Admin
     */
    use GetInstance;

    public $priority = 15;
    public $themes = [];
    public $module = [
        'modules_wordpress',
        'modules_woocommerce',
        'modules_reviewx',
        'modules_zapier',
        'modules_freemius',
    ];
    public $default_source    = 'wp_reviews';
    public $default_theme = 'reviews_total-rated';


    /**
     * Initially Invoked when initialized.
     */
    public function __construct() {
        $this->id    = 'reviews';
        $this->title = __('Reviews', 'notificationx');
        $this->themes = [
            'total-rated'     => [
                'source'                => NOTIFICATIONX_ADMIN_URL . 'images/extensions/themes/wporg/total-rated.png',
                'image_shape' => 'square',
                'template'  => [
                    'first_param'         => 'tag_rated',
                    'custom_first_param'  => __('Someone', 'notificationx'),
                    'second_param'        => __('people rated', 'notificationx'),
                    'third_param'         => 'tag_plugin_name',
                    'fourth_param'        => 'tag_rating',
                    'custom_fourth_param' => __('Some time ago', 'notificationx'),
                ]
            ],
            'reviewed'     => [
                'source'                => NOTIFICATIONX_ADMIN_URL . 'images/extensions/themes/wporg/reviewed.png',
                'image_shape' => 'circle',
                'template'  => [
                    'first_param'         =>  'tag_username',
                    'custom_first_param'  => __('Someone', 'notificationx'),
                    'second_param'        => __('just reviewed', 'notificationx'),
                    'third_param'         => 'tag_plugin_name',
                    'fourth_param'        => 'tag_rating',
                    'custom_fourth_param' => __('Some time ago', 'notificationx'),
                ]
            ],
            'review_saying' => [
                'source'               => NOTIFICATIONX_ADMIN_URL . 'images/extensions/themes/wporg/saying-review.png',
                'image_shape' => 'circle',
                'template'  => [
                    'first_param'         => 'tag_username',
                    'custom_first_param'  => __('Someone', 'notificationx'),
                    'second_param'        => __('saying', 'notificationx'),
                    'third_param'         => 'tag_title',
                    'custom_third_param'  => __('Excellent', 'notificationx'),
                    'review_fourth_param' => __('about', 'notificationx'),
                    'fifth_param'         => 'tag_plugin_name',
                    'sixth_param'         => 'tag_custom',
                    'custom_sixth_param'  => __('Try it now', 'notificationx'),
                ]
            ],
            'review-comment' => [
                'source'                => NOTIFICATIONX_ADMIN_URL . 'images/extensions/themes/wporg/review-with-comment.jpg',
                'image_shape' => 'rounded',
                'template'  => [
                    'first_param'         => 'tag_username',
                    'custom_first_param'  => __('Someone', 'notificationx'),
                    'second_param'        => __('just reviewed', 'notificationx'),
                    'third_param'         => 'tag_plugin_review',
                    'fourth_param'        => 'tag_rating',
                    'custom_fourth_param' => __('Some time ago', 'notificationx'),
                ]
            ],
            'review-comment-2' => [
                'source'                => NOTIFICATIONX_ADMIN_URL . 'images/extensions/themes/wporg/review-with-comment-2.jpg',
                'template'  => [
                    'first_param'         => 'tag_username',
                    'custom_first_param'  => __('Someone', 'notificationx'),
                    'second_param'        => __('just reviewed', 'notificationx'),
                    'third_param'         => 'tag_plugin_review',
                    'fourth_param'        => 'tag_rating',
                    'custom_fourth_param' => __('Some time ago', 'notificationx'),
                ]
            ],
            'review-comment-3' => [
                'source'                => NOTIFICATIONX_ADMIN_URL . 'images/extensions/themes/wporg/review-with-comment-3.jpg',
                'image_shape' => 'circle',
                'template'  => [
                    'first_param'         => 'tag_username',
                    'custom_first_param'  => __('Someone', 'notificationx'),
                    'second_param'        => __('just reviewed', 'notificationx'),
                    'third_param'         => 'tag_plugin_review',
                    'fourth_param'        => 'tag_time',
                    'custom_fourth_param' => __('Some time ago', 'notificationx'),
                ]
            ],
        ];

        $this->templates = [
            'wp_reviews_template_new'  => [
                'first_param' => [
                    'tag_username' => __('Username', 'notificationx'),
                    'tag_rated'    => __('Rated', 'notificationx'),
                ],
                'third_param' => [
                    'tag_plugin_name'     => __('Plugin Name', 'notificationx'),
                    'tag_plugin_review'   => __('Review', 'notificationx'),
                    'tag_anonymous_title' => __('Anonymous Title', 'notificationx'),
                ],
                'fourth_param' => [
                    'tag_rating'   => __('Rating', 'notificationx'),
                    'tag_time'     => __('Definite Time', 'notificationx'),
                ],
                '_themes' => [
                    'reviews_total-rated',
                    'reviews_reviewed',
                    'reviews_review-comment',
                    'reviews_review-comment-2',
                    'reviews_review-comment-3',
                ],
            ],
            'review_saying_template_new' => [
                'first_param' => [
                    'tag_username' => __('Username', 'notificationx'),
                ],
                'third_param' => [
                    'tag_title'           => __('Review Title', 'notificationx'),
                    'tag_anonymous_title' => __('Anonymous Title', 'notificationx'),
                ],
                'fifth_param' => [
                    'tag_plugin_name' => __('Plugin Name', 'notificationx'),
                ],
                'sixth_param' => [
                    // @todo maybe add some predefined texts.
                ],
                '_themes' => [
                    'reviews_review_saying',
                ],
            ],
        ];
        add_filter('nx_link_types', [$this, 'link_types']);
        parent::__construct();
    }

    public function init() {
        parent::init();
        add_filter("nx_filtered_entry_{$this->id}", array($this, 'conversion_data'), 11, 2);
    }

    /**
     * Hooked to nx_before_metabox_load action.
     *
     * @return void
     */
    public function init_fields() {
        parent::init_fields();
        add_filter('nx_notification_template', [$this, 'review_templates'], 7);
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
        $triggers[$this->id]['link_type'] = "@link_type:product_page";
        return $triggers;
    }

    /**
     * Adds option to Link Type field in Content tab.
     *
     * @param array $options
     * @return array
     */
    public function link_types($options) {
        $_options = GlobalFields::get_instance()->normalize_fields([
            'review_page' => __('Product Page', 'notificationx'),
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
    public function review_templates($template) {
        $template["review_fourth_param"] = [
            // 'label'     => __("Review Fourth Parameter", 'notificationx'),
            'name'      => "review_fourth_param",                            // changed name from "conversion_size"
            'type'      => "text",
            'priority'  => 27,
            'default'   => __('About', 'notificationx'),
            'rules' => Rules::includes('themes', 'reviews_review_saying'),
        ];
        return $template;
    }
    /**
     * This method is an implementable method for All Extension coming forward.
     *
     * @param array $args Settings arguments.
     * @return mixed
     */
    public function content_trim_length_dependency($dependency) {
        $dependency[] = 'reviews_review-comment';
        $dependency[] = 'reviews_review-comment-2';
        $dependency[] = 'reviews_review-comment-3';
        $dependency[] = 'woo_reviews_review-comment';
        $dependency[] = 'woo_reviews_review-comment-2';
        $dependency[] = 'woo_reviews_review-comment-3';
        $dependency[] = 'reviewx_review-comment';
        $dependency[] = 'reviewx_review-comment-2';
        $dependency[] = 'reviewx_review-comment-3';
        return $dependency;
    }

    // @todo frontend
    public function conversion_data($saved_data, $settings) {
        if(empty($saved_data['content']) && !empty($saved_data['plugin_review'])){
            $saved_data['content'] = $saved_data['plugin_review'];
        }
        if (!empty($saved_data['content'])) {
            $trim_length = 100;
            if ($settings['themes'] == 'reviews_review-comment-3' || $settings['themes'] == 'reviews_review-comment-3') {
                $trim_length = 80;
            }
            $nx_trimmed_length = apply_filters('nx_text_trim_length', $trim_length, $settings);
            $review_content = $saved_data['content'];
            if (strlen($review_content) > $nx_trimmed_length) {
                $review_content = substr($review_content, 0, $nx_trimmed_length) . '...';
            }
            if ($settings['themes'] == 'reviews_review-comment-2') { // || $settings['theme'] == 'comments_theme-six-free'
                $review_content = '" ' . $review_content . ' "';
            }
            $saved_data['plugin_review'] = $review_content;
        }
        if(empty($saved_data['title']))
            $saved_data['title'] = isset($saved_data['post_title']) ? $saved_data['post_title'] : '';

        return $saved_data;
    }
}
