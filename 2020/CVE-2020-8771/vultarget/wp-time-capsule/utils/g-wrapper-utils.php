<?php

class Utils_Base {
	public function getTempFolderFromOutFile($download_file, $mode = '') {
		$download_file = wp_normalize_path($download_file);
		$temp_folder   = WPTC_Factory::get('config')->get_backup_dir(true) . '/' . WPTC_TEMP_DIR_BASENAME . '/';
		if (wptc_is_wp_content_path($download_file)) {
			$download_file = str_replace(wptc_add_trailing_slash(dirname(WPTC_WP_CONTENT_DIR)), $temp_folder, $download_file);
		} else {
			$download_file = str_replace(WPTC_ABSPATH, $temp_folder, $download_file);
		}

		$this->createRecursiveFileSystemFolder(dirname($download_file));
		return $download_file;
	}

	public function createRecursiveFileSystemFolder($this_temp_folder, $this_absbath_length = null, $override_abspath_check = true) {
		global $wp_filesystem;
		$home_path = get_home_path_wptc();

		if (!$wp_filesystem) {
			initiate_filesystem_wptc();
			if (empty($wp_filesystem)) {
				send_response_wptc('FS_INIT_FAILED-033');
				return false;
			}
		}

		$folders = explode('/', $this_temp_folder);
		foreach ($folders as $key => $folder) {
			$current_folder = '';
			for($i=0; $i<=$key; $i++){
				$sub_dir = (string) $folders[$i];
				if ($sub_dir === false || $sub_dir === '' || $sub_dir === NULL) {
					continue;
				}
				if (is_windows_machine_wptc() && empty($current_folder)) {
					$current_folder .= $sub_dir;
				} else {
					$current_folder .= '/'. $sub_dir;
				}
			}

			if (empty($current_folder)){
				continue;
			}

			if($override_abspath_check && stripos($current_folder.'/', $home_path) === false && $override_abspath_check && stripos($current_folder.'/', WPTC_WP_CONTENT_DIR) === false) {
				continue;
			}

			if ($wp_filesystem && !$wp_filesystem->is_dir($current_folder)) {
				if (!$wp_filesystem->mkdir($current_folder, 0755)) {
					$wp_filesystem->chmod(dirname($current_folder), 0755);
					if(!$wp_filesystem->mkdir($current_folder, 0755)){
					}
				}
			} else {

				if(strpos($current_folder, 'WPTC_TEMP_DIR_BASENAME') !== false && $wp_filesystem->chmod($current_folder, 0755)){
					//
				} else {
					//
				}
			}
		}
	}

	public function prepareOpenSetOutFile($outFile, $mode, &$handle) {
		// wptc_log(func_get_args(), "--------" . __FUNCTION__ . "--------");
		global $wp_filesystem;
		if (!$wp_filesystem) {
			initiate_filesystem_wptc();
			if (empty($wp_filesystem)) {
				send_response_wptc('FS_INIT_FAILED-017');
				return false;
			}
		}
		$tempFolderFile = $this->getTempFolderFromOutFile(wp_normalize_path($outFile));
		$chRes = $wp_filesystem->chmod($tempFolderFile, false, true);

		// wptc_log($tempFolderFile, '--------$tempFolderFile--------');
		// wptc_log($mode, '--------$mode--------');

		$handle = @fopen($tempFolderFile, $mode);
		// wptc_log($handle, '--------$handle--------');
		return $tempFolderFile;
	}

	public function get_formatted_range($isChunkDownload = array()) {
		if (!empty($isChunkDownload)) {
			$this_range = 'bytes=' . $isChunkDownload['c_offset'] . '-' . $isChunkDownload['c_limit'] . '';
		} else {
			$this_range = 'bytes=0-4024000';
		}
		wptc_log($this_range, "--------formatted_range--------");
		return $this_range;
	}
}

class Gdrive_Utils extends Utils_Base {
	public function get_dir_id_from_list_result(&$files) {
		$list_result = array();
		if (!method_exists($files, 'getItems')) {
			return false;
		}
		$list_result = array_merge($list_result, $files->getItems());
		if (empty($list_result)) {
			return array();
		}
		$list_result = (array) $list_result;

		$list_result = (array) $list_result[0];
		$folder_id = $list_result['id'];

		return $folder_id;
	}

	public function formatted_upload_result(&$upload_result, $extra_data = array()) {
		$req_result = new stdclass;
		$req_result->revision = $upload_result->version;
		$req_result->rev = $upload_result->headRevisionId;
		$req_result->bytes = $upload_result->fileSize;
		$req_result->g_file_id = $upload_result->id;
		$req_result->title = $upload_result->title;

		$common_format = array();
		$common_format['body'] = $req_result;

		return $common_format;
	}
}

class S3_Utils extends Utils_Base {
	public function formatted_upload_result($upload_result, $extra_data = array()) {
		$req_result = new stdclass;
		$req_result->revision = $upload_result['VersionId'];
		$req_result->rev = $upload_result['VersionId'];
		$req_result->bytes = $extra_data['filesize'];
		$req_result->g_file_id = '';
		$req_result->title = $extra_data['title'];

		$common_format = array();
		$common_format['body'] = $req_result;

		return $common_format;
	}
}
