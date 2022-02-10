<?php

namespace OXI_IMAGE_HOVER_PLUGINS\Modules\Caption\Admin;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Description of Effects3
 *
 * @author biplo
 */
use OXI_IMAGE_HOVER_PLUGINS\Modules\Caption\Modules as Modules;
use OXI_IMAGE_HOVER_PLUGINS\Classes\Controls as Controls;

class Effects4 extends Modules {

    public function register_effects() {
        return $this->add_control(
                        'image_hover_effects', $this->style, [
                    'label' => __('Effects Direction', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::SELECT,
                    'default' => '',
                    'options' => [
                        'oxi-image-border-reveal' => __('Border Reveal', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-image-border-reveal-vertical' => __('Border Reveal Vertical', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-image-border-reveal-horizontal' => __('Border Reveal Horizontal', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-image-border-reveal-corners-1' => __('Border Reveal Corners One', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-image-border-reveal-corners-2' => __('Border Reveal Corners Two', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-image-border-reveal-top-left' => __('Border Reveal Top Left', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-image-border-reveal-top-right' => __('Border Reveal Top Right', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-image-border-reveal-bottom-left' => __('Border Reveal Bottom Left', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-image-border-reveal-bottom-right' => __('Border Reveal Bottom Right', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-image-border-reveal-cc-1' => __('Border Reveal CC One', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-image-border-reveal-ccc-1' => __('Border Reveal CCC One', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-image-border-reveal-cc-2' => __('Border Reveal CC Two', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-image-border-reveal-ccc-2' => __('Border Reveal CCC Two', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-image-border-reveal-cc-3' => __('Border Reveal CC Three', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'oxi-image-border-reveal-ccc-3' => __('Border Reveal CCC Three', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    ],
                    'selector' => [
                        '{{WRAPPER}} .oxi-image-caption-hover' => '',
                    ],
                    'simpledescription' => 'Allows you to Set Effects Direction.',
                    'description' => 'Allows you to Set Effects Direction.',
                        ]
        );
    }

    public function register_content_settings() {
        $this->start_controls_section(
                'oxi-image-hover', [
            'label' => esc_html__('General Settings', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'showing' => TRUE,
                ]
        );
        $this->add_control(
                'oxi-image-hover-background', $this->style, [
            'label' => __('Color', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'type' => Controls::COLOR,
            'oparetor' => true,
            'default' => 'rgba(9, 124, 219, 1)',
            'selector' => [
                '{{WRAPPER}} .oxi-image-caption-hover,
                {{WRAPPER}} .oxi-image-caption-hover:before,
                {{WRAPPER}} .oxi-image-caption-hover:after,
                {{WRAPPER}} .oxi-image-caption-hover .oxi-image-hover-figure,
                {{WRAPPER}} .oxi-image-caption-hover .oxi-image-hover-figure:before,
                {{WRAPPER}} .oxi-image-caption-hover .oxi-image-hover-figure:after,
                {{WRAPPER}} .oxi-image-caption-hover .oxi-image-hover-figure-caption,
                {{WRAPPER}} .oxi-image-caption-hover .oxi-image-hover-figure-caption:before,
                {{WRAPPER}} .oxi-image-caption-hover .oxi-image-hover-figure-caption:after' => 'background-color: {{VALUE}};',
            ],
            'simpledescription' => 'Customize Hover Background with transparent options.',
            'description' => 'Customize Hover Background with transparent options.',
                ]
        );
        $this->add_control(
                'oxi-image-hover-content-alignment', $this->style, [
            'label' => __('Content Alignment', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'type' => Controls::SELECT,
            'default' => 'image-hover-align-center-center',
            'options' => [
                'image-hover-align-top-left' => __('Top Left', OXI_IMAGE_HOVER_TEXTDOMAIN),
                'image-hover-align-top-center' => __('Top Center', OXI_IMAGE_HOVER_TEXTDOMAIN),
                'image-hover-align-top-right' => __('Top Right', OXI_IMAGE_HOVER_TEXTDOMAIN),
                'image-hover-align-center-left' => __('Center Left', OXI_IMAGE_HOVER_TEXTDOMAIN),
                'image-hover-align-center-center' => __('Center Center', OXI_IMAGE_HOVER_TEXTDOMAIN),
                'image-hover-align-center-right' => __('Center Right', OXI_IMAGE_HOVER_TEXTDOMAIN),
                'image-hover-align-bottom-left' => __('Bottom Left', OXI_IMAGE_HOVER_TEXTDOMAIN),
                'image-hover-align-bottom-center' => __('Bottom Center', OXI_IMAGE_HOVER_TEXTDOMAIN),
                'image-hover-align-bottom-right' => __('Bottom Right', OXI_IMAGE_HOVER_TEXTDOMAIN),
            ],
            'selector' => [
                '{{WRAPPER}} .oxi-image-hover-caption-tab' => '',
            ],
            'simpledescription' => 'Customize Content Aginment as Top, Bottom, Left or Center.',
            'description' => 'Customize Content Aginment as Top, Bottom, Left or Center.',
                ]
        );
        $this->start_controls_tabs(
                'image-hover-content-start-tabs',
                [
                    'options' => [
                        'normal' => esc_html__('Normal ', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'hover' => esc_html__('Hover ', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    ]
                ]
        );
        $this->start_controls_tab();
        $this->add_group_control(
                'oxi-image-hover-boxshadow', $this->style, [
            'type' => Controls::BOXSHADOW,
            'selector' => [
                '{{WRAPPER}} .oxi-image-caption-hover' => '',
            ],
            'description' => 'Box Shadow property attaches one or more shadows into Image shape.',
                ]
        );
        $this->end_controls_tab();
        $this->start_controls_tab();
        $this->add_group_control(
                'oxi-image-hover-hover-boxshadow', $this->style, [
            'type' => Controls::BOXSHADOW,
            'selector' => [
                '{{WRAPPER}} .oxi-image-caption-hover:hover,'
                . '{{WRAPPER}} .oxi-image-caption-hover.oxi-touch' => '',
            ],
            'description' => 'Allows you at hover to attaches one or more shadows into Image shape.',
                ]
        );
        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->add_responsive_control(
                'oxi-image-hover-padding', $this->style, [
            'label' => __('Padding', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'type' => Controls::DIMENSIONS,
            'simpledimensions' => 'double',
            'separator' => TRUE,
            'default' => [
                'unit' => 'px',
                'size' => '',
            ],
            'range' => [
                'px' => [
                    'min' => 0,
                    'max' => 500,
                    'step' => 1,
                ],
                '%' => [
                    'min' => 0,
                    'max' => 100,
                    'step' => 1,
                ],
                'em' => [
                    'min' => 0,
                    'max' => 100,
                    'step' => .1,
                ],
            ],
            'selector' => [
                '{{WRAPPER}} .oxi-image-hover-caption-tab' => 'padding:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
            'simpledescription' => 'Padding used to generate space around an Image Hover content.',
            'description' => 'Padding used to generate space around an Image Hover content.',
                ]
        );
        $this->end_controls_section();
    }

}
