<?php

namespace OXI_IMAGE_HOVER_PLUGINS\Modules\Caption\Admin;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Description of Effects26
 *
 * @author biplo
 */
use OXI_IMAGE_HOVER_PLUGINS\Modules\Caption\Modules as Modules;
use OXI_IMAGE_HOVER_PLUGINS\Classes\Controls as Controls;

class Effects26 extends Modules {

    public function register_effects() {
        return $this->add_control(
                        'image_hover_effects', $this->style, [
                    'label' => __('Effects Direction', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::SELECT,
                    'default' => '',
                    'options' => [
                        'oxi-image-splash-up' => __('Splash Up', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-image-splash-down' => __('Splash Down', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-image-splash-left' => __('Splash Left', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-image-splash-right' => __('Splash Right', OXI_IMAGE_HOVER_TEXTDOMAIN),
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
