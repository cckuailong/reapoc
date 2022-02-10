<?php

namespace WebpConverter\Error\Notice;

/**
 * {@inheritdoc}
 */
class SettingsIncorrectNotice implements ErrorNotice {

	const ERROR_KEY = 'settings_incorrect';

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
			__( 'The plugin settings are incorrect! Check them out and save them again. Please remember that you must have at least one option selected for each field.', 'webp-converter-for-media' ),
		];
	}
}
