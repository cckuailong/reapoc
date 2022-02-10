<?php

namespace WebpConverter\Conversion\Method;

/**
 * Interface for class that converts images.
 */
interface MethodInterface {

	/**
	 * Returns name of conversion method.
	 *
	 * @return string
	 */
	public function get_name(): string;

	/**
	 * Returns label of conversion method.
	 *
	 * @return string
	 */
	public function get_label(): string;

	/**
	 * @return bool
	 */
	public static function is_pro_feature(): bool;

	/**
	 * Returns status of whether method is installed.
	 *
	 * @return bool
	 */
	public static function is_method_installed(): bool;

	/**
	 * Returns status of whether method is active.
	 *
	 * @param string $format Extension of output format.
	 *
	 * @return bool
	 */
	public static function is_method_active( string $format ): bool;

	/**
	 * @return bool
	 */
	public function is_fatal_error(): bool;

	/**
	 * Returns errors generated during image conversion.
	 *
	 * @return string[] Errors messages.
	 */
	public function get_errors(): array;

	/**
	 * Returns weight of source files before converting.
	 *
	 * @return int
	 */
	public function get_size_before(): int;

	/**
	 * Returns weight of output files after converting.
	 *
	 * @return int
	 */
	public function get_size_after(): int;

	/**
	 * Converts source paths to output formats.
	 *
	 * @param string[] $paths            Server paths of source images.
	 * @param mixed[]  $plugin_settings  .
	 * @param bool     $regenerate_force .
	 *
	 * @return void
	 */
	public function convert_paths( array $paths, array $plugin_settings, bool $regenerate_force );
}
