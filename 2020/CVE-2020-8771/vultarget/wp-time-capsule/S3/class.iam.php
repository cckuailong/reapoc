<?php

use AwsWPTC\Iam\IamClient;

class WPTC_IAM_S3{

	private $config,
			$iam_client,
			$s3_client,
			$site_url,
			$bucket_name,
			$hashed_url;

	public function __construct(){
		$this->init();
		$this->set_site_details();
	}

	public function authorize_full_access(){
		wptc_log(func_get_args(), "--------" . __FUNCTION__ . "--------");
		$credentials = $this->get_credentials();

		try {
			$this->iam_client =  IamClient::factory($credentials);
		} catch (Exception $e) {
			wptc_log($e->getMessage(),'-----------$e->getMessage()------authorize_full_access----------');
			return array( 'error' => $e->getMessage() );
		}


		return $this->init_S3_client($credentials);
	}

	public function authorize_restricted_access(){
		wptc_log(func_get_args(), "--------" . __FUNCTION__ . "--------");
		$credentials = $this->get_credentials();

		return $this->init_S3_client($credentials);
	}

	private function init_S3_client($credentials){
		wptc_log(func_get_args(), "--------" . __FUNCTION__ . "--------");

		include_once WPTC_PLUGIN_DIR . 'S3/class.s3.php';

		$this->s3_client = new WPTC_S3();

		// wptc_log($this->s3_client,'-----------$this->s3_client----------------');
		
		return $this->s3_client->init_connection($credentials, $this->bucket_name);
	}

	private function init(){
		$this->config = WPTC_Factory::get('config');
	}

	private function set_site_details(){
		$this->site_url    = $this->config->get_cloud_root_dir();
		$this->hashed_url  = 'WPTC_'. md5($this->site_url);
		$this->bucket_name = $this->config->get_option('as3_bucket_name');
	}

	private function get_credentials(){
		$access_key    = $this->config->get_option('as3_access_key');
		$secure_key    = $this->config->get_option('as3_secure_key');

		return array(
			'version' => 'latest',
			'region'  => $this->config->get_option('as3_bucket_region'),
			'key'     => trim($access_key),
			'secret'  => trim(str_replace(' ', '+', $secure_key)),
		);
	}

	private function save_keys($response){
		wptc_log(func_get_args(), "--------" . __FUNCTION__ . "--------");
		$this->config->set_option('as3_access_key', $response['access_key_id']);
		$this->config->set_option('as3_secure_key', $response['access_key_secret']);
		$this->config->set_option('is_auto_generated_iam', true);
	}

	private function get_create_access_key_result($response) {
		if(empty($response)){
			return false;
		}

		$response_arr = [
			'access_key_id' => $response['AccessKey']['AccessKeyId'],
			'access_key_secret' => $response['AccessKey']['SecretAccessKey'],
		];

		return $response_arr;
	}

	public function process_full_access(){
		wptc_log(func_get_args(), "--------" . __FUNCTION__ . "--------");

		$this->delete_user_if_already_existed();

		$create_bucket     = $this->s3_client->create_bucket();
		wptc_log($create_bucket,'----------$create_bucket-----------------');

		if (isset($create_bucket['error'])) {
			return $create_bucket;
		}

		wptc_log(array(),'-----------Bucket created successfully----------------');

		$user_created_result     = $this->create_new_IAM_user();
		wptc_log($user_created_result,'----------$user_created_result-----------------');

		if (isset($user_created_result['error'])) {
			return $user_created_result;
		}

		wptc_log(array(),'-----------New user created successfully----------------');

		$key_created_result      = $this->create_new_access_key();
		wptc_log($key_created_result,'----------$key_created_result-----------------');

		if (isset($key_created_result['error'])) {
			return $key_created_result;
		}

		wptc_log(array(),'-----------New access key created successfully----------------');

		$policy_created_result 	 = $this->create_policy();
		wptc_log($policy_created_result,'----------$policy_created_result-----------------');

		if (isset($policy_created_result['error'])) {
			return $policy_created_result;
		}

		wptc_log(array(),'-----------New access policy created successfully----------------');

		$attach_policy_result    = $this->attach_policy($policy_created_result['Policy']['Arn']);
		wptc_log($attach_policy_result,'----------$attach_policy_result-----------------');

		if (isset($attach_policy_result['error'])) {
			return $attach_policy_result;
		}

		wptc_log(array(),'-----------policy attached successfully----------------');

		$create_file_result      = $this->s3_client->create_verification_file($this->site_url);
		wptc_log($create_file_result,'----------$create_file_result-----------------');

		if (isset($create_file_result['error'])) {
			return $create_file_result;
		}

		wptc_log(array(),'-----------verification file created successfully----------------');

		$enable_versioning       = $this->s3_client->enable_versioning();
		wptc_log($enable_versioning,'----------$enable_versioning-----------------');

		if (isset($enable_versioning['error'])) {
			return $enable_versioning;
		}

		$upsert_site_life_cycle       = $this->s3_client->upsert_site_life_cycle();
		wptc_log($upsert_site_life_cycle,'----------$upsert_site_life_cycle-----------------');

		if (isset($upsert_site_life_cycle['error'])) {
			return $upsert_site_life_cycle;
		}

		wptc_log(array(),'-----------version enabled for bucket successfully----------------');

		$this->save_keys($key_created_result);

		return true;
	}

	public function process_restricted_access(){
		$create_file_result      = $this->s3_client->create_verification_file($this->site_url);
		wptc_log($create_file_result,'----------$create_file_result-----------------');

		if (isset($create_file_result['error'])) {
			return $create_file_result;
		}

		$upsert_site_life_cycle       = $this->s3_client->upsert_site_life_cycle();
		wptc_log($upsert_site_life_cycle,'----------$upsert_site_life_cycle-----------------');

		if (isset($upsert_site_life_cycle['error'])) {
			return $upsert_site_life_cycle;
		}
	}

	private function delete_user_if_already_existed(){

		if (!$this->is_user_already_exist()) {
			wptc_log(array(),'-----------User not exist before----------------');
			return ;
		}

		$keys = $this->list_access_keys();
		if (!empty($keys)) {
			$this->delete_access_keys($keys);
		}

		$policies = $this->list_policy();

		wptc_log($policies,'-----------$policies----------------');

		if (!empty($policies)) {
			$this->detach_policy($policies['PolicyArn']);
			$this->delete_policy($policies['PolicyArn']);
		}

		$this->delete_user();
	}

	private function is_user_already_exist(){
		try {
			$response = $this->iam_client->listUsers(array(
			));

			$users = $response->get('Users');

			// wptc_log($users,'-----------$response->get(Users);----------------');

			return in_array($this->hashed_url, array_column($users, 'UserName'));

		} catch (Exception $e) {
			$err_msg = $e->getMessage();
			wptc_log($err_msg,'-----------$err_msg----------------');
		}

		return false;
	}

	private function list_access_keys(){
		try {
			$response = $this->iam_client->listAccessKeys(array(
				'UserName'   => $this->hashed_url,
			));

			return $response->get('AccessKeyMetadata');
		} catch (Exception $e) {
			$err_msg = $e->getMessage();
			wptc_log($err_msg,'-----------$err_msg----------------');
		}

		return false;
	}

	private function delete_access_keys($keys){
		wptc_log(func_get_args(), "--------" . __FUNCTION__ . "--------");
		try {
			foreach ($keys as $key) {
				$response = $this->iam_client->deleteAccessKey(array(
					'AccessKeyId' => $key['AccessKeyId'],
					'UserName'    => $this->hashed_url,
				));

				wptc_log($response,'-----------$delete_access_keys ----------------');
			}
		} catch (Exception $e) {
			$err_msg = $e->getMessage();
			wptc_log($err_msg,'-----------$err_msg----------------');
		}
	}

	private function list_policy(){
		try {
			$response = $this->iam_client->listAttachedUserPolicies(array(
				'UserName' => $this->hashed_url,
			));

			wptc_log($response,'-----------$response list_policy----------------');

			$policies = $response->get('AttachedPolicies');

			if (empty($policies)) {
				return false;
			}

			$key = array_search($this->hashed_url . '_Policy', array_column($policies, 'PolicyName'));

			if($key === false){
				return $key;
			}

			return $policies[$key];

		} catch (Exception $e) {
			$err_msg = $e->getMessage();
			wptc_log($err_msg,'-----------$err_msg----------------');
		}

		return false;
	}

	private function detach_policy($PolicyArn){
		wptc_log(func_get_args(), "--------" . __FUNCTION__ . "--------");
		try {
			$deleteUserPolicy =  $this->iam_client->detachUserPolicy(array(
				'PolicyArn' => $PolicyArn,
				'UserName'   => $this->hashed_url,
			));

			wptc_log($deleteUserPolicy,'-----------$deleteUserPolicy----------------');
		} catch (Exception $e) {
			$err_msg = $e->getMessage();
			wptc_log($err_msg,'-----------$err_msg----------------');
		}
	}

	private function delete_policy($PolicyArn){
		wptc_log(func_get_args(), "--------" . __FUNCTION__ . "--------");
		try {
			$deleteUserPolicy =  $this->iam_client->deletePolicy(array(
				'PolicyArn' => $PolicyArn,
			));

			wptc_log($deleteUserPolicy,'-----------$deleteUserPolicy----------------');
		} catch (Exception $e) {
			$err_msg = $e->getMessage();
			wptc_log($err_msg,'-----------$err_msg----------------');
		}
	}

	private function delete_user(){
		try {
			$deleteUser =  $this->iam_client->deleteUser(array(
				'UserName' => $this->hashed_url,
			));
			wptc_log($deleteUser,'-----------$deleteUser----------------');
		} catch (Exception $e) {
			$err_msg = $e->getMessage();
			wptc_log($err_msg,'-----------$err_msg----------------');
		}
	}

	/*
	 Maximum limit IAM user creation is 5000
	 https://docs.aws.amazon.com/IAM/latest/UserGuide/reference_iam-limits.html
	*/
	private function create_new_IAM_user() {

		try {
			$response = $this->iam_client->createUser(array(
				'UserName' => $this->hashed_url,
			));

			$response = wptc_get_result_array_s3($response);

			wptc_log($response, "--------createNewIAMUser-sffsf----result---");

			return $response;
		} catch (Exception $e) {

			$err_msg = $e->getMessage();

			if (empty($err_msg)) {
				return array('error' => 'Failed to create user' );
			}

			return array('error' => $err_msg );
		}
	}

	/*
	 Only 2 access keys allowed per user.
	 https://docs.aws.amazon.com/IAM/latest/UserGuide/reference_iam-limits.html
	*/
	private function create_new_access_key() {
		try {
			$response = $this->iam_client->createAccessKey(array(
				'UserName' => $this->hashed_url,
			));

			$response = wptc_get_result_array_s3($response);

			wptc_log($response, "--------create_new_access_key-----result---");

			$proper_result = $this->get_create_access_key_result($response);

			wptc_log($proper_result, "--------create_new_access_key-----proper_result---");

			return $proper_result;
		} catch (Exception $e) {
			$err_msg = $e->getMessage();

			if (empty($err_msg)) {
				return array('error' => 'Failed to create new access key' );
			}

			return array('error' => $err_msg );
		}
	}


	/*
	 Maximum policies limit 1500
	 https://docs.aws.amazon.com/IAM/latest/UserGuide/reference_iam-limits.html
	*/

	private function create_policy() {

		$policy = '{
		   "Version":"2012-10-17",
		   "Statement": [
		        {
		            "Sid": "VisualEditor0",
		            "Effect": "Allow",
		            "Action": [
		                "s3:PutObject",
		                "s3:GetObjectAcl",
		                "s3:GetObject",
		                "s3:AbortMultipartUpload",
		                "s3:DeleteObjectVersion",
		                "s3:DeleteObject",
		                "s3:GetObjectVersion",
		                "s3:ListMultipartUploadParts"
		            ],
		            "Resource":"arn:aws:s3:::' . $this->resouce_path() . '*"
		        },
		        {
		            "Sid": "VisualEditor1",
		            "Effect": "Allow",
		            "Action": [
		                "s3:GetLifecycleConfiguration",
		                "s3:PutLifecycleConfiguration",
		                "s3:DeleteBucketPolicy"
		            ],
		            "Resource": "arn:aws:s3:::' . $this->bucket_name .'"
		        }
			]
		}';

		try {
			$response = $this->iam_client->createPolicy(array(
				'PolicyName' => $this->hashed_url . '_Policy',
				'PolicyDocument' => $policy
			));

			$response = wptc_get_result_array_s3($response);

			wptc_log($response, "--------create_policy----result----");

			return $response;
		} catch (Exception $e) {
			$err_msg = $e->getMessage();

			if (empty($err_msg)) {
				return array('error' => 'Failed to Create Policy' );
			}

			return array('error' => $err_msg );
		}
	}

	private function attach_policy($PolicyArn) {
		wptc_log(func_get_args(), "--------" . __FUNCTION__ . "--------");
		try {
			$response = $this->iam_client->attachUserPolicy(array(
				'UserName' => $this->hashed_url,
				'PolicyArn' => $PolicyArn
			));

			$response = wptc_get_result_array_s3($response);
			wptc_log($response, "--------attach_policy----result----");
			return $response;
		} catch (Exception $e) {
			$err_msg = $e->getMessage();

			if (empty($err_msg)) {
				return array('error' => 'Failed to Attach Policy' );
			}

			return array('error' => $err_msg );
		}
	}

	private function resouce_path(){
		return $this->bucket_name . '/wp-time-capsule/' .  $this->site_url . '/';
	}
}
