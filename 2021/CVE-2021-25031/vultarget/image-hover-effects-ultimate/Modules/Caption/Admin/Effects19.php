<?php

namespace OXI_IMAGE_HOVER_PLUGINS\Modules\Caption\Admin;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Description of Effects19
 *
 * @author biplo
 */
use OXI_IMAGE_HOVER_PLUGINS\Modules\Caption\Modules as Modules;
use OXI_IMAGE_HOVER_PLUGINS\Classes\Controls as Controls;

class Effects19 extends Modules {

    public function register_effects() {
        return $this->add_control(
                        'image_hover_effects', $this->style, [
                    'label' => __('Effects Direction', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::SELECT,
                    'default' => '',
                    'options' => [
                        'oxi-image-pixel-up' => __('Pixel Up', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-image-pixel-down' => __('Pixel Down', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-image-pixel-left' => __('Pixel Left', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-image-pixel-right' => __('Pixel Right', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-image-pixel-top-left' => __('Pixel Top Left', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-image-pixel-top-right' => __('Pixel Top Right', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-image-pixel-bottom-left' => __('Pixel Bottom Left', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-image-pixel-bottom-right' => __('Pixel Bottom Right', OXI_IMAGE_HOVER_TEXTDOMAIN),
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
