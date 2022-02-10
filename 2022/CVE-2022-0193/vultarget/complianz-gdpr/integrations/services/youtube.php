<?php
defined( 'ABSPATH' ) or die( "you do not have acces to this page!" );

add_filter( 'cmplz_known_script_tags', 'cmplz_youtube_script' );
function cmplz_youtube_script( $tags ) {
	$tags[] = 'www.youtube.com/iframe_api';

	return $tags;
}

add_filter( 'cmplz_known_iframe_tags', 'cmplz_youtube_iframetags' );
function cmplz_youtube_iframetags( $tags ) {
	$tags[] = 'youtube.com';
	$tags[] = 'youtube-nocookie.com';
	$tags[] = 'youtu.be';

	return $tags;
}

/**
 * Get the first video id from a video series
 *
 * @param string $src
 *
 * @return string
 */

function cmplz_youtube_get_video_id_from_series($src){
	$output = wp_remote_get($src);
	$youtube_id = false;
	if (isset($output['body'])) {
		$body = $output['body'];
		$body = stripcslashes($body);
		$series_pattern = '/VIDEO_ID\': "([^#\&\?].*?)"/i';
		if ( preg_match( $series_pattern, $body, $matches ) ) {
			$youtube_id = $matches[1];
		}
	}
	return $youtube_id;
}

/**
 * Get screenshot from youtube as placeholder
 * @param $new_src
 * @param $src
 *
 * @return mixed|string
 */

function cmplz_youtube_placeholder( $new_src, $src ) {
	$youtube_pattern
		= '/.*(?:youtu.be\/|v\/|u\/\w\/|embed\/videoseries\?list=RD|embed\/|watch\?v=)([^#\&\?]*).*/i';
	if ( preg_match( $youtube_pattern, $src, $matches ) ) {
		$youtube_id = $matches[1];
		//check if it's a video series. If so, we get the first video
		if ($youtube_id === 'videoseries') {
			//get the videoseries id
			$series_pattern = '/.*(?:youtu.be\/|v\/|u\/\w\/|embed\/videoseries\?list=RD|embed\/|watch\?v=)[^#\&\?]*\?list=(.*)/i';
			//if we find the unique id, we save it in the cache
			if ( preg_match( $series_pattern, $src, $matches ) ) {
				$series_id = $matches[1];

				$youtube_id = get_transient("cmplz_youtube_videoseries_video_id_$series_id");
				if (!$youtube_id){
					//we do a get on the url to retrieve the first video
					$youtube_id = cmplz_youtube_get_video_id_from_series($src);
					set_transient( "cmplz_youtube_videoseries_video_id_$series_id", $youtube_id,
						WEEK_IN_SECONDS );
				}
			} else{
				$youtube_id = cmplz_youtube_get_video_id_from_series($src);
			}
		}
		/*
		 * The highest resolution of youtube thumbnail is the maxres, but it does not
		 * always exist. In that case, we take the hq thumb
		 * To lower the number of file exists checks, we cache the result.
		 *
		 * */
		$new_src = get_transient( "cmplz_youtube_image_$youtube_id" );
		if ( ! $new_src || ! cmplz_file_exists_on_url( $new_src ) ) {
			$new_src
				= "https://img.youtube.com/vi/$youtube_id/maxresdefault.jpg";
			if ( ! cmplz_remote_file_exists( $new_src ) ) {
				$new_src
					= "https://img.youtube.com/vi/$youtube_id/hqdefault.jpg";
			}
			$new_src = cmplz_download_to_site( $new_src,
				'youtube' . $youtube_id );

			set_transient( "cmplz_youtube_image_$youtube_id", $new_src,
				WEEK_IN_SECONDS );
		}
	}

	return $new_src;
}

add_filter( 'cmplz_placeholder_youtube', 'cmplz_youtube_placeholder', 10, 2 );


