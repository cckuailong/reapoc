<?php

class Wptc_User_Service_Curl_Wrapper extends Wptc_Base_Curl_Wrapper {
	protected $domain_url;
	protected $post_type;

	public function __construct() {
		$this->init();
	}

	private function init() {
		$this->set_domain_url_and_post_type();
	}

	protected function set_domain_url_and_post_type() {
		$this->domain_url = WPTC_USER_SERVICE_URL;
		$this->post_type = 'POST';
	}
}