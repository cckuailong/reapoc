<?php
namespace PowerpackElementsLite\Modules\GravityForms\Widgets;

use PowerpackElementsLite\Base\Powerpack_Widget;
use PowerpackElementsLite\Classes\PP_Helper;
use PowerpackElementsLite\Classes\PP_Config;

// Elementor Classes
use Elementor\Controls_Manager;
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
 * Gravity Forms Widget
 */
class Gravity_Forms extends Powerpack_Widget {

	/**
	 * Retrieve gravity forms widget name.
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return parent::get_widget_name( 'Gravity_Forms' );
	}

	/**
	 * Retrieve gravity forms widget title.
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return parent::get_widget_title( 'Gravity_Forms' );
	}

	/**
	 * Retrieve gravity forms widget icon.
	 *
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return parent::get_widget_icon( 'Gravity_Forms' );
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
		return parent::get_widget_keywords( 'Gravity_Forms' );
	}

	/**
	 * Retrieve the list of scripts the gravity forms widget depended on.
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
	 * Register gravity forms widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @access protected
	 */
	protected function _register_controls() { // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
		$this->register_controls();
	}

	/**
	 * Register gravity forms widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 2.3.2
	 * @access protected
	 */
	protected function register_controls() {

		/* Content Tab */
		$this->register_content_gravity_forms_controls();
		$this->register_content_errors_controls();
		$this->register_content_help_docs_controls();

		/* Style Tab */
		$this->register_style_title_controls();
		$this->register_style_labels_controls();
		$this->register_style_input_controls();
		$this->register_style_field_description_controls();
		$this->register_style_section_field_controls();
		$this->register_style_price_controls();
		$this->register_style_placeholder_controls();
		$this->register_style_radio_controls();
		$this->register_style_submit_button_controls();
		$this->register_style_pagination_controls();
		$this->register_style_progress_bar_controls();
		$this->register_style_errors_controls();
		$this->register_style_thankyou_controls();
	}

	/*-----------------------------------------------------------------------------------*/
	/* CONTENT TAB
	/*-----------------------------------------------------------------------------------*/

	protected function register_content_gravity_forms_controls() {
		/**
		 * Content Tab: Gravity Forms
		 * -------------------------------------------------
		 */
		$this->start_controls_section(
			'section_gravity_forms',
			array(
				'label' => __( 'Gravity Forms', 'powerpack' ),
			)
		);

		$this->add_control(
			'contact_form_list',
			array(
				'label'       => esc_html__( 'Contact Form', 'powerpack' ),
				'type'        => Controls_Manager::SELECT,
				'label_block' => true,
				'options'     => PP_Helper::get_contact_forms( 'Gravity_Forms' ),
				'default'     => '0',
			)
		);

		$this->add_control(
			'custom_title_description',
			array(
				'label'        => __( 'Custom Title & Description', 'powerpack' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'powerpack' ),
				'label_off'    => __( 'No', 'powerpack' ),
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'form_title',
			array(
				'label'        => __( 'Title', 'powerpack' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'label_on'     => __( 'Show', 'powerpack' ),
				'label_off'    => __( 'Hide', 'powerpack' ),
				'return_value' => 'yes',
				'condition'    => array(
					'custom_title_description!' => 'yes',
				),
			)
		);

		$this->add_control(
			'form_description',
			array(
				'label'        => __( 'Description', 'powerpack' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'label_on'     => __( 'Show', 'powerpack' ),
				'label_off'    => __( 'Hide', 'powerpack' ),
				'return_value' => 'yes',
				'condition'    => array(
					'custom_title_description!' => 'yes',
				),
			)
		);

		$this->add_control(
			'form_title_custom',
			array(
				'label'       => esc_html__( 'Title', 'powerpack' ),
				'type'        => Controls_Manager::TEXT,
				'label_block' => true,
				'default'     => '',
				'condition'   => array(
					'custom_title_description' => 'yes',
				),
			)
		);

		$this->add_control(
			'form_description_custom',
			array(
				'label'     => esc_html__( 'Description', 'powerpack' ),
				'type'      => Controls_Manager::TEXTAREA,
				'default'   => '',
				'condition' => array(
					'custom_title_description' => 'yes',
				),
			)
		);

		$this->add_control(
			'labels_switch',
			array(
				'label'        => __( 'Labels', 'powerpack' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'label_on'     => __( 'Show', 'powerpack' ),
				'label_off'    => __( 'Hide', 'powerpack' ),
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'placeholder_switch',
			array(
				'label'        => __( 'Placeholder', 'powerpack' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'label_on'     => __( 'Show', 'powerpack' ),
				'label_off'    => __( 'Hide', 'powerpack' ),
				'return_value' => 'yes',
			)
		);

		$this->add_control(
			'form_ajax',
			array(
				'label'        => __( 'Use Ajax', 'powerpack' ),
				'type'         => Controls_Manager::SWITCHER,
				'description'  => __( 'Use ajax to submit the form', 'powerpack' ),
				'label_on'     => __( 'Yes', 'powerpack' ),
				'label_off'    => __( 'No', 'powerpack' ),
				'return_value' => 'yes',
			)
		);

		$this->end_controls_section();
	}

	protected function register_content_errors_controls() {
		/**
		 * Content Tab: Errors
		 * -------------------------------------------------
		 */
		$this->start_controls_section(
			'section_errors',
			array(
				'label' => __( 'Errors', 'powerpack' ),
			)
		);

		$this->add_control(
			'error_messages',
			array(
				'label'                => __( 'Error Messages', 'powerpack' ),
				'type'                 => Controls_Manager::SELECT,
				'default'              => 'show',
				'options'              => array(
					'show' => __( 'Show', 'powerpack' ),
					'hide' => __( 'Hide', 'powerpack' ),
				),
				'selectors_dictionary' => array(
					'show' => 'block',
					'hide' => 'none',
				),
				'selectors'            => array(
					'{{WRAPPER}} .pp-gravity-form .validation_message:not(.validation_message--hidden-on-empty)' => 'display: {{VALUE}} !important;',
				),
			)
		);

		$this->add_control(
			'validation_errors',
			array(
				'label'                => __( 'Validation Errors', 'powerpack' ),
				'type'                 => Controls_Manager::SELECT,
				'default'              => 'show',
				'options'              => array(
					'show' => __( 'Show', 'powerpack' ),
					'hide' => __( 'Hide', 'powerpack' ),
				),
				'selectors_dictionary' => array(
					'show' => 'block',
					'hide' => 'none',
				),
				'selectors'            => array(
					'{{WRAPPER}} .pp-gravity-form .validation_error' => 'display: {{VALUE}} !important;',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function register_content_help_docs_controls() {

		$help_docs = PP_Config::get_widget_help_links( 'Gravity_Forms' );

		if ( ! empty( $help_docs ) ) {

			/**
			 * Content Tab: Help Docs
			 *
			 * @since  1.4.8
			 * @access protected
			 */
			$this->start_controls_section(
				'section_help_docs',
				array(
					'label' => __( 'Help Docs', 'powerpack' ),
				)
			);

			$hd_counter = 1;
			foreach ( $help_docs as $hd_title => $hd_link ) {
				$this->add_control(
					'help_doc_' . $hd_counter,
					array(
						'type'            => Controls_Manager::RAW_HTML,
						'raw'             => sprintf( '%1$s ' . $hd_title . ' %2$s', '<a href="' . $hd_link . '" target="_blank" rel="noopener">', '</a>' ),
						'content_classes' => 'pp-editor-doc-links',
					)
				);

				$hd_counter++;
			}

			$this->end_controls_section();
		}
	}

	protected function register_style_title_controls() {
		/**
		 * Style Tab: Title and Description
		 * -------------------------------------------------
		 */
		$this->start_controls_section(
			'section_general_style',
			array(
				'label' => __( 'Title & Description', 'powerpack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'heading_alignment',
			array(
				'label'     => __( 'Alignment', 'powerpack' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'left'   => array(
						'title' => __( 'Left', 'powerpack' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'powerpack' ),
						'icon'  => 'eicon-text-align-center',
					),
					'right'  => array(
						'title' => __( 'Right', 'powerpack' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .pp-gravity-form .gform_wrapper .gform_heading, {{WRAPPER}} .pp-gravity-form .pp-gravity-form-heading' => 'text-align: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'title_heading',
			array(
				'label'     => __( 'Title', 'powerpack' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'title_text_color',
			array(
				'label'     => __( 'Text Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .pp-gravity-form .gform_wrapper .gform_title, {{WRAPPER}} .pp-gravity-form .pp-gravity-form-title' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typography',
				'label'    => __( 'Typography', 'powerpack' ),
				'scheme'   => Scheme_Typography::TYPOGRAPHY_4,
				'selector' => '{{WRAPPER}} .pp-gravity-form .gform_wrapper .gform_title, {{WRAPPER}} .pp-gravity-form .pp-gravity-form-title',
			)
		);

		$this->add_control(
			'description_heading',
			array(
				'label'     => __( 'Description', 'powerpack' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'description_text_color',
			array(
				'label'     => __( 'Text Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .pp-gravity-form .gform_wrapper .gform_description, {{WRAPPER}} .pp-gravity-form .pp-gravity-form-description' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'description_typography',
				'label'    => __( 'Typography', 'powerpack' ),
				'scheme'   => Scheme_Typography::TYPOGRAPHY_4,
				'selector' => '{{WRAPPER}} .pp-gravity-form .gform_wrapper .gform_description, {{WRAPPER}} .pp-gravity-form .pp-gravity-form-description',
			)
		);

		$this->end_controls_section();
	}

	protected function register_style_labels_controls() {
		/**
		 * Style Tab: Labels
		 * -------------------------------------------------
		 */
		$this->start_controls_section(
			'section_label_style',
			array(
				'label'     => __( 'Labels', 'powerpack' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'labels_switch' => 'yes',
				),
			)
		);

		$this->add_control(
			'text_color_label',
			array(
				'label'     => __( 'Text Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .pp-gravity-form .gfield label' => 'color: {{VALUE}}',
				),
				'condition' => array(
					'labels_switch' => 'yes',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'typography_label',
				'label'     => __( 'Typography', 'powerpack' ),
				'selector'  => '{{WRAPPER}} .pp-gravity-form .gfield label',
				'condition' => array(
					'labels_switch' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'label_spacing',
			array(
				'label'      => __( 'Spacing', 'powerpack' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					),
				),
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .pp-gravity-form .gfield label' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				),
				'condition'  => array(
					'labels_switch' => 'yes',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function register_style_input_controls() {
		/**
		 * Style Tab: Input & Textarea
		 * -------------------------------------------------
		 */
		$this->start_controls_section(
			'section_fields_style',
			array(
				'label' => __( 'Input & Textarea', 'powerpack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'input_alignment',
			array(
				'label'     => __( 'Alignment', 'powerpack' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'left'   => array(
						'title' => __( 'Left', 'powerpack' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'powerpack' ),
						'icon'  => 'eicon-text-align-center',
					),
					'right'  => array(
						'title' => __( 'Right', 'powerpack' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .pp-gravity-form .gfield input[type="text"], {{WRAPPER}} .pp-gravity-form .gfield textarea' => 'text-align: {{VALUE}};',
				),
			)
		);

		$this->start_controls_tabs( 'tabs_fields_style' );

		$this->start_controls_tab(
			'tab_fields_normal',
			array(
				'label' => __( 'Normal', 'powerpack' ),
			)
		);

		$this->add_control(
			'field_bg_color',
			array(
				'label'     => __( 'Background Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#f9f9f9',
				'selectors' => array(
					'{{WRAPPER}} .pp-gravity-form .gform_wrapper input:not([type=radio]):not([type=checkbox]):not([type=submit]):not([type=button]):not([type=image]):not([type=file]), {{WRAPPER}} .pp-gravity-form .gfield textarea, {{WRAPPER}} .pp-gravity-form .gfield select' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'field_text_color',
			array(
				'label'     => __( 'Text Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .pp-gravity-form .gform_wrapper input:not([type=radio]):not([type=checkbox]):not([type=submit]):not([type=button]):not([type=image]):not([type=file]), {{WRAPPER}} .pp-gravity-form .gfield textarea, {{WRAPPER}} .pp-gravity-form .gfield select' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_responsive_control(
			'field_spacing',
			array(
				'label'      => __( 'Spacing', 'powerpack' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					),
				),
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .pp-gravity-form .gform_wrapper.gravity-theme .gform_fields' => 'grid-row-gap: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'field_padding',
			array(
				'label'      => __( 'Padding', 'powerpack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'default'    => array(
					'top'      => 10,
					'right'    => 10,
					'bottom'   => 10,
					'left'     => 10,
					'unit'     => 'px',
					'isLinked' => true,
				),
				'selectors'  => array(
					'{{WRAPPER}} .pp-gravity-form .gform_wrapper input:not([type=radio]):not([type=checkbox]):not([type=submit]):not([type=button]):not([type=image]):not([type=file]), {{WRAPPER}} .pp-gravity-form .gfield textarea, {{WRAPPER}} .pp-gravity-form .gfield select' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'text_indent',
			array(
				'label'      => __( 'Text Indent', 'powerpack' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 60,
						'step' => 1,
					),
					'%'  => array(
						'min'  => 0,
						'max'  => 30,
						'step' => 1,
					),
				),
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .pp-gravity-form .gform_wrapper input:not([type=radio]):not([type=checkbox]):not([type=submit]):not([type=button]):not([type=image]):not([type=file]), {{WRAPPER}} .pp-gravity-form .gfield textarea, {{WRAPPER}} .pp-gravity-form .gfield select' => 'text-indent: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'input_width',
			array(
				'label'      => __( 'Input Width', 'powerpack' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 1200,
						'step' => 1,
					),
				),
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .pp-gravity-form .gform_wrapper input:not([type=radio]):not([type=checkbox]):not([type=submit]):not([type=button]):not([type=image]):not([type=file]), {{WRAPPER}} .pp-gravity-form .gfield select' => 'width: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'input_height',
			array(
				'label'      => __( 'Input Height', 'powerpack' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 80,
						'step' => 1,
					),
				),
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .pp-gravity-form .gform_wrapper input:not([type=radio]):not([type=checkbox]):not([type=submit]):not([type=button]):not([type=image]):not([type=file]), {{WRAPPER}} .pp-gravity-form .gfield select' => 'height: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'textarea_width',
			array(
				'label'      => __( 'Textarea Width', 'powerpack' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 1200,
						'step' => 1,
					),
				),
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .pp-gravity-form .gfield textarea' => 'width: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_responsive_control(
			'textarea_height',
			array(
				'label'      => __( 'Textarea Height', 'powerpack' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 400,
						'step' => 1,
					),
				),
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .pp-gravity-form .gfield textarea' => 'height: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'field_border',
				'label'       => __( 'Border', 'powerpack' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .pp-gravity-form .gform_wrapper input:not([type=radio]):not([type=checkbox]):not([type=submit]):not([type=button]):not([type=image]):not([type=file]), {{WRAPPER}} .pp-gravity-form .gfield textarea, {{WRAPPER}} .pp-gravity-form .gfield select',
				'separator'   => 'before',
			)
		);

		$this->add_control(
			'field_radius',
			array(
				'label'      => __( 'Border Radius', 'powerpack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .pp-gravity-form .gform_wrapper input:not([type=radio]):not([type=checkbox]):not([type=submit]):not([type=button]):not([type=image]):not([type=file]), {{WRAPPER}} .pp-gravity-form .gfield textarea, {{WRAPPER}} .pp-gravity-form .gfield select' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'field_typography',
				'label'     => __( 'Typography', 'powerpack' ),
				'scheme'    => Scheme_Typography::TYPOGRAPHY_4,
				'selector'  => '{{WRAPPER}} .pp-gravity-form .gform_wrapper input:not([type=radio]):not([type=checkbox]):not([type=submit]):not([type=button]):not([type=image]):not([type=file]), {{WRAPPER}} .pp-gravity-form .gfield textarea, {{WRAPPER}} .pp-gravity-form .gfield select',
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'      => 'field_box_shadow',
				'selector'  => '{{WRAPPER}} .pp-gravity-form .gform_wrapper input:not([type=radio]):not([type=checkbox]):not([type=submit]):not([type=button]):not([type=image]):not([type=file]), {{WRAPPER}} .pp-gravity-form .gfield textarea, {{WRAPPER}} .pp-gravity-form .gfield select',
				'separator' => 'before',
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_fields_focus',
			array(
				'label' => __( 'Focus', 'powerpack' ),
			)
		);

		$this->add_control(
			'field_bg_color_focus',
			array(
				'label'     => __( 'Background Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .pp-gravity-form .gform_wrapper input:not([type=radio]):not([type=checkbox]):not([type=submit]):not([type=button]):not([type=image]):not([type=file]):focus, {{WRAPPER}} .pp-gravity-form .gform_wrapper .gfield textarea:focus' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'field_text_color_focus',
			array(
				'label'     => __( 'Text Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .pp-gravity-form .gform_wrapper input:not([type=radio]):not([type=checkbox]):not([type=submit]):not([type=button]):not([type=image]):not([type=file]):focus, {{WRAPPER}} .pp-gravity-form .gform_wrapper .gfield textarea:focus' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'focus_input_border',
				'label'       => __( 'Border', 'powerpack' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .pp-gravity-form .gform_wrapper input:not([type=radio]):not([type=checkbox]):not([type=submit]):not([type=button]):not([type=image]):not([type=file]):focus, {{WRAPPER}} .pp-gravity-form .gfield textarea:focus',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'      => 'focus_box_shadow',
				'selector'  => '{{WRAPPER}} .pp-gravity-form .gfield input:focus, {{WRAPPER}} .pp-gravity-form .gfield textarea:focus',
				'separator' => 'before',
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	protected function register_style_field_description_controls() {
		/**
		 * Style Tab: Field Description
		 * -------------------------------------------------
		 */
		$this->start_controls_section(
			'section_field_description_style',
			array(
				'label' => __( 'Field Description', 'powerpack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'field_description_text_color',
			array(
				'label'     => __( 'Text Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .pp-gravity-form .gfield .gfield_description' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'field_description_typography',
				'label'    => __( 'Typography', 'powerpack' ),
				'selector' => '{{WRAPPER}} .pp-gravity-form .gfield .gfield_description',
			)
		);

		$this->add_responsive_control(
			'field_description_spacing',
			array(
				'label'      => __( 'Spacing', 'powerpack' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					),
				),
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .pp-gravity-form .gfield .gfield_description' => 'padding-top: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function register_style_section_field_controls() {
		/**
		 * Style Tab: Section Field
		 * -------------------------------------------------
		 */
		$this->start_controls_section(
			'section_field_style',
			array(
				'label' => __( 'Section Field', 'powerpack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'section_field_text_color',
			array(
				'label'     => __( 'Text Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .pp-gravity-form .gfield.gsection .gsection_title' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'section_field_typography',
				'label'     => __( 'Typography', 'powerpack' ),
				'scheme'    => Scheme_Typography::TYPOGRAPHY_4,
				'selector'  => '{{WRAPPER}} .pp-gravity-form .gfield.gsection .gsection_title',
				'separator' => 'before',
			)
		);

		$this->add_control(
			'section_field_border_type',
			array(
				'label'     => __( 'Border Type', 'powerpack' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'solid',
				'options'   => array(
					'none'   => __( 'None', 'powerpack' ),
					'solid'  => __( 'Solid', 'powerpack' ),
					'double' => __( 'Double', 'powerpack' ),
					'dotted' => __( 'Dotted', 'powerpack' ),
					'dashed' => __( 'Dashed', 'powerpack' ),
				),
				'selectors' => array(
					'{{WRAPPER}} .pp-gravity-form .gfield.gsection' => 'border-bottom-style: {{VALUE}}',
				),
				'separator' => 'before',
			)
		);

		$this->add_responsive_control(
			'section_field_border_height',
			array(
				'label'      => __( 'Border Height', 'powerpack' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => array(
					'size' => 1,
				),
				'range'      => array(
					'px' => array(
						'min'  => 1,
						'max'  => 20,
						'step' => 1,
					),
				),
				'size_units' => array( 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .pp-gravity-form .gfield.gsection' => 'border-bottom-width: {{SIZE}}{{UNIT}}',
				),
				'condition'  => array(
					'section_field_border_type!' => 'none',
				),
			)
		);

		$this->add_control(
			'section_field_border_color',
			array(
				'label'     => __( 'Border Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .pp-gravity-form .gfield.gsection' => 'border-bottom-color: {{VALUE}}',
				),
				'condition' => array(
					'section_field_border_type!' => 'none',
				),
			)
		);

		$this->add_responsive_control(
			'section_field_margin',
			array(
				'label'      => __( 'Margin', 'powerpack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .pp-gravity-form .gfield.gsection' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'separator'  => 'before',
			)
		);

		$this->end_controls_section();
	}

	protected function register_style_price_controls() {
		/**
		 * Style Tab: Price
		 * -------------------------------------------------
		 */
		$this->start_controls_section(
			'section_price_style',
			array(
				'label' => __( 'Price', 'powerpack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'price_label_color',
			array(
				'label'     => __( 'Price Label Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .pp-gravity-form .gform_wrapper .ginput_product_price_label' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'price_text_color',
			array(
				'label'     => __( 'Price Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .pp-gravity-form .gform_wrapper .ginput_product_price' => 'color: {{VALUE}}',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function register_style_placeholder_controls() {
		/**
		 * Style Tab: Placeholder
		 * -------------------------------------------------
		 */
		$this->start_controls_section(
			'section_placeholder_style',
			array(
				'label'     => __( 'Placeholder', 'powerpack' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => array(
					'placeholder_switch' => 'yes',
				),
			)
		);

		$this->add_control(
			'text_color_placeholder',
			array(
				'label'     => __( 'Text Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} .pp-gravity-form .gfield input::-webkit-input-placeholder, {{WRAPPER}} .pp-gravity-form .gfield textarea::-webkit-input-placeholder' => 'color: {{VALUE}}',
				),
				'condition' => array(
					'placeholder_switch' => 'yes',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function register_style_radio_controls() {
		/**
		 * Style Tab: Radio & Checkbox
		 * -------------------------------------------------
		 */
		$this->start_controls_section(
			'section_radio_checkbox_style',
			array(
				'label' => __( 'Radio & Checkbox', 'powerpack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'custom_radio_checkbox',
			array(
				'label'        => __( 'Custom Styles', 'powerpack' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Yes', 'powerpack' ),
				'label_off'    => __( 'No', 'powerpack' ),
				'return_value' => 'yes',
			)
		);

		$this->add_responsive_control(
			'radio_checkbox_size',
			array(
				'label'      => __( 'Size', 'powerpack' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => array(
					'size' => '15',
					'unit' => 'px',
				),
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 80,
						'step' => 1,
					),
				),
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .pp-custom-radio-checkbox input[type="checkbox"], {{WRAPPER}} .pp-custom-radio-checkbox input[type="radio"]' => 'width: {{SIZE}}{{UNIT}} !important; height: {{SIZE}}{{UNIT}}',
				),
				'condition'  => array(
					'custom_radio_checkbox' => 'yes',
				),
			)
		);

		$this->start_controls_tabs( 'tabs_radio_checkbox_style' );

		$this->start_controls_tab(
			'radio_checkbox_normal',
			array(
				'label'     => __( 'Normal', 'powerpack' ),
				'condition' => array(
					'custom_radio_checkbox' => 'yes',
				),
			)
		);

		$this->add_control(
			'radio_checkbox_color',
			array(
				'label'     => __( 'Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .pp-custom-radio-checkbox input[type="checkbox"], {{WRAPPER}} .pp-custom-radio-checkbox input[type="radio"]' => 'background: {{VALUE}}',
				),
				'condition' => array(
					'custom_radio_checkbox' => 'yes',
				),
			)
		);

		$this->add_responsive_control(
			'radio_checkbox_border_width',
			array(
				'label'      => __( 'Border Width', 'powerpack' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 15,
						'step' => 1,
					),
				),
				'size_units' => array( 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .pp-custom-radio-checkbox input[type="checkbox"], {{WRAPPER}} .pp-custom-radio-checkbox input[type="radio"]' => 'border-width: {{SIZE}}{{UNIT}}',
				),
				'condition'  => array(
					'custom_radio_checkbox' => 'yes',
				),
			)
		);

		$this->add_control(
			'radio_checkbox_border_color',
			array(
				'label'     => __( 'Border Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .pp-custom-radio-checkbox input[type="checkbox"], {{WRAPPER}} .pp-custom-radio-checkbox input[type="radio"]' => 'border-color: {{VALUE}}',
				),
				'condition' => array(
					'custom_radio_checkbox' => 'yes',
				),
			)
		);

		$this->add_control(
			'checkbox_heading',
			array(
				'label'     => __( 'Checkbox', 'powerpack' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'custom_radio_checkbox' => 'yes',
				),
			)
		);

		$this->add_control(
			'checkbox_border_radius',
			array(
				'label'      => __( 'Border Radius', 'powerpack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .pp-custom-radio-checkbox input[type="checkbox"], {{WRAPPER}} .pp-custom-radio-checkbox input[type="checkbox"]:before' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'custom_radio_checkbox' => 'yes',
				),
			)
		);

		$this->add_control(
			'radio_heading',
			array(
				'label'     => __( 'Radio Buttons', 'powerpack' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'custom_radio_checkbox' => 'yes',
				),
			)
		);

		$this->add_control(
			'radio_border_radius',
			array(
				'label'      => __( 'Border Radius', 'powerpack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .pp-custom-radio-checkbox input[type="radio"], {{WRAPPER}} .pp-custom-radio-checkbox input[type="radio"]:before' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'condition'  => array(
					'custom_radio_checkbox' => 'yes',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'radio_checkbox_checked',
			array(
				'label'     => __( 'Checked', 'powerpack' ),
				'condition' => array(
					'custom_radio_checkbox' => 'yes',
				),
			)
		);

		$this->add_control(
			'radio_checkbox_color_checked',
			array(
				'label'     => __( 'Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .pp-custom-radio-checkbox input[type="checkbox"]:checked:before, {{WRAPPER}} .pp-custom-radio-checkbox input[type="radio"]:checked:before' => 'background: {{VALUE}}',
				),
				'condition' => array(
					'custom_radio_checkbox' => 'yes',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	protected function register_style_submit_button_controls() {
		/**
		 * Style Tab: Submit Button
		 * -------------------------------------------------
		 */
		$this->start_controls_section(
			'section_submit_button_style',
			array(
				'label' => __( 'Submit Button', 'powerpack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'button_align',
			array(
				'label'     => __( 'Alignment', 'powerpack' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'left'   => array(
						'title' => __( 'Left', 'powerpack' ),
						'icon'  => 'eicon-h-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'powerpack' ),
						'icon'  => 'eicon-h-align-center',
					),
					'right'  => array(
						'title' => __( 'Right', 'powerpack' ),
						'icon'  => 'eicon-h-align-right',
					),
				),
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .pp-gravity-form .gform_footer,
                    {{WRAPPER}} .pp-gravity-form .gform_page_footer'   => 'text-align: {{VALUE}};',
				),
				'condition' => array(
					'button_width_type!' => 'full-width',
				),
			)
		);

		$this->add_control(
			'button_width_type',
			array(
				'label'        => __( 'Width', 'powerpack' ),
				'type'         => Controls_Manager::SELECT,
				'default'      => 'custom',
				'options'      => array(
					'auto'       => __( 'Auto', 'powerpack' ),
					'full-width' => __( 'Full Width', 'powerpack' ),
					'custom'     => __( 'Custom', 'powerpack' ),
				),
				'prefix_class' => 'pp-gravity-form-button-',
			)
		);

		$this->add_responsive_control(
			'button_width',
			array(
				'label'      => __( 'Width', 'powerpack' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => array(
					'size' => '100',
					'unit' => 'px',
				),
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 1200,
						'step' => 1,
					),
				),
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .pp-gravity-form .gform_footer input[type="submit"], {{WRAPPER}} .pp-gravity-form .gform_page_footer input[type="submit"]' => 'width: {{SIZE}}{{UNIT}}',
				),
				'condition'  => array(
					'button_width_type' => 'custom',
				),
			)
		);

		$this->start_controls_tabs( 'tabs_button_style' );

		$this->start_controls_tab(
			'tab_button_normal',
			array(
				'label' => __( 'Normal', 'powerpack' ),
			)
		);

		$this->add_control(
			'button_bg_color_normal',
			array(
				'label'     => __( 'Background Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .pp-gravity-form .gform_footer input[type="submit"],
                    {{WRAPPER}} .pp-gravity-form .gform_page_footer input[type="submit"]' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'button_text_color_normal',
			array(
				'label'     => __( 'Text Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .pp-gravity-form .gform_footer input[type="submit"],
                    {{WRAPPER}} .pp-gravity-form .gform_page_footer input[type="submit"]' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'button_border_normal',
				'label'       => __( 'Border', 'powerpack' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .pp-gravity-form .gform_footer input[type="submit"], {{WRAPPER}} .pp-gravity-form .gform_page_footer input[type="submit"]',
			)
		);

		$this->add_control(
			'button_border_radius',
			array(
				'label'      => __( 'Border Radius', 'powerpack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .pp-gravity-form .gform_footer input[type="submit"], {{WRAPPER}} .pp-gravity-form .gform_page_footer input[type="submit"]' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'button_padding',
			array(
				'label'      => __( 'Padding', 'powerpack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .pp-gravity-form .gform_footer input[type="submit"], {{WRAPPER}} .pp-gravity-form .gform_page_footer input[type="submit"]' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'button_margin',
			array(
				'label'      => __( 'Margin Top', 'powerpack' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					),
				),
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .pp-gravity-form .gform_footer input[type="submit"], {{WRAPPER}} .pp-gravity-form .gform_page_footer input[type="submit"]' => 'margin-top: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'button_typography',
				'label'     => __( 'Typography', 'powerpack' ),
				'scheme'    => Scheme_Typography::TYPOGRAPHY_4,
				'selector'  => '{{WRAPPER}} .pp-gravity-form .gform_footer input[type="submit"], {{WRAPPER}} .pp-gravity-form .gform_page_footer input[type="submit"]',
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'      => 'button_box_shadow',
				'selector'  => '{{WRAPPER}} .pp-gravity-form .gform_footer input[type="submit"], {{WRAPPER}} .pp-gravity-form .gform_page_footer input[type="submit"]',
				'separator' => 'before',
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_button_hover',
			array(
				'label' => __( 'Hover', 'powerpack' ),
			)
		);

		$this->add_control(
			'button_bg_color_hover',
			array(
				'label'     => __( 'Background Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .pp-gravity-form .gform_footer input[type="submit"]:hover, {{WRAPPER}} .pp-gravity-form .gform_page_footer input[type="submit"]:hover' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'button_text_color_hover',
			array(
				'label'     => __( 'Text Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .pp-gravity-form .gform_footer input[type="submit"]:hover, {{WRAPPER}} .pp-gravity-form .gform_page_footer input[type="submit"]:hover' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'button_border_color_hover',
			array(
				'label'     => __( 'Border Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .pp-gravity-form .gform_footer input[type="submit"]:hover, {{WRAPPER}} .pp-gravity-form .gform_page_footer input[type="submit"]:hover' => 'border-color: {{VALUE}}',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	protected function register_style_pagination_controls() {
		/**
		 * Style Tab: Pagination
		 * -------------------------------------------------
		 */
		$this->start_controls_section(
			'section_pagination_style',
			array(
				'label' => __( 'Pagination', 'powerpack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'pagination_buttons_width_type',
			array(
				'label'        => __( 'Width', 'powerpack' ),
				'type'         => Controls_Manager::SELECT,
				'default'      => 'auto',
				'options'      => array(
					'auto'       => __( 'Auto', 'powerpack' ),
					'full-width' => __( 'Full Width', 'powerpack' ),
					'custom'     => __( 'Custom', 'powerpack' ),
				),
				'prefix_class' => 'pp-gravity-form-pagination-buttons-',
			)
		);

		$this->add_responsive_control(
			'pagination_buttons_width',
			array(
				'label'      => __( 'Width', 'powerpack' ),
				'type'       => Controls_Manager::SLIDER,
				'default'    => array(
					'size' => '100',
					'unit' => 'px',
				),
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 1200,
						'step' => 1,
					),
				),
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .pp-gravity-form .gform_page_footer input[type="button"]' => 'width: {{SIZE}}{{UNIT}}',
				),
				'condition'  => array(
					'pagination_buttons_width_type' => 'custom',
				),
			)
		);

		$this->start_controls_tabs( 'tabs_pagination_buttons_style' );

		$this->start_controls_tab(
			'tab_pagination_buttons_normal',
			array(
				'label' => __( 'Normal', 'powerpack' ),
			)
		);

		$this->add_control(
			'pagination_buttons_bg_color_normal',
			array(
				'label'     => __( 'Background Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .pp-gravity-form .gform_page_footer input[type="button"]' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'pagination_buttons_text_color_normal',
			array(
				'label'     => __( 'Text Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .pp-gravity-form .gform_page_footer input[type="button"]' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'pagination_buttons_border_normal',
				'label'       => __( 'Border', 'powerpack' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .pp-gravity-form .gform_page_footer input[type="button"]',
			)
		);

		$this->add_control(
			'pagination_buttons_border_radius',
			array(
				'label'      => __( 'Border Radius', 'powerpack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .pp-gravity-form .gform_page_footer input[type="button"]' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'pagination_buttons_padding',
			array(
				'label'      => __( 'Padding', 'powerpack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .pp-gravity-form .gform_page_footer input[type="button"]' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'pagination_buttons_margin',
			array(
				'label'      => __( 'Margin Top', 'powerpack' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					),
				),
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .pp-gravity-form .gform_page_footer input[type="button"]' => 'margin-top: {{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'      => 'pagination_buttons_typography',
				'label'     => __( 'Typography', 'powerpack' ),
				'scheme'    => Scheme_Typography::TYPOGRAPHY_4,
				'selector'  => '{{WRAPPER}} .pp-gravity-form .gform_page_footer input[type="button"]',
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'      => 'pagination_buttons_box_shadow',
				'selector'  => '{{WRAPPER}} .pp-gravity-form .gform_page_footer input[type="button"]',
				'separator' => 'before',
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_pagination_buttons_hover',
			array(
				'label' => __( 'Hover', 'powerpack' ),
			)
		);

		$this->add_control(
			'pagination_buttons_bg_color_hover',
			array(
				'label'     => __( 'Background Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .pp-gravity-form .gform_page_footer input[type="button"]:hover' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'pagination_buttons_text_color_hover',
			array(
				'label'     => __( 'Text Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .pp-gravity-form .gform_page_footer input[type="button"]:hover' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'pagination_buttons_border_color_hover',
			array(
				'label'     => __( 'Border Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .pp-gravity-form .gform_page_footer input[type="button"]:hover' => 'border-color: {{VALUE}}',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	protected function register_style_progress_bar_controls() {
		/**
		 * Style Tab: Progress Bar
		 * -------------------------------------------------
		 */
		$this->start_controls_section(
			'section_progress_bar_style',
			array(
				'label' => __( 'Progress Bar', 'powerpack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->start_controls_tabs( 'tabs_progress_bar_style' );

		$this->start_controls_tab(
			'tab_progress_bar_default',
			array(
				'label' => __( 'Default', 'powerpack' ),
			)
		);

		$this->add_control(
			'progress_bar_default_bg',
			array(
				'label'     => __( 'Background Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .pp-gravity-form .gform_wrapper .gf_progressbar' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'progress_bar_text_color',
			array(
				'label'     => __( 'Text Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .pp-gravity-form .gform_wrapper .gf_progressbar_percentage span' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'progress_bar_typography',
				'label'    => __( 'Typography', 'powerpack' ),
				'selector' => '{{WRAPPER}} .pp-gravity-form .gform_wrapper .gf_progressbar_percentage span',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'progress_bar_default_border',
				'label'       => __( 'Border', 'powerpack' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .pp-gravity-form .gform_wrapper .gf_progressbar',
			)
		);

		$this->add_control(
			'progress_bar_border_radius',
			array(
				'label'      => __( 'Border Radius', 'powerpack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .pp-gravity-form .gform_wrapper .gf_progressbar, {{WRAPPER}} .pp-gravity-form .gform_wrapper .gf_progressbar_percentage, {{WRAPPER}} .pp-gravity-form .gform_wrapper .gf_progressbar:after' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'progress_bar_default_padding',
			array(
				'label'      => __( 'Padding', 'powerpack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .pp-gravity-form .gform_wrapper .gf_progressbar' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'progress_bar_default_box_shadow',
				'selector' => '{{WRAPPER}} .pp-gravity-form .gform_wrapper .gf_progressbar',
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_progress_bar_progress',
			array(
				'label' => __( 'Progress', 'powerpack' ),
			)
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			array(
				'name'     => 'progress_bar_bg',
				'label'    => __( 'Background', 'powerpack' ),
				'types'    => array( 'classic', 'gradient' ),
				'selector' => '{{WRAPPER}} .pp-gravity-form .gform_wrapper .gf_progressbar_percentage',
				'exclude'  => array( 'image' ),
			)
		);

		$this->add_responsive_control(
			'progress_bar_height',
			array(
				'label'      => __( 'Height', 'powerpack' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => array(
					'px' => array(
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					),
				),
				'size_units' => array( 'px' ),
				'selectors'  => array(
					'{{WRAPPER}} .pp-gravity-form .gform_wrapper .gf_progressbar_percentage, {{WRAPPER}} .pp-gravity-form .gform_wrapper .gf_progressbar:after' => 'height: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .pp-gravity-form .gform_wrapper .gf_progressbar:after' => 'margin-top: -{{SIZE}}{{UNIT}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'progress_bar_progress_box_shadow',
				'selector' => '{{WRAPPER}} .pp-gravity-form .gform_wrapper .gf_progressbar:after',
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'progress_bar_label_heading',
			array(
				'label'     => __( 'Label', 'powerpack' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'progress_bar_label_color',
			array(
				'label'     => __( 'Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .pp-gravity-form .gform_wrapper .gf_progressbar_wrapper .gf_progressbar_title, {{WRAPPER}} .pp-gravity-form .gform_wrapper .gf_step' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'progress_bar_label_typography',
				'label'    => __( 'Typography', 'powerpack' ),
				'selector' => '{{WRAPPER}} .pp-gravity-form .gform_wrapper .gf_progressbar_wrapper .gf_progressbar_title, {{WRAPPER}} .pp-gravity-form .gform_wrapper .gf_step',
			)
		);

		$this->end_controls_section();
	}

	protected function register_style_errors_controls() {
		/**
		 * Style Tab: Errors
		 * -------------------------------------------------
		 */
		$this->start_controls_section(
			'section_error_style',
			array(
				'label' => __( 'Errors', 'powerpack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_control(
			'error_messages_heading',
			array(
				'label'     => __( 'Error Messages', 'powerpack' ),
				'type'      => Controls_Manager::HEADING,
				'condition' => array(
					'error_messages' => 'show',
				),
			)
		);

		$this->add_control(
			'error_message_text_color',
			array(
				'label'     => __( 'Text Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .pp-gravity-form .gfield .validation_message' => 'color: {{VALUE}}',
				),
				'condition' => array(
					'error_messages' => 'show',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'error_message_typography',
				'label'    => __( 'Typography', 'powerpack' ),
				'selector' => '{{WRAPPER}} .pp-gravity-form .gfield .validation_message',
			)
		);

		$this->add_control(
			'validation_errors_heading',
			array(
				'label'     => __( 'Validation Errors', 'powerpack' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => array(
					'validation_errors' => 'show',
				),
			)
		);

		$this->add_control(
			'validation_error_description_color',
			array(
				'label'     => __( 'Error Description Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .pp-gravity-form .gform_wrapper .validation_error' => 'color: {{VALUE}}',
				),
				'condition' => array(
					'validation_errors' => 'show',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'validation_error_description_typography',
				'label'    => __( 'Error Description Typography', 'powerpack' ),
				'selector' => '{{WRAPPER}} .pp-gravity-form .gform_wrapper .validation_error',
			)
		);

		$this->add_control(
			'validation_error_border_color',
			array(
				'label'     => __( 'Error Border Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .pp-gravity-form .gform_wrapper .validation_error' => 'border-top-color: {{VALUE}}; border-bottom-color: {{VALUE}}',
					'{{WRAPPER}} .pp-gravity-form .gfield_error' => 'border-top-color: {{VALUE}}; border-bottom-color: {{VALUE}}',
				),
				'condition' => array(
					'validation_errors' => 'show',
				),
			)
		);

		$this->add_control(
			'validation_errors_bg_color',
			array(
				'label'     => __( 'Error Field Background Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .pp-gravity-form .gfield_error' => 'background: {{VALUE}}',
				),
				'condition' => array(
					'validation_errors' => 'show',
				),
			)
		);

		$this->add_control(
			'validation_error_field_label_color',
			array(
				'label'     => __( 'Error Field Label Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .pp-gravity-form .gfield_error .gfield_label' => 'color: {{VALUE}}',
				),
				'condition' => array(
					'validation_errors' => 'show',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'validation_error_field_label_typography',
				'label'    => __( 'Error Field Label Typography', 'powerpack' ),
				'selector' => '{{WRAPPER}} .pp-gravity-form .gfield_error .gfield_label',
			)
		);

		$this->add_control(
			'validation_error_field_input_border_color',
			array(
				'label'     => __( 'Error Field Input Border Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .pp-gravity-form .gform_wrapper li.gfield_error input:not([type=radio]):not([type=checkbox]):not([type=submit]):not([type=button]):not([type=image]):not([type=file]), {{WRAPPER}} .gform_wrapper li.gfield_error textarea' => 'border-color: {{VALUE}}',
				),
				'condition' => array(
					'validation_errors' => 'show',
				),
			)
		);

		$this->add_control(
			'validation_error_field_input_border_width',
			array(
				'label'     => __( 'Error Field Input Border Width', 'powerpack' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 1,
				'min'       => 1,
				'max'       => 10,
				'step'      => 1,
				'selectors' => array(
					'{{WRAPPER}} .pp-gravity-form .gform_wrapper li.gfield_error input:not([type=radio]):not([type=checkbox]):not([type=submit]):not([type=button]):not([type=image]):not([type=file]), {{WRAPPER}} .gform_wrapper li.gfield_error textarea' => 'border-width: {{VALUE}}px',
				),
				'condition' => array(
					'validation_errors' => 'show',
				),
			)
		);

		$this->end_controls_section();
	}

	protected function register_style_thankyou_controls() {
		/**
		 * Style Tab: Thank You Message
		 * -------------------------------------------------
		 */
		$this->start_controls_section(
			'section_ty_style',
			array(
				'label' => __( 'Thank You Message', 'powerpack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'ty_message_text_alignment',
			array(
				'label'     => __( 'Alignment', 'powerpack' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => array(
					'left'   => array(
						'title' => __( 'Left', 'powerpack' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => __( 'Center', 'powerpack' ),
						'icon'  => 'eicon-text-align-center',
					),
					'right'  => array(
						'title' => __( 'Right', 'powerpack' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .pp-gravity-form .gform_confirmation_wrapper' => 'text-align: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'ty_message_text_color',
			array(
				'label'     => __( 'Text Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .pp-gravity-form .gform_confirmation_wrapper' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'ty_message_background_color',
			array(
				'label'     => __( 'Background Color', 'powerpack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'{{WRAPPER}} .pp-gravity-form .gform_confirmation_wrapper' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'ty_message_typography',
				'label'    => __( 'Typography', 'powerpack' ),
				'selector' => '{{WRAPPER}} .gform_confirmation_wrapper',
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'ty_message_border',
				'label'       => __( 'Border', 'powerpack' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .gform_confirmation_wrapper',
				'separator'   => 'before',
			)
		);

		$this->add_control(
			'ty_message_field_radius',
			array(
				'label'      => __( 'Border Radius', 'powerpack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} .gform_confirmation_wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'ty_message_field_padding',
			array(
				'label'      => __( 'Padding', 'powerpack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', 'em', '%' ),
				'default'    => array(
					'top'      => 10,
					'right'    => 10,
					'bottom'   => 10,
					'left'     => 10,
					'unit'     => 'px',
					'isLinked' => true,
				),
				'selectors'  => array(
					'{{WRAPPER}} .gform_confirmation_wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render gravity forms widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
	protected function render() {
		$settings = $this->get_settings();

		$this->add_render_attribute(
			'contact-form',
			'class',
			array(
				'pp-contact-form',
				'pp-gravity-form',
			)
		);

		if ( 'yes' !== $settings['labels_switch'] ) {
			$this->add_render_attribute( 'contact-form', 'class', 'labels-hide' );
		}

		if ( 'yes' !== $settings['placeholder_switch'] ) {
			$this->add_render_attribute( 'contact-form', 'class', 'placeholder-hide' );
		}

		if ( 'yes' === $settings['custom_title_description'] ) {
			$this->add_render_attribute( 'contact-form', 'class', 'title-description-hide' );
		}

		if ( 'yes' === $settings['custom_radio_checkbox'] ) {
			$this->add_render_attribute( 'contact-form', 'class', 'pp-custom-radio-checkbox' );
		}

		if ( class_exists( 'GFCommon' ) ) {
			if ( ! empty( $settings['contact_form_list'] ) ) { ?>
				<div <?php echo wp_kses_post( $this->get_render_attribute_string( 'contact-form' ) ); ?>>
					<?php if ( 'yes' === $settings['custom_title_description'] ) { ?>
						<div class="pp-gravity-form-heading">
							<?php if ( $settings['form_title_custom'] ) { ?>
								<h3 class="pp-contact-form-title pp-gravity-form-title">
									<?php echo esc_attr( $settings['form_title_custom'] ); ?>
								</h3>
							<?php } ?>
							<?php if ( $settings['form_description_custom'] ) { ?>
								<div class="pp-contact-form-description pp-gravity-form-description">
									<?php echo $this->parse_text_editor( $settings['form_description_custom'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> 
								</div>
							<?php } ?>
						</div>
					<?php } ?>
					<?php
					$form_id          = $settings['contact_form_list'];
					$form_title       = $settings['form_title'];
					$form_description = $settings['form_description'];
					$form_ajax        = $settings['form_ajax'];

					gravity_form( $form_id, $form_title, $form_description, $display_inactive = false, $field_values = null, $form_ajax, '', $echo = true );
					?>
				</div>
				<?php
			} else {
				$placeholder = sprintf( 'Click here to edit the "%1$s" settings and choose a contact form from the dropdown list.', esc_attr( $this->get_title() ) );

				echo esc_attr( $this->render_editor_placeholder(
					array(
						'title' => __( 'No Contact Form Selected!', 'powerpack' ),
						'body'  => $placeholder,
					)
				) );
			}
		}
	}
}
