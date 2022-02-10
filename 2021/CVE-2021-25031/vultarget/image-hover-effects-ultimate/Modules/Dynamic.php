<?php

namespace OXI_IMAGE_HOVER_PLUGINS\Modules;

if (!defined('ABSPATH')) {
    exit;
}

/**
 *
 * @author biplo
 */
use OXI_IMAGE_HOVER_PLUGINS\Classes\Controls as Controls;

trait Dynamic {

    use \OXI_IMAGE_HOVER_PLUGINS\Modules\Display\Files\Admin_Query;

    public function register_dynamic_control() {
        $this->start_controls_section(
                'oxi-image-hover', [
            'label' => esc_html__('Dynamic Settings', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'showing' => true,
                ]
        );

        $this->add_control(
                'image_hover_dynamic_note',
                $this->style,
                [
                    'label' => __('Note', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::HEADING,
                    'description' => 'Dynamic Property will works only at live Sites. Kindly use shortcode at page or post then check it.'
                ]
        );

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
                'image_hover_dynamic_content', $this->style,
                [
                    'label' => __('Dynamic Content', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::SWITCHER,
                    'default' => 'no',
                    'yes' => __('Yes', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'no' => __('No', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'return_value' => 'yes',
                    'description' => 'Wanna Dynamic Content?.',
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

    /*
     * @return void
     * Start Post Query for Display Post
     */

    public function register_dynamic_query() {
        $this->start_controls_section(
                'image_hover_dynamic_content_tabs',
                [
                    'label' => esc_html__('Post Query', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'showing' => TRUE,
                    'condition' => [
                        'image_hover_dynamic_content' => 'yes',
                    ],
                ]
        );
        $this->add_control(
                'image_hover_dynamic_content_type',
                $this->style,
                [
                    'label' => __('Post Type', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::SELECT,
                    'default' => 'post',
                    'options' => $this->post_type(),
                    'description' => 'Select Post Type for Query.'
                ]
        );
        $this->add_control(
                'image_hover_dynamic_content_author',
                $this->style,
                [
                    'label' => __('Author', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::SELECT,
                    'multiple' => true,
                    'options' => $this->post_author(),
                    'description' => 'Confirm Author list if you wanna those author post only.'
                ]
        );
        foreach ($this->post_type() as $key => $value) {
            if ($key != 'page') :
                $this->add_control(
                        $key . '_category',
                        $this->style,
                        [
                            'label' => __(' Category', OXI_IMAGE_HOVER_TEXTDOMAIN),
                            'type' => Controls::SELECT,
                            'multiple' => true,
                            'options' => $this->post_category($key),
                            'condition' => [
                                'image_hover_dynamic_content_type' => $key
                            ],
                            'description' => 'Confirm Category list if you wanna those Category post only.',
                        ]
                );
                $this->add_control(
                        $key . '_tag',
                        $this->style,
                        [
                            'label' => __(' Tags', OXI_IMAGE_HOVER_TEXTDOMAIN),
                            'type' => Controls::SELECT,
                            'multiple' => true,
                            'options' => $this->post_tags($key),
                            'condition' => [
                                'image_hover_dynamic_content_type' => $key
                            ],
                            'description' => 'Confirm Post Tags if you wanna show those tags post only.',
                        ]
                );
            endif;
        }
        $this->add_control(
                'image_hover_dynamic_content_offset',
                $this->style,
                [
                    'label' => __('Offset', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::NUMBER,
                    'default' => 0,
                    'description' => 'Confirm Post Offset.',
                ]
        );
        $this->add_control(
                'image_hover_dynamic_content_orderby',
                $this->style,
                [
                    'label' => __(' Order By', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::SELECT,
                    'default' => 'ID',
                    'options' => [
                        'ID' => 'Post ID',
                        'author' => 'Post Author',
                        'title' => 'Title',
                        'date' => 'Date',
                        'modified' => 'Last Modified Date',
                        'parent' => 'Parent Id',
                        'rand' => 'Random',
                        'comment_count' => 'Comment Count',
                        'menu_order' => 'Menu Order',
                    ],
                    'description' => 'Set Post Query Order by Condition.',
                ]
        );

        $this->add_control(
                'image_hover_dynamic_content_ordertype',
                $this->style,
                [
                    'label' => __(' Order Type', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::SELECT,
                    'options' => [
                        'asc' => 'Ascending',
                        'desc' => 'Descending',
                    ],
                    'description' => 'Set Post Query Order by Condition.',
                ]
        );
        $this->add_control(
                'image_hover_dynamic_post_excerpt',
                $this->style,
                [
                    'label' => __('Excerpt Word Limit', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::NUMBER,
                    'min' => 1,
                    'description' => 'Confirm Excerpt Word Limit.',
                ]
        );
        $this->add_control(
                'image_hover_dynamic_content_thumb_sizes',
                $this->style,
                [
                    'label' => __('Image Size', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::SELECT,
                    'options' => $this->thumbnail_sizes(),
                    'description' => 'Set Image Thumbnail Size.',
                ]
        );
        $this->end_controls_section();
    }

    public function register_dynamic_load_more_button() {
        $this->start_controls_section(
                'image_hover_dynamic_load',
                [
                    'label' => esc_html__('Load More Button', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'showing' => true,
                    'condition' => [
                        'image_hover_dynamic_load' => 'yes',
                        'image_hover_dynamic_load_type' => 'button'
                    ],
                ]
        );

        $this->add_control(
                'image_hover_dynamic_load_button_text', $this->style, [
            'label' => __('Button Text', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'type' => Controls::TEXT,
            'default' => 'Load More',
            'placeholder' => 'Load More Button',
            'description' => 'Add Button text as Unicode also supported.',
                ]
        );

        $this->add_control(
                'image_hover_dynamic_load_button_position',
                $this->style,
                [
                    'label' => __('Position', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::CHOOSE,
                    'operator' => Controls::OPERATOR_ICON,
                    'default' => 'left',
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
                        '{{WRAPPER}} .oxi-image-hover-load-more-button-wrap' => 'text-align:{{VALUE}};',
                    ],
                    'description' => 'Add Button text as Unicode also supported.',
                ]
        );

        $this->add_group_control(
                'image_hover_dynamic_load_button_typho', $this->style, [
            'type' => Controls::TYPOGRAPHY,
            'selector' => [
                '{{WRAPPER}} .oxi-image-hover-load-more-button-wrap .oxi-image-load-more-button' => '',
                '{{WRAPPER}} .oxi-image-hover-load-more-button-wrap .oxi-image-load-more-button .oxi-image-hover-loader button__loader' => '',
                '{{WRAPPER}} .oxi-image-hover-load-more-button-wrap .oxi-image-load-more-button span' => '',
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
                'image_hover_dynamic_load_button_color', $this->style, [
            'label' => __('Color', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'type' => Controls::COLOR,
            'default' => '#ffffff',
            'selector' => [
                '{{WRAPPER}} .oxi-image-hover-load-more-button-wrap .oxi-image-load-more-button' => 'color: {{VALUE}};',
                '{{WRAPPER}} .oxi-image-hover-load-more-button-wrap .oxi-image-load-more-button .oxi-image-hover-loader button__loader' => 'color: {{VALUE}};',
                '{{WRAPPER}} .oxi-image-hover-load-more-button-wrap .oxi-image-load-more-button span' => 'color: {{VALUE}};',
                '{{WRAPPER}} .oxi-image-hover-load-more-button-wrap .oxi-image-load-more-button:hover' => 'color: {{VALUE}};',
                '{{WRAPPER}} .oxi-image-hover-load-more-button-wrap .oxi-image-load-more-button:hover .oxi-image-hover-loader button__loader' => 'color: {{VALUE}};',
                '{{WRAPPER}} .oxi-image-hover-load-more-button-wrap .oxi-image-load-more-button:hover span' => 'color: {{VALUE}};',
            ],
            'description' => 'Customize your button color.',
                ]
        );
        $this->add_control(
                'image_hover_dynamic_load_button_background', $this->style, [
            'label' => __('Background', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'type' => Controls::GRADIENT,
            'default' => 'rgba(171, 0, 201, 1)',
            'selector' => [
                '{{WRAPPER}} .oxi-image-hover-load-more-button-wrap .oxi-image-load-more-button' => 'background: {{VALUE}};',
            ],
            'description' => 'Customize your button Background Color.',
                ]
        );
        $this->add_group_control(
                'image_hover_dynamic_load_button_border',
                $this->style,
                [
                    'type' => Controls::BORDER,
                    'selector' => [
                        '{{WRAPPER}} .oxi-image-hover-load-more-button-wrap .oxi-image-load-more-button' => ''
                    ],
                    'description' => 'Customize your button border color.',
                ]
        );
        $this->add_group_control(
                'image_hover_dynamic_load_button_tx_shadow', $this->style, [
            'type' => Controls::TEXTSHADOW,
            'selector' => [
                '{{WRAPPER}} .oxi-image-hover-load-more-button-wrap .oxi-image-load-more-button span' => '',
            ],
            'description' => 'Customize your button Shadow.',
                ]
        );
        $this->end_controls_tab();
        $this->start_controls_tab();
        $this->add_control(
                'image_hover_dynamic_load_button_hover_color', $this->style, [
            'label' => __('Color', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'type' => Controls::COLOR,
            'default' => '#ffffff',
            'selector' => [
                '{{WRAPPER}} > .oxi-image-hover-load-more-button-wrap > .oxi-image-load-more-button:hover' => 'color: {{VALUE}};',
                '{{WRAPPER}} > .oxi-image-hover-load-more-button-wrap > .oxi-image-load-more-button:hover .oxi-image-hover-loader button__loader' => 'color: {{VALUE}};',
                '{{WRAPPER}} > .oxi-image-hover-load-more-button-wrap > .oxi-image-load-more-button:hover span' => 'color: {{VALUE}};',
            ],
            'description' => 'Color property is used to set the Hover color of the Button.',
                ]
        );
        $this->add_control(
                'image_hover_dynamic_load_button_hover_background', $this->style, [
            'label' => __('Background', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'type' => Controls::GRADIENT,
            'default' => '#ffffff',
            'selector' => [
                '{{WRAPPER}} > .oxi-image-hover-load-more-button-wrap > .oxi-image-load-more-button:hover' => 'background: {{VALUE}};',
            ],
            'description' => 'Background property is used to set the Hover Background of the Button.',
                ]
        );
        $this->add_group_control(
                'image_hover_dynamic_load_button_hover_border',
                $this->style,
                [
                    'type' => Controls::BORDER,
                    'selector' => [
                        '{{WRAPPER}} >  .oxi-image-hover-load-more-button-wrap  > .oxi-image-load-more-button:hover' => ''
                    ],
                    'description' => 'Border property is used to set the Hover Border of the Button.',
                ]
        );
        $this->add_group_control(
                'image_hover_dynamic_load_button_hover_tx_shadow', $this->style, [
            'type' => Controls::TEXTSHADOW,
            'selector' => [
                '{{WRAPPER}} >  .oxi-image-hover-load-more-button-wrap >  .oxi-image-load-more-button:hover span' => '',
            ],
            'description' => 'Text Shadow property adds shadow to Hover Button.',
                ]
        );
        $this->end_controls_tab();
        $this->end_controls_tabs();

        $this->add_group_control(
                'image_hover_dynamic_load_button_boxshadow', $this->style, [
            'type' => Controls::BOXSHADOW,
            'selector' => [
                '{{WRAPPER}} .oxi-image-hover-load-more-button-wrap .oxi-image-load-more-button' => '',
            ],
            'description' => 'Allows you at hover to attaches one or more shadows into Button.',
                ]
        );
        $this->add_responsive_control(
                'image_hover_dynamic_load_button_radius', $this->style, [
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
                '{{WRAPPER}} .oxi-image-hover-load-more-button-wrap .oxi-image-load-more-button' => 'border-radius:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                '{{WRAPPER}} .oxi-image-hover-load-more-button-wrap .oxi-image-load-more-button:hover' => 'border-radius:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
            'description' => 'Allows you to add rounded corners to Button with options.',
                ]
        );
        $this->add_responsive_control(
                'image_hover_dynamic_load_button_padding', $this->style, [
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
                '{{WRAPPER}} .oxi-image-hover-load-more-button-wrap .oxi-image-load-more-button' => 'padding:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
            'description' => 'Generate space around a Button, inside of any defined borders or Background.',
                ]
        );
        $this->add_responsive_control(
                'image_hover_dynamic_load_button_margin', $this->style, [
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
                '{{WRAPPER}} .oxi-image-hover-load-more-button-wrap' => 'padding:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
            'description' => 'Generate space around a Button, Outside of Content.',
                ]
        );
        $this->end_controls_section();
    }

    public function register_carousel_query_settings() {
        $this->start_controls_section(
                'image_hover_dynamic_cartabs',
                [
                    'label' => esc_html__('Carousel Query', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'showing' => TRUE,
                    'condition' => [
                        'image_hover_dynamic_carousel' => 'yes',
                    ],
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
                    'default' => 'no',
                    'yes' => __('Yes', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'no' => __('No', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'return_value' => 'yes',
                    'description' => 'Do you want Dots for pagination.'
                ]
        );

        $this->end_controls_section();
    }

    public function register_carousel_arrows_settings() {
        $this->start_controls_section(
                'carousel-arrow',
                [
                    'label' => esc_html__('Carousel Arrows', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'showing' => TRUE,
                    'condition' => [
                        'image_hover_dynamic_carousel' => 'yes',
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
                        'size' => '10',
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
                        'size' => '15',
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
                        'image_hover_dynamic_carousel' => 'yes',
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
                        'size' => '10',
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
                        'size' => '0',
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
                        'size' => '10',
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

        $this->register_dynamic_query();
        $this->register_carousel_query_settings();
        $this->register_carousel_arrows_settings();

        $this->register_dynamic_load_more_button();

        $this->end_section_devider();

        $this->end_section_tabs();
    }

}
