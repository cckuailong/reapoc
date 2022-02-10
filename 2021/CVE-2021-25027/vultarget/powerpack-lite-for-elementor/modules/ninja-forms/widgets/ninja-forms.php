<?php
namespace PowerpackElementsLite\Modules\NinjaForms\Widgets;

use PowerpackElementsLite\Base\Powerpack_Widget;
use PowerpackElementsLite\Classes\PP_Helper;

// Elementor Classes
use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Core\Schemes\Typography as Scheme_Typography;
use Elementor\Core\Schemes\Color as Scheme_Color;
use Elementor\Modules\DynamicTags\Module as TagsModule;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Ninja Forms Widget
 */
class Ninja_Forms extends Powerpack_Widget {

	public function get_name() {
		return parent::get_widget_name( 'Ninja_Forms' );
	}

	public function get_title() {
		return parent::get_widget_title( 'Ninja_Forms' );
	}

	public function get_icon() {
		return parent::get_widget_icon( 'Ninja_Forms' );
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
		return parent::get_widget_keywords( 'Ninja_Forms' );
	}

	protected function _register_controls() { // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
		$this->register_controls();
	}

	/**
	 * Register ninja forms widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 2.3.2
	 * @access protected
	 */
	protected function register_controls() {
		/* Content Tab */
		$this->register_content_form_controls();
		$this->register_content_errors_controls();
		$this->register_content_upgrade_pro_controls();

		/* Style Tab */
		$this->register_style_title_controls();
		$this->register_style_label_controls();
		$this->register_style_input_controls();
		$this->register_style_field_description_controls();
		$this->register_style_placeholder_controls();
		$this->register_style_checkbox_controls();
		$this->register_style_submit_button_controls();
		$this->register_style_success_message_controls();
		$this->register_style_required_notice_controls();
		$this->register_style_errors_controls();
	}

	/*-----------------------------------------------------------------------------------*/
	/*	CONTENT TAB
	/*-----------------------------------------------------------------------------------*/

	/**
	 * Content Tab: Ninja Forms
	 * -------------------------------------------------
	 */
	/**
	 * Content Tab: Ninja Forms
	 * -------------------------------------------------
	 */
	protected function register_content_form_controls() {
		$this->start_controls_section(
			'section_form',
			[
				'label'                 => __( 'Ninja Forms', 'powerpack' ),
			]
		);

		$this->add_control(
			'contact_form_list',
			[
				'label'                 => esc_html__( 'Contact Form', 'powerpack' ),
				'type'                  => Controls_Manager::SELECT,
				'label_block'           => true,
				'options'               => PP_Helper::get_contact_forms( 'Ninja_Forms' ),
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
				'prefix_class'          => 'pp-ninja-form-title-',
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
				'prefix_class'          => 'pp-ninja-form-labels-',
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
					'{{WRAPPER}} .pp-ninja-form .nf-error-wrap .nf-error-required-error' => 'display: {{VALUE}} !important;',
				],
			]
		);

		$this->add_control(
			'validation_errors',
			[
				'label'                 => __( 'Validation Errors', 'powerpack' ),
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
					'{{WRAPPER}} .pp-ninja-form .nf-form-errors .nf-error-field-errors' => 'display: {{VALUE}} !important;',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Content Tab: Errors
	 * -------------------------------------------------
	 */
	protected function register_content_upgrade_pro_controls() {

		if ( ! is_pp_elements_active() ) {
			/**
			 * Content Tab: Upgrade PowerPack
			 *
			 * @since 1.2.9.4
			 */
			$this->start_controls_section(
				'section_upgrade_powerpack',
				array(
					'label' => apply_filters( 'upgrade_powerpack_title', __( 'Get PowerPack Pro', 'powerpack' ) ),
					'tab'   => Controls_Manager::TAB_CONTENT,
				)
			);

			$this->add_control(
				'upgrade_powerpack_notice',
				array(
					'label'           => '',
					'type'            => Controls_Manager::RAW_HTML,
					'raw'             => apply_filters( 'upgrade_powerpack_message', sprintf( __( 'Upgrade to %1$s Pro Version %2$s for 70+ widgets, exciting extensions and advanced features.', 'powerpack' ), '<a href="#" target="_blank" rel="noopener">', '</a>' ) ),
					'content_classes' => 'upgrade-powerpack-notice elementor-panel-alert elementor-panel-alert-info',
				)
			);

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
					'{{WRAPPER}} .pp-ninja-form .nf-form-title h3, {{WRAPPER}} .pp-ninja-form-heading' => 'text-align: {{VALUE}};',
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
					'{{WRAPPER}} .pp-ninja-form .nf-form-title h3, {{WRAPPER}} .pp-contact-form-title' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'                  => 'form_title_typography',
				'label'                 => __( 'Typography', 'powerpack' ),
				'selector'              => '{{WRAPPER}} .pp-ninja-form .nf-form-title h3, {{WRAPPER}} .pp-contact-form-title',
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
					'{{WRAPPER}} .pp-ninja-form .nf-form-title h3, {{WRAPPER}} .pp-contact-form-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .pp-ninja-form .pp-contact-form-description' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'                  => 'form_description_typography',
				'label'                 => __( 'Typography', 'powerpack' ),
				'scheme'                => Scheme_Typography::TYPOGRAPHY_4,
				'selector'              => '{{WRAPPER}} .pp-ninja-form .pp-contact-form-description',
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
					'{{WRAPPER}} .pp-contact-form-description' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
				'label'                 => __( 'Labels', 'powerpack' ),
				'tab'                   => Controls_Manager::TAB_STYLE,
				'condition'             => [
					'labels_switch' => 'yes',
				],
			]
		);

		$this->add_control(
			'text_color_label',
			[
				'label'                 => __( 'Text Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'selectors'             => [
					'{{WRAPPER}} .pp-ninja-form .nf-field-label label' => 'color: {{VALUE}}',
				],
				'condition'             => [
					'labels_switch' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'                  => 'typography_label',
				'label'                 => __( 'Typography', 'powerpack' ),
				'selector'              => '{{WRAPPER}} .pp-ninja-form .nf-field-label label',
				'condition'             => [
					'labels_switch' => 'yes',
				],
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
				'label'                 => __( 'Input & Textarea', 'powerpack' ),
				'tab'                   => Controls_Manager::TAB_STYLE,
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
					'{{WRAPPER}} .pp-ninja-form .nf-field input[type="text"], {{WRAPPER}} .pp-ninja-form .nf-field input[type="email"], {{WRAPPER}} .pp-ninja-form .nf-field input[type="tel"], {{WRAPPER}} .pp-ninja-form .nf-field textarea, {{WRAPPER}} .pp-ninja-form .nf-field select' => 'text-align: {{VALUE}};',
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
				'label'                 => __( 'Background Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .pp-ninja-form .nf-field input[type="text"], {{WRAPPER}} .pp-ninja-form .nf-field input[type="email"], {{WRAPPER}} .pp-ninja-form .nf-field input[type="tel"], {{WRAPPER}} .pp-ninja-form .nf-field textarea, {{WRAPPER}} .pp-ninja-form .nf-field select' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'field_text_color',
			[
				'label'                 => __( 'Text Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .pp-ninja-form .nf-field input[type="text"], {{WRAPPER}} .pp-ninja-form .nf-field input[type="email"], {{WRAPPER}} .pp-ninja-form .nf-field input[type="tel"], {{WRAPPER}} .pp-ninja-form .nf-field textarea, {{WRAPPER}} .pp-ninja-form .nf-field select' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'                  => 'field_border',
				'label'                 => __( 'Border', 'powerpack' ),
				'placeholder'           => '1px',
				'default'               => '1px',
				'selector'              => '{{WRAPPER}} .pp-ninja-form .nf-field input[type="text"], {{WRAPPER}} .pp-ninja-form .nf-field input[type="email"], {{WRAPPER}} .pp-ninja-form .nf-field input[type="tel"], {{WRAPPER}} .pp-ninja-form .nf-field textarea, {{WRAPPER}} .pp-ninja-form .nf-field select',
				'separator'             => 'before',
			]
		);

		$this->add_control(
			'field_radius',
			[
				'label'                 => __( 'Border Radius', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', 'em', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-ninja-form .nf-field input[type="text"], {{WRAPPER}} .pp-ninja-form .nf-field input[type="email"], {{WRAPPER}} .pp-ninja-form .nf-field input[type="tel"], {{WRAPPER}} .pp-ninja-form .nf-field textarea, {{WRAPPER}} .pp-ninja-form .nf-field select' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .pp-ninja-form .nf-field input[type="text"], {{WRAPPER}} .pp-ninja-form .nf-field input[type="email"], {{WRAPPER}} .pp-ninja-form .nf-field input[type="tel"], {{WRAPPER}} .pp-ninja-form .nf-field textarea, {{WRAPPER}} .pp-ninja-form .nf-field select' => 'text-indent: {{SIZE}}{{UNIT}}',
				],
				'separator'             => 'before',
			]
		);

		$this->add_responsive_control(
			'input_width',
			[
				'label'                 => __( 'Input Width', 'powerpack' ),
				'type'                  => Controls_Manager::SLIDER,
				'range'                 => [
					'px' => [
						'min'   => 0,
						'max'   => 1200,
						'step'  => 1,
					],
				],
				'size_units'            => [ 'px', 'em', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-ninja-form .nf-field input[type="text"], {{WRAPPER}} .pp-ninja-form .nf-field input[type="email"], {{WRAPPER}} .pp-ninja-form .nf-field input[type="tel"], {{WRAPPER}} .pp-ninja-form .nf-field select' => 'width: {{SIZE}}{{UNIT}}',
				],
				'separator'             => 'before',
			]
		);

		$this->add_responsive_control(
			'input_height',
			[
				'label'                 => __( 'Input Height', 'powerpack' ),
				'type'                  => Controls_Manager::SLIDER,
				'range'                 => [
					'px' => [
						'min'   => 0,
						'max'   => 80,
						'step'  => 1,
					],
				],
				'size_units'            => [ 'px', 'em', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-ninja-form .nf-field input[type="text"], {{WRAPPER}} .pp-ninja-form .nf-field input[type="email"], {{WRAPPER}} .pp-ninja-form .nf-field input[type="tel"], {{WRAPPER}} .pp-ninja-form .nf-field select' => 'height: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'textarea_width',
			[
				'label'                 => __( 'Textarea Width', 'powerpack' ),
				'type'                  => Controls_Manager::SLIDER,
				'range'                 => [
					'px' => [
						'min'   => 0,
						'max'   => 1200,
						'step'  => 1,
					],
				],
				'size_units'            => [ 'px', 'em', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-ninja-form .nf-field textarea' => 'width: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'textarea_height',
			[
				'label'                 => __( 'Textarea Height', 'powerpack' ),
				'type'                  => Controls_Manager::SLIDER,
				'range'                 => [
					'px' => [
						'min'   => 0,
						'max'   => 400,
						'step'  => 1,
					],
				],
				'size_units'            => [ 'px', 'em', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-ninja-form .nf-field textarea' => 'height: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'field_padding',
			[
				'label'                 => __( 'Padding', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', 'em', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-ninja-form .nf-field input[type="text"], {{WRAPPER}} .pp-ninja-form .nf-field input[type="email"], {{WRAPPER}} .pp-ninja-form .nf-field input[type="tel"], {{WRAPPER}} .pp-ninja-form .nf-field textarea, {{WRAPPER}} .pp-ninja-form .nf-field select' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator'             => 'before',
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
					'{{WRAPPER}} .pp-ninja-form .nf-field-container' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'                  => 'field_typography',
				'label'                 => __( 'Typography', 'powerpack' ),
				'selector'              => '{{WRAPPER}} .pp-ninja-form .nf-field input[type="text"], {{WRAPPER}} .pp-ninja-form .nf-field input[type="email"], {{WRAPPER}} .pp-ninja-form .nf-field input[type="tel"], {{WRAPPER}} .pp-ninja-form .nf-field textarea, {{WRAPPER}} .pp-ninja-form .nf-field select',
				'separator'             => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'                  => 'field_box_shadow',
				'selector'              => '{{WRAPPER}} .pp-ninja-form .nf-field input[type="text"], {{WRAPPER}} .pp-ninja-form .nf-field input[type="email"], {{WRAPPER}} .pp-ninja-form .nf-field input[type="tel"], {{WRAPPER}} .pp-ninja-form .nf-field textarea, {{WRAPPER}} .pp-ninja-form .nf-field select',
				'separator'             => 'before',
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
			'field_bg_color_focus',
			[
				'label'                 => __( 'Background Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .pp-ninja-form .nf-field input:focus, {{WRAPPER}} .pp-ninja-form .nf-field textarea:focus' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'                  => 'focus_input_border',
				'label'                 => __( 'Border', 'powerpack' ),
				'placeholder'       => '1px',
				'default'               => '1px',
				'selector'              => '{{WRAPPER}} .pp-ninja-form .nf-field input:focus, {{WRAPPER}} .pp-ninja-form .nf-field textarea:focus',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'                  => 'focus_box_shadow',
				'selector'              => '{{WRAPPER}} .pp-ninja-form .nf-field input:focus, {{WRAPPER}} .pp-ninja-form .nf-field textarea:focus',
				'separator'             => 'before',
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
					'{{WRAPPER}} .pp-ninja-form .nf-field .nf-field-description' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'                  => 'field_description_typography',
				'label'                 => __( 'Typography', 'powerpack' ),
				'selector'              => '{{WRAPPER}} .pp-ninja-form .nf-field .nf-field-description',
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
					'{{WRAPPER}} .pp-ninja-form .nf-field .nf-field-description' => 'margin-bottom: {{SIZE}}{{UNIT}}',
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
				'label'                 => __( 'Placeholder', 'powerpack' ),
				'tab'                   => Controls_Manager::TAB_STYLE,
				'condition'             => [
					'placeholder_switch'   => 'yes',
				],
			]
		);

		$this->add_control(
			'text_color_placeholder',
			[
				'label'                 => __( 'Text Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'selectors'             => [
					'{{WRAPPER}} .pp-ninja-form .nf-field input::-webkit-input-placeholder, {{WRAPPER}} .pp-ninja-form .nf-field textarea::-webkit-input-placeholder' => 'color: {{VALUE}}',
					'{{WRAPPER}} .pp-ninja-form .nf-field input::-moz-input-placeholder, {{WRAPPER}} .pp-ninja-form .nf-field textarea::-moz-input-placeholder' => 'color: {{VALUE}}',
					'{{WRAPPER}} .pp-ninja-form .nf-field input:-ms-input-placeholder, {{WRAPPER}} .pp-ninja-form .nf-field textarea:-ms-input-placeholder' => 'color: {{VALUE}}',
					'{{WRAPPER}} .pp-ninja-form .nf-field input:-moz-placeholder, {{WRAPPER}} .pp-ninja-form .nf-field textarea:-moz-placeholder' => 'color: {{VALUE}}',
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
	}

	/**
	 * Style Tab: Submit Button
	 * -------------------------------------------------
	 */
	protected function register_style_submit_button_controls() {
		$this->start_controls_section(
			'section_submit_button_style',
			[
				'label'                 => __( 'Submit Button', 'powerpack' ),
				'tab'                   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'button_align',
			[
				'label'                 => __( 'Alignment', 'powerpack' ),
				'type'                  => Controls_Manager::CHOOSE,
				'options'               => [
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
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .pp-ninja-form .submit-container'   => 'text-align: {{VALUE}};',
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
				'prefix_class'          => 'pp-ninja-form-button-',
			]
		);

		$this->add_responsive_control(
			'button_width',
			[
				'label'                 => __( 'Width', 'powerpack' ),
				'type'                  => Controls_Manager::SLIDER,
				'default'               => [
					'size'      => '130',
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
					'{{WRAPPER}} .pp-ninja-form .submit-container input[type="button"]' => 'width: {{SIZE}}{{UNIT}}',
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
				'label'                 => __( 'Normal', 'powerpack' ),
			]
		);

		$this->add_control(
			'button_bg_color_normal',
			[
				'label'                 => __( 'Background Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .pp-ninja-form .submit-container input[type="button"]' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'button_text_color_normal',
			[
				'label'                 => __( 'Text Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .pp-ninja-form .submit-container input[type="button"]' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'                  => 'button_border_normal',
				'label'                 => __( 'Border', 'powerpack' ),
				'placeholder'       => '1px',
				'default'               => '1px',
				'selector'              => '{{WRAPPER}} .pp-ninja-form .submit-container input[type="button"]',
			]
		);

		$this->add_control(
			'button_border_radius',
			[
				'label'                 => __( 'Border Radius', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', 'em', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-ninja-form .submit-container input[type="button"]' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'button_padding',
			[
				'label'                 => __( 'Padding', 'powerpack' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', 'em', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .pp-ninja-form .submit-container input[type="button"]' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .pp-ninja-form .submit-container input[type="button"]' => 'margin-top: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_button_hover',
			[
				'label'                 => __( 'Hover', 'powerpack' ),
			]
		);

		$this->add_control(
			'button_bg_color_hover',
			[
				'label'                 => __( 'Background Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .pp-ninja-form .submit-container input[type="button"]:hover' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'button_text_color_hover',
			[
				'label'                 => __( 'Text Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .pp-ninja-form .submit-container input[type="button"]:hover' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'button_border_color_hover',
			[
				'label'                 => __( 'Border Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .pp-ninja-form .submit-container input[type="button"]:hover' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'                  => 'button_typography',
				'label'                 => __( 'Typography', 'powerpack' ),
				'scheme'            => Scheme_Typography::TYPOGRAPHY_4,
				'selector'              => '{{WRAPPER}} .pp-ninja-form .submit-container input[type="button"]',
				'separator'             => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'                  => 'button_box_shadow',
				'selector'              => '{{WRAPPER}} .pp-ninja-form .submit-container input[type="button"]',
				'separator'             => 'before',
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Style Tab: Success Message
	 * -------------------------------------------------
	 */
	protected function register_style_success_message_controls() {
		$this->start_controls_section(
			'section_success_message_style',
			[
				'label'                 => __( 'Success Message', 'powerpack' ),
				'tab'                   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'success_message_text_color',
			[
				'label'                 => __( 'Text Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'selectors'             => [
					'{{WRAPPER}} .pp-ninja-form .nf-response-msg' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'                  => 'success_message_typography',
				'label'                 => __( 'Typography', 'powerpack' ),
				'selector'              => '{{WRAPPER}} .pp-ninja-form .nf-response-msg',
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Style Tab: Required Fields Notice
	 * -------------------------------------------------
	 */
	protected function register_style_required_notice_controls() {
		$this->start_controls_section(
			'section_required_notice_style',
			[
				'label'                 => __( 'Required Fields Notice', 'powerpack' ),
				'tab'                   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'required_notice_text_color',
			[
				'label'                 => __( 'Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .pp-ninja-form .nf-form-fields-required' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'required_notice_spacing',
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
					'{{WRAPPER}} .pp-ninja-form .nf-form-fields-required' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'                  => 'required_notice_typography',
				'label'                 => __( 'Typography', 'powerpack' ),
				'scheme'            => Scheme_Typography::TYPOGRAPHY_4,
				'selector'              => '{{WRAPPER}} .pp-ninja-form .nf-form-fields-required',
			]
		);

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
			'error_messages_heading',
			[
				'label'                 => __( 'Error Messages', 'powerpack' ),
				'type'                  => Controls_Manager::HEADING,
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
					'{{WRAPPER}} .pp-ninja-form .nf-error-wrap .nf-error-required-error' => 'color: {{VALUE}}',
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
				'selector'              => '{{WRAPPER}} .pp-ninja-form .nf-error-wrap .nf-error-required-error',
			]
		);

		$this->add_control(
			'error_fields_heading',
			[
				'label'                 => __( 'Error Field', 'powerpack' ),
				'type'                  => Controls_Manager::HEADING,
				'separator'             => 'before',
				'condition'             => [
					'validation_errors' => 'show',
				],
			]
		);

		$this->add_control(
			'validation_error_field_input_border_color',
			[
				'label'                 => __( 'Error Field Input Border Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .pp-ninja-form .nf-error .ninja-forms-field' => 'border-color: {{VALUE}}',
				],
				'condition'             => [
					'validation_errors' => 'show',
				],
			]
		);

		$this->add_control(
			'validation_errors_heading',
			[
				'label'                 => __( 'Validation Errors', 'powerpack' ),
				'type'                  => Controls_Manager::HEADING,
				'separator'             => 'before',
				'condition'             => [
					'validation_errors' => 'show',
				],
			]
		);

		$this->add_control(
			'validation_error_description_color',
			[
				'label'                 => __( 'Error Description Color', 'powerpack' ),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .pp-ninja-form .nf-form-errors .nf-error-field-errors' => 'color: {{VALUE}}',
				],
				'condition'             => [
					'validation_errors' => 'show',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'                  => 'validation_error_description_typography',
				'label'                 => __( 'Error Description Typography', 'powerpack' ),
				'selector'              => '{{WRAPPER}} .pp-ninja-form .nf-form-errors .nf-error-field-errors',
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings();

		$this->add_render_attribute( 'contact-form', [
			'class' => [
				'pp-contact-form',
				'pp-ninja-form',
			],
			'id' => [
				'pp-ninja-form-' . get_the_ID(),
			],
		]);

		if ( 'yes' !== $settings['placeholder_switch'] ) {
			$this->add_render_attribute( 'contact-form', 'class', 'placeholder-hide' );
		}

		if ( 'yes' === $settings['custom_title_description'] ) {
			$this->add_render_attribute( 'contact-form', 'class', 'title-description-hide' );
		}

		if ( 'yes' === $settings['custom_radio_checkbox'] ) {
			$this->add_render_attribute( 'contact-form', 'class', 'pp-custom-radio-checkbox' );
		}

		if ( class_exists( 'Ninja_Forms' ) ) {
			if ( ! empty( $settings['contact_form_list'] ) ) { ?>
				<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'contact-form' ) ); ?>>
					<?php if ( 'yes' === $settings['custom_title_description'] ) { ?>
						<div class="pp-caldera-form-heading">
							<?php if ( $settings['form_title_custom'] ) { ?>
								<h3 class="pp-contact-form-title pp-caldera-form-title">
									<?php echo esc_attr( $settings['form_title_custom'] ); ?>
								</h3>
							<?php } ?>
							<?php if ( $settings['form_description_custom'] ) { ?>
								<div class="pp-contact-form-description pp-caldera-form-description">
									<?php echo $this->parse_text_editor( $settings['form_description_custom'] ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								</div>
							<?php } ?>
						</div>
					<?php } ?>
					<?php
						$form_id = $settings['contact_form_list'];

						echo do_shortcode( '[ninja_form id="' . $form_id . '" ]' );
					?>
				</div>
				<?php
			} else {
				$placeholder = sprintf( 'Click here to edit the "%1$s" settings and choose a contact form from the dropdown list.', esc_attr( $this->get_title() ) );

				echo esc_attr( $this->render_editor_placeholder(
					[
						'title' => __( 'No Contact Form Selected!', 'powerpack' ),
						'body' => $placeholder,
					]
				) );
			}
		}
	}
}
