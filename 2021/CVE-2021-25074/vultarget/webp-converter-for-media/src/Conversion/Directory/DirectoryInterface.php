<?php

namespace WebpConverter\Conversion\Directory;

/**
 * Interface for class that supports data about directory.
 */
interface DirectoryInterface {

	/**
	 * Returns type of directory.
	 *
	 * @return string Directory type.
	 */
	public function get_type(): string;

	/**
	 * Returns label of directory.
	 *
	 * @return string Directory label.
	 */
	public function get_label(): string;

	/**
	 * Returns status if directory is available.
	 *
	 * @return bool Directory is available?
	 */
	public function is_available(): bool;

	/**
	 * Returns status if directory is destined for output.
	 *
	 * @return bool Directory for output?
	 */
	public function is_output_directory(): bool;

	/**
	 * Returns relative path of directory.
	 *
	 * @return string Relative path of directory.
	 */
	public function get_relative_path(): string;

	/**
	 * Returns server path of directory.
	 *
	 * @return string Server path of directory.
	 */
	public function get_server_path(): string;

	/**
	 * Returns URL of directory.
	 *
	 * @return string URL of directory.
	 */
	public function get_path_url(): string;
}
