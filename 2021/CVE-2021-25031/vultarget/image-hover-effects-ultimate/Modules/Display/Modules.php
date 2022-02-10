<?php

namespace OXI_IMAGE_HOVER_PLUGINS\Modules\Display;

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

    use \OXI_IMAGE_HOVER_PLUGINS\Modules\Display\Files\Admin_Query;

    public function register_controls() {
        $this->start_section_header(
                'oxi-image-hover-start-tabs', [
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

    public function register_general_tabs() {
        $this->start_section_tabs(
                'oxi-image-hover-start-tabs', [
            'condition' => [
                'oxi-image-hover-start-tabs' => 'general-settings',
            ],
                ]
        );
        $this->start_section_devider();
        $this->register_post_query_settings();

        $this->end_section_devider();
        $this->start_section_devider();
        $this->register_post_condition_settings();

        $this->end_section_devider();
        $this->end_section_tabs();
    }

    /*
     * @return void
     * Start Post Query for Display Post
     */

    public function register_post_query_settings() {
        $this->start_controls_section(
                'display-post',
                [
                    'label' => esc_html__('Post Query', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'showing' => TRUE,
                ]
        );
        $this->add_control(
                'display_post_post_type',
                $this->style,
                [
                    'label' => __('Post Type', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'loader' => TRUE,
                    'type' => Controls::SELECT,
                    'default' => 'post',
                    'options' => $this->post_type(),
                    'description' => 'Select Post Type for Query.'
                ]
        );
        $this->add_control(
                'display_post_author',
                $this->style,
                [
                    'label' => __('Author', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'loader' => TRUE,
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
                            'loader' => TRUE,
                            'options' => $this->post_category($key),
                            'condition' => [
                                'display_post_post_type' => $key
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
                            'loader' => TRUE,
                            'options' => $this->post_tags($key),
                            'condition' => [
                                'display_post_post_type' => $key
                            ],
                            'description' => 'Confirm Post Tags if you wanna show those tags post only.',
                        ]
                );
            endif;

            $this->add_control(
                    $key . '_include',
                    $this->style,
                    [
                        'label' => __(' Include Post', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'type' => Controls::SELECT,
                        'multiple' => true,
                        'loader' => TRUE,
                        'options' => $this->post_include($key),
                        'condition' => [
                            'display_post_post_type' => $key
                        ],
                        'description' => 'Only those post will viewing in Post list.',
                    ]
            );
            $this->add_control(
                    $key . '_exclude',
                    $this->style,
                    [
                        'label' => __(' Exclude Post', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        'type' => Controls::SELECT,
                        'multiple' => true,
                        'loader' => TRUE,
                        'options' => $this->post_exclude($key),
                        'condition' => [
                            'display_post_post_type' => $key
                        ],
                        'description' => 'Those Post can\'t viewing.',
                    ]
            );
        }
        $this->end_controls_section();
    }

    /*
     * @return void
     * Start Post Condtion for Display Post
     */

    public function register_post_condition_settings() {
        $this->start_controls_section(
                'display-post',
                [
                    'label' => esc_html__('Post Condition', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'showing' => TRUE,
                ]
        );
        $this->add_control(
                'display_post_style',
                $this->style,
                [
                    'label' => __('Post Style', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'loader' => TRUE,
                    'type' => Controls::SELECT,
                    'options' => $this->post_style(),
                    'description' => 'Customize your Display Post Style based on your Created Effects. Kindly save and Reload after style selected.'
                ]
        );
        $this->add_control(
                'display_post_per_page',
                $this->style,
                [
                    'label' => __('Post Per Page', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::NUMBER,
                    'loader' => TRUE,
                    'min' => 1,
                    'description' => 'How many Post You want to Viewing into page.',
                ]
        );
        $this->add_control(
                'display_post_excerpt',
                $this->style,
                [
                    'label' => __('Excerpt Word Limit', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::NUMBER,
                    'loader' => TRUE,
                    'min' => 1,
                    'description' => 'Confirm Excerpt Word Limit.',
                ]
        );
        $this->add_control(
                'display_post_offset',
                $this->style,
                [
                    'label' => __('Offset', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::NUMBER,
                    'loader' => TRUE,
                    'description' => 'Confirm Excerpt Word Limit.',
                ]
        );
        $this->add_control(
                'display_post_orderby',
                $this->style,
                [
                    'label' => __(' Order By', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::SELECT,
                    'default' => 'ID',
                    'loader' => TRUE,
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
                'display_post_ordertype',
                $this->style,
                [
                    'label' => __(' Order Type', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::SELECT,
                    'loader' => TRUE,
                    'options' => [
                        'asc' => 'Ascending',
                        'desc' => 'Descending',
                    ],
                    'description' => 'Set Post Query Order by Condition.',
                ]
        );
        $this->add_control(
                'display_post_thumb_sizes',
                $this->style,
                [
                    'label' => __('Image Size', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::SELECT,
                    'loader' => TRUE,
                    'options' => $this->thumbnail_sizes(),
                    'description' => 'Set Image Thumbnail Size.',
                ]
        );
        $this->add_control(
                'display_post_load_more', $this->style,
                [
                    'label' => __('Load More', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::SWITCHER,
                    'default' => 'no',
                    'yes' => __('Yes', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'no' => __('No', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'return_value' => 'yes',
                    'description' => 'Wanna load More Options?.',
                ]
        );
        $this->add_control(
                'display_post_load_more_type', $this->style,
                [
                    'label' => __('Load More Type', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::CHOOSE,
                    'loader' => TRUE,
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
                        'display_post_load_more' => 'yes'
                    ],
                    'description' => 'Select Load More Type, As we offer Infinite loop or Button.',
                ]
        );
        $this->end_controls_section();

        $this->start_controls_section(
                'display-post',
                [
                    'label' => esc_html__('Load More Button', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'showing' => false,
                    'condition' => [
                        'display_post_load_more' => 'yes',
                        'display_post_load_more_type' => 'button'
                    ],
                ]
        );

        $this->add_control(
                'display_post_load_button_text', $this->style, [
            'label' => __('Button Text', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'type' => Controls::TEXT,
            'default' => 'Load More',
            'placeholder' => 'Load More Button',
            'selector' => [
                '{{WRAPPER}} .oxi-image-hover-load-more-button-wrap .oxi-image-load-more-button span' => '',
            ],
            'description' => 'Add Button text as Unicode also supported.',
                ]
        );

        $this->add_control(
                'display_post_load_button_position',
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
                'display_post_load_button_typho', $this->style, [
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
                'display_post_load_button_color', $this->style, [
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
                'display_post_load_button_background', $this->style, [
            'label' => __('Background', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'type' => Controls::GRADIENT,
            'default' => 'rgba(171, 0, 201, 1)',
            'selector' => [
                '{{WRAPPER}} .oxi-image-hover-load-more-button-wrap .oxi-image-load-more-button' => 'background: {{VALUE}};',
                '{{WRAPPER}} .oxi-image-hover-load-more-button-wrap .oxi-image-load-more-button:hover' => 'background: {{VALUE}};',
            ],
            'description' => 'Customize your button Background Color.',
                ]
        );
        $this->add_group_control(
                'display_post_load_button_border',
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
                'display_post_load_button_tx_shadow', $this->style, [
            'type' => Controls::TEXTSHADOW,
            'selector' => [
                '{{WRAPPER}} .oxi-image-hover-load-more-button-wrap .oxi-image-load-more-button span' => '',
            ],
            'description' => 'Customize your button Shadow.',
                ]
        );

        $this->add_responsive_control(
                'display_post_load_button_radius', $this->style, [
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
        $this->add_group_control(
                'display_post_load_button_boxshadow', $this->style, [
            'type' => Controls::BOXSHADOW,
            'selector' => [
                '{{WRAPPER}} .oxi-image-hover-load-more-button-wrap .oxi-image-load-more-button' => '',
                '{{WRAPPER}} .oxi-image-hover-load-more-button-wrap .oxi-image-load-more-button:hover' => '',
            ],
            'description' => 'Allows you to attaches one or more shadows into Button.',
                ]
        );
        $this->end_controls_tab();
        $this->start_controls_tab();
        $this->add_control(
                'display_post_load_button_hover_color', $this->style, [
            'label' => __('Color', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'type' => Controls::COLOR,
            'default' => '#ffffff',
            'selector' => [
                '{{WRAPPER}}  > .oxi-image-hover-load-more-button-wrap .oxi-image-load-more-button:hover' => 'color: {{VALUE}};',
                '{{WRAPPER}}  > .oxi-image-hover-load-more-button-wrap .oxi-image-load-more-button:hover .oxi-image-hover-loader button__loader' => 'color: {{VALUE}};',
                '{{WRAPPER}}  > .oxi-image-hover-load-more-button-wrap .oxi-image-load-more-button:hover span' => 'color: {{VALUE}};',
            ], 'description' => 'Color property is used to set the Hover color of the Button.',
                ]
        );
        $this->add_control(
                'display_post_load_button_hover_background', $this->style, [
            'label' => __('Background', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'type' => Controls::GRADIENT,
            'default' => '#ffffff',
            'selector' => [
                '{{WRAPPER}}  > .oxi-image-hover-load-more-button-wrap .oxi-image-load-more-button:hover' => 'background: {{VALUE}};',
            ],
            'description' => 'Background property is used to set the Hover Background of the Button.',
                ]
        );
        $this->add_group_control(
                'display_post_load_button_hover_border',
                $this->style,
                [
                    'type' => Controls::BORDER,
                    'selector' => [
                        '{{WRAPPER}}  > .oxi-image-hover-load-more-button-wrap .oxi-image-load-more-button:hover' => ''
                    ],
                    'description' => 'Border property is used to set the Hover Border of the Button.',
                ]
        );
        $this->add_group_control(
                'display_post_load_button_hover_tx_shadow', $this->style, [
            'type' => Controls::TEXTSHADOW,
            'selector' => [
                '{{WRAPPER}}  > .oxi-image-hover-load-more-button-wrap .oxi-image-load-more-button:hover span' => '',
            ],
            'description' => 'Text Shadow property adds shadow to Hover Button.',
                ]
        );

        $this->add_responsive_control(
                'display_post_load_button_hover_radius', $this->style, [
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
                '{{WRAPPER}}  > .oxi-image-hover-load-more-button-wrap .oxi-image-load-more-button:hover' => 'border-radius:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
            'description' => 'Allows you to add rounded corners at hover to Button with options.',
                ]
        );
        $this->add_group_control(
                'display_post_load_button_button_boxshadow', $this->style, [
            'type' => Controls::BOXSHADOW,
            'selector' => [
                '{{WRAPPER}}  > .oxi-image-hover-load-more-button-wrap .oxi-image-load-more-button:hover' => '',
            ],
            'description' => 'Allows you at hover to attaches one or more shadows into Button.',
                ]
        );

        $this->end_controls_tab();
        $this->end_controls_tabs();

        $this->add_responsive_control(
                'display_post_load_button_button_padding', $this->style, [
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
                'display_post_load_button_button_margin', $this->style, [
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

    /**
     * Template Modal opener
     * Define Multiple Data With Single Data
     *
     * @since 9.3.0
     */
    public function modal_opener() {

    }

}
