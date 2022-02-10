<?php

namespace OXI_IMAGE_HOVER_PLUGINS\Modules\Caption\Admin;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Description of Effects24
 *
 * @author biplo
 */
use OXI_IMAGE_HOVER_PLUGINS\Modules\Caption\Modules as Modules;
use OXI_IMAGE_HOVER_PLUGINS\Classes\Controls as Controls;

class Effects24 extends Modules {

    public function register_effects() {
        return $this->add_control(
                        'image_hover_effects', $this->style, [
                    'label' => __('Effects Direction', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::SELECT,
                    'default' => '',
                    'options' => [
                        'oxi-image-shutter-out-horizontal' => __('Shutter Out Horizontal', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-image-shutter-out-vertical' => __('Shutter Out Vertical', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-image-shutter-out-diagonal-1' => __('Shutter Out Diagonal One', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-image-shutter-out-diagonal-2' => __('Shutter Out Diagonal Two', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-image-shutter-in-horizontal' => __('Shutter In Horizontal', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-image-shutter-in-vertical' => __('Shutter In Vertical', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-image-shutter-in-out-horizontal' => __('Shutter In Out Horizontal', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-image-shutter-in-out-vertical' => __('Shutter In Out Vertical', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-image-shutter-in-out-diagonal-1' => __('Shutter In Out Diagonal One', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-image-shutter-in-out-diagonal-2' => __('Shutter In Out Diagonal Two', OXI_IMAGE_HOVER_TEXTDOMAIN),
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
