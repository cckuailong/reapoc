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

class WPTC_Logger {
	const LOGFILE = 'wptc-backup-log.txt';

	private $logFile = null;

	public function log($msg, $type = "", $action_id = "", $data = null , $show_user = 1) {
		$this->set_log_now($type, $msg, $action_id, $data, $show_user);
		//return true;
		//$fh = fopen($this->get_log_file(), 'a');

		//$msg = iconv('UTF-8', 'UTF-8//IGNORE', $msg);
		// $log = sprintf("%s@%s", date('Y-m-d H:i:s', strtotime(current_time('mysql'))), $msg) . "\n";

		/* if (!empty($files)) {
			$log .= "Uploaded Files:" . json_encode($files) . "\n";
		*/
		//@file_put_contents($this->get_log_file(), $log, FILE_APPEND);

		/* if (@fwrite($fh, $log) === false || @fclose($fh) === false) {
			throw new Exception('Error writing to log file.');
		*/
	}

	public function get_log() {
		global $wp_filesystem;
		if (!$wp_filesystem) {
			initiate_filesystem_wptc();
			if (empty($wp_filesystem)) {
				send_response_wptc('FS_INIT_FAILED-010');
				return false;
			}
		}
		$file = $this->get_log_file();
		if (!file_exists($file)) {
			return false;
		}

		$contents = trim($wp_filesystem->get_contents($file));
		if (strlen($contents) < 1) {
			return false;
		}

		return explode("\n", $contents);
	}

	public function delete_log() {
		global $wp_filesystem;
		if (!$wp_filesystem) {
			initiate_filesystem_wptc();
			if (empty($wp_filesystem)) {
				send_response_wptc('FS_INIT_FAILED-011');
				return false;
			}
		}
		$this->logFile = null;
		$this_log_file = $this->get_log_file();

		if ($wp_filesystem->exists($this_log_file)) {
			$wp_filesystem->delete($this->get_log_file());
		}
	}

	public function get_log_file() {
		if (!$this->logFile) {

			$config = WPTC_Factory::get('config');
			$path = $config->wp_filesystem_safe_abspath_replace($config->get_backup_dir()) . self::LOGFILE;

			$files = glob($path . '.*');
			if (isset($files[0])) {
				$this->logFile = $files[0];
			} else {
				$this->logFile = $path . '.' . WPTC_Factory::secret(self::LOGFILE);
			}
		}
		return $this->logFile;
	}

	public function set_log_now($type, $msg, $action_id, $data, $show_user) {
		global $wpdb;
		$current_time = time();
		$LogData = serialize(array('action' => $type, 'log_time' => $current_time, 'msg' => $msg, 'data' => $data));
		$action_id = empty($action_id) ? $current_time : $action_id;
		$result = $wpdb->insert($wpdb->base_prefix . 'wptc_activity_log', array('type' => $type, 'log_data' => $LogData, 'action_id' => $action_id, 'show_user' => $show_user));
	}
}
