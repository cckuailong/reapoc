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

class Effects10 extends Modules {

    public function register_effects() {
        return $this->add_control(
                        'image_hover_effects', $this->style, [
                    'label' => __('Effects Direction', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::SELECT,
                    'default' => '',
                    'options' => [
                        'oxi-image-fall-away-horizontal' => __('Fall Away Horizontal', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-image-fall-away-vertical' => __('Fall Away Vertical', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-image-fall-away-cc' => __('Fall Away CC', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-image-fall-away-ccc' => __('Fall Away CCC', OXI_IMAGE_HOVER_TEXTDOMAIN),
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
