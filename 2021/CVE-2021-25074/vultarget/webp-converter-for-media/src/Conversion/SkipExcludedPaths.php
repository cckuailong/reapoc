<?php

namespace WebpConverter\Conversion;

use WebpConverter\HookableInterface;

/**
 * Removes from list of source directory paths those that are excluded.
 */
class SkipExcludedPaths implements HookableInterface {

	const DIRS_EXCLUDED = [
		'.',
		'..',
		'.git',
		'.svn',
		'node_modules',
	];

	/**
	 * {@inheritdoc}
	 */
	public function init_hooks() {
		add_filter( 'webpc_supported_source_directory', [ $this, 'skip_excluded_directory' ], 0, 3 );
	}

	/**
	 * Returns the status if the given directory path should be converted.
	 *
	 * @param bool   $path_status .
	 * @param string $dirname     .
	 * @param string $server_path .
	 *
	 * @return bool Status if the given path is not excluded.
	 * @internal
	 */
	public function skip_excluded_directory( bool $path_status, string $dirname, string $server_path ): bool {
		if ( in_array( $dirname, self::DIRS_EXCLUDED ) ) {
			return false;
		}

		return $path_status;
	}
}
