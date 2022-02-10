<?php

namespace WebpConverter\Conversion\Directory;

/**
 * Supports data about /uploads directory.
 */
class UploadsDirectory extends DirectoryAbstract {

	const DIRECTORY_TYPE = 'uploads';
	const DIRECTORY_PATH = 'wp-content/uploads';

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
	public function get_label(): string {
		return '/' . self::DIRECTORY_TYPE;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_relative_path(): string {
		return self::DIRECTORY_PATH;
	}
}
