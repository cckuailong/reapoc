<?php

namespace OXI_IMAGE_HOVER_PLUGINS\Modules\General\Admin;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Description of Effects1
 *
 * @author biplo
 */
use OXI_IMAGE_HOVER_PLUGINS\Modules\General\Modules as Modules;
use OXI_IMAGE_HOVER_PLUGINS\Classes\Controls as Controls;

class Effects11 extends Modules {

    public function register_effects() {
        return $this->add_control(
                        'image_hover_effects', $this->style, [
                    'label' => __('Effects Direction', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::SELECT,
                    'default' => 'left_to_right',
                    'options' => [
                        'top_to_bottom' => __('Top To Bottom', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'bottom_to_top' => __('Bottom To Top', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'left_to_right' => __('Left To Right', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'right_to_left' => __('Right To Left', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    ],
                    'selector' => [
                        '{{WRAPPER}} .oxi-image-hover-figure' => '',
                    ],
                    'simpledescription' => 'Allows you to Set Effects Direction.',
                    'description' => 'Allows you to Set Effects Direction.',
                        ]
        );
    }

    public function register_effects_time() {
        $this->add_control(
                'oxi-image-hover-effects-time', $this->style, [
            'label' => __('Effects Time (S)', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'type' => Controls::SLIDER,
            'simpleenable' => false,
            'default' => [
                'unit' => 'ms',
                'size' => '',
            ],
            'range' => [
                'ms' => [
                    'min' => 0.0,
                    'max' => 5000,
                    'step' => 1,
                ],
                's' => [
                    'min' => 0.0,
                    'max' => 5,
                    'step' => 0.01,
                ],
            ],
            'selector' => [
                '{{WRAPPER}} .oxi-image-hover-style *,{{WRAPPER}} .oxi-image-hover-style *:before,{{WRAPPER}} .oxi-image-hover-style *:after' => '-webkit-transition: all {{SIZE}}{{UNIT}} ease-in-out; -moz-transition: all {{SIZE}}{{UNIT}} ease-in-out; transition: all {{SIZE}}{{UNIT}} ease-in-out;',
                '{{WRAPPER}} .oxi-image-general-hover-style-11 .oxi-image-hover-figure .oxi-image-hover-figure-caption' => '-webkit-transition: all {{SIZE}}{{UNIT}} ease-in-out {{SIZE}}{{UNIT}} ; -moz-transition: all {{SIZE}}{{UNIT}} ease-in-out {{SIZE}}{{UNIT}} ; transition: all {{SIZE}}{{UNIT}} ease-in-out {{SIZE}}{{UNIT}} ;',
            ],
            'description' => 'Set Effects Durations as How long you want to run Effects. Options available with Second or Milisecond.',
                ]
        );
    }

}
