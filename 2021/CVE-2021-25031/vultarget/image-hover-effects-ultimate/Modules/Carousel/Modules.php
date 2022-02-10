<?php

namespace OXI_IMAGE_HOVER_PLUGINS\Modules\Carousel;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Description of Modules
 *
 * @author biplo
 */
use OXI_IMAGE_HOVER_PLUGINS\Classes\Controls as Controls;
use OXI_IMAGE_HOVER_PLUGINS\Page\Admin_Render as Admin_Render;

class Modules extends Admin_Render {

    public function register_controls() {
        $this->start_section_header(
                'oxi-image-hover-start-tabs',
                [
                    'options' => [
                        'general-settings' => esc_html__('General Settings', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'custom' => esc_html__('Custom CSS', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    ]
                ]
        );
        $this->register_general_tabs();
        $this->register_custom_tabs();
    }

    public function register_custom_tabs() {
        $this->start_section_tabs(
                'oxi-image-hover-start-tabs',
                [
                    'condition' => [
                        'oxi-image-hover-start-tabs' => 'custom'
                    ],
                    'padding' => '10px'
                ]
        );

        $this->start_controls_section(
                'oxi-image-hover',
                [
                    'label' => esc_html__('Custom CSS', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'showing' => TRUE,
                ]
        );
        $this->add_control(
                'image-hover-custom-css',
                $this->style,
                [
                    'label' => __('', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::TEXTAREA,
                    'default' => '',
                    'description' => 'Custom CSS Section. You can add custom css into textarea.'
                ]
        );
        $this->end_controls_section();
        $this->end_section_tabs();
    }

    public function register_general_tabs() {
        $this->start_section_tabs(
                'oxi-image-hover-start-tabs',
                [
                    'condition' => [
                        'oxi-image-hover-start-tabs' => 'general-settings',
                    ],
                ]
        );
        $this->start_section_devider();

        $this->register_carousel_query_settings();
        $this->end_section_devider();
        $this->start_section_devider();
        $this->register_carousel_arrows_settings();
        $this->register_carousel_dots_settings();
        $this->end_section_devider();
        $this->end_section_tabs();
    }

    public function register_carousel_query_settings() {
        $this->start_controls_section(
                'display-post',
                [
                    'label' => esc_html__('Carousel Query', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'showing' => TRUE,
                ]
        );
        $this->add_control(
                'carousel_note',
                $this->style,
                [
                    'label' => __('Note', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::HEADING,
                    'description' => 'Works after saving and reloading all the fields.'
                ]
        );
        $this->add_control(
                'carousel_register_style',
                $this->style,
                [
                    'label' => __('Carousel Style', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'loader' => TRUE,
                    'type' => Controls::SELECT,
                    'options' => $this->all_style(),
                    'description' => 'Confirm Your Shortcode name which one you wanna create carousel.'
                ]
        );
        $this->add_responsive_control(
                'carousel_item_slide',
                $this->style,
                [
                    'label' => __('Multiple Items', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::SLIDER,
                    'separator' => TRUE,
                    'default' => [
                        'unit' => 'px',
                        'size' => '1',
                    ],
                    'range' => [
                        'px' => [
                            'min' => 1,
                            'max' => 10,
                            'step' => 1,
                        ],
                    ],
                    'description' => 'How many Item You want to Slide per click.'
                ]
        );
        $this->add_control(
                'carousel_autoplay',
                $this->style,
                [
                    'label' => __('Autoplay', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::SWITCHER,
                    'loader' => TRUE,
                    'default' => 'yes',
                    'yes' => __('Yes', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'no' => __('No', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'return_value' => 'yes',
                    'description' => 'Do you want slider autoplay?.'
                ]
        );
        $this->add_control(
                'carousel_autoplay_speed',
                $this->style,
                [
                    'label' => __('Autoplay Speed', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::NUMBER,
                    'default' => 2000,
                    'condition' => [
                        'carousel_autoplay' => 'yes',
                    ],
                    'description' => 'Set Autoplay Speed, Set with millisecond.'
                ]
        );
        $this->add_control(
                'carousel_speed',
                $this->style,
                [
                    'label' => __('Animation Speed', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::NUMBER,
                    'default' => 500,
                    'description' => 'Set Animation Speed, Set with millisecond.'
                ]
        );
        $this->add_control(
                'carousel_pause_on_hover',
                $this->style,
                [
                    'label' => __('Pause on Hover', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::SWITCHER,
                    'loader' => TRUE,
                    'default' => 'yes',
                    'yes' => __('Yes', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'no' => __('No', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'return_value' => 'yes',
                    'description' => 'Do you want Pause on Hover.'
                ]
        );
        $this->add_control(
                'carousel_infinite',
                $this->style,
                [
                    'label' => __('Infinite Loop', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::SWITCHER,
                    'loader' => TRUE,
                    'default' => 'yes',
                    'yes' => __('Yes', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'no' => __('No', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'return_value' => 'yes',
                    'description' => 'Do you want Infinite Loop.'
                ]
        );
        $this->add_control(
                'carousel_adaptive_height',
                $this->style,
                [
                    'label' => __('Adaptive Height', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::SWITCHER,
                    'loader' => TRUE,
                    'default' => 'yes',
                    'yes' => __('Yes', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'no' => __('No', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'return_value' => 'yes',
                    'description' => 'Do you want auto height.'
                ]
        );
        $this->add_control(
                'carousel_center_mode',
                $this->style,
                [
                    'label' => __('Center Mode', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::SWITCHER,
                    'loader' => TRUE,
                    'default' => 'no',
                    'yes' => __('Yes', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'no' => __('No', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'return_value' => 'yes',
                    'description' => 'Do you want center mode Options?'
                ]
        );
        $this->add_control(
                'carousel_show_arrows',
                $this->style,
                [
                    'label' => __('Arrows', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::SWITCHER,
                    'loader' => TRUE,
                    'default' => 'yes',
                    'yes' => __('Yes', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'no' => __('No', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'return_value' => 'yes',
                    'description' => 'Do you want Arrows for navigation.'
                ]
        );
        $this->add_control(
                'carousel_show_dots',
                $this->style,
                [
                    'label' => __('Dots', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::SWITCHER,
                    'loader' => TRUE,
                    'default' => 'no',
                    'yes' => __('Yes', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'no' => __('No', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'return_value' => 'yes',
                    'description' => 'Do you want Dots for pagination.'
                ]
        );

        $this->end_controls_section();
    }

    public function all_style() {
        $a = 'button%';
        $b = 'general%';
        $c = 'square%';
        $d = 'caption%';
        $alldata = $this->wpdb->get_results($this->wpdb->prepare("SELECT id, name FROM $this->parent_table WHERE style_name LIKE %s OR style_name LIKE %s OR style_name LIKE %s OR style_name LIKE %s ORDER by id ASC", $a, $b, $c, $d), ARRAY_A);
        $st = [];
        foreach ($alldata as $k => $value) {
            $st[$value['id']] = $value['name'] != '' ? $value['name'] : 'Shortcode ID ' . $value['id'];
        }

        return $st;
    }

    public function register_carousel_arrows_settings() {
        $this->start_controls_section(
                'carousel-arrow',
                [
                    'label' => esc_html__('Carousel Arrows', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'showing' => TRUE,
                    'condition' => [
                        'carousel_show_arrows' => 'yes',
                    ],
                ]
        );

        $this->start_controls_tabs(
                'oxi-image-hover-start-tabs',
                [
                    'options' => [
                        'normal' => esc_html__('Left Arrow Icon', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'hover' => esc_html__('Right Arrow Icon', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    ]
                ]
        );
        $this->start_controls_tab();
        $this->add_control(
                'carousel_left_arrow',
                $this->style,
                [
                    'label' => __('Left Arrow', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::ICON,
                    'default' => 'fas fa-chevron-left',
                    'description' => 'Select Left Arrow Icon From Icon List.'
                ]
        );
        $this->end_controls_tab();
        $this->start_controls_tab();
        $this->add_control(
                'carousel_right_arrow',
                $this->style,
                [
                    'label' => __('Right Arrow', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::ICON,
                    'default' => 'fas fa-chevron-right',
                    'description' => 'Select Right Arrow Icon From Icon List.'
                ]
        );
        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->add_responsive_control(
                'carousel_arrows_size',
                $this->style,
                [
                    'label' => __('Size', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::SLIDER,
                    'separator' => TRUE,
                    'default' => [
                        'unit' => 'px',
                        'size' => '20',
                    ],
                    'range' => [
                        'px' => [
                            'min' => 1,
                            'max' => 200,
                            'step' => 1,
                        ],
                        '%' => [
                            'min' => 0,
                            'max' => 50,
                            'step' => 1,
                        ],
                        'em' => [
                            'min' => 0,
                            'max' => 20,
                            'step' => .1,
                        ],
                    ],
                    'selector' => [
                        '{{WRAPPER}} .oxi_carousel_arrows .oxi-icons' => 'font-size:{{SIZE}}{{UNIT}}; line-height:{{SIZE}}{{UNIT}};',
                    ],
                    'description' => 'Set Arrow icon size.'
                ]
        );
        $this->add_responsive_control(
                'carousel_arrows_position_x',
                $this->style,
                [
                    'label' => __('Position X', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::SLIDER,
                    'default' => [
                        'unit' => 'px',
                        'size' => '25',
                    ],
                    'range' => [
                        'px' => [
                            'min' => -1200,
                            'max' => 1200,
                            'step' => 1,
                        ],
                        '%' => [
                            'min' => -100,
                            'max' => 100,
                            'step' => 1,
                        ],
                        'em' => [
                            'min' => -100,
                            'max' => 100,
                            'step' => .1,
                        ],
                    ],
                    'selector' => [
                        '{{WRAPPER}} .oxi_carousel_arrows.oxi_carousel_prev' => 'left:{{SIZE}}{{UNIT}}; right:auto;',
                        '{{WRAPPER}} .oxi_carousel_arrows.oxi_carousel_next' => 'right:{{SIZE}}{{UNIT}}; left:auto',
                    ],
                    'description' => 'Set Arrow icon Posiztion X.'
                ]
        );
        $this->add_responsive_control(
                'carousel_arrows_position_y',
                $this->style,
                [
                    'label' => __('Position Y', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::SLIDER,
                    'default' => [
                        'unit' => '%',
                        'size' => '50',
                    ],
                    'range' => [
                        'px' => [
                            'min' => -1200,
                            'max' => 1200,
                            'step' => 1,
                        ],
                        '%' => [
                            'min' => -100,
                            'max' => 100,
                            'step' => 1,
                        ],
                        'em' => [
                            'min' => -100,
                            'max' => 100,
                            'step' => .1,
                        ],
                    ],
                    'selector' => [
                        '{{WRAPPER}} .oxi_carousel_arrows' => 'top:{{SIZE}}{{UNIT}}; transform: translateY(-{{SIZE}}{{UNIT}});',
                    ],
                    'description' => 'Set Arrow icon Posiztion Y.'
                ]
        );
        $this->start_controls_tabs(
                'oxi-image-hover-start-tabs',
                [
                    'options' => [
                        'normal' => esc_html__('Normal', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'hover' => esc_html__('Hover', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    ]
                ]
        );
        $this->start_controls_tab();
        $this->add_control(
                'carousel_arrows_color',
                $this->style,
                [
                    'label' => __('Color', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::COLOR,
                    'default' => '#ffffff',
                    'selector' => [
                        '{{WRAPPER}} .oxi_carousel_arrows .oxi-icons' => 'color: {{VALUE}};',
                    ],
                    'description' => 'Select Arrow icon Color.'
                ]
        );
        $this->add_control(
                'carousel_arrows_background',
                $this->style,
                [
                    'label' => __('Background', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::GRADIENT,
                    'default' => 'rgba(171, 0, 201, 1)',
                    'selector' => [
                        '{{WRAPPER}} .oxi_carousel_arrows .oxi-icons' => 'background: {{VALUE}};',
                    ],
                    'description' => 'Confirm Arrow Background Color.'
                ]
        );
        $this->add_group_control(
                'carousel_arrows_border',
                $this->style,
                [
                    'type' => Controls::BORDER,
                    'selector' => [
                        '{{WRAPPER}} .oxi_carousel_arrows .oxi-icons' => ''
                    ],
                    'description' => 'Confirm Arrow Border with Customization.'
                ]
        );
        $this->add_group_control(
                'carousel_arrows_shadow',
                $this->style,
                [
                    'type' => Controls::BOXSHADOW,
                    'selector' => [
                        '{{WRAPPER}} .oxi_carousel_arrows .oxi-icons' => '',
                    ],
                    'description' => 'Confirm Arrow Background Shadow.'
                ]
        );
        $this->end_controls_tab();
        $this->start_controls_tab();
        $this->add_control(
                'carousel_arrows_color_hover',
                $this->style,
                [
                    'label' => __('Color', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::COLOR,
                    'default' => '#ffffff',
                    'selector' => [
                        '{{WRAPPER}} .oxi_carousel_arrows .oxi-icons:hover' => 'color: {{VALUE}};',
                    ],
                    'description' => 'Confirm Arrow hover icon Color.'
                ]
        );
        $this->add_control(
                'carousel_arrows_background_hover',
                $this->style,
                [
                    'label' => __('Background', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::GRADIENT,
                    'default' => 'rgba(171, 0, 201, 1)',
                    'selector' => [
                        '{{WRAPPER}} .oxi_carousel_arrows .oxi-icons:hover' => 'background: {{VALUE}};',
                    ],
                    'description' => 'Confirm Arrow hover icon Background Color.'
                ]
        );
        $this->add_group_control(
                'carousel_arrows_border_hover',
                $this->style,
                [
                    'type' => Controls::BORDER,
                    'selector' => [
                        '{{WRAPPER}} .oxi_carousel_arrows .oxi-icons:hover' => ''
                    ],
                    'description' => 'Confirm Arrow hover Border with Customization.'
                ]
        );
        $this->add_group_control(
                'carousel_arrows_shadow_hover',
                $this->style,
                [
                    'type' => Controls::BOXSHADOW,
                    'selector' => [
                        '{{WRAPPER}} .oxi_carousel_arrows .oxi-icons:hover' => '',
                    ],
                    'description' => 'Confirm Arrow hover Background Shadow.'
                ]
        );
        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->add_responsive_control(
                'carousel_arrows_radius',
                $this->style,
                [
                    'label' => __('Border Radius', OXI_IMAGE_HOVER_TEXTDOMAIN),
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
                        '{{WRAPPER}} .oxi_carousel_arrows .oxi-icons' => 'border-radius:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                    'description' => 'Allows you to add rounded corners at hover to Arrow with options.',
                ]
        );
        $this->add_responsive_control(
                'carousel_arrows_padding',
                $this->style,
                [
                    'label' => __('Padding', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::DIMENSIONS,
                    'default' => [
                        'unit' => 'px',
                        'size' => '10',
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
                        '{{WRAPPER}} .oxi_carousel_arrows .oxi-icons' => 'padding:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                    'description' => 'Generate space around a Arrow, inside of any defined borders or Background.',
                ]
        );
        $this->end_controls_section();
    }

    public function register_carousel_dots_settings() {
        $this->start_controls_section(
                'shortcode-addons',
                [
                    'label' => esc_html__('Carousel Dots', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'showing' => FALSE,
                    'condition' => [
                        'carousel_show_dots' => 'yes',
                    ],
                ]
        );
        $this->add_responsive_control(
                'carousel_dots_position_width',
                $this->style,
                [
                    'label' => __('Width', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::SLIDER,
                    'default' => [
                        'unit' => 'px',
                        'size' => '10',
                    ],
                    'range' => [
                        'px' => [
                            'min' => 1,
                            'max' => 200,
                            'step' => 1,
                        ],
                        '%' => [
                            'min' => 1,
                            'max' => 50,
                            'step' => 1,
                        ],
                        'em' => [
                            'min' => 1,
                            'max' => 15,
                            'step' => 1,
                        ],
                    ],
                    'selector' => [
                        '{{WRAPPER}} .oxi_carousel_dots li' => 'width: {{SIZE}}{{UNIT}};',
                    ],
                    'description' => 'Confirm Dots Width with multiple options.',
                ]
        );
        $this->add_responsive_control(
                'carousel_dots_position_height',
                $this->style,
                [
                    'label' => __('Height', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::SLIDER,
                    'default' => [
                        'unit' => 'px',
                        'size' => '20',
                    ],
                    'range' => [
                        'px' => [
                            'min' => 0,
                            'max' => 100,
                            'step' => 1,
                        ],
                        '%' => [
                            'min' => 0,
                            'max' => 50,
                            'step' => 1,
                        ],
                        'em' => [
                            'min' => 0,
                            'max' => 10,
                            'step' => 1,
                        ],
                    ],
                    'selector' => [
                        '{{WRAPPER}} .oxi_carousel_dots li' => 'height: {{SIZE}}{{UNIT}};',
                    ],
                    'description' => 'Confirm Dots Height with multiple options.',
                ]
        );
        $this->add_responsive_control(
                'carousel_dots_position_Y',
                $this->style,
                [
                    'label' => __('Position Y', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::SLIDER,
                    'default' => [
                        'unit' => '%',
                        'size' => '35',
                    ],
                    'range' => [
                        'px' => [
                            'min' => -900,
                            'max' => 900,
                            'step' => 1,
                        ],
                        '%' => [
                            'min' => -100,
                            'max' => 100,
                            'step' => 1,
                        ],
                        'em' => [
                            'min' => -100,
                            'max' => 100,
                            'step' => 1,
                        ],
                    ],
                    'selector' => [
                        '{{WRAPPER}} .oxi_carousel_dots' => 'bottom: {{SIZE}}{{UNIT}};',
                    ],
                    'description' => 'Confirm Dots position with Position Y.',
                ]
        );
        $this->add_responsive_control(
                'carousel_dots_spacing',
                $this->style,
                [
                    'label' => __('Spacing', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::SLIDER,
                    'default' => [
                        'unit' => 'px',
                        'size' => '3',
                    ],
                    'range' => [
                        'px' => [
                            'min' => 0,
                            'max' => 100,
                            'step' => 1,
                        ],
                        '%' => [
                            'min' => 0,
                            'max' => 30,
                            'step' => 1,
                        ],
                        'em' => [
                            'min' => 0,
                            'max' => 5,
                            'step' => 1,
                        ],
                    ],
                    'selector' => [
                        '{{WRAPPER}} .oxi_carousel_dots li' => 'margin: 0px {{SIZE}}{{UNIT}};',
                    ],
                    'description' => 'Confirm Dots Spacing with multiple options.',
                ]
        );
        $this->start_controls_tabs(
                'shortcode-addons-start-tabs',
                [
                    'options' => [
                        'normal' => esc_html__('Normal', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'hover' => esc_html__('Hover', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'active' => esc_html__('Active', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    ]
                ]
        );
        $this->start_controls_tab();
        $this->add_control(
                'carousel_dots_bg_color',
                $this->style,
                [
                    'label' => __('Background', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::COLOR,
                    'default' => 'rgb(0, 0, 0)',
                    'oparetor' => 'RGB',
                    'selector' => [
                        '{{WRAPPER}} .oxi_carousel_dots li button:before' => 'background: {{VALUE}};',
                    ],
                    'description' => 'Confirm Dots Background Color.',
                ]
        );
        $this->add_group_control(
                'carousel_dots_border',
                $this->style,
                [
                    'type' => Controls::BORDER,
                    'selector' => [
                        '{{WRAPPER}} .oxi_carousel_dots li button:before' => '',
                    ],
                    'description' => 'customize Dots border with multiple options.',
                ]
        );
        $this->end_controls_tab();

        $this->start_controls_tab();

        $this->add_control(
                'carousel_dots_bg_color_hover',
                $this->style,
                [
                    'label' => __('Background', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::COLOR,
                    'default' => 'rgb(119, 119, 119)',
                    'oparetor' => 'RGB',
                    'selector' => [
                        '{{WRAPPER}} .oxi_carousel_dots li:hover button:before' => 'background: {{VALUE}};',
                    ],
                    'description' => 'Confirm Dots hover Background Color.',
                ]
        );
        $this->add_group_control(
                'carousel_dots_border_hover',
                $this->style,
                [
                    'type' => Controls::BORDER,
                    'selector' => [
                        '{{WRAPPER}} .oxi_carousel_dots li:hover button:before' => '',
                    ],
                    'description' => 'Customize Dots hover border with multiple options.',
                ]
        );
        $this->end_controls_tab();
        $this->start_controls_tab();

        $this->add_control(
                'carousel_dots_bg_color_active',
                $this->style,
                [
                    'label' => __('Background', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::COLOR,
                    'default' => '#AB00C9',
                    'oparetor' => 'RGB',
                    'selector' => [
                        '{{WRAPPER}} .oxi_carousel_dots li.slick-active button:before' => 'background: {{VALUE}};',
                    ],
                    'description' => 'Confirm Dots hover Background Color.',
                ]
        );
        $this->add_group_control(
                'carousel_dots_border_active',
                $this->style,
                [
                    'type' => Controls::BORDER,
                    'selector' => [
                        '{{WRAPPER}} .oxi_carousel_dots li.slick-active button:before' => '',
                    ],
                    'description' => 'Customize Dots Active border with multiple options.',
                ]
        );
        $this->end_controls_tab();
        $this->end_controls_tabs();
        $this->add_responsive_control(
                'carousel_dots_border_radius_normal',
                $this->style,
                [
                    'label' => __('Border Radius', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::DIMENSIONS,
                    'separator' => TRUE,
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
                            'min' => 0,
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
                        '{{WRAPPER}} .oxi_carousel_dots li button:before' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                    'description' => 'Allows you to add rounded  to Dots with options.',
                ]
        );
        $this->end_controls_section();
    }

    /**
     * Template Modal opener
     * Define Multiple Data With Single Data
     *
     * @since 9.3.0
     */
    public function modal_opener() {

    }

}
