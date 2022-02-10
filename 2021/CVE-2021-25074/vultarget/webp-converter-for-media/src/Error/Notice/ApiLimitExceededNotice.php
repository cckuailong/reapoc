<?php

namespace WebpConverter\Error\Notice;

/**
 * {@inheritdoc}
 */
class ApiLimitExceededNotice implements ErrorNotice {

	const ERROR_KEY = 'token_limit';

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
			__( 'Your limit for the number of converted images in the current billing period has been exceeded. You can wait until the end of the billing period. Then your limit will be renewed. Another solution is to cancel the current subscription and buy a new one with a new limit.', 'webp-converter-for-media' ),
		];
	}
}
