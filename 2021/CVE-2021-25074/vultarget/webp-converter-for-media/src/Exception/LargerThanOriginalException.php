<?php

namespace WebpConverter\Exception;

/**
 * {@inheritdoc}
 */
class LargerThanOriginalException extends ExceptionAbstract {

	const ERROR_MESSAGE = 'Image "%1$s" converted to .%2$s is larger than original and has been deleted.';
	const ERROR_CODE    = 'larger_than_original';

	/**
	 * {@inheritdoc}
	 */
	public function get_error_message( array $values ): string {
		return sprintf(
			self::ERROR_MESSAGE,
			$values[0],
			pathinfo( $values[1], PATHINFO_EXTENSION )
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_error_status(): string {
		return self::ERROR_CODE;
	}
}
