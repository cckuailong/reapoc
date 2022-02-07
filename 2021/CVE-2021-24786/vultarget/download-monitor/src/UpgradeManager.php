<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class DLM_Upgrade_Manager {

	/**
	 * Setup to run updater on wp_loaded
	 */
	public function setup() {
		add_action( 'wp_loaded', array( $this, 'check' ) );
	}

	/**
	 * Check if there's a plugin update
	 */
	public function check() {

		// Get current version
		$current_version = get_option( DLM_Constants::OPTION_CURRENT_VERSION, 0 );

		// Check if update is required
		if ( version_compare( DLM_VERSION, $current_version, '>' ) ) {

			// Do update
			$this->do_upgrade( $current_version );

			// Update version code
			$this->update_current_version_code();

		}

	}

	/**
	 * An update is required, do it
	 *
	 * @param $current_version
	 */
	private function do_upgrade( $current_version ) {
		global $wpdb;

		$installer = new DLM_Installer();

		// Upgrade to version 1.7.0
		if ( version_compare( $current_version, '1.7.0', '<' ) ) {

			// Adding new capabilities
			$installer->init_user_roles();

			// Set default 'No access message'
			$dlm_no_access_error = get_option( 'dlm_no_access_error', '' );
			if ( '' === $dlm_no_access_error ) {
				update_option( 'dlm_no_access_error', sprintf( __( 'You do not have permission to access this download. %sGo to homepage%s', 'download-monitor' ), '<a href="' . home_url() . '">', '</a>' ) );
			}

		}

		// Upgrade to version 1.9.0
		if ( version_compare( $current_version, '1.9.0', '<' ) ) {

			// setup no access page endpoints
			$no_access_page_endpoint = new DLM_Download_No_Access_Page_Endpoint();
			$no_access_page_endpoint->setup();

			// flush rules after page creation
			flush_rewrite_rules();
		}

		// upgrade to version 4.0
		if ( version_compare( $current_version, '4.0.0', '<' ) ) {

			// upgrade log table
			$wpdb->query( "ALTER TABLE {$wpdb->download_log} CHANGE `download_date` `download_date` DATETIME NULL DEFAULT NULL;" );
			$wpdb->query( "ALTER TABLE {$wpdb->download_log} ADD `meta_data` LONGTEXT NULL DEFAULT NULL AFTER `download_status_message`;" );
			$wpdb->query( "ALTER TABLE {$wpdb->download_log} DROP `type`;" );

			// add new capability
			$wp_roles = new WP_Roles();
			$wp_roles->add_cap( 'administrator', 'dlm_view_reports' );

		}

		// upgrade to version 4.1
		if ( version_compare( $current_version, '4.1.0', '<' ) ) {
			update_option( 'dlm_logging_ua', 1 );
		}

		// upgrade to version 4.3
		if ( version_compare( $current_version, '4.3.0', '<' ) ) {
			$installer->create_shop_tables();
		}

		// upgrade to version 4.4
		if ( version_compare( $current_version, '4.4.0', '<' ) ) {
			$wpdb->query( "ALTER TABLE {$wpdb->prefix}dlm_order_item CHANGE `download_id` `product_id` int(20);" );

			// flush rules because of new post type in 4.4
			flush_rewrite_rules();
		}

	}

	/**
	 * Update the current version code
	 */
	private function update_current_version_code() {
		update_option( DLM_Constants::OPTION_CURRENT_VERSION, DLM_VERSION );
	}

}