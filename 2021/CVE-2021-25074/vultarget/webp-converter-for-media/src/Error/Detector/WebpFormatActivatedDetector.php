<?php

namespace WebpConverter\Error\Detector;

use WebpConverter\Conversion\Format\WebpFormat;
use WebpConverter\Error\Notice\WebpRequiredNotice;
use WebpConverter\PluginData;
use WebpConverter\Settings\Option\OutputFormatsOption;

/**
 * Checks if the WebP as output format is active.
 */
class WebpFormatActivatedDetector implements ErrorDetector {

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
		if ( in_array( WebpFormat::FORMAT_EXTENSION, $plugin_settings[ OutputFormatsOption::OPTION_NAME ] ) ) {
			return null;
		}

		return new WebpRequiredNotice();
	}
}
