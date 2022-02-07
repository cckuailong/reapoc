<?php

class DLM_Hasher {

	/**
	 * Get array with hashes for given file path.
	 * Array will always contain all hash keys but hash values will only be set if user option for hash is turned on.
	 *
	 * @param string $file_path
	 *
	 * @return array
	 */
	public function get_file_hashes( $file_path ) {
		$md5    = false;
		$sha1   = false;
		$sha256 = false;
		$crc32b = false;

		if ( $file_path ) {
			list( $file_path, $remote_file ) = download_monitor()->service( 'file_manager' )->parse_file_path( $file_path );

			if ( ! empty( $file_path ) ) {
				if ( ! $remote_file || apply_filters( 'dlm_allow_remote_hash_file', false ) ) {

					if ( $this->is_hash_enabled( 'md5' ) ) {
						$md5 = $this->generate_hash( 'md5', $file_path );
					}

					if ( $this->is_hash_enabled( 'sha1' ) ) {
						$sha1 = $this->generate_hash( 'sha1', $file_path );
					}

					if ( $this->is_hash_enabled( 'sha256' ) ) {
						$sha256 = $this->generate_hash( 'sha256', $file_path );
					}

					if ( $this->is_hash_enabled( 'crc32b' ) ) {
						$crc32b = $this->generate_hash( 'crc32b', $file_path );
					}

				}
			}
		}

		return apply_filters( "dlm_file_hashes", array( 'md5' => $md5, 'sha1' => $sha1, 'sha256' => $sha256, 'crc32b' => $crc32b ), $file_path );
	}

	/**
	 * Generate hash of $type for $file_path
	 *
	 * @param string $type
	 * @param string $file_path
	 *
	 * @return string
	 */
	public function generate_hash( $type, $file_path ) {
		$hash = "";
		switch ( $type ) {
			case 'md5':
				$hash = hash_file( 'md5', $file_path );
				break;
			case 'sha1':
				$hash = hash_file( 'sha1', $file_path );
				break;
			case 'sha256':
				$hash = hash_file( 'sha256', $file_path );
				break;
			case 'crc32b':
				$hash = hash_file( 'crc32b', $file_path );
				break;
		}

		return $hash;
	}

	/**
	 * Check if generation of given hash $type is enabled
	 *
	 * @param string $type
	 *
	 * @return bool
	 */
	public function is_hash_enabled( $type ) {
		return ( "1" == get_option( 'dlm_generate_hash_' . $type, 0 ) );
	}

	/**
	 * Get available and enabled hashes
	 *
	 * @return array
	 */
	public function get_available_hashes() {
		$hashes = array( 'md5', 'sha1', 'crc32b', 'sha256' );

		foreach ( $hashes as $hash_key => $hash ) {
			if ( ! $this->is_hash_enabled( $hash ) ) {
				unset( $hashes[ $hash_key ] );
			}
		}

		return $hashes;
	}
}