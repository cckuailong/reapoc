<?php

class DLM_WordPress_Download_Repository implements DLM_Download_Repository {

	/**
	 * Filter query arguments for download WP_Query queries
	 *
	 * @param array $args
	 * @param int $limit
	 * @param int $offset
	 *
	 * @return array
	 */
	private function filter_query_args( $args = array(), $limit = 0, $offset = 0 ) {

		// limit must be int, not abs
		$limit = intval( $limit );

		// most be absint
		$offset = absint( $offset );

		// start with removing reserved keys
		unset( $args['post_type'] );
		unset( $args['posts_per_page'] );
		unset( $args['offset'] );
		unset( $args['paged'] );
		unset( $args['nopaging'] );

		// setup our reserved keys
		$args['post_type']      = 'dlm_download';
		$args['posts_per_page'] = - 1;

		// set limit if set
		if ( $limit > 0 ) {
			$args['posts_per_page'] = $limit;
		}

		// set offset if set
		if ( $offset > 0 ) {
			$args['offset'] = $offset;
		}

		return $args;
	}

	/**
	 * Returns number of rows for given filters
	 *
	 * @param array $filters
	 *
	 * @return int
	 */
	public function num_rows( $filters = array() ) {
		$q = new WP_Query();
		$q->query( $this->filter_query_args( $filters ) );

		return $q->found_posts;
	}

	/**
	 * Retrieve single download
	 *
	 * @param int $id
	 *
	 * @return DLM_Download
	 * @throws Exception
	 */
	public function retrieve_single( $id ) {
		$downloads = $this->retrieve( array( 'p' => absint( $id ) ) );

		if ( count( $downloads ) != 1 ) {
			throw new Exception( "Download not found" );
		}

		return array_shift( $downloads );
	}

	/**
	 * Retrieve downloads
	 *
	 * @param array $filters
	 * @param int $limit
	 * @param int $offset
	 *
	 * @return array<DLM_Download>
	 */
	public function retrieve( $filters = array(), $limit = 0, $offset = 0 ) {

		$items = array();

		$q     = new WP_Query();

		$posts = $q->query( $this->filter_query_args( $filters, $limit, $offset ) );

		if ( count( $posts ) > 0 ) {
			foreach ( $posts as $post ) {

				// create download object

				/**
				 * @var $download \DLM_Download
				 */
				$download = download_monitor()->service( 'download_factory' )->make( ( ( 1 == get_post_meta( $post->ID, '_is_purchasable', true ) ) ? 'product' : 'regular' ) );
				$download->set_id( $post->ID );
				$download->set_status( $post->post_status );
				$download->set_title( $post->post_title );
				$download->set_slug( $post->post_name );
				$download->set_author( $post->post_author );
				$download->set_description( $post->post_content );
				$download->set_excerpt( wpautop( do_shortcode( $post->post_excerpt ) ) );
				$download->set_redirect_only( ( 'yes' == get_post_meta( $post->ID, '_redirect_only', true ) ) );
				$download->set_featured( ( 'yes' == get_post_meta( $post->ID, '_featured', true ) ) );
				$download->set_members_only( ( 'yes' == get_post_meta( $post->ID, '_members_only', true ) ) );
				$download->set_download_count( absint( get_post_meta( $post->ID, '_download_count', true ) ) );

				// This is added for backwards compatibility but will be removed in a later version!
				$download->post = $post;

				// add download to return array
				$items[] = $download;
			}
		}

		return $items;
	}

	/**
	 * @param DLM_Download $download
	 *
	 * @throws \Exception
	 *
	 * @return bool
	 */
	public function persist( $download ) {

		// check if new download or existing
		if ( 0 == $download->get_id() ) {

			// create
			$download_id = wp_insert_post( array(
				'post_title'   => $download->get_title(),
				'post_content' => $download->get_description(),
				'post_excerpt' => $download->get_excerpt(),
				'post_author'  => $download->get_author(),
				'post_type'    => 'dlm_download',
				'post_status'  => $download->get_status()
			) );

			if ( is_wp_error( $download_id ) ) {
				throw new \Exception( 'Unable to insert download in WordPress database' );
			}
			// set new vehicle ID
			$download->set_id( $download_id );

		} else {

			// update
			$download_id = wp_update_post( array(
				'ID'           => $download->get_id(),
				'post_title'   => $download->get_title(),
				'post_content' => $download->get_description(),
				'post_excerpt' => $download->get_excerpt(),
				'post_author'  => $download->get_author(),
				'post_status'  => $download->get_status()
			) );

			if ( is_wp_error( $download_id ) ) {
				throw new \Exception( 'Unable to update download in WordPress database' );
			}

		}

		// persist 'Download Options'
		update_post_meta( $download_id, '_featured', ( ( $download->is_featured() ) ? 'yes' : 'no' ) );
		update_post_meta( $download_id, '_members_only', ( ( $download->is_members_only() ) ? 'yes' : 'no' ) );
		update_post_meta( $download_id, '_redirect_only', ( ( $download->is_redirect_only() ) ? 'yes' : 'no' ) );

		// other download meta
		update_post_meta( $download_id, '_download_count', $download->get_download_count() );

		// clear versions transient
		download_monitor()->service( 'transient_manager' )->clear_versions_transient( $download_id );

		return true;
	}

}