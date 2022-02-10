<?php
defined( 'ABSPATH' ) or die( "you do not have acces to this page!" );

add_filter( 'cmplz_known_script_tags', 'cmplz_videopress_script' );
function cmplz_videopress_script( $tags ) {
	$tags[] = 'videopress.com/videopress-iframe.js';

	return $tags;
}


add_filter( 'cmplz_known_iframe_tags', 'cmplz_videopress_iframetags' );
function cmplz_videopress_iframetags( $tags ) {
	$tags[] = 'videopress.com/embed';

	return $tags;
}
