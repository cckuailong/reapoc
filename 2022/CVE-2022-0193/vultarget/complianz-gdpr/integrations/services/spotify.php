<?php
defined( 'ABSPATH' ) or die( "you do not have acces to this page!" );


add_filter( 'cmplz_known_iframe_tags', 'cmplz_spotify_iframetags' );
function cmplz_spotify_iframetags( $tags ) {
	$tags[] = 'open.spotify.com/embed';
	return $tags;
}

/**
 * function to let complianz detect this integration as having placeholders.
 */

function cmplz_spotify_placeholder() {

}
