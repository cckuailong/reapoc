<?php
defined( 'ABSPATH' ) or die( "you do not have acces to this page!" );

/**
 * Make sure swatches is released from the cookie blocker.
 * Can be used for other script ID's well. Copy/Paste/Rename
 */

function cmplz_whitelist_woo_variation( $class, $total_match, $found ) {
	$string = 'woo-variation-swatches-js-extra'; //'string from inline script or source that should be whitelisted'
	if ( $found && false !== strpos( $total_match, $string ) ) {
		$class = 'cmplz-native'; // add cmplz-script for Marketing and cmplz-stats for Statistics
	}
	return $class;
}
add_filter ( 'cmplz_script_class', 'cmplz_whitelist_woo_variation', 10 , 3 );
