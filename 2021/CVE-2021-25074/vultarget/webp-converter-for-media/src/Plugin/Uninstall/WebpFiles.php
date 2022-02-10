<?php

namespace WebpConverter\Plugin\Uninstall;

use WebpConverter\Conversion\Format\FormatFactory;
use WebpConverter\Conversion\SkipCrashed;
use WebpConverter\Conversion\SkipLarger;

/**
 * Removes all output files /uploads-webpc directory.
 */
class WebpFiles {

	/**
	 * Removes output images from output directory.
	 *
	 * @param string|null $output_path Server path.
	 *
	 * @return void
	 */
	public static function remove_webp_files( string $output_path = null ) {
		$path  = ( $output_path !== null ) ? $output_path : apply_filters( 'webpc_dir_path', '', 'webp' );
		$paths = self::get_paths_from_location( $path );
		if ( $output_path === null ) {
			$paths[] = $path;
		}
		self::remove_files( $paths );
	}

	/**
	 * Searches list of paths to remove from given directory.
	 *
	 * @param string   $path  Server path.
	 * @param string[] $paths Server paths already found.
	 *
	 * @return string[] Server paths.
	 */
	private static function get_paths_from_location( string $path, array $paths = [] ): array {
		if ( ! file_exists( $path ) ) {
			return $paths;
		}

		$files = glob( $path . '/*' ) ?: [];
		foreach ( $files as $file ) {
			if ( is_dir( $file ) ) {
				$paths = self::get_paths_from_location( $file, $paths );
			}
			$paths[] = $file;
		}
		return $paths;
	}

	/**
	 * Removes selected paths from disc.
	 *
	 * @param string[] $paths Server paths.
	 *
	 * @return void
	 */
	private static function remove_files( array $paths ) {
		if ( ! $paths ) {
			return;
		}

		$extensions   = ( new FormatFactory() )->get_format_extensions();
		$extensions[] = SkipLarger::DELETED_FILE_EXTENSION;
		$extensions[] = SkipCrashed::CRASHED_FILE_EXTENSION;

		foreach ( $paths as $path ) {
			if ( ! is_writable( $path ) || ! is_writable( dirname( $path ) ) ) {
				continue;
			}

			$extension = pathinfo( $path, PATHINFO_EXTENSION );
			if ( is_file( $path ) && in_array( $extension, $extensions ) ) {
				unlink( $path );
			} elseif ( is_dir( $path ) ) {
				rmdir( $path );
			}
		}
	}
}
