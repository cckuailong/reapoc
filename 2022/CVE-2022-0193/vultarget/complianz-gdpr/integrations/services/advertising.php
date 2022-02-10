<?php
defined( 'ABSPATH' ) or die( "you do not have acces to this page!" );

add_filter( 'cmplz_known_script_tags', 'cmplz_advertising_script' );
function cmplz_advertising_script( $tags ) {
	$tags[] = 'google_ad_client';
	$tags[] = 'pagead/js/adsbygoogle.js';
	$tags[] = 'doubleclick.net';
	$tags[] = 'googlesyndication.com';
	$tags[] = 'googleads.g.doubleclick.net';
	$tags[] = 'advads_tracking_ads';
	$tags[] = 'advanced_ads';

	return $tags;
}


add_filter( 'cmplz_known_iframe_tags', 'cmplz_advertising_iframetags' );
function cmplz_advertising_iframetags( $tags ) {
	$tags[] = 'googleads';
	$tags[] = 'doubleclick';

	return $tags;
}
