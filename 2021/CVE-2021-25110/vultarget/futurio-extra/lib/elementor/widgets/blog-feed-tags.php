<?php

use Elementor\Controls_Manager;
use Elementor\Scheme_Color;
use Elementor\Widget_Base;
use Elementor\Group_Control_Typography;
use Elementor\Scheme_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Elementor text editor widget.
 *
 * Elementor widget that displays a WYSIWYG text editor, just like the WordPress
 * editor.
 *
 * @since 1.0.0
 */
class Futurio_Extra_Blog_Feed_Tags extends Widget_Base {

    /**
     * Get widget name.
     *
     * Retrieve text editor widget name.
     *
     * @since 1.0.0
     * @access public
     *
     * @return string Widget name.
     */
    public function get_name() {
        return 'blog-tags';
    }

    /**
     * Get widget title.
     *
     * Retrieve text editor widget title.
     *
     * @since 1.0.0
     * @access public
     *
     * @return string Widget title.
     */
    public function get_title() {
        return __('Post tags', 'futurio-extra');
    }

    /**
     * Get widget icon.
     *
     * Retrieve text editor widget icon.
     *
     * @since 1.0.0
     * @access public
     *
     * @return string Widget icon.
     */
    public function get_icon() {
        return 'eicon-folder';
    }

    /**
     * Get widget categories.
     *
     * Retrieve the list of categories the text editor widget belongs to.
     *
     * Used to determine where to display the widget in the editor.
     *
     * @since 2.0.0
     * @access public
     *
     * @return array Widget categories.
     */
    public function get_categories() {
        return ['blog-layout'];
    }

    /**
     * Register text editor widget controls.
     *
     * Adds different input fields to allow the user to change and customize the widget settings.
     *
     * @since 1.0.0
     * @access protected
     */
    protected function _register_controls() {
        $this->start_controls_section(
                'section_tags',
                [
                    'label' => __('Content', 'futurio-extra'),
                ]
        );

        $this->add_control(
                'separator',
                [
                    'label' => __('Separator', 'futurio-extra'),
                    'type' => Controls_Manager::TEXT,
                    'default' => ' / ',
                ]
        );
        $this->add_control(
                'text',
                [
                    'label' => __('Text before', 'futurio-extra'),
                    'type' => Controls_Manager::TEXT,
                    'default' => __('Tags:', 'futurio-extra'),
                    'placeholder' => __('Tags:', 'futurio-extra'),
                ]
        );
        $this->add_control(
                'icon',
                [
                    'label' => __('Icon', 'futurio-extra'),
                    'type' => Controls_Manager::ICON,
                    'label_block' => true,
                    'default' => 'fa fa-tags',
                    'separator' => 'before',
                ]
        );

        $this->add_control(
                'icon_align',
                [
                    'label' => __('Icon Position', 'futurio-extra'),
                    'type' => Controls_Manager::SELECT,
                    'default' => 'left',
                    'options' => [
                        'left' => __('Before', 'futurio-extra'),
                        'right' => __('After', 'futurio-extra'),
                    ],
                    'condition' => [
                        'icon!' => '',
                    ],
                ]
        );

        $this->add_control(
                'icon_indent',
                [
                    'label' => __('Icon Spacing', 'futurio-extra'),
                    'type' => Controls_Manager::SLIDER,
                    'range' => [
                        'px' => [
                            'max' => 50,
                        ],
                    ],
                    'condition' => [
                        'icon!' => '',
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .futurio-elementor-tags .elementor-align-icon-right' => 'margin-left: {{SIZE}}{{UNIT}};',
                        '{{WRAPPER}} .futurio-elementor-tags .elementor-align-icon-left' => 'margin-right: {{SIZE}}{{UNIT}};',
                    ],
                ]
        );


        $this->end_controls_section();

        $this->start_controls_section(
                'section_style',
                [
                    'label' => __('Content', 'futurio-extra'),
                    'tab' => Controls_Manager::TAB_STYLE,
                ]
        );

        $this->add_responsive_control(
                'align',
                [
                    'label' => __('Alignment', 'futurio-extra'),
                    'type' => Controls_Manager::CHOOSE,
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
                    'selectors' => [
                        '{{WRAPPER}} .futurio-elementor-tags' => 'text-align: {{VALUE}};',
                    ],
                ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
                'text_style',
                [
                    'label' => __('Text', 'futurio-extra'),
                    'tab' => Controls_Manager::TAB_STYLE,
                ]
        );

        $this->add_group_control(
                Group_Control_Typography::get_type(),
                [
                    'name' => 'text_typography',
                    'scheme' => Scheme_Typography::TYPOGRAPHY_3,
                    'selector' => '{{WRAPPER}} .futurio-elementor-feed-title',
                ]
        );


        $this->add_control(
                'text_separator_color',
                [
                    'label' => __('Text Color', 'futurio-extra'),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} .futurio-elementor-feed-title' => 'color: {{VALUE}}',
                    ],
                ]
        );
        $this->add_group_control(
                Group_Control_Border::get_type(),
                [
                    'name' => 'text_border',
                    'placeholder' => '1px',
                    'default' => '1px',
                    'selector' => '{{WRAPPER}} .futurio-elementor-feed-title',
                    'separator' => 'before',
                ]
        );

        $this->add_control(
                'text_border_radius',
                [
                    'label' => __('Border Radius', 'futurio-extra'),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => ['px', '%'],
                    'selectors' => [
                        '{{WRAPPER}} .futurio-elementor-feed-title' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                ]
        );

        $this->add_group_control(
                Group_Control_Box_Shadow::get_type(),
                [
                    'name' => 'text_box_shadow',
                    'selector' => '{{WRAPPER}} .futurio-elementor-feed-title',
                ]
        );

        $this->add_responsive_control(
                'text_text_padding',
                [
                    'label' => __('Padding', 'futurio-extra'),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => ['px', 'em', '%'],
                    'selectors' => [
                        '{{WRAPPER}} .futurio-elementor-feed-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                    'separator' => 'before',
                ]
        );

        $this->add_responsive_control(
                'text_text_margin',
                [
                    'label' => __('Margin', 'futurio-extra'),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => ['px', 'em', '%'],
                    'selectors' => [
                        '{{WRAPPER}} .futurio-elementor-feed-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                    'separator' => 'before',
                ]
        );
        $this->end_controls_section();

        $this->start_controls_section(
                'cats_style',
                [
                    'label' => __('Tags', 'futurio-extra'),
                    'tab' => Controls_Manager::TAB_STYLE,
                ]
        );

        $this->add_group_control(
                Group_Control_Typography::get_type(),
                [
                    'name' => 'typography',
                    'scheme' => Scheme_Typography::TYPOGRAPHY_3,
                    'selector' => '{{WRAPPER}} a',
                ]
        );

        $this->start_controls_tabs('title_tabs_title');

        $this->start_controls_tab(
                'title_tab_title_normal',
                [
                    'label' => __('Normal', 'futurio-extra'),
                ]
        );

        $this->add_responsive_control(
                'title_color',
                [
                    'label' => __('Tag Color', 'futurio-extra'),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} a' => 'color: {{VALUE}}',
                    ],
                ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
                'title_tab_title_hover',
                [
                    'label' => __('Hover', 'futurio-extra'),
                ]
        );

        $this->add_responsive_control(
                'title_color_hover',
                [
                    'label' => __('Tag Color', 'futurio-extra'),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} a:hover' => 'color: {{VALUE}}',
                    ],
                ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_control(
                'separator_color',
                [
                    'label' => __('Separator Color', 'futurio-extra'),
                    'type' => Controls_Manager::COLOR,
                    'separator' => 'before',
                    'selectors' => [
                        '{{WRAPPER}}' => 'color: {{VALUE}}',
                    ],
                ]
        );
        $this->add_group_control(
                Group_Control_Border::get_type(),
                [
                    'name' => 'border',
                    'placeholder' => '1px',
                    'default' => '1px',
                    'selector' => '{{WRAPPER}} .futurio-elementor-tags a',
                    'separator' => 'before',
                ]
        );

        $this->add_control(
                'border_radius',
                [
                    'label' => __('Border Radius', 'futurio-extra'),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => ['px', '%'],
                    'selectors' => [
                        '{{WRAPPER}} .futurio-elementor-tags a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                ]
        );

        $this->add_group_control(
                Group_Control_Box_Shadow::get_type(),
                [
                    'name' => 'button_box_shadow',
                    'selector' => '{{WRAPPER}} .futurio-elementor-tags a',
                ]
        );

        $this->add_responsive_control(
                'text_padding',
                [
                    'label' => __('Padding', 'futurio-extra'),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => ['px', 'em', '%'],
                    'selectors' => [
                        '{{WRAPPER}} .futurio-elementor-tags a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                    'separator' => 'before',
                ]
        );

        $this->add_responsive_control(
                'text_margin',
                [
                    'label' => __('Margin', 'futurio-extra'),
                    'type' => Controls_Manager::DIMENSIONS,
                    'size_units' => ['px', 'em', '%'],
                    'selectors' => [
                        '{{WRAPPER}} .futurio-elementor-tags a' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    ],
                    'separator' => 'before',
                ]
        );
        $this->end_controls_section();
    }

    /**
     * Render text editor widget output on the frontend.
     *
     * Written in PHP and used to generate the final HTML.
     *
     * @since 1.0.0
     * @access protected
     */
    protected function render() {
        $settings = $this->get_settings();

        $separator = $settings['separator'];

        $tags_list = get_the_tag_list('', $separator);

        $this->add_render_attribute([
            'futurio-tags' => [
                'class' => 'futurio-elementor-tags',
            ],
            'futurio-tags-title' => [
                'class' => 'futurio-elementor-feed-title',
            ],
            'icon-align' => [
                'class' => [
                    'futurio-elementor-icon elementor-button-icon',
                    'futurio-elementor-icon elementor-align-icon-' . $settings['icon_align'],
                ],
            ],
            'text' => [
                'class' => 'futurio-elementor-icon elementor-button-text',
            ],
        ]);

        if (is_home() || is_archive() || is_search()) {
            ?>
            <div <?php echo $this->get_render_attribute_string('futurio-tags'); ?>>
                <div <?php echo $this->get_render_attribute_string('futurio-tags-title'); ?>>
                    <?php if (!empty($settings['icon'])) : ?>
                        <span <?php echo $this->get_render_attribute_string('icon-align'); ?>>
                            <i class="<?php echo esc_attr($settings['icon']); ?>" aria-hidden="true"></i>
                        </span>
                    <?php endif; ?>
                    <span <?php echo $this->get_render_attribute_string('text'); ?>><?php echo $settings['text']; ?></span>
                </div>
                <?php echo wp_kses_data($tags_list); ?>
            </div>
            <?php
        } else {
            ?>
            <div <?php echo $this->get_render_attribute_string('futurio-tags'); ?>>
                <div <?php echo $this->get_render_attribute_string('futurio-tags-title'); ?>>
                    <?php if (!empty($settings['icon'])) : ?>
                        <span <?php echo $this->get_render_attribute_string('icon-align'); ?>>
                            <i class="<?php echo esc_attr($settings['icon']); ?>" aria-hidden="true"></i>
                        </span>
                    <?php endif; ?>
                    <span <?php echo $this->get_render_attribute_string('text'); ?>><?php echo $settings['text']; ?></span>
                </div>
                <a href="#">Dummy tag</a><?php echo esc_html($separator); ?><a href="#">Second tag</a>
            </div>
            <?php
        }
    }

}
