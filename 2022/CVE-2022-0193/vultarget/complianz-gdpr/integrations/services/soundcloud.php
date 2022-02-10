<?php
defined( 'ABSPATH' ) or die( "you do not have acces to this page!" );


add_filter( 'cmplz_known_iframe_tags', 'cmplz_soundcloud_iframetags' );
function cmplz_soundcloud_iframetags( $tags ) {
	$tags[] = 'soundcloud.com/player';
	return $tags;
}

/**
 * function to let complianz detect this integration as having placeholders.
 */

function cmplz_soundcloud_placeholder() {

}
