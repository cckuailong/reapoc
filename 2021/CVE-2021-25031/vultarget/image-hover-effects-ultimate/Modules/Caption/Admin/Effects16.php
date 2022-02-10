<?php

namespace OXI_IMAGE_HOVER_PLUGINS\Modules\Caption\Admin;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Description of Effects16
 *
 * @author biplo
 */
use OXI_IMAGE_HOVER_PLUGINS\Modules\Caption\Modules as Modules;
use OXI_IMAGE_HOVER_PLUGINS\Classes\Controls as Controls;

class Effects16 extends Modules {

    public function register_effects() {
        return $this->add_control(
                        'image_hover_effects', $this->style, [
                    'label' => __('Effects Direction', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::SELECT,
                    'default' => '',
                    'options' => [
                        'oxi-image-modal-slide-up' => __('Modal Slide Up', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-image-modal-slide-down' => __('Modal Slide Down', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-image-modal-slide-left' => __('Modal Slide Left', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-image-modal-slide-right' => __('Modal Slide Right', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-image-modal-hinge-up' => __('Modal Hinge Up', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-image-modal-hinge-down' => __('Modal Hinge Down', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-image-modal-hinge-left' => __('Modal Hinge Left', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-image-modal-hinge-right' => __('Modal Hinge Right', OXI_IMAGE_HOVER_TEXTDOMAIN),
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
