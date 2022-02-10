<?php

namespace WebpConverter\Action;

use WebpConverter\HookableInterface;

/**
 * Initializes conversion of all image sizes in directory.
 */
class ConvertDir implements HookableInterface {

	/**
	 * {@inheritdoc}
	 */
	public function init_hooks() {
		add_action( 'webpc_convert_dir', [ $this, 'convert_files_by_directory' ], 10, 2 );
	}

	/**
	 * Converts all images in directory to output formats.
	 *
	 * @param string $dir_path       Server path of directory.
	 * @param bool   $skip_converted Skip converted images?
	 *
	 * @return void
	 * @internal
	 */
	public function convert_files_by_directory( string $dir_path, bool $skip_converted = true ) {
		$paths = apply_filters( 'webpc_dir_files', [], $dir_path, $skip_converted );
		do_action( 'webpc_convert_paths', $paths );
	}
}
