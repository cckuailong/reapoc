<?php
defined( 'ABSPATH' ) or die( "you do not have acces to this page!" );

add_filter( 'cmplz_known_script_tags', 'cmplz_pixelyoursite_bing_script' );
function cmplz_pixelyoursite_bing_script( $tags ) {
	$tags[] = 'pixelyoursite-bing/dist';

	return $tags;
}
