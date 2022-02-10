<?php

namespace WebpConverter\Exception;

/**
 * {@inheritdoc}
 */
class ResolutionOversizeException extends ExceptionAbstract {

	const ERROR_MESSAGE = 'Image is larger than maximum 8K resolution: "%s".';
	const ERROR_CODE    = 'max_resolution';

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
