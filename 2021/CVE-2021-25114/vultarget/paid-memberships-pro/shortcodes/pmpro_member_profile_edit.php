<?php
/**
 * Display a Member Profile Form that allows members to edit their information on the front end.
 * Supports the core WordPress User fields that PMPro uses.
 * Add Ons and other plugins can hook into this form using the pmpro_show_user_profile action.
 *
 */
function pmpro_shortcode_member_profile_edit( $atts, $content=null, $code='' ) {
	// $content ::= text within enclosing form of shortcode element
	// $code    ::= the shortcode found, when == callback name
	// examples: [pmpro_member_profile_edit]

	ob_start();

	// Get the current action for the view.
	if ( ! empty( $_REQUEST[ 'view' ] ) ) {
		$view = sanitize_text_field( $_REQUEST[ 'view' ] );
	} else {
		$view = NULL;
	}

	if ( ! empty( $view ) && $view == 'change-password' ) {
		// Display the Change Password form.
		pmpro_change_password_form();
	} else {
		// Display the Member Profile Edit form.
		pmpro_member_profile_edit_form();
	}

	$content = ob_get_contents();
	ob_end_clean();

	return $content;
}
add_shortcode( 'pmpro_member_profile_edit', 'pmpro_shortcode_member_profile_edit' );
