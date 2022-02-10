<?php

namespace WebpConverter\Exception;

/**
 * {@inheritdoc}
 */
class RemoteErrorResponseException extends ExceptionAbstract {

	const ERROR_MESSAGE = null;
	const ERROR_CODE    = 'remote_response_error';

	/**
	 * {@inheritdoc}
	 */
	public function get_error_message( array $values ): string {
		return $values[0];
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_error_status(): string {
		return self::ERROR_CODE;
	}
}
