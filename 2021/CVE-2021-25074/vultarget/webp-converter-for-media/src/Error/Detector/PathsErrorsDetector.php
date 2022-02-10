<?php

namespace WebpConverter\Error\Detector;

use WebpConverter\Error\Notice\PathHtaccessNotWritableNotice;
use WebpConverter\Error\Notice\PathUploadsUnavailableNotice;
use WebpConverter\Error\Notice\PathWebpDuplicatedNotice;
use WebpConverter\Error\Notice\PathWebpNotWritableNotice;

/**
 * Checks for configuration errors about incorrect paths of directories.
 */
class PathsErrorsDetector implements ErrorDetector {

	/**
	 * {@inheritdoc}
	 */
	public function get_error() {
		if ( $this->if_uploads_path_exists() !== true ) {
			return new PathUploadsUnavailableNotice();
		} elseif ( $this->if_htaccess_is_writeable() !== true ) {
			return new PathHtaccessNotWritableNotice();
		} elseif ( $this->if_paths_are_different() !== true ) {
			return new PathWebpDuplicatedNotice();
		} elseif ( $this->if_webp_path_is_writeable() !== true ) {
			return new PathWebpNotWritableNotice();
		}

		return null;
	}

	/**
	 * Checks if path of uploads directory is exists.
	 *
	 * @return bool Verification status.
	 */
	private function if_uploads_path_exists(): bool {
		$path = apply_filters( 'webpc_dir_path', '', 'uploads' );
		return ( is_dir( $path ) && ( $path !== ABSPATH ) );
	}

	/**
	 * Checks if paths of wp-content and uploads directories are writable.
	 *
	 * @return bool Verification status.
	 */
	private function if_htaccess_is_writeable(): bool {
		$path_dir  = apply_filters( 'webpc_dir_path', '', 'uploads' );
		$path_file = $path_dir . '/.htaccess';
		if ( file_exists( $path_file ) ) {
			return ( is_readable( $path_file ) && is_writable( $path_file ) );
		} else {
			return is_writable( $path_dir );
		}
	}

	/**
	 * Checks if uploads directory path and output directory are different.
	 *
	 * @return bool Verification status.
	 */
	private function if_paths_are_different(): bool {
		$path_uploads = apply_filters( 'webpc_dir_path', '', 'uploads' );
		$path_webp    = apply_filters( 'webpc_dir_path', '', 'webp' );
		return ( $path_uploads !== $path_webp );
	}

	/**
	 * Checks if path of output directory is writable.
	 *
	 * @return bool Verification status.
	 */
	private function if_webp_path_is_writeable(): bool {
		$path = apply_filters( 'webpc_dir_path', '', 'webp' );
		return ( is_dir( $path ) || is_writable( dirname( $path ) ) );
	}
}
