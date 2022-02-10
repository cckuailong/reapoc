<?php
defined( 'ABSPATH' ) or die( "you do not have acces to this page!" );

add_filter( 'cmplz_known_script_tags', 'cmplz_facebookforwordpress_script' );
function cmplz_facebookforwordpress_script( $tags ) {
	$tags[] = 'fbq';

	return $tags;
}

/**
 * Add social media to the list of detected items, so it will get set as default, and will be added to the notice about it
 *
 * @param $social_media
 *
 * @return array
 */
function cmplz_facebookforwordpress_detected_social_media( $social_media ) {
	if ( ! in_array( 'facebook', $social_media ) ) {
		$social_media[] = 'facebook';
	}

	return $social_media;
}

add_filter( 'cmplz_detected_social_media',
	'cmplz_facebookforwordpress_detected_social_media' );

/**
 * Block pixel image
 */

add_filter( 'cmplz_image_tags', 'cmplz_facebookforwordpress_imagetags' );
function cmplz_facebookforwordpress_imagetags( $tags ) {
	$tags[] = 'https://www.facebook.com/tr';

	return $tags;
}
