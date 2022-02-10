<?php
defined( 'ABSPATH' ) or die( "you do not have acces to this page!" );
if ( !cmplz_integration_plugin_is_active( 'google-maps-easy' ) &&
     !cmplz_integration_plugin_is_active( 'g1-maps' ) &&
     !cmplz_integration_plugin_is_active( 'generatepress-maps' ) &&
     !cmplz_integration_plugin_is_active( 'map-multi-marker' ) &&
     !cmplz_integration_plugin_is_active( 'wp-google-maps' ) &&
     !cmplz_integration_plugin_is_active( 'avada-maps' ) &&
     !cmplz_integration_plugin_is_active( 'wp-google-maps-widget' )
	) {
	add_filter( 'cmplz_placeholder_markers', 'cmplz_acf_placeholder' );
	add_filter( 'cmplz_dependencies', 'cmplz_acf_dependencies' );
	add_filter( 'cmplz_known_script_tags', 'cmplz_acf_script' );
}
function cmplz_acf_script( $tags ) {
	if( acf_get_setting('enqueue_google_maps') ) {
		$tags[] = 'maps.googleapis.com/maps/api/js';
		$tags[] = 'google.maps.MapTypeId';
	}

	return $tags;
}

/**
 * Add placeholder to the list
 *
 * @param $tags
 *
 * @return array
 */

function cmplz_acf_placeholder( $tags ) {
	if( acf_get_setting('enqueue_google_maps') ) {
		$tags['google-maps'][] = 'acf-map';
	}
	return $tags;
}

/**
 * Conditionally add the dependency from the CF 7 inline script to the .js file
 * $deps['wait-for-this-script'] = 'script-that-should-wait';
 */

function cmplz_acf_dependencies( $tags ) {
	if( acf_get_setting('enqueue_google_maps') ) {
		$tags['maps.googleapis.com/maps/api/js'] = 'google.maps.MapTypeId';
	}
	return $tags;
}
