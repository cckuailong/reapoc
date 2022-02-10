<?php
/**
 * Upgrade to 2.6
 * We changed the pmpro_cron_expire_memberships cron
 * to run hourly instead of daily.
 * To ensure that existing members still expire at least
 * 1 calendar day after their expiration date, we are
 * updating old expiration date timestamps to set
 * the time component to 11:59. This way e.g.
 * someone who checked out at 3pm on Dec 31 won't expire
 * until Jan 1 at midnight.
 * Going forward, we will always set the expiration time to 11:59
 * unless the level is set up to expire hourly.
 */
function pmpro_upgrade_2_6() {
	// Map email settings to new email template settings.
	// Note: the old settings were true to enable, the new settings are true to disable.
	$admin_checkout = pmpro_getOption( 'email_admin_checkout' );
	if ( empty( $admin_checkout ) ) {
		pmpro_setOption( 'email_checkout_check_admin_disabled', 'true' );
		pmpro_setOption( 'email_checkout_express_admin_disabled', 'true' );
		pmpro_setOption( 'email_checkout_free_admin_disabled', 'true' );
		pmpro_setOption( 'email_checkout_freetrial_admin_disabled', 'true' );
		pmpro_setOption( 'email_checkout_paid_admin_disabled', 'true' );
		pmpro_setOption( 'email_checkout_trial_admin_disabled', 'true' );
	}
	$admin_changes = pmpro_getOption( 'email_admin_changes' );
	if ( empty( $admin_changes ) ) {
		pmpro_setOption( 'email_admin_change_admin_disabled', 'true' );
	}
	$admin_cancels = pmpro_getOption( 'email_admin_cancels' );
	if ( empty( $admin_cancels ) ) {
		pmpro_setOption( 'email_cancel_admin_disabled', 'true' );
	}
	$admin_billing = pmpro_getOption( 'email_admin_billing' );
	if ( empty( $admin_billing ) ) {
		pmpro_setOption( 'email_billing_admin_disabled', 'true' );
	}
	
	// Reschedule cron job for hourly checks.
	$next = wp_next_scheduled( 'pmpro_cron_expire_memberships' );
	if ( ! empty( $next ) ) {
		wp_unschedule_event( $next, 'pmpro_cron_expire_memberships' );
	}
	pmpro_maybe_schedule_event( current_time( 'timestamp' ), 'hourly', 'pmpro_cron_expire_memberships' );
}