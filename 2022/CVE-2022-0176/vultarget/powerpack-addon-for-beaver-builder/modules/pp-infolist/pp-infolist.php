<?php

/**
 * @class PPInfoListModule
 */
class PPInfoListModule extends FLBuilderModule {

    /**
     * Constructor function for the module. You must pass the
     * name, description, dir and url in an array to the parent class.
     *
     * @method __construct
     */
    public function __construct()
    {
        parent::__construct(array(
            'name'          => __('Info List', 'bb-powerpack-lite'),
            'description'   => __('Addon to display info list.', 'bb-powerpack-lite'),
            'group'         => pp_get_modules_group(),
            'category'		=> pp_get_modules_cat( 'content' ),
            'dir'           => BB_POWERPACK_DIR . 'modules/pp-infolist/',
            'url'           => BB_POWERPACK_URL . 'modules/pp-infolist/',
            'editor_export' => true, // Defaults to true and can be omitted.
            'enabled'       => true, // Defaults to true and can be omitted.
            'partial_refresh'   => true,
        ));

		$this->add_css( BB_POWERPACK_LITE()->fa_css );
    }

	public function filter_settings( $settings, $helper )
	{
		// Handle old link, link_target, link_nofollow fields.
		$settings = PP_Module_Fields::handle_link_field( $settings, array(
			'link'			=> array(
				'type'			=> 'link'
			),
			'link_target'	=> array(
				'type'			=> 'target'
			),
		), 'link' );

		// Handle title's old typography fields.
		$settings = PP_Module_Fields::handle_typography_field( $settings, array(
			'title_font'	=> array(
				'type'			=> 'font'
			),
			'title_font_size'	=> array(
				'type'			=> 'font_size',
			),
		), 'title_typography' );

		// Handle text's old typography fields.
		$settings = PP_Module_Fields::handle_typography_field( $settings, array(
			'text_font'	=> array(
				'type'			=> 'font'
			),
			'text_font_size'	=> array(
				'type'			=> 'font_size',
			),
		), 'text_typography' );

		return $settings;
	}
}

/**
 * Register the module and its form settings.
 */
FLBuilder::register_module('PPInfoListModule', array(
	'general'      => array( // Tab
		'title'         => __('General', 'bb-powerpack-lite'), // Tab title
		'sections'      => array(
            'layouts'   => array(
                'title' => '',
                'fields'    => array(
                    'layouts'   => array(
                        'type'  => 'select',
                        'default'   => '1',
                        'label'     => __('Icon Position', 'bb-powerpack-lite'),
                        'options'   => array(
                            '1'     => __('Left', 'bb-powerpack-lite'),
                            '2'     => __('Right', 'bb-powerpack-lite'),
                            '3'     => __('Top', 'bb-powerpack-lite'),
						),
						'responsive' => true,
						'preview'	=> array(
							'type'	=> 'none',
						),
                    ),
                ),
            ),
            'connector'         => array(
                'title'             => __('Connector Line', 'bb-powerpack-lite'),
                'fields'            => array(
                    'connector_type'    => array(
                        'type'              => 'pp-switch',
                        'label'             => __('Style', 'bb-powerpack-lite'),
                        'default'           => 'dashed',
                        'options'           => array(
                            'none'              => __('None', 'bb-powerpack-lite'),
                            'solid'             => __('Solid', 'bb-powerpack-lite'),
                            'dashed'            => __('Dashed', 'bb-powerpack-lite'),
                            'dotted'            => __('Dotted', 'bb-powerpack-lite'),
                        ),
                        'toggle'  => array(
                            'solid'  => array(
                                'fields'    => array('connector_width', 'connector_color')
                            ),
                            'dashed'  => array(
                                'fields'    => array('connector_width', 'connector_color')
                            ),
                            'dotted'  => array(
                                'fields'    => array('connector_width', 'connector_color')
                            )
                        )
                    ),
                    'connector_width'   => array(
                        'type'              => 'unit',
                        'label'             => __('Width', 'bb-powerpack-lite'),
                        'default'           => '1',
						'slider'			=> true,
						'units'				=> array('px')
                    ),
                    'connector_color'   => array(
                        'type'              => 'color',
                        'label'             => __('Color', 'bb-powerpack-lite'),
                        'default'           => '000000',
						'show_reset'        => true,
						'connections'		=> array('color'),
                    ),
                ),
            ),
		)
	),
    'list_items'    => array(
        'title'     => __('List Items', 'bb-powerpack-lite'),
        'sections'  => array(
            'general'   => array(
                'title'     => '',
                'fields'    => array(
                    'list_items'    => array(
                        'type'          => 'form',
						'label'         => __('List Item', 'bb-powerpack-lite'),
						'form'          => 'pp_list_item', // ID from registered form below
						'preview_text'  => 'title', // Name of a field to use for the preview text
						'multiple'      => true
                    ),
                ),
            ),
        ),
    ),
    'style'     => array(
        'title' => __('Style', 'bb-powerpack-lite'),
        'sections'  => array(
            'style' => array(
                'title' => __('Sizes', 'bb-powerpack-lite'),
                'fields'    => array(
                    'icon_font_size'    => array(
                        'type'          => 'unit',
                        'default'       => '16',
                        'label'         => __('Icon Size', 'bb-powerpack-lite'),
                        'units'   		=> array( 'px' ),
						'slider'		=> true,
						'responsive'	=> true,
                        'preview'       => array(
                            'type'          => 'css',
                            'rules'     => array(
                                array(
                                    'selector'      => '.pp-infolist-icon-inner span',
                                    'property'      => 'font-size',
                                    'unit'          => 'px'
                                ),
                                array(
                                    'selector'      => '.pp-infolist-icon-inner span:before',
                                    'property'      => 'font-size',
                                    'unit'          => 'px'
                                ),
                            ),
                        )
                    ),
                    'icon_box_width'    => array(
                        'type'      	=> 'unit',
                        'label'     	=> __('Icon Box Size', 'bb-powerpack-lite'),
                        'default'       => '40',
                        'units'   		=> array( 'px' ),
						'slider'		=> true,
						'responsive'	=> true,
                        'preview'       => array(
                            'type'      => 'css',
                            'rules'     => array(
                                array(
                                    'selector'      => '.pp-infolist-icon-inner',
                                    'property'      => 'height',
                                    'unit'          => 'px'
                                ),
                                array(
                                    'selector'      => '.pp-infolist-icon-inner',
                                    'property'      => 'width',
                                    'unit'          => 'px'
                                ),
                            ),
                        ),
                    ),
                )
            ),
            'icon_border'   => array(
                'title'         => __('Border', 'bb-powerpack-lite'),
				'collapsed'			=> true,
                'fields'        => array(
                    'show_border'   => array(
                        'type'      => 'pp-switch',
                        'label'     => __('Show Border', 'bb-powerpack-lite'),
                        'default'   => 'no',
                        'options'   => array(
                            'yes'    => __('Yes', 'bb-powerpack-lite'),
                            'no'    => __('No', 'bb-powerpack-lite'),
                        ),
                        'toggle'    => array(
                            'yes'   => array(
                                'fields'    => array ('icon_border_width', 'icon_border_color', 'icon_border_color_hover', 'icon_border_style', 'icon_box_size')
                            )
                        ),
                    ),
                    'icon_border_style'     => array(
                        'type'      => 'pp-switch',
                        'label'     => __('Border Style', 'bb-powerpack-lite'),
                        'default'   => 'solid',
                        'options'   => array(
                            'solid'      => __('Solid', 'bb-powerpack-lite'),
                            'dotted'      => __('Dotted', 'bb-powerpack-lite'),
                            'dashed'      => __('Dashed', 'bb-powerpack-lite'),
                            'double'      => __('Double', 'bb-powerpack-lite'),
                        ),
                    ),
                    'icon_border_width'    => array(
                        'type'          => 'unit',
                        'label'         => __('Border Width', 'bb-powerpack-lite'),
                        'default'       => 1,
                        'units'   		=> array( 'px' ),
						'slider'		=> true,
                        'preview'       => array(
                            'type'          => 'css',
                            'rules'     => array(
                                array(
                                    'selector'      => '.pp-infolist-icon',
                                    'property'      => 'border-width',
                                    'unit'          => 'px'
                                ),
                                array(
                                    'selector'      => '.pp-infolist-icon-inner img',
                                    'property'      => 'border-width',
                                    'unit'          => 'px'
                                ),
                            ),
                        )
                    ),
                    'icon_border_color'    => array(
                        'type'          => 'color',
                        'label'         => __('Border Color', 'bb-powerpack-lite'),
						'show_reset'    => true,
						'connections'	=> array('color'),
                        'preview'       => array(
                            'type'          => 'css',
                            'rules'     => array(
                                array(
                                    'selector'      => '.pp-infolist-icon',
                                    'property'      => 'border-color',
                                ),
                            ),
                        )
                    ),
                    'icon_border_color_hover'    => array(
                        'type'          => 'color',
                        'label'         => __('Border Color Hover', 'bb-powerpack-lite'),
						'show_reset'    => true,
						'connections'	=> array('color'),
                        'preview'       => array(
                            'type'          => 'css',
                            'rules'     => array(
                                array(
                                    'selector'      => '.pp-infolist-icon:hover',
                                    'property'      => 'border-color',
                                ),
                                array(
                                    'selector'      => '.pp-infolist-icon-inner img:hover',
                                    'property'      => 'border-color',
                                ),
                            ),
                        )
                    ),
                    'icon_border_radius'    => array(
                        'type'          => 'unit',
                        'label'         => __('Round Corners', 'bb-powerpack-lite'),
                        'default'       => '0',
                        'units'  	 	=> array( 'px' ),
						'slider'		=> true,
                        'preview'       => array(
                            'type'          => 'css',
                            'rules'     => array(
                                array(
                                    'selector'      => '.pp-infolist-icon',
                                    'property'      => 'border-radius',
                                    'unit'          => 'px'
                                ),
                                array(
                                    'selector'      => '.pp-infolist-icon-inner',
                                    'property'      => 'border-radius',
                                    'unit'          => 'px'
                                ),
                                array(
                                    'selector'      => '.pp-infolist-icon-inner span.pp-icon',
                                    'property'      => 'border-radius',
                                    'unit'          => 'px'
                                ),
                                array(
                                    'selector'      => '.pp-infolist-icon-inner img',
                                    'property'      => 'border-radius',
                                    'unit'          => 'px'
                                ),
                            ),
                        )
                    ),
                ),
            ),
            'icon_spacing'   => array(
                'title'          => __('Spacing', 'bb-powerpack-lite'),
				'collapsed'			=> true,
                'fields'        => array(
                    'list_spacing'  => array(
                        'type'      => 'unit',
                        'label'     => __('List Spacing', 'bb-powerpack-lite'),
                        'default'   => 25,
                        'help'      => __('Spacing between two list items.', 'bb-powerpack-lite'),
                        'units'   		=> array( 'px' ),
						'slider'		=> true,
						'responsive'	=> true,
                        'preview'       => array(
                            'type'      => 'css',
                            'selector'  => '.pp-infolist-wrap .pp-list-item',
                            'property'  => 'padding-bottom',
                            'unit'      => 'px'
                        ),
                    ),
                    'icon_gap'  => array(
                        'type'      => 'unit',
                        'label'     => __('Icon Spacing', 'bb-powerpack-lite'),
                        'default'   => 20,
                        'help'   => __('Distance between icon and content.', 'bb-powerpack-lite'),
                        'units'   		=> array( 'px' ),
						'slider'		=> true,
						'responsive'	=> true,
                        'preview'       => array(
                            'type'      => 'css',
                            'rules'     => array(
                                array(
                                    'selector'  => '.pp-infolist-wrap .layout-1 .pp-icon-wrapper',
                                    'property'  => 'margin-right',
                                    'unit'      => 'px'
                                ),
                                array(
                                    'selector'  => '.pp-infolist-wrap .layout-2 .pp-icon-wrapper',
                                    'property'  => 'margin-left',
                                    'unit'      => 'px'
                                ),
                                array(
                                    'selector'  => '.pp-infolist-wrap .layout-3 .pp-icon-wrapper',
                                    'property'  => 'margin-bottom',
                                    'unit'      => 'px'
                                ),
                            ),
                        ),
                    ),
                    'icon_box_size'     => array(
                        'type'          => 'unit',
                        'default'     => '0',
                        'label'         => __('Inside Spacing', 'bb-powerpack-lite'),
                        'units'   		=> array( 'px' ),
						'slider'		=> true,
						'responsive'	=> true,
                        'help'      => __('Space between icon and the border', 'bb-powerpack-lite'),
                        'preview'       => array(
                            'type'          => 'css',
                            'rules'           => array(
                                array(
                                    'selector'      => '.pp-infolist-icon-inner img',
                                    'property'     => 'padding',
                                    'unit'          => 'px'
                                ),
                                array(
                                    'selector'      => '.pp-infolist-icon',
                                    'property'     => 'padding',
                                    'unit'          => 'px'
                                ),
                            ),
                        )
                    ),
                )
            ),
        ),
    ),
    'typography'      => array( // Tab
		'title'         => __('Typography', 'bb-powerpack-lite'), // Tab title
		'sections'      => array( // Tab Sections
            'general'     => array(
                'title'     => __('Title', 'bb-powerpack-lite'),
                'fields'    => array(
					'title_tag'	=> array(
						'type'		=> 'select',
						'label'		=> __('HTML Tag', 'bb-powerpack-lite'),
						'default'	=> 'h3',
						'options'	=> array(
							'h1'		=> 'h1',
							'h2'		=> 'h2',
							'h3'		=> 'h3',
							'h4'		=> 'h4',
							'h5'		=> 'h5',
							'h6'		=> 'h6',
							'p'			=> 'p',
							'span'		=> 'span',
							'div'		=> 'div'
						)
					),
                    'title_color'    => array(
						'type'          => 'color',
						'label'         => __('Color', 'bb-powerpack-lite'),
						'show_reset'    => true,
						'connections'	=> array('color'),
                        'preview'       => array(
                            'type'          => 'css',
                            'rules'     => array(
                                array(
                                    'selector'      => '.pp-infolist-title h3',
                                    'property'      => 'color',
                                ),
                                array(
                                    'selector'      => '.pp-infolist-title a h3',
                                    'property'      => 'color',
                                ),
                            ),
                        )
					),
                   'title_typography'	=> array(
						'type'			=> 'typography',
						'label'			=> __('Typography', 'bb-powerpack-lite'),
						'responsive'  	=> true,
						'preview'		=> array(
							'type'			=> 'css',
							'selector'		=> '.pp-infolist-title h3',
						),
					),
                    'title_margin'      => array(
                        'type'              => 'pp-multitext',
                        'label'             => __('Margin', 'bb-powerpack-lite'),
                        'description'       => 'px',
                        'default'           => array(
                            'top'               => 0,
                            'bottom'            => 0
                        ),
                        'options'           => array(
                            'top'               => array(
                                'placeholder'       => __('Top', 'bb-powerpack-lite'),
                                'tooltip'           => __('Top', 'bb-powerpack-lite'),
                                'icon'              => 'fa-long-arrow-up',
                                'preview'           => array(
                                    'selector'          => '.pp-infolist-title h3',
                                    'property'          => 'margin-top',
                                    'unit'              => 'px'
                                ),
                            ),
                            'bottom'            => array(
                                'placeholder'       => __('Bottom', 'bb-powerpack-lite'),
                                'tooltip'           => __('Bottom', 'bb-powerpack-lite'),
                                'icon'              => 'fa-long-arrow-down',
                                'preview'           => array(
                                    'selector'          => '.pp-infolist-title h3',
                                    'property'          => 'margin-bottom',
                                    'unit'              => 'px'
                                ),
                            )
                        )
                    ),
                ),
            ),
            'text_typography'   => array(
                'title'     => __('Description', 'bb-powerpack-lite'),
				'collapsed'			=> true,
                'fields'    => array(
                    'text_color'    => array(
						'type'          => 'color',
						'label'         => __('Color', 'bb-powerpack-lite'),
						'show_reset'    => true,
						'connections'	=> array('color'),
                        'preview'       => array(
                            'type'          => 'css',
                            'selector'      => '.pp-infolist-description',
                            'property'      => 'color',
                        )
					),
                   'text_typography'	=> array(
						'type'			=> 'typography',
						'label'			=> __('Typography', 'bb-powerpack-lite'),
						'responsive'  	=> true,
						'preview'		=> array(
							'type'			=> 'css',
							'selector'		=> '.pp-infolist-description',
						),
					),
                ),
            ),
		)
	)
));

FLBuilder::register_settings_form('pp_list_item', array(
	'title' => __('Add Item', 'bb-powerpack-lite'),
	'tabs'  => array(
        'general'      => array( // Tab
			'title'         => __('General', 'bb-powerpack-lite'), // Tab title
			'sections'      => array( // Tab Sections
                'type'      => array(
                    'title'     => __('Icon', 'bb-powerpack-lite'),
                    'fields'    => array(
                        'icon_type'      => array(
                            'type'      => 'select',
                            'label'     => __('Icon Type', 'bb-powerpack-lite'),
                            'default'   => 'icon',
                            'options'   => array(
                                'icon'      => __('Icon', 'bb-powerpack-lite'),
                                'image'      => __('Image', 'bb-powerpack-lite'),
                            ),
                            'toggle'        => array(
                                'icon'      => array(
                                    'fields'        => array('icon_select', 'icon_color', 'icon_color_hover', 'icon_background', 'icon_background_hover'),
                                    'tabs'          => array('icon_styles'),
                                ),
                                'image'      => array(
                                    'fields'        => array('image_select'),
                                ),
                            ),
                        ),
                        'icon_select'       => array(
                            'type'      => 'icon',
                            'label'     => __('Icon', 'bb-powerpack-lite'),
                            'show_remove'    => true,
                        ),
                        'image_select'       => array(
                            'type'      => 'photo',
                            'label'     => __('Image Icon', 'bb-powerpack-lite'),
                            'show_remove'    => true,
                            'connections'   => array( 'photo' ),
                        ),
                        'icon_animation'     => array(
                            'type'      => 'select',
                            'label'     => __('Animation', 'bb-powerpack-lite'),
                            'default'     => 'none',
                            'options'       => array(
    							'none'          => __('None', 'bb-powerpack-lite'),
    							'swing'          => __('Swing', 'bb-powerpack-lite'),
    							'pulse'          => __('Pulse', 'bb-powerpack-lite'),
    							'flash'          => __('Flash', 'bb-powerpack-lite'),
    							'fadeIn'          => __('Fade In', 'bb-powerpack-lite'),
    							'fadeInUp'          => __('Fade In Up', 'bb-powerpack-lite'),
    							'fadeInDown'          => __('Fade In Down', 'bb-powerpack-lite'),
    							'fadeInLeft'          => __('Fade In Left', 'bb-powerpack-lite'),
    							'fadeInRight'          => __('Fade In Right', 'bb-powerpack-lite'),
                                'slideInUp'          => __('Slide In Up', 'bb-powerpack-lite'),
    							'slideInDown'          => __('Slide In Down', 'bb-powerpack-lite'),
                                'slideInLeft'          => __('Slide In Left', 'bb-powerpack-lite'),
    							'slideInRight'          => __('Slide In Right', 'bb-powerpack-lite'),
    							'bounceIn'          => __('Bounce In', 'bb-powerpack-lite'),
                                'bounceInUp'          => __('Bounce In Up', 'bb-powerpack-lite'),
    							'bounceInDown'          => __('Bounce In Down', 'bb-powerpack-lite'),
    							'bounceInLeft'          => __('Bounce In Left', 'bb-powerpack-lite'),
    							'bounceInRight'          => __('Bounce In Right', 'bb-powerpack-lite'),
    							'flipInX'          => __('Flip In X', 'bb-powerpack-lite'),
    							'FlipInY'          => __('Flip In Y', 'bb-powerpack-lite'),
    							'lightSpeedIn'          => __('Light Speed In', 'bb-powerpack-lite'),
    							'rotateIn'          => __('Rotate In', 'bb-powerpack-lite'),
                                'rotateInUpLeft'          => __('Rotate In Up Left', 'bb-powerpack-lite'),
                                'rotateInUpRight'          => __('Rotate In Up Right', 'bb-powerpack-lite'),
    							'rotateInDownLeft'          => __('Rotate In Down Left', 'bb-powerpack-lite'),
    							'rotateInDownRight'          => __('Rotate In Down Right', 'bb-powerpack-lite'),
    							'rollIn'          => __('Roll In', 'bb-powerpack-lite'),
    							'zoomIn'          => __('Zoom In', 'bb-powerpack-lite'),
                                'slideInUp'          => __('Slide In Up', 'bb-powerpack-lite'),
    							'slideInDown'          => __('Slide In Down', 'bb-powerpack-lite'),
    							'slideInLeft'          => __('Slide In Left', 'bb-powerpack-lite'),
    							'slideInRight'          => __('Slide In Right', 'bb-powerpack-lite'),
    						)
                        ),
                        'animation_duration'    => array(
                            'type'      => 'text',
                            'label'     => __('Animation Duration', 'bb-powerpack-lite'),
                            'default'     => '1000',
                            'maxlength'     => '4',
                            'size'      => '5',
                            'description'   => _x( 'ms', 'Value unit for animation duration. Such as: "1s"', 'bb-powerpack-lite' ),
                            'preview'       => array(
                                'type'      => 'css',
                                'selector'  => '.animated',
                                'property'  => 'animation-duration'
                            ),
                        ),
                    ),
                ),
                'title'     => array(
                    'title'     => __('Title', 'bb-powerpack-lite'),
                    'fields'    => array(
                        'title'     => array(
                            'type'      => 'text',
                            'label'     => '',
                            'default'     => '',
                            'connections'   => array( 'string', 'html', 'url' ),
                            'preview'       => array(
    							'type'          => 'text',
    							'selector'      => '.pp-infolist-title h3'
    						)
                        ),
                    ),
                ),
                'description'    => array(
                    'title'         => __('Description', 'bb-powerpack-lite'),
                    'fields'        => array(
                        'description'   => array(
                            'type'      => 'editor',
                            'label'     => '',
                            'default'   => '',
                            'media_buttons' => false,
                            'rows'      => 4,
                            'connections'   => array( 'string', 'html', 'url' ),
                            'preview'   => array(
    							'type'       => 'text',
    							'selector'   => '.pp-infolist-description'
    						)
                        ),
                    ),
                ),
                'link_type'     => array(
                    'title'     => __('Link', 'bb-powerpack-lite'),
                    'fields'    => array(
                        'link_type'     => array(
                            'type'      => 'select',
                            'label'     => __('Link Type', 'bb-powerpack-lite'),
                            'default'     => 'none',
                            'options'   => array(
                                'none'  => __('None', 'bb-powerpack-lite'),
                                'box'  => __('Complete Box', 'bb-powerpack-lite'),
                                'title'  => __('Title Only', 'bb-powerpack-lite'),
                                'read_more'  => __('Read More', 'bb-powerpack-lite'),
                            ),
                            'toggle'    => array(
                                'box'     => array(
                                    'fields'    => array('link')
                                ),
                                'title'     => array(
                                    'fields'    => array('link')
                                ),
                                'read_more'     => array(
                                    'fields'    => array('read_more_text', 'read_more_color', 'read_more_color_hover', 'link', 'read_more_font', 'read_more_font_size')
                                ),
                            )
                        ),
                        'link'  => array(
							'type'          => 'link',
							'label'         => __('Link', 'bb-powerpack-lite'),
							'placeholder'   => 'http://www.example.com',
							'show_target'	=> true,
							'connections'   => array( 'url' ),
							'preview'       => array(
								'type'          => 'none'
							)
						),
                        'read_more_text'     => array(
                            'type'      => 'text',
                            'label'         => __('Text', 'bb-powerpack-lite'),
                            'default'       => __('Read More', 'bb-powerpack-lite'),
                            'preview'       => array(
                                'type'      => 'text',
                                'selector'  => '.pp-more-link'
                            ),
                        ),
                        'read_more_color'    => array(
                            'type'      => 'color',
                            'label'     => __('Link Color', 'bb-powerpack-lite'),
                            'default'   => '000000',
							'show_reset'    => true,
							'connections'	=> array('color'),
                            'preview'   => array(
                                'type'  => 'css',
                                'selector'  => '.pp-more-link',
                                'property'  => 'color'
                            ),
                        ),
                        'read_more_color_hover'    => array(
                            'type'      => 'color',
                            'label'     => __('Link Hover Color', 'bb-powerpack-lite'),
                            'default'   => 'dddddd',
							'show_reset'    => true,
							'connections'	=> array('color'),
                            'preview'   => array(
                                'type'  => 'css',
                                'selector'  => '.pp-more-link:hover',
                                'property'  => 'color'
                            ),
                        ),
                    ),
                ),
            ),
        ),
        'icon_styles'   => array(
            'title'     => __('Icon Style', 'bb-powerpack-lite'),
            'sections'  => array(
                'icon_styles'   => array(
                    'title'     => '',
                    'fields'    => array(
                        'icon_color'    => array(
    						'type'          => 'color',
    						'label'         => __('Color', 'bb-powerpack-lite'),
							'show_reset'    => true,
							'connections'	=> array('color'),
                            'preview'       => array(
                                'type'          => 'css',
                                'selector'      => '.pp-infolist-icon-inner',
                                'property'      => 'color',
                            )
    					),
                        'icon_color_hover'    => array(
    						'type'          => 'color',
    						'label'         => __('Color Hover', 'bb-powerpack-lite'),
							'show_reset'    => true,
							'connections'	=> array('color'),
    					),
                        'icon_background'    => array(
    						'type'          => 'color',
    						'label'         => __('Background', 'bb-powerpack-lite'),
    						'show_reset'    => true,
							'show_alpha'	=> true,
							'connections'	=> array('color'),
                            'preview'       => array(
                                'type'          => 'css',
                                'selector'      => '.pp-infolist-icon-inner .pp-icon',
                                'property'      => 'background',
                            )
    					),
                        'icon_background_hover'    => array(
    						'type'          => 'color',
    						'label'         => __('Background Hover', 'bb-powerpack-lite'),
    						'show_reset'    => true,
							'show_alpha'	=> true,
							'connections'	=> array('color'),
    					),
                    ),
                ),
            ),
        ),
    ),
));
