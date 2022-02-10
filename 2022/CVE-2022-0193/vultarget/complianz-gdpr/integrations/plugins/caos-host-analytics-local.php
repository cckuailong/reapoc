<?php
defined( 'ABSPATH' ) or die( "you do not have acces to this page!" );

add_filter( 'caos_gtag_script_element_attributes',
	'cmplz_caos_script_classes' );
add_filter( 'caos_analytics_script_element_attributes',
	'cmplz_caos_script_classes' );
function cmplz_caos_script_classes( $attr ) {
	$classes = COMPLIANZ::$cookie_admin->get_statistics_script_classes();
	$attr    .= ' class="' . implode( ' ', $classes ) . '" ';

	return $attr;
}

add_filter( 'cmplz_known_script_tags', 'cmplz_caos_script' );
function cmplz_caos_script( $tags ) {
	$tags[] = 'analytics.js';
	$tags[] = 'gtag.js';
	$tags[] = 'ga.js';

	$classes = COMPLIANZ::$cookie_admin->get_statistics_script_classes();

	//block these only if not anonymous
	if (!in_array('cmplz-native', $classes )) {
		$tags[] = 'caosLocalGa';
		$tags[] = 'CaosGtag';
	}
	return $tags;
}

/**
 * Add the correct classes for the Caos script front-end
 * @param $tag
 * @param $handle
 *
 * @return string|string[]
 */
function cmplz_caos_add_data_attribute($tag, $handle) {
	$frontend = new CAOS_Frontend_Tracking();
	$caos_handle = $frontend->handle;

	if ( $handle != $caos_handle )
		return $tag;

	$classes = COMPLIANZ::$cookie_admin->get_statistics_script_classes();
	$attr = ' class="' . implode( ' ', $classes ) . '" ';

	return str_replace( ' src', " $attr src", $tag );
}
add_filter('script_loader_tag', 'cmplz_caos_add_data_attribute', 10, 2);

/**
 * We remove some actions to integrate fully
 * */

function cmplz_caos_remove_scripts_others() {
	remove_action( 'cmplz_statistics_script',
		array( COMPLIANZ::$cookie_admin, 'get_statistics_script' ), 10 );
}

add_action( 'after_setup_theme', 'cmplz_caos_remove_scripts_others' );


/**
 * Hide the stats configuration options when caos is enabled.
 *
 * @param $fields
 *
 * @return mixed
 */

function cmplz_caos_filter_fields( $fields ) {
	unset( $fields['configuration_by_complianz'] );
	unset( $fields['UA_code'] );
	return $fields;
}

add_filter( 'cmplz_fields', 'cmplz_caos_filter_fields' );


add_filter( 'cmplz_default_value', 'cmplz_caos_set_default', 20, 2 );
function cmplz_caos_set_default( $value, $fieldname ) {
	if ( $fieldname == 'compile_statistics' ) {
		return "google-analytics";
	}

	return $value;
}

/**
 * Remove stuff which is not necessary anymore
 *
 * */

function cmplz_caos_remove_actions() {
	remove_action( 'cmplz_notice_compile_statistics',
		'cmplz_show_compile_statistics_notice', 10 );
}

add_action( 'init', 'cmplz_caos_remove_actions' );

/**
 * Add notice to tell a user to choose Analytics
 *
 * @param $args
 */
function cmplz_caos_show_compile_statistics_notice( $args ) {
	cmplz_sidebar_notice( sprintf( __( "You use %s, which means the answer to this question should be Google Analytics.",
		'complianz-gdpr' ), 'CAOS host analytics locally' ) );
}

add_action( 'cmplz_notice_compile_statistics',
	'cmplz_caos_show_compile_statistics_notice', 10, 1 );

/**
 * Make sure there's no warning about configuring GA anymore
 *
 * @param $warnings
 *
 * @return mixed
 */

function cmplz_caos_filter_warnings( $warnings ) {
	unset( $warnings[ 'ga-needs-configuring' ] );
	return $warnings;
}

add_filter( 'cmplz_warning_types', 'cmplz_caos_filter_warnings' );
