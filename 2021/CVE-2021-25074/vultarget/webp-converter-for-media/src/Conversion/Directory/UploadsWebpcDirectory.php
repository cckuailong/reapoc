<?php

namespace WebpConverter\Conversion\Directory;

/**
 * Supports data about /uploads-webpc directory.
 */
class UploadsWebpcDirectory extends DirectoryAbstract {

	const DIRECTORY_TYPE = 'webp';
	const DIRECTORY_PATH = 'wp-content/uploads-webpc';

	/**
	 * {@inheritdoc}
	 */
	public function get_type(): string {
		return self::DIRECTORY_TYPE;
	}

	/**
	 * {@inheritdoc}
	 */
	public function is_available(): bool {
		return true;
	}

	/**
	 * {@inheritdoc}
	 */
	public function is_output_directory(): bool {
		return true;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_relative_path(): string {
		return self::DIRECTORY_PATH;
	}
}
