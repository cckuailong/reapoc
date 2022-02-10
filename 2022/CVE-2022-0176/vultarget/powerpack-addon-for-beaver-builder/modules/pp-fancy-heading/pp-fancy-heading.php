<?php

/**
 * @class PPFancyHeadingModule
 */
class PPFancyHeadingModule extends FLBuilderModule {

    /**
     * Constructor function for the module. You must pass the
     * name, description, dir and url in an array to the parent class.
     *
     * @method __construct
     */
    public function __construct()
    {
        parent::__construct(array(
            'name'          => __('Fancy Heading', 'bb-powerpack-lite'),
            'description'   => __('Fancy Heading module with animated colors and backgroud cliping.', 'bb-powerpack-lite'),
            'group'         => pp_get_modules_group(),
            'category'		=> pp_get_modules_cat( 'creative' ),
            'dir'           => BB_POWERPACK_DIR . 'modules/pp-fancy-heading/',
            'url'           => BB_POWERPACK_URL . 'modules/pp-fancy-heading/',
            'editor_export' => true, // Defaults to true and can be omitted.
            'enabled'       => true, // Defaults to true and can be omitted.
        ));
	}

	public function filter_settings( $settings, $helper ) {
		// Handle Heading's old typography fields.
		$settings = PP_Module_Fields::handle_typography_field( $settings, array(
			'font_family'	=> array(
				'type'			=> 'font'
			),
			'font_size_custom'	=> array(
				'type'          => 'font_size',
				'condition'     => ( isset( $settings->font_size ) && 'custom' == $settings->font_size )
			),
			'line_height_custom'	=> array(
				'type'          => 'line_height',
				'condition'     => ( isset( $settings->line_height ) && 'custom' == $settings->line_height )
			),
			'text_alignment'	=> array(
				'type'			=> 'text_align',
			),
			'letter_spacing'	=> array(
				'type'			=> 'letter_spacing',
			),
		), 'font_typography' );
		
		return $settings;
	}

	public function enqueue_scripts() {
		$this->add_js('modernizr-custom');
	}
}

/**
 * Register the module and its form settings.
 */
FLBuilder::register_module('PPFancyHeadingModule', array(
    'general'       => array( // Tab
        'title'         => __('General', 'bb-powerpack-lite'), // Tab title
        'sections'      => array( // Tab Sections
            'general'       => array( // Section
                'title'         => __('Heading', 'bb-powerpack-lite'), // Section Title
                'fields'        => array( // Section Fields
                    'heading_title'		=> array(
                        'type'          => 'text',
                        'label'         => __('Title', 'bb-powerpack-lite'),
                        'default'       => __('AWESOME TITLE!', 'bb-powerpack-lite'),
                        'connections'   => array( 'string', 'html', 'url' ),
                        'preview'         => array(
                            'type'             => 'text',
                            'selector'         => '.pp-fancy-heading-title',
                        )
                    ),
                    'heading_type'		=> array(
                        'type'          => 'select',
                        'label'         => __('Type', 'bb-powerpack-lite'),
                        'default'       => 'gradient',
                        'options'       => array(
                            'gradient'      => __('Gradient Animation', 'bb-powerpack-lite'),
                            'solid'         => __('Color Animation', 'bb-powerpack-lite'),
                            'fade'          => __('Fade Animation', 'bb-powerpack-lite'),
                            'rotate'        => __('Rotate/Flip Animation', 'bb-powerpack-lite'),
                            'clip'          => __('Clip to Image', 'bb-powerpack-lite')
                        ),
                        'toggle'        => array(
                            'gradient'      => array(
                                'fields'        => array('primary_color', 'secondary_color', 'animation_speed')
                            ),
                            'solid'         => array(
                                'fields'        => array('primary_color', 'animation_speed')
                            ),
                            'fade'         => array(
                                'fields'       => array('primary_color', 'animation_speed')
                            ),
                            'rotate'       => array(
                                'fields'       => array('primary_color', 'animation_speed')
                            ),
                            'clip'          => array(
                                'fields'        => array('bg_image', 'bg_repeat', 'bg_position', 'bg_attachment')
                            )
                        )
                    ),
                    'primary_color'		=> array(
                        'type'          => 'color',
                        'label'         => __('Primary Color', 'bb-powerpack-lite'),
                        'default'       => '255dea',
						'show_reset'    => false,
						'connections'	=> array('color'),
                    ),
                    'secondary_color'	=> array(
                        'type'              => 'color',
                        'label'             => __('Secondary Color', 'bb-powerpack-lite'),
                        'default'           => '34d6e5',
						'show_reset'        => false,
						'connections'		=> array('color'),
                    ),
                    'animation_speed'   => array(
                        'type'              => 'unit',
                        'label'             => __('Animation Speed', 'bb-powerpack-lite'),
                        'default'           => 20,
                        'units'       		=> array('seconds'),
                        'slider'			=> true,
                    ),
                    'bg_image'			=> array(
                        'type'          => 'photo',
                        'label'         => __('Image', 'bb-powerpack-lite'),
                        'show_remove'   => false
                    ),
                    'bg_repeat'			=> array(
                        'type'      => 'select',
                        'label'     => __('Repeat', 'bb-powerpack-lite'),
                        'default'   => 'no-repeat',
                        'options'   => array(
                            'no-repeat' => __('None', 'bb-powerpack-lite'),
                            'repeat'    => __('Tile', 'bb-powerpack-lite'),
                            'repeat-x'  => __('Horizontal', 'bb-powerpack-lite'),
                            'repeat-y'  => __('Vertical', 'bb-powerpack-lite'),
                        ),
                        'help'  => __('Repeat applies to how the image should display in the background. Choosing none will display the image as uploaded. Tile will repeat the image as many times as needed to fill the background horizontally and vertically. You can also specify the image to only repeat horizontally or vertically.', 'bb-powerpack-lite')
                    ),
                    'bg_position'		=> array(
                        'type'              => 'select',
                        'label'             => __('Position', 'bb-powerpack-lite'),
                        'default'           => 'center center',
                        'options'           => array(
                            'left top'          => __('Left Top', 'bb-powerpack-lite'),
                            'left center'       => __('Left Center', 'bb-powerpack-lite'),
                            'left bottom'       => __('Left Bottom', 'bb-powerpack-lite'),
                            'right top'         => __('Right Top', 'bb-powerpack-lite'),
                            'right center'      => __('Right Center', 'bb-powerpack-lite'),
                            'right bottom'      => __('Right Bottom', 'bb-powerpack-lite'),
                            'center top'        => __('Center Top', 'bb-powerpack-lite'),
                            'center center'     => __('Center Center', 'bb-powerpack-lite'),
                            'center bottom'     => __('Center Bottom', 'bb-powerpack-lite'),
                        ),
                        'help'  => __('Position will tell the image where it should sit in the background.', 'bb-powerpack-lite')
                    ),
                    'bg_attachment'		=> array(
                        'type'          => 'pp-switch',
                        'label'         => __('Attachment', 'bb-powerpack-lite'),
                        'default'       => 'scroll',
                        'options'       => array(
                            'scroll'        => __('Scroll', 'bb-powerpack-lite'),
                            'fixed'         => __('Fixed', 'bb-powerpack-lite'),
                        ),
                        'help'  => __('Attachment will specify how the image reacts when scrolling a page. When scrolling is selected, the image will scroll with page scrolling. This is the default setting. Fixed will allow the image to scroll within the background if fill is selected in the scale setting.', 'bb-powerpack-lite')
                    )
                )
            )
        )
    ),
    'typography'    => array(
        'title'         => __('Typography', 'bb-powerpack-lite'),
        'sections'      => array(
            'typography'    => array(
                'title'         => '',
                'fields'        => array(
                    'html_tag'      => array(
                        'type'          => 'select',
                        'label'         => __('HTML Tag', 'bb-powerpack-lite'),
                        'default'       => 'h2',
                        'options'       => array(
                            'h1'            => 'h1',
                            'h2'            => 'h2',
                            'h3'            => 'h3',
                            'h4'            => 'h4',
                            'h5'            => 'h5',
                            'h6'            => 'h6'
                        )
					),
					'font_typography'	=> array(
						'type'        	   => 'typography',
						'label'       	   => __( 'Typography', 'bb-powerpack-lite' ),
						'responsive'  	   => true,
						'preview'          => array(
							'type'         		=> 'css',
							'selector' 		    => '.pp-fancy-heading-title',
						),
					),
                )
            )
        )
    )
));
