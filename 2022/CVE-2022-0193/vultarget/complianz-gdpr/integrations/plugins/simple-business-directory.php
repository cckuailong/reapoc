<?php
defined( 'ABSPATH' ) or die( "you do not have acces to this page!" );

add_filter( 'cmplz_known_script_tags', 'cmplz_sbd_script' );
function cmplz_sbd_script( $tags ) {
	$tags[] = 'maps.googleapis.com';
	$tags[] = 'directory-script.js';
	$tags[] = 'category-tab.js';

	return $tags;
}

/**
 * Add services to the list of detected items, so it will get set as default, and will be added to the notice about it
 *
 * @param $services
 *
 * @return array
 */

function cmplz_sbd_detected_services( $services ) {
	if ( ! in_array( 'google-maps', $services ) ) {
		$services[] = 'google-maps';
	}
	return $services;
}

add_filter( 'cmplz_detected_services', 'cmplz_sbd_detected_services' );


/**
 * Add placeholder for google maps
 *
 * @param $tags
 *
 * @return mixed
 */

function cmplz_sbd_placeholder( $tags ) {
	$tags['google-maps'][] = 'sbd-single-item-map';
	return $tags;
}

add_filter( 'cmplz_placeholder_markers', 'cmplz_sbd_placeholder' );


/**
 * Conditionally add the dependency from the plugin core file to the api files
 */

add_filter( 'cmplz_dependencies', 'cmplz_sbd_dependencies' );
function cmplz_sbd_dependencies( $tags ) {
	$tags['maps.googleapis.com'] = 'directory-script.js';
	return $tags;
}
