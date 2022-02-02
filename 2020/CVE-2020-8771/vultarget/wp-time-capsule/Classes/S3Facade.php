<?php
/**
* A class with functions the perform a backup of WordPress
*
* @copyright Copyright (C) 2011-2014 Awesoft Pty. Ltd. All rights reserved.
* @author Michael De Wildt (http://www.mikeyd.com.au/)
* @license This program is free software; you can redistribute it and/or modify
*          it under the terms of the GNU General Public License as published by
*          the Free Software Foundation; either version 2 of the License, or
*          (at your option) any later version.
*
*          This program is distributed in the hope that it will be useful,
*          but WITHOUT ANY WARRANTY; without even the implied warranty of
*          MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*          GNU General Public License for more details.
*
*          You should have received a copy of the GNU General Public License
*          along with this program; if not, write to the Free Software
*          Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110, USA.
*/

use AwsWPTC\S3\S3Client;

class WPTC_S3Facade {

	const RETRY_COUNT = 3;

	private $s3_wrapper,
	$client,
	$config,
	$directory_cache = array(),
	$as3_bucket,
	$common_s3;

	public $userInfo;

	public function __construct() {
		$this->init();
	}

	public function init() {
		try {

			$this->config = WPTC_Factory::get('config');

			$as3_access_key    = $this->config->get_option('as3_access_key');
			$as3_secure_key    = $this->config->get_option('as3_secure_key');
			$as3_bucket_region = $this->config->get_option('as3_bucket_region');
			$this->as3_bucket  = $this->config->get_option('as3_bucket_name');

			$credentials = array(
				'key'                       => trim($as3_access_key),
				'secret'                    => trim(str_replace(' ', '+', $as3_secure_key)),
				'region'                    => $as3_bucket_region,
				'ssl.certificate_authority' => false,
			);

			if (!empty($as3_bucket_region)) {
				$credentials['signature'] = 'v4';
			}

			$this->client = S3Client::factory($credentials);

			$this->init_additional_S3_client($this->client, $this->as3_bucket, $as3_bucket_region);

			$this->enable_versioning();

			$this->s3_wrapper = new S3_Wptc_Wrapper($this->client, $this->as3_bucket);
			$this->s3_wrapper->setTracker(new WPTC_UploadTracker());

		} catch (Exception $e) {
			// return $this->process_exception($e);
		}
	}

	private function init_additional_S3_client($client, $bucket, $region){

		include_once WPTC_PLUGIN_DIR . 'S3/class.s3.php';

		$this->common_s3 = new WPTC_S3();
		return $this->common_s3->force_init($client, $bucket, $region);
	}

	private function is_500_error($http_code) {
		if ($http_code === 503) {
			return true;
		}
	}

	private function is_conflicting_error($http_code) {
		if ($http_code === 409) {
			return true;
		}
	}

	private function is_forbidden_error($http_code, $err_msg) {

		if (stripos($err_msg, 'The specified bucket is not valid') !== false) {
			return true;
		}

		if ($http_code === 403 || $http_code === 301 || $http_code === 404) {
			return true;
		}
	}

	public function enable_versioning() {

		if( wptc_is_auto_generated_iam() ){
			return ;
		}

		$this->common_s3->enable_versioning();
	}

	public function get_authorize_url() {
		return $this->client->createAuthUrl();
	}

	public function is_authorized($is_restore = false) {
		if( wptc_is_auto_generated_iam() ){
			return $this->check_wptc_verify_file();
		}

		$response = $this->common_s3->is_bucket_exist();

		if (!empty($response['error'])) {
			$this->die_with_error($response['error']);
		}

		return true;
	}

	public function check_wptc_verify_file(){

		$cloud_root_dir = $this->config->get_cloud_root_dir();

		try {
			$keyInfo = $this->client->doesObjectExist(
				$this->as3_bucket,
				WPTC_CLOUD_DIR_NAME . '/' . $cloud_root_dir . '/' . WPTC_S3_VERIFICATION_FILE
			);
			// wptc_log($keyInfo,'-----------check_wptc_verify_file----------------');
			return $keyInfo;
		} catch (Exception $e) {
			wptc_log($e->getMessage(),'-----------check_wptc_verify_file----------------');
		}

		return false;
	}

	public function validate_max_revision_limit($requested_revision_limit) {

		$response = $this->common_s3->upsert_site_life_cycle($requested_revision_limit);

		if (!empty($response['error'])) {
			return $response;
		}

		return true;
	}

	public function unlink_account() {
		return $this;
	}

	public function get_directory_contents($path) {
		return array();
	}

	public function get_account_info() {
		$req_result = new stdclass;
		$req_result->email = 'example@revmakx.com';
		return $req_result;
	}

	public function get_quota_div() {
		//$account_info = $this->get_account_info();
		$return_var = '';
		$return_var = 'Amazon S3 - ' . $this->as3_bucket;
		return $return_var;
	}

	public function upload_file($path, $file) {
		$i = 0;
		$backup_id = wptc_get_cookie('backupID');
		while ($i++ < self::RETRY_COUNT) {
			try {
				if (empty($this->s3_wrapper)) {
					$this->init();
				}
				return $this->s3_wrapper->putFile($file, wptc_remove_secret($file), $path);
			} catch (Exception $e) {
				if ($i >= self::RETRY_COUNT) {
					$base_name_file = basename($file);
					return array('error' => "File upload error ($file).");
				} else {
					if (!method_exists($e, 'getStatusCode')) {
						return array('error' => $e->getMessage());
					}
					if ($e->getStatusCode() == 503) {
						return array('too_many_requests' => $e->getMessage());
					}
					wptc_log($e->getMessage(), '-----------Retry uploading-------------');
					wptc_log($file,'--------------$file-------------');
					WPTC_Factory::get('logger')->log(__("Retry uploading " . $e->getMessage(), 'wptc'), 'backups', $backup_id);
				}
			}
		}
		throw $e;
	}

	public function chunk_upload_file($path, $file, $processed_file, $meta_data_backup = null) {

		$offest = $upload_id = null;
		if ($meta_data_backup == 1) {
			$offest = $processed_file['offset'];
			$upload_id = $processed_file['upload_id'];
			$s3_part_number = $processed_file['s3_part_number'];
			$s3_parts_array = $processed_file['s3_parts_array'];
		} else if ($processed_file) {
			$offest = $processed_file->offset;
			$upload_id = $processed_file->uploadid;
			$s3_part_number = $processed_file->s3_part_number;
			$s3_parts_array = $processed_file->s3_parts_array;
		} else {
			$s3_parts_array = array();
			$s3_part_number = '';
		}
		if (empty($this->s3_wrapper)) {
			$this->init();
		}
		return $this->s3_wrapper->chunkedUpload($file, wptc_remove_secret($file), $path, true, $offest, $upload_id, $s3_part_number, $s3_parts_array);
	}

	public function download_file($path, $file, $revision = '', $isChunkDownload = null, $g_file_id = null) {
		wptc_replace_abspath($file);
		$i = 0;
		$restore_action_id = $this->config->get_option('restore_action_id');
		while ($i++ < self::RETRY_COUNT) {
			try {
				if (empty($this->s3_wrapper)) {
					$this->init();
				}
				return $this->s3_wrapper->getFile($path, $file, $revision, null, $g_file_id, $this->as3_bucket);
			} catch (Exception $e) {
				wptc_log($file, "--------retrying-chunk_download_file-------");
				if ($i >= self::RETRY_COUNT) {
					$base_name_file = basename($file);
					return array('error' => $e->getMessage()." - File chunk download error ($file).");
				} else {
					WPTC_Factory::get('logger')->log(__("Retry chunk downloading " . $e->getMessage(), 'wptc'), 'restores', $restore_action_id);
				}
			}
		}
	}

	public function chunk_download_file($path, $file, $revision = '', $isChunkDownload = null, $g_file_id = null, $meta_file_download = null) {
		wptc_replace_abspath($file);
		$i = 0;
		$restore_action_id = $this->config->get_option('restore_action_id');
		while ($i++ < self::RETRY_COUNT) {
			try {
				if (empty($this->s3_wrapper)) {
					$this->init();
				}
				return $this->s3_wrapper->chunkedDownload($path, $file, $revision, $isChunkDownload, $g_file_id = null, $meta_file_download);
			} catch (Exception $e) {
				wptc_log($file, "--------retrying-chunk_download_file-------");
				if ($i >= self::RETRY_COUNT) {
					$base_name_file = basename($file);
					return array('error' => $e->getMessage()." - File chunk download error ($file).");
				} else {
					WPTC_Factory::get('logger')->log(__("Retry chunk downloading " . $e->getMessage(), 'wptc'), 'restores', $restore_action_id);
				}
			}
		}
	}

	public function abort_all_multipart_uploads($parent_folder) {
		try{
			$running_multi_part_uploads = $this->client->listMultipartUploads(array(
				'Bucket' => $this->as3_bucket,
			));

			if (!is_array($running_multi_part_uploads['Uploads'])) {
				return false;
			}

			foreach ($running_multi_part_uploads['Uploads'] as $k => $v) {

				$this_key = $v['Key'];
				$this_site_path = WPTC_CLOUD_DIR_NAME . '/' . $parent_folder;

				$safe_to_abort = (stripos($this_key, $this_site_path) === 0) ? true : false;

				if ($safe_to_abort) {
					wptc_log($v, "--------running_multi_part_uploads--------");
					$result = $this->client->abortMultipartUpload(array(
						'Bucket' => $this->as3_bucket,
						'Key' => $v['Key'],
						'UploadId' => $v['UploadId'],
					));
				}
			}
		} catch (Exception $e){
			wptc_log(array(),'-----------Error on aborting all multipart uploads----------------');

			if(method_exists ( $e , 'getStatusCode')){
				$http_code = $e->getStatusCode();
			} else if(method_exists ( $e , 'getCode')) {
				$http_code = $e->getCode();
			} else {
				$http_code = 0;
			}

			$err_msg = $e->getMessage();

			unset($e);

			wptc_log($err_msg, '---------------$err_msg-----------------');
			wptc_log($http_code, '---------------$http_code-----------------');
		}
	}

	private function can_show_error_alert($http_code){
		if ($http_code >= 500 && $http_code <= 599 ) {
			return false;
		}

		return true;
	}

	private function process_exception($e){

		if(method_exists ( $e , 'getStatusCode')){
			$http_code = $e->getStatusCode();
		} else if(method_exists ( $e , 'getCode')) {
			$http_code = $e->getCode();
		} else {
			$http_code = 0;
		}


		$err_msg = $e->getMessage();

		unset($e);

		wptc_log($err_msg, '---------------$err_msg-----------------');
		wptc_log($http_code, '---------------$http_code-----------------');


		return ;
		if(is_any_ongoing_wptc_restore_process()){
			if ($http_code === 0 || $http_code === 5 || $http_code === 6 || $http_code === 7 ) {
				return true;
			}
			return false;
		}

		if (!$this->config->get_option('is_user_logged_in')){
			return false;
		}

		if (!$this->config->get_option('main_account_email')) {
			return false;
		}

		if ($this->is_500_error($http_code)) {
			return $this->return_based_on_req($http_code, $err_msg, false);
		}
		if ($this->is_conflicting_error($http_code)) {
			return $this->return_based_on_req($http_code, $err_msg, false);
		}

		if ($this->is_forbidden_error($http_code, $err_msg)) {
			WPTC_Factory::get('logger')->log('Amazon S3 authorization is revoked - HTTP (' . $http_code . ') ' . $err_msg . ' ', 'others');
			$this->config->set_option('last_cloud_error', $err_msg);
			$this->unlink_account();
			$this->config->set_option('default_repo', false);
			return $this->return_based_on_req($http_code, $err_msg, false);
		}

		if ($http_code == 503) {
			return $this->return_based_on_req($http_code, $err_msg, array('too_many_requests' => $err_ms));
		}

		$this->die_with_error($err_msg, $http_code);
	}

	private function die_with_error($err_msg, $http_code = 0){
		if(is_wptc_server_req()){
			backup_proper_exit_wptc($err_msg);
		}

		if ($this->can_show_error_alert($http_code)) {
			$this->config->set_option('show_user_php_error', '(HTTP Code :' . $http_code . ')' . $err_msg);
		}

		return $this->return_based_on_req($http_code, $err_msg, 'TEMPORARY_CONNECTION_ISSUE');
	}

	private function return_based_on_req($http_code = false, $err_msg = false, $return_message = false){
		if($this->is_init_auth_request()){
			die('(HTTP Code :'.$http_code.')'.$err_msg);
		}
		return $return_message;
	}

	private function is_init_auth_request(){
		if( isset($_POST['action']) && $_POST['action'] == 'get_s3_authorize_url_wptc' ) {
			return true;
		}
		return false;
	}

	public function retrieve_revisions($file){
		return false;
	}

}
