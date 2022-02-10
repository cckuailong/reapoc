<?php


namespace WebpConverter\Settings\Page;

use WebpConverter\Conversion\Endpoint\ImagesCounterEndpoint;
use WebpConverter\Conversion\Endpoint\PathsEndpoint;
use WebpConverter\Conversion\Endpoint\RegenerateEndpoint;
use WebpConverter\Loader\LoaderAbstract;
use WebpConverter\PluginData;
use WebpConverter\PluginInfo;
use WebpConverter\Repository\TokenRepository;
use WebpConverter\Service\ViewLoader;
use WebpConverter\Settings\PluginOptions;
use WebpConverter\Settings\SettingsSave;

/**
 * Supports default tab in plugin settings page.
 */
class SettingsPage extends PageAbstract {

	const PAGE_VIEW_PATH = 'views/settings.php';

	/**
	 * @var PluginInfo
	 */
	private $plugin_info;

	/**
	 * @var PluginData
	 */
	private $plugin_data;

	/**
	 * @var TokenRepository
	 */
	private $token_repository;

	public function __construct(
		PluginInfo $plugin_info,
		PluginData $plugin_data,
		TokenRepository $token_repository
	) {
		$this->plugin_info      = $plugin_info;
		$this->plugin_data      = $plugin_data;
		$this->token_repository = $token_repository;
	}

	/**
	 * {@inheritdoc}
	 */
	public function is_page_active(): bool {
		return ( ! isset( $_GET['action'] ) || ( $_GET['action'] !== 'server' ) ); // phpcs:ignore
	}

	/**
	 * {@inheritdoc}
	 */
	public function show_page_view() {
		( new SettingsSave( $this->plugin_data ) )->save_settings();

		( new ViewLoader( $this->plugin_info ) )->load_view(
			self::PAGE_VIEW_PATH,
			[
				'errors_messages'         => apply_filters( 'webpc_server_errors_messages', [] ),
				'errors_codes'            => apply_filters( 'webpc_server_errors', [] ),
				'options'                 => ( new PluginOptions() )->get_options(),
				'submit_value'            => SettingsSave::SUBMIT_VALUE,
				'submit_activate_token'   => SettingsSave::SUBMIT_TOKEN_ACTIVATE,
				'submit_deactivate_token' => SettingsSave::SUBMIT_TOKEN_DEACTIVATE,
				'token_valid_status'      => $this->token_repository->get_token()->get_valid_status(),
				'settings_url'            => sprintf(
					'%1$s&%2$s=%3$s',
					PageIntegration::get_settings_page_url(),
					SettingsSave::NONCE_PARAM_KEY,
					wp_create_nonce( SettingsSave::NONCE_PARAM_VALUE )
				),
				'settings_debug_url'      => sprintf(
					'%s&action=server',
					PageIntegration::get_settings_page_url()
				),
				'api_calculate_url'       => ( new ImagesCounterEndpoint( $this->plugin_data, $this->token_repository ) )->get_route_url(),
				'api_paths_url'           => ( new PathsEndpoint( $this->plugin_data, $this->token_repository ) )->get_route_url(),
				'api_regenerate_url'      => ( new RegenerateEndpoint( $this->plugin_data ) )->get_route_url(),
			]
		);

		do_action( LoaderAbstract::ACTION_NAME, true );
	}
}
