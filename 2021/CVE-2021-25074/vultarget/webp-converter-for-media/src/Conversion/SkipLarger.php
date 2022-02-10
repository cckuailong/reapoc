<?php

namespace WebpConverter\Conversion;

use WebpConverter\Exception;
use WebpConverter\Settings\Option\ExtraFeaturesOption;

/**
 * Deletes output after conversion if it is larger than original.
 */
class SkipLarger {

	const DELETED_FILE_EXTENSION = 'deleted';

	/**
	 * Removes converted output image if it is larger than original image.
	 *
	 * @param string  $output_path     .
	 * @param string  $source_path     .
	 * @param mixed[] $plugin_settings .
	 *
	 * @return void
	 * @throws Exception\LargerThanOriginalException
	 */
	public function remove_image_if_is_larger( string $output_path, string $source_path, array $plugin_settings ) {
		if ( file_exists( $output_path . '.' . self::DELETED_FILE_EXTENSION ) ) {
			unlink( $output_path . '.' . self::DELETED_FILE_EXTENSION );
		}

		if ( ! in_array( ExtraFeaturesOption::OPTION_VALUE_ONLY_SMALLER, $plugin_settings[ ExtraFeaturesOption::OPTION_NAME ] )
			|| ( ! file_exists( $output_path ) || ! file_exists( $source_path ) )
			|| ( filesize( $output_path ) < filesize( $source_path ) ) ) {
			return;
		}

		$file = fopen( $output_path . '.' . self::DELETED_FILE_EXTENSION, 'w' );
		if ( $file !== false ) {
			fclose( $file );
			unlink( $output_path );
		}

		throw new Exception\LargerThanOriginalException( [ $source_path, $output_path ] );
	}
}
