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
abstract class WPTC_Processed_Base {
	protected
	$db,
	$processed = array(),
	$restore_app_functions
	;

	public function __construct() {
		$this->db = WPTC_Factory::db();
		if (defined('DB_PREFIX_WPTC')) {
			$this->db->base_prefix = DB_PREFIX_WPTC;
		} else{
			$this->db->base_prefix = 'wp_'; //improve this when we implement continue from email feature for restore
		}
		$this->restore_app_functions = new WPTC_Restore_App_Functions();
	}

	abstract protected function getTableName();

	abstract protected function getProcessType();

	abstract protected function getRestoreTableName();

	abstract protected function getId();

	abstract protected function getRevisionId();

	abstract protected function getFileId(); //file column is not unique now .. so we should update using file_id

	abstract protected function getUploadMtime();

	protected function getBackupID() {
		return 'backupID';
	}

	protected function getVar($val) {
		return $this->db->get_var(
			$this->db->prepare("SELECT * FROM {$this->db->base_prefix}wptc_processed_{$this->getTableName()} WHERE {$this->getId()} = %s", $val)
		);
	}

	protected function get_processed_restores($this_backup_id = null) {
		$all_restores = $this->db->get_results(
			$this->db->prepare("
				SELECT *
				FROM {$this->db->base_prefix}wptc_processed_{$this->getRestoreTableName()} ")
		);

		return $all_restores;
	}

	protected function getBackups($this_backup_id = null) {
		$current_time = time();
		$last_month_time = strtotime(date('Y-m-d', strtotime(date('Y-m') . " -1 month")));

		if (empty($this_backup_id)) {
			$all_backups = $this->db->get_results(
				$this->db->prepare("
				SELECT *
				FROM {$this->db->base_prefix}wptc_processed_{$this->getTableName()}
				WHERE {$this->getBackupID()} > %s ", $last_month_time)
			);
		} else {
			$all_backups = $this->db->get_results(
				$this->db->prepare("
				SELECT *
				FROM {$this->db->base_prefix}wptc_processed_{$this->getTableName()}
				WHERE {$this->getBackupID()} = %s ", $this_backup_id)
			);
		}

		return $all_backups;
	}

	protected function get_all_restores() {
		$all_restores = $this->db->get_results(
			$this->db->prepare("
			SELECT *
			FROM {$this->db->base_prefix}wptc_processed_{$this->getTableName()}  ") //manual
		);
		return $all_restores;
	}

	protected function upsert($data) {
		if (!empty($data[$this->getUploadMtime()])) {
			//am introducing this condition to avoid conflicts with multipart upload     manual
			//am adding an extra condition to check the modified time (if the modified time is different then add the values to DB or else leave it)
			$exists = $this->db->get_var(
				$this->db->prepare("SELECT * FROM {$this->db->base_prefix}wptc_processed_{$this->getTableName()} WHERE {$this->getId()} = %s AND {$this->getUploadMtime()} = %s ", $data[$this->getId()], $data[$this->getUploadMtime()]));
		} else {
			$exists = $this->db->get_var(
				$this->db->prepare("SELECT * FROM {$this->db->base_prefix}wptc_processed_{$this->getTableName()} WHERE {$this->getId()} = %s ", $data[$this->getId()]));
		}
		if (is_null($exists)) {
			$this_insert_result = $this->db->insert("{$this->db->base_prefix}wptc_processed_{$this->getTableName()}", $data);
			if ($this_insert_result) {
				$data['file_id'] = $this_insert_result; //am not adding file_id to the processed restored file array
			}
			// $this->processed[] = (object) $data;
		} else {
			if (!empty($data['file_id'])) {
				$this->db->update( //am changing the whole update process to file_id
					"{$this->db->base_prefix}wptc_processed_{$this->getTableName()}",
					$data,
					array($this->getFileId() => $data[$this->getFileId()])
				);
			} else {
				$this->db->update( //am changing the whole update process to file_id
					"{$this->db->base_prefix}wptc_processed_{$this->getTableName()}",
					$data,
					array($this->getId() => $data[$this->getId()])
				);
			}

			// $i = 0;
			// foreach ($this->processed as $p) {
			// 	$id = $this->getId();
			// 	if ($p->$id == $data[$this->getId()]) {
			// 		break;
			// 	}
			// 	$i++;
			// }

			// $this->processed[$i] = (object) $data;
		}
	}

	public function truncate() {
		$this->db->query("TRUNCATE {$this->db->base_prefix}wptc_processed_{$this->getTableName()}");
	}

	public function get_stored_backup_name($backup_id = null) {
		$this_name = $this->db->get_results("SELECT backup_name FROM {$this->db->base_prefix}wptc_backups WHERE backup_id = '$backup_id'");
		return $this_name;
	}
}
