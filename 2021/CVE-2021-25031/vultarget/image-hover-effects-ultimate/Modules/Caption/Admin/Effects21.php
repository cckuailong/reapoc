<?php

namespace OXI_IMAGE_HOVER_PLUGINS\Modules\Caption\Admin;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Description of Effects21
 *
 * @author biplo
 */
use OXI_IMAGE_HOVER_PLUGINS\Modules\Caption\Modules as Modules;
use OXI_IMAGE_HOVER_PLUGINS\Classes\Controls as Controls;

class Effects21 extends Modules {

    public function register_effects() {
        return $this->add_control(
                        'image_hover_effects', $this->style, [
                    'label' => __('Effects Direction', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::SELECT,
                    'default' => '',
                    'options' => [
                        'oxi-image-reveal-up' => __('Reveal Up', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-image-reveal-down' => __('Reveal Down', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-image-reveal-left' => __('Reveal Left', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-image-reveal-right' => __('Reveal Right', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-image-reveal-top-left' => __('Reveal Top Left', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-image-reveal-top-right' => __('Reveal Top Right', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-image-reveal-bottom-left' => __('Reveal Bottom Left', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-image-reveal-bottom-right' => __('Reveal Bottom Right', OXI_IMAGE_HOVER_TEXTDOMAIN),
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
