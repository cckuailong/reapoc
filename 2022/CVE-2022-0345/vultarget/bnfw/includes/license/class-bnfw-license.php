<?php
/**
 * License handler for BNFW
 *
 * @since 1.4
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly

/**
 * BNFW_License Class
 */
class BNFW_License {
	private $file;
	private $license;
	private $item_name;
	private $item_shortname;
	private $version;
	private $author;
	private $api_url = 'https://betternotificationsforwp.com/';

	/**
	 * Class constructor
	 *
	 * @param string $_file
	 * @param string $_item_name
	 * @param string $_version
	 * @param string $_author
	 * @param string $_optname
	 * @param string $_api_url
	 */
	function __construct( $_file, $_item_name, $_version, $_author, $_optname = null, $_api_url = null ) {
		$bnfw_options = get_option( 'bnfw_licenses' );

		$this->file           = $_file;
		$this->item_name      = $_item_name;
		$this->item_shortname = 'bnfw_' . preg_replace( '/[^a-zA-Z0-9_\s]/', '', str_replace( ' ', '_', strtolower( $this->item_name ) ) );
		$this->version        = $_version;
		$this->license        = isset( $bnfw_options[ $this->item_shortname . '_license_key' ] ) ? trim( $bnfw_options[ $this->item_shortname . '_license_key' ] ) : '';
		$this->author         = $_author;
		$this->api_url        = is_null( $_api_url ) ? $this->api_url : $_api_url;

		// Setup hooks
		$this->hooks();
		$this->auto_updater();
	}

	/**
	 * Setup hooks
	 *
	 * @access  private
	 * @return  void
	 */
	private function hooks() {
		// Register settings
		add_filter( 'bnfw_settings_licenses', array( $this, 'settings' ), 1 );

		// Activate license key on settings save
		add_action( 'admin_init', array( $this, 'activate_license' ) );

		// Deactivate license key
		add_action( 'admin_init', array( $this, 'deactivate_license' ) );

		add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'update_plugins_transient_unserialize_icons' ), 99 );
	}

	/**
	 * Filter the transient data for our plugin's icons.
	 * Since icons are passed back as serialized arrays, we need to unserialize them.
	 * This has to be run from within your plugin.
	 *
	 * Based on code from https://renventura.com/adding-update-icons-for-commercial-plugins/
	 *
	 * @param  (object) $transient - Full transient data
	 *
	 * @return mixed (object) $transient
	 */
	public function update_plugins_transient_unserialize_icons( $transient ) {

		if ( is_object( $transient ) && isset( $transient->response ) && is_array( $transient->response ) ) {

			$basename = plugin_basename( __FILE__ );

			// Received a response for our plugin
			$plugin = isset( $transient->response[ $basename ] ) ? $transient->response[ $basename ] : new stdClass;

			// Are there any icons set for the plugin?
			if ( isset( $plugin->icons ) ) {
				$icons = is_string( $plugin->icons ) ? unserialize( $plugin->icons ) : $plugin->icons;
				$transient->response[ $basename ]->icons = $icons;
			}
		}

		return $transient;
	}

	/**
	 * Auto updater
	 *
	 * @access  private
	 * @return  void
	 */
	private function auto_updater() {
		// Setup the updater
		$edd_updater = new EDD_SL_Plugin_Updater(
			$this->api_url,
			$this->file,
			array(
				'version'   => $this->version,
				'license'   => $this->license,
				'item_name' => $this->item_name,
				'author'    => $this->author,
			)
		);
	}

	/**
	 * Add license field to settings
	 *
	 * @access  public
	 *
	 * @param array $settings
	 *
	 * @return  array
	 */
	public function settings( $settings ) {
		if ( 'Multisite Add-on' === $this->item_name && ! is_multisite() ) {
			return $settings;
		}

		if ( 'Multisite Add-on' === $this->item_name && is_multisite() ) {
			if ( ! is_main_site() ) {
				return $settings;
			}
		}

		$bnfw_license_settings = array(
			array(
				'id'      => $this->item_shortname . '_license_key',
				'name'    => sprintf( esc_html__( '%1$s License Key', 'bnfw' ), $this->item_name ),
				'desc'    => '',
				'type'    => 'license_key',
				'options' => array( 'is_valid_license_option' => $this->item_shortname . '_license_active' ),
				'size'    => 'regular',
			),
		);

		return array_merge( $settings, $bnfw_license_settings );
	}

	/**
	 * Activate the license key
	 *
	 * @access  public
	 * @return  void
	 */
	public function activate_license() {
		if ( ! isset( $_POST['bnfw_licenses'] ) ) {
			return;
		}

		if ( ! isset( $_POST['bnfw_licenses'][ $this->item_shortname . '_license_key' ] ) ) {
			return;
		}

		if ( 'valid' == get_option( $this->item_shortname . '_license_active' ) ) {
			return;
		}

		$license = sanitize_text_field( $_POST['bnfw_licenses'][ $this->item_shortname . '_license_key' ] );

		// Data to send to the API
		$api_params = array(
			'edd_action' => 'activate_license',
			'license'    => $license,
			'item_name'  => urlencode( $this->item_name ),
		);

		if ( 'Multisite Add-on' === $this->item_name && is_multisite() ) {
			$api_params['url'] = get_home_url( get_main_site_id() );
		}

		// Call the API
		$response = wp_remote_get(
			esc_url_raw( add_query_arg( $api_params, $this->api_url ) ),
			array(
				'timeout'   => 15,
				'body'      => $api_params,
				'sslverify' => false,
			)
		);

		// Make sure there are no errors
		if ( is_wp_error( $response ) ) {
			return;
		}

		// Decode license data
		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		update_option( $this->item_shortname . '_license_active', $license_data->license );
	}

	/**
	 * Deactivate the license key
	 *
	 * @access  public
	 * @return  void
	 */
	public function deactivate_license() {
		if ( ! isset( $_POST['bnfw_licenses'] ) ) {
			return;
		}

		if ( ! isset( $_POST['bnfw_licenses'][ $this->item_shortname . '_license_key' ] ) ) {
			return;
		}

		// Run on deactivate button press
		if ( isset( $_POST[ $this->item_shortname . '_license_key_deactivate' ] ) ) {

			// Data to send to the API
			$api_params = array(
				'edd_action' => 'deactivate_license',
				'license'    => $this->license,
				'item_name'  => urlencode( $this->item_name ),
			);

			if ( 'Multisite Add-on' === $this->item_name && is_multisite() ) {
				$api_params['url'] = get_home_url( get_main_site_id() );
			}

			// Call the API
			$response = wp_remote_get(
				esc_url_raw( add_query_arg( $api_params, $this->api_url ) ),
				array(
					'timeout'   => 15,
					'sslverify' => false,
				)
			);

			// Make sure there are no errors
			if ( is_wp_error( $response ) ) {
				return;
			}

			// Decode the license data
			$license_data = json_decode( wp_remote_retrieve_body( $response ) );

			if ( 'deactivated' == $license_data->license ) {
				delete_option( $this->item_shortname . '_license_active' );
			}
		}
	}
}
