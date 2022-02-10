<?php
/**
 * Helpers class
 */

class FWP_Demos_Helpers {

	/**
	 * Gets and returns url body using wp_remote_get
	 *
	 * @since 1.0.0
	 */
	public static function get_remote( $url ) {

		// Get data
		$response = wp_remote_get( $url );

		// Check for errors
		if ( is_wp_error( $response ) or ( wp_remote_retrieve_response_code( $response ) != 200 ) ) {
			return false;
		}

		// Get remote body val
		$body = wp_remote_retrieve_body( $response );

		// Return data
		if ( ! empty( $body ) ) {
			return $body;
		} else {
			return false;
		}
	}

	/**
	 * Perform a HTTP HEAD or GET request.
	 *
	 * If $file_path is a writable filename, this will do a GET request and write
	 * the file to that path.
	 *
	 * This is a re-implementation of the deprecated wp_get_http() function from WP Core,
	 * but this time using the recommended WP_Http() class and the WordPress filesystem.
	 *
	 * @param string      $url       URL to fetch.
	 * @param string|bool $file_path Optional. File path to write request to. Default false.
	 * @param array       $args      Optional. Arguments to be passed-on to the request.
	 * @return bool|string False on failure and string of headers if HEAD request.
	 */
	public static function wp_get_http( $url, $file_path = false, $red = 1 ) {

		// No need to proceed if we don't have a $url or a $file_path.
		if ( ! $url || ! $file_path ) {
			return false;
		}

		$try_file_get_contents = false;

		// Make sure we normalize $file_path.
		$file_path = wp_normalize_path( $file_path );

		// Include the WP_Http class if it doesn't already exist.
		if ( ! class_exists( 'WP_Http' ) ) {
			include_once( wp_normalize_path( ABSPATH . WPINC . '/class-http.php' ) );
		}
		// Inlude the wp_remote_get function if it doesn't already exist.
		if ( ! function_exists( 'wp_remote_get' ) ) {
			include_once( wp_normalize_path( ABSPATH . WPINC . '/http.php' ) );
		}

		$args = wp_parse_args( $args, array(
			'timeout'    => 30,
			'user-agent' => 'avada-user-agent',
		) );
		$response = wp_remote_get( esc_url_raw( $url ), $args );
		$body     = wp_remote_retrieve_body( $response );

		// Try file_get_contents if body is empty.
		if ( empty( $body ) ) {
			if ( function_exists( 'ini_get' ) && ini_get( 'allow_url_fopen' ) ) {
				$body = @file_get_contents( $url );
			}
		}

		// Initialize the Wordpress filesystem.
		global $wp_filesystem;
		if ( empty( $wp_filesystem ) ) {
			require_once( ABSPATH . '/wp-admin/includes/file.php' );
			WP_Filesystem();
		}

		// Attempt to write the file.
		if ( ! $wp_filesystem->put_contents( $file_path, $body, FS_CHMOD_FILE ) ) {
			// If the attempt to write to the file failed, then fallback to fwrite.
			@unlink( $file_path );
			$fp = fopen( $file_path, 'w' );
			$written = fwrite( $fp, $body );
			fclose( $fp );
			if ( false === $written ) {
				return false;
			}
		}

		// If all went well, then return the headers of the request.
		if ( isset( $response['headers'] ) ) {
			$response['headers']['response'] = $response['response']['code'];
			return $response['headers'];
		}

		// If all else fails, then return false.
		return false;

	}

}