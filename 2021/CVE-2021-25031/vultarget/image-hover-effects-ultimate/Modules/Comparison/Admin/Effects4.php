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

class Effects4 extends Modules {

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
        $this->end_section_devider();
        $this->end_section_tabs();
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
                        'px' => [
                            'min' => 0,
                            'max' => 1500,
                            'step' => 10,
                        ],
                        '%' => [
                            'min' => 10,
                            'max' => 100,
                            'step' => 1,
                        ],
                    ],
                    'selector' => [
                        '{{WRAPPER}} .oxi-addons-main-wrapper-image-comparison .oxi-addons-main.oxi__image_width' => 'width: {{SIZE}}{{UNIT}} !important;',
                    ],
                    'description' => 'Set Image Width as like as you want with multiple options.',
                ]
        );
        $this->add_responsive_control(
                'oxi_image_magnifier_image_height',
                $this->style,
                [
                    'label' => __('Height', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::SLIDER,
                    'condition' => [
                        'oxi_image_magnifier_image_switcher' => 'oxi__image_width',
                    ],
                    'default' => [
                        'unit' => '%',
                        'size' => '',
                    ],
                    'range' => [
                        'px' => [
                            'min' => 10,
                            'max' => 1500,
                            'step' => 10,
                        ],
                        '%' => [
                            'min' => 10,
                            'max' => 150,
                            'step' => 1,
                        ],
                    ],
                    'selector' => [
                        '{{WRAPPER}} .oxi-addons-main-wrapper-image-comparison .oxi-addons-main.oxi__image_width::after' => 'padding-bottom: {{SIZE}}{{UNIT}} !important;',
                    ],
                    'description' => 'Set Image Height as like as you want with multiple options.',
                ]
        );
        $this->add_control(
                'oxi_image_comparison_hover_width', $this->style, [
            'label' => __('Hover Devider', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'type' => Controls::SLIDER,
            'separator' => true,
            'default' => [
                'unit' => '%',
                'size' => 5,
            ],
            'range' => [
                '%' => [
                    'min' => 2,
                    'max' => 100,
                    'step' => 1,
                ],
            ],
            'description' => 'Set Image Hover Width as How many Devider to show into sinlge image .',
                ]
        );
        $this->add_control(
                'oxi_image_comparison_hover_transition', $this->style, [
            'label' => __('Hover Transition', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'type' => Controls::SLIDER,
            'default' => [
                'unit' => 'px',
                'size' => 1.5,
            ],
            'range' => [
                'px' => [
                    'min' => 0.0,
                    'max' => 5,
                    'step' => 0.01,
                ],
            ],
            'selector' => [
                '{{WRAPPER}} .oxi_addons_image_style_4_box .oxi_addons_font_view_img' => 'transition:all {{SIZE}}s ease-in-out;',
            ],
            'description' => 'Set Image Hover Transition as like as you want with Second options.',
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
                        'link' => 'https://www.oxilabdemos.com/image-hover/wp-content/uploads/2020/01/placeholder.png',
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
                        'link' => 'https://www.oxilabdemos.com/image-hover/wp-content/uploads/2020/01/placeholder.png',
                    ],
                    'description' => 'Update Your After Image of Comparison Box.',
                ]
        );
        $this->end_controls_tab();
        $this->end_controls_tabs();

        echo '</div>';
    }

}
