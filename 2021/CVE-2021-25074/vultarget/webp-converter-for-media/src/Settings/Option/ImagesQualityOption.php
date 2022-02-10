<?php

namespace WebpConverter\Settings\Option;

/**
 * {@inheritdoc}
 */
class ImagesQualityOption extends OptionAbstract {

	const OPTION_NAME = 'quality';

	/**
	 * {@inheritdoc}
	 */
	public function get_priority(): int {
		return 60;
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
		return OptionAbstract::OPTION_TYPE_QUALITY;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_label(): string {
		return __( 'Images quality', 'webp-converter-for-media' );
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_info(): string {
		return __( 'Adjust the quality of the images being converted. Remember that higher quality also means larger file sizes. The recommended value is 85%.', 'webp-converter-for-media' );
	}

	/**
	 * {@inheritdoc}
	 *
	 * @return string[]
	 */
	public function get_values( array $settings ): array {
		return [
			'75'  => '75%',
			'80'  => '80%',
			'85'  => '85%',
			'90'  => '90%',
			'95'  => '95%',
			'100' => '100%',
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_default_value( array $settings = null ): string {
		return '85';
	}
}
