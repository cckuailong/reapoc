<?php

class DLM_LU_Download_Upgrader {

	/**
	 * Get legacy tables
	 *
	 * @return array
	 */
	public function get_legacy_tables() {
		global $wpdb;

		return array(
			'files'   => $wpdb->prefix . "download_monitor_files",
			'tax'     => $wpdb->prefix . "download_monitor_taxonomies",
			'rel'     => $wpdb->prefix . "download_monitor_relationships",
			'formats' => $wpdb->prefix . "download_monitor_formats",
			'stats'   => $wpdb->prefix . "download_monitor_stats",
			'log'     => $wpdb->prefix . "download_monitor_log",
			'meta'    => $wpdb->prefix . "download_monitor_file_meta"
		);
	}

	/**
	 * Add terms to download
	 *
	 * @param array $terms
	 * @param string $taxonomy
	 * @param int $download_id
	 */
	private function add_terms_to_download( $terms, $taxonomy, $download_id ) {
		$term_ids = array();

		foreach ( $terms as $term ) {

			try {
				$term_obj = term_exists( $term, $taxonomy );

				if ( $term_obj !== 0 && $term_obj !== null ) {
					$term_id = $term_obj['term_id'];
				} else {
					$term_obj = wp_insert_term( $term, $taxonomy );

					if ( is_wp_error( $term_obj ) ) {
						throw new Exception( 'Error on wp_insert_term()' );
					}

					$term_id = $term_obj['term_id'];
				}

				$term_ids[] = $term_id;
			} catch ( Exception $e ) {
				DLM_Debug_Logger::log( "add_terms_to_download Exception: " . $e->getMessage() );
			}

		}

		wp_set_post_terms( $download_id, $term_ids, $taxonomy );
	}

	/**
	 * Upgrade legacy download thumnail
	 *
	 * @param string $url URL to fetch attachment from
	 * @param int $download_id
	 *
	 * @return bool
	 */
	private function upgrade_thumbnail( $url, $download_id ) {

		$attachment_id    = '';
		$attachment_url   = '';
		$attachment_file  = '';
		$upload_dir       = wp_upload_dir();
		$attachment_array = array(
			'post_title'   => '',
			'post_content' => '',
			'post_status'  => 'inherit',
			'post_parent'  => $download_id
		);

		// check if thumbnail is in a local directory, if it is we copy it
		if ( strstr( $url, site_url() ) ) {
			$abs_url  = str_replace( trailingslashit( site_url() ), trailingslashit( ABSPATH ), $url );
			$new_name = wp_unique_filename( $upload_dir['path'], basename( $url ) );
			$new_url  = trailingslashit( $upload_dir['path'] ) . $new_name;

			if ( copy( $abs_url, $new_url ) ) {
				$url = basename( $new_url );
			}
		}

		try {

			if ( ! strstr( $url, 'http' ) ) {

				// Local file
				$attachment_file = trailingslashit( $upload_dir['path'] ) . $url;

				// We have the path, check it exists
				if ( file_exists( $attachment_file ) ) {

					$attachment_url = str_replace( trailingslashit( ABSPATH ), trailingslashit( site_url() ), $attachment_file );

					if ( $info = wp_check_filetype( $attachment_file ) ) {
						$attachment_array['post_mime_type'] = $info['type'];
					} else {
						throw new Exception( 'Invalid file type' );
					}

					$attachment_array['guid'] = $attachment_url;

					$attachment_id = wp_insert_attachment( $attachment_array, $attachment_file, $download_id );

				} else {
					throw new Exception( 'Local image did not exist!' );
				}

			} else {

				// if the URL is absolute, but does not contain address, then upload it assuming base_site_url
				if ( preg_match( '|^/[\w\W]+$|', $url ) ) {
					$url = rtrim( site_url(), '/' ) . $url;
				}

				// fetch remote file
				$upload = $this->fetch_remote_file( $url );

				if ( is_wp_error( $upload ) ) {
					throw new Exception( 'Error fetching remote file' );
				}

				if ( $info = wp_check_filetype( $upload['file'] ) ) {
					$attachment_array['post_mime_type'] = $info['type'];
				} else {
					throw new Exception( 'Invalid file type' );
				}

				$attachment_array['guid'] = $upload['url'];

				$attachment_file = $upload['file'];

				// as per wp-admin/includes/upload.php
				$attachment_id = wp_insert_attachment( $attachment_array, $attachment_file, $download_id );

				unset( $upload );
			}


			// set new meta data
			if ( ! is_wp_error( $attachment_id ) && $attachment_id > 0 ) {
				wp_update_attachment_metadata( $attachment_id, wp_generate_attachment_metadata( $attachment_id, $attachment_file ) );
				update_post_meta( $download_id, '_thumbnail_id', $attachment_id );
			}

		} catch ( Exception $e ) {
			DLM_Debug_Logger::log( "Legacy Upgrade Thumbnail Exception: " . $e->getMessage() );

			return false;
		}

		return true;
	}

	/**
	 * Fetch a remote file
	 *
	 * @param $url string
	 *
	 * @throws Exception
	 *
	 * @return array
	 */
	private function fetch_remote_file( $url ) {

		// extract the file name and extension from the url
		$file_name = basename( $url );

		// Ensure url is valid
		$url = str_replace( " ", '%20', $url );

		// Get the file
		$response = wp_remote_get( $url, array(
			'timeout' => 10
		) );

		// check for error in response
		if ( is_wp_error( $response ) ) {
			throw new Exception( "wp_remote_get response is WP error " );
		}

		// Upload the file
		$upload = wp_upload_bits( $file_name, '', wp_remote_retrieve_body( $response ) );

		if ( $upload['error'] ) {
			throw new Exception( $upload['error'] );
		}

		// Get filesize
		$filesize = filesize( $upload['file'] );

		// check if filesize is greater than 0
		if ( 0 == $filesize ) {
			@unlink( $upload['file'] );
			unset( $upload );

			throw new Exception( 'Zero size file downloaded' );
		}

		unset( $response );

		return $upload;
	}

	/**
	 * Upgrade a single download item. Do NOT call this without it being in queue.
	 *
	 * @param $download_id
	 *
	 * @return bool
	 */
	public function upgrade_download( $download_id ) {
		global $wpdb;

		$queue = new DLM_LU_Download_Queue();

		$legacy_tables = $this->get_legacy_tables();

		// mark download upgrading
		$queue->mark_download_upgrading( $download_id );

		// get legacy download information
		$legacy_download = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM `" . $legacy_tables['files'] . "` WHERE `id` = %d ;", $download_id ) );

		// create new Download object
		$download = new DLM_Download();
		$download->set_status( 'publish' );
		$download->set_author( 1 );

		// get user ID of user that created legacy download
		if ( ! empty( $legacy_download->user ) ) {
			$user = get_user_by( 'login', $legacy_download->user );
			if ( $user ) {
				$download->set_author( $user->ID );
			}
		}

		// set title & description
		$download->set_title( $legacy_download->title );
		$download->set_description( $legacy_download->file_description );
		$download->set_excerpt( "" );

		// set download options
		$download->set_featured( false ); // there was no featured in legacy
		$download->set_redirect_only( false ); // there was no redirect only in legacy
		$download->set_members_only( ( 1 === absint( $legacy_download->members ) ) );

		// set download count
		$download->set_download_count( absint( $legacy_download->hits ) );

		// store new download
		download_monitor()->service( 'download_repository' )->persist( $download );

		// create new version
		/** @var DLM_Download_Version $new_version */
		$version = new DLM_Download_Version();

		// set download id on version
		$version->set_download_id( $download->get_id() );

		// set version name on version
		$version->set_version( $legacy_download->dlversion );

		// set download count in version
		$version->set_download_count( absint( $legacy_download->hits ) );

		// set mirrors
		$urls = array();
		if ( $legacy_download->mirrors ) {
			$urls = explode( "\n", $legacy_download->mirrors );
		}
		$urls = array_filter( array_merge( array( $legacy_download->filename ), (array) $urls ) );
		$version->set_mirrors( $urls );

		// set other version data
		$version->set_filesize( "" ); // empty filesize so it's calculated on persist
		$version->set_author( $download->get_author() );

		// version date
		$version_date = new DateTime( $legacy_download->postDate );
		if ( $version_date->format( 'U' ) < 0 ) {
			$version_date = new DateTime();
		}
		$version->set_date( $version_date );

		// persist new version
		download_monitor()->service( 'version_repository' )->persist( $version );

		// clear download transient
		download_monitor()->service( 'transient_manager' )->clear_versions_transient( $download->get_id() );

		// upgrade categories
		$terms = $wpdb->get_col( $wpdb->prepare( "SELECT T.name 
		FROM `{$legacy_tables['tax']}` AS T
		LEFT JOIN `{$legacy_tables['rel']}` AS R ON T.id = R.taxonomy_id
		WHERE R.download_id = %d
		AND T.taxonomy = 'category'", $legacy_download->id ) );
		if ( $terms ) {
			$this->add_terms_to_download( $terms, 'dlm_download_category', $download->get_id() );
		}

		// upgrade tags
		$terms = $wpdb->get_col( $wpdb->prepare( "SELECT T.name 
		FROM `{$legacy_tables['tax']}` AS T
		LEFT JOIN `{$legacy_tables['rel']}` AS R ON T.id = R.taxonomy_id
		WHERE R.download_id = %d
		AND T.taxonomy = 'tag'", $legacy_download->id ) );
		if ( $terms ) {
			$this->add_terms_to_download( $terms, 'dlm_download_tag', $download->get_id() );
		}

		// upgrade any custom meta data
		$meta_fields = $wpdb->get_results( $wpdb->prepare( "SELECT meta_name, meta_value FROM {$legacy_tables['meta']} WHERE download_id = %d;", $legacy_download->id ) );

		foreach ( $meta_fields as $meta ) {

			// thumbnails were also stored as file_meta, so we add an exception check here
			if ( 'thumbnail' === $meta->meta_name ) {
				$this->upgrade_thumbnail( $meta->meta_value, $download->get_id() );
				continue;
			}

			// force was an old option that is no longer supported
			if ( 'force' === $meta->meta_name ) {
				continue;
			}

			update_post_meta( $download->get_id(), $meta->meta_name, $meta->meta_value );
		}

		// mark download as upgraded
		$queue->mark_download_upgraded( $legacy_download->id, $download->get_id() );

		return true;
	}

}