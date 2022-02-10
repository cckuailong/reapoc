<?php
defined( 'ABSPATH' ) or die( "you do not have acces to this page!" );
define('CMPLZ_GOOGLE_MAPS_INTEGRATION_ACTIVE', true);

add_filter( 'cmplz_known_script_tags', 'cmplz_novo_map_script' );
function cmplz_novo_map_script( $tags ) {
	$tags[] = 'infobox.js';
	$tags[] = 'maps.googleapis.com';
	$tags[] = 'new google.maps';

	return $tags;
}

/**
 * Add services to the list of detected items, so it will get set as default, and will be added to the notice about it
 *
 * @param $services
 *
 * @return array
 */

function cmplz_novo_map_detected_services( $services ) {
	if ( ! in_array( 'google-maps', $services ) ) {
		$services[] = 'google-maps';
	}
	return $services;
}
add_filter( 'cmplz_detected_services', 'cmplz_novo_map_detected_services' );

/**
 * Initialize Novo Map
 *
 */

function cmplz_novo_initDomContentLoaded() {
	if(!wp_script_is('jquery', 'done')) {
		wp_enqueue_script('jquery');
	}
	ob_start();
	?>
	<script>
		jQuery(document).ready(function ($) {
			$(document).on("cmplzRunAfterAllScripts", cmplz_novo_fire_domContentLoadedEvent);
			function cmplz_novo_fire_domContentLoadedEvent() {
				dispatchEvent(new Event('load'));
			}
		});
	</script>
	<?php
	$script = ob_get_clean();
	$script = str_replace(array('<script>', '</script>'), '', $script);
	wp_add_inline_script( 'jquery', $script );
}
add_action( 'wp_enqueue_scripts', 'cmplz_novo_initDomContentLoaded' );

/**
 * Add placeholder for google maps
 *
 * @param $tags
 *
 * @return mixed
 */

function cmplz_novo_map_placeholder( $tags ) {
	$tags['google-maps'][] = 'novo-map-container';
	return $tags;
}

add_filter( 'cmplz_placeholder_markers', 'cmplz_novo_map_placeholder' );


/**
 * Conditionally add the dependency from the plugin core file to the api files
 */

add_filter( 'cmplz_dependencies', 'cmplz_novo_map_dependencies' );
function cmplz_novo_map_dependencies( $tags ) {
	$tags['maps.googleapis.com'] = 'new google.maps';
	$tags['new google.maps'] = 'infobox.js';
	return $tags;
}
