<?php

namespace WebpConverter\Error\Detector;

use WebpConverter\Error\Notice\SettingsIncorrectNotice;
use WebpConverter\PluginData;
use WebpConverter\Settings\Option\ConversionMethodOption;
use WebpConverter\Settings\Option\ImagesQualityOption;
use WebpConverter\Settings\Option\LoaderTypeOption;
use WebpConverter\Settings\Option\OutputFormatsOption;
use WebpConverter\Settings\Option\SupportedDirectoriesOption;
use WebpConverter\Settings\Option\SupportedExtensionsOption;

/**
 * Checks for configuration errors about incorrectly saved plugin settings.
 */
class SettingsIncorrectDetector implements ErrorDetector {

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

		if ( ( ! isset( $plugin_settings[ SupportedExtensionsOption::OPTION_NAME ] )
				|| ! $plugin_settings[ SupportedExtensionsOption::OPTION_NAME ] )
			|| ( ! isset( $plugin_settings[ SupportedDirectoriesOption::OPTION_NAME ] )
				|| ! $plugin_settings[ SupportedDirectoriesOption::OPTION_NAME ] )
			|| ( ! isset( $plugin_settings[ OutputFormatsOption::OPTION_NAME ] )
				|| ! $plugin_settings[ OutputFormatsOption::OPTION_NAME ] )
			|| ( ! isset( $plugin_settings[ ConversionMethodOption::OPTION_NAME ] )
				|| ! $plugin_settings[ ConversionMethodOption::OPTION_NAME ] )
			|| ( ! isset( $plugin_settings[ ImagesQualityOption::OPTION_NAME ] )
				|| ! $plugin_settings[ ImagesQualityOption::OPTION_NAME ] )
			|| ( ! isset( $plugin_settings[ LoaderTypeOption::OPTION_NAME ] )
				|| ! $plugin_settings[ LoaderTypeOption::OPTION_NAME ] ) ) {
			return new SettingsIncorrectNotice();
		}

		return null;
	}
}
