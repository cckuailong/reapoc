<?php

namespace WebpConverter\Notice;

use WebpConverter\HookableInterface;
use WebpConverter\PluginInfo;
use WebpConverter\Service\OptionsAccessManager;
use WebpConverter\Service\ViewLoader;
use WebpConverter\Settings\AdminAssets;

/**
 * Supports ability to display notice and its management.
 */
class NoticeIntegration implements HookableInterface {

	/**
	 * @var PluginInfo
	 */
	private $plugin_info;

	/**
	 * @var NoticeInterface
	 */
	private $notice;

	public function __construct( PluginInfo $plugin_info, NoticeInterface $notice ) {
		$this->plugin_info = $plugin_info;
		$this->notice      = $notice;
	}

	/**
	 * {@inheritdoc}
	 */
	public function init_hooks() {
		add_action( 'admin_init', [ $this, 'init_notice_hooks' ] );

		if ( $ajax_action = $this->notice->get_ajax_action_to_disable() ) {
			add_action( 'wp_ajax_' . $ajax_action, [ $this, 'set_disable_value' ] );
		}
	}

	/**
	 * Initializes displaying notice in administration panel.
	 *
	 * @return void
	 * @internal
	 */
	public function init_notice_hooks() {
		if ( ! $this->notice->is_available() || ! $this->notice->is_active() ) {
			return;
		}

		( new AdminAssets( $this->plugin_info ) )->init_hooks();
		if ( ! is_multisite() ) {
			add_action( 'admin_notices', [ $this, 'load_notice' ] );
		} else {
			add_action( 'network_admin_notices', [ $this, 'load_notice' ] );
		}
	}

	/**
	 * Loads view template for notice.
	 *
	 * @return void
	 * @internal
	 */
	public function load_notice() {
		( new ViewLoader( $this->plugin_info ) )->load_view(
			$this->notice->get_output_path(),
			$this->notice->get_vars_for_view()
		);
	}

	/**
	 * Sets value for option that specifies whether to display notice.
	 *
	 * @return void
	 */
	public function set_default_value() {
		if ( OptionsAccessManager::get_option( $this->notice->get_option_name() ) !== null ) {
			return;
		}

		OptionsAccessManager::update_option( $this->notice->get_option_name(), $this->notice->get_default_value() );
	}

	/**
	 * Sets options to disable notice.
	 *
	 * @return void
	 */
	public function set_disable_value() {
		OptionsAccessManager::update_option( $this->notice->get_option_name(), $this->notice->get_disable_value() );
	}
}
