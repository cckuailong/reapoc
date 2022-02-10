<?php

namespace OXI_IMAGE_HOVER_PLUGINS\Modules\Comparison;

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
                'oxi-image-hover-start-tabs', [
            'options' => [
                'general-settings' => esc_html__('General Settings', OXI_IMAGE_HOVER_TEXTDOMAIN),
                'custom' => esc_html__('Custom CSS', OXI_IMAGE_HOVER_TEXTDOMAIN),
            ],
                ]
        );
        $this->register_general_tabs();
        $this->register_custom_tabs();
    }

    public function register_custom_tabs() {
        $this->start_section_tabs(
                'oxi-image-hover-start-tabs', [
            'condition' => [
                'oxi-image-hover-start-tabs' => 'custom',
            ],
            'padding' => '10px',
                ]
        );

        $this->start_controls_section(
                'oxi-image-hover', [
            'label' => esc_html__('Custom CSS', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'showing' => true,
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
        $this->register_general_style();
        $this->end_section_devider();
        $this->start_section_devider();
        $this->register_image_settings();
        $this->register_handle_settings();
        $this->register_button_settings();
        $this->end_section_devider();
        $this->end_section_tabs();
    }

    /*
     * @return void
     * Start Module Method for Genaral Style  #Light-box
     */

    public function register_general_style() {
        $this->start_controls_section(
                'oxi-image-hover', [
            'label' => esc_html__('General Style', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'showing' => true,
                ]
        );
        $this->add_group_control(
                'oxi-image-hover-col', $this->style, [
            'type' => Controls::COLUMN,
            'selector' => [
                '{{WRAPPER}} .oxi-addons-main-wrapper-image-comparison' => '',
            ],
                ]
        );

        $this->add_group_control(
                'oxi_image_magnifier_button_border',
                $this->style,
                [
                    'type' => Controls::BORDER,
                    'selector' => [
                        '{{WRAPPER}} .oxi-addons-main-wrapper-image-comparison .oxi-addons-main' => '',
                    ],
                    'description' => 'Border property is used to set the Border of the Comparison Body.',
                ]
        );
        $this->add_responsive_control(
                'oxi_image_magnifier_radius',
                $this->style,
                [
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
                        '{{WRAPPER}} .oxi-addons-main-wrapper-image-comparison .oxi-addons-main' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                    'description' => 'Allows you to add rounded corners to Comparison with options.',
                ]
        );
        $this->add_group_control(
                'oxi_image_magnifier_shadow',
                $this->style,
                [
                    'label' => __('Box Shadow', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::BOXSHADOW,
                    'selector' => [
                        '{{WRAPPER}} .oxi-addons-main-wrapper-image-comparison .oxi-addons-main' => '',
                    ],
                    'description' => 'Allows you at hover to attaches one or more shadows into Comparison Body.',
                ]
        );
        $this->add_responsive_control(
                'oxi_image_magnifier_margin',
                $this->style,
                [
                    'label' => __('Margin', OXI_IMAGE_HOVER_TEXTDOMAIN),
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
                        '{{WRAPPER}} .oxi-addons-main-wrapper-image-comparison' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                    'description' => 'Generate space outside of Comparison Body.',
                ]
        );
        $this->end_controls_section();
    }

    /*
     * @return void
     * Start Module Method for Image Setting #Light-box
     */

    public function register_image_settings() {
        $this->start_controls_section(
                'shortcode-addons', [
            'label' => esc_html__('Image Settings', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'showing' => true,
                ]
        );
        $this->add_responsive_control(
                'oxi_image_magnifier_image_position',
                $this->style,
                [
                    'label' => __('Image Postion', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::CHOOSE,
                    'default' => 'center',
                    'operator' => Controls::OPERATOR_ICON,
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
                        '{{WRAPPER}}  .oxi-addons-main-wrapper-image-comparison' => 'justify-content: {{VALUE}};',
                    ],
                    'description' => 'Set Image Positions if you wanna set Custom Positions else default center value will works.',
                ]
        );

        $this->add_control(
                'oxi_image_magnifier_image_switcher',
                $this->style,
                [
                    'label' => __('Custom Width', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::SWITCHER,
                    'default' => 'no',
                    'loader' => true,
                    'label_on' => __('Yes', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'label_off' => __('No', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'return_value' => 'oxi__image_width',
                    'description' => 'Wanna Set Image Custom  Width.',
                ]
        );
        $this->add_responsive_control(
                'oxi_image_magnifier_image_width',
                $this->style,
                [
                    'label' => __('Width', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::SLIDER,
                    'condition' => [
                        'oxi_image_magnifier_image_switcher' => 'oxi__image_width',
                    ],
                    'default' => [
                        'unit' => 'px',
                        'size' => '',
                    ],
                    'range' => [
                        '%' => [
                            'min' => 10,
                            'max' => 100,
                            'step' => 1,
                        ],
                        'px' => [
                            'min' => 0,
                            'max' => 1500,
                            'step' => 10,
                        ],
                    ],
                    'selector' => [
                        '{{WRAPPER}} .oxi-addons-main-wrapper-image-comparison .oxi-addons-main.oxi__image_width' => 'max-width: {{SIZE}}{{UNIT}};',
                    ],
                    'description' => 'Set Image Width as like as you want with multiple options.',
                ]
        );

        $this->end_controls_section();
    }

    /*
     * @return void
     * Start Module Method for Magnifi Setting #Light-box
     */

    public function register_handle_settings() {
        $this->start_controls_section(
                'shortcode-addons',
                [
                    'label' => esc_html__('Handle Setting', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'showing' => true,
                ]
        );
        $this->add_control(
                'oxi_image_comparison_handle_color',
                $this->style,
                [
                    'label' => __('Handle Color', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::COLOR,
                    'default' => '#787878',
                    'selector' => [
                        '{{WRAPPER}} .oxi-addons-main-wrapper-image-comparison .twentytwenty-handle' => 'border-color: {{VALUE}};',
                        '{{WRAPPER}} .oxi-addons-main-wrapper-image-comparison .twentytwenty-up-arrow' => 'border-bottom-color: {{VALUE}};',
                        '{{WRAPPER}} .oxi-addons-main-wrapper-image-comparison .twentytwenty-down-arrow' => 'border-top-color: {{VALUE}};',
                        '{{WRAPPER}} .oxi-addons-main-wrapper-image-comparison .twentytwenty-left-arrow' => 'border-right-color: {{VALUE}};',
                        '{{WRAPPER}} .oxi-addons-main-wrapper-image-comparison .twentytwenty-right-arrow' => 'border-left-color: {{VALUE}};',
                        '{{WRAPPER}} .oxi-addons-main-wrapper-image-comparison .twentytwenty-horizontal .twentytwenty-handle::before, {{WRAPPER}} .oxi-addons-main-wrapper-image-comparison .twentytwenty-horizontal .twentytwenty-handle::after, {{WRAPPER}} .oxi-addons-main-wrapper-image-comparison .twentytwenty-vertical .twentytwenty-handle::before, {{WRAPPER}} .oxi-addons-main-wrapper-image-comparison .twentytwenty-vertical .twentytwenty-handle::after' => 'background: {{VALUE}};',
                    ],
                    'description' => 'Set Handle & Arrow color.',
                ]
        );
        $this->end_controls_section();
    }

    /*
     * @return void
     * Start Module Method for Magnifi Setting #Light-box
     */

    public function register_button_settings() {
        $this->start_controls_section(
                'shortcode-addons',
                [
                    'label' => esc_html__('Overlay Settings', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'showing' => false,
                ]
        );
        $this->add_control(
                'oxi_image_compersion_overlay_controler',
                $this->style,
                [
                    'label' => __('Overlay', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::CHOOSE,
                    'default' => 'true',
                    'loader' => true,
                    'options' => [
                        'true' => [
                            'title' => __('True', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        ],
                        'false' => [
                            'title' => __('False', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        ],
                    ],
                    'description' => 'Wanna Set Overlay?',
                ]
        );
        $this->add_control(
                'oxi_image_comparison_before_text',
                $this->style,
                [
                    'label' => __('Before Button Text', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::TEXT,
                    'default' => 'Before',
                    'placeholder' => 'Before',
                    'condition' => [
                        'oxi_image_compersion_overlay_controler' => 'true',
                    ],
                    'description' => 'Set Your Comparison Before Text while Unicode also Supported.',
                ]
        );
        $this->add_control(
                'oxi_image_comparison_after_text',
                $this->style,
                [
                    'label' => __('After Button Text', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::TEXT,
                    'default' => 'after',
                    'placeholder' => 'after',
                    'condition' => [
                        'oxi_image_compersion_overlay_controler' => 'true',
                    ],
                    'description' => 'Set Your Comparison After Text while Unicode also Supported.',
                ]
        );
        $this->add_group_control(
                'oxi_image_comparison_typograpy',
                $this->style,
                [
                    'type' => Controls::TYPOGRAPHY,
                    'separator' => true,
                    'condition' => [
                        'oxi_image_compersion_overlay_controler' => 'true',
                    ],
                    'selector' => [
                        '{{WRAPPER}} .oxi-addons-main-wrapper-image-comparison .twentytwenty-before-label::before, {{WRAPPER}} .oxi-addons-main-wrapper-image-comparison .twentytwenty-after-label::before, {{WRAPPER}} .oxi-addons-main-wrapper-image-comparison .twentytwenty-before-label::before,  {{WRAPPER}} .oxi-addons-main-wrapper-image-comparison .twentytwenty-after-label::before' => '',
                    ],
                ]
        );
        $this->add_control(
                'oxi_image_comparison_text_color',
                $this->style,
                [
                    'label' => __('Color', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::COLOR,
                    'default' => '#787878',
                    'condition' => [
                        'oxi_image_compersion_overlay_controler' => 'true',
                    ],
                    'selector' => [
                        '{{WRAPPER}} .oxi-addons-main-wrapper-image-comparison .twentytwenty-before-label::before, {{WRAPPER}} .oxi-addons-main-wrapper-image-comparison .twentytwenty-after-label::before, {{WRAPPER}} .oxi-addons-main-wrapper-image-comparison .twentytwenty-before-label::before,  {{WRAPPER}} .oxi-addons-main-wrapper-image-comparison .twentytwenty-after-label::before' => 'color: {{VALUE}};',
                    ],
                    'description' => 'Update Your Text Color as like as you want.',
                ]
        );
        $this->add_control(
                'oxi_image_comparison_overlay_bg_color',
                $this->style,
                [
                    'label' => __('Background Color', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::COLOR,
                    'oparetor' => 'RGB',
                    'default' => '#fff',
                    'condition' => [
                        'oxi_image_compersion_overlay_controler' => 'true',
                    ],
                    'selector' => [
                        '{{WRAPPER}} .oxi-addons-main-wrapper-image-comparison .twentytwenty-before-label::before, {{WRAPPER}} .oxi-addons-main-wrapper-image-comparison .twentytwenty-after-label::before, {{WRAPPER}} .oxi-addons-main-wrapper-image-comparison .twentytwenty-before-label::before,  {{WRAPPER}} .oxi-addons-main-wrapper-image-comparison .twentytwenty-after-label::before' => 'background: {{VALUE}};',
                    ],
                    'description' => 'Update Your Text Background Color as like as you want.',
                ]
        );
        $this->add_group_control(
                'oxi_image_comparison_overlay_text_shadow',
                $this->style,
                [
                    'type' => Controls::TEXTSHADOW,
                    'condition' => [
                        'oxi_image_compersion_overlay_controler' => 'true',
                    ],
                    'selector' => [
                        '{{WRAPPER}} .oxi-addons-main-wrapper-image-comparison .twentytwenty-before-label::before, {{WRAPPER}} .oxi-addons-main-wrapper-image-comparison .twentytwenty-after-label::before, {{WRAPPER}} .oxi-addons-main-wrapper-image-comparison .twentytwenty-before-label::before,  {{WRAPPER}} .oxi-addons-main-wrapper-image-comparison .twentytwenty-after-label::before' => '',
                    ],
                    'description' => 'Update Your Text Shadow as like as you want.',
                ]
        );

        $this->add_responsive_control(
                'oxi_image_comparison_overlay_button_border_radius',
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
                    'condition' => [
                        'oxi_image_compersion_overlay_controler' => 'true',
                    ],
                    'selector' => [
                        '{{WRAPPER}} .oxi-addons-main-wrapper-image-comparison .twentytwenty-before-label::before, {{WRAPPER}} .oxi-addons-main-wrapper-image-comparison .twentytwenty-after-label::before, {{WRAPPER}} .oxi-addons-main-wrapper-image-comparison .twentytwenty-before-label::before,  {{WRAPPER}} .oxi-addons-main-wrapper-image-comparison .twentytwenty-after-label::before' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                    'description' => 'Update Your Text Border Radius With Multiple values.',
                ]
        );
        $this->add_responsive_control(
                'oxi_image_comparison_overlay_button_padding',
                $this->style,
                [
                    'label' => __('Padding', OXI_IMAGE_HOVER_TEXTDOMAIN),
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
                    'condition' => [
                        'oxi_image_compersion_overlay_controler' => 'true',
                    ],
                    'selector' => [
                        '{{WRAPPER}} .oxi-addons-main-wrapper-image-comparison .twentytwenty-before-label::before, {{WRAPPER}} .oxi-addons-main-wrapper-image-comparison .twentytwenty-after-label::before, {{WRAPPER}} .oxi-addons-main-wrapper-image-comparison .twentytwenty-before-label::before,  {{WRAPPER}} .oxi-addons-main-wrapper-image-comparison .twentytwenty-after-label::before' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                    'description' => 'Update Your Text Padding with Multiple values.',
                ]
        );

        $this->end_controls_section();
    }

    /*
     * @return void
     * Start Module Method for Modal Opener and Modal  #Light-box
     */

    public function modal_opener() {
        $this->add_substitute_control('', [], [
            'type' => Controls::MODALOPENER,
            'title' => __('Add New Comparison', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'sub-title' => __('Open Comparison Form', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'showing' => true,
        ]);
    }

    public function modal_form_data() {
        echo '<div class="modal-header">
                    <h4 class="modal-title">Image Hover Form</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">';

        $this->start_controls_tabs(
                'shortcode-addons-start-tabs', [
            'options' => [
                'before' => esc_html__('Before Image', OXI_IMAGE_HOVER_TEXTDOMAIN),
                'after' => esc_html__('After Image', OXI_IMAGE_HOVER_TEXTDOMAIN),
            ],
                ]
        );
        $this->start_controls_tab();
        $this->add_group_control(
                'oxi_image_comparison_image_one',
                $this->style,
                [
                    'label' => __('URL', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::MEDIA,
                    'default' => [
                        'type' => 'media-library',
                        'link' => '',
                    ],
                    'description' => 'Update Your Before Image of Comparison Box.',
                ]
        );
        $this->end_controls_tab();
        $this->start_controls_tab();
        $this->add_group_control(
                'oxi_image_comparison_image_two',
                $this->style,
                [
                    'label' => __('URL', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::MEDIA,
                    'default' => [
                        'type' => 'media-library',
                        'link' => '',
                    ],
                    'description' => 'Update Your After Image of Comparison Box.',
                ]
        );
        $this->end_controls_tab();
        $this->end_controls_tabs();

        $this->add_control(
                'oxi_image_comparison_body_offset',
                $this->style,
                [
                    'label' => __('Coparison Offset', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::SLIDER,
                    'default' => [
                        'unit' => 'px',
                        'size' => '0.5',
                    ],
                    'range' => [
                        'px' => [
                            'min' => 0,
                            'max' => 1,
                            'step' => 0.1,
                        ],
                    ],
                    'description' => 'Update Your Before Image Offset of Comparison Box.',
                ]
        );
        $this->add_control(
                'oxi_image_comparison_click',
                $this->style,
                [
                    'label' => __('Click To Move', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::CHOOSE,
                    'default' => 'false',
                    'loader' => true,
                    'options' => [
                        'true' => [
                            'title' => __('True', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        ],
                        'false' => [
                            'title' => __('False', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        ],
                    ],
                    'selector' => [
                        '{{WRAPPER}} .oxi-addons-align-btn1' => 'text-align:{{VALUE}};',
                    ],
                    'description' => 'Update Your Comparison Clicking Option.',
                ]
        );
        $this->add_control(
                'oxi_image_comparison_position',
                $this->style,
                [
                    'label' => __('Position', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::CHOOSE,
                    'default' => 'true',
                    'loader' => true,
                    'descrption' => 'After Save then Refresh page!',
                    'options' => [
                        'true' => [
                            'title' => __('Horizontal', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        ],
                        'false ' => [
                            'title' => __('Vertical ', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        ],
                    ],
                    'description' => 'Update Your Comparison Box Positios as Horizontal or Vertical.',
                ]
        );
        $this->add_control(
                'oxi_image_comparison_hover',
                $this->style,
                [
                    'label' => __('Hover To Move', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::CHOOSE,
                    'default' => 'false',
                    'loader' => true,
                    'options' => [
                        'true' => [
                            'title' => __('True', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        ],
                        'false' => [
                            'title' => __('False', OXI_IMAGE_HOVER_TEXTDOMAIN),
                        ],
                    ],
                    'selector' => [
                        '{{WRAPPER}} .oxi-addons-align-btn1' => 'text-align:{{VALUE}};',
                    ],
                    'description' => 'Update Your Comparison Box Hover to Move Options.',
                ]
        );
        echo '</div>';
    }

}
