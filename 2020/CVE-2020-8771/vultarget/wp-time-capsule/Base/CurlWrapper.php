<?php

abstract class Wptc_Base_Curl_Wrapper {
	protected $domain_url;
	protected $post_type;
	protected $cron_posts_always_needed;

	public function __construct() {
		$this->init();
	}

	private function init() {

	}

	protected abstract function set_defaults();

	public function refresh_always_needed() {
		$app_id = $this->config->get_option('appID');

		$email = trim($this->config->get_option('main_account_email', true));
		$email_encoded = base64_encode($email);

		$pwd = trim($this->config->get_option('main_account_pwd', true));
		$pwd_encoded = base64_encode($pwd);

		$this->cron_posts_always_needed = array(
			'app_id' => $app_id,
			'email' => $email_encoded,
			'pwd' => $pwd_encoded,
			'site_url' => WPTC_Factory::get('config')->get_option('site_url_wptc'),
		);
	}

	public function do_call($route_path, $post_arr) {
		if(empty($this->cron_posts_always_needed['app_id'])){
			$this->refresh_always_needed();
		}

		$post_arr = array_merge($this->cron_posts_always_needed, $post_arr);
		$wptc_token = WPTC_Factory::get('config')->get_option('wptc_token');
		$post_arr['version'] = WPTC_VERSION;
		$post_arr['source'] = 'WPTC';
		$post_arr['cron_url'] = wptc_add_trailing_slash(WPTC_Factory::get('config')->get_option('site_url_wptc'));

		if (WPTC_DEBUG) {
			wptc_log_server_request($post_arr, '----REQUEST-----', WPTC_CRSERVER_URL . "/" . $route_path);
		}
		$chb = curl_init();
		// wptc_log($post_arr,'--------------$post_arr-------------');
		curl_setopt($chb, CURLOPT_URL, $this->domain_url . "/" . ltrim($route_path, '/'));
		curl_setopt($chb, CURLOPT_CUSTOMREQUEST, $this->post_type);
		// curl_setopt($chb, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($chb, CURLOPT_POSTFIELDS, http_build_query($post_arr, '', '&'));
		curl_setopt($chb, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($chb, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($chb, CURLOPT_SSL_VERIFYHOST, FALSE);

		$headers[] = WPTC_DEFAULT_CURL_CONTENT_TYPE;

		if(!empty($wptc_token)){
			$headers[] = "Authorization: $wptc_token";
		}

		curl_setopt($chb, CURLOPT_HTTPHEADER, $headers );

		if (!defined('WPTC_CURL_TIMEOUT')) {
			define('WPTC_CURL_TIMEOUT', 20);
		}
		curl_setopt($chb, CURLOPT_TIMEOUT, WPTC_CURL_TIMEOUT);

		$pushresult = curl_exec($chb);

		if (WPTC_DEBUG) {
			wptc_log_server_request($pushresult, '-----RESPONSE-----');
		}

		return $pushresult;
	}
}
