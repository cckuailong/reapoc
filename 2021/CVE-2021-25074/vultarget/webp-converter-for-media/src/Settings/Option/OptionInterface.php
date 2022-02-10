<?php

namespace WebpConverter\Settings\Option;

/**
 * Interface for class that supports data field in plugin settings.
 */
interface OptionInterface {

	/**
	 * Returns order priority (ascending).
	 *
	 * @return int
	 */
	public function get_priority(): int;

	/**
	 * Returns name of option.
	 *
	 * @return string
	 */
	public function get_name(): string;

	/**
	 * Returns type of field.
	 *
	 * @return string
	 */
	public function get_type(): string;

	/**
	 * Returns label of option.
	 *
	 * @return string
	 */
	public function get_label(): string;

	/**
	 * @return string[]|null
	 */
	public function get_notice_lines();

	/**
	 * Returns additional information of field.
	 *
	 * @return string|null
	 */
	public function get_info();

	/**
	 * Returns available values for field.
	 *
	 * @param mixed[] $settings Plugin settings.
	 *
	 * @return string[]|null
	 */
	public function get_values( array $settings );

	/**
	 * Returns default value of field.
	 *
	 * @param mixed[]|null $settings Plugin settings.
	 *
	 * @return string|string[]
	 */
	public function get_default_value( array $settings = null );

	/**
	 * Returns unavailable values for field.
	 *
	 * @param mixed[] $settings Plugin settings.
	 *
	 * @return string[]|null
	 */
	public function get_disabled_values( array $settings );

	/**
	 * Returns default value of field when debugging.
	 *
	 * @param mixed[] $settings Plugin settings.
	 *
	 * @return string|string[]
	 */
	public function get_value_for_debug( array $settings );
}
