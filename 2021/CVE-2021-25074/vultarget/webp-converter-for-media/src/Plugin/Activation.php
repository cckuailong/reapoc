<?php

namespace WebpConverter\Plugin;

use WebpConverter\HookableInterface;
use WebpConverter\Plugin\Activation\DefaultSettings;
use WebpConverter\Plugin\Activation\RefreshLoader;
use WebpConverter\Plugin\Activation\WebpDirectory;
use WebpConverter\PluginInfo;

/**
 * Runs actions after plugin activation.
 */
class Activation implements HookableInterface {

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
		register_activation_hook( $this->plugin_info->get_plugin_file(), [ $this, 'load_activation_actions' ] );
	}

	/**
	 * Initializes actions when plugin is activated.
	 *
	 * @return void
	 * @internal
	 */
	public function load_activation_actions() {
		( new WebpDirectory() )->create_directory_for_uploads_webp();
		( new DefaultSettings( $this->plugin_info ) )->add_default_options();
		( new RefreshLoader() )->refresh_image_loader();
	}
}
