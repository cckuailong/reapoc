<?php
defined( 'ABSPATH' ) or die( "you do not have acces to this page!" );

add_filter( 'cmplz_known_script_tags', 'cmplz_meks_plugin_script' );
function cmplz_meks_plugin_script( $tags ) {

	$tags[] = 'main-osm.js';
	return $tags;
}

/**
 * Add services to the list of detected items, so it will get set as default, and will be added to the notice about it
 *
 * @param $services
 *
 * @return array
 */
function cmplz_meks_plugin_detected_services( $services ) {

	if ( ! in_array( 'openstreetmaps', $services ) ) {
		$services[] = 'openstreetmaps';
	}

	return $services;
}

add_filter( 'cmplz_detected_services',
	'cmplz_meks_plugin_detected_services' );

/**
 * Add placeholder to the list
 *
 * @param $tags
 *
 * @return array
 */

function cmplz_meks_placeholder( $tags ) {
	$tags['openstreetmaps'][] = 'mks-maps';

	return $tags;
}

add_filter( 'cmplz_placeholder_markers', 'cmplz_meks_placeholder' );
