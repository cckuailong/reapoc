<?php

/**
 * @class PPFBButtonModule
 */
class PPFBButtonModule extends FLBuilderModule {

	/**
	 * @method __construct
	 */
	public function __construct()
	{
		parent::__construct(array(
			'name'          	=> __( 'Facebook Button', 'bb-powerpack-lite' ),
			'description'   	=> __( 'A module for fetch facebook button.', 'bb-powerpack-lite' ),
			'group'         	=> pp_get_modules_group(),
			'category'			=> pp_get_modules_cat( 'creative' ),
			'dir'           	=> BB_POWERPACK_DIR . 'modules/pp-facebook-button/',
			'url'           	=> BB_POWERPACK_URL . 'modules/pp-facebook-button/',
			'editor_export' 	=> true, // Defaults to true and can be omitted.
			'enabled'       	=> true, // Defaults to true and can be omitted.
		));
	}
}

/**
 * Register the module and its form settings.
 */
FLBuilder::register_module( 'PPFBButtonModule', array(
	'general'       => array( // Tab
		'title'         => __( 'General', 'bb-powerpack-lite' ), // Tab title
		'description'	=> pp_get_fb_module_desc(),
		'sections'      => array( // Tab Sections
			'general'       => array( // Section
				'title'         => __( 'Button', 'bb-powerpack-lite' ), // Section Title
				'fields'        => array( // Section Fields
					'button_type'  => array(
						'type'			=> 'select',
						'label'         => __( 'Type', 'bb-powerpack-lite' ),
						'default'       => 'like',
						'options'       => array(
							'like'			=> __( 'Like', 'bb-powerpack-lite' ),
							'recommend'		=> __( 'Recommend', 'bb-powerpack-lite' ),
						),
						'toggle'	=> array(
							'recommend'	=> array(
								'fields'	=> array( 'show_share' , 'url_type' ),
							),
							'like'	=> array(
								'fields'	=> array( 'show_share', 'url_type' ),
							),
						),
						'preview'		=> array(
							'type'			=> 'none'
						)
					),
					'layout'  => array(
						'type'			=> 'select',
						'label'         => __( 'Layout', 'bb-powerpack-lite' ),
						'default'       => 'standard',
						'options'       => array(
							'standard'		=> __( 'Standard', 'bb-powerpack-lite' ),
							'button'		=> __( 'Button', 'bb-powerpack-lite' ),
							'button_count'	=> __( 'Button Count', 'bb-powerpack-lite' ),
							'box_count'     => __( 'Box Count', 'bb-powerpack-lite' ),
						),
						'toggle'	=> array(
							'standard'	=> array(
								'fields'	=> array( 'show_faces' ),
							),
						),
						'preview'		=> array(
							'type'			=> 'none'
						)
					),
					'size'  => array(
						'type'			=> 'pp-switch',
						'label'         => __( 'Size', 'bb-powerpack-lite' ),
						'default'       => 'small',
						'options'       => array(
							'small'       	=> __( 'Small', 'bb-powerpack-lite' ),
							'large'			=> __( 'Large', 'bb-powerpack-lite' ),
						),
						'preview'		=> array(
							'type'			=> 'none'
						)
					),
					'color_scheme'  => array(
						'type'			=> 'pp-switch',
						'label'         => __( 'Color Scheme', 'bb-powerpack-lite' ),
						'default'       => 'light',
						'options'       => array(
							'light'       	=> __( 'Light', 'bb-powerpack-lite' ),
							'dark'			=> __( 'Dark', 'bb-powerpack-lite' ),
						),
						'preview'		=> array(
							'type'			=> 'none'
						)
					),
					'show_share'  => array(
						'type'			=> 'pp-switch',
						'label'         => __( 'Share Button', 'bb-powerpack-lite' ),
						'default'       => 'no',
						'options'       => array(
							'yes'       	=> __( 'Yes', 'bb-powerpack-lite' ),
							'no'			=> __( 'No', 'bb-powerpack-lite' ),
						),
						'preview'		=> array(
							'type'			=> 'none'
						)
					),
					'show_faces'  => array(
						'type'			=> 'pp-switch',
						'label'         => __( 'Faces', 'bb-powerpack-lite' ),
						'default'       => 'no',
						'options'       => array(
							'yes'       	=> __( 'Yes', 'bb-powerpack-lite' ),
							'no'			=> __( 'No', 'bb-powerpack-lite' ),
						),
						'preview'		=> array(
							'type'			=> 'none'
						)
					),
					'url_type'  => array(
						'type'			=> 'pp-switch',
						'label'         => __( 'Target URL', 'bb-powerpack-lite' ),
						'default'       => 'current_page',
						'options'       => array(
							'current_page'	=> __( 'Current Page', 'bb-powerpack-lite' ),
							'custom'		=> __( 'Custom', 'bb-powerpack-lite' ),
						),
						'toggle'	=> array(
							'custom'	=> array(
								'fields'	=> array( 'url' ),
							),
						),
						'preview'		=> array(
							'type'			=> 'none'
						)
					),
					'url'	=> array(
						'type'          	=> 'text',
						'label'         	=> __( 'URL', 'bb-powerpack-lite' ),
						'placeholder'		=> __( 'http://your-link.com', 'bb-powerpack-lite' ),
						'connections'   	=> array( 'url' ),
						'preview'			=> array(
							'type'				=> 'none'
						)
					),
					'alignment'	=> array(
						'type'		=> 'align',
						'label'		=> __('Alignment', 'bb-powerpack-lite'),
						'default'	=> 'left',
						'preview'	=> array(
							'type'		=> 'css',
							'selector'	=> '.fl-module-content',
							'property'	=> 'text-align'
						)
					)
				),
			),
		),
	),
));
