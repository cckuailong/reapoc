<?php
global $current_user, $pmpro_msg, $pmpro_msgt, $pmpro_levels, $pmpro_pages;

// Redirect to login.
if ( ! is_user_logged_in() ) {
	$redirect = apply_filters( 'pmpro_account_preheader_redirect', pmpro_login_url( get_permalink( $pmpro_pages['account'] ) ) );
	if ( $redirect ) {
		wp_redirect( $redirect );
		exit;
	}
}

// Make sure the membership level is set for the user.
if( $current_user->ID ) {
    $current_user->membership_level = pmpro_getMembershipLevelForUser( $current_user->ID );
}

// Process the msg param.
if ( isset($_REQUEST['msg'] ) ) {
    if ( $_REQUEST['msg'] == 1 ) {
        $pmpro_msg = __( 'Your membership status has been updated - Thank you!', 'paid-memberships-pro' );
    } else {
        $pmpro_msg = __( 'Sorry, your request could not be completed - please try again in a few moments.', 'paid-memberships-pro' );
        $pmpro_msgt = 'pmpro_error';
    }
} else {
    $pmpro_msg = false;
}

/**
 * Check if the current logged in user has a membership level.
 * If not, and the site is using the pmpro_account_preheader_redirect
 * filter, redirect to that page.
 */
if ( ! empty( $current_user->ID ) && empty( $current_user->membership_level->ID ) ) {
	$redirect = apply_filters( 'pmpro_account_preheader_redirect', false );
	if ( $redirect ) {
		wp_redirect( $redirect );
		exit;
	}
}

/**
 * Add-Ons might need this global to be set.
 */
$pmpro_levels = pmpro_getAllLevels();