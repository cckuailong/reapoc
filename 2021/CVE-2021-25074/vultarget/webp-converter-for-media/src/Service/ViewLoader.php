<?php

namespace WebpConverter\Service;

use WebpConverter\PluginInfo;

/**
 * Supports loading views from /templates directory.
 */
class ViewLoader {

	/**
	 * @var PluginInfo
	 */
	private $plugin_info;

	public function __construct( PluginInfo $plugin_info ) {
		$this->plugin_info = $plugin_info;
	}

	/**
	 * Loads view with given variables.
	 *
	 * @param string  $path   Server path relative to plugin root directory.
	 * @param mixed[] $params Variables for view.
	 *
	 * @return void
	 */
	public function load_view( string $path, array $params = [] ) {
		extract( $params ); // phpcs:ignore
		$view_path = sprintf( '%1$s/templates/%2$s', $this->plugin_info->get_plugin_directory_path(), $path );
		if ( file_exists( $view_path ) ) {
			/** @noinspection PhpIncludeInspection */ // phpcs:ignore
			require_once $view_path;
		}
	}
}
