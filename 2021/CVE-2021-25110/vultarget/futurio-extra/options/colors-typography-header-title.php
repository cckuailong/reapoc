<?php

if ( !class_exists( 'Kirki' ) ) {
	return;
}


Kirki::add_section( 'header_colors_section', array(
	'title'		 => esc_attr__( 'Header & Title', 'futurio-extra' ),
	'panel'		 => 'colors',
	'priority'	 => 10,
) );


/**
 * Header colors
 */
Kirki::add_field( 'futurio_extra', array(
  'type' => 'radio-buttonset',
  'settings' => 'header_typography_tab',
  'section' => 'header_colors_section',
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
	'settings'	 => 'header_typography_site_title',
	'label'		 => esc_attr__( 'Site title font', 'futurio-extra' ),
	'section'	 => 'header_colors_section',
	'transport'	 => 'auto',
	'default'	 => array(
		'font-family'	 => '',
    'color' => '',
		'variant'		 => '700',
		'letter-spacing' => '0px',
		'font-size'		 => '28px',
    'line-height'		 => '32px',
		'text-transform' => 'none',
	),
  'choices' => futurio_extra_g_fonts(),
	'priority'	 => 10,
	'output'	 => array(
		array(
			'element' => '.site-branding-text h1.site-title a:hover, .site-branding-text .site-title a:hover, .site-branding-text h1.site-title, .site-branding-text .site-title, .site-branding-text h1.site-title a, .site-branding-text .site-title a',
		),
	),
  'active_callback'	 => array(
		array(
			'setting'	 => 'header_typography_tab',
			'operator'	 => '==',
			'value'		 => 'desktop',
		),
	),
) );
Kirki::add_field( 'futurio_extra', array(
	'type'		 => 'typography',
	'settings'	 => 'header_typography_site_desc',
	'transport'	 => 'auto',
	'label'		 => esc_attr__( 'Site description font', 'futurio-extra' ),
	'section'	 => 'header_colors_section',
	'default'	 => array(
		'font-family'	 => '',
    'color' => '',
		'variant'		 => '400',
		'letter-spacing' => '0px',
		'font-size'		 => '15px',
    'line-height'		 => '22px',
		'text-transform' => 'none',
	),
  'choices' => futurio_extra_g_fonts(),
	'priority'	 => 10,
	'output'	 => array(
		array(
			'element' => 'p.site-description',
		),
	),
  'active_callback'	 => array(
		array(
			'setting'	 => 'header_typography_tab',
			'operator'	 => '==',
			'value'		 => 'desktop',
		),
	),
) );
Kirki::add_field( 'futurio_extra', array(
	'type'		 => 'typography',
	'settings'	 => 'header_typography_site_title_tablet',
	'label'		 => esc_attr__( 'Site title font', 'futurio-extra' ),
	'section'	 => 'header_colors_section',
	'transport'	 => 'auto',
	'default'	 => array(
		'letter-spacing' => '',
		'font-size'		 => '',
    'line-height'		 => '',
		'text-transform' => '',
	),
	'priority'	 => 10,
	'output'	 => array(
		array(
			'element' => '.site-branding-text h1.site-title a:hover, .site-branding-text .site-title a:hover, .site-branding-text h1.site-title, .site-branding-text .site-title, .site-branding-text h1.site-title a, .site-branding-text .site-title a',
      'media_query'	 => '@media (max-width: 991px)',
		),
	),
  'active_callback'	 => array(
		array(
			'setting'	 => 'header_typography_tab',
			'operator'	 => '==',
			'value'		 => 'tablet',
		),
	),
) );
Kirki::add_field( 'futurio_extra', array(
	'type'		 => 'typography',
	'settings'	 => 'header_typography_site_desc_tablet',
	'transport'	 => 'auto',
	'label'		 => esc_attr__( 'Site description font', 'futurio-extra' ),
	'section'	 => 'header_colors_section',
	'default'	 => array(
		'letter-spacing' => '',
		'font-size'		 => '',
    'line-height'		 => '',
		'text-transform' => '',
	),
  'choices' => futurio_extra_g_fonts(),
	'priority'	 => 10,
	'output'	 => array(
		array(
			'element' => 'p.site-description',
      'media_query'	 => '@media (max-width: 991px)',
		),
	),
  'active_callback'	 => array(
		array(
			'setting'	 => 'header_typography_tab',
			'operator'	 => '==',
			'value'		 => 'tablet',
		),
	),
) );

Kirki::add_field( 'futurio_extra', array(
	'type'		 => 'typography',
	'settings'	 => 'header_typography_site_title_mobile',
	'label'		 => esc_attr__( 'Site title font', 'futurio-extra' ),
	'section'	 => 'header_colors_section',
	'transport'	 => 'auto',
	'default'	 => array(
		'letter-spacing' => '',
		'font-size'		 => '',
    'line-height'		 => '',
		'text-transform' => '',
	),
	'priority'	 => 10,
	'output'	 => array(
		array(
			'element' => '.site-branding-text h1.site-title a:hover, .site-branding-text .site-title a:hover, .site-branding-text h1.site-title, .site-branding-text .site-title, .site-branding-text h1.site-title a, .site-branding-text .site-title a',
      'media_query'	 => '@media (max-width: 767px)',
		),
	),
  'active_callback'	 => array(
		array(
			'setting'	 => 'header_typography_tab',
			'operator'	 => '==',
			'value'		 => 'mobile',
		),
	),
) );
Kirki::add_field( 'futurio_extra', array(
	'type'		 => 'typography',
	'settings'	 => 'header_typography_site_desc_mobile',
	'transport'	 => 'auto',
	'label'		 => esc_attr__( 'Site description font', 'futurio-extra' ),
	'section'	 => 'header_colors_section',
	'default'	 => array(
		'letter-spacing' => '',
		'font-size'		 => '',
    'line-height'		 => '',
		'text-transform' => '',
	),
  'choices' => futurio_extra_g_fonts(),
	'priority'	 => 10,
	'output'	 => array(
		array(
			'element' => 'p.site-description',
      'media_query'	 => '@media (max-width: 767px)',
		),
	),
  'active_callback'	 => array(
		array(
			'setting'	 => 'header_typography_tab',
			'operator'	 => '==',
			'value'		 => 'mobile',
		),
	),
) );

Kirki::add_field( 'futurio_extra', array(
	'type'        => 'custom',
	'settings'    => 'header_colors_section_end',
	'label'       => '<hr/>',
	'section'     => 'header_colors_section',
	'default'     => '',  
) );