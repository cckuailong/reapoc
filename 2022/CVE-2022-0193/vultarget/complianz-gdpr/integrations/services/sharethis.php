<?php
defined( 'ABSPATH' ) or die( "you do not have acces to this page!" );

add_filter( 'cmplz_known_script_tags', 'cmplz_sharethis_script' );
function cmplz_sharethis_script( $tags ) {

	$tags[] = 'sharethis.com';

	return $tags;
}
