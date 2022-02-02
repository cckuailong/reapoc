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

class WPTC_UploadTracker {
	private $processed_files;
	private $processed_restored_files;

	public function __construct() {
		$this->processed_files = new WPTC_Processed_Files();
		//$this->processed_restored_files = new WPTC_Processed_Restoredfiles();
	}

	public function track_upload($file, $upload_id, $offset, $s3_part_number = 1, $s3_parts_array = array()) {
		wptc_log(func_get_args(), "--------" . __FUNCTION__ . "--------");
		WPTC_Factory::get('config')->die_if_stopped();
		if(strrpos($file, 'wordpress-db_meta_data.sql') !== false){
			$config = WPTC_Factory::get('config');
			$config->set_option('meta_data_upload_offset', $offset);
			$config->set_option('meta_data_upload_id', $upload_id);
			$config->set_option('meta_data_upload_s3_part_number', $s3_part_number);
			$config->set_option('meta_data_upload_s3_parts_array', serialize($s3_parts_array));
		}
		$this_processed_file = $this->processed_files->update_file($file, $upload_id, $offset, $s3_part_number, $s3_parts_array);

		WPTC_Factory::get('logger')->log(sprintf(
			__("Uploaded %sMB of %sMB", 'wptc'),
			round($offset / 1048576, 1),
			round(filesize($file) / 1048576, 1)
		), 'backups', wptc_get_cookie('backupID'));
	}

	public function track_download($file, $upload_id = null, $offset = null, $isChunkDownload = array()) {
		wptc_log(func_get_args(), "--------track_download--------");
		//WPTC_Factory::get('config')->die_if_stopped();


		$this->processed_restored_files = new WPTC_Processed_Restoredfiles();
		$this->processed_restored_files->update_file($file, $upload_id, intval($offset));
		$restore_action_id = WPTC_Factory::get('config')->get_option('restore_action_id');

		WPTC_Factory::get('logger')->log(sprintf(
			__("Downloaded %sMB ", 'wptc'),
			round($offset / 1048576, 1)
		), 'restores', $restore_action_id);
	}

	public function track_meta_download($offset, $isChunkDownload){
		$processed_files = WPTC_Factory::get('processed-files');
		$config = WPTC_Factory::get('config');
		$meta_stats = $processed_files->get_last_meta_file();
		if (!$meta_stats) {
			wptc_log(array(), '-----------meta_status is empty-------------');
			die();
		}
		$download_status = 'notDone';
		// wptc_log($meta_stats,'--------------$meta_stats-------------');
		// wptc_log($offset,'--------------$offset-------------');
		if (!empty($offset) && $offset >= $meta_stats->uploaded_file_size) {
			$offset = 0;
			$download_status = 'done';
			$config->set_option('staging_progress_status', 'meta_download_completed');
		}
		$meta_file_download_data = array(
				'download_status' => $download_status,
				'c_offset' => $offset,
				'backupID' => wptc_get_cookie('backupID'),
				);
		// wptc_log($meta_file_download_data,'--------------$meta_file_download_data-------------');
		$config->set_option('meta_chunk_download', serialize($meta_file_download_data));
	}
}
