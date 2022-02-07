<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * DLM_Ajax_Handler class.
 */
class DLM_Ajax_Handler {

	/**
	 * __construct function.
	 *
	 * @access public
	 */
	public function __construct() {
		add_action( 'wp_ajax_download_monitor_remove_file', array( $this, 'remove_file' ) );
		add_action( 'wp_ajax_download_monitor_add_file', array( $this, 'add_file' ) );
		add_action( 'wp_ajax_download_monitor_list_files', array( $this, 'list_files' ) );
		add_action( 'wp_ajax_download_monitor_insert_panel_upload', array( $this, 'insert_panel_upload' ) );
		add_action( 'wp_ajax_dlm_settings_lazy_select', array( $this, 'handle_settings_lazy_select' ) );
		add_action( 'wp_ajax_dlm_extension', array( $this, 'handle_extensions' ) );
		add_action( 'wp_ajax_dlm_dismiss_notice', array( $this, 'dismiss_notice' ) );
	}

	/**
	 * insert_panel_upload function.
	 *
	 * @access public
	 * @return void
	 */
	public function insert_panel_upload() {

		check_ajax_referer( 'file-upload' );

		// Check user rights
		if ( ! current_user_can( 'manage_downloads' ) ) {
			die();
		}

		require_once( ABSPATH . 'wp-admin/includes/image.php' );
		require_once( ABSPATH . 'wp-admin/includes/file.php' );
		require_once( ABSPATH . 'wp-admin/includes/media.php' );

		$attachment_id = media_handle_upload( 'async-upload', 0 );

		if ( ! is_wp_error( $attachment_id ) ) {
			$attachment_url = wp_get_attachment_url( $attachment_id );

			if ( false !== $attachment_url ) {
				echo $attachment_url;
			}
		}

		die();
	}

	/**
	 * remove_file function.
	 *
	 * @access public
	 * @return void
	 */
	public function remove_file() {

		check_ajax_referer( 'remove-file', 'security' );

		// Check user rights
		if ( ! current_user_can( 'manage_downloads' ) ) {
			die();
		}

		$file = get_post( intval( $_POST['file_id'] ) );

		if ( $file && $file->post_type == "dlm_download_version" ) {
			// clear transient
			download_monitor()->service( 'transient_manager' )->clear_versions_transient( $file->post_parent );

			wp_delete_post( $file->ID );
		}

		die();
	}

	/**
	 * add_file function.
	 *
	 * @access public
	 * @return void
	 */
	public function add_file() {

		// check nonce
		check_ajax_referer( 'add-file', 'security' );

		// Check user rights
		if ( ! current_user_can( 'manage_downloads' ) ) {
			die();
		}

		// get POST data
		$download_id = absint( $_POST['post_id'] );
		$size        = absint( $_POST['size'] );

		/** @var DLM_Download_Version $new_version */
		$new_version = new DLM_Download_Version();

		// set download id
		$new_version->set_download_id( $download_id );

		// set other version data
		$new_version->set_author( get_current_user_id() );
		$new_version->set_date( new DateTime( current_time( 'mysql' ) ) );

		// persist new version
		download_monitor()->service( 'version_repository' )->persist( $new_version );

		// clear download transient
		download_monitor()->service( 'transient_manager' )->clear_versions_transient( $download_id );

		// output new version admin html
		download_monitor()->service( 'view_manager' )->display( 'meta-box/version', array(
			'version_increment'   => $size,
			'file_id'             => $new_version->get_id(),
			'file_version'        => $new_version->get_version(),
			'file_post_date'      => $new_version->get_date(),
			'file_download_count' => $new_version->get_download_count(),
			'file_urls'           => $new_version->get_mirrors(),
			'version'             => $new_version
		) );

		die();
	}

	/**
	 * list_files function.
	 *
	 * @access public
	 * @return void
	 */
	public function list_files() {

		// Check Nonce
		check_ajax_referer( 'list-files', 'security' );

		// Check user rights
		if ( ! current_user_can( 'manage_downloads' ) ) {
			die();
		}

		$path = esc_attr( stripslashes( $_POST['path'] ) );

		if ( $path ) {

			// List all files
			$files = download_monitor()->service( 'file_manager' )->list_files( $path );

			foreach ( $files as $found_file ) {

				// Multi-byte-safe pathinfo
				$file = download_monitor()->service( 'file_manager' )->mb_pathinfo( $found_file['path'] );

				if ( $found_file['type'] == 'folder' ) {

					echo '<li><a href="#" class="folder" data-path="' . trailingslashit( $file['dirname'] ) . $file['basename'] . '">' . $file['basename'] . '</a></li>';

				} else {

					$filename  = $file['basename'];
					$extension = ( empty( $file['extension'] ) ) ? '' : $file['extension'];

					if ( substr( $filename, 0, 1 ) == '.' ) {
						continue;
					} // Ignore files starting with . like htaccess
					if ( in_array( $extension, array( '', 'php', 'html', 'htm', 'tmp' ) ) ) {
						continue;
					} // Ignored file types

					echo '<li><a href="#" class="file filetype-' . sanitize_title( $extension ) . '" data-path="' . trailingslashit( $file['dirname'] ) . $file['basename'] . '">' . $file['basename'] . '</a></li>';

				}

			}
		}

		die();
	}

	/**
	 * Handle notice dismissal
	 */
	public function dismiss_notice() {

		// check notice
		if ( ! isset( $_POST['notice'] ) || empty( $_POST['notice'] ) ) {
			exit;
		}

		// the notice
		$notice = $_POST['notice'];

		// check nonce
		check_ajax_referer( 'dlm_hide_notice-' . $notice, 'nonce' );

		// update option
		update_option( 'dlm_hide_notice-' . $notice, 1 );

		// send JSON
		wp_send_json( array( 'response' => 'success' ) );
	}

	/**
	 * Handle lazy select AJAX calls
	 */
	public function handle_settings_lazy_select() {

		// check nonce
		check_ajax_referer( 'dlm-settings-lazy-select-nonce', 'nonce' );

		// settings key
		$option_key = sanitize_text_field( $_POST['option'] );

		// get options
		$options = apply_filters( 'dlm_settings_lazy_select_'.$option_key, array() );

		// send options
		wp_send_json( $options );
		exit;

	}

	/**
	 * Handle extensions AJAX
	 */
	public function handle_extensions() {

		// Check nonce
		check_ajax_referer( 'dlm-ajax-nonce', 'nonce' );

		// Post vars
		$product_id       = sanitize_text_field( $_POST['product_id'] );
		$key              = sanitize_text_field( $_POST['key'] );
		$email            = sanitize_text_field( $_POST['email'] );
		$extension_action = $_POST['extension_action'];

		// Get products
		$products = DLM_Product_Manager::get()->get_products();

		// Check if product exists
		$response = "";
		if ( isset( $products[ $product_id ] ) ) {

			// Get correct product
			/** @var DLM_Product $product */
			$product = $products[ $product_id ];

			// Set new key in license object
			$product->get_license()->set_key( $key );

			// Set new email in license object
			$product->get_license()->set_email( $email );

			if ( 'activate' === $extension_action ) {
				// Try to activate the license
				$response = $product->activate();
			} else {
				// Try to deactivate the license
				$response = $product->deactivate();
			}

		}

		// Send JSON
		wp_send_json( $response );
	}
}