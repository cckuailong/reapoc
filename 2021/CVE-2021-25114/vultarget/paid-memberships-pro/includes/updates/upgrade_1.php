<?php
function pmpro_upgrade_1()
{
	/*
		default options
	*/
	$nonmembertext = sprintf( __( 'This content is for !!levels!! members only.<br /><a href="%s">Join Now</a>', 'paid-memberships-pro' ), "!!levels_page_url!!" );
	pmpro_setOption("nonmembertext", $nonmembertext);

	$notloggedintext = sprintf( __( 'This content is for !!levels!! members only.<br /><a href="%s">Login</a> <a href="%s">Join Now</a>', 'paid-memberships-pro' ), '!!login_url!!', "!!levels_page_url!!" );
	pmpro_setOption("notloggedintext", $notloggedintext);

	$rsstext = __( 'This content is for members only. Visit the site and log in/register to read.', 'paid-memberships-pro' );
	pmpro_setOption("rsstext", $rsstext);

	$gateway_environment = "sandbox";
	pmpro_setOption("gateway_environment", $gateway_environment);

	$pmpro_currency = "USD";
	pmpro_setOption("currency", $pmpro_currency);

	$pmpro_accepted_credit_cards = "Visa,Mastercard,American Express,Discover";
	pmpro_setOption("accepted_credit_cards", $pmpro_accepted_credit_cards);

	$parsed = parse_url( home_url() );
	$hostname = $parsed['host'];
	$host_parts = explode( ".", $hostname );
	if ( count( $host_parts ) > 1 ) {
		$email_domain = $host_parts[count($host_parts) - 2] . "." . $host_parts[count($host_parts) - 1];
	} else {
		$email_domain = $parsed['host'];
	}
	
	$from_email = "wordpress@" . $email_domain;
	pmpro_setOption("from_email", $from_email);

	$from_name = "WordPress";
	pmpro_setOption("from_name", $from_name);

	//setting new email settings defaults
	pmpro_setOption("email_admin_checkout", "1");
	pmpro_setOption("email_admin_changes", "1");
	pmpro_setOption("email_admin_cancels", "1");
	pmpro_setOption("email_admin_billing", "1");
	pmpro_setOption("tospage", "");
	
	//don't want these pointers to show on new installs
	update_option( 'pmpro_dismissed_wp_pointers', array( 'pmpro_v2_menu_moved' ) );

	//let's pause the nag for the first week of use
	$pmpro_nag_paused = current_time('timestamp')+(3600*24*7);
	update_option('pmpro_nag_paused', $pmpro_nag_paused, 'no');

	//db update
	pmpro_db_delta();

	//update version and return
	pmpro_setOption("db_version", "1.71");		//no need to run other updates
	return 1.71;
}
