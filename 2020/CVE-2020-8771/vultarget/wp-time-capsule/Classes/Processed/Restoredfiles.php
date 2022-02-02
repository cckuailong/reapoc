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

class WPTC_Processed_Restoredfiles extends WPTC_Processed_Base {
	private $is_multisite         = false;
	private $multisite_upload_dir = false;

	protected function getTableName() {
		return 'restored_files';
	}

	protected function getProcessType() {
		if (!$this->config->get_option('is_bridge_process')) {
			return null;
		} else {
			return 'restore';
		}
	}

	protected function getId() {
		return 'file';
	}

	protected function getRevisionId() {
		return 'revision_id';
	}

	protected function getRestoreTableName() {
		return 'restored_files';
	}

	protected function getFileId() {
		return 'file_id';
	}

	protected function getUploadMtime() {
		return 'mtime_during_upload';
	}

	public function get_restored_files_from_base() {
		return $this->get_processed_restores();
	}

	public function get_restore_queue_from_base() {
		return $this->get_all_restores();
	}

	public function get_limited_restore_queue_from_base($offset) {
		return $this->get_limited_restores($offset);
	}

	public function get_sql_files_iterator_of_folder($cur_res_b_id, $folder_name) {
		return $this->get_sql_files_iterator_of_folder_by_restore_id($cur_res_b_id, $folder_name);
	}

	public function get_sql_files_iterator_for_site($cur_res_b_id, $offset) {
		return $this->get_sql_files_iterator_for_whole_site_by_restore_id($cur_res_b_id, $offset);
	}

	public function get_file_count() {
		$this_count = $this->db->get_var(" SELECT COUNT(*) FROM {$this->db->base_prefix}wptc_processed_{$this->getTableName()} WHERE download_status = 'done' ");
		return $this_count;
	}

	public function get_file($file_name) {

		$file_name = wp_normalize_path($file_name);

		$prepared_query = $this->db->prepare(" SELECT * FROM {$this->db->base_prefix}wptc_processed_{$this->getTableName()} WHERE file = %s ", $file_name);

		$this_file = $this->db->get_results($prepared_query);

		if (!empty($this_file)) {
			return $this_file[0];
		}

		return false;
	}

	public function get_formatted_sql_file_for_restore_to_point_id($backup_id, $type) {

		$value = $this->db->get_results(
			$this->db->prepare("SELECT * FROM {$this->db->base_prefix}wptc_processed_files WHERE backupID = %s AND file LIKE '%%-backup.sql%%' AND file LIKE '%%-wptc-secret%%'", $backup_id)
		);

		if (empty($value)) {
			if(!$this->is_realtime_partial_query_backup($backup_id, $type)){
				wptc_log(array(),'-----------Not realtime----------------');
				return false;
			}

			$this->config->set_option('is_realtime_partial_query_restore', true);
			wptc_log(array(),'-----------YES realtime----------------');
			return $this->get_realtime_database_backups($backup_id);
		}

		$prepared_file_array = array();

		$prepared_file_array[$value[0]->revision_id] = array();
		//$prepared_file_array[$value[0]->revision_id]['file_name'] = str_replace("\\", "\\\\", $file);
		$prepared_file_array[$value[0]->revision_id]['file'] = wp_normalize_path($value[0]->file);
		$prepared_file_array[$value[0]->revision_id]['uploaded_file_size'] = $value[0]->uploaded_file_size;
		$prepared_file_array[$value[0]->revision_id]['mtime_during_upload'] = $value[0]->mtime_during_upload;
		$prepared_file_array[$value[0]->revision_id]['g_file_id'] = $value[0]->g_file_id;
		$prepared_file_array[$value[0]->revision_id]['file_hash'] = $value[0]->file_hash;
		$prepared_file_array[$value[0]->revision_id]['revision_number'] = $value[0]->revision_number;
		$prepared_file_array[$value[0]->revision_id]['backup_id'] = $value[0]->backupID;

		return $prepared_file_array;
	}

	public function get_state_file($backup_id) {

		$value = $this->db->get_results(
			$this->db->prepare("SELECT * FROM {$this->db->base_prefix}wptc_processed_files WHERE backupID = %s AND file LIKE '%%wptc_current_files_state.txt%%'", $backup_id)
		);

		$prepared_file_array = array();

		if (empty($value)) {
			return $prepared_file_array;
		}

		$prepared_file_array[$value[0]->revision_id] = array();
		$prepared_file_array[$value[0]->revision_id]['file'] = wp_normalize_path($value[0]->file);
		$prepared_file_array[$value[0]->revision_id]['uploaded_file_size'] = $value[0]->uploaded_file_size;
		$prepared_file_array[$value[0]->revision_id]['mtime_during_upload'] = $value[0]->mtime_during_upload;
		$prepared_file_array[$value[0]->revision_id]['g_file_id'] = $value[0]->g_file_id;
		$prepared_file_array[$value[0]->revision_id]['file_hash'] = $value[0]->file_hash;
		$prepared_file_array[$value[0]->revision_id]['revision_number'] = $value[0]->revision_number;
		$prepared_file_array[$value[0]->revision_id]['backup_id'] = $value[0]->backupID;

		return $prepared_file_array;
	}

	private function is_realtime_partial_query_backup($backup_id, $type){

		if ($type !== 'S') {
			return false;
		}

		$saved_queries = $this->db->get_var(
								$this->db->prepare(
									" SELECT file FROM {$this->db->base_prefix}wptc_processed_files WHERE backupID = %s AND file LIKE '%%wptc_saved_queries%%' AND file LIKE '%%-wptc-secret%%'", $backup_id
								)
							);
		wptc_log($saved_queries,'-----------saved_queries----------------');
		//If saved queries found on this backup then assume its an partial query backup.
		if (!empty($saved_queries)) {
			return true;
		}

		$is_auto_backup = $this->db->get_var(
								$this->db->prepare(
									" SELECT this_id FROM {$this->db->base_prefix}wptc_backups WHERE backup_id = %s AND backup_name = 'Auto Backup'" , $backup_id
								)
							);
		wptc_log($is_auto_backup,'-----------$is_auto_backup----------------');
		//If saved queries found on this backup then assume its an partial query backup.
		if (!empty($is_auto_backup)) {
			return true;
		}

		return false;
	}

	private function get_realtime_database_backups($backup_id){

		$sql = "SELECT * FROM {$this->db->base_prefix}wptc_processed_files WHERE backupID >= (
					SELECT backupID FROM {$this->db->base_prefix}wptc_processed_files WHERE backupID <= " . $backup_id ." AND file LIKE '%-backup.sql%' AND file LIKE '%-wptc-secret%' ORDER BY file_id DESC LIMIT 1
					) AND backupID <= " . $backup_id ." AND file LIKE '%-wptc-secret%' AND file NOT LIKE '%-wptc_meta%' ORDER BY file_id ASC";

		$db_files =	$this->db->get_results(
							$sql
			);

		if (empty($db_files)) {
			return false;
		}

		$prepared_file_array = array();

		$rename_counter = $counter = 0;

		foreach ($db_files as $db_file) {

			$file = $this->sort_partial_query_backups($db_file->file, $rename_counter);

			$prepared_file_array[$counter] = array();
			$prepared_file_array[$counter]['file'] = wp_normalize_path($file);
			$prepared_file_array[$counter]['uploaded_file_size'] = $db_file->uploaded_file_size;
			$prepared_file_array[$counter]['mtime_during_upload'] = $db_file->mtime_during_upload;
			$prepared_file_array[$counter]['g_file_id'] = $db_file->g_file_id;
			$prepared_file_array[$counter]['file_hash'] = $db_file->file_hash;
			$prepared_file_array[$counter]['revision_number'] = $db_file->revision_number;
			$prepared_file_array[$counter]['revision_id'] = $db_file->revision_id;
			$prepared_file_array[$counter]['backup_id'] = $db_file->backupID;
			$counter++;
		}

		wptc_log($prepared_file_array,'-----------$prepared_file_array----------------');

		return $prepared_file_array;
	}

	public function sort_partial_query_backups($file, &$rename_counter) {

		if (empty($file) ) {
			return $file;
		}

		if (false === strstr($file, "wptc_saved_queries")) {
			return $file;
		}

		$file = wptc_remove_secret($file, false);
		return $this->rename_partial_query_file($file, $rename_counter);
	}

	public function rename_partial_query_file($file, &$rename_counter) {

		if (strstr($file, '.crypt') !== false && strstr($file, '.gz') !== false) {
			return substr($file, 0, -13) . '_' . ++$rename_counter . '.sql.gz.crypt';
		}

		if (strstr($file, '.gz') !== false) {
			return substr($file, 0, -7) . '_' . ++$rename_counter . '.sql.gz';
		}

		if (strstr($file, '.crypt') !== false) {
			return substr($file, 0, -10) . '_' . ++$rename_counter . '.sql.crypt';
		}

		return substr($file, 0, -4) . '_' . ++$rename_counter . '.sql';
	}

	public function file_complete($file) {
		$this->update_file($file, 0, 0);
	}

	public function update_file($file, $upload_id = null, $offset = 0, $backupID = 0, $chunked = null) {

		$file = wptc_remove_fullpath($file);

		//am adding few conditions to insert the new file with new backup id if the file is modified				//manual
		$may_be_stored_file_obj = $this->get_file($file);

		if (empty($may_be_stored_file_obj) && WPTC_BACKWARD_DB_SEARCH) {
			$may_be_stored_file_obj = $this->get_file($file);
		}

		wptc_log($may_be_stored_file_obj, "--------may_be_stored_file_obj--------");

		if ($may_be_stored_file_obj) {
			$may_be_stored_file_id = $may_be_stored_file_obj->file_id;
		}

		$download_status = 'notDone';

		if (!empty($may_be_stored_file_obj) && !empty($may_be_stored_file_id)) {

			if (!empty($offset) && $offset >= $may_be_stored_file_obj->uploaded_file_size) {
				$offset = 0;
				$download_status = 'done';
			}

			//this condition is to update the tables based on file_id
			$upsert_array = array(
				'file' => $file,
				'offset' => $offset,
				'backupID' => $may_be_stored_file_obj->backupID, //get the backup ID from cookie
				'file_id' => $may_be_stored_file_id,
				'revision_id' => $may_be_stored_file_obj->revision_id,
				'download_status' => $download_status,
			);
		} else {
			$upsert_array = array(
				'file' => $file,
				'offset' => $offset,
				'download_status' => $download_status,
				'backupID' => $may_be_stored_file_obj->backupID, //get the backup ID from cookie
			);
		}

		$this->upsert($upsert_array);
	}

	public function add_files_for_restoring($files_to_restore, $restore_app_functions_obj, $check_hash = false) {

		if (empty($files_to_restore)) {
			return false;
		}

		$query = '';
		$is_multicall_needed = false;
		$no_of_files_to_restore = count($files_to_restore);

		wptc_log(count($files_to_restore), "--------no of files to restore--------");

		$count = 0;

		foreach ($files_to_restore as $revision => $file_meta) {
			if (empty($file_meta) || empty($file_meta['file'])) {
				continue;
			}
			
			$count++;

			$file = $file_meta['file'];

			if (!wptc_is_always_include_file($file) && $restore_app_functions_obj->is_multisite_skip_file($file)) {
				
				continue;
			}

			//remove secrets from amy wptc file
			if ( strpos($file_meta['file'], "wptc-secret") !== false ) {
				$file_meta['file'] = wptc_remove_secret($file_meta['file'], false);
			}

			if (empty($file_meta['file'])) {
				continue;
			}

			if (empty($file_meta['file_hash'])) {
				$file_meta['file_hash'] = $this->get_file_hash($file_meta['file'], $file_meta['backup_id']);
			}

			$file_meta['uploaded_file_size'] =  empty($file_meta['uploaded_file_size']) ? 0 : $file_meta['uploaded_file_size'];
			$file_meta['revision_id'] = empty($file_meta['revision_id']) ? $revision : $file_meta['revision_id'];
			$file_meta['offset'] = empty($file_meta['offset']) ? 0 : $file_meta['offset'];
			$file_meta['revision_number'] = empty($file_meta['revision_number']) ? 0 : $file_meta['revision_number'];

			if ($check_hash) {
				$is_same_hash = $this->restore_app_functions->is_file_hash_same($file_meta['file'], $file_meta['file_hash'] ,$file_meta['uploaded_file_size'], $file_meta['mtime_during_upload']);
				// wptc_log($is_same_hash, '---------------$is_same_hash-----------------');

				if ($is_same_hash) {
					// wptc_log($file_meta['file'], '-----Hash does not modified so continue-----------');
					continue;
				}
			}

			$file_meta['file'] = wptc_remove_fullpath($file_meta['file']);

			if ($this->is_already_inserted($file_meta['file'])) {
				// wptc_log($file_meta['file'], '-----file is duplicating so continue-----------');
				continue;
			}

			$query .= empty($query) ? "(" : ",(" ;

			$query .= $this->db->prepare("%s, %d, %f, %s, %s, %s , 'notDone' , %s , %s, %s)", $file_meta['file'], $file_meta['offset'], $file_meta['backup_id'], $file_meta['revision_number'], $file_meta['revision_id'], $file_meta['mtime_during_upload'], $file_meta['uploaded_file_size'], $file_meta['g_file_id'], $file_meta['file_hash']);

			$is_multicall_needed = $restore_app_functions_obj->maybe_call_again_tc(true);

			wptc_manual_debug('', 'add_files_for_restoring', 200);

			if( $is_multicall_needed ){

				wptc_log('', "--------multicall_is_needed----add_files_for_restoring----");

				break;
			}

			if($count == 301 || $is_multicall_needed){
				$count = 0;

				if (empty($query)) {

					continue;
				}

				$this->insert_this_query($query);
				$query = '';

				continue;
			}
		}

		if(!empty($query)){
			$this->insert_this_query($query);
			$query = '';
		}

		if( $is_multicall_needed ){
			$restore_app_functions_obj->die_with_msg("wptcs_callagain_wptce");
		}
	}

	public function insert_this_query($query = '')
	{
		if (empty($query)) {

			return;
		}

		$sql = "insert into " . $this->db->base_prefix . "wptc_processed_restored_files (file, offset, backupID, revision_number, revision_id, mtime_during_upload, download_status, uploaded_file_size, g_file_id, file_hash) values $query";

		$result = $this->db->query($sql);

		if ($result === false) {
			wptc_log(substr($query, 0, 300),'-----------$query---error on add_files_for_restoring-------------');
			wptc_log($this->db->last_error,'-----------$this->db->last_error----------------');
		}

		wptc_log($result,'-----------$add_files_for_restoring file inserted---batch-------------');
	}

	private function is_already_inserted($file){
		$qry = "SELECT file_id FROM " . $this->db->base_prefix . "wptc_processed_restored_files WHERE file = '" . $file . "'";
		$is_already_inserted = $this->db->get_var($qry);
		// wptc_log($is_already_inserted ,'-----------$is_already_inserted----------------');
		return $is_already_inserted;
	}

	public function add_files($new_files) {
		foreach ($new_files as $file) {
			$this->upsert(array(
				'file' => $file['file'],
				'uploadid' => null,
				'offset' => null,
				'backupID' => $file['backupID'],
				'revision_number' => $file['revision_number'],
				'revision_id' => $file['revision_id'],
				'mtime_during_upload' => $file['mtime_during_upload'],
				'download_status' => 'done',
				'copy_status' => $file['copy_status'],
			));
		}

		return $this;
	}

	public function add_future_files($files) {
		if (empty($files)) {
			return false;
		}

		$query = '';

		foreach ($files as $key => $file_meta) {
			if (empty($file_meta)) {
				continue;
			}

			$query .= empty($query) ? "(" : ",(" ;

			$query .= $this->db->prepare("%s, %d, %f, %s , %s , %s, %s , %s , %s, %s, %d)", $file_meta['file'], $file_meta['offset'], $file_meta['backupID'], $file_meta['revision_number'], $file_meta['revision_id'], $file_meta['mtime_during_upload'], $file_meta['download_status'], $file_meta['uploaded_file_size'], $file_meta['g_file_id'], $file_meta['file_hash'], $file_meta['is_future_file']);
		}

		if (empty($query)) {
			return ;
		}

		$sql = "insert into " . $this->db->base_prefix . "wptc_processed_restored_files (file, offset, backupID, revision_number, revision_id, mtime_during_upload, download_status, uploaded_file_size, g_file_id, file_hash, is_future_file) values $query";

		$result = $this->db->query($sql);

		return ;
	}

	private function get_file_hash($file_name, $backupID){
		$sql = "SELECT file_hash FROM {$this->db->prefix}wptc_processed_files WHERE file = '".$file_name."' AND backupID=".$backupID." ORDER BY file_id";
		return $this->db->get_var($sql);
	}

	public function get_future_files($offset = 0){
		$sql = "SELECT file FROM {$this->db->prefix}wptc_processed_restored_files WHERE is_future_file = 1 ORDER BY file_id ASC limit $offset , " . WPTC_RESTORE_ADDING_FILES_LIMIT;
		return $this->db->get_results($sql);
	}

	public function delete_file_from_download_list($file){
		$sql = "DELETE FROM {$this->db->prefix}wptc_processed_restored_files WHERE is_future_file != 1 AND file = '" . $file . "'";
		wptc_log($sql,'-----------$sql----------------');
		$result = $this->db->query($sql);
		wptc_log($result,'-----------delete $result----------------');
	}
}
