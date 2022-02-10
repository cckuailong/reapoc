<?php

namespace WebpConverter\Exception;

/**
 * {@inheritdoc}
 */
class SourcePathException extends ExceptionAbstract {

	const ERROR_MESSAGE = 'Source path "%s" for image does not exist or is unreadable.';
	const ERROR_CODE    = 'file_unreadable';

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
