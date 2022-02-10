<?php
defined( 'ABSPATH' ) or die( "you do not have acces to this page!" );

add_filter( 'cmplz_known_script_tags', 'cmplz_tidio_live_chat_script' );
function cmplz_tidio_live_chat_script( $tags ) {

	$tags[] = 'document.tidioChatCode';
	$tags[] = 'code.tidio.co';

	return $tags;
}

/**
 * Because Tidio default loads async, but the onload even already has passed when the user accepts, we have to disable this.
 *
 * @param $async
 *
 * @return bool
 */
function cmplz_tidio_live_chat_force_non_async( $async ) {
	return false;
}

add_filter( 'option_tidio-async-load',
	'cmplz_tidio_live_chat_force_non_async' );
