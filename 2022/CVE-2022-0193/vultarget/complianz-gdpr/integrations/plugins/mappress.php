<?php
defined( 'ABSPATH' ) or die( "you do not have acces to this page!" );
define('CMPLZ_GOOGLE_MAPS_INTEGRATION_ACTIVE', true);

add_filter( 'cmplz_known_script_tags', 'cmplz_mappress_script' );
function cmplz_mappress_script( $tags ) {

	$tags[] = 'mappress-google-maps-for-wordpress/js/mappress';

	return $tags;
}

/**
 * Add some custom css for the placeholder
 */

add_action( 'wp_footer', 'cmplz_mapppress_css' );
function cmplz_mapppress_css() {
	?>
	<style>
		.mapp-main .cmplz-placeholder-element {
			height: 100%;
			width: 100%;
		}
	</style>
	<?php
}

/**
 * Add placeholder to the list
 *
 * @param $tags
 *
 * @return array
 */
function cmplz_mappress_placeholder( $tags ) {
	$tags['google-maps'][] = 'mapp-canvas-panel';

	return $tags;
}

add_filter( 'cmplz_placeholder_markers', 'cmplz_mappress_placeholder' );


/**
 * Add services to the list of detected items, so it will get set as default, and will be added to the notice about it
 *
 * @param $services
 *
 * @return array
 */
function cmplz_mappress_detected_services( $services ) {

	if ( ! in_array( 'google-maps', $services ) ) {
		$services[] = 'google-maps';
	}

	return $services;
}

add_filter( 'cmplz_detected_services', 'cmplz_mappress_detected_services' );
