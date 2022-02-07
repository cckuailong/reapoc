<?php

if (!interface_exists('UpdraftCentral_Host_Interface')) require_once('interface.php');

/**
 * This class is the basic bridge between the UpdraftCentral and UpdraftPlus.
 */
class UpdraftPlus_Host implements UpdraftCentral_Host_Interface {

	const PLUGIN_NAME = 'updraftplus';

	private $translations;

	protected static $_instance = null;

	/**
	 * Creates an instance of this class. Singleton Pattern
	 *
	 * @return object Instance of this class
	 */
	public static function instance() {
		if (empty(self::$_instance)) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Class constructor
	 */
	public function __construct() {
		add_action('updraftplus_debugtools_dashboard', array($this, 'debugtools_dashboard'), 20);

		$this->maybe_initialize_required_objects();
	}

	/**
	 * Retrieves or shows a message from the translations collection based on its identifier key
	 *
	 * @param string $key  The ID of the the message
	 * @param bool   $echo Indicate whether the message is to be shown directly (echoed) or just for retrieval
	 *
	 * @return string/void
	 */
	public function retrieve_show_message($key, $echo = false) {
		if (empty($key) || !isset($this->translations[$key])) return '';

		if ($echo) {
			echo $this->translations[$key];
			return;
		}

		return $this->translations[$key];
	}

	/**
	 * Below are interface methods' implementations that are required by UpdraftCentral to function properly. Please
	 * see the "interface.php" to check all the required interface methods.
	 */

	/**
	 * Checks whether the plugin's DIR constant is currently define or not
	 *
	 * @return bool
	 */
	public function is_host_dir_set() {
		return defined('UPDRAFTPLUS_DIR') ? true : false;
	}

	/**
	 * Retrieves the filter used by UpdraftPlus to log errors or certain events
	 *
	 * @return string
	 */
	public function get_logline_filter() {
		return 'updraftplus_logline';
	}

	/**
	 * Checks whether debug mod is set
	 *
	 * @return bool
	 */
	public function get_debug_mode() {
		return $this->get_option('updraft_debug_mode');
	}

	/**
	 * Gets an RPC object, and sets some defaults on it that we always want
	 *
	 * @param string $indicator_name indicator name
	 * @return array|bool
	 */
	public function get_udrpc($indicator_name) {
		global $updraftplus;

		if ($updraftplus) {
			return $updraftplus->get_udrpc($indicator_name);
		}

		return false;
	}

	/**
	 * Used as a central location (to avoid repetition) to register or de-register hooks into the WP HTTP API
	 *
	 * @param bool $register True to register, false to de-register
	 * @return void
	 */
	public function register_wp_http_option_hooks($register = true) {
		global $updraftplus;

		if ($updraftplus) {
			$updraftplus->register_wp_http_option_hooks($register);
		}
	}

	/**
	 * Retrieves the class name of the host plugin
	 *
	 * @return string|bool
	 */
	public function get_class_name() {
		global $updraftplus;

		if ($updraftplus) {
			return get_class($updraftplus);
		}

		return false;
	}

	/**
	 * Returns the instance of the host plugin
	 *
	 * @return object|bool
	 */
	public function get_instance() {
		global $updraftplus;

		if ($updraftplus) {
			return $updraftplus;
		}

		return false;
	}

	/**
	 * Returns the admin instance of the host plugin
	 *
	 * @return object|bool
	 */
	public function get_admin_instance() {
		global $updraftplus_admin;

		if ($updraftplus_admin) {
			return $updraftplus_admin;
		} else {
			if (defined('UPDRAFTPLUS_DIR') && file_exists(UPDRAFTPLUS_DIR.'/admin.php')) {
				include_once(UPDRAFTPLUS_DIR.'/admin.php');
				$updraftplus_admin = new UpdraftPlus_Admin();
				return $updraftplus_admin;
			}
		}

		return false;
	}

	/**
	 * Retrieves the host plugin's Options class
	 *
	 * @return class|bool
	 */
	public function get_option_class() {
		if ($this->has_options()) {
			return UpdraftPlus_Options;
		}

		return false;
	}

	/**
	 * Checks whether the host plugin's Options class exists
	 *
	 * @return bool
	 */
	public function has_options() {
		return class_exists('UpdraftPlus_Options');
	}

	/**
	 * Updates a specific option's value
	 *
	 * @param string $option	Specify option name
	 * @param string $value	    Specify option value
	 * @param bool   $use_cache Whether or not to use the WP options cache
	 * @param string $autoload	Whether to autoload (only takes effect on a change of value)
	 *
	 * @return bool
	 */
	public function update_option($option, $value, $use_cache = true, $autoload = 'yes') {
		if ($this->has_options()) {
			return UpdraftPlus_Options::update_updraft_option($option, $value, $use_cache, $autoload);
		}

		return false;
	}

	/**
	 * Retrieves a specific option's value
	 *
	 * @param string $option  Specify option name
	 * @param mixed  $default Optional. The default value to return when option is not found
	 *
	 * @return mixed|bool
	 */
	public function get_option($option, $default = null) {
		if ($this->has_options()) {
			return UpdraftPlus_Options::get_updraft_option($option, $default);
		}

		return false;
	}

	/**
	 * Returns the current version of the host plugin
	 *
	 * @return string|bool
	 */
	public function get_version() {
		global $updraftplus;

		if ($updraftplus) {
			return $updraftplus->version;
		}

		return false;
	}

	/**
	 * Returns the filesystem class of the host's plugin
	 *
	 * @return class|bool
	 */
	public function get_filesystem_functions() {
		if ($this->has_filesystem_functions()) {
			return UpdraftPlus_Filesystem_Functions;
		}

		return false;
	}

	/**
	 * Checks whether the filesystem class of the host plugin exists
	 *
	 * @return bool
	 */
	public function has_filesystem_functions() {
		return class_exists('UpdraftPlus_Filesystem_Functions');
	}

	/**
	 * Checks whether force debugging is set
	 *
	 * @return bool
	 */
	public function is_force_debug() {
		return (defined('UPDRAFTPLUS_UDRPC_FORCE_DEBUG') && UPDRAFTPLUS_UDRPC_FORCE_DEBUG) ? true : false;
	}

	/**
	 * Adds a section to the 'advanced tools' page for generating UpdraftCentral keys. Called by a filter
	 * inside the constructor method of this class.
	 *
	 * @return void
	 */
	public function debugtools_dashboard() {
		global $updraftcentral_main;

		if (!class_exists('UpdraftCentral_Main')) {
			if (defined('UPDRAFTCENTRAL_CLIENT_DIR') && file_exists(UPDRAFTCENTRAL_CLIENT_DIR.'/bootstrap.php')) {
				include_once(UPDRAFTCENTRAL_CLIENT_DIR.'/bootstrap.php');
				$updraftcentral_main = new UpdraftCentral_Main();
			}
		}

		if ($updraftcentral_main) {
			$updraftcentral_main->debugtools_dashboard();
		}
	}

	/**
	 * Initializes required objects (if not yet initialized) for UpdraftCentral usage
	 *
	 * @return void
	 */
	private function maybe_initialize_required_objects() {
		global $updraftplus;

		if (!class_exists('UpdraftPlus')) {
			if (defined('UPDRAFTPLUS_DIR') && file_exists(UPDRAFTPLUS_DIR.'/class-updraftplus.php')) {
				include_once(UPDRAFTPLUS_DIR.'/class-updraftplus.php');
				if (empty($updraftplus) || !is_a($updraftplus, 'UpdraftPlus')) {
					$updraftplus = new UpdraftPlus();
				}
			}
		}

		if (!class_exists('UpdraftPlus_Options')) {
			if (defined('UPDRAFTPLUS_DIR') && file_exists(UPDRAFTPLUS_DIR.'/options.php')) {
				require_once(UPDRAFTPLUS_DIR.'/options.php');
			}
		}

		if (!class_exists('UpdraftPlus_Filesystem_Functions')) {
			if (defined('UPDRAFTPLUS_DIR') && file_exists(UPDRAFTPLUS_DIR.'/includes/class-filesystem-functions.php')) {
				require_once(UPDRAFTPLUS_DIR.'/includes/class-filesystem-functions.php');
			}
		}

		// Load updraftplus translations
		if (defined('UPDRAFTCENTRAL_CLIENT_DIR') && file_exists(UPDRAFTCENTRAL_CLIENT_DIR.'/translations-updraftplus.php')) {
			$this->translations = include_once(UPDRAFTCENTRAL_CLIENT_DIR.'/translations-updraftplus.php');
		}
	}
}
