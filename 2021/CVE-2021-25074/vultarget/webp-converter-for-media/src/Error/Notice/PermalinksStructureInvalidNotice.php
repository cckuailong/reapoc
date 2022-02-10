<?php

namespace WebpConverter\Error\Notice;

/**
 * {@inheritdoc}
 */
class PermalinksStructureInvalidNotice implements ErrorNotice {

	const ERROR_KEY = 'permalinks_invalid';

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
			__( 'The URL structure for permalinks on your website are not set. This is necessary for the REST API to function properly. In the admin panel, go to Settings -> Permalinks and select an option other than "Plain". Then save your changes.', 'webp-converter-for-media' ),
		];
	}
}
