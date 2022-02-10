<?php

namespace OXI_IMAGE_HOVER_PLUGINS\Modules\Comparison\Admin;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Description of Effects1
 *
 * @author biplob
 */
use OXI_IMAGE_HOVER_PLUGINS\Classes\Controls as Controls;
use OXI_IMAGE_HOVER_PLUGINS\Modules\Comparison\Modules as Modules;

class Effects2 extends Modules {

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
     * Start Module Method for Magnifi Setting #Light-box
     */

    public function register_handle_settings() {
        $this->start_controls_section(
                'shortcode-addons', [
            'label' => esc_html__('Handle Setting', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'showing' => true,
                ]
        );
        $this->add_control(
                'oxi_image_comparison_handle_color', $this->style, [
            'label' => __('Handle Color', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'type' => Controls::COLOR,
            'default' => '#0a0a0a',
            'selector' => [
                '{{WRAPPER}} .oxi-addons-main-wrapper-image-comparison-style-2 .beer-handle::after, {{WRAPPER}} .oxi-addons-main-wrapper-image-comparison-style-2 .beer-handle::before' => 'color: {{VALUE}};',
            ],
            'description' => 'Set Handle & Arrow color.',
                ]
        );
        $this->add_control(
                'oxi_image_comparison_handle_bg_color', $this->style, [
            'label' => __('Background', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'type' => Controls::COLOR,
            'oparetor' => 'RGB',
            'default' => '#fff',
            'selector' => [
                '{{WRAPPER}} .oxi-addons-main-wrapper-image-comparison-style-2 .beer-handle' => 'background: {{VALUE}};',
                '{{WRAPPER}} .oxi-addons-main-wrapper-image-comparison-style-2 .beer-range:focus~.beer-handle' => 'background: {{VALUE}};',
            ],
            'description' => 'Set Background color of Handle.',
                ]
        );

        $this->add_responsive_control(
                'oxi_image_comparison_handle_width', $this->style, [
            'label' => __('Width', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'type' => Controls::SLIDER,
            'separator' => true,
            'default' => [
                'unit' => 'px',
                'size' => 50,
            ],
            'range' => [
                'px' => [
                    'min' => 1,
                    'max' => 150,
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
                '{{WRAPPER}} .oxi-addons-main-wrapper-image-comparison-style-2 .beer-handle' => 'width:{{SIZE}}{{UNIT}};',
            ],
            'description' => 'Set Handle Width with multiple options.',
                ]
        );
        $this->add_responsive_control(
                'oxi_image_comparison_handle_height', $this->style, [
            'label' => __('Height', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'type' => Controls::SLIDER,
            'separator' => true,
            'default' => [
                'unit' => 'px',
                'size' => 50,
            ],
            'range' => [
                'px' => [
                    'min' => 1,
                    'max' => 150,
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
                '{{WRAPPER}} .oxi-addons-main-wrapper-image-comparison-style-2 .beer-handle' => 'height:{{SIZE}}{{UNIT}};',
            ],
            'description' => 'Set Handle Height with multiple options.',
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
                'shortcode-addons', [
            'label' => esc_html__('Button Settings', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'showing' => false,
                ]
        );
        $this->add_control(
                'oxi_image_compersion_button_controler', $this->style, [
            'label' => __('Button', OXI_IMAGE_HOVER_TEXTDOMAIN),
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
                'oxi_image_comparison_before_text', $this->style, [
            'label' => __('Before Button Text', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'type' => Controls::TEXT,
            'default' => 'Before',
            'placeholder' => 'Before',
            'condition' => [
                'oxi_image_compersion_button_controler' => 'true',
            ],
            'description' => 'Set Your Comparison Before Text while Unicode also Supported.',
                ]
        );
        $this->add_control(
                'oxi_image_comparison_after_text', $this->style, [
            'label' => __('After Button Text', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'type' => Controls::TEXT,
            'default' => 'After',
            'placeholder' => 'After',
            'condition' => [
                'oxi_image_compersion_button_controler' => 'true',
            ],
            'description' => 'Set Your Comparison After Text while Unicode also Supported.',
                ]
        );
        $this->add_group_control(
                'oxi_image_comparison_typograpy', $this->style, [
            'type' => Controls::TYPOGRAPHY,
            'separator' => true,
            'condition' => [
                'oxi_image_compersion_button_controler' => 'true',
            ],
            'selector' => [
                '{{WRAPPER}} .oxi-addons-main-wrapper-image-comparison-style-2 .beer-reveal[data-beer-label]::after, {{WRAPPER}} .oxi-addons-main-wrapper-image-comparison-style-2 .beer-slider[data-beer-label]::after' => '',
            ],
                ]
        );
        $this->add_control(
                'oxi_image_comparison_text_color', $this->style, [
            'label' => __('Color', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'type' => Controls::COLOR,
            'default' => '#787878',
            'condition' => [
                'oxi_image_compersion_button_controler' => 'true',
            ],
            'selector' => [
                '{{WRAPPER}} .oxi-addons-main-wrapper-image-comparison-style-2 .beer-reveal[data-beer-label]::after, {{WRAPPER}} .oxi-addons-main-wrapper-image-comparison-style-2 .beer-slider[data-beer-label]::after' => 'color: {{VALUE}};',
            ], 'description' => 'Update Your Text Color as like as you want.',
                ]
        );
        $this->add_control(
                'oxi_image_comparison_button_bg_color', $this->style, [
            'label' => __('Background Color', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'type' => Controls::COLOR,
            'oparetor' => 'RGB',
            'default' => '#fff',
            'condition' => [
                'oxi_image_compersion_button_controler' => 'true',
            ],
            'selector' => [
                '{{WRAPPER}} .oxi-addons-main-wrapper-image-comparison-style-2 .beer-reveal[data-beer-label]::after, {{WRAPPER}} .oxi-addons-main-wrapper-image-comparison-style-2 .beer-slider[data-beer-label]::after' => 'background: {{VALUE}};',
            ], 'description' => 'Update Your Text Background Color as like as you want.',
                ]
        );
        $this->add_group_control(
                'oxi_image_comparison_button_text_shadow', $this->style, [
            'type' => Controls::TEXTSHADOW,
            'condition' => [
                'oxi_image_compersion_button_controler' => 'true',
            ],
            'selector' => [
                '{{WRAPPER}} .oxi-addons-main-wrapper-image-comparison-style-2 .beer-reveal[data-beer-label]::after, {{WRAPPER}} .oxi-addons-main-wrapper-image-comparison-style-2 .beer-slider[data-beer-label]::after' => '',
            ],
            'description' => 'Update Your Text Shadow as like as you want.',
                ]
        );

        $this->add_responsive_control(
                'oxi_image_comparison_button_border_radius', $this->style, [
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
                'oxi_image_compersion_button_controler' => 'true',
            ],
            'selector' => [
                '{{WRAPPER}} .oxi-addons-main-wrapper-image-comparison-style-2 .beer-reveal[data-beer-label]::after, {{WRAPPER}} .oxi-addons-main-wrapper-image-comparison-style-2 .beer-slider[data-beer-label]::after' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
            'description' => 'Update Your Text Border Radius With Multiple values.',
                ]
        );
        $this->add_responsive_control(
                'oxi_image_comparison_button_button_padding', $this->style, [
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
                'oxi_image_compersion_button_controler' => 'true',
            ],
            'selector' => [
                '{{WRAPPER}} .oxi-addons-main-wrapper-image-comparison-style-2 .beer-reveal[data-beer-label]::after, {{WRAPPER}} .oxi-addons-main-wrapper-image-comparison-style-2 .beer-slider[data-beer-label]::after' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
            'description' => 'Update Your Text Padding with Multiple values.',
                ]
        );

        $this->end_controls_section();
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
                        'size' => '50',
                    ],
                    'range' => [
                        'px' => [
                            'min' => 1,
                            'max' => 100,
                            'step' => 1,
                        ],
                    ],
                    'description' => 'Update Your Before Image Offset of Comparison Box.',
                ]
        );

        echo '</div>';
    }

}
