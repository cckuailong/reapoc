<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class DLM_File_Manager {

	/**
	 * Returns a listing of all files in the specified folder and all subdirectories up to 100 levels deep.
	 * The depth of the recursiveness can be controlled by the $levels param.
	 *
	 * @access public
	 *
	 * @param string $folder (default: '')
	 *
	 * @return array|bool
	 */
	public function list_files( $folder = '' ) {
		if ( empty( $folder ) ) {
			return false;
		}

		// A listing of all files and dirs in $folder, excepting . and ..
		// By default, the sorted order is alphabetical in ascending order
		$files = array_diff( scandir( $folder ), array( '..', '.' ) );

		$dlm_files = array();

		foreach ( $files as $file ) {
			$dlm_files[] = array(
				'type' => ( is_dir( $folder . '/' . $file ) ? 'folder' : 'file' ),
				'path' => $folder . '/' . $file
			);
		}

		return $dlm_files;
	}

	/**
	 * Parse a file path and return the new path and whether or not it's remote
	 *
	 * @param  string $file_path
	 *
	 * @return array
	 */
	public function parse_file_path( $file_path ) {

		$remote_file      = true;
		$parsed_file_path = parse_url( $file_path );

		$wp_uploads     = wp_upload_dir();
		$wp_uploads_dir = $wp_uploads['basedir'];
		$wp_uploads_url = $wp_uploads['baseurl'];

		if ( ( ! isset( $parsed_file_path['scheme'] ) || ! in_array( $parsed_file_path['scheme'], array(
					'http',
					'https',
					'ftp'
				) ) ) && isset( $parsed_file_path['path'] ) && file_exists( $parsed_file_path['path'] )
		) {

			/** This is an absolute path */
			$remote_file = false;

		} elseif ( strpos( $file_path, $wp_uploads_url ) !== false ) {

			/** This is a local file given by URL so we need to figure out the path */
			$remote_file = false;
			$file_path   = trim( str_replace( $wp_uploads_url, $wp_uploads_dir, $file_path ) );
			$file_path   = realpath( $file_path );

		} elseif ( is_multisite() && ( ( strpos( $file_path, network_site_url( '/', 'http' ) ) !== false ) || ( strpos( $file_path, network_site_url( '/', 'https' ) ) !== false ) ) ) {

			/** This is a local file outside of wp-content so figure out the path */
			$remote_file = false;
			// Try to replace network url
			$file_path = str_replace( network_site_url( '/', 'https' ), ABSPATH, $file_path );
			$file_path = str_replace( network_site_url( '/', 'http' ), ABSPATH, $file_path );
			// Try to replace upload URL
			$file_path = str_replace( $wp_uploads_url, $wp_uploads_dir, $file_path );
			$file_path = realpath( $file_path );

		} elseif ( strpos( $file_path, site_url( '/', 'http' ) ) !== false || strpos( $file_path, site_url( '/', 'https' ) ) !== false ) {

			/** This is a local file outside of wp-content so figure out the path */
			$remote_file = false;
			$file_path   = str_replace( site_url( '/', 'https' ), ABSPATH, $file_path );
			$file_path   = str_replace( site_url( '/', 'http' ), ABSPATH, $file_path );
			$file_path   = realpath( $file_path );

		} elseif ( file_exists( ABSPATH . $file_path ) ) {

			/** Path needs an abspath to work */
			$remote_file = false;
			$file_path   = ABSPATH . $file_path;
			$file_path   = realpath( $file_path );
		}

		return array( $file_path, $remote_file );
	}

	/**
	 * Gets the filesize of a path or URL.
	 *
	 * @access public
	 *
	 * @param string $file_path
	 *
	 * @return string size on success, -1 on failure
	 */
	public function get_file_size( $file_path ) {
		if ( $file_path ) {
			list( $file_path, $remote_file ) = $this->parse_file_path( $file_path );

			if ( ! empty( $file_path ) ) {
				if ( $remote_file ) {
					$file = wp_remote_head( $file_path );

					if ( ! is_wp_error( $file ) && ! empty( $file['headers']['content-length'] ) ) {
						return $file['headers']['content-length'];
					}
				} else {
					if ( file_exists( $file_path ) && ( $filesize = filesize( $file_path ) ) ) {
						return $filesize;
					}
				}
			}
		}

		return - 1;
	}

	/**
	 * Encode files for storage
	 *
	 * @param  array $files
	 *
	 * @return string
	 */
	public function json_encode_files( $files ) {
		if ( version_compare( phpversion(), "5.4.0", ">=" ) ) {
			$files = json_encode( $files, JSON_UNESCAPED_UNICODE );
		} else {
			$files = json_encode( $files );
			if ( function_exists( 'mb_convert_encoding' ) ) {
				$files = preg_replace_callback( '/\\\\u([0-9a-f]{4})/i', array(
					$this,
					'json_unscaped_unicode_fallback'
				), $files );
			}
		}

		return $files;
	}

	/**
	 * Fallback for PHP < 5.4 where JSON_UNESCAPED_UNICODE does not exist.
	 *
	 * @param  array $matches
	 *
	 * @return string
	 */
	public function json_unscaped_unicode_fallback( $matches ) {
		$sym = mb_convert_encoding(
			pack( 'H*', $matches[1] ),
			'UTF-8',
			'UTF-16'
		);

		return $sym;
	}

	/**
	 * Multi-byte-safe pathinfo replacement.
	 *
	 * @param $filepath
	 *
	 * @return mixed
	 */
	public function mb_pathinfo( $filepath ) {
		$ret = array();
		preg_match( '%^(.*?)[\\\\/]*(([^/\\\\]*?)(\.([^\.\\\\/]+?)|))[\\\\/\.]*$%im', $filepath, $m );
		if ( isset( $m[1] ) ) {
			$ret['dirname'] = $m[1];
		}
		if ( isset( $m[2] ) ) {
			$ret['basename'] = $m[2];
		}
		if ( isset( $m[5] ) ) {
			$ret['extension'] = $m[5];
		}
		if ( isset( $m[3] ) ) {
			$ret['filename'] = $m[3];
		}

		return $ret;
	}

	/**
	 * Get file name for given path
	 *
	 * @param string $file_path
	 *
	 * @return string
	 */
	public function get_file_name( $file_path ) {
		return current( explode( '?', DLM_Utils::basename( $file_path ) ) );
	}

	/**
	 * Get file type of give file name
	 *
	 * @param string $file_name
	 *
	 * @return string
	 */
	public function get_file_type( $file_name ) {
		return strtolower( substr( strrchr( $file_name, "." ), 1 ) );
	}

	/**
	 * Gets md5, sha1 and crc32 hashes for a file and store it.
	 *
	 * @deprecated use hasher service get_file_hashes() instead
	 *
	 * @param string $file_path
	 *
	 * @return array of sizes
	 */
	public function get_file_hashes( $file_path ) {
		return download_monitor()->service( 'hasher' )->get_file_hashes( $file_path );
	}

}