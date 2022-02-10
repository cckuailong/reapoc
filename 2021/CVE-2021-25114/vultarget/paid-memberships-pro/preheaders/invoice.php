<?php

global $current_user, $pmpro_invoice;

if ( $current_user->ID ) {
	$current_user->membership_level = pmpro_getMembershipLevelForUser( $current_user->ID );
}

//get invoice from DB
if ( ! empty( $_REQUEST['invoice'] ) ) {
	$invoice_code = sanitize_text_field( $_REQUEST['invoice'] );
} else {
	$invoice_code = NULL;
}

// Redirect non-user to the login page; pass the Invoice page as the redirect_to query arg.
if ( ! is_user_logged_in() ) {
	if ( ! empty( $invoice_code ) ) {
		$invoice_url = add_query_arg( 'invoice', $invoice_code, pmpro_url( 'invoice' ) );
	} else {
		$invoice_url = pmpro_url( 'invoice' );
	}
	wp_redirect( add_query_arg( 'redirect_to', urlencode( $invoice_url ), wp_login_url() ) );
	exit;
}

if ( ! empty( $invoice_code ) ) {
	$pmpro_invoice = new MemberOrder( $invoice_code );

	if ( ! $pmpro_invoice->id ) {
		// Redirect user to the account page if no invoice found.
		wp_redirect( pmpro_url( 'account' ) );
		exit;
	}

	// Make sure they have permission to view this.
	if ( ! current_user_can( 'pmpro_orders' ) && $current_user->ID != $pmpro_invoice->user_id ) {
		wp_redirect( pmpro_url( 'account' ) ); //no permission
		exit;
	}
}
