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

class Effects6 extends Modules {

    public function register_effects() {
        return $this->add_control(
                        'image_hover_effects', $this->style, [
                    'label' => __('Effects Direction', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::SELECT,
                    'default' => '',
                    'options' => [
                        'oxi-image-circle-up' => __('Circle Up', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-image-circle-down' => __('Circle Down', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-image-circle-left' => __('Circle Left', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-image-circle-right' => __('Circle Right', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-image-circle-top-left' => __('Circle Top Left', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-image-circle-top-right' => __('Circle Top Right', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-image-circle-bottom-left' => __('Circle Bottom Left', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-image-circle-bottom-right' => __('Circle Bottom Right', OXI_IMAGE_HOVER_TEXTDOMAIN),
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
