<?php

namespace WebpConverter\Plugin\Uninstall;

/**
 * Removes .htaccess file /uploads-webpc directory.
 */
class HtaccessFile {

	/**
	 * Removes .htaccess file from /uploads-webpc directory.
	 *
	 * @return void
	 */
	public static function remove_htaccess_file() {
		$path = sprintf( '%s/.htaccess', apply_filters( 'webpc_dir_path', '', 'webp' ) );
		if ( is_writable( $path ) ) {
			unlink( $path );
		}
	}
}
