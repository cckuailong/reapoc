<?php

namespace WebpConverter\Conversion\Directory;

/**
 * Supports data about /gallery directory.
 */
class GalleryDirectory extends DirectoryAbstract {

	const DIRECTORY_TYPE = 'gallery';
	const DIRECTORY_PATH = 'wp-content/gallery';

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
