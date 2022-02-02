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

class WPTC_DropboxFacade {

	const RETRY_COUNT = 3;
	private static $instance = null;

	private
	$dropbox,
	$oauth_state,
	$oauth,
	$account_info_cache,
	$config,
	$directory_cache = array()
	;

	public function __construct() {
		$this->config = WPTC_Factory::get('config');
		$this->init();
	}

	public function init() {
		try {
			if (!extension_loaded('curl')) {
				throw new Exception(sprintf(
					__('The cURL extension is not loaded. %sPlease ensure its installed and activated.%s', 'wpbtd'),
					'<a href="http://php.net/manual/en/curl.installation.php">',
					'</a>'
				));
			}

			$this->oauth = new WPTC_Dropbox_OAuth_Consumer_Curl(WPTC_DROPBOX_CLIENT_ID, WPTC_DROPBOX_CLIENT_SECRET);

			$this->oauth_state = $this->config->get_option('dropbox_oauth_state');
			$this->token = $this->get_token();

			if(empty($this->oauth_state)){
				return false;
			}

			if ($this->oauth_state == 'access') {
				$this->set_token();
			}

			$this->dropbox = new WPTC_Dropbox_API($this->oauth);
			$this->dropbox->setTracker(new WPTC_UploadTracker());
		}
		catch (Exception $e) {
			if ($this->oauth_state != 'request') {
				return $this->process_exception($e);
			}
		}
	}

	private function is_auth_error($http_status, $message) {
		if ($http_status == 403 || $http_status == 401 || $message == 'invalid_access_token') {
			return true;
		}
		return false;
	}

	private function get_token() {
		$token = $this->config->get_option("dropbox_access_token");

		if (empty($token)) {
			$result_token = new stdClass;
			$result_token->oauth_token = null;
			$result_token->token_type = null;
			$result_token->account_id = null;
			$result_token->uid = null;
		}

		$token = json_decode($token);

		if ($token) {
			$result_token = new stdClass;
			$result_token->oauth_token = $token->access_token;
			$result_token->token_type = $token->token_type;
			$result_token->account_id = $token->account_id;
			$result_token->uid = $token->uid;
		}

		return $result_token;
	}

	public function is_authorized($is_restore = false) {
		try {
			//Do not check wptc account authorizations on restore
			// if (!$is_restore) {
			// 	$this->ping_server_if_storage_quota_low();
			// }

			$this->get_account_info();

		} catch (Exception $e) {
			if ($this->oauth_state != 'request') {
				return $this->process_exception($e);
			}
			return false;
		}
		return true;
	}

	public function validate_max_revision_limit($requested_revision_limit = 30) {
		$account_info = $this->get_account_info();

		if (empty($account_info) || $account_info === 'TEMPORARY_CONNECTION_ISSUE') {
			// do_action('set_revision_limit_wptc', WPTC_DEFAULT_MAX_REVISION_LIMIT, $cross_check = false);

			return WPTC_DEFAULT_MAX_REVISION_LIMIT;
		}

		$account_type = $account_info->account_type;

		wptc_log($account_type, '-----validate_max_revision_limit---$account_type--------');

		//data empty so send default one
		if (!isset($account_type)) {
			// do_action('set_revision_limit_wptc', WPTC_DEFAULT_MAX_REVISION_LIMIT, $cross_check = false);

			return WPTC_DEFAULT_MAX_REVISION_LIMIT;
		}

		// wptc_log($account_type, '--------$account_type--------');

		$type = (array) $account_type;

		$type = $type['.tag'];

		// wptc_log($type, '--------$type--------');

		//This user not eligilbe for 120 days restore limti
		if ($type === 'basic') {
			// do_action('set_revision_limit_wptc', WPTC_DEFAULT_MAX_REVISION_LIMIT, $cross_check = false);

			return WPTC_DEFAULT_MAX_REVISION_LIMIT;
		}

		//Dropbox max revision limit on any type of account other than basic has max 120 days revision limit , (pro, business account types)
		// return do_action('set_revision_limit_wptc', 365, $cross_check = false);

		return $requested_revision_limit;
	}

	private function quota_info(){
		try{
			$response = $this->dropbox->quotaInfo();
		} catch(Exception $e){
			if ($this->oauth_state != 'request') {
				return $this->process_exception($e);
			}
			return false;
		}
		return $response['body'];
	}

	public function ping_server_if_storage_quota_low() {

		return false;

		if (!is_wptc_node_server_req()) {
			return ;
		}

		$account_info = $this->get_account_info();
		if(empty($account_info) ||  $account_info === 'TEMPORARY_CONNECTION_ISSUE') return false;

		$quota_info = $this->quota_info();

		if(empty($quota_info) || $quota_info === 'TEMPORARY_CONNECTION_ISSUE') return false;

		$remaining_quota = $quota_info->allocation->allocated - $quota_info->used;

		wptc_log($remaining_quota, '--------$remaining_quota--------');

		if ((empty($remaining_quota) && $remaining_quota != 0 ) || $remaining_quota > WPTC_MIN_REQUIRED_STORAGE_SPACE) {
			return true;
		}

		$name = $account_info->name->display_name;
		$connectedEmail = $account_info->email;
		$cloudAccount = $this->config->get_option('default_repo');

		$err_info = array(
			'name' => $name,
			'cloudAccount' => $cloudAccount,
			'connectedEmail' => $connectedEmail,
			'type' => 'limit_exceed',
		);

		error_alert_wptc_server($err_info);
		if(is_wptc_server_req() && is_any_ongoing_wptc_backup_process()){
			$backup = new WPTC_BackupController();
			$backup->proper_backup_force_complete_exit('Dropbox Storage Quota is low So backup stopped !');
		}

		return false;
	}

	public function get_authorize_url() {
		return $this->oauth->getAuthoriseUrl();
	}

	public function migrate_to_v2() {
		try{
			$token = $this->oauth->upgradeOAuth();
			$token_arr = array(
				'access_token' => $token->access_token,
				'token_type' => $token->token_type,
				'uid' => '',
				'account_id' => '',
			);
			wptc_log($token_arr, '--------$token_arr--------');
			$this->config->set_option('dropbox_access_token', json_encode($token_arr));
			$this->config->set_option('dropbox_oauth_state', 'access');
			$this->config->set_option('default_repo', 'dropbox');
			$this->config->delete_option('access_token');
			$this->config->delete_option('access_token_secret');
			$this->config->delete_option('request_token');
			$this->config->delete_option('request_token_secret');
			$this->config->delete_option('oauth_state');
			$this->set_token();
		} catch (Exception $e){
			return false;
		}
	}

	public function get_account_info() {
		if (!isset($this->account_info_cache)) {
			if (!$this->dropbox) {
				return false;
			}
			try{
				// $account_info_res = array(
				// 	'name' => array(
				// 		'display_name' => '..'
				// 	),
				// 	'email' => '..'
				// );
				// $this->account_info_cache = (object)$account_info_res;

				$response = $this->dropbox->accountInfo();
				$this->account_info_cache = $response['body'];
			} catch(Exception $e){
				if ($this->oauth_state != 'request') {
					return $this->process_exception($e);
				}
				return false;
			}
		}

		// wptc_log( $this->account_info_cache, '--------account_info_cache--------');

		return $this->account_info_cache;
	}

	private function set_token(){
		$token = $this->get_token();
		// wptc_log($token, '--------$token--------');
		$this->oauth->setToken($token);
	}

	public function unlink_account() {
		// $this->oauth->resetToken();
		// return $this->save_tokens();
		$this->oauth_state = null;
	}

	public function get_quota_div() {
		$account_info = $this->get_account_info();

		if(empty($account_info) || $account_info === 'TEMPORARY_CONNECTION_ISSUE') return false;

		return 'Dropbox - ' . $account_info->email;
	}

	public function upload_file($path, $file) {
		$i = 0;
		$backup_id = wptc_get_cookie('backupID');
		while ($i++ < self::RETRY_COUNT) {
			try {
				return $this->dropbox->putFile($file, wptc_remove_secret($file), $path);
			} catch (Exception $e) {
				if ($i > self::RETRY_COUNT) {
					$base_name_file = basename($file);
					return array('error' => "File upload error ($file).");
				} else {
					WPTC_Factory::get('logger')->log(__("Retry uploading " . $e->getMessage(), 'wptc'), 'backups', $backup_id);
				}
			}
		}
		throw $e;
	}

	public function download_file($path, $file, $revision = '', $isChunkDownload = null, $download_current_path = null) {
		wptc_replace_abspath($file);

		$i = 0;
		$restore_action_id = $this->config->get_option('restore_action_id');
		while ($i++ < self::RETRY_COUNT) {
			try {
				return $this->dropbox->getFile($path, $file, $revision);
			} catch (Exception $e) {
				if ($i >= self::RETRY_COUNT) {
					$base_name_file = basename($file);
					return array('error' => $e->getMessage()." - File download error.");
				} else {
					WPTC_Factory::get('logger')->log(__("Retry downloading " . $e->getMessage(), 'wptc'), 'restores', $restore_action_id);
				}
			}
		}
	}

	public function chunk_download_file($path, $file, $revision = '', $isChunkDownload = null, $extra , $meta_file_download = null) {
		wptc_replace_abspath($file);
		$i = 0;
		$restore_action_id = $this->config->get_option('restore_action_id');
		while ($i++ < self::RETRY_COUNT) {
			try {
				return $this->dropbox->chunkedDownload($path, $file, $revision, $isChunkDownload, $meta_file_download);
			} catch (Exception $e) {
				if ($i >= self::RETRY_COUNT) {
					$base_name_file = basename($file);
					return array('error' => $e->getMessage()." - File chunk download error.");
				} else {
					WPTC_Factory::get('logger')->log(__("Retry chunk downloading " . $e->getMessage(), 'wptc'), 'restores', $restore_action_id);
				}
			}
		}
	}
	public function chunk_upload_file($path, $file, $processed_file, $meta_data_backup =
		null) {
		wptc_log(func_get_args(), "--------" . __FUNCTION__ . "--------");
		$offest = $upload_id = null;
		if ($meta_data_backup == 1) {
			$offest = $processed_file['offset'];
			$upload_id = $processed_file['upload_id'];
		} else if ($processed_file ) {
			$offest = $processed_file->offset;
			$upload_id = $processed_file->uploadid;
		}
		return $this->dropbox->chunkedUpload($file, wptc_remove_secret($file), $path, true, $offest, $upload_id);
	}

	private function can_show_error_alert($http_code){
		if ($http_code >= 500 && $http_code <= 599 ) {
			return false;
		}

		return true;
	}

	private function process_exception($e){
		$err_msg = $e->getMessage();
		$http_code = $e->getCode();

		wptc_log($http_code, '---------------$http_code-----------------');
		wptc_log($err_msg, "--------e---dropbox init-----");

		if(is_any_ongoing_wptc_restore_process()){
			if ($http_code === 0 || $http_code === 5 || $http_code === 6 || $http_code === 7 ) {
				return true;
			}
			return false;
		}


		if ($this->is_auth_error($http_code, $err_msg)) {
			WPTC_Factory::get('logger')->log('Dropbox authorization is revoked - HTTP (' . $http_code . ') ' . $err_msg . ' ', 'others');
			$this->config->set_option('last_cloud_error', $err_msg);
			$this->config->set_option('default_repo', false);
		}


		if(is_wptc_server_req()){
			backup_proper_exit_wptc($err_msg);
		} else if((isset($_POST['action']) && $_POST['action'] === 'get_dropbox_authorize_url_wptc')) {
			die('(HTTP Code :'.$http_code.')'.$err_msg);
		} else {
			if ($this->can_show_error_alert($http_code)) {
				$this->config->set_option('show_user_php_error', '(HTTP Code :'.$http_code.')'.$err_msg);
			}

			return 'TEMPORARY_CONNECTION_ISSUE';
		}
	}

	public function retrieve_revisions($file_meta){
		return  $this->dropbox->revisions($file_meta->revision_number);
	}

	//Not used
	// public function get_file_details($path) {
	// 	return $this->dropbox->metaData($path);
	// }

	//Not used
	// public function delete_file($file) {
	// 	return $this->dropbox->delete($file);
	// }

	//Not used
	// public function create_directory($path) {
	// 	try {
	// 		$this->dropbox->create($path);
	// 	} catch (Exception $e) {}
	// }

	//Not used
	// public function get_directory_contents($path) {
	// 	if (!isset($this->directory_cache[$path])) {
	// 		try {
	// 			$this->directory_cache[$path] = array();
	// 			$response = $this->dropbox->metaData($path, null, 10000, false, false); //($path, null, 10000, false, false)
	// 			foreach ($response['body']->contents as $val) {
	// 				if (!$val->is_dir) {
	// 					$this->directory_cache[$path][] = basename($val->path);
	// 				}
	// 			}
	// 		} catch (Exception $e) {
	// 			$this->create_directory($path);
	// 		}
	// 	}

	// 	return $this->directory_cache[$path];
	// }
}
