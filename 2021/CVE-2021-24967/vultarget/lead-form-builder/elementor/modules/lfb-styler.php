<?php
if ( ! defined( 'ABSPATH' ) ) exit; // If this file is called directly, abort.


//Elementor Classes
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Scheme_Typography;
use Elementor\Scheme_Color;



/**
 * Lead Form Styler Widget Class
 */
class Lead_Form_Styler extends Widget_Base {
	
	/**
	 * Retrieve Lead Form Styler Widget Name.
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'lead-form-styler';
	}

	/**
	 * Retrieve Lead Form Styler Widget Title.
	 *
	 * @access public
	 *
	 * @return string Widget Title.
	 */
	public function get_title() {
		return esc_html__( 'Lead Form Styler', 'lead-form-builder' );
	}

	/**
	 * Retrieve Lead Form Styler Widget Icon.
	 *
	 * @access public
	 *
	 * @return string Widget Icon.
	 */
	public function get_icon() {
		return 'eicon-form-horizontal';
	}

	/**
	 * Retrieve Lead Form Styler Widget Keywords.
	 *
	 * @access public
	 *
	 * @return string Widget Keywords.
	 */
	public function get_keywords() {
		return [ 'form', 'lead form'];
	}

	/**
	 * Retrieve Lead Form Styler Widget Category.
	 *
	 * @access public
	 *
	 * @return string Widget Category.
	 */
	public function get_categories() {
		return [ 'lfb-category' ];
	}


	/**
	 * Register Lead Form Styler Widget controls.
	 *
	 * @access protected
	 */
	protected function _register_controls() {

  	$this->lf_styler_general_controls();    
  	
    $this->lf_styler_general_style_controls();  
    $this->lf_styler_label_style_controls();  
  	$this->lf_field_style_controls();
  	$this->lf_radio_checkbox_style_controls();
  	$this->lf_button_style_controls();
}

	protected function lf_styler_general_controls() {

	$this->start_controls_section(
      'general_settings_section',
      [
        'label' => __( 'Lead Form', 'lead-form-builder' ),
        'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
      ]
    );

		$this->add_control(
			'lf_form', // id
			[
				'label' => __( 'Select Form', 'lead-form-builder' ),
				'type' => Controls_Manager::SELECT,
				'options' => 
					$this->lfb_get_lf_forms()	
			]
		);

		$this->end_controls_section();

	}

	protected function lf_styler_general_style_controls() {

		$this->start_controls_section(
	      'general_style_settings_section',
	      [
	        'label' => __( 'Lead Form General Style', 'lead-form-builder' ),
	        'tab' => \Elementor\Controls_Manager::TAB_STYLE,
	      ]
	    );

        $this->add_control(
			'lfb_module_size',
			[
				'label' => __( 'Form Container Size', 'lead-form-builder' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 5,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => '%',
					'size' => 100,
				],
				'selectors' => [
					'{{WRAPPER}} .lead-form-container' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
	      'lfb_styler_alignment',
	      [
	        'label'        => __( 'Form Container Alignment', 'lead-form-builder' ),
	        'type'         => Controls_Manager::CHOOSE,
	        'label_block'  => true,
	        'options'      => [
	          'flex-start'   => [
	            'title' => __( 'Left', 'lead-form-builder' ),
	            'icon'  => 'fa fa-align-left',
	          ],
	          'center' => [
	            'title' => __( 'Center', 'lead-form-builder' ),
	            'icon'  => 'fa fa-align-center',
	          ],
	          'flex-end'  => [
	            'title' => __( 'Right', 'lead-form-builder' ),
	            'icon'  => 'fa fa-align-right',
	          ],
	        ],
	        'selectors'    => [
	          '{{WRAPPER}} .lead-form-wrapper' => 'justify-content: {{VALUE}};',
	        ],
	      ]
	    );

		$this->end_controls_section();

	}

	protected function lf_styler_label_style_controls() {

		//Label Styles
			$this->start_controls_section(
				'lf_field_label',
				[
					'label' => __( 'Label &amp; Title Styling', 'lead-form-builder' ),
					'tab' => Controls_Manager::TAB_STYLE,
				]
			);
			$this->add_control(
				'lf_form_label_color',
				[
					'label' => __( 'Label Color', 'lead-form-builder' ),
					'type' => Controls_Manager::COLOR,
					'scheme' => [
						'type' => Scheme_Color::get_type(),
						'value' => Scheme_Color::COLOR_2,
					],
				'selectors' => [
					'{{WRAPPER}} .lf-field label' => 'color: {{VALUE}};',
				],
				]
			);
			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' => 'lf_form_labels_typography',
					'label'=> __( 'Label Typography', 'lead-form-builder' ),
					'scheme' => Scheme_Typography::TYPOGRAPHY_1,
					'default' => [
						'font_weight' => [
				// Inner control settings
							'default' => '200',
						],
						'font_size' => [
							'default' => '15px',
						],
					],
					'selector' => '{{WRAPPER}} .lf-field label',
				]
			);

		$this->add_control(
            'lf_hide_input_label',
            [
                'label'                 => __( 'Hide All Fields Labels', 'lead-form-builder' ),
                'type'                  => Controls_Manager::SWITCHER,
                'label_on'              => __( 'Yes', 'lead-form-builder' ),
                'label_off'             => __( 'No', 'lead-form-builder' ),
                'default'				=> 'no',
                'return_value'          => 'yes',
            ]
        );	

		$this->add_control(
            'lf_hide_radio_checkbox_label',
            [
                'label'                 => __( 'Hide Radio &amp; Checkbox Labels', 'lead-form-builder' ),
                'type'                  => Controls_Manager::SWITCHER,
                'label_on'              => __( 'Yes', 'lead-form-builder' ),
                'label_off'             => __( 'No', 'lead-form-builder' ),
                'default'				=> 'no',
                'return_value'          => 'yes',
            ]
        );	

		$this->add_control(
            'lf_hide_form_title',
            [
                'label'                 => __( 'Hide Form Title', 'lead-form-builder' ),
                'type'                  => Controls_Manager::SWITCHER,
                'label_on'              => __( 'Yes', 'lead-form-builder' ),
                'label_off'             => __( 'No', 'lead-form-builder' ),
                'default'				=> 'no',
                'return_value'          => 'yes',
                'default'      			=> 'no',
            ]
        );

        $this->add_control(
				'lf_form_title_color',
				[
					'label' => __( 'Form Title Color', 'lead-form-builder' ),
					'type' => Controls_Manager::COLOR,
					'scheme' => [
						'type' => Scheme_Color::get_type(),
						'value' => Scheme_Color::COLOR_2,
					],
				'condition' => [
						'lf_hide_form_title!' => 'yes'
				],
				'selectors' => [
					'{{WRAPPER}} .lead-form-front h1' => 'color: {{VALUE}};',
				],
				]
			);
			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' => 'lf_form_title_typography',
					'label'=> __( 'Form Title Typography', 'lead-form-builder' ),
					'scheme' => Scheme_Typography::TYPOGRAPHY_1,
					'default' => [
						'font_weight' => [
				// Inner control settings
							'default' => '200',
						],
						'font_size' => [
							'default' => '15px',
						],
					],
					'condition' => [
						'lf_hide_form_title!' => 'yes'
					],
					'selector' => '{{WRAPPER}} .lead-form-front h1',
				]
			);
		
			$this->end_controls_section();
	}

protected function lf_field_style_controls() {

	$this->start_controls_section(
		'form_inputs',
		[
			'label' => __( 'Field Styling', 'lead-form-builder' ),
			'tab' => Controls_Manager::TAB_STYLE,
		]
	);

	$this->start_controls_tabs(
		'form_inputs_tabs'
	);

		$this->start_controls_tab(
			'form_inputs_color_typo_tab',
			[
				'label' => __( 'Color', 'lead-form-builder' ),
			]
		);

		$this->add_control(
				'lf_field_color_heading',
				[
					'label'     => __( 'Choose Color', 'lead-form-builder' ),
					'type'      => Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);

			$this->add_control(
				'form_inputs_bg',
				[
					'label' => __( 'Field Background', 'lead-form-builder' ),
					'type' => Controls_Manager::COLOR,
					'default' => '#f9f9f9',
					'scheme' => [
						'type' => Scheme_Color::get_type(),
						'value' => Scheme_Color::COLOR_3,
					],
					'selectors' => [
						'{{WRAPPER}} .lf-field input:not([type=submit]), {{WRAPPER}} .lf-field textarea' => 'background-color: {{VALUE}};',
					],
				]
			);

			$this->add_control(
				'form_inputs_txt_color',
				[
					'label' => __( 'Input Text', 'lead-form-builder' ),
					'type' => Controls_Manager::COLOR,
					'scheme' => [
						'type' => Scheme_Color::get_type(),
						'value' => Scheme_Color::COLOR_2,
					],
					'selectors' => [
						'{{WRAPPER}} .lf-field input:not([type=submit]), 
						{{WRAPPER}} .lf-field textarea' => 'color: {{VALUE}};',

					],
				]
			);

			$this->add_control(
				'form_inputs_placeholder_color',
				[
					'label' => __( 'Placeholder Text', 'lead-form-builder' ),
					'type' => Controls_Manager::COLOR,
					'scheme' => [
						'type' => Scheme_Color::get_type(),
						'value' => Scheme_Color::COLOR_2,
					],
					'selectors' => [
						'{{WRAPPER}} .lf-field ::placeholder' => 'color: {{VALUE}};',

					],
				]
			);

			$this->add_control(
				'form_select_input_text_color',
				[
					'label' => __( 'Select Drop Down Text', 'lead-form-builder' ),
					'type' => Controls_Manager::COLOR,
					'scheme' => [
						'type' => Scheme_Color::get_type(),
						'value' => Scheme_Color::COLOR_2,
					],
					'selectors' => [
						'{{WRAPPER}} .lf-field select' => 'color: {{VALUE}};',
					],
				]
			);

			$this->add_control(
				'form_select_inputs_bg',
				[
					'label' => __( 'Select Drop Down Background', 'lead-form-builder' ),
					'type' => Controls_Manager::COLOR,
					'default' => '#fff',
					'scheme' => [
						'type' => Scheme_Color::get_type(),
						'value' => Scheme_Color::COLOR_3,
					],
					'selectors' => [
						'{{WRAPPER}} .lf-field select' => 'background: {{VALUE}};',
					],
				]
			);			

			$this->add_control(
				'lf_field_typography_heading',
				[
					'label'     => __( 'Typography', 'lead-form-builder' ),
					'type'      => Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'label' => __( 'Field Text', 'lead-form-builder' ),
					'name' => 'form_field_typography',
					'scheme' => Scheme_Typography::TYPOGRAPHY_1,
					'selector' => '{{WRAPPER}} .lf-field input:not([type=submit]), {{WRAPPER}} .lf-field textarea'
				]
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'label' => __( 'Field Placeholder Text', 'lead-form-builder' ),
					'name' => 'form_field_placeholder_typography',
					'scheme' => Scheme_Typography::TYPOGRAPHY_1,
					'selector' => '{{WRAPPER}} .lf-field ::placeholder'
				]
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[	
					'label' => __( 'Select Drop Down Text', 'lead-form-builder' ),
					'name' => 'form_drop_down_typography',
					'scheme' => Scheme_Typography::TYPOGRAPHY_1,
					'selector' => '{{WRAPPER}} .lf-field select',
				]

			);
			
		$this->add_control(
				'lf_field_border_heading',
				[
					'label'     => __( 'Border', 'lead-form-builder' ),
					'type'      => Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);
	
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'form_inputs_border',
				'label' => __( 'Border', 'lead-form-builder' ),
				'selector' => '{{WRAPPER}} .lf-field input:not([type=submit]):not([type="checkbox"]):not([type="radio"]),
						   {{WRAPPER}} .lf-field textarea'
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'form_inputs_dimensions_tab',
			[
				'label' => __( 'Dimensions', 'lead-form-builder' ),
			]
		);


			$this->add_responsive_control(
				'lf_text_align',
				[
					'label'     => __( 'Field Alignment', 'lead-form-builder' ),
					'separator' => 'before',
					'type'      => Controls_Manager::CHOOSE,
					'options'   => [
						'left'   => [
							'title' => __( 'Left', 'lead-form-builder' ),
							'icon'  => 'fa fa-align-left',
						],
						'center' => [
							'title' => __( 'Center', 'lead-form-builder' ),
							'icon'  => 'fa fa-align-center',
						],
						'right'  => [
							'title' => __( 'Right', 'lead-form-builder' ),
							'icon'  => 'fa fa-align-right',
						],
					],
					'selectors' => [
						'{{WRAPPER}} .lead-form-container .leadform-show-form' => 'text-align: {{VALUE}};',
						' {{WRAPPER}} .lead-form-container select' => 'text-align-last:{{VALUE}};',
					],
				]
			);

			$this->add_control(
				'lf_field_spacing_heading',
				[
					'label'     => __( 'Spacing', 'lead-form-builder' ),
					'type'      => Controls_Manager::HEADING,
				]
			);
			$this->add_responsive_control(
				'lf_input_margin_top',
				[
					'label'      => __( 'Between Label &amp; Fields', 'lead-form-builder' ),
					'type'       => Controls_Manager::SLIDER,
					'size_units' => [ 'px', 'em', 'rem' ],
					'range'      => [
						'px' => [
							'min' => 1,
							'max' => 200,
						],
					],
					'default'    => [
						'unit' => 'px',
						'size' => 5,
					],
					'selectors'  => [
						'{{WRAPPER}} .lf-field label' => 'margin-bottom: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'lf_input_margin_bottom',
				[
					'label'      => __( 'Between Fields', 'lead-form-builder' ),
					'type'       => Controls_Manager::SLIDER,
					'size_units' => [ 'px', 'em', 'rem' ],
					'range'      => [
						'px' => [
							'min' => 1,
							'max' => 200,
						],
					],
					'default'    => [
						'unit' => 'px',
						'size' => 10,
					],
					'selectors'  => [
						'{{WRAPPER}} .lf-field' => 'margin-bottom: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_control(
				'date_icon_alignment_heading',
				[
					'label'     => __( 'Calendar Icon Alignment', 'lead-form-builder' ),
					'type'      => Controls_Manager::HEADING,
					'separator' => 'before',
				]
			); 

			$this->add_responsive_control(
				'input_date_icon_alignment',
				[
					'label' => __( 'Bottom to Top', 'lead-form-builder' ),
					'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
					'em' => [
						'min' => 1,
						'max' => 10,
					],
				],
                'default'   => [
                    'size'  => 0,
                    'unit'  => '%'
                ],
					'selectors' => [
						'{{WRAPPER}} .lf-field .lfb-date-icon,
						{{WRAPPER}} .lf-field .lfb_input_upload::before' => 'bottom: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'input_date_icon_left_alignment',
				[
					'label' => __( 'Right to Left', 'lead-form-builder' ),
					'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 250,
					],
					'em' => [
						'min' => 0,
						'max' => 15,
					],
					'%' => [
						'min' => 1,
						'max' => 50,
					],
				],
                'default'   => [
                    'size'  => 0,
                    'unit'  => '%'
                ],
					'selectors' => [
						'{{WRAPPER}} .lf-field .lfb-date-icon' => 'right: {{SIZE}}{{UNIT}};',
					],
				]
			);	



			$this->add_control(
				'lf_field_dimensions_heading',
				[
					'label'     => __( 'Field Dimensions', 'lead-form-builder' ),
					'type'      => Controls_Manager::HEADING,
					'separator' => 'before',
				]
			); 

			$this->add_responsive_control(
				'form_text_inputs_width',
				[
					'label' => __( 'Input Field Width', 'lead-form-builder' ),
					'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', '%' ],
				'range' => [
					'px' => [
						'min' => 10,
						'max' => 1200,
					],
					'em' => [
						'min' => 1,
						'max' => 80,
					],
				],
                'default'   => [
                    'size'  => 100,
                    'unit'  => '%'
                ],
					'selectors' => [
						'{{WRAPPER}} .lf-field ' => 'width: {{SIZE}}{{UNIT}};',
					],
				]
			);

		$this->add_responsive_control(
  			'form_text_inputs_height',
  			[
  				'label' => __( 'Input Field Height', 'lead-form-builder' ),
  				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1200,
					],
					'em' => [
						'min' => 1,
						'max' => 80,
					],
				],
				'selectors' => [
						'{{WRAPPER}} .lf-field input:not([type=submit]):not([type=checkbox]):not([type=radio])' => 'height: {{SIZE}}{{UNIT}};',
					],
  			]
  		); 

		$this->add_responsive_control(
			'form_textarea_inputs_width',
			[
				'label' => __( 'Textarea Width', 'lead-form-builder' ),
				'type' => Controls_Manager::SLIDER,
			'size_units' => [ 'px', 'em', '%' ],
			'range' => [
				'px' => [
					'min' => 10,
					'max' => 1200,
				],
				'em' => [
					'min' => 1,
					'max' => 80,
				],
			],
            'default'   => [
                'size'  => 100,
                'unit'  => '%'
            ],
				'selectors' => [
					'{{WRAPPER}} .lf-field textarea' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'form_textarea_inputs_height',
			[
				'label' => __( 'Textarea Height', 'lead-form-builder' ),
				'type' => Controls_Manager::SLIDER,
			'size_units' => [ 'px', 'em', '%' ],
			'range' => [
				'px' => [
					'min' => 10,
					'max' => 1200,
				],
				'em' => [
					'min' => 1,
					'max' => 80,
				],
			],
			'selectors' => [
					'{{WRAPPER}} .lf-field textarea' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
  		); 

			
	       $this->add_control(
				'form_inputs_border_radius',
				[
					'label' => __( 'Border Radius', 'lead-form-builder' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'selectors' => [
						'{{WRAPPER}} .lf-field input:not([type=submit]):not([type="checkbox"]):not([type="radio"]),
						   {{WRAPPER}} .lf-field textarea,{{WRAPPER}} .lfb-date-icon ' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

	       $this->add_control(
				'lf_field_padding_heading',
				[
					'label'     => __( 'Padding', 'lead-form-builder' ),
					'type'      => Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);

			$this->add_control(
				'form_select_inputs_padding',
				[
					'label' => __( 'Drop-Down Select Text', 'lead-form-builder' ),
					'type' => Controls_Manager::DIMENSIONS,
					'description' => __( 'Padding Around Text', 'lead-form-builder' ),
					'size_units' => [ 'px', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .lf-field select' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_control(
				'lf_field_margin_padding_heading',
				[
					'label'      => __( 'Fields Margin', 'lead-form-builder' ),
					'type'      => Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);

			$this->add_responsive_control(
				'lf_field_margin',
				[
					'label' => __( 'Margin', 'lead-form-builder' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'selectors' => [
						'{{WRAPPER}} .lf-field input:not([type=submit]):not([type="checkbox"]):not([type="radio"]),
						   {{WRAPPER}} .lf-field textarea' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->end_controls_tab();

			$this->end_controls_tabs();
		
			$this->end_controls_section();
	}

	protected function lf_button_style_controls() {

			$this->start_controls_section(
				'form_btn',
				[
					'label' => __( 'Button Styling', 'lead-form-builder' ),
					'tab' => Controls_Manager::TAB_STYLE,
				]
			);

			$this->add_responsive_control(
				'form_btn_padding',
				[
					'label' => __( 'Padding', 'lead-form-builder' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'selectors' => [
						'{{WRAPPER}} .lf-field input[type="submit"]' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);
			$this->add_responsive_control(
				'form_btn_margin',
				[
					'label' => __( 'Margin', 'lead-form-builder' ),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'default' => [
						'top' => 10,
						'right' => 0,
						'bottom' => 0,
						'left' => 0,
						'unit' => 'px'
					],
					'selectors' => [
						'{{WRAPPER}} .lf-field input[type="submit"]' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' => 'form_btn_typography',
					'scheme' => Scheme_Typography::TYPOGRAPHY_1,
					'selector' => '{{WRAPPER}} .lf-field input[type="submit"]',
				]
			);

			$this->add_control(
				'lf_button_dimensions_heading',
				[
					'label'     => __( 'Button Dimensions', 'lead-form-builder' ),
					'type'      => Controls_Manager::HEADING,
					'separator' => 'before',
				]
			); 

			$this->add_responsive_control(
				'form_btn_width',
				[
					'label' => __( 'Button Width', 'lead-form-builder' ),
					'type' => Controls_Manager::SLIDER,
					'default' => [
						'size' => 125,
						'unit' => 'px',
					],
					'tablet_default' => [
						'unit' => '%',
					],
					'mobile_default' => [
						'unit' => '%',
					],
					'size_units' => [ 'px', '%' ],
					'range' => [
						'px' => [
							'min' => 5,
							'max' => 1000,
						],
						'%' => [
							'min' => 5,
							'max' => 100,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .lf-field input[type="submit"]' => 'width: {{SIZE}}{{UNIT}};',
					],
				]
			);
			
			$this->add_responsive_control(
				'form_btn_height',
				[
					'label' => __( 'Button Height', 'lead-form-builder' ),
					'type' => Controls_Manager::SLIDER,
					'tablet_default' => [
						'unit' => 'px',
					],
					'mobile_default' => [
						'unit' => 'px',
					],
					'size_units' => [ 'px', 'em' ],
					'range' => [
						'px' => [
							'min' => 5,
							'max' => 300,
						],
						'em' => [
							'min' => 1,
							'max' => 20,
						],
					],
					'selectors' => [
						'{{WRAPPER}} .lf-field input[type="submit"]' => 'height: {{SIZE}}{{UNIT}};',
					],
				]
			);

		$this->add_responsive_control(
	      'lfb_button_alignment',
	      [
	        'label'        => __( 'Button Alignment', 'lead-form-builder' ),
	        'type'         => Controls_Manager::CHOOSE,
	        'label_block'  => true,
	        'separator' => 'after',
			'options'      => [
	          'left'   => [
	            'title' => __( 'Left', 'lead-form-builder' ),
	            'icon'  => 'fa fa-align-left',
	          ],
	          'center' => [
	            'title' => __( 'Center', 'lead-form-builder' ),
	            'icon'  => 'fa fa-align-center',
	          ],
	          'right'  => [
	            'title' => __( 'Right', 'lead-form-builder' ),
	            'icon'  => 'fa fa-align-right',
	          ],
	        ],
	        'selectors'    => [
	          '{{WRAPPER}} .submit-type.lf-field' => 'text-align: {{VALUE}};',
	        ],
	      ]
	    );


			$this->add_control(
				'lf_button_color_heading',
				[
					'label'      => __( 'Button Color Options', 'lead-form-builder' ),
					'type'      => Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);

			$this->start_controls_tabs( 'tabs_button_style' );

			$this->start_controls_tab(
				'tab_button_normal',
				[
					'label' => __( 'Normal', 'lead-form-builder' ),
				]
			);

				$this->add_control(
					'button_text_color',
					[
						'label'     => __( 'Text Color', 'lead-form-builder' ),
						'type'      => Controls_Manager::COLOR,
						'default'   => '',
						'selectors' => [
							'{{WRAPPER}} .lf-field input[type="submit"]' => 'color: {{VALUE}};',
						],
					]
				);

				$this->add_group_control(
					Group_Control_Background::get_type(),
					[
						'name'           => 'btn_background_color',
						'label'          => __( 'Background Color', 'lead-form-builder' ),
						'types'          => [ 'classic', 'gradient' ],
						'fields_options' => [
							'color' => [
								'scheme' => [
									'type'  => Scheme_Color::get_type(),
									'value' => Scheme_Color::COLOR_4,
								],
								'default' => '#000',
							],
						],
						'selector'       => '{{WRAPPER}} .lf-field input[type="submit"]',
					]
				);

				$this->add_group_control(
					Group_Control_Border::get_type(),
					[
						'name'        => 'btn_border',
						'label'       => __( 'Border', 'lead-form-builder' ),
						'fields_options' => [
							'border' => [
								'default' => 'none',
							],
							'width' => [
								'default' => [
									'top' => '0',
									'right' => '0',
									'bottom' => '0',
									'left' => '0',
									'isLinked' => true,
								],
							],
							'color' => [
								'default' => '#000',
							],
						],
						'selector'    => '{{WRAPPER}} .lf-field input[type="submit"]',
					]
				);

				$this->add_responsive_control(
					'btn_border_radius',
					[
						'label'      => __( 'Border Radius', 'lead-form-builder' ),
						'type'       => Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%' ],
						'selectors'  => [
							'{{WRAPPER}} .lf-field input[type="submit"]' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
					]
				);

				$this->add_group_control(
					Group_Control_Box_Shadow::get_type(),
					[
						'name'     => 'button_box_shadow',
						'selector' => '{{WRAPPER}} .lf-field input[type="submit"]',
					]
				);

			$this->end_controls_tab();

			$this->start_controls_tab(
				'tab_button_hover',
				[
					'label' => __( 'Hover', 'lead-form-builder' ),
				]
			);

				$this->add_control(
					'btn_hover_color',
					[
						'label'     => __( 'Text Color', 'lead-form-builder' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .lf-field input[type="submit"]:hover' => 'color: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'button_hover_border_color',
					[
						'label'     => __( 'Border Hover Color', 'lead-form-builder' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .lf-field input[type="submit"]:hover' => 'border-color: {{VALUE}};',
						],
					]
				);

				$this->add_group_control(
					Group_Control_Background::get_type(),
					[
						'name'     => 'button_background_hover_color',
						'label'    => __( 'Background Color', 'lead-form-builder' ),
						'types'    => [ 'classic', 'gradient' ],
						'selector' => '{{WRAPPER}} .lf-field input[type="submit"]:hover',
					]
				);

			$this->end_controls_tab();

			$this->end_controls_tabs();
			
			$this->end_controls_section();
	}

	protected function lf_radio_checkbox_style_controls() {

		$this->start_controls_section(
			'lf_radio_checkbox_style',
			[
				'label' => __( 'Radio &amp; Checkbox Styling', 'lead-form-builder' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
            'lf_custom_radio_checkbox',
            [
                'label'                 => __( 'Change Default Style', 'lead-form-builder' ),
                'type'                  => Controls_Manager::SWITCHER,
                'label_on'              => __( 'Yes', 'lead-form-builder' ),
                'label_off'             => __( 'No', 'lead-form-builder' ),
                'return_value'          => 'yes',
                'default'      			=> 'no',
            ]
        );

			$this->add_control(
				'lf_radio_checkbox_size',
				[
					'label'      => __( 'Size', 'lead-form-builder' ),
					'type'       => Controls_Manager::SLIDER,
					'size_units' => [ 'px', 'em', 'rem' ],
					'default'    => [
						'unit' => 'px',
						'size' => '16',
					],
					'range'      => [
						'px' => [
							'min' => 1,
							'max' => 50,
						],
					],
					'condition'  => [
						'lf_custom_radio_checkbox' => 'yes',
					],
					'selectors'  => [
						'{{WRAPPER}} .lf-custom-radio-checkbox .lf-field input[type=checkbox]' => 'width: {{SIZE}}{{UNIT}}; height:{{SIZE}}{{UNIT}};',
						'{{WRAPPER}} .lf-custom-radio-checkbox .lf-field input[type=radio]' => 'width: {{SIZE}}{{UNIT}}; height:{{SIZE}}{{UNIT}};'
					],
				]
			);

			$this->add_control(
				'lf_checkbox_check_size',
				[
					'label'      => __( 'Checkbox Check Size', 'lead-form-builder' ),
					'type'       => Controls_Manager::SLIDER,
					'size_units' => [ 'px', 'em', 'rem' ],
					'default'    => [
						'unit' => 'px',
						'size' => '15',
					],
					'range'      => [
						'px' => [
							'min' => 1,
							'max' => 50,
						],
					],
					'condition'  => [
						'lf_custom_radio_checkbox' => 'yes',
					],
					'selectors'  => [
						'{{WRAPPER}} .lf-custom-radio-checkbox .lf-field input[type=checkbox]:checked:before' => 'font-size: {{SIZE}}{{UNIT}};'
					],
				]
			);

			$this->add_control(
				'lf_radio_dot_size',
				[
					'label'      => __( 'Radio Button Dot Size', 'lead-form-builder' ),
					'type'       => Controls_Manager::SLIDER,
					'size_units' => [ 'px', 'em', 'rem' ],
					'default'    => [
						'unit' => 'px',
						'size' => '6',
					],
					'range'      => [
						'px' => [
							'min' => 1,
							'max' => 50,
						],
					],
					'condition'  => [
						'lf_custom_radio_checkbox' => 'yes',
					],
					'selectors'  => [
						'{{WRAPPER}} .lf-custom-radio-checkbox .lf-field input[type=radio]:checked:before' => 'width: {{SIZE}}{{UNIT}}; height:{{SIZE}}{{UNIT}};'
					],
				]
			);

			$this->add_control(
            'lf_radio_checkbox_spacing',
            [
                'label'                 => __( 'Spacing', 'lead-form-builder' ),
                'type'                  => Controls_Manager::HEADING,
                'separator'             => 'before',
				'condition'             => [
					'lf_custom_radio_checkbox' => 'yes',
				],
            ]
        );

			$this->add_responsive_control(
				'lf_radio_checkbox_spacing_left',
				[
					'label'      => __( 'Between Label &amp; Radio or Checkbox', 'lead-form-builder' ),
					'type'       => Controls_Manager::SLIDER,
					'size_units' => [ 'px', 'em', 'rem' ],
					'default'    => [
						'size' => '5',
						'unit' => 'px',
					],
					'range'      => [
						'px' => [
							'min' => 0,
							'max' => 50,
						],
						'em' => [
							'min' => 0,
							'max' => 5,
						],
						'rem' => [
							'min' => 0,
							'max' => 5,
						],
					],
					'condition'  => [
						'lf_custom_radio_checkbox' => 'yes',
					],
					'selectors' => [
						'{{WRAPPER}} .checkbox-type.lf-field ul li input, 
						 {{WRAPPER}} .radio-type.lf-field ul li input,
						 {{WRAPPER}} .lf-field.lfb-terms span input' => 'margin-right: {{SIZE}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'lf_radio_checkbox_spacing_label_left',
				[
					'label'      => __( 'Between Fields', 'lead-form-builder' ),
					'type'       => Controls_Manager::SLIDER,
					'separator'  => 'after',
					'size_units' => [ 'px', 'em', 'rem' ],
					'default'    => [
						'size' => '0',
						'unit' => 'px',
					],
					'range'      => [
						'px' => [
							'min' => 0,
							'max' => 100,
						],
						'em' => [
							'min' => 0,
							'max' => 10,
						],
						'rem' => [
							'min' => 0,
							'max' => 10,
						],
					],
					'condition'  => [
						'lf_custom_radio_checkbox' => 'yes',
					],
					'selectors' => [
						'{{WRAPPER}} .checkbox-type.lf-field ul li, 
						 {{WRAPPER}} .radio-type.lf-field ul li,
						 {{WRAPPER}} .lf-field.lfb-terms span' => 'margin-bottom: {{SIZE}}{{UNIT}};',
					],
				]
			);

		$this->add_control(
            'checkbox_heading',
            [
                'label'                 => __( 'Border Radius', 'lead-form-builder' ),
                'type'                  => Controls_Manager::HEADING,
				'condition'             => [
					'lf_custom_radio_checkbox' => 'yes',
				],
            ]
        );

		$this->add_control(
			'checkbox_border_radius',
			[
				'label'                 => __( 'Checkbox', 'lead-form-builder' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', 'em', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .lf-custom-radio-checkbox .lf-field input[type=checkbox], {{WRAPPER}} .lf-custom-radio-checkbox .lf-field input[type=checkbox]:before' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
                'condition'             => [
                    'lf_custom_radio_checkbox' => 'yes',
                ],
			]
		);
        

		$this->add_control(
			'radio_border_radius',
			[
				'label'                 => __( 'Radio', 'lead-form-builder' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', 'em', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .lf-custom-radio-checkbox .lf-field input[type=radio], {{WRAPPER}} .lf-custom-radio-checkbox .lf-field input[type=radio]:before' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
                'condition'             => [
                    'lf_custom_radio_checkbox' => 'yes',
                ],
			]
		);

		$this->end_controls_section();
	}


	public function lfb_get_lf_forms(){
			$list = array();
			global $wpdb;
			$table_name = $wpdb->prefix.'lead_form';
			$lf_forms = $wpdb->get_results( "SELECT id, form_title FROM $table_name WHERE form_status = 'ACTIVE'");

			foreach ($lf_forms as $form){
					$list[$form->id] = $form->form_title;
			}
			return $list;
	}
	
	protected function render() {
		$settings = $this->get_settings_for_display();

?>   
	<div class="lead-form-wrapper">
		<?php 
			$hide_form_title = ( $settings['lf_hide_form_title'] == 'yes' ) ? ' hide-form-title' : '';
			$lf_hide_input_label = ( $settings['lf_hide_input_label'] == 'yes' ) ? ' hide-input-label' : '';
			$lf_hide_radio_checkbox_label = ( $settings['lf_hide_radio_checkbox_label'] == 'yes' ) ? ' hide-radio-checkbox-label' : '';
		?>
	    <div class="lead-form-container<?php echo $hide_form_title . $lf_hide_input_label . $lf_hide_radio_checkbox_label; ?>">
	    	
	    	<?php 
	    	// Check if form is selected 
			    	if ( $settings['lf_form'] == '' ) { 
			    			echo '<p class="select-lf-form">' ."Please select a form".'</p>';
			    	}
			    	if ( $settings['lf_custom_radio_checkbox'] == 'yes' ) { ?>
			         		<div class="lf-custom-radio-checkbox">
		     		<?php } ?>
					<?php echo do_shortcode( '[lead-form form-id='.$settings["lf_form"].']' ); 
			    		if ( $settings['lf_custom_radio_checkbox'] == 'yes' ) { ?>
			         		</div>
			     	<?php } ?>
		</div>
	</div>	
<?php	
	}
	
	protected function _content_template() {}
}


Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Lead_Form_Styler() );