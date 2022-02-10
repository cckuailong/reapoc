<?php

function swpm_handle_subsc_signup_stand_alone( $ipn_data, $subsc_ref, $unique_ref, $swpm_id = '' ) {
	global $wpdb;
	$settings         = SwpmSettings::get_instance();
	$membership_level = $subsc_ref;

	if ( isset( $ipn_data['subscr_id'] ) && ! empty( $ipn_data['subscr_id'] ) ) {
		$subscr_id = $ipn_data['subscr_id'];
	} else {
		$subscr_id = $unique_ref;
	}

	swpm_debug_log_subsc( 'swpm_handle_subsc_signup_stand_alone(). Custom value: ' . $ipn_data['custom'] . ', Unique reference: ' . $unique_ref, true );
	parse_str( $ipn_data['custom'], $custom_vars );

	if ( empty( $swpm_id ) ) {
		// Lets try to find an existing user profile for this payment.
		$email    = $ipn_data['payer_email'];
		$query_db = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}swpm_members_tbl WHERE email = %s", $email ), OBJECT ); // db call ok; no-cache ok.
		if ( ! $query_db ) { // try to retrieve the member details based on the unique_ref.
			swpm_debug_log_subsc( 'Could not find any record using the given email address (' . $email . '). Attempting to query database using the unique reference: ' . $unique_ref, true );
			if ( ! empty( $unique_ref ) ) {
				$query_db = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}swpm_members_tbl WHERE subscr_id = %s", $unique_ref ), OBJECT ); // db call ok; no-cache ok.
				if ( $query_db ) {
					$swpm_id = $query_db->member_id;
					swpm_debug_log_subsc( 'Found a match in the member database using unique reference. Member ID: ' . $swpm_id, true );
				} else {
					swpm_debug_log_subsc( 'Did not find a match for an existing member profile for the given reference. This must be a new payment from a new member.', true );
				}
			} else {
				swpm_debug_log_subsc( 'Unique reference is missing in the notification so we have to assume that this is not a payment for an existing member.', true );
			}
		} else {
			$swpm_id = $query_db->member_id;
			swpm_debug_log_subsc( 'Found a match in the member database. Member ID: ' . $swpm_id, true );
		}
	}

	if ( ! empty( $swpm_id ) ) {
		// This is payment from an existing member/user. Update the existing member account.
		swpm_debug_log_subsc( 'Modifying the existing membership profile... Member ID: ' . $swpm_id, true );

		// Upgrade the member account.
		$account_state = 'active'; // This is renewal or upgrade of a previously active account. So the status should be set to active.

		$resultset = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}swpm_members_tbl WHERE member_id = %d", $swpm_id ), OBJECT );
		if ( ! $resultset ) {
			swpm_debug_log_subsc( 'ERROR! Could not find a member account record for the given Member ID: ' . $swpm_id, false );
			return;
		}
		$old_membership_level = $resultset->membership_level;

		// If the payment is for the same/existing membership level, then this is a renewal. Refresh the start date as appropriate.
		$args                = array(
			'swpm_id'              => $swpm_id,
			'membership_level'     => $membership_level,
			'old_membership_level' => $old_membership_level,
		);
		$subscription_starts = SwpmMemberUtils::calculate_access_start_date_for_account_update( $args );
		$subscription_starts = apply_filters( 'swpm_account_update_subscription_starts', $subscription_starts, $args );
		swpm_debug_log_subsc( 'Setting access starts date value to: ' . $subscription_starts, true );

		swpm_debug_log_subsc( 'Updating the current membership level (' . $old_membership_level . ') of this member to the newly paid level (' . $membership_level . ')', true );
		// Set account status to active, update level to the newly paid level, update access start date, update subsriber ID (if applicable).
		$wpdb->query(
			$wpdb->prepare(
				"UPDATE {$wpdb->prefix}swpm_members_tbl SET account_state=%s, membership_level=%d,subscription_starts=%s,subscr_id=%s WHERE member_id=%d",
				$account_state,
				$membership_level,
				$subscription_starts,
				$subscr_id,
				$swpm_id
			)
		);

		// Trigger level changed/updated action hook.
		do_action(
			'swpm_membership_level_changed',
			array(
				'member_id'  => $swpm_id,
				'from_level' => $old_membership_level,
				'to_level'   => $membership_level,
			)
		);

		// Set Email details for the account upgrade notification.
		$email   = $ipn_data['payer_email'];
		$subject = $settings->get_value( 'upgrade-complete-mail-subject' );
		if ( empty( $subject ) ) {
			$subject = 'Member Account Upgraded';
		}
		$body = $settings->get_value( 'upgrade-complete-mail-body' );
		if ( empty( $body ) ) {
			$body = 'Your account has been upgraded successfully';
		}
		$from_address = $settings->get_value( 'email-from' );

		$additional_args = array();
		$email_body      = SwpmMiscUtils::replace_dynamic_tags( $body, $swpm_id, $additional_args );
		$headers         = 'From: ' . $from_address . "\r\n";

				$subject    = apply_filters( 'swpm_email_upgrade_complete_subject', $subject );
				$email_body = apply_filters( 'swpm_email_upgrade_complete_body', $email_body );

		if ( $settings->get_value( 'disable-email-after-upgrade' ) ) {
			swpm_debug_log_subsc( 'The disable upgrade email settings is checked. No account upgrade/update email will be sent.', true );
			//Nothing to do.
		} else {
			SwpmMiscUtils::mail( $email, $subject, $email_body, $headers );
			swpm_debug_log_subsc( 'Member upgrade/update completion email successfully sent to: ' . $email, true );
		}
		// End of existing user account upgrade/update.
	} else {
		// create new member account.
		$default_account_status = $settings->get_value( 'default-account-status', 'active' );

		$data              = array();
		$data['user_name'] = '';
		$data['password']  = '';

		$data['first_name']       = $ipn_data['first_name'];
		$data['last_name']        = $ipn_data['last_name'];
		$data['email']            = $ipn_data['payer_email'];
		$data['membership_level'] = $membership_level;
		$data['subscr_id']        = $subscr_id;

		$data['gender']                = 'not specified';
		$data['address_street']        = $ipn_data['address_street'];
		$data['address_city']          = $ipn_data['address_city'];
		$data['address_state']         = $ipn_data['address_state'];
		$data['address_zipcode']       = isset( $ipn_data['address_zip'] ) ? $ipn_data['address_zip'] : '';
		$data['country']               = isset( $ipn_data['address_country'] ) ? $ipn_data['address_country'] : '';
		$data['member_since']          = $data['subscription_starts'] = $data['last_accessed'] = SwpmUtils::get_current_date_in_wp_zone();
		$data['account_state']         = $default_account_status;
		$reg_code                      = uniqid();
		$md5_code                      = md5( $reg_code );
		$data['reg_code']              = $md5_code;
		$data['referrer']              = $data['extra_info'] = $data['txn_id'] = '';
		$data['last_accessed_from_ip'] = isset( $custom_vars['user_ip'] ) ? $custom_vars['user_ip'] : ''; // Save the users IP address.

		swpm_debug_log_subsc( 'Creating new member account. Membership level ID: ' . $membership_level . ', Subscriber ID value: ' . $data['subscr_id'], true );

		$data = array_filter( $data ); // Remove any null values.
		$wpdb->insert( "{$wpdb->prefix}swpm_members_tbl", $data ); // Create the member record.
		$id = $wpdb->insert_id;
		if ( empty( $id ) ) {
			swpm_debug_log_subsc( 'Error! Failed to insert a new member record. This request will fail.', false );
			return;
		}

		$separator = '?';
		$url       = $settings->get_value( 'registration-page-url' );
		if ( strpos( $url, '?' ) !== false ) {
			$separator = '&';
		}

		$reg_url = $url . $separator . 'member_id=' . $id . '&code=' . $md5_code;
		swpm_debug_log_subsc( 'Member signup URL: ' . $reg_url, true );

		$subject = $settings->get_value( 'reg-prompt-complete-mail-subject' );
		if ( empty( $subject ) ) {
			$subject = 'Please complete your registration';
		}
		$body = $settings->get_value( 'reg-prompt-complete-mail-body' );
		if ( empty( $body ) ) {
			$body = "Please use the following link to complete your registration. \n {reg_link}";
		}
		$from_address = $settings->get_value( 'email-from' );
		$body         = html_entity_decode( $body );

		$additional_args = array( 'reg_link' => $reg_url );
		$email_body      = SwpmMiscUtils::replace_dynamic_tags( $body, $id, $additional_args );
		$headers         = 'From: ' . $from_address . "\r\n";

				$subject    = apply_filters( 'swpm_email_complete_registration_subject', $subject );
				$email_body = apply_filters( 'swpm_email_complete_registration_body', $email_body );
		if ( empty( $email_body ) ) {
			swpm_debug_log_subsc( 'Notice: Member signup (prompt to complete registration) email body has been set empty via the filter hook. No email will be sent.', true );
		} else {
			SwpmMiscUtils::mail( $email, $subject, $email_body, $headers );
			swpm_debug_log_subsc( 'Member signup (prompt to complete registration) email successfully sent to: ' . $email, true );
		}
	}

}

/*
 * All in one function that can handle notification for refund, cancellation, end of term
 */

function swpm_handle_subsc_cancel_stand_alone( $ipn_data, $refund = false ) {

	global $wpdb;

        $swpm_id = '';
        if ( isset( $ipn_data['custom'] ) ){
            $customvariables = SwpmTransactions::parse_custom_var( $ipn_data['custom'] );
            $swpm_id         = $customvariables['swpm_id'];
        }

	swpm_debug_log_subsc( 'Refund/Cancellation check - lets see if a member account needs to be deactivated.', true );
	// swpm_debug_log_subsc("Parent txn id: " . $ipn_data['parent_txn_id'] . ", Subscr ID: " . $ipn_data['subscr_id'] . ", SWPM ID: " . $swpm_id, true);.

	if ( ! empty( $swpm_id ) ) {
		// This IPN has the SWPM ID. Retrieve the member record using member ID.
		swpm_debug_log_subsc( 'Member ID is present. Retrieving member account from the database. Member ID: ' . $swpm_id, true );
		$resultset = SwpmMemberUtils::get_user_by_id( $swpm_id );
	} elseif ( isset( $ipn_data['subscr_id'] ) && ! empty( $ipn_data['subscr_id'] ) ) {
		// This IPN has the subscriber ID. Retrieve the member record using subscr_id.
		$subscr_id = $ipn_data['subscr_id'];
		swpm_debug_log_subsc( 'Subscriber ID is present. Retrieving member account from the database. Subscr_id: ' . $subscr_id, true );
		$resultset = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT * FROM {$wpdb->prefix}swpm_members_tbl where subscr_id LIKE %s",
				'%' . $wpdb->esc_like( $subscr_id ) . '%'
			),
			OBJECT
		);
	} else {
		// Refund for a one time transaction. Use the parent transaction ID to retrieve the profile.
		$subscr_id = $ipn_data['parent_txn_id'];
		$resultset = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT * FROM {$wpdb->prefix}swpm_members_tbl where subscr_id LIKE %s",
				'%' . $wpdb->esc_like( $subscr_id ) . '%'
			),
			OBJECT
		);
	}

	if ( $resultset ) {
		// We have found a member profile for this notification.

		$member_id = $resultset->member_id;

		// First, check if this is a refund notification.
		if ( $refund ) {
			// This is a refund (not just a subscription cancellation or end). So deactivate the account regardless and bail.
			SwpmMemberUtils::update_account_state( $member_id, 'inactive' ); // Set the account status to inactive.
			swpm_debug_log_subsc( 'Subscription refund notification received! Member account deactivated.', true );
			return;
		}

		// This is a cancellation or end of subscription term (no refund).
		// Lets retrieve the membership level and details.
		$level_id = $resultset->membership_level;
		swpm_debug_log_subsc( 'Membership level ID of the member is: ' . $level_id, true );
		$level_row          = SwpmUtils::get_membership_level_row_by_id( $level_id );
		$subs_duration_type = $level_row->subscription_duration_type;

		swpm_debug_log_subsc( 'Subscription duration type: ' . $subs_duration_type, true );

		if ( SwpmMembershipLevel::NO_EXPIRY == $subs_duration_type ) {
			// This is a level with "no expiry" or "until cancelled" duration.
			swpm_debug_log_subsc( 'This is a level with "no expiry" or "until cancelled" duration', true );

			// Deactivate this account as the membership level is "no expiry" or "until cancelled".
			$account_state = 'inactive';
			SwpmMemberUtils::update_account_state( $member_id, $account_state );
			swpm_debug_log_subsc( 'Subscription cancellation or end of term received! Member account deactivated. Member ID: ' . $member_id, true );
		} elseif ( SwpmMembershipLevel::FIXED_DATE == $subs_duration_type ) {
			// This is a level with a "fixed expiry date" duration.
			swpm_debug_log_subsc( 'This is a level with a "fixed expiry date" duration.', true );
			swpm_debug_log_subsc( 'Nothing to do here. The account will expire on the fixed set date.', true );
		} else {
			// This is a level with "duration" type expiry (example: 30 days, 1 year etc). subscription_period has the duration/period.
			$subs_period      = $level_row->subscription_period;
			$subs_period_unit = SwpmMembershipLevel::get_level_duration_type_string( $level_row->subscription_duration_type );

			swpm_debug_log_subsc( 'This is a level with "duration" type expiry. Duration period: ' . $subs_period . ', Unit: ' . $subs_period_unit, true );
			swpm_debug_log_subsc( 'Nothing to do here. The account will expire after the duration time is over.', true );

			// TODO Later as an improvement. If you wanted to segment the members who have unsubscribed, you can set the account status to "unsubscribed" here.
			// Make sure the cronjob to do expiry check and deactivate the member accounts treat this status as if it is "active".
		}

		$ipn_data['member_id'] = $member_id;
		do_action( 'swpm_subscription_payment_cancelled', $ipn_data ); // Hook for recurring payment received.
	} else {
		swpm_debug_log_subsc( 'No associated active member record found for this notification.', false );
		return;
	}
}

function swpm_update_member_subscription_start_date_if_applicable( $ipn_data ) {
	global $wpdb;
	$email = isset( $ipn_data['payer_email'] ) ? $ipn_data['payer_email'] : '';
	$subscr_id = $ipn_data['subscr_id'];
	$account_state = SwpmSettings::get_instance()->get_value( 'default-account-status', 'active' );
        $account_state = apply_filters( 'swpm_account_status_for_subscription_start_date_update', $account_state );

	swpm_debug_log_subsc( 'Updating subscription start date if applicable for this subscription payment. Subscriber ID: ' . $subscr_id . ', Email: ' . $email . ', Account status: ' . $account_state, true );

	// We can also query using the email address or SWPM ID (if present in custom var).

        //Try to find the profile with the given subscr_id. It will exact match subscr_id or match subscr_id|123
        $query_db = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}swpm_members_tbl WHERE subscr_id = %s OR subscr_id LIKE %s", $subscr_id, $subscr_id.'|%' ), OBJECT );
	if ( $query_db ) {
		$swpm_id               = $query_db->member_id;
		$current_primary_level = $query_db->membership_level;
		swpm_debug_log_subsc( 'Found a record in the member table. The Member ID of the account to check is: ' . $swpm_id . ' Membership Level: ' . $current_primary_level, true );

		$ipn_data['member_id'] = $swpm_id;
		do_action( 'swpm_recurring_payment_received', $ipn_data ); // Hook for recurring payment received.

		$subscription_starts = SwpmUtils::get_current_date_in_wp_zone();

		$wpdb->query(
			$wpdb->prepare(
				"UPDATE {$wpdb->prefix}swpm_members_tbl SET account_state=%s,subscription_starts=%s WHERE member_id=%d",
				$account_state,
				$subscription_starts,
				$swpm_id
			)
		);
		swpm_debug_log_subsc( 'Updated the member profile with current date as the subscription start date.', true );
		// Lets check to see if the subscriber ID and the subscription start date value was updated correctly.
		$member_record = SwpmMemberUtils::get_user_by_id( $swpm_id );
		swpm_debug_log_subsc( 'Value after update - Subscriber ID: ' . $member_record->subscr_id . ', Start Date: ' . $member_record->subscription_starts, true );
	} else {
		swpm_debug_log_subsc( 'Did not find an existing record in the members table for subscriber ID: ' . $subscr_id, true );
		swpm_debug_log_subsc( 'This could be a new subscription payment for a new subscription agreement.', true );
	}
}

function swpm_is_paypal_recurring_payment($payment_data){
    $recurring_payment = false;
    $transaction_type = $payment_data['txn_type'];

    if ($transaction_type == "recurring_payment") {
        $recurring_payment = true;

    } else if ($transaction_type == "subscr_payment") {
        $item_number = $payment_data['item_number'];
        $subscr_id = $payment_data['subscr_id'];
        swpm_debug_log_subsc('Is recurring payment check debug data: ' . $item_number . "|" . $subscr_id, true);

        $result = SwpmTransactions::get_transaction_row_by_subscr_id($subscr_id);
        if (isset($result)) {
            swpm_debug_log_subsc('This subscr_id exists in the transactions db. Recurring payment check flag value is true.', true);
            $recurring_payment = true;
            return $recurring_payment;
        }
    }
    if ($recurring_payment) {
        swpm_debug_log_subsc('Recurring payment check flag value is true.', true);
    }
    return $recurring_payment;
}

function swpm_debug_log_subsc( $message, $success, $end = false ) {
	$settings      = SwpmSettings::get_instance();
	$debug_enabled = $settings->get_value( 'enable-debug' );
	if ( empty( $debug_enabled ) ) { // Debug is not enabled.
		return;
	}

	$debug_log_file_name = SIMPLE_WP_MEMBERSHIP_PATH . 'log.txt';

	// Timestamp.
        $log_timestamp = SwpmUtils::get_current_timestamp_for_debug_log();
	$text = '[' . $log_timestamp . '] - ' . ( ( $success ) ? 'SUCCESS: ' : 'FAILURE: ' ) . $message . "\n";
	if ( $end ) {
		$text .= "\n------------------------------------------------------------------\n\n";
	}
	// Write to log.
	$fp = fopen( $debug_log_file_name, 'a' );
	fwrite( $fp, $text );
	fclose( $fp );  // close file.
}
