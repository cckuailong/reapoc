<?php
defined( 'ABSPATH' ) or die( "you do not have acces to this page!" );
define('CMPLZ_GOOGLE_MAPS_INTEGRATION_ACTIVE', true);

add_filter( 'cmplz_known_script_tags', 'cmplz_google_maps_easy_script' );
add_filter( 'cmplz_placeholder_markers', 'cmplz_google_maps_easy_placeholder' );
add_filter( 'cmplz_dependencies', 'cmplz_google_maps_easy_dependencies' );


function cmplz_google_maps_easy_script( $tags ) {
	$tags[] = 'google-maps-easy';
	$tags[] = 'maps.googleapis.com';

	return $tags;
}

/**
 * Add services to the list of detected items, so it will get set as default, and will be added to the notice about it
 *
 * @param $services
 *
 * @return array
 */

function cmplz_google_maps_easy_detected_services( $services ) {
	if ( ! in_array( 'google-maps', $services ) ) {
		$services[] = 'google-maps';
	}

	return $services;
}

add_filter( 'cmplz_detected_services', 'cmplz_google_maps_easy_detected_services' );


/**
 * Add placeholder for google maps
 *
 * @param $tags
 *
 * @return mixed
 */

function cmplz_google_maps_easy_placeholder( $tags ) {
	$tags['google-maps'][] = 'gmpMapDetailsContainer';
	return $tags;
}



/**
 * Conditionally add the dependency from the plugin core file to the api files
 */

function cmplz_google_maps_easy_dependencies( $tags ) {
	$tags['maps.googleapis.com'] = 'google-maps-easy';
	return $tags;
}
