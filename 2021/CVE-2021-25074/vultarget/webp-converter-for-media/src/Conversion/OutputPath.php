<?php

namespace WebpConverter\Conversion;

use WebpConverter\Conversion\Format\FormatFactory;

/**
 * Generates output paths from source paths.
 */
class OutputPath {

	/**
	 * Generates output path from path of source image.
	 *
	 * @param string $path           Server path of source image.
	 * @param bool   $create_dir     Create output directory structure?
	 * @param string $file_extension Output format extension.
	 *
	 * @return string|null Server path for output image.
	 */
	public static function get_path( string $path, bool $create_dir = false, string $file_extension = '' ) {
		$paths = self::get_paths( $path, $create_dir, [ $file_extension ] );
		return $paths[0] ?? null;
	}

	/**
	 * Generates output paths from paths of source image for all output formats.
	 * Creates directory structure of output path, if it does not exist.
	 *
	 * @param string   $path            Server path of source image.
	 * @param bool     $create_dir      Create output directory structure?
	 * @param string[] $file_extensions Output format extensions.
	 *
	 * @return string[] Server paths for output images.
	 */
	public static function get_paths( string $path, bool $create_dir = false, array $file_extensions = null ): array {
		$new_path = self::get_directory_path( $path );
		if ( $new_path && $create_dir && ! self::make_directories( self::check_directories( $new_path ) ) ) {
			return [];
		}

		$extensions = ( new FormatFactory() )->get_format_extensions();
		$paths      = [];
		foreach ( $extensions as $extension ) {
			$output_path = sprintf( '%1$s.%2$s', $new_path, $extension );
			if ( ( $file_extensions === null ) || in_array( $extension, $file_extensions, true ) ) {
				$paths[] = $output_path;
			}
		}
		return $paths;
	}

	/**
	 * Generates output path from path of source directory.
	 *
	 * @param string $path Server path of source directory.
	 *
	 * @return string|null Server paths for output directory.
	 */
	public static function get_directory_path( string $path ) {
		$webp_root    = apply_filters( 'webpc_dir_path', '', 'webp' );
		$uploads_root = dirname( $webp_root );
		$output_path  = str_replace( realpath( $uploads_root ) ?: '', '', realpath( $path ) ?: '' );
		$output_path  = trim( $output_path, '\/' );

		if ( ! $output_path ) {
			return null;
		}

		return sprintf( '%1$s/%2$s', $webp_root, $output_path );
	}

	/**
	 * Checks if directories for output path exist.
	 *
	 * @param string $path Server path of output.
	 *
	 * @return string[] Directory paths to be created.
	 */
	private static function check_directories( string $path ): array {
		$current = dirname( $path );
		$paths   = [];
		while ( ! file_exists( $current ) ) {
			$paths[] = $current;
			$current = dirname( $current );
		}
		return $paths;
	}

	/**
	 * Creates new directories.
	 *
	 * @param string[] $paths Output directory paths to be created.
	 *
	 * @return bool Paths created successfully?
	 */
	private static function make_directories( array $paths ): bool {
		$paths = array_reverse( $paths );
		foreach ( $paths as $path ) {
			if ( ! is_writable( dirname( $path ) ) ) {
				return false;
			}
			mkdir( $path );
		}
		return true;
	}
}
