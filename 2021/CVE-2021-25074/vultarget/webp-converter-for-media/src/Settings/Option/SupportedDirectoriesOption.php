<?php

namespace WebpConverter\Settings\Option;

use WebpConverter\Conversion\Directory\DirectoryFactory;

/**
 * {@inheritdoc}
 */
class SupportedDirectoriesOption extends OptionAbstract {

	const OPTION_NAME = 'dirs';

	/**
	 * {@inheritdoc}
	 */
	public function get_priority(): int {
		return 20;
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
		return __( 'List of supported directories', 'webp-converter-for-media' );
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_info(): string {
		return __( 'Files from these directories will be supported in output formats.', 'webp-converter-for-media' );
	}

	/**
	 * {@inheritdoc}
	 *
	 * @return string[]
	 */
	public function get_values( array $settings ): array {
		return ( new DirectoryFactory() )->get_directories();
	}

	/**
	 * {@inheritdoc}
	 *
	 * @return string[]
	 */
	public function get_default_value( array $settings = null ): array {
		return [ 'uploads' ];
	}

	/**
	 * {@inheritdoc}
	 *
	 * @return string[]
	 */
	public function get_value_for_debug( array $settings ): array {
		return [ 'uploads' ];
	}
}
