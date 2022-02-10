<?php
defined( 'ABSPATH' ) or die( "you do not have acces to this page!" );
define('CMPLZ_GOOGLE_MAPS_INTEGRATION_ACTIVE', true);

add_filter( 'cmplz_known_script_tags', 'cmplz_g1_gmaps_script' );
add_filter( 'cmplz_dependencies', 'cmplz_g1_gmaps_dependencies' );
add_filter( 'cmplz_placeholder_markers', 'cmplz_g1_gmaps_placeholder' );

function cmplz_g1_gmaps_script( $tags ) {
	$tags[] = 'g1-gmaps.js';
	$tags[] = 'infobox_packed.js';
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

function cmplz_g1_gmaps_detected_services( $services ) {
	if ( ! in_array( 'google-maps', $services ) ) {
		$services[] = 'google-maps';
	}

	return $services;
}

add_filter( 'cmplz_detected_services', 'cmplz_g1_gmaps_detected_services' );


/**
 * Add placeholder for google maps
 *
 * @param $tags
 *
 * @return mixed
 */

function cmplz_g1_gmaps_placeholder( $tags ) {
	$tags['google-maps'][] = 'g1gmap-main';

	return $tags;
}



/**
 * Conditionally add the dependency from the plugin core file to the api files
 */

function cmplz_g1_gmaps_dependencies( $tags ) {

	$tags['maps.googleapis.com'] = 'g1-gmaps.js';

	return $tags;
}
