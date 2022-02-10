<?php

namespace OXI_IMAGE_HOVER_PLUGINS\Modules\Caption\Admin;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Description of Effects12
 *
 * @author biplo
 */
use OXI_IMAGE_HOVER_PLUGINS\Modules\Caption\Modules as Modules;
use OXI_IMAGE_HOVER_PLUGINS\Classes\Controls as Controls;

class Effects12 extends Modules {

    public function register_effects() {
        return $this->add_control(
                        'image_hover_effects', $this->style, [
                    'label' => __('Effects Direction', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::SELECT,
                    'default' => '',
                    'options' => [
                        'oxi-image-flip-horizontal' => __('Flip Horizontal', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-image-flip-vertical' => __('Flip Vertical', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-image-flip-diagonal-1' => __('Flip Diagoanl One', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-image-flip-diagonal-2' => __('Flip Diagoanl Two', OXI_IMAGE_HOVER_TEXTDOMAIN),
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
