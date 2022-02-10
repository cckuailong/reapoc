<?php

namespace WebpConverter\Settings\Option;

use WebpConverter\Conversion\Method\GdMethod;
use WebpConverter\Loader\PassthruLoader;

/**
 * {@inheritdoc}
 */
class ExtraFeaturesOption extends OptionAbstract {

	const OPTION_NAME                   = 'features';
	const OPTION_VALUE_ONLY_SMALLER     = 'only_smaller';
	const OPTION_VALUE_MOD_EXPIRES      = 'mod_expires';
	const OPTION_VALUE_KEEP_METADATA    = 'keep_metadata';
	const OPTION_VALUE_CRON_ENABLED     = 'cron_enabled';
	const OPTION_VALUE_CRON_CONVERSION  = 'cron_conversion';
	const OPTION_VALUE_REFERER_DISABLED = 'referer_disabled';
	const OPTION_VALUE_DEBUG_ENABLED    = 'debug_enabled';

	/**
	 * {@inheritdoc}
	 */
	public function get_priority(): int {
		return 80;
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
		return __( 'Extra features', 'webp-converter-for-media' );
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_info(): string {
		return __( 'Options allow you to enable new functionalities that will increase capabilities of plugin', 'webp-converter-for-media' );
	}

	/**
	 * {@inheritdoc}
	 *
	 * @return string[]
	 */
	public function get_values( array $settings ): array {
		return [
			self::OPTION_VALUE_ONLY_SMALLER     => __(
				'Automatic removal of files in output formats larger than original',
				'webp-converter-for-media'
			),
			self::OPTION_VALUE_MOD_EXPIRES      => __(
				'Browser Caching for files in output formats (saving images in browser cache memory)',
				'webp-converter-for-media'
			),
			self::OPTION_VALUE_KEEP_METADATA    => __(
				'Keep images metadata stored in EXIF or XMP formats (unavailable for GD conversion method)',
				'webp-converter-for-media'
			),
			self::OPTION_VALUE_CRON_ENABLED     => __(
				'Enable cron to automatically convert images from outside Media Library (images from Media Library are converted immediately after upload)',
				'webp-converter-for-media'
			),
			self::OPTION_VALUE_CRON_CONVERSION  => __(
				'Enable cron to convert images uploaded to Media Library to speed up process of adding images (deactivate this option if images added to Media Library are not automatically converted)',
				'webp-converter-for-media'
			),
			self::OPTION_VALUE_REFERER_DISABLED => __(
				'Force redirections to output formats for all domains (by default, images in output formats are loaded only in domain of your website - when image is displayed via URL on another domain that original file is loaded)',
				'webp-converter-for-media'
			),
			self::OPTION_VALUE_DEBUG_ENABLED    => __(
				'Log errors while converting to debug.log file (when debugging in WordPress is active)',
				'webp-converter-for-media'
			),
		];
	}

	/**
	 * {@inheritdoc}
	 *
	 * @return string[]
	 */
	public function get_disabled_values( array $settings ): array {
		$values = [];
		if ( ( $settings[ ConversionMethodOption::OPTION_NAME ] ?? '' ) === GdMethod::METHOD_NAME ) {
			$values[] = self::OPTION_VALUE_KEEP_METADATA;
		}
		if ( ( $settings[ LoaderTypeOption::OPTION_NAME ] ?? '' ) === PassthruLoader::LOADER_TYPE ) {
			$values[] = self::OPTION_VALUE_REFERER_DISABLED;
		}
		return $values;
	}

	/**
	 * {@inheritdoc}
	 *
	 * @return string[]
	 */
	public function get_default_value( array $settings = null ): array {
		return [
			self::OPTION_VALUE_ONLY_SMALLER,
			self::OPTION_VALUE_MOD_EXPIRES,
			self::OPTION_VALUE_CRON_CONVERSION,
			self::OPTION_VALUE_REFERER_DISABLED,
			self::OPTION_VALUE_DEBUG_ENABLED,
		];
	}

	/**
	 * {@inheritdoc}
	 *
	 * @return string[]
	 */
	public function get_value_for_debug( array $settings ): array {
		return [
			self::OPTION_VALUE_REFERER_DISABLED,
		];
	}
}
