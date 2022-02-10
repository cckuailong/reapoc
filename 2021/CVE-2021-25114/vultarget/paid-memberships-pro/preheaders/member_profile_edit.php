<?php

// Redirect to login.
if ( ! is_user_logged_in() ) {
	$redirect = apply_filters( 'pmpro_member_profile_edit_preheader_redirect', pmpro_login_url() );
	if ( $redirect ) {
		wp_redirect( $redirect );
		exit;
	}
}