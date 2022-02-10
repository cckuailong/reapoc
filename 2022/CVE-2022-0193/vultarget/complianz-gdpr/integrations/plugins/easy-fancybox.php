<?php
defined( 'ABSPATH' ) or die( "you do not have acces to this page!" );

add_filter( 'cmplz_known_script_tags', 'cmplz_easy_fancybox_script' );
function cmplz_easy_fancybox_script( $tags ) {
	if ( cmplz_uses_thirdparty('youtube') ) {
// 	$tags[] = 'plugins/easy-fancybox/';
	$tags[] = 'fancybox-youtube';
  }
	return $tags;
}
