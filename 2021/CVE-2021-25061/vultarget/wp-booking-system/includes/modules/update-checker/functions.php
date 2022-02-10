<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Includes the files needed for the 
 *
 */
function wpbs_include_files_update_checker() {

	// Get legend admin dir path
	$dir_path = plugin_dir_path( __FILE__ );

	// Include actions file
	if( file_exists( $dir_path . 'functions-actions-update-checker.php' ) )
		include $dir_path . 'functions-actions-update-checker.php';

	// Include update checker class
	if( file_exists( $dir_path . 'class-update-checker.php' ) )
		include $dir_path . 'class-update-checker.php';

}
add_action( 'wpbs_include_files', 'wpbs_include_files_update_checker' );


/**
 * Initializes the update checker
 *
 */
function wpbs_init_plugin_update_cheker() {

	$serial_key = get_option( 'wpbs_serial_key', '' );
	$website_id = get_option( 'wpbs_registered_website_id', '' );

	if( empty( $serial_key ) || empty( $website_id ) )
		return;

	$url_args = array(
		'request'      => 'get_update',
		'product_slug' => 'wp-booking-system-premium',
		'serial_key'   => $serial_key
	);
	
	$update_checker = new WPBS_PluginUpdateChecker( add_query_arg( $url_args, 'https://www.wpbookingsystem.com/u/' ), WPBS_FILE, 'wp-booking-system-premium', 24 );

}
add_action( 'plugins_loaded', 'wpbs_init_plugin_update_cheker' );


/**
 * Adds a new tab to the Settings page of the plugin
 *
 * @param array $tabs
 *
 * @return $tabs
 *
 */
function wpbs_submenu_page_settings_tabs_register_website( $tabs ) {

	$tabs['register_website'] = __( 'Upgrade to Premium', 'wp-booking-system' );

	return $tabs;

}
add_filter( 'wpbs_submenu_page_settings_tabs', 'wpbs_submenu_page_settings_tabs_register_website', 50 );


/**
 * Adds the HTML for the Register Version tab
 *
 */
function wpbs_submenu_page_settings_tab_register_website() {

	include 'views/view-register-website.php';

}
add_action( 'wpbs_submenu_page_settings_tab_register_website', 'wpbs_submenu_page_settings_tab_register_website' );


/**
 * Registers the admin notices needed for the register/deregister website functionality
 *
 */
function wpbs_register_admin_notices_update_checker() {

	if( empty( $_GET['wpbs_message'] ) )
		return;

	/**
	 * Register website notices
	 *
	 */
	wpbs_admin_notices()->register_notice( 'register_website_general_error', '<p>' . __( 'Something went wrong. Could not complete the operation.', 'wp-booking-system' ) . '</p>', 'error' );

	wpbs_admin_notices()->register_notice( 'register_website_serial_key_missing', '<p>' . __( 'Please provide a serial key.', 'wp-booking-system' ) . '</p>', 'error' );

	wpbs_admin_notices()->register_notice( 'register_website_response_error', '<p>' . __( 'Something went wrong. Could not connect to our server to register your website.', 'wp-booking-system' ) . '</p>', 'error' );

	wpbs_admin_notices()->register_notice( 'register_website_already_registered', '<p>' . sprintf( __( 'This website is already registered with the provided serial key. Please log into %syour account on our website%s to view all your registered websites.', 'wp-booking-system' ), '<a href="https://www.wpbookingsystem.com/account/" target="_blank">', '</a>' ) . '</p>', 'error' );

	wpbs_admin_notices()->register_notice( 'register_website_serial_expired', '<p>' . sprintf( __( 'The provided serial key is either invalid or expired. You cannot register a website with an invalid or expired serial key. %sPlease visit our website to set up or renew your WP Booking System license%s.', 'wp-booking-system' ), '<a href="https://www.wpbookingsystem.com/" target="_blank">', '</a>' ) . '</p>', 'error' );

	wpbs_admin_notices()->register_notice( 'register_website_maximum_websites', '<p>' . sprintf( __( 'The maximum number of websites have been registered for this serial key. To upgrade your license, %splease visit our website%s.', 'wp-booking-system' ), '<a href="https://www.wpbookingsystem.com/" target="_blank">', '</a>' ) . '</p>', 'error' );

	wpbs_admin_notices()->register_notice( 'register_website_success', '<p>' . sprintf( __( 'Website successfully registered. To view all your registered websites, please check %syour account page on our website%s.', 'wp-booking-system' ), '<a href="https://www.wpbookingsystem.com/account/" target="_blank">', '</a>' ) . '</p>' );	

	/**
	 * Deregister website notices
	 *
	 */
	wpbs_admin_notices()->register_notice( 'deregister_website_general_error', '<p>' . __( 'Something went wrong. Could not complete the operation.', 'wp-booking-system' ) . '</p>', 'error' );

	wpbs_admin_notices()->register_notice( 'deregister_website_serial_key_missing', '<p>' . __( 'Please provide a serial key.', 'wp-booking-system' ) . '</p>', 'error' );

	wpbs_admin_notices()->register_notice( 'deregister_website_response_error', '<p>' . __( 'Something went wrong. Could not connect to our server to deregister your website.', 'wp-booking-system' ) . '</p>', 'error' );

	wpbs_admin_notices()->register_notice( 'deregister_website_success', '<p>' . __( 'Website successfully deregistered.', 'wp-booking-system' ) . '</p>' );

	// Check for updates
	wpbs_admin_notices()->register_notice( 'check_for_updates_success', '<p>' . sprintf( __( 'Please visit the %sPlugins page%s to check if a new update is available for WP Booking System.', 'wp-booking-system' ), '<a href="' . admin_url( 'plugins.php' ) . '">', '</a>' ) . '</p>' );

}
add_action( 'admin_init', 'wpbs_register_admin_notices_update_checker' );




/**
 * Adds a notice to the admin screen that the version of WP Booking System is
 * not registered.
 *
 */
function wpbs_register_admin_notice_serial_expired() {

	if( ! current_user_can( 'manage_options' ) )
		return;

	$serial_key = get_option( 'wpbs_serial_key', '' );

	if( empty( $serial_key ) )
		return;

	$transient = get_transient( 'wpbs_serial_status' );

	if( false === $transient )
		return;

	if( $transient != -1 )
		return;

	echo '<div class="notice notice-error">';

		echo '<p>' . sprintf( __( "Your WP Booking System serial key has expired. Plugin updates are not available without a valid serial key. %sPlease click here to purchase a new license%s.", 'wp-booking-system' ), '<a href="https://www.wpbookingsystem.com/" target="_blank">', '</a>' ) . '</p>';

	echo '</div>';

}
add_action( 'admin_notices', 'wpbs_register_admin_notice_serial_expired' );