<?php
/**
 * Settings class
 * Server Scheduler settings
 *
 * @package advanced-cron-manager
 */

namespace underDEV\AdvancedCronManager\Server;

use underDEV\Utils;

/**
 * Settings class.
 */
class Settings {

	/**
	 * View class
	 *
	 * @var object
	 */
	public $view;

	/**
	 * Ajax class
	 *
	 * @var object
	 */
	private $ajax;

	/**
	 * Constructor
	 *
	 * @param Utils\View $view View class.
	 * @param Utils\Ajax $ajax Ajax class.
	 */
	public function __construct( Utils\View $view, Utils\Ajax $ajax ) {
		$this->view = $view;
		$this->ajax = $ajax;

		$this->option_name = 'acm_server_settings';

		$this->default = array(
			'server_enable' => 0,
		);
	}

	/**
	 * Loads Server Scheduler settings part
	 *
	 * @return void
	 */
	public function load_settings_part() {
		$this->view->set_var( 'settings', $this->get_settings() );
		$this->view->get_view( 'server/settings' );
	}

	/**
	 * Gets Settings
	 * Supports lazy loading
	 *
	 * @param  boolean $force if refresh stored events.
	 * @return array          saved settings
	 */
	public function get_settings( $force = false ) {

		if ( empty( $this->settings ) || $force ) {
			$this->settings = get_option( $this->option_name, $this->default );
		}

		return $this->settings;

	}

	/**
	 * Saves settings
	 * Called by AJAX
	 *
	 * @return void
	 */
	public function save_settings() {

		$this->ajax->verify_nonce( 'acm/server/settings/save' );

		$errors = array();

		$form_options = array_map( function( $val ) {
			return 0;
		}, $this->default );

		// phpcs:ignore
		$form_data = wp_parse_args( $_REQUEST['data'], $form_options );

		update_option( $this->option_name, $form_data );

		$this->ajax->response( __( 'Settings has been saved', 'advanced-cron-manager' ), $errors );

	}

}
