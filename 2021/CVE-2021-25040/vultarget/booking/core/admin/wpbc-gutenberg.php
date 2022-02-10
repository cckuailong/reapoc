<?php
/**
 * @version 1.0
 * @package Booking Calendar
 * @subpackage Getenberg integration
 * @category inserting into posts
 *
 * @author wpdevelop
 * @link https://wpbookingcalendar.com/
 * @email info@wpbookingcalendar.com
 *
 * @modified 2018-04-22
 */

//FixIn: 8.3.3.99

if ( ! defined( 'ABSPATH' ) ) exit;                                             // Exit if accessed directly

function wpbc_gutenberg_block_booking() {

	if ( function_exists( 'register_block_type' ) ) {

		wp_register_script( 'gutenberg-wpbc-booking', wpbc_plugin_url( '/js/wpbc-gutenberg.js' ), array(
			'wp-blocks',
			'wp-element',
			'wpbc-wpdevelop-bootstrap'                                                                                  //FixIn: 8.8.2.12
		) );
	    wp_register_style( 'gutenberg-wpbc-editor', wpbc_plugin_url( '/css/wpbc-gutenberg.css' ),
		    array( 'wp-edit-blocks' ),
	        filemtime( plugin_dir_path( __FILE__ ) . '../../css/wpbc-gutenberg.css' )
	    );
		register_block_type( 'booking/booking', array(
			   'editor_script' => 'gutenberg-wpbc-booking'
			 , 'editor_style'  => 'gutenberg-wpbc-editor'
		) );
	}
}
add_action( 'init', 'wpbc_gutenberg_block_booking' );