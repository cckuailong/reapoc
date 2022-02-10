<?php
if ( ! defined( 'myCRED_VERSION' ) ) exit;

/**
 * myCRED_Setup class
 * Used when the plugin has been activated for the first time. Handles the setup
 * wizard along with temporary admin menus.
 * @since 0.1
 * @version 1.2
 */
if ( ! class_exists( 'myCRED_Setup' ) ) :
	class myCRED_Setup {

		public $status = false;
		public $core;

		/**
		 * Construct
		 */
		public function __construct() {

			$this->core = mycred();

		}

		/**
		 * Load Class
		 * @since 1.7
		 * @version 1.0
		 */
		public function load() {
			
			$this->setup_default_point_type();

			// Add general settings
			add_option( 'mycred_version', myCRED_VERSION );
			add_option( 'mycred_key',     wp_generate_password( 12, true, true ) );

			require_once myCRED_MODULES_DIR . 'mycred-module-addons.php';
			$addons_module = new myCRED_Addons_Module();
			$installed_addons = $addons_module->get();

			// Add add-ons settings
			add_option( 'mycred_pref_addons', array(
				'installed' => $installed_addons,
				'active'    => array_keys( $installed_addons )
			) );

			// Add hooks settings
			$option_id = apply_filters( 'mycred_option_id', 'mycred_pref_hooks' );
			add_option( $option_id, array(
				'installed'  => array(),
				'active'     => array(),
				'hook_prefs' => array()
			) );

		}

		/**
		 * Process Setup Steps
		 * @since 0.1
		 * @version 1.3
		 */
		public function setup_default_point_type() {

			$first_type = $this->core->defaults();

			// Save our first point type
			mycred_update_option( 'mycred_pref_core', $first_type );

			mycred_upload_default_point_image();

			// Install database
			if ( ! function_exists( 'mycred_install_log' ) )
				require_once myCRED_INCLUDES_DIR . 'mycred-functions.php';

			mycred_install_log( $first_type['format']['decimals'], true );

			mycred_add_option( 'mycred_setup_completed', time() );

		}
	}
endif;
