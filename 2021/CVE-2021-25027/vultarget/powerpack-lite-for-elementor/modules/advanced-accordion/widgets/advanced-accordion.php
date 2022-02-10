<?php
/**
 * PowerPack Advanced Accordion Widget
 *
 * @package PPE
 */

namespace PowerpackElementsLite\Modules\AdvancedAccordion\Widgets;

use PowerpackElementsLite\Base\Powerpack_Widget;
use PowerpackElementsLite\Classes\PP_Helper;
use PowerpackElementsLite\Classes\PP_Config;

// Elementor Classes.
use Elementor\Controls_Manager;
use Elementor\Control_Media;
use Elementor\Utils;
use Elementor\Icons_Manager;
use Elementor\Repeater;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Core\Schemes;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Advanced Accordion Widget
 */
class Advanced_Accordion extends Powerpack_Widget {

	/**
	 * Retrieve advanced accordion widget name.
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return parent::get_widget_name( 'Advanced_Accordion' );
	}

	/**
	 * Retrieve advanced accordion widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return parent::get_widget_title( 'Advanced_Accordion' );
	}

	/**
	 * Retrieve advanced accordion widget icon.
	 *
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return parent::get_widget_icon( 'Advanced_Accordion' );
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
		return parent::get_widget_keywords( 'Advanced_Accordion' );
	}

	/**
	 * Retrieve the list of scripts the advanced accordion widget depended on.
	 *
	 * Used to set scripts dependencies required to run the widget.
	 *
	 * @access public
	 *
	 * @return array Widget scripts dependencies.
	 */
	public function get_script_depends() {
		return array(
			'powerpack-frontend',
		);
	}

	/**
	 * Register advanced accordion widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @access protected
	 */
	protected function _register_controls() { // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
		$this->register_controls();
	}

	/**
	 * Register advanced accordion widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @access protected
	 */
	protected function register_controls() {
		/* Content Tab */
		$this->register_content_accordion_controls();
		$this->register_content_toggle_icon_controls();
		$this->register_content_settings_controls();
		$this->register_content_help_docs_controls();

		/* Style Tab */
		$this->register_style_items_controls();
		$this->register_style_title_controls();
		$this->register_style_content_controls();
		$this->register_style_toggle_icon_controls();
	}


	/*-----------------------------------------------------------------------------------*/
	/*	CONTENT TAB
	/*-----------------------------------------------------------------------------------*/

	/**
	 * Register accordion content controls
	 *
	 * @return void
	 */
	protected function register_content_accordion_controls() {

		$this->start_controls_section(
			'section_accordion_tabs',
			[
				'label'                 => esc_html__( 'Accordion', 'powerpack' ),
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'tab_title',
			[
				'label'                 => __( 'Title', 'powerpack' ),
				'type'                  => Controls_Manager::TEXT,
				'label_block'           => true,
				'default'               => __( 'Accordion Title', 'powerpack' ),
				'dynamic'               => [
					'active'   => true,
				],
			]
		);

		$repeater->add_control(
			'tab_title_icon',
			[
				'label'                 => __( 'Icon', 'powerpack' ),
				'type'                  => Controls_Manager::ICONS,
				'label_block'           => true,
				'fa4compatibility'      => 'accordion_tab_title_icon',
			]
		);

		$repeater->add_control(
			'content_type',
			[
				'label'                 => esc_html__( 'Content Type', 'powerpack' ),
				'type'                  => Controls_Manager::SELECT,
				'label_block'           => false,
				'options'               => [
					'content'   => __( 'Content', 'powerpack' ),
					'image'     => __( 'Image', 'powerpack' ),
					'section'   => __( 'Saved Section', 'powerpack' ),
					'widget'    => __( 'Saved Widget', 'powerpack' ),
					'template'  => __( 'Saved Page Template', 'powerpack' ),
				],
				'default'               => 'content',
			]
		);

		$repeater->add_control(
			'accordion_content',
			[
				'label'                 => esc_html__( 'Content', 'powerpack' ),
				'type'                  => Controls_Manager::WYSIWYG,
				'default'               => esc_html__( 'Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'powerpack' ),
				'dynamic'               => [ 'active' => true ],
				'condition'             => [
					'content_type'  => 'content',
				],
			]
		);

		$repeater->add_control(
			'image',
			[
				'label'                 => __( 'Image', 'powerpack' ),
				'type'                  => Controls_Manager::MEDIA,
				'dynamic'               => [
					'active'   => true,
				],
				'default'               => [
					'url' => Utils::get_placeholder_image_src(),
				],
				'conditions'            => [
					'terms' => [
						[
							'name'      => 'content_type',
							'operator'  => '==',
							'value'     => 'image',
						],
					],
				],
			]
		);

		$repeater->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'                  => 'image',
				'label'                 => __( 'Image Size', 'powerpack' ),
				'default'               => 'large',
				'exclude'               => [ 'custom' ],
				'conditions'            => [
					'terms' => [
						[
							'name'      => 'content_type',
							'operator'  => '==',
							'value'     => 'image',
						],
					],
				],
			]
		);

		$repeater->add_control(
			'saved_widget',
			[
				'label'                 => __( 'Choose Widget', 'powerpack' ),
				'type'                  => 'pp-query',
				'label_block'           => false,
				'multiple'              => false,
				'query_type'            => 'templates-widget',
				'conditions'        => [
					'terms' => [
						[
							'name'      => 'content_type',
							'operator'  => '==',
							'value'     => 'widget',
						],
					],
				],
			]
		);

		$repeater->add_control(
			'saved_section',
			[
				'label'                 => __( 'Choose Section', 'powerpack' ),
				'type'                  => 'pp-query',
				'label_block'           => false,
				'multiple'              => false,
				'query_type'            => 'templates-section',
				'conditions'        => [
					'terms' => [
						[
							'name'      => 'content_type',
							'operator'  => '==',
							'value'     => 'section',
						],
					],
				],
			]
		);

		$repeater->add_control(
			'templates',
			[
				'label'                 => __( 'Choose Template', 'powerpack' ),
				'type'                  => 'pp-query',
				'label_block'           => false,
				'multiple'              => false,
				'query_type'            => 'templates-page',
				'conditions'        => [
					'terms' => [
						[
							'name'      => 'content_type',
							'operator'  => '==',
							'value'     => 'template',
						],
					],
				],
			]
		);

		$repeater->add_control(
			'accordion_tab_default_active',
			[
				'label'                 => esc_html__( 'Active as Default', 'powerpack' ),
				'type'                  => Controls_Manager::SWITCHER,
				'default'               => 'no',
				'return_value'          => 'yes',
			]
		);

		$this->add_control(
			'tabs',
			[
				'type'                  => Controls_Manager::REPEATER,
				'default'               => [
					[ 'tab_title' => esc_html__( 'Accordion Tab Title 1', 'powerpack' ) ],
					[ 'tab_title' => esc_html__( 'Accordion Tab Title 2', 'powerpack' ) ],
					[ 'tab_title' => esc_html__( 'Accordion Tab Title 3', 'powerpack' ) ],
				],
				'fields'                => $repeater->get_controls(),
				'title_field'           => '{{tab_title}}',
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Register toggle icon content controls
	 *
	 * @return void
	 */
	protected function register_content_toggle_icon_controls() {

		$this->start_controls_section(
			'section_accordion_toggle_icon',
			[
				'label'                 => esc_html__( 'Toggle Icon', 'powerpack' ),
			]
		);

		$this->add_control(
			'toggle_icon_show',
			[
				'label'                 => esc_html__( 'Toggle Icon', 'powerpack' ),
				'type'                  => Controls_Manager::SWITCHER,
				'default'               => 'yes',
				'label_on'              => __( 'Show', 'powerpack' ),
				'label_off'             => __( 'Hide', 'powerpack' ),
				'return_value'          => 'yes',
			]
		);

		$this->add_control(
			'select_toggle_icon',
			[
				'label'                 => __( 'Normal Icon', 'powerpack' ),
				'type'                  => Controls_Manager::ICONS,
				'label_block'           => false,
				'skin'                  => 'inline',
				'fa4compatibility'      => 'toggle_icon_normal',
				'default'               => [
					'value'     => 'fas fa-plus',
					'library'   => 'fa-solid',
				],
				'recommended'           => [
					'fa-regular' => [
						'plus-square',
						'caret-square-up',
						'caret-square-down',
					],
					'fa-solid' => [
						'chevron-up',
						'chevron-down',
						'angle-up',
						'angle-down',
						'angle-right',
						'angle-dowble-up',
						'angle-dowble-down',
						'caret-up',
						'caret-down',
						'caret-square-up',
						'caret-square-down',
						'plus',
						'plus-circle',
						'plus-square',
						'minus',
					],
				],
				'condition'             => [
					'toggle_icon_show' => 'yes',
				],
			]
		);

		$this->add_control(
			'select_toggle_icon_active',
			[
				'label'                 => __( 'Active Icon', 'powerpack' ),
				'type'                  => Controls_Manager::ICONS,
				'label_block'           => false,
				'skin'                  => 'inline',
				'fa4compatibility'      => 'toggle_icon_active',
				'default'               => [
					'value'     => 'fas fa-minus',
					'library'   => 'fa-solid',
				],
				'recommended'           => [
					'fa-regular' => [
						'minus-square',
						'caret-square-up',
						'caret-square-down',
					],
					'fa-solid' => [
						'chevron-up',
						'chevron-down',
						'angle-up',
						'angle-down',
						'angle-dowble-up',
						'angle-dowble-down',
						'caret-up',
						'caret-down',
						'caret-square-up',
						'caret-square-down',
						'minus',
						'minus-circle',
						'minus-square',
					],
				],
				'condition'             => [
					'toggle_icon_show' => 'yes',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Register settings content controls
	 *
	 * @return void
	 */
	protected function register_content_settings_controls() {

		$this->start_controls_section(
			'section_accordion_settings',
			[
				'label'                 => esc_html__( 'Settings', 'powerpack' ),
			]
		);

		$this->add_control(
			'accordion_type',
			[
				'label'                 => esc_html__( 'Accordion Type', 'powerpack' ),
				'type'                  => Controls_Manager::SELECT,
				'default'               => 'accordion',
				'label_block'           => false,
				'options'               => [
					'accordion'     => esc_html__( 'Accordion', 'powerpack' ),
					'toggle'        => esc_html__( 'Toggle', 'powerpack' ),
				],
				'frontend_available'    => true,
			]
		);

		$this->add_control(
			'toggle_speed',
			[
				'label'                 => esc_html__( 'Toggle Speed (ms)', 'powerpack' ),
				'type'                  => Controls_Manager::NUMBER,
				'label_block'           => false,
				'default'               => 300,
				'frontend_available'    => true,
			]
		);

		$this->add_control(
			'title_html_tag',
			[
				'label'                 => __( 'Title HTML Tag', 'powerpack' ),
				'type'                  => Controls_Manager::SELECT,
				'options'               => [
					'h1' => 'H1',
					'h2' => 'H2',
					'h3' => 'H3',
					'h4' => 'H4',
					'h5' => 'H5',
					'h6' => 'H6',
					'div' => 'div',
				],
				'default'               => 'div',
			]
		);

		$this->end_controls_section();
	}

	protected function register_content_help_docs_controls() {

		$help_docs = PP_Config::get_widget_help_links( 'Advanced_Accordion' );
		if ( ! empty( $help_docs ) ) {
			/**
			 * Content Tab: Docs Links
			 *
			 * @since 2.4.1
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

	/**
	 * Register items style controls
	 *
	 * @return void
	 */
	protected function register_style_items_controls() {

		$this->start_controls_section(
			'section_accordion_items_style',
			[
				'label'                 => esc_html__( 'Items', 'powerpack' ),
				'tab'                   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'accordion_items_spacing',
			[
				'label'                 => __( 'Items Gaps', 'powerpack' ),
				'type'                  => Controls_Manager::SLIDER,
				'range'                 => [
					'px'    => [
						'min' => 0,
						'max' => 200,
					],
				],
				'default'               => [
					'size'  => 10,
				],
				'selectors'             => [
					'{{WRAPPER}} .pp-accordion-item' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs( 'accordion_tabs_style' );

		$this->start_controls_tab(
			'accordion_tab_normal',
			[
				'label'                 => __( 'Normal', 'powerpack' ),
			]
		);

		$this->add_control(
			'accordion_items_border_border',
			[
				'label'                 => esc_html__( 'Border Type', 'powerpack' ),
				'type'                  => Controls_Manager::SELECT,
				'default'               => 'solid',
				'options'               => [
					''          => __( 'None', 'powerpack' ),
					'solid'     => __( 'Solid', 'powerpack' ),
					'double'    => __( 'Double', 'powerpack' ),
					'dotted'    => __( 'Dotted', 'powerpack' ),
				],
				'selectors'             => [
					'{{WRAPPER}} .pp-accordion-item' => 'border-style: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'accordion_items_border_width',
			[
				'label'                 => esc_html__( 'Border Width', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px' ],
				'default'               => [
					'top'       => 1,
					'right'     => 1,
					'bottom'    => 1,
					'left'      => 1,
					'isLinked'  => true,
				],
				'selectors'             => [
					'{{WRAPPER}} .pp-accordion-item' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'             => [
					'accordion_items_border_border!' => '',
				],
			]
		);

		$this->add_control(
			'accordion_items_border_color',
			[
				'label'                 => esc_html__( 'Border Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'scheme'                => [
					'type' => Schemes\Color::get_type(),
					'value' => Schemes\Color::COLOR_3,
				],
				'default'               => '#d4d4d4',
				'selectors'             => [
					'{{WRAPPER}} .pp-accordion-item' => 'border-color: {{VALUE}};',
				],
				'condition'             => [
					'accordion_items_border_border!' => '',
				],
			]
		);

		$this->add_responsive_control(
			'accordion_items_border_radius',
			[
				'label'                 => esc_html__( 'Border Radius', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', 'em', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-accordion-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'                  => 'accordion_items_box_shadow',
				'selector'              => '{{WRAPPER}} .pp-accordion-item',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'accordion_tab_hover',
			[
				'label'                 => __( 'Hover', 'powerpack' ),
			]
		);

		$this->add_control(
			'accordion_items_border_color_hover',
			[
				'label'                 => esc_html__( 'Border Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .pp-accordion-item:hover' => 'border-color: {{VALUE}};',
				],
				'condition'             => [
					'accordion_items_border_border!' => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'                  => 'accordion_items_box_shadow_hover',
				'selector'              => '{{WRAPPER}} .pp-accordion-item:hover',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'accordion_tab_active',
			[
				'label'                 => __( 'Active', 'powerpack' ),
			]
		);

		$this->add_control(
			'accordion_items_border_color_active',
			[
				'label'                 => esc_html__( 'Border Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .pp-accordion-item.pp-accordion-item-active' => 'border-color: {{VALUE}};',
				],
				'condition'             => [
					'accordion_items_border_border!' => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'                  => 'accordion_items_box_shadow_active',
				'selector'              => '{{WRAPPER}} .pp-accordion-item.pp-accordion-item-active',
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_responsive_control(
			'accordion_items_padding',
			[
				'label'                 => esc_html__( 'Padding', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', 'em', '%' ],
				'separator'             => 'before',
				'selectors'             => [
					'{{WRAPPER}} .pp-accordion-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Register title style controls
	 *
	 * @return void
	 */
	protected function register_style_title_controls() {

		$this->start_controls_section(
			'section_title_style',
			[
				'label'                 => esc_html__( 'Title', 'powerpack' ),
				'tab'                   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'accordion_title_bottom_spacing',
			[
				'label'                 => __( 'Bottom Spacing', 'powerpack' ),
				'type'                  => Controls_Manager::SLIDER,
				'range'                 => [
					'px'    => [
						'min' => 0,
						'max' => 200,
					],
				],
				'selectors'             => [
					'{{WRAPPER}} .pp-advanced-accordion .pp-accordion-tab-title' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs( 'accordion_title_tabs_style' );

		$this->start_controls_tab(
			'accordion_title_tab_normal',
			[
				'label'                 => __( 'Normal', 'powerpack' ),
			]
		);

		$this->add_control(
			'tab_title_text_color',
			[
				'label'                 => esc_html__( 'Text Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'scheme'                => [
					'type' => Schemes\Color::get_type(),
					'value' => Schemes\Color::COLOR_1,
				],
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .pp-advanced-accordion .pp-accordion-tab-title' => 'color: {{VALUE}};',
					'{{WRAPPER}} .pp-advanced-accordion .pp-accordion-tab-title svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'tab_title_bg_color',
			[
				'label'                 => esc_html__( 'Background Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors' => [
					'{{WRAPPER}} .pp-advanced-accordion .pp-accordion-tab-title' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'                  => 'tab_title_typography',
				'selector'              => '{{WRAPPER}} .pp-advanced-accordion .pp-accordion-tab-title',
				'scheme'                => Schemes\Typography::TYPOGRAPHY_1,
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'                  => 'tab_title_border',
				'label'                 => esc_html__( 'Border', 'powerpack' ),
				'selector'              => '{{WRAPPER}} .pp-advanced-accordion .pp-accordion-tab-title',
			]
		);

		$this->add_responsive_control(
			'tab_title_padding',
			[
				'label'                 => esc_html__( 'Padding', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', 'em', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-advanced-accordion .pp-accordion-tab-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'accordion_title_tab_hover',
			[
				'label'                 => __( 'Hover', 'powerpack' ),
			]
		);

		$this->add_control(
			'tab_title_text_color_hover',
			[
				'label'                 => esc_html__( 'Text Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .pp-advanced-accordion .pp-accordion-tab-title:hover' => 'color: {{VALUE}};',
					'{{WRAPPER}} .pp-advanced-accordion .pp-accordion-tab-title:hover svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'tab_title_bg_color_hover',
			[
				'label'                 => esc_html__( 'Background Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors' => [
					'{{WRAPPER}} .pp-advanced-accordion .pp-accordion-tab-title:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'tab_title_border_color_hover',
			[
				'label'                 => esc_html__( 'Border Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .pp-advanced-accordion .pp-accordion-tab-title:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'accordion_title_tab_active',
			[
				'label'                 => __( 'Active', 'powerpack' ),
			]
		);

		$this->add_control(
			'tab_title_text_color_active',
			[
				'label'                 => esc_html__( 'Text Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'scheme'                => [
					'type' => Schemes\Color::get_type(),
					'value' => Schemes\Color::COLOR_4,
				],
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .pp-advanced-accordion .pp-accordion-tab-title.pp-accordion-tab-active' => 'color: {{VALUE}};',
					'{{WRAPPER}} .pp-advanced-accordion .pp-accordion-tab-title.pp-accordion-tab-active svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'tab_title_bg_color_active',
			[
				'label'                 => esc_html__( 'Background Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors' => [
					'{{WRAPPER}} .pp-advanced-accordion .pp-accordion-tab-title.pp-accordion-tab-active' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'tab_title_border_color_active',
			[
				'label'                 => esc_html__( 'Border Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .pp-advanced-accordion .pp-accordion-tab-title.pp-accordion-tab-active' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'tab_icon_heading',
			[
				'label'                 => __( 'Icon', 'powerpack' ),
				'type'                  => Controls_Manager::HEADING,
				'separator'             => 'before',
			]
		);

		$this->add_responsive_control(
			'tab_icon_size',
			[
				'label'                 => __( 'Icon Size', 'powerpack' ),
				'type'                  => Controls_Manager::SLIDER,
				'default'               => [
					'size'  => 16,
					'unit'  => 'px',
				],
				'size_units'            => [ 'px' ],
				'range'                 => [
					'px'        => [
						'min'   => 0,
						'max'   => 100,
						'step'  => 1,
					],
				],
				'selectors'             => [
					'{{WRAPPER}} .pp-advanced-accordion .pp-accordion-tab-title .pp-accordion-tab-icon' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'tab_icon_spacing',
			[
				'label'                 => __( 'Icon Spacing', 'powerpack' ),
				'type'                  => Controls_Manager::SLIDER,
				'default'               => [
					'size'  => 10,
					'unit'  => 'px',
				],
				'size_units'            => [ 'px' ],
				'range'                 => [
					'px'    => [
						'min'   => 0,
						'max'   => 100,
						'step'  => 1,
					],
				],
				'selectors'             => [
					'{{WRAPPER}} .pp-advanced-accordion .pp-accordion-tab-title .pp-accordion-tab-icon' => 'margin-right: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Register content style controls
	 *
	 * @return void
	 */
	protected function register_style_content_controls() {

		$this->start_controls_section(
			'section_content_style',
			[
				'label'                 => esc_html__( 'Content', 'powerpack' ),
				'tab'                   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'tab_content_bg_color',
			[
				'label'                 => esc_html__( 'Background Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors' => [
					'{{WRAPPER}} .pp-advanced-accordion .pp-accordion-item .pp-accordion-tab-content' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'tab_content_text_color',
			[
				'label'                 => esc_html__( 'Text Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '#333',
				'selectors'             => [
					'{{WRAPPER}} .pp-advanced-accordion .pp-accordion-item .pp-accordion-tab-content' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'                  => 'tab_content_typography',
				'selector'              => '{{WRAPPER}} .pp-advanced-accordion .pp-accordion-item .pp-accordion-tab-content',
				'scheme'                => Schemes\Typography::TYPOGRAPHY_3,
			]
		);

		$this->add_responsive_control(
			'tab_content_padding',
			[
				'label'                 => esc_html__( 'Padding', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', 'em', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-advanced-accordion .pp-accordion-item .pp-accordion-tab-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Register toggle icon style controls
	 *
	 * @return void
	 */
	protected function register_style_toggle_icon_controls() {

		$this->start_controls_section(
			'section_toggle_icon_style',
			[
				'label'                 => esc_html__( 'Toggle Icon', 'powerpack' ),
				'tab'                   => Controls_Manager::TAB_STYLE,
				'condition' => [
					'toggle_icon_show' => 'yes',
				],
			]
		);

		$this->add_control(
			'toggle_icon_align',
			[
				'label'   => __( 'Alignment', 'powerpack' ),
				'type'    => Controls_Manager::CHOOSE,
				'options' => [
					'left'  => [
						'title' => __( 'Start', 'powerpack' ),
						'icon' => 'eicon-h-align-left',
					],
					'right' => [
						'title' => __( 'End', 'powerpack' ),
						'icon' => 'eicon-h-align-right',
					],
				],
				'default' => is_rtl() ? 'left' : 'right',
				'toggle'  => false,
			]
		);

		$this->add_responsive_control(
			'toggle_icon_spacing',
			[
				'label'     => __( 'Spacing', 'powerpack' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .pp-toggle-icon-align-left .pp-accordion-toggle-icon' => 'margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .pp-toggle-icon-align-right .pp-accordion-toggle-icon' => 'margin-left: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'toggle_icon_size',
			[
				'label'      => __( 'Size', 'powerpack' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => [
					'size'  => 16,
					'unit'  => 'px',
				],
				'size_units' => [ 'px' ],
				'range'      => [
					'px'    => [
						'min'   => 0,
						'max'   => 100,
						'step'  => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .pp-advanced-accordion .pp-accordion-tab-title .pp-accordion-toggle-icon' => 'font-size: {{SIZE}}{{UNIT}};',
				],
				'condition'  => [
					'toggle_icon_show' => 'yes',
				],
			]
		);

		$this->start_controls_tabs( 'accordion_toggle_icon_tabs_style' );

		$this->start_controls_tab(
			'accordion_toggle_icon_tab_normal',
			[
				'label'                 => __( 'Normal', 'powerpack' ),
			]
		);

		$this->add_control(
			'toggle_icon_color',
			[
				'label'                 => esc_html__( 'Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'scheme'                => [
					'type' => Schemes\Color::get_type(),
					'value' => Schemes\Color::COLOR_1,
				],
				'default'               => '',
				'selectors' => [
					'{{WRAPPER}} .pp-advanced-accordion .pp-accordion-tab-title .pp-accordion-toggle-icon' => 'color: {{VALUE}};',
					'{{WRAPPER}} .pp-advanced-accordion .pp-accordion-tab-title .pp-accordion-toggle-icon svg' => 'fill: {{VALUE}};',
				],
				'condition' => [
					'toggle_icon_show' => 'yes',
				],
			]
		);

		$this->add_control(
			'toggle_icon_background_color',
			[
				'label'                 => esc_html__( 'Background Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors' => [
					'{{WRAPPER}} .pp-advanced-accordion .pp-accordion-tab-title .pp-accordion-toggle-icon' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'toggle_icon_show' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'                  => 'toggle_icon_border',
				'label'                 => esc_html__( 'Border', 'powerpack' ),
				'selector'              => '{{WRAPPER}} .pp-advanced-accordion .pp-accordion-tab-title .pp-accordion-toggle-icon',
			]
		);

		$this->add_responsive_control(
			'toggle_icon_border_radius',
			[
				'label'                 => esc_html__( 'Border Radius', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', 'em', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-advanced-accordion .pp-accordion-tab-title .pp-accordion-toggle-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'toggle_icon_padding',
			[
				'label'                 => esc_html__( 'Padding', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', 'em', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-advanced-accordion .pp-accordion-tab-title .pp-accordion-toggle-icon' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'accordion_toggle_icon_tab_hover',
			[
				'label'                 => __( 'Hover', 'powerpack' ),
			]
		);

		$this->add_control(
			'toggle_icon_hover_color',
			[
				'label'                 => esc_html__( 'Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors' => [
					'.pp-accordion-toggle-icon, {{WRAPPER}} .pp-advanced-accordion .pp-accordion-item:hover .pp-accordion-tab-title .pp-accordion-toggle-icon' => 'color: {{VALUE}};',
					'{{WRAPPER}} .pp-advanced-accordion .pp-accordion-item:hover .pp-accordion-tab-title .pp-accordion-toggle-icon svg' => 'fill: {{VALUE}};',
				],
				'condition'             => [
					'toggle_icon_show' => 'yes',
				],
			]
		);

		$this->add_control(
			'toggle_icon_hover_background_color',
			[
				'label'                 => esc_html__( 'Background Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors' => [
					'{{WRAPPER}} .pp-advanced-accordion .pp-accordion-item:hover .pp-accordion-tab-title .pp-accordion-toggle-icon' => 'background-color: {{VALUE}};',
				],
				'condition'             => [
					'toggle_icon_show' => 'yes',
				],
			]
		);

		$this->add_control(
			'toggle_icon_hover_border_color',
			[
				'label'                 => esc_html__( 'Border Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors' => [
					'{{WRAPPER}} .pp-advanced-accordion .pp-accordion-item:hover .pp-accordion-tab-title .pp-accordion-toggle-icon' => 'border-color: {{VALUE}};',
				],
				'condition'             => [
					'toggle_icon_show' => 'yes',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'accordion_toggle_icon_tab_active',
			[
				'label'                 => __( 'Active', 'powerpack' ),
			]
		);

		$this->add_control(
			'toggle_icon_active_color',
			[
				'label'                 => esc_html__( 'Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'scheme'                => [
					'type' => Schemes\Color::get_type(),
					'value' => Schemes\Color::COLOR_4,
				],
				'default'               => '',
				'selectors' => [
					'{{WRAPPER}} .pp-advanced-accordion .pp-accordion-tab-title.pp-accordion-tab-active .pp-accordion-toggle-icon' => 'color: {{VALUE}};',
					'{{WRAPPER}} .pp-advanced-accordion .pp-accordion-tab-title.pp-accordion-tab-active .pp-accordion-toggle-icon svg' => 'fill: {{VALUE}};',
				],
				'condition'             => [
					'toggle_icon_show' => 'yes',
				],
			]
		);

		$this->add_control(
			'toggle_icon_active_background_color',
			[
				'label'                 => esc_html__( 'Background Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors' => [
					'{{WRAPPER}} .pp-advanced-accordion .pp-accordion-tab-title.pp-accordion-tab-active .pp-accordion-toggle-icon' => 'background-color: {{VALUE}};',
				],
				'condition'             => [
					'toggle_icon_show' => 'yes',
				],
			]
		);

		$this->add_control(
			'toggle_icon_active_border_color',
			[
				'label'                 => esc_html__( 'Border Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors' => [
					'{{WRAPPER}} .pp-advanced-accordion .pp-accordion-tab-title.pp-accordion-tab-active .pp-accordion-toggle-icon' => 'border-color: {{VALUE}};',
				],
				'condition'             => [
					'toggle_icon_show' => 'yes',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Render accordion content.
	 *
	 * @since 2.3.2
	 */
	protected function get_accordion_content( $tab ) {
		$settings     = $this->get_settings_for_display();
		$content_type = $tab['content_type'];
		$output       = '';

		switch ( $content_type ) {
			case 'content':
				$output = do_shortcode( $tab['accordion_content'] );
				break;

			case 'image':
				$image_url = Group_Control_Image_Size::get_attachment_image_src( $tab['image']['id'], 'image', $tab );

				if ( ! $image_url ) {
					$image_url = $tab['image']['url'];
				}

				$image_html = '<div class="pp-showcase-preview-image">';

				$image_html .= '<img src="' . esc_url( $image_url ) . '" alt="' . esc_attr( Control_Media::get_image_alt( $tab['image'] ) ) . '">';

				$image_html .= '</div>';

				$output = $image_html;
				break;

			case 'section':
				$output = \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $tab['saved_section'] );
				break;

			case 'template':
				$output = \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $tab['templates'] );
				break;

			case 'widget':
				$output = \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $tab['saved_widget'] );
				break;

			default:
				return;
		}

		return $output;
	}

	protected function render() {
		$settings   = $this->get_settings_for_display();
		$id_int     = substr( $this->get_id_int(), 0, 3 );

		$this->add_render_attribute( 'accordion', [
			'class'                 => [ 'pp-advanced-accordion', 'pp-toggle-icon-align-' . $settings['toggle_icon_align'] ],
			'id'                    => 'pp-advanced-accordion-' . esc_attr( $this->get_id() ),
			'data-accordion-id'     => esc_attr( $this->get_id() ),
		] );
		?>
		<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'accordion' ) ); ?>>
			<?php
			foreach ( $settings['tabs'] as $index => $tab ) :

				$tab_count = $index + 1;
				$tab_title_setting_key = $this->get_repeater_setting_key( 'tab_title', 'tabs', $index );
				$tab_content_setting_key = $this->get_repeater_setting_key( 'accordion_content', 'tabs', $index );

				$tab_title_class    = [ 'pp-accordion-tab-title' ];
				$tab_content_class  = [ 'pp-accordion-tab-content' ];

				if ( 'yes' === $tab['accordion_tab_default_active'] ) {
					$tab_title_class[]      = 'pp-accordion-tab-active-default';
					$tab_content_class[]    = 'pp-accordion-tab-active-default';
				}

				$this->add_render_attribute( $tab_title_setting_key, [
					'id'                => 'pp-accordion-tab-title-' . $id_int . $tab_count,
					'class'             => $tab_title_class,
					'tabindex'          => $id_int . $tab_count,
					'data-tab'          => $tab_count,
					'role'              => 'tab',
					'aria-controls'     => 'pp-accordion-tab-content-' . $id_int . $tab_count,
				]);

				$this->add_render_attribute( $tab_content_setting_key, [
					'id'                => 'pp-accordion-tab-content-' . $id_int . $tab_count,
					'class'             => $tab_content_class,
					'data-tab'          => $tab_count,
					'role'              => 'tabpanel',
					'aria-labelledby'   => 'pp-accordion-tab-title-' . $id_int . $tab_count,
				] );

				if ( 'content' === $tab['content_type'] ) {
					$this->add_inline_editing_attributes( $tab_content_setting_key, 'advanced' );
				}

				$migration_allowed = Icons_Manager::is_migration_allowed();

				// Title Icon - add old default
				if ( ! isset( $tab['accordion_tab_title_icon'] ) && ! $migration_allowed ) {
					$tab['accordion_tab_title_icon'] = isset( $fallback_defaults[ $index ] ) ? $fallback_defaults[ $index ] : '';
				}

				$migrated_title_icon = isset( $tab['__fa4_migrated']['tab_title_icon'] );
				$is_new_title_icon = ! isset( $tab['accordion_tab_title_icon'] ) && $migration_allowed;

				// Toggle Icon Normal
				if ( ! isset( $settings['toggle_icon_normal'] ) && ! $migration_allowed ) {
					// add old default
					$settings['toggle_icon_normal'] = 'fa fa-plus';
				}

				$has_toggle_icon = ! empty( $settings['toggle_icon_normal'] );

				if ( $has_toggle_icon ) {
					$this->add_render_attribute( 'toggle-icon', 'class', $settings['toggle_icon_normal'] );
					$this->add_render_attribute( 'toggle-icon', 'aria-hidden', 'true' );
				}

				if ( ! $has_toggle_icon && ! empty( $settings['select_toggle_icon']['value'] ) ) {
					$has_toggle_icon = true;
				}
				$migrated_normal = isset( $settings['__fa4_migrated']['select_toggle_icon'] );
				$is_new_normal = ! isset( $settings['toggle_icon_normal'] ) && $migration_allowed;

				// Toggle Icon Active
				if ( ! isset( $settings['toggle_icon_active'] ) && ! $migration_allowed ) {
					// add old default
					$settings['toggle_icon_active'] = 'fa fa-minus';
				}

				$has_toggle_active_icon = ! empty( $settings['toggle_icon_active'] );

				if ( $has_toggle_active_icon ) {
					$this->add_render_attribute( 'toggle-icon', 'class', $settings['toggle_icon_active'] );
					$this->add_render_attribute( 'toggle-icon', 'aria-hidden', 'true' );
				}

				if ( ! $has_toggle_active_icon && ! empty( $settings['select_toggle_icon_active']['value'] ) ) {
					$has_toggle_active_icon = true;
				}
				$migrated = isset( $settings['__fa4_migrated']['select_toggle_icon_active'] );
				$is_new = ! isset( $settings['toggle_icon_active'] ) && $migration_allowed;
				?>
				<div class="pp-accordion-item">
					<?php $title_tag = PP_Helper::validate_html_tag( $settings['title_html_tag'] ); ?>
					<<?php echo esc_html( $title_tag ); ?> <?php echo wp_kses_post( $this->get_render_attribute_string( $tab_title_setting_key ) ); ?>>
						<span class="pp-accordion-title-icon">
							<?php if ( ! empty( $tab['accordion_tab_title_icon'] ) || ( ! empty( $tab['tab_title_icon']['value'] ) && $is_new_title_icon ) ) { ?>
								<span class="pp-accordion-tab-icon pp-icon">
									<?php
									if ( $is_new_title_icon || $migrated_title_icon ) {
										Icons_Manager::render_icon( $tab['tab_title_icon'], [ 'aria-hidden' => 'true' ] );
									} else { ?>
										<i class="<?php echo esc_attr( $tab['accordion_tab_title_icon'] ); ?>" aria-hidden="true"></i>
									<?php } ?>
								</span>
							<?php } ?>
							<span class="pp-accordion-title-text">
								<?php echo wp_kses_post( $tab['tab_title'] ); ?>
							</span>
						</span>
						<?php if ( 'yes' === $settings['toggle_icon_show'] ) { ?>
							<div class="pp-accordion-toggle-icon">
								<?php if ( $has_toggle_icon ) { ?>
									<span class='pp-accordion-toggle-icon-close pp-icon'>
										<?php
										if ( $is_new_normal || $migrated_normal ) {
											Icons_Manager::render_icon( $settings['select_toggle_icon'], [ 'aria-hidden' => 'true' ] );
										} elseif ( ! empty( $settings['toggle_icon_normal'] ) ) {
											?><i <?php echo wp_kses_post( $this->get_render_attribute_string( 'toggle-icon' ) ); ?>></i><?php
										}
										?>
									</span>
								<?php } ?>
								<?php if ( $has_toggle_active_icon ) { ?>
									<span class='pp-accordion-toggle-icon-open pp-icon'>
										<?php
										if ( $is_new_normal || $migrated_normal ) {
											Icons_Manager::render_icon( $settings['select_toggle_icon_active'], [ 'aria-hidden' => 'true' ] );
										} elseif ( ! empty( $settings['toggle_icon_active'] ) ) {
											?><i <?php echo wp_kses_post( $this->get_render_attribute_string( 'toggle-icon' ) ); ?>></i><?php
										}
										?>
									</span>
								<?php } ?>
							</div>
						<?php } ?>
					</<?php echo esc_html( $title_tag ); ?>>

					<div <?php echo wp_kses_post( $this->get_render_attribute_string( $tab_content_setting_key ) ); ?>>
						<?php echo $this->get_accordion_content( $tab ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
		<?php
	}
}
