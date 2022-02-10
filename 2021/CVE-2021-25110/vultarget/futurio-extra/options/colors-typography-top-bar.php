<?php

if ( !class_exists( 'Kirki' ) ) {
	return;
}


Kirki::add_panel( 'colors', array(
	'priority'	 => 10,
	'title'		 => esc_attr__( 'Colors and Typography', 'futurio-extra' ),
) );

Kirki::add_section( 'top_bar_colors_section', array(
	'title'		 => esc_attr__( 'Top bar', 'futurio-extra' ),
	'panel'		 => 'colors',
	'priority'	 => 10,
) );

/**
 * Top Menu Colors
 */
Kirki::add_field( 'futurio_extra', array(
  'type' => 'radio-buttonset',
  'settings' => 'topmenu_typography_tab',
  'section' => 'top_bar_colors_section',
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
	'settings'	 => 'topmenu_typography',
	'label'		 => esc_attr__( 'Top bar font', 'futurio-extra' ),
	'section'	 => 'top_bar_colors_section',
	'transport'	 => 'auto',
	'default'	 => array(
		'font-family'	 => '',
		'font-size'		 => '12px',
		'variant'		 => '400',
		'letter-spacing' => '0px',
		'text-transform' => 'none',
    futurio_extra_col()	=> '',
	),
  'choices' => futurio_extra_g_fonts(),
	'priority'	 => 10,
	'output'	 => array(
		array(
			'element' => '.top-bar-section',
		), 
	),
  'active_callback'	 => array(
		array(
			'setting'	 => 'topmenu_typography_tab',
			'operator'	 => '==',
			'value'		 => 'desktop',
		),
	),
) );
Kirki::add_field( 'futurio_extra', array(
	'type'		 => 'typography',
	'settings'	 => 'topmenu_typography_tablet',
	'label'		 => esc_attr__( 'Top bar font', 'futurio-extra' ),
	'section'	 => 'top_bar_colors_section',
	'transport'	 => 'auto',
	'default'	 => array(
		'font-size'		 => '',
		'variant'		 => '',
		'letter-spacing' => '',
		'text-transform' => '',
	),
	'priority'	 => 10,
	'output'	 => array(
		array(
			'element' => '.top-bar-section',
      'media_query'	 => '@media (max-width: 991px)',
		), 
	),
  'active_callback'	 => array(
		array(
			'setting'	 => 'topmenu_typography_tab',
			'operator'	 => '==',
			'value'		 => 'tablet',
		),
	),
) );
Kirki::add_field( 'futurio_extra', array(
	'type'		 => 'typography',
	'settings'	 => 'topmenu_typography_mobile',
	'label'		 => esc_attr__( 'Top bar font', 'futurio-extra' ),
	'section'	 => 'top_bar_colors_section',
	'transport'	 => 'auto',
	'default'	 => array(
		'font-size'		 => '',
		'variant'		 => '',
		'letter-spacing' => '',
		'text-transform' => '',
	),
	'priority'	 => 10,
	'output'	 => array(
		array(
			'element' => '.top-bar-section',
      'media_query'	 => '@media (max-width: 767px)',
		), 
	),
  'active_callback'	 => array(
		array(
			'setting'	 => 'topmenu_typography_tab',
			'operator'	 => '==',
			'value'		 => 'mobile',
		),
	),
) );
Kirki::add_field( 'futurio_extra', array(
	'type'        => 'custom',
	'settings'    => 'top_bar_colors_section_end',
	'label'       => '<hr/>',
	'section'     => 'top_bar_colors_section',
	'default'     => '',  
) );