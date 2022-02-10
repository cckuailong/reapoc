<?php

namespace WebpConverter\Error\Detector;

use WebpConverter\Conversion\Format\WebpFormat;
use WebpConverter\Conversion\Method\GdMethod;
use WebpConverter\Conversion\Method\ImagickMethod;
use WebpConverter\Conversion\Method\RemoteMethod;
use WebpConverter\Error\Notice\LibsWithoutWebpSupportNotice;
use WebpConverter\PluginData;
use WebpConverter\Settings\Option\ConversionMethodOption;

/**
 * Checks for configuration errors about image conversion methods that do not support WebP output format.
 */
class LibsWithoutWebpSupportDetector implements ErrorDetector {

	/**
	 * @var PluginData
	 */
	private $plugin_data;

	public function __construct( PluginData $plugin_data ) {
		$this->plugin_data = $plugin_data;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_error() {
		$plugin_settings = $this->plugin_data->get_plugin_settings();
		if ( $plugin_settings[ ConversionMethodOption::OPTION_NAME ] === RemoteMethod::METHOD_NAME ) {
			return null;
		}

		if ( GdMethod::is_method_active( WebpFormat::FORMAT_EXTENSION )
			|| ImagickMethod::is_method_active( WebpFormat::FORMAT_EXTENSION ) ) {
			return null;
		}

		return new LibsWithoutWebpSupportNotice();
	}
}
