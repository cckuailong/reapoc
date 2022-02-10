<?php

namespace OXI_IMAGE_HOVER_PLUGINS\Modules\Flipbox\Admin;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Description of Effects1
 *
 * @author biplo
 */
use OXI_IMAGE_HOVER_PLUGINS\Modules\Flipbox\Modules as Modules;
use OXI_IMAGE_HOVER_PLUGINS\Classes\Controls as Controls;

class Effects12 extends Modules {

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

    public function register_back_icon_settings() {
        $this->start_controls_section(
                'oxi-image-hover', [
            'label' => esc_html__('Icon Settings', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'showing' => FALSE,
                ]
        );
        $this->add_control(
                'oxi-image-flip-back-icon-underline', $this->style, [
            'label' => __('Icon Underline', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'type' => Controls::CHOOSE,
            'operator' => Controls::OPERATOR_TEXT,
            'default' => '',
            'options' => [
                'oxi-image-hover-icon-underline' => [
                    'title' => __('Show', OXI_IMAGE_HOVER_TEXTDOMAIN),
                ],
                '' => [
                    'title' => __('Hide', OXI_IMAGE_HOVER_TEXTDOMAIN),
                ],
            ],
            'selector' => [
                '{{WRAPPER}} .oxi-image-hover-figure-backend .oxi-image-hover-icon' => '',
            ],
            'description' => 'Allows you to set Icon Underline.',
                ]
        );
        $this->add_control(
                'oxi-image-flip-back-icon-position', $this->style, [
            'label' => __('Position', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'type' => Controls::CHOOSE,
            'operator' => Controls::OPERATOR_ICON,
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
        $this->start_controls_section(
                'oxi-image-flip-back-icon-underline', [
            'label' => esc_html__('Icon Underline', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'showing' => false,
            'condition' => [
                'oxi-image-flip-back-icon-underline' => 'oxi-image-hover-icon-underline'
            ]
                ]
        );
        $this->add_control(
                'oxi-image-flip-back-icon-underline-position',
                $this->style,
                [
                    'label' => __('Position', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::CHOOSE,
                    'operator' => Controls::OPERATOR_ICON,
                    'toggle' => true,
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
                        '{{WRAPPER}} .oxi-image-hover-figure-backend .oxi-image-hover-icon.oxi-image-hover-icon-underline:before' => '{{VALUE}}',
                    ],
                    'description' => 'Allows you to set Icon Underline Position.',
                ]
        );

        $this->add_control(
                'oxi-image-flip-back-icon-underline-color', $this->style, [
            'label' => __('Color', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'type' => Controls::COLOR,
            'default' => '#ffffff',
            'selector' => [
                '{{WRAPPER}} .oxi-image-hover-figure-backend .oxi-image-hover-icon.oxi-image-hover-icon-underline:before' => 'border-bottom-color: {{VALUE}};',
            ],
            'description' => 'Allows you to set Icon Underline Color.',
                ]
        );
        $this->add_control(
                'oxi-image-flip-back-icon-underline-type', $this->style, [
            'label' => __('Underline Type', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'type' => Controls::SELECT,
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
                '{{WRAPPER}} .oxi-image-hover-figure-backend .oxi-image-hover-icon.oxi-image-hover-icon-underline:before' => 'border-bottom-style: {{VALUE}};',
            ],
            'description' => 'Allows you to set Icon Underline Type.',
                ]
        );
        $this->add_responsive_control(
                'oxi-image-flip-back-icon-underline-width', $this->style, [
            'label' => __('Width', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'type' => Controls::SLIDER,
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
                '{{WRAPPER}} .oxi-image-hover-figure-backend .oxi-image-hover-icon.oxi-image-hover-icon-underline:before' => 'width:{{SIZE}}{{UNIT}};',
            ],
            'description' => 'Allows you to set Icon Underline Width.',
                ]
        );
        $this->add_responsive_control(
                'oxi-image-flip-back-icon-underline-height', $this->style, [
            'label' => __('Size', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'type' => Controls::SLIDER,
            'default' => [
                'unit' => 'px',
                'size' => 2,
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
                '{{WRAPPER}} .oxi-image-hover-figure-backend .oxi-image-hover-icon.oxi-image-hover-icon-underline:before' => 'border-bottom-width: {{SIZE}}{{UNIT}};',
            ],
            'description' => 'Allows you to set Icon Underline Height.',
                ]
        );

        $this->add_responsive_control(
                'oxi-image-flip-back-icon-underline-distance', $this->style, [
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
                '{{WRAPPER}} .oxi-image-hover-figure-backend .oxi-image-hover-icon.oxi-image-hover-icon-underline' => 'margin-bottom:{{SIZE}}{{UNIT}};',
            ],
            'description' => 'Allows you to set Icon Underline Distance.',
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

        $this->add_control(
                'image_hover_front_heading', $this->style, [
            'label' => __('Front Title', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'type' => Controls::TEXT,
            'default' => 'Border Flipbox',
            'placeholder' => '',
            'description' => 'Add Your Flipbox Front Title.'
                ]
        );
        $this->add_control(
                'image_hover_back_heading', $this->style, [
            'label' => __('Backend Title', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'type' => Controls::TEXT,
            'default' => 'Border Flipbox',
            'placeholder' => 'Heading',
            'description' => 'Add Your Flipbox Backend Title.'
                ]
        );
        $this->add_control(
                'image_hover_front_icon', $this->style, [
            'label' => __('Front Icon', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'type' => Controls::ICON,
            'description' => 'Add Your Flipbox Front Icon.'
                ]
        );
        $this->add_control(
                'image_hover_back_icon', $this->style, [
            'label' => __('Backend Icon', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'type' => Controls::ICON,
            'description' => 'Add Your Flipbox Backend Icon.'
                ]
        );
        $this->add_control(
                'image_hover_back_description', $this->style, [
            'label' => __('Backend Description', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'type' => Controls::TEXTAREA,
            'description' => 'Add Your Backend Description Unless make it blank.'
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
                'image_hover_front_image', $this->style, [
            'label' => __('Image', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'type' => Controls::MEDIA,
            'description' => 'Add or Modify Your Front Image. Adjust Front background to get better design.'
                ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab();
        $this->add_group_control(
                'image_hover_back_image', $this->style, [
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
