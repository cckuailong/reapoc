<?php

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Scheme_Color;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Scheme_Typography;

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

class Futurio_Advanced_Text_Block extends Widget_Base {

    public function get_name() {
        return 'advanced-text-block';
    }

    public function get_title() {
        return __('Advanced Text Block', 'futurio_extra');
    }

    public function get_icon() {
        return 'eicon-text-area';
    }

    public function get_categories() {
        return array('basic');
    }

    public function get_script_depends() {
        return [
            'futurio-animate-scripts'
        ];
    }

    protected function _register_controls() {

        $this->start_controls_section(
                'content_section',
                [
                    'label' => __('Advanced Text Block', 'futurio_extra'),
                    'tab' => Controls_Manager::TAB_CONTENT,
                ]
        );
        $this->add_control(
                'content_description',
                [
                    'type' => Controls_Manager::WYSIWYG,
                    'default' => __('I am text block. Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'futurio-extra'),
                    'placeholder' => __('Type your description here', 'futurio_extra'),
                ]
        );
        $this->add_control(
                'header_size',
                [
                    'label' => __('HTML Tag', 'futurio-extra'),
                    'type' => Controls_Manager::SELECT,
                    'options' => [
                        'h1' => 'H1',
                        'h2' => 'H2',
                        'h3' => 'H3',
                        'h4' => 'H4',
                        'h5' => 'H5',
                        'h6' => 'H6',
                        'div' => 'div',
                        'span' => 'span',
                        'p' => 'p',
                    ],
                    'default' => 'div',
                ]
        );
        $this->add_responsive_control(
                'content_align',
                [
                    'label' => __('Alignment', 'futurio_extra'),
                    'type' => Controls_Manager::CHOOSE,
                    'options' => [
                        'left' => [
                            'title' => __('Left', 'futurio_extra'),
                            'icon' => 'fa fa-align-left',
                        ],
                        'center' => [
                            'title' => __('Center', 'futurio_extra'),
                            'icon' => 'fa fa-align-center',
                        ],
                        'right' => [
                            'title' => __('Right', 'futurio_extra'),
                            'icon' => 'fa fa-align-right',
                        ],
                        'justify' => [
                            'title' => __('Justify', 'futurio_extra'),
                            'icon' => 'fa fa-align-justify',
                        ],
                    ],
                    'devices' => ['desktop', 'tablet', 'mobile'],
                    'prefix_class' => 'text-%s',
                ]
        );
        $this->end_controls_section();

        $this->start_controls_section(
                'section_styling',
                [
                    'label' => __('Typography', 'futurio_extra'),
                    'tab' => Controls_Manager::TAB_STYLE,
                ]
        );
        $this->add_control(
                'content_color',
                [
                    'label' => __('Text Color', 'futurio_extra'),
                    'type' => Controls_Manager::COLOR,
                    'default' => '#888',
                    'selectors' => [
                        '{{WRAPPER}} .futurio_extra_adv_text_block .text-content-block p,{{WRAPPER}} .futurio_extra_adv_text_block .text-content-block' => 'color:{{VALUE}};',
                    ],
                ]
        );
        $this->add_group_control(
                Group_Control_Typography::get_type(),
                [
                    'name' => 'content_typography',
                    'label' => __('Typography', 'futurio_extra'),
                    'scheme' => Scheme_Typography::TYPOGRAPHY_3,
                    'selector' => '{{WRAPPER}} .futurio_extra_adv_text_block .text-content-block,{{WRAPPER}} .futurio_extra_adv_text_block .text-content-block p',
                ]
        );

        $this->end_controls_section();

        /* Adv tab */
        $this->start_controls_section(
                'section_animation_styling',
                [
                    'label' => __('On Scroll View Animation', 'futurio_extra'),
                    'tab' => Controls_Manager::TAB_STYLE,
                ]
        );
        $this->add_control(
                'animation_effects',
                [
                    'label' => __('Choose Animation Effect', 'futurio_extra'),
                    'type' => Controls_Manager::SELECT,
                    'default' => 'no-animation',
                    'options' => $this->futurio_get_animation_options(),
                ]
        );
        $this->add_control(
                'animation_delay',
                [
                    'type' => Controls_Manager::SLIDER,
                    'label' => __('Animation Delay', 'futurio_extra'),
                    'default' => [
                        'unit' => '',
                        'size' => 50,
                    ],
                    'range' => [
                        '' => [
                            'min' => 0,
                            'max' => 4000,
                            'step' => 15,
                        ],
                    ],
                    'render_type' => 'ui',
                    'condition' => [
                        'animation_effects!' => 'no-animation',
                    ],
                ]
        );
        $this->add_control(
                'animation_duration_default',
                [
                    'label' => esc_html__('Animation Duration', 'futurio_extra'),
                    'type' => Controls_Manager::SWITCHER,
                    'default' => 'no',
                    'condition' => [
                        'animation_effects!' => 'no-animation',
                    ],
                ]
        );
        $this->add_control(
                'animate_duration',
                [
                    'type' => Controls_Manager::SLIDER,
                    'label' => __('Duration Speed', 'futurio_extra'),
                    'default' => [
                        'unit' => 'px',
                        'size' => 50,
                    ],
                    'range' => [
                        'px' => [
                            'min' => 100,
                            'max' => 10000,
                            'step' => 100,
                        ],
                    ],
                    'render_type' => 'ui',
                    'condition' => [
                        'animation_effects!' => 'no-animation',
                        'animation_duration_default' => 'yes',
                    ],
                ]
        );
        $this->end_controls_section();
    }

    protected function futurio_get_animation_options() {
        return array(
            'no-animation' => __('No-animation', 'futurio_extra'),
            'transition.fadeIn' => __('FadeIn', 'futurio_extra'),
            'transition.flipXIn' => __('FlipXIn', 'futurio_extra'),
            'transition.flipYIn' => __('FlipYIn', 'futurio_extra'),
            'transition.flipBounceXIn' => __('FlipBounceXIn', 'futurio_extra'),
            'transition.flipBounceYIn' => __('FlipBounceYIn', 'futurio_extra'),
            'transition.swoopIn' => __('SwoopIn', 'futurio_extra'),
            'transition.whirlIn' => __('WhirlIn', 'futurio_extra'),
            'transition.shrinkIn' => __('ShrinkIn', 'futurio_extra'),
            'transition.expandIn' => __('ExpandIn', 'futurio_extra'),
            'transition.bounceIn' => __('BounceIn', 'futurio_extra'),
            'transition.bounceUpIn' => __('BounceUpIn', 'futurio_extra'),
            'transition.bounceDownIn' => __('BounceDownIn', 'futurio_extra'),
            'transition.bounceLeftIn' => __('BounceLeftIn', 'futurio_extra'),
            'transition.bounceRightIn' => __('BounceRightIn', 'futurio_extra'),
            'transition.slideUpIn' => __('SlideUpIn', 'futurio_extra'),
            'transition.slideDownIn' => __('SlideDownIn', 'futurio_extra'),
            'transition.slideLeftIn' => __('SlideLeftIn', 'futurio_extra'),
            'transition.slideRightIn' => __('SlideRightIn', 'futurio_extra'),
            'transition.slideUpBigIn' => __('SlideUpBigIn', 'futurio_extra'),
            'transition.slideDownBigIn' => __('SlideDownBigIn', 'futurio_extra'),
            'transition.slideLeftBigIn' => __('SlideLeftBigIn', 'futurio_extra'),
            'transition.slideRightBigIn' => __('SlideRightBigIn', 'futurio_extra'),
            'transition.perspectiveUpIn' => __('PerspectiveUpIn', 'futurio_extra'),
            'transition.perspectiveDownIn' => __('PerspectiveDownIn', 'futurio_extra'),
            'transition.perspectiveLeftIn' => __('PerspectiveLeftIn', 'futurio_extra'),
            'transition.perspectiveRightIn' => __('PerspectiveRightIn', 'futurio_extra'),
        );
    }

    protected function render() {

        $settings = $this->get_settings_for_display();
        $content = $settings['content_description'];
        $block = $settings['header_size'];

        $animation_effects = $settings["animation_effects"];
        $animation_delay = '';
        if ($settings["animation_effects"] != 'no-animation') {
            $animation_delay = $settings["animation_delay"]["size"];
        }
        $animate_duration = '';
        if ($settings["animation_duration_default"] == 'yes') {
            $animate_duration = $settings["animate_duration"]["size"];
        }
        if ($animation_effects == 'no-animation') {
            $animated_class = '';
            $animation_attr = '';
        } else {
            $animated_class = 'animate-general';
            $animation_attr = ' data-animate-type="' . esc_attr($animation_effects) . '" data-animate-delay="' . esc_attr($animation_delay) . '"';
            if ($settings["animation_duration_default"] == 'yes') {
                $animation_attr .= ' data-animate-duration="' . esc_attr($animate_duration) . '"';
            }
        }

        $text_block = '<div class="futurio_extra_adv_text_block ' . $animated_class . '" ' . $animation_attr . '>';
        $text_block .= '<' . $block . ' class="text-content-block">';
        $text_block .= $content;
        $text_block .= '</div>';
        $text_block .= '</' . $block . '>';

        echo $text_block;
    }

    protected function content_template() {
        
    }

}
