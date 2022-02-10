<?php

/**
 * @class PPLineSeparatorModule
 */
class PPLineSeparatorModule extends FLBuilderModule {

    /**
     * Constructor function for the module. You must pass the
     * name, description, dir and url in an array to the parent class.
     *
     * @method __construct
     */
    public function __construct()
    {
        parent::__construct(array(
            'name'          => __('Divider', 'bb-powerpack-lite'),
            'description'   => __('Addon to add dividers in the row.', 'bb-powerpack-lite'),
            'group'         => pp_get_modules_group(),
            'category'		=> pp_get_modules_cat( 'creative' ),
            'dir'           => BB_POWERPACK_DIR . 'modules/pp-line-separator/',
            'url'           => BB_POWERPACK_URL . 'modules/pp-line-separator/',
            'editor_export' => true, // Defaults to true and can be omitted.
            'enabled'       => true, // Defaults to true and can be omitted.
            'partial_refresh'   => true,
		));
		
		$this->add_css( BB_POWERPACK_LITE()->fa_css );
    }

	public function filter_settings( $settings, $helper )
	{
		// Handle old icon border and radius fields.
		$settings = PP_Module_Fields::handle_border_field( $settings, array(
			'font_icon_border_style'	=> array(
				'type'				=> 'style',
				'condition'			=> ( isset( $settings->show_border ) && 'yes' == $settings->show_border ),
			),
			'font_icon_border_width'	=> array(
				'type'				=> 'width',
				'condition'			=> ( isset( $settings->show_border ) && 'yes' == $settings->show_border ),
			),
			'font_icon_border_color'	=> array(
				'type'				=> 'color',
				'condition'			=> ( isset( $settings->show_border ) && 'yes' == $settings->show_border ),
			),
			'icon_border_radius'	=> array(
				'type'				=> 'radius',
				'condition'			=> ( isset( $settings->show_border ) && 'yes' == $settings->show_border ),
			),
		), 'icon_border' );

		return $settings;
	}
}

/**
 * Register the module and its form settings.
 */
FLBuilder::register_module('PPLineSeparatorModule', array(
	'general'      => array( // Tab
		'title'         => __('Separator', 'bb-powerpack-lite'), // Tab title
		'sections'      => array( // Tab Sections
            'separator'      => array(
                'title'     => '',
                'fields'    => array(
                    'line_separator'     => array(
                       'type'      => 'select',
                       'label'     => __('Separator', 'bb-powerpack-lite'),
                       'default'     => 'line_only',
                       'options'       => array(
                           'line_only'          => __('Line', 'bb-powerpack-lite'),
                           'icon_image'          => __('Icon/Image', 'bb-powerpack-lite'),
                           'line_with_icon'     => __('Line With Icon/Image', 'bb-powerpack-lite'),
                       ),
                       'toggle' => array(
                            'line_only'      => array(
                                'fields'  => array('line_style', 'separator_alignment'),
                                'sections' => array('line_style_section'),
                            ),
                            'icon_image'    => array(
                                'fields'    => array('icon_image_select', 'separator_alignment'),
                                'sections'  => array('border_section', 'image_icon_style_section', 'icon_image_settings'),
                            ),
                            'line_with_icon'      => array(
                                'fields'  => array('line_style', 'separator_alignment', 'icon_image', 'font_icon_line_space', 'icon_image_select', 'icon_line_space'),
                                'sections' => array('line_style_section', 'border_section', 'image_icon_style_section', 'icon_image_settings')
                            ),
                        )
                    ),
                    'line_style'     => array(
                        'type'      => 'select',
                        'label'     => __('Line Style', 'bb-powerpack-lite'),
                        'default'     => 'none',
                        'options'       => array(
                             'none'           => __('None', 'bb-powerpack-lite'),
                             'solid'          => __('Solid', 'bb-powerpack-lite'),
                             'dashed'         => __('Dashed', 'bb-powerpack-lite'),
                             'dotted'         => __('Dotted', 'bb-powerpack-lite'),
                             'double'         => __('Double', 'bb-powerpack-lite'),
                         )
                    ),
                    'separator_alignment'    => array(
                        'type'      => 'align',
                        'label'     => 'Separator Alignment',
                        'default'   => 'center',
                    ),
                ),
            ),
            'icon_image_settings'   => array(
                'title'     => '',
                'fields'    => array(
                    'icon_image_select'    => array(
                        'type'      => 'pp-switch',
                        'label'     => __('Icon Source', 'bb-powerpack-lite'),
                        'default'   => 'icon',
                        'options'   => array(
                            'icon'  => __('Icon', 'bb-powerpack-lite'),
                            'image'  => __('Image', 'bb-powerpack-lite'),
                        ),
                        'toggle'    => array(
                            'icon'  => array(
                                'fields'  => array('separator_icon', 'font_icon_font_size', 'font_icon_color', 'font_icon_bg_color', 'font_icon_padding_top_bottom', 'font_icon_padding_left_right'),
                            ),
                            'image'  => array(
                                'fields'  => array('separator_image', 'font_icon_font_size', 'font_icon_bg_color', 'font_icon_padding_top_bottom', 'font_icon_padding_left_right'),
                            ),
                        ),
                    ),
                    'separator_icon'          => array(
						'type'          => 'icon',
						'label'         => __('Icon', 'bb-powerpack-lite')
					),
                    'separator_image'   => array(
                        'type'          => 'photo',
                        'label'         => __('Select Image', 'bb-powerpack-lite'),
                        'connections'   => array( 'photo' ),
                    ),
                    'icon_line_space'   => array(
                        'type'      => 'unit',
                        'label'     => __('Line-Icon gap', 'bb-powerpack-lite'),
                        'units'		=> array( 'px' ),
						'slider'	=> true,
                        'preview'   => array(
                            'type'  => 'css',
                            'rules'     => array(
                                array(
                                    'selector'      => '.pp-line-separator-inner.pp-line-icon:before',
                                    'property'      => 'margin-right',
                                    'unit'          => 'px'
                                ),
                                array(
                                    'selector'      => '.pp-line-separator-inner.pp-line-icon:after',
                                    'property'      => 'margin-left',
                                    'unit'          => 'px'
                                ),
                            ),
                        ),
                    ),
                ),
            ),
		)
	),
    'style'     => array(
        'title'     => __('Style', 'bb-powerpack-lite'),
        'sections'      => array(
            'line_style_section'    => array( // Section
                'title'             => __('Line Style', 'bb-powerpack-lite'), // Section Title
                'fields'            => array( // Section Fields
					'line_width'   => array(
                        'type'          => 'unit',
                        'label'         => __('Custom Width', 'bb-powerpack-lite'),
                        'units'			=> array( '%' ),
						'slider'		=> true,
                        'default'       => '100',
                        'preview'       => array(
                            'type'      => 'css',
                            'rules'     => array(
                                array(
                                    'selector'  => '.pp-line-separator-inner.pp-line-only .pp-line-separator',
                                    'property'  => 'width',
                                    'unit'      => '%'
                                ),
                                array(
                                    'selector'  => '.pp-line-separator-inner.pp-line-icon .pp-line-separator.pp-icon-image',
                                    'property'  => 'width',
                                    'unit'      => '%'
                                ),
                                array(
                                    'selector'  => '.pp-line-separator-inner.pp-line-icon:before',
                                    'property'  => 'width',
                                    'unit'      => '%'
                                ),
                                array(
                                    'selector'  => '.pp-line-separator-inner.pp-line-icon:after',
                                    'property'  => 'width',
                                    'unit'      => '%'
                                ),
                            ),
                        )
                    ),
                    'line_height'       => array(
                        'type'          => 'unit',
                        'label'         => __('Line Height', 'bb-powerpack-lite'),
                        'units'			=> array( 'px' ),
						'slider'		=> true,
                        'default'       => '1',
                        'preview'       => array(
                            'type'      => 'css',
                            'rules'           => array(
                               array(
                                   'selector'        => '.pp-line-separator.pp-line-only',
                                   'property'        => 'height',
                                   'unit'            => 'px'
                               ),
                               array(
                                   'selector'        => '.pp-line-separator.pp-line-only',
                                   'property'        => 'border-bottom-width',
                                   'unit'            => 'px'
                               ),
                               array(
                                   'selector'  => '.pp-line-separator.pp-line-icon',
                                   'property'  => 'border-bottom-width',
                                   'unit'      => 'px'
                               ),
                               array(
                                   'selector'  => '.pp-line-separator-inner.pp-line-icon:before',
                                   'property'  => 'border-bottom-width',
                                   'unit'      => 'px'
                               ),
                               array(
                                   'selector'  => '.pp-line-separator-inner.pp-line-icon:after',
                                   'property'  => 'border-bottom-width',
                                   'unit'      => 'px'
                               ),
                           ),
                        )
                    ),
                    'line_color'    => array(
                        'type'          => 'color',
                        'label'         => __('Line Color', 'bb-powerpack-lite'),
                        'default'       => '000000',
						'show_reset'    => true,
						'connections'	=> array('color'),
                        'preview'         => array(
                            'type'            => 'css',
                            'rules'     => array(
                                array(
                                    'selector'        => '.pp-line-separator.pp-line-only',
                                    'property'        => 'border-bottom-color'
                                ),
                                array(
                                    'selector'        => '.pp-line-separator.pp-line-icon',
                                    'property'        => 'border-bottom-color'
                                ),
                                array(
                                    'selector'  => '.pp-line-separator-inner.pp-line-icon:before',
                                    'property'  => 'border-bottom-color',
                                ),
                                array(
                                    'selector'  => '.pp-line-separator-inner.pp-line-icon:after',
                                    'property'  => 'border-bottom-color',
                                ),
                            ),
                        )
                    ),
                )
            ),
            'image_icon_style_section'    => array( // Section
                'title'             => __('Icon Style', 'bb-powerpack-lite'), // Section Title
                'fields'            => array( // Section Fields
					'font_icon_font_size'   => array(
                        'type'          => 'unit',
                        'label'         => __('Icon Size', 'bb-powerpack-lite'),
                        'units'			=> array( 'px' ),
						'slider'		=> true,
                        'default'       => '16',
                        'preview'       => array(
                            'type'      => 'css',
                            'rules'     => array(
                                array(
                                    'selector'  => '.pp-line-separator.pp-icon-wrap span.pp-icon',
                                    'property'  => 'font-size',
                                    'unit'      => 'px'
                                ),
                                array(
                                    'selector'  => '.pp-line-separator.pp-icon-wrap span.pp-icon:before',
                                    'property'  => 'font-size',
                                    'unit'      => 'px'
                                ),
                                array(
                                    'selector'  => '.pp-line-separator.pp-image-wrap img',
                                    'property'  => 'width',
                                    'unit'      => 'px'
                                ),
                            ),
                        )
                    ),
                    'font_icon_color'    => array(
                        'type'          => 'color',
                        'label'         => __('Color', 'bb-powerpack-lite'),
                        'default'       => '000000',
						'show_reset'    => true,
						'connections'	=> array('color'),
                        'preview'         => array(
                            'type'            => 'css',
                            'selector'        => '.pp-line-separator.pp-icon-wrap span.pp-icon',
                            'property'        => 'color'
                        )
                    ),
                    'font_icon_bg_color'    => array(
                        'type'          => 'color',
                        'label'         => __('Background Color', 'bb-powerpack-lite'),
                        'default'       => '',
						'show_reset'    => true,
						'connections'	=> array('color'),
                        'preview'         => array(
                            'type'            => 'css',
                            'rules'           => array(
                                array(
                                    'selector'        => '.pp-line-separator.pp-icon-wrap span.pp-icon',
                                    'property'        => 'background-color'
                                ),
                                array(
                                    'selector'        => '.pp-line-separator.pp-image-wrap',
                                    'property'        => 'background-color'
                                ),
                            ),
                        )
                    ),
                    'font_icon_padding_top_bottom'   => array(
                        'type'          => 'unit',
                        'label'         => __('Padding Top/Bottom', 'bb-powerpack-lite'),
                        'default'       => '0',
                        'units'			=> array( 'px' ),
						'slider'		=> true,
                        'preview'       => array(
                            'type'      => 'css',
                            'rules'           => array(
                               array(
                                   'selector'        => '.pp-line-separator.pp-icon-wrap span.pp-icon',
                                   'property'        => 'padding-top',
                                   'unit'            => 'px'
                               ),
                               array(
                                   'selector'        => '.pp-line-separator.pp-icon-wrap span.pp-icon',
                                   'property'        => 'padding-bottom',
                                   'unit'            => 'px'
                               ),
                               array(
                                   'selector'        => '.pp-line-separator.pp-image-wrap',
                                   'property'        => 'padding-top',
                                   'unit'            => 'px'
                               ),
                               array(
                                   'selector'        => '.pp-line-separator.pp-image-wrap',
                                   'property'        => 'padding-bottom',
                                   'unit'            => 'px'
                               ),
                           ),
                        )
                    ),
                    'font_icon_padding_left_right'   => array(
                        'type'          => 'unit',
                        'label'         => __('Padding Left/Right', 'bb-powerpack-lite'),
                        'default'       => '0',
                        'units'			=> array( 'px' ),
						'slider'		=> true,
                        'preview'       => array(
                            'type'      => 'css',
                            'rules'           => array(
                               array(
                                   'selector'        => '.pp-line-separator.pp-icon-wrap span.pp-icon',
                                   'property'        => 'padding-left',
                                   'unit'            => 'px'
                               ),
                               array(
                                   'selector'        => '.pp-line-separator.pp-icon-wrap span.pp-icon',
                                   'property'        => 'padding-right',
                                   'unit'            => 'px'
                               ),
                               array(
                                   'selector'        => '.pp-line-separator.pp-image-wrap',
                                   'property'        => 'padding-left',
                                   'unit'            => 'px'
                               ),
                               array(
                                   'selector'        => '.pp-line-separator.pp-image-wrap',
                                   'property'        => 'padding-right',
                                   'unit'            => 'px'
                               ),
                           ),
                        )
                    ),
                )
            ),
            'border_section'    => array(
                'title'     => __('Border Styling', 'bb-powerpack-lite'),
                'fields'    => array(
                    'icon_border'	=> array(
						'type'          => 'border',
						'label'         => __( 'Border', 'bb-powerpack-lite' ),
						'responsive'	=> true,
						'preview'   	=> array(
                            'type'  		=> 'css',
                            'selector'  	=> '.pp-line-separator-inner.pp-icon-image .pp-icon-wrap span.pp-icon, .pp-line-separator-inner.pp-icon-image .pp-image-wrap',
                            'property'  	=> 'border',
                        ),
					),
                ),
            ),
        ),
    ),
));
