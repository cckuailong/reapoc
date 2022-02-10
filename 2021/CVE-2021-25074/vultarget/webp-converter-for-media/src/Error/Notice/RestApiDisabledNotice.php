<?php

namespace WebpConverter\Error\Notice;

/**
 * {@inheritdoc}
 */
class RestApiDisabledNotice implements ErrorNotice {

	const ERROR_KEY = 'rest_api_disabled';

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
			sprintf(
			/* translators: %1$s: anchor tag, %2$s: anchor tag, %3$s: anchor tag */
				__( 'The REST API on your website is not available. Please verify this and try again. Pay special attention to the filters: %1$s, %2$s and %3$s.', 'webp-converter-for-media' ),
				'<a href="https://developer.wordpress.org/reference/hooks/rest_enabled/" target="_blank">rest_enabled</a>',
				'<a href="https://developer.wordpress.org/reference/hooks/rest_jsonp_enabled/" target="_blank">rest_jsonp_enabled</a>',
				'<a href="https://developer.wordpress.org/reference/hooks/rest_authentication_errors/" target="_blank">rest_authentication_errors</a>'
			),
		];
	}
}
