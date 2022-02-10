<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Registers the Single Calendar block
 *
 */
if( function_exists( 'register_block_type' ) ) {

	function wpbs_register_block_type_single_calendar() {

		wp_register_script( 'wpbs-script-block-single-calendar', WPBS_PLUGIN_DIR_URL . 'includes/modules/blocks/single-calendar/assets/js/build/script-block-single-calendar.js', array( 'wp-blocks', 'wp-element', 'wp-editor', 'wp-i18n' ) );

		register_block_type( 
			'wp-booking-system/single-calendar', 
			array(
				'attributes' => array(
					'id' => array(
						'type' => 'string'
					),
					'form_id' => array(
						'type' => 'string'
					),
					'title' => array(
						'type' => 'string'
					),
					'legend' => array(
						'type' => 'string'
					),
					'language' => array(
						'type' => 'string'
					)
				),
				'editor_script'   => 'wpbs-script-block-single-calendar', 
				'render_callback' => 'wpbs_block_to_shortcode_single_calendar'
			)	
		);

	}
	add_action( 'init', 'wpbs_register_block_type_single_calendar' );

}


/**
 * Render callback for the server render block
 * Transforms the attributes from the blocks into the needed shortcode arguments
 *
 * @param array $args
 *
 * @return string
 *
 */
function wpbs_block_to_shortcode_single_calendar( $args ) {

	if( empty( $args['id'] ) ) {

		return '<div style="padding: 20px; background-color: #f1f1f1;">' . __( 'Please select a calendar to display.' ) . '</div>';

	}
	
	// Execute the shortcode
	return WPBS_Shortcodes::single_calendar( $args );

}