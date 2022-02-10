<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Includes the files needed for the Backup/Restore admin area
 *
 */
function wpbs_include_files_admin_backup() {

	// Get legend admin dir path
	$dir_path = plugin_dir_path( __FILE__ );

	// Include actions
	if( file_exists( $dir_path . 'functions-actions-backup.php' ) )
		include $dir_path . 'functions-actions-backup.php';

	// Include submenu page
	if( file_exists( $dir_path . 'class-submenu-page-backup.php' ) )
		include $dir_path . 'class-submenu-page-backup.php';

}
add_action( 'wpbs_include_files', 'wpbs_include_files_admin_backup' );


/**
 * Register the Backup/Restore admin submenu page
 *
 */
function wpbs_register_submenu_page_backup( $submenu_pages ) {

	if( ! is_array( $submenu_pages ) )
		return $submenu_pages;

	$submenu_pages['backup'] = array(
		'class_name' => 'WPBS_Submenu_Page_Backup',
		'data' 		 => array(
			'page_title' => __( 'Backup/Restore', 'wp-booking-system' ),
			'menu_title' => __( 'Backup/Restore', 'wp-booking-system' ),
			'capability' => apply_filters( 'wpbs_submenu_page_capability_backup', 'manage_options' ),
			'menu_slug'  => 'wpbs-backup'
		)
	);

	return $submenu_pages;

}
add_filter( 'wpbs_register_submenu_page', 'wpbs_register_submenu_page_backup', 75 );