<?php

if ( !class_exists( 'Kirki' ) ) {
	return;
}

Kirki::add_panel( 'colors', array(
	'priority'	 => 10,
	'title'		 => esc_attr__( 'Colors and Typography', 'futurio-extra' ),
) );

Kirki::add_section( 'archive_colors_section', array(
	'title'		 => esc_attr__( 'Blog & Archive', 'futurio-extra' ),
	'panel'		 => 'colors',
	'priority'	 => 10,
) );


/**
 * Colors
 */
Kirki::add_field( 'futurio_extra', array(
  'type' => 'radio-buttonset',
  'settings' => 'archive_typography_tab',
  'section' => 'archive_colors_section',
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
	'settings'	 => 'archive_titles_typography',
	'label'		 => esc_attr__( 'Titles', 'futurio-extra' ),
	'section'	 => 'archive_colors_section',
	'transport'	 => 'auto',
  'choices' => futurio_extra_g_fonts(),
	'default'	 => array(
		'font-family'	 => '',
		'font-size'		 => '26px',
		'variant'		 => '300',
		'line-height'	 => '1.6',
		'letter-spacing' => '0px',
    futurio_extra_col()	=> '',
	),
	'priority'	 => 10,
	'output'	 => array(
		array(
			'element' => '.news-item h2.entry-title a',
		),
		array(
			'choice'	 => 'color',
			'element'	 => '.comments-meta a',
		),
	),
  'active_callback'	 => array(
		array(
			'setting'	 => 'archive_typography_tab',
			'operator'	 => '==',
			'value'		 => 'desktop',
		),
	),
) );
Kirki::add_field( 'futurio_extra', array(
	'type'		 => 'typography',
	'settings'	 => 'archive_titles_typography_excerpt',
	'label'		 => esc_attr__( 'Excerpt', 'futurio-extra' ),
	'section'	 => 'archive_colors_section',
  'choices' => futurio_extra_g_fonts(),
	'transport'	 => 'auto',
	'default'	 => array(
		'font-family'	 => '',
		'font-size'		 => '16px',
		'variant'		 => '300',
		'line-height'	 => '1.6',
		'letter-spacing' => '0px', 
    futurio_extra_col()	=> '',
	),
	'priority'	 => 10,
	'output'	 => array(
		array(
			'element' => '.news-item .post-excerpt',
		),
	),
  'active_callback'	 => array(
		array(
			'setting'	 => 'archive_typography_tab',
			'operator'	 => '==',
			'value'		 => 'desktop',
		),
	),
) );
Kirki::add_field( 'futurio_extra', array(
	'type'		 => 'typography',
	'settings'	 => 'archive_titles_typography_tablet',
	'label'		 => esc_attr__( 'Titles', 'futurio-extra' ),
	'section'	 => 'archive_colors_section',
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
			'element' => '.news-item h2.entry-title a',
      'media_query'	 => '@media (max-width: 991px)',
		),
	),
  'active_callback'	 => array(
		array(
			'setting'	 => 'archive_typography_tab',
			'operator'	 => '==',
			'value'		 => 'tablet',
		),
	),
) );
Kirki::add_field( 'futurio_extra', array(
	'type'		 => 'typography',
	'settings'	 => 'archive_titles_typography_excerpt_tablet',
	'label'		 => esc_attr__( 'Excerpt', 'futurio-extra' ),
	'section'	 => 'archive_colors_section',
	'transport'	 => 'auto',
	'default'	 => array(
		'font-size'		 => '',
		'line-height'	 => '',
		'letter-spacing' => '', 
	),
	'priority'	 => 10,
	'output'	 => array(
		array(
			'element' => '.news-item .post-excerpt',
      'media_query'	 => '@media (max-width: 991px)',
		),
	),
  'active_callback'	 => array(
		array(
			'setting'	 => 'archive_typography_tab',
			'operator'	 => '==',
			'value'		 => 'tablet',
		),
	),
) );
Kirki::add_field( 'futurio_extra', array(
	'type'		 => 'typography',
	'settings'	 => 'archive_titles_typography_mobile',
	'label'		 => esc_attr__( 'Titles', 'futurio-extra' ),
	'section'	 => 'archive_colors_section',
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
			'element' => '.news-item h2.entry-title a',
      'media_query'	 => '@media (max-width: 767px)',
		),
	),
  'active_callback'	 => array(
		array(
			'setting'	 => 'archive_typography_tab',
			'operator'	 => '==',
			'value'		 => 'mobile',
		),
	),
) );
Kirki::add_field( 'futurio_extra', array(
	'type'		 => 'typography',
	'settings'	 => 'archive_titles_typography_excerpt_mobile',
	'label'		 => esc_attr__( 'Excerpt', 'futurio-extra' ),
	'section'	 => 'archive_colors_section',
	'transport'	 => 'auto',
	'default'	 => array(
		'font-size'		 => '',
		'line-height'	 => '',
		'letter-spacing' => '', 
	),
	'priority'	 => 10,
	'output'	 => array(
		array(
			'element' => '.news-item .post-excerpt',
      'media_query'	 => '@media (max-width: 767px)',
		),
	),
  'active_callback'	 => array(
		array(
			'setting'	 => 'archive_typography_tab',
			'operator'	 => '==',
			'value'		 => 'mobile',
		),
	),
) );
Kirki::add_field( 'futurio_extra', array(
	'type'        => 'custom',
	'settings'    => 'archive_typography_tab_end',
	'label'       => '<hr/>',
	'section'     => 'archive_colors_section',
	'default'     => '',  
) );