<?php

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Scheme_Color;
use Elementor\Scheme_Typography;
use Elementor\Widget_Base;

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly

class Futurio_Extra_Widget_Writing_Effect_Headline extends Widget_Base {

    public function get_name() {
        return 'writing-effect-headline';
    }

    public function get_title() {
        return __('Writing Effect Headline', 'futurio-extra');
    }

    public function get_icon() {
        return 'fa fa-i-cursor';
    }

    public function get_categories() {
        return ['basic'];
    }

    protected function _register_controls() {

        $this->start_controls_section(
                'text_elements',
                [
                    'label' => __('Headline', 'futurio-extra'),
                ]
        );

        $this->add_control(
                'before_text',
                [
                    'label' => __('Before Text', 'futurio-extra'),
                    'type' => Controls_Manager::TEXT,
                    'default' => 'Static text before',
                    'placeholder' => __('Enter your headline', 'futurio-extra'),
                    'label_block' => true,
                    'separator' => 'before',
                ]
        );

        $this->add_control(
                'written_text',
                [
                    'label' => __('Written Text', 'futurio-extra'),
                    'type' => Controls_Manager::TEXTAREA,
                    'placeholder' => __('Enter each word in a separate line', 'futurio-extra'),
                    'description' => __('Enter each word in a separate line', 'futurio-extra'),
                    'separator' => 'none',
                    'default' => "First line\n2nd line\nlast line",
                    'rows' => 5,
                ]
        );

        $this->add_control(
                'after_text',
                [
                    'label' => __('After Text', 'futurio-extra'),
                    'type' => Controls_Manager::TEXT,
                    'placeholder' => __('Enter your headline', 'futurio-extra'),
                    'label_block' => true,
                    'separator' => 'none',
                ]
        );

        $this->add_control(
                'writing_speed',
                [
                    'label' => __('Writing speed', 'futurio-extra'),
                    'type' => Controls_Manager::SLIDER,
                    'description' => __('Lower is faster', 'futurio-extra'),
                    'default' => [
                        'size' => 50,
                        'unit' => 'ms'
                    ],
                    'range' => [
                        'ms' => [
                            'min' => 10,
                            'max' => 1000,
                            'step' => 1,
                        ],
                    ],
                ]
        );

        $this->add_control(
                'delay_loop',
                [
                    'label' => __('Delay', 'futurio-extra'),
                    'type' => Controls_Manager::SLIDER,
                    'description' => __('Lower is faster', 'futurio-extra'),
                    'default' => [
                        'size' => 1000,
                        'unit' => 'ms'
                    ],
                    'range' => [
                        'ms' => [
                            'min' => 100,
                            'max' => 10000,
                            'step' => 100,
                        ],
                    ],
                ]
        );

        $this->add_control(
                'loop',
                [
                    'label' => __('Loop writing effect', 'futurio-extra'),
                    'type' => Controls_Manager::SWITCHER,
                    'default' => 'yes',
                    'off' => __('Off', 'futurio-extra'),
                    'on' => __('On', 'futurio-extra'),
                    'separator' => 'before',
                ]
        );

        $this->add_responsive_control(
                'alignment',
                [
                    'label' => __('Alignment', 'futurio-extra'),
                    'type' => Controls_Manager::CHOOSE,
                    'label_block' => false,
                    'options' => [
                        'left' => [
                            'title' => __('Left', 'futurio-extra'),
                            'icon' => 'fa fa-align-left',
                        ],
                        'center' => [
                            'title' => __('Center', 'futurio-extra'),
                            'icon' => 'fa fa-align-center',
                        ],
                        'right' => [
                            'title' => __('Right', 'futurio-extra'),
                            'icon' => 'fa fa-align-right',
                        ],
                    ],
                    'default' => 'center',
                    'separator' => 'before',
                    'selectors' => [
                        '{{WRAPPER}} .futurio-extra-written-headline' => 'text-align: {{VALUE}}',
                    ],
                ]
        );

        $this->add_control(
                'tag',
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
                    'default' => 'h3',
                ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
                'section_style_text',
                [
                    'label' => __('Headline', 'futurio-extra'),
                    'tab' => Controls_Manager::TAB_STYLE,
                ]
        );

        $this->add_control(
                'title_color',
                [
                    'label' => __('Text Color', 'futurio-extra'),
                    'type' => Controls_Manager::COLOR,
                    'scheme' => [
                        'type' => Scheme_Color::get_type(),
                        'value' => Scheme_Color::COLOR_2,
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .futurio-extra-written-headline' => 'color: {{VALUE}}',
                    ],
                ]
        );

        $this->add_group_control(
                Group_Control_Typography::get_type(),
                [
                    'name' => 'title_typography',
                    'scheme' => Scheme_Typography::TYPOGRAPHY_1,
                    'selector' => '{{WRAPPER}} .futurio-extra-written-headline',
                ]
        );

        $this->add_control(
                'heading_words_style',
                [
                    'type' => Controls_Manager::HEADING,
                    'label' => __('Written Text', 'futurio-extra'),
                    'separator' => 'before',
                ]
        );

        $this->add_control(
                'words_color',
                [
                    'label' => __('Text Color', 'futurio-extra'),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .futurio-extra-written-headline .written-lines, {{WRAPPER}} .typed-cursor' => 'color: {{VALUE}}',
                    ],
                ]
        );

        $this->add_group_control(
                Group_Control_Typography::get_type(),
                [
                    'name' => 'words_typography',
                    'selector' => '{{WRAPPER}} .futurio-extra-written-headline .written-lines, {{WRAPPER}} .typed-cursor',
                    'exclude' => ['font_size'],
                ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

        $tag = $settings['tag'];

        $this->add_render_attribute('headline',
                [
                    'class' => 'futurio-extra-written-headline',
                    'data-speed' => $settings['writing_speed']['size'],
                    'data-delay' => $settings['delay_loop']['size'],
                    'data-loop' => $settings['loop'] === 'yes' ? 1 : 0,
                ]
        );

        wp_enqueue_script('jquery-typed');
        wp_enqueue_script('futurio-extra-frontend');
        ?>
        <<?php echo $tag; ?> <?php echo $this->get_render_attribute_string('headline'); ?>>
        <?php if (!empty($settings['before_text'])) : ?>
            <span class="before-written"><?php echo $settings['before_text']; ?></span>
        <?php endif; ?>

        <?php if (!empty($settings['written_text'])) : ?>
            <span class="written-lines"><?php echo $settings['written_text']; ?></span>
        <?php endif; ?>

        <?php if (!empty($settings['after_text'])) : ?>
            <span class="after-written"><?php echo $settings['after_text']; ?></span>
        <?php endif; ?>
        </<?php echo $tag; ?>>
        <?php
    }

    protected function _content_template() {
        ?>
        <#
        var headlineClasses = 'futurio-extra-written-headline';
        var speed = settings.writing_speed.size;
        var delay = settings.delay_loop.size;
        var loop = settings.loop === 'yes' ? 1 : 0;
        #>
        <{{{ settings.tag }}} class="{{{ headlineClasses }}}" data-speed="{{{ speed }}}" data-delay="{{{ delay }}}" data-loop="{{{ loop }}}">
        <# if ( settings.before_text ) { #>
        <span class="before-written">{{{ settings.before_text }}}</span>
        <# } #>

        <# if ( settings.written_text ) { #>
        <span class="written-lines">{{{ settings.written_text }}}</span>
        <# } #>

        <# if ( settings.after_text ) { #>
        <span class="after-written">{{{ settings.after_text }}}</span>
        <# } #>
        </{{{ settings.tag }}}>
        <?php
    }

}
