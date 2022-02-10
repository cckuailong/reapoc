<?php

namespace WebpConverter\Notice;

use WebpConverter\HookableInterface;
use WebpConverter\PluginInfo;

/**
 * Adds integration for list of notices.
 */
class NoticeFactory implements HookableInterface {

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
		$this->set_integration( new ThanksNotice() );
		$this->set_integration( new WelcomeNotice() );
	}

	/**
	 * Sets integration for notice.
	 *
	 * @param NoticeInterface $notice .
	 *
	 * @return void
	 */
	private function set_integration( NoticeInterface $notice ) {
		( new NoticeIntegration( $this->plugin_info, $notice ) )->init_hooks();
	}
}
