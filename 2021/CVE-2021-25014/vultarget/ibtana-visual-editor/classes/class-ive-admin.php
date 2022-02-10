<?php
/**
 * IVE Admin.
 *
 * @package IVE
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'IVE_Admin' ) ) {

	/**
	 * Class IVE_Admin.
	 */
	final class IVE_Admin {

		/**
		 * Calls on initialization
		 *
		 * @since 0.0.1
		 */
		public static function init() {

			if ( ! is_admin() ) {
				return;
			}

      add_action( 'wp_ajax_ive-get-installed-theme', __CLASS__ . '::get_installed_theme' );
			add_action( 'wp_ajax_ive-theme-activate', __CLASS__ . '::theme_activate' );

			add_action( 'wp_ajax_ive-check-plugin-exists', __CLASS__ . '::check_plugin_exists' );
		}

		public static function check_plugin_exists() {

			// Check for nonce security
			if ( ! wp_verify_nonce( $_POST['wpnonce'], 'ive_whizzie_nonce' ) ) {
				exit;
			}

			$plugin_text_domain	= sanitize_text_field( $_POST['plugin_text_domain'] );
			$main_plugin_file		= sanitize_text_field( $_POST['main_plugin_file'] );
			$plugin_path				= $plugin_text_domain . '/' . $main_plugin_file;

			$get_plugins					= get_plugins();
			$is_plugin_installed	= false;
			$activation_status 		= false;
			if ( isset( $get_plugins[$plugin_path] ) ) {
				$is_plugin_installed = true;

				$activation_status = is_plugin_active( $plugin_path );
			}
			wp_send_json_success(
        array(
          'install_status'  =>	$is_plugin_installed,
					'active_status'		=>	$activation_status,
					'plugin_path'			=>	$plugin_path,
					'plugin_slug'			=>	$plugin_text_domain
        )
      );
		}


    public static function get_installed_theme() {

			// Check for nonce security
			if ( ! wp_verify_nonce( $_POST['wpnonce'], 'ive_whizzie_nonce' ) ) {
				exit;
			}

      $theme = wp_get_theme( sanitize_text_field( $_POST['slug'] ) );
      $does_exist = $theme->exists();
      wp_send_json_success(
        array(
          'success'         => true,
          'install_status'  => $does_exist
        )
      );
    }

		/**
		 * Required Plugin Activate
		 *
		 * @since 1.8.2
		 */
		public static function theme_activate() {

			// Check for nonce security
			if ( ! wp_verify_nonce( $_POST['wpnonce'], 'ive_whizzie_nonce' ) ) {
				exit;
			}

			$theme_slug = ( isset( $_POST['slug'] ) ) ? sanitize_text_field( $_POST['slug'] ) : '';

			if ( ! current_user_can( 'switch_themes' ) || ! $theme_slug ) {
				wp_send_json_error(
					array(
						'success' => false,
						'message' => __( 'No Theme specified', 'ibtana-visual-editor' ),
					)
				);
			}

			$activate = switch_theme( $theme_slug );

			if ( is_wp_error( $activate ) ) {
				wp_send_json_error(
					array(
						'success' => false,
						'message' => $activate->get_error_message(),
					)
				);
			}

			wp_send_json_success(
				array(
					'success' => true,
					'message' => __( 'Theme Successfully Activated', 'ibtana-visual-editor' ),
				)
			);
		}

	}

	IVE_Admin::init();
}
