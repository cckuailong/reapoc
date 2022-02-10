<?php

namespace WebpConverter\Plugin;

use WebpConverter\HookableInterface;
use WebpConverter\Loader\LoaderAbstract;
use WebpConverter\Plugin\Activation\DefaultSettings;
use WebpConverter\PluginInfo;
use WebpConverter\Service\OptionsAccessManager;

/**
 * Runs actions after plugin update to new version.
 */
class Update implements HookableInterface {

	const VERSION_OPTION = 'webpc_latest_version';

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
		add_action( 'admin_init', [ $this, 'run_actions_after_update' ], 0 );
	}

	/**
	 * Initializes actions after updating plugin to different version.
	 *
	 * @return void
	 * @internal
	 */
	public function run_actions_after_update() {
		$version = OptionsAccessManager::get_option( self::VERSION_OPTION );
		if ( $version === $this->plugin_info->get_plugin_version() ) {
			return;
		}

		( new DefaultSettings( $this->plugin_info ) )->add_default_options();
		do_action( LoaderAbstract::ACTION_NAME, true );

		OptionsAccessManager::update_option( self::VERSION_OPTION, $this->plugin_info->get_plugin_version() );
	}
}
