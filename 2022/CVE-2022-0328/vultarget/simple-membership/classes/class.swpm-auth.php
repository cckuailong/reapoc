<?php

class SwpmAuth {

	public $protected;
	public $permitted;
	private $isLoggedIn;
	private $lastStatusMsg;
	private static $_this;
	public $userData;

	private function __construct() {
		//check if we need to display custom message on the login form
		$custom_msg = filter_input( INPUT_COOKIE, 'swpm-login-form-custom-msg', FILTER_SANITIZE_STRING );
		if ( ! empty( $custom_msg ) ) {
			$this->lastStatusMsg = $custom_msg;
			//let's 'unset' the cookie
			setcookie( 'swpm-login-form-custom-msg', '', time() - 3600, COOKIEPATH, COOKIE_DOMAIN );
		}
		$this->isLoggedIn = false;
		$this->userData   = null;
		$this->protected  = SwpmProtection::get_instance();
	}

	private function init() {
		$valid = $this->validate();
		//SwpmLog::log_auth_debug("init:". ($valid? "valid": "invalid"), true);
		if ( ! $valid ) {
			$this->authenticate();
		}
	}

	public static function get_instance() {
		if ( empty( self::$_this ) ) {
			self::$_this = new SwpmAuth();
			self::$_this->init();
		}
		return self::$_this;
	}

	private function authenticate( $user = null, $pass = null ) {
		global $wpdb;
		$swpm_password  = empty( $pass ) ? filter_input( INPUT_POST, 'swpm_password' ) : $pass;
		$swpm_user_name = empty( $user ) ? apply_filters( 'swpm_user_name', filter_input( INPUT_POST, 'swpm_user_name' ) ) : $user;

		if ( ! empty( $swpm_user_name ) && ! empty( $swpm_password ) ) {
			//SWPM member login request.
			//Trigger action hook that can be used to check stuff before the login request is processed by the plugin.
			$args = array(
				'username' => $swpm_user_name,
				'password' => $swpm_password,
			);
			do_action( 'swpm_before_login_request_is_processed', $args );

			//First, lets make sure this user is not already logged into the site as an "Admin" user. We don't want to override that admin login session.
			if ( current_user_can( 'administrator' ) ) {
				//This user is logged in as ADMIN then trying to do another login as a member. Stop the login request processing (we don't want to override your admin login session).
				$wp_profile_page = SIMPLE_WP_MEMBERSHIP_SITE_HOME_URL . '/wp-admin/profile.php';
				$error_msg       = '';
				$error_msg      .= '<p>' . SwpmUtils::_( 'Warning! Simple Membership plugin cannot process this login request to prevent you from getting logged out of WP Admin accidentally.' ) . '</p>';
				$error_msg      .= '<p><a href="' . $wp_profile_page . '" target="_blank">' . SwpmUtils::_( 'Click here' ) . '</a>' . SwpmUtils::_( ' to see the profile you are currently logged into in this browser.' ) . '</p>';
				$error_msg      .= '<p>' . SwpmUtils::_( 'You are logged into the site as an ADMIN user in this browser. First, logout from WP Admin then you will be able to log in as a normal member.' ) . '</p>';
				$error_msg      .= '<p>' . SwpmUtils::_( 'Alternatively, you can use a different browser (where you are not logged-in as ADMIN) to test the membership login.' ) . '</p>';
				$error_msg      .= '<p>' . SwpmUtils::_( 'Your normal visitors or members will never see this message. This message is ONLY for ADMIN user.' ) . '</p>';
				wp_die( $error_msg );
			}

			//If captcha is present and validation failed, it returns an error string. If validation succeeds, it returns an empty string.
			$captcha_validation_output = apply_filters( 'swpm_validate_login_form_submission', '' );
			if ( ! empty( $captcha_validation_output ) ) {
				$this->lastStatusMsg = SwpmUtils::_( 'Captcha validation failed on login form.' );
				return;
			}

			if ( is_email( $swpm_user_name ) ) {//User is trying to log-in using an email address
				$email    = sanitize_email( $swpm_user_name );
				$query    = $wpdb->prepare( 'SELECT user_name FROM ' . $wpdb->prefix . 'swpm_members_tbl WHERE email = %s', $email );
				$username = $wpdb->get_var( $query );
				if ( $username ) {//Found a user record
					$swpm_user_name = $username; //Grab the usrename value so it can be used in the authentication process.
					SwpmLog::log_auth_debug( 'Authentication request using email address: ' . $email . ', Found a user record with username: ' . $swpm_user_name, true );
				}
			}

			//Lets process the request. Check username and password
			$user = sanitize_user( $swpm_user_name );
			$pass = trim( $swpm_password );
			SwpmLog::log_auth_debug( 'Authentication request - Username: ' . $swpm_user_name, true );

			$query          = 'SELECT * FROM ' . $wpdb->prefix . 'swpm_members_tbl WHERE user_name = %s';
			$userData       = $wpdb->get_row( $wpdb->prepare( $query, $user ) );
			$this->userData = $userData;
			if ( ! $userData ) {
				$this->isLoggedIn    = false;
				$this->userData      = null;
				$this->lastStatusMsg = SwpmUtils::_( 'User Not Found.' );
				return false;
			}
			$check = $this->check_password( $pass, $userData->password );
			if ( ! $check ) {
				$this->isLoggedIn    = false;
				$this->userData      = null;
				$this->lastStatusMsg = SwpmUtils::_( 'Password Empty or Invalid.' );
				return false;
			}
			if ( $this->check_constraints() ) {
				$rememberme = filter_input( INPUT_POST, 'rememberme' );
				$remember   = empty( $rememberme ) ? false : true;
				$this->set_cookie( $remember );
				$this->isLoggedIn    = true;
				$this->lastStatusMsg = 'Logged In.';
				SwpmLog::log_auth_debug( 'Authentication successful for username: ' . $user . '. Executing swpm_login action hook.', true );
				do_action( 'swpm_login', $user, $pass, $remember );
				return true;
			}
		}
		return false;
	}

	private function check_constraints() {
		if ( empty( $this->userData ) ) {
			return false;
		}
		global $wpdb;
		$enable_expired_login = SwpmSettings::get_instance()->get_value( 'enable-expired-account-login', '' );

		//Update the last accessed date and IP address for this login attempt. $wpdb->update(table, data, where, format, where format)
		$last_accessed_date = current_time( 'mysql' );
		$last_accessed_ip   = SwpmUtils::get_user_ip_address();
		$wpdb->update(
			$wpdb->prefix . 'swpm_members_tbl',
			array(
				'last_accessed'         => $last_accessed_date,
				'last_accessed_from_ip' => $last_accessed_ip,
			),
			array( 'member_id' => $this->userData->member_id ),
			array( '%s', '%s' ),
			array( '%d' )
		);

		//Check the member's account status.
		$can_login = true;
		if ( $this->userData->account_state == 'inactive' && empty( $enable_expired_login ) ) {
			$this->lastStatusMsg = SwpmUtils::_( 'Account is inactive.' );
			$can_login           = false;
		} elseif ( ( $this->userData->account_state == 'expired' ) && empty( $enable_expired_login ) ) {
			$this->lastStatusMsg = SwpmUtils::_( 'Account has expired.' );
			$can_login           = false;
		} elseif ( $this->userData->account_state == 'pending' ) {
			$this->lastStatusMsg = SwpmUtils::_( 'Account is pending.' );
			$can_login           = false;
		} elseif ( $this->userData->account_state == 'activation_required' ) {
			$resend_email_url    = add_query_arg(
				array(
					'swpm_resend_activation_email' => '1',
					'swpm_member_id'               => $this->userData->member_id,
				),
				get_home_url()
			);
			$msg                 = sprintf( SwpmUtils::_( 'You need to activate your account. If you didn\'t receive an email then %s to resend the activation email.' ), '<a href="' . $resend_email_url . '">' . SwpmUtils::_( 'click here' ) . '</a>' );
			$this->lastStatusMsg = $msg;
			$can_login           = false;
		}

		if ( ! $can_login ) {
			$this->isLoggedIn = false;
			$this->userData   = null;
			return false;
		}

		if ( SwpmUtils::is_subscription_expired( $this->userData ) ) {
			if ( $this->userData->account_state == 'active' ) {
				$wpdb->update( $wpdb->prefix . 'swpm_members_tbl', array( 'account_state' => 'expired' ), array( 'member_id' => $this->userData->member_id ), array( '%s' ), array( '%d' ) );
			}
			if ( empty( $enable_expired_login ) ) {
				$this->lastStatusMsg = SwpmUtils::_( 'Account has expired.' );
				$this->isLoggedIn    = false;
				$this->userData      = null;
				return false;
			}
		}

		$this->permitted     = SwpmPermission::get_instance( $this->userData->membership_level );
		$this->lastStatusMsg = SwpmUtils::_( 'You are logged in as:' ) . $this->userData->user_name;
		$this->isLoggedIn    = true;
		return true;
	}

	private function check_password( $plain_password, $hashed_pw ) {
		global $wp_hasher;
		if ( empty( $plain_password ) ) {
			return false;
		}
		if ( empty( $wp_hasher ) ) {
			require_once ABSPATH . 'wp-includes/class-phpass.php';
			$wp_hasher = new PasswordHash( 8, true );
		}
		return $wp_hasher->CheckPassword( $plain_password, $hashed_pw );
	}

	public function match_password( $password ) {
		if ( ! $this->is_logged_in() ) {
			return false;
		}
		return $this->check_password( $password, $this->get( 'password' ) );
	}

	public function login_to_swpm_using_wp_user( $user ) {
		if ( $this->isLoggedIn ) {
			return false;
		}
		$email  = $user->user_email;
		$member = SwpmMemberUtils::get_user_by_email( $email );
		if ( empty( $member ) ) {
			//There is no swpm profile with this email.
			return false;
		}
		$this->userData   = $member;
		$this->isLoggedIn = true;
		$this->set_cookie();
		SwpmLog::log_auth_debug( 'Member has been logged in using WP User object.', true );
		$this->check_constraints();
		return true;
	}

	public function login( $user, $pass, $remember = '', $secure = '' ) {
		SwpmLog::log_auth_debug( 'SwpmAuth::login()', true );
		if ( $this->isLoggedIn ) {
			return;
		}
		if ( $this->authenticate( $user, $pass ) && $this->validate() ) {
			$this->set_cookie( $remember, $secure );
		} else {
			$this->isLoggedIn = false;
			$this->userData   = null;
		}
		return $this->lastStatusMsg;
	}

	public function logout() {
		if ( ! $this->isLoggedIn ) {
			return;
		}
		setcookie( SIMPLE_WP_MEMBERSHIP_AUTH, ' ', time() - YEAR_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN );
		setcookie( SIMPLE_WP_MEMBERSHIP_SEC_AUTH, ' ', time() - YEAR_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN );
		$this->userData      = null;
		$this->isLoggedIn    = false;
		$this->lastStatusMsg = SwpmUtils::_( 'Logged Out Successfully.' );
		do_action( 'swpm_logout' );
	}

	private function set_cookie( $remember = '', $secure = '' ) {
		if ( $remember ) {
			$expiration = time() + 1209600; //14 days
			$expire     = $expiration + 43200; //12 hours grace period
		} else {
			$expiration = time() + 259200; //3 days.
			$expire     = $expiration; //The minimum cookie expiration should be at least a few days.
		}

		$expire = apply_filters( 'swpm_auth_cookie_expiry_value', $expire );

		setcookie( 'swpm_in_use', 'swpm_in_use', $expire, COOKIEPATH, COOKIE_DOMAIN );

		$expiration_timestamp = SwpmUtils::get_expiration_timestamp( $this->userData );
		$enable_expired_login = SwpmSettings::get_instance()->get_value( 'enable-expired-account-login', '' );
		// make sure cookie doesn't live beyond account expiration date.
		// but if expired account login is enabled then ignore if account is expired
		$expiration = empty( $enable_expired_login ) ? min( $expiration, $expiration_timestamp ) : $expiration;
		$pass_frag  = substr( $this->userData->password, 8, 4 );
		$scheme     = 'auth';
		if ( ! $secure ) {
			$secure = is_ssl();
		}
		$key              = self::b_hash( $this->userData->user_name . $pass_frag . '|' . $expiration, $scheme );
		$hash             = hash_hmac( 'md5', $this->userData->user_name . '|' . $expiration, $key );
		$auth_cookie      = $this->userData->user_name . '|' . $expiration . '|' . $hash;
		$auth_cookie_name = $secure ? SIMPLE_WP_MEMBERSHIP_SEC_AUTH : SIMPLE_WP_MEMBERSHIP_AUTH;
		setcookie( $auth_cookie_name, $auth_cookie, $expire, COOKIEPATH, COOKIE_DOMAIN, $secure, true );
	}

	private function validate() {
		$auth_cookie_name = is_ssl() ? SIMPLE_WP_MEMBERSHIP_SEC_AUTH : SIMPLE_WP_MEMBERSHIP_AUTH;
		if ( ! isset( $_COOKIE[ $auth_cookie_name ] ) || empty( $_COOKIE[ $auth_cookie_name ] ) ) {
			return false;
		}
		$cookie_elements = explode( '|', $_COOKIE[ $auth_cookie_name ] );
		if ( count( $cookie_elements ) != 3 ) {
			return false;
		}

		//SwpmLog::log_auth_debug("validate() - " . $_COOKIE[$auth_cookie_name], true);
		list($username, $expiration, $hmac) = $cookie_elements;
		$expired                            = $expiration;
		// Allow a grace period for POST and AJAX requests
		if ( defined( 'DOING_AJAX' ) || 'POST' == $_SERVER['REQUEST_METHOD'] ) {
			$expired += HOUR_IN_SECONDS;
		}
		// Quick check to see if an honest cookie has expired
		if ( $expired < time() ) {
			$this->lastStatusMsg = SwpmUtils::_( 'Session Expired.' ); //do_action('auth_cookie_expired', $cookie_elements);
			SwpmLog::log_auth_debug( 'validate() - Session Expired', true );
			return false;
		}

		global $wpdb;
		$query = ' SELECT * FROM ' . $wpdb->prefix . 'swpm_members_tbl WHERE user_name = %s';
		$user  = $wpdb->get_row( $wpdb->prepare( $query, $username ) );
		if ( empty( $user ) ) {
			$this->lastStatusMsg = SwpmUtils::_( 'Invalid Username' );
			return false;
		}

		$pass_frag = substr( $user->password, 8, 4 );
		$key       = self::b_hash( $username . $pass_frag . '|' . $expiration );
		$hash      = hash_hmac( 'md5', $username . '|' . $expiration, $key );
		if ( $hmac != $hash ) {
			$this->lastStatusMsg = SwpmUtils::_( 'Please login again.' );
			SwpmLog::log_auth_debug( 'validate() - Bad Hash', true );
                        do_action('swpm_validate_login_hash_mismatch');
			wp_logout(); //Force logout of WP user session to clear the bad hash.
			return false;
		}

		if ( $expiration < time() ) {
			$GLOBALS['login_grace_period'] = 1;
		}
		$this->userData = $user;
		return $this->check_constraints();
	}

	public static function b_hash( $data, $scheme = 'auth' ) {
		$salt = wp_salt( $scheme ) . 'j4H!B3TA,J4nIn4.';
		return hash_hmac( 'md5', $data, $salt );
	}

	public function is_logged_in() {
		return $this->isLoggedIn;
	}

	public function get( $key, $default = '' ) {
		if ( isset( $this->userData->$key ) ) {
			return $this->userData->$key;
		}
		if ( isset( $this->permitted->$key ) ) {
			return $this->permitted->$key;
		}
		if ( ! empty( $this->permitted ) ) {
			return $this->permitted->get( $key, $default );
		}
		return $default;
	}

	public function get_message() {
		return $this->lastStatusMsg;
	}

	public function get_expire_date() {
		if ( $this->isLoggedIn ) {
			return SwpmUtils::get_formatted_expiry_date( $this->get( 'subscription_starts' ), $this->get( 'subscription_period' ), $this->get( 'subscription_duration_type' ) );
		}
		return '';
	}

	public function delete() {
		if ( ! $this->is_logged_in() ) {
			return;
		}
		$user_name = $this->get( 'user_name' );
		$user_id   = $this->get( 'member_id' );
		$subscr_id = $this->get( 'subscr_id' );
		$email     = $this->get( 'email' );

		$this->logout();
                wp_clear_auth_cookie();

		SwpmMembers::delete_swpm_user_by_id( $user_id );
		SwpmMembers::delete_wp_user( $user_name );
	}

	public function reload_user_data() {
		if ( ! $this->is_logged_in() ) {
			return;
		}
		global $wpdb;
		$query          = 'SELECT * FROM ' . $wpdb->prefix . 'swpm_members_tbl WHERE member_id = %d';
		$this->userData = $wpdb->get_row( $wpdb->prepare( $query, $this->userData->member_id ) );
	}

	public function is_expired_account() {
		if ( ! $this->is_logged_in() ) {
			return null;
		}
		$account_status = $this->get( 'account_state' );
		if ( $account_status == 'expired' || $account_status == 'inactive' ) {
			//Expired or Inactive accounts are both considered to be expired.
			return true;
		}
		return false;
	}

}
