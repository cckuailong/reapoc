<?php
defined( 'ABSPATH' ) or die( "you do not have acces to this page!" );
define('CMPLZ_GOOGLE_MAPS_INTEGRATION_ACTIVE', true);

add_filter( 'cmplz_known_script_tags', 'cmplz_wp_google_map_plugin_script' );
add_filter( 'cmplz_placeholder_markers', 'cmplz_wp_google_map_plugin_placeholder' );
add_filter( 'cmplz_dependencies', 'cmplz_wp_google_map_plugin_dependencies' );

function cmplz_wp_google_map_plugin_script( $tags ) {
	$tags[] = 'maps.js';
	$tags[] = 'maps.googleapis.com';
	$tags[] = 'maps.google.com';
	$tags[] = 'wpgmp_map';

	return $tags;
}

/**
 * Add services to the list of detected items, so it will get set as default, and will be added to the notice about it
 *
 * @param $services
 *
 * @return array
 */

function cmplz_wp_google_map_plugin_detected_services( $services ) {
	if ( ! in_array( 'google-maps', $services ) ) {
		$services[] = 'google-maps';
	}

	return $services;
}
add_filter( 'cmplz_detected_services', 'cmplz_wp_google_map_plugin_detected_services' );


/**
 * Add placeholder for google maps
 *
 * @param $tags
 *
 * @return mixed
 */

function cmplz_wp_google_map_plugin_placeholder( $tags ) {
	$tags['google-maps'][] = 'wpgmp_map_container';
	return $tags;
}



/**
 * Conditionally add the dependency
 * $deps['wait-for-this-script'] = 'script-that-should-wait';
 */

 function cmplz_wp_google_map_plugin_dependencies( $tags ) {
	 $tags['maps.js'] = 'wpgmp_map';
	 return $tags;
 }
