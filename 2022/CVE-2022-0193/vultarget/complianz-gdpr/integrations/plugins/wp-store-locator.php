<?php
defined( 'ABSPATH' ) or die( "you do not have acces to this page!" );

/**
 * Block the script.
 *
 * @param $tags
 *
 * @return array
 */
function cmplz_wpsl_googlemaps_script( $tags ) {
	$tags[] = 'maps.google.com/maps/api/js';
	$tags[] = 'wpsl-gmap';
	$tags[] = 'wpsl-js-js-extra';
	$tags[] = 'wpsl-js-js';

	return $tags;
}
add_filter( 'cmplz_known_script_tags', 'cmplz_wpsl_googlemaps_script' );

/**
 * Conditionally add the dependency
 * $deps['wait-for-this-script'] = 'script-that-should-wait';
 */

function cmplz_wpsl_maps_dependencies( $tags ) {
	$tags['maps.google.com/maps/api/js'] = 'wpsl-gmap';
	return $tags;
}
add_filter( 'cmplz_dependencies', 'cmplz_wpsl_maps_dependencies' );

/**
 * Add a placeholder to a div with class "wpsl-gmap-canvas"
 * @param $tags
 *
 * @return mixed
 */
function cmplz_wpsl_maps_placeholder( $tags ) {
	$tags['google-maps'][] = "wpsl-gmap-canvas";
	return $tags;
}
add_filter( 'cmplz_placeholder_markers', 'cmplz_wpsl_maps_placeholder' );

/**
 * Add services to the list of detected items, so it will get set as default, and will be added to the notice about it
 *
 * @param $services
 *
 * @return array
 */
function cmplz_wp_google_maps_detected_services( $services ) {
	if ( ! in_array( 'google-maps', $services ) ) {
		$services[] = 'google-maps';
	}

	return $services;
}
add_filter( 'cmplz_detected_services', 'cmplz_wp_google_maps_detected_services' );
