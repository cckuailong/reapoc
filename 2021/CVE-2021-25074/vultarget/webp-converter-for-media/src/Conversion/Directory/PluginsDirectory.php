<?php

namespace WebpConverter\Conversion\Directory;

/**
 * Supports data about /plugins directory.
 */
class PluginsDirectory extends DirectoryAbstract {

	const DIRECTORY_TYPE = 'plugins';
	const DIRECTORY_PATH = 'wp-content/plugins';

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
