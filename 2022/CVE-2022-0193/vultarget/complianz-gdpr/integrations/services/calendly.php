<?php
defined( 'ABSPATH' ) or die( "you do not have acces to this page!" );


add_filter( 'cmplz_placeholder_markers', 'cmplz_calendly_placeholder' );
function cmplz_calendly_placeholder( $tags ) {
	$tags['calendly'][] = 'calendly-inline-widget';

	return $tags;
}

add_filter( 'cmplz_known_script_tags', 'cmplz_calendly_script' );
function cmplz_calendly_script( $tags ) {
	$tags[] = 'assets.calendly.com';

	return $tags;
}


add_filter( 'cmplz_known_iframe_tags', 'cmplz_calendly_iframetags' );
function cmplz_calendly_iframetags( $tags ) {
	$tags[] = 'calendly.com';

	return $tags;
}

