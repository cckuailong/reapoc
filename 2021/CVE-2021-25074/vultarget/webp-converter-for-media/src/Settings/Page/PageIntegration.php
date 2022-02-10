<?php

namespace WebpConverter\Settings\Page;

use WebpConverter\HookableInterface;
use WebpConverter\Notice\NoticeIntegration;
use WebpConverter\Notice\WelcomeNotice;
use WebpConverter\PluginInfo;
use WebpConverter\Settings\AdminAssets;

/**
 * Adds plugin settings page in admin panel.
 */
class PageIntegration implements HookableInterface {

	const ADMIN_MENU_PAGE = 'webpc_admin_page';

	/**
	 * @var PluginInfo
	 */
	private $plugin_info;

	public function __construct( PluginInfo $plugin_info ) {
		$this->plugin_info = $plugin_info;
	}

	/**
	 * Objects of supported plugin settings pages.
	 *
	 * @var PageInterface[]
	 */
	private $pages = [];

	/**
	 * {@inheritdoc}
	 */
	public function init_hooks() {
		add_action( 'admin_menu', [ $this, 'add_settings_page_for_admin' ] );
		add_action( 'network_admin_menu', [ $this, 'add_settings_page_for_network' ] );
	}

	/**
	 * Sets integration for page.
	 *
	 * @param PageInterface $page .
	 *
	 * @return self
	 */
	public function set_page_integration( PageInterface $page ) {
		$this->pages[] = $page;

		return $this;
	}

	/**
	 * Returns URL of plugin settings page.
	 *
	 * @return string
	 */
	public static function get_settings_page_url(): string {
		if ( ! is_multisite() ) {
			return menu_page_url( self::ADMIN_MENU_PAGE, false );
		} else {
			return network_admin_url( 'settings.php?page=' . self::ADMIN_MENU_PAGE );
		}
	}

	/**
	 * Adds settings page to menu for non-multisite websites.
	 *
	 * @return void
	 * @internal
	 */
	public function add_settings_page_for_admin() {
		if ( is_multisite() ) {
			return;
		}
		$this->add_settings_page( 'options-general.php' );
	}

	/**
	 * Adds settings page to menu for multisite websites.
	 *
	 * @return void
	 * @internal
	 */
	public function add_settings_page_for_network() {
		$this->add_settings_page( 'settings.php' );
	}

	/**
	 * Creates plugin settings page in WordPress Admin Dashboard.
	 *
	 * @param string $menu_page Parent menu page.
	 *
	 * @return void
	 */
	private function add_settings_page( string $menu_page ) {
		$page = add_submenu_page(
			$menu_page,
			'WebP Converter for Media',
			'WebP Converter',
			'manage_options',
			self::ADMIN_MENU_PAGE,
			[ $this, 'load_settings_page' ]
		);
		add_action( 'load-' . $page, [ $this, 'load_scripts_for_page' ] );
	}

	/**
	 * Loads selected view on plugin settings page.
	 *
	 * @return void
	 * @internal
	 */
	public function load_settings_page() {
		foreach ( $this->pages as $page ) {
			$this->init_page_is_active( $page );
		}
	}

	/**
	 * Initializes page loading if is active.
	 *
	 * @param PageInterface $page .
	 *
	 * @return void
	 */
	private function init_page_is_active( PageInterface $page ) {
		if ( ! $page->is_page_active() ) {
			return;
		}

		$page->show_page_view();
	}

	/**
	 * Loads assets on plugin settings page.
	 *
	 * @return void
	 * @internal
	 */
	public function load_scripts_for_page() {
		( new NoticeIntegration( $this->plugin_info, new WelcomeNotice() ) )->set_disable_value();
		( new AdminAssets( $this->plugin_info ) )->init_hooks();
	}
}
