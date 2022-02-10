<?php

namespace WebpConverter\Exception;

/**
 * {@inheritdoc}
 */
class FilesizeOversizeException extends ExceptionAbstract {

	const ERROR_MESSAGE = 'Image is larger than the maximum size of %1$sMB: "%2$s".';
	const ERROR_CODE    = 'max_filezile';

	/**
	 * {@inheritdoc}
	 */
	public function get_error_message( array $values ): string {
		return sprintf(
			self::ERROR_MESSAGE,
			round( $values[0] / 1024 / 1024 ),
			$values[1]
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_error_status(): string {
		return self::ERROR_CODE;
	}
}
