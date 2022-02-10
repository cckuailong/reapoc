<?php
if (!defined('WPVIVID_PLUGIN_DIR')){
    die;
}
/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://wpvivid.com
 * @since      0.9.1
 *
 * @package    wpvivid
 * @subpackage wpvivid/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      0.9.1
 * @package    wpvivid
 * @subpackage wpvivid/includes
 * @author     wpvivid team
 */
class WPvivid_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * 
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'wpvivid-backuprestore',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
