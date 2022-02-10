<?php

namespace OXI_IMAGE_HOVER_PLUGINS\Modules\Square\Admin;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Description of Effects19
 *
 * @author biplo
 */
use OXI_IMAGE_HOVER_PLUGINS\Modules\Square\Modules as Modules;
use OXI_IMAGE_HOVER_PLUGINS\Classes\Controls as Controls;

class Effects19 extends Modules {

    public function register_content_settings() {
        $this->start_controls_section(
                'oxi-image-hover', [
            'label' => esc_html__('Content Settings', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'showing' => TRUE,
                ]
        );
        $this->add_control(
                'oxi-image-hover-background-color', $this->style, [
            'label' => esc_html__('Background', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'type' => Controls::GRADIENT,
            'default' => 'rgba(255, 116, 3, 1)',
            'selector' => [
                '{{WRAPPER}} .oxi-image-hover-caption-tab' => 'background:{{VALUE}};',
            ],
            'simpledescription' => 'Customize Hover Background with transparent options.',
            'description' => 'Customize Hover Background with Color or Gradient or Image properties.',
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
        $this->add_responsive_control(
                'oxi-image-hover-border-radius', $this->style, [
            'label' => __('Border Radius', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'type' => Controls::DIMENSIONS,
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
                    'max' => 50,
                    'step' => 1,
                ],
                'em' => [
                    'min' => 0,
                    'max' => 100,
                    'step' => .1,
                ],
            ],
            'selector' => [
                '{{WRAPPER}} .oxi-image-hover-figure,'
                . '{{WRAPPER}} .oxi-image-hover-figure:before,'
                . '{{WRAPPER}} .oxi-image-hover-image,'
                . '{{WRAPPER}} .oxi-image-hover-image:before,'
                . '{{WRAPPER}} .oxi-image-hover-image img,'
                . '{{WRAPPER}} .oxi-image-hover-figure-caption,'
                . '{{WRAPPER}} .oxi-image-hover-figure-caption:before,'
                . '{{WRAPPER}} .oxi-image-hover-figure-caption:after,'
                . '{{WRAPPER}} .oxi-image-hover-figure-caption .oxi-image-hover-caption-tab' => 'border-radius:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
            'simpledescription' => 'Allows you to add rounded corners to Image with options.',
            'description' => 'Allows you to add rounded corners to Image with options.',
                ]
        );
        $this->add_group_control(
                'oxi-image-hover-boxshadow', $this->style, [
            'type' => Controls::BOXSHADOW,
            'selector' => [
                '{{WRAPPER}} .oxi-image-hover-image:before' => '',
            ],
            'description' => 'Box Shadow property attaches one or more shadows into Image shape.',
                ]
        );
        $this->end_controls_tab();
        $this->start_controls_tab();
        $this->add_responsive_control(
                'oxi-image-hover-hover-border-radius', $this->style, [
            'label' => __('Border Radius', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'type' => Controls::DIMENSIONS,
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
                    'max' => 50,
                    'step' => 1,
                ],
                'em' => [
                    'min' => 0,
                    'max' => 100,
                    'step' => .1,
                ],
            ],
            'selector' => [
                '{{WRAPPER}} .oxi-image-hover:hover .oxi-image-hover-figure,'
                . '{{WRAPPER}} .oxi-image-hover.oxi-touch .oxi-image-hover-figure,'
                . '{{WRAPPER}} .oxi-image-hover:hover  .oxi-image-hover-figure:before,'
                . '{{WRAPPER}} .oxi-image-hover.oxi-touch  .oxi-image-hover-figure:before,'
                . '{{WRAPPER}} .oxi-image-hover:hover .oxi-image-hover-figure .oxi-image-hover-image,'
                . '{{WRAPPER}} .oxi-image-hover.oxi-touch .oxi-image-hover-figure .oxi-image-hover-image,'
                . '{{WRAPPER}} .oxi-image-hover:hover .oxi-image-hover-figure .oxi-image-hover-image:before,'
                . '{{WRAPPER}} .oxi-image-hover.oxi-touch .oxi-image-hover-figure .oxi-image-hover-image:before,'
                . '{{WRAPPER}} .oxi-image-hover:hover .oxi-image-hover-figure .oxi-image-hover-image img,'
                . '{{WRAPPER}} .oxi-image-hover.oxi-touch .oxi-image-hover-figure .oxi-image-hover-image img,'
                . '{{WRAPPER}} .oxi-image-hover:hover .oxi-image-hover-figure .oxi-image-hover-figure-caption,'
                . '{{WRAPPER}} .oxi-image-hover.oxi-touch .oxi-image-hover-figure .oxi-image-hover-figure-caption,'
                . '{{WRAPPER}} .oxi-image-hover:hover .oxi-image-hover-figure .oxi-image-hover-figure-caption:before,'
                . '{{WRAPPER}} .oxi-image-hover.oxi-touch .oxi-image-hover-figure .oxi-image-hover-figure-caption:before,'
                . '{{WRAPPER}} .oxi-image-hover:hover .oxi-image-hover-figure .oxi-image-hover-figure-caption:after,'
                . '{{WRAPPER}} .oxi-image-hover.oxi-touch .oxi-image-hover-figure .oxi-image-hover-figure-caption:after,'
                . '{{WRAPPER}} .oxi-image-hover:hover .oxi-image-hover-figure .oxi-image-hover-figure-caption .oxi-image-hover-caption-tab,'
                . '{{WRAPPER}} .oxi-image-hover.oxi-touch .oxi-image-hover-figure .oxi-image-hover-figure-caption .oxi-image-hover-caption-tab' => 'border-radius:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
            'simpledescription' => 'Allows you to add rounded corners at Hover to Image with options.',
            'description' => 'Allows you to add rounded corners at Hover to Image with options.',
                ]
        );
        $this->add_group_control(
                'oxi-image-hover-hover-boxshadow', $this->style, [
            'type' => Controls::BOXSHADOW,
            'selector' => [
                '{{WRAPPER}} .oxi-image-hover-figure .oxi-image-hover-figure-caption:before' => '',
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
            'separator' => TRUE,
            'simpledimensions' => 'double',
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
                '{{WRAPPER}} .oxi-image-hover-figure .oxi-image-hover-caption-tab' => 'padding:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
            'simpledescription' => 'Padding used to generate space around an Image Hover content.',
            'description' => 'Padding used to generate space around an Image Hover content.',
                ]
        );
        $this->end_controls_section();
    }

    public function register_heading_settings() {
        $this->start_controls_section(
                'oxi-image-hover', [
            'label' => esc_html__('Heading Settings', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'showing' => TRUE,
                ]
        );
        $this->add_group_control(
                'oxi-image-hover-heading-typho', $this->style, [
            'type' => Controls::TYPOGRAPHY,
            'include' => Controls::ALIGNNORMAL,
            'selector' => [
                '{{WRAPPER}} .oxi-image-hover-figure-caption .oxi-image-hover-heading' => '',
            ]
                ]
        );
        $this->add_control(
                'oxi-image-hover-heading-color', $this->style, [
            'label' => __('Color', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'type' => Controls::COLOR,
            'default' => '#ffffff',
            'selector' => [
                '{{WRAPPER}} .oxi-image-hover-figure-caption .oxi-image-hover-heading' => 'color: {{VALUE}};',
            ],
            'simpledescription' => 'Color property is used to set the color of the Heading.',
            'description' => 'Color property is used to set the color of the Heading.',
                ]
        );
        $this->add_group_control(
                'oxi-image-hover-heading-tx-shadow', $this->style, [
            'type' => Controls::TEXTSHADOW,
            'selector' => [
                '{{WRAPPER}} .oxi-image-hover-figure-caption .oxi-image-hover-heading' => '',
            ],
            'simpledescription' => 'Text Shadow property adds shadow to Heading.',
            'description' => 'Text Shadow property adds shadow to Heading.',
                ]
        );

        $this->add_responsive_control(
                'oxi-image-hover-heading-margin', $this->style, [
            'label' => __('Margin', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'type' => Controls::DIMENSIONS,
            'simpledimensions' => 'heading',
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
                '{{WRAPPER}} .oxi-image-hover-figure-caption .oxi-image-hover-figure-heading' => 'padding:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
            'simpledescription' => 'Margin properties are used to create space around Heading.',
            'description' => 'Margin properties are used to create space around Heading.',
                ]
        );
        $this->add_control(
                'oxi-image-hover-heading-animation', $this->style, [
            'label' => __('Animation', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'type' => Controls::SELECT,
            'default' => '',
            'options' => [
                '' => __('None', OXI_IMAGE_HOVER_TEXTDOMAIN),
                'iheu-fade-up' => __('Fade Up', OXI_IMAGE_HOVER_TEXTDOMAIN),
                'iheu-fade-down' => __('Fade Down', OXI_IMAGE_HOVER_TEXTDOMAIN),
                'iheu-fade-left' => __('Fade Left', OXI_IMAGE_HOVER_TEXTDOMAIN),
                'iheu-fade-right' => __('Fade Right', OXI_IMAGE_HOVER_TEXTDOMAIN),
                'iheu-fade-up-big' => __('Fade up Big', OXI_IMAGE_HOVER_TEXTDOMAIN),
                'iheu-fade-down-big' => __('Fade down Big', OXI_IMAGE_HOVER_TEXTDOMAIN),
                'iheu-fade-left-big' => __('Fade left Big', OXI_IMAGE_HOVER_TEXTDOMAIN),
                'iheu-fade-right-big' => __('Fade Right Big', OXI_IMAGE_HOVER_TEXTDOMAIN),
                'iheu-zoom-in' => __('Zoom In', OXI_IMAGE_HOVER_TEXTDOMAIN),
                'iheu-zoom-out' => __('Zoom Out', OXI_IMAGE_HOVER_TEXTDOMAIN),
                'iheu-flip-x' => __('Flip X', OXI_IMAGE_HOVER_TEXTDOMAIN),
                'iheu-flip-y' => __('Flip Y', OXI_IMAGE_HOVER_TEXTDOMAIN),
            ],
            'selector' => [
                '{{WRAPPER}} .oxi-image-hover-figure-caption .oxi-image-hover-figure-heading' => '',
            ],
            'simpledescription' => 'Allows you to animated Heading while viewing.',
            'description' => 'Allows you to animated Heading while viewing.',
                ]
        );
        $this->add_control(
                'oxi-image-hover-heading-animation-delay', $this->style, [
            'label' => __('Animation Delay', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'type' => Controls::SELECT,
            'default' => '',
            'options' => [
                '' => __('None', OXI_IMAGE_HOVER_TEXTDOMAIN),
                'oxi-image-hover-delay-xs' => __('Delay XS', OXI_IMAGE_HOVER_TEXTDOMAIN),
                'oxi-image-hover-delay-sm' => __('Delay SM', OXI_IMAGE_HOVER_TEXTDOMAIN),
                'oxi-image-hover-delay-md' => __('Delay MD', OXI_IMAGE_HOVER_TEXTDOMAIN),
                'oxi-image-hover-delay-lg' => __('Delay LG', OXI_IMAGE_HOVER_TEXTDOMAIN),
                'oxi-image-hover-delay-xl' => __('Delay XL', OXI_IMAGE_HOVER_TEXTDOMAIN),
                'oxi-image-hover-delay-xxl' => __('Delay XXL', OXI_IMAGE_HOVER_TEXTDOMAIN),
            ],
            'selector' => [
                '{{WRAPPER}} .oxi-image-hover-figure-caption .oxi-image-hover-figure-heading' => '',
            ],
            'simpledescription' => 'Allows you to animation delay at Heading while viewing.',
            'description' => 'Allows you to animation delay at Heading while viewing.',
                ]
        );

        $this->end_controls_section();
    }

    public function register_heading_underline() {

    }

}
