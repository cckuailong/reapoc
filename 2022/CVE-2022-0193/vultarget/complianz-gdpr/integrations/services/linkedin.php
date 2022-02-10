<?php
defined( 'ABSPATH' ) or die( "you do not have acces to this page!" );

add_filter( 'cmplz_known_script_tags', 'cmplz_linkedin_script' );
function cmplz_linkedin_script( $tags ) {
	$tags[] = 'platform.linkedin.com/in.js';
	return $tags;
}

add_filter( 'cmplz_placeholder_markers', 'cmplz_linkedin_placeholder' );
function cmplz_linkedin_placeholder( $tags ) {
	$tags['linkedin'][] = 'share-update-card';

	return $tags;
}

add_filter( 'cmplz_known_iframe_tags', 'cmplz_linkedin_iframetags' );
function cmplz_linkedin_iframetags( $tags ) {
	$tags[] = 'linkedin.com/embed/feed/update';

	return $tags;
}

/**
 * Add some custom css for the placeholder
 */
