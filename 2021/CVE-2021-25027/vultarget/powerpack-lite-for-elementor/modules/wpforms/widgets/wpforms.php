<?php
namespace PowerpackElementsLite\Modules\WPforms\Widgets;

use PowerpackElementsLite\Base\Powerpack_Widget;
use PowerpackElementsLite\Classes\PP_Config;
use PowerpackElementsLite\Classes\PP_Helper;

// Elementor Classes
use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Core\Schemes\Typography as Scheme_Typography;
use Elementor\Core\Schemes\Color as Scheme_Color;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * WPForms Widget
 */
class WPforms extends Powerpack_Widget {

	public function get_name() {
		return parent::get_widget_name( 'WP_Forms' );
	}

	public function get_title() {
		return parent::get_widget_title( 'WP_Forms' );
	}

	public function get_icon() {
		return parent::get_widget_icon( 'WP_Forms' );
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
		return parent::get_widget_keywords( 'WP_Forms' );
	}

	protected function _register_controls() { // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
		$this->register_controls();
	}

	/**
	 * Register wpforms widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 2.0.3
	 * @access protected
	 */
	protected function register_controls() {

		/*-----------------------------------------------------------------------------------*/
		/*	Content Tab
		/*-----------------------------------------------------------------------------------*/

		$this->start_controls_section(
			'section_wpforms',
			[
				'label'                 => __( 'WPForms', 'powerpack' ),
			]
		);

		$this->add_control(
			'contact_form_list',
			[
				'label'                 => esc_html__( 'Contact Form', 'powerpack' ),
				'type'                  => Controls_Manager::SELECT,
				'label_block'           => true,
				'options'               => PP_Helper::get_contact_forms( 'WP_Forms' ),
				'default'               => '0',
			]
		);

		$this->add_control(
			'custom_title_description',
			[
				'label'                 => __( 'Custom Title & Description', 'powerpack' ),
				'type'                  => Controls_Manager::SWITCHER,
				'label_on'              => __( 'Yes', 'powerpack' ),
				'label_off'             => __( 'No', 'powerpack' ),
				'return_value'          => 'yes',
			]
		);

		$this->add_control(
			'form_title',
			[
				'label'                 => __( 'Title', 'powerpack' ),
				'type'                  => Controls_Manager::SWITCHER,
				'default'               => 'yes',
				'label_on'              => __( 'Show', 'powerpack' ),
				'label_off'             => __( 'Hide', 'powerpack' ),
				'return_value'          => 'yes',
				'condition'             => [
					'custom_title_description!'   => 'yes',
				],
			]
		);

		$this->add_control(
			'form_description',
			[
				'label'                 => __( 'Description', 'powerpack' ),
				'type'                  => Controls_Manager::SWITCHER,
				'default'               => 'yes',
				'label_on'              => __( 'Show', 'powerpack' ),
				'label_off'             => __( 'Hide', 'powerpack' ),
				'return_value'          => 'yes',
				'condition'             => [
					'custom_title_description!'   => 'yes',
				],
			]
		);

		$this->add_control(
			'form_title_custom',
			[
				'label'                 => esc_html__( 'Title', 'powerpack' ),
				'type'                  => Controls_Manager::TEXT,
				'label_block'           => true,
				'default'               => '',
				'condition'             => [
					'custom_title_description'   => 'yes',
				],
			]
		);

		$this->add_control(
			'form_description_custom',
			[
				'label'                 => esc_html__( 'Description', 'powerpack' ),
				'type'                  => Controls_Manager::TEXTAREA,
				'default'               => '',
				'condition'             => [
					'custom_title_description'   => 'yes',
				],
			]
		);

		$this->add_control(
			'labels_switch',
			[
				'label'                 => __( 'Labels', 'powerpack' ),
				'type'                  => Controls_Manager::SWITCHER,
				'default'               => 'yes',
				'label_on'              => __( 'Show', 'powerpack' ),
				'label_off'             => __( 'Hide', 'powerpack' ),
				'return_value'          => 'yes',
				'prefix_class'          => 'pp-wpforms-labels-',
			]
		);

		$this->add_control(
			'placeholder_switch',
			[
				'label'                 => __( 'Placeholder', 'powerpack' ),
				'type'                  => Controls_Manager::SWITCHER,
				'default'               => 'yes',
				'label_on'              => __( 'Show', 'powerpack' ),
				'label_off'             => __( 'Hide', 'powerpack' ),
				'return_value'          => 'yes',
			]
		);

		$this->end_controls_section();

		/**
		 * Content Tab: Errors
		 * -------------------------------------------------
		 */
		$this->start_controls_section(
			'section_errors',
			[
				'label'                 => __( 'Errors', 'powerpack' ),
			]
		);

		$this->add_control(
			'error_messages',
			[
				'label'                 => __( 'Error Messages', 'powerpack' ),
				'type'                  => Controls_Manager::SELECT,
				'default'               => 'show',
				'options'               => [
					'show'          => __( 'Show', 'powerpack' ),
					'hide'          => __( 'Hide', 'powerpack' ),
				],
				'selectors_dictionary'  => [
					'show'          => 'block',
					'hide'          => 'none',
				],
				'selectors'             => [
					'{{WRAPPER}} .pp-wpforms label.wpforms-error' => 'display: {{VALUE}} !important;',
				],
			]
		);

		$this->end_controls_section();

		$help_docs = PP_Config::get_widget_help_links( 'WP_Forms' );

		if ( ! empty( $help_docs ) ) {

			/**
			 * Content Tab: Help Docs
			 *
			 * @since 1.4.15
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

		/*-----------------------------------------------------------------------------------*/
		/*	STYLE TAB
		/*-----------------------------------------------------------------------------------*/

		/**
		 * Style Tab: Form Title & Description
		 * -------------------------------------------------
		 */
		$this->start_controls_section(
			'section_form_title_style',
			[
				'label'                 => __( 'Title & Description', 'powerpack' ),
				'tab'                   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'heading_alignment',
			[
				'label'                 => __( 'Alignment', 'powerpack' ),
				'type'                  => Controls_Manager::CHOOSE,
				'options'               => [
					'left'      => [
						'title' => __( 'Left', 'powerpack' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center'    => [
						'title' => __( 'Center', 'powerpack' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'     => [
						'title' => __( 'Right', 'powerpack' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .wpforms-head-container, {{WRAPPER}} .pp-wpforms-heading' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'title_heading',
			[
				'label'                 => __( 'Title', 'powerpack' ),
				'type'                  => Controls_Manager::HEADING,
				'separator'             => 'before',
			]
		);

		$this->add_control(
			'form_title_text_color',
			[
				'label'                 => __( 'Text Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .pp-contact-form-title, {{WRAPPER}} .wpforms-title' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'                  => 'form_title_typography',
				'label'                 => __( 'Typography', 'powerpack' ),
				'selector'              => '{{WRAPPER}} .pp-contact-form-title, {{WRAPPER}} .wpforms-title',
			]
		);

		$this->add_responsive_control(
			'form_title_margin',
			[
				'label'                 => __( 'Margin', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', 'em', '%' ],
				'allowed_dimensions'    => 'vertical',
				'placeholder'           => [
					'top'      => '',
					'right'    => 'auto',
					'bottom'   => '',
					'left'     => 'auto',
				],
				'selectors'             => [
					'{{WRAPPER}} .pp-contact-form-title, {{WRAPPER}} .wpforms-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'description_heading',
			[
				'label'                 => __( 'Description', 'powerpack' ),
				'type'                  => Controls_Manager::HEADING,
				'separator'             => 'before',
			]
		);

		$this->add_control(
			'form_description_text_color',
			[
				'label'                 => __( 'Text Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .pp-contact-form-description, {{WRAPPER}} .wpforms-description' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'                  => 'form_description_typography',
				'label'                 => __( 'Typography', 'powerpack' ),
				'scheme'                => Scheme_Typography::TYPOGRAPHY_4,
				'selector'              => '{{WRAPPER}} .pp-contact-form-description, {{WRAPPER}} .wpforms-description',
			]
		);

		$this->add_responsive_control(
			'form_description_margin',
			[
				'label'                 => __( 'Margin', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', 'em', '%' ],
				'allowed_dimensions'    => 'vertical',
				'placeholder'           => [
					'top'      => '',
					'right'    => 'auto',
					'bottom'   => '',
					'left'     => 'auto',
				],
				'selectors'             => [
					'{{WRAPPER}} .pp-contact-form-description, {{WRAPPER}} .wpforms-description' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		/**
		 * Style Tab: Labels
		 * -------------------------------------------------
		 */
		$this->start_controls_section(
			'section_label_style',
			[
				'label'             => __( 'Labels', 'powerpack' ),
				'tab'               => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'text_color_label',
			[
				'label'             => __( 'Text Color', 'powerpack' ),
				'type'              => Controls_Manager::COLOR,
				'selectors'         => [
					'{{WRAPPER}} .pp-wpforms .wpforms-field label' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'              => 'typography_label',
				'label'             => __( 'Typography', 'powerpack' ),
				'scheme'            => Scheme_Typography::TYPOGRAPHY_4,
				'selector'          => '{{WRAPPER}} .pp-wpforms .wpforms-field label',
			]
		);

		$this->end_controls_section();

		/**
		 * Style Tab: Input & Textarea
		 * -------------------------------------------------
		 */
		$this->start_controls_section(
			'section_fields_style',
			[
				'label'             => __( 'Input & Textarea', 'powerpack' ),
				'tab'               => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'input_alignment',
			[
				'label'                 => __( 'Alignment', 'powerpack' ),
				'type'                  => Controls_Manager::CHOOSE,
				'options'               => [
					'left'      => [
						'title' => __( 'Left', 'powerpack' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center'    => [
						'title' => __( 'Center', 'powerpack' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'     => [
						'title' => __( 'Right', 'powerpack' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .pp-wpforms .wpforms-field input:not([type=radio]):not([type=checkbox]):not([type=submit]):not([type=button]):not([type=image]):not([type=file]), {{WRAPPER}} .pp-wpforms .wpforms-field textarea, {{WRAPPER}} .pp-wpforms .wpforms-field select' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->start_controls_tabs( 'tabs_fields_style' );

		$this->start_controls_tab(
			'tab_fields_normal',
			[
				'label'                 => __( 'Normal', 'powerpack' ),
			]
		);

		$this->add_control(
			'field_bg_color',
			[
				'label'             => __( 'Background Color', 'powerpack' ),
				'type'              => Controls_Manager::COLOR,
				'default'           => '',
				'selectors'         => [
					'{{WRAPPER}} .pp-wpforms .wpforms-field input:not([type=radio]):not([type=checkbox]):not([type=submit]):not([type=button]):not([type=image]):not([type=file]), {{WRAPPER}} .pp-wpforms .wpforms-field textarea, {{WRAPPER}} .pp-wpforms .wpforms-field select' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'field_text_color',
			[
				'label'             => __( 'Text Color', 'powerpack' ),
				'type'              => Controls_Manager::COLOR,
				'default'           => '',
				'selectors'         => [
					'{{WRAPPER}} .pp-wpforms .wpforms-field input:not([type=radio]):not([type=checkbox]):not([type=submit]):not([type=button]):not([type=image]):not([type=file]), {{WRAPPER}} .pp-wpforms .wpforms-field textarea, {{WRAPPER}} .pp-wpforms .wpforms-field select' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'              => 'field_border',
				'label'             => __( 'Border', 'powerpack' ),
				'placeholder'       => '1px',
				'default'           => '1px',
				'selector'          => '{{WRAPPER}} .pp-wpforms .wpforms-field input:not([type=radio]):not([type=checkbox]):not([type=submit]):not([type=button]):not([type=image]):not([type=file]), {{WRAPPER}} .pp-wpforms .wpforms-field textarea, {{WRAPPER}} .pp-wpforms .wpforms-field select',
				'separator'         => 'before',
			]
		);

		$this->add_control(
			'field_radius',
			[
				'label'             => __( 'Border Radius', 'powerpack' ),
				'type'              => Controls_Manager::DIMENSIONS,
				'size_units'        => [ 'px', 'em', '%' ],
				'selectors'         => [
					'{{WRAPPER}} .pp-wpforms .wpforms-field input:not([type=radio]):not([type=checkbox]):not([type=submit]):not([type=button]):not([type=image]):not([type=file]), {{WRAPPER}} .pp-wpforms .wpforms-field textarea, {{WRAPPER}} .pp-wpforms .wpforms-field select' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'text_indent',
			[
				'label'                 => __( 'Text Indent', 'powerpack' ),
				'type'                  => Controls_Manager::SLIDER,
				'range'                 => [
					'px'        => [
						'min'   => 0,
						'max'   => 60,
						'step'  => 1,
					],
					'%'         => [
						'min'   => 0,
						'max'   => 30,
						'step'  => 1,
					],
				],
				'size_units'            => [ 'px', 'em', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-wpforms .wpforms-field input:not([type=radio]):not([type=checkbox]):not([type=submit]):not([type=button]):not([type=image]):not([type=file]), {{WRAPPER}} .pp-wpforms .wpforms-field textarea, {{WRAPPER}} .pp-wpforms .wpforms-field select' => 'text-indent: {{SIZE}}{{UNIT}}',
				],
				'separator'         => 'before',
			]
		);

		$this->add_responsive_control(
			'input_width',
			[
				'label'             => __( 'Input Width', 'powerpack' ),
				'type'              => Controls_Manager::SLIDER,
				'range'             => [
					'px' => [
						'min'   => 0,
						'max'   => 1200,
						'step'  => 1,
					],
				],
				'size_units'        => [ 'px', 'em', '%' ],
				'selectors'         => [
					'{{WRAPPER}} .pp-wpforms .wpforms-field input:not([type=radio]):not([type=checkbox]):not([type=submit]):not([type=button]):not([type=image]):not([type=file]), {{WRAPPER}} .pp-wpforms .wpforms-field select' => 'width: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'input_height',
			[
				'label'             => __( 'Input Height', 'powerpack' ),
				'type'              => Controls_Manager::SLIDER,
				'range'             => [
					'px' => [
						'min'   => 0,
						'max'   => 80,
						'step'  => 1,
					],
				],
				'size_units'        => [ 'px', 'em', '%' ],
				'selectors'         => [
					'{{WRAPPER}} .pp-wpforms .wpforms-field input:not([type=radio]):not([type=checkbox]):not([type=submit]):not([type=button]):not([type=image]):not([type=file]), {{WRAPPER}} .pp-wpforms .wpforms-field select' => 'height: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'textarea_width',
			[
				'label'             => __( 'Textarea Width', 'powerpack' ),
				'type'              => Controls_Manager::SLIDER,
				'range'             => [
					'px' => [
						'min'   => 0,
						'max'   => 1200,
						'step'  => 1,
					],
				],
				'size_units'        => [ 'px', 'em', '%' ],
				'selectors'         => [
					'{{WRAPPER}} .pp-wpforms .wpforms-field textarea' => 'width: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'textarea_height',
			[
				'label'             => __( 'Textarea Height', 'powerpack' ),
				'type'              => Controls_Manager::SLIDER,
				'range'             => [
					'px' => [
						'min'   => 0,
						'max'   => 400,
						'step'  => 1,
					],
				],
				'size_units'        => [ 'px', 'em', '%' ],
				'selectors'         => [
					'{{WRAPPER}} .pp-wpforms .wpforms-field textarea' => 'height: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'field_padding',
			[
				'label'             => __( 'Padding', 'powerpack' ),
				'type'              => Controls_Manager::DIMENSIONS,
				'size_units'        => [ 'px', 'em', '%' ],
				'selectors'         => [
					'{{WRAPPER}} .pp-wpforms .wpforms-field input:not([type=radio]):not([type=checkbox]):not([type=submit]):not([type=button]):not([type=image]):not([type=file]), {{WRAPPER}} .pp-wpforms .wpforms-field textarea, {{WRAPPER}} .pp-wpforms .wpforms-field select' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator'         => 'before',
			]
		);

		$this->add_responsive_control(
			'field_spacing',
			[
				'label'                 => __( 'Spacing', 'powerpack' ),
				'type'                  => Controls_Manager::SLIDER,
				'range'                 => [
					'px'        => [
						'min'   => 0,
						'max'   => 100,
						'step'  => 1,
					],
				],
				'size_units'            => [ 'px', 'em', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-wpforms .wpforms-field' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'              => 'field_typography',
				'label'             => __( 'Typography', 'powerpack' ),
				'scheme'            => Scheme_Typography::TYPOGRAPHY_4,
				'selector'          => '{{WRAPPER}} .pp-wpforms .wpforms-field input:not([type=radio]):not([type=checkbox]):not([type=submit]):not([type=button]):not([type=image]):not([type=file]), {{WRAPPER}} .pp-wpforms .wpforms-field textarea, {{WRAPPER}} .pp-wpforms .wpforms-field select',
				'separator'         => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'              => 'field_box_shadow',
				'selector'          => '{{WRAPPER}} .pp-wpforms .wpforms-field input:not([type=radio]):not([type=checkbox]):not([type=submit]):not([type=button]):not([type=image]):not([type=file]), {{WRAPPER}} .pp-wpforms .wpforms-field textarea, {{WRAPPER}} .pp-wpforms .wpforms-field select',
				'separator'         => 'before',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_fields_focus',
			[
				'label'                 => __( 'Focus', 'powerpack' ),
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'              => 'focus_input_border',
				'label'             => __( 'Border', 'powerpack' ),
				'placeholder'       => '1px',
				'default'           => '1px',
				'selector'          => '{{WRAPPER}} .pp-wpforms .wpforms-field input:focus, {{WRAPPER}} .pp-wpforms .wpforms-field textarea:focus',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'              => 'focus_box_shadow',
				'selector'          => '{{WRAPPER}} .pp-wpforms .wpforms-field input:focus, {{WRAPPER}} .pp-wpforms .wpforms-field textarea:focus',
				'separator'         => 'before',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		/**
		 * Style Tab: Field Description
		 * -------------------------------------------------
		 */
		$this->start_controls_section(
			'section_field_description_style',
			[
				'label'                 => __( 'Field Description', 'powerpack' ),
				'tab'                   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'field_description_text_color',
			[
				'label'                 => __( 'Text Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'selectors'             => [
					'{{WRAPPER}} .pp-wpforms .wpforms-field .wpforms-field-description, {{WRAPPER}} .pp-wpforms .wpforms-field .wpforms-field-sublabel' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'                  => 'field_description_typography',
				'label'                 => __( 'Typography', 'powerpack' ),
				'selector'              => '{{WRAPPER}} .pp-wpforms .wpforms-field .wpforms-field-description, {{WRAPPER}} .pp-wpforms .wpforms-field .wpforms-field-sublabel',
			]
		);

		$this->add_responsive_control(
			'field_description_spacing',
			[
				'label'                 => __( 'Spacing', 'powerpack' ),
				'type'                  => Controls_Manager::SLIDER,
				'range'                 => [
					'px'        => [
						'min'   => 0,
						'max'   => 100,
						'step'  => 1,
					],
				],
				'size_units'            => [ 'px', 'em', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-wpforms .wpforms-field .wpforms-field-description, {{WRAPPER}} .pp-wpforms .wpforms-field .wpforms-field-sublabel' => 'padding-top: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->end_controls_section();

		/**
		 * Style Tab: Placeholder
		 * -------------------------------------------------
		 */
		$this->start_controls_section(
			'section_placeholder_style',
			[
				'label'             => __( 'Placeholder', 'powerpack' ),
				'tab'               => Controls_Manager::TAB_STYLE,
				'condition'             => [
					'placeholder_switch'   => 'yes',
				],
			]
		);

		$this->add_control(
			'text_color_placeholder',
			[
				'label'             => __( 'Text Color', 'powerpack' ),
				'type'              => Controls_Manager::COLOR,
				'selectors'         => [
					'{{WRAPPER}} .pp-wpforms .wpforms-field input::-webkit-input-placeholder, {{WRAPPER}} .pp-wpforms .wpforms-field textarea::-webkit-input-placeholder' => 'color: {{VALUE}}',
				],
				'condition'             => [
					'placeholder_switch'   => 'yes',
				],
			]
		);

		$this->end_controls_section();

		/**
		 * Style Tab: Radio & Checkbox
		 * -------------------------------------------------
		 */
		$this->start_controls_section(
			'section_radio_checkbox_style',
			[
				'label'                 => __( 'Radio & Checkbox', 'powerpack' ),
				'tab'                   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'custom_radio_checkbox',
			[
				'label'                 => __( 'Custom Styles', 'powerpack' ),
				'type'                  => Controls_Manager::SWITCHER,
				'label_on'              => __( 'Yes', 'powerpack' ),
				'label_off'             => __( 'No', 'powerpack' ),
				'return_value'          => 'yes',
			]
		);

		$this->add_responsive_control(
			'radio_checkbox_size',
			[
				'label'                 => __( 'Size', 'powerpack' ),
				'type'                  => Controls_Manager::SLIDER,
				'default'               => [
					'size'      => '15',
					'unit'      => 'px',
				],
				'range'                 => [
					'px'        => [
						'min'   => 0,
						'max'   => 80,
						'step'  => 1,
					],
				],
				'size_units'            => [ 'px', 'em', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-custom-radio-checkbox input[type="checkbox"], {{WRAPPER}} .pp-custom-radio-checkbox input[type="radio"]' => 'width: {{SIZE}}{{UNIT}} !important; height: {{SIZE}}{{UNIT}}',
				],
				'condition'             => [
					'custom_radio_checkbox' => 'yes',
				],
			]
		);

		$this->start_controls_tabs( 'tabs_radio_checkbox_style' );

		$this->start_controls_tab(
			'radio_checkbox_normal',
			[
				'label'                 => __( 'Normal', 'powerpack' ),
				'condition'             => [
					'custom_radio_checkbox' => 'yes',
				],
			]
		);

		$this->add_control(
			'radio_checkbox_color',
			[
				'label'                 => __( 'Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .pp-custom-radio-checkbox input[type="checkbox"], {{WRAPPER}} .pp-custom-radio-checkbox input[type="radio"]' => 'background: {{VALUE}}',
				],
				'condition'             => [
					'custom_radio_checkbox' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'radio_checkbox_border_width',
			[
				'label'                 => __( 'Border Width', 'powerpack' ),
				'type'                  => Controls_Manager::SLIDER,
				'range'                 => [
					'px'        => [
						'min'   => 0,
						'max'   => 15,
						'step'  => 1,
					],
				],
				'size_units'            => [ 'px' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-custom-radio-checkbox input[type="checkbox"], {{WRAPPER}} .pp-custom-radio-checkbox input[type="radio"]' => 'border-width: {{SIZE}}{{UNIT}}',
				],
				'condition'             => [
					'custom_radio_checkbox' => 'yes',
				],
			]
		);

		$this->add_control(
			'radio_checkbox_border_color',
			[
				'label'                 => __( 'Border Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .pp-custom-radio-checkbox input[type="checkbox"], {{WRAPPER}} .pp-custom-radio-checkbox input[type="radio"]' => 'border-color: {{VALUE}}',
				],
				'condition'             => [
					'custom_radio_checkbox' => 'yes',
				],
			]
		);

		$this->add_control(
			'checkbox_heading',
			[
				'label'                 => __( 'Checkbox', 'powerpack' ),
				'type'                  => Controls_Manager::HEADING,
				'condition'             => [
					'custom_radio_checkbox' => 'yes',
				],
			]
		);

		$this->add_control(
			'checkbox_border_radius',
			[
				'label'                 => __( 'Border Radius', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', 'em', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-custom-radio-checkbox input[type="checkbox"], {{WRAPPER}} .pp-custom-radio-checkbox input[type="checkbox"]:before' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'             => [
					'custom_radio_checkbox' => 'yes',
				],
			]
		);

		$this->add_control(
			'radio_heading',
			[
				'label'                 => __( 'Radio Buttons', 'powerpack' ),
				'type'                  => Controls_Manager::HEADING,
				'condition'             => [
					'custom_radio_checkbox' => 'yes',
				],
			]
		);

		$this->add_control(
			'radio_border_radius',
			[
				'label'                 => __( 'Border Radius', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', 'em', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-custom-radio-checkbox input[type="radio"], {{WRAPPER}} .pp-custom-radio-checkbox input[type="radio"]:before' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'             => [
					'custom_radio_checkbox' => 'yes',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'radio_checkbox_checked',
			[
				'label'                 => __( 'Checked', 'powerpack' ),
				'condition'             => [
					'custom_radio_checkbox' => 'yes',
				],
			]
		);

		$this->add_control(
			'radio_checkbox_color_checked',
			[
				'label'                 => __( 'Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .pp-custom-radio-checkbox input[type="checkbox"]:checked:before, {{WRAPPER}} .pp-custom-radio-checkbox input[type="radio"]:checked:before' => 'background: {{VALUE}}',
				],
				'condition'             => [
					'custom_radio_checkbox' => 'yes',
				],
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();

		/**
		 * Style Tab: Submit Button
		 * -------------------------------------------------
		 */
		$this->start_controls_section(
			'section_submit_button_style',
			[
				'label'             => __( 'Submit Button', 'powerpack' ),
				'tab'               => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'button_align',
			[
				'label'             => __( 'Alignment', 'powerpack' ),
				'type'              => Controls_Manager::CHOOSE,
				'options'           => [
					'left'        => [
						'title'   => __( 'Left', 'powerpack' ),
						'icon'    => 'eicon-h-align-left',
					],
					'center'      => [
						'title'   => __( 'Center', 'powerpack' ),
						'icon'    => 'eicon-h-align-center',
					],
					'right'       => [
						'title'   => __( 'Right', 'powerpack' ),
						'icon'    => 'eicon-h-align-right',
					],
				],
				'default'           => '',
				'selectors'         => [
					'{{WRAPPER}} .pp-wpforms .wpforms-submit-container'   => 'text-align: {{VALUE}};',
					'{{WRAPPER}} .pp-wpforms .wpforms-submit-container .wpforms-submit' => 'display:inline-block;',
				],
				'condition'             => [
					'button_width_type' => 'custom',
				],
			]
		);

		$this->add_control(
			'button_width_type',
			[
				'label'                 => __( 'Width', 'powerpack' ),
				'type'                  => Controls_Manager::SELECT,
				'default'               => 'custom',
				'options'               => [
					'full-width'    => __( 'Full Width', 'powerpack' ),
					'custom'        => __( 'Custom', 'powerpack' ),
				],
				'prefix_class'          => 'pp-wpforms-form-button-',
			]
		);

		$this->add_responsive_control(
			'button_width',
			[
				'label'                 => __( 'Width', 'powerpack' ),
				'type'                  => Controls_Manager::SLIDER,
				'default'               => [
					'size'      => '100',
					'unit'      => 'px',
				],
				'range'                 => [
					'px'        => [
						'min'   => 0,
						'max'   => 1200,
						'step'  => 1,
					],
				],
				'size_units'            => [ 'px', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-wpforms .wpforms-submit-container .wpforms-submit' => 'width: {{SIZE}}{{UNIT}}',
				],
				'condition'             => [
					'button_width_type' => 'custom',
				],
			]
		);

		$this->start_controls_tabs( 'tabs_button_style' );

		$this->start_controls_tab(
			'tab_button_normal',
			[
				'label'             => __( 'Normal', 'powerpack' ),
			]
		);

		$this->add_control(
			'button_bg_color_normal',
			[
				'label'             => __( 'Background Color', 'powerpack' ),
				'type'              => Controls_Manager::COLOR,
				'default'           => '',
				'selectors'         => [
					'{{WRAPPER}} .pp-wpforms .wpforms-submit-container .wpforms-submit' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'button_text_color_normal',
			[
				'label'             => __( 'Text Color', 'powerpack' ),
				'type'              => Controls_Manager::COLOR,
				'default'           => '',
				'selectors'         => [
					'{{WRAPPER}} .pp-wpforms .wpforms-submit-container .wpforms-submit' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'              => 'button_border_normal',
				'label'             => __( 'Border', 'powerpack' ),
				'placeholder'       => '1px',
				'default'           => '1px',
				'selector'          => '{{WRAPPER}} .pp-wpforms .wpforms-submit-container .wpforms-submit',
			]
		);

		$this->add_control(
			'button_border_radius',
			[
				'label'             => __( 'Border Radius', 'powerpack' ),
				'type'              => Controls_Manager::DIMENSIONS,
				'size_units'        => [ 'px', 'em', '%' ],
				'selectors'         => [
					'{{WRAPPER}} .pp-wpforms .wpforms-submit-container .wpforms-submit' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'button_padding',
			[
				'label'             => __( 'Padding', 'powerpack' ),
				'type'              => Controls_Manager::DIMENSIONS,
				'size_units'        => [ 'px', 'em', '%' ],
				'selectors'         => [
					'{{WRAPPER}} .pp-wpforms .wpforms-submit-container .wpforms-submit' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'button_margin',
			[
				'label'                 => __( 'Margin Top', 'powerpack' ),
				'type'                  => Controls_Manager::SLIDER,
				'range'                 => [
					'px'        => [
						'min'   => 0,
						'max'   => 100,
						'step'  => 1,
					],
				],
				'size_units'            => [ 'px', 'em', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-wpforms .wpforms-submit-container' => 'margin-top: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'              => 'button_typography',
				'label'             => __( 'Typography', 'powerpack' ),
				'scheme'            => Scheme_Typography::TYPOGRAPHY_4,
				'selector'          => '{{WRAPPER}} .pp-wpforms .wpforms-submit-container .wpforms-submit',
				'separator'         => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'              => 'button_box_shadow',
				'selector'          => '{{WRAPPER}} .pp-wpforms .wpforms-submit-container .wpforms-submit',
				'separator'         => 'before',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_button_hover',
			[
				'label'             => __( 'Hover', 'powerpack' ),
			]
		);

		$this->add_control(
			'button_bg_color_hover',
			[
				'label'             => __( 'Background Color', 'powerpack' ),
				'type'              => Controls_Manager::COLOR,
				'default'           => '',
				'selectors'         => [
					'{{WRAPPER}} .pp-wpforms .wpforms-submit-container .wpforms-submit:hover' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'button_text_color_hover',
			[
				'label'             => __( 'Text Color', 'powerpack' ),
				'type'              => Controls_Manager::COLOR,
				'default'           => '',
				'selectors'         => [
					'{{WRAPPER}} .pp-wpforms .wpforms-submit-container .wpforms-submit:hover' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'button_border_color_hover',
			[
				'label'             => __( 'Border Color', 'powerpack' ),
				'type'              => Controls_Manager::COLOR,
				'default'           => '',
				'selectors'         => [
					'{{WRAPPER}} .pp-wpforms .wpforms-submit-container .wpforms-submit:hover' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		/**
		 * Style Tab: Errors
		 * -------------------------------------------------
		 */
		$this->start_controls_section(
			'section_error_style',
			[
				'label'                 => __( 'Errors', 'powerpack' ),
				'tab'                   => Controls_Manager::TAB_STYLE,
				'condition'             => [
					'error_messages' => 'show',
				],
			]
		);

		$this->add_control(
			'error_message_text_color',
			[
				'label'                 => __( 'Text Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .pp-wpforms label.wpforms-error' => 'color: {{VALUE}}',
				],
				'condition'             => [
					'error_messages' => 'show',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'                  => 'error_message_typography',
				'label'                 => __( 'Typography', 'powerpack' ),
				'selector'              => '{{WRAPPER}} .pp-wpforms label.wpforms-error',
			]
		);

		$this->add_control(
			'error_field_input_border_color',
			[
				'label'                 => __( 'Error Field Input Border Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .pp-wpforms input.wpforms-error, {{WRAPPER}} .pp-wpforms textarea.wpforms-error' => 'border-color: {{VALUE}}',
				],
				'condition'             => [
					'error_messages' => 'show',
				],
			]
		);

		$this->add_control(
			'error_field_input_border_width',
			[
				'label'                 => __( 'Error Field Input Border Width', 'powerpack' ),
				'type'                  => Controls_Manager::NUMBER,
				'default'               => 1,
				'min'                   => 1,
				'max'                   => 10,
				'step'                  => 1,
				'selectors'             => [
					'{{WRAPPER}} .pp-wpforms input.wpforms-error, {{WRAPPER}} .pp-wpforms textarea.wpforms-error' => 'border-width: {{VALUE}}px',
				],
				'condition'             => [
					'error_messages' => 'show',
				],
			]
		);

		$this->end_controls_section();

		/**
		 * Style Tab: Confirmation Message
		 * -------------------------------------------------
		 */
		$this->start_controls_section(
			'section_confirmation_style',
			[
				'label'                 => __( 'Confirmation Message', 'powerpack' ),
				'tab'                   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'                  => 'confirmation_typography',
				'label'                 => __( 'Typography', 'powerpack' ),
				'selector'              => '{{WRAPPER}} .pp-wpforms .wpforms-confirmation-container-full',
			]
		);

		$this->add_control(
			'confirmation_text_color',
			[
				'label'             => __( 'Text Color', 'powerpack' ),
				'type'              => Controls_Manager::COLOR,
				'default'           => '',
				'selectors'         => [
					'{{WRAPPER}} .pp-wpforms .wpforms-confirmation-container-full' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'confirmation_bg_color',
			[
				'label'             => __( 'Background Color', 'powerpack' ),
				'type'              => Controls_Manager::COLOR,
				'default'           => '',
				'selectors'         => [
					'{{WRAPPER}} .pp-wpforms .wpforms-confirmation-container-full' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'              => 'confirmation_border',
				'label'             => __( 'Border', 'powerpack' ),
				'placeholder'       => '1px',
				'default'           => '1px',
				'selector'          => '{{WRAPPER}} .pp-wpforms .wpforms-confirmation-container-full',
			]
		);

		$this->add_control(
			'confirmation_border_radius',
			[
				'label'             => __( 'Border Radius', 'powerpack' ),
				'type'              => Controls_Manager::DIMENSIONS,
				'size_units'        => [ 'px', 'em', '%' ],
				'selectors'         => [
					'{{WRAPPER}} .pp-wpforms .wpforms-confirmation-container-full' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings();

		$this->add_render_attribute( 'contact-form', 'class', [
			'pp-contact-form',
			'pp-wpforms',
		] );

		if ( 'yes' !== $settings['placeholder_switch'] ) {
			$this->add_render_attribute( 'contact-form', 'class', 'placeholder-hide' );
		}

		if ( 'yes' === $settings['custom_title_description'] ) {
			$this->add_render_attribute( 'contact-form', 'class', 'title-description-hide' );
		}

		if ( 'yes' === $settings['custom_radio_checkbox'] ) {
			$this->add_render_attribute( 'contact-form', 'class', 'pp-custom-radio-checkbox' );
		}

		if ( function_exists( 'wpforms' ) ) {
			if ( ! empty( $settings['contact_form_list'] ) ) { ?>
				<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'contact-form' ) ); ?>>
					<?php if ( 'yes' === $settings['custom_title_description'] ) { ?>
						<div class="pp-wpforms-heading">
							<?php if ( $settings['form_title_custom'] ) { ?>
								<h3 class="pp-contact-form-title pp-wpforms-title">
									<?php echo esc_attr( $settings['form_title_custom'] ); ?>
								</h3>
							<?php } ?>
							<?php if ( $settings['form_description_custom'] ) { ?>
								<div class="pp-contact-form-description pp-wpforms-description">
									<?php echo $this->parse_text_editor( $settings['form_description_custom'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> 
								</div>
							<?php } ?>
						</div>
					<?php } ?>
					<?php
						$pp_form_title = $settings['form_title'];
						$pp_form_description = $settings['form_description'];

					if ( 'yes' === $settings['custom_title_description'] ) {
						$pp_form_title = false;
						$pp_form_description = false;
					}

						echo wpforms_display( $settings['contact_form_list'], $pp_form_title, $pp_form_description ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					?>
				</div>
				<?php
			} else {
				$placeholder = sprintf( 'Click here to edit the "%1$s" settings and choose a contact form from the dropdown list.', esc_attr( $this->get_title() ) );

				echo wp_kses_post( $this->render_editor_placeholder( [
					'title' => __( 'No Contact Form Selected!', 'powerpack' ),
					'body' => $placeholder,
				] ) );
			}
		}
	}
}
