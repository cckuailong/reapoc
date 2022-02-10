<?php

namespace WebpConverter;

use WebpConverter\Settings\PluginOptions;

/**
 * Manages plugin values.
 */
class PluginData {

	/**
	 * Handler of class with plugin settings.
	 *
	 * @var PluginOptions
	 */
	private $settings_object;

	/**
	 * Cached settings of plugin.
	 *
	 * @var mixed[]|null
	 */
	private $plugin_settings = null;

	/**
	 * Cached settings of plugin for debug.
	 *
	 * @var mixed[]|null
	 */
	private $debug_settings = null;

	public function __construct() {
		$this->settings_object = new PluginOptions();
	}

	/**
	 * Returns settings of plugin.
	 *
	 * @return mixed[] Settings of plugin.
	 */
	public function get_plugin_settings(): array {
		if ( $this->plugin_settings === null ) {
			$this->plugin_settings = $this->settings_object->get_values();
		}
		return $this->plugin_settings;
	}

	/**
	 * Returns settings of plugin for debug.
	 *
	 * @return mixed[] Settings of plugin for debug.
	 */
	public function get_debug_settings(): array {
		if ( $this->debug_settings === null ) {
			$this->debug_settings = $this->settings_object->get_values( true );
		}
		return $this->debug_settings;
	}

	/**
	 * Clears cache for settings of plugin.
	 *
	 * @return void
	 */
	public function invalidate_plugin_settings() {
		$this->plugin_settings = null;
		$this->debug_settings  = null;
	}
}
