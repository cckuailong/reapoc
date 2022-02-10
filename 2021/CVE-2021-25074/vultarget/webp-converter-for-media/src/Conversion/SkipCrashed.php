<?php

namespace WebpConverter\Conversion;

/**
 * Excludes re-conversion of files that caused converting error.
 */
class SkipCrashed {

	const CRASHED_FILE_EXTENSION = 'crashed';

	/**
	 * @param string $output_path .
	 *
	 * @return void
	 */
	public function create_crashed_file( string $output_path ) {
		$file = fopen( $output_path . '.' . self::CRASHED_FILE_EXTENSION, 'w' );
		if ( $file === false ) {
			return;
		}

		fclose( $file );
	}

	/**
	 * @param string $output_path .
	 *
	 * @return void
	 */
	public function delete_crashed_file( string $output_path ) {
		if ( ! file_exists( $output_path ) || ! file_exists( $output_path . '.' . self::CRASHED_FILE_EXTENSION ) ) {
			return;
		}

		unlink( $output_path . '.' . self::CRASHED_FILE_EXTENSION );
	}
}
