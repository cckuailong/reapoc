<?php
	global $besecure;
	$besecure = false;

	global $wpdb, $current_user, $pmpro_msg, $pmpro_msgt, $pmpro_confirm, $pmpro_error;

	// Get the level IDs they are requesting to cancel using the old ?level param.
	if ( ! empty( $_REQUEST['level'] ) && empty( $_REQUEST['levelstocancel'] ) ) {
		$requested_ids = $_REQUEST['level'];
	}

	// Get the level IDs they are requesting to cancel from the ?levelstocancel param.
	if ( ! empty( $_REQUEST['levelstocancel'] ) ) {
		$requested_ids = $_REQUEST['levelstocancel'];
	}

	// Redirection logic.
	if ( ! is_user_logged_in() ) {
		if ( ! empty( $requested_ids ) ) {
			$redirect = add_query_arg( 'levelstocancel', $requested_ids, pmpro_url( 'cancel' ) );
		} else {
			$redirect = pmpro_url( 'cancel' );
		}
		// Redirect non-user to the login page; pass the Cancel page with specific ?levelstocancel as the redirect_to query arg.
		wp_redirect( add_query_arg( 'redirect_to', urlencode( $redirect ), pmpro_login_url() ) );
		exit;
	} else {
		// Get the membership level for the current user.
		$current_user->membership_level = pmpro_getMembershipLevelForUser( $current_user->ID) ;
		// If user has no membership level, redirect to levels page.
		if ( ! isset( $current_user->membership_level->ID ) ) {
			wp_redirect( pmpro_url( 'levels' ) );
			exit;
		}
	}

	//check if a level was passed in to cancel specifically
	if ( ! empty ( $requested_ids ) && $requested_ids != 'all' ) {
		//convert spaces back to +
		$requested_ids = str_replace(array(' ', '%20'), '+', $requested_ids );

		//get the ids
		$requested_ids = preg_replace("/[^0-9\+]/", "", $requested_ids );
		$old_level_ids = array_map( 'intval', explode( "+", $requested_ids ) );

		// Make sure the user has the level they are trying to cancel.
		if ( ! pmpro_hasMembershipLevel( $old_level_ids ) ) {
			// If they don't have the level, return to Membership Account.
			wp_redirect( pmpro_url( 'account' ) );
			exit;
		}
	} else {
		$old_level_ids = false;	//cancel all levels
	}

	//are we confirming a cancellation?
	if(isset($_REQUEST['confirm']))
		$pmpro_confirm = (bool)$_REQUEST['confirm'];
	else
		$pmpro_confirm = false;

	if($pmpro_confirm) {
        if(!empty($old_level_ids)) {
        	$worked = true;
			foreach($old_level_ids as $old_level_id) {
				$one_worked = pmpro_cancelMembershipLevel($old_level_id, $current_user->ID, 'cancelled');
				$worked = $worked && $one_worked !== false;
			}
        }
		else {
			$old_level_ids = $wpdb->get_col("SELECT DISTINCT(membership_id) FROM $wpdb->pmpro_memberships_users WHERE user_id = '" . $current_user->ID . "' AND status = 'active'");
			$worked = pmpro_changeMembershipLevel(0, $current_user->ID, 'cancelled');
		}
        
		if($worked != false && empty($pmpro_error))
		{
			$pmpro_msg = __("Your membership has been cancelled.", 'paid-memberships-pro' );
			$pmpro_msgt = "pmpro_success";

			//send an email to the member
			$myemail = new PMProEmail();
			$myemail->sendCancelEmail($current_user, $old_level_ids);

			//send an email to the admin
			$myemail = new PMProEmail();
			$myemail->sendCancelAdminEmail($current_user, $old_level_ids);
		} else {
			global $pmpro_error;
			$pmpro_msg = $pmpro_error;
			$pmpro_msgt = "pmpro_error";
		}
	}
