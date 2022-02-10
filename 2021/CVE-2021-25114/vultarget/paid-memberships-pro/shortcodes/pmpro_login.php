<?php
/**
 * Display a Member Login Form and Optional "Logged In" state with Display Name, Log Out link and the "Member Form" menu.
 * The menu is only shown to users with an active membership level.
 * The menu can be customized per-level using the Nav Menus Add On for Paid Memberships Pro.
 *
 */
function pmpro_shortcode_login( $atts, $content=null, $code='' ) {
	// $atts    ::= array of attributes
	// $content ::= text within enclosing form of shortcode element
	// $code    ::= the shortcode found, when == callback name
	// examples: [pmpro_login show_menu="1"]

	extract( shortcode_atts( array(
		'display_if_logged_in' => true,
		'show_menu' => true,
		'show_logout_link' => true,
		'location' => 'shortcode'
	), $atts ) );

	// Turn 0's into falses.
	if ( $display_if_logged_in === '0' || $display_if_logged_in === 'false' || $display_if_logged_in === 'no' ) {
		$display_if_logged_in = false;
	} else {
		$display_if_logged_in = true;
	}

	if ( $show_menu === '0' || $show_menu === 'false' || $show_menu === 'no' ){
		$show_menu = false;
	} else {
		$show_menu = true;
	}

	if ( $show_logout_link === '0' || $show_logout_link === 'false' || $show_logout_link === 'no' ) {
		$show_logout_link = false;
	} else {
		$show_logout_link = true;
	}

	// Display the login form using shortcode attributes.
	return pmpro_login_forms_handler( $show_menu, $show_logout_link, $display_if_logged_in, $location, false );	
}
add_shortcode( 'pmpro_login', 'pmpro_shortcode_login' );
