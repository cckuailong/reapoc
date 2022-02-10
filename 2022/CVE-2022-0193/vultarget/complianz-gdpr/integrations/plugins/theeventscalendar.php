<?php
add_filter( 'cmplz_known_script_tags', 'cmplz_theeventscalendar_script' );
function cmplz_theeventscalendar_script( $tags ) {
	$tags[] = 'the-events-calendar/src/resources/js/embedded-map.';
	return $tags;
}

/**
 * Conditionally add the dependency
 * $deps['wait-for-this-script'] = 'script-that-should-wait';
 */

function cmplz_eventscalendar_dependencies( $tags ) {
	$tags['maps.googleapis.com'] = 'the-events-calendar/src/resources/js/embedded-map.';
	return $tags;
}
add_filter( 'cmplz_dependencies', 'cmplz_eventscalendar_dependencies' );


/**
 * Add placeholder for google maps
 *
 * @param $tags
 *
 * @return mixed
 */

function cmplz_theeventscalendar_placeholder( $tags ) {
	$tags['google-maps'][] = 'tribe-events-venue-map';
	return $tags;
}

add_filter( 'cmplz_placeholder_markers', 'cmplz_theeventscalendar_placeholder' );
