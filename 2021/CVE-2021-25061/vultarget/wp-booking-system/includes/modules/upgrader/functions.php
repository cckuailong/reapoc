<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Includes the files needed for the Settings admin area
 *
 */
function wpbs_include_files_upgrader() {

	// Get legend admin dir path
	$dir_path = plugin_dir_path( __FILE__ );

	// Include submenu page
	if( file_exists( $dir_path . 'class-submenu-page-upgrader.php' ) )
		include $dir_path . 'class-submenu-page-upgrader.php';

	// Include actions
	if( file_exists( $dir_path . 'functions-actions-upgrader.php' ) )
		include $dir_path . 'functions-actions-upgrader.php';

	// Include AJAX actions
	if( file_exists( $dir_path . 'functions-actions-ajax-upgrader.php' ) )
		include $dir_path . 'functions-actions-ajax-upgrader.php';

}
add_action( 'wpbs_include_files', 'wpbs_include_files_upgrader' );


/**
 * Registers the upgrader submenu page if an upgrade is needed
 * Deregisters all other pages if an upgraded is needed
 *
 * @param array $submenu_pages
 *
 * @return array
 *
 */
function wpbs_register_submenu_page_upgrader( $submenu_pages ) {

	if( ! is_array( $submenu_pages ) )
		return $submenu_pages;

	// Check is there is a need for an upgrade
	if( false === wpbs_process_upgrade_from() )
		return $submenu_pages;

	// Remove all registered pages
	$submenu_pages = array();

	// Add the welcome page
	$submenu_pages['upgrader'] = array(
		'class_name' => 'WPBS_Submenu_Page_Upgrader',
		'data' 		 => array(
			'page_title' => __( 'Welcome', 'wp-booking-system' ),
			'menu_title' => __( 'Welcome', 'wp-booking-system' ),
			'capability' => apply_filters( 'wpbs_submenu_page_capability_upgrader', 'manage_options' ),
			'menu_slug'  => 'wpbs-upgrader'
		)
	);

	return $submenu_pages;

}
add_filter( 'wpbs_register_submenu_page', 'wpbs_register_submenu_page_upgrader', 1000 );


/**
 * Returns a string detailing from which plugin the upgrade should be made
 *
 * @return mixed false|string (string values can be "old_premium" and "free")
 *
 */
function wpbs_process_upgrade_from() {

	/**
	 * Check to see if the upgrade has been skipped
	 *
	 */
	$upgrade_skipped = get_option( 'wpbs_upgrade_5_0_0_skipped' );

	if( false !== $upgrade_skipped )
		return false;

	/**
	 * Check to see if the upgrade has already been made
	 *
	 */
	$upgrade_done = get_option( 'wpbs_upgrade_5_0_0' );

	if( false !== $upgrade_done )
		return false;

	/**
	 * Check to see if there was an old version installed.
	 *
	 */
	$old_version_installed = get_option( 'wpbs_db_version' );

	if( false === $old_version_installed )
		return false;
	
	return true;

}


/**
 * Function ported from the previous version of the plugin. Should not be used in any circumstances
 * outside of the plugin
 *
 * @access private
 *
 * @param string $str
 *
 * @return string
 *
 */
function _wpbs_replace_custom( $str ) {
    return str_replace( 
        array(
            '--AMP--',
            '--DOUBLEQUOTE--',
            '--QUOTE--',
            '--LT--',
            '--GT--'
        ),
        array(
            '&',
            '"',
            "'",
            '<',
            '>'
        ),
        $str );
}


