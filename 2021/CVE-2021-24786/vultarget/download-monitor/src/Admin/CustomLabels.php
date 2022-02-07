<?php

class DLM_Custom_Labels {

	/**
	 * Setup custom labels
	 */
	public function setup() {
		add_filter( 'enter_title_here', array( $this, 'enter_title_here' ), 1, 2 );
		add_filter( 'post_updated_messages', array( $this, 'post_updated_messages' ) );
	}

	/**
	 * enter_title_here function.
	 *
	 * @param string $text
	 * @param WP_Post $post
	 *
	 * @access public
	 * @return string
	 */
	public function enter_title_here( $text, $post ) {
		if ( 'dlm_download' == $post->post_type ) {
			return __( 'Download title', 'download-monitor' );
		}

		return $text;
	}

	/**
	 * post_updated_messages function.
	 *
	 * @access public
	 *
	 * @param array $messages
	 *
	 * @return array
	 */
	public function post_updated_messages( $messages ) {
		global $post;

		$messages['dlm_download'] = array(
			0  => '', // Unused. Messages start at index 1.
			1  => __( 'Download updated.', 'download-monitor' ),
			2  => __( 'Custom field updated.', 'download-monitor' ),
			3  => __( 'Custom field deleted.', 'download-monitor' ),
			4  => __( 'Download updated.', 'download-monitor' ),
			5  => isset( $_GET['revision'] ) ? sprintf( __( 'Download restored to revision from %s', 'download-monitor' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6  => __( 'Download published.', 'download-monitor' ),
			7  => __( 'Download saved.', 'download-monitor' ),
			8  => __( 'Download submitted.', 'download-monitor' ),
			9  => sprintf( __( 'Download scheduled for: <strong>%1$s</strong>.', 'download-monitor' ),
				date_i18n( __( 'M j, Y @ G:i', 'download-monitor' ), strtotime( $post->post_date ) ) ),
			10 => __( 'Download draft updated.', 'download-monitor' ),
		);

		return $messages;
	}

}