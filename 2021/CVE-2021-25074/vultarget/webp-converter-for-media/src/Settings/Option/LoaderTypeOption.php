<?php

namespace WebpConverter\Settings\Option;

use WebpConverter\Loader\HtaccessLoader;
use WebpConverter\Loader\PassthruLoader;

/**
 * {@inheritdoc}
 */
class LoaderTypeOption extends OptionAbstract {

	const OPTION_NAME = 'loader_type';

	/**
	 * {@inheritdoc}
	 */
	public function get_priority(): int {
		return 70;
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
		return OptionAbstract::OPTION_TYPE_RADIO;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_label(): string {
		return __( 'Image loading mode', 'webp-converter-for-media' );
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_info(): string {
		return __( 'By changing image loading mode it allows you to bypass some server configuration problems.', 'webp-converter-for-media' );
	}

	/**
	 * {@inheritdoc}
	 *
	 * @return string[]
	 */
	public function get_values( array $settings ): array {
		return [
			HtaccessLoader::LOADER_TYPE => sprintf(
			/* translators: %s: loader type */
				__( '%s (recommended)', 'webp-converter-for-media' ),
				__( 'via .htaccess', 'webp-converter-for-media' )
			),
			PassthruLoader::LOADER_TYPE => sprintf(
			/* translators: %s: loader type */
				__( '%s (without rewrites in .htaccess files or Nginx configuration)', 'webp-converter-for-media' ),
				'Pass Thru'
			),
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_default_value( array $settings = null ): string {
		return HtaccessLoader::LOADER_TYPE;
	}
}
