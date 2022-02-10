<?php

namespace WebpConverter\Plugin\Activation;

use WebpConverter\Notice\NoticeIntegration;
use WebpConverter\Notice\ThanksNotice;
use WebpConverter\Notice\WelcomeNotice;
use WebpConverter\PluginInfo;

/**
 * Adds default options for plugin settings.
 */
class DefaultSettings {

	/**
	 * @var PluginInfo
	 */
	private $plugin_info;

	public function __construct( PluginInfo $plugin_info ) {
		$this->plugin_info = $plugin_info;
	}

	/**
	 * Sets default value for admin notices.
	 *
	 * @return void
	 */
	public function add_default_options() {
		( new NoticeIntegration( $this->plugin_info, new ThanksNotice() ) )->set_default_value();
		( new NoticeIntegration( $this->plugin_info, new WelcomeNotice() ) )->set_default_value();
	}
}
