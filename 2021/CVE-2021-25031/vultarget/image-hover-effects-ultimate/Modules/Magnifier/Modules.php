<?php

namespace OXI_IMAGE_HOVER_PLUGINS\Modules\Magnifier;

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
            ]
                ]
        );
        $this->register_general_tabs();
        $this->register_custom_tabs();
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
        $this->register_magnifi_settings();

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
                '{{WRAPPER}} .oxi_addons__image_magnifier_column' => '',
            ],
                ]
        );

        $this->add_group_control(
                'oxi_image_magnifier_button_border',
                $this->style,
                [
                    'type' => Controls::BORDER,
                    'selector' => [
                        '{{WRAPPER}} .oxi_addons__image_magnifier' => '',
                    ],
                    'description' => 'Border property is used to set the Border of the Magnifier Body.',
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
                            'min' => -100,
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
                        '{{WRAPPER}} .oxi_addons__image_magnifier' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                        '{{WRAPPER}} .oxi_addons__image_magnifier .oxi_addons__image' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                        '{{WRAPPER}} .oxi_addons__image_magnifier .zoomableInPlace' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                        '{{WRAPPER}} .oxi_addons__image_magnifier .zoomable' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                    'description' => 'Allows you to add rounded corners to Magnifier with options.',
                ]
        );
        $this->add_group_control(
                'oxi_image_magnifier_shadow',
                $this->style,
                [
                    'label' => __('Box Shadow', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::BOXSHADOW,
                    'selector' => [
                        '{{WRAPPER}} .oxi_addons__image_magnifier' => '',
                    ],
                    'description' => 'Allows you at hover to attaches one or more shadows into Magnifier Body.',
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
                        '{{WRAPPER}} .oxi_addons__image_magnifier_column' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                    'description' => 'Generate space outside of Magnifier Body.',
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
                    'default' => '0 auto',
                    'operator' => Controls::OPERATOR_ICON,
                    'options' => [
                        '0 0 0 auto' => [
                            'title' => __('Left', OXI_IMAGE_HOVER_TEXTDOMAIN),
                            'icon' => 'fa fa-align-left',
                        ],
                        '0 auto' => [
                            'title' => __('Center', OXI_IMAGE_HOVER_TEXTDOMAIN),
                            'icon' => 'fa fa-align-center',
                        ],
                        '0 auto 0 0' => [
                            'title' => __('Right', OXI_IMAGE_HOVER_TEXTDOMAIN),
                            'icon' => 'fa fa-align-right',
                        ],
                    ],
                    'selector' => [
                        '{{WRAPPER}} .oxi_addons__image_magnifier_style_body ' => 'margin: {{VALUE}};',
                    ],
                    'description' => 'Set Image Positions IF you wanna set Custom Positions else default center value will works.',
                ]
        );

        $this->add_control(
                'oxi_image_magnifier_image_switcher',
                $this->style,
                [
                    'label' => __('Custom Width Height', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::SWITCHER,
                    'default' => 'no',
                    'loader' => true,
                    'label_on' => __('Yes', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'label_off' => __('No', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'return_value' => 'oxi__image_height_width',
                    'description' => 'Wanna Set Image Custom Height or Width.',
                ]
        );
        $this->add_responsive_control(
                'oxi_image_magnifier_image_width',
                $this->style,
                [
                    'label' => __('Width', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::SLIDER,
                    'condition' => [
                        'oxi_image_magnifier_image_switcher' => 'oxi__image_height_width',
                    ],
                    'default' => [
                        'unit' => 'px',
                        'size' => '',
                    ],
                    'range' => [
                        '%' => [
                            'min' => 50,
                            'max' => 100,
                            'step' => 1,
                        ],
                        'px' => [
                            'min' => 100,
                            'max' => 1500,
                            'step' => 10,
                        ],
                    ],
                    'selector' => [
                        '{{WRAPPER}} .oxi_addons__image_magnifier_style_body' => 'max-width: {{SIZE}}{{UNIT}};',
                    ],
                    'description' => 'Set Image Width as like as you want with multiple options.',
                ]
        );
        $this->add_responsive_control(
                'oxi_image_magnifier_height',
                $this->style,
                [
                    'label' => __('Height', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::SLIDER,
                    'default' => [
                        'unit' => 'px',
                        'size' => '',
                    ],
                    'range' => [
                        'px' => [
                            'min' => 0,
                            'max' => 1200,
                            'step' => 1,
                        ],
                        '%' => [
                            'min' => 1,
                            'max' => 100,
                            'step' => 1,
                        ],
                    ],
                    'selector' => [
                        '{{WRAPPER}} .oxi_addons__image_magnifier_style_body.oxi__image_height_width:after' => 'padding-bottom: {{SIZE}}{{UNIT}};',
                    ],
                    'condition' => [
                        'oxi_image_magnifier_image_switcher' => 'oxi__image_height_width',
                    ],
                    'description' => 'Set Image Height as like as you want with multiple options.',
                ]
        );
        $this->add_control(
                'oxi_image_magnifier_grayscale_switter',
                $this->style,
                [
                    'label' => __('Grayscale', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::SWITCHER,
                    'default' => 'no',
                    'loader' => true,
                    'label_on' => __('Yes', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'label_off' => __('No', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'return_value' => 'oxi_addons_grayscale',
                    'description' => 'Set Grayscale Property if you wanna Grayscale like black & white or Colorful.',
                ]
        );
        $this->add_responsive_control(
                'oxi_image_magnifier_opacity',
                $this->style,
                [
                    'label' => __('Opacity', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::SLIDER,
                    'default' => [
                        'unit' => 'px',
                        'size' => '',
                    ],
                    'range' => [
                        'px' => [
                            'min' => 0,
                            'max' => 1,
                            'step' => 0.1,
                        ],
                    ],
                    'description' => 'Set Opacity Property if you wanna set Opacity of your Image Or Not.',
                    'selector' => [
                        '{{WRAPPER}} .oxi_addons__image_magnifier .oxi_addons__image' => 'opacity: {{SIZE}};',
                    ],
                ]
        );

        $this->end_controls_section();
    }

    /*
     * @return void
     * Start Module Method for Magnifi Setting #Light-box
     */

    public function register_magnifi_settings() {
        $this->start_controls_section(
                'shortcode-addons', [
            'label' => esc_html__('Magnifi Settings', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'showing' => false,
                ]
        );
        $this->add_control(
                'oxi_image_magnifier_magnifi_zoom',
                $this->style,
                [
                    'label' => __('Zoom', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::SLIDER,
                    'default' => [
                        'unit' => 'px',
                        'size' => 2,
                    ],
                    'range' => [
                        'px' => [
                            'min' => 0,
                            'max' => 10,
                            'step' => 1,
                        ],
                    ],
                    'description' => 'How much Zoom you wnat to add while Hover Image.',
                ]
        );

        $this->add_control(
                'oxi_image_magnifier_magnifi_switcher',
                $this->style,
                [
                    'label' => __('Magnifi Width Height', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::SWITCHER,
                    'default' => 'no',
                    'loader' => true,
                    'label_on' => __('Yes', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'label_off' => __('No', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'return_value' => 'yes',
                    'description' => 'Wanna Custom Height or Width Hover Magnifier?',
                ]
        );

        $this->add_control(
                'oxi_image_magnifier_magnifi_width',
                $this->style,
                [
                    'label' => __('Width', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::SLIDER,
                    'condition' => [
                        'oxi_image_magnifier_magnifi_switcher' => 'yes',
                    ],
                    'default' => [
                        'unit' => 'px',
                        'size' => '',
                    ],
                    'range' => [
                        'px' => [
                            'min' => 100,
                            'max' => 1500,
                            'step' => 10,
                        ],
                    ],
                    'description' => 'Set Custom Width For Magnifier?',
                ]
        );
        $this->add_control(
                'oxi_image_magnifier_magnifi_height',
                $this->style,
                [
                    'label' => __('Height', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::SLIDER,
                    'default' => [
                        'unit' => 'px',
                        'size' => '',
                    ],
                    'range' => [
                        'px' => [
                            'min' => 0,
                            'max' => 1200,
                            'step' => 1,
                        ],
                    ],
                    'condition' => [
                        'oxi_image_magnifier_magnifi_switcher' => 'yes',
                    ],
                    'description' => 'Set Custom Height For Magnifier?',
                ]
        );

        $this->end_controls_section();
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

    /*
     * @return void
     * Start Module Method for Modal Opener and Modal  #Light-box
     */

    public function modal_opener() {
        $this->add_substitute_control('', [], [
            'type' => Controls::MODALOPENER,
            'title' => __('Add New Magnifier', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'sub-title' => __('Open Magnifier Form', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'showing' => true,
        ]);
    }

    public function modal_form_data() {
        echo '<div class="modal-header">
                    <h4 class="modal-title">Image Hover Form</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">';

        $this->add_group_control(
                'oxi_image_magnifier_img', $this->style, [
            'label' => esc_html__('Media Type', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'type' => Controls::MEDIA,
            'default' => [
                'type' => 'media-library',
                'link' => 'https://www.shortcode-addons.com/wp-content/uploads/2020/01/placeholder.png',
            ],
                ]
        );

        $this->add_control(
                'oxi_image_magnifier_magnifi_position', $this->style, [
            'label' => __('Magnifi Position', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'type' => Controls::SELECT,
            'default' => 'right',
            'loader' => true,
            'options' => [
                'none' => __('Default', OXI_IMAGE_HOVER_TEXTDOMAIN),
                'top' => __('Top', OXI_IMAGE_HOVER_TEXTDOMAIN),
                'right' => __('Right', OXI_IMAGE_HOVER_TEXTDOMAIN),
                'bottom' => __('Bottom', OXI_IMAGE_HOVER_TEXTDOMAIN),
                'left' => __('Left', OXI_IMAGE_HOVER_TEXTDOMAIN),
            ],
                ]
        );
        $this->add_control(
                'oxi_image_magnifier_magnifi_position_top', $this->style, [
            'label' => __('Top Position', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'description' => 'After save You will show the changes',
            'type' => Controls::SLIDER,
            'condition' => [
                'oxi_image_magnifier_magnifi_position' => 'top',
            ],
            'default' => [
                'unit' => 'px',
                'size' => '',
            ],
            'range' => [
                'px' => [
                    'min' => -500,
                    'max' => 500,
                    'step' => 2,
                ],
            ],
                ]
        );
        $this->add_control(
                'oxi_image_magnifier_magnifi_position_right', $this->style, [
            'label' => __('Right Position', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'type' => Controls::SLIDER,
            'description' => 'After save You will show the changes',
            'condition' => [
                'oxi_image_magnifier_magnifi_position' => 'right',
            ],
            'default' => [
                'unit' => 'px',
                'size' => '',
            ],
            'range' => [
                'px' => [
                    'min' => -500,
                    'max' => 500,
                    'step' => 2,
                ],
            ],
                ]
        );
        $this->add_control(
                'oxi_image_magnifier_magnifi_position_bottom', $this->style, [
            'label' => __('Bottom Position', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'type' => Controls::SLIDER,
            'description' => '   save You will show the changes',
            'condition' => [
                'oxi_image_magnifier_magnifi_position' => 'bottom',
            ],
            'default' => [
                'unit' => 'px',
                'size' => '',
            ],
            'range' => [
                'px' => [
                    'min' => -500,
                    'max' => 500,
                    'step' => 2,
                ],
            ],
                ]
        );
        $this->add_control(
                'oxi_image_magnifier_magnifi_position_left', $this->style, [
            'label' => __('Left Position', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'type' => Controls::SLIDER,
            'condition' => [
                'oxi_image_magnifier_magnifi_position' => 'left',
            ],
            'description' => 'After save You will show the changes',
            'default' => [
                'unit' => 'px',
                'size' => '',
            ],
            'range' => [
                'px' => [
                    'min' => -500,
                    'max' => 500,
                    'step' => 2,
                ],
            ],
                ]
        );
        echo '</div>';
    }

}
