<?php
defined( 'ABSPATH' ) or die( "you do not have acces to this page!" );
add_filter( 'cmplz_known_script_tags', 'cmplz_facebook_script' );
function cmplz_facebook_script( $tags ) {
	$tags[] = 'connect.facebook.net';

	return $tags;
}

add_filter( 'cmplz_placeholder_markers', 'cmplz_facebook_placeholder' );
function cmplz_facebook_placeholder( $tags ) {
	$tags['facebook'][] = "fb-page";
	$tags['facebook'][] = "fb-post";

	return $tags;
}

add_filter( 'cmplz_known_iframe_tags', 'cmplz_facebook_iframetags' );
function cmplz_facebook_iframetags( $tags ) {
	$tags[] = 'facebook.com/plugins';

	return $tags;
}

/**
 * Add some custom css for the placeholder
 */

add_action( 'wp_head', 'cmplz_facebook_css' );
function cmplz_facebook_css() {
	?>
	<style>
		.cmplz-placeholder-element > blockquote.fb-xfbml-parse-ignore {
			margin: 0 20px;
		}
	</style>
	<?php
}
