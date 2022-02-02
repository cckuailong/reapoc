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

class WPTC_Extension_DefaultOutput extends WPTC_Extension_Base {
	const MAX_ERRORS = 100;
	const CHUNK_SIZE = 4194304; //4MB chunk size

	private
	$error_count,
	$root,
	$processed_files,
	$chunk_size;

	public function set_root($root) {
		$this->root = $root;
		return $this;
	}

	public function out($source, $file, $processed_file = null) {
		if ($this->error_count > self::MAX_ERRORS) {
			WPTC_Factory::get('logger')->log(sprintf(__("The backup is having trouble uploading files to " . DEFAULT_REPO_LABEL . ", it has failed %s times and is aborting the backup.", 'wptc'), self::MAX_ERRORS), 'backups', wptc_get_cookie('backupID'));
			throw new Exception(sprintf(__('The backup is having trouble uploading files to ' . DEFAULT_REPO_LABEL . ', it has failed %s times and is aborting the backup.', 'wptc'), self::MAX_ERRORS));
			$backup = new WPTC_BackupController();
			$backup->proper_backup_force_complete_exit();
		}
		if (!$this->dropbox) {
			WPTC_Factory::get('logger')->log(sprintf(__("The backup is having trouble uploading files to " . DEFAULT_REPO_LABEL . ", it has failed %s times and is aborting the backup.", 'wptc'), self::MAX_ERRORS), 'backups', wptc_get_cookie('backupID'));
			throw new Exception(__("" . DEFAULT_REPO_LABEL . " API not set"));
			$backup = new WPTC_BackupController();
			$backup->proper_backup_force_complete_exit();
		}
		$dropbox_path = $this->config->get_cloud_path($source, $file, $this->root);
		$dropbox_path = wp_normalize_path($dropbox_path);
		if (empty($this->processed_files)) {
			$this->processed_files = WPTC_Factory::get('processed-files');
		}

		try {

			$file_size = filesize($file);

				if ($file_size > $this->get_chunked_upload_threashold()) {

					$msg = __("Uploading large file '%s' (%sMB) in chunks", 'wptc');

					if ($processed_file && $processed_file->offset > 0) {
						$msg = __("Resuming upload of large file '%s'", 'wptc');
					}

					WPTC_Factory::get('logger')->log(sprintf($msg, basename($file), round($file_size / 1048576, 1)), 'backups', $this->backup_id);
					wptc_manual_debug('', 'during_chunk_upload');
					return $this->dropbox->chunk_upload_file($dropbox_path, $file, $processed_file);
				} else {
					return $this->dropbox->upload_file($dropbox_path, $file);
				}

		} catch (Exception $e) {
			
			WPTC_Factory::get('logger')->log(__("Error uploading to Cloud " . $e->getMessage(), 'wptc'), 'backups', $this->backup_id);

			$this->error_count++;

			//if there is any error we are showing it via ajax
			$error_array = array();
			$temp_file = wp_normalize_path($file);
			$error_array['file_name'] = $temp_file;
			$error_array['error'] = strip_tags($e->getMessage());
			$this->config->append_option_arr_bool_compat('mail_backup_errors', $error_array, 'unable_to_upload');

			return $error_array;
		}
	}

	public function drop_download($source, $file, $revision = null, $processed_file = null, $restore_single_file = null, $meta_file_download = null) {
		$restore_action_id = $this->config->get_option('restore_action_id');

		if (!$this->dropbox) {
			if($meta_file_download == 1){
				$this->dropbox = WPTC_Factory::get(DEFAULT_REPO);
			} else {
				WPTC_Factory::get('logger')->log(__(" API not set"), 'restores', $restore_action_id);
				throw new Exception(__(" API not set"));
			}
		}

		$fileindrop = $file;
		if (strpos($file, 'wptc_saved_queries') !== false) {
			$fileindrop = $this->delete_all_between('wptc_saved_queries', '.sql', $file);
		}
		$dropbox_path = $this->config->get_cloud_path($source, $fileindrop, $this->root);

		try {
			$dropbox_source_file = $dropbox_path . '/' . basename($fileindrop);

			if (!empty($meta_file_download)) {
				$dropbox_source_file = $source;
			}

			if ($restore_single_file['uploaded_file_size'] < self::CHUNK_SIZE) {
				return $this->dropbox->download_file($dropbox_source_file, $file, $revision, null, $restore_single_file['g_file_id']);
			} else {
				$isChunkDownload['c_offset'] = (!empty($processed_file->offset)) ? $processed_file->offset : 0; //here am getting the restored files offset ..
				if (!$processed_file) {
					$isChunkDownload['c_limit'] = self::CHUNK_SIZE;
				} else {
					$isChunkDownload['c_limit'] = (($isChunkDownload['c_offset'] + self::CHUNK_SIZE) > $processed_file->uploaded_file_size) ? ($processed_file->uploaded_file_size) : ($isChunkDownload['c_offset'] + self::CHUNK_SIZE);
				}
				if ($meta_file_download == 1) {
					$meta_data_status = $this->config->get_option('meta_chunk_download');
					if (!empty($meta_data_status)) {
						$meta_data_status = unserialize($meta_data_status);
						$download_status = $meta_data_status['download_status'];
						if ($download_status == 'done') {
							return 'already completed';
						}
						$offset = $meta_data_status['c_offset'];
						$isChunkDownload['c_offset'] = $offset;
						$isChunkDownload['c_limit'] = (($isChunkDownload['c_offset'] + self::CHUNK_SIZE) > $processed_file->uploaded_file_size) ? ($processed_file->uploaded_file_size) : ($isChunkDownload['c_offset'] + self::CHUNK_SIZE);
					} else {
						$isChunkDownload['c_limit'] = self::CHUNK_SIZE;
					}
				}
				return $this->dropbox->chunk_download_file($dropbox_source_file, $file, $revision, $isChunkDownload, $restore_single_file['g_file_id'], $meta_file_download);
			}

		} catch (Exception $e) {
			wptc_log($e->getMessage(), "--------default output exception--------");
			WPTC_Factory::get('logger')->log(sprintf(__("Error downloading '%s' : %s", 'wptc'), $file, strip_tags($e->getMessage())), 'restores', $restore_action_id);

			reset_restore_related_settings_wptc();

			$this->error_count++;

			$error_array = array();
			$error_array['error'] = strip_tags($e->getMessage());
			echo json_encode($error_array);

			exit;
		}
	}

	public function delete_all_between($beginning, $end, $string) {
		$beginningPos = strrpos($string, $beginning) + strlen($beginning);
		$endPos = strrpos($string, $end);
		$len = $endPos - $beginningPos;
		if ($beginningPos === false || $endPos === false) {
			return $string;
		}
		$textToDelete = substr($string, $beginningPos, $len);
		wptc_log($textToDelete, "--------textToDelete--------");
		return str_replace($textToDelete, '', $string);
	}

	public function retry_revision_failed_file($source, $file, $file_meta){
		wptc_log(func_get_args(), "--------" . __FUNCTION__ . "--------");

		if ($file_meta->download_status === 'retry') {
			return false;
		}

		global $wpdb;

		$revisions = $this->dropbox->retrieve_revisions($file_meta);

		wptc_log($revisions,'-----------$revisions----------------');

		if (empty($revisions)) {
			$wpdb->update("{$wpdb->base_prefix}wptc_processed_restored_files", array( 'download_status' => 'retry' ),  array('file_id' => $file_meta->file_id) );
			return false;
		}

		$revisions = $this->get_revision_object($revisions);

		$last_time_diff = 0;
		$revision_id = false;

		foreach ($revisions as $value) {
			$modified_date = $this->get_modified_date($value);
			if ($modified_date === false) {
				continue;
			}

			wptc_log($modified_date,'-----------$modified_date----------------');

			$timestamp = strtotime($modified_date);

			wptc_log($timestamp,'-----------$timestamp----------------');
			// $time_diff = $file_meta->backupID - $timestamp;
			$time_diff = $timestamp - $file_meta->backupID;

			if ($time_diff <= -300) {
				continue;
			}

			$time_diff = abs($time_diff);

			wptc_log($time_diff,'-----------$time_diff----------------');

			if ($last_time_diff === 0 || $last_time_diff > $time_diff )  {
				$revision_id = $this->get_id($value);
				wptc_log($revision_id,'-----------$revision_id----------------');
				if ($revision_id === false) {
					continue;
				}
				$last_time_diff = $time_diff;
			}

		}

		if ($revision_id === false) {
			$wpdb->update("{$wpdb->base_prefix}wptc_processed_restored_files", array( 'download_status' => 'retry' ),  array('file_id' => $file_meta->file_id) );
			return false;
		}

		$result = $wpdb->update("{$wpdb->base_prefix}wptc_processed_restored_files", array('download_status' => 'retry', 'revision_id' => $revision_id),  array('file_id' => $file_meta->file_id) );

		wptc_log($result,'-----------$result----------------');
		wptc_log($wpdb->last_error,'-----------$wpdb->last_error----------------');
		wptc_log($last_time_diff,'-----------$value->last_time_diff()----------------');
		wptc_log($revision_id,'-----------$value->revision_id()----------------');

		return true;
	}

	private function get_revision_object($response){
		switch (DEFAULT_REPO) {
			case 'dropbox':
				return $response['body']->entries;
			case 'g_drive':
				return $response;
			case 's3':
				return false;
		}
	}

	private function get_modified_date($value){
		switch (DEFAULT_REPO) {
			case 'dropbox':
				return $value->client_modified;
			case 'g_drive':
				return $value->getModifiedDate();
			case 's3':
				return false;
		}
	}

	private function get_id($value){
		switch (DEFAULT_REPO) {
			case 'dropbox':
				return $value->rev;
			case 'g_drive':
				return $value->getId();
			case 's3':
				return false;
		}
	}

	public function start() {
		return true;
	}

	public function end() {}
	public function complete() {}
	public function failure() {}

	public function get_menu() {}
	public function get_type() {}

	public function is_enabled() {}
	public function set_enabled($bool) {}
	public function clean_up() {}
}
