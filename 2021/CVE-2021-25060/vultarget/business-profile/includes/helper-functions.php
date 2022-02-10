<?php

if ( ! function_exists( 'bpfwp_get_post_image_url' ) ) {
	function bpfwp_get_post_image_url() {
		global $post;

		if ( has_post_thumbnail( $post->ID ) ) {
			$image_array = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'single-post-thumbnail' );

			return $image_array[0];
		}

		return false;
	}
}

if ( ! function_exists( 'bpfwp_get_site_logo_url' ) ) {
	function bpfwp_get_site_logo_url() {
		$custom_logo_id = get_theme_mod( 'custom_logo' );

		if ( $custom_logo_id ) {
			$image_array = wp_get_attachment_image_src( $custom_logo_id );

			return $image_array[0];
		}

		return false;
	}
}

if ( ! function_exists( 'bpfwp_get_site_logo_width' ) ) {
	function bpfwp_get_site_logo_width() {
		$custom_logo_id = get_theme_mod( 'custom_logo' );

		if ( $custom_logo_id ) {
			$image_array = wp_get_attachment_image_src( $custom_logo_id );

			return $image_array[1];
		}

		return false;
	}
}

if ( ! function_exists( 'bpfwp_get_site_logo_height' ) ) {
	function bpfwp_get_site_logo_height() {
		$custom_logo_id = get_theme_mod( 'custom_logo' );

		if ( $custom_logo_id ) {
			$image_array = wp_get_attachment_image_src( $custom_logo_id );

			return $image_array[2];
		}

		return false;
	}
}

if ( ! function_exists( 'bpfwp_wc_get_most_recent_review_rating' ) ) {
	function bpfwp_wc_get_most_recent_review_rating() {
		global $post;
	
		$reviews = get_comments( array( 'post_id' => $post->ID ) );
	
		if ( $reviews ) {
			$most_recent_comment = reset($reviews);
	
			return get_comment_meta( $most_recent_comment->comment_ID, 'rating', true );
		}
	}
}

if ( ! function_exists( 'bpfwp_wc_get_most_recent_review_body' ) ) {
	function bpfwp_wc_get_most_recent_review_body() {
		global $post;
	
		$reviews = get_comments( array( 'post_id' => $post->ID ) ); 
	
		if ( $reviews ) {
			$most_recent_comment = reset($reviews);
	
			return $most_recent_comment->comment_content;
		}
	}
}

if ( ! function_exists( 'bpfwp_wc_get_most_recent_review_author' ) ) {
	function bpfwp_wc_get_most_recent_review_author() {
		global $post;
	
		$reviews = get_comments( array( 'post_id' => $post->ID ) ); 
	
		if ( $reviews ) {
			$most_recent_comment = reset($reviews);
	
			return $most_recent_comment->comment_author;
		}
	}
}