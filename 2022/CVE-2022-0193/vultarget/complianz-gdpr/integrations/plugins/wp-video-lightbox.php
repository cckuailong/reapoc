<?php
defined( 'ABSPATH' ) or die( "you do not have acces to this page!" );

add_filter( 'cmplz_known_script_tags', 'cmplz_wp_video_lightbox_script' );
function cmplz_wp_video_lightbox_script( $tags ) {
	if ( cmplz_uses_thirdparty('youtube') ) {
	$tags[] = 'wp-video-lightbox';
  }
	return $tags;
}
