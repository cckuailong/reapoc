<?php
defined( 'ABSPATH' ) or die( "you do not have acces to this page!" );

add_filter( 'cmplz_known_script_tags', 'cmplz_volocation_script' );
function cmplz_volocation_script( $tags ) {
	// $tags[] = 'google-maps-easy';
	$tags[] = 'maps.googleapis.com';
	$tags[] = 'markerclusterer.js';
	$tags[] = 'locator.js';

	return $tags;
}

/**
 * Add services to the list of detected items, so it will get set as default, and will be added to the notice about it
 *
 * @param $services
 *
 * @return array
 */

function cmplz_volocation_detected_services( $services ) {
	if ( ! in_array( 'google-maps', $services ) ) {
		$services[] = 'google-maps';
	}

	return $services;
}

add_filter( 'cmplz_detected_services', 'cmplz_volocation_detected_services' );


/**
 * Add placeholder for google maps
 *
 * @param $tags
 *
 * @return mixed
 */

function cmplz_volocation_placeholder( $tags ) {
	$tags['google-maps'][] = 'voslpmapcontainer';

	return $tags;
}

add_filter( 'cmplz_placeholder_markers', 'cmplz_volocation_placeholder' );

/**
 * Hide element based on consent
 */

add_action( 'wp_footer', 'cmplz_volocation_css' );
function cmplz_volocation_css() {
	?>
	<style>
		.cmplz-status-deny .voslpsearch,  .cmplz-status-deny .col-lg-8 ,
		.cmplz-status-deny #maplist .col-lg-3.overflowscroll {
			display:none;
		}
    .cmplz-status-marketing .voslpsearch,  .cmplz-status-marketing .col-lg-8 ,
		.cmplz-status-marketing #maplist .col-lg-3.overflowscroll {
			display:block;
		}

	</style>

	<?php
}

/**
 * Conditionally add the dependency from the plugin core file to the api files
 */
// add_filter( 'cmplz_dependencies', 'cmplz_volocation_dependencies' );
// function cmplz_volocation_dependencies( $tags ) {
// $tags['maps.googleapis.com'] = 'google-maps-easy';
// 	return $tags;
// }
