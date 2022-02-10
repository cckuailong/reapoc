<?php

/**
 * @class PPBreadcrumbsModule
 */
class PPBreadcrumbsModule extends FLBuilderModule {

    /**
     * @method __construct
     */
    public function __construct()
    {
        parent::__construct(array(
            'name'          => __('Breadcrumbs', 'bb-powerpack'),
            'description'   => __('Breadcrumbs module.', 'bb-powerpack'),
            'group'         => pp_get_modules_group(),
            'category'		=> pp_get_modules_cat( 'content' ),
            'dir'           => BB_POWERPACK_DIR . 'modules/pp-breadcrumbs/',
            'url'           => BB_POWERPACK_URL . 'modules/pp-breadcrumbs/',
            'editor_export' => true, // Defaults to true and can be omitted.
            'enabled'       => true, // Defaults to true and can be omitted.
        ));
    }
}

/**
 * Register the module and its form settings.
 */
FLBuilder::register_module('PPBreadcrumbsModule', array(
    'general'       => array( // Tab
        'title'         => __('General', 'bb-powerpack'), // Tab title
        'sections'      => array( // Tab Sections
            'general'       => array( // Section
                'title'         => '', // Section Title
				'description'	=> __('<br>To display breadcrumbs, you need to enable breadcrumbs in respective SEO plugins settings page.', 'bb-powerpack'),
                'fields'        => array( // Section Fields
					'seo_type'      => array(
                        'type'          => 'select',
                        'label'         => __( 'Select Type', 'bb-powerpack' ),
                        'default'       => 'yoast',
                        'options'       => array(
                            'yoast'				=> __( 'Yoast', 'bb-powerpack' ),
                            'rankmath'			=> __( 'Rankmath', 'bb-powerpack' ),
                            'navxt'				=> __( 'Breadcrumb NavXT', 'bb-powerpack' ),
                            'seopress'			=> __( 'SEOPress', 'bb-powerpack' ),
                        ),
						'help'			=> __( 'Select your active SEO plugin', 'bb-powerpack' )
					),
                    'alignment'     => array(
						'type'          => 'align',
						'label'         => __('Alignment', 'bb-powerpack'),
						'default'       => 'left',
						'responsive'	=> true,
						'preview'         => array(
							'type'            => 'css',
							'selector'        => '.pp-breadcrumbs',
							'property'        => 'text-align'
						),
					),
                )
            ),
        )
    ),
	'style'	=> array(
		'title'		=> __( 'Style', 'bb-powerpack' ),
		'sections'	=> array(
			'box'		=> array(
				'title'		=> __( 'Box', 'bb-powerpack' ),
				'fields'	=> array(
					'box_bg_color'	=> array(
						'type'          => 'color',
                        'label'         => __('Background Color', 'bb-powerpack'),
                        'default'       => '',
						'show_reset'    => true,
						'show_remove'	=> true,
						'connections'	=> array('color'),
						'preview'          => array(
							'type'         		=> 'css',
							'selector' 		    => '.pp-breadcrumbs',
							'property'			=> 'background-color'	
						),
					),
					'box_padding'	=> array(
                        'type'				=> 'dimension',
                        'label'				=> __('Padding', 'bb-powerpack'),
						'slider'			=> true,
						'units'				=> array( 'px' ),
                        'preview'			=> array(
                            'type'				=> 'css',
                            'selector'			=> '.pp-breadcrumbs',
                            'property'			=> 'padding',
                            'unit'				=> 'px'
                        ),
                        'responsive'		=> true,
					),
					'box_border'	=> array(
						'type'					=> 'border',
						'label'					=> __('Border', 'bb-powerpack'),
						'responsive'			=> true,
						'preview'				=> array(
							'type'					=> 'css',
							'selector'				=> '.pp-breadcrumbs',
						),
					),
				),
			),
			'links'		=> array(
				'title'		=> __( 'Links', 'bb-powerpack' ),
				'fields'	=> array(
					'link_typography'	=> array(
						'type'        	   => 'typography',
						'label'       	   => __( 'Typography', 'bb-powerpack' ),
						'responsive'  	   => true,
						'preview'          => array(
							'type'         		=> 'css',
							'selector' 		    => '.pp-breadcrumbs a, .pp-breadcrumbs span:not(.separator)',
						),
					),
					'text_color'		=> array(
                        'type'          => 'color',
                        'label'         => __('Text Color', 'bb-powerpack'),
                        'default'       => '',
						'show_reset'    => false,
						'connections'	=> array('color'),
                    ),
					'link_color'		=> array(
                        'type'          => 'color',
                        'label'         => __('Link Color', 'bb-powerpack'),
                        'default'       => '',
						'show_reset'    => false,
						'connections'	=> array('color'),
                    ),
					'link_hover_color'		=> array(
                        'type'          => 'color',
                        'label'         => __('Link Hover Color', 'bb-powerpack'),
                        'default'       => '',
						'show_reset'    => false,
						'connections'	=> array('color'),
                    ),
					'link_bg_color'		=> array(
                        'type'          => 'color',
                        'label'         => __('Background Color', 'bb-powerpack'),
                        'default'       => '',
						'show_reset'    => true,
						'show_remove'	=> true,
						'connections'	=> array('color'),
                    ),
					'link_bg_hover'		=> array(
                        'type'          => 'color',
                        'label'         => __('Background Hover Color', 'bb-powerpack'),
                        'default'       => '',
						'show_reset'    => true,
						'show_remove'	=> true,
						'connections'	=> array('color'),
                    ),
					'link_padding'	=> array(
                        'type'				=> 'dimension',
                        'label'				=> __('Padding', 'bb-powerpack'),
						'slider'			=> true,
						'units'				=> array( 'px' ),
                        'preview'			=> array(
                            'type'				=> 'css',
                            'selector'			=> '.pp-breadcrumbs a, .pp-breadcrumbs span:not(.separator)',
                            'property'			=> 'padding',
                            'unit'				=> 'px'
                        ),
                        'responsive'		=> true,
					),
					'link_spacing' => array(
						'type'          => 'unit',
						'label'         => __('Spacing', 'bb-powerpack'),
						'default'       => '',
						'slider'		=> true,
						'units'		   	=> array( 'px' )
					),
					'link_border'	=> array(
						'type'					=> 'border',
						'label'					=> __('Border', 'bb-powerpack'),
						'responsive'			=> true,
						'preview'				=> array(
							'type'					=> 'css',
							'selector'				=> '.pp-breadcrumbs a, .pp-breadcrumbs span:not(.separator)',
						),
					),
				)
			)
		)
	)
));
