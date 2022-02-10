<?php
/**
 * Wizard installation plugin helper
 *
 * @since             1.0.0
 * @package           TInvWishlist
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Wizard installation plugin helper
 */
class TInvWL_WizardSetup {

	/**
	 * Plugin name
	 *
	 * @var string
	 */
	public $_name;

	/**
	 * Plugin version
	 *
	 * @var string
	 */
	public $_version;

	/**
	 * Constructor
	 *
	 * @param string $plugin_name Plugin name.
	 * @param string $version Plugin version.
	 */
	function __construct( $plugin_name, $version ) {
		$this->_name    = $plugin_name;
		$this->_version = $version;
		add_action( 'init', array( $this, 'load' ) );
		add_action( 'admin_init', array( $this, 'redirect' ) );
	}

	/**
	 * Setup trigger for show wizard installation
	 */
	public static function setup() {
		set_transient( '_tinvwl_activation_redirect', 1, 30 );
	}

	/**
	 * Load wizard
	 */
	public function load() {
		$page = filter_input( INPUT_GET, 'page' );
		if ( ! empty( $page ) ) {
			switch ( $page ) {
				case 'tinvwl-wizard' :
					new TInvWL_Wizard( $this->_name, $this->_version );
			}
		}
	}

	/**
	 * Apply redirect to wizard
	 *
	 * @return void
	 */
	public function redirect() {
		if ( ! get_transient( '_tinvwl_activation_redirect' ) ) {
			return;
		}
		delete_transient( '_tinvwl_activation_redirect' );

		$page     = filter_input( INPUT_GET, 'page' );
		$activate = filter_input( INPUT_GET, 'activate-multi' );
		if ( in_array( $page, array( 'tinvwl-wizard' ) ) || is_network_admin() || ! is_null( $activate ) || apply_filters( 'tinvwl_prevent_automatic_wizard_redirect', false ) ) { // @codingStandardsIgnoreLine WordPress.PHP.StrictInArray.MissingTrueStrict
			return;
		}

		wp_safe_redirect( admin_url( 'index.php?page=tinvwl-wizard' ) );
		exit;
	}
}
