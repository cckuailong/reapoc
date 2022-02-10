<?php

namespace OXI_IMAGE_HOVER_PLUGINS\Modules\Button\Admin;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Description of Effects6
 *
 * @author biplo
 */
use OXI_IMAGE_HOVER_PLUGINS\Modules\Button\Modules as Modules;
use OXI_IMAGE_HOVER_PLUGINS\Classes\Controls as Controls;

class Effects6 extends Modules {

    public function register_effects() {
        return $this->add_control(
                        'image_hover_effects', $this->style, [
                    'label' => __('Effects Direction', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::SELECT,
                    'default' => 'left_to_right',
                    'options' => [
                        'left_to_right' => __('Left to Right', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'right_to_left' => __('Right to Left', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'top_to_bottom' => __('Top to Bottom', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'bottom_to_top' => __('Bottom to Top', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    ],
                    'selector' => [
                        '{{WRAPPER}} .oxi-image-hover-figure' => '',
                    ],
                    'simpledescription' => 'Allows you to Set Effects Direction.',
                    'description' => 'Allows you to Set Effects Direction.',
                        ]
        );
    }

}
