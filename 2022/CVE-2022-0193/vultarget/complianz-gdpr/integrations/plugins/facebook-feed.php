<?php
defined( 'ABSPATH' ) or die( "you do not have acces to this page!" );

add_filter( 'cmplz_known_script_tags', 'cmplz_facebook_feed_script' );
function cmplz_facebook_feed_script( $tags ) {

	$tags[] = 'plugins/custom-facebook-feed/js/cff-scripts.js';
	$tags[] = 'plugins/custom-facebook-feed/js/cff-scripts.min.js';


	return $tags;
}

/**
 * Add social media to the list of detected items, so it will get set as default, and will be added to the notice about it
 *
 * @param $social_media
 *
 * @return array
 */

function cmplz_facebook_feed_detected_social_media( $social_media ) {
	if ( ! in_array( 'facebook', $social_media ) ) {
		$social_media[] = 'facebook';
	}

	return $social_media;
}

add_filter( 'cmplz_detected_social_media',
	'cmplz_facebook_feed_detected_social_media' );


/**
 * placeholders will be handled in the service
 */
