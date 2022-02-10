<?php

namespace OXI_IMAGE_HOVER_PLUGINS\Modules\Caption\Admin;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Description of Effects25
 *
 * @author biplo
 */
use OXI_IMAGE_HOVER_PLUGINS\Modules\Caption\Modules as Modules;
use OXI_IMAGE_HOVER_PLUGINS\Classes\Controls as Controls;

class Effects25 extends Modules {

    public function register_effects() {
        return $this->add_control(
                        'image_hover_effects', $this->style, [
                    'label' => __('Effects Direction', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::SELECT,
                    'default' => '',
                    'options' => [
                        'oxi-image-slide-up' => __('Slide Up', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-image-slide-down' => __('Slide Down', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-image-slide-left' => __('Slide Left', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-image-slide-right' => __('Slide Right', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-image-slide-top-left' => __('Slide Top Left', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-image-slide-top-right' => __('Slide Top Right', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-image-slide-bottom-left' => __('Slide Bottom Left', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-image-slide-bottom-right' => __('Slide Bottom Right', OXI_IMAGE_HOVER_TEXTDOMAIN),
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
