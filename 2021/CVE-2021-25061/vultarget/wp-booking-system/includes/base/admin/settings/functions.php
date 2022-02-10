<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Includes the files needed for the Settings admin area
 *
 */
function wpbs_include_files_admin_settings() {

	// Get legend admin dir path
	$dir_path = plugin_dir_path( __FILE__ );

	// Include submenu page
	if( file_exists( $dir_path . 'class-submenu-page-settings.php' ) )
		include $dir_path . 'class-submenu-page-settings.php';

}
add_action( 'wpbs_include_files', 'wpbs_include_files_admin_settings' );


/**
 * Register the Settings admin submenu page
 *
 */
function wpbs_register_submenu_page_settings( $submenu_pages ) {

	if( ! is_array( $submenu_pages ) )
		return $submenu_pages;

	$submenu_pages['settings'] = array(
		'class_name' => 'WPBS_Submenu_Page_Settings',
		'data' 		 => array(
			'page_title' => __( 'Settings', 'wp-booking-system' ),
			'menu_title' => __( 'Settings', 'wp-booking-system' ),
			'capability' => apply_filters( 'wpbs_submenu_page_capability_settings', 'manage_options' ),
			'menu_slug'  => 'wpbs-settings'
		)
	);

	return $submenu_pages;

}
add_filter( 'wpbs_register_submenu_page', 'wpbs_register_submenu_page_settings', 50 );