<?php
defined( 'ABSPATH' ) or die( "you do not have acces to this page!" );


/**
 * prevent lazyloading for blocked video's
 */

add_filter( 'cmplz_iframe_html', 'cmplz_wprocket_no_lazyloading' );
function cmplz_wprocket_no_lazyloading( $html ) {

	$html = COMPLIANZ::$cookie_blocker->add_data( $html, 'iframe', 'no-lazy',
		1 );

	return $html;
}
