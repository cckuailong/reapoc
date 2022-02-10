<?php
defined( 'ABSPATH' ) or die( "you do not have acces to this page!" );

add_filter( 'cmplz_known_script_tags', 'cmplz_addthis_script' );
function cmplz_addthis_script( $tags ) {
	$tags[] = 'addthis.com';
	return $tags;
}
