<?php
namespace PowerpackElementsLite\Modules\IconList\Widgets;

use PowerpackElementsLite\Base\Powerpack_Widget;
use PowerpackElementsLite\Classes\PP_Config;

// Elementor Classes
use Elementor\Controls_Manager;
use Elementor\Control_Media;
use Elementor\Utils;
use Elementor\Repeater;
use Elementor\Icons_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Image_Size;
use Elementor\Core\Schemes;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Icon List Widget
 */
class Icon_List extends Powerpack_Widget {

	/**
	 * Retrieve icon list widget name.
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return parent::get_widget_name( 'Icon_List' );
	}

	/**
	 * Retrieve icon list widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return parent::get_widget_title( 'Icon_List' );
	}

	/**
	 * Retrieve icon list widget icon.
	 *
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return parent::get_widget_icon( 'Icon_List' );
	}

	/**
	 * Get widget keywords.
	 *
	 * Retrieve the list of keywords the widget belongs to.
	 *
	 * @access public
	 *
	 * @return array Widget keywords.
	 */
	public function get_keywords() {
		return parent::get_widget_keywords( 'Icon_List' );
	}

	/**
	 * Register icon list widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * Remove this after Elementor v3.4.0
	 *
	 * @access protected
	 */
	protected function _register_controls() { // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
		$this->register_controls();
	}

	/**
	 * Register icon list widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 2.3.2
	 * @access protected
	 */
	protected function register_controls() {

		/* Content Tab */
		$this->register_content_list_controls();
		$this->register_content_help_docs_controls();

		/* Style Tab */
		$this->register_style_list_controls();
		$this->register_style_icon_controls();
		$this->register_style_text_controls();
	}

	/*-----------------------------------------------------------------------------------*/
	/*	CONTENT TAB
	/*-----------------------------------------------------------------------------------*/

	protected function register_content_list_controls() {
		/**
		 * Content Tab: List
		 */
		$this->start_controls_section(
			'section_list',
			[
				'label'                 => __( 'List', 'powerpack' ),
			]
		);

		$this->add_control(
			'view',
			[
				'label'                 => __( 'Layout', 'powerpack' ),
				'type'                  => Controls_Manager::CHOOSE,
				'default'               => 'traditional',
				'options'               => [
					'traditional'  => [
						'title'    => __( 'Default', 'powerpack' ),
						'icon'     => 'eicon-editor-list-ul',
					],
					'inline'       => [
						'title'    => __( 'Inline', 'powerpack' ),
						'icon'     => 'eicon-ellipsis-h',
					],
				],
				'render_type'           => 'template',
				'prefix_class'          => 'pp-icon-list-',
				'label_block'           => false,
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'text',
			array(
				'label'       => __( 'Text', 'powerpack' ),
				'label_block' => true,
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => array(
					'active' => true,
				),
				'default'     => __( 'List Item #1', 'powerpack' ),
			)
		);

		$repeater->add_control(
			'pp_icon_type',
			array(
				'label'       => esc_html__( 'Icon Type', 'powerpack' ),
				'type'        => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options'     => array(
					'none'  => array(
						'title' => esc_html__( 'None', 'powerpack' ),
						'icon'  => 'eicon-ban',
					),
					'icon'  => array(
						'title' => esc_html__( 'Icon', 'powerpack' ),
						'icon'  => 'eicon-star',
					),
					'image' => array(
						'title' => esc_html__( 'Image', 'powerpack' ),
						'icon'  => 'eicon-image-bold',
					),
					'text'  => array(
						'title' => esc_html__( 'Text', 'powerpack' ),
						'icon'  => 'eicon-font',
					),
				),
				'default'     => 'icon',
			)
		);

		$repeater->add_control(
			'icon',
			array(
				'label'            => __( 'Icon', 'powerpack' ),
				'type'             => Controls_Manager::ICONS,
				'label_block'      => true,
				'default'          => array(
					'value'   => 'fas fa-check',
					'library' => 'fa-solid',
				),
				'fa4compatibility' => 'list_icon',
				'condition'        => array(
					'pp_icon_type' => 'icon',
				),
			)
		);

		$repeater->add_control(
			'list_image',
			array(
				'label'       => __( 'Image', 'powerpack' ),
				'label_block' => true,
				'type'        => Controls_Manager::MEDIA,
				'dynamic'     => array(
					'active' => true,
				),
				'default'     => array(
					'url' => Utils::get_placeholder_image_src(),
				),
				'condition'   => array(
					'pp_icon_type' => 'image',
				),
			)
		);

		$repeater->add_control(
			'icon_text',
			array(
				'label'       => __( 'Number/Text', 'powerpack' ),
				'label_block' => false,
				'type'        => Controls_Manager::TEXT,
				'default'     => '',
				'condition'   => array(
					'pp_icon_type' => 'number',
				),
			)
		);

		$repeater->add_control(
			'link',
			array(
				'label'       => __( 'Link', 'powerpack' ),
				'type'        => Controls_Manager::URL,
				'label_block' => true,
				'dynamic'     => array(
					'active' => true,
				),
				'placeholder' => __( 'http://your-link.com', 'powerpack' ),
			)
		);

		$this->add_control(
			'list_items',
			array(
				'label'       => '',
				'type'        => Controls_Manager::REPEATER,
				'default'     => array(
					array(
						'text' => __( 'List Item #1', 'powerpack' ),
						'icon' => __( 'fa fa-check', 'powerpack' ),
					),
					array(
						'text' => __( 'List Item #2', 'powerpack' ),
						'icon' => __( 'fa fa-check', 'powerpack' ),
					),
					array(
						'text' => __( 'List Item #3', 'powerpack' ),
						'icon' => __( 'fa fa-check', 'powerpack' ),
					),
				),
				'fields'      => $repeater->get_controls(),
				'title_field' => '<i class="{{ icon }}" aria-hidden="true"></i> {{{ text }}}',
			)
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'                  => 'image',
				'label'                 => __( 'Image Size', 'powerpack' ),
				'default'               => 'full',
				'separator'             => 'before',
			]
		);

		$this->end_controls_section();
	}

	protected function register_content_help_docs_controls() {
		$help_docs = PP_Config::get_widget_help_links( 'Icon_List' );

		if ( ! empty( $help_docs ) ) {

			/**
			 * Content Tab: Help Docs
			 *
			 * @since 1.4.8
			 * @access protected
			 */
			$this->start_controls_section(
				'section_help_docs',
				[
					'label' => __( 'Help Docs', 'powerpack' ),
				]
			);

			$hd_counter = 1;
			foreach ( $help_docs as $hd_title => $hd_link ) {
				$this->add_control(
					'help_doc_' . $hd_counter,
					[
						'type'            => Controls_Manager::RAW_HTML,
						'raw'             => sprintf( '%1$s ' . $hd_title . ' %2$s', '<a href="' . $hd_link . '" target="_blank" rel="noopener">', '</a>' ),
						'content_classes' => 'pp-editor-doc-links',
					]
				);

				$hd_counter++;
			}

			$this->end_controls_section();
		}
	}

	/*-----------------------------------------------------------------------------------*/
	/*	STYLE TAB
	/*-----------------------------------------------------------------------------------*/

	protected function register_style_list_controls() {
		/**
		 * Style Tab: List
		 */
		$this->start_controls_section(
			'section_list_style',
			[
				'label'                 => __( 'List', 'powerpack' ),
				'tab'                   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'                  => 'items_background',
				'label'                 => __( 'Background', 'powerpack' ),
				'types'                 => [ 'classic', 'gradient' ],
				'selector'              => '{{WRAPPER}} .pp-list-items li',
			]
		);

		$this->add_responsive_control(
			'items_spacing',
			[
				'label'                 => __( 'List Items Gap', 'powerpack' ),
				'type'                  => Controls_Manager::SLIDER,
				'range'                 => [
					'px' => [
						'max' => 50,
					],
				],
				'separator'             => 'before',
				'selectors'             => [
					'{{WRAPPER}} .pp-list-items:not(.pp-inline-items) li:not(:last-child)' => 'margin-bottom: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .pp-list-items.pp-inline-items li:not(:last-child)' => 'margin-right: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'list_items_padding',
			[
				'label'                 => __( 'Padding', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-list-items li' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'list_items_alignment',
			[
				'label'                 => __( 'Alignment', 'powerpack' ),
				'type'                  => Controls_Manager::CHOOSE,
				'options'               => [
					'left'      => [
						'title' => __( 'Left', 'powerpack' ),
						'icon'  => 'eicon-h-align-left',
					],
					'center'    => [
						'title' => __( 'Center', 'powerpack' ),
						'icon'  => 'eicon-h-align-center',
					],
					'right'     => [
						'title' => __( 'Right', 'powerpack' ),
						'icon'  => 'eicon-h-align-right',
					],
				],
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}}.pp-icon-list-traditional .pp-list-items li, {{WRAPPER}}.pp-icon-list-inline .pp-list-items' => 'justify-content: {{VALUE}};',
				],
				'selectors_dictionary' => [
					'left' => 'flex-start',
					'right' => 'flex-end',
				],
			]
		);

		$this->add_control(
			'divider',
			[
				'label'                 => __( 'Divider', 'powerpack' ),
				'type'                  => Controls_Manager::SWITCHER,
				'label_off'             => __( 'Off', 'powerpack' ),
				'label_on'              => __( 'On', 'powerpack' ),
				'separator'             => 'before',
			]
		);

		$this->add_control(
			'divider_style',
			[
				'label'                 => __( 'Style', 'powerpack' ),
				'type'                  => Controls_Manager::SELECT,
				'options'               => [
					'solid'    => __( 'Solid', 'powerpack' ),
					'double'   => __( 'Double', 'powerpack' ),
					'dotted'   => __( 'Dotted', 'powerpack' ),
					'dashed'   => __( 'Dashed', 'powerpack' ),
					'groove'   => __( 'Groove', 'powerpack' ),
					'ridge'    => __( 'Ridge', 'powerpack' ),
				],
				'default'               => 'solid',
				'condition'             => [
					'divider' => 'yes',
				],
				'selectors'             => [
					'{{WRAPPER}} .pp-list-items:not(.pp-inline-items) li:not(:last-child)' => 'border-bottom-style: {{VALUE}};',
					'{{WRAPPER}} .pp-list-items.pp-inline-items li:not(:last-child)' => 'border-right-style: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'divider_weight',
			[
				'label'                 => __( 'Weight', 'powerpack' ),
				'type'                  => Controls_Manager::SLIDER,
				'default'               => [
					'size' => 1,
				],
				'range'                 => [
					'px'   => [
						'min' => 1,
						'max' => 10,
					],
				],
				'condition'             => [
					'divider' => 'yes',
				],
				'selectors'             => [
					'{{WRAPPER}} .pp-list-items:not(.pp-inline-items) li:not(:last-child)' => 'border-bottom-width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .pp-list-items.pp-inline-items li:not(:last-child)' => 'border-right-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'divider_color',
			[
				'label'                 => __( 'Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '#ddd',
				'scheme'                => [
					'type'  => Schemes\Color::get_type(),
					'value' => Schemes\Color::COLOR_3,
				],
				'condition'             => [
					'divider'  => 'yes',
				],
				'selectors'             => [
					'{{WRAPPER}} .pp-list-items:not(.pp-inline-items) li:not(:last-child)' => 'border-bottom-color: {{VALUE}};',
					'{{WRAPPER}} .pp-list-items.pp-inline-items li:not(:last-child)' => 'border-right-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function register_style_icon_controls() {
		/**
		 * Style Tab: Icon
		 */
		$this->start_controls_section(
			'section_icon_style',
			[
				'label'                 => __( 'Icon', 'powerpack' ),
				'tab'                   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'icon_position',
			[
				'label'                 => __( 'Position', 'powerpack' ),
				'type'                  => Controls_Manager::CHOOSE,
				'label_block'           => false,
				'toggle'                => false,
				'default'               => 'left',
				'options'               => [
					'left'      => [
						'title' => __( 'Left', 'powerpack' ),
						'icon'  => 'eicon-h-align-left',
					],
					'right'     => [
						'title' => __( 'Right', 'powerpack' ),
						'icon'  => 'eicon-h-align-right',
					],
				],
				'prefix_class'          => 'pp-icon-',
			]
		);

		$this->add_control(
			'icon_vertical_align',
			[
				'label'                 => __( 'Vertical Alignment', 'powerpack' ),
				'type'                  => Controls_Manager::CHOOSE,
				'label_block'           => false,
				'default'               => 'middle',
				'options'               => [
					'top'          => [
						'title'    => __( 'Top', 'powerpack' ),
						'icon'     => 'eicon-v-align-top',
					],
					'middle'       => [
						'title'    => __( 'Center', 'powerpack' ),
						'icon'     => 'eicon-v-align-middle',
					],
					'bottom'       => [
						'title'    => __( 'Bottom', 'powerpack' ),
						'icon'     => 'eicon-v-align-bottom',
					],
				],
				'selectors_dictionary'  => [
					'top'          => 'flex-start',
					'middle'       => 'center',
					'bottom'       => 'flex-end',
				],
				'selectors'             => [
					'{{WRAPPER}} .pp-list-container .pp-list-items li'   => 'align-items: {{VALUE}};',
				],
			]
		);

		$this->start_controls_tabs( 'tabs_icon_style' );

		$this->start_controls_tab(
			'tab_icon_normal',
			[
				'label'                 => __( 'Normal', 'powerpack' ),
			]
		);

		$this->add_control(
			'icon_color',
			[
				'label'                 => __( 'Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .pp-list-items .pp-icon-list-icon' => 'color: {{VALUE}};',
					'{{WRAPPER}} .pp-list-items .pp-icon-list-icon svg' => 'fill: {{VALUE}};',
				],
				'scheme'                => [
					'type'  => Schemes\Color::get_type(),
					'value' => Schemes\Color::COLOR_1,
				],
			]
		);

		$this->add_control(
			'icon_bg_color',
			[
				'label'                 => __( 'Background Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .pp-list-items .pp-icon-wrapper' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'icon_size',
			[
				'label'                 => __( 'Size', 'powerpack' ),
				'type'                  => Controls_Manager::SLIDER,
				'default'               => [
					'size' => 14,
				],
				'range'                 => [
					'px' => [
						'min' => 6,
						'max' => 100,
					],
				],
				'selectors'             => [
					'{{WRAPPER}} .pp-list-items .pp-icon-list-icon' => 'font-size: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .pp-list-items .pp-icon-list-image img' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'icon_spacing',
			[
				'label'                 => __( 'Spacing', 'powerpack' ),
				'type'                  => Controls_Manager::SLIDER,
				'default'               => [
					'size' => 8,
				],
				'range'                 => [
					'px' => [
						'max' => 50,
					],
				],
				'selectors'             => [
					'{{WRAPPER}}.pp-icon-left .pp-list-items .pp-icon-wrapper' => 'margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}}.pp-icon-right .pp-list-items .pp-icon-wrapper' => 'margin-left: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'                  => 'icon_border',
				'label'                 => __( 'Border', 'powerpack' ),
				'placeholder'           => '1px',
				'default'               => '1px',
				'selector'              => '{{WRAPPER}} .pp-list-items .pp-icon-wrapper',
			]
		);

		$this->add_control(
			'icon_border_radius',
			[
				'label'                 => __( 'Border Radius', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-list-items .pp-icon-wrapper, {{WRAPPER}} .pp-list-items .pp-icon-list-image img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'icon_padding',
			[
				'label'                 => __( 'Padding', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-list-items .pp-icon-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_icon_hover',
			[
				'label'                 => __( 'Hover', 'powerpack' ),
			]
		);

		$this->add_control(
			'icon_color_hover',
			[
				'label'                 => __( 'Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .pp-icon-list-item:hover .pp-icon-wrapper .pp-icon-list-icon' => 'color: {{VALUE}};',
					'{{WRAPPER}} .pp-icon-list-item:hover .pp-icon-wrapper .pp-icon-list-icon svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'icon_bg_color_hover',
			[
				'label'                 => __( 'Background Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .pp-icon-list-item:hover .pp-icon-wrapper' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'icon_border_color_hover',
			[
				'label'                 => __( 'Border Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .pp-icon-list-item:hover .pp-icon-wrapper' => 'border-color: {{VALUE}};',
				],
				'scheme'                => [
					'type'  => Schemes\Color::get_type(),
					'value' => Schemes\Color::COLOR_2,
				],
			]
		);

		$this->add_control(
			'icon_hover_animation',
			[
				'label'                 => __( 'Animation', 'powerpack' ),
				'type'                  => Controls_Manager::HOVER_ANIMATION,
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	protected function register_style_text_controls() {
		/**
		 * Style Tab: Text
		 */
		$this->start_controls_section(
			'section_text_style',
			[
				'label'                 => __( 'Text', 'powerpack' ),
				'tab'                   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs( 'tabs_text_style' );

		$this->start_controls_tab(
			'tab_text_normal',
			[
				'label'                 => __( 'Normal', 'powerpack' ),
			]
		);

		$this->add_control(
			'text_color',
			[
				'label'                 => __( 'Text Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .pp-icon-list-text' => 'color: {{VALUE}};',
				],
				'scheme' => [
					'type'  => Schemes\Color::get_type(),
					'value' => Schemes\Color::COLOR_2,
				],
			]
		);

		$this->add_control(
			'text_bg_color',
			[
				'label'                 => __( 'Background Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .pp-icon-list-text' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'                  => 'text_typography',
				'label'                 => __( 'Typography', 'powerpack' ),
				'scheme'                => Schemes\Typography::TYPOGRAPHY_3,
				'selector'              => '{{WRAPPER}} .pp-icon-list-text',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_text_hover',
			[
				'label'                 => __( 'Hover', 'powerpack' ),
			]
		);

		$this->add_control(
			'text_hover_color',
			[
				'label'                 => __( 'Text Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .pp-icon-list-item:hover .pp-icon-list-text' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'text_hover_bg_color',
			[
				'label'                 => __( 'Background Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .pp-icon-list-item:hover .pp-icon-list-text' => 'background: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Render icon list widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		$this->add_render_attribute( [
			'icon-list' => [
				'class' => 'pp-list-items',
			],
			'icon'      => [
				'class' => 'pp-icon-list-icon',
			],
			'icon-wrap' => [
				'class' => 'pp-icon-wrapper',
			],
		] );

		if ( 'inline' === $settings['view'] ) {
			$this->add_render_attribute( 'icon-list', 'class', 'pp-inline-items' );
		}

		$i = 1;
		?>
		<div class="pp-list-container">
			<ul <?php echo wp_kses_post( $this->get_render_attribute_string( 'icon-list' ) ); ?>>
				<?php foreach ( $settings['list_items'] as $index => $item ) : ?>
					<?php if ( $item['text'] ) { ?>
						<?php
							$item_key = $this->get_repeater_setting_key( 'item', 'list_items', $index );
							$text_key = $this->get_repeater_setting_key( 'text', 'list_items', $index );

							$this->add_render_attribute( [
								$item_key => [
									'class' => 'pp-icon-list-item',
								],
								$text_key => [
									'class' => 'pp-icon-list-text',
								],
							] );

							$this->add_inline_editing_attributes( $text_key, 'none' );
						?>
						<li <?php echo wp_kses_post( $this->get_render_attribute_string( $item_key ) ); ?>>
							<?php
							if ( '' !== $item['link']['url'] ) {
								$link_key = 'link_' . $i;

								$this->add_link_attributes( $link_key, $item['link'] );
								?>
								<a <?php echo wp_kses_post( $this->get_render_attribute_string( $link_key ) ); ?>>
								<?php
							}

							$this->render_iconlist_icon( $item, $i );
							?>
							<span <?php echo wp_kses_post( $this->get_render_attribute_string( $text_key ) ); ?>>
								<?php echo wp_kses_post( $item['text'] ); ?>
							</span>
							<?php
							if ( '' !== $item['link']['url'] ) { ?>
								</a>
								<?php
							}
							?>
						</li>
					<?php } ?>
					<?php $i++;
				endforeach; ?>
			</ul>
		</div>
		<?php
	}

	/**
	 * Render info-box carousel icon output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
	protected function render_iconlist_icon( $item, $i ) {
		$settings = $this->get_settings_for_display();

		$fallback_defaults = [
			'fa fa-check',
			'fa fa-times',
			'fa fa-dot-circle-o',
		];

		$migration_allowed = Icons_Manager::is_migration_allowed();

		// add old default
		if ( ! isset( $item['list_icon'] ) && ! $migration_allowed ) {
			$item['list_icon'] = isset( $fallback_defaults[ $index ] ) ? $fallback_defaults[ $index ] : 'fa fa-check';
		}

		$migrated = isset( $item['__fa4_migrated']['icon'] );
		$is_new = ! isset( $item['list_icon'] ) && $migration_allowed;

		if ( 'none' !== $item['pp_icon_type'] ) {
			$icon_key = 'icon_' . $i;
			$this->add_render_attribute( $icon_key, 'class', 'pp-icon-wrapper' );

			if ( $settings['icon_hover_animation'] ) {
				$icon_animation = 'elementor-animation-' . $settings['icon_hover_animation'];
			} else {
				$icon_animation = '';
			}
			?>
			<span <?php echo wp_kses_post( $this->get_render_attribute_string( $icon_key ) ); ?>>
				<?php
				if ( 'icon' === $item['pp_icon_type'] ) {
					if ( ! empty( $item['list_icon'] ) || ( ! empty( $item['icon']['value'] ) && $is_new ) ) { ?>
						<span class="pp-icon-list-icon pp-icon <?php echo esc_attr( $icon_animation ); ?>">
						<?php
						if ( $is_new || $migrated ) {
							Icons_Manager::render_icon( $item['icon'], [ 'aria-hidden' => 'true' ] );
						} else { ?>
							<i class="<?php echo esc_attr( $item['list_icon'] ); ?>" aria-hidden="true"></i>
							<?php
						}
						echo '</span>';
					}
				} elseif ( 'image' === $item['pp_icon_type'] ) {
					$image_url = Group_Control_Image_Size::get_attachment_image_src( $item['list_image']['id'], 'image', $settings );

					if ( $image_url ) {
						$image_html = '<img src="' . esc_url( $image_url ) . '" alt="' . esc_attr( Control_Media::get_image_alt( $item['list_image'] ) ) . '">';
					} else {
						$image_html = '<img src="' . esc_url( $item['list_image']['url'] ) . '">';
					}
					?>
					<span class="pp-icon-list-image <?php echo esc_attr( $icon_animation ); ?>"><?php echo wp_kses_post( $image_html ); ?></span>
					<?php
				} elseif ( 'number' === $item['pp_icon_type'] ) {
					if ( $item['icon_text'] ) {
						$number = $item['icon_text'];
					} else {
						$number = $i;
					}
					?>
					<span class="pp-icon-list-icon <?php echo esc_attr( $icon_animation ); ?>"><?php echo esc_attr( $number ); ?></span>
					<?php
				}
				?>
			</span>
			<?php
		}
	}

	/**
	 * Render icon list widget output in the editor.
	 *
	 * Written as a Backbone JavaScript template and used to generate the live preview.
	 *
	 * @access protected
	 */
	protected function content_template() {
		?>
		<div class="pp-list-container">
			<#
				var iconsHTML = {},
					migrated = {},
					list_class = '';
			   
				if ( settings.view == 'inline' ) {
					list_class = 'pp-inline-items';
				}
			   
				view.addRenderAttribute( 'list_items', 'class', [ 'pp-list-items', list_class ] );
			#>
			<ul {{{ view.getRenderAttributeString( 'list_items' ) }}}>
				<# var i = 1; #>
				<# _.each( settings.list_items, function( item, index ) {

					var itemKey = view.getRepeaterSettingKey( 'item', 'list_items', index ),
						textKey = view.getRepeaterSettingKey( 'text', 'list_items', index );
				   
					view.addRenderAttribute( itemKey, {
						'class': 'pp-icon-list-item'
					});
					view.addRenderAttribute( textKey, {
						'class': 'pp-icon-list-text'
					});

					view.addInlineEditingAttributes( textKey );
					#>
					<# if ( item.text != '' ) { #>
						<li {{{ view.getRenderAttributeString( itemKey ) }}}>
							<# if ( item.link && item.link.url ) { #>
								<a href="{{ item.link.url }}">
							<# } #>
							<# if ( item.pp_icon_type != 'none' ) { #>
								<#
									if ( settings.icon_position == 'right' ) {
										var icon_class = 'pp-icon-right';
									} else {
										var icon_class = 'pp-icon-left';
									}
								#>
								<span class="pp-icon-wrapper {{ icon_class }}">
									<# if ( item.pp_icon_type == 'icon' ) { #>
										<# if ( item.list_icon || item.icon.value ) { #>
											<span class="pp-icon-list-icon pp-icon elementor-animation-{{ settings.icon_hover_animation }}" aria-hidden="true">
											<#
												iconsHTML[ index ] = elementor.helpers.renderIcon( view, item.icon, { 'aria-hidden': true }, 'i', 'object' );
												migrated[ index ] = elementor.helpers.isIconMigrated( item, 'icon' );
												if ( iconsHTML[ index ] && iconsHTML[ index ].rendered && ( ! item.list_icon || migrated[ index ] ) ) { #>
													{{{ iconsHTML[ index ].value }}}
												<# } else { #>
													<i class="{{ item.list_icon }}" aria-hidden="true"></i>
												<# }
											#>
											</span>
										<# } #>
									<# } else if ( item.pp_icon_type == 'image' ) { #>
										<span class="pp-icon-list-image elementor-animation-{{ settings.icon_hover_animation }}">
											<#
											var image = {
												id: item.list_image.id,
												url: item.list_image.url,
												size: settings.image_size,
												dimension: settings.image_custom_dimension,
												model: view.getEditModel()
											};
											var image_url = elementor.imagesManager.getImageUrl( image );
											#>
											<img src="{{{ image_url }}}" />
										</span>
									<# } else if ( item.pp_icon_type == 'number' ) { #>
										<#
											if ( item.icon_text ) {
												var number = item.icon_text;
											} else {
												var number = i;
											}
										#>
										<span class="pp-icon-list-icon elementor-animation-{{ settings.icon_hover_animation }}">
											{{ number }}
										</span>
									<# } #>
								</span>
							<# } #>

							<#
								var text = item.text;

								var text_html = '<span' + ' ' + view.getRenderAttributeString( textKey ) + ' >' + text + '</span>';

								print( text_html );
							#>

							<# if ( item.link && item.link.url ) { #>
								</a>
							<# } #>
						</li>
					<# } #>
				<# i++ } ); #>
			</ul>
		</div>
		<?php
	}
}
