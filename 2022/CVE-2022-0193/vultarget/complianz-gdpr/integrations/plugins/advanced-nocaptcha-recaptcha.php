<?php
defined( 'ABSPATH' ) or die( "you do not have acces to this page!" );

/**
 * Add blocklist tags
 * @param array $tags
 *
 * @return array
 */
function cmplz_advanced_captcha_nocaptcha_script( $tags ) {
	if (defined('WPCF7_VERSION') && version_compare(WPCF7_VERSION, 5.4, '>=')) return $tags;

	if (cmplz_get_value('block_recaptcha_service') === 'yes'){
		$tags[] = 'google.com/recaptcha/api.js';
		$tags[] = 'var anr_captcha';
	}
	return $tags;
}
add_filter( 'cmplz_known_script_tags', 'cmplz_advanced_captcha_nocaptcha_script' );

/**
 * Conditionally add the dependency
 * $deps['wait-for-this-script'] = 'script-that-should-wait';
 */

function cmplz_advanced_captcha_nocaptcha_dependencies( $tags ) {
	if (defined('WPCF7_VERSION') && version_compare(WPCF7_VERSION, 5.4, '>=')) return $tags;

	if (cmplz_get_value('block_recaptcha_service') === 'yes'){
		$tags[ 'google.com/recaptcha/api.js' ] = 'var anr_captcha';
	}
	return $tags;
}
add_filter( 'cmplz_dependencies', 'cmplz_advanced_captcha_nocaptcha_dependencies' );

/**
 * Add a placeholder
 */

add_filter( 'cmplz_placeholder_markers', 'cmplz_advanced_captcha_nocaptcha_placeholder' );
function cmplz_advanced_captcha_nocaptcha_placeholder( $tags ) {
	if (defined('WPCF7_VERSION') && version_compare(WPCF7_VERSION, 5.4, '>=')) return $tags;

	if (cmplz_get_value('block_recaptcha_service') === 'yes'){
		$tags['google-recaptcha'][] = 'anr_captcha_field_div'; //ultimate member
	}
	return $tags;
}

/**
 * Add css
 */
function cmplz_advanced_captcha_nocaptcha_css() {
	if (defined('WPCF7_VERSION') && version_compare(WPCF7_VERSION, 5.4, '>=')) return;

	if (cmplz_get_value('block_recaptcha_service') === 'yes'){

		?>
	<style>
		.anr_captcha_field .cmplz-blocked-content-container {
			max-width: initial !important;
			height: 80px !important;
			margin-bottom: 20px;
		}

		@media only screen and (max-width: 400px) {
			.anr_captcha_field .cmplz-blocked-content-container, {
				height: 100px !important
			}
		}

		.anr_captcha_field .cmplz-blocked-content-container .cmplz-blocked-content-notice{
			max-width: initial;
			padding: 7px;
		}
	</style>
	<?php
	}
}
add_action( 'wp_footer', 'cmplz_advanced_captcha_nocaptcha_css' );

/**
 * Add services to the list of detected items, so it will get set as default, and will be added to the notice about it
 *
 * @param $services
 *
 * @return array
 */
function cmplz_advanced_captcha_nocaptcha_services( $services ) {
	if (defined('WPCF7_VERSION') && version_compare(WPCF7_VERSION, 5.4, '>=')) return $services;

	if ( ! in_array( 'google-recaptcha', $services ) ) {
		$services[] = 'google-recaptcha';
	}

	return $services;
}
add_filter( 'cmplz_detected_services', 'cmplz_advanced_captcha_nocaptcha_services' );
