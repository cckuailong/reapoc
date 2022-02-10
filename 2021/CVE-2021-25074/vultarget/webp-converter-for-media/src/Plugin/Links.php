<?php

namespace WebpConverter\Plugin;

use WebpConverter\HookableInterface;
use WebpConverter\PluginInfo;
use WebpConverter\Settings\Page\PageIntegration;

/**
 * Adds links to plugin in list of plugins in panel.
 */
class Links implements HookableInterface {

	const DONATION_URL = 'https://ko-fi.com/gbiorczyk/?utm_source=webp-converter-for-media&utm_medium=plugin-links';

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
		add_filter( 'plugin_action_links_' . $this->plugin_info->get_plugin_basename(), [ $this, 'add_plugin_links_for_admin' ] );
		add_filter( 'network_admin_plugin_action_links_' . $this->plugin_info->get_plugin_basename(), [ $this, 'add_plugin_links_for_network' ] );
	}

	/**
	 * Adds new links to list of plugin actions for non-multisite websites.
	 *
	 * @param string[] $links Plugin action links.
	 *
	 * @return string[] Plugin action links.
	 * @internal
	 */
	public function add_plugin_links_for_admin( array $links ): array {
		if ( is_multisite() ) {
			return $links;
		}

		$links = $this->add_link_to_settings( $links );
		return $this->add_link_to_donate( $links );
	}

	/**
	 * Adds new links to list of plugin actions for multisite websites.
	 *
	 * @param string[] $links Plugin action links.
	 *
	 * @return string[] Plugin action links.
	 * @internal
	 */
	public function add_plugin_links_for_network( array $links ): array {
		$links = $this->add_link_to_settings( $links );
		return $this->add_link_to_donate( $links );
	}

	/**
	 * Adds link to plugin settings page.
	 *
	 * @param string[] $links Plugin action links.
	 *
	 * @return string[] Plugin action links.
	 */
	private function add_link_to_settings( array $links ): array {
		array_unshift(
			$links,
			sprintf(
			/* translators: %1$s: open anchor tag, %2$s: close anchor tag */
				esc_html( __( '%1$sSettings%2$s', 'webp-converter-for-media' ) ),
				'<a href="' . PageIntegration::get_settings_page_url() . '">',
				'</a>'
			)
		);
		return $links;
	}

	/**
	 * Adds link to donation.
	 *
	 * @param string[] $links Plugin action links.
	 *
	 * @return string[] Plugin action links.
	 * @internal
	 */
	private function add_link_to_donate( array $links ): array {
		$links[] = sprintf(
		/* translators: %1$s: open anchor tag, %2$s: close anchor tag */
			esc_html( __( '%1$sProvide us a coffee%2$s', 'webp-converter-for-media' ) ),
			'<a href="' . self::DONATION_URL . '" target="_blank">',
			'</a>'
		);
		return $links;
	}
}
