<?php
/*
	Expiring Memberships
*/	
add_action("pmpro_cron_expire_memberships", "pmpro_cron_expire_memberships");
function pmpro_cron_expire_memberships()
{
	global $wpdb;

	//clean up errors in the memberships_users table that could cause problems
	pmpro_cleanup_memberships_users_table();

	$today = date("Y-m-d H:i:00", current_time("timestamp"));

	//look for memberships that expired before today
	$sqlQuery = "SELECT mu.user_id, mu.membership_id, mu.startdate, mu.enddate FROM $wpdb->pmpro_memberships_users mu WHERE mu.status = 'active' AND mu.enddate IS NOT NULL AND mu.enddate <> '0000-00-00 00:00:00' AND mu.enddate <= '" . esc_sql( $today ) . "' ORDER BY mu.enddate";

	if(defined('PMPRO_CRON_LIMIT'))
		$sqlQuery .= " LIMIT " . PMPRO_CRON_LIMIT;
	$expired = $wpdb->get_results($sqlQuery);

	foreach($expired as $e)
	{
		do_action("pmpro_membership_pre_membership_expiry", $e->user_id, $e->membership_id );		

		//remove their membership
		pmpro_changeMembershipLevel(false, $e->user_id, 'expired', $e->membership_id);

		do_action("pmpro_membership_post_membership_expiry", $e->user_id, $e->membership_id );

		if( get_user_meta( $e->user_id, 'pmpro_disable_notifications', true ) ){
			$send_email = false;
		}
		
		$send_email = apply_filters("pmpro_send_expiration_email", true, $e->user_id);

		if($send_email)
		{
			//send an email
			$pmproemail = new PMProEmail();
			$euser = get_userdata($e->user_id);
			if ( ! empty( $euser ) ) {
				$pmproemail->sendMembershipExpiredEmail($euser);

				if(current_user_can('manage_options')) {
					printf(__("Membership expired email sent to %s. ", 'paid-memberships-pro' ), $euser->user_email);
				} else {
					echo ". ";
				}
			}
		}
	}
}

/*
	Expiration Warning Emails
*/
add_action("pmpro_cron_expiration_warnings", "pmpro_cron_expiration_warnings");
function pmpro_cron_expiration_warnings()
{
	global $wpdb;

	//clean up errors in the memberships_users table that could cause problems
	pmpro_cleanup_memberships_users_table();

	$today = date("Y-m-d H:i:s", current_time("timestamp"));

	$pmpro_email_days_before_expiration = apply_filters("pmpro_email_days_before_expiration", 7);

	// Configure the interval to select records from
	$interval_start = $today;
	$interval_end = date( 'Y-m-d H:i:s', strtotime( "{$today} +{$pmpro_email_days_before_expiration} days", current_time( 'timestamp' ) ) );

	//look for memberships that are going to expire within one week (but we haven't emailed them within a week)
	$sqlQuery = $wpdb->prepare(
		"SELECT DISTINCT
  				mu.user_id,
  				mu.membership_id,
  				mu.startdate,
 				mu.enddate,
 				um.meta_value AS notice
 			FROM {$wpdb->pmpro_memberships_users} AS mu
 			  LEFT JOIN {$wpdb->usermeta} AS um ON um.user_id = mu.user_id
            	AND um.meta_key = %s
			WHERE ( um.meta_value IS NULL OR DATE_ADD(um.meta_value, INTERVAL %d DAY) < %s )
				AND ( mu.status = 'active' )
 			    AND ( mu.enddate IS NOT NULL )
 			    AND ( mu.enddate <> '0000-00-00 00:00:00' )
 			    AND ( mu.enddate BETWEEN %s AND %s )
 			    AND ( mu.membership_id <> 0 OR mu.membership_id <> NULL )
			ORDER BY mu.enddate
			",
		"pmpro_expiration_notice",
		$pmpro_email_days_before_expiration,
		$today,
		$interval_start,
		$interval_end
	);

	if(defined('PMPRO_CRON_LIMIT'))
		$sqlQuery .= " LIMIT " . PMPRO_CRON_LIMIT;

	$expiring_soon = $wpdb->get_results($sqlQuery);

	foreach($expiring_soon as $e)
	{
		$send_email = apply_filters("pmpro_send_expiration_warning_email", true, $e->user_id);
		if($send_email)
		{
			//send an email
			$pmproemail = new PMProEmail();
			$euser = get_userdata($e->user_id);
			if ( ! empty( $euser ) ) {
				$pmproemail->sendMembershipExpiringEmail($euser);

				if(current_user_can('manage_options')) {
					printf(__("Membership expiring email sent to %s. ", 'paid-memberships-pro' ), $euser->user_email);
				} else {
					echo ". ";
				}
			}
		}

		//delete all user meta for this key to prevent duplicate user meta rows
		delete_user_meta($e->user_id, "pmpro_expiration_notice");

		//update user meta so we don't email them again
		update_user_meta($e->user_id, "pmpro_expiration_notice", $today);
	}
}

/*
	Credit Card Expiring Warnings
*/
add_action("pmpro_cron_credit_card_expiring_warnings", "pmpro_cron_credit_card_expiring_warnings");
function pmpro_cron_credit_card_expiring_warnings()
{
	global $wpdb;

	//clean up errors in the memberships_users table that could cause problems
	pmpro_cleanup_memberships_users_table();

	$next_month_date = date("Y-m-01", strtotime("+2 months", current_time("timestamp")));

	$sqlQuery = "SELECT mu.user_id
					FROM  $wpdb->pmpro_memberships_users mu
						LEFT JOIN $wpdb->usermeta um1 ON mu.user_id = um1.user_id
							AND meta_key =  'pmpro_ExpirationMonth'
						LEFT JOIN $wpdb->usermeta um2 ON mu.user_id = um2.user_id
							AND um2.meta_key =  'pmpro_ExpirationYear'
						LEFT JOIN $wpdb->usermeta um3 ON mu.user_id = um3.user_id
							AND um3.meta_key = 'pmpro_credit_card_expiring_warning'
					WHERE mu.status =  'active'
						AND mu.cycle_number > 0
						AND um1.meta_value IS NOT NULL AND um2.meta_value IS NOT NULL
						AND um1.meta_value <> '' AND um2.meta_value <> ''
						AND CONCAT(um2.meta_value, '-', um1.meta_value, '-01') < '" . esc_sql( $next_month_date ) . "'
						AND (um3.meta_value IS NULL OR CONCAT(um2.meta_value, '-', um1.meta_value, '-01') <> um3.meta_value)
				";

	if(defined('PMPRO_CRON_LIMIT'))
		$sqlQuery .= " LIMIT " . PMPRO_CRON_LIMIT;

	$cc_expiring_user_ids = $wpdb->get_col($sqlQuery);

	if(!empty($cc_expiring_user_ids))
	{
		require_once(ABSPATH . 'wp-includes/pluggable.php');

		foreach($cc_expiring_user_ids as $user_id)
		{
			//get user
			$euser = get_userdata($user_id);
			if ( empty( $euser ) ) {
				continue;
			}

			//make sure their level doesn't have a billing limit that's been reached
			$euser->membership_level = pmpro_getMembershipLevelForUser($euser->ID);
			if(!empty($euser->membership_level->billing_limit))
			{
				/*
					There is a billing limit on this level, skip for now.
					We should figure out how to tell if the limit has been reached
					and if not, email the user about the expiring credit card.
				*/
				continue;
			}

			//make sure they are using a credit card type billing method for their current membership level (check the last order)
			$last_order = new MemberOrder();
			$last_order->getLastMemberOrder($euser->ID);
			if(empty($last_order->accountnumber) || (!empty($last_order->accountnumber) && 'XXXXXXXXXXXXXXXX' == $last_order->accountnumber))
				continue;

			//okay send them an email
			$send_email = apply_filters("pmpro_send_credit_card_expiring_email", true, $euser->ID);

			if($send_email)
			{
				//send an email
				$pmproemail = new PMProEmail();
				$pmproemail->sendCreditCardExpiringEmail($euser,$last_order);

				if(current_user_can('manage_options')) {
					printf(__("Credit card expiring email sent to %s. ", 'paid-memberships-pro' ), $euser->user_email);
				} else {
					echo ". ";
				}
			}

			//update user meta so we don't email them again
			update_user_meta($euser->ID, "pmpro_credit_card_expiring_warning", $euser->pmpro_ExpirationYear . "-" . $euser->pmpro_ExpirationMonth . "-01");
		}
	}
}

/*
	Trial Ending Emails
	Commented out as of version 1.7.2 since this caused issues on some sites
	and doesn't take into account the many "custom trial" solutions that are
	in the wild (e.g. some trials are actually a delay of the subscription start date)
*/
//add_action("pmpro_cron_trial_ending_warnings", "pmpro_cron_trial_ending_warnings");
function pmpro_cron_trial_ending_warnings()
{
	global $wpdb;

	//clean up errors in the memberships_users table that could cause problems
	pmpro_cleanup_memberships_users_table();

	$today = date("Y-m-d 00:00:00", current_time("timestamp"));

	$pmpro_email_days_before_trial_end = apply_filters("pmpro_email_days_before_trial_end", 7);

	//look for memberships with trials ending soon (but we haven't emailed them within a week)
	$sqlQuery = "
	SELECT
		mu.user_id, mu.membership_id, mu.startdate, mu.cycle_period, mu.trial_limit FROM $wpdb->pmpro_memberships_users mu LEFT JOIN $wpdb->usermeta um ON um.user_id = mu.user_id AND um.meta_key = 'pmpro_trial_ending_notice'
	WHERE
		mu.status = 'active' AND mu.trial_limit IS NOT NULL AND mu.trial_limit > 0 AND
		(
			(cycle_period = 'Day' AND DATE_ADD(mu.startdate, INTERVAL mu.trial_limit Day) <= DATE_ADD('" . esc_sql( $today ) . "', INTERVAL " . $pmpro_email_days_before_trial_end . " Day)) OR
			(cycle_period = 'Week' AND DATE_ADD(mu.startdate, INTERVAL mu.trial_limit Week) <= DATE_ADD('" . esc_sql( $today ) . "', INTERVAL " . $pmpro_email_days_before_trial_end . " Day)) OR
			(cycle_period = 'Month' AND DATE_ADD(mu.startdate, INTERVAL mu.trial_limit Month) <= DATE_ADD('" . esc_sql( $today ) . "', INTERVAL " . $pmpro_email_days_before_trial_end . " Day)) OR
			(cycle_period = 'Year' AND DATE_ADD(mu.startdate, INTERVAL mu.trial_limit Year) <= DATE_ADD('" . esc_sql( $today ) . "', INTERVAL " . $pmpro_email_days_before_trial_end . " Day))
		)

		AND (um.meta_value IS NULL OR um.meta_value = '' OR DATE_ADD(um.meta_value, INTERVAL " . $pmpro_email_days_before_trial_end . " Day) <= '" . $today . "')
	ORDER BY mu.startdate";

	$trial_ending_soon = $wpdb->get_results($sqlQuery);

	foreach($trial_ending_soon as $e)
	{
		$send_email = apply_filters("pmpro_send_trial_ending_email", true, $e->user_id);
		if($send_email)
		{
			//send an email
			$pmproemail = new PMProEmail();
			$euser = get_userdata($e->user_id);
			if ( ! empty( $euser ) ) {
				$pmproemail->sendTrialEndingEmail($euser);

				if(current_user_can('manage_options')) {
					printf(__("Trial ending email sent to %s. ", 'paid-memberships-pro' ), $euser->user_email);
				} else {
					echo ". ";
				}
			}
		}

		//update user meta so we don't email them again
		update_user_meta($e->user_id, "pmpro_trial_ending_notice", $today);
	}
}

add_action( 'pmpro_cron_admin_activity_email', 'pmpro_cron_admin_activity_email' );
function pmpro_cron_admin_activity_email() {
	$frequency = pmpro_getOption( 'activity_email_frequency' );
	if ( empty( $frequency ) ) {
		$frequency = 'week';
	}
	// Send every day, Monday each week, or first Monday of every month.
	if (
		'day' === $frequency ||
		( 'week' === $frequency && 'Mon' === date( 'D' ) ) ||
		( 'month' === $frequency && 'Mon' === date( 'D' ) && 7 >= date( 'j' ) )
	) {
		$pmproemail = new PMPro_Admin_Activity_Email();
		$pmproemail->sendAdminActivity();
	}
}
