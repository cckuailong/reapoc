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

class Effects9 extends Modules {

    public function register_effects() {
        return $this->add_control(
                        'image_hover_effects', $this->style, [
                    'label' => __('Effects Direction', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::SELECT,
                    'default' => '',
                    'options' => [
                        'oxi-image-fade-in-up' => __('Fade In Up', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-image-fade-in-down' => __('Fade In Down', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-image-fade-in-left' => __('Fade In Left', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-image-fade-in-right' => __('Fade In Right', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    ],
                    'selector' => [
                        '{{WRAPPER}} .oxi-image-caption-hover' => '',
                    ]
                        ]
        );
    }

}
