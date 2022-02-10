<?php
/**
 * Internationalization (i18n) class
 * Loads plugin textdomain
 *
 * @deprecated 2.0 Plugin is using WordPress.org translation repository
 *                 https://translate.wordpress.org/projects/wp-plugins/advanced-cron-manager
 *                 Please translate it there. NOTE: It's working only in WP >=4.6
 *
 * @package advanced-cron-manager
 */

namespace underDEV\AdvancedCronManager;

use underDEV\Utils;

/**
 * Internationalization class
 */
class Internationalization {

	/**
	 * Files class
	 *
	 * @var instance of underDEV\AdvancedCronManager\Files
	 */
	public $files;

	/**
	 * Class constructor
	 *
	 * @param Utils\Files $files underDEV\AdvancedCronManager\Files instance.
	 */
	public function __construct( Utils\Files $files ) {
		$this->files = $files;
	}

	/**
	 * Loads textdomain
	 *
	 * @return void
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'advanced-cron-manager', false, $this->files->dir_path( 'languages' ) );
	}

}
