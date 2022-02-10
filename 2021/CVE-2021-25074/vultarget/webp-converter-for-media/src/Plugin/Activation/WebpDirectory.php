<?php

namespace WebpConverter\Plugin\Activation;

/**
 * Creates /upload-webpc directory for output files.
 */
class WebpDirectory {

	/**
	 * Creates directory for output images.
	 *
	 * @return void
	 */
	public function create_directory_for_uploads_webp() {
		$path = apply_filters( 'webpc_dir_path', '', 'webp' );
		if ( ! file_exists( $path ) && is_writable( dirname( $path ) ) ) {
			mkdir( $path );
		}
	}
}
