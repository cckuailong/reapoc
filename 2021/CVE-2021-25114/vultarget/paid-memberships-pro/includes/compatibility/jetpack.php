<?php
/**
 * Jetpack Compatibility.
 *
 * @since 2.6.5
 */

 /**
  * Support Jetpack SSO login for WordPress.com.
  *
  * @since 2.6.4
  */
function pmpro_jetpack_sso_handle_login() {
	global $pmpro_pages, $action;

	// Only do this if we're on the login page.
	if ( empty( $pmpro_pages['login'] ) || ! is_page( $pmpro_pages['login'] ) ) {
		return;
	}

	// Only do this if the sso module is active.
	if ( ! Jetpack::is_module_active( 'sso' ) ) {
		return;
	}

	$action = isset( $_REQUEST['action'] ) ? $_REQUEST['action'] : 'login';

	do_action( 'login_init' );
}

add_action( 'wp', 'pmpro_jetpack_sso_handle_login', 20 );
