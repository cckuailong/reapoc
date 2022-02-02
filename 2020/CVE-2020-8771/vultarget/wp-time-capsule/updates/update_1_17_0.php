<?php

use AwsWPTC\S3\S3Client;

Class Wptc_Update_1_17_0{

	private $s3_client;
	private $config;

	public function __construct($config){
		$this->config = $config;

		if($this->init_S3() === false){
			return;
		}

		$this->upgrade_lifecycle();
	}

	private function init_S3(){

		include_once WPTC_PLUGIN_DIR . 'S3/class.s3.php';
		$this->s3_client = new WPTC_S3();

		$response = $this->s3_client->init_connection(
			$this->get_credentials(),
			$this->config->get_option('as3_bucket_name')
		);

		if ($response['error']) {
			wptc_log($response,'-----------Wptc_Update_1_17_0 upgrade failed----------------');
			return false;
		}

		return true;
	}

	private function get_credentials(){
		$as3_bucket_region = $this->config->get_option('as3_bucket_region');
		$as3_access_key    = $this->config->get_option('as3_access_key');
		$as3_secure_key    = $this->config->get_option('as3_secure_key');

		return array (
				'version' => 'latest',
				'region'  => $as3_bucket_region,
				'key'     => $as3_access_key,
				'secret'  => trim(str_replace(' ', '+', $as3_secure_key)),
			);
	}

	private function upgrade_lifecycle() {
		$current_revision = $this->s3_client->is_site_life_cycle_present($return = true);

		wptc_log($current_revision,'-----------$current_revision----------------');

		if ($current_revision >= 365 ) {
			wptc_log(array(),'-----------Already in 365 days----------------');
			return ;
		}

		$this->s3_client->delete_all_lifecycles();

		$this->s3_client->upsert_site_life_cycle();
	}
}