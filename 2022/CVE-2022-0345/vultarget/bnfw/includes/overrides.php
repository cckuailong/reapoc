<?php
/**
 * Override default WordPress emails
 *
 */

/**
 * Email login credentials to a newly-registered user.
 *
 * A new user registration notification is also sent to admin email.
 *
 * @param int    $user_id    User ID.
 * @param null   $deprecated Not used (argument deprecated).
 * @param string $notify     Optional. Type of notification that should happen. Accepts 'admin' or an empty
 *                           string (admin only), or 'both' (admin and user). The empty string value was kept
 *                           for backward-compatibility purposes with the renamed parameter. Default empty.
 */
if ( ! function_exists( 'wp_new_user_notification' ) ) {
	function wp_new_user_notification( $user_id, $deprecated = null, $notify = '' ) {
		global $wp_version, $wp_hasher;

		$bnfw = BNFW::factory();
		$user = get_userdata( $user_id );

		if ( version_compare( $wp_version, '4.3', '>=' ) ) {
			// for WordPress 4.3 and above

			if ( version_compare( $wp_version, '4.3', '=' ) ) {
				$notify = $deprecated;
			} else {
				if ( $deprecated !== null ) {
					_deprecated_argument( __FUNCTION__, '4.3.1' );
				}
			}

			// The blogname option is escaped with esc_html on the way into the database in sanitize_option
			// we want to reverse this for the plain text arena of emails.
			$blogname = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );

			if ( ! $bnfw->notifier->notification_exists( 'admin-user', false ) ) {
				$message = sprintf( esc_html__( 'New user registration on your site %s:' ), $blogname ) . "\r\n\r\n";
				$message .= sprintf( esc_html__( 'Username: %s' ), $user->user_login ) . "\r\n\r\n";
				$message .= sprintf( esc_html__( 'E-mail: %s' ), $user->user_email ) . "\r\n";

				$wp_new_user_notification_email_admin = array(
					'to'      => get_option( 'admin_email' ),
					/* translators: Password change notification email subject. %s: Site title */
					'subject' => __( '[%s] New User Registration' ),
					'message' => $message,
					'headers' => '',
				);

				/**
				 * Filters the contents of the new user notification email sent to the site admin.
				 *
				 * @since 4.9.0
				 *
				 * @param array   $wp_new_user_notification_email {
				 *                                                Used to build wp_mail().
				 *
				 * @type string   $to                             The intended recipient - site admin email address.
				 * @type string   $subject                        The subject of the email.
				 * @type string   $message                        The body of the email.
				 * @type string   $headers                        The headers of the email.
				 * }
				 *
				 * @param WP_User $user                           User object for new user.
				 * @param string  $blogname                       The site title.
				 */
				$wp_new_user_notification_email_admin = apply_filters( 'wp_new_user_notification_email_admin', $wp_new_user_notification_email_admin, $user, $blogname );

				@wp_mail(
					$wp_new_user_notification_email_admin['to'],
					wp_specialchars_decode( sprintf( $wp_new_user_notification_email_admin['subject'], $blogname ) ),
					$wp_new_user_notification_email_admin['message'],
					$wp_new_user_notification_email_admin['headers']
				);
			}

			if ( 'admin' === $notify || empty( $notify ) ) {
				return;
			}

			// Generate something random for a password reset key.
			$key = wp_generate_password( 20, false );

			/** This action is documented in wp-login.php */
			do_action( 'retrieve_password_key', $user->user_login, $key );

			// Now insert the key, hashed, into the DB.
			if ( empty( $wp_hasher ) ) {
				require_once ABSPATH . WPINC . '/class-phpass.php';
				$wp_hasher = new PasswordHash( 8, true );
			}
			$hashed = time() . ':' . $wp_hasher->HashPassword( $key );
                        
                        wp_update_user(
				array(
				'ID' => $user->ID,
				'user_activation_key' => $hashed,
				)
			);
                        
			if ( $bnfw->notifier->notification_exists( 'new-user', false ) ) {
				$notifications = $bnfw->notifier->get_notifications( 'new-user' );
				$password_url  = network_site_url( 'wp-login.php?action=rp&key=' . $key . '&login=' . rawurlencode( $user->user_login ), 'login' );

				foreach ( $notifications as $notification ) {
					$setting = $bnfw->notifier->read_settings( $notification->ID );
					$trigger_notification = apply_filters( 'bnfw_trigger_new-user_notification', true, $setting, $user );

					if ( $trigger_notification ) {
						$bnfw->engine->send_registration_email( $setting, $user, $password_url );
					}
				}
			} else {
				$message = sprintf( esc_html__( 'Username: %s' ), $user->user_login ) . "\r\n\r\n";
				$message .= esc_html__( 'To set your password, visit the following address:' ) . "\r\n\r\n";
				$message .= '<' . network_site_url( "wp-login.php?action=rp&key=$key&login=" . rawurlencode( $user->user_login ), 'login' ) . ">\r\n\r\n";

				$message .= wp_login_url() . "\r\n";

				wp_mail( $user->user_email, sprintf( esc_html__( '[%s] Your username and password info' ), $blogname ), $message );
			}
		} else {

			// for WordPress below 4.3
			$plaintext_pass = $deprecated;

			// The blogname option is escaped with esc_html on the way into the database in sanitize_option
			// we want to reverse this for the plain text arena of emails.
			$blogname = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );

			if ( ! $bnfw->notifier->notification_exists( 'admin-user', false ) ) {
				$message = sprintf( esc_html__( 'New user registration on your site %s:' ), $blogname ) . "\r\n\r\n";
				$message .= sprintf( esc_html__( 'Username: %s' ), $user->user_login ) . "\r\n\r\n";
				$message .= sprintf( esc_html__( 'E-mail: %s' ), $user->user_email ) . "\r\n";

				@wp_mail( get_option( 'admin_email' ), sprintf( esc_html__( '[%s] New User Registration' ), $blogname ), $message );
			}

			if ( empty( $plaintext_pass ) ) {
				return;
			}

			if ( $bnfw->notifier->notification_exists( 'new-user', false ) ) {
				$notifications = $bnfw->notifier->get_notifications( 'new-user' );
				foreach ( $notifications as $notification ) {
					$bnfw->engine->send_registration_email( $bnfw->notifier->read_settings( $notification->ID ), $user, $plaintext_pass );
				}
			} else {
				$message = sprintf( esc_html__( 'Username: %s' ), $user->user_login ) . "\r\n";
				$message .= sprintf( esc_html__( 'Password: %s' ), $plaintext_pass ) . "\r\n";
				$message .= wp_login_url() . "\r\n";

				wp_mail( $user->user_email, sprintf( esc_html__( '[%s] Your username and password' ), $blogname ), $message );
			}
		}
	}
}

if ( ! function_exists( 'wp_password_change_notification' ) ) {
	/**
	 * Notify the blog admin of a user changing password, normally via email.
	 *
	 * @param WP_User $user User object.
	 */
	function wp_password_change_notification( $user ) {
		$bnfw = BNFW::factory();

		if ( $bnfw->notifier->notification_exists( 'admin-password-changed', false ) ) {
			$notifications = $bnfw->notifier->get_notifications( 'admin-password-changed' );

			if ( count( $notifications ) > 0 ) {
				// Ideally there should be only one notification for this type.
				// If there are multiple notification then we will read data about only the last one
				$bnfw->engine->send_notification( $bnfw->notifier->read_settings( end( $notifications )->ID ), $user->ID );
			}
		} else {
			// send a copy of password change notification to the admin
			// but check to see if it's the admin whose password we're changing, and skip this.
			if ( 0 !== strcasecmp( $user->user_email, get_option( 'admin_email' ) ) ) {
				/* translators: %s: user name */
				$message = sprintf( __( 'Password changed for user: %s' ), $user->user_login ) . "\r\n";
				// The blogname option is escaped with esc_html on the way into the database in sanitize_option
				// we want to reverse this for the plain text arena of emails.
				$blogname = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
				/* translators: %s: site title */
				wp_mail( get_option( 'admin_email' ), sprintf( __( '[%s] Password Changed' ), $blogname ), $message );
			}
		}
	}
}
