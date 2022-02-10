<?php

namespace OXI_IMAGE_HOVER_PLUGINS\Modules\Square\Admin;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Description of Effects21
 *
 * @author biplo
 */
use OXI_IMAGE_HOVER_PLUGINS\Modules\Square\Modules as Modules;
use OXI_IMAGE_HOVER_PLUGINS\Classes\Controls as Controls;

class Effects22 extends Modules {

    public function register_effects() {
        return $this->add_control(
                        'image_hover_effects',
                        $this->style,
                        [
                            'label' => __('Effects Direction', OXI_IMAGE_HOVER_TEXTDOMAIN),
                            'type' => Controls::SELECT,
                            'default' => 'bottom_to_left',
                            'options' => [
                                'position_to_top_left' => __('Top Left', OXI_IMAGE_HOVER_TEXTDOMAIN),
                                'position_to_top_right' => __('Top Right', OXI_IMAGE_HOVER_TEXTDOMAIN),
                                'position_to_bottom_left' => __('Bottom Left', OXI_IMAGE_HOVER_TEXTDOMAIN),
                                'position_to_bottom_right' => __('Bottom Right', OXI_IMAGE_HOVER_TEXTDOMAIN),
                            ],
                            'selector' => [
                                '{{WRAPPER}} .oxi-image-hover-figure' => '',
                            ],
                            'simpledescription' => 'Allows you to Set Effects Direction.',
                            'description' => 'Allows you to Set Effects Direction.',
                        ]
        );
    }

    public function register_general_style() {
        $this->start_controls_section(
                'oxi-image-hover',
                [
                    'label' => esc_html__('Width & Height', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'showing' => true,
                ]
        );
        $this->add_responsive_control(
                'oxi-image-hover-width',
                $this->style,
                [
                    'label' => __('Width', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::SLIDER,
                    'default' => [
                        'unit' => 'px',
                        'size' => '',
                    ],
                    'range' => [
                        'px' => [
                            'min' => 0,
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
                        '{{WRAPPER}} .oxi-image-hover-style-square' => 'max-width:{{SIZE}}{{UNIT}};',
                    ],
                    'simpledescription' => 'Customize Image Width as like as you want, will be pixel Value.',
                    'description' => 'Customize Image Width with several options as Pixel, Percent or EM.',
                ]
        );
        $this->add_responsive_control(
                'oxi-image-hover-height',
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
                            'max' => 1000,
                            'step' => 1,
                        ],
                        '%' => [
                            'min' => 1,
                            'max' => 200,
                            'step' => 1,
                        ],
                    ],
                    'selector' => [
                        '{{WRAPPER}} .oxi-image-hover-style-square:after ' => 'padding-bottom:{{SIZE}}{{UNIT}};',
                    ],
                    'simpledescription' => 'Customize Image Height as like as you want, will be Percent Value.',
                    'description' => 'Customize Image Height with several options as Pixel, Percent or EM.',
                ]
        );
        $this->add_responsive_control(
                'oxi-image-hover-margin',
                $this->style,
                [
                    'label' => __('Margin', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::DIMENSIONS,
                    'simpledimensions' => 'double',
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
                            'max' => 100,
                            'step' => 1,
                        ],
                        'em' => [
                            'min' => 0,
                            'max' => 200,
                            'step' => .1,
                        ],
                    ],
                    'selector' => [
                        '{{WRAPPER}} .oxi-image-hover-style' => 'padding:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                    'simpledescription' => 'Margin properties are used to create space around Image.',
                    'description' => 'Margin properties are used to create space around Image with several options as Pixel, or Percent or EM.',
                ]
        );
        $this->end_controls_section();
    }

    public function register_content_settings() {
        $this->start_controls_section(
                'oxi-image-hover',
                [
                    'label' => esc_html__('Content Settings', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'showing' => TRUE,
                ]
        );
        $this->add_responsive_control(
                'oxi-image-hover-content-width',
                $this->style,
                [
                    'label' => __('Width', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::SLIDER,
                    'default' => [
                        'unit' => 'px',
                        'size' => '',
                    ],
                    'range' => [
                        'px' => [
                            'min' => 0,
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
                        '{{WRAPPER}} .oxi-image-hover-figure-caption' => 'width:{{SIZE}}{{UNIT}};',
                    ],
                    'simpledescription' => 'Customize Image Width as like as you want, will be pixel Value.',
                    'description' => 'Customize Image Width with several options as Pixel, Percent or EM.',
                ]
        );

        $this->add_control(
                'oxi-image-hover-background-color', $this->style, [
            'label' => esc_html__('Background', OXI_IMAGE_HOVER_TEXTDOMAIN),
            'type' => Controls::GRADIENT,
            'default' => 'rgba(255, 116, 3, 1)',
            'selector' => [
                '{{WRAPPER}} .oxi-image-hover-caption-tab' => 'background:{{VALUE}};',
            ],
            'simpledescription' => 'Customize Hover Background with transparent options.',
            'description' => 'Customize Hover Background with Color or Gradient or Image properties.',
                ]
        );
        $this->add_responsive_control(
                'oxi-image-hover-border-radius',
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
                        '{{WRAPPER}} .oxi-image-hover .oxi-image-hover-caption-tab, {{WRAPPER}} .oxi-image-hover:hover .oxi-image-hover-caption-tab, {{WRAPPER}} .oxi-image-hover.oxi-touch .oxi-image-hover-caption-tab' => 'border-radius:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                    'simpledescription' => 'Allows you to add rounded corners at Hover to Image with options.',
                    'description' => 'Allows you to add rounded corners at Hover to Image with options.',
                ]
        );

        $this->add_responsive_control(
                'oxi-image-hover-content-margin',
                $this->style,
                [
                    'label' => __('Margin', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::DIMENSIONS,
                    'simpledimensions' => 'Double',
                    'default' => [
                        'unit' => 'px',
                        'size' => -10,
                    ],
                    'range' => [
                        'px' => [
                            'min' => -100,
                            'max' => 500,
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
                        '{{WRAPPER}} .oxi-image-hover-figure .oxi-image-hover-figure-caption' => 'margin:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                    'simpledescription' => 'Margin properties are used to create space around Body.',
                    'description' => 'Margin properties are used to create space around Content Body. Negetive Value will replace extra place',
                ]
        );
        $this->end_controls_section();
    }

    public function register_heading_settings() {
        $this->start_controls_section(
                'oxi-image-hover',
                [
                    'label' => esc_html__('Heading Settings', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'showing' => TRUE,
                ]
        );

        $this->add_group_control(
                'oxi-image-hover-heading-typho',
                $this->style,
                [
                    'type' => Controls::TYPOGRAPHY,
                    'include' => Controls::ALIGNNORMAL,
                    'selector' => [
                        '{{WRAPPER}} .oxi-image-hover-figure-caption .oxi-image-hover-heading' => '',
                    ]
                ]
        );
        $this->add_control(
                'oxi-image-hover-heading-color',
                $this->style,
                [
                    'label' => __('Color', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::COLOR,
                    'default' => '#ffffff',
                    'selector' => [
                        '{{WRAPPER}} .oxi-image-hover-figure-caption .oxi-image-hover-heading' => 'color: {{VALUE}};',
                    ],
                    'simpledescription' => 'Color property is used to set the color of the Heading.',
                    'description' => 'Color property is used to set the color of the Heading.',
                ]
        );
        $this->add_group_control(
                'oxi-image-hover-heading-tx-shadow',
                $this->style,
                [
                    'type' => Controls::TEXTSHADOW,
                    'selector' => [
                        '{{WRAPPER}} .oxi-image-hover-figure-caption .oxi-image-hover-heading' => '',
                    ],
                    'simpledescription' => 'Text Shadow property adds shadow to Heading.',
                    'description' => 'Text Shadow property adds shadow to Heading.',
                ]
        );
        $this->add_responsive_control(
                'oxi-image-hover-heading-margin',
                $this->style,
                [
                    'label' => __('Margin', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::DIMENSIONS,
                    'simpledimensions' => 'heading',
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
                        '{{WRAPPER}} .oxi-image-hover-figure-caption .oxi-image-hover-figure-heading' => 'padding:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                    'simpledescription' => 'Margin properties are used to create space around Heading.',
                    'description' => 'Margin properties are used to create space around Heading.',
                ]
        );
        $this->add_control(
                'oxi-image-hover-heading-animation',
                $this->style,
                [
                    'label' => __('Animation', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::SELECT,
                    'default' => '',
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
                        '{{WRAPPER}} .oxi-image-hover-figure-caption .oxi-image-hover-figure-heading' => '',
                    ],
                    'simpledescription' => 'Allows you to animated Heading while viewing.',
                    'description' => 'Allows you to animated Heading while viewing.',
                ]
        );
        $this->add_control(
                'oxi-image-hover-heading-animation-delay',
                $this->style,
                [
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
                        '{{WRAPPER}} .oxi-image-hover-figure-caption .oxi-image-hover-figure-heading' => '',
                    ],
                    'simpledescription' => 'Allows you to animation delay at Heading while viewing.',
                    'description' => 'Allows you to animation delay at Heading while viewing.',
                ]
        );

        $this->end_controls_section();
    }

    public function register_heading_underline() {

    }

    public function register_description_settings() {

    }

    public function register_button_settings() {

    }

    public function modal_form_data() {
        echo '<div class="modal-header">
                    <h4 class="modal-title">Image Hover Form</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">';
        $this->add_control(
                'image_hover_heading',
                $this->style,
                [
                    'label' => __('Title', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::TEXT,
                    'default' => '',
                    'placeholder' => 'Heading',
                    'description' => 'Add Your Image Hover Title.'
                ]
        );

        $this->add_group_control(
                'image_hover_image',
                $this->style,
                [
                    'label' => __('Image', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::MEDIA,
                    'description' => 'Add or Modify Your Image. You can use Media Library or Custom URL'
                ]
        );
        $this->add_group_control(
                'image_hover_button_link',
                $this->style,
                [
                    'label' => __('URL', OXI_IMAGE_HOVER_TEXTDOMAIN),
                    'type' => Controls::URL,
                    'separator' => TRUE,
                    'default' => '',
                    'placeholder' => 'https://www.yoururl.com',
                    'description' => 'Add Your Desire Link or Url Unless make it blank'
                ]
        );

        echo '</div>';
    }

}
