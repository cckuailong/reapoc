<?php

namespace MEC;

use MEC\Attendees\AttendeesTable;

/**
 * Core Class in Plugin
 */
final class Base {

	/**
	 * Plugin Version
	 *
	 * @var string
	 */
	public static $version = '1.0.0';

	/**
	 * Session instance
	 *
	 * @var bool
	 */
	protected static $instance;

	/**
	 * MEC Constructor
	 */
	public function __construct() {

		$this->define();
		$this->includes();
		$this->init_hooks();
		$this->admin();
		$this->enqueue_scripts();
	}

	/**
	 * MEC Instance
	 *
	 * @return self()
	 */
	public static function instance() {

		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Set Constants
	 *
	 * @return void
	 */
	public function define() {

		define( 'MEC_CORE_PD', plugin_dir_path( MEC_CORE_FILE ) );
		define( 'MEC_CORE_PDI', plugin_dir_path( MEC_CORE_FILE ) . 'src/' );
		define( 'MEC_CORE_PU_JS', plugins_url( 'assets/js/', MEC_CORE_FILE ) );
		define( 'MEC_CORE_PU_CSS', plugins_url( 'assets/css/', MEC_CORE_FILE ) );
		define( 'MEC_CORE_PU_IMG', plugins_url( 'assets/img/', MEC_CORE_FILE ) );
		define( 'MEC_CORE_PU_FONTS', plugins_url( 'assets/fonts/', MEC_CORE_FILE ) );
		define( 'MEC_CORE_TEMPLATES', plugin_dir_path( MEC_CORE_FILE ) . 'templates/' );
	}

	/**
	 * Include Files
	 *
	 * @return void
	 */
	public function includes() {

	}


	/**
	 * Include Files If is Admin
	 *
	 * @return void
	 */
	public function admin() {

		if ( !is_admin() ) {
			return;
		}
	}


	/**
	 * Register actions enqueue scripts
	 *
	 * @return void
	 */
	public function enqueue_scripts() {


	}

	/**
	 * Add Hooks - Actions and Filters
	 *
	 * @return void
	 */
	public function init_hooks() {

		add_action( 'init', [ $this, 'init' ] );

		register_activation_hook( MEC_CORE_FILE, __CLASS__ . '::register_activation' );
		$db_version = get_option('mec_core_db','1.0.0');
		if(version_compare($db_version, MEC_VERSION, '<')){

			static::register_activation();
		}
	}

	/**
	 * Active Plugin
	 *
	 * @return void
	 */
	public static function register_activation() {

		AttendeesTable::create_table();

		update_option('mec_core_db',MEC_VERSION);
	}


	/**
	 * Init MEC after WordPress
	 *
	 * @return void
	 */
	public function init() {

	}

	public static function get_main(){

		global $MEC_Main;
		if(is_null($MEC_Main)){

			$MEC_Main = new \MEC_main();
		}

		return $MEC_Main;
	}
}