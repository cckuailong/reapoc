<?php
defined( 'ABSPATH' ) or die( "you do not have acces to this page!" );

add_filter( 'cmplz_set_cookies_on_consent', 'cmplz_wp_donottrack_add_cookie' );
function cmplz_wp_donottrack_add_cookie( $cookies ) {
	$cookies['dont_track_me'] = array( '0', '1' );

	return $cookies;
}
