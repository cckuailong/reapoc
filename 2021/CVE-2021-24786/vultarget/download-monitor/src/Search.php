<?php


/**
 * Class DLM_Search
 *
 * Add search support to Download Monitor downloads
 */
class DLM_Search {

	/**
	 * Setup the search
	 */
	public function setup() {

		// frontend
		if ( ! is_admin() ) {

			if ( '1' == get_option( 'dlm_wp_search_enabled' ) ) {
				add_filter( 'post_type_link', array( $this, 'filter_download_permalink' ), 20, 2 );
			}

		}

	}

	/**
	 * @param $post_link string
	 * @param $post WP_Post
	 *
	 * @return string
	 */
	public function filter_download_permalink( $post_link, $post ) {
		// check if we're filtering a dlm_download permalink in a search query
		if ( 'dlm_download' == $post->post_type && is_search() ) {

			// fetch download object
			try {
				/** @var DLM_Download $download */
				$download = download_monitor()->service( 'download_repository' )->retrieve_single( $post->ID );

				// allow this search download URL to be filtered
				return apply_filters( 'dlm_search_download_url', $download->get_the_download_link(), $download );
			} catch ( Exception $e ) {
			}
		}

		return $post_link;
	}

}