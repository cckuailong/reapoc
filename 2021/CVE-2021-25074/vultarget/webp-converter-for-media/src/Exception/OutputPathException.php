<?php

namespace WebpConverter\Exception;

/**
 * {@inheritdoc}
 */
class OutputPathException extends ExceptionAbstract {

	const ERROR_MESSAGE = 'An error occurred creating destination directory for "%s" file.';
	const ERROR_CODE    = 'output_path';

	/**
	 * {@inheritdoc}
	 */
	public function get_error_message( array $values ): string {
		return sprintf( self::ERROR_MESSAGE, $values[0] );
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_error_status(): string {
		return self::ERROR_CODE;
	}
}
