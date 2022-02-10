<?php

namespace OXI_IMAGE_HOVER_PLUGINS\Modules\Caption\Admin;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Description of Effects1
 *
 * @author biplo
 */
use OXI_IMAGE_HOVER_PLUGINS\Modules\Caption\Modules as Modules;
use OXI_IMAGE_HOVER_PLUGINS\Classes\Controls as Controls;

class Effects1 extends Modules {

    public function register_effects() {
        return $this->add_control(
                        'image_hover_effects', $this->style, [
                    'label' => __('Effects Direction', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::SELECT,
                    'default' => '',
                    'options' => [
                        'oxi-image-blinds-horizontal' => __('Blinds Horizontal', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-image-blinds-vertical' => __('Blinds Vertical', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-image-blinds-up' => __('Blinds Up', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-image-blinds-down' => __('Blinds Down', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-image-blinds-left' => __('Blinds Left', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-image-blinds-right' => __('Blinds Right', OXI_IMAGE_HOVER_TEXTDOMAIN),
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
