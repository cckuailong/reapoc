<?php

/**
 * @class PPHeadingModule
 */
class PPHeadingModule extends FLBuilderModule {

	/**
	 * Constructor function for the module. You must pass the
	 * name, description, dir and url in an array to the parent class.
	 *
	 * @method __construct
	 */
	public function __construct() {
		parent::__construct(
			array(
				'name'            => __( 'Smart Headings', 'bb-powerpack-lite' ),
				'description'     => __( 'A module for Smart Headings.', 'bb-powerpack-lite' ),
				'group'           => pp_get_modules_group(),
				'category'        => pp_get_modules_cat( 'content' ),
				'dir'             => BB_POWERPACK_DIR . 'modules/pp-heading/',
				'url'             => BB_POWERPACK_URL . 'modules/pp-heading/',
				'editor_export'   => true, // Defaults to true and can be omitted.
				'enabled'         => true, // Defaults to true and can be omitted.
				'partial_refresh' => true,
			)
		);
	}

	public function filter_settings( $settings, $helper ) {
		// Handle old link field.
		$settings = PP_Module_Fields::handle_link_field(
			$settings,
			array(
				'heading_link'            => array(
					'type' => 'link',
				),
				'heading_link_target' => array(
					'type' => 'target',
				),
			),
			'heading_link'
		);

		// Handle old title border width field.
		if ( isset( $settings->heading_border ) && is_array( $settings->heading_border ) ) {
			$settings = PP_Module_Fields::handle_multitext_field( $settings, 'heading_border', 'dimension' );
		}
		if ( isset( $settings->heading2_border ) && is_array( $settings->heading2_border ) ) {
			$settings = PP_Module_Fields::handle_multitext_field( $settings, 'heading2_border', 'dimension' );
		}

		// Handle old title padding field.
		if ( isset( $settings->heading_padding ) && is_array( $settings->heading_padding ) ) {
			$settings = PP_Module_Fields::handle_multitext_field( $settings, 'heading_padding', 'dimension' );
		}
		if ( isset( $settings->heading2_padding ) && is_array( $settings->heading2_padding ) ) {
			$settings = PP_Module_Fields::handle_multitext_field( $settings, 'heading2_padding', 'dimension' );
		}

		// Title Gradient Fields
		if ( isset( $settings->heading_gradient ) && 'yes' == $settings->heading_gradient ) {
			$settings->heading_color_type = 'gradient';
			unset( $settings->heading_gradient );
		}

		// - Handle old title gradient field.
		$settings = PP_Module_Fields::handle_gradient_field( $settings, array(
			'heading_gradient_primary_color'	=> array(
				'type'		=> 'primary_color',
			),
			'heading_gradient_secondary_color'	=> array(
				'type'		=> 'secondary_color',
			),
			'heading_gradient_degree'	=> array(
				'type'		=> 'angle',
			),
		), 'heading_gradient_setting' );

		// Secondary Title Gradient Fields
		if ( isset( $settings->heading2_gradient ) && 'yes' == $settings->heading2_gradient ) {
			$settings->heading2_color_type = 'gradient';
			unset( $settings->heading2_gradient );
		}

		// - Handle old secondary title gradient field.
		$settings = PP_Module_Fields::handle_gradient_field( $settings, array(
			'heading2_gradient_primary_color'	=> array(
				'type'		=> 'primary_color',
			),
			'heading2_gradient_secondary_color'	=> array(
				'type'		=> 'secondary_color',
			),
			'heading2_gradient_degree'	=> array(
				'type'		=> 'angle',
			),
		), 'heading2_gradient_setting' );

		// Heading Typography.
		$settings = PP_Module_Fields::handle_typography_field( $settings, array(
			'heading_font'	=> array(
				'type'			=> 'font'
			),
			'heading_font_size'	=> array(
				'type'			=> 'font_size',
				'condition'		=> ( isset( $settings->heading_font_size_select ) && 'custom' == $settings->heading_font_size_select )
			),
			'heading_tablet_font_size' => array(
				'type'			=> 'medium_font_size',
			),
			'heading_tablet_line_height_n' => array(
				'type'			=> 'medium_line_height',
			),
			'heading_mobile_font_size' => array(
				'type'			=> 'responsive_font_size',
			),
			'heading_mobile_line_height_n' => array(
				'type'			=> 'responsive_line_height',
			),
			'heading_line_height_n'	=> array(
				'type'			=> 'line_height',
			),
			'heading_letter_space'	=> array(
				'type'			=> 'letter_spacing'
			),
			'heading_text_transform'	=> array(
				'type'			=> 'text_transform',
				'condition'		=> ( isset( $settings->heading_text_transform ) && 'default' != $settings->heading_text_transform )
			),
			'heading_shadow'	=> array(
				'type'				=> 'text_shadow',
				'color'				=> ( isset( $settings->heading_shadow_color ) ) ? $settings->heading_shadow_color : '',
				'condition'			=> ( isset( $settings->heading_show_shadow ) && 'yes' == $settings->heading_show_shadow )
			)
		), 'title_typography' );

		if ( isset( $settings->heading_shadow_color ) ) {
			//unset( $settings->heading_shadow_color );
		}

		// Secondary Title Typography.
		$settings = PP_Module_Fields::handle_typography_field( $settings, array(
			'heading2_font'	=> array(
				'type'			=> 'font'
			),
			'heading2_font_size'	=> array(
				'type'			=> 'font_size',
				'condition'		=> ( isset( $settings->heading2_font_size_select ) && 'custom' == $settings->heading2_font_size_select )
			),
			'heading2_tablet_font_size' => array(
				'type'			=> 'medium_font_size',
			),
			'heading2_tablet_line_height_n' => array(
				'type'			=> 'medium_line_height',
			),
			'heading2_mobile_font_size' => array(
				'type'			=> 'responsive_font_size',
			),
			'heading2_mobile_line_height_n' => array(
				'type'			=> 'responsive_line_height',
			),
			'heading2_line_height_n'	=> array(
				'type'			=> 'line_height',
			),
			'heading2_letter_space'	=> array(
				'type'			=> 'letter_spacing'
			),
			'heading2_text_transform'	=> array(
				'type'			=> 'text_transform',
				'condition'		=> ( isset( $settings->heading2_text_transform ) && 'default' != $settings->heading2_text_transform )
			),
			'heading2_shadow'	=> array(
				'type'				=> 'text_shadow',
				'color'				=> ( isset( $settings->heading2_shadow_color ) ) ? $settings->heading2_shadow_color : '',
				'condition'			=> ( isset( $settings->heading2_show_shadow ) && 'yes' == $settings->heading2_show_shadow )
			)
		), 'title2_typography' );

		if ( isset( $settings->heading2_shadow_color ) ) {
			//unset( $settings->heading2_shadow_color );
		}

		// Sub Heading / Description Typography.
		$settings = PP_Module_Fields::handle_typography_field( $settings, array(
			'sub_heading_font'	=> array(
				'type'		=> 'font'
			),
			'sub_heading_font_size'	=> array(
				'type'		=> 'font_size',
				'condition'	=> ( isset( $settings->sub_heading_font_size_select ) && 'custom' == $settings->sub_heading_font_size_select )
			),
			'sub_heading_line_height_n'	=> array(
				'type'		=> 'line_height'
			),
			'sub_heading_tablet_font_size'	=> array(
				'type'		=> 'medium_font_size'
			),
			'sub_heading_tablet_line_height_n'	=> array(
				'type'		=> 'medium_line_height'
			),
			'sub_heading_mobile_font_size'	=> array(
				'type'		=> 'responsive_font_size'
			),
			'sub_heading_mobile_line_height_n'	=> array(
				'type'		=> 'responsive_line_height'
			),
		), 'desc_typography' );

		if ( isset( $settings->sub_heading_font_size_select ) ) {
			unset( $settings->sub_heading_font_size_select );
		}

		// Alignment
		if ( isset( $settings->heading_tablet_alignment ) && ! empty( $settings->heading_tablet_alignment ) ) {
			$settings->heading_alignment_medium = $settings->heading_tablet_alignment;
			unset( $settings->heading_tablet_alignment );
		}
		if ( isset( $settings->heading_mobile_alignment ) && ! empty( $settings->heading_mobile_alignment ) ) {
			$settings->heading_alignment_responsive = $settings->heading_mobile_alignment;
			unset( $settings->heading_mobile_alignment );
		}

		return $settings;
	}
}

/**
 * Register the module and its form settings.
 */
FLBuilder::register_module(
	'PPHeadingModule',
	array(
		'heading_info_tab'   => array( // Tab
			'title'         => __('General', 'bb-powerpack-lite'), // Tab title
			'sections'      => array( // Tab Sections
				'heading_section'       => array( // Section
					'title'        => __('Heading', 'bb-powerpack-lite'), // Section Title
					'fields'       => array( // Section Fields
						'heading_tag'    => array(
							'type'          => 'select',
							'label'         => __('Tag', 'bb-powerpack-lite'),
							'default'       => 'h2',
							'options'       => array(
								'h1'            => 'H1',
								'h2'            => 'H2',
								'h3'            => 'H3',
								'h4'            => 'H4',
								'h5'            => 'H5',
								'h6'            => 'H6',
							)
						),
						'heading_title'   => array(
							'type'          => 'text',
							'label'         => __('Title', 'bb-powerpack-lite'),
							'class'         => '',
							'default'       => __('Title', 'bb-powerpack-lite'),
							'connections'   => array( 'string', 'html', 'url' ),
							'preview'       => array(
								'type'      => 'text',
								'selector'  => '.pp-heading-content .pp-heading .heading-title span.pp-primary-title',
							)
						),
						'dual_heading'  => array(
							'type'          => 'pp-switch',
							'label'         => __('Dual Heading?', 'bb-powerpack-lite'),
							'default'       => 'no',
							'options'       => array(
								'yes'           => __('Yes', 'bb-powerpack-lite'),
								'no'            => __('No', 'bb-powerpack-lite')
							),
							'toggle'        => array(
								'yes'           => array(
									'sections'      => array('title2_typography', 'heading2_style'),
									'fields'        => array('heading_title2', 'heading_style', 'heading2_tablet_font_size', 'heading2_tablet_line_height_n', 'heading2_mobile_font_size', 'heading2_mobile_line_height_n')
								)
							),
							'help'  => __('This option allows you to create dual color heading or dual font heading.', 'bb-powerpack-lite')
						),
						'heading_title2'   => array(
							'type'          => 'text',
							'label'         => __('Secondary Title', 'bb-powerpack-lite'),
							'class'         => '',
							'default'       => __('Secondary Title', 'bb-powerpack-lite'),
							'connections'   => array( 'string', 'html', 'url' ),
							'preview'       => array(
								'type'      => 'text',
								'selector'  => '.pp-heading-content .pp-heading .heading-title span.pp-secondary-title',
							)
						),
						'heading_style'     => array(
							'type'              => 'pp-switch',
							'label'             => __('Style', 'bb-powerpack-lite'),
							'default'           => 'inline-block',
							'options'           => array(
								'inline-block'      => __('Inline', 'bb-powerpack-lite'),
								'block'             => __('Stacked', 'bb-powerpack-lite')
							),
							'preview'           => array(
								'type'              => 'css',
								'selector'          => '.pp-heading-content .pp-heading .heading-title span.title-text',
								'property'          => 'display'
							)
						),
						'heading_alignment'     => array(
						'type'                   => 'align',
						'label'                  => __('Alignment', 'bb-powerpack-lite'),
						'default'                => 'center',
						'responsive'				=> true
					),
					)
				),
				'heading_link'	=> array(
					'title'			=> __('Link', 'bb-powerpack-lite'),
					'fields'		=> array(
						'enable_link'   => array(
							'type'          => 'pp-switch',
							'label'         => __('Enable Link', 'bb-powerpack-lite'),
							'default'       => 'yes',
							'options'       => array(
								'yes'           => __('Yes', 'bb-powerpack-lite'),
								'no'            => __('No', 'bb-powerpack-lite')
							),
							'toggle'        => array(
								'yes'           => array(
									'fields'        => array('heading_link')
								)
							)
						),
						'heading_link'          => array(
							'type'          => 'link',
							'label'         => __('Link', 'bb-powerpack-lite'),
							'connections'   => array( 'url' ),
							'show_target'	=> true,
							'show_nofollow'	=> true,
							'preview'         => array(
								'type'            => 'none'
							)
						),
					)
				),
				'heading_sub_title'         => array(
					'title'                     => __('Description', 'bb-powerpack-lite'),
					'fields'                    => array(
						'heading_sub_title'     => array(
							'type'                  => 'editor',
							'label'                 => '',
							'default'               => __('Description', 'bb-powerpack-lite'),
							'rows'                  => '6',
							'media_buttons'         => false,
							'connections'            => array( 'string', 'html', 'url' ),
							'preview'               => array(
								'type'                  => 'text',
								'selector'              => '.pp-heading-content .pp-sub-heading'
							)
						),
					)
				),
			)
		),
		'heading_separator'  => array(
			'title'                     => __('Separator', 'bb-powerpack-lite'),
			'sections'                  => array(
				'heading_separator'     => array(
					'title'                     => '',
					'fields'                    => array(
						'heading_separator'     => array(
							'type'      => 'select',
							'label'     => __('Separator', 'bb-powerpack-lite'),
							'default'     => 'left',
							'options'       => array(
								'no_spacer'			=> __('No Separator', 'bb-powerpack-lite'),
								'inline'        	=> __('Inline', 'bb-powerpack-lite'),
								'line_only'     	=> __('Line', 'bb-powerpack-lite'),
								'icon_only'     	=> __('Icon/Image', 'bb-powerpack-lite'),
								'line_with_icon'    => __('Line With Icon/Image', 'bb-powerpack-lite'),
							),
							'toggle' => array(
								'inline'        => array(
									'fields'  => array('heading_line_style', 'font_title_line_space'),
									'sections' => array('heading_line_style_section'),
								),
								'line_only'      => array(
									'fields'  => array('heading_line_style', 'heading_separator_postion'),
									'sections' => array('heading_line_style_section', 'heading_separator_style_section'),
								),
								'icon_only'      => array(
									'fields'  => array('heading_separator_postion', 'font_icon_color'),
									'sections'  => array('heading_separator_style_section', 'heading_icon_image_style_section', 'heading_icon_image_settings'),
								),
								'line_with_icon'      => array(
									'fields'  => array('heading_line_style', 'font_icon_line_space', 'heading_separator_postion', 'font_icon_color'),
									'sections' => array('heading_line_style_section', 'heading_separator_style_section', 'heading_icon_image_style_section', 'heading_icon_image_settings')
								),
								'no_spacer' => array(
									'fields' => array(),
									'sections' => array(),
								)
							)
						),
						'heading_separator_postion' => array(
							'type'    => 'select',
							'label'   => __( 'Position', 'bb-powerpack-lite' ),
							'default' => 'middle',
							'options' => array(
								'top'     => __( 'Above Heading', 'bb-powerpack-lite' ),
								'between' => __( 'Between Dual Heading', 'bb-powerpack-lite' ),
								'middle'  => __( 'Below Heading', 'bb-powerpack-lite' ),
								'bottom'  => __( 'Below Description', 'bb-powerpack-lite' ),
							),
							'help'    => __( '"Between Dual Heading" option only works, when Dual Heading Enabled and styled as "Stacked".', 'bb-powerpack-lite' ),
						),
						'heading_line_style'     => array(
							'type'      => 'pp-switch',
							'label'     => __('Line Style', 'bb-powerpack-lite'),
							'default'     => 'solid',
							'options'       => array(
								'solid'          => __('Solid', 'bb-powerpack-lite'),
								'dashed'         => __('Dashed', 'bb-powerpack-lite'),
								'dotted'         => __('Dotted', 'bb-powerpack-lite'),
								'double'         => __('Double', 'bb-powerpack-lite'),
							)
						),
						'font_title_line_space'   => array(
							'type'          => 'unit',
							'label'         => __('Space between Line & Title', 'bb-powerpack-lite'),
							'units'   		=> array('px'),
							'slider'		=> true,
							'default'       => '20',
							'preview'       => array(
								'type'      => 'css',
								'rules'           => array(
									array(
										'selector'        => '.pp-heading-content .pp-heading.pp-separator-inline .heading-title span',
										'property'        => 'padding-left',
										'unit'            => 'px',
									),
									array(
										'selector'        => '.pp-heading-content .pp-heading.pp-separator-inline .heading-title span',
										'property'        => 'padding-right',
										'unit'            => 'px',
									),
								),
							)
						),
					)
				),
				'heading_icon_image_settings'       => array( // Section
					'title'        => __('Icon', 'bb-powerpack-lite'), // Section Title
					'fields'       => array( // Section Fields
						'heading_icon_select'       => array(
							'type'          => 'select',
							'label'         => __('Icon Source', 'bb-powerpack-lite'),
							'default'       => 'font_icon_select',
							'options'       => array(
								'font_icon_select'         => __('Icon Library', 'bb-powerpack-lite'),
								'custom_icon_select'       => __('Custom Image', 'bb-powerpack-lite')
							),
							'toggle' => array(
								'font_icon_select'    => array(
									'fields'   => array('heading_font_icon_select', 'font_icon_color'),
								),
								'custom_icon_select'   => array(
									'fields'  => array('heading_custom_icon_select'),
								),
							)
						),
						'heading_font_icon_select' => array(
							'type'          => 'icon',
							'label'         => __('Icon', 'bb-powerpack-lite')
						),
						'heading_custom_icon_select'     => array(
							'type'              => 'photo',
							'label'         => __('Custom Image', 'bb-powerpack-lite'),
							'default'       => '',
						),
						'font_icon_line_space'   => array(
							'type'          => 'unit',
							'label'         => __('Space between Line & Icon/Image', 'bb-powerpack-lite'),
							'units'   		=> array('px'),
							'slider'		=> true,
							'default'       => '20',
							'preview'       => array(
								'type'      => 'css',
								'rules'           => array(
								array(
									'selector'        => '.pp-heading-content .pp-heading-separator.line_with_icon:before',
									'property'        => 'margin-right',
									'unit'            => 'px'
								),
								array(
									'selector'        => '.pp-heading-content .pp-heading-separator.line_with_icon:after',
									'property'        => 'margin-left',
									'unit'            => 'px'
								),
							),
							)
						),
					)
				),
				'heading_line_style_section'    => array( // Section
					'title'             => __('Separator Size & Color', 'bb-powerpack-lite'), // Section Title
					'fields'            => array( // Section Fields
						'line_width'   => array(
							'type'          => 'unit',
							'label'         => __('Width', 'bb-powerpack-lite'),
							'units'   		=> array('px'),
							'slider'		=> true,
							'default'       => '100',
							'preview'       => array(
								'type'      => 'css',
								'rules'           => array(
								array(
									'selector'        => '.pp-heading-content .pp-heading-separator .pp-separator-line',
									'property'        => 'width',
									'unit'            => 'px'
								),
								array(
									'selector'        => '.pp-heading-content .pp-heading-separator.line_with_icon:after',
									'property'        => 'width',
									'unit'            => 'px'
								),
								array(
									'selector'        => '.pp-heading-content .pp-heading-separator.line_with_icon:before',
									'property'        => 'width',
									'unit'            => 'px'
								),
								array(
									'selector'        => '.pp-heading-content .pp-heading.pp-separator-inline .heading-title span:before',
									'property'        => 'width',
									'unit'            => 'px'
								),
								array(
									'selector'        => '.pp-heading-content .pp-heading.pp-separator-inline .heading-title span:after',
									'property'        => 'width',
									'unit'            => 'px'
								),
							),
							)
						),
						'line_height'       => array(
							'type'          => 'unit',
							'label'         => __('Height', 'bb-powerpack-lite'),
							'units'   		=> array('px'),
							'slider'		=> true,
							'default'       => '1',
							'preview'       => array(
								'type'      => 'css',
								'rules'           => array(
								array(
									'selector'        => '.pp-heading-content .pp-heading-separator.line_with_icon:before',
									'property'        => 'border-bottom-width',
									'unit'            => 'px'
								),
								array(
									'selector'        => '.pp-heading-content .pp-heading-separator.line_with_icon:after',
									'property'        => 'border-bottom-width',
									'unit'            => 'px'
								),
								array(
									'selector'        => '.pp-heading-content .pp-heading-separator .pp-separator-line',
									'property'        => 'border-bottom-width',
									'unit'            => 'px'
								),
								array(
									'selector'        => '.pp-heading-content .pp-heading.pp-separator-inline .heading-title span:before',
									'property'        => 'border-bottom-width',
									'unit'            => 'px'
								),
								array(
									'selector'        => '.pp-heading-content .pp-heading.pp-separator-inline .heading-title span:after',
									'property'        => 'border-bottom-width',
									'unit'            => 'px'
								),
							),
							)
						),
						'line_color'    => array(
							'type'          => 'color',
							'label'         => __('Color', 'bb-powerpack-lite'),
							'default'       => '',
							'show_reset'    => true,
							'connections'	=> array('color'),
							'preview'         => array(
								'type'            => 'css',
								'rules'           => array(
								array(
									'selector'        => '.pp-heading-content .pp-heading-separator.line_with_icon:before',
									'property'        => 'border-color',
								),
								array(
									'selector'        => '.pp-heading-content .pp-heading-separator.line_with_icon:after',
									'property'        => 'border-color',
								),
								array(
									'selector'        => '.pp-heading-content .pp-heading-separator .pp-separator-line',
									'property'        => 'border-bottom-color',
								),
								array(
									'selector'        => '.pp-heading-content .pp-heading.pp-separator-inline .heading-title span:before',
									'property'        => 'border-color',
								),
								array(
									'selector'        => '.pp-heading-content .pp-heading.pp-separator-inline .heading-title span:after',
									'property'        => 'border-color',
								),
							),
							)
						),
					)
				),
				'heading_icon_image_style_section'    => array( // Section
					'title'             => __('Icon/Image Style', 'bb-powerpack-lite'), // Section Title
					'fields'            => array( // Section Fields
						'font_icon_font_size'   => array(
							'type'          => 'unit',
							'label'         => __('Icon Size', 'bb-powerpack-lite'),
							'units'   		=> array('px'),
							'slider'		=> true,
							'default'       => '16',
							'preview'       => array(
								'type'      => 'css',
								'rules'           => array(
								array(
									'selector'        => '.pp-heading-content .pp-heading-separator .pp-heading-separator-icon i, .pp-heading-content .pp-heading-separator .pp-heading-separator-icon i:before',
									'property'        => 'font-size',
									'unit'            => 'px'
								),
								array(
									'selector'        => '.pp-heading-content .pp-heading-separator img.heading-icon-image',
									'property'        => 'width',
									'unit'            => 'px'
								),
							),
							)
						),
						'font_icon_color'    => array(
							'type'          => 'color',
							'label'         => __('Color', 'bb-powerpack-lite'),
							'default'       => '',
							'show_reset'    => true,
							'connections'	=> array('color'),
							'preview'         => array(
								'type'            => 'css',
								'selector'        => '.pp-heading-content .pp-heading-separator',
								'property'        => 'color'
							)
						),
						'font_icon_bg_color'    => array(
							'type'          => 'color',
							'label'         => __('Background Color', 'bb-powerpack-lite'),
							'default'       => '',
							'show_reset'    => true,
							'show_alpha'	=> true,
							'connections'	=> array('color'),
							'preview'         => array(
								'type'            => 'css',
								'rules'           => array(
								array(
									'selector'        => '.pp-heading-content .pp-heading-separator .pp-heading-separator-icon',
									'property'        => 'background-color',
								),
								array(
									'selector'        => '.pp-heading-content .pp-heading-separator.icon_only span',
									'property'        => 'background-color',
								),
							),
							)
						),
						'field_separator_5'  => array(
							'type'                => 'pp-separator',
							'color'               => 'eeeeee'
						),
						'font_icon_border_width'   => array(
							'type'          => 'unit',
							'label'         => __('Border Width', 'bb-powerpack-lite'),
							'units'   		=> array('px'),
							'slider'		=> true,
							'default'       => '0',
							'preview'       => array(
								'type'      => 'css',
								'rules'           => array(
								array(
									'selector'        => '.pp-heading-content .pp-heading-separator .pp-heading-separator-icon',
									'property'        => 'border-width',
									'unit'            => 'px'
								),
								array(
									'selector'        => '.pp-heading-content .pp-heading-separator.icon_only span',
									'property'        => 'border-width',
									'unit'            => 'px'
								),
							),
							)
						),
						'font_icon_border_style'     => array(
							'type'      => 'select',
							'label'     => __('Border Style', 'bb-powerpack-lite'),
							'default'     => 'none',
							'options'       => array(
								'none'          => __('None', 'bb-powerpack-lite'),
								'solid'          => __('Solid', 'bb-powerpack-lite'),
								'dashed'          => __('Dashed', 'bb-powerpack-lite'),
								'dotted'          => __('Dotted', 'bb-powerpack-lite'),
								'double'          => __('Double', 'bb-powerpack-lite'),
							)
						),
						'font_icon_border_color'    => array(
							'type'          => 'color',
							'label'         => __('Border Color', 'bb-powerpack-lite'),
							'default'       => '',
							'show_reset'    => true,
							'connections'	=> array('color'),
							'preview'         => array(
								'type'            => 'css',
								'rules'           => array(
								array(
									'selector'        => '.pp-heading-content .pp-heading-separator .pp-heading-separator-icon',
									'property'        => 'border-color',
									'unit'            => 'px'
								),
								array(
									'selector'        => '.pp-heading-content .pp-heading-separator.icon_only span',
									'property'        => 'border-color',
								),
							),
							)
						),
						'font_icon_border_radius'   => array(
							'type'          => 'unit',
							'label'         => __('Round Corners', 'bb-powerpack-lite'),
							'units'   		=> array('px'),
							'slider'		=> true,
							'default'       => '100',
							'preview'       => array(
								'type'      => 'css',
								'rules'           => array(
								array(
									'selector'        => '.pp-heading-content .pp-heading-separator .pp-heading-separator-icon',
									'property'        => 'border-radius',
									'unit'            => 'px'
								),
								array(
									'selector'        => '.pp-heading-content .pp-heading-separator.icon_only span',
									'property'        => 'border-radius',
									'unit'            => 'px'
								),
								array(
									'selector'        => '.pp-heading-content .pp-heading-separator.icon_only img',
									'property'        => 'border-radius',
									'unit'            => 'px'
								),
								array(
									'selector'        => '.pp-heading-content .pp-heading-separator.line_with_icon img',
									'property'        => 'border-radius',
									'unit'            => 'px'
								)
							),
							)
						),
						'field_separator_6'  => array(
							'type'                => 'pp-separator',
							'color'               => 'eeeeee'
						),
						'font_icon_padding'	=> array(
							'type'		=> 'dimension',
							'label'		=> __('Padding', 'bb-powerpack-lite'),
							'units'   	=> array('px'),
							'slider'	=> true,
							'default'	=> '0',
							'responsive'	=> true,
							'preview'       => array(
								'type'      => 'css',
								'rules'           => array(
								array(
									'selector'        => '.pp-heading-content .pp-heading-separator .pp-heading-separator-icon',
									'property'        => 'padding',
									'unit'            => 'px'
								),
								array(
									'selector'        => '.pp-heading-content .pp-heading-separator.icon_only span',
									'property'        => 'padding',
									'unit'            => 'px'
								),
							),
							)
						),
					)
				),
				'heading_separator_style_section'    => array( // Section
					'title'             => __('Margin', 'bb-powerpack-lite'), // Section Title
					'fields'            => array( // Section Fields
						'separator_heading_top_margin'   => array(
							'type'          => 'unit',
							'label'         => __('Margin Top', 'bb-powerpack-lite'),
							'units'   		=> array('px'),
							'slider'		=> true,
							'default'       => '10',
							'preview'       => array(
								'type'      => 'css',
								'selector'  => '.pp-heading-content .pp-heading-separator',
								'property'  => 'margin-top',
								'unit'      => 'px'
							)
						),
						'separator_heading_bottom_margin' => array(
							'type'          => 'unit',
							'label'         => __('Margin Bottom', 'bb-powerpack-lite'),
							'units'   		=> array('px'),
							'slider'		=> true,
							'default'       => '10',
							'preview'       => array(
								'type'      => 'css',
								'selector'  => '.pp-heading-content .pp-heading-separator',
								'property'  => 'margin-bottom',
								'unit'      => 'px'
							)
						),
					)
				),
			)
		),
		'heading_style_tab'  => array( // Tab
			'title'         => __('Style', 'bb-powerpack-lite'), // Tab title
			'sections'      => array( // Tab Sections
				'main_heading_style'       => array( // Section
					'title'         => __('Title', 'bb-powerpack-lite'), // Section Title
					'fields'        => array( // Section Fields
						'heading_color_type'	=> array(
							'type'		=> 'pp-switch',
							'label'		=> __('Color Type', 'bb-powerpack-lite'),
							'default'	=> 'solid',
							'options'	=> array(
								'solid'		=> __('Solid', 'bb-powerpack-lite'),
								'gradient'	=> __('Gradient', 'bb-powerpack-lite'),
							),
							'toggle'	=> array(
								'solid'		=> array(
									'fields'	=> array( 'heading_color' )
								),
								'gradient'	=> array(
									'fields'	=> array( 'heading_gradient_setting' )
								)
							)
						),
						'heading_color'    => array(
							'type'          => 'color',
							'label'         => __('Color', 'bb-powerpack-lite'),
							'default'       => '',
							'show_reset'    => true,
							'show_alpha'    => true,
							'connections'	=> array('color'),
							'preview'         => array(
								'type'            => 'css',
								'selector'        => '.pp-heading-content .pp-heading .heading-title span.pp-primary-title',
								'property'        => 'color'
							)
						),
						'heading_gradient_setting'	=> array(
							'type'		=> 'gradient',
							'label'		=> __('Gradient', 'bb-powerpack-lite'),
							'preview'	=> array(
								'type'		=> 'css',
								'selector'  => '.pp-heading-content .pp-heading .heading-title span.pp-primary-title',
								'property'  => 'background-image'
							)
						),
						'field_separator_1'  => array(
							'type'                => 'pp-separator',
							'color'               => 'eeeeee'
						),
						'heading_bg_color' => array(
							'type'          => 'color',
							'label'         => __('Background Color', 'bb-powerpack-lite'),
							'default'       => '',
							'show_reset'    => true,
							'show_alpha'    => true,
							'connections'	=> array('color'),
							'preview'         => array(
								'type'            => 'css',
								'selector'        => '.pp-heading-content .pp-heading .heading-title span.pp-primary-title',
								'property'        => 'background-color'
							)
						),
						'field_separator_1_1'  => array(
							'type'                => 'pp-separator',
							'color'               => 'eeeeee'
						),
						'heading_border_style'  => array(
							'type'                  => 'select',
							'label'                 => __('Border Style', 'bb-powerpack-lite'),
							'default'               => 'none',
							'options'               => array(
								'none'                	=> __('None', 'bb-powerpack-lite'),
								'dashed'                => __('Dashed', 'bb-powerpack-lite'),
								'dotted'                => __('Dotted', 'bb-powerpack-lite'),
								'double'                => __('Double', 'bb-powerpack-lite'),
								'solid'                 => __('Solid', 'bb-powerpack-lite'),
							),
							'preview'               => array(
								'type'                  => 'css',
								'rules'                 => array(
									array(
										'selector'              => '.pp-heading-content .pp-heading .heading-title span.pp-primary-title',
										'property'              => 'border-top-style',
									),
									array(
										'selector'              => '.pp-heading-content .pp-heading .heading-title span.pp-primary-title',
										'property'              => 'border-bottom-style',
									),
									array(
										'selector'              => '.pp-heading-content .pp-heading .heading-title span.pp-primary-title',
										'property'              => 'border-left-style',
									),
									array(
										'selector'              => '.pp-heading-content .pp-heading .heading-title span.pp-primary-title',
										'property'              => 'border-right-style',
									)
								)
							)
						),
						'heading_border'	=> array(
							'type'				=> 'dimension',
							'label'				=> __('Border Width', 'bb-powerpack-lite'),
							'units'				=> array('px'),
							'slider'			=> true,
							'responsive'		=> false,
						),
						'heading_border_color' => array(
							'type'          => 'color',
							'label'         => __('Border Color', 'bb-powerpack-lite'),
							'default'       => '000000',
							'show_reset'    => true,
							'connections'	=> array('color'),
							'preview'         => array(
								'type'            => 'css',
								'selector'        => '.pp-heading-content .pp-heading .heading-title span.pp-primary-title',
								'property'        => 'border-color'
							)
						),
						'field_separator_2'  => array(
							'type'                => 'pp-separator',
							'color'               => 'eeeeee'
						),
						'heading_padding'	=> array(
							'type'				=> 'dimension',
							'label'				=> __('Padding', 'bb-powerpack-lite'),
							'units'				=> array('px'),
							'slider'			=> true,
							'responsive'		=> true,
							'preview'			=> array(
								'type'				=> 'css',
								'selector'			=> '.pp-heading-content .pp-heading .heading-title span.pp-primary-title',
								'property'			=> 'padding',
								'unit'				=> 'px'
							)
						),
						'heading_top_margin'   => array(
							'type'          => 'unit',
							'label'         => __('Margin Top', 'bb-powerpack-lite'),
							'units'			=> array('px'),
							'slider'		=> true,
							'default'       => '10',
							'preview'       => array(
								'type'      => 'css',
								'selector'  => '.pp-heading-content .pp-heading .heading-title',
								'property'  => 'margin-top',
								'unit'      => 'px'
							)
						),
						'heading_bottom_margin'   => array(
							'type'          => 'unit',
							'label'         => __('Margin Bottom', 'bb-powerpack-lite'),
							'units'			=> array('px'),
							'slider'		=> true,
							'default'       => '10',
							'preview'       => array(
								'type'      => 'css',
								'selector'  => '.pp-heading-content .pp-heading .heading-title',
								'property'  => 'margin-bottom',
								'unit'      => 'px'
							)
						),
					),
				),
				'heading2_style'	=> array( // Section
					'title'         	=> __('Secondary Title', 'bb-powerpack-lite'), // Section Title
					'collapsed'			=> true,
					'fields'        	=> array( // Section Fields
						'heading2_color_type'	=> array(
							'type'		=> 'pp-switch',
							'label'		=> __('Color Type', 'bb-powerpack-lite'),
							'default'	=> 'solid',
							'options'	=> array(
								'solid'		=> __('Solid', 'bb-powerpack-lite'),
								'gradient'	=> __('Gradient', 'bb-powerpack-lite'),
							),
							'toggle'	=> array(
								'solid'		=> array(
									'fields'	=> array( 'heading2_color' )
								),
								'gradient'	=> array(
									'fields'	=> array( 'heading2_gradient_setting' )
								)
							)
						),
						'heading2_color'    => array(
							'type'          => 'color',
							'label'         => __('Color', 'bb-powerpack-lite'),
							'default'       => '',
							'show_reset'    => true,
							'show_alpha'    => true,
							'connections'	=> array('color'),
							'preview'         => array(
								'type'            => 'css',
								'selector'        => '.pp-heading-content .pp-heading span.pp-secondary-title',
								'property'        => 'color'
							)
						),
						'heading2_gradient_setting'	=> array(
							'type'		=> 'gradient',
							'label'		=> __('Gradient', 'bb-powerpack-lite'),
							'preview'	=> array(
								'type'		=> 'css',
								'selector'  => '.pp-heading-content .pp-heading .heading-title span.pp-secondary-title',
								'property'  => 'background-image'
							)
						),
						'field_separator_2_1'  => array(
							'type'                => 'pp-separator',
							'color'               => 'eeeeee'
						),
						'heading2_bg_color' => array(
							'type'          => 'color',
							'label'         => __('Background Color', 'bb-powerpack-lite'),
							'default'       => '',
							'show_reset'    => true,
							'show_alpha'    => true,
							'connections'	=> array('color'),
							'preview'         => array(
								'type'            => 'css',
								'selector'        => '.pp-heading-content .pp-heading span.pp-secondary-title',
								'property'        => 'background-color'
							)
						),
						'field_separator_3'  => array(
							'type'                => 'pp-separator',
							'color'               => 'eeeeee'
						),
						'heading2_border_style'  => array(
							'type'                  => 'select',
							'label'                 => __('Border Style', 'bb-powerpack-lite'),
							'default'               => 'none',
							'options'               => array(
								'none'                => __('None', 'bb-powerpack-lite'),
								'dashed'                => __('Dashed', 'bb-powerpack-lite'),
								'dotted'                => __('Dotted', 'bb-powerpack-lite'),
								'double'                => __('Double', 'bb-powerpack-lite'),
								'solid'                 => __('Solid', 'bb-powerpack-lite'),
							),
						),
						'heading2_border'   => array(
							'type'				=> 'dimension',
							'label'				=> __('Border Width', 'bb-powerpack-lite'),
							'units'				=> array('px'),
							'slider'			=> true,
							'responsive'		=> false,
						),
						'heading2_border_color' => array(
							'type'          => 'color',
							'label'         => __('Border Color', 'bb-powerpack-lite'),
							'default'       => '000000',
							'show_reset'    => true,
							'connections'	=> array('color'),
							'preview'         => array(
								'type'            => 'css',
								'selector'        => '.pp-heading-content .pp-heading .heading-title span.pp-secondary-title',
								'property'        => 'border-color'
							)
						),
						'field_separator_4'  => array(
							'type'                => 'pp-separator',
							'color'               => 'eeeeee'
						),
						'heading2_padding'   => array(
							'type'				=> 'dimension',
							'label'				=> __('Padding', 'bb-powerpack-lite'),
							'units'				=> array('px'),
							'slider'			=> true,
							'responsive'		=> true,
							'preview'			=> array(
								'type'				=> 'css',
								'selector'			=> '.pp-heading-content .pp-heading .heading-title span.pp-secondary-title',
								'property'			=> 'padding',
								'unit'				=> 'px'
							)
						),
						'heading2_left_margin'   => array(
							'type'          => 'unit',
							'label'         => __('Margin Left', 'bb-powerpack-lite'),
							'units'			=> array('px'),
							'slider'		=> true,
							'default'       => '0',
							'preview'       => array(
								'type'      => 'css',
								'selector'  => '.pp-heading-content .pp-heading .heading-title span.pp-secondary-title',
								'property'  => 'margin-left',
								'unit'      => 'px'
							)
						),
					)
				),
				'sub_heading_style'       => array( // Section
					'title'         => __('Description', 'bb-powerpack-lite'), // Section Title
					'collapsed'		=> true,
					'fields'        => array( // Section Fields
						'sub_heading_color'    => array(
							'type'          => 'color',
							'label'         => __('Color', 'bb-powerpack-lite'),
							'default'       => '',
							'show_reset'    => true,
							'connections'	=> array('color'),
							'preview'         => array(
								'type'            => 'css',
								'selector'        => '.pp-heading-content .pp-sub-heading',
								'property'        => 'color'
							)
						),
						'sub_heading_top_margin'   => array(
							'type'          => 'unit',
							'label'         => __('Margin Top', 'bb-powerpack-lite'),
							'units'			=> array('px'),
							'slider'		=> true,
							'default'       => '0',
							'preview'       => array(
								'type'      => 'css',
								'selector'  => '.pp-heading-content .pp-sub-heading',
								'property'  => 'margin-top',
								'unit'      => 'px'
							)
						),
						'sub_heading_bottom_margin'   => array(
							'type'          => 'unit',
							'label'         => __('Margin Bottom', 'bb-powerpack-lite'),
							'units'			=> array('px'),
							'slider'		=> true,
							'default'       => '0',
							'preview'       => array(
								'type'      => 'css',
								'selector'  => '.pp-heading-content .pp-sub-heading',
								'property'  => 'margin-bottom',
								'unit'      => 'px'
							)
						),
					)
				),
			)
		),
		'heading_typography' => array(
			'title'                 => __('Typography', 'bb-powerpack-lite'),
			'sections'              => array(
				'title_typography'      => array(
					'title'                 => __('Title', 'bb-powerpack-lite'),
					'fields'                => array(
						'title_typography'		=> array(
							'type'					=> 'typography',
							'label'					=> __('Typography', 'bb-powerpack-lite'),
							'responsive'  			=> true,
							'preview'				=> array(
								'type'					=> 'css',
								'selector'				=> '.pp-heading-content .pp-heading .heading-title'
							)
						),
					)
				),
				'title2_typography'      => array(
					'title'                 => __('Secondary Title', 'bb-powerpack-lite'),
					'fields'                => array(
						'title2_typography'		=> array(
							'type'					=> 'typography',
							'label'					=> __('Typography', 'bb-powerpack-lite'),
							'responsive'  			=> true,
							'preview'				=> array(
								'type'					=> 'css',
								'selector'				=> '.pp-heading-content .pp-heading .heading-title span.pp-secondary-title'
							)
						),  
					)
				),
				'sub_heading_style'       => array( // Section
					'title'         => __('Description', 'bb-powerpack-lite'), // Section Title
					'fields'        => array( // Section Fields
						'desc_typography'	=> array(
							'type'					=> 'typography',
							'label'					=> __('Typography', 'bb-powerpack-lite'),
							'responsive'  			=> true,
							'preview'				=> array(
								'type'					=> 'css',
								'selector'				=> '.pp-heading-content .pp-sub-heading, .pp-heading-content .pp-sub-heading p'
							)
						),
					)
				)
			)
		),
	)
);
