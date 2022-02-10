<?php

namespace WebpConverter\Action;

use WebpConverter\Conversion\OutputPath;
use WebpConverter\Conversion\SkipCrashed;
use WebpConverter\Conversion\SkipLarger;
use WebpConverter\HookableInterface;

/**
 * Deletes all images in list of paths.
 */
class DeletePaths implements HookableInterface {

	/**
	 * {@inheritdoc}
	 */
	public function init_hooks() {
		add_action( 'webpc_delete_paths', [ $this, 'delete_files_by_paths' ] );
	}

	/**
	 * Deletes images from output directory.
	 *
	 * @param string[] $paths Server paths of output images.
	 *
	 * @return void
	 * @internal
	 */
	public function delete_files_by_paths( array $paths ) {
		foreach ( $paths as $path ) {
			$this->delete_file_by_path( $path );
		}
	}

	/**
	 * Deletes image from output directory.
	 *
	 * @param string $path Server path of output image.
	 *
	 * @return void
	 */
	private function delete_file_by_path( string $path ) {
		if ( ! ( $output_paths = OutputPath::get_paths( $path ) ) ) {
			return;
		}

		foreach ( $output_paths as $output_path ) {
			if ( is_writable( $output_path ) ) {
				unlink( $output_path );
			}

			if ( is_writable( $output_path . '.' . SkipLarger::DELETED_FILE_EXTENSION ) ) {
				unlink( $output_path . '.' . SkipLarger::DELETED_FILE_EXTENSION );
			}

			if ( is_writable( $output_path . '.' . SkipCrashed::CRASHED_FILE_EXTENSION ) ) {
				unlink( $output_path . '.' . SkipCrashed::CRASHED_FILE_EXTENSION );
			}
		}
	}
}
