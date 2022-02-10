<?php
defined( 'ABSPATH' ) or die( "you do not have acces to this page!" );
add_filter( 'cmplz_known_script_tags', 'cmplz_custom_googlemaps_script' );
add_filter( 'cmplz_dependencies', 'cmplz_custom_maps_dependencies' );

/**
 * Block the script, and an inline script with string 'initMap'.
 * initMap can also be something else. That's the problem with custom maps :)
 *
 * @param $tags
 *
 * @return array
 */
function cmplz_custom_googlemaps_script( $tags ) {
	$tags[] = 'maps.googleapis.com/maps/api/js';
	$tags[] = 'show_google_map_acf.js';

	return $tags;
}

/**
 * add the dependency
 * $deps['wait-for-this-script'] = 'script-that-should-wait';
 */
function cmplz_custom_maps_dependencies( $tags ) {
	$tags['maps.googleapis.com/maps/api/'] = 'show_google_map_acf.js';

	return $tags;
}
add_filter( 'cmplz_dependencies', 'cmplz_custom_maps_dependencies' );
