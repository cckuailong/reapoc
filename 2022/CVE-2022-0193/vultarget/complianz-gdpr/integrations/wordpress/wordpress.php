<?php
defined( 'ABSPATH' ) or die( "you do not have acces to this page!" );
/**
 * Tell the consent API we're following the api
 */
$plugin = cmplz_plugin;
add_filter( "wp_consent_api_registered_$plugin", function () {
	return true;
} );
/**
 * Tell plugins to wait for the consenttype to be set
 */
add_filter( 'wp_consent_api_waitfor_consent_hook',
	'cmplz_wordpress_waitfor_consenttype' );
function cmplz_wordpress_waitfor_consenttype( $waitfor ) {
	if ( cmplz_geoip_enabled() ) {
		$waitfor = true;
	}

	return $waitfor;
}

/**
 * Check if WP Consent API is active
 *
 * @return bool
 */
function cmplz_consent_api_active() {
	return function_exists( 'wp_has_consent' );
}

/**
 * If disabled in the wizard, the consent checkbox is disabled, and personal data is not stored.
 */

function cmplz_wordpress_maybe_disable_wordpress_personaldata_storage() {
	if ( ! cmplz_consent_mode() ) {
		if ( cmplz_get_value( 'uses_wordpress_comments' ) === 'yes'
		     && cmplz_get_value( 'block_wordpress_comment_cookies' ) === 'yes'
		) {
			add_filter( 'pre_comment_user_ip',
				'cmplz_wordpress_remove_commentsip' );
			remove_action( 'set_comment_cookies', 'wp_set_comment_cookies',
				10 );
			add_filter( 'comment_form_default_fields',
				'cmplz_wordpress_comment_form_hide_cookies_consent' );
		}
	}
}

add_action( 'init',
	'cmplz_wordpress_maybe_disable_wordpress_personaldata_storage' );


/**
 * Helper function to disable storing of comments ip
 *
 * @param $comment_author_ip
 *
 * @return string
 */

function cmplz_wordpress_remove_commentsip( $comment_author_ip ) {
	return '';
}

/**
 * Remove the WP consent checkbox for comment fields
 *
 * @param $fields
 *
 * @return mixed
 */


function cmplz_wordpress_comment_form_hide_cookies_consent( $fields ) {
	unset( $fields['cookies'] );

	return $fields;
}


/**
 * Consent API
 */
function cmplz_consent_api_add_consent_types( $consenttypes ) {
	$consenttypes = cmplz_get_used_consenttypes();

	return $consenttypes;
}

add_filter( 'wp_consent_types', 'cmplz_consent_api_add_consent_types' );


function cmplz_consent_api_set_cookie_expiry( $expiry ) {
	$expiry = cmplz_get_value( 'cookie_expiry' );

	return $expiry;
}

add_filter( 'wp_consent_types', 'wp_consent_api_cookie_expiration' );


function cmplz_consent_api_get_consenttype( $consenttype ) {
	$consenttype = COMPLIANZ::$company->get_default_consenttype();

	return $consenttype;
}

add_filter( 'wp_get_consent_type', 'cmplz_consent_api_get_consenttype' );



