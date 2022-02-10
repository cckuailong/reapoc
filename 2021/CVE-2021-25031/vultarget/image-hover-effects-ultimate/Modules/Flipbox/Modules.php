<?php

namespace OXI_IMAGE_HOVER_PLUGINS\Modules\Flipbox;

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

    public function register_controls() {


        if (apply_filters('oxi-image-hover-plugin-version', false) == FALSE):
            $this->start_section_header(
                    'oxi-image-hover-start-tabs', [
                'options' => [
                    'general-settings' => esc_html__('General Settings', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'frontend' => esc_html__('Frontend', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'backend' => esc_html__('Backend', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'custom' => esc_html__('Custom CSS', OXI_IMAGE_HOVER_TEXTDOMAIN),
                ]
                    ]
            );
        else:
            $this->start_section_header(
                    'oxi-image-hover-start-tabs', [
                'options' => [
                    'general-settings' => esc_html__('General Settings', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'frontend' => esc_html__('Frontend', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'backend' => esc_html__('Backend', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'dynamic' => esc_html__('Dynamic Content', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'custom' => esc_html__('Custom CSS', OXI_IMAGE_HOVER_TEXTDOMAIN),
                ]
                    ]
            );
        endif;

        $this->register_general_tabs();
        $this->register_frontend_tabs();
        $this->register_backend_tabs();
        $this->register_dynamic_data();
        $this->register_custom_tabs();
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

    public function register_general_tabs() {
        $this->start_section_tabs(
                'oxi-image-hover-start-tabs', [
            'condition' => [
                'oxi-image-hover-start-tabs' => 'general-settings'
            ]
                ]
        );
        $this->start_section_devider();
        // register_column_effects
        $this->register_column_effects();

        $this->end_section_devider();

        $this->start_section_devider();
        //register_general_style
        $this->register_general_style();

        $this->end_section_devider();
        $this->end_section_tabs();
    }

    public function register_frontend_tabs() {
        $this->start_section_tabs(
                'oxi-image-hover-start-tabs', [
            'condition' => [
                'oxi-image-hover-start-tabs' => 'frontend'
            ]
                ]
        );
        $this->start_section_devider();
        $this->register_front_content_settings();
        $this->register_front_description_settings();
        $this->end_section_devider();
        $this->start_section_devider();
        $this->register_front_heading_settings();
        $this->register_front_icon_settings();
        $this->end_section_devider();
        $this->end_section_tabs();
    }

    public function register_backend_tabs() {
        $this->start_section_tabs(
                'oxi-image-hover-start-tabs', [
            'condition' => [
                'oxi-image-hover-start-tabs' => 'backend'
            ]
                ]
        );
        $this->start_section_devider();
        $this->register_back_content_settings();
        $this->register_back_description_settings();
        $this->end_section_devider();

        $this->start_section_devider();
        $this->register_back_heading_settings();
        $this->register_back_icon_settings();
        $this->register_back_button_settings();
        $this->end_section_devider();

        $this->end_section_tabs();
    }

    public function register_custom_tabs() {
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
                ]
        );
        $this->add_control(
                'image_hover_effects', $this->style, [
            'label' => __('Effects Direction', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'type' => Controls::SELECT,
            'default' => '',
            'options' => [
                'oxi-image-flip-top-to-bottom' => __('Top To Bottom', OXI_IMAGE_HOVER_TEXTDOMAIN),
                'oxi-image-flip-bottom-to-top' => __('Bottom To Top', OXI_IMAGE_HOVER_TEXTDOMAIN),
                'oxi-image-flip-left-to-right' => __('Left To Right', OXI_IMAGE_HOVER_TEXTDOMAIN),
                'oxi-image-flip-right-to-left' => __('Right To Left', OXI_IMAGE_HOVER_TEXTDOMAIN),
            ],
            'selector' => [
                '{{WRAPPER}} .oxi-image-hover-figure' => '',
            ],
            'description' => 'Set Effects Direction as How you want to run Effects.',
                ]
        );
        $this->add_control(
                'image_hover_timing_type', $this->style, [
            'label' => __('Timing Type', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'type' => Controls::SELECT,
            'default' => '',
            'options' => [
                '' => __('Normal Animation', OXI_IMAGE_HOVER_TEXTDOMAIN),
                'easing_easeInOutExpo' => __('EaseOutBack', OXI_IMAGE_HOVER_TEXTDOMAIN),
                'easing_easeInOutCirc' => __('EaseInOutExpo', OXI_IMAGE_HOVER_TEXTDOMAIN),
                'easing_easeOutBack' => __('EaseInOutCirc', OXI_IMAGE_HOVER_TEXTDOMAIN),
            ],
            'selector' => [
                '{{WRAPPER}} .oxi-image-hover-figure' => '',
            ],
            'description' => 'Set effects type as how your effects animated.',
                ]
        );
        $this->add_control(
                'oxi-image-hover-effects-time', $this->style, [
            'label' => __('Effects Duration (S)', OXI_IMAGE_HOVER_TEXTDOMAIN),
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
                '{{WRAPPER}} .oxi-image-hover-style *,{{WRAPPER}} .oxi-image-hover-style *:before,{{WRAPPER}} .oxi-image-hover-style *:after' => '-webkit-transition: all {{SIZE}}{{UNIT}}; -moz-transition: all {{SIZE}}{{UNIT}}; transition: all {{SIZE}}{{UNIT}};',
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
                '{{WRAPPER}} .oxi-image-hover-style-flipbox' => 'max-width:{{SIZE}}{{UNIT}};',
            ],
            'description' => 'Customize Flipbox Width with several options as Pixel, Percent or EM.',
                ]
        );
        $this->add_responsive_control(
                'oxi-image-hover-height', $this->style, [
            'label' => __('Height', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'type' => Controls::SLIDER,
            'default' => [
                'unit' => '%',
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
                '{{WRAPPER}} .oxi-image-hover-style-flipbox:after ' => 'padding-bottom:{{SIZE}}{{UNIT}};',
            ],
            'description' => 'Customize FLipbox Height with several options as Pixel, Percent or EM.',
                ]
        );
        $this->add_responsive_control(
                'oxi-image-hover-margin', $this->style, [
            'label' => __('Margin', OXI_IMAGE_HOVER_TEXTDOMAIN),
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
            'description' => 'Margin properties are used to create space around Flipbox with several options as Pixel, or Percent or EM.',
                ]
        );
        $this->end_controls_section();
    }

    public function register_front_content_settings() {
        $this->start_controls_section(
                'oxi-image-hover', [
            'label' => esc_html__('Content Settings', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'showing' => TRUE,
                ]
        );
        $this->add_control(
                'oxi-image-flip-front-alignment', $this->style, [
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
                '{{WRAPPER}} .oxi-image-hover-figure-front-section' => '',
            ],
            'description' => 'Customize Content Aginment as Top, Bottom, Left or Center.',
                ]
        );
        $this->add_group_control(
                'oxi-image-flip-front-background', $this->style, [
            'type' => Controls::BACKGROUND,
            'selector' => [
                '{{WRAPPER}} .oxi-image-hover-figure-front-section' => '',
            ],
            'description' => 'Customize Hover Background with Color or Gradient or Image properties.',
                ]
        );

        $this->add_group_control(
                'oxi-image-flip-front-border', $this->style, [
            'type' => Controls::BORDER,
            'selector' => [
                '{{WRAPPER}} .oxi-image-hover-figure-front-section' => '',
            ],
            'description' => 'Border property is used to set the Hover Border of the Flipbox.',
                ]
        );

        $this->add_responsive_control(
                'oxi-image-flip-front-border-radius', $this->style, [
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
                '{{WRAPPER}} .oxi-image-hover-figure-frontend, '
                . '{{WRAPPER}} .oxi-image-hover-figure-frontend:before, '
                . '{{WRAPPER}} .oxi-image-hover-figure-frontend:after, '
                . '{{WRAPPER}} .oxi-image-hover-figure-front-section, '
                . '{{WRAPPER}} .oxi-image-hover-figure-backend, '
                . '{{WRAPPER}} .oxi-image-hover-figure-backend:before, '
                . '{{WRAPPER}} .oxi-image-hover-figure-backend:after, '
                . '{{WRAPPER}} .oxi-image-hover-figure-back-section ' => 'border-radius:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
            'description' => 'Allows you to add rounded corners to Flipbox with options.',
                ]
        );
        $this->add_group_control(
                'oxi-image-flip-front-boxshadow', $this->style, [
            'type' => Controls::BOXSHADOW,
            'selector' => [
                '{{WRAPPER}} .oxi-image-hover-figure-frontend:before' => '',
                '{{WRAPPER}} .oxi-image-hover-figure-backend:before' => '',
            ],
            'description' => 'Allows you at hover to attaches one or more shadows into Button.',
                ]
        );
        $this->add_responsive_control(
                'oxi-image-flip-front-padding', $this->style, [
            'label' => __('Padding', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'type' => Controls::DIMENSIONS,
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
                '{{WRAPPER}} .oxi-image-hover-figure-front-section' => 'padding:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
            'description' => 'Generate space around a Flipbox, inside of any defined borders or Background.',
                ]
        );
        $this->end_controls_section();
    }

    public function register_front_heading_settings() {
        $this->start_controls_section(
                'oxi-image-hover', [
            'label' => esc_html__('Heading Settings', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'showing' => TRUE,
                ]
        );
        $this->add_control(
                'oxi-image-flip-front-heading-underline', $this->style,
                [
                    'label' => __('Haading Underline', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::CHOOSE,
                    'operator' => Controls::OPERATOR_TEXT,
                    'default' => '',
                    'options' => [
                        'oxi-image-hover-heading-underline' => [
                            'title' => __('Show', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        ],
                        '' => [
                            'title' => __('Hide', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        ],
                    ],
                    'selector' => [
                        '{{WRAPPER}} .oxi-image-hover-figure-front-section .oxi-image-hover-heading' => '',
                    ],
                    'description' => 'Wanna set Heading Underline? Customization Panel will viewing while values "Show".',
                ]
        );

        $this->add_group_control(
                'oxi-image-flip-front-heading-typho', $this->style, [
            'type' => Controls::TYPOGRAPHY,
            'include' => Controls::ALIGNNORMAL,
            'selector' => [
                '{{WRAPPER}} .oxi-image-hover-figure-front-section .oxi-image-hover-heading' => '',
            ]
                ]
        );
        $this->add_control(
                'oxi-image-flip-front-heading-color', $this->style, [
            'label' => __('Color', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'type' => Controls::COLOR,
            'default' => '#ffffff',
            'selector' => [
                '{{WRAPPER}} .oxi-image-hover-figure-front-section .oxi-image-hover-heading' => 'color: {{VALUE}};',
            ],
            'description' => 'Color property is used to set the color of the Heading.',
                ]
        );
        $this->add_group_control(
                'oxi-image-flip-front-heading-tx-shadow', $this->style, [
            'type' => Controls::TEXTSHADOW,
            'selector' => [
                '{{WRAPPER}} .oxi-image-hover-figure-front-section .oxi-image-hover-heading' => '',
            ],
            'description' => 'Text Shadow property adds shadow to Heading.',
                ]
        );

        $this->add_responsive_control(
                'oxi-image-flip-front-heading-margin', $this->style, [
            'label' => __('Margin', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'type' => Controls::DIMENSIONS,
            'default' => [
                'unit' => 'px',
                'size' => '',
            ],
            'range' => [
                'px' => [
                    'min' => 1,
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
                '{{WRAPPER}} .oxi-image-hover-figure-front-section .oxi-image-hover-heading' => 'padding:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
            'description' => 'Margin properties are used to create space around Heading.',
                ]
        );
        $this->end_controls_section();
        $this->start_controls_section(
                'oxi-image-hover-head-underline', [
            'label' => esc_html__('Heading Underline', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'showing' => false,
            'condition' => [
                'oxi-image-flip-front-heading-underline' => 'oxi-image-hover-heading-underline'
            ]
                ]
        );
        $this->add_control(
                'oxi-image-flip-front-underline-position',
                $this->style,
                [
                    'label' => __('Position', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::CHOOSE,
                    'toggle' => true,
                    'operator' => Controls::OPERATOR_ICON,
                    'default' => 'left: 50%; transform: translateX(-50%);',
                    'options' => [
                        'left: 0; transform: translateX(0%);' => [
                            'title' => __('Left', OXI_IMAGE_HOVER_TEXTDOMAIN),
                            'icon' => 'fa fa-align-left',
                        ],
                        'left: 50%; transform: translateX(-50%);' => [
                            'title' => __('Center', OXI_IMAGE_HOVER_TEXTDOMAIN),
                            'icon' => 'fa fa-align-center',
                        ],
                        'left: 100%; transform: translateX(-100%);' => [
                            'title' => __('Right', OXI_IMAGE_HOVER_TEXTDOMAIN),
                            'icon' => 'fa fa-align-right',
                        ],
                    ],
                    'selector' => [
                        '{{WRAPPER}} .oxi-image-hover-figure-front-section .oxi-image-hover-heading.oxi-image-hover-heading-underline:before' => '{{VALUE}}',
                    ],
                    'description' => 'Allows you to set Heading Underline Position.',
                ]
        );

        $this->add_control(
                'oxi-image-flip-front-underline-color', $this->style, [
            'label' => __('Color', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'type' => Controls::COLOR,
            'default' => '#ffffff',
            'selector' => [
                '{{WRAPPER}} .oxi-image-hover-figure-front-section .oxi-image-hover-heading.oxi-image-hover-heading-underline:before' => 'border-bottom-color: {{VALUE}};',
            ],
            'description' => 'Allows you to set Heading Underline Color.',
                ]
        );
        $this->add_control(
                'oxi-image-flip-front-underline-type', $this->style, [
            'label' => __('Underline Type', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'type' => Controls::SELECT,
            'simpleenable' => false,
            'default' => 'solid',
            'options' => [
                '' => __('None', OXI_IMAGE_HOVER_TEXTDOMAIN),
                'solid' => __('Solid', OXI_IMAGE_HOVER_TEXTDOMAIN),
                'dotted' => __('Dotted', OXI_IMAGE_HOVER_TEXTDOMAIN),
                'dashed' => __('Dashed', OXI_IMAGE_HOVER_TEXTDOMAIN),
                'double' => __('Double', OXI_IMAGE_HOVER_TEXTDOMAIN),
                'groove' => __('Groove', OXI_IMAGE_HOVER_TEXTDOMAIN),
                'ridge' => __('Ridge', OXI_IMAGE_HOVER_TEXTDOMAIN),
                'inset' => __('Inset', OXI_IMAGE_HOVER_TEXTDOMAIN),
                'outset' => __('Outset', OXI_IMAGE_HOVER_TEXTDOMAIN),
                'hidden' => __('Hidden', OXI_IMAGE_HOVER_TEXTDOMAIN),
            ],
            'selector' => [
                '{{WRAPPER}} .oxi-image-hover-figure-front-section .oxi-image-hover-heading.oxi-image-hover-heading-underline:before' => 'border-bottom-style: {{VALUE}};',
            ],
            'description' => 'Allows you to set Heading Underline Type.',
                ]
        );
        $this->add_responsive_control(
                'oxi-image-flip-front-underline-width', $this->style, [
            'label' => __('Width', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'type' => Controls::SLIDER,
            'simpleenable' => false,
            'default' => [
                'unit' => 'px',
                'size' => 100,
            ],
            'range' => [
                'px' => [
                    'min' => 1,
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
                '{{WRAPPER}} .oxi-image-hover-figure-front-section .oxi-image-hover-heading.oxi-image-hover-heading-underline:before' => 'width:{{SIZE}}{{UNIT}};',
            ],
            'description' => 'Allows you to set Heading Underline Width.',
                ]
        );
        $this->add_responsive_control(
                'oxi-image-flip-front-underline-height', $this->style, [
            'label' => __('Size', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'type' => Controls::SLIDER,
            'simpleenable' => false,
            'default' => [
                'unit' => 'px',
                'size' => 1,
            ],
            'range' => [
                'px' => [
                    'min' => 0,
                    'max' => 50,
                    'step' => 1,
                ],
                '%' => [
                    'min' => 1,
                    'max' => 100,
                    'step' => 1,
                ],
            ],
            'selector' => [
                '{{WRAPPER}} .oxi-image-hover-figure-front-section .oxi-image-hover-heading.oxi-image-hover-heading-underline:before' => 'border-bottom-width: {{SIZE}}{{UNIT}};',
            ],
            'description' => 'Allows you to set Heading Underline Height.',
                ]
        );

        $this->add_responsive_control(
                'oxi-image-flip-front-underline-distance', $this->style, [
            'label' => __('Distance', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'type' => Controls::SLIDER,
            'default' => [
                'unit' => 'px',
                'size' => 0,
            ],
            'range' => [
                'px' => [
                    'min' => 1,
                    'max' => 300,
                    'step' => 1,
                ],
                '%' => [
                    'min' => 1,
                    'max' => 100,
                    'step' => 1,
                ],
            ],
            'selector' => [
                '{{WRAPPER}} .oxi-image-hover-figure-front-section .oxi-image-hover-heading.oxi-image-hover-heading-underline' => 'margin-bottom:{{SIZE}}{{UNIT}};',
            ],
            'description' => 'Allows you to set Heading Underline Distance.',
                ]
        );
        $this->end_controls_section();
    }

    public function register_front_description_settings() {
        $this->start_controls_section(
                'oxi-image-hover', [
            'label' => esc_html__('Description Settings', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'showing' => FALSE,
                ]
        );
        $this->add_group_control(
                'oxi-image-flip-front-desc-typho', $this->style, [
            'type' => Controls::TYPOGRAPHY,
            'include' => Controls::ALIGNNORMAL,
            'selector' => [
                '{{WRAPPER}} .oxi-image-hover-figure-front-section .oxi-image-hover-content' => '',
            ]
                ]
        );
        $this->add_control(
                'oxi-image-flip-front-desc-color', $this->style, [
            'label' => __('Color', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'type' => Controls::COLOR,
            'default' => '#ffffff',
            'selector' => [
                '{{WRAPPER}} .oxi-image-hover-figure-front-section .oxi-image-hover-content' => 'color: {{VALUE}};',
            ],
            'description' => 'Color property is used to set the color of the Description.',
                ]
        );
        $this->add_group_control(
                'oxi-image-flip-front-desc-tx-shadow', $this->style, [
            'type' => Controls::TEXTSHADOW,
            'selector' => [
                '{{WRAPPER}} .oxi-image-hover-figure-front-section .oxi-image-hover-content' => '',
            ],
            'description' => 'Text Shadow property adds shadow to Description.',
                ]
        );

        $this->add_responsive_control(
                'oxi-image-flip-front-desc-margin', $this->style, [
            'label' => __('Margin', OXI_IMAGE_HOVER_TEXTDOMAIN),
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
                '{{WRAPPER}} .oxi-image-hover-figure-front-section .oxi-image-hover-content' => 'padding:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
            'description' => 'Margin properties are used to create space around Description.',
                ]
        );
        $this->end_controls_section();
    }

    public function register_front_icon_settings() {
        $this->start_controls_section(
                'imahe-hover', [
            'label' => esc_html__('Icon Settings', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'showing' => FALSE,
                ]
        );
        $this->add_control(
                'oxi-image-flip-front-icon-position',
                $this->style,
                [
                    'label' => __('Position', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::CHOOSE,
                    'operator' => Controls::OPERATOR_ICON,
                    'toggle' => true,
                    'default' => 'center',
                    'options' => [
                        'left' => [
                            'title' => __('Left', OXI_IMAGE_HOVER_TEXTDOMAIN),
                            'icon' => 'fa fa-align-left',
                        ],
                        'center' => [
                            'title' => __('Center', OXI_IMAGE_HOVER_TEXTDOMAIN),
                            'icon' => 'fa fa-align-center',
                        ],
                        'right' => [
                            'title' => __('Right', OXI_IMAGE_HOVER_TEXTDOMAIN),
                            'icon' => 'fa fa-align-right',
                        ],
                    ],
                    'selector' => [
                        '{{WRAPPER}} .oxi-image-hover-figure-front-section .oxi-image-hover-icon' => 'text-align:{{VALUE}};',
                    ],
                    'description' => 'Allows you to set Icon Position.',
                ]
        );
        $this->add_responsive_control(
                'oxi-image-flip-front-icon-width', $this->style, [
            'label' => __('Width Height', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'type' => Controls::SLIDER,
            'default' => [
                'unit' => 'px',
                'size' => 60,
            ],
            'range' => [
                'px' => [
                    'min' => 0,
                    'max' => 2000,
                    'step' => 1,
                ],
                'em' => [
                    'min' => 0,
                    'max' => 200,
                    'step' => .1,
                ],
                'rem' => [
                    'min' => 0,
                    'max' => 200,
                    'step' => 0.1,
                ],
            ],
            'selector' => [
                '{{WRAPPER}} .oxi-image-hover-figure-front-section .oxi-image-hover-icon .oxi-icons' => 'width:{{SIZE}}{{UNIT}}; height:{{SIZE}}{{UNIT}};',
            ],
            'description' => 'Allows you to Set Icon Width Height.',
                ]
        );
        $this->add_responsive_control(
                'oxi-image-flip-front-icon-size', $this->style, [
            'label' => __('Icon Size', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'type' => Controls::SLIDER,
            'default' => [
                'unit' => 'px',
                'size' => 20,
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
                '{{WRAPPER}} .oxi-image-hover-figure-front-section .oxi-image-hover-icon .oxi-icons' => 'font-size:{{SIZE}}{{UNIT}};',
            ],
            'description' => 'Allows you to Set Icon Size.',
                ]
        );
        $this->add_control(
                'oxi-image-flip-front-icon-color', $this->style, [
            'label' => __('Color', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'type' => Controls::COLOR,
            'default' => '#b414c9',
            'selector' => [
                '{{WRAPPER}} .oxi-image-hover-figure-front-section .oxi-image-hover-icon .oxi-icons' => 'color: {{VALUE}};',
            ],
            'description' => 'Color property is used to set the color of the Icon.',
                ]
        );
        $this->add_group_control(
                'oxi-image-flip-front-icon-background', $this->style, [
            'type' => Controls::BACKGROUND,
            'selector' => [
                '{{WRAPPER}} .oxi-image-hover-figure-front-section .oxi-image-hover-icon .oxi-icons' => '',
            ],
            'description' => 'Customize Icon Background with Color or Gradient or Image properties.',
                ]
        );

        $this->add_group_control(
                'oxi-image-flip-front-icon-border', $this->style, [
            'type' => Controls::BORDER,
            'selector' => [
                '{{WRAPPER}} .oxi-image-hover-figure-front-section .oxi-image-hover-icon .oxi-icons' => '',
            ],
            'description' => 'Border property is used to set the Border of the Icon.',
                ]
        );

        $this->add_responsive_control(
                'oxi-image-flip-front-icon-border-radius', $this->style, [
            'label' => __('Border Radius', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'type' => Controls::DIMENSIONS,
            'default' => [
                'unit' => 'px',
                'size' => '',
            ],
            'range' => [
                '%' => [
                    'min' => 0,
                    'max' => 50,
                    'step' => .1,
                ],
                'px' => [
                    'min' => -200,
                    'max' => 200,
                    'step' => 1,
                ],
                'em' => [
                    'min' => 0,
                    'max' => 10,
                    'step' => .1,
                ],
            ],
            'selector' => [
                '{{WRAPPER}} .oxi-image-hover-figure-front-section .oxi-image-hover-icon .oxi-icons' => 'border-radius:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
            'description' => 'Allows you to add rounded corners to Icon with 4 values.',
                ]
        );
        $this->add_responsive_control(
                'oxi-image-flip-front-icon-margin', $this->style, [
            'label' => __('Margin', OXI_IMAGE_HOVER_TEXTDOMAIN),
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
                '{{WRAPPER}} .oxi-image-hover-figure-front-section .oxi-image-hover-icon ' => 'padding:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
            'description' => 'Margin properties are used to create space around Icon.',
                ]
        );
        $this->end_controls_section();
    }

    public function register_back_content_settings() {
        $this->start_controls_section(
                'oxi-image-hover', [
            'label' => esc_html__('Content Settings', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'showing' => TRUE,
                ]
        );
        $this->add_control(
                'oxi-image-flip-back-content-alignment', $this->style, [
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
                '{{WRAPPER}} .oxi-image-hover-figure-back-section' => '',
            ],
            'description' => 'Customize Content Aginment as Top, Bottom, Left or Center.',
                ]
        );
        $this->add_group_control(
                'oxi-image-flip-back-background', $this->style, [
            'type' => Controls::BACKGROUND,
            'selector' => [
                '{{WRAPPER}} .oxi-image-hover-figure-back-section' => '',
            ],
            'description' => 'Customize Hover Background with Color or Gradient or Image properties.',
                ]
        );

        $this->add_group_control(
                'oxi-image-flip-back-border', $this->style, [
            'type' => Controls::BORDER,
            'selector' => [
                '{{WRAPPER}} .oxi-image-hover-figure-back-section' => '',
            ],
            'description' => 'Border property is used to set the Hover Border of the Flipbox.',
                ]
        );

        $this->add_responsive_control(
                'oxi-image-flip-back-border-radius', $this->style, [
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
                '{{WRAPPER}} .oxi-image-hover:hover .oxi-image-hover-figure .oxi-image-hover-figure-frontend, '
                . '{{WRAPPER}} .oxi-image-hover.oxi-touch .oxi-image-hover-figure .oxi-image-hover-figure-frontend, '
                . '{{WRAPPER}} .oxi-image-hover:hover .oxi-image-hover-figure .oxi-image-hover-figure-frontend:before, '
                . '{{WRAPPER}} .oxi-image-hover.oxi-touch .oxi-image-hover-figure .oxi-image-hover-figure-frontend:before, '
                . '{{WRAPPER}} .oxi-image-hover:hover .oxi-image-hover-figure .oxi-image-hover-figure-frontend:after, '
                . '{{WRAPPER}} .oxi-image-hover.oxi-touch .oxi-image-hover-figure .oxi-image-hover-figure-frontend:after, '
                . '{{WRAPPER}} .oxi-image-hover:hover .oxi-image-hover-figure .oxi-image-hover-figure-front-section, '
                . '{{WRAPPER}} .oxi-image-hover.oxi-touch .oxi-image-hover-figure .oxi-image-hover-figure-front-section, '
                . '{{WRAPPER}} .oxi-image-hover:hover .oxi-image-hover-figure .oxi-image-hover-figure-front-section, '
                . '{{WRAPPER}} .oxi-image-hover.oxi-touch .oxi-image-hover-figure .oxi-image-hover-figure-front-section, '
                . '{{WRAPPER}} .oxi-image-hover:hover .oxi-image-hover-figure .oxi-image-hover-figure-front-section img, '
                . '{{WRAPPER}} .oxi-image-hover.oxi-touch .oxi-image-hover-figure .oxi-image-hover-figure-front-section img, '
                . '{{WRAPPER}} .oxi-image-hover:hover .oxi-image-hover-figure .oxi-image-hover-figure-backend, '
                . '{{WRAPPER}} .oxi-image-hover.oxi-touch .oxi-image-hover-figure .oxi-image-hover-figure-backend, '
                . '{{WRAPPER}} .oxi-image-hover:hover .oxi-image-hover-figure .oxi-image-hover-figure-backend:before, '
                . '{{WRAPPER}} .oxi-image-hover.oxi-touch .oxi-image-hover-figure .oxi-image-hover-figure-backend:before, '
                . '{{WRAPPER}} .oxi-image-hover:hover .oxi-image-hover-figure .oxi-image-hover-figure-backend:after, '
                . '{{WRAPPER}} .oxi-image-hover.oxi-touch .oxi-image-hover-figure .oxi-image-hover-figure-backend:after, '
                . '{{WRAPPER}} .oxi-image-hover:hover .oxi-image-hover-figure .oxi-image-hover-figure-back-section, '
                . '{{WRAPPER}} .oxi-image-hover.oxi-touch .oxi-image-hover-figure .oxi-image-hover-figure-back-section ' => 'border-radius:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
            'description' => 'Allows you to add rounded corners to Flipbox with options.',
                ]
        );
        $this->add_group_control(
                'oxi-image-flip-back-boxshadow', $this->style, [
            'type' => Controls::BOXSHADOW,
            'selector' => [
                '{{WRAPPER}} .oxi-image-hover-figure-backend:before' => '',
            ],
            'description' => 'Allows you at hover to attaches one or more shadows into Button.',
                ]
        );
        $this->add_responsive_control(
                'oxi-image-flip-back-padding', $this->style, [
            'label' => __('Padding', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'type' => Controls::DIMENSIONS,
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
                '{{WRAPPER}} .oxi-image-hover-figure-back-section' => 'padding:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
            'description' => 'Generate space around a Flipbox, inside of any defined borders or Background.',
                ]
        );
        $this->end_controls_section();
    }

    public function register_back_heading_settings() {
        $this->start_controls_section(
                'oxi-image-hover', [
            'label' => esc_html__('Heading Settings', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'showing' => TRUE,
                ]
        );
        $this->add_control(
                'oxi-image-flip-back-heading-underline', $this->style,
                [
                    'label' => __('Haading Underline', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::CHOOSE,
                    'operator' => Controls::OPERATOR_TEXT,
                    'default' => '',
                    'options' => [
                        'oxi-image-hover-heading-underline' => [
                            'title' => __('Show', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        ],
                        '' => [
                            'title' => __('Hide', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        ],
                    ],
                    'selector' => [
                        '{{WRAPPER}} .oxi-image-hover-figure-backend .oxi-image-hover-heading' => '',
                    ],
                    'description' => 'Wanna set Heading Underline? Customization Panel will viewing while values "Show".',
                ]
        );

        $this->add_group_control(
                'oxi-image-flip-back-heading-typho', $this->style, [
            'type' => Controls::TYPOGRAPHY,
            'include' => Controls::ALIGNNORMAL,
            'selector' => [
                '{{WRAPPER}} .oxi-image-hover-figure-backend .oxi-image-hover-heading' => '',
            ]
                ]
        );
        $this->add_control(
                'oxi-image-flip-back-heading-color', $this->style, [
            'label' => __('Color', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'type' => Controls::COLOR,
            'default' => '#ffffff',
            'selector' => [
                '{{WRAPPER}} .oxi-image-hover-figure-backend .oxi-image-hover-heading' => 'color: {{VALUE}};',
            ],
            'description' => 'Color property is used to set the color of the Heading.',
                ]
        );
        $this->add_group_control(
                'oxi-image-flip-back-heading-tx-shadow', $this->style, [
            'type' => Controls::TEXTSHADOW,
            'selector' => [
                '{{WRAPPER}} .oxi-image-hover-figure-backend .oxi-image-hover-heading' => '',
            ],
            'description' => 'Text Shadow property adds shadow to Heading.',
                ]
        );

        $this->add_responsive_control(
                'oxi-image-flip-back-heading-margin', $this->style, [
            'label' => __('Margin', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'type' => Controls::DIMENSIONS,
            'default' => [
                'unit' => 'px',
                'size' => '',
            ],
            'range' => [
                'px' => [
                    'min' => 1,
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
                '{{WRAPPER}} .oxi-image-hover-figure-backend .oxi-image-hover-heading' => 'padding:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
            'description' => 'Margin properties are used to create space around Heading.',
                ]
        );
        $this->add_control(
                'oxi-image-flip-back-heading-animation', $this->style, [
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
                '{{WRAPPER}} .oxi-image-hover-figure-backend .oxi-image-hover-heading' => '',
            ],
            'description' => 'Allows you to animated Heading while viewing.',
                ]
        );
        $this->add_control(
                'oxi-image-flip-back-animation-delay', $this->style, [
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
                '{{WRAPPER}} .oxi-image-hover-figure-backend .oxi-image-hover-heading' => '',
            ],
            'description' => 'Allows you to animation delay at Heading while viewing.',
                ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
                'oxi-image-flip-back-head-underline', [
            'label' => esc_html__('Heading Underline', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'showing' => false,
            'condition' => [
                'oxi-image-flip-back-heading-underline' => 'oxi-image-hover-heading-underline'
            ]
                ]
        );
        $this->add_control(
                'oxi-image-flip-back-heading-underline-position',
                $this->style,
                [
                    'label' => __('Position', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::CHOOSE,
                    'operator' => Controls::OPERATOR_ICON,
                    'default' => 'left: 50%; transform: translateX(-50%);',
                    'options' => [
                        'left: 0; transform: translateX(0%);' => [
                            'title' => __('Left', OXI_IMAGE_HOVER_TEXTDOMAIN),
                            'icon' => 'fa fa-align-left',
                        ],
                        'left: 50%; transform: translateX(-50%);' => [
                            'title' => __('Center', OXI_IMAGE_HOVER_TEXTDOMAIN),
                            'icon' => 'fa fa-align-center',
                        ],
                        'left: 100%; transform: translateX(-100%);' => [
                            'title' => __('Right', OXI_IMAGE_HOVER_TEXTDOMAIN),
                            'icon' => 'fa fa-align-right',
                        ],
                    ],
                    'selector' => [
                        '{{WRAPPER}} .oxi-image-hover-figure-backend .oxi-image-hover-heading.oxi-image-hover-heading-underline:before' => '{{VALUE}}',
                    ],
                    'description' => 'Allows you to set Heading Underline Position.',
                ]
        );

        $this->add_control(
                'oxi-image-flip-back-underline-color', $this->style, [
            'label' => __('Color', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'type' => Controls::COLOR,
            'default' => '#ffffff',
            'selector' => [
                '{{WRAPPER}} .oxi-image-hover-figure-backend .oxi-image-hover-heading.oxi-image-hover-heading-underline:before' => 'border-bottom-color: {{VALUE}};',
            ],
            'description' => 'Allows you to set Heading Underline Color.',
                ]
        );
        $this->add_control(
                'oxi-image-flip-back-underline-type', $this->style, [
            'label' => __('Underline Type', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'type' => Controls::SELECT,
            'simpleenable' => false,
            'default' => 'solid',
            'options' => [
                '' => __('None', OXI_IMAGE_HOVER_TEXTDOMAIN),
                'solid' => __('Solid', OXI_IMAGE_HOVER_TEXTDOMAIN),
                'dotted' => __('Dotted', OXI_IMAGE_HOVER_TEXTDOMAIN),
                'dashed' => __('Dashed', OXI_IMAGE_HOVER_TEXTDOMAIN),
                'double' => __('Double', OXI_IMAGE_HOVER_TEXTDOMAIN),
                'groove' => __('Groove', OXI_IMAGE_HOVER_TEXTDOMAIN),
                'ridge' => __('Ridge', OXI_IMAGE_HOVER_TEXTDOMAIN),
                'inset' => __('Inset', OXI_IMAGE_HOVER_TEXTDOMAIN),
                'outset' => __('Outset', OXI_IMAGE_HOVER_TEXTDOMAIN),
                'hidden' => __('Hidden', OXI_IMAGE_HOVER_TEXTDOMAIN),
            ],
            'selector' => [
                '{{WRAPPER}} .oxi-image-hover-figure-backend .oxi-image-hover-heading.oxi-image-hover-heading-underline:before' => 'border-bottom-style: {{VALUE}};',
            ],
            'description' => 'Allows you to set Heading Underline Type.',
                ]
        );
        $this->add_responsive_control(
                'oxi-image-flip-back-underline-width', $this->style, [
            'label' => __('Width', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'type' => Controls::SLIDER,
            'simpleenable' => false,
            'default' => [
                'unit' => 'px',
                'size' => 100,
            ],
            'range' => [
                'px' => [
                    'min' => 1,
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
                '{{WRAPPER}} .oxi-image-hover-figure-backend .oxi-image-hover-heading.oxi-image-hover-heading-underline:before' => 'width:{{SIZE}}{{UNIT}};',
            ],
            'description' => 'Allows you to set Heading Underline Width.',
                ]
        );
        $this->add_responsive_control(
                'oxi-image-flip-back-underline-height', $this->style, [
            'label' => __('Size', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'type' => Controls::SLIDER,
            'default' => [
                'unit' => 'px',
                'size' => 1,
            ],
            'range' => [
                'px' => [
                    'min' => 0,
                    'max' => 50,
                    'step' => 1,
                ],
                '%' => [
                    'min' => 1,
                    'max' => 100,
                    'step' => 1,
                ],
            ],
            'selector' => [
                '{{WRAPPER}} .oxi-image-hover-figure-backend .oxi-image-hover-heading.oxi-image-hover-heading-underline:before' => 'border-bottom-width: {{SIZE}}{{UNIT}};',
            ],
            'description' => 'Allows you to set Icon Underline Height.',
                ]
        );

        $this->add_responsive_control(
                'oxi-image-flip-back-underline-distance', $this->style, [
            'label' => __('Distance', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'type' => Controls::SLIDER,
            'default' => [
                'unit' => 'px',
                'size' => 0,
            ],
            'range' => [
                'px' => [
                    'min' => 1,
                    'max' => 300,
                    'step' => 1,
                ],
                '%' => [
                    'min' => 1,
                    'max' => 100,
                    'step' => 1,
                ],
            ],
            'selector' => [
                '{{WRAPPER}} .oxi-image-hover-figure-backend .oxi-image-hover-heading.oxi-image-hover-heading-underline' => 'margin-bottom:{{SIZE}}{{UNIT}};',
            ],
            'description' => 'Allows you to set Heading Underline Distance.',
                ]
        );
        $this->end_controls_section();
    }

    public function register_back_description_settings() {
        $this->start_controls_section(
                'oxi-image-hover', [
            'label' => esc_html__('Description Settings', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'showing' => FALSE,
                ]
        );
        $this->add_group_control(
                'oxi-image-flip-back-desc-typho', $this->style, [
            'type' => Controls::TYPOGRAPHY,
            'include' => Controls::ALIGNNORMAL,
            'selector' => [
                '{{WRAPPER}} .oxi-image-hover-figure-backend .oxi-image-hover-content' => '',
            ]
                ]
        );
        $this->add_control(
                'oxi-image-flip-back-desc-color', $this->style, [
            'label' => __('Color', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'type' => Controls::COLOR,
            'default' => '#ffffff',
            'selector' => [
                '{{WRAPPER}} .oxi-image-hover-figure-backend .oxi-image-hover-content' => 'color: {{VALUE}};',
            ],
            'description' => 'Color property is used to set the color of the Description.',
                ]
        );
        $this->add_group_control(
                'oxi-image-flip-back-desc-tx-shadow', $this->style, [
            'type' => Controls::TEXTSHADOW,
            'selector' => [
                '{{WRAPPER}} .oxi-image-hover-figure-backend .oxi-image-hover-content' => '',
            ],
            'description' => 'Text Shadow property adds shadow to Description.',
                ]
        );

        $this->add_responsive_control(
                'oxi-image-flip-back-desc-margin', $this->style, [
            'label' => __('Margin', OXI_IMAGE_HOVER_TEXTDOMAIN),
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
                '{{WRAPPER}} .oxi-image-hover-figure-backend .oxi-image-hover-content' => 'padding:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
            'description' => 'Margin properties are used to create space around Description.',
                ]
        );
        $this->add_control(
                'oxi-image-flip-back-desc-animation', $this->style, [
            'label' => __('Animation', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'type' => Controls::SELECT,
            'default' => 'solid',
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
                '{{WRAPPER}} .oxi-image-hover-figure-backend .oxi-image-hover-content' => '',
            ],
            'description' => 'Allows you to animated Description while viewing.',
                ]
        );
        $this->add_control(
                'oxi-image-flip-back-desc-animation-delay', $this->style, [
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
                '{{WRAPPER}} .oxi-image-hover-figure-backend .oxi-image-hover-content' => '',
            ],
            'description' => 'Allows you to animation delay at Description while viewing.',
                ]
        );

        $this->end_controls_section();
    }

    public function register_back_icon_settings() {
        $this->start_controls_section(
                'oxi-image-hover', [
            'label' => esc_html__('Icon Settings', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'showing' => FALSE,
                ]
        );
        $this->add_control(
                'oxi-image-flip-back-icon-position',
                $this->style,
                [
                    'label' => __('Position', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::CHOOSE,
                    'operator' => Controls::OPERATOR_ICON,
                    'default' => 'center',
                    'toggle' => true,
                    'options' => [
                        'left' => [
                            'title' => __('Left', OXI_IMAGE_HOVER_TEXTDOMAIN),
                            'icon' => 'fa fa-align-left',
                        ],
                        'center' => [
                            'title' => __('Center', OXI_IMAGE_HOVER_TEXTDOMAIN),
                            'icon' => 'fa fa-align-center',
                        ],
                        'right' => [
                            'title' => __('Right', OXI_IMAGE_HOVER_TEXTDOMAIN),
                            'icon' => 'fa fa-align-right',
                        ],
                    ],
                    'selector' => [
                        '{{WRAPPER}} .oxi-image-hover-figure-back-section .oxi-image-hover-icon' => 'text-align:{{VALUE}}',
                    ],
                    'description' => 'Allows you to set Icon Position.',
                ]
        );
        $this->add_responsive_control(
                'oxi-image-flip-back-icon-width', $this->style, [
            'label' => __('Width', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'type' => Controls::SLIDER,
            'default' => [
                'unit' => 'px',
                'size' => 60,
            ],
            'range' => [
                'px' => [
                    'min' => 1,
                    'max' => 2000,
                    'step' => 1,
                ],
                'em' => [
                    'min' => 1,
                    'max' => 200,
                    'step' => .1,
                ],
                'rem' => [
                    'min' => 1,
                    'max' => 200,
                    'step' => 0.1,
                ],
            ],
            'selector' => [
                '{{WRAPPER}} .oxi-image-hover-figure-backend .oxi-image-hover-icon .oxi-icons' => 'width:{{SIZE}}{{UNIT}}; height:{{SIZE}}{{UNIT}};',
            ],
            'description' => 'Allows you to Set Icon Width Height.',
                ]
        );
        $this->add_responsive_control(
                'oxi-image-flip-back-icon-size', $this->style, [
            'label' => __('Icon Size', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'type' => Controls::SLIDER,
            'default' => [
                'unit' => 'px',
                'size' => 20,
            ],
            'range' => [
                'px' => [
                    'min' => 1,
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
                '{{WRAPPER}} .oxi-image-hover-figure-backend .oxi-image-hover-icon .oxi-icons' => 'font-size:{{SIZE}}{{UNIT}};',
            ],
            'description' => 'Allows you to Set Icon Size.',
                ]
        );
        $this->add_control(
                'oxi-image-flip-back-icon-color', $this->style, [
            'label' => __('Color', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'type' => Controls::COLOR,
            'default' => '#b414c9',
            'selector' => [
                '{{WRAPPER}} .oxi-image-hover-figure-backend .oxi-image-hover-icon .oxi-icons' => 'color: {{VALUE}};',
            ],
            'description' => 'Color property is used to set the color of the Icon.',
                ]
        );
        $this->add_group_control(
                'oxi-image-flip-back-icon-background', $this->style, [
            'type' => Controls::BACKGROUND,
            'selector' => [
                '{{WRAPPER}} .oxi-image-hover-figure-backend .oxi-image-hover-icon .oxi-icons' => '',
            ],
            'description' => 'Customize Icon Background with Color or Gradient or Image properties.',
                ]
        );

        $this->add_group_control(
                'oxi-image-flip-back-icon-border', $this->style, [
            'type' => Controls::BORDER,
            'selector' => [
                '{{WRAPPER}} .oxi-image-hover-figure-backend .oxi-image-hover-icon .oxi-icons' => '',
            ],
            'description' => 'Border property is used to set the Border of the Icon.',
                ]
        );

        $this->add_responsive_control(
                'oxi-image-flip-back-icon-border-radius', $this->style, [
            'label' => __('Border Radius', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'type' => Controls::DIMENSIONS,
            'default' => [
                'unit' => 'px',
                'size' => 100,
            ],
            'range' => [
                '%' => [
                    'min' => 0,
                    'max' => 50,
                    'step' => .1,
                ],
                'px' => [
                    'min' => -200,
                    'max' => 200,
                    'step' => 1,
                ],
                'em' => [
                    'min' => 0,
                    'max' => 10,
                    'step' => .1,
                ],
            ],
            'selector' => [
                '{{WRAPPER}} .oxi-image-hover-figure-backend .oxi-image-hover-icon .oxi-icons' => 'border-radius:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
            'description' => 'Allows you to add rounded corners to Icon with 4 values.',
                ]
        );
        $this->add_responsive_control(
                'oxi-image-flip-back-icon-margin', $this->style, [
            'label' => __('Margin', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'type' => Controls::DIMENSIONS,
            'default' => [
                'unit' => 'px',
                'size' => '',
            ],
            'range' => [
                'px' => [
                    'min' => 1,
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
                '{{WRAPPER}} .oxi-image-hover-figure-backend .oxi-image-hover-icon' => 'padding:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
            'description' => 'Margin properties are used to create space around Icon.',
                ]
        );
        $this->add_control(
                'oxi-image-flip-back-icon-animation', $this->style, [
            'label' => __('Animation', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'type' => Controls::SELECT,
            'default' => 'solid',
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
                '{{WRAPPER}} .oxi-image-hover-figure-backend .oxi-image-hover-icon' => '',
            ],
            'description' => 'Allows you to animated Icon while viewing.',
                ]
        );
        $this->add_control(
                'oxi-image-flip-back-icon-animation-delay', $this->style, [
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
                '{{WRAPPER}} .oxi-image-hover-figure-backend .oxi-image-hover-icon' => '',
            ],
            'description' => 'Allows you to animation delay of Icon while viewing.',
                ]
        );

        $this->end_controls_section();
    }

    public function register_back_button_settings() {
        $this->start_controls_section(
                'oxi-image-hover', [
            'label' => esc_html__('Button Settings', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'showing' => false,
                ]
        );
        $this->add_control(
                'oxi-image-flip-back-button-position',
                $this->style,
                [
                    'label' => __('Position', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::CHOOSE,
                    'operator' => Controls::OPERATOR_ICON,
                    'default' => '',
                    'toggle' => true,
                    'options' => [
                        'left' => [
                            'title' => __('Left', OXI_IMAGE_HOVER_TEXTDOMAIN),
                            'icon' => 'fa fa-align-left',
                        ],
                        'center' => [
                            'title' => __('Center', OXI_IMAGE_HOVER_TEXTDOMAIN),
                            'icon' => 'fa fa-align-center',
                        ],
                        'right' => [
                            'title' => __('Right', OXI_IMAGE_HOVER_TEXTDOMAIN),
                            'icon' => 'fa fa-align-right',
                        ],
                    ],
                    'selector' => [
                        '{{WRAPPER}}  .oxi-image-hover-button' => 'text-align:{{VALUE}}',
                    ],
                    'description' => 'Allows you set Button Align as Left, Center or Right.',
                ]
        );

        $this->add_group_control(
                'oxi-image-flip-back-button-typho', $this->style, [
            'type' => Controls::TYPOGRAPHY,
            'selector' => [
                '{{WRAPPER}} .oxi-image-hover-button a.oxi-image-btn' => '',
            ]
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
                'oxi-image-flip-back-button-color', $this->style, [
            'label' => __('Color', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'type' => Controls::COLOR,
            'default' => '#ffffff',
            'selector' => [
                '{{WRAPPER}}  .oxi-image-hover-button a.oxi-image-btn' => 'color: {{VALUE}};',
                '{{WRAPPER}}  .oxi-image-hover-button a.oxi-image-btn:hover' => 'color: {{VALUE}};',
            ],
            'description' => 'Color property is used to set the color of the Button.',
                ]
        );
        $this->add_control(
                'oxi-image-flip-back-button-background', $this->style, [
            'label' => __('Background', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'type' => Controls::GRADIENT,
            'default' => 'rgba(171, 0, 201, 1)',
            'selector' => [
                '{{WRAPPER}} .oxi-image-hover-button a.oxi-image-btn' => 'background: {{VALUE}};',
                '{{WRAPPER}} .oxi-image-hover-button a.oxi-image-btn:hover' => 'background: {{VALUE}};',
            ],
            'description' => 'Background property is used to set the Background of the Button.',
                ]
        );
        $this->add_group_control(
                'oxi-image-flip-back-button-border', $this->style, [
            'type' => Controls::BORDER,
            'selector' => [
                '{{WRAPPER}} .oxi-image-hover-button  a.oxi-image-btn' => '',
            ],
            'simpledescription' => 'Button',
            'description' => 'Border property is used to set the Border of the Button.',
                ]
        );
        $this->add_group_control(
                'oxi-image-flip-back-button-tx-shadow', $this->style, [
            'type' => Controls::TEXTSHADOW,
            'selector' => [
                '{{WRAPPER}} .oxi-image-hover-button  a.oxi-image-btn' => '',
                '{{WRAPPER}} .oxi-image-hover-button  a.oxi-image-btn:hover' => '',
            ],
            'description' => 'Text Shadow property adds shadow to Button.',
                ]
        );

        $this->add_responsive_control(
                'oxi-image-flip-back-button-radius', $this->style, [
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
                '{{WRAPPER}} .oxi-image-hover-button a.oxi-image-btn' => 'border-radius:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                '{{WRAPPER}} .oxi-image-hover-button a.oxi-image-btn:hover' => 'border-radius:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
            'description' => 'Allows you to add rounded corners to Button with options.',
                ]
        );
        $this->add_group_control(
                'oxi-image-flip-back-button-boxshadow', $this->style, [
            'type' => Controls::BOXSHADOW,
            'selector' => [
                '{{WRAPPER}} .oxi-image-hover-button a.oxi-image-btn' => '',
                '{{WRAPPER}} .oxi-image-hover-button a.oxi-image-btn:hover' => '',
            ],
            'description' => 'Allows you to attaches one or more shadows into Button.',
                ]
        );
        $this->end_controls_tab();
        $this->start_controls_tab();
        $this->add_control(
                'oxi-image-flip-back-button-hover-color', $this->style, [
            'label' => __('Color', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'type' => Controls::COLOR,
            'default' => '#ffffff',
            'selector' => [
                '{{WRAPPER}} .oxi-image-hover-figure .oxi-image-hover-button a.oxi-image-btn:hover' => 'color: {{VALUE}};',
            ],
            'description' => 'Color property is used to set the Hover color of the Button.',
                ]
        );
        $this->add_control(
                'oxi-image-flip-back-button-hover-background', $this->style, [
            'label' => __('Background', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'type' => Controls::GRADIENT,
            'default' => '#ffffff',
            'selector' => [
                '{{WRAPPER}} .oxi-image-hover-figure .oxi-image-hover-button a.oxi-image-btn:hover' => 'background: {{VALUE}};',
            ],
            'description' => 'Background property is used to set the Hover Background of the Button.',
                ]
        );
        $this->add_group_control(
                'oxi-image-flip-back-button-hover-border', $this->style, [
            'type' => Controls::BORDER,
            'selector' => [
                '{{WRAPPER}} .oxi-image-hover-figure .oxi-image-hover-button a.oxi-image-btn:hover' => '',
            ],
            'description' => 'Border property is used to set the Hover Border of the Button.',
                ]
        );
        $this->add_group_control(
                'oxi-image-flip-back-button-hover-tx-shadow', $this->style, [
            'type' => Controls::TEXTSHADOW,
            'selector' => [
                '{{WRAPPER}} .oxi-image-hover-figure .oxi-image-hover-button a.oxi-image-btn:hover' => '',
            ],
            'description' => 'Text Shadow property adds shadow to Hover Button.',
                ]
        );

        $this->add_responsive_control(
                'oxi-image-flip-back-button-hover-radius', $this->style, [
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
                '{{WRAPPER}} .oxi-image-hover-figure .oxi-image-hover-button a.oxi-image-btn:hover' => 'border-radius:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
            'description' => 'Allows you to add rounded corners at hover to Button with options.',
                ]
        );
        $this->add_group_control(
                'oxi-image-flip-back-button-hover-boxshadow', $this->style, [
            'type' => Controls::BOXSHADOW,
            'selector' => [
                '{{WRAPPER}} .oxi-image-hover-figure .oxi-image-hover-button a.oxi-image-btn:hover' => '',
            ],
            'description' => 'Allows you at hover to attaches one or more shadows into Button.',
                ]
        );

        $this->end_controls_tab();
        $this->end_controls_tabs();

        $this->add_responsive_control(
                'oxi-image-flip-back-button-padding', $this->style, [
            'label' => __('Padding', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'type' => Controls::DIMENSIONS,
            'separator' => TRUE,
            'default' => [
                'unit' => 'px',
                'size' => '',
            ],
            'range' => [
                'px' => [
                    'min' => 1,
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
                '{{WRAPPER}} .oxi-image-hover-button a.oxi-image-btn' => 'padding:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
            'simpledescription' => 'Generate space around a Button, inside of any defined borders or Background.',
            'description' => 'Generate space around a Button, inside of any defined borders or Background.',
                ]
        );
        $this->add_responsive_control(
                'oxi-image-flip-back-button-margin', $this->style, [
            'label' => __('Margin', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'type' => Controls::DIMENSIONS,
            'default' => [
                'unit' => 'px',
                'size' => '',
            ],
            'range' => [
                'px' => [
                    'min' => -100,
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
                '{{WRAPPER}} .oxi-image-hover-button a.oxi-image-btn' => 'margin:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
            'description' => 'Generate space around a Button, Outside of Content.',
                ]
        );
        $this->add_control(
                'oxi-image-flip-back-button-animation', $this->style, [
            'label' => __('Animation', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'type' => Controls::SELECT,
            'default' => 'solid',
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
                '{{WRAPPER}} .oxi-image-hover-button' => '',
            ],
            'description' => 'Allows you to animated Button while viewing.',
                ]
        );
        $this->add_control(
                'oxi-image-flip-back-button-animation-delay', $this->style, [
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
                '{{WRAPPER}} .oxi-image-hover-button' => '',
            ],
            'description' => 'Allows you to animation delay at Button while viewing.',
                ]
        );

        $this->end_controls_section();
    }

    public function modal_opener() {
        $this->add_substitute_control('', [], [
            'type' => Controls::MODALOPENER,
            'title' => __('Add New Flip Box', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'sub-title' => __('Open Flip Box Form', OXI_IMAGE_HOVER_TEXTDOMAIN),
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
                'image_hover_front_heading', $this->style, [
            'label' => __('Title', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'type' => Controls::TEXT,
            'default' => '',
            'placeholder' => 'Heading',
            'description' => 'Add Your Flipbox Backend Title else make it blank.'
                ]
        );
        $this->add_control(
                'image_hover_back_heading', $this->style, [
            'label' => __('Title', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'type' => Controls::TEXT,
            'default' => '',
            'placeholder' => 'Heading',
            'description' => 'Add Your Flipbox Backend Title.'
                ]
        );
        $this->add_control(
                'image_hover_front_icon', $this->style, [
            'label' => __('Icon', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'type' => Controls::ICON,
                ]
        );
        $this->add_control(
                'image_hover_back_icon', $this->style, [
            'label' => __('Icon', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'type' => Controls::ICON,
                ]
        );
        $this->add_control(
                'image_hover_front_description', $this->style, [
            'label' => __('Short Description', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'type' => Controls::TEXTAREA,
            'default' => '',
            'description' => 'Add Your Description Unless make it blank.'
                ]
        );
        $this->add_control(
                'image_hover_back_description', $this->style, [
            'label' => __('Short Description', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'type' => Controls::TEXTAREA,
            'default' => '',
            'description' => 'Add Your Description Unless make it blank.'
                ]
        );
        $this->start_controls_tabs(
                'image_hover-start-tabs', [
            'separator' => TRUE,
            'options' => [
                'frontend' => esc_html__('Front Image', OXI_IMAGE_HOVER_TEXTDOMAIN),
                'backend' => esc_html__('Backend Image', OXI_IMAGE_HOVER_TEXTDOMAIN),
            ]
                ]
        );
        $this->start_controls_tab();

        $this->add_group_control(
                'image_hover_front_image', $this->style,
                [
                    'label' => __('Image', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::MEDIA,
                    'description' => 'Add or Modify Your Front Background Image. Adjust Front Background to get better design.'
                ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab();
        $this->add_group_control(
                'image_hover_back_image', $this->style,
                [
                    'label' => __('Feature Image', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::MEDIA,
                    'description' => 'Add or Modify Your Backend Image. Adjust Backend background to get better design.'
                ]
        );
        $this->end_controls_tab();
        $this->end_controls_tabs();

        $this->add_group_control(
                'image_hover_button_link', $this->style, [
            'label' => __('URL', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'type' => Controls::URL,
            'separator' => TRUE,
            'default' => '',
            'placeholder' => 'https://www.yoururl.com',
            'description' => 'Add Your Desire Link or Url Unless make it blank'
                ]
        );
        $this->add_control(
                'image_hover_button_text', $this->style, [
            'label' => __('Button Text', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'type' => Controls::TEXT,
            'default' => '',
            'description' => 'Customize your button text. Button will only view while Url given'
                ]
        );
        echo '</div>';
    }

    /**
     * Template Parent Item Data Rearrange
     *
     * @since 2.0.0
     */
    public function Rearrange() {
        return '<li class="list-group-item" id="{{id}}">{{image_hover_front_heading}}</li>';
    }

}
