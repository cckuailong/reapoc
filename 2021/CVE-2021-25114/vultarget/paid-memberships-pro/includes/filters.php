<?php
/*
	This file was added in version 1.5.5 of the plugin. This file is meant to store various hacks, filters, and actions that were originally developed outside of the PMPro core and brought in later... or just things that are cleaner/easier to impement via hooks and filters.
*/

/*
	If checking out for the same level, add remaining days to the enddate.
	Pulled in from: https://gist.github.com/3678054
*/
function pmpro_checkout_level_extend_memberships( $level ) {
	global $pmpro_msg, $pmpro_msgt;

	// does this level expire? are they an existing user of this level?
	if ( ! empty( $level ) && ! empty( $level->expiration_number ) && pmpro_hasMembershipLevel( $level->id ) ) {
		// get the current enddate of their membership
		global $current_user;
		$user_level = pmpro_getSpecificMembershipLevelForUser( $current_user->ID, $level->id );

		// bail if their existing level doesn't have an end date
		if ( empty( $user_level ) || empty( $user_level->enddate ) ) {
			return $level;
		}

		// calculate days left
		$todays_date = strtotime( current_time( 'Y-m-d' ) );
		$expiration_date = strtotime( date( 'Y-m-d', $user_level->enddate ) );
		$time_left = $expiration_date - $todays_date;

		// time left?
		if ( $time_left > 0 ) {
			// convert to days and add to the expiration date (assumes expiration was 1 year)
			$days_left = floor( $time_left / ( 60 * 60 * 24 ) );

			// figure out days based on period
			if ( $level->expiration_period == 'Day' ) {
				$total_days = $days_left + $level->expiration_number;
			} elseif ( $level->expiration_period == 'Week' ) {
				$total_days = $days_left + $level->expiration_number * 7;
			} elseif ( $level->expiration_period == 'Month' ) {
				$total_days = $days_left + $level->expiration_number * 30;
			} elseif ( $level->expiration_period == 'Year' ) {
				$total_days = $days_left + $level->expiration_number * 365;
			}

			// update number and period
			$level->expiration_number = $total_days;
			$level->expiration_period = 'Day';
		}
	}

	return $level;
}
add_filter( 'pmpro_checkout_level', 'pmpro_checkout_level_extend_memberships' );
/*
	Same thing as above but when processed by the ipnhandler for PayPal standard.
*/
function pmpro_ipnhandler_level_extend_memberships( $level, $user_id ) {
	global $pmpro_msg, $pmpro_msgt;

	// does this level expire? are they an existing user of this level?
	if ( ! empty( $level ) && ! empty( $level->expiration_number ) && pmpro_hasMembershipLevel( $level->id, $user_id ) ) {
		// get the current enddate of their membership
		$user_level = pmpro_getSpecificMembershipLevelForUser( $user_id, $level->id );

		// bail if their existing level doesn't have an end date
		if ( empty( $user_level ) || empty( $user_level->enddate ) ) {
			return $level;
		}

		// calculate days left
		$todays_date = current_time( 'timestamp' );
		$expiration_date = $user_level->enddate;
		$time_left = $expiration_date - $todays_date;

		// time left?
		if ( $time_left > 0 ) {
			// convert to days and add to the expiration date (assumes expiration was 1 year)
			$days_left = floor( $time_left / ( 60 * 60 * 24 ) );

			// figure out days based on period
			if ( $level->expiration_period == 'Day' ) {
				$total_days = $days_left + $level->expiration_number;
			} elseif ( $level->expiration_period == 'Week' ) {
				$total_days = $days_left + $level->expiration_number * 7;
			} elseif ( $level->expiration_period == 'Month' ) {
				$total_days = $days_left + $level->expiration_number * 30;
			} elseif ( $level->expiration_period == 'Year' ) {
				$total_days = $days_left + $level->expiration_number * 365;
			}

			// update number and period
			$level->expiration_number = $total_days;
			$level->expiration_period = 'Day';
		}
	}

	return $level;
}
add_filter( 'pmpro_ipnhandler_level', 'pmpro_ipnhandler_level_extend_memberships', 10, 2 );

/*
	If checking out for the same level, keep your old startdate.
	Added with 1.5.5
*/
function pmpro_checkout_start_date_keep_startdate( $startdate, $user_id, $level ) {
	if ( pmpro_hasMembershipLevel( $level->id, $user_id ) ) {
		global $wpdb;
		$sqlQuery = "SELECT startdate FROM $wpdb->pmpro_memberships_users WHERE user_id = '" . esc_sql( $user_id ) . "' AND membership_id = '" . esc_sql( $level->id ) . "' AND status = 'active' ORDER BY id DESC LIMIT 1";
		$old_startdate = $wpdb->get_var( $sqlQuery );

		if ( ! empty( $old_startdate ) ) {
			$startdate = "'" . $old_startdate . "'";
		}
	}

	return $startdate;
}
add_filter( 'pmpro_checkout_start_date', 'pmpro_checkout_start_date_keep_startdate', 10, 3 );

/*
	Stripe Lite Pulled into Core Plugin
*/
// Stripe Lite, Set the Globals/etc
$stripe_billingaddress = pmpro_getOption( 'stripe_billingaddress' );
if ( empty( $stripe_billingaddress ) ) {
	global $pmpro_stripe_lite;
	$pmpro_stripe_lite = true;
	add_filter( 'pmpro_stripe_lite', '__return_true' );
	add_filter( 'pmpro_required_billing_fields', 'pmpro_required_billing_fields_stripe_lite' );
}

// Stripe Lite, Don't Require Billing Fields
function pmpro_required_billing_fields_stripe_lite( $fields ) {
	global $gateway;

	// ignore if not using stripe
	if ( $gateway != 'stripe' ) {
		return $fields;
	}

	// some fields to remove
	$remove = array( 'bfirstname', 'blastname', 'baddress1', 'bcity', 'bstate', 'bzipcode', 'bphone', 'bcountry' );

	// if a user is logged in, don't require bemail either
	global $current_user;
	if ( ! empty( $current_user->user_email ) ) {
		$remove[] = 'bemail';
	}

	// remove the fields
	foreach ( $remove as $field ) {
		unset( $fields[ $field ] );
	}

	// ship it!
	return $fields;
}

// copy other discount code to discount code if latter is not set
if ( empty( $_REQUEST['discount_code'] ) && ! empty( $_REQUEST['other_discount_code'] ) ) {
	$_REQUEST['discount_code'] = $_REQUEST['other_discount_code'];
	$_POST['discount_code'] = $_POST['other_discount_code'];
	$_GET['discount_code'] = $_GET['other_discount_code'];
}

// apply all the_content filters to confirmation messages for levels
function pmpro_pmpro_confirmation_message( $message ) {
	return wpautop( $message );
}
add_filter( 'pmpro_confirmation_message', 'pmpro_pmpro_confirmation_message' );

// apply all the_content filters to level descriptions
function pmpro_pmpro_level_description( $description ) {
	return wpautop( $description );
}
add_filter( 'pmpro_level_description', 'pmpro_pmpro_level_description' );

/*
	PayPal doesn't allow start dates > 1 year out.
	So if we detect that, let's try to squeeze some of
	that time into a trial.

	Otherwise, let's cap at 1 year out.

	Note that this affects PayPal Standard as well, but the fix
	for that flavor of PayPal is different and may be included in future
	updates.
*/
function pmpro_pmpro_subscribe_order_startdate_limit( $order, $gateway ) {
	$affected_gateways = array( 'paypalexpress', 'paypal' );

	if ( in_array( $gateway->gateway, $affected_gateways ) ) {
		$original_start_date = strtotime( $order->ProfileStartDate, current_time( 'timestamp' ) );
		$one_year_out = strtotime( '+1 Year', current_time( 'timestamp' ) );
		$two_years_out = strtotime( '+2 Year', current_time( 'timestamp' ) );
		$one_year_out_date = date_i18n( 'Y-m-d', $one_year_out ) . 'T0:0:0';
		if ( ! empty( $order->ProfileStartDate ) && $order->ProfileStartDate > $one_year_out_date ) {
			// try to squeeze into the trial
			if ( empty( $order->TrialBillingPeriod ) ) {
				// update the order
				$order->TrialAmount = 0;
				$order->TrialBillingPeriod = 'Day';
				$order->TrialBillingFrequency = min( 365, strtotime( $order->ProfileStartDate, current_time( 'timestamp' ) ) );
				$order->TrialBillingCycles = 1;
			}

			// max out at 1 year out no matter what
			$order->ProfileStartDate = $one_year_out_date;

			// if we were going to try to push it more than 2 years out, let's notify the admin
			if ( ! empty( $order->TrialBillilngPeriod ) || $original_start_date > $two_years_out ) {
				// setup user data
				global $current_user;
				if ( empty( $order->user_id ) ) {
					$order->user_id = $current_user->ID;
				}
				$order->getUser();

				// get level data
				$level = pmpro_getLevel( $order->membership_id );

				// create email
				$pmproemail = new PMProEmail();
				$body = '<p>' . __( "There was a potential issue while setting the 'Profile Start Date' for a user's subscription at checkout. PayPal does not allow one to set a Profile Start Date further than 1 year out. Typically, this is not an issue, but sometimes a combination of custom code or add ons for PMPro (e.g. the Prorating or Auto-renewal Checkbox add ons) will try to set a Profile Start Date out past 1 year in order to respect an existing user's original expiration date before they checked out. The user's information is below. PMPro has allowed the checkout and simply restricted the Profile Start Date to 1 year out with a possible additional free Trial of up to 1 year. You should double check this information to determine if maybe the user has overpaid or otherwise needs to be addressed. If you get many of these emails, you should consider adjusting your custom code to avoid these situations.", 'paid-memberships-pro' ) . '</p>';
				$body .= '<p>' . sprintf( __( 'User: %1$s<br />Email: %2$s<br />Membership Level: %3$s<br />Order #: %4$s<br />Original Profile Start Date: %5$s<br />Adjusted Profile Start Date: %6$s<br />Trial Period: %7$s<br />Trial Frequency: %8$s<br />', 'paid-memberships-pro' ), $order->user->user_nicename, $order->user->user_email, $level->name, $order->code, date( 'c', $original_start_date ), $one_year_out_date, $order->TrialBillingPeriod, $order->TrialBillingFrequency ) . '</p>';
				$pmproemail->template = 'profile_start_date_limit_check';
				$pmproemail->subject = sprintf( __( 'Profile Start Date Issue Detected and Fixed at %s', 'paid-memberships-pro' ), get_bloginfo( 'name' ) );
				$pmproemail->data = array( 'body' => $body );
				$pmproemail->sendEmail( get_bloginfo( 'admin_email' ) );
			}
		}
	}

	return $order;
}
add_filter( 'pmpro_subscribe_order', 'pmpro_pmpro_subscribe_order_startdate_limit', 99, 2 );

/**
 * Before changing membership at checkout,
 * let's remember the order for checkout
 * so we can ignore that when cancelling old orders.
 */
function pmpro_set_checkout_order_before_changing_membership_levels( $user_id, $order ) {
	global $pmpro_checkout_order;
	$pmpro_checkout_order = $order;
}
add_action( 'pmpro_checkout_before_change_membership_level', 'pmpro_set_checkout_order_before_changing_membership_levels', 10, 2);

/**
 * Ignore the checkout order when cancelling old orders.
 */
function pmpro_ignore_checkout_order_when_cancelling_old_orders( $order_ids ) {
	global $pmpro_checkout_order;

	if ( ! empty( $pmpro_checkout_order ) && ! empty( $pmpro_checkout_order->id ) ) {
		$order_ids = array_diff( $order_ids, array( $pmpro_checkout_order->id ) );
	}

	return $order_ids;
}
add_filter( 'pmpro_other_order_ids_to_cancel', 'pmpro_ignore_checkout_order_when_cancelling_old_orders' );