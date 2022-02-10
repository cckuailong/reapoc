<?php
namespace PowerpackElementsLite\Modules\FluentForms\Widgets;

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
 * WP Fluent Forms Widget
 */
class Fluent_Forms extends Powerpack_Widget {

	public function get_name() {
		return parent::get_widget_name( 'Fluent_Forms' );
	}

	public function get_title() {
		return parent::get_widget_title( 'Fluent_Forms' );
	}

	public function get_icon() {
		return parent::get_widget_icon( 'Fluent_Forms' );
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
		return parent::get_widget_keywords( 'Fluent_Forms' );
	}

	protected function _register_controls() { // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
		$this->register_controls();
	}

	/**
	 * Register fluent forms widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 2.0.3
	 * @access protected
	 */
	protected function register_controls() {
		/* Content Tab */
		$this->register_content_forms_controls();
		$this->register_content_errors_controls();
		$this->register_content_help_docs_controls();

		/* Style Tab */
		$this->register_style_title_controls();
		$this->register_style_label_controls();
		$this->register_style_input_controls();
		$this->register_style_field_description_controls();
		$this->register_style_placeholder_controls();
		$this->register_style_checkbox_controls();
		$this->register_style_submit_button_controls();
		$this->register_style_errors_controls();
		$this->register_style_confirmation_controls();
	}

	/*-----------------------------------------------------------------------------------*/
	/*	Content Tab
	/*-----------------------------------------------------------------------------------*/

	protected function register_content_forms_controls() {
		$this->start_controls_section(
			'section_contact_forms',
			[
				'label'             => __( 'WP Fluent Forms', 'powerpack' ),
			]
		);

		$this->add_control(
			'contact_form_list',
			[
				'label'             => esc_html__( 'Contact Form', 'powerpack' ),
				'type'              => Controls_Manager::SELECT,
				'label_block'       => true,
				'options'           => PP_Helper::get_contact_forms( 'Fluent_Forms' ),
				'default'           => '0',
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
				'prefix_class'          => 'pp-fluent-forms-labels-',
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
	}

	/**
	 * Content Tab: Errors
	 * -------------------------------------------------
	 */
	protected function register_content_errors_controls() {
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
					'{{WRAPPER}} .pp-fluent-forms .ff-el-is-error .error' => 'display: {{VALUE}} !important;',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function register_content_help_docs_controls() {
		$help_docs = PP_Config::get_widget_help_links( 'Fluent_Forms' );
		if ( ! empty( $help_docs ) ) {
			/**
			 * Content Tab: Docs Links
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
	}

	/*-----------------------------------------------------------------------------------*/
	/*	STYLE TAB
	/*-----------------------------------------------------------------------------------*/

	/**
	 * Style Tab: Form Title & Description
	 * -------------------------------------------------
	 */
	protected function register_style_title_controls() {
		$this->start_controls_section(
			'section_form_title_style',
			[
				'label'                 => __( 'Title & Description', 'powerpack' ),
				'tab'                   => Controls_Manager::TAB_STYLE,
				'condition'             => [
					'custom_title_description'   => 'yes',
				],
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
					'{{WRAPPER}} .pp-fluent-forms-heading' => 'text-align: {{VALUE}};',
				],
				'condition'             => [
					'custom_title_description'   => 'yes',
				],
			]
		);

		$this->add_control(
			'title_heading',
			[
				'label'                 => __( 'Title', 'powerpack' ),
				'type'                  => Controls_Manager::HEADING,
				'separator'             => 'before',
				'condition'             => [
					'custom_title_description'   => 'yes',
				],
			]
		);

		$this->add_control(
			'form_title_text_color',
			[
				'label'                 => __( 'Text Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .pp-contact-form .pp-contact-form-title' => 'color: {{VALUE}}',
				],
				'condition'             => [
					'custom_title_description'   => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'                  => 'form_title_typography',
				'label'                 => __( 'Typography', 'powerpack' ),
				'selector'              => '{{WRAPPER}} .pp-contact-form .pp-contact-form-title',
				'condition'             => [
					'custom_title_description'   => 'yes',
				],
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
					'{{WRAPPER}} .pp-contact-form .pp-contact-form-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'             => [
					'custom_title_description'   => 'yes',
				],
			]
		);

		$this->add_control(
			'description_heading',
			[
				'label'                 => __( 'Description', 'powerpack' ),
				'type'                  => Controls_Manager::HEADING,
				'separator'             => 'before',
				'condition'             => [
					'custom_title_description'   => 'yes',
				],
			]
		);

		$this->add_control(
			'form_description_text_color',
			[
				'label'                 => __( 'Text Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .pp-contact-form .pp-contact-form-description' => 'color: {{VALUE}}',
				],
				'condition'             => [
					'custom_title_description'   => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'                  => 'form_description_typography',
				'label'                 => __( 'Typography', 'powerpack' ),
				'scheme'                => Scheme_Typography::TYPOGRAPHY_4,
				'selector'              => '{{WRAPPER}} .pp-contact-form .pp-contact-form-description',
				'condition'             => [
					'custom_title_description'   => 'yes',
				],
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
					'{{WRAPPER}} .pp-contact-form .pp-contact-form-description' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'             => [
					'custom_title_description'   => 'yes',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Style Tab: Labels
	 * -------------------------------------------------
	 */
	protected function register_style_label_controls() {
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
					'{{WRAPPER}} .pp-fluent-forms .ff-el-input--label label' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'              => 'typography_label',
				'label'             => __( 'Typography', 'powerpack' ),
				'scheme'            => Scheme_Typography::TYPOGRAPHY_4,
				'selector'          => '{{WRAPPER}} .pp-fluent-forms .ff-el-input--label label',
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Style Tab: Input & Textarea
	 * -------------------------------------------------
	 */
	protected function register_style_input_controls() {
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
					'{{WRAPPER}} .pp-fluent-forms .ff-el-group input:not([type=radio]):not([type=checkbox]):not([type=submit]):not([type=button]):not([type=image]):not([type=file]), {{WRAPPER}} .pp-fluent-forms .ff-el-group textarea, {{WRAPPER}} .pp-fluent-forms .ff-el-group select' => 'text-align: {{VALUE}};',
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
					'{{WRAPPER}} .pp-fluent-forms .ff-el-group input:not([type=radio]):not([type=checkbox]):not([type=submit]):not([type=button]):not([type=image]):not([type=file]), {{WRAPPER}} .pp-fluent-forms .ff-el-group textarea, {{WRAPPER}} .pp-fluent-forms .ff-el-group select' => 'background-color: {{VALUE}}',
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
					'{{WRAPPER}} .pp-fluent-forms .ff-el-group input:not([type=radio]):not([type=checkbox]):not([type=submit]):not([type=button]):not([type=image]):not([type=file]), {{WRAPPER}} .pp-fluent-forms .ff-el-group textarea, {{WRAPPER}} .pp-fluent-forms .ff-el-group select' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'              => 'field_typography',
				'label'             => __( 'Typography', 'powerpack' ),
				'scheme'            => Scheme_Typography::TYPOGRAPHY_4,
				'selector'          => '{{WRAPPER}} .pp-fluent-forms .ff-el-group input:not([type=radio]):not([type=checkbox]):not([type=submit]):not([type=button]):not([type=image]):not([type=file]), {{WRAPPER}} .pp-fluent-forms .ff-el-group textarea, {{WRAPPER}} .pp-fluent-forms .ff-el-group select',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'              => 'field_border',
				'label'             => __( 'Border', 'powerpack' ),
				'placeholder'       => '1px',
				'default'           => '1px',
				'selector'          => '{{WRAPPER}} .pp-fluent-forms .ff-el-group input:not([type=radio]):not([type=checkbox]):not([type=submit]):not([type=button]):not([type=image]):not([type=file]), {{WRAPPER}} .pp-fluent-forms .ff-el-group textarea, {{WRAPPER}} .pp-fluent-forms .ff-el-group select',
			]
		);

		$this->add_control(
			'field_radius',
			[
				'label'             => __( 'Border Radius', 'powerpack' ),
				'type'              => Controls_Manager::DIMENSIONS,
				'size_units'        => [ 'px', 'em', '%' ],
				'selectors'         => [
					'{{WRAPPER}} .pp-fluent-forms .ff-el-group input:not([type=radio]):not([type=checkbox]):not([type=submit]):not([type=button]):not([type=image]):not([type=file]), {{WRAPPER}} .pp-fluent-forms .ff-el-group textarea, {{WRAPPER}} .pp-fluent-forms .ff-el-group select' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'              => 'field_box_shadow',
				'selector'          => '{{WRAPPER}} .pp-fluent-forms .ff-el-group input:not([type=radio]):not([type=checkbox]):not([type=submit]):not([type=button]):not([type=image]):not([type=file]), {{WRAPPER}} .pp-fluent-forms .ff-el-group textarea, {{WRAPPER}} .pp-fluent-forms .ff-el-group select',
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
					'{{WRAPPER}} .pp-fluent-forms .ff-el-group input:not([type=radio]):not([type=checkbox]):not([type=submit]):not([type=button]):not([type=image]):not([type=file]), {{WRAPPER}} .pp-fluent-forms .ff-el-group textarea, {{WRAPPER}} .pp-fluent-forms .ff-el-group select' => 'text-indent: {{SIZE}}{{UNIT}}',
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
					'{{WRAPPER}} .pp-fluent-forms .ff-el-group input:not([type=radio]):not([type=checkbox]):not([type=submit]):not([type=button]):not([type=image]):not([type=file]), {{WRAPPER}} .pp-fluent-forms .ff-el-group select' => 'width: {{SIZE}}{{UNIT}}',
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
					'{{WRAPPER}} .pp-fluent-forms .ff-el-group input:not([type=radio]):not([type=checkbox]):not([type=submit]):not([type=button]):not([type=image]):not([type=file]), {{WRAPPER}} .pp-fluent-forms .ff-el-group select' => 'height: {{SIZE}}{{UNIT}}',
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
					'{{WRAPPER}} .pp-fluent-forms .ff-el-group textarea' => 'width: {{SIZE}}{{UNIT}}',
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
					'{{WRAPPER}} .pp-fluent-forms .ff-el-group textarea' => 'height: {{SIZE}}{{UNIT}}',
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
					'{{WRAPPER}} .pp-fluent-forms .ff-el-group input:not([type=radio]):not([type=checkbox]):not([type=submit]):not([type=button]):not([type=image]):not([type=file]), {{WRAPPER}} .pp-fluent-forms .ff-el-group textarea, {{WRAPPER}} .pp-fluent-forms .ff-el-group select' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .pp-fluent-forms .ff-el-group' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_fields_focus',
			[
				'label'                 => __( 'Focus', 'powerpack' ),
			]
		);

		$this->add_control(
			'focus_field_bg_color',
			[
				'label'             => __( 'Background Color', 'powerpack' ),
				'type'              => Controls_Manager::COLOR,
				'default'           => '',
				'selectors'         => [
					'{{WRAPPER}} .pp-fluent-forms .ff-el-group input:not([type=radio]):not([type=checkbox]):not([type=submit]):not([type=button]):not([type=image]):not([type=file]):focus, {{WRAPPER}} .pp-fluent-forms .ff-el-group textarea:focus, {{WRAPPER}} .pp-fluent-forms .ff-el-group select:focus' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'focus_field_text_color',
			[
				'label'             => __( 'Text Color', 'powerpack' ),
				'type'              => Controls_Manager::COLOR,
				'default'           => '',
				'selectors'         => [
					'{{WRAPPER}} .pp-fluent-forms .ff-el-group input:not([type=radio]):not([type=checkbox]):not([type=submit]):not([type=button]):not([type=image]):not([type=file]):focus, {{WRAPPER}} .pp-fluent-forms .ff-el-group textarea:focus, {{WRAPPER}} .pp-fluent-forms .ff-el-group select:focus' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'              => 'focus_input_border',
				'label'             => __( 'Border', 'powerpack' ),
				'placeholder'       => '1px',
				'default'           => '1px',
				'selector'          => '{{WRAPPER}} .pp-fluent-forms .ff-el-group input:not([type=radio]):not([type=checkbox]):not([type=submit]):not([type=button]):not([type=image]):not([type=file]):focus, {{WRAPPER}} .pp-fluent-forms .ff-el-group textarea:focus, {{WRAPPER}} .pp-fluent-forms .ff-el-group select:focus',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'              => 'focus_box_shadow',
				'selector'          => '{{WRAPPER}} .pp-fluent-forms .ff-el-group input:focus, {{WRAPPER}} .pp-fluent-forms .ff-el-group textarea:focus, {{WRAPPER}} .pp-fluent-forms .ff-el-group select:focus',
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Style Tab: Field Description
	 * -------------------------------------------------
	 */
	protected function register_style_field_description_controls() {
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
					'{{WRAPPER}} .pp-fluent-forms .ff-el-input--content .ff-el-help-message' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'                  => 'field_description_typography',
				'label'                 => __( 'Typography', 'powerpack' ),
				'selector'              => '{{WRAPPER}} .pp-fluent-forms .ff-el-input--content .ff-el-help-message',
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
					'{{WRAPPER}} .pp-fluent-forms .ff-el-input--content .ff-el-help-message' => 'padding-top: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Style Tab: Placeholder
	 * -------------------------------------------------
	 */
	protected function register_style_placeholder_controls() {
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
					'{{WRAPPER}} .pp-fluent-forms .ff-el-group input::-webkit-input-placeholder, {{WRAPPER}} .pp-fluent-forms .ff-el-group textarea::-webkit-input-placeholder' => 'color: {{VALUE}}',
				],
				'condition'             => [
					'placeholder_switch'   => 'yes',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Style Tab: Radio & Checkbox
	 * -------------------------------------------------
	 */
	protected function register_style_checkbox_controls() {
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

		$this->add_control(
			'radio_checkbox_color_label',
			[
				'label'             => __( 'Label Color', 'powerpack' ),
				'type'              => Controls_Manager::COLOR,
				'selectors'         => [
					'{{WRAPPER}} .pp-fluent-forms .ff-el-group .ff-el-form-check-label' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'              => 'radio_checkbox_typography_label',
				'label'             => __( 'Typography', 'powerpack' ),
				'scheme'            => Scheme_Typography::TYPOGRAPHY_4,
				'selector'          => '{{WRAPPER}} .pp-fluent-forms .ff-el-group .ff-el-form-check-label',
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

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'              => 'radio_checkbox_box_shadow',
				'selector'          => '{{WRAPPER}} .pp-custom-radio-checkbox input[type="checkbox"], {{WRAPPER}} .pp-custom-radio-checkbox input[type="radio"]',
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
				'separator'         => 'before',
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
				'separator'         => 'before',
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

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'              => 'radio_checkbox_box_shadow_checked',
				'selector'          => '{{WRAPPER}} .pp-custom-radio-checkbox input[type="checkbox"]:checked, {{WRAPPER}} .pp-custom-radio-checkbox input[type="radio"]:checked',
				'condition'             => [
					'custom_radio_checkbox' => 'yes',
				],
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Style Tab: Submit Button
	 * -------------------------------------------------
	 */
	protected function register_style_submit_button_controls() {
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
					'{{WRAPPER}} .pp-fluent-forms .frm-fluent-form > .ff-el-group:last-child'   => 'text-align: {{VALUE}};',
					'{{WRAPPER}} .pp-fluent-forms .ff-el-group .ff-btn-submit' => 'display:inline-block;',
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
				'prefix_class'          => 'pp-fluent-forms-form-button-',
			]
		);

		$this->add_responsive_control(
			'button_width',
			[
				'label'                 => __( 'Custom Width', 'powerpack' ),
				'type'                  => Controls_Manager::SLIDER,
				'default'               => [
					'size'      => '',
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
					'{{WRAPPER}} .pp-fluent-forms .ff-el-group .ff-btn-submit' => 'width: {{SIZE}}{{UNIT}}',
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
			'button_text_color_normal',
			[
				'label'             => __( 'Text Color', 'powerpack' ),
				'type'              => Controls_Manager::COLOR,
				'default'           => '',
				'selectors'         => [
					'{{WRAPPER}} .pp-fluent-forms .ff-el-group .ff-btn-submit' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'button_bg_color_normal',
			[
				'label'             => __( 'Background Color', 'powerpack' ),
				'type'              => Controls_Manager::COLOR,
				'default'           => '',
				'selectors'         => [
					'{{WRAPPER}} .pp-fluent-forms .ff-el-group .ff-btn-submit' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'              => 'button_typography',
				'label'             => __( 'Typography', 'powerpack' ),
				'scheme'            => Scheme_Typography::TYPOGRAPHY_4,
				'selector'          => '{{WRAPPER}} .pp-fluent-forms .ff-el-group .ff-btn-submit',
				'separator'         => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'              => 'button_border_normal',
				'label'             => __( 'Border', 'powerpack' ),
				'placeholder'       => '1px',
				'default'           => '1px',
				'selector'          => '{{WRAPPER}} .pp-fluent-forms .ff-el-group .ff-btn-submit',
			]
		);

		$this->add_control(
			'button_border_radius',
			[
				'label'             => __( 'Border Radius', 'powerpack' ),
				'type'              => Controls_Manager::DIMENSIONS,
				'size_units'        => [ 'px', 'em', '%' ],
				'selectors'         => [
					'{{WRAPPER}} .pp-fluent-forms .ff-el-group .ff-btn-submit' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .pp-fluent-forms .ff-el-group .ff-btn-submit' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .pp-fluent-forms .ff-el-group .ff-btn-submit' => 'margin-top: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'              => 'button_box_shadow',
				'selector'          => '{{WRAPPER}} .pp-fluent-forms .ff-el-group .ff-btn-submit',
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
			'button_text_color_hover',
			[
				'label'             => __( 'Text Color', 'powerpack' ),
				'type'              => Controls_Manager::COLOR,
				'default'           => '',
				'selectors'         => [
					'{{WRAPPER}} .pp-fluent-forms .ff-el-group .ff-btn-submit:hover' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'button_bg_color_hover',
			[
				'label'             => __( 'Background Color', 'powerpack' ),
				'type'              => Controls_Manager::COLOR,
				'default'           => '',
				'selectors'         => [
					'{{WRAPPER}} .pp-fluent-forms .ff-el-group .ff-btn-submit:hover' => 'background-color: {{VALUE}}',
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
					'{{WRAPPER}} .pp-fluent-forms .ff-el-group .ff-btn-submit:hover' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/**
	 * Style Tab: Errors
	 * -------------------------------------------------
	 */
	protected function register_style_errors_controls() {
		$this->start_controls_section(
			'section_error_style',
			[
				'label'                 => __( 'Errors', 'powerpack' ),
				'tab'                   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'error_message_text_color',
			[
				'label'                 => __( 'Text Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .pp-fluent-forms .ff-el-is-error .error' => 'color: {{VALUE}}',
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
				'selector'              => '{{WRAPPER}} .pp-fluent-forms .ff-el-is-error .error',
				'condition'             => [
					'error_messages' => 'show',
				],
			]
		);

		$this->add_control(
			'error_field_input_border_color',
			[
				'label'                 => __( 'Error Field Input Border Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .pp-fluent-forms .ff-el-is-error .ff-el-form-control' => 'border-color: {{VALUE}}',
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
					'{{WRAPPER}} .pp-fluent-forms .ff-el-is-error .ff-el-form-control' => 'border-width: {{VALUE}}px',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Style Tab: Confirmation Message
	 * -------------------------------------------------
	 */
	protected function register_style_confirmation_controls() {
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
				'selector'              => '{{WRAPPER}} .pp-fluent-forms .ff-message-success',
			]
		);

		$this->add_control(
			'confirmation_text_color',
			[
				'label'             => __( 'Text Color', 'powerpack' ),
				'type'              => Controls_Manager::COLOR,
				'default'           => '',
				'selectors'         => [
					'{{WRAPPER}} .pp-fluent-forms .ff-message-success' => 'color: {{VALUE}}',
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
					'{{WRAPPER}} .pp-fluent-forms .ff-message-success' => 'background-color: {{VALUE}}',
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
				'selector'          => '{{WRAPPER}} .pp-fluent-forms .ff-message-success',
			]
		);

		$this->add_control(
			'confirmation_border_radius',
			[
				'label'             => __( 'Border Radius', 'powerpack' ),
				'type'              => Controls_Manager::DIMENSIONS,
				'size_units'        => [ 'px', 'em', '%' ],
				'selectors'         => [
					'{{WRAPPER}} .pp-fluent-forms .ff-message-success' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'              => 'confirmation_box_shadow',
				'selector'          => '{{WRAPPER}} .pp-fluent-forms .ff-message-success',
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Render fluent forms widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.4.11.0
	 * @access protected
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		$this->add_render_attribute( 'contact-form', 'class', [
			'pp-contact-form',
			'pp-fluent-forms',
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

		if ( function_exists( 'wpFluentForm' ) ) {
			if ( ! empty( $settings['contact_form_list'] ) ) { ?>
				<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'contact-form' ) ); ?>>
					<?php if ( 'yes' === $settings['custom_title_description'] ) { ?>
						<div class="pp-fluent-forms-heading">
							<?php if ( $settings['form_title_custom'] ) { ?>
								<h3 class="pp-contact-form-title pp-fluent-forms-title">
									<?php echo esc_attr( $settings['form_title_custom'] ); ?>
								</h3>
							<?php } ?>
							<?php if ( $settings['form_description_custom'] ) { ?>
								<div class="pp-contact-form-description pp-fluent-forms-description">
									<?php echo $this->parse_text_editor( $settings['form_description_custom'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								</div>
							<?php } ?>
						</div>
					<?php } ?>
					<?php echo do_shortcode( '[fluentform id="' . $settings['contact_form_list'] . '" ]' ); ?>
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
