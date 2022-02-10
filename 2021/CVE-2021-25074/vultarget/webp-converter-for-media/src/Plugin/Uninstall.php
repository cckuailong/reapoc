<?php

namespace WebpConverter\Plugin;

use WebpConverter\HookableInterface;
use WebpConverter\Plugin\Uninstall\DebugFiles;
use WebpConverter\Plugin\Uninstall\HtaccessFile;
use WebpConverter\Plugin\Uninstall\PluginSettings;
use WebpConverter\Plugin\Uninstall\WebpFiles;
use WebpConverter\PluginInfo;

/**
 * Runs actions before plugin uninstallation.
 */
class Uninstall implements HookableInterface {

	/**
	 * @var PluginInfo
	 */
	private $plugin_info;

	public function __construct( PluginInfo $plugin_info ) {
		$this->plugin_info = $plugin_info;
	}

	/**
	 * {@inheritdoc}
	 */
	public function init_hooks() {
		register_uninstall_hook( $this->plugin_info->get_plugin_file(), [ 'WebpConverter\Plugin\Uninstall', 'load_uninstall_actions' ] );
	}

	/**
	 * Initializes actions when plugin is uninstalled.
	 *
	 * @return void
	 * @internal
	 */
	public static function load_uninstall_actions() {
		PluginSettings::remove_plugin_settings();
		HtaccessFile::remove_htaccess_file();
		WebpFiles::remove_webp_files();
		DebugFiles::remove_debug_files();
	}
}
