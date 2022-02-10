<?php

namespace OXI_IMAGE_HOVER_PLUGINS\Modules\Caption\Admin;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Description of Effects28
 *
 * @author biplo
 */
use OXI_IMAGE_HOVER_PLUGINS\Modules\Caption\Modules as Modules;
use OXI_IMAGE_HOVER_PLUGINS\Classes\Controls as Controls;

class Effects28 extends Modules {

    public function register_effects() {
        return $this->add_control(
                        'image_hover_effects', $this->style, [
                    'label' => __('Effects Direction', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::SELECT,
                    'default' => '',
                    'options' => [
                        'oxi-image-strip-shutter-up' => __('Strip Shutter Up', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-image-strip-shutter-down' => __('Strip Shutter Down', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-image-strip-shutter-left' => __('Strip Shutter Left', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-image-strip-shutter-right' => __('Strip Shutter Right', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-image-strip-horizontal-up' => __('Strip Horizontal Up', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-image-strip-horizontal-down' => __('Strip Horizontal Down', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-image-strip-horizontal-top-left' => __('Strip Horizontal Top Left', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-image-strip-horizontal-top-right' => __('Strip Horizontal Top Right', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-image-strip-horizontal-left' => __('Strip Horizontal Left', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-image-strip-horizontal-right' => __('Strip Horizontal Right', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-image-strip-vertical-left' => __('Strip Vertical Left', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-image-strip-vertical-right' => __('Strip Vertical Right', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-image-strip-vertical-top-left' => __('Strip Vertical Top Left', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-image-strip-vertical-top-right' => __('Strip Vertical Top Right', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-image-strip-vertical-bottom-left' => __('Strip Vertical Bottom Left', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-image-strip-vertical-bottom-right' => __('Strip Vertical Bottom Right', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    ],
                    'selector' => [
                        '{{WRAPPER}} .oxi-image-caption-hover' => '',
                    ],
                    'simpledescription' => 'Allows you to Set Effects Direction.',
                    'description' => 'Allows you to Set Effects Direction.',
                        ]
        );
    }

}
