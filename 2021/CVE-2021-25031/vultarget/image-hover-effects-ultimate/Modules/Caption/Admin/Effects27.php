<?php

namespace OXI_IMAGE_HOVER_PLUGINS\Modules\Caption\Admin;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Description of Effects27
 *
 * @author biplo
 */
use OXI_IMAGE_HOVER_PLUGINS\Modules\Caption\Modules as Modules;
use OXI_IMAGE_HOVER_PLUGINS\Classes\Controls as Controls;

class Effects27 extends Modules {

    public function register_effects() {
        return $this->add_control(
                        'image_hover_effects', $this->style, [
                    'label' => __('Effects Direction', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::SELECT,
                    'default' => '',
                    'options' => [
                        'oxi-image-stack-up' => __('Stack Up', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-image-stack-down' => __('Stack Down', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-image-stack-left' => __('Stack Left', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-image-stack-right' => __('Stack Right', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-image-stack-top-left' => __('Stack Top Left', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-image-stack-top-right' => __('Stack Top Right', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-image-stack-bottom-left' => __('Stack Bottom Left', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-image-stack-bottom-right' => __('Stack Bottom Right', OXI_IMAGE_HOVER_TEXTDOMAIN),
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
