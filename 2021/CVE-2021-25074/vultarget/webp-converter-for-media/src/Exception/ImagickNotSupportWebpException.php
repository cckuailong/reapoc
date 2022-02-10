<?php

namespace WebpConverter\Exception;

/**
 * {@inheritdoc}
 */
class ImagickNotSupportWebpException extends ExceptionAbstract {

	const ERROR_MESSAGE = 'Server configuration: Imagick does not support WebP format.';
	const ERROR_CODE    = 'server_configuration';

	/**
	 * {@inheritdoc}
	 */
	public function get_error_message( array $values ): string {
		return self::ERROR_MESSAGE;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_error_status(): string {
		return self::ERROR_CODE;
	}
}
