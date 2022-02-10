<?php

/**
 * @class PPTwitterTimelineModule
 */
class PPTwitterTimelineModule extends FLBuilderModule {

	/**
	 * @method __construct
	 */
	public function __construct()
	{
		parent::__construct(array(
			'name'          => __( 'Twitter Embedded Timeline', 'bb-powerpack-lite' ),
			'description'   => __( 'A module to embed twitter timeline.', 'bb-powerpack-lite' ),
			'group'         	=> pp_get_modules_group(),
			'category'			=> pp_get_modules_cat( 'creative' ),
			'dir'           	=> BB_POWERPACK_DIR . 'modules/pp-twitter-timeline/',
			'url'           	=> BB_POWERPACK_URL . 'modules/pp-twitter-timeline/',
			'editor_export' 	=> true, // Defaults to true and can be omitted.
			'enabled'       	=> true, // Defaults to true and can be omitted.
		));

		$this->add_js( 'pp-twitter-widgets' );
	}
}

/**
 * Register the module and its form settings.
 */
FLBuilder::register_module('PPTwitterTimelineModule', array(
	'general'       => array( // Tab
		'title'         => __( 'General', 'bb-powerpack-lite' ), // Tab title
		'sections'      => array( // Tab Sections
			'general'       => array( // Section
				'title'         => __( 'General', 'bb-powerpack-lite' ), // Section Title
				'fields'        => array( // Section Fields
					'username'     	=> array(
						'type'          => 'text',
						'label'         => __( 'User Name', 'bb-powerpack-lite' ),
						'default'       => '',
						'connections'	=> array( 'string' ),
					),
					'theme'	=> array(
						'type'		=> 'pp-switch',
						'label'     => __( 'Theme', 'bb-powerpack-lite' ),
						'default'   => 'light',
						'options'   => array(
							'light'		=> __( 'Light', 'bb-powerpack-lite' ),
							'dark'		=> __( 'Dark', 'bb-powerpack-lite' ),
						),
					),
					'show_replies'	=> array(
						'type'		=> 'pp-switch',
						'label'     => __( 'Show Replies', 'bb-powerpack-lite' ),
						'default'   => 'no',
						'options'   => array(
							'yes'		=> __( 'Yes', 'bb-powerpack-lite' ),
							'no'		=> __( 'No', 'bb-powerpack-lite' ),
						),
					),
					'layout'  => array(
						'type'			=> 'select',
						'label'         => __( 'Layout', 'bb-powerpack-lite' ),
						'default'       => '',
						'options'       => array(
							''				=> '',
							'noheader'		=> __( 'No Header', 'bb-powerpack-lite' ),
							'nofooter'      => __( 'No Footer', 'bb-powerpack-lite' ),
							'noborders'     => __( 'No Borders', 'bb-powerpack-lite' ),
							'transparent'   => __( 'Transparent', 'bb-powerpack-lite' ),
							'noscrollbar'   => __( 'No Scroll Bar', 'bb-powerpack-lite' ),
						),
						'multi-select'  => true,
						'description'	=> __('Press <strong>ctrl + click</strong> OR <strong>cmd + click</strong> OR <strong>shift + click</strong> to select multiple.', 'bb-powerpack-lite')
					),
					'width'     	=> array(
						'type'          => 'unit',
						'label'         => __( 'Width', 'bb-powerpack-lite' ),
						'default'       => '',
						'units'   		=> array( 'px' ),
						'slider'		=> array(
							'min'			=> '1',
							'max'			=> '2000',
							'step'			=> '50'
						),
					),
					'height'     	=> array(
						'type'          => 'unit',
						'label'         => __( 'Height', 'bb-powerpack-lite' ),
						'default'       => '',
						'units'   		=> array( 'px' ),
						'slider'		=> array(
							'min'			=> '1',
							'max'			=> '2000',
							'step'			=> '50'
						),
					),
					'tweet_limit'     	=> array(
						'type'          => 'unit',
						'label'         => __( 'Tweet Limit', 'bb-powerpack-lite' ),
						'default'       => '',
						'slider'		=> true,
					),
					'link_color'     	=> array(
						'type'          => 'color',
						'label'         => __( 'Link Color', 'bb-powerpack-lite' ),
						'show_reset'	=> true,
					),
					'border_color'     	=> array(
						'type'          => 'color',
						'label'         => __( 'Border Color', 'bb-powerpack-lite' ),
						'show_reset'	=> true,
					),
				),
			),
		),
	),
));
