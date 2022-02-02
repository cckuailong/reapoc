<?php

use AwsWPTC\S3\S3Client;

class WPTC_S3{

	private $s3_client,
			$bucket_name,
			$site_url,
			$config,
			$cached_lifecycle_revision_days,
			$bucket_region;

	public function __construct(){
		$this->config  		 = WPTC_Factory::get('config');
		$this->site_url      = $this->config->get_cloud_root_dir();
	}

	public function init_connection($credentials, $bucket_name){

		$this->bucket_name 	 = $bucket_name;
		$this->bucket_region = $credentials['region'];

		if (!empty($this->bucket_region)) {
			$credentials['signature'] = 'v4';
		}

		try {
			$this->s3_client = S3Client::factory($credentials);
		} catch (Exception $e) {
			wptc_log($e->getMessage(),'-----------$e->getMessage()---init_connection-------------');
			return array( 'error' => $e->getMessage() );
		}
	}

	public function force_init($s3_client, $bucket_name, $region){
		$this->s3_client 	 = $s3_client;
		$this->bucket_name 	 = $bucket_name;
		$this->bucket_region = $region;
	}

	public function is_bucket_exist(){
		wptc_log(func_get_args(), "--------" . __FUNCTION__ . "--------");
		try{

			wptc_log($this->bucket_name,'-----------$this->bucket_name----------------');

			$headBucket = $this->s3_client->headBucket(array(
				'Bucket' => $this->bucket_name,
			));

			wptc_log($headBucket,'-----------$headBucket----------------');

		} catch (Exception $e) {

			$err_msg = $e->getMessage();


			wptc_log($err_msg,'-----------$err_msg----------------');

			if (empty($err_msg)) {
				return array('error' => 'Cannot access bucket.' );
			}

			if (stristr($err_msg, '404 Not Found')) {
				return false;
			}

			return array('error' => $err_msg );
		}

		return true;
	}

	public function enable_versioning(){
		try{
			$response = $this->s3_client->putBucketVersioning(
				array(
					'Bucket' => $this->bucket_name,
					'Status' => 'Enabled',
				)
			);
			wptc_log($response,'-----------$response enable_versioning----------------');
		} catch (Exception $e) {
			$err_msg = $e->getMessage();

			if (empty($err_msg)) {
				return array('error' => 'Failed to enable versioning' );
			}

			return array('error' => $err_msg );
		}

		return true;
	}

	public function get_bucket_lifecycle(){
		wptc_log(func_get_args(), "--------" . __FUNCTION__ . "--------");

		try{

			$response = $this->s3_client->getBucketLifecycleConfiguration(array(
				'Bucket' =>  $this->bucket_name,
			));

			// wptc_log($response,'-----------$response----------------');
		} catch (Exception $e) {

			$err_msg = $e->getMessage();

			wptc_log($err_msg,'-----------$err_msg----------------');

			if (empty($err_msg)) {
				return array('error' => 'Failed to get lifecycle' );
			}

			if ($err_msg === 'The lifecycle configuration does not exist') {
				return array();
			}

			return array('error' => $err_msg );
		}

		return $response;
	}

	public function upsert_site_life_cycle($revision_days = false){

		if (!$revision_days) {
			// Validate
			$response = $this->is_site_life_cycle_present();

			if (isset($response['error'])) {
				return $response;
			}
		}

		$existing_rules = $this->get_existing_life_cycle_rules();
		$new_rule       = $this->get_this_site_rule($revision_days);

		$this->merge_rules($existing_rules, $new_rule);

		$new_rules['Bucket'] = $this->bucket_name;
		$new_rules['Rules']  = $existing_rules;

		// wptc_log($new_rules,'-----------$new_rules----------------');
		try{
			$response = $this->s3_client->putBucketLifecycleConfiguration($new_rules);

			do_action('set_revision_limit_wptc', $this->cached_lifecycle_revision_days);

			wptc_log($response,'-----------$response----------------');
			return $response;

		} catch (Exception $e) {

			$err_msg = $e->getMessage();

			wptc_log($err_msg,'-----------$err_msg----------------');

			if (empty($err_msg)) {
				return array('error' => 'Failed to upsert lifecycle.' );
			}

			return array('error' => $err_msg );
		}
	}

	private function get_this_site_rule($revision_days){

		$revision_days = empty($revision_days) ? $this->config->get_option('eligible_revision_limit') : $revision_days;

		$this->cached_lifecycle_revision_days = $revision_days;

		return array(
			'ID'     => $this->site_url,
			'Status' => 'Enabled',
			//Not  required in new version of SDK so overided SDK schema to support empty string.
    		'Prefix' => 'wp-time-capsule/' . $this->site_url . '/',
			'NoncurrentVersionExpiration' => array(
				'NoncurrentDays' => $revision_days,
			),
			//Supports available for AbortIncompleteMultipartUpload only after < 3.0 SDK so overrided SDK schema.
			'AbortIncompleteMultipartUpload' => array(
				'DaysAfterInitiation' => '5',
	        ),
		);
	}

	private function merge_rules(&$existing_rules, $new_rule){

		if ( empty($existing_rules) || is_string($existing_rules) ) {
			$existing_rules = array();
			return $existing_rules[] = $new_rule;
		}

		foreach ($existing_rules as $key => $rule) {
			if ($rule['ID'] === 'WPTC') {
				unset($existing_rules[$key]);
			}
			if ($rule['ID'] === $this->site_url) {
				$existing_rules[$key] = $new_rule;
				return;
			}
		}

		$existing_rules[] = $new_rule;
	}

	public function is_site_life_cycle_present($return = false){

		$site_rule = $this->get_existing_life_cycle_rules($this_site_only = true);

		if(!empty($site_rule) && $site_rule['Status'] !== 'Enabled' ){
			return false;
		}

		if(empty($site_rule['NoncurrentVersionExpiration']['NoncurrentDays'])){
			return false;
		}

		return ($return) ? $site_rule['NoncurrentVersionExpiration']['NoncurrentDays'] : true;
	}

	public function get_existing_life_cycle_rules($this_site_only = false){
		$response  = $this->get_bucket_lifecycle();

		if (empty($response)) {
			return array();
		}

		if (!empty($response['error'])) {
			return $response['error'];
		}

		$rules = $response->get('Rules');

		// wptc_log($rules,'-----------$rules----------------');

		if (empty($rules)) {
			return array();
		}

		if (!$this_site_only) {
			return $rules;
		}

		$site_rule = array();

		foreach ($rules as $key => $rule) {
			if ($rule['ID'] === $this->site_url) {
				$site_rule = $rule;
				break;
			}
		}

		if (empty($site_rule)) {
			return array();
		}

		return $site_rule;
	}

	public function create_verification_file(){
		try {
			$response = $this->s3_client->putObject(array(
				'Body' => '',
				'Bucket' => $this->bucket_name,
				'Key' => '/wp-time-capsule/' .  $this->site_url . '/' . WPTC_S3_VERIFICATION_FILE,
			));

			$response = wptc_get_result_array_s3($response);
			wptc_log($response,'-----------$response createVerificationFile----------------');

			return $response;
		} catch (Exception $e) {

			$err_msg = $e->getMessage();

			if (empty($err_msg)) {
				return array('error' => 'Failed to create Backup Storage Space in cloud' );
			}

			return array('error' => $err_msg );
		}
	}

	public function create_bucket(){

		$response = $this->is_bucket_exist();

		wptc_log($response,'-----------$response----------------');

		if(!empty($response['error'])){
			return $response;
		}

		if ($response) {
			return true;
		}

		try{



			$config = array(
				'Bucket'  			 => $this->bucket_name,
			);

			if ($this->bucket_region !== 'us-east-1') {
				$config['LocationConstraint'] = $this->bucket_region ;
			}

			wptc_log($config,'-----------$config----------------');

			$response =  $this->s3_client->createBucket($config);
			wptc_log($response,'-----------$response----------------');
		} catch (Exception $e) {

			$err_msg = $e->getMessage();

			if (empty($err_msg)) {
				return array('error' => 'Failed to create bucket' );
			}

			return array('error' => $err_msg );
		}
	}

	public function delete_all_lifecycles(){
		try {
			$response = $this->s3_client->deleteBucketLifecycle(array(
				'Bucket' => $this->bucket_name,
			));

			return $response;
		} catch (Exception $e) {

			$err_msg = $e->getMessage();

			if (empty($err_msg)) {
				return array('error' => 'Failed to delete all lifecycles' );
			}

			return array('error' => $err_msg );
		}
	}
}
