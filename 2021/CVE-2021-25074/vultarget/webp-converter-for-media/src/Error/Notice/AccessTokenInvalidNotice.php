<?php

namespace WebpConverter\Error\Notice;

/**
 * {@inheritdoc}
 */
class AccessTokenInvalidNotice implements ErrorNotice {

	const ERROR_KEY = 'token_invalid';

	/**
	 * {@inheritdoc}
	 */
	public function get_key(): string {
		return self::ERROR_KEY;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_message(): array {
		return [
			__( 'Your access token is invalid or your subscription has expired. Check the value given in and try to activate it again.', 'webp-converter-for-media' ),
		];
	}
}
