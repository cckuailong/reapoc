<?php

/**
 * @class PPDualButtonModule
 */
class PPDualButtonModule extends FLBuilderModule {

	/**
	 * Constructor function for the module. You must pass the
	 * name, description, dir and url in an array to the parent class.
	 *
	 * @method __construct
	 */
	public function __construct() {
		parent::__construct(
			array(
				'name'          => __( 'Dual Button', 'bb-powerpack-lite' ),
				'description'   => __( 'A module for Dual Button.', 'bb-powerpack-lite' ),
				'group'         => pp_get_modules_group(),
				'category'      => pp_get_modules_cat( 'content' ),
				'dir'           => BB_POWERPACK_DIR . 'modules/pp-dual-button/',
				'url'           => BB_POWERPACK_URL . 'modules/pp-dual-button/',
				'editor_export' => true, // Defaults to true and can be omitted.
				'enabled'       => true, // Defaults to true and can be omitted.
			)
		);
	}

	public function filter_settings( $settings, $helper ) {
		// Handle old link field - Button 1.
		$settings = PP_Module_Fields::handle_link_field(
			$settings,
			array(
				'button_link_1'    => array(
					'type' => 'link',
				),
				'link_target_1'    => array(
					'type' => 'target',
				),
				'link_no_follow_1' => array(
					'type' => 'nofollow',
				),
			),
			'button_1_link'
		);

		// Handle old link field - Button 2.
		$settings = PP_Module_Fields::handle_link_field(
			$settings,
			array(
				'button_link_2'    => array(
					'type' => 'link',
				),
				'link_target_2'    => array(
					'type' => 'target',
				),
				'link_no_follow_2' => array(
					'type' => 'nofollow',
				),
			),
			'button_2_link'
		);

		// Handle old Background dual color field - Button 1.
		$settings = PP_Module_Fields::handle_dual_color_field(
			$settings,
			'button_1_bg_color',
			array(
				'primary'   => 'button_1_bg_color_default',
				'secondary' => 'button_1_bg_color_hover',
			)
		);

		// Handle old Text dual color field - Button 1.
		$settings = PP_Module_Fields::handle_dual_color_field(
			$settings,
			'button_1_text_color',
			array(
				'primary'   => 'button_1_text_color_default',
				'secondary' => 'button_1_text_color_hover',
			)
		);

		// Handle old border dual color field - Button 1.
		$settings = PP_Module_Fields::handle_dual_color_field(
			$settings,
			'button_1_border_color',
			array(
				'primary'   => 'button_1_border_color_default',
				'secondary' => 'button_1_border_color_hover',
			)
		);

		// Handle old Background dual color field - Button 2.
		$settings = PP_Module_Fields::handle_dual_color_field(
			$settings,
			'button_2_bg_color',
			array(
				'primary'   => 'button_2_bg_color_default',
				'secondary' => 'button_2_bg_color_hover',
			)
		);

		// Handle old Text dual color field - Button 2.
		$settings = PP_Module_Fields::handle_dual_color_field(
			$settings,
			'button_2_text_color',
			array(
				'primary'   => 'button_2_text_color_default',
				'secondary' => 'button_2_text_color_hover',
			)
		);

		// Handle old border dual color field - Button 2.
		$settings = PP_Module_Fields::handle_dual_color_field(
			$settings,
			'button_2_border_color',
			array(
				'primary'   => 'button_2_border_color_default',
				'secondary' => 'button_2_border_color_hover',
			)
		);

		// Handle old button padding multitext field.
		$settings = PP_Module_Fields::handle_multitext_field(
			$settings,
			'button_padding',
			'padding',
			'button_padding',
			array(
				'top'    => 'button_top_padding',
				'bottom' => 'button_bottom_padding',
				'left'   => 'button_left_padding',
				'right'  => 'button_right_padding',
			)
		);

		// Handle old typography fields.
		$settings = PP_Module_Fields::handle_typography_field(
			$settings,
			array(
				'button_font_family'    => array(
					'type' => 'font',
				),
				'button_font_size'      => array(
					'type' => 'font_size',
					'keys' => array(
						'desktop' => 'button_font_size_desktop',
						'tablet'  => 'button_font_size_tablet',
						'mobile'  => 'button_font_size_mobile',
					),
				),
				'button_line_height'    => array(
					'type' => 'line_height',
					'keys' => array(
						'desktop' => 'button_line_height_desktop',
						'tablet'  => 'button_line_height_tablet',
						'mobile'  => 'button_line_height_mobile',
					),
				),
				'button_letter_spacing' => array(
					'type' => 'letter_spacing',
				),
			),
			'button_typography'
		);

		return $settings;
	}

}

/**
 * Register the module and its form settings.
 */
FLBuilder::register_module(
	'PPDualButtonModule',
	array(
		'button_1_tab'      => array( // Tab
			'title'    => __( 'Button 1', 'bb-powerpack-lite' ), // Tab title
			'sections' => array( // Tab Sections
				'button_sections_1' => array(
					'title'  => '',
					'fields' => array(
						'button_1_title'           => array(
							'type'    => 'text',
							'label'   => 'Text',
							'default' => __( 'Button 1', 'bb-powerpack-lite' ),
							'preview' => array(
								'type'     => 'text',
								'selector' => '.pp-dual-button-1 span.pp-button-1-text',
							),
						),
						'button_1_link'            => array(
							'type'          => 'link',
							'label'         => __( 'Link', 'bb-powerpack-lite' ),
							'connections'   => array( 'url' ),
							'show_target'   => true,
							'show_nofollow' => true,
						),
						'button_icon_select_1'     => array(
							'type'    => 'pp-switch',
							'label'   => __( 'Icon Type', 'bb-powerpack-lite' ),
							'default' => 'none',
							'options' => array(
								'none'        => __( 'None', 'bb-powerpack-lite' ),
								'font_icon'   => __( 'Icon', 'bb-powerpack-lite' ),
								'custom_icon' => __( 'Image', 'bb-powerpack-lite' ),
							),
							'toggle'  => array(
								'font_icon'   => array(
									'fields'   => array( 'button_font_icon_1', 'button_1_font_icon_size', 'button_1_font_icon_hover_color', 'button_1_icon_aligment' ),
									'sections' => array( 'icon_style' ),
								),
								'custom_icon' => array(
									'fields'   => array( 'button_custom_icon_1', 'button_1_custom_icon_width', 'button_1_icon_aligment' ),
									'sections' => array( 'icon_style' ),
								),
							),
						),
						'button_font_icon_1'       => array(
							'type'  => 'icon',
							'label' => __( 'Icon', 'bb-powerpack-lite' ),
						),
						'button_custom_icon_1'     => array(
							'type'        => 'photo',
							'label'       => __( 'Custom Image', 'bb-powerpack-lite' ),
							'connections' => array( 'photo' ),
						),
						'button_1_icon_aligment'   => array(
							'type'    => 'pp-switch',
							'label'   => __( 'Icon Position', 'bb-powerpack-lite' ),
							'default' => 'left',
							'options' => array(
								'left'  => __( 'Before Text', 'bb-powerpack-lite' ),
								'right' => __( 'After Text', 'bb-powerpack-lite' ),
							),
						),
						'button_1_effect'          => array(
							'type'    => 'select',
							'label'   => __( 'Hover Transition', 'bb-powerpack-lite' ),
							'default' => 'none',
							'options' => array(
								'none'                   => __( 'None', 'bb-powerpack-lite' ),
								'fade'                   => __( 'Fade', 'bb-powerpack-lite' ),
								'sweep_right'            => __( 'Sweep To Right', 'bb-powerpack-lite' ),
								'sweep_left'             => __( 'Sweep To Left', 'bb-powerpack-lite' ),
								'sweep_bottom'           => __( 'Sweep To Bottom', 'bb-powerpack-lite' ),
								'sweep_top'              => __( 'Sweep To Top', 'bb-powerpack-lite' ),
								'bounce_right'           => __( 'Bounce To Right', 'bb-powerpack-lite' ),
								'bounce_left'            => __( 'Bounce To Left', 'bb-powerpack-lite' ),
								'bounce_bottom'          => __( 'Bounce To Bottom', 'bb-powerpack-lite' ),
								'bounce_top'             => __( 'Bounce To Top', 'bb-powerpack-lite' ),
								'radial_out'             => __( 'Radial Out', 'bb-powerpack-lite' ),
								'radial_in'              => __( 'Radial In', 'bb-powerpack-lite' ),
								'rectangle_out'          => __( 'Rectangle Out', 'bb-powerpack-lite' ),
								'rectangle_in'           => __( 'Rectangle In', 'bb-powerpack-lite' ),
								'shutter_in_horizontal'  => __( 'Shutter In Horizontal', 'bb-powerpack-lite' ),
								'shutter_out_horizontal' => __( 'Shutter Out Horizontal', 'bb-powerpack-lite' ),
								'shutter_in_vertical'    => __( 'Shutter In Vertical', 'bb-powerpack-lite' ),
								'shutter_out_vertical'   => __( 'Shutter Out Vertical', 'bb-powerpack-lite' ),
								'shutter_out_diagonal'   => __( 'Shutter Out Diagonal', 'bb-powerpack-lite' ),
								'shutter_in_diagonal'    => __( 'Shutter In Diagonal', 'bb-powerpack-lite' ),
							),
						),
						'button_1_effect_duration' => array(
							'type'        => 'text',
							'label'       => __( 'Transition Speed', 'bb-powerpack-lite' ),
							'size'        => 5,
							'maxlength'   => 4,
							'default'     => 200,
							'description' => 'ms',
						),
						'button_1_css_class'       => array(
							'type'    => 'text',
							'label'   => __( 'CSS Class', 'bb-powerpack-lite' ),
							'default' => '',
						),
						'enable_title_attr_1'      => array(
							'type'    => 'pp-switch',
							'label'   => __( 'Enable Title Attribute', 'bb-powerpack-lite' ),
							'default' => 'no',
							'options' => array(
								'yes' => __( 'Yes', 'bb-powerpack-lite' ),
								'no'  => __( 'No', 'bb-powerpack-lite' ),
							),
							'toggle'  => array(
								'yes' => array(
									'fields' => array( 'title_attr_1' ),
								),
							),
						),
						'title_attr_1'             => array(
							'type'    => 'text',
							'label'   => 'Attribute Text',
							'default' => __( 'My Button 1', 'bb-powerpack-lite' ),
							'preview' => array(
								'type' => 'none',
							),
						),
					),
				),
			),
		),
		'button_2_tab'      => array(
			'title'    => __( 'Button 2', 'bb-powerpack-lite' ),
			'sections' => array(
				'button_sections_2' => array(
					'title'  => '',
					'fields' => array(
						'button_2_title'           => array(
							'type'    => 'text',
							'label'   => 'Text',
							'default' => __( 'Button 2', 'bb-powerpack-lite' ),
							'preview' => array(
								'type'     => 'text',
								'selector' => '.pp-dual-button-2 span.pp-button-2-text',
							),
						),
						'button_2_link'            => array(
							'type'          => 'link',
							'label'         => __( 'Link', 'bb-powerpack-lite' ),
							'connections'   => array( 'url' ),
							'show_target'   => true,
							'show_nofollow' => true,
						),
						'button_icon_select_2'     => array(
							'type'    => 'pp-switch',
							'label'   => __( 'Icon Type', 'bb-powerpack-lite' ),
							'default' => 'none',
							'options' => array(
								'none'        => __( 'None', 'bb-powerpack-lite' ),
								'font_icon'   => __( 'Icon', 'bb-powerpack-lite' ),
								'custom_icon' => __( 'Image', 'bb-powerpack-lite' ),
							),
							'toggle'  => array(
								'font_icon'   => array(
									'fields'   => array( 'button_font_icon_2', 'button_2_font_icon_size', 'button_2_font_icon_hover_color', 'button_2_icon_aligment' ),
									'sections' => array( 'icon_style' ),
								),
								'custom_icon' => array(
									'fields'   => array( 'button_custom_icon_2', 'button_2_custom_icon_width', 'button_2_icon_aligment' ),
									'sections' => array( 'icon_style' ),
								),
							),
						),
						'button_font_icon_2'       => array(
							'type'  => 'icon',
							'label' => __( 'Icon', 'bb-powerpack-lite' ),
						),
						'button_custom_icon_2'     => array(
							'type'        => 'photo',
							'label'       => __( 'Custom Image', 'bb-powerpack-lite' ),
							'connections' => array( 'photo' ),
						),
						'button_2_icon_aligment'   => array(
							'type'    => 'pp-switch',
							'label'   => __( 'Icon Position', 'bb-powerpack-lite' ),
							'default' => 'left',
							'options' => array(
								'left'  => __( 'Before Text', 'bb-powerpack-lite' ),
								'right' => __( 'After Text', 'bb-powerpack-lite' ),
							),
						),
						'button_2_effect'          => array(
							'type'    => 'select',
							'label'   => __( 'Hover Transition', 'bb-powerpack-lite' ),
							'default' => 'none',
							'options' => array(
								'none'                   => __( 'None', 'bb-powerpack-lite' ),
								'fade'                   => __( 'Fade', 'bb-powerpack-lite' ),
								'sweep_right'            => __( 'Sweep To Right', 'bb-powerpack-lite' ),
								'sweep_left'             => __( 'Sweep To Left', 'bb-powerpack-lite' ),
								'sweep_bottom'           => __( 'Sweep To Bottom', 'bb-powerpack-lite' ),
								'sweep_top'              => __( 'Sweep To Top', 'bb-powerpack-lite' ),
								'bounce_right'           => __( 'Bounce To Right', 'bb-powerpack-lite' ),
								'bounce_left'            => __( 'Bounce To Left', 'bb-powerpack-lite' ),
								'bounce_bottom'          => __( 'Bounce To Bottom', 'bb-powerpack-lite' ),
								'bounce_top'             => __( 'Bounce To Top', 'bb-powerpack-lite' ),
								'radial_out'             => __( 'Radial Out', 'bb-powerpack-lite' ),
								'radial_in'              => __( 'Radial In', 'bb-powerpack-lite' ),
								'rectangle_out'          => __( 'Rectangle Out', 'bb-powerpack-lite' ),
								'rectangle_in'           => __( 'Rectangle In', 'bb-powerpack-lite' ),
								'shutter_in_horizontal'  => __( 'Shutter In Horizontal', 'bb-powerpack-lite' ),
								'shutter_out_horizontal' => __( 'Shutter Out Horizontal', 'bb-powerpack-lite' ),
								'shutter_in_vertical'    => __( 'Shutter In Vertical', 'bb-powerpack-lite' ),
								'shutter_out_vertical'   => __( 'Shutter Out Vertical', 'bb-powerpack-lite' ),
								'shutter_out_diagonal'   => __( 'Shutter Out Diagonal', 'bb-powerpack-lite' ),
								'shutter_in_diagonal'    => __( 'Shutter In Diagonal', 'bb-powerpack-lite' ),
							),
						),
						'button_2_effect_duration' => array(
							'type'        => 'text',
							'label'       => __( 'Transition Speed', 'bb-powerpack-lite' ),
							'size'        => 5,
							'maxlength'   => 4,
							'default'     => '200',
							'description' => 'ms',
						),
						'button_2_css_class'       => array(
							'type'    => 'text',
							'label'   => __( 'CSS Class', 'bb-powerpack-lite' ),
							'default' => '',
						),
						'enable_title_attr_2'      => array(
							'type'    => 'pp-switch',
							'label'   => __( 'Enable Title Attribute', 'bb-powerpack-lite' ),
							'default' => 'no',
							'options' => array(
								'yes' => __( 'Yes', 'bb-powerpack-lite' ),
								'no'  => __( 'No', 'bb-powerpack-lite' ),
							),
							'toggle'  => array(
								'yes' => array(
									'fields' => array( 'title_attr_1' ),
								),
							),
						),
						'title_attr_2'             => array(
							'type'    => 'text',
							'label'   => 'Attribute Text',
							'default' => __( 'My Button 2', 'bb-powerpack-lite' ),
							'preview' => array(
								'type' => 'none',
							),
						),
					),
				),
			),
		),
		'style'             => array( // Tab
			'title'    => __( 'Style', 'bb-powerpack-lite' ), // Tab title
			'sections' => array( // Tab Sections
				'structure'      => array( // Section
					'title'  => __( 'Structure', 'bb-powerpack-lite' ), // Section Title
					'fields' => array( // Section Fields
						'button_alignment'            => array(
							'type'    => 'align',
							'label'   => __( 'Alignment', 'bb-powerpack-lite' ),
							'default' => 'left',
						),
						'button_padding'              => array(
							'type'       => 'dimension',
							'label'      => __( 'Padding', 'bb-powerpack-lite' ),
							'default'    => '10',
							'units'      => array( 'px' ),
							'slider'     => true,
							'responsive' => true,
							'preview'    => array(
								'type'     => 'css',
								'selector' => '.pp-dual-button-content a.pp-button',
								'property' => 'padding',
								'unit'     => 'px',
							),
						),
						'button_border_style'         => array(
							'type'    => 'pp-switch',
							'label'   => __( 'Border Style', 'bb-powerpack-lite' ),
							'default' => 'none',
							'options' => array(
								'none'   => __( 'None', 'bb-powerpack-lite' ),
								'solid'  => __( 'Solid', 'bb-powerpack-lite' ),
								'dashed' => __( 'Dashed', 'bb-powerpack-lite' ),
								'dotted' => __( 'Dotted', 'bb-powerpack-lite' ),
							),
							'toggle'  => array(
								'solid'  => array(
									'fields' => array( 'button_border_width', 'button_1_border_color_default', 'button_1_border_color_hover', 'button_2_border_color_default', 'button_2_border_color_hover' ),
								),
								'dashed' => array(
									'fields' => array( 'button_border_width', 'button_1_border_color_default', 'button_1_border_color_hover', 'button_2_border_color_default', 'button_2_border_color_hover' ),
								),
								'dotted' => array(
									'fields' => array( 'button_border_width', 'button_1_border_color_default', 'button_1_border_color_hover', 'button_2_border_color_default', 'button_2_border_color_hover' ),
								),
							),
						),
						'button_border_width'         => array(
							'type'    => 'unit',
							'label'   => __( 'Border Width', 'bb-powerpack-lite' ),
							'default' => '1',
							'units'   => array( 'px' ),
							'slider'  => true,
							'preview' => array(
								'type'     => 'css',
								'selector' => '.pp-dual-button-content .pp-button',
								'property' => 'border-width',
								'unit'     => 'px',
							),
						),
						'button_border_radius'        => array(
							'type'    => 'unit',
							'label'   => __( 'Round Corners', 'bb-powerpack-lite' ),
							'default' => '0',
							'units'   => array( 'px' ),
							'slider'  => true,
							'preview' => array(
								'type'  => 'css',
								'rules' => array(
									array(
										'selector' => '.pp-dual-button-1 .pp-button',
										'property' => 'border-top-left-radius',
										'unit'     => 'px',
									),
									array(
										'selector' => '.pp-dual-button-1 .pp-button',
										'property' => 'border-bottom-left-radius',
										'unit'     => 'px',
									),
									array(
										'selector' => '.pp-dual-button-2 .pp-button',
										'property' => 'border-top-right-radius',
										'unit'     => 'px',
									),
									array(
										'selector' => '.pp-dual-button-2 .pp-button',
										'property' => 'border-bottom-right-radius',
										'unit'     => 'px',
									),
								),
							),
						),
						'button_width'                => array(
							'type'    => 'unit',
							'label'   => __( 'Width', 'bb-powerpack-lite' ),
							'default' => '200',
							'units'   => array( 'px' ),
							'slider'  => true,
							'preview' => array(
								'type'     => 'css',
								'selector' => '.pp-dual-button-content .pp-button',
								'property' => 'width',
								'unit'     => 'px',
							),
						),
						'button_spacing'              => array(
							'type'    => 'unit',
							'label'   => __( 'Spacing', 'bb-powerpack-lite' ),
							'default' => '10',
							'units'   => array( 'px' ),
							'slider'  => true,
							'preview' => array(
								'type'     => 'css',
								'selector' => '.pp-dual-button-content .pp-spacer',
								'property' => 'width',
								'unit'     => 'px',
							),
						),
						'responsive_breakpoint'       => array(
							'type'    => 'unit',
							'label'   => __( 'Responsive Breakpoint', 'bb-powerpack-lite' ),
							'default' => '480',
							'units'   => array( 'px' ),
							'help'    => __( 'Buttons will be stacked on top of each other.', 'bb-powerpack-lite' ),
						),
						'button_alignment_responsive' => array(
							'type'    => 'align',
							'label'   => __( 'Responsive Alignment', 'bb-powerpack-lite' ),
							'default' => '',
						),
					),
				),
				'button_1_style' => array( // Section
					'title'  => __( 'Button 1', 'bb-powerpack-lite' ), // Section Title
					'fields' => array( // Section Fields
						'button_1_bg_color_default'     => array(
							'type'        => 'color',
							'label'       => __( 'Background Color', 'bb-powerpack-lite' ),
							'default'     => 'eeeeee',
							'show_reset'  => true,
							'show_alpha'  => true,
							'connections' => array( 'color' ),
							'preview'     => array(
								'type'     => 'css',
								'selector' => '.pp-dual-button-content .pp-dual-button-1 a',
								'property' => 'background-color',
							),
						),
						'button_1_text_color_default'   => array(
							'type'        => 'color',
							'label'       => __( 'Text Color', 'bb-powerpack-lite' ),
							'default'     => '565656',
							'show_reset'  => true,
							'connections' => array( 'color' ),
							'preview'     => array(
								'type'     => 'css',
								'selector' => '.pp-dual-button-content .pp-dual-button-1 a',
								'property' => 'color',
							),
						),
						'button_1_bg_color_hover'       => array(
							'type'        => 'color',
							'label'       => __( 'Background Hover Color', 'bb-powerpack-lite' ),
							'default'     => 'dddddd',
							'show_reset'  => true,
							'show_alpha'  => true,
							'connections' => array( 'color' ),
							'preview'     => array(
								'type' => 'none',
							),
						),
						'button_1_text_color_hover'     => array(
							'type'        => 'color',
							'label'       => __( 'Text Hover Color', 'bb-powerpack-lite' ),
							'default'     => '565656',
							'show_reset'  => true,
							'connections' => array( 'color' ),
							'preview'     => array(
								'type' => 'none',
							),
						),
						'button_1_border_color_default' => array(
							'type'        => 'color',
							'label'       => __( 'Border Color', 'bb-powerpack-lite' ),
							'default'     => '333333',
							'show_alpha'  => true,
							'show_reset'  => true,
							'connections' => array( 'color' ),
							'preview'     => array(
								'type'     => 'css',
								'selector' => '.pp-dual-button-1 .pp-button',
								'property' => 'border-color',
							),
						),
						'button_1_border_color_hover'   => array(
							'type'        => 'color',
							'label'       => __( 'Border Hover Color', 'bb-powerpack-lite' ),
							'default'     => 'c6c6c6',
							'show_alpha'  => true,
							'show_reset'  => true,
							'connections' => array( 'color' ),
							'preview'     => array(
								'type' => 'none',
							),
						),
						'button_1_font_icon_size'       => array(
							'type'    => 'unit',
							'label'   => __( 'Icon Size', 'bb-powerpack-lite' ),
							'default' => '20',
							'units'   => array( 'px' ),
							'slider'  => true,
							'preview' => array(
								'type'     => 'css',
								'selector' => '.pp-dual-button-content .pp-dual-button-1 .pp-font-icon',
								'property' => 'font-size',
								'unit'     => 'px',
							),
						),
						'button_1_custom_icon_width'    => array(
							'type'    => 'unit',
							'label'   => __( 'Image Width', 'bb-powerpack-lite' ),
							'units'   => array( 'px' ),
							'slider'  => true,
							'default' => '20',
							'preview' => array(
								'type'     => 'css',
								'selector' => '.pp-dual-button-content .pp-dual-button-1 .pp-custom-icon',
								'property' => 'width',
								'unit'     => 'px',
							),
						),
					),
				),
				'button_2_style' => array( // Section
					'title'  => __( 'Button 2', 'bb-powerpack-lite' ), // Section Title
					'fields' => array( // Section Fields
						'button_2_bg_color_default'     => array(
							'type'        => 'color',
							'label'       => __( 'Background Color', 'bb-powerpack-lite' ),
							'default'     => 'dddddd',
							'show_reset'  => true,
							'show_alpha'  => true,
							'connections' => array( 'color' ),
							'preview'     => array(
								'type'     => 'css',
								'selector' => '.pp-dual-button-content .pp-dual-button-2 a',
								'property' => 'background-color',
							),
						),
						'button_2_text_color_default'   => array(
							'type'        => 'color',
							'label'       => __( 'Text Color', 'bb-powerpack-lite' ),
							'default'     => '565656',
							'show_reset'  => true,
							'connections' => array( 'color' ),
							'preview'     => array(
								'type'     => 'css',
								'selector' => '.pp-dual-button-content .pp-dual-button-2 a',
								'property' => 'color',
							),
						),
						'button_2_bg_color_hover'       => array(
							'type'        => 'color',
							'label'       => __( 'Background Hover Color', 'bb-powerpack-lite' ),
							'default'     => 'eeeeee',
							'show_reset'  => true,
							'show_alpha'  => true,
							'connections' => array( 'color' ),
							'preview'     => array(
								'type' => 'none',
							),
						),
						'button_2_text_color_hover'     => array(
							'type'        => 'color',
							'label'       => __( 'Text Hover Color', 'bb-powerpack-lite' ),
							'default'     => '565656',
							'show_reset'  => true,
							'connections' => array( 'color' ),
							'preview'     => array(
								'type' => 'none',
							),
						),
						'button_2_border_color_default' => array(
							'type'        => 'color',
							'label'       => __( 'Border Color', 'bb-powerpack-lite' ),
							'default'     => '333333',
							'show_alpha'  => true,
							'show_reset'  => true,
							'connections' => array( 'color' ),
							'preview'     => array(
								'type'     => 'css',
								'selector' => '.pp-dual-button-2 .pp-button',
								'property' => 'border-color',
							),
						),
						'button_2_border_color_hover'   => array(
							'type'        => 'color',
							'label'       => __( 'Border Hover Color', 'bb-powerpack-lite' ),
							'default'     => 'c6c6c6',
							'show_alpha'  => true,
							'show_reset'  => true,
							'connections' => array( 'color' ),
							'preview'     => array(
								'type' => 'none',
							),
						),
						'button_2_font_icon_size'       => array(
							'type'    => 'unit',
							'label'   => __( 'Icon Size', 'bb-powerpack-lite' ),
							'default' => 20,
							'units'   => array( 'px' ),
							'slider'  => true,
							'preview' => array(
								'type'     => 'css',
								'selector' => '.pp-dual-button-content .pp-dual-button-2 .pp-font-icon',
								'property' => 'font-size',
								'unit'     => 'px',
							),
						),
						'button_2_custom_icon_width'    => array(
							'type'    => 'unit',
							'label'   => __( 'Image Width', 'bb-powerpack-lite' ),
							'units'   => array( 'px' ),
							'slider'  => true,
							'default' => 20,
							'preview' => array(
								'type'     => 'css',
								'selector' => '.pp-dual-button-content .pp-dual-button-2 .pp-custom-icon',
								'property' => 'width',
								'unit'     => 'px',
							),
						),
					),
				),
			),
		),
		'button_typography' => array(
			'title'    => __( 'Typography', 'bb-powerpack-lite' ),
			'sections' => array(
				'typography' => array(
					'title'  => '',
					'fields' => array(
						'button_typography' => array(
							'type'       => 'typography',
							'label'      => __( 'Typography', 'bb-powerpack-lite' ),
							'responsive' => true,
							'preview'    => array(
								'type'     => 'font',
								'selector' => '.pp-dual-button-content .pp-button',
							),
						),
					),
				),
			),
		),
	)
);
