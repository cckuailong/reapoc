<?php

namespace OXI_IMAGE_HOVER_PLUGINS\Modules\Filter;

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

    public $allcategory = [];

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
        $this->register_category_menu();
        $this->register_category_data_style();
        $this->end_section_devider();
        $this->start_section_devider();
        $this->register_category_style();
        $this->end_section_devider();
        $this->end_section_tabs();
    }

    public function register_category_menu() {
        $this->start_controls_section(
                'image-hover',
                [
                    'label' => esc_html__('Category Menu', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'showing' => TRUE,
                ]
        );

        $all_category_data = (array_key_exists('category_menu_settings', $this->style) && is_array($this->style['category_menu_settings'])) ? $this->style['category_menu_settings'] : [];

        foreach ($all_category_data as $value) :
            $this->allcategory[$value['category_item_text']] = $value['category_item_text'];
        endforeach;

        $this->add_control(
                'category_parent_cat',
                $this->style,
                [
                    'label' => __('Parent Category', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::SELECT,
                    'description' => __('Select Parent Category show after Save and Reload', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'loader' => TRUE,
                    'options' => $this->allcategory,
                ]
        );

        $this->add_repeater_control(
                'category_menu_settings',
                $this->style,
                [
                    'label' => __('', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::REPEATER,
                    'fields' => [
                        'category_item_text' => [
                            'label' => esc_html__('Category Name', OXI_IMAGE_HOVER_TEXTDOMAIN),
                            'type' => Controls::TEXT,
                            'placeholder' => 'Add or Edit Category Name',
                        ],
                    ],
                    'title_field' => 'category_item_text',
                    'button' => 'Add New Category',
                ]
        );
        $this->end_controls_section();
    }

    public function register_category_style() {
        $this->start_controls_section(
                'image-hover',
                [
                    'label' => esc_html__('Menu Style', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'showing' => TRUE,
                ]
        );
        $this->add_control(
                'category_menu_align',
                $this->style,
                [
                    'label' => __('Menu Align', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::CHOOSE,
                    'operator' => Controls::OPERATOR_ICON,
                    'toggle' => TRUE,
                    'default' => 'flex-start',
                    'options' => [
                        'flex-start' => [
                            'title' => __('Left', OXI_IMAGE_HOVER_TEXTDOMAIN),
                            'icon' => 'fa fa-align-left',
                        ],
                        'center' => [
                            'title' => __('Center', OXI_IMAGE_HOVER_TEXTDOMAIN),
                            'icon' => 'fa fa-align-center',
                        ],
                        'flex-end' => [
                            'title' => __('Right', OXI_IMAGE_HOVER_TEXTDOMAIN),
                            'icon' => 'fa fa-align-right',
                        ],
                    ],
                    'selector' => [
                        '{{WRAPPER}} .image-hover-filter-style .image-hover-category-menu' => 'justify-content: {{VALUE}};',
                    ],
                    'description' => __('Set menu align as left, right or center ', OXI_IMAGE_HOVER_TEXTDOMAIN),
                ]
        );
        $this->add_group_control(
                'category_menu_typo',
                $this->style,
                [
                    'type' => Controls::TYPOGRAPHY,
                    'include' => Controls::ALIGNNORMAL,
                    'selector' => [
                        '{{WRAPPER}} .image-hover-filter-style .image-hover-category-menu-item' => '',
                    ],
                ]
        );
        $this->add_control(
                'category_menu_width_type',
                $this->style,
                [
                    'label' => __('Width Mode', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::CHOOSE,
                    'toggle' => TRUE,
                    'loader' => TRUE,
                    'default' => 'dynamic',
                    'options' => [
                        'category_fix_width' => [
                            'title' => __('Static', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        ],
                        'cat_dynamic' => [
                            'title' => __('Dynamic', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        ]
                    ],
                    'selector' => [
                        '{{WRAPPER}} .image-hover-filter-style .image-hover-category-menu-item' => '',
                    ],
                    'description' => __('menu width condition as Static or Dynamic', OXI_IMAGE_HOVER_TEXTDOMAIN),
                ]
        );
        $this->add_responsive_control(
                'category_menu_width_size',
                $this->style,
                [
                    'label' => __('Width', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::SLIDER,
                    'default' => [
                        'unit' => 'px',
                        'size' => '120',
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
                            'step' => .1,
                        ],
                        'em' => [
                            'min' => 0,
                            'max' => 10,
                            'step' => .1,
                        ],
                    ],
                    'condition' => [
                        'category_menu_width_type' => 'category_fix_width'
                    ],
                    'selector' => [
                        '{{WRAPPER}} .image-hover-filter-style .image-hover-category-menu-item.category_fix_width' => 'width: {{SIZE}}{{UNIT}};',
                    ],
                    'description' => 'Adjust Filter Menu Width with multiple Options',
                ]
        );

        $this->start_controls_tabs(
                'image-hover-start-tabs',
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
                'category_menu_color',
                $this->style,
                [
                    'label' => __('Color', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::COLOR,
                    'default' => '#8d8d8d',
                    'selector' => [
                        '{{WRAPPER}} .image-hover-filter-style .image-hover-category-menu-item' => 'color: {{VALUE}};',
                    ],
                    'description' => 'Adjust Filter Menu Color',
                ]
        );
        $this->add_control(
                'category_menu_background',
                $this->style,
                [
                    'label' => __('Background Color', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::COLOR,
                    'default' => '',
                    'oparetor' => 'RGB',
                    'selector' => [
                        '{{WRAPPER}} .image-hover-filter-style .image-hover-category-menu-item' => 'background: {{VALUE}};',
                    ],
                    'description' => 'Adjust Filter Menu Background with Multiple Options',
                ]
        );
        $this->add_group_control(
                'ategory_menu_border',
                $this->style,
                [
                    'type' => Controls::BORDER,
                    'selector' => [
                        '{{WRAPPER}} .image-hover-filter-style .image-hover-category-menu-item' => '',
                    ],
                    'description' => 'Adjust Filter Menu Border with Multiple Options',
                ]
        );
        $this->add_group_control(
                'category_menu_shadow',
                $this->style,
                [
                    'type' => Controls::BOXSHADOW,
                    'selector' => [
                        '{{WRAPPER}} .image-hover-filter-style .image-hover-category-menu-item' => '',
                    ],
                    'description' => 'Adjust Filter Menu Boxshadow with Multiple Options',
                ]
        );
        $this->add_responsive_control(
                'category_menu_border-radius',
                $this->style,
                [
                    'label' => __('Border Radius', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::DIMENSIONS,
                    'default' => [
                        'unit' => 'px',
                        'size' => '',
                    ],
                    'range' => [
                        'px' => [
                            'min' => 0,
                            'max' => 200,
                            'step' => 1,
                        ],
                        '%' => [
                            'min' => 0,
                            'max' => 50,
                            'step' => .1,
                        ],
                        'em' => [
                            'min' => 0,
                            'max' => 10,
                            'step' => .1,
                        ],
                    ],
                    'selector' => [
                        '{{WRAPPER}} .image-hover-filter-style .image-hover-category-menu-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                    'description' => 'Adjust Filter Menu Border Radius with Multiple Options',
                ]
        );
        $this->end_controls_tab();

        $this->start_controls_tab();
        $this->add_control(
                'category_menu_hover_color',
                $this->style,
                [
                    'label' => __('Color', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::COLOR,
                    'default' => '#ffffff',
                    'selector' => [
                        '{{WRAPPER}} .image-hover-filter-style .image-hover-category-menu-item:hover' => 'color: {{VALUE}};',
                    ],
                    'description' => 'Adjust Filter Menu Color',
                ]
        );
        $this->add_control(
                'category_menu_hover_background',
                $this->style,
                [
                    'label' => __('Background Color', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::COLOR,
                    'default' => 'rgba(71, 201, 229, 1)',
                    'oparetor' => 'RGB',
                    'selector' => [
                        '{{WRAPPER}} .image-hover-filter-style .image-hover-category-menu-item:hover' => 'background: {{VALUE}};',
                    ],
                    'description' => 'Adjust Filter Menu Background with Multiple Options',
                ]
        );
        $this->add_group_control(
                'category_menu_hover_border',
                $this->style,
                [
                    'type' => Controls::BORDER,
                    'selector' => [
                        '{{WRAPPER}} .image-hover-filter-style .image-hover-category-menu-item:hover' => '',
                    ],
                    'description' => 'Adjust Filter Menu Border with Multiple Options',
                ]
        );
        $this->add_group_control(
                'category_menu_hover_shadow',
                $this->style,
                [
                    'type' => Controls::BOXSHADOW,
                    'selector' => [
                        '{{WRAPPER}} .image-hover-filter-style .image-hover-category-menu-item:hover' => '',
                    ],
                    'description' => 'Adjust Filter Menu Boxshadow with Multiple Options',
                ]
        );
        $this->add_responsive_control(
                'category_menu_hover_border_radius',
                $this->style,
                [
                    'label' => __('Border Radius', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::DIMENSIONS,
                    'default' => [
                        'unit' => 'px',
                        'size' => '',
                    ],
                    'range' => [
                        'px' => [
                            'min' => 0,
                            'max' => 200,
                            'step' => 1,
                        ],
                        '%' => [
                            'min' => 0,
                            'max' => 50,
                            'step' => .1,
                        ],
                        'em' => [
                            'min' => 0,
                            'max' => 10,
                            'step' => .1,
                        ],
                    ],
                    'selector' => [
                        '{{WRAPPER}} .image-hover-filter-style .image-hover-category-menu-item:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                    'description' => 'Adjust Filter Menu Border Radius with Multiple Options',
                ]
        );
        $this->end_controls_tab();

        $this->start_controls_tab();
        $this->add_control(
                'category_menu_active_color',
                $this->style,
                [
                    'label' => __('Color', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::COLOR,
                    'default' => '#ffffff',
                    'selector' => [
                        '{{WRAPPER}} .image-hover-filter-style .image-hover-category-menu-item.oxi_active' => 'color: {{VALUE}};',
                    ],
                    'description' => 'Adjust Filter Menu Color',
                ]
        );
        $this->add_control(
                'category_menu_active_background',
                $this->style,
                [
                    'label' => __('Background Color', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::COLOR,
                    'default' => 'rgba(71, 201, 229, 1)',
                    'oparetor' => 'RGB',
                    'selector' => [
                        '{{WRAPPER}} .image-hover-filter-style .image-hover-category-menu-item.oxi_active' => 'background: {{VALUE}};',
                    ],
                    'description' => 'Adjust Filter Menu Background with Multiple Options',
                ]
        );
        $this->add_group_control(
                'category_menu_active_border',
                $this->style,
                [
                    'type' => Controls::BORDER,
                    'selector' => [
                        '{{WRAPPER}} .image-hover-filter-style .image-hover-category-menu-item.oxi_active' => '',
                    ],
                    'description' => 'Adjust Filter Menu Border with Multiple Options',
                ]
        );
        $this->add_group_control(
                'category_menu_active_shadow',
                $this->style,
                [
                    'type' => Controls::BOXSHADOW,
                    'selector' => [
                        '{{WRAPPER}} .image-hover-filter-style .image-hover-category-menu-item.oxi_active' => '',
                    ],
                    'description' => 'Adjust Filter Menu Boxshadow with Multiple Options',
                ]
        );
        $this->add_responsive_control(
                'category_menu_active_border_radius',
                $this->style,
                [
                    'label' => __('Border Radius', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::DIMENSIONS,
                    'default' => [
                        'unit' => 'px',
                        'size' => '',
                    ],
                    'range' => [
                        'px' => [
                            'min' => 0,
                            'max' => 200,
                            'step' => 1,
                        ],
                        '%' => [
                            'min' => 0,
                            'max' => 50,
                            'step' => .1,
                        ],
                        'em' => [
                            'min' => 0,
                            'max' => 10,
                            'step' => .1,
                        ],
                    ],
                    'selector' => [
                        '{{WRAPPER}} .image-hover-filter-style .image-hover-category-menu-item.oxi_active' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                    'description' => 'Adjust Filter Menu Border Radius with Multiple Options',
                ]
        );
        $this->end_controls_tab();
        $this->end_controls_tabs();

        $this->add_responsive_control(
                'category_menu_padding',
                $this->style,
                [
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
                            'max' => 200,
                            'step' => 1,
                        ],
                        '%' => [
                            'min' => 0,
                            'max' => 50,
                            'step' => .1,
                        ],
                        'em' => [
                            'min' => 0,
                            'max' => 10,
                            'step' => .1,
                        ],
                    ],
                    'selector' => [
                        '{{WRAPPER}} .image-hover-filter-style .image-hover-category-menu-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                    'description' => 'Adjust Filter Menu Padding with Multiple Options',
                ]
        );
        $this->add_responsive_control(
                'category_menu_margin',
                $this->style,
                [
                    'label' => __('Margin', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::DIMENSIONS,
                    'default' => [
                        'unit' => 'px',
                        'size' => '',
                    ],
                    'range' => [
                        'px' => [
                            'min' => 0,
                            'max' => 200,
                            'step' => 1,
                        ],
                        '%' => [
                            'min' => 0,
                            'max' => 50,
                            'step' => .1,
                        ],
                        'em' => [
                            'min' => 0,
                            'max' => 10,
                            'step' => .1,
                        ],
                    ],
                    'selector' => [
                        '{{WRAPPER}} .image-hover-filter-style .image-hover-category-menu-item' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                    'description' => 'Adjust Filter Menu Margin with Multiple Options',
                ]
        );
        $this->end_controls_section();
    }

    public function register_category_data_style() {
        $this->start_controls_section(
                'image-hover',
                [
                    'label' => esc_html__('Item Data Settings', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'showing' => TRUE,
                ]
        );
        $this->add_group_control(
                'category_col',
                $this->style,
                [
                    'label' => __('Item Per Rows', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::COLUMN,
                    'loader' => TRUE,
                    'selector' => [
                        '{{WRAPPER}} .image-hover-category-item-show' => '',
                    ],
                ]
        );
        $this->add_responsive_control(
                'category_data_item',
                $this->style,
                [
                    'label' => __('Item Padding', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::DIMENSIONS,
                    'default' => [
                        'unit' => 'px',
                        'size' => '',
                    ],
                    'range' => [
                        'px' => [
                            'min' => 0,
                            'max' => 100,
                            'step' => 1,
                        ],
                        '%' => [
                            'min' => 0,
                            'max' => 10,
                            'step' => .1,
                        ],
                        'em' => [
                            'min' => 0,
                            'max' => 2,
                            'step' => .1,
                        ],
                    ],
                    'selector' => [
                        '{{WRAPPER}} .image-hover-filter-style .image-hover-category-item-show' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                    'description' => 'Adjust Filter Item Padding with Multiple Options',
                ]
        );
        $this->add_responsive_control(
                'category_data_body_padding',
                $this->style,
                [
                    'label' => __('Margin', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::DIMENSIONS,
                    'default' => [
                        'unit' => 'px',
                        'size' => '',
                    ],
                    'range' => [
                        'px' => [
                            'min' => 0,
                            'max' => 100,
                            'step' => 1,
                        ],
                        '%' => [
                            'min' => 0,
                            'max' => 10,
                            'step' => .1,
                        ],
                        'em' => [
                            'min' => 0,
                            'max' => 2,
                            'step' => .1,
                        ],
                    ],
                    'selector' => [
                        '{{WRAPPER}} .image-hover-filter-style .image-hover-category' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                    'description' => 'Adjust Filter Item Margin with Multiple Options',
                ]
        );
        $this->end_controls_section();
    }

    /*
     * @return void
     * Start Module Method for Modal Opener and Modal
     */

    public function modal_opener() {
        $this->add_substitute_control('', [], [
            'type' => Controls::MODALOPENER,
            'title' => __('Add New Data', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'sub-title' => __('Category Data Form', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'showing' => true,
        ]);
    }

    public function modal_form_data() {
        echo '<div class="modal-header">
                    <h4 class="modal-title">Image Hover Form</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">';
        $this->add_control(
                'image_hover_heading', $this->style, [
            'label' => __('Title', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'type' => Controls::TEXT,
            'default' => 'Title 01',
            'placeholder' => 'Title 01',
            'description' => 'Set Title For repeting Your Category Data.'
                ]
        );

        $this->add_control(
                'image_hover_info', $this->style, [
            'label' => __('Image Hover Shortcode', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'type' => Controls::TEXTAREA,
            'description' => 'Add Image Hover Shortcode. After saved kindly reload to loading CSS or JS properly '
                ]
        );
        $this->add_control(
                'image_hover_category_select', $this->style, [
            'label' => __('Category Select', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'type' => Controls::SELECT,
            'multiple' => TRUE,
            'options' => $this->allcategory,
            'description' => 'Select Category For your Shortcode. '
                ]
        );

        echo '</div>';
    }

}
