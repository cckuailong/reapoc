<?php

namespace WebpConverter\Exception;

/**
 * {@inheritdoc}
 */
class ConversionErrorException extends ExceptionAbstract {

	const ERROR_MESSAGE = 'Error occurred while converting image: "%s".';
	const ERROR_CODE    = 'convert_error';

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
