<?php
defined( 'ABSPATH' ) or die( "you do not have acces to this page!" );

add_filter( 'cmplz_known_script_tags', 'cmplz_trustpulse_script' );
function cmplz_trustpulse_script( $tags ) {

	$tags[] = 'trustpulse.com';
	$tags[] = 'trstplse.com';

	return $tags;
}
