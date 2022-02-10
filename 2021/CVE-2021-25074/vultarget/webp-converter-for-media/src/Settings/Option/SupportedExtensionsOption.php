<?php

namespace WebpConverter\Settings\Option;

/**
 * {@inheritdoc}
 */
class SupportedExtensionsOption extends OptionAbstract {

	const OPTION_NAME = 'extensions';

	/**
	 * {@inheritdoc}
	 */
	public function get_priority(): int {
		return 10;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_name(): string {
		return self::OPTION_NAME;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_type(): string {
		return OptionAbstract::OPTION_TYPE_CHECKBOX;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_label(): string {
		return __( 'List of supported files extensions', 'webp-converter-for-media' );
	}

	/**
	 * {@inheritdoc}
	 *
	 * @return string[]
	 */
	public function get_values( array $settings ): array {
		return [
			'jpg'  => '.jpg',
			'jpeg' => '.jpeg',
			'png'  => '.png',
			'gif'  => '.gif',
		];
	}

	/**
	 * {@inheritdoc}
	 *
	 * @return string[]
	 */
	public function get_default_value( array $settings = null ): array {
		return [ 'jpg', 'jpeg', 'png' ];
	}

	/**
	 * {@inheritdoc}
	 *
	 * @return string[]
	 */
	public function get_value_for_debug( array $settings ): array {
		return [ 'png2', 'png' ];
	}
}
