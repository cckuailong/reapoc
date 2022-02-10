<?php
/**
 * Provide backwards compatibility for older versions of Business Profile.
 *
 * @package   BusinessProfile
 * @copyright Copyright (c) 2016, Theme of the Crop
 * @license   GPL-2.0+
 * @since     1.0.6
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'bpfwpCompatibility', false ) ) :

	/**
	 * Class to handle backwards compatibility issues for Business Profile.
	 *
	 * @since 1.0.6
	 */
	class bpfwpCompatibility {

		/**
		 * Set up hooks.
		 *
		 * @since  1.0.6
		 * @access public
		 * @return void
		 */
		public function __construct() {
			// Preserve this defined constant in case anyone relied on it
			// to check if the plugin was active.
			define( 'BPFWP_TEXTDOMAIN', 'bpfwpdomain' );

			// Load a .mo file for an old textdomain if one exists.
			add_filter( 'load_textdomain_mofile', array( $this, 'load_old_textdomain' ), 10, 2 );

			// Run a filter that was renamed in version 1.1.
			add_filter( 'bpfwp_default_display_settings', array( $this, 'run_contact_card_defaults' ) );

		}

		/**
		 * Load a .mo file for an old textdomain if one exists
		 *
		 * In versions prior to 1.0.6, the textdomain did not match the plugin
		 * slug. This had to be changed to comply with upcoming changes to
		 * how translations are managed in the .org repo. This function
		 * checks to see if an old translation file exists and loads it if
		 * it does, so that people don't lose their translations.
		 *
		 * Old textdomain: bpfwpdomain
		 *
		 * @since  1.0.6
		 * @access public
		 * @param  string $mofile The path to the current mofile.
		 * @param  string $textdomain The current textdomain.
		 * @return string $mofile The modified mofile.
		 */
		public function load_old_textdomain( $mofile, $textdomain ) {
			if ( 'business-profile' !== $textdomain ) {
				return $mofile;
			}

			if ( 0 === strpos( $mofile, WP_LANG_DIR . '/plugins/' ) && ! file_exists( $mofile ) ) {
				$mofile = dirname( $mofile ) . DIRECTORY_SEPARATOR . str_replace( $textdomain, 'bpfwpdomain', basename( $mofile ) );
			}

			return $mofile;
		}

		/**
		 * Run a filter that was renamed in version 1.1
		 *
		 * @since  1.1
		 * @access public
		 * @param  array $defaults The contact card defaults.
		 * @return array $defaults The filtered contact card defaults.
		 */
		public function run_contact_card_defaults( $defaults ) {
			return apply_filters( 'bpwfp_contact_card_defaults', $defaults );
		}
	}
endif;
