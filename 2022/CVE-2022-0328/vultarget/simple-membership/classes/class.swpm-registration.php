<?php

/**
 * Description of BRegistration
 *
 * @author nur
 */
abstract class SwpmRegistration {

	protected $member_info     = array();
	var $email_activation      = false;
	protected static $_intance = null;

	//public abstract static function get_instance();
	protected function send_reg_email() {
		global $wpdb;
		if ( empty( $this->member_info ) ) {
			return false;
		}

		$member_info = $this->member_info;
		$settings    = SwpmSettings::get_instance();
		$subject     = $settings->get_value( 'reg-complete-mail-subject' );
		$body        = $settings->get_value( 'reg-complete-mail-body' );

		if ( $this->email_activation ) {
			$swpm_user = SwpmMemberUtils::get_user_by_user_name( $member_info['user_name'] );
			$member_id = $swpm_user->member_id;
			$act_code  = md5( uniqid() . $member_id );
			$enc_pass  = SwpmUtils::crypt( $member_info['plain_password'] );
			$user_data = array(
				'timestamp'      => time(),
				'act_code'       => $act_code,
				'plain_password' => $enc_pass,
			);
			$user_data = apply_filters( 'swpm_email_activation_data', $user_data );
			update_option( 'swpm_email_activation_data_usr_' . $member_id, $user_data, false );
			$body                           = $settings->get_value( 'email-activation-mail-body' );
			$subject                        = $settings->get_value( 'email-activation-mail-subject' );
			$activation_link                = add_query_arg(
				array(
					'swpm_email_activation' => '1',
					'swpm_member_id'        => $member_id,
					'swpm_token'            => $act_code,
				),
				get_home_url()
			);

			// Allow hooks to change the value of activation_link
			$activation_link = apply_filters('swpm_send_reg_email_activation_link', $activation_link);

			$member_info['activation_link'] = $activation_link;
		}

		$from_address                         = $settings->get_value( 'email-from' );
		$login_link                           = $settings->get_value( 'login-page-url' );
		$headers                              = 'From: ' . $from_address . "\r\n";
		$member_info['membership_level_name'] = SwpmPermission::get_instance( $member_info['membership_level'] )->get( 'alias' );
		$member_info['password']              = $member_info['plain_password'];
		$member_info['login_link']            = $login_link;
		$values                               = array_values( $member_info );
		$keys                                 = array_map( 'swpm_enclose_var', array_keys( $member_info ) );
		$body                                 = html_entity_decode( $body );
		$body                                 = str_replace( $keys, $values, $body );

		$swpm_user = SwpmMemberUtils::get_user_by_user_name( $member_info['user_name'] );
		$member_id = $swpm_user->member_id;
		$body      = SwpmMiscUtils::replace_dynamic_tags( $body, $member_id ); //Do the standard merge var replacement.

		$email = sanitize_email( filter_input( INPUT_POST, 'email', FILTER_UNSAFE_RAW ) );

		if ( empty( $email ) ) {
			$email = $swpm_user->email;
		}

		$body = apply_filters( 'swpm_registration_complete_email_body', $body ); //This filter can be used to modify the registration complete email body dynamically.
		//Send notification email to the member
		$subject = apply_filters( 'swpm_email_registration_complete_subject', $subject );
		$body    = apply_filters( 'swpm_email_registration_complete_body', $body ); //You can override the email to empty to disable this email.
		if ( ! empty( $body ) ) {
			SwpmMiscUtils::mail( trim( $email ), $subject, $body, $headers );
			SwpmLog::log_simple_debug( 'Member registration complete email sent to: ' . $email . '. From email address value used: ' . $from_address, true );
		} else {
			SwpmLog::log_simple_debug( 'NOTICE: Registration complete email body value is empty. Member registration complete email will NOT be sent.', true );
		}

		if ( $settings->get_value( 'enable-admin-notification-after-reg' ) && ! $this->email_activation ) {
			//Send notification email to the site admin
			$admin_notification  = $settings->get_value( 'admin-notification-email' );
			$admin_notification  = empty( $admin_notification ) ? $from_address : $admin_notification;
			$notify_emails_array = explode( ',', $admin_notification );

			$headers = 'From: ' . $from_address . "\r\n";

			$admin_notify_subject = $settings->get_value( 'reg-complete-mail-subject-admin' );
			if ( empty( $admin_notify_subject ) ) {
				$admin_notify_subject = 'Notification of New Member Registration';
			}

			$admin_notify_body = $settings->get_value( 'reg-complete-mail-body-admin' );
			if ( empty( $admin_notify_body ) ) {
				$admin_notify_body = "A new member has completed the registration.\n\n" .
						"Username: {user_name}\n" .
						"Email: {email}\n\n" .
						"Please login to the admin dashboard to view details of this user.\n\n" .
						"You can customize this email message from the Email Settings menu of the plugin.\n\n" .
						'Thank You';
			}
			$additional_args   = array( 'password' => $member_info['plain_password'] );
			$admin_notify_body = SwpmMiscUtils::replace_dynamic_tags( $admin_notify_body, $member_id, $additional_args ); //Do the standard merge var replacement.

			foreach ( $notify_emails_array as $to_email ) {
				$to_email             = trim( $to_email );
				$admin_notify_subject = apply_filters( 'swpm_email_admin_notify_subject', $admin_notify_subject );
				$admin_notify_body    = apply_filters( 'swpm_email_admin_notify_body', $admin_notify_body );
				SwpmMiscUtils::mail( $to_email, $admin_notify_subject, $admin_notify_body, $headers );
				SwpmLog::log_simple_debug( 'Admin notification email sent to: ' . $to_email, true );
			}
		}
		return true;
	}

}

function swpm_enclose_var( $n ) {
	return '{' . $n . '}';
}
