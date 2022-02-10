<?php

namespace OXI_IMAGE_HOVER_PLUGINS\Modules\Caption\Admin;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Description of Effects14
 *
 * @author biplo
 */
use OXI_IMAGE_HOVER_PLUGINS\Modules\Caption\Modules as Modules;
use OXI_IMAGE_HOVER_PLUGINS\Classes\Controls as Controls;

class Effects15 extends Modules {

    public function register_effects() {
        return $this->add_control(
                        'image_hover_effects', $this->style, [
                    'label' => __('Effects Direction', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::SELECT,
                    'default' => '',
                    'options' => [
                        'oxi-image-lightspeed-in-left' => __('Lightspeed In Left', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-image-lightspeed-in-right' => __('Lightspeed In Right', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-image-lightspeed-out-left' => __('Lightspeed Out Left', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-image-lightspeed-out-right' => __('Lightspeed Out Right', OXI_IMAGE_HOVER_TEXTDOMAIN),
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
