<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link        https://catchplugins.com/plugins
 * @since      1.0.0
 *
 * @package    Catch_Breadcrumb
 * @subpackage Catch_Breadcrumb/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Catch_Breadcrumb
 * @subpackage Catch_Breadcrumb/includes
 * @author     Catch Plugins <info@catchplugins.com>
 */
class Catch_Breadcrumb_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'catch-breadcrumb',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}
}