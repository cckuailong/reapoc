<?php

/**
 * Class DLM_Product
 * The base class for all Download Monitor Extensions
 */
class DLM_Product {

	/**
	 * The store URL
	 */
	const STORE_URL = 'https://www.download-monitor.com/?wc-api=';

	/**
	 * Activation endpoint
	 */
	const ENDPOINT_ACTIVATION = 'wp_plugin_licencing_activation_api';

	/**
	 * Update endpoint
	 */
	const ENDPOINT_UPDATE = 'wp_plugin_licencing_update_api';

	/**
	 * @var String
	 */
	private $product_id;

	/**
	 * @var string
	 */
	private $product_name = "";

	/**
	 * @var String
	 */
	private $plugin_name;

	/**
	 * @var string
	 */
	private $version = false;

	/**
	 * @var DLM_Product_License
	 */
	private $license = null;

	/**
	 * Constructor
	 *
	 * @param String $product_id
	 * @param string|bool $version
	 * @param string $product_name
	 */
	function __construct( $product_id, $version = false, $product_name = "" ) {
		$this->product_id = $product_id;

		// The plugin file name
		$this->plugin_name = $this->product_id . '/' . $this->product_id . '.php';

		// set product name
		$this->product_name = $product_name;

		// BC
		if ( empty( $this->product_name ) ) {
			$this->product_name = $this->product_id;
		}

		// Set plugin version
		$this->version = $version;
	}

	/**
	 * @return String
	 */
	public function get_product_id() {
		return $this->product_id;
	}

	/**
	 * @param String $product_id
	 */
	public function set_product_id( $product_id ) {
		$this->product_id = $product_id;
	}

	/**
	 * @return string
	 */
	public function get_product_name() {
		return $this->product_name;
	}

	/**
	 * @param string $product_name
	 */
	public function set_product_name( $product_name ) {
		$this->product_name = $product_name;
	}

	/**
	 * @return String
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * @param String $plugin_name
	 */
	public function set_plugin_name( $plugin_name ) {
		$this->plugin_name = $plugin_name;
	}

	/**
	 * Get the license, license will be automatically loaded if not set yet.
	 *
	 * @return DLM_Product_License
	 */
	public function get_license() {
		if ( null === $this->license ) {
			$this->license = new DLM_Product_License( $this->product_id );
		}

		return $this->license;
	}

	/**
	 * Set the license
	 *
	 * @param DLM_Product_License $license
	 */
	public function set_license( $license ) {
		$this->license = $license;
		$this->license->store();
	}

	/**
	 * Attempt to activate a plugin licence
	 *
	 * @return String
	 */
	public function activate() {

		// Get License
		$license = $this->get_license();

		try {

			// Check License key
			if ( '' === $license->get_key() ) {
				throw new Exception( 'Please enter your license key.' );
			}

			// Check license email
			if ( '' === $license->get_email() ) {
				throw new Exception( 'Please enter the email address associated with your license.' );
			}

			// Do activate request
			$request = wp_remote_get( self::STORE_URL . self::ENDPOINT_ACTIVATION . '&' . http_build_query( array(
					'email'          => $license->get_email(),
					'licence_key'    => $license->get_key(),
					'api_product_id' => $this->product_id,
					'request'        => 'activate',
					'instance'       => site_url()
				), '', '&' ) );

			// Check request
			if ( is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) != 200 ) {
				throw new Exception( 'Connection failed to the License Key API server. Try again later.' );
			}

			// Get activation result
			$activate_results = json_decode( wp_remote_retrieve_body( $request ), true );

			// Check if response is correct
			if ( ! empty( $activate_results['activated'] ) ) {

				// Set local activation status to true
				$license->set_status( 'active' );
				$this->set_license( $license );

				// Return Message
				return array(
					'result'  => 'success',
					'message' => __( 'License successfully activated.', 'download-monitor' )
				);

			} elseif ( $activate_results === false ) {
				throw new Exception( 'Connection failed to the License Key API server. Try again later.' );
			} elseif ( isset( $activate_results['error_code'] ) ) {
				throw new Exception( $activate_results['error'] );
			}


		} catch ( Exception $e ) {

			// Set local activation status to false
			$license->set_status( 'inactivate' );
			$this->set_license( $license );

			// Return error message
			return array( 'result' => 'failed', 'message' => $e->getMessage() );
		}
	}

	/**
	 * Attempt to deactivate a licence
	 */
	public function deactivate() {

		// Get License
		$license = $this->get_license();

		try {

			// Check License key
			if ( '' === $license->get_key() ) {
				throw new Exception( "Can't deactivate license without a license key." );
			}

			// The Request
			$request = wp_remote_get( self::STORE_URL . self::ENDPOINT_ACTIVATION . '&' . http_build_query( array(
					'api_product_id' => $this->product_id,
					'licence_key'    => $license->get_key(),
					'request'        => 'deactivate',
					'instance'       => site_url(),
				), '', '&' ) );

			// Check request
			if ( is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) != 200 ) {
				throw new Exception( 'Connection failed to the License Key API server. Try again later.' );
			}

			// Get result
			$result = json_decode( wp_remote_retrieve_body( $request ), true );

			/** @todo check result * */

			// Set new license status
			$license->set_status( 'inactive' );
			$this->set_license( $license );

			return array( 'result' => 'success' );

		} catch ( Exception $e ) {

			// Return error message
			return array( 'result' => 'failed', 'message' => $e->getMessage() );
		}

	}

	/**
	 * Check for plugin updates
	 *
	 * @var $check_for_updates_data
	 *
	 * @return array
	 */
	public function check_for_updates( $check_for_updates_data ) {

		// Get license
		$license = $this->get_license();

		// Check if checked is set
		if ( empty( $check_for_updates_data->checked ) ) {
			return $check_for_updates_data;
		}

		// Only check for data if license is activated
		if ( true !== $license->is_active() ) {
			return $check_for_updates_data;
		}

		// Get current version
		$current_ver = ( $this->version !== false ) ? $this->version : $check_for_updates_data->checked[ $this->plugin_name ];

		// The request
		$request = wp_remote_get( self::STORE_URL . self::ENDPOINT_UPDATE . '&' . http_build_query( array(
				'request'        => 'pluginupdatecheck',
				'plugin_name'    => $this->plugin_name,
				'version'        => $current_ver,
				'api_product_id' => $this->product_id,
				'licence_key'    => $license->get_key(),
				'email'          => $license->get_email(),
				'instance'       => site_url()
			), '', '&' ) );

		// Check if request is correct
		if ( is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) != 200 ) {
			return $check_for_updates_data;
		}

		// Check for a plugin update
		$response = maybe_unserialize( wp_remote_retrieve_body( $request ) );

		// $response must be an object
		if ( ! is_object( $response ) ) {
			return $check_for_updates_data;
		}

		if ( isset( $response->errors ) ) {
			$this->handle_errors( $response->errors );
			return $check_for_updates_data;
		}

		// Set version variables
		if ( is_object( $response ) && false !== $response && isset( $response->new_version ) ) {

			// Check if there's a new version
			if ( version_compare( $response->new_version, $current_ver, '>' ) ) {
				$check_for_updates_data->response[ $this->plugin_name ] = $response;
			}

		}

		return $check_for_updates_data;
	}

	/**
	 * Plugins API
	 *
	 * @param bool $false
	 * @param string $action
	 * @param array $args
	 *
	 * @return mixed
	 */
	public function plugins_api( $false, $action, $args ) {


		// License
		$license = $this->get_license();

		// Only take over plugin info screen if license is activated
		if ( true !== $license->is_active() ) {
			return $false;
		}

		// Check if this request if for this product
		if ( ! isset( $args->slug ) || ( $args->slug !== $this->product_id ) ) {
			return $false;
		}

		// Get the current version
		$plugin_info = get_site_transient( 'update_plugins' );
		$current_ver = ( $this->version !== false ) ? $this->version : ( isset( $plugin_info->checked[ $this->plugin_name ] ) ? $plugin_info->checked[ $this->plugin_name ] : '' );

		$request = wp_remote_get( self::STORE_URL . self::ENDPOINT_UPDATE . '&' . http_build_query( array(
				'request'        => 'plugininformation',
				'plugin_name'    => $this->plugin_name,
				'version'        => $current_ver,
				'api_product_id' => $this->product_id,
				'licence_key'    => $license->get_key(),
				'email'          => $license->get_email(),
				'instance'       => site_url()
			), '', '&' ) );

		// Check if request is correct
		if ( is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) != 200 ) {
			return $false;
		}

		// Check for a plugin update
		$response = maybe_unserialize( wp_remote_retrieve_body( $request ) );

		// $response must be an object
		if ( ! is_object( $response ) ) {
			return $false;
		}

		// Handle errors
		if ( isset( $response->errors ) ) {
			$this->handle_errors( $response->errors );
			return $false;
		}

		// If everything is okay return the $response
		if ( isset( $response ) && is_object( $response ) && false !== $response ) {
			return $response;
		}
	}

	/**
	 * Handle errors from the API
	 *
	 * @param  array $errors
	 */

	public function handle_errors( $errors ) {

		// loop through errors
		foreach( $errors as $error_key => $error ) {

			// add error to WP
			DLM_Product_Manager::get()->error_handler()->add( $error );

			// check if error is no activation
			if( 'no_activation' == $error_key ) {
				// remove local activation if there's no license on API side
				$this->get_license()->set_status( 'inactive' );
				$this->get_license()->store();
			}
		}

	}


	/**
	 * Display notice after extension plugin row
	 *
	 * @param string $file
	 * @param array $plugin_data
	 */
	public function after_plugin_row( $file, $plugin_data ) {

		// Don't show if license is activated
		if ( $this->get_license()->is_active() ) {
			return;
		}

		// Output row with message telling people to activate their license
		$id = sanitize_title( $plugin_data['Name'] );
		echo '<tr class="plugin-update-tr active">';
		echo '<td colspan="3" class="plugin-update colspanchange">';
		echo '<div style="padding: 6px 12px; margin: 0 10px 8px 31px; background: lightYellow;">';
		printf( __( '<a href="%s">Register your copy</a> of the <strong>%s</strong> extension to receive access to automatic upgrades and support. Need a license key? <a href="%s" target="_blank">Purchase one now</a>.', 'download-monitor' ), admin_url( 'edit.php?post_type=dlm_download&page=dlm-extensions#installed-extensions' ), $this->get_product_name(), $this->get_tracking_url( 'plugins_page' ) );
		echo '</div></td></tr>';

		// Disable bottom border on parent row
		echo '<style scoped="scoped">';
		echo sprintf( "#%s td, #%s th { box-shadow: none !important; }", $id, $id );
		echo '</style>';
	}

	/**
	 * Gets a Google Analytics Campaign URL for this product
	 *
	 * @param string $link_identifier
	 *
	 * @return string The full URL
	 */
	public function get_tracking_url( $link_identifier = '' ) {
		$tracking_vars = array(
			'utm_campaign' => $this->get_product_name() . '_licensing',
			'utm_medium'   => 'link',
			'utm_source'   => $this->get_product_name(),
			'utm_content'  => $link_identifier
		);

		// url encode tracking vars
		$tracking_vars = urlencode_deep( $tracking_vars );
		$query_string  = build_query( $tracking_vars );

		return 'https://www.download-monitor.com/extensions/' . str_ireplace( 'dlm-', '', $this->get_product_id() ) . '?' . $query_string;
	}

}