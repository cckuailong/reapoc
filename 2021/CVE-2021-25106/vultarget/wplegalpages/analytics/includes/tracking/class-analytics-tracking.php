<?php
/**
 * Analytics Tracking Class.
 *
 * @package     Analytics
 * @copyright   Copyright (c) 2019, CyberChimps, Inc.
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License Version 3
 * @since       1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Analytics Tracking.
 */
class Analytics_Tracking {

	private $slug;

	/**
	 * The tracking option name.
	 *
	 * @var string
	 */
	protected $option_name = 'ask-for-usage-last-request';

	/**
	 * The limit for the option.
	 *
	 * @var int
	 */
	protected $threshold = WEEK_IN_SECONDS;

	/**
	 * The endpoint to send the data to.
	 *
	 * @var string
	 */
	protected $endpoint = WP_STAT__ADDRESS . '/wp-json/action/submit/usage/stats/';

	/**
	 * The endpoint to send the tracking data to.
	 *
	 * @var string
	 */
	protected $tracking_endpoint = WP_STAT__ADDRESS . '/wp-json/action/track/analytics/click';

	/**
	 * The current time.
	 *
	 * @var int
	 */
	private $current_time;

	public function __construct( $slug ) {
		$this->slug = $slug;
		if ( ! $this->analytics_tracking_enabled() ) {
			return;
		}
		$this->current_time = time();
		$this->analytics_tracking_register_hooks();
	}

	public function analytics_tracking_register_hooks() {
		if ( ! $this->analytics_tracking_enabled() ) {
			return;
		}

		add_action( 'admin_init', array( $this, 'analytics_tracking_send' ), 1 );
		// Add an action hook that will be triggered at the specified time by `wp_schedule_single_event()`.
		add_action( 'analytics_tracking_send_data_after_core_update', array( $this, 'analytics_tracking_send' ) );
		add_action( 'upgrader_process_complete', array( $this, 'analytics_tracking_schedule_data_sending' ), 10, 2 );
		add_action( 'wp_ajax_wplegalpages_track_analytics', array( $this, 'analytics_tracking_send_clicks' ), 10, 1 );
	}

	/**
	 * Send click tracking data.
	 *
	 * @return array
	 */
	public function analytics_tracking_send_clicks() {
		if ( isset( $_POST['data'] ) && ! empty( $_POST['data'] ) ) {
			if ( isset( $_POST['data']['nonce'] ) && ! empty( $_POST['data']['nonce'] ) ) {
				$nonce = isset( $_POST['data']['nonce'] ) ? $_POST['data']['nonce'] : '';
				if ( wp_verify_nonce( $nonce, 'track_analytics' ) ) {
					$event = isset( $_POST['data']['event'] ) ? sanitize_text_field( $_POST['data']['event'] ) : '';
					if ( ! empty( $event ) ) {
						$args     = array(
							'method' => 'POST',
							'body'   => array(
								'product_name' => $this->slug,
								'event'        => $event,
								'default'      => $this->analytics_tracking_get_default_data(),
							),
						);
						$response = wp_remote_post( $this->tracking_endpoint, $args );
					}
				}
			}
		}
		return array( 'response' => true );
	}

	/**
	 * Schedules a new sending of the tracking data after a WordPress core update.
	 *
	 * @param bool|WP_Upgrader $upgrader Optional. WP_Upgrader instance or false.
	 *                                   Depending on context, it might be a Theme_Upgrader,
	 *                                   Plugin_Upgrader, Core_Upgrade, or Language_Pack_Upgrader.
	 *                                   instance. Default false.
	 * @param array            $data     Array of update data.
	 *
	 * @return void
	 */
	public function analytics_tracking_schedule_data_sending( $upgrader = false, $data = array() ) {
		// Return if it's not a WordPress core update.
		if ( ! $upgrader || ! isset( $data['type'] ) || $data['type'] !== 'core' ) {
			return;
		}

		/*
		 * To uniquely identify the scheduled cron event, `wp_next_scheduled()`
		 * needs to receive the same arguments as those used when originally
		 * scheduling the event otherwise it will always return false.
		 */
		if ( ! wp_next_scheduled( 'analytics_tracking_send_data_after_core_update', true ) ) {
			/*
			 * Schedule sending of data tracking 6 hours after a WordPress core
			 * update. Pass a `true` parameter for the callback `$force` argument.
			 */
			wp_schedule_single_event( ( time() + ( HOUR_IN_SECONDS * 4 ) ), 'analytics_tracking_send_data_after_core_update', true );
		}
	}

	/**
	 * Sends the tracking data.
	 *
	 * @param bool $force Whether to send the tracking data ignoring the week time treshhold. Default false.
	 */
	public function analytics_tracking_send( $force = false ) {
		if ( ! $this->analytics_tracking_should_send_data( $force ) ) {
			return;
		}

		$args = array(
			'method' => 'POST',
			'body'   => array(
				'product_name' => $this->slug,
				'default'      => $this->analytics_tracking_get_default_data(),
				'server'       => $this->analytics_tracking_get_server_data(),
				'plugins'      => $this->analytics_tracking_get_plugins_data(),
				'themes'       => $this->analytics_tracking_get_themes_data(),
			),
		);

		$response = wp_remote_post( $this->endpoint, $args );

		if ( ! is_wp_error( $response ) ) {
			update_option( $this->slug . '-' . $this->option_name, $this->current_time, 'yes' );
		}
	}

	public function analytics_tracking_get_default_data() {
		$data = array(
			'site_title'    => get_option( 'blogname' ),
			'timestamp'     => (int) date( 'Uv' ),
			'wp_version'    => $this->analytics_tracking_get_wordpress_version(),
			'home_url'      => home_url(),
			'admin_url'     => admin_url(),
			'admin_email'   => get_bloginfo( 'admin_email' ),
			'is_multisite'  => is_multisite(),
			'site_language' => get_bloginfo( 'language' ),
		);

		return json_encode( $data );
	}

	public function analytics_tracking_get_server_data() {
		$data = array();

		// Validate if the server address is a valid IP-address.
		$ipaddress = filter_input( INPUT_SERVER, 'SERVER_ADDR', FILTER_VALIDATE_IP );
		if ( $ipaddress ) {
			$data['ip']       = $ipaddress;
			$data['hostname'] = gethostbyaddr( $ipaddress );
		}

		$data['os']             = php_uname();
		$data['php_version']    = PHP_VERSION;
		$data['curl_version']   = $this->analytics_tracking_get_curl_info();
		$data['php_extensions'] = $this->analytics_tracking_get_php_extensions();

		return json_encode( $data );
	}

	public function analytics_tracking_get_plugins_data() {
		$data = array();

		if ( ! function_exists( 'get_plugin_data' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$plugins = wp_get_active_and_valid_plugins();
		$plugins = array_map( 'get_plugin_data', $plugins );
		$plugins = array_map( array( $this, 'analytics_tracking_format_plugin' ), $plugins );

		foreach ( $plugins as $plugin ) {
			$plugin_key          = sanitize_title( $plugin['name'] );
			$data[ $plugin_key ] = $plugin;
		}

		return json_encode( $data );
	}

	public function analytics_tracking_get_themes_data() {
		$theme = wp_get_theme();

		$data = array(
			'name'         => $theme->get( 'Name' ),
			'url'          => $theme->get( 'ThemeURI' ),
			'version'      => $theme->get( 'Version' ),
			'author'       => array(
				'name' => $theme->get( 'Author' ),
				'url'  => $theme->get( 'AuthorURI' ),
			),
			'parent_theme' => $this->analytics_tracking_get_parent_theme( $theme ),
		);

		return json_encode( $data );
	}

	/**
	 * Returns the WordPress version.
	 *
	 * @return string The version.
	 */
	protected function analytics_tracking_get_wordpress_version() {
		global $wp_version;

		return $wp_version;
	}

	/**
	 * Returns details about the curl version.
	 *
	 * @return array|null The curl info. Or null when curl isn't available..
	 */
	protected function analytics_tracking_get_curl_info() {
		if ( ! function_exists( 'curl_version' ) ) {
			return null;
		}

		$curl = curl_version();

		$ssl_support = true;
        // phpcs:ignore WordPress.WP.AlternativeFunctions.curl_curl_version_ssl -- This only concerns the basic act of getting the curl version.
		if ( ! $curl['features'] && CURL_VERSION_SSL ) {
			$ssl_support = false;
		}

		return array(
			'version'     => $curl['version'],
			'ssl_support' => $ssl_support,
		);
	}

	/**
	 * Returns a list with php extensions.
	 *
	 * @return array Returns the state of the php extensions.
	 */
	protected function analytics_tracking_get_php_extensions() {
		return array(
			'imagick'   => extension_loaded( 'imagick' ),
			'filter'    => extension_loaded( 'filter' ),
			'bcmath'    => extension_loaded( 'bcmath' ),
			'pcre'      => extension_loaded( 'pcre' ),
			'xml'       => extension_loaded( 'xml' ),
			'pdo_mysql' => extension_loaded( 'pdo_mysql' ),
		);
	}

	/**
	 * Formats the plugin array.
	 *
	 * @param array $plugin The plugin details.
	 *
	 * @return array The formatted array.
	 */
	protected function analytics_tracking_format_plugin( array $plugin ) {
		return array(
			'name'    => $plugin['Name'],
			'version' => $plugin['Version'],
		);
	}

	/**
	 * Returns the name of the parent theme.
	 *
	 * @param WP_Theme $theme The theme object.
	 *
	 * @return null|string The name of the parent theme or null.
	 */
	private function analytics_tracking_get_parent_theme( WP_Theme $theme ) {
		if ( is_child_theme() ) {
			return $theme->get( 'Template' );
		}
		return null;
	}

	/**
	 * Determines whether to send the tracking data.
	 *
	 * Returns false if tracking is disabled or the current page is one of the
	 * admin plugins pages. Returns true when there's no tracking data stored or
	 * the data was sent more than two weeks ago. The two weeks interval is set
	 * when instantiating the class.
	 *
	 * @param bool $ignore_time_treshhold Whether to send the tracking data ignoring the two weeks time treshhold. Default false.
	 *
	 * @return bool True when tracking data should be sent.
	 */
	protected function analytics_tracking_should_send_data( $ignore_time_treshhold = false ) {
		global $pagenow;

		// Only send tracking on the main site of a multi-site instance. This returns true on non-multisite installs.
		if ( ! is_main_site() ) {
			return false;
		}

		// Because we don't want to possibly block plugin actions with our routines.
		if ( in_array( $pagenow, array( 'plugins.php', 'plugin-install.php', 'plugin-editor.php' ), true ) ) {
			return false;
		}

		$last_time = get_option( $this->slug . '-' . $this->option_name );

		// When tracking data haven't been sent yet or when sending data is forced.
		if ( ! $last_time || $ignore_time_treshhold ) {
			return true;
		}

		return $this->analytics_tracking_exceeds_treshhold( $this->current_time - $last_time );
	}

	/**
	 * Checks if the given amount of seconds exceeds the set threshold.
	 *
	 * @param int $seconds The amount of seconds to check.
	 *
	 * @return bool True when seconds is bigger than threshold.
	 */
	protected function analytics_tracking_exceeds_treshhold( $seconds ) {
		return ( $seconds > $this->threshold );
	}

	/**
	 * See if we should run tracking at all.
	 *
	 * @return bool True when we can track, false when we can't.
	 */
	private function analytics_tracking_enabled() {
		// Check if we're allowing tracking.
		$tracking = get_option( $this->slug . '-ask-for-usage-optin' );

		if ( $tracking === false ) {
			return false;
		}

		// Save this state.
		if ( $tracking === null ) {
			$tracking = apply_filters( 'analytics_tracking_enable', false );
			update_option( $this->slug . '-ask-for-usage-optin', $tracking );
		}

		if ( $tracking === false ) {
			return false;
		}

		return true;
	}
}
