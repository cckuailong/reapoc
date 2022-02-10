<?php
defined( 'ABSPATH' ) or die( "you do not have acces to this page!" );

add_filter( 'cmplz_known_script_tags', 'cmplz_hotjar_script' );
function cmplz_hotjar_script( $tags ) {

	/**
	 * hotjar should get blocked if
	 * - not privacy friendly or
	 * - privacy friendly AND Germany (consent_for_anonymous_stats = yes)
	 */

	if ( cmplz_get_value( 'consent_for_anonymous_stats' ) === 'yes'
	     || cmplz_get_value( 'hotjar_privacyfriendly' ) !== 'yes'
	) {
		$tags[] = 'static.hotjar.com';
	}

	return $tags;
}
