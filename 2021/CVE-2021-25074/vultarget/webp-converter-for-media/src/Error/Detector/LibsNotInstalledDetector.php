<?php

namespace WebpConverter\Error\Detector;

use WebpConverter\Conversion\Method\GdMethod;
use WebpConverter\Conversion\Method\ImagickMethod;
use WebpConverter\Conversion\Method\RemoteMethod;
use WebpConverter\Error\Notice\LibsNotInstalledNotice;
use WebpConverter\PluginData;
use WebpConverter\Settings\Option\ConversionMethodOption;

/**
 * Checks for configuration errors about non-installed methods for converting images.
 */
class LibsNotInstalledDetector implements ErrorDetector {

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

		if ( GdMethod::is_method_installed() || ImagickMethod::is_method_installed() ) {
			return null;
		}

		return new LibsNotInstalledNotice();
	}
}
