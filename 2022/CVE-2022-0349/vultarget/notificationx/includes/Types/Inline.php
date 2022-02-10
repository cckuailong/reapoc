<?php

/**
 * Extension Abstract
 *
 * @package NotificationX\Extensions
 */

namespace NotificationX\Types;

use NotificationX\Extensions\GlobalFields;
use NotificationX\GetInstance;

/**
 * Extension Abstract for all Extension.
 */
class Inline extends Types {
    /**
     * Instance of Admin
     *
     * @var Admin
     */
    use GetInstance;
    public $priority       = 60;
    public $is_pro         = true;
    public $module         = ['modules_woocommerce'];
    public $id             = 'inline';
    public $default_source = 'woo_inline';

    /**
     * Initially Invoked when initialized.
     */
    public function __construct() {
        $this->title = __('Inline Notification', 'notificationx');
        $sales_count_template = [
            'first_param'         => 'tag_sales_count',
            'custom_first_param'  => __( 'Someone', 'notificationx' ),
            'second_param'        => __( 'people purchased', 'notificationx' ),
            'third_param'         => 'tag_product_title',
            'fourth_param'        => 'tag_7days',
            'custom_fourth_param' => __( 'in last {{day:7}}', 'notificationx' ),
        ];
        $this->themes = [
            'conv-theme-seven' => array(
                'is_pro'      => true,
                'source'      => NOTIFICATIONX_ADMIN_URL . 'images/extensions/themes/pro/inline.jpg',
                'image_shape' => 'rounded',
                'template'    => $sales_count_template,
            ),
        ];
        $this->templates = [
            'woo_template_sales_count' => [
                'first_param'  => [
                    'tag_sales_count' => __( 'Sales Count', 'notificationx' ),
                ],
                'third_param'  => [
                    'tag_product_title' => __( 'Product Title', 'notificationx' ),
                ],
                'fourth_param' => [
                    'tag_1day'   => __( 'In last 1 day', 'notificationx' ),
                    'tag_7days'  => __( 'In last 7 days', 'notificationx' ),
                    'tag_30days' => __( 'In last 30 days', 'notificationx' ),
                ],
                '_themes'      => [
                    "{$this->id}_conv-theme-six",
                    "{$this->id}_conv-theme-seven",
                    "{$this->id}_conv-theme-eight",
                    "{$this->id}_conv-theme-nine",
                ],
            ],
        ];

        // nx_comment_colored_themes
        parent::__construct();

    }

    /**
     * Hooked to nx_before_metabox_load action.
     *
     * @return void
     */
    public function init_fields() {
        parent::init_fields();
        add_filter( 'nx_show_on_exclude', array( $this, 'show_on_exclude' ), 10, 4 );

    }

    /**
     * Making sure inline notice don't show as normal notice
     * if pro is disabled.
     *
     * @param  bool $exclude
     * @param  array $settings
     * @return bool
     */
    public function show_on_exclude( $exclude, $settings ) {
        if ( ! empty( $settings['inline_location'] ) && $this->id === $settings['type'] ) {
            return true;
        }
        return $exclude;
    }
}
