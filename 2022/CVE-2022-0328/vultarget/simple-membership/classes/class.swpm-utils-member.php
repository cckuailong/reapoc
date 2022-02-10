<?php

/**
 * SwpmMemberUtils
 * All the utility functions related to member records should be added to this class
 */
class SwpmMemberUtils {

	public static function create_swpm_member_entry_from_array_data( $fields ) {
		global $wpdb;
		$res = $wpdb->insert( $wpdb->prefix . 'swpm_members_tbl', $fields );

		if ( ! $res ) {
			//DB error occurred
			$error_msg = 'create_swpm_member_entry_from_array_data() - DB error occurred: ' . json_encode( $wpdb->last_result );
			SwpmLog::log_simple_debug( $error_msg, false );
		}

		$member_id = $wpdb->insert_id;
		SwpmLog::log_simple_debug( 'create_swpm_member_entry_from_array_data() - SWPM member entry created successfully. Member ID: ' . $member_id, true );
		return $member_id;
	}

	public static function is_member_logged_in() {
		$auth = SwpmAuth::get_instance();
		if ( $auth->is_logged_in() ) {
			return true;
		} else {
			return false;
		}
	}

	public static function get_logged_in_members_id() {
		$auth = SwpmAuth::get_instance();
		if ( ! $auth->is_logged_in() ) {
			return SwpmUtils::_( 'User is not logged in.' );
		}
		return $auth->get( 'member_id' );
	}

	public static function get_logged_in_members_username() {
		$auth = SwpmAuth::get_instance();
		if ( ! $auth->is_logged_in() ) {
			return SwpmUtils::_( 'User is not logged in.' );
		}
		return $auth->get( 'user_name' );
	}

	public static function get_logged_in_members_level() {
		$auth = SwpmAuth::get_instance();
		if ( ! $auth->is_logged_in() ) {
			return SwpmUtils::_( 'User is not logged in.' );
		}
		return $auth->get( 'membership_level' );
	}

	public static function get_logged_in_members_level_name() {
		$auth = SwpmAuth::get_instance();
		if ( $auth->is_logged_in() ) {
			return $auth->get( 'alias' );
		}
		return SwpmUtils::_( 'User is not logged in.' );
	}

	public static function get_logged_in_members_email() {
		$auth = SwpmAuth::get_instance();
		if ( ! $auth->is_logged_in() ) {
			return SwpmUtils::_( 'User is not logged in.' );
		}
		return $auth->get( 'email' );
	}

	public static function get_member_field_by_id( $id, $field, $default = '' ) {
		global $wpdb;
		$query    = 'SELECT * FROM ' . $wpdb->prefix . 'swpm_members_tbl WHERE member_id = %d';
		$userData = $wpdb->get_row( $wpdb->prepare( $query, $id ) );
		if ( isset( $userData->$field ) ) {
			return $userData->$field;
		}

		return apply_filters( 'swpm_get_member_field_by_id', $default, $id, $field );
	}

	public static function get_formatted_expiry_date_by_user_id( $swpm_id ) {
		$expiry_timestamp = self::get_expiry_date_timestamp_by_user_id( $swpm_id );
		if ( $expiry_timestamp == PHP_INT_MAX ) {
			//No Expiry Setting
			$formatted_expiry_date = SwpmUtils::_( 'No Expiry' );
		} else {
			$expiry_date           = date( 'Y-m-d', $expiry_timestamp );
			$formatted_expiry_date = SwpmUtils::get_formatted_date_according_to_wp_settings( $expiry_date );
		}
		return $formatted_expiry_date;
	}

	public static function get_expiry_date_timestamp_by_user_id( $swpm_id ) {
		$swpm_user        = self::get_user_by_id( $swpm_id );
		$expiry_timestamp = SwpmUtils::get_expiration_timestamp( $swpm_user );
		return $expiry_timestamp;
	}

	public static function get_user_by_id( $swpm_id ) {
		//Retrieves the SWPM user record for the given member ID
		global $wpdb;
		$query  = $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}swpm_members_tbl WHERE member_id = %d", $swpm_id );
		$result = $wpdb->get_row( $query );
		return $result;
	}

	public static function get_user_by_user_name( $swpm_user_name ) {
		//Retrieves the SWPM user record for the given member username
		global $wpdb;
		$query  = $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}swpm_members_tbl WHERE user_name = %s", $swpm_user_name );
		$result = $wpdb->get_row( $query );
		return $result;
	}

	public static function get_user_by_email( $swpm_email ) {
		//Retrieves the SWPM user record for the given member email address
		global $wpdb;
		$query  = $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}swpm_members_tbl WHERE email = %s", $swpm_email );
		$result = $wpdb->get_row( $query );
		return $result;
	}

	public static function get_user_by_subsriber_id( $subsc_id ) {
		//Retrieves the SWPM user record for the given member ID
		global $wpdb;
		$query  = $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}swpm_members_tbl WHERE subscr_id = %s", $subsc_id );
		$result = $wpdb->get_row( $query );
		return $result;
	}

	public static function get_wp_user_from_swpm_user_id( $swpm_id ) {
		//Retrieves the WP user record for the given SWPM member ID.
		$swpm_user_row = self::get_user_by_id( $swpm_id );
		$username      = $swpm_user_row->user_name;
		$wp_user       = get_user_by( 'login', $username );
		return $wp_user;
	}

	public static function get_all_members_of_a_level( $level_id ) {
		//Retrieves all the SWPM user records for the given membership level
		global $wpdb;
		$query  = $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}swpm_members_tbl WHERE membership_level = %s", $level_id );
		$result = $wpdb->get_results( $query );
		return $result;
	}

	/*
	 * Use this function to update or set membership level of a member easily.
	 */
	public static function update_membership_level( $member_id, $target_membership_level ) {
		global $wpdb;
		$members_table_name = $wpdb->prefix . 'swpm_members_tbl';
		$query              = $wpdb->prepare( "UPDATE $members_table_name SET membership_level=%s WHERE member_id=%s", $target_membership_level, $member_id );
		$resultset          = $wpdb->query( $query );
	}

	/*
	 * Use this function to update or set account status of a member easily.
	 */
	public static function update_account_state( $member_id, $new_status = 'active' ) {
		global $wpdb;
		$members_table_name = $wpdb->prefix . 'swpm_members_tbl';

		SwpmLog::log_simple_debug( 'Updating the account status value of member (' . $member_id . ') to: ' . $new_status, true );
		$query     = $wpdb->prepare( "UPDATE $members_table_name SET account_state=%s WHERE member_id=%s", $new_status, $member_id );
		$resultset = $wpdb->query( $query );
	}

	/*
	 * Use this function to update or set access starts date of a member easily.
	 */
	public static function update_access_starts_date( $member_id, $new_date ) {
		global $wpdb;
		$members_table_name = $wpdb->prefix . 'swpm_members_tbl';
		$query              = $wpdb->prepare( "UPDATE $members_table_name SET subscription_starts=%s WHERE member_id=%s", $new_date, $member_id );
		$resultset          = $wpdb->query( $query );
	}

	/*
	 * Calculates the Access Starts date value considering the level and current expiry. Useful for after payment member profile update.
	 */
	public static function calculate_access_start_date_for_account_update( $args ) {
		$swpm_id              = $args['swpm_id'];
		$membership_level     = $args['membership_level'];
		$old_membership_level = $args['old_membership_level'];

		$subscription_starts = SwpmUtils::get_current_date_in_wp_zone();//( date( 'Y-m-d' ) );
		if ( $membership_level == $old_membership_level ) {
			//Payment for the same membership level (renewal).

			//Algorithm - ONLY set the $subscription_starts date to current expiry date if the current expiry date is in the future.
			//Otherwise set $subscription_starts to TODAY.
			$expiry_timestamp = self::get_expiry_date_timestamp_by_user_id( $swpm_id );
			if ( $expiry_timestamp > time() ) {
				//Account is not expired. Expiry date is in the future.
				$level_row          = SwpmUtils::get_membership_level_row_by_id( $membership_level );
				$subs_duration_type = $level_row->subscription_duration_type;
				if ( $subs_duration_type == SwpmMembershipLevel::NO_EXPIRY ) {
					//No expiry type level.
					//Use todays date for $subscription_starts date parameter.
				} elseif ( $subs_duration_type == SwpmMembershipLevel::FIXED_DATE ) {
					//Fixed date expiry level.
					//Use todays date for $subscription_starts date parameter.
				} else {
					//Duration expiry level.
					//Set the $subscription_starts date to the current expiry date so the renewal time starts from then.
					$subscription_starts = date( 'Y-m-d', $expiry_timestamp );
				}
			} else {
				//Account is already expired.
				//Use todays date for $subscription_starts date parameter.
			}
		} else {
			//Payment for a NEW membership level (upgrade).
			//Use todays date for $subscription_starts date parameter.
		}

		return $subscription_starts;
	}

	public static function is_valid_user_name( $user_name ) {
		return preg_match( '/^[a-zA-Z0-9.\-_*@]+$/', $user_name ) == 1;
	}

        public static function check_and_die_if_email_belongs_to_admin_user( $email_to_check ){
		//Check if the email belongs to an existing wp user account.
		$wp_user_id = email_exists( $email_to_check );
		if ( $wp_user_id ) {
                    //A wp user account exist with this email.
                    //Check if the user has admin role.
                    $admin_user = SwpmMemberUtils::wp_user_has_admin_role( $wp_user_id );
                    if ( $admin_user ) {
                        //This email belongs to an admin user. Cannot use/register using an admin user's email from front-end. Show error message then exit.
                        $error_msg = '<p>This email address (' . $email_to_check . ') belongs to an admin user. This email cannot be used to register a new account on this site for security reasons. Contact site admin.</p>';
                        $error_msg .= '<p>For testing purpose, you can create another user account that is completely separate from the admin user account of this site.</p>';
                        wp_die( $error_msg );
                    }
		}
        }

        public static function check_and_die_if_username_belongs_to_admin_user( $username_to_check ){
                //Check if the username belongs to an existing wp user account.
                $wp_user_id = username_exists( $username_to_check );
                if ( $wp_user_id ) {
                    //A wp user account exists with this username.
                    //Check if the user has admin role.
                    $admin_user = SwpmMemberUtils::wp_user_has_admin_role( $wp_user_id );
                    if ( $admin_user ) {
                        //This Username belongs to an admin user. Cannot use/register using an existing admin user's username from front-end. Show error message then exit.
                        $error_msg = '<p>This username (' . $username_to_check . ') belongs to an admin user. It cannot be used to register a new account on this site for security reasons. Contact site admin.</p>';
                        $error_msg .= '<p>For testing purpose, you can create another user account that is completely separate from the admin user account of this site.</p>';
                        wp_die( $error_msg );
                    }
                }
        }

	public static function wp_user_has_admin_role( $wp_user_id ) {
		$caps = get_user_meta( $wp_user_id, 'wp_capabilities', true );
		if ( is_array( $caps ) && in_array( 'administrator', array_keys( (array) $caps ) ) ) {
			//This wp user has "administrator" role.
			return true;
		}
		return false;
	}

	public static function update_wp_user_role_with_level_id( $wp_user_id, $level_id ) {
		$level_row = SwpmUtils::get_membership_level_row_by_id( $level_id );
		$user_role = $level_row->role;
		self::update_wp_user_role( $wp_user_id, $user_role );
	}

	public static function update_wp_user_role( $wp_user_id, $role ) {
		if ( SwpmUtils::is_multisite_install() ) {//MS install
			return; //TODO - don't do this for MS install
		}

		$admin_user = self::wp_user_has_admin_role( $wp_user_id );
		if ( $admin_user ) {
			SwpmLog::log_simple_debug( 'This user has admin role. No role modification will be done.', true );
			return;
		}

		//wp_update_user() function will trigger the 'set_user_role' hook.
		wp_update_user(
			array(
				'ID'   => $wp_user_id,
				'role' => $role,
			)
		);
		SwpmLog::log_simple_debug( 'User role updated.', true );
	}
}
