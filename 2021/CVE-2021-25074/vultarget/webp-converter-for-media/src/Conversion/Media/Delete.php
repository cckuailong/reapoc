<?php

namespace WebpConverter\Conversion\Media;

use WebpConverter\HookableInterface;

/**
 * Removes image from its output format when removing image from media library.
 */
class Delete implements HookableInterface {

	/**
	 * {@inheritdoc}
	 */
	public function init_hooks() {
		add_filter( 'wp_delete_file', [ $this, 'delete_attachment_file' ] );
	}

	/**
	 * Deletes output image based on server path of source image.
	 *
	 * @param string $path Server path of source image.
	 *
	 * @return string Server path of source image.
	 * @internal
	 */
	public function delete_attachment_file( string $path ): string {
		do_action( 'webpc_delete_paths', [ $path ] );
		return $path;
	}
}
