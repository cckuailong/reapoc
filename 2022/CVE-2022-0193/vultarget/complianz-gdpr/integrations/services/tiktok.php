<?php
defined( 'ABSPATH' ) or die( "you do not have acces to this page!" );

add_filter( 'cmplz_known_script_tags', 'cmplz_tiktok_script' );
function cmplz_tiktok_script( $tags ) {
	$tags[] = 'tiktok.com/embed.js';
	$tags[] = 'tiktok.com';
	$tags[] = 'www.tiktok.com/embed.js';
	// $tags[] = 'instawidget.net/js/instawidget.js';

	return $tags;
}

add_filter( 'cmplz_placeholder_markers', 'cmplz_tiktok_placeholder' );
function cmplz_tiktok_placeholder( $tags ) {
	$tags['tiktok'][] = 'tiktok-embed';

	return $tags;
}


add_filter( 'cmplz_known_iframe_tags', 'cmplz_tiktok_iframetags' );
function cmplz_tiktok_iframetags( $tags ) {
	$tags[] = 'tiktok.com';

	return $tags;
}
/**
 * Add some custom css for the placeholder
 */

add_action( 'wp_footer', 'cmplz_tiktok_css' );
function cmplz_tiktok_css() {
	?>
	<style>
		.tiktok-embed.cmplz-placeholder-element > div { max-width: 100%;}
	</style>
	<?php
}
