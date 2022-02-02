<?php

class Google_Wptc_Wrapper {
	private $client,
			$service,
			$wptc_folder_id,
			$service_dets,
			$handle,
			$config,
			$site_root_folder,
			$processed_files;

	public function __construct($client) {
		$this->client = $client;
		$this->service = new WPTC_Google_Service_Drive($client);
		$this->utils = new Gdrive_Utils();
		$this->processed_files = WPTC_Factory::get('processed-restoredfiles');
		$this->config = WPTC_Factory::get('config');
		$this->init();
	}

	private function init(){
		if (!empty($this->site_root_folder)) {
			return $this->site_root_folder;
		}

		$this->site_root_folder = $this->config->get_option('dropbox_location');
	}

	public function get_service_dets() {
		if (!$this->service_dets) {
			$this->service_dets = $this->service->about->get();
		}
		return $this->service_dets;
	}

	public function quota_bytes_left() {
		$about = $this->get_service_dets();

		$total = $about->getQuotaBytesTotal();
		$used = $about->getQuotaBytesUsed();

		$remaining = $total - $used;

		return $remaining;
	}

	public function setTracker($tracker) {
		$this->tracker = $tracker;
	}

	public function get_exact_parent_id_of_cur_file($dir_path, $parent_dir = false) {

		if (empty($dir_path)) {
			return false;
		}

		$sub_dirs = explode('/', $dir_path);

		if (empty($sub_dirs)) {
			return false;
		}

		$prev_parent_id = $this->get_wptc_folder_id_or_create_it();

		if (empty($prev_parent_id)) {
			return false;
		}

		if ($sub_dirs[0] == $this->site_root_folder) {

			$new_parent_id = $this->get_this_site_main_folder_id_or_create_it($prev_parent_id);

			if (empty($new_parent_id)) {
				return false;
			}

			$prev_parent_id = $new_parent_id;
			$sub_dirs 		= array_slice($sub_dirs, 1);
		}

		$count = 0;

		foreach ($sub_dirs as $k => $sub_dir) {

			$count++;

			$parameters 	 = array();
			$parameters['q'] = "title = '$sub_dir' and trashed = false and '$prev_parent_id' in parents and 'me' in owners and mimeType = 'application/vnd.google-apps.folder'";

			try {
				$files = $this->service->files->listFiles($parameters);
			} catch (Exception $e) {
				wptc_log($e->getMessage(), "--------exception get_exact_parent_id_of_cur_file--------");
				throw $e;
			}

			if (!method_exists($files, 'getItems')) {
				return false;
			}

			$prev_parent_result = $this->utils->get_dir_id_from_list_result($files);
			$prev_parent_id 	= empty($prev_parent_result) ?  $this->create_new_sub_folder($sub_dir, $prev_parent_id) : $prev_parent_result;

			$this->store_sub_folder_values($sub_dirs, $prev_parent_id, $count);
		}

		if (!empty($parent_dir)) {
			$this->add_folders_id_cache($parent_dir, $prev_parent_id);
		}

		return $prev_parent_id;
	}

	public function store_sub_folder_values($sub_dirs, $prev_parent_id, $count){

		if (($key = array_search($this->site_root_folder, $sub_dirs)) !== false) {
			unset($sub_dirs[$key]);
		}

		$sub_dirs = array_values(array_filter($sub_dirs));
		$dir_path = '';

		for ($i=0; $i <$count; $i++) {
			$dir_path .= '/' . $sub_dirs[$i];
		}

		if (empty($dir_path)) {
			return ;
		}

		$this->add_folders_id_cache($dir_path, $prev_parent_id);
	}

	public function is_too_many_request_error($err_code){
		wptc_log($err_code, '---------err_code in is_too_many_request_error-------------');
		$status_codes = array(500, 503, 429, 28);
		if (in_array($err_code, $status_codes)) {
			return true;
		}
		return false;
	}

	private function get_this_site_main_folder_id_or_create_it($prev_parent_id) {
		$site_main_folder_id = $this->config->get_option('cached_g_drive_this_site_main_folder_id');

		if ($site_main_folder_id) {
			return $site_main_folder_id;
		}

		$parameters 	 = array();
		$parameters['q'] = "title = '$this->site_root_folder' and trashed = false and '$prev_parent_id' in parents and 'me' in owners and mimeType = 'application/vnd.google-apps.folder'";

		try{
			$files = $this->service->files->listFiles($parameters);
		} catch (Exception $e) {
			wptc_log($e->getMessage(), "--------exception get_this_site_main_folder_id_or_create_it--------");
			throw $e;
		}

		if (!method_exists($files, 'getItems')) {
			return false;
		}

		$prev_parent_result = $this->utils->get_dir_id_from_list_result($files);

		if (empty($prev_parent_result)) {
			$prev_parent_id = $this->create_new_sub_folder($this->site_root_folder, $prev_parent_id);
		} else {
			$prev_parent_id = $prev_parent_result;
		}

		$this->config->set_option('cached_g_drive_this_site_main_folder_id', $prev_parent_id);

		return $prev_parent_id;
	}

	private function get_wptc_folder_id_or_create_it() {
		$g_drive_folder_id = $this->config->get_option('cached_wptc_g_drive_folder_id');

		if (!empty($g_drive_folder_id)) {
			return $g_drive_folder_id;
		}

		$parameters 	 = array();
		$parameters['q'] = "title = 'WP Time Capsule' and trashed = false and 'root' in parents and 'me' in owners and mimeType = 'application/vnd.google-apps.folder'";
		try{
			$files = $this->service->files->listFiles($parameters);
		} catch (Exception $e) {
			wptc_log($e->getMessage(), "--------exception get_wptc_folder_id_or_create_it--------");
			throw $e;
		}

		if (!method_exists($files, 'getItems')) {
			return false;
		}

		$prev_parent_result = $this->utils->get_dir_id_from_list_result($files);

		if (empty($prev_parent_result)) {
			$prev_parent_id = $this->create_new_sub_folder('WP Time Capsule', false);
		} else {
			$prev_parent_id = $prev_parent_result;
		}

		$this->config->set_option('cached_wptc_g_drive_folder_id', $prev_parent_id);

		return $prev_parent_id;
	}

	public function create_new_sub_folder($dir_name, $parent_id) {
		$file = new WPTC_Google_Service_Drive_DriveFile();
		$file->setTitle($dir_name);
		$file->setMimeType('application/vnd.google-apps.folder');

		if (!empty($parent_id)) {
			$parent = new WPTC_Google_Service_Drive_ParentReference();
			$parent->setId($parent_id);
			$file->setParents(array($parent));
		}

		try{
			$createdFolder = $this->service->files->insert($file, array(
				'mimeType' => 'application/vnd.google-apps.folder',
			));
		} catch (Exception $e) {
			wptc_log($e->getMessage(), "--------exception create_new_sub_folder--------");
			throw $e;
		}

		if ($createdFolder) {
			$createdFolder = (array) $createdFolder;
			return $createdFolder['id'];
		}

		return false;
	}

	public function putFile($file, $filename = false, $path = '', $overwrite = true, $offset = 0, $uploadID = null) {
		wptc_log(func_get_args(), "--------" . __FUNCTION__ . "--------");
		try {

			$parent_meta = $this->processed_files->get_gdrive_caches_id($file);

			wptc_log($parent_meta,'-----------$parent_meta----------------');

			if(empty($parent_meta['g_file_id'])){
				$cur_parent_id = $this->get_exact_parent_id_of_cur_file($path, $parent_meta['parent_dir']);
			} else {
				$cur_parent_id = $parent_meta['g_file_id'];
			}

			if (empty($cur_parent_id)) {
				$error_msg = "Unable to get parent folder ID for " . $file;
				return array('error' => $error_msg);
			}

			$filename = (is_string($filename)) ? $filename : basename($file);
			$file_id  = $this->is_file_already_exists($file);

			wptc_log($file_id,'-----------$file_id----------------');

			$file_obj = new WPTC_Google_Service_Drive_DriveFile();

			$file_obj->setTitle(basename($filename));

			$parent = new WPTC_Google_Service_Drive_ParentReference();
			$parent->setId($cur_parent_id);
			$file_obj->setParents(array($parent));

			$handle = @fopen($file, 'r');

			if (empty($file_id)) {
				$result = $this->service->files->insert($file_obj,
							array(
								'data' => wptc_is_zero_bytes_file($file) ? ' ': fread($handle, filesize($file)) ,
								'uploadType' => 'media'
							)
						);
			} else {
				$result = $this->service->files->update( $file_id,	$file_obj,
							array(
								'data' => wptc_is_zero_bytes_file($file) ? ' ': fread($handle, filesize($file)) ,
								'newRevision' => true,
								'uploadType' => 'media'
							)
						);
			}

			fclose($handle);

			return $this->utils->formatted_upload_result($result);

		} catch (Exception $e) {
			$err_reason = $e->getMessage();
			$err_code 	= $e->getCode();
			wptc_log($err_reason, '---------Exception putFile-------------');

			if (stripos($err_reason, 'Exceeded') !== false || $this->is_too_many_request_error($err_code)) {
				return array('too_many_requests' => $e->getMessage());
			} else if($err_code == 400) {
				return array('error'=> 'Failed to parse Content-Range header- (400) ');
			}

			throw $e;
		}
	}

	public function is_file_already_exists($file) {
		$extra = array();

		if ($this->is_secret_file($file)) {
			$extra['secret_file'] 	= true;
			$extra['removed_secret'] = $this->remove_secret($file);
			$extra['is_gz_file'] 	= $this->is_gz_file($file);
		}

		return $this->processed_files->get_g_file_id($file, $extra);
	}

	private function is_secret_file($file){
		if (strstr($file, 'wptc-secret') !== false) {
			return true;
		}

		return false;
	}

	private function remove_secret($file){
		$file = wptc_remove_fullpath($file);
		$find = ".sql";
		return substr($file, 0, ( stripos($file, $find) + strlen($find) ) );
	}

	private function is_gz_file($file){
		if (strstr($file, 'wptc-secret.gz') !== false) {
			return true;
		}

		return false;
	}

	public function chunkedUpload($file, $filename = false, $path = '', $overwrite = true, $offset = 0, $uploadID = null) {
		try {

			$parent_meta = $this->processed_files->get_gdrive_caches_id($file);

			wptc_log($parent_meta,'-----------$parent_meta----------------');

			if(empty($parent_meta['g_file_id'])){
				$cur_parent_id = $this->get_exact_parent_id_of_cur_file($path, $parent_meta['parent_dir']);
			} else {
				$cur_parent_id = $parent_meta['g_file_id'];
			}

			if (empty($cur_parent_id)) {
				return array('error' => 'Unable to get parent folder ID for ' . $file);
			}

			$filename 	= (is_string($filename)) ? $filename : basename($file);
			$file_id 	= $this->is_file_already_exists($file);

			$file_obj = new WPTC_Google_Service_Drive_DriveFile();
			$file_obj->setTitle(basename($filename));

			wptc_log($file_id,'-----------$file_id----------------');

			$parent = new WPTC_Google_Service_Drive_ParentReference();
			$parent->setId($cur_parent_id);
			$file_obj->setParents(array($parent));

			$this->client->setDefer(true);

			if (empty($file_id)) {
				$request = $this->service->files->insert($file_obj, array(
						'uploadType' => 'multipart'
					)
			);
			} else {
				$request = $this->service->files->update($file_id, $file_obj, array(
						'newRevision' => true,
						'uploadType' => 'multipart'
					)
			);
			}

			$upload_file_block_size = 5 * 1024 * 1024;
			$media 					= new WPTC_Google_Http_MediaFileUpload($this->client, $request, '', null, true, $upload_file_block_size);
			$handle 				= fopen($file, "rb");
			$complete_backup_result = array();

			$media->setFileSize(filesize($file));


			$break = false;

			while (empty($complete_backup_result)) {
				if ($uploadID) {
					$media->resume($uploadID);
				}

				fseek($handle, $offset);

				wptc_log($offset, '---------$offset------------');
				wptc_log($upload_file_block_size, '---------$upload_file_block_size------------');
				wptc_log($uploadID, '---------$uploadID------------');

				$chunk 					= fread($handle, $upload_file_block_size);
				$complete_backup_result = $media->nextChunk($chunk);
				$uploadID 				= $media->getResumeUri();
				$offset 				= ftell($handle);

				if ($this->tracker) {
					$this->tracker->track_upload($file, $uploadID, $offset);
				}

				if ($offset < filesize($file) && is_wptc_timeout_cut() ) {
					$break = true;
					break;
				}
			}

			fclose($handle);
			$this->client->setDefer(false);

			if ($break) {
				wptc_log(array(), "--------exitng by backup path time--------");
				global $current_process_file_id;
				backup_proper_exit_wptc('', $current_process_file_id);
			}

			wptc_log(array(), "--------must have uploaded--------");
			return $this->utils->formatted_upload_result($complete_backup_result);

		} catch (Exception $e) {
			$err_reason = $e->getMessage();
			$err_code = $e->getCode();
			WPTC_Base_Factory::get('Wptc_App_Functions')->log_activity('backup', 'Chunk upload restarted File (' . $file . ') Reason : ' . $err_code . ' - ' . $err_reason);
			WPTC_Base_Factory::get('Wptc_App_Functions')->reset_chunk_upload_on_failure($file, $err_code . ' - ' . $err_reason);
		}
	}

	public function getFile($file, $outFile = false, $revision = null, $isChunkDownload = array(), $g_file_id = null) {
		try {

			$handle = null;
			if ($outFile !== false) {
				$this->utils->prepareOpenSetOutFile($outFile, 'wb', $handle);
			}

			$download_file_dets                = array();
			$download_file_dets['outFile']     = $outFile;
			$download_file_dets['g_file_id']   = $g_file_id;
			$download_file_dets['revision_id'] = $revision;

			$process_download_result = $this->process_download($handle, $download_file_dets);

			if ($handle) {
				fclose($handle);
			}
			return $process_download_result;

		} catch (Exception $e) {
			throw $e;
		}
	}

	public function process_download(&$handle, $download_file_dets) {
		try {

			$file 		 = $this->service->revisions->get($download_file_dets['g_file_id'], $download_file_dets['revision_id']);
			$downloadUrl = $file->getDownloadUrl();

			if (!$downloadUrl) {
				return array("error" => "Google Drive file doesnt have nay content.", "error_code" => "google_error_download_url");
			}

			$request         = new WPTC_Google_Http_Request($downloadUrl, 'GET', null, null);
			$signHttpRequest = $this->client->getAuth()->sign($request);
			$httpRequest     = $this->client->getIo()->makeRequest($signHttpRequest);

			if ($httpRequest->getResponseHttpCode() == 200) {
				if($this->is_zero_bytes_file($download_file_dets['outFile'], $download_file_dets['g_file_id'])){
					fwrite($handle, '');
				} else {
					fwrite($handle, $httpRequest->getResponseBody());
				}
				return true;
			} else {
				wptc_log(array(), '--------google_error_bad_response_code--------');
				return true;
				//file failed but do not stop because of this one file failure.
				// return array("error" => "There is some error.", "error_code" => "google_error_bad_response_code");
			}
		} catch (Exception $e) {
			wptc_log($e->getMessage(), "--------excepion process_download--------");

			$err_code = $e->getCode();

			if ( $e->getCode() == 404 || $e->getCode() == 0) {
				return array("error" => $e->getMessage(), "error_code" => $e->getCode());
			}
			throw $e;
		}
	}

	private function is_zero_bytes_file($file, $g_file_id){
		$uploaded_file_size = $this->processed_files->get_file_uploaded_file_size($file, $g_file_id);
		wptc_log($uploaded_file_size, '---------------$uploaded_file_size-----------------');
		if ($uploaded_file_size != 1) {
			return false;
		}
		return wptc_is_file_in_zero_bytes_list($file);
	}

	public function chunkedDownload($file, $outFile = false, $revision = null, $isChunkDownload = array(), $g_file_id = null, $meta_file_download = null) {
		$handle = null;
		$tempFolder = $this->utils->getTempFolderFromOutFile(wp_normalize_path($outFile));
		if ($outFile !== false) {
			if ($isChunkDownload['c_offset'] == 0) {
				//while restoring ... first
				$tempFolderFile = $this->utils->prepareOpenSetOutFile($outFile, 'wb', $handle);
			} else {
				$tempFolderFile = $this->utils->prepareOpenSetOutFile($outFile, 'rb+', $handle);
			}
		}

		$download_file_dets                = array();
		$download_file_dets['outFile']     = $outFile;
		$download_file_dets['g_file_id']   = $g_file_id;
		$download_file_dets['revision_id'] = $revision;

		fseek($handle, $isChunkDownload['c_offset']);
		$result = $this->process_multipart_download($handle, $download_file_dets, $isChunkDownload);

		if ($result && !isset($result['error'])) {
			$offset = ftell($handle);
			if (empty($meta_file_download)) {
				if ($this->tracker) {
					$this->tracker->track_download($outFile, false, $offset, $isChunkDownload);
				}
			} else {
				$this->tracker->track_meta_download($offset, $isChunkDownload);
			}
			if ($handle) {
				fclose($handle);
			}
			return array(
				'name' => ($outFile) ? $outFile : basename($file),
				'chunked' => true,
			);
		} else if (!empty($result) && is_array($result) && isset($result['too_many_requests'])) {
			return $result;
		} else {
			if ($handle) {
				fclose($handle);
			}

			return array('error' => $result['error']);
		}

	}

	public function process_multipart_download(&$handle, $download_file_dets, $isChunkDownload) {
		try {

			$file = $this->service->revisions->get($download_file_dets['g_file_id'], $download_file_dets['revision_id']);

			$downloadUrl = $file->getDownloadUrl();
			wptc_log($downloadUrl, "--------downloadUrl--------");
			if ($downloadUrl) {
				$request = new WPTC_Google_Http_Request($downloadUrl, 'GET', array('Range' => $this->utils->get_formatted_range($isChunkDownload)), null);

				$signHttpRequest = $this->client->getAuth()->sign($request);
				$httpRequest = $this->client->getIo()->makeRequest($signHttpRequest);
				if ($httpRequest->getResponseHttpCode() == 200 || $httpRequest->getResponseHttpCode() == 206) {
					$result = fwrite($handle, $httpRequest->getResponseBody());

					if($result == false){
						wptc_log($result, "--------process_multipart_download----fwrite result is false ----");
					}

					return true;
				} else {
					return array("error" => "There is some error.");
				}
			} else {
				return array("error" => "Google Drive file doesnt have nay content.");
			}
		} catch (Exception $e) {
			wptc_log($e->getMessage(), "--------excepion process_multipart_download--------");
			throw $e;
		}
	}

	public function retrieve_revisions($fileId) {
		try {
			$revisions = $this->service->revisions->listRevisions($fileId);
			return $revisions->getItems();
		} catch (Exception $e) {
			return false;
		}

		return false;
	}

	private function add_folders_id_cache($path, $id){

		if ($this->is_skip_caches($path)) {
			wptc_log(array(),'-----------YES SKIP----------------');
			return ;
		}

		global $wptc_gdrive_dirs_ids;

		$path = wptc_remove_fullpath($path);

		if ($path === '/') {
			return ;
		}

		$new_path = array(
			'file' => $path,
			'g_file_id' => $id
		);

		if (empty($wptc_gdrive_dirs_ids)) {
			$wptc_gdrive_dirs_ids = $new_path;
		} else {
			if (search_array_wptc($wptc_gdrive_dirs_ids, 'file', $path) === false) {
				$wptc_gdrive_dirs_ids[] = $new_path;
			}
		}

		if (count($wptc_gdrive_dirs_ids) > 50) {
			$this->processed_files->insert_gdrive_caches();
		}
	}

	private function is_skip_caches($path){

		if (wptc_add_trailing_slash($path) === WPTC_ABSPATH) {
			return true;
		}

		if (ltrim($path, '/') === $this->site_root_folder) {
			return true;
		}

		return false;
	}
}
