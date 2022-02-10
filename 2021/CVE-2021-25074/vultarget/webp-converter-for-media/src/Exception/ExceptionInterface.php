<?php

namespace WebpConverter\Exception;

/**
 * {@inheritdoc}
 */
interface ExceptionInterface {

	/**
	 * @param mixed[]|string $value Params of exception.
	 */
	public function __construct( $value = [] );

	/**
	 * Returns message of error.
	 *
	 * @param string[] $values Params from class constructor.
	 *
	 * @return string Error message.
	 */
	public function get_error_message( array $values ): string;

	/**
	 * Returns status of error.
	 *
	 * @return string Error status.
	 */
	public function get_error_status(): string;
}
