<?php
defined( 'ABSPATH' ) or die( "you do not have acces to this page!" );

/**
 * When the recaptcha integration is enabled, rate my post won't work anymore before consent.
 * We have to add the script to the cookieblocker to prevent dependency issues.
 */

add_filter( 'cmplz_known_script_tags', 'cmplz_ratemypost_script' );
function cmplz_ratemypost_script( $tags ) {

	if (cmplz_uses_thirdparty('google-recaptcha')) {
		$tags[] = '/js/rate-my-post';
	}

	return $tags;
}
