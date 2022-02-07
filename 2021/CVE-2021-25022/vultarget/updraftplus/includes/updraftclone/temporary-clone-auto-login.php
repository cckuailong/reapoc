<?php

if (!defined('ABSPATH')) die('No direct access allowed');

class UpdraftPlus_Temporary_Clone_Auto_Login {
	
	/**
	 * Constructor for the class.
	 */
	public function __construct() {
		if (!empty($_REQUEST['uc_auto_login'])) add_action('wp_loaded', array($this, 'handle_url_actions'));
	}

	/**
	 * Log in the indicated user, if no user is currently logged in. Do verification before calling this function.
	 *
	 * @param WP_User $user - WP user object
	 */
	public function autologin_user($user) {
		if (is_user_logged_in()) return;
		if (!is_object($user) || !is_a($user, 'WP_User')) return;
		delete_user_meta($user->ID, 'uc_allow_auto_login');
		wp_set_current_user($user->ID, $user->user_login);
		wp_set_auth_cookie($user->ID);
		try {
			// WooCommerce (3.4.4) dies here. We catch and carry on to avoid confusing the user about something that nothing can be done about / is a one-time issue.
			do_action('wp_login', $user->user_login);
			if (wp_redirect(admin_url())) exit;
		} catch (Exception $e) {
			$log_message = 'Exception ('.get_class($e).') occurred during the wp_login action call: '.$e->getMessage().' (Code: '.$e->getCode().', line '.$e->getLine().' in '.$e->getFile().')';
			error_log($log_message);
		// @codingStandardsIgnoreLine
		} catch (Error $e) {
			$log_message = 'PHP Fatal error ('.get_class($e).') occurred during the wp_login action call. Error Message: '.$e->getMessage().' (Code: '.$e->getCode().', line '.$e->getLine().' in '.$e->getFile().')';
			error_log($log_message);
		}
	}

	/**
	 * Pass in a WP_User object to get a login hash. The caller should/must first check that this user is aloud to autologin
	 *
	 * @param WP_User         $user     - WordPress user object
	 * @param boolean|integer $use_time - false or a timestamp
	 *
	 * @return string                   - a hash to log the user in
	 */
	public static function get_autologin_key($user, $use_time = false) {
		if (false === $use_time) $use_time = time();
		// Start of day
		$use_time = $use_time - ($use_time % 86400);
		if (!defined('UPDRAFTPLUS_UNIQUE_TOKEN')) return;
		$hash_it = $user->ID.'_'.$use_time.'_'.UPDRAFTPLUS_UNIQUE_TOKEN;
		$hash = hash('sha256', $hash_it);
		return $hash;
	}

	/**
	 * Called upon the WP action wp_loaded
	 *
	 * @return void
	 */
	public function handle_url_actions() {

		if (!isset($_SERVER['REQUEST_METHOD']) || 'GET' != $_SERVER['REQUEST_METHOD'] || !isset($_REQUEST['uc_auto_login'])) return;

		if (0 == get_current_user_id()) {

			if (isset($_REQUEST['uc_login']) && '' !== $_REQUEST['uc_login'] && !empty($_REQUEST['uc_lkey'])) {

				if ($this->auto_login_key_matches($_REQUEST['uc_lkey'], $_REQUEST['uc_login'])) {
					$login_user = get_user_by('login', $_REQUEST['uc_login']);
					$allow_autolink = get_user_meta($login_user->ID, 'uc_allow_auto_login');

					if ($allow_autolink) {
						$this->autologin_user($login_user);
					}
				}
			}
		}
	}

	/**
	 * Check an auto-login key. This does not perform any checks on the user - the caller should do these (e.g. do not allow for privileged users)
	 *
	 * @param String $check_key  - the key to check for validity
	 * @param String $user_login - login for the user renewing his licences
	 *
	 * @return boolean           - indicates if the check was successful or not
	 */
	private function auto_login_key_matches($check_key, $user_login) {

		$login_user = get_user_by('login', $user_login);
		if (is_a($login_user, 'WP_User')) {
			$time_now = time();
			for ($i=0; $i <= apply_filters('uc_autologinexpirydays', 3); $i++) {
				$key = $this->get_autologin_key($login_user, $time_now - 86400*$i);
				if ($key && $key == $check_key) {
					return true;
				}
			}
		}

		return false;
	}
}

if (defined('UPDRAFTPLUS_THIS_IS_CLONE')) {
	new UpdraftPlus_Temporary_Clone_Auto_Login();
}
