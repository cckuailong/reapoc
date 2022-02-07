<?php

class DLM_WordPress_Version_Repository implements DLM_Version_Repository {

	/**
	 * Filter query arguments for version WP_Query queries
	 *
	 * @param array $args
	 * @param int $limit
	 * @param int $offset
	 *
	 * @return array
	 */
	private function filter_query_args( $args = array(), $limit = 0, $offset = 0 ) {

		// most be absint
		$limit  = absint( $limit );
		$offset = absint( $offset );

		// start with removing reserved keys
		unset( $args['post_type'] );
		unset( $args['posts_per_page'] );
		unset( $args['offset'] );
		unset( $args['paged'] );
		unset( $args['nopaging'] );

		// setup our reserved keys
		$args['post_type']      = 'dlm_download_version';
		$args['posts_per_page'] = - 1;
		$args['orderby']        = 'menu_order';
		$args['order']          = 'ASC';

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
	 * Retrieve single version
	 *
	 * @param int $id
	 *
	 * @return DLM_Download_Version
	 * @throws Exception
	 */
	public function retrieve_single( $id ) {
		$versions = $this->retrieve( array( 'p' => absint( $id ) ) );

		if ( count( $versions ) != 1 ) {
			throw new Exception( "Version not found" );
		}

		return array_shift( $versions );
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

			/** @var DLM_File_Manager $file_manager */
			$file_manager = download_monitor()->service( 'file_manager' );

			foreach ( $posts as $post ) {

				// create download object
				$version = new DLM_Download_Version();
				$version->set_id( $post->ID );
				$version->set_author( $post->post_author );
				$version->set_download_id( $post->post_parent );
				$version->set_menu_order( $post->menu_order );
				$version->set_date( new DateTime( $post->post_date ) );
				$version->set_version( strtolower( get_post_meta( $version->get_id(), '_version', true ) ) );
				$version->set_download_count( absint( get_post_meta( $version->get_id(), '_download_count', true ) ) );
				$version->set_filesize( get_post_meta( $version->get_id(), '_filesize', true ) );
				$version->set_md5( get_post_meta( $version->get_id(), '_md5', true ) );
				$version->set_sha1( get_post_meta( $version->get_id(), '_sha1', true ) );
				$version->set_sha256( get_post_meta( $version->get_id(), '_sha256', true ) );
				$version->set_crc32b( get_post_meta( $version->get_id(), '_crc32', true ) );

				// mirrors
				$mirrors = get_post_meta( $version->get_id(), '_files', true );
				if ( is_string( $mirrors ) ) {
					$mirrors = array_filter( (array) json_decode( $mirrors ) );
				} elseif ( is_array( $mirrors ) ) {
					$mirrors = array_filter( $mirrors );
				} else {
					$mirrors = array();
				}
				$version->set_mirrors( $mirrors );

				// url
				$url = current( $mirrors );
				$version->set_url( $url );

				// filename
				$filename = $file_manager->get_file_name( $url );
				$version->set_filename( $filename );

				// filetype
				$version->set_filetype( $file_manager->get_file_type( $filename ) );

				// fix empty file sizes
				if ( "" === $version->get_filesize() ) {
					// Get the file size
					$filesize = $file_manager->get_file_size( $url );

					update_post_meta( $version->get_id(), '_filesize', $filesize );
					$version->set_filesize( $filesize );
				}

				// add download to return array
				$items[] = $version;
			}
		}

		return $items;
	}

	/**
	 * @param DLM_Download_Version $version
	 *
	 * @throws Exception
	 *
	 * @return bool
	 */
	public function persist( $version ) {

		// check if new download or existing
		if ( 0 == $version->get_id() ) {

			// create
			$version_id = wp_insert_post( array(
				'post_title'   => $version->get_title(),
				'post_author'  => $version->get_author(),
				'post_type'    => 'dlm_download_version',
				'post_status'  => 'publish',
				'post_parent'  => $version->get_download_id(),
				'menu_order'   => $version->get_menu_order(),
				'post_date'    => $version->get_date()->format( 'Y-m-d H:i:s' )
			) );

			if ( is_wp_error( $version_id ) ) {
				throw new \Exception( 'Unable to insert version in WordPress database' );
			}
			// set new vehicle ID
			$version->set_id( $version_id );

		} else {

			// update
			$version_id = wp_update_post( array(
				'ID'           => $version->get_id(),
				'post_title'   => $version->get_title(),
				'post_author'  => $version->get_author(),
				'post_status'  => 'publish',
				'post_parent'  => $version->get_download_id(),
				'menu_order'   => $version->get_menu_order(),
				'post_date'    => $version->get_date()->format( 'Y-m-d H:i:s' )
			) );

			if ( is_wp_error( $version_id ) ) {
				throw new \Exception( 'Unable to update version in WordPress database' );
			}

		}

		// store version download count if it's not NULL
		if ( null !== $version->get_download_count() ) {
			update_post_meta( $version_id, '_download_count', absint( $version->get_download_count() ) );
		}

		// store version
		update_post_meta( $version_id, '_version', $version->get_version() );

		// store mirrors
		update_post_meta( $version_id, '_files', download_monitor()->service( 'file_manager' )->json_encode_files( $version->get_mirrors() ) );

		// set filesize and hashes
		$filesize       = - 1;
		$main_file_path = current( $version->get_mirrors() );
		if ( $main_file_path ) {
			$filesize = download_monitor()->service( 'file_manager' )->get_file_size( $main_file_path );
			$hashes   = download_monitor()->service( 'hasher' )->get_file_hashes( $main_file_path );
			update_post_meta( $version_id, '_filesize', $filesize );
			update_post_meta( $version_id, '_md5', $hashes['md5'] );
			update_post_meta( $version_id, '_sha1', $hashes['sha1'] );
			update_post_meta( $version_id, '_sha256', $hashes['sha256'] );
			update_post_meta( $version_id, '_crc32', $hashes['crc32b'] );
		} else {
			update_post_meta( $version_id, '_filesize', $filesize );
			update_post_meta( $version_id, '_md5', '' );
			update_post_meta( $version_id, '_sha1', '' );
			update_post_meta( $version_id, '_sha256', '' );
			update_post_meta( $version_id, '_crc32', '' );
		}

		return true;
	}

}