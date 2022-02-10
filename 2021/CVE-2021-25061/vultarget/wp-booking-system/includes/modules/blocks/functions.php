<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Function that register the needed categories for the different block
 * available in the plugin
 *
 */
function wpbs_register_block_categories( $categories, $post ) {

	/**
	 * Filter the post types where the blocks are available
	 *
	 * @param array
	 *
	 */
	$post_types = apply_filters( 'wpbs_register_block_categories_post_types', array( 'post', 'page' ) );

	if( ! in_array( $post->post_type, $post_types ) )
		return $categories;

	$categories[] = array(
		'slug'  => 'wp-booking-system',
		'title' => 'WP Booking System',
		'icon'	=> ''
	);

	return $categories;

}
add_filter( 'block_categories', 'wpbs_register_block_categories', 10, 2 );


/**
 * Adds the needed JavaScript variables up in the WordPress admin head
 *
 */
function wpbs_add_javascript_variables() {

	if( ! function_exists( 'get_current_screen' ) )
		return;

	$screen = get_current_screen();

	if( is_null( $screen ) )
		return;

	/**
	 * Filter the post types where the calendar media button should appear
	 *
	 * @param array
	 *
	 */
	$post_types = apply_filters( 'wpbs_register_block_categories_post_types', array( 'post', 'page' ) );

	if( ! in_array( $screen->post_type, $post_types ) )
	    return;

	$settings = get_option( 'wpbs_settings', array() );

	echo '<script type="text/javascript">';

	// Add calendars to be globally available
	$calendars = wpbs_get_calendars( array( 'number' => -1, 'status' => 'active' ) );

	echo 'var wpbs_calendars = [';

	foreach( $calendars as $key => $calendar ) {
		echo '{ "id" : ' . $calendar->get('id') . ', "name" : "' . $calendar->get('name') . '" }';

		if( $key != count( $calendars ) - 1 )
			echo ',';

	}

	echo '];';

	// Add Forms to be globally available
	$forms = wpbs_get_forms( array( 'number' => -1, 'status' => 'active' ) );

	echo 'var wpbs_forms = [';

	foreach( $forms as $key => $form ) {
		echo '{ "id" : ' . $form->get('id') . ', "name" : "' . $form->get('name') . '" }';

		if( $key != count( $forms ) - 1 )
			echo ',';

	}

	echo '];';

	// Add languages to be globally available
	$languages = wpbs_get_languages();

	echo 'var wpbs_languages = [';
	
	if( ! empty( $settings['active_languages'] ) ) {

		foreach( $settings['active_languages'] as $key => $code ) {

			if( empty( $languages[$code] ) )
				continue;

			echo '{ "code" : "' . $code . '", "name" : "' . $languages[$code] . '" }';

			if( $key != count( $settings['active_languages'] ) - 1 )
				echo ',';

		}

	}

	echo '];';

	echo '</script>';


	// Enqueue front-end scripts on the admin part
	wp_register_script( 'wpbs-front-end-script', WPBS_PLUGIN_DIR_URL . 'assets/js/script-front-end.js', array( 'jquery' ), WPBS_VERSION, true );
	wp_enqueue_script( 'wpbs-front-end-script' );

}
add_action( 'admin_enqueue_scripts', 'wpbs_add_javascript_variables', 10 );