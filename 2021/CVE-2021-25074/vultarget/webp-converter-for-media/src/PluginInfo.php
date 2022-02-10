<?php

namespace WebpConverter;

/**
 * Stores information about the plugin.
 */
class PluginInfo {

	/**
	 * @var string
	 */
	private $plugin_file;

	/**
	 * @var string
	 */
	private $plugin_version;

	/**
	 * @var string
	 */
	private $plugin_basename;

	/**
	 * @var string
	 */
	private $plugin_directory_path;

	/**
	 * @var string
	 */
	private $plugin_directory_url;

	/**
	 * @param string $plugin_file    Path to the main plugin file.
	 * @param string $plugin_version .
	 */
	public function __construct( string $plugin_file, string $plugin_version ) {
		$this->plugin_file           = $plugin_file;
		$this->plugin_version        = $plugin_version;
		$this->plugin_basename       = plugin_basename( $plugin_file );
		$this->plugin_directory_path = plugin_dir_path( $plugin_file );
		$this->plugin_directory_url  = plugin_dir_url( $plugin_file );
	}

	public function get_plugin_file(): string {
		return $this->plugin_file;
	}

	public function get_plugin_version(): string {
		return $this->plugin_version;
	}

	public function get_plugin_basename(): string {
		return $this->plugin_basename;
	}

	public function get_plugin_directory_path(): string {
		return $this->plugin_directory_path;
	}

	public function get_plugin_directory_url(): string {
		return $this->plugin_directory_url;
	}
}
