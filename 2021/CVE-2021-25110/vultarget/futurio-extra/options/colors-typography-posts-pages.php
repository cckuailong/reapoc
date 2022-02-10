<?php

if ( !class_exists( 'Kirki' ) ) {
	return;
}

Kirki::add_panel( 'colors', array(
	'priority'	 => 10,
	'title'		 => esc_attr__( 'Colors and Typography', 'futurio-extra' ),
) );

Kirki::add_section( 'post_page_colors_section', array(
	'title'		 => esc_attr__( 'Posts & Pages', 'futurio-extra' ),
	'panel'		 => 'colors',
	'priority'	 => 10,
) );


/**
 * Colors
 */
Kirki::add_field( 'futurio_extra', array(
  'type' => 'radio-buttonset',
  'settings' => 'post_page_typography_tab',
  'section' => 'post_page_colors_section',
  'transport' => 'postMessage',
  'default' => 'desktop',
  'choices' => array(
      'desktop' => '<i class="dashicons dashicons-desktop"></i>',
      'tablet' => '<i class="dashicons dashicons-tablet"></i>',
      'mobile' => '<i class="dashicons dashicons-smartphone"></i>',
  ),
));
Kirki::add_field( 'futurio_extra', array(
	'type'		 => 'typography',
	'settings'	 => 'post_page_titles_typography',
	'label'		 => esc_attr__( 'Titles', 'futurio-extra' ),
	'section'	 => 'post_page_colors_section',
	'transport'	 => 'auto',
  'choices' => futurio_extra_g_fonts(),
	'default'	 => array(
		'font-family'	 => '',
		'font-size'		 => '',
		'variant'		 => '',
		'line-height'	 => '',
		'letter-spacing' => '',
    futurio_extra_col()	=> '',
	),
	'priority'	 => 10,
	'output'	 => array(
		array(
			'element' => '.full-head-img h1.single-title, .single-head h1.single-title',
		),
		array(
			'choice'	 => 'color',
			'element'	 => '.comments-meta a',
		),
	),
  'active_callback'	 => array(
		array(
			'setting'	 => 'post_page_typography_tab',
			'operator'	 => '==',
			'value'		 => 'desktop',
		),
	),
) );
Kirki::add_field( 'futurio_extra', array(
	'type'		 => 'typography',
	'settings'	 => 'post_page_titles_typography_excerpt',
	'label'		 => esc_attr__( 'Content', 'futurio-extra' ),
	'section'	 => 'post_page_colors_section',
  'choices' => futurio_extra_g_fonts(),
	'transport'	 => 'auto',
	'default'	 => array(
		'font-family'	 => '',
		'font-size'		 => '',
		'variant'		 => '',
		'line-height'	 => '',
		'letter-spacing' => '', 
    futurio_extra_col()	=> '',
	),
	'priority'	 => 10,
	'output'	 => array(
		array(
			'element' => '.single-entry-summary',
		),
	),
  'active_callback'	 => array(
		array(
			'setting'	 => 'post_page_typography_tab',
			'operator'	 => '==',
			'value'		 => 'desktop',
		),
	),
) );
Kirki::add_field( 'futurio_extra', array(
	'type'		 => 'typography',
	'settings'	 => 'post_page_titles_typography_tablet',
	'label'		 => esc_attr__( 'Titles', 'futurio-extra' ),
	'section'	 => 'post_page_colors_section',
	'transport'	 => 'auto',
	'default'	 => array(
		'font-size'		 => '',
		'variant'		 => '',
		'line-height'	 => '',
		'letter-spacing' => '',
	),
	'priority'	 => 10,
	'output'	 => array(
		array(
			'element' => '.full-head-img h1.single-title, .single-head h1.single-title',
      'media_query'	 => '@media (max-width: 991px)',
		),
	),
  'active_callback'	 => array(
		array(
			'setting'	 => 'post_page_typography_tab',
			'operator'	 => '==',
			'value'		 => 'tablet',
		),
	),
) );
Kirki::add_field( 'futurio_extra', array(
	'type'		 => 'typography',
	'settings'	 => 'post_page_titles_typography_excerpt_tablet',
	'label'		 => esc_attr__( 'Content', 'futurio-extra' ),
	'section'	 => 'post_page_colors_section',
	'transport'	 => 'auto',
	'default'	 => array(
		'font-size'		 => '',
		'line-height'	 => '',
		'letter-spacing' => '', 
	),
	'priority'	 => 10,
	'output'	 => array(
		array(
			'element' => '.single-entry-summary',
      'media_query'	 => '@media (max-width: 991px)',
		),
	),
  'active_callback'	 => array(
		array(
			'setting'	 => 'post_page_typography_tab',
			'operator'	 => '==',
			'value'		 => 'tablet',
		),
	),
) );
Kirki::add_field( 'futurio_extra', array(
	'type'		 => 'typography',
	'settings'	 => 'post_page_titles_typography_mobile',
	'label'		 => esc_attr__( 'Titles', 'futurio-extra' ),
	'section'	 => 'post_page_colors_section',
	'transport'	 => 'auto',
	'default'	 => array(
		'font-size'		 => '',
		'variant'		 => '',
		'line-height'	 => '',
		'letter-spacing' => '',
	),
	'priority'	 => 10,
	'output'	 => array(
		array(
			'element' => '.full-head-img h1.single-title, .single-head h1.single-title',
      'media_query'	 => '@media (max-width: 767px)',
		),
	),
  'active_callback'	 => array(
		array(
			'setting'	 => 'post_page_typography_tab',
			'operator'	 => '==',
			'value'		 => 'mobile',
		),
	),
) );
Kirki::add_field( 'futurio_extra', array(
	'type'		 => 'typography',
	'settings'	 => 'post_page_titles_typography_excerpt_mobile',
	'label'		 => esc_attr__( 'Content', 'futurio-extra' ),
	'section'	 => 'post_page_colors_section',
	'transport'	 => 'auto',
	'default'	 => array(
		'font-size'		 => '',
		'line-height'	 => '',
		'letter-spacing' => '', 
	),
	'priority'	 => 10,
	'output'	 => array(
		array(
			'element' => '.single-entry-summary',
      'media_query'	 => '@media (max-width: 767px)',
		),
	),
  'active_callback'	 => array(
		array(
			'setting'	 => 'post_page_typography_tab',
			'operator'	 => '==',
			'value'		 => 'mobile',
		),
	),
) );
Kirki::add_field( 'futurio_extra', array(
	'type'        => 'custom',
	'settings'    => 'post_page_typography_tab_end',
	'label'       => '<hr/>',
	'section'     => 'post_page_colors_section',
	'default'     => '',  
) );