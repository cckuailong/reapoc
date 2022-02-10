<?php

if ( !class_exists( 'Kirki' ) ) {
	return;
}

function futurio_extra_do_not_filter_anything( $value ) {
	return $value;
}

Kirki::add_section( 'custom_code_section', array(
	'title'		 => esc_attr__( 'Custom Codes', 'futurio-extra' ),
	'priority'	 => 10,
) );

Kirki::add_field( 'futurio_extra', array(
	'type'			 => 'textarea',
	'settings'		 => 'header-code',
	'label'			 => __( 'Code to be added to the HEAD', 'futurio-extra' ),
	'description'	 => __( 'Suitable for Google Analytics code', 'futurio-extra' ),
	'section'		 => 'custom_code_section',
	'transport'		 => 'postMessage',
	'sanitize_callback' => 'futurio_extra_do_not_filter_anything',
	'default'		 => '',
	'priority'		 => 10,
) );

add_action( 'wp_head', 'futurio_extra_add_googleanalytics', 10 );

function futurio_extra_add_googleanalytics() {
 $header_code = get_theme_mod( 'header-code', '' );
 if ( $header_code ) {
  echo get_theme_mod( 'header-code', '' );
 }
} 


Kirki::add_field( 'futurio_extra', array(
	'type'			 => 'textarea',
	'settings'		 => 'footer-code',
	'label'			 => __( 'Code to be added to the footer', 'futurio-extra' ),
	'section'		 => 'custom_code_section',
	'transport'		 => 'postMessage',
	'sanitize_callback' => 'futurio_extra_do_not_filter_anything',
	'default'		 => '',
	'priority'		 => 10,
) );

add_action( 'wp_footer', 'futurio_extra_add_footer_code' );

function futurio_extra_add_footer_code() {
 $header_code = get_theme_mod( 'footer-code', '' );
 if ( $header_code ) {
  echo get_theme_mod( 'footer-code', '' );
 }
} 