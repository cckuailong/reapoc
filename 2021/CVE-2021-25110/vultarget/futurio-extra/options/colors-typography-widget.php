<?php

if ( !class_exists( 'Kirki' ) ) {
	return;
}


Kirki::add_panel( 'colors', array(
	'priority'	 => 10,
	'title'		 => esc_attr__( 'Colors and Typography', 'futurio-extra' ),
) );

Kirki::add_section( 'sidebar_widget_section', array(
	'title'		 => esc_attr__( 'Widget', 'futurio-extra' ),
	'panel'		 => 'colors',
	'priority'	 => 10,
) );

/**
 * Widgets colors
 */
Kirki::add_field( 'futurio_extra', array(
  'type' => 'radio-buttonset',
  'settings' => 'widget_title_color_tab',
  'section' => 'sidebar_widget_section',
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
	'settings'	 => 'awidget_title_color',
	'label'		 => esc_attr__( 'Widget Titles', 'futurio-extra' ),
	'section'	 => 'sidebar_widget_section',
  'choices' => futurio_extra_g_fonts(),
	'transport'	 => 'auto',
	'default'	 => array(
		'font-family'	 => '',
		'font-size'		 => '15px',
		'variant'		 => '400',
		'line-height'	 => '1.6',
		'letter-spacing' => '0px',
    futurio_extra_col()	=> '',
	),
	'priority'	 => 10,
	'output'	 => array(
		array(
			'element' => '#sidebar .widget-title h3',
		),
		array(
			'choice'	 => 'color',
			'property'	 => 'background-color',
			'element'	 => '#sidebar .widget-title:after, .offcanvas-sidebar .widget-title:after',
		),
    array(
			'choice'	 => 'color',
			'property'	 => 'border-color',
			'element'	 => '.widget-title h3',
		),
	),
  'active_callback'	 => array(
		array(
			'setting'	 => 'widget_title_color_tab',
			'operator'	 => '==',
			'value'		 => 'desktop',
		),
	),
) );
Kirki::add_field( 'futurio_extra', array(
	'type'		 => 'typography',
	'settings'	 => 'sidebar_widget_font',
	'label'		 => esc_attr__( 'Font', 'futurio-extra' ),
	'section'	 => 'sidebar_widget_section',
  'choices' => futurio_extra_g_fonts(),
	'transport'	 => 'auto',
	'default'	 => array(
		'font-family'	 => '',
		'font-size'		 => '15px',
		'variant'		 => '400',
		'line-height'	 => '1.6',
		'letter-spacing' => '0px',
    futurio_extra_col()	=> '',
	),
	'priority'	 => 10,
	'output'	 => array(
		array(
			'element' => '.widget',
		),
	),
  'active_callback'	 => array(
		array(
			'setting'	 => 'widget_title_color_tab',
			'operator'	 => '==',
			'value'		 => 'desktop',
		),
	),
) );

Kirki::add_field( 'futurio_extra', array(
	'type'		 => 'typography',
	'settings'	 => 'awidget_title_color_tablet',
	'label'		 => esc_attr__( 'Widget Titles', 'futurio-extra' ),
	'section'	 => 'sidebar_widget_section',
	'transport'	 => 'auto',
	'default'	 => array(
		'font-size'		 => '',
		'line-height'	 => '',
		'letter-spacing' => '',
	),
	'priority'	 => 10,
	'output'	 => array(
		array(
			'element' => '#sidebar .widget-title h3',
      'media_query'	 => '@media (max-width: 991px)',
		),
	),
  'active_callback'	 => array(
		array(
			'setting'	 => 'widget_title_color_tab',
			'operator'	 => '==',
			'value'		 => 'tablet',
		),
	),
) );
Kirki::add_field( 'futurio_extra', array(
	'type'		 => 'typography',
	'settings'	 => 'sidebar_widget_font_tablet',
	'label'		 => esc_attr__( 'Font', 'futurio-extra' ),
	'section'	 => 'sidebar_widget_section',
	'transport'	 => 'auto',
	'default'	 => array(
		'font-size'		 => '',
		'line-height'	 => '',
		'letter-spacing' => '',
	),
	'priority'	 => 10,
	'output'	 => array(
		array(
			'element' => '.widget',
      'media_query'	 => '@media (max-width: 991px)',
		),
	),
  'active_callback'	 => array(
		array(
			'setting'	 => 'widget_title_color_tab',
			'operator'	 => '==',
			'value'		 => 'tablet',
		),
	),
) );
Kirki::add_field( 'futurio_extra', array(
	'type'		 => 'typography',
	'settings'	 => 'awidget_title_color_mobile',
	'label'		 => esc_attr__( 'Widget Titles', 'futurio-extra' ),
	'section'	 => 'sidebar_widget_section',
	'transport'	 => 'auto',
	'default'	 => array(
		'font-size'		 => '',
		'line-height'	 => '',
		'letter-spacing' => '',
	),
	'priority'	 => 10,
	'output'	 => array(
		array(
			'element' => '#sidebar .widget-title h3',
      'media_query'	 => '@media (max-width: 767px)',
		),
	),
  'active_callback'	 => array(
		array(
			'setting'	 => 'widget_title_color_tab',
			'operator'	 => '==',
			'value'		 => 'mobile',
		),
	),
) );
Kirki::add_field( 'futurio_extra', array(
	'type'		 => 'typography',
	'settings'	 => 'sidebar_widget_font_mobile',
	'label'		 => esc_attr__( 'Font', 'futurio-extra' ),
	'section'	 => 'sidebar_widget_section',
	'transport'	 => 'auto',
	'default'	 => array(
		'font-size'		 => '',
		'line-height'	 => '',
		'letter-spacing' => '',
	),
	'priority'	 => 10,
	'output'	 => array(
		array(
			'element' => '.widget',
      'media_query'	 => '@media (max-width: 767px)',
		),
	),
  'active_callback'	 => array(
		array(
			'setting'	 => 'widget_title_color_tab',
			'operator'	 => '==',
			'value'		 => 'mobile',
		),
	),
) );
Kirki::add_field( 'futurio_extra', array(
	'type'        => 'custom',
	'settings'    => 'sidebar_widget_section_end',
	'label'       => '<hr/>',
	'section'     => 'sidebar_widget_section',
	'default'     => '',  
) );