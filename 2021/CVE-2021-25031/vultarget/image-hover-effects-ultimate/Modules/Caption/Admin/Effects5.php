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

class Effects5 extends Modules {

    public function register_effects() {
        return $this->add_control(
                        'image_hover_effects', $this->style, [
                    'label' => __('Effects Direction', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::SELECT,
                    'default' => '',
                    'options' => [
                        'oxi-image-bounce-in' => __('Bounce In', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-image-bounce-in-up' => __('Bounce In Up', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-image-bounce-in-down' => __('Bounce In Down', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-image-bounce-in-left' => __('Bounce In Left', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-image-bounce-in-right' => __('Bounce In Right', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-image-bounce-out' => __('Bounce Out', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-image-bounce-out-up' => __('Bounce Out Up', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-image-bounce-out-down' => __('Bounce Out Down', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-image-bounce-out-left' => __('Bounce Out Left', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-image-bounce-out-right' => __('Bounce Out Right', OXI_IMAGE_HOVER_TEXTDOMAIN),
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
