<?php
defined( 'ABSPATH' ) or die( "you do not have acces to this page!" );

add_filter( 'cmplz_known_script_tags', 'cmplz_pinterest_script' );
function cmplz_pinterest_script( $tags ) {
	$tags[] = 'assets.pinterest.com';
	$tags[] = 'pinmarklet.js';

	return $tags;
}
