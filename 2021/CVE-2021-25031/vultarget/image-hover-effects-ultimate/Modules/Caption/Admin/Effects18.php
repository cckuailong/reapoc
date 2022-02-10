<?php

namespace OXI_IMAGE_HOVER_PLUGINS\Modules\Caption\Admin;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Description of Effects18
 *
 * @author biplo
 */
use OXI_IMAGE_HOVER_PLUGINS\Modules\Caption\Modules as Modules;
use OXI_IMAGE_HOVER_PLUGINS\Classes\Controls as Controls;

class Effects18 extends Modules {

    public function register_effects() {
        return $this->add_control(
                        'image_hover_effects', $this->style, [
                    'label' => __('Effects Direction', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::SELECT,
                    'default' => '',
                    'options' => [
                        'oxi-image-pivot-in-top-left' => __('Pivot In Top Left', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-image-pivot-in-top-right' => __('Pivot In Top Right', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-image-pivot-in-bottom-left' => __('Pivot In Bottom Left', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-image-pivot-in-bottom-right' => __('Pivot In Bottom Right', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-image-pivot-out-top-left' => __('Pivot Out Top Left', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-image-pivot-out-top-right' => __('Pivot Out Top Right', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-image-pivot-out-bottom-left' => __('Pivot Out Bottom Left', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-image-pivot-out-bottom-right' => __('Pivot Out Bottom Right', OXI_IMAGE_HOVER_TEXTDOMAIN),
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
