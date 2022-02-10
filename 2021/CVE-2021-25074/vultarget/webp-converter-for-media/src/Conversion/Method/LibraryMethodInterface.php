<?php

namespace WebpConverter\Conversion\Method;

/**
 * Interface for class that converts images using the PHP library.
 */
interface LibraryMethodInterface {

	/**
	 * Creates image object based on source path.
	 *
	 * @param string  $source_path     Server path of source image.
	 * @param mixed[] $plugin_settings .
	 *
	 * @return mixed Image object.
	 */
	public function create_image_by_path( string $source_path, array $plugin_settings );

	/**
	 * Converts image and saves to output location.
	 *
	 * @param mixed   $image           Image object.
	 * @param string  $source_path     Server path of source image.
	 * @param string  $output_path     Server path for output image.
	 * @param string  $format          Extension of output format.
	 * @param mixed[] $plugin_settings .
	 *
	 * @return void
	 */
	public function convert_image_to_output( $image, string $source_path, string $output_path, string $format, array $plugin_settings );
}
