<?php
/**
 * add the dependency
 * $deps['wait-for-this-script'] = 'script-that-should-wait';
 */
define('CMPLZ_GOOGLE_MAPS_INTEGRATION_ACTIVE', true);

add_filter( 'cmplz_dependencies', 'cmplz_multimarker_dependencies' );
add_filter( 'cmplz_known_script_tags', 'cmplz_multimarker_script' );
add_filter( 'cmplz_placeholder_markers', 'cmplz_multimarker_placeholder' );

function cmplz_multimarker_dependencies( $tags ) {
	$tags['maps.googleapis.com/maps/api/js'] = 'map-multi-marker/asset/js/';

	return $tags;
}

/**
 * Block the scripts.
 * initMap can also be something else. That's the problem with custom maps :)
 *
 * @param $tags
 *
 * @return array
 */
function cmplz_multimarker_script( $tags ) {
	$tags[] = 'maps.googleapis.com/maps/api/js';
	$tags[] = 'map-multi-marker/asset/js/';

	return $tags;
}

/**
 * Add a placeholder to a div with class "my-maps-class"
 * @param $tags
 *
 * @return mixed
 */
function cmplz_multimarker_placeholder( $tags ) {
	$tags['google-maps'][] = "map-multi-marker";
	return $tags;
}



/**
 * Add services to the list of detected items, so it will get set as default, and will be added to the notice about it
 *
 * @param $services
 *
 * @return array
 */
function cmplz_multimarker_detected_services( $services ) {

	if ( ! in_array( 'google-maps', $services ) ) {
		$services[] = 'google-maps';
	}

	return $services;
}

add_filter( 'cmplz_detected_services', 'cmplz_multimarker_detected_services' );


