<?php

namespace OXI_IMAGE_HOVER_PLUGINS\Modules\Button;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Description of Modules
 *
 * @author biplo
 */
use OXI_IMAGE_HOVER_PLUGINS\Page\Admin_Render as Admin_Render;
use OXI_IMAGE_HOVER_PLUGINS\Classes\Controls as Controls;

class Modules extends Admin_Render {

    use \OXI_IMAGE_HOVER_PLUGINS\Modules\Dynamic;

    public $StyleChanger = [
        'Button-1',
        'Button-2',
        'Button-3',
        'Button-4',
        'Button-5',
        'Button-6',
        'Button-7',
        'Button-8',
        'Button-9',
        'Button-10',
        'Button-11',
    ];

    public function register_effects() {
        return '';
    }

    public function register_column_effects() {
        $this->start_controls_section(
                'oxi-image-hover', [
            'label' => esc_html__('Column & Effects', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'showing' => TRUE,
                ]
        );
        $this->add_group_control(
                'oxi-image-hover-col', $this->style, [
            'type' => Controls::COLUMN,
            'selector' => [
                '{{WRAPPER}} .oxi-image-hover-style' => '',
            ],
            'simpledescription' => 'How much column want to show into a single rows ',
            'description' => 'Define how much column you want to show into single rows. Customize possible with desktop or tab or mobile Settings.',
                ]
        );
        $this->register_effects();
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
                '{{WRAPPER}} .oxi-image-hover-style *,{{WRAPPER}} .oxi-image-hover-style *:before, .oxi-image-hover-style *:after' => '-webkit-transition: all {{SIZE}}{{UNIT}} ease-in-out; -moz-transition: all {{SIZE}}{{UNIT}} ease-in-out; transition: all {{SIZE}}{{UNIT}} ease-in-out;',
            ],
            'description' => 'Set Effects Durations as How long you want to run Effects. Options available with Second or Milisecond.',
                ]
        );
        $this->add_group_control(
                'oxi-image-hover-animation', $this->style, [
            'type' => Controls::ANIMATION,
            'separator' => TRUE,
            'selector' => [
                '{{WRAPPER}} .oxi-image-hover-style' => '',
            ]
                ]
        );
        $this->end_controls_section();
    }

    public function register_general_style() {
        $this->start_controls_section(
                'oxi-image-hover', [
            'label' => esc_html__('Width & Height', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'showing' => true,
                ]
        );
        $this->add_responsive_control(
                'oxi-image-hover-width', $this->style, [
            'label' => __('Width', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'type' => Controls::SLIDER,
            'default' => [
                'unit' => 'px',
                'size' => '',
            ],
            'range' => [
                'px' => [
                    'min' => 0,
                    'max' => 1900,
                    'step' => 1,
                ],
                '%' => [
                    'min' => 1,
                    'max' => 100,
                    'step' => 1,
                ],
                'em' => [
                    'min' => 1,
                    'max' => 200,
                    'step' => 0.1,
                ],
            ],
            'selector' => [
                '{{WRAPPER}} .oxi-image-hover-style-button' => 'max-width:{{SIZE}}{{UNIT}};',
            ],
            'simpledescription' => 'Customize Image Width as like as you want, will be pixel Value.',
            'description' => 'Customize Image Width with several options as Pixel, Percent or EM.',
                ]
        );
        $this->add_responsive_control(
                'oxi-image-hover-height', $this->style, [
            'label' => __('Height', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'type' => Controls::SLIDER,
            'default' => [
                'unit' => 'px',
                'size' => '',
            ],
            'range' => [
                'px' => [
                    'min' => 0,
                    'max' => 1000,
                    'step' => 1,
                ],
                '%' => [
                    'min' => 1,
                    'max' => 200,
                    'step' => 1,
                ],
            ],
            'selector' => [
                '{{WRAPPER}} .oxi-image-hover-style-button:after ' => 'padding-bottom:{{SIZE}}{{UNIT}};',
            ],
            'simpledescription' => 'Customize Image Height as like as you want, will be Percent Value.',
            'description' => 'Customize Image Height with several options as Pixel, Percent or EM.',
                ]
        );
        $this->add_responsive_control(
                'oxi-image-hover-margin', $this->style, [
            'label' => __('Margin', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'type' => Controls::DIMENSIONS,
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
                    'max' => 200,
                    'step' => .1,
                ],
            ],
            'selector' => [
                '{{WRAPPER}} .oxi-image-hover-style' => 'padding:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
            'simpledescription' => 'Margin properties are used to create space around Image.',
            'description' => 'Margin properties are used to create space around Image with several options as Pixel, or Percent or EM.',
                ]
        );
        $this->end_controls_section();
    }

    public function register_content_settings() {
        $this->start_controls_section(
                'oxi-image-hover', [
            'label' => esc_html__('General Settings', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'showing' => TRUE,
                ]
        );
        $this->add_control(
                'oxi-image-hover-background-color', $this->style, [
            'label' => esc_html__('Background', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'type' => Controls::GRADIENT,
            'default' => 'rgba(255, 116, 3, 1)',
            'oparetor' => true,
            'selector' => [
                '{{WRAPPER}} .oxi-image-hover-caption-tab' => 'background:{{VALUE}};',
            ],
            'simpledescription' => 'Customize Hover Background with transparent options.',
            'description' => 'Customize Hover Background with Color or Gradient or Image properties.',
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
                '{{WRAPPER}}   .oxi-image-hover:hover .oxi-image-hover-figure,'
                . '{{WRAPPER}} .oxi-image-hover.oxi-touch .oxi-image-hover-figure,'
                . '{{WRAPPER}} .oxi-image-hover:hover  .oxi-image-hover-figure:before,'
                . '{{WRAPPER}} .oxi-image-hover.oxi-touch .oxi-image-hover-figure:before,'
                . '{{WRAPPER}} .oxi-image-hover:hover .oxi-image-hover-figure .oxi-image-hover-image,'
                . '{{WRAPPER}}.oxi-image-hover.oxi-touch .oxi-image-hover-figure .oxi-image-hover-image,'
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

    public function register_icon_settings() {
        $this->start_controls_section(
                'oxi-image-hover', [
            'label' => esc_html__('Icon Settings', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'showing' => TRUE,
                ]
        );
        $this->add_responsive_control(
                'oxi-image-hover-icon-width-height', $this->style, [
            'label' => __('Width Height', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'type' => Controls::SLIDER,
            'default' => [
                'unit' => 'px',
                'size' => '',
            ],
            'range' => [
                'px' => [
                    'min' => 0,
                    'max' => 1000,
                    'step' => 1,
                ],
                'em' => [
                    'min' => 1,
                    'max' => 100,
                    'step' => 1,
                ],
            ],
            'selector' => [
                '{{WRAPPER}} .oxi-image-button-hover .oxi-image-hover-caption-tab .oxi-icons' => 'width:{{SIZE}}{{UNIT}};height:{{SIZE}}{{UNIT}};',
            ],
            'simpledescription' => 'Set Icon box Width Height Based on Pixel.',
            'description' => 'Set Icon box Width Height.Customize with your design',
                ]
        );
        $this->add_responsive_control(
                'oxi-image-hover-icon-size', $this->style, [
            'label' => __('Size', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'type' => Controls::SLIDER,
            'default' => [
                'unit' => 'px',
                'size' => '',
            ],
            'range' => [
                'px' => [
                    'min' => 0,
                    'max' => 1000,
                    'step' => 1,
                ],
                'em' => [
                    'min' => 1,
                    'max' => 100,
                    'step' => 1,
                ],
            ],
            'selector' => [
                '{{WRAPPER}} .oxi-image-button-hover .oxi-image-hover-caption-tab .oxi-icons' => 'font-size:{{SIZE}}{{UNIT}};',
            ],
            'simpledescription' => 'Set Icon size Based on Pixel.',
            'description' => 'Set Icon size.Customize with your design',
                ]
        );
        $this->start_controls_tabs(
                'oxi-image-hover-start-tabs',
                [
                    'options' => [
                        'normal' => esc_html__('Normal ', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'hover' => esc_html__('Hover ', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    ]
                ]
        );
        $this->start_controls_tab();
        $this->add_control(
                'oxi-image-hover-icon-color', $this->style, [
            'label' => __('Color', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'type' => Controls::COLOR,
            'default' => '#ffffff',
            'selector' => [
                '{{WRAPPER}} .oxi-image-button-hover .oxi-image-hover-caption-tab .oxi-icons' => 'color: {{VALUE}};',
            ],
            'simpledescription' => 'Color property is used to set the color of the Icon.',
            'description' => 'Color property is used to set the color of the Icon.',
                ]
        );
        $this->add_control(
                'oxi-image-hover-icon-background', $this->style, [
            'label' => __('Background', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'type' => Controls::GRADIENT,
            'default' => 'rgba(171, 0, 201, 1)',
            'selector' => [
                '{{WRAPPER}} .oxi-image-button-hover .oxi-image-hover-caption-tab .oxi-icons' => 'background: {{VALUE}};',
            ],
            'simpledescription' => 'Background property is used to set the Background of the Icon Box.',
            'description' => 'Background property is used to set the Background of the Icon Box.',
                ]
        );
        $this->add_group_control(
                'oxi-image-hover-icon-border',
                $this->style,
                [
                    'type' => Controls::BORDER,
                    'selector' => [
                        '{{WRAPPER}} .oxi-image-button-hover .oxi-image-hover-caption-tab .oxi-icons' => ''
                    ],
                    'simpledescription' => 'Icon',
                    'description' => 'Border property is used to set the Border of the Icon.',
                ]
        );
        $this->add_responsive_control(
                'oxi-image-hover-icon-radius', $this->style, [
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
                '{{WRAPPER}} .oxi-image-button-hover .oxi-image-hover-caption-tab .oxi-icons' => 'border-radius:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
            'simpledescription' => 'Allows you to add rounded corners to Icon with options.',
            'description' => 'Allows you to add rounded corners to Icon with options.',
                ]
        );
        $this->add_group_control(
                'oxi-image-hover-icon-boxshadow', $this->style, [
            'type' => Controls::BOXSHADOW,
            'selector' => [
                '{{WRAPPER}} .oxi-image-button-hover .oxi-image-hover-caption-tab .oxi-icons' => '',
            ],
            'simpledescription' => 'Box Shadow property adds shadow to Icon.',
            'description' => 'Box Shadow property adds shadow to Icon.',
                ]
        );
        $this->end_controls_tab();
        $this->start_controls_tab();
        $this->add_control(
                'oxi-image-hover-icon-hover-color', $this->style, [
            'label' => __('Color', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'type' => Controls::COLOR,
            'default' => '#ffffff',
            'selector' => [
                '{{WRAPPER}} .oxi-image-button-hover .oxi-image-hover-caption-tab .oxi-icons:hover' => 'color: {{VALUE}};',
            ],
            'simpledescription' => 'Hover Color is used at Hover to set the color of the Icon.',
            'description' => 'Hover Color is used at Hover to set the color of the Icon.',
                ]
        );
        $this->add_control(
                'oxi-image-hover-icon-hover-background', $this->style, [
            'label' => __('Background', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'type' => Controls::GRADIENT,
            'default' => '#ffffff',
            'selector' => [
                '{{WRAPPER}} .oxi-image-button-hover .oxi-image-hover-caption-tab .oxi-icons:hover' => 'background: {{VALUE}};',
            ],
            'simpledescription' => 'Hover Background  is used at hover to set the Background of the Icon Box.',
            'description' => 'Hover Background  is used at hover to set the Background of the Icon Box.',
                ]
        );
        $this->add_group_control(
                'oxi-image-hover-icon-hover-border',
                $this->style,
                [
                    'type' => Controls::BORDER,
                    'selector' => [
                        '{{WRAPPER}} .oxi-image-button-hover .oxi-image-hover-caption-tab .oxi-icons:hover' => ''
                    ],
                    'simpledescription' => 'Icon',
                    'description' => 'Hover Border is used at hover to set the Border of the Icon.',
                ]
        );
        $this->add_responsive_control(
                'oxi-image-hover-icon-hover-radius', $this->style, [
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
                '{{WRAPPER}} .oxi-image-button-hover .oxi-image-hover-caption-tab .oxi-icons:hover' => 'border-radius:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
            'simpledescription' => 'Allows you to add rounded corners at hover to Icon with options.',
            'description' => 'Allows you to add rounded corners at hover to Icon with options.',
                ]
        );
        $this->add_group_control(
                'oxi-image-hover-icon-hover-boxshadow', $this->style, [
            'type' => Controls::BOXSHADOW,
            'selector' => [
                '{{WRAPPER}} .oxi-image-button-hover .oxi-image-hover-caption-tab .oxi-icons:hover' => '',
            ],
            'simpledescription' => 'Box Shadow property adds shadow at hover to Icon.',
            'description' => 'Box Shadow property adds shadow at hover to Icon.',
                ]
        );

        $this->end_controls_tab();
        $this->end_controls_tabs();

        $this->add_responsive_control(
                'oxi-image-hover-icon-margin', $this->style, [
            'label' => __('Margin', OXI_IMAGE_HOVER_TEXTDOMAIN),
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
                '{{WRAPPER}} .oxi-image-button-hover .oxi-image-hover-caption-tab .oxi-image-hover-icon' => 'padding:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
            'simpledescription' => 'Generate space around a Icon Box, Outside of icon.',
            'description' => 'Generate space around a Icon Box, Outside of icon.',
                ]
        );
        $this->end_controls_section();
    }

    public function register_controls() {
        if (apply_filters('oxi-image-hover-plugin-version', false) == FALSE):
            $this->start_section_header(
                    'shortcode-addons-start-tabs', [
                'options' => [
                    'button-settings' => esc_html__('General Settings', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'custom' => esc_html__('Custom CSS', OXI_IMAGE_HOVER_TEXTDOMAIN),
                ]
                    ]
            );
        else:
            $this->start_section_header(
                    'shortcode-addons-start-tabs', [
                'options' => [
                    'button-settings' => esc_html__('General Settings', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'dynamic' => esc_html__('Dynamic Content', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'custom' => esc_html__('Custom CSS', OXI_IMAGE_HOVER_TEXTDOMAIN),
                ]
                    ]
            );
        endif;

        $this->start_section_tabs(
                'oxi-image-hover-start-tabs', [
            'condition' => [
                'oxi-image-hover-start-tabs' => 'button-settings'
            ]
                ]
        );
        $this->start_section_devider();
        $this->register_column_effects();
        $this->register_icon_settings();
        $this->end_section_devider();
        $this->start_section_devider();
        $this->register_general_style();
        $this->register_content_settings();
        $this->end_section_devider();
        $this->end_section_tabs();

        $this->register_dynamic_data();

        $this->start_section_tabs(
                'oxi-image-hover-start-tabs', [
            'condition' => [
                'oxi-image-hover-start-tabs' => 'custom'
            ],
            'padding' => '10px'
                ]
        );

        $this->start_controls_section(
                'oxi-image-hover', [
            'label' => esc_html__('Custom CSS', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'showing' => TRUE,
                ]
        );
        $this->add_control(
                'image-hover-custom-css', $this->style, [
            'label' => __('', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'type' => Controls::TEXTAREA,
            'default' => '',
            'description' => 'Custom CSS Section. You can add custom css into textarea.'
                ]
        );
        $this->end_controls_section();
        $this->end_section_tabs();
    }

    public function register_dynamic_data() {
        $this->start_section_tabs(
                'oxi-image-hover-start-tabs', [
            'condition' => [
                'oxi-image-hover-start-tabs' => 'dynamic'
            ],
                ]
        );
        $this->start_section_devider();

        $this->register_dynamic_control();

        $this->end_section_devider();

        $this->start_section_devider();
        $this->register_carousel_query_settings();
        $this->register_carousel_arrows_settings();
        $this->register_carousel_dots_settings();

        $this->register_dynamic_load_more_button();

        $this->end_section_devider();

        $this->end_section_tabs();
    }

    public function register_dynamic_control() {
        $this->start_controls_section(
                'oxi-image-hover', [
            'label' => esc_html__('Dynamic Settings', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'showing' => true,
                ]
        );

        if (apply_filters('oxi-image-hover-plugin-version', false) == FALSE):
            $this->add_control(
                    'image_hover_premium_note',
                    $this->style,
                    [
                        'label' => __('Note', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'type' => Controls::HEADING,
                        'description' => 'Dynamic Property only for Premium Version.'
                    ]
            );
        else:
            $this->add_control(
                    'image_hover_dynamic_note',
                    $this->style,
                    [
                        'label' => __('Note', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'type' => Controls::HEADING,
                        'description' => 'Dynamic Property will works only at live Sites. Kindly use shortcode at page or post then check it.'
                    ]
            );
        endif;

        $this->add_control(
                'image_hover_dynamic_load_per_page',
                $this->style,
                [
                    'label' => __('Load Once', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::NUMBER,
                    'default' => '10',
                    'min' => 1,
                    'description' => 'How many Image or Content You want to Viewing per load.',
                ]
        );
        $this->add_control(
                'image_hover_dynamic_carousel', $this->style,
                [
                    'label' => __('Carousel', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::SWITCHER,
                    'default' => 'no',
                    'yes' => __('Yes', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'no' => __('No', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'return_value' => 'yes',
                    'description' => 'Wanna Add Carousel into Hover Effects?.',
                    'notcondition' => TRUE,
                    'condition' => [
                        'image_hover_dynamic_load' => 'yes',
                    ],
                ]
        );

        $this->add_control(
                'image_hover_dynamic_load', $this->style,
                [
                    'label' => __('Load More', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::SWITCHER,
                    'default' => 'no',
                    'yes' => __('Yes', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'no' => __('No', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'return_value' => 'yes',
                    'description' => 'Wanna load More Options?.',
                    'notcondition' => TRUE,
                    'condition' => [
                        'image_hover_dynamic_carousel' => 'yes',
                    ],
                ]
        );

        $this->add_control(
                'image_hover_dynamic_load_type', $this->style,
                [
                    'label' => __('Load More Type', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::CHOOSE,
                    'operator' => Controls::OPERATOR_TEXT,
                    'default' => 'button',
                    'options' => [
                        'button' => [
                            'title' => __('Button', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        ],
                        'infinite' => [
                            'title' => __('Infinite', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        ],
                    ],
                    'condition' => [
                        'image_hover_dynamic_load' => 'yes'
                    ],
                    'description' => 'Select Load More Type, As we offer Infinite loop or Button.',
                ]
        );

        $this->end_controls_section();
    }

    public function modal_opener() {
        $this->add_substitute_control('', [], [
            'type' => Controls::MODALOPENER,
            'title' => __('Add New Image Hover', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'sub-title' => __('Open Image Hover Form', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'showing' => TRUE,
        ]);
    }

    public function modal_form_data() {
        echo '<div class="modal-header">
                    <h4 class="modal-title">Image Hover Form</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">';
        $this->add_control(
                'image_hover_title', $this->style, [
            'label' => __('Rearrange Title', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'type' => Controls::TEXT,
            'default' => 'Image Serial',
            'placeholder' => 'Image Serial Name',
            'description' => 'Add Your Image Serial Name for rearrange Image.'
                ]
        );
        $this->add_control(
                'image_hover_first_icon', $this->style, [
            'label' => __('First Icon', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'type' => Controls::ICON,
            'separator' => TRUE,
            'default' => '',
            'placeholder' => 'Heading',
            'description' => 'Add Your Image Hover First Icon.'
                ]
        );
        $this->add_group_control(
                'image_hover_first_icon_link', $this->style, [
            'label' => __('First URL', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'type' => Controls::URL,
            'default' => '',
            'placeholder' => 'https://www.yoururl.com',
            'description' => 'Add Your Desire Link or Url for First Icon'
                ]
        );
        $this->add_control(
                'image_hover_second_icon', $this->style, [
            'label' => __('Second Icon', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'type' => Controls::ICON,
            'separator' => TRUE,
            'default' => '',
            'placeholder' => 'Heading',
            'description' => 'Add Your Image Hover Second Icon.'
                ]
        );
        $this->add_group_control(
                'image_hover_second_icon_link', $this->style, [
            'label' => __('Second URL', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'type' => Controls::URL,
            'default' => '',
            'placeholder' => 'https://www.yoururl.com',
            'description' => 'Add Your Desire Link or Url for Second Icon'
                ]
        );

        $this->start_controls_tabs(
                'image_hover-start-tabs', [
            'separator' => TRUE,
            'options' => [
                'frontend' => esc_html__('Image', OXI_IMAGE_HOVER_TEXTDOMAIN),
                'backend' => esc_html__('Feature Image', OXI_IMAGE_HOVER_TEXTDOMAIN),
            ]
                ]
        );
        $this->start_controls_tab();

        $this->add_group_control(
                'image_hover_image', $this->style,
                [
                    'label' => __('Image', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::MEDIA,
                    'description' => 'Add or Modify Your Image. You can use Media Library or Custom URL'
                ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab();
        $this->add_group_control(
                'image_hover_feature_image', $this->style,
                [
                    'label' => __('Feature Image', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::MEDIA,
                    'description' => 'Add or Modify Your Feature Image. Adjust background to get better design.'
                ]
        );
        $this->end_controls_tab();
        $this->end_controls_tabs();
        echo '</div>';
    }

    /**
     * Template Parent Item Data Rearrange
     *
     * @since 2.0.0
     */
    public function Rearrange() {
        return '<li class="list-group-item" id="{{id}}">{{image_hover_title}}</li>';
    }

}
