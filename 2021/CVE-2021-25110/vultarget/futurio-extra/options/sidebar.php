<?php

if ( !class_exists( 'Kirki' ) ) {
	return;
}

Kirki::add_section( 'main_sidebar', array(
	'title'		 => esc_attr__( 'Sidebar', 'futurio-extra' ),
	'priority'	 => 10,
) );

Kirki::add_field( 'futurio_extra', array(
	'type'		 => 'radio-buttonset',
	'settings'	 => 'sidebar_position',
	'label'		 => __( 'Sidebar position', 'futurio-extra' ),
	'section'	 => 'main_sidebar',
	'default'	 => 'right',
	'priority'	 => 10,
	'choices'	 => array(
		'left'			 => __( 'Left', 'futurio-extra' ),
		'right'			 => __( 'Right', 'futurio-extra' ),
	),
) );

Kirki::add_field( 'futurio_extra', array(
	'type'		 => 'radio-buttonset',
	'settings'	 => 'sidebar_size',
	'label'		 => __( 'Sidebar size', 'futurio-extra' ),
	'section'	 => 'main_sidebar',
	'default'	 => '3',
	'priority'	 => 10,
	'choices'	 => array(
		'3'			 => __( '3', 'futurio-extra' ),
		'4'			 => __( '4', 'futurio-extra' ),
	),
) );