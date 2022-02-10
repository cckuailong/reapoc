<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Includes the files needed for the Calendar admin area
 *
 */
function wpbs_include_files_admin_calendar() {

	// Get legend admin dir path
	$dir_path = plugin_dir_path( __FILE__ );

	// Include submenu page
	if( file_exists( $dir_path . 'class-submenu-page-calendar.php' ) )
		include $dir_path . 'class-submenu-page-calendar.php';

	// Include calendars list table
	if( file_exists( $dir_path . 'class-list-table-calendars.php' ) )
		include $dir_path . 'class-list-table-calendars.php';

	// Include calendar editor outputter
	if( file_exists( $dir_path . 'class-calendar-editor-outputter.php' ) )
		include $dir_path . 'class-calendar-editor-outputter.php';

	if( file_exists( $dir_path . 'functions-actions-calendar.php' ) )
		include $dir_path . 'functions-actions-calendar.php';

	if( file_exists( $dir_path . 'functions-actions-ajax-calendar.php' ) )
		include $dir_path . 'functions-actions-ajax-calendar.php';

	if( file_exists( $dir_path . 'functions-shortcode-generator.php' ) )
		include $dir_path . 'functions-shortcode-generator.php';

}
add_action( 'wpbs_include_files', 'wpbs_include_files_admin_calendar' );


/**
 * Register the Calendars admin submenu page
 *
 */
function wpbs_register_submenu_page_calendars( $submenu_pages ) {

	if( ! is_array( $submenu_pages ) )
		return $submenu_pages;

	$submenu_pages['calendars'] = array(
		'class_name' => 'WPBS_Submenu_Page_Calendars',
		'data' 		 => array(
			'page_title' => __( 'Calendars', 'wp-booking-system' ),
			'menu_title' => __( 'Calendars', 'wp-booking-system' ),
			'capability' => apply_filters( 'wpbs_submenu_page_capability_calendars', 'manage_options' ),
			'menu_slug'  => 'wpbs-calendars'
		)
	);

	return $submenu_pages;

}
add_filter( 'wpbs_register_submenu_page', 'wpbs_register_submenu_page_calendars', 20 );


/**
 * Returns the HTML for the legend item icon
 *
 * @param int    $legend_item_id
 * @param string $type
 * @param array  $color
 *
 * @return string
 *
 */
function wpbs_get_legend_item_icon( $legend_item_id, $type, $color = array() ) {

	$output = '<div class="wpbs-legend-item-icon wpbs-legend-item-icon-' . esc_attr( $legend_item_id ) . '" data-type="' . esc_attr( $type ) . '">';

		for( $i = 0; $i <= 1; $i++ ){
			
			$svg = '';	
			if($type == "split"){
				$svg = ($i == 0) ? '<svg height="100%" width="100%" viewBox="0 0 50 50" preserveAspectRatio="none"><polygon points="0,0 0,50 50,0" /></svg>' : '<svg height="100%" width="100%" viewBox="0 0 50 50" preserveAspectRatio="none"><polygon points="0,50 50,50 50,0" /></svg>';
			} 
			
			
			$output .= '<div class="wpbs-legend-item-icon-color" ' . ( ! empty( $color[$i] ) ? 'style="background-color: ' . esc_attr( $color[$i] ) . ';"' : '' ) . '>' . $svg . '</div>';
		}

	$output .= '</div>';

	return $output;

}

/**
 * Show the update screen to existing users who updated from version 1.x
 * 
 */
function wpbs_show_update_screen(){

	if(get_option('wpbs_hide_v2_welcome_screen')){
		return;
	}

	if(!get_option('wpbs_db_version')){
		return;
	}

	if(isset($_GET['wpbs-welcome']) && $_GET['wpbs-welcome'] == 1){
		add_option('wpbs_hide_v2_welcome_screen', true);
		return;
	}

	include('views/view-welcome-to-2.0.php');
}