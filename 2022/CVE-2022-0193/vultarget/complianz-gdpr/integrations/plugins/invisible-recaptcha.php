<?php
defined( 'ABSPATH' ) or die( "you do not have acces to this page!" );

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
