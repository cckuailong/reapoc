<?php defined( 'ABSPATH' ) or die( "you do not have acces to this page!" );

/**
 * Whitelisting podcast player inline script.
 * Compatiblity fix for Complianz GDPR/CCPA
 *
 * https://wordpress.org/support/plugin/podcast-player/
 * author: @vedathemes
 */

add_filter ( 'cmplz_script_class',
	function( $class, $total_match, $found ) {
		if ( $found && false !== strpos( $total_match, 'pppublic-js-extra' ) ) {
			$class = 'cmplz-native'; // add cmplz-script for Marketing and cmplz-stats for Statistics
		}
		return $class;
	}, 10 , 3
);
