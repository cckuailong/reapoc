<?php

namespace WebpConverter\Plugin\Deactivation;

use WebpConverter\HookableInterface;
use WebpConverter\PluginData;
use WebpConverter\PluginInfo;
use WebpConverter\Service\ViewLoader;
use WebpConverter\Settings\AdminAssets;

/**
 * Displays modal with poll in list of plugins when you try to deactivate plugin.
 */
class Modal implements HookableInterface {

	const FEEDBACK_API_URL = 'https://feedback.gbiorczyk.pl/';

	/**
	 * @var PluginInfo
	 */
	private $plugin_info;

	/**
	 * @var PluginData
	 */
	private $plugin_data;

	public function __construct( PluginInfo $plugin_info, PluginData $plugin_data ) {
		$this->plugin_info = $plugin_info;
		$this->plugin_data = $plugin_data;
	}

	/**
	 * {@inheritdoc}
	 */
	public function init_hooks() {
		if ( basename( ( $_SERVER['SCRIPT_FILENAME'] ?? '' ), '.php' ) !== 'plugins' ) { // phpcs:ignore
			return;
		}

		( new AdminAssets( $this->plugin_info ) )->init_hooks();
		add_action( 'admin_footer', [ $this, 'load_deactivation_modal' ] );
	}

	/**
	 * Loads modal with poll when plugin is deactivated.
	 *
	 * @return void
	 */
	public function load_deactivation_modal() {
		( new ViewLoader( $this->plugin_info ) )->load_view(
			'views/deactivation-modal.php',
			[
				'errors'         => apply_filters( 'webpc_server_errors', [] ),
				'reasons'        => $this->get_reasons(),
				'settings'       => $this->plugin_data->get_plugin_settings(),
				'api_url'        => self::FEEDBACK_API_URL,
				'plugin_version' => $this->plugin_info->get_plugin_version(),
			]
		);
	}

	/**
	 * Returns list of reasons for plugin deactivation.
	 *
	 * @return array[] Reasons for plugin deactivation.
	 */
	private function get_reasons(): array {
		return [
			[
				'key'         => 'server_config',
				'label'       => __( 'I have "Server configuration error" in plugin settings', 'webp-converter-for-media' ),
				'placeholder' => esc_attr( __( 'What is your error? Have you been looking for solution to this issue?', 'webp-converter-for-media' ) ),
			],
			[
				'key'         => 'website_broken',
				'label'       => __( 'This plugin broke my website', 'webp-converter-for-media' ),
				'placeholder' => esc_attr( __( 'What exactly happened?', 'webp-converter-for-media' ) ),
			],
			[
				'key'         => 'better_plugin',
				'label'       => __( 'I found a better plugin', 'webp-converter-for-media' ),
				'placeholder' => esc_attr( __( 'What is name of this plugin? Why is it better?', 'webp-converter-for-media' ) ),
			],
			[
				'key'         => 'misunderstanding',
				'label'       => __( 'I do not understand how the plugin works', 'webp-converter-for-media' ),
				'placeholder' => esc_attr( __( 'What is non-understandable to you? Did you search for this in plugin FAQ?', 'webp-converter-for-media' ) ),
			],
			[
				'key'         => 'temporary_deactivation',
				'label'       => __( 'This is a temporary deactivation', 'webp-converter-for-media' ),
				'placeholder' => '',
			],
			[
				'key'         => 'other',
				'label'       => __( 'Other reason', 'webp-converter-for-media' ),
				'placeholder' => esc_attr( __( 'What is reason? What can we improve for you?', 'webp-converter-for-media' ) ),
			],
		];
	}
}
