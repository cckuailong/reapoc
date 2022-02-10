<?php

namespace OXI_IMAGE_HOVER_PLUGINS\Modules\Caption\Admin;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Description of Effects2
 *
 * @author biplo
 */
use OXI_IMAGE_HOVER_PLUGINS\Modules\Caption\Modules as Modules;
use OXI_IMAGE_HOVER_PLUGINS\Classes\Controls as Controls;

class Effects2 extends Modules {

    public function register_effects() {
        return $this->add_control(
                        'image_hover_effects', $this->style, [
                    'label' => __('Effects Direction', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::SELECT,
                    'default' => '',
                    'options' => [
                        'oxi-image-blocks-rotate-left' => __('Block Rotate Left', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-image-blocks-rotate-right' => __('Block Rotate Right', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-image-blocks-rotate-in-left' => __('Block Rotate In Left', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-image-blocks-rotate-in-right' => __('Block Rotate In Right', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-image-blocks-in' => __('Block In', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-image-blocks-out' => __('Block Out', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-image-blocks-float-up' => __('Block Float Up', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-image-blocks-float-down' => __('Block Float Down', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-image-blocks-float-left' => __('Block Float Left', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-image-blocks-float-right' => __('Block Float Right', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-image-blocks-zoom-top-left' => __('Block Zoom Top Left', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-image-blocks-zoom-top-right' => __('Block Zoom Top Right', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-image-blocks-zoom-bottom-left' => __('Block Zoom Bottom Left', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-image-blocks-zoom-bottom-right' => __('Block Zoom Bottom Right', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    ],
                    'selector' => [
                        '{{WRAPPER}} .oxi-image-caption-hover' => '',
                    ]
                        ]
        );
    }

}
