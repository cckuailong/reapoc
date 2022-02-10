<?php
/**
 * Export/Import plugin settings class
 *
 * @since             1.17.0
 * @package           TInvWishlist
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Export/Import plugin settings class
 */
class TInvWL_Export {

	/**
	 * Plugin name.
	 *
	 * @var string
	 */
	public $_name;

	/**
	 * Plugin version.
	 *
	 * @var string
	 */
	public $_version;

	/**
	 * Constructor.
	 *
	 * @param string $plugin_name Plugin name.
	 * @param string $version Plugin version.
	 */
	function __construct( $plugin_name, $version ) {
		$this->_name    = $plugin_name;
		$this->_version = $version;
		$this->define_hooks();

	}

	/**
	 * Define hooks.
	 */
	function define_hooks() {
		add_action( 'admin_action_tinvwl_export_settings', array( $this, 'export_settings' ) );
		add_action( 'admin_action_tinvwl_import_settings', array( $this, 'import_settings' ) );

		if ( isset( $_REQUEST['error'] ) && isset( $_REQUEST['page'] ) && $_REQUEST['page'] === 'tinvwl-export-import-settings' ) {
			add_action( 'admin_notices', array( $this, 'show_error' ) );
		}
	}

	/**
	 * Show error on the current page.
	 */
	public function show_error() {
		printf(
			'<div class="notice notice-error"><p><strong>%s</strong></p></div>',
			sanitize_text_field( $_REQUEST['error'] )
		);
	}

	/**
	 * Get sanitized blog name.
	 *
	 * @return string|string[]|void
	 */
	private function blog_name() {

		$name = get_bloginfo( 'name' );

		// WordPress can have a blank site title, which will cause initial client creation to fail
		if ( empty( $name ) ) {
			$name = wp_parse_url( home_url(), PHP_URL_HOST );

			if ( $port = wp_parse_url( home_url(), PHP_URL_PORT ) ) {
				$name .= ':' . $port;
			}
		}

		$name = preg_replace( '/[^A-Za-z0-9 ]/', '', $name );
		$name = preg_replace( '/\s+/', ' ', $name );
		$name = str_replace( ' ', '-', $name );

		return $name;
	}


	/**
	 * Get all plugin settings.
	 *
	 * @return array|bool
	 */
	public function get_settings() {
		$dir = TINVWL_PATH . 'admin/settings/';
		if ( ! file_exists( $dir ) || ! is_dir( $dir ) ) {
			return false;
		}
		$files = scandir( $dir );
		$ids   = array();
		foreach ( $files as $value ) {
			if ( preg_match( '/\.class\.php$/i', $value ) ) {
				$file    = preg_replace( '/\.class\.php$/i', '', $value );
				$class   = 'TInvWL_Admin_Settings_' . ucfirst( $file );
				$options = $class::instance( $this->_name, $this->_version );
				if ( method_exists( $options, 'constructor_data' ) && __FUNCTION__ != 'constructor_data' ) {

					foreach ( $options->constructor_data() as $data ) {
						$ids[] = $data['id'];
					}
				}
			}
		}
		foreach ( array_keys( $ids, 'save_buttons', true ) as $key ) {
			unset( $ids[ $key ] );
		}
		$ids = array_values( $ids );

		$settings = array();

		foreach ( $ids as $id ) {
			$settings[ 'tinvwl-' . $id ] = get_option( 'tinvwl-' . $id );
		}

		return $settings;
	}

	/**
	 * Allow upload JSON extension.
	 *
	 * @param array $mimes
	 *
	 * @return array
	 */
	function json_mime_type( $mimes ) {
		$mimes['json'] = 'application/json';

		return $mimes;
	}

	/**
	 * handle import settings upload and updating database.
	 */
	public function import_settings() {

		$nonce_value = isset( $_REQUEST['tinvwl_import_nonce'] ) ? $_REQUEST['tinvwl_import_nonce'] : '';

		if ( ! wp_verify_nonce( $nonce_value, 'tinvwl_import' ) || ! in_array( 'administrator', (array) wp_get_current_user()->roles ) ) {
			exit(
			wp_redirect(
				admin_url(
					'admin.php?page=tinvwl-export-import-settings&error=' .
					rawurlencode( __( 'There was an error importing your settings, please try again.', 'ti-woocommerce-wishlist' ) )
				)
			)
			);
		}

		if ( ! function_exists( 'wp_handle_upload' ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
		}

		if ( isset( $_FILES['settings-file'] ) && $_FILES['settings-file']['error'] !== 4 ) {

			if ( $_FILES['settings-file']['error'] === 0 ) {
				$uploadedfile     = $_FILES['settings-file'];
				$upload_overrides = array(
					'test_form' => false,
					'mimes'     => array( 'json' => 'application/json' ),
				);

				add_filter( 'upload_mimes', array( $this, 'json_mime_type' ) );
				$movefile = wp_handle_upload( $uploadedfile, $upload_overrides );
				remove_filter( 'upload_mimes', array( $this, 'json_mime_type' ) );

				if ( $movefile && ! isset( $movefile['error'] ) ) {

					$settings_json = file_get_contents( $movefile['file'] );
					unlink( $movefile['file'] );

					if ( empty( $settings_json ) ) {
						exit(
						wp_redirect(
							admin_url(
								'admin.php?page=tinvwl-export-import-settings&error=' .
								rawurlencode( __( 'The settings file is empty.', 'ti-woocommerce-wishlist' ) )
							)
						)
						);
					}

					$settings = json_decode( $settings_json, true );

					if ( empty( $settings ) ) {
						exit(
						wp_redirect(
							admin_url(
								'admin.php?page=tinvwl-export-import-settings&error=' .
								rawurlencode( __( 'The settings file is not valid.', 'ti-woocommerce-wishlist' ) )
							)
						)
						);
					}
				} else {
					exit(
					wp_redirect(
						admin_url(
							'admin.php?page=tinvwl-export-import-settings&error=' .
							rawurlencode( $movefile['error'] )
						)
					)
					);
				}
			} else {
				switch ( $_FILES['settings-file']['error'] ) {
					case 1:
					case 2:
						exit(
						wp_redirect(
							admin_url(
								'admin.php?page=tinvwl-export-import-settings&error=' .
								rawurlencode( __( 'The file you are uploading is too big.', 'ti-woocommerce-wishlist' ) )
							)
						)
						);
						break;
					case 3:
						exit(
						wp_redirect(
							admin_url(
								'admin.php?page=tinvwl-export-import-settings&error=' .
								rawurlencode( __( 'There was an error uploading the file.', 'ti-woocommerce-wishlist' ) )
							)
						)
						);
						break;
					case 6:
					case 7:
					case 8:
						exit(
						wp_redirect(
							admin_url(
								'admin.php?page=tinvwl-export-import-settings&error=' .
								rawurlencode( __( 'There was an error importing your settings, please try again.', 'ti-woocommerce-wishlist' ) )
							)
						)
						);
						break;
				}
			}
		} else {
			$settings_json = trim( stripslashes( $_POST['settings-json'] ) );

			if ( empty( $settings_json ) ) {
				exit(
				wp_redirect(
					admin_url(
						'admin.php?page=tinvwl-export-import-settings&error=' .
						rawurlencode( __( 'Please upload the TI WooCommerce Wishlist setting file or copy the content.', 'ti-woocommerce-wishlist' ) )
					)
				)
				);
			}

			$settings = json_decode( $settings_json, true );

			if ( empty( $settings ) ) {
				exit(
				wp_redirect(
					admin_url(
						'admin.php?page=tinvwl-export-import-settings&error=' .
						rawurlencode( __( 'The settings json is not valid.', 'ti-woocommerce-wishlist' ) )
					)
				)
				);
			}
		}

		foreach ( $settings as $key => $value ) {
			update_option( $key, $value );
		}

		exit( wp_redirect( admin_url( 'admin.php?page=tinvwl' ) ) );
	}

	/**
	 * Handle settings export in a JSON file.
	 */
	public function export_settings() {

		$nonce_value = isset( $_REQUEST['tinvwl_import_nonce'] ) ? $_REQUEST['tinvwl_import_nonce'] : '';

		if ( ! wp_verify_nonce( $nonce_value, 'tinvwl_import' ) || ! in_array( 'administrator', (array) wp_get_current_user()->roles ) ) {
			exit(
			wp_redirect(
				admin_url(
					'admin.php?page=tinvwl-export-import-settings&error=' .
					rawurlencode( __( 'There was an error exporting your settings, please try again.', 'ti-woocommerce-wishlist' ) )
				)
			)
			);
		}

		header( 'Content-Type: application/json' );
		$name = urlencode( $this->blog_name() );
		header( "Content-Disposition: attachment; filename=ti_woocommerce_wishlist_settings-$name.json" );
		header( 'Pragma: no-cache' );

		$settings = $this->get_settings();

		echo json_encode( $settings );
		exit;
	}
}
