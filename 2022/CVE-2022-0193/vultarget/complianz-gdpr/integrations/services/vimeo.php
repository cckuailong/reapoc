<?php
defined( 'ABSPATH' ) or die( "you do not have acces to this page!" );

add_filter( 'cmplz_known_iframe_tags', 'cmplz_vimeo_iframetags' );
function cmplz_vimeo_iframetags( $tags ) {
	$tags[] = 'player.vimeo.com';
	$tags[] = 'i.vimeocdn.com';
	return $tags;
}

add_filter( 'cmplz_iframe_tags_not_including', 'cmplz_vimeo_not_including_iframetags' );
function cmplz_vimeo_not_including_iframetags( $tags ) {
	$tags[] = 'dnt=1';
	$tags[] = 'dnt=true';
	return $tags;
}

function cmplz_vimeo_placeholder( $placeholder_src, $src ) {
	//get id, used only for storing in transient
	$vimeo_pattern
		= '/(?:www\.|player\.)?vimeo.com\/(?:channels\/(?:\w+\/)?|groups\/(?:[^\/]*)\/videos\/|album\/(?:\d+)\/video\/|video\/|)(\d+)(?:[a-zA-Z0-9_\-]+)?/i';
	if ( preg_match( $vimeo_pattern, $src, $matches ) ) {
		$vimeo_id = $matches[1];
		$new_src  = get_transient( "cmplz_vimeo_image_$vimeo_id" );
		if ( ! $new_src || ! cmplz_file_exists_on_url( $new_src ) ) {
			$data = json_decode( file_get_contents( 'http://vimeo.com/api/oembed.json?url=' . $src ) );
			if (!empty($data) ) {
				$placeholder_src = $data->thumbnail_url;
				$placeholder_src = cmplz_download_to_site( $placeholder_src,
					'vimeo' . $vimeo_id );

				set_transient( "cmplz_vimeo_image_$vimeo_id", $placeholder_src,
					WEEK_IN_SECONDS );
			}
		} else {
			$placeholder_src = $new_src;
		}
	}
	return $placeholder_src;
}

add_filter( 'cmplz_placeholder_vimeo', 'cmplz_vimeo_placeholder', 10, 2 );
