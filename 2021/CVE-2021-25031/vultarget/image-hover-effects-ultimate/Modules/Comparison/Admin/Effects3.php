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

class Effects3 extends Modules {

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
                    ], 'description' => 'Allows you to add rounded corners to Comparison with options.',
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
                    ], 'description' => 'Allows you at hover to attaches one or more shadows into Comparison Body.',
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
                        '{{WRAPPER}} .oxi_addons__image_comparison_wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                    'description' => 'Allows you at hover to attaches one or more shadows into Comparison Body.',
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
                        '{{WRAPPER}} .oxi-addons-main-wrapper-image-comparison .oxi-addons-main.oxi__image_width .mbac-wrap' => 'width: {{SIZE}}{{UNIT}} !important;',
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
                        'unit' => 'px',
                        'size' => '',
                    ],
                    'range' => [
                        '%' => [
                            'min' => 10,
                            'max' => 200,
                            'step' => 1,
                        ],
                        'px' => [
                            'min' => 0,
                            'max' => 1200,
                            'step' => 10,
                        ],
                    ],
                    'selector' => [
                        '{{WRAPPER}} .oxi-addons-main-wrapper-image-comparison .oxi-addons-main.oxi__image_width .mbac-wrap' => 'height: {{SIZE}}{{UNIT}} !important;',
                        '{{WRAPPER}} .oxi-addons-main-wrapper-image-comparison .oxi-addons-main.oxi__image_width .mbac-wrap .oxi-img' => 'height: {{SIZE}}{{UNIT}} !important;',
                    ],
                    'description' => 'Set Image Height as like as you want with multiple options.',
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

        $this->add_group_control(
                'oxi_image_accordion_image',
                $this->style,
                [
                    'label' => __('URL', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::MEDIA,
                    'default' => [
                        'type' => 'media-library',
                        'link' => '',
                    ],
                    'description' => 'Add or Update Your Comparison Image.',
                ]
        );

        echo '</div>';
    }

}
