<?php
defined( 'ABSPATH' ) or die( "you do not have acces to this page!" );

add_filter( 'cmplz_known_script_tags', 'cmplz_openstreetmaps_plugin_script' );
function cmplz_openstreetmaps_plugin_script( $tags ) {

	$tags[] = 'ol.js';
	$tags[] = 'var attribution = new ol.control.Attribution';

	return $tags;
}

add_filter( 'cmplz_dependencies', 'cmplz_openstreetmaps_plugin_dependencies' );
function cmplz_openstreetmaps_plugin_dependencies( $tags ) {
	$tags['ol.js'] = 'ol.control.Attribution';

	return $tags;
}

/**
 * Add services to the list of detected items, so it will get set as default, and will be added to the notice about it
 *
 * @param $services
 *
 * @return array
 */
function cmplz_openstreetmaps_plugin_detected_services( $services ) {

	if ( ! in_array( 'openstreetmaps', $services ) ) {
		$services[] = 'openstreetmaps';
	}

	return $services;
}

add_filter( 'cmplz_detected_services',
	'cmplz_openstreetmaps_plugin_detected_services' );

/**
 * Add placeholder to the list
 *
 * @param $tags
 *
 * @return array
 */

function cmplz_osm_placeholder( $tags ) {
	$tags['openstreetmaps'][] = 'map'; //doesn't work, is too generic

	return $tags;
}

add_filter( 'cmplz_placeholder_markers', 'cmplz_osm_placeholder' );

/**
 * Add some custom css for the placeholder
 */

add_action( 'wp_footer', 'cmplz_openstreetmaps_plugin_css' );
function cmplz_openstreetmaps_plugin_css() {
    ?>
    <style>
        .cmplz-placeholder-element .ol-popup {
            display: none;
        }
    </style>
    <?php
}




