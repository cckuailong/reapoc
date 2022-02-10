<?php
defined( 'ABSPATH' ) or die( "you do not have acces to this page!" );

add_filter( 'cmplz_known_script_tags',
	'cmplz_so_widgets_bundle_widget_script' );
function cmplz_so_widgets_bundle_widget_script( $tags ) {
	$tags[] = 'sow.google-map.min.js';

	return $tags;
}

/**
 * Add placeholder to the list
 *
 * @param $tags
 *
 * @return array
 */
function cmplz_so_widgets_bundle_placeholder( $tags ) {

	$tags['google-maps'][] = 'so-widget-sow-google-map';

	return $tags;
}

add_filter( 'cmplz_placeholder_markers',
	'cmplz_so_widgets_bundle_placeholder' );


/**
 * Add services to the list of detected items, so it will get set as default, and will be added to the notice about it
 *
 * @param $services
 *
 * @return array
 */
function cmplz_so_widgets_bundle_detected_services( $services ) {

	if ( ! in_array( 'google-maps', $services ) ) {
		$services[] = 'google-maps';
	}

	return $services;
}

add_filter( 'cmplz_detected_services',
	'cmplz_so_widgets_bundle_detected_services' );
