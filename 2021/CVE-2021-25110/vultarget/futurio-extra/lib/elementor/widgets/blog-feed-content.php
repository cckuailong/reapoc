<?php

use Elementor\Controls_Manager;
use Elementor\Scheme_Color;
use Elementor\Widget_Base;
use Elementor\Group_Control_Typography;
use Elementor\Scheme_Typography;

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
class Futurio_Extra_Blog_Feed_Content extends Widget_Base {

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
        return 'blog-content';
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
        return __('Content', 'futurio-extra');
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
        return 'eicon-align-left';
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
                'section_editor',
                [
                    'label' => __('Content', 'futurio-extra'),
                ]
        );

        $this->add_control(
                'content_type',
                [
                    'label' => __('Content', 'futurio-extra'),
                    'type' => Controls_Manager::SELECT,
                    'default' => 'excerpt',
                    'options' => [
                        'excerpt' => __('Excerpt', 'futurio-extra'),
                        'full' => __('Full Content', 'futurio-extra'),
                    ],
                ]
        );
        $this->add_control(
                'limit',
                [
                    'label' => __('Excerpt', 'futurio-extra'),
                    'type' => Controls_Manager::SLIDER,
                    'default' => [
                        'size' => 35,
                    ],
                    'range' => [
                        'ms' => [
                            'min' => 0,
                            'max' => 100,
                            'step' => 1,
                        ],
                    ],
                    'condition' => [
                        'content_type' => 'excerpt',
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
                        'justify' => [
                            'title' => __('Justified', 'futurio-extra'),
                            'icon' => 'fa fa-align-justify',
                        ],
                    ],
                    'selectors' => [
                        '{{WRAPPER}} .futurio-elementor-content' => 'text-align: {{VALUE}};',
                    ],
                ]
        );

        $this->add_control(
                'text_color',
                [
                    'label' => __('Text Color', 'futurio-extra'),
                    'type' => Controls_Manager::COLOR,
                    'default' => '',
                    'selectors' => [
                        '{{WRAPPER}}' => 'color: {{VALUE}};',
                    ],
                    'scheme' => [
                        'type' => Scheme_Color::get_type(),
                        'value' => Scheme_Color::COLOR_3,
                    ],
                ]
        );

        $this->add_group_control(
                Group_Control_Typography::get_type(),
                [
                    'name' => 'typography',
                    'scheme' => Scheme_Typography::TYPOGRAPHY_3,
                ]
        );

        $this->start_controls_tabs('footer_title_tabs_title');

        $this->start_controls_tab(
                'footer_title_tab_title_normal',
                [
                    'label' => __('Normal', 'futurio-extra'),
                ]
        );

        $this->add_responsive_control(
                'footer_title_color',
                [
                    'label' => __('Links Color', 'futurio-extra'),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} a' => 'color: {{VALUE}}',
                    ],
                ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
                'footer_title_tab_title_hover',
                [
                    'label' => __('Hover', 'futurio-extra'),
                ]
        );

        $this->add_responsive_control(
                'footer_title_color_hover',
                [
                    'label' => __('Links Color', 'futurio-extra'),
                    'type' => Controls_Manager::COLOR,
                    'selectors' => [
                        '{{WRAPPER}} a:hover' => 'color: {{VALUE}}',
                    ],
                ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

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
        $limit = $settings['limit']['size'];


        $this->add_render_attribute('futurio-content', 'class', ['futurio-elementor-content']);

        if (is_home() || is_archive() || is_search()) {
            ?>
            <div <?php echo $this->get_render_attribute_string('futurio-content'); ?>>
                <?php
                if ($settings['content_type'] == 'excerpt') {
                    echo wp_trim_words(wp_strip_all_tags(get_the_excerpt()), $limit);
                } else {
                    the_content();
                }
                ?>
            </div>
            <?php
        } else {
            ?>
            <div <?php echo $this->get_render_attribute_string('futurio-content'); ?>>
                <?php
                $content = 'This is a dummy text to demonstration purposes. It will be replaced on blog feed with the post content/excerpt. Do not use this widget in the posts/pages. Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Morbi scelerisque luctus velit. Etiam quis quam. Duis viverra diam non justo. Suspendisse sagittis ultrices augue. Duis sapien nunc, commodo et, interdum suscipit, sollicitudin et, dolor. Donec ipsum massa, ullamcorper in, auctor et. End of the dummy content.';
                if ($settings['content_type'] == 'excerpt') {
                    echo wp_trim_words(wp_strip_all_tags($content), $limit);
                } else {
                    echo $content;
                }
                ?>
            </div>
            <?php
        }
    }

}
