<?php

/**
 * Description of BFrontRegistration
 *
 * @author nur
 */
class SwpmFrontRegistration extends SwpmRegistration {

	public static function get_instance() {
		self::$_intance = empty( self::$_intance ) ? new SwpmFrontRegistration() : self::$_intance;
		return self::$_intance;
	}

	public function regigstration_ui( $level ) {

		$settings_configs = SwpmSettings::get_instance();

                //Check if the hide rego from logged-in users feature is enabled before rendering the registration form.
                $hide_rego_to_logged_users = $settings_configs->get_value( 'hide-rego-form-to-logged-users' );
                if ( ! empty( $hide_rego_to_logged_users ) ){
                    //Hide registration form to logged-in users feature is enabled. Check if the form should be hidden.
                    if ( SwpmMemberUtils::is_member_logged_in() ) {

                        $rego_hidden_to_logged_users_msg = '<div class="registration_hidden_to_logged_users_msg">';
                        $rego_hidden_to_logged_users_msg .= SwpmUtils::_( "You are already logged in. You don't need to create another account. So the registration form is hidden." );
                        $rego_hidden_to_logged_users_msg .= '</div>';
                        return $rego_hidden_to_logged_users_msg;
                    }
                }

		//Trigger the filter to override the registration form (the form builder addon uses this filter)
		$form = apply_filters( 'swpm_registration_form_override', '', $level ); //The $level value could be empty also so the code handling the filter need to check for it.
		if ( ! empty( $form ) ) {
			//An addon has overridden the registration form. So use that one.
			return $form;
		}

		$joinuspage_url = $settings_configs->get_value( 'join-us-page-url' );
		$membership_level = '';
		global $wpdb;

		if ( SwpmUtils::is_paid_registration() ) {
			//Lets check if this is a registration for paid membership
			$member = SwpmUtils::get_paid_member_info();
			if ( empty( $member ) ) {
				SwpmUtils::e( 'Error! Invalid Request. Could not find a match for the given security code and the user ID.' );
			} else {
				$membership_level = $member->membership_level;
			}
		} elseif ( ! empty( $level ) ) {
			//Membership level is specified in the shortcode (level specific registration form).
			$member           = SwpmTransfer::$default_fields;
			$membership_level = absint( $level );
		}

		//Check if free membership registration is disalbed on the site
		if ( empty( $membership_level ) ) {
			$joinuspage_link         = '<a href="' . $joinuspage_url . '">' . SwpmUtils::_( 'Join Us' ) . '</a>';
			$free_rego_disabled_msg  = '<p>';
			$free_rego_disabled_msg .= SwpmUtils::_( 'Free membership is disabled on this site. Please make a payment from the ' );
			$free_rego_disabled_msg .= SwpmUtils::_( $joinuspage_link );
			$free_rego_disabled_msg .= SwpmUtils::_( ' page to pay for a premium membership.' );
			$free_rego_disabled_msg .= '</p><p>';
			$free_rego_disabled_msg .= SwpmUtils::_( 'You will receive a unique link via email after the payment. You will be able to use that link to complete the premium membership registration.' );
			$free_rego_disabled_msg .= '</p>';
			return $free_rego_disabled_msg;
		}

		//Handle the registration form in core plugin
		$membership_info = SwpmPermission::get_instance( $membership_level );
		$membership_level = $membership_info->get( 'id' );
		if ( empty( $membership_level ) ) {
			return 'Error! Failed to retrieve membership level ID from the membership info object.';
		}
		$level_identifier = md5( $membership_level );
		$membership_level_alias = $membership_info->get( 'alias' );
		$swpm_registration_submit = filter_input( INPUT_POST, 'swpm_registration_submit' );
		if ( ! empty( $swpm_registration_submit ) ) {
			$member = array_map( 'sanitize_text_field', $_POST );
		}
		ob_start();
		extract( (array) $member, EXTR_SKIP );
		include SIMPLE_WP_MEMBERSHIP_PATH . 'views/add.php';
		return ob_get_clean();
	}

	public function register_front_end() {

		//If captcha is present and validation failed, it returns an error string. If validation succeeds, it returns an empty string.
		$captcha_validation_output = apply_filters( 'swpm_validate_registration_form_submission', '' );
		if ( ! empty( $captcha_validation_output ) ) {
			$message = array(
				'succeeded' => false,
				'message'   => SwpmUtils::_( 'Security check: captcha validation failed.' ),
			);
			SwpmTransfer::get_instance()->set( 'status', $message );
			return;
		}

		//Check if Terms and Conditions enabled
		$terms_enabled = SwpmSettings::get_instance()->get_value( 'enable-terms-and-conditions' );
		if ( ! empty( $terms_enabled ) ) {
			//check if user checked "I accept terms" checkbox
			if ( empty( $_POST['accept_terms'] ) ) {
				$message = array(
					'succeeded' => false,
					'message'   => SwpmUtils::_( 'You must accept the terms and conditions.' ),
				);
				SwpmTransfer::get_instance()->set( 'status', $message );
				return;
			}
		}

		//Check if Privacy Policy enabled
		$pp_enabled = SwpmSettings::get_instance()->get_value( 'enable-privacy-policy' );
		if ( ! empty( $pp_enabled ) ) {
			//check if user checked "I agree with Privacy Policy" checkbox
			if ( empty( $_POST['accept_pp'] ) ) {
				$message = array(
					'succeeded' => false,
					'message'   => SwpmUtils::_( 'You must agree to the privacy policy.' ),
				);
				SwpmTransfer::get_instance()->set( 'status', $message );
				return;
			}
		}

		//Validate swpm level hash data.
		$hash_val_posted = sanitize_text_field( $_POST['swpm_level_hash'] );
		$level_value     = sanitize_text_field( $_POST['membership_level'] );
		$swpm_p_key      = get_option( 'swpm_private_key_one' );
		$hash_val        = md5( $swpm_p_key . '|' . $level_value );
		if ( $hash_val != $hash_val_posted ) {//Level hash validation failed.
			$msg  = '<p>Error! Security check failed for membership level validation.</p>';
			$msg .= '<p>The submitted membership level data does not seem to be authentic.</p>';
			$msg .= '<p>If you are using caching please empty the cache data and try again.</p>';
			wp_die( $msg );
		}

		$this->email_activation = get_option( 'swpm_email_activation_lvl_' . $level_value );

		//Crete the member profile and send notification
		if ( $this->create_swpm_user() && $this->prepare_and_create_wp_user_front_end() && $this->send_reg_email() ) {
			do_action( 'swpm_front_end_registration_complete' ); //Keep this action hook for people who are using it (so their implementation doesn't break).
			do_action( 'swpm_front_end_registration_complete_user_data', $this->member_info );

			//Check if there is after registration redirect
			if ( ! $this->email_activation ) {
				$after_rego_url = SwpmSettings::get_instance()->get_value( 'after-rego-redirect-page-url' );
				$after_rego_url = apply_filters( 'swpm_after_registration_redirect_url', $after_rego_url );
				if ( ! empty( $after_rego_url ) ) {
					//Yes. Need to redirect to this after registration page
					SwpmLog::log_simple_debug( 'After registration redirect is configured in settings. Redirecting user to: ' . $after_rego_url, true );
					wp_redirect( $after_rego_url );
					exit( 0 );
				}
			}

			//Set the registration complete message
			if ( $this->email_activation ) {
				$email_act_msg  = '<div class="swpm-registration-success-msg">';
				$email_act_msg .= SwpmUtils::_( 'You need to confirm your email address. Please check your email and follow instructions to complete your registration.' );
				$email_act_msg .= '</div>';
                                $email_act_msg = apply_filters( 'swpm_registration_email_activation_msg', $email_act_msg );//Can be added to the custom messages addon.
				$message        = array(
					'succeeded' => true,
					'message'   => $email_act_msg,
				);
			} else {
				$login_page_url = SwpmSettings::get_instance()->get_value( 'login-page-url' );

				// Allow hooks to change the value of login_page_url
				$login_page_url = apply_filters('swpm_register_front_end_login_page_url', $login_page_url);

				$after_rego_msg = '<div class="swpm-registration-success-msg">' . SwpmUtils::_( 'Registration Successful. ' ) . SwpmUtils::_( 'Please' ) . ' <a href="' . $login_page_url . '">' . SwpmUtils::_( 'Login' ) . '</a></div>';
				$after_rego_msg = apply_filters( 'swpm_registration_success_msg', $after_rego_msg );
				$message        = array(
					'succeeded' => true,
					'message'   => $after_rego_msg,
				);
			}
			SwpmTransfer::get_instance()->set( 'status', $message );
			return;
		}
	}

	private function create_swpm_user() {
		global $wpdb;
		$member = SwpmTransfer::$default_fields;
		$form   = new SwpmFrontForm( $member );
		if ( ! $form->is_valid() ) {
			$message = array(
				'succeeded' => false,
				'message'   => SwpmUtils::_( 'Please correct the following' ),
				'extra'     => $form->get_errors(),
			);
			SwpmTransfer::get_instance()->set( 'status', $message );
			return false;
		}

		$member_info = $form->get_sanitized_member_form_data();

		//Check if the email belongs to an existing wp user account with admin role.
                SwpmMemberUtils::check_and_die_if_email_belongs_to_admin_user($member_info['email']);

		//Go ahead and create the SWPM user record.
		$free_level                           = SwpmUtils::get_free_level();
		$account_status                       = SwpmSettings::get_instance()->get_value( 'default-account-status', 'active' );
		$member_info['last_accessed_from_ip'] = SwpmUtils::get_user_ip_address();
		$member_info['member_since']          = SwpmUtils::get_current_date_in_wp_zone(); //date( 'Y-m-d' );
		$member_info['subscription_starts']   = SwpmUtils::get_current_date_in_wp_zone(); //date( 'Y-m-d' );
		$member_info['account_state']         = $account_status;
		if ( $this->email_activation ) {
			$member_info['account_state'] = 'activation_required';
		}
		$plain_password = $member_info['plain_password'];
		unset( $member_info['plain_password'] );

		if ( SwpmUtils::is_paid_registration() ) {
			$member_info['reg_code'] = '';
			$member_id               = filter_input( INPUT_GET, 'member_id', FILTER_SANITIZE_NUMBER_INT );
			$code                    = filter_input( INPUT_GET, 'code', FILTER_SANITIZE_STRING );
			$wpdb->update(
				$wpdb->prefix . 'swpm_members_tbl',
				$member_info,
				array(
					'member_id' => $member_id,
					'reg_code'  => $code,
				)
			);

			$query                           = $wpdb->prepare( 'SELECT membership_level FROM ' . $wpdb->prefix . 'swpm_members_tbl WHERE member_id=%d', $member_id );
			$member_info['membership_level'] = $wpdb->get_var( $query );
			$last_insert_id                  = $member_id;
		} elseif ( ! empty( $free_level ) ) {
			$member_info['membership_level'] = $free_level;
			$wpdb->insert( $wpdb->prefix . 'swpm_members_tbl', $member_info );
			$last_insert_id = $wpdb->insert_id;
		} else {
			$message = array(
				'succeeded' => false,
				'message'   => SwpmUtils::_( 'Membership Level Couldn\'t be found.' ),
			);
			SwpmTransfer::get_instance()->set( 'status', $message );
			return false;
		}
		$member_info['plain_password'] = $plain_password;
		$this->member_info             = $member_info;
		return true;
	}

	private function prepare_and_create_wp_user_front_end() {
		global $wpdb;
		$member_info = $this->member_info;

		//Retrieve the user role assigned for this level
		$query     = $wpdb->prepare( 'SELECT role FROM ' . $wpdb->prefix . 'swpm_membership_tbl WHERE id = %d', $member_info['membership_level'] );
		$user_role = $wpdb->get_var( $query );
		//Check to make sure that the user role of this level is not admin.
		if ( $user_role == 'administrator' ) {
			//For security reasons we don't allow users with administrator role to be creted from the front-end. That can only be done from the admin dashboard side.
			$error_msg  = '<p>Error! The user role for this membership level (level ID: ' . $member_info['membership_level'] . ') is set to "Administrator".</p>';
			$error_msg .= '<p>For security reasons, member registration to this level is not permitted from the front end.</p>';
			$error_msg .= '<p>An administrator of the site can manually create a member record with this access level from the admin dashboard side.</p>';
			wp_die( $error_msg );
		}

		$wp_user_info                    = array();
		$wp_user_info['user_nicename']   = implode( '-', explode( ' ', $member_info['user_name'] ) );
		$wp_user_info['display_name']    = $member_info['user_name'];
		$wp_user_info['user_email']      = $member_info['email'];
		$wp_user_info['nickname']        = $member_info['user_name'];
		$wp_user_info['first_name']      = $member_info['first_name'];
		$wp_user_info['last_name']       = $member_info['last_name'];
		$wp_user_info['user_login']      = $member_info['user_name'];
		$wp_user_info['password']        = $member_info['plain_password'];
		$wp_user_info['role']            = $user_role;
		$wp_user_info['user_registered'] = date( 'Y-m-d H:i:s' );
		SwpmUtils::create_wp_user( $wp_user_info );
		return true;
	}

	public function edit_profile_front_end() {
		global $wpdb;
		//Check that the member is logged in
		$auth = SwpmAuth::get_instance();
		if ( ! $auth->is_logged_in() ) {
			return;
		}

		//Check nonce
		if ( ! isset( $_POST['swpm_profile_edit_nonce_val'] ) || ! wp_verify_nonce( $_POST['swpm_profile_edit_nonce_val'], 'swpm_profile_edit_nonce_action' ) ) {
			//Nonce check failed.
			wp_die( SwpmUtils::_( 'Error! Nonce verification failed for front end profile edit.' ) );
		}

		$user_data = (array) $auth->userData;
		unset( $user_data['permitted'] );
		$form = new SwpmForm( $user_data );
		if ( $form->is_valid() ) {
			global $wpdb;
			$msg_str = '<div class="swpm-profile-update-success">' . SwpmUtils::_( 'Profile updated successfully.' ) . '</div>';
			$message = array(
				'succeeded' => true,
				'message'   => $msg_str,
			);

			$member_info = $form->get_sanitized_member_form_data();
			SwpmUtils::update_wp_user( $auth->get( 'user_name' ), $member_info ); //Update corresponding wp user record.

			//Lets check if password was also changed.
			$password_also_changed = false;
			if ( isset( $member_info['plain_password'] ) ) {
				//Password was also changed.
				$msg_str = '<div class="swpm-profile-update-success">' . SwpmUtils::_( 'Profile updated successfully. You will need to re-login since you changed your password.' ) . '</div>';
				$message = array(
					'succeeded' => true,
					'message'   => $msg_str,
				);
				unset( $member_info['plain_password'] );
				//Set the password chagned flag.
				$password_also_changed = true;
			}

			//Update the data in the swpm database.
			$swpm_id = $auth->get( 'member_id' );
			//SwpmLog::log_simple_debug("Updating member profile data with SWPM ID: " . $swpm_id, true);
			$member_info = array_filter( $member_info );//Remove any null values.
			$wpdb->update( $wpdb->prefix . 'swpm_members_tbl', $member_info, array( 'member_id' => $swpm_id ) );
			$auth->reload_user_data();//Reload user data after update so the profile page reflects the new data.

			if ( $password_also_changed ) {
				//Password was also changed. Logout the user's current session.
				wp_logout(); //Log the user out from the WP user session also.
				SwpmLog::log_simple_debug( 'Member has updated the password from profile edit page. Logging the user out so he can re-login using the new password.', true );
			}

			SwpmTransfer::get_instance()->set( 'status', $message );

			do_action( 'swpm_front_end_profile_edited', $member_info );
			return true; //Successful form submission.
		} else {
			$msg_str = '<div class="swpm-profile-update-error">' . SwpmUtils::_( 'Please correct the following.' ) . '</div>';
			$message = array(
				'succeeded' => false,
				'message'   => $msg_str,
				'extra'     => $form->get_errors(),
			);
			SwpmTransfer::get_instance()->set( 'status', $message );
			return false; //Error in the form submission.
		}
	}

	public function reset_password( $email ) {

		//If captcha is present and validation failed, it returns an error string. If validation succeeds, it returns an empty string.
		$captcha_validation_output = apply_filters( 'swpm_validate_pass_reset_form_submission', '' );
		if ( ! empty( $captcha_validation_output ) ) {
			$message = '<div class="swpm-reset-pw-error">' . SwpmUtils::_( 'Captcha validation failed.' ) . '</div>';
			$message = array(
				'succeeded' => false,
				'message'   => $message,
			);
			SwpmTransfer::get_instance()->set( 'status', $message );
			return;
		}

		$email = sanitize_email( $email );
		if ( ! is_email( $email ) ) {
			$message = '<div class="swpm-reset-pw-error">' . SwpmUtils::_( 'Email address not valid.' ) . '</div>';
			$message = array(
				'succeeded' => false,
				'message'   => $message,
			);
			SwpmTransfer::get_instance()->set( 'status', $message );
			return;
		}
		global $wpdb;
		$query = 'SELECT member_id,user_name,first_name, last_name FROM ' .
				$wpdb->prefix . 'swpm_members_tbl ' .
				' WHERE email = %s';
		$user  = $wpdb->get_row( $wpdb->prepare( $query, $email ) );
		if ( empty( $user ) ) {
			$message  = '<div class="swpm-reset-pw-error">' . SwpmUtils::_( 'No user found with that email address.' ) . '</div>';
			$message .= '<div class="swpm-reset-pw-error-email">' . SwpmUtils::_( 'Email Address: ' ) . $email . '</div>';
			$message  = array(
				'succeeded' => false,
				'message'   => $message,
			);
			SwpmTransfer::get_instance()->set( 'status', $message );
			return;
		}
		$settings = SwpmSettings::get_instance();
		$password = wp_generate_password();

		$password_hash = SwpmUtils::encrypt_password( trim( $password ) ); //should use $saned??;
		$wpdb->update( $wpdb->prefix . 'swpm_members_tbl', array( 'password' => $password_hash ), array( 'member_id' => $user->member_id ) );

		//Update wp user password
		add_filter( 'send_password_change_email', array( &$this, 'dont_send_password_change_email' ), 1, 3 ); //Stop WordPress from sending a reset password email to admin.
		SwpmUtils::update_wp_user( $user->user_name, array( 'plain_password' => $password ) );

		$body            = $settings->get_value( 'reset-mail-body' );
		$subject         = $settings->get_value( 'reset-mail-subject' );
		$body            = html_entity_decode( $body );
		$additional_args = array( 'password' => $password );
		$body            = SwpmMiscUtils::replace_dynamic_tags( $body, $user->member_id, $additional_args );
		$from            = $settings->get_value( 'email-from' );
		$headers         = 'From: ' . $from . "\r\n";
		$subject         = apply_filters( 'swpm_email_password_reset_subject', $subject );
		$body            = apply_filters( 'swpm_email_password_reset_body', $body );
		SwpmMiscUtils::mail( $email, $subject, $body, $headers );
		SwpmLog::log_simple_debug( 'Member password has been reset. Password reset email sent to: ' . $email, true );

		$message  = '<div class="swpm-reset-pw-success-box">';
		$message .= '<div class="swpm-reset-pw-success">' . SwpmUtils::_( 'New password has been sent to your email address.' ) . '</div>';
		$message .= '<div class="swpm-reset-pw-success-email">' . SwpmUtils::_( 'Email Address: ' ) . $email . '</div>';
		$message .= '</div>';

		$message = array(
			'succeeded'       => false,
			'message'         => $message,
			'pass_reset_sent' => true,
		);
		SwpmTransfer::get_instance()->set( 'status', $message );
	}

	function dont_send_password_change_email( $send = false, $user = '', $userdata = '' ) {
		//Stop the WordPress's default password change email notification to site admin
		//Only the simple membership plugin's password reset email will be sent.
		return false;
	}

	public function email_activation() {
		$login_page_url = SwpmSettings::get_instance()->get_value( 'login-page-url' );

		// Allow hooks to change the value of login_page_url
		$login_page_url = apply_filters('swpm_email_activation_login_page_url', $login_page_url);

		$member_id = FILTER_INPUT( INPUT_GET, 'swpm_member_id', FILTER_SANITIZE_NUMBER_INT );

		$member = SwpmMemberUtils::get_user_by_id( $member_id );
		if ( empty( $member ) ) {
			//can't find member
			echo SwpmUtils::_( "Can't find member account." );
			wp_die();
		}
		if ( $member->account_state !== 'activation_required' ) {
			//account already active
			echo SwpmUtils::_( 'Account already active. ' ) . '<a href="' . $login_page_url . '">' . SwpmUtils::_( 'click here' ) . '</a>' . SwpmUtils::_( ' to login.' );
			wp_die();
		}
		$code     = FILTER_INPUT( INPUT_GET, 'swpm_token', FILTER_SANITIZE_STRING );
		$act_data = get_option( 'swpm_email_activation_data_usr_' . $member_id );
		if ( empty( $code ) || empty( $act_data ) || $act_data['act_code'] !== $code ) {
			//code mismatch
			wp_die( SwpmUtils::_( 'Activation code mismatch. Cannot activate this account. Please contact the site admin.' ) );
		}
		//activation code match
		delete_option( 'swpm_email_activation_data_usr_' . $member_id );
		//store rego form id in constant so FB addon could use it
		if ( ! empty( $act_data['fb_form_id'] ) ) {
			define( 'SWPM_EMAIL_ACTIVATION_FORM_ID', $act_data['fb_form_id'] );
		}
		$activation_account_status = apply_filters( 'swpm_activation_feature_override_account_status', 'active' );
		SwpmMemberUtils::update_account_state( $member_id, $activation_account_status );
		$this->member_info                   = (array) $member;
		$this->member_info['plain_password'] = SwpmUtils::crypt( $act_data['plain_password'], 'd' );
		$this->send_reg_email();

		$msg = '<div class="swpm_temporary_msg" style="font-weight: bold;">' . SwpmUtils::_( 'Success! Your account has been activated successfully.' ) . '</div>';

		$after_rego_url = SwpmSettings::get_instance()->get_value( 'after-rego-redirect-page-url' );
		$after_rego_url = apply_filters( 'swpm_after_registration_redirect_url', $after_rego_url );
		if ( ! empty( $after_rego_url ) ) {
			//Yes. Need to redirect to this after registration page
			SwpmLog::log_simple_debug( 'After registration redirect is configured in settings. Redirecting user to: ' . $after_rego_url, true );
			SwpmMiscUtils::show_temporary_message_then_redirect( $msg, $after_rego_url );
			exit( 0 );
		}

		//show success message and redirect to login page
		SwpmMiscUtils::show_temporary_message_then_redirect( $msg, $login_page_url );
		exit( 0 );
	}

	public function resend_activation_email() {
		$login_page_url = SwpmSettings::get_instance()->get_value( 'login-page-url' );

		// Allow hooks to change the value of login_page_url
		$login_page_url = apply_filters('swpm_resend_activation_email_login_page_url', $login_page_url);

		$member_id = FILTER_INPUT( INPUT_GET, 'swpm_member_id', FILTER_SANITIZE_NUMBER_INT );

		$member = SwpmMemberUtils::get_user_by_id( $member_id );
		if ( empty( $member ) ) {
			//can't find member
			echo SwpmUtils::_( 'Cannot find member account.' );
			wp_die();
		}
		if ( $member->account_state !== 'activation_required' ) {
			//account already active
			$acc_active_msg = SwpmUtils::_( 'Account already active. ' ) . '<a href="' . $login_page_url . '">' . SwpmUtils::_( 'click here' ) . '</a>' . SwpmUtils::_( ' to login.' );
			echo $acc_active_msg;
			wp_die();
		}
		$act_data = get_option( 'swpm_email_activation_data_usr_' . $member_id );
		if ( ! empty( $act_data ) ) {
			//looks like activation data has been removed for some reason. We won't be able to have member's plain password in this case
			$act_data['plain_password'] = '';
		}

		delete_option( 'swpm_email_activation_data_usr_' . $member_id );

		$this->member_info                   = (array) $member;
		$this->member_info['plain_password'] = SwpmUtils::crypt( $act_data['plain_password'], 'd' );
		$this->email_activation              = true;
		$this->send_reg_email();

		$msg = '<div class="swpm_temporary_msg" style="font-weight: bold;">' . SwpmUtils::_( 'Activation email has been sent. Please check your email and activate your account.' ) . '</div>';
		SwpmMiscUtils::show_temporary_message_then_redirect( $msg, $login_page_url );
		wp_die();
	}

}
