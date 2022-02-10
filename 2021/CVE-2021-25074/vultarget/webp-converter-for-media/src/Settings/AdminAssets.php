<?php

namespace WebpConverter\Settings;

use WebpConverter\HookableInterface;
use WebpConverter\PluginInfo;

/**
 * Initializes loading of assets in admin panel.
 */
class AdminAssets implements HookableInterface {

	const CSS_FILE_PATH = 'assets/build/css/styles.css';
	const JS_FILE_PATH  = 'assets/build/js/scripts.js';

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
		add_filter( 'admin_enqueue_scripts', [ $this, 'load_styles' ] );
		add_filter( 'admin_enqueue_scripts', [ $this, 'load_scripts' ] );
	}

	/**
	 * Loads CSS assets.
	 *
	 * @return void
	 * @internal
	 */
	public function load_styles() {
		wp_register_style(
			'webp-converter',
			$this->plugin_info->get_plugin_directory_url() . self::CSS_FILE_PATH,
			[],
			$this->plugin_info->get_plugin_version()
		);
		wp_enqueue_style( 'webp-converter' );
	}

	/**
	 * Loads JavaScript assets.
	 *
	 * @return void
	 * @internal
	 */
	public function load_scripts() {
		wp_register_script(
			'webp-converter',
			$this->plugin_info->get_plugin_directory_url() . self::JS_FILE_PATH,
			[],
			$this->plugin_info->get_plugin_version(),
			true
		);
		wp_enqueue_script( 'webp-converter' );
	}
}
