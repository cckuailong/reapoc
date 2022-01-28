<?php
/**
 * Class Wpcf7r_Updates
 * A class used for updating the plugin using th QS API
 */

defined( 'ABSPATH' ) || exit;

class Wpcf7r_Updates {
	// Plugin current version
	private $current_version;

	// Plugin remote update path
	private $update_url;

	// Plugin Slug (plugin_directory/plugin_file.php)
	private $plugin_slug;

	// Plugin name (plugin_file)
	private $slug;

	/**
	 * Initialize a new instance of the WordPress Auto-Update class
	 *
	 * @param $current_version
	 * @param $plugin_slug
	 */
	public function __construct( $current_version, $plugin_slug ) {
		// Set the class public variables
		$this->current_version = $current_version;
		$this->update_url      = add_query_arg( 'update', '', WPCF7_PRO_REDIRECT_PLUGIN_UPDATES );

		// Set the Plugin Slug
		$this->plugin_slug = $plugin_slug;
		list ($t1, $t2)    = explode( '/', $plugin_slug );
		$this->slug        = str_replace( '.php', '', $t2 );

		$this->activation_id = WPCF7r_Utils::get_activation_id();
		$this->serial        = WPCF7r_Utils::get_serial_key();
		$this->domain        = $_SERVER['HTTP_HOST'];
		// define the alternative API for updating checking
		add_filter( 'pre_set_site_transient_update_plugins', array( &$this, 'check_update' ) );

		// Define the alternative response for information checking
		add_filter( 'plugins_api', array( &$this, 'check_info' ), 10, 3 );
	}

	/**
	 * Add our self-hosted autoupdate plugin to the filter transient
	 *
	 * @param $transient
	 * @return 'self-hosted' autoupdate plugin to the filter transient
	 */
	public function check_update( $transient ) {

		if ( empty( $transient->checked ) ) {
			return $transient;
		}

		// Get the remote version
		$remote_version = $this->get_remote( 'version' );
		
		if ( $remote_version && ! is_wp_error( $remote_version ) ) {
			// If a newer version is available, add the update
			if ( version_compare( $this->current_version, $remote_version->new_version, '<' ) ) {
				$obj                                       = new stdClass();
				$obj->slug                                 = $this->slug;
				$obj->new_version                          = $remote_version->new_version;
				$obj->url                                  = $remote_version->url;
				$obj->requires                             = $remote_version->requires;
				$obj->plugin                               = $this->plugin_slug;
				$obj->package                              = $remote_version->package;
				$obj->tested                               = $remote_version->tested;
				$transient->response[ $this->plugin_slug ] = $obj;
			}
		}

		return $transient;
	}

	/**
	 * Add our self-hosted description to the filter
	 *
	 * @param $false
	 * @param $action
	 * @param $arg
	 * @return bool|object
	 */
	public function check_info( $false, $action, $arg ) {
		if ( ( 'query_plugins' === $action || 'plugin_information' === $action ) &&
			isset( $arg->slug ) && $arg->slug === $this->slug ) {

			$information = $this->get_remote( 'info' );

			$information->sections = (array) $information->sections;
			$information->banners  = (array) $information->banners;

			$array_pattern = array(
				'/^([\*\s])*(\d\d\.\d\d\.\d\d\d\d[^\n]*)/m',
				'/^\n+|^[\t\s]*\n+/m',
				'/\n/',
			);
			$array_replace = array(
				'<h4>$2</h4>',
				'</div><div>',
				'</div><div>',
			);

			$information->sections['changelog'] = '<div>' . preg_replace( $array_pattern, $array_replace, $information->sections['changelog'] ) . '</div>';

			return $information;
		}

		return $false;
	}

	/**
	 * Return the remote version
	 *
	 * @param $action
	 * @return string $remote_version
	 */
	public function get_remote( $action = '' ) {

		$params = array(
			'body' => array(
				'action'        => $action,
				'activation_id' => $this->activation_id,
				'serial'        => $this->serial,
				'plugin'        => $this->plugin_slug,
				'domain'        => $this->domain,
			),
		);

		// Make the POST request
		$response = wp_remote_post( $this->update_url, $params );

		// Check if response is valid
		if ( ! is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) === 200 ) {
			$response = json_decode( wp_remote_retrieve_body( $response ) );

		}

		return $response;
	}
}
