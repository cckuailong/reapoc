<?php
/**
 * Plugin Name:			Futurio demo import
 * Plugin URI:			https://futuriowp.com/
 * Description:			Demo Import
 * Version:				  1.0
 * Author:				  FuturioWP
 * Author URI:			https://futuriowp.com/
 *
 * @package Futurio_Extra
 * @category Core
 * @author FuturioWP
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Returns the main instance of Futurio_Extra to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return object Futurio_Extra
 */
function Futurio_Extra() {
	return Futurio_Extra::instance();
} // End Futurio_Extra()

Futurio_Extra();

/**
 * Main Futurio_Extra Class
 *
 * @class Futurio_Extra
 * @version	1.0.0
 * @since 1.0.0
 * @package	Futurio_Extra
 */
final class Futurio_Extra {
	/**
	 * Futurio_Extra The single instance of Futurio_Extra.
	 * @var 	object
	 * @access  private
	 * @since 	1.0.0
	 */
	private static $_instance = null;

	/**
	 * The token.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $token;

	/**
	 * The version number.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $version;

	// Admin - Start
	/**
	 * The admin object.
	 * @var     object
	 * @access  public
	 * @since   1.0.0
	 */
	public $admin;

	/**
	 * Constructor function.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function __construct() {
		$this->token 			= 'futurio-extra';
		$this->plugin_url 		= plugin_dir_url( __FILE__ );
		$this->plugin_path 		= plugin_dir_path( __FILE__ );
		$this->version 			= '1.4.29';

		define( 'FE_URL', $this->plugin_url );
		define( 'FE_PATH', $this->plugin_path );
		define( 'FE_VERSION', $this->version );	


		register_activation_hook( __FILE__, array( $this, 'install' ) );

		// Setup all the things
		add_action( 'init', array( $this, 'setup' ) );

	}

	/**
	 * Main Futurio_Extra Instance
	 *
	 * Ensures only one instance of Futurio_Extra is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see Futurio_Extra()
	 * @return Main Futurio_Extra instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) )
			self::$_instance = new self();
		return self::$_instance;
	} // End instance()

	/**
	 * Cloning is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), '1.0.0' );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), '1.0.0' );
	}

	/**
	 * Installation.
	 * Runs on activation.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function install() {
	
	}



	/**
	 * Setup all the things.
	 * @return void
	 */
	public function setup() {
		$theme = wp_get_theme();

		if ( 'Futurio' == $theme->name || 'futurio' == $theme->template ) {
		
				require_once( FE_PATH .'/demos.php' );

		}
	}



} // End Class