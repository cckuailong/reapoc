<?php

/**
 * @class PPTwitterGridModule
 */
class PPTwitterGridModule extends FLBuilderModule {

	/**
	 * @method __construct
	 */
	public function __construct()
	{
		parent::__construct(array(
			'name'          => __( 'Twitter Embedded Grid', 'bb-powerpack-lite' ),
			'description'   => __( 'A collection timeline displays multiple Tweets curated by a Twitter user in their chosen display order or sorted by time.', 'bb-powerpack-lite' ),
			'group'         	=> pp_get_modules_group(),
			'category'			=> pp_get_modules_cat( 'creative' ),
			'dir'           	=> BB_POWERPACK_DIR . 'modules/pp-twitter-grid/',
			'url'           	=> BB_POWERPACK_URL . 'modules/pp-twitter-grid/',
			'editor_export' 	=> true, // Defaults to true and can be omitted.
			'enabled'       	=> true, // Defaults to true and can be omitted.
		));

		$this->add_js( 'pp-twitter-widgets' );
	}
}

/**
 * Register the module and its form settings.
 */
FLBuilder::register_module('PPTwitterGridModule', array(
	'general'       => array( // Tab
		'title'         => __( 'General', 'bb-powerpack-lite' ), // Tab title
		'description'	=> sprintf(
			__('Please refer these guidelines %s and %s', 'bb-powerpack-lite'),
			'<a href="https://help.twitter.com/en/using-twitter/advanced-tweetdeck-features?lang=browser" target="_blank">https://help.twitter.com/en/using-twitter/advanced-tweetdeck-features?lang=browser</a>',
			'<a href="https://developer.twitter.com/en/docs/tweets/curate-a-collection/overview/overview" target="_blank">https://developer.twitter.com/en/docs/tweets/curate-a-collection/overview/overview</a>'
		),
		'sections'      => array( // Tab Sections
			'general'       => array( // Section
				'title'         => __( 'General', 'bb-powerpack-lite' ), // Section Title
				'fields'        => array( // Section Fields
					'url'     		=> array(
						'type'          => 'text',
						'label'         => __( 'Collection URL', 'bb-powerpack-lite' ),
						'default'       => '',
						'connections'	=> array( 'url' ),
					),
					'footer'  		=> array(
						'type'			=> 'pp-switch',
						'label'         => __( 'Show Footer?', 'bb-powerpack-lite' ),
						'default'       => 'yes',
						'options'       => array(
							'yes'			=> __( 'Yes', 'bb-powerpack-lite' ),
							'no'       		=> __( 'No', 'bb-powerpack-lite' ),
						),
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
					'tweet_limit'	=> array(
						'type'          => 'unit',
						'label'         => __( 'Tweet Limit', 'bb-powerpack-lite' ),
						'default'       => '',
						'slider'		=> true,
					),
				),
			),
		),
	),
));
