<?php
defined( 'ABSPATH' ) or die( "you do not have acces to this page!" );

/**
 * User Registration https://nl.wordpress.org/plugins/user-registration/
 */

add_filter( 'cmplz_known_script_tags', 'cmplz_user_registration_script' );
function cmplz_user_registration_script( $tags ) {
	$recaptcha_enabled
		= get_option( 'user_registration_login_options_enable_recaptcha',
		'no' );

	if ( 'yes' == $recaptcha_enabled ) {
		$tags[] = 'user-registration.min.js';
		$tags[] = 'user-registration.js';
	}

	return $tags;
}



/**
 * Conditionally add the dependency
 * $deps['wait-for-this-script'] = 'script-that-should-wait';
 */

add_filter( 'cmplz_dependencies', 'cmplz_userregistration_dependencies' );
function cmplz_userregistration_dependencies( $tags ) {
	$recaptcha_enabled
		= get_option( 'user_registration_login_options_enable_recaptcha',
		'no' );

	if ( 'yes' == $recaptcha_enabled ) {
		$tags['recaptcha/api.js'] = 'user-registration.min.js';
	}

	return $tags;
}
