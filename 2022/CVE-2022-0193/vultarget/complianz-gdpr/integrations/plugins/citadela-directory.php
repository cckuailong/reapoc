<?php
defined( 'ABSPATH' ) or die( "you do not have acces to this page!" );

add_filter( 'cmplz_known_script_tags', 'cmplz_citadela_directory_script' );
function cmplz_citadela_directory_script( $tags ) {
	$tags[] = 'leaflet-maps-initializer.js';

	return $tags;
}

/**
 * Add services to the list of detected items, so it will get set as default, and will be added to the notice about it
 *
 * @param $services
 *
 * @return array
 */

function cmplz_citadela_directory_detected_services( $services ) {
	if ( ! in_array( 'openstreetmaps', $services ) ) {
		$services[] = 'openstreepmaps';
	}

	return $services;
}

add_filter( 'cmplz_detected_services', 'cmplz_citadela_directory_detected_services' );


/**
 * Add placeholder for google maps
 *
 * @param $tags
 *
 * @return mixed
 */

function cmplz_citadela_directory_placeholder( $tags ) {
	$tags['openstreetmaps'][] = 'citadela-openstreetmap';

	return $tags;
}

add_filter( 'cmplz_placeholder_markers', 'cmplz_citadela_directory_placeholder' );
