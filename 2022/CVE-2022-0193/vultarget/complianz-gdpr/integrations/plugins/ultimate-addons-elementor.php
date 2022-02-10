<?php
defined( 'ABSPATH' ) or die( "you do not have acces to this page!" );
function cmplz_uafe_initDomContentLoaded() {
	if ( cmplz_uses_thirdparty('google-maps') ) {
		if(!wp_script_is('jquery', 'done')) {
			wp_enqueue_script('jquery');
		}
		ob_start();
		/**
		 * Using the frontend init method is not very nice, as it has some unwanted side effects, but in this case using the runReadyTrigger does not seem to work because UAFE
		 * is adding their own hook. This hook isn't fired with runreadytrigger. As a result, the map does not initialize.
		 */
		?>
		<script>
			jQuery(document).ready(function ($) {
				$(document).on("cmplzRunAfterAllScripts", cmplz_uafe_fire_initOnReadyComponents);
				function cmplz_uafe_fire_initOnReadyComponents() {
					setTimeout(cmplz_uafe_trigger_element, 2000);
				}

				function cmplz_uafe_trigger_element()
				{

					window.elementorFrontend.init();
					// $('.elementor-widget-uael-google-map').each(function () {
					// 	// window.elementorFrontend.init();
					// 	elementorFrontend.elementsHandler.runReadyTrigger( $(this) );
					// });
				}
			});
		</script>
		<?php
		$script = ob_get_clean();
		$script = str_replace(array('<script>', '</script>'), '', $script);
		wp_add_inline_script( 'jquery', $script);
	}
}
add_action( 'wp_enqueue_scripts', 'cmplz_uafe_initDomContentLoaded' );

add_filter( 'cmplz_known_script_tags', 'cmplz_uafe_script' );
function cmplz_uafe_script( $tags ) {

	$tags[] = 'uael-google-map.js';
	$tags[] = 'uael-google-map.js';
	$tags[] = 'maps.googleapis.com';

	return $tags;
}

/**
 * Add services to the list of detected items, so it will get set as default, and will be added to the notice about it
 *
 * @param $services
 *
 * @return array
 */

function cmplz_uafe_detected_services( $services ) {
	if ( ! in_array( 'google-maps', $services ) ) {
		$services[] = 'google-maps';
	}

	return $services;
}

add_filter( 'cmplz_detected_services', 'cmplz_uafe_detected_services' );


/**
 * Add placeholder for google maps
 *
 * @param $tags
 *
 * @return mixed
 */

function cmplz_uafe_placeholder( $tags ) {
	$tags['google-maps'][] = 'uael-google-map-wrap';

	return $tags;
}

add_filter( 'cmplz_placeholder_markers', 'cmplz_uafe_placeholder' );


/**
 * Conditionally add the dependency from the plugin core file to the api files
 */

add_filter( 'cmplz_dependencies', 'cmplz_uafe_dependencies' );
function cmplz_uafe_dependencies( $tags ) {

	$tags['maps.googleapis.com'] = 'uael-google-map.js';

	return $tags;
}
