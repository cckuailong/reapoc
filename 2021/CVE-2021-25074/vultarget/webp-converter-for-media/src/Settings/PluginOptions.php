<?php

namespace WebpConverter\Settings;

use WebpConverter\Service\OptionsAccessManager;
use WebpConverter\Settings\Option\OptionIntegration;
use WebpConverter\Settings\Option\OptionsAggregator;

/**
 * Allows to integration with plugin settings by providing list of settings fields and saved values.
 */
class PluginOptions {

	/**
	 * @var OptionsAggregator
	 */
	private $options_aggregator;

	public function __construct() {
		$this->options_aggregator = new OptionsAggregator();
	}

	/**
	 * Returns options of plugin settings.
	 *
	 * @param bool         $is_debug        Is debugging?
	 * @param array[]|null $posted_settings Settings submitted in form.
	 *
	 * @return array[] Options of plugin settings.
	 */
	public function get_options( bool $is_debug = false, array $posted_settings = null ): array {
		$is_save  = ( $posted_settings !== null );
		$settings = ( $is_save ) ? $posted_settings : OptionsAccessManager::get_option( SettingsSave::SETTINGS_OPTION, [] );

		$options = [];
		foreach ( $this->options_aggregator->get_options() as $option_object ) {
			$options[] = ( new OptionIntegration( $option_object ) )->get_option_data( $settings, $is_debug, $is_save );
		}
		return $options;
	}

	/**
	 * Returns values of plugin settings.
	 *
	 * @param bool         $is_debug        Is debugging?
	 * @param array[]|null $posted_settings Settings submitted in form.
	 *
	 * @return array[] Values of plugin settings.
	 */
	public function get_values( bool $is_debug = false, array $posted_settings = null ): array {
		$values = [];
		foreach ( $this->get_options( $is_debug, $posted_settings ) as $option ) {
			$values[ $option['name'] ] = $option['value'];
		}
		return $values;
	}
}
