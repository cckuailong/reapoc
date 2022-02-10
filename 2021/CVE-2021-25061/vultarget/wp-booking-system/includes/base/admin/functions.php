<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Includes the files needed for the admin area
 *
 */
function wpbs_include_files_admin() {

	// Get calendar dir path
	$dir_path = plugin_dir_path( __FILE__ );

	// Include the db layer classes
	if( file_exists( $dir_path . 'class-admin-notices.php' ) )
		include $dir_path . 'class-admin-notices.php';

}
add_action( 'wpbs_include_files', 'wpbs_include_files_admin' );


/**
 * Adds a central action hook on the admin_init that the plugin and add-ons
 * can use to do certain actions, like adding a new calendar, editing a calendar, deleting, etc.
 *
 */
function wpbs_register_do_actions() {

	if( empty( $_REQUEST['wpbs_action'] ) )
		return;

	$action = sanitize_text_field( $_REQUEST['wpbs_action'] );

	/**
	 * Hook that should be used by all processes that make a certain action
	 * withing the plugin, like adding a new calendar, editing a calendar, deleting, etc.
	 *
	 */
	do_action( 'wpbs_action_' . $action );

}
add_action( 'admin_init', 'wpbs_register_do_actions' );


/**
 * Builds and returns the HTML with a tooltip for the given message
 *
 * @param string $message
 *
 * @return string
 *
 */
function wpbs_get_output_tooltip( $message ) {

	$output = '<span class="wpbs-tooltip-wrapper">';

		// Icon
		$output .= '<span class="wpbs-tooltip-icon">?</span>';

		// Message
		$output .= '<span class="wpbs-tooltip-message">';

			$output .= $message;

			// Arrow
			$output .= '<span class="wpbs-tooltip-arrow"></span>';

		$output .= '</span>';

	$output .= '</span>';

	return $output;

}