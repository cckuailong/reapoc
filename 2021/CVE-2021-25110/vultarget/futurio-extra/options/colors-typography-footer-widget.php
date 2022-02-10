<?php

if ( !class_exists( 'Kirki' ) ) {
	return;
}


Kirki::add_panel( 'colors', array(
	'priority'	 => 10,
	'title'		 => esc_attr__( 'Colors and Typography', 'futurio-extra' ),
) );

Kirki::add_section( 'footer_typography_section', array(
	'title'		 => esc_attr__( 'Footer widgets', 'futurio-extra' ),
	'panel'		 => 'colors',
	'priority'	 => 10,
) );


/**
 * Footer widget colors
 */
Kirki::add_field( 'futurio_extra', array(
  'type' => 'radio-buttonset',
  'settings' => 'footer_font_color_tab',
  'section' => 'footer_typography_section',
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
	'settings'	 => 'footer_font_color',
	'label'		 => esc_attr__( 'Font', 'futurio-extra' ),
	'section'	 => 'footer_typography_section',
  'choices' => futurio_extra_g_fonts(),
	'transport'	 => 'auto',
	'default'	 => array(
		'font-family'	 => '',
		'variant'		 => '400',
		'letter-spacing' => '0px',
		'font-size'		 => '15px',
		'text-transform' => 'none',
    futurio_extra_col()	=> '',
	),
	'priority'	 => 10,
	'output'	 => array(
		array(
			'element' => '#content-footer-section .widget',
		),
	),
  'active_callback'	 => array(
		array(
			'setting'	 => 'footer_font_color_tab',
			'operator'	 => '==',
			'value'		 => 'desktop',
		),
	),
) );
Kirki::add_field( 'futurio_extra', array(
	'type'		 => 'typography',
	'settings'	 => 'footer_widget_title_color',
	'label'		 => esc_attr__( 'Widget Titles', 'futurio-extra' ),
	'section'	 => 'footer_typography_section',
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
			'element' => '#content-footer-section .widget-title h3',
		),
		array(
			'choice'	 => 'color',
			'property'	 => 'background-color',
			'element'	 => '#content-footer-section .widget-title:after',
		),
    array(
			'choice'	 => 'color',
			'property'	 => 'border-color',
			'element'	 => '#content-footer-section .widget-title h3',
		),
	),
  'active_callback'	 => array(
		array(
			'setting'	 => 'footer_font_color_tab',
			'operator'	 => '==',
			'value'		 => 'desktop',
		),
	),
) );
Kirki::add_field( 'futurio_extra', array(
	'type'		 => 'typography',
	'settings'	 => 'footer_font_color_tablet',
	'label'		 => esc_attr__( 'Font', 'futurio-extra' ),
	'section'	 => 'footer_typography_section',
	'transport'	 => 'auto',
	'default'	 => array(
		'variant'		 => '',
		'letter-spacing' => '',
		'font-size'		 => '',
		'text-transform' => '',
	),
	'priority'	 => 10,
	'output'	 => array(
		array(
			'element' => '#content-footer-section .widget',
      'media_query'	 => '@media (max-width: 991px)',
		),
	),
  'active_callback'	 => array(
		array(
			'setting'	 => 'footer_font_color_tab',
			'operator'	 => '==',
			'value'		 => 'tablet',
		),
	),
) );
Kirki::add_field( 'futurio_extra', array(
	'type'		 => 'typography',
	'settings'	 => 'footer_widget_title_color_tablet',
	'label'		 => esc_attr__( 'Widget Titles', 'futurio-extra' ),
	'section'	 => 'footer_typography_section',
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
			'element' => '#content-footer-section .widget-title h3',
      'media_query'	 => '@media (max-width: 991px)',
		),
		array(
			'choice'	 => 'color',
			'property'	 => 'background-color',
			'element'	 => '#content-footer-section .widget-title:after',
      'media_query'	 => '@media (max-width: 991px)',
		),
    array(
			'choice'	 => 'color',
			'property'	 => 'border-color',
			'element'	 => '#content-footer-section .widget-title h3',
      'media_query'	 => '@media (max-width: 991px)',
		),
	),
  'active_callback'	 => array(
		array(
			'setting'	 => 'footer_font_color_tab',
			'operator'	 => '==',
			'value'		 => 'tablet',
		),
	),
) );
Kirki::add_field( 'futurio_extra', array(
	'type'		 => 'typography',
	'settings'	 => 'footer_font_color_mobile',
	'label'		 => esc_attr__( 'Font', 'futurio-extra' ),
	'section'	 => 'footer_typography_section',
	'transport'	 => 'auto',
	'default'	 => array(
		'variant'		 => '',
		'letter-spacing' => '',
		'font-size'		 => '',
		'text-transform' => '',
	),
	'priority'	 => 10,
	'output'	 => array(
		array(
			'element' => '#content-footer-section .widget',
      'media_query'	 => '@media (max-width: 767px)',
		),
	),
  'active_callback'	 => array(
		array(
			'setting'	 => 'footer_font_color_tab',
			'operator'	 => '==',
			'value'		 => 'mobile',
		),
	),
) );
Kirki::add_field( 'futurio_extra', array(
	'type'		 => 'typography',
	'settings'	 => 'footer_widget_title_color_mobile',
	'label'		 => esc_attr__( 'Widget Titles', 'futurio-extra' ),
	'section'	 => 'footer_typography_section',
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
			'element' => '#content-footer-section .widget-title h3',
      'media_query'	 => '@media (max-width: 767px)',
		),
		array(
			'choice'	 => 'color',
			'property'	 => 'background-color',
			'element'	 => '#content-footer-section .widget-title:after',
      'media_query'	 => '@media (max-width: 767px)',
		),
    array(
			'choice'	 => 'color',
			'property'	 => 'border-color',
			'element'	 => '#content-footer-section .widget-title h3',
      'media_query'	 => '@media (max-width: 767px)',
		),
	),
  'active_callback'	 => array(
		array(
			'setting'	 => 'footer_font_color_tab',
			'operator'	 => '==',
			'value'		 => 'mobile',
		),
	),
) );
Kirki::add_field( 'futurio_extra', array(
	'type'        => 'custom',
	'settings'    => 'footer_typography_section_end',
	'label'       => '<hr/>',
	'section'     => 'footer_typography_section',
	'default'     => '',  
) );
