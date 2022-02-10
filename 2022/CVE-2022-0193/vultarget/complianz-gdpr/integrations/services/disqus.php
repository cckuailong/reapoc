<?php
defined( 'ABSPATH' ) or die( "you do not have acces to this page!" );

add_filter( 'cmplz_known_script_tags', 'cmplz_disqus_script' );
function cmplz_disqus_script( $tags ) {
	$tags[] = 'disqus.com';
	return $tags;
}

add_filter( 'cmplz_known_iframe_tags', 'cmplz_disqus_iframetags' );
function cmplz_disqus_iframetags( $tags ) {
	$tags[] = 'disqus.com';
	return $tags;
}
