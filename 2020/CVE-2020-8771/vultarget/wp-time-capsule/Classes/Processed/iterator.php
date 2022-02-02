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

class WPTC_Processed_iterator extends WPTC_Processed_Base {
	const COMPLETE = -1;

	protected function getTableName() {
		return 'iterator';
	}

	protected function getProcessType() {
		return 'iterator';
	}

	protected function getRestoreTableName() {
		return 'restored_files';
	}

	protected function getId() {
		return 'name';
	}

	protected function getRevisionId() {
		return 'name';
	}

	protected function getFileId() {
		return 'file_id';
	}

	protected function getUploadMtime() {
		return 'mtime_during_upload';
	}

	public function get_table($name) {
		$single_table_result = $this->db->get_results("SELECT * FROM {$this->db->base_prefix}wptc_processed_{$this->getTableName()} WHERE name = '$name'");

		if (!empty($single_table_result)) {
			return $single_table_result[0];
		}
	}

	public function get_unfnished_folder() {
		$sql = "SELECT * FROM {$this->db->base_prefix}wptc_processed_{$this->getTableName()} WHERE offset != -1 LIMIT 1";
		$response = $this->db->get_results($sql);

		// wptc_log($response, '-------get_unfnished_folder---response----');

		return empty($response) ? false : $response[0];
	}

	public function is_complete($name) {
		$table = $this->get_table($name);

		if ($table) {
			return $table->offset == self::COMPLETE;
		}

		return false;
	}

	public function count_complete() {
		$i = 0;

		$process_table_values = $this->db->get_results("SELECT * FROM {$this->db->base_prefix}wptc_processed_{$this->getTableName()}");

		foreach ($process_table_values as $table) {
			if ($table->offset == self::COMPLETE) {
				$i++;
			}
		}

		return $i;
	}

	public function update_iterator($table, $offset) {
		$this->upsert(array(
			'name' => $table,
			'offset' => $offset,
		));
	}
}
