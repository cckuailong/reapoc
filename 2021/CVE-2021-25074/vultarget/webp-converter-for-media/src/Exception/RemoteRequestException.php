<?php

namespace WebpConverter\Exception;

/**
 * {@inheritdoc}
 */
class RemoteRequestException extends ExceptionAbstract {

	const ERROR_MESSAGE = 'There was an error connecting to the API, received a response code of %1$s: "%2$s".';
	const ERROR_CODE    = 'remote_request_failed';

	/**
	 * {@inheritdoc}
	 */
	public function get_error_message( array $values ): string {
		return sprintf( self::ERROR_MESSAGE, $values[0], $values[1] );
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_error_status(): string {
		return self::ERROR_CODE;
	}
}
