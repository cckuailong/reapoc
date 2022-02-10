<?php

/**
 * @class PPContactForm7Module
 */
class PPContactForm7Module extends FLBuilderModule {

    /**
     * Constructor function for the module. You must pass the
     * name, description, dir and url in an array to the parent class.
     *
     * @method __construct
     */
    public function __construct()
    {
        parent::__construct(array(
            'name'          => __('Contact Form 7', 'bb-powerpack-lite'),
            'description'   => __('A module for Contact Form 7.', 'bb-powerpack-lite'),
            'group'         => pp_get_modules_group(),
            'category'		=> pp_get_modules_cat( 'form_style' ),
            'dir'           => BB_POWERPACK_DIR . 'modules/pp-contact-form-7/',
            'url'           => BB_POWERPACK_URL . 'modules/pp-contact-form-7/',
            'editor_export' => true, // Defaults to true and can be omitted.
            'enabled'       => true, // Defaults to true and can be omitted.
        ));
	}
	public function filter_settings( $settings, $helper ) {

		// Handle Form old padding field.
		$settings = PP_Module_Fields::handle_multitext_field( $settings, 'form_padding', 'padding', 'form_padding' );

		// Handle old Form border and radius fields.
		$settings = PP_Module_Fields::handle_border_field( $settings, array(
			'form_border_style'	=> array(
				'type'				=> 'style',
			),
			'form_border_width'	=> array(
				'type'				=> 'width',
			),
			'form_border_color'	=> array(
				'type'				=> 'color',
			),
			'form_border_radius'	=> array(
				'type'				=> 'radius',
			),
		), 'form_border_group' );

		// Handle old Button border and radius fields.
		$settings = PP_Module_Fields::handle_border_field( $settings, array(
			'button_border_width'	=> array(
				'type'				=> 'width',
			),
			'button_border_color'	=> array(
				'type'				=> 'color',
			),
			'button_border_radius'	=> array(
				'type'				=> 'radius',
			),
		), 'button_border_group' );

		// Handle old Validation Error border and radius fields.
		$settings = PP_Module_Fields::handle_border_field( $settings, array(
			'form_error_field_border_type'	=> array(
				'type'				=> 'style',
			),
			'form_error_field_border_width'	=> array(
				'type'				=> 'width',
			),
			'form_error_field_border_color'	=> array(
				'type'				=> 'color',
			),
		), 'form_error_field_border_group' );

		// Handle Description's old typography fields.
		$settings = PP_Module_Fields::handle_typography_field( $settings, array(
			'title_font_family'	=> array(
				'type'			=> 'font'
			),
			'title_font_size'	=> array(
				'type'          => 'font_size',
			),
			'title_alignment'	=> array(
				'type'          => 'text_align',
			),
		), 'title_typography' );

		// Handle Description's old typography fields.
		$settings = PP_Module_Fields::handle_typography_field( $settings, array(
			'description_font_family'	=> array(
				'type'			=> 'font'
			),
			'description_font_size'	=> array(
				'type'          => 'font_size',
			),
			'description_alignment'	=> array(
				'type'          => 'text_align',
			),
		), 'description_typography' );

		// Handle Label's old typography fields.
		$settings = PP_Module_Fields::handle_typography_field( $settings, array(
			'label_font_family'	=> array(
				'type'			=> 'font'
			),
			'label_font_size'	=> array(
				'type'          => 'font_size',
			),
		), 'label_typography' );

		// Handle Input's old typography fields.
		$settings = PP_Module_Fields::handle_typography_field( $settings, array(
			'input_font_family'	=> array(
				'type'			=> 'font'
			),
			'input_font_size'	=> array(
				'type'          => 'font_size',
			),
		), 'input_typography' );

		// Handle Button's old typography fields.
		$settings = PP_Module_Fields::handle_typography_field( $settings, array(
			'button_font_family'	=> array(
				'type'			=> 'font'
			),
			'button_font_size'	=> array(
				'type'          => 'font_size',
			),
		), 'button_typography' );

		// Handle Form Background opacity + color field.
        if ( isset( $settings->form_bg_opacity ) ) {
            $opacity = $settings->form_bg_opacity >= 0 ? $settings->form_bg_opacity : 1;
            $color = $settings->form_bg_color;

            if ( ! empty( $color ) ) {
                $color = pp_hex2rgba( pp_get_color_value( $color ), $opacity );
                $settings->form_bg_color = $color;
            }

            unset( $settings->form_bg_opacity );
		}

		return $settings;
	}
}

require_once BB_POWERPACK_DIR . 'modules/pp-contact-form-7/includes/functions.php';

/**
 * Register the module and its form settings.
 */
FLBuilder::register_module('PPContactForm7Module', array(
    'form'       => array(
        'title'         => __('Form', 'bb-powerpack-lite'), // Tab title
        'sections'      => array( // Tab Sections
            'select_form'       => array( // Section
                'title'         => '', // Section Title
                'fields'        => array( // Section Fields
                    'select_form_field' => array(
                        'type'          => 'select',
                        'label'         => __('Select Form', 'bb-powerpack-lite'),
                        'default'       => '',
                        'options'       => cf7_module_form_titles()
                    ),
                    'custom_title'      => array(
                        'type'          => 'text',
                        'label'         => __('Custom Title', 'bb-powerpack-lite'),
                        'default'       => '',
                        'description'   => '',
                        'connections'   => array('string'),
						'preview'       => array(
                            'type'      => 'text',
                            'selector'  => '.pp-cf7-form-title'
                        )
                    ),
                    'custom_description'    => array(
                        'type'              => 'textarea',
                        'label'             => __('Custom Description', 'bb-powerpack-lite'),
                        'default'           => '',
                        'placeholder'       => '',
                        'rows'              => '6',
                        'connections'       => array('string', 'html'),
                        'preview'           => array(
                            'type'          => 'text',
                            'selector'      => '.pp-cf7-form-description'
                        )
                    ),
                )
            ),
        )
    ),
    'style'       => array( // Tab
        'title'         => __('Style', 'bb-powerpack-lite'), // Tab title
        'sections'      => array( // Tab Sections
            'form_setting'	=> array( // Section
                'title'         => __('Form Background', 'bb-powerpack-lite'), // Section Title
                'fields'        => array( // Section Fields
                    'form_bg_type'      => array(
                        'type'          => 'pp-switch',
                        'label'         => __('Background Type', 'bb-powerpack-lite'),
                        'default'       => 'color',
                        'options'       => array(
                            'color'     => __('Color', 'bb-powerpack-lite'),
                            'image'     => __('Image', 'bb-powerpack-lite'),
                        ),
                        'toggle'        => array(
                            'color'     => array(
                                'fields'    => array('form_bg_color', 'form_bg_opacity'),
                            ),
                            'image'     => array(
                                'fields'    => array('form_bg_image', 'form_bg_size', 'form_bg_repeat'),
                            ),
                        ),
                    ),
                    'form_bg_color'     => array(
                        'type'          => 'color',
                        'label'         => __('Background Color', 'bb-powerpack-lite'),
                        'default'       => 'ffffff',
                        'show_reset'    => true,
                        'show_alpha'    => true,
                        'preview'       => array(
                            'type'      => 'css',
                            'selector'  => '.pp-cf7-content',
                            'property'  => 'background-color'
                        )
                    ),
                    'form_bg_image'     => array(
                        'type'              => 'photo',
                        'label'         => __('Background Image', 'bb-powerpack-lite'),
                        'default'       => '',
                        'preview'       => array(
                            'type'      => 'css',
                            'selector'  => '.pp-cf7-content',
                            'property'  => 'background-image'
                        )
                    ),
                    'form_bg_size'      => array(
                        'type'          => 'pp-switch',
                        'label'         => __('Background Size', 'bb-powerpack-lite'),
                        'default'       => 'false',
                        'options'       => array(
                            'contain'   => __('Contain', 'bb-powerpack-lite'),
                            'cover'     => __('Cover', 'bb-powerpack-lite'),
                        )
                    ),
                    'form_bg_repeat'    => array(
                        'type'          => 'pp-switch',
                        'label'         => __('Background Repeat', 'bb-powerpack-lite'),
                        'default'       => 'no-repeat',
                        'options'       => array(
                            'repeat-x'      => __('Repeat X', 'bb-powerpack-lite'),
                            'repeat-y'      => __('Repeat Y', 'bb-powerpack-lite'),
                            'no-repeat'     => __('No Repeat', 'bb-powerpack-lite'),
                        )
                    ),
                )
            ),
            'form_border'	=> array(
				'title'             => __('Form Border', 'bb-powerpack-lite'),
				'collapsed'			=> true,
                'fields'            => array(
                    'form_show_border'      => array(
                        'type'          => 'pp-switch',
                        'label'         => __('Show Border', 'bb-powerpack-lite'),
                        'default'       => 'no',
                        'options'       => array(
                            'yes'        => __('Yes', 'bb-powerpack-lite'),
                            'no'        => __('No', 'bb-powerpack-lite'),
                        ),
                        'toggle'        => array(
                            'yes'       => array(
                                'fields'    => array('form_border_group'),
                            ),
                        ),
					),
					'form_border_group'	=> array(
						'type'					=> 'border',
						'label'					=> __('Border Style', 'bb-powerpack-lite'),
						'responsive'			=> true,
						'preview'				=> array(
							'type'					=> 'css',
							'selector'				=> '.pp-cf7-content',
						)
					),
                )
            ),
            'form_corners'	=> array(
				'title'			=> __('Padding', 'bb-powerpack-lite'),
				'collapsed'		=> true,
                'fields'		=> array(
                    'form_padding'	=> array(
                        'type'          => 'unit',
                        'label'         => __('Padding', 'bb-powerpack-lite'),
                        'units'			=> array('px'),
                        'slider'		=> true,
                        'default'       => 10,
                        'preview'       => array(
                            'type'      => 'css',
                            'selector'  => '.pp-cf7-content',
                            'property'  => 'padding',
                            'unit'      => 'px'
                        )
                    ),
                )
            ),
        )
    ),
    'input_style'           => array(
        'title'                 => __('Inputs', 'bb-powerpack-lite'),
        'sections'              => array(
            'input_style'           => array( // Section
                'title'                 => __('Colors', 'bb-powerpack-lite'), // Section Title
                'fields'                => array( // Section Fields
                    'input_field_text_color'	=> array(
                        'type'                      => 'color',
                        'label'                     => __('Text Color', 'bb-powerpack-lite'),
                        'default'                   => '',
                        'show_reset'                => true,
                        'preview'                   => array(
                            'type'                      => 'css',
                            'selector'                  => '.pp-cf7-content input.wpcf7-text, .pp-cf7-content .wpcf7-textarea, .pp-cf7-content .wpcf7-quiz, .pp-cf7-content .wpcf7-number, .pp-cf7-content .wpcf7-date,.pp-cf7-content .wpcf7-file',
                            'property'                  => 'color'
                        )
                    ),
                    'input_field_bg_color'		=> array(
                        'type'                  => 'color',
                        'label'                 => __('Background Color', 'bb-powerpack-lite'),
                        'default'               => '',
                        'show_reset'            => true,
                        'show_alpha'            => true,
                        'preview'               => array(
                            'type'                  => 'css',
                            'selector'              => '.pp-cf7-content input.wpcf7-text, .pp-cf7-content .wpcf7-textarea, .pp-cf7-content .wpcf7-quiz, .pp-cf7-content .wpcf7-number, .pp-cf7-content .wpcf7-date,.pp-cf7-content .wpcf7-file',
                            'property'              => 'background-color'
                        )
                    ),
                )
            ),
            'input_sizes'			=> array(
				'title'                     => __('Sizes & Padding', 'bb-powerpack-lite'),
				'collapsed'					=> true,
                'fields'                    => array(
					'input_width'              => array(
						'type'                      => 'unit',
						'label'                     => __('Input Width', 'bb-powerpack-lite'),
						'units'						=> array('%'),
						'slider'					=> true,
						'preview'                   => array(
							'type'                      => 'css',
							'selector'                  => '.pp-cf7-content input.wpcf7-text, .pp-cf7-content .wpcf7-quiz, .pp-cf7-content .wpcf7-number, .pp-cf7-content .wpcf7-date, .pp-cf7-content .wpcf7-file',
							'property'                  => 'width',
							'unit'                      => '%'
						),
					),
                    'input_height'              => array(
                        'type'                      => 'unit',
						'label'                     => __('Input Height', 'bb-powerpack-lite'),
						'units'						=> array('px'),
						'slider'					=> true,
                        'preview'                   => array(
                            'type'                      => 'css',
                            'selector'                  => '.pp-cf7-content input.wpcf7-text, .pp-cf7-content .wpcf7-quiz, .pp-cf7-content .wpcf7-number, .pp-cf7-content .wpcf7-date, .pp-cf7-content .wpcf7-file',
                            'property'                  => 'height',
                            'unit'                      => 'px'
                        ),
                    ),
                    'textarea_height'           => array(
                        'type'                      => 'unit',
                        'label'                     => __('Textarea Height', 'bb-powerpack-lite'),
						'units'						=> array('px'),
						'slider'					=> true,
                        'default'                   => 200,
                        'preview'                   => array(
                            'type'                      => 'css',
                            'selector'                  => '.pp-cf7-content .wpcf7-textarea',
                            'property'                  => 'height',
                            'unit'                      => 'px'
                        ),
                    ),
                    'input_field_padding'       => array(
                        'type'                      => 'unit',
                        'label'                     => __('Padding', 'bb-powerpack-lite'),
						'units'						=> array('px'),
						'slider'					=> true,
						'default'                   => 12,
                        'preview'                   => array(
                            'type'                      => 'css',
                            'selector'                  => '.pp-cf7-content input.wpcf7-text, .pp-cf7-content .wpcf7-textarea, .pp-cf7-content .wpcf7-quiz, .pp-cf7-content .wpcf7-number, .pp-cf7-content .wpcf7-date,.pp-cf7-content .wpcf7-file',
                            'property'                  => 'padding',
                            'unit'                      => 'px'
                        )
                    ),
                    'input_field_margin_top'    => array(
                        'type'                      => 'unit',
                        'label'                     => __('Margin Top', 'bb-powerpack-lite'),
						'units'						=> array('px'),
						'slider'					=> true,
                        'default'                   => 5,
                        'preview'                   => array(
                            'type'                      => 'css',
                            'selector'                  => '.pp-cf7-content input.wpcf7-text, .pp-cf7-content .wpcf7-textarea, .pp-cf7-content .wpcf7-quiz, .pp-cf7-content .wpcf7-number, .pp-cf7-content .wpcf7-date,.pp-cf7-content .wpcf7-file',
                            'property'                  => 'margin-top',
                            'unit'                      => 'px'
                        )
                    ),
                    'input_field_margin'        => array(
                        'type'                      => 'unit',
                        'label'                     => __('Margin Bottom', 'bb-powerpack-lite'),
						'units'						=> array('px'),
						'slider'					=> true,
                        'default'                   => 10,
                        'preview'                   => array(
                            'type'                      => 'css',
                            'selector'                  => '.pp-cf7-content input.wpcf7-text, .pp-cf7-content .wpcf7-textarea, .pp-cf7-content .wpcf7-quiz, .pp-cf7-content .wpcf7-number, .pp-cf7-content .wpcf7-date,.pp-cf7-content .wpcf7-file',
                            'property'                  => 'margin-bottom',
                            'unit'                      => 'px'
                        )
                    ),
                )
            ),
            'input_border'			=> array(
				'title'                     => __('Border', 'bb-powerpack-lite'),
				'collapsed'					=> true,
                'fields'                    => array(
                    'input_field_border_width'  => array(
                        'type'                      => 'unit',
                        'label'                     => __('Border Width', 'bb-powerpack-lite'),
						'units'						=> array('px'),
						'slider'					=> true,
                        'default'                   => 1,
                    ),
                    'input_field_border_color'  => array(
                        'type'                      => 'color',
                        'label'                     => __('Border Color', 'bb-powerpack-lite'),
                        'default'                   => '',
                        'show_reset'                => true,
                        'preview'                   => array(
                            'type'                      => 'css',
                            'selector'                  => '.pp-cf7-content input.wpcf7-text, .pp-cf7-content .wpcf7-textarea, .pp-cf7-content .wpcf7-quiz, .pp-cf7-content .wpcf7-number, .pp-cf7-content .wpcf7-date,.pp-cf7-content .wpcf7-file',
                            'property'                  => 'border-color'
                        )
                    ),
                    'input_field_border_focus'  => array(
                        'type'                      => 'color',
                        'label'                     => __('Border Color Focus', 'bb-powerpack-lite'),
                        'default'                   => '',
                        'show_reset'                => true,
                    ),
                    'input_field_border_position'   => array(
                        'type'                          => 'select',
                        'label'                         => __('Border Position', 'bb-powerpack-lite'),
                        'default'                       => 'border',
                        'options'				        => array(
                        	'border'			            => __('Default', 'bb-powerpack-lite'),
                        	'border-top'		            => __('Top', 'bb-powerpack-lite'),
                        	'border-bottom'		            => __('Bottom', 'bb-powerpack-lite'),
                        	'border-left'                   => __('Left', 'bb-powerpack-lite'),
                        	'border-right'		            => __('Right', 'bb-powerpack-lite'),
                        ),
                    ),
                )
            ),
            'input_general'			=> array(
				'title'                     => __('General', 'bb-powerpack-lite'),
				'collapsed'					=> true,
                'fields'                    => array(
                    'input_field_border_radius' => array(
                        'type'                      => 'unit',
                        'label'                     => __('Round Corners', 'bb-powerpack-lite'),
						'units'						=> array('px'),
						'slider'					=> true,
                        'default'                   => 2,
                        'preview'                   => array(
                            'type'                      => 'css',
                            'selector'                  => '.pp-cf7-content input.wpcf7-text, .pp-cf7-content .wpcf7-textarea, .pp-cf7-content .wpcf7-quiz, .pp-cf7-content .wpcf7-number, .pp-cf7-content .wpcf7-date,.pp-cf7-content .wpcf7-file',
                            'property'                  => 'border-radius',
                            'unit'                      => 'px'
                        )
                    ),
                    'input_field_box_shadow'    => array(
                        'type'                      => 'pp-switch',
                        'label'                     => __('Enable Box Shadow', 'bb-powerpack-lite'),
                        'default'                   => 'no',
                        'options'                   => array(
                            'yes'                       => __('Yes', 'bb-powerpack-lite'),
                            'no'                        => __('No', 'bb-powerpack-lite'),
                        ),
                        'toggle'                    => array(
                            'yes'                       => array(
                                'fields'                    => array('shadow_color', 'shadow_direction')
                            ),
                        ),
                    ),
                    'shadow_color'              => array(
                        'type'                      => 'color',
                        'label'                     => __('Shadow Color', 'bb-powerpack-lite'),
                        'show_reset'                => true,
                        'preview'                   => array(
                            'type'                      => 'css',
                            'selector'                  => '.pp-cf7-content input.wpcf7-text, .pp-cf7-content .wpcf7-textarea, .pp-cf7-content .wpcf7-quiz, .pp-cf7-content .wpcf7-number, .pp-cf7-content .wpcf7-date,.pp-cf7-content .wpcf7-file',
                            'property'                  => 'box-shadow'
                        ),
                    ),
                    'shadow_direction'          => array(
                        'type'                      => 'pp-switch',
                        'label'                     => __('Shadow Direction', 'bb-powerpack-lite'),
                        'default'                   => 'out',
                        'options'                   => array(
                            'out'                       => __('Outside', 'bb-powerpack-lite'),
                            'inset'                     => __('Inside', 'bb-powerpack-lite'),
                        ),
                    ),
                )
            ),
            'placeholder_style'		=> array( // Section
				'title'         => __('Placeholder', 'bb-powerpack-lite'), // Section Title
				'collapsed'					=> true,
                'fields'        => array( // Section Fields
                    'show_placeholder' 	=> array(
                        'type'          => 'pp-switch',
                        'label'         => __('Show Placeholder', 'bb-powerpack-lite'),
                        'default'       => 'yes',
                        'options'		=> array(
                       		'yes'	         => __('Yes', 'bb-powerpack-lite'),
                       		'no'	         => __('No', 'bb-powerpack-lite'),
                        ),
                        'toggle' => array(
                            'yes' => array(
                                'fields' => array('placeholder_color')
                            )
                        )
                    ),
                    'placeholder_color'  => array(
                        'type'                  => 'color',
                        'label'                 => __('Color', 'bb-powerpack-lite'),
                        'default'               => '999999',
                        'show_reset'            => true,
                        'preview'               => array(
                            'type'              => 'css',
                            'selector'          => '.pp-cf7-content input[type=text]::-webkit-input-placeholder, .pp-cf7-content input[type=tel]::-webkit-input-placeholder, .pp-cf7-content input[type=email]::-webkit-input-placeholder, .pp-cf7-content textarea::-webkit-input-placeholder',
                            'property'          => 'color'
                        )
                    ),
                )
            ),
        )
    ),
    'button_style'          => array(
        'title'                 => __('Button', 'bb-powerpack-lite'),
        'sections'              => array(
            'button_settings'       => array( // Section
                'title'                 => __('Colors', 'bb-powerpack-lite'), // Section Title
                'fields'                => array( // Section Fields
                    'button_bg_color'       => array(
                        'type'                  => 'color',
                        'label'                 => __('Background Color', 'bb-powerpack-lite'),
                        'default'               => '',
                        'show_reset'            => true,
                        'show_alpha'            => true,
                        'preview'               => array(
                            'type'                  => 'css',
                            'selector'              => '.pp-cf7-content .wpcf7-submit',
                            'property'              => 'background-color'
                        )
                    ),
                    'button_hover_bg_color' => array(
                        'type'                  => 'color',
                        'label'                 => __('Background Color Hover', 'bb-powerpack-lite'),
                        'default'               => '',
                        'show_reset'            => true,
                        'show_alpha'            => true,
                        'preview'               => array(
                            'type'                  => 'css',
                            'selector'              => '.pp-cf7-content .wpcf7-submit:hover',
                            'property'              => 'background-color'
                        )
                    ),
                    'button_text_color'     => array(
                        'type'                  => 'color',
                        'label'                 => __('Text Color', 'bb-powerpack-lite'),
                        'default'               => '',
                        'show_reset'            => true,
                        'preview'               => array(
                            'type'                  => 'css',
                            'selector'              => '.pp-cf7-content .wpcf7-submit',
                            'property'              => 'color'
                        )
                    ),
                    'button_hover_text_color'   => array(
                        'type'                      => 'color',
                        'label'                     => __('Text Color Hover', 'bb-powerpack-lite'),
                        'default'                   => '',
                        'show_reset'                => true,
                        'preview'                   => array(
                            'type'                      => 'css',
                            'selector'                  => '.pp-cf7-content .wpcf7-submit:hover',
                            'property'                  => 'color'
                        )
                    ),
                )
            ),
            'button_size'           => array(
				'title'                 => __('Sizes & Alignment', 'bb-powerpack-lite'),
				'collapsed'				=> true,
                'fields'                => array(
                    'button_width'          => array(
                        'type'                  => 'pp-switch',
                        'label'                 => __('Full Width', 'bb-powerpack-lite'),
                        'default'               => 'false',
                        'options'               => array(
                            'true'                  => __('Yes', 'bb-powerpack-lite'),
                            'false'                 => __('No', 'bb-powerpack-lite'),
                        ),
                        'toggle'            => array(
                            'false'             => array(
                                'fields'            => array('button_width_size', 'button_alignment')
                            ),
                        ),
                    ),
                    'button_width_size'     => array(
                        'type'                  => 'unit',
                        'label'                 => __('Button Width', 'bb-powerpack-lite'),
                        'units'           		=> array('px'),
                        'slider'				=> true,
                        'default'               => '',
                        'preview'               => array(
                            'type'                  => 'css',
                            'selector'              => '.pp-cf7-content .wpcf7-submit',
                            'property'              => 'width',
                            'unit'                  => 'px'
                        )
                    ),
                    'button_alignment'      => array(
                        'type'                  => 'align',
                        'label'                 => __('Button Alignment', 'bb-powerpack-lite'),
                        'default'               => 'none',
                    ),
                )
            ),
            'button_corners'        => array(
				'title'                 => __('Padding', 'bb-powerpack-lite'),
				'collapsed'				=> true,
                'fields'                => array(
                    'button_padding_top_bottom' => array(
                        'type'                      => 'unit',
                        'label'                     => __('Top/Bottom Padding', 'bb-powerpack-lite'),
                        'units'						=> array('px'),
                        'slider'                    => true,
                        'default'                   => '',
                        'preview'                   => array(
                            'type'                      => 'css',
                            'rules'                     => array(
                                array(
                                    'selector'              => '.pp-cf7-content .wpcf7-submit',
                                    'property'              => 'padding-top',
                                    'unit'                  => 'px'
                                ),
                                array(
                                    'selector'              => '.pp-cf7-content .wpcf7-submit',
                                    'property'              => 'padding-bottom',
                                    'unit'                  => 'px'
                                ),
                            ),
                        )
                    ),
                    'button_padding_left_right' => array(
                        'type'                      => 'unit',
                        'label'                     => __('Left/Right Padding', 'bb-powerpack-lite'),
                        'units'						=> array('px'),
                        'slider'                    => true,
                        'default'                   => '',
                        'preview'                   => array(
                            'type'                      => 'css',
                            'rules'                     => array(
                                array(
                                    'selector'              => '.pp-cf7-content .wpcf7-submit',
                                    'property'              => 'padding-left',
                                    'unit'                  => 'px'
                                ),
                                array(
                                    'selector'              => '.pp-cf7-content .wpcf7-submit',
                                    'property'              => 'padding-right',
                                    'unit'                  => 'px'
                                ),
                            ),
                        )
                    ),
                )
            ),
            'button_border'         => array(
				'title'                 => __('Border', 'bb-powerpack-lite'),
				'collapsed'				=> true,
                'fields'                => array(
					'button_border_group'	=> array(
						'type'					=> 'border',
						'label'					=> __('Border Style', 'bb-powerpack-lite'),
						'responsive'			=> true,
						'preview'				=> array(
							'type'					=> 'css',
							'selector'				=> '.pp-cf7-content .wpcf7-submit',
						)
					),
                    'button_border_color_hover' => array(
                        'type'                      => 'color',
                        'label'                     => __('Border Color Hover', 'bb-powerpack-lite'),
                        'default'                   => '',
                        'show_reset'                => true,
                        'preview'                   => array(
                            'type'                      => 'css',
                            'selector'                  => '.pp-cf7-content .wpcf7-submit:hover',
                            'property'                  => 'border-color'
                        )
                    ),
                )
            ),
        )
    ),
    'error_style'           => array(
        'title'                 => __('Errors', 'bb-powerpack-lite'),
        'sections'              => array(
            'form_error_styling'    => array( // Section
                'title'                 => __('Errors Style', 'bb-powerpack-lite'), // Section Title
                'fields'                => array( // Section Fields
                    'validation_error'      => array(
                        'type'              => 'pp-switch',
                        'label'             => __('Validation Error', 'bb-powerpack-lite'),
                        'default'           => 'block',
                        'options'           => array(
                            'block'             => __('Show', 'bb-powerpack-lite'),
                            'none'              => __('Hide', 'bb-powerpack-lite'),
                        ),
                        'toggle'            => array(
                            'block'             => array(
                                'fields'        => array('validation_error_color', 'validation_error_font_size', 'form_error_field_background_color', 'form_error_field_border_color', 'form_error_field_border_type', 'form_error_field_border_width'),
                            ),
                        ),
                    ),
					'validation_error_color'   => array(
                        'type'                      => 'color',
                        'label'                     => __('Validation Error Color', 'bb-powerpack-lite'),
                        'default'                   => '000000',
                        'show_reset'                => true,
                        'preview'                   => array(
                            'type'                      => 'css',
                            'selector'                  => '.wpcf7-response-output',
                            'property'                  => 'color'
                        )
                    ),
                    'validation_error_font_size'    => array(
                        'type'                          => 'unit',
                        'label'                         => __('Validation Error Font Size', 'bb-powerpack-lite'),
                        'units'							=> array('px'),
                        'slider'						=> true,
                        'default'                       => '',
                        'preview'                       => array(
                            'type'                          => 'css',
                            'selector'                      => '.wpcf7-response-output',
                            'property'                      => 'font-size',
                            'unit'                          => 'px'
                        )
                    ),
                    'form_error_field_background_color' => array(
                        'type'                              => 'color',
                        'label'                             => __('Validation Error Background Color', 'bb-powerpack-lite'),
                        'default'                           => 'ffffff',
                        'show_reset'                        => true,
                        'show_alpha'                        => true,
                        'preview'                           => array(
                            'type'                              => 'css',
                            'selector'                          => '.wpcf7-response-output',
                            'property'                          => 'color'
                        )
					),
					'form_error_field_border_group'	=> array(
						'type'					=> 'border',
						'label'					=> __('Validation Error Border Style', 'bb-powerpack-lite'),
						'responsive'			=> true,
						'preview'				=> array(
							'type'					=> 'css',
							'selector'				=> '.wpcf7-response-output',
						)
					),
					'validation_message'   => array(
                        'type'                 => 'pp-switch',
                        'label'                => __('Error Field Message', 'bb-powerpack-lite'),
                        'default'              => 'true',
                        'options'              => array(
                            'block'            => __('Show', 'bb-powerpack-lite'),
                            'none'             => __('Hide', 'bb-powerpack-lite'),
                        ),
                        'toggle'               => array(
                            'block'                => array(
                                'fields'           => array('validation_message_color'),
                            ),
                        ),
                    ),
					'validation_message_color' => array(
                        'type'                    => 'color',
                        'label'                   => __('Error Field Label Color', 'bb-powerpack-lite'),
                        'default'                 => 'ff0000',
                        'show_reset'              => true,
                        'preview'                 => array(
                            'type'                    => 'css',
                            'selector'                => '.wpcf7-not-valid-tip',
                            'property'                => 'color'
                        )
                    ),
                )
            ),
        )
    ),
    'form_typography'       => array( // Tab
        'title'         => __('Typography', 'bb-powerpack-lite'), // Tab title
        'sections'      => array( // Tab Sections
            'title_typography'       => array( // Section
                'title'         => __('Title', 'bb-powerpack-lite'), // Section Title
				'fields'        => array( // Section Fields
					'title_typography'	=> array(
						'type'        	   => 'typography',
						'label'       	   => __( 'Typography', 'bb-powerpack-lite' ),
						'responsive'  	   => true,
						'preview'          => array(
							'type'         		=> 'css',
							'selector' 		    => '.pp-cf7-form-title',
						),
					),
                    'title_color'       => array(
                        'type'          => 'color',
                        'label'         => __('Color', 'bb-powerpack-lite'),
                        'default'       => '',
                        'show_reset'    => true,
                        'preview'       => array(
                            'type'      => 'css',
                            'selector'  => '.pp-cf7-form-title',
                            'property'  => 'color'
                        )
                    ),
                )
            ),
            'description_typography'    => array(
				'title'     => __('Description', 'bb-powerpack-lite'),
				'collapsed'	=> true,
                'fields'    => array(
					'description_typography'	=> array(
						'type'        	   => 'typography',
						'label'       	   => __( 'Typography', 'bb-powerpack-lite' ),
						'responsive'  	   => true,
						'preview'          => array(
							'type'         		=> 'css',
							'selector' 		    => '.pp-cf7-form-description'
						),
					),
                    'description_color' => array(
                        'type'          => 'color',
                        'label'         => __('Color', 'bb-powerpack-lite'),
                        'default'       => '',
                        'show_reset'    => true,
                        'preview'       => array(
                            'type'      => 'css',
                            'selector'  => '.pp-cf7-form-description',
                            'property'  => 'color'
                        )
                    ),
                ),
            ),
            'label_typography'       => array( // Section
				'title'         => __('Label', 'bb-powerpack-lite'), // Section Title
				'collapsed'		=> true,
				'fields'        => array( // Section Fields
					'label_typography'	=> array(
						'type'        	   => 'typography',
						'label'       	   => __( 'Typography', 'bb-powerpack-lite' ),
						'responsive'  	   => true,
						'preview'          => array(
							'type'         		=> 'css',
							'selector' 		    => '.pp-cf7-content form p'
						),
					),
                    'form_label_color'  => array(
                        'type'          => 'color',
                        'label'         => __('Color', 'bb-powerpack-lite'),
                        'default'       => '',
                        'show_reset'    => true,
                        'preview'       => array(
                            'type'      => 'css',
                            'selector'  => '.pp-cf7-content form p',
                            'property'  => 'color'
                        )
                    ),
                )
            ),
            'input_typography'       => array( // Section
				'title'         => __('Input', 'bb-powerpack-lite'), // Section Title
				'collapsed'		=> true,
				'fields'        => array( // Section Fields
					'input_typography'	=> array(
						'type'        	   => 'typography',
						'label'       	   => __( 'Typography', 'bb-powerpack-lite' ),
						'responsive'  	   => true,
						'preview'          => array(
							'type'         		=> 'css',
							'selector' 		    => '.pp-cf7-content input.wpcf7-text, .pp-cf7-content .wpcf7-textarea, .pp-cf7-content .wpcf7-quiz, .pp-cf7-content .wpcf7-number, .pp-cf7-content .wpcf7-date,.pp-cf7-content .wpcf7-file',
						),
					),
                )
            ),
            'button_typography'       => array( // Section
				'title'         => __('Button', 'bb-powerpack-lite'), // Section Title
				'collapsed'		=> true,
				'fields'        => array( // Section Fields
					'button_typography'	=> array(
						'type'        	   => 'typography',
						'label'       	   => __( 'Typography', 'bb-powerpack-lite' ),
						'responsive'  	   => true,
						'preview'          => array(
							'type'         		=> 'css',
							'selector' 		    => '.pp-cf7-content .wpcf7-submit'
						),
					),
                )
            ),
        )
    )
));
