<?php

class DomainCheckAdminAjax {
	public static function ajax_domain_search($action = null, $domain = null) {
		if (isset($_POST['action']) && $_POST['action'] == 'domain_search'
			&& isset($_POST['domain']) && $_POST['domain']) {
			$use_cache = false;
			if (isset($_POST['cache'])) {
				$use_cache = $_POST['cache'];
			}
			$force_owned = false;
			if (isset($_POST['force_owned'])) {
				$force_owned = true;
			}
			$force_watch = false;
			if (isset($_POST['force_watch']) && $_POST['force_watch']) {
				$force_watch = true;
			}
			$ssl = false;
			if (isset($_POST['force_ssl']) && $_POST['force_ssl']) {
				$ssl = true;
			}
			if ($ssl) {
				DomainCheckSearch::ssl_search($_POST['domain'], $force_watch, true);
				return;
			}
			DomainCheckSearch::domain_search($_POST['domain'], $use_cache, $force_owned, $force_watch, true);
		}
	}

	public static function ajax_settings() {
		if (isset($_POST['method'])) {
			switch($_POST['method']) {
				case 'email_additional_emails':
					if (isset($_POST['email_additional_emails'])) {
						//check if emails exist...
						$email_arr = explode("\n", $_POST['email_additional_emails']);
						foreach ($email_arr as $email_arr_idx => $email) {
							$email = sanitize_email($email);
							if (is_email($email)) {
								$email_arr[$email_arr_idx] = $email;
							} else {
								unset($email_arr[$email_arr_idx]);
							}
						}
						if (!is_array($email_arr) || !count($email_arr)) {
							$email_arr = array();
						}
						update_option(DomainCheckConfig::OPTIONS_PREFIX . $_POST['method'], $email_arr);
						self::ajax_success(array('message' => 'Success! Setting updated!'));
					}
					break;
				case 'email_primary_email':
					if (isset($_POST['email_primary_email'])) {
						//check if emails exist...
						$email = $_POST['email_primary_email'];
						$email = sanitize_email($email);
						if (is_email($email)) {
							update_option(DomainCheckConfig::OPTIONS_PREFIX . $_POST['method'], $email);
							self::ajax_success(array('message' => 'Success! Setting updated!'));
						} else {

						}
					}
					break;
				case 'domain_extension_favorites':
					if (isset($_POST['domain_extension_favorites'])) {
						$item_arr = explode("\n", $_POST['domain_extension_favorites']);
						foreach ($item_arr as $item_arr_idx => $item) {
							if (strpos($item, '.') === 0) {
								$item = substr($item, 1, strlen($item) - 1);
							}
							$item = strtolower($item);
							if (DomainCheckWhoisData::extension_supported($item)) {
								$item_arr[$item_arr_idx] = $item;
							} else {
								unset($item_arr[$item_arr_idx]);
							}
						}
						if (!is_array($item_arr) || !count($item_arr)) {
							$item_arr = DomainCheckConfig::$options[DomainCheckConfig::OPTIONS_PREFIX . 'settings']['domain_extension_favorites'];
						}
						update_option(DomainCheckConfig::OPTIONS_PREFIX . $_POST['method'], $item_arr);
						self::ajax_success(array('message' => 'Success! Setting updated!'));
					}
					break;
				case 'coupons_primary_site':
					if (isset($_POST['coupons_primary_site'])) {
						//check if emails exist...
						$site = $_POST['coupons_primary_site'];
						$data = DomainCheckCouponData::get_data();
						if (isset($data[$site])) {
							update_option(DomainCheckConfig::OPTIONS_PREFIX . $_POST['method'], $site);
							self::ajax_success(array('message' => 'Success! Setting updated!'));
						}
					}
					break;
				case 'email_schedule_cron':
					if (isset($_POST['email_schedule_cron'])) {
						$schedule = $_POST['email_schedule_cron'];
						$ret = DomainCheckCron::cron_schedule( 'domain_check_cron_email', $schedule );
						if ( $ret ) {
							self::ajax_success( array( 'message' => 'Success! Setting updated!' ) );
						} else {
							self::ajax_success( array( 'message' => 'Error! Schedule not updated!' ) );
						}
					}
					break;
				case 'email_test':
					if (isset($_POST['email_address'])) {
						$email = trim($_POST['email_address']);
						$email = sanitize_email($email);
						if (is_email($email)) {
							$ret = DomainCheckEmail::email_test($email);
							if (!is_array($ret)) {
								self::ajax_success(array('message' => 'Success! Email sent to ' . $email . '!'));
							} else {
								self::ajax_error('Error sending email to ' . $email . '. ' . $ret['error']);
							}
						} else {
							self::ajax_error($email . ' is not a valid email address.');
						}
					}
					break;
			}
		}
		self::ajax_error('Setting not updated.');
	}

	public static function ajax_success($data) {
		$data = array(
			'success' => 1,
			'data' => $data
		);
		echo json_encode($data);
		wp_die();
	}

	public static function ajax_error($message, $code = 0) {
		$data = array(
			'error' => $message
		);
		if ($code) {
			$data['code'] = $code;
		}
		echo json_encode($data);
		wp_die();
	}
}