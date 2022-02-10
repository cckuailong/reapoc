<?php

namespace WebpConverter\Exception;

/**
 * {@inheritdoc}
 */
class ImageInvalidException extends ExceptionAbstract {

	const ERROR_MESSAGE = '"%s" is not a valid image file.';
	const ERROR_CODE    = 'invalid_image';

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
