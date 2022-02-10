<?php

namespace WebpConverter\Conversion\Directory;

/**
 * Supports data about /themes directory.
 */
class ThemesDirectory extends DirectoryAbstract {

	const DIRECTORY_TYPE = 'themes';
	const DIRECTORY_PATH = 'wp-content/themes';

	/**
	 * {@inheritdoc}
	 */
	public function get_type(): string {
		return self::DIRECTORY_TYPE;
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
