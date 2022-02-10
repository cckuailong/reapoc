<?php
defined( 'ABSPATH' ) or die( "you do not have acces to this page!" );

if ( ! class_exists( "cmplz_export_settings" ) ) {
	class cmplz_export_settings {
		private static $_this;

		function __construct() {
			if ( isset( self::$_this ) ) {
				wp_die( sprintf( '%s is a singleton class and you cannot create a second instance.',
					get_class( $this ) ) );
			}

			self::$_this = $this;

			add_action( 'admin_init', array( $this, 'process_export_action' ),
				10, 1 );
		}

		static function this() {
			return self::$_this;
		}

		public function process_export_action() {
			if ( ! is_user_logged_in() ) {
				return;
			}

			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}

			if ( isset( $_GET['action'] )
			     && $_GET['action'] === 'cmplz_export_settings'
			) {
				$settings = get_option( 'complianz_options_settings' );
				$wizard   = get_option( 'complianz_options_wizard' );
				unset( $wizard['used_cookies'] );

				$json = json_encode( array(
					'settings' => $settings,
					'wizard'   => $wizard,
					'banners'  => cmplz_get_cookiebanners(),
					'errors'  => cmplz_get_console_errors(),
					'jquery'  => get_option('cmplz_detected_missing_jquery') ? 'no-jquery' : 'found-jquery',
				) );

				$json = $json . '#--COMPLIANZ--#'
				        . strlen( utf8_decode( $json ) );

				header( 'Content-disposition: attachment; filename=complianz-export.json' );
				header( 'Content-type: application/json' );
				echo $json;
				die();
			}
		}
	}
}
