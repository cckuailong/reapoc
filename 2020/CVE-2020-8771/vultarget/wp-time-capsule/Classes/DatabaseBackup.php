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

class WPTC_DatabaseBackup {
	const WAIT_TIMEOUT = 600; //10 minutes
	const NOT_STARTED = 0;
	const COMPLETE = 1;
	const IN_PROGRESS = 2;

	private $temp,
			$database,
			$config,
			$exclude_class_obj,
			$app_functions,
			$select_query_limit,
			$processed_files,
			$total_tables_size = 0,
			$bulk_table_insert;

	public function __construct($processed = null) {
		$this->database = WPTC_Factory::db();
		$this->config = WPTC_Factory::get('config');
		$this->processed = $processed ? $processed : new WPTC_Processed_iterator();
		$this->exclude_class_obj = WPTC_Base_Factory::get('Wptc_ExcludeOption');
		$this->app_functions = WPTC_Base_Factory::get('Wptc_App_Functions');
		$this->processed_files = WPTC_Factory::get('processed-files');
		$this->set_wait_timeout();
		$this->init_query_limit();
	}

	private function init_query_limit(){
		if (!empty($this->select_query_limit)) {
			return $select_query_limit;
		}

		$this->select_query_limit = $this->app_functions->get_backup_db_query_limit();
		return $this->select_query_limit;
	}

	public function get_status() {

		if (wptc_is_meta_data_backup()) {
			return self::IN_PROGRESS;
		}

		if ($this->processed->count_complete() == 0) {
			return self::NOT_STARTED;
		}

		$count = $this->processed_files->get_overall_tables();

		if ($this->processed->count_complete() <= $count) {
			return self::IN_PROGRESS;
		}

		return self::COMPLETE;
	}

	public function get_file() {
		$file = apply_filters('get_realtime_partial_db_file_wptc', '');

		wptc_log($file,'----------get_file-$file----------------');

		if ($file) {
			//If its real time backup then pass partial db file.
			return $file;
		}

		if (wptc_is_meta_data_backup()) {
			$file = rtrim($this->config->get_backup_dir(), '/') . '/' . DB_NAME . "-wptc_meta.sql";
		} else {
			$file = rtrim($this->config->get_backup_dir(), '/') . '/' . DB_NAME . "-backup.sql";
		}

		$files = glob($file . '*');

		if (isset($files[0])) {
			return $files[0];
		}

		$prepared_file_name = $file . '.' . WPTC_Factory::secret(DB_NAME);

		return $prepared_file_name;
	}

	private function set_wait_timeout() {
		$this->database->query("SET SESSION wait_timeout=" . self::WAIT_TIMEOUT);
	}

	private function write_db_dump_header() {

		if($this->config->choose_db_backup_path() === false){
			$get_default_backup_dir = $this->config->get_default_backup_dir();
			$msg = sprintf(__("A database backup cannot be created because WordPress does not have write access to '%s', please ensure this directory has write access.", 'wptc'), $get_default_backup_dir);
				WPTC_Factory::get('logger')->log($msg, 'backups', wptc_get_cookie('backupID'));
				return false;
		}

		//clearing the db file for the first time by simple logic to clear all the contents of the file if it already exists;
		$db_file = $this->get_file();
		wptc_log($db_file,'-----------$db_file----------------');
		$fp = fopen($db_file, 'w');
		fwrite($fp, '');
		fclose($fp);

		$blog_time = strtotime(current_time('mysql'));

		$this->write_to_temp("-- WP Time Capsule SQL Dump\n");
		$this->write_to_temp("-- Version " . WPTC_VERSION . "\n");
		$this->write_to_temp("-- https://wptimecapsule.com\n");
		$this->write_to_temp("-- Generation Time: " . date("F j, Y", $blog_time) . " at " . date("H:i", $blog_time) . "\n\n");
		$this->write_to_temp("
/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;\n\n");
		$this->write_to_temp("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "`;\n");
		$this->write_to_temp("USE `" . DB_NAME . "`;\n\n");

		$this->persist();

		$this->processed->update_iterator('header', -1);
	}

	public function execute() {

		if (apply_filters('is_realtime_partial_db_backup_wptc', '')) {
			$this->processed->update_iterator('header', -1);
		}

		if (!$this->processed->is_complete('header')) {
			$this->write_db_dump_header();
		}

		$backup_id = wptc_get_cookie('backupID');

		$table_meta = $this->processed_files->get_unfinished_table();

		while ( $table_meta ) {

			if ($table_meta->offset > 1) {
				WPTC_Factory::get('logger')->log(sprintf(__("Resuming table '%s' at row %s.", 'wptc'), $table_meta->name, $table_meta->offset), 'backups', $backup_id);
			}

			$table_skip_status = $this->exclude_class_obj->is_excluded_table($table_meta->name);
			$this->backup_database_table($table_meta->name, $table_meta->offset, $table_skip_status);

			WPTC_Factory::get('logger')->log(sprintf(__("Processed table %s.", 'wptc'), $table_meta->name), 'backups', $backup_id);

			$table_meta = $this->processed_files->get_unfinished_table();
		}

		$this->write_to_temp("
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;\n\n");
		$blog_time = strtotime(current_time('mysql'));
		$this->write_to_temp("-- Dump completed on ". date("F j, Y", $blog_time) . " at " . date("H:i", $blog_time) );
		$this->persist();
	}

	public function collect_tables_for_backup(){

		if($this->config->get_option('collected_tables_for_backups')){

			wptc_log('', "--------skipping collect_tables_for_backup--------");

			return ;
		}

		$tables = $this->processed_files->get_all_tables();

		if (apply_filters('is_realtime_partial_db_backup_wptc', '')) {
			$tables = apply_filters('get_realtime_full_backup_needed_tables_wptc', time());
			$tables = $this->backup_trigger_table($tables);
		}

		if (empty($tables)) {
			return true;
		}

		$offset = $this->get_colllected_tables_offset();

		$counter = 0;

		foreach ($tables as $table) {

			if ($offset > $counter++) {
				continue;
			}

			$exclude_status = $this->exclude_class_obj->is_excluded_table($table);

			// wptc_log($table, '---------------$table-----------------');
			// wptc_log($exclude_status, '---------------$exclude_status-----------------');

			if ($exclude_status === 'table_excluded') {
				continue;
			}

			// wptc_log($table , '---------------Table not completed-----------------');

			if (is_wptc_table($table)) {
				$this->prepare_table_bulk_insert($table, -1);
				continue;
			}

			$this->prepare_table_bulk_insert($table, 0);

			if($this->app_functions->is_backup_request_timeout($return = true, true)){
				$this->save_colllected_tables();
				$this->save_colllected_tables_size();
				$this->config->set_option('collected_tables_for_backups_offset', $counter);
			}
		}

		$this->config->set_option('collected_tables_for_backups_offset', $counter);
		$this->save_colllected_tables();
		$this->save_colllected_tables_size();
		$this->config->set_option('collected_tables_for_backups', true);
	}

	private function is_already_inserted($table){
		$qry = "SELECT name FROM " . $this->database->base_prefix . "wptc_processed_iterator WHERE name = '" . $table . "'";
		$is_already_inserted = $this->database->get_var($qry);

		// wptc_log($is_already_inserted ,'-----------$is_already_inserted----------------');
		
		return $is_already_inserted;
	}

	private function prepare_table_bulk_insert($table, $offset){

		if ($this->is_already_inserted($table)) {
			return ;
		}

		$this->bulk_table_insert .= empty($this->bulk_table_insert) ? "(" : ",(" ;
		$this->bulk_table_insert .= $this->database->prepare("NULL, %s, %d)", $table, $offset);

		$this->total_tables_size += $this->processed_files->get_table_size($table, $return = false);
	}

	private function get_colllected_tables_offset(){
		$offset = $this->config->get_option('collected_tables_for_backups_offset');
		return empty($offset) ? 0 : $offset;
	}

	private function save_colllected_tables_size(){
		$current_size = $this->config->get_option('collected_tables_for_backups_size');
		$current_size = empty($current_size) ? 0 : $current_size;
		$current_size += $this->total_tables_size;

		$this->config->set_option('collected_tables_for_backups_size', $current_size);
	}

	private function save_colllected_tables(){

		$sql = "insert into " . $this->database->base_prefix . "wptc_processed_iterator (id, name, offset) values $this->bulk_table_insert";
		wptc_log($sql,'-----------$sql save_colllected_tables----------------');
		$result = $this->database->query($sql);
		wptc_log($result,'-----------$result save_colllected_tables----------------');
	}

	public function backup_database_table($table, $offset, $table_skip_status) {

		wptc_manual_debug('', 'start_backup_' . $table);

		$db_error = __('Error while accessing database.', 'wptc');

		if ($offset == 0) {
			$this->write_to_temp("\n--\n-- Table structure for table `$table`\n--\n\n");

			$table_creation_query = '';
			$table_creation_query .= "DROP TABLE IF EXISTS `$table`;";
			$table_creation_query .= "
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;\n";

			$table_create = $this->database->get_row("SHOW CREATE TABLE `$table`", ARRAY_N);
			if ($table_create === false) {
				throw new Exception($db_error . ' (ERROR_3)');
			}

			$table_creation_query .= $table_create[1].";";
			$table_creation_query .= "\n/*!40101 SET character_set_client = @saved_cs_client */;\n\n";

			if ($table_skip_status !== 'content_excluded') {
				$table_creation_query .= "--\n-- Dumping data for table `$table`\n--\n";
				$table_creation_query .= "\nLOCK TABLES `$table` WRITE;\n";
				$table_creation_query .= "/*!40000 ALTER TABLE `$table` DISABLE KEYS */;";

			}

			$this->write_to_temp($table_creation_query . "\n");
		}

		if ($table_skip_status === 'content_excluded') {
			$this->processed->update_iterator($table, -1); //Done
			return true;
		}

		$row_count = $offset;
		$table_count = $this->database->get_var("SELECT COUNT(*) FROM $table");
		$columns = $this->database->get_results("SHOW COLUMNS IN `$table`", OBJECT_K);

		if ($table_count != 0) {
			for ($i = $offset; $i < $table_count; $i = $i + $this->select_query_limit) {

				wptc_manual_debug('', 'during_db_backup', 1000);

				$table_data = $this->database->get_results("SELECT * FROM $table LIMIT " . $this->select_query_limit . " OFFSET $i", ARRAY_A);
				if ($table_data === false || !is_array($table_data[0])) {
					throw new Exception($db_error . ' (ERROR_4)');
				}

				$out = '';
				foreach ($table_data as $key => $row) {
					$data_out = $this->create_row_insert_statement($table, $row, $columns);
					$out .= $data_out;
					$row_count++;
				}

				$this->write_to_temp($out);
				$this->persist();

				wptc_log($table . ' - ' . $row_count, '---------Table backing up-----------------------');

				if($this->app_functions->is_backup_request_timeout($return = true, true)){
					$this->processed->update_iterator($table, $row_count);
					send_response_wptc('Backing up table' . $table . ' (Offset : ' .$row_count . ')' );
				}

				if ($row_count >= $table_count) {
					$this->processed->update_iterator($table, -1); //Done
				} else {
					$this->processed->update_iterator($table, $row_count);
				}
			}
		}
		$this->processed->update_iterator($table, -1); //Done
		$this->write_to_temp("/*!40000 ALTER TABLE `$table` ENABLE KEYS */;\n");
		$this->write_to_temp("UNLOCK TABLES;\n");
		$this->persist();
		return true;
	}

	private function backup_trigger_table(&$tables){
		wptc_log(func_get_args(), "--------" . __FUNCTION__ . "--------");
		$table = WPTC_Base_Factory::get('Trigger_Init')->get_trigger_query_tablename();
		wptc_log($table,'-----------$table----------------');

		//unset if already backedup
		if ($this->config->get_option('trigger_table_backedup')) {
			if (($key = array_search($table, $tables)) !== false) {
				unset($tables[$key]);
			}
			wptc_log(array(),'-----------11----------------');
			return $tables;
		}

		if (!in_array($table, $tables)) {
			wptc_log(array(),'-----------22----------------');
			return $tables;
		}

		$this->backup_specific_column_of_table($table, 'query');

		if (($key = array_search($table, $tables)) !== false) {
			unset($tables[$key]);
		}

		WPTC_Base_Factory::get('Trigger_Init')->truncate_table();

		//this flag will be cleared on backup completion
		$this->config->set_option('trigger_table_backedup', true);

		return $tables;
	}

	public function backup_specific_column_of_table($table, $column){
		wptc_log(func_get_args(), "--------" . __FUNCTION__ . "--------");

		if ($this->processed->is_complete($table)) {
			wptc_log(array(),'-----------table alreay completed----------------');
			// return true;
		}

		$table_backup_stat = $this->processed->get_table($table);
		$offset = empty($table_backup_stat->offset) ? 0 : $table_backup_stat->offset ;
		$row_count = $offset;

		wptc_log($offset,'-----------$offset----------------');

		$table_count = $this->database->get_var("SELECT COUNT(*) FROM $table");

		wptc_log($table_count,'-----------$table_count----------------');

		if ($offset == 0 && $table_count != 0) {
			wptc_log(array(),'-----------First request----------------');
			$this->write_to_temp("\n\n -- Specific table " . $table . " And "  . $column . " \n\n");
		}

		if ($table_count == 0) {
			$this->processed->update_iterator($table, -1); //Done
			return true;
		}

		for ($i = $offset; $i < $table_count; $i = $i + $this->select_query_limit) {

			$table_data = $this->database->get_results("SELECT $column FROM $table LIMIT " . $this->select_query_limit . " OFFSET $i");

			if ($table_data === false || empty($table_data)) {
				throw new Exception($db_error . ' (ERROR_4)');
			}

			$out = '';
			foreach ($table_data as $key => $row) {
				$save_sql = substr(rtrim($row->$column), -1) == ';' ? $row->$column : $row->$column . ' ;';
				$out .= strtr( $save_sql, array(  "\n" => "\\n",  "\r" => "\\r"  ) ) . "\n";
				$row_count++;
			}

			$this->write_to_temp($out);
			$this->persist();

			if($this->app_functions->is_backup_request_timeout($return = true, true)){
				send_response_wptc('Backing up table' . $table . ' (Offset : ' .$row_count . ')' );
			}

			if ($row_count >= $table_count) {
				$this->processed->update_iterator($table, -1); //Done
			} else {
				$this->processed->update_iterator($table, $row_count);
			}
		}


		return true;
	}

	protected function create_row_insert_statement( $table, array $row, array $columns = array()) {
		$values = $this->create_row_insert_values($row, $columns);
		$joined = join(', ', $values);
		$sql    = "INSERT INTO `$table` VALUES($joined);\n";
		return $sql;
	}

	protected function create_row_insert_values($row, $columns) {
		$values = array();

		foreach ($row as $columnName => $value) {
			$type = $columns[$columnName]->Type;
			// If it should not be enclosed
			if ($value === null) {
				$values[] = 'null';
			} elseif (strpos($type, 'int') !== false
				|| strpos($type, 'float') !== false
				|| strpos($type, 'double') !== false
				|| strpos($type, 'decimal') !== false
				|| strpos($type, 'bool') !== false
			) {
				$values[] = $value;
			} else {
				$values[] = $this->quote_and_esc_sql($value);
			}
		}

		return $values;
	}

	/*
		there is a behavioural change in esc_sql() after WP-v4.8.3
		https://make.wordpress.org/core/2017/10/31/changed-behaviour-of-esc_sql-in-wordpress-4-8-3/
	*/
	private function quote_and_esc_sql($value){
		if ( $this->is_wp_version_greater_than_4_8_3() || method_exists($this->database, 'remove_placeholder_escape') ) {
			return  "'" . $this->database->remove_placeholder_escape( esc_sql( $value ) ) . "'";
		}

		return  "'" . esc_sql( $value ) . "'";
	}

	public function is_wp_version_greater_than_4_8_3(){
		return version_compare($this->app_functions->get_wp_core_version(), '4.8.3', '>=');
	}

	public function shell_db_dump(){

		wptc_log('', "----trying----shell_db_dump----backup----");

		if(!$this->is_shell_exec_available()){
			return 'failed';
		}

		$status = $this->config->get_option('shell_db_dump_status');

		if ($status === 'failed' || $status === 'error') {
			return 'failed';
		}

		if ($status === 'completed') {
			return 'completed';
		}

		if ($status === 'running') {
			return $this->check_is_shell_db_dump_running();
		}

		wptc_set_time_limit(0);
		$this->config->set_option('shell_db_dump_status', 'running');
		return $this->backup_db_dump();
	}

	private function check_is_shell_db_dump_running(){
		$file = $this->get_file();

		if ( !file_exists($file) || !is_file($file) ) {
			$this->config->set_option('shell_db_dump_status', 'failed');
			return 'failed';
		}

		$filesize = @filesize($file);

		if ($filesize === false) {
			$this->config->set_option('shell_db_dump_status', 'failed');
			return 'failed';
		}

		wptc_log($filesize, '---------------$filesize-----------------');
		wptc_log($this->config->get_option('shell_db_dump_prev_size'), '---------------$prev-----------------');

		if ($this->config->get_option('shell_db_dump_prev_size') === false || $this->config->get_option('shell_db_dump_prev_size') === null) {
			$this->config->set_option('shell_db_dump_prev_size', $filesize );
			return 'running';
		} else if($this->config->get_option('shell_db_dump_prev_size') < $filesize){
			$this->config->set_option('shell_db_dump_prev_size', $filesize );
			return 'running';
		} else {
			return 'failed';
		}
		$this->config->set_option('shell_db_dump_status');
	}

	private function backup_db_dump() {
		if (!apply_filters('is_realtime_partial_db_backup_wptc', '')) {
			$this->mysqldump_structure_only_tables();
		}

		$this->mysqldump_full_tables();

		$file = $this->get_file();

		$filesize = wptc_get_file_size($file);

		if ( $filesize === false || $filesize == 0 || !is_file($file)) {

			$this->config->set_option('shell_db_dump_status', 'failed');

			if (file_exists($file)) {
				@unlink($file);
			}

			return 'failed';
		}

		$this->config->set_option('shell_db_dump_status', 'completed');
		return 'do_not_continue';
	}

	private function mysqldump_structure_only_tables(){
		$tables = $this->processed_files->get_all_included_tables($structure_only = true);

		if (empty($tables)) {
			return true;
		}

		$tables =  implode("\" \"",$tables);

		$this->exec_mysqldump($tables, $structure_only = '--no-data');
	}

	private function mysqldump_full_tables(){
		wptc_log(func_get_args(), "--------" . __FUNCTION__ . "--------");
		if (apply_filters('is_realtime_partial_db_backup_wptc', '')) {
			wptc_log(array(),'-----------1----------------');
			$tables = apply_filters('get_realtime_full_backup_needed_tables_wptc', time());
			$tables = $this->backup_trigger_table($tables);
		} else {
			$tables = $this->processed_files->get_all_included_tables();
		}

		wptc_log($tables,'-----------$tables----------------');

		if (empty($tables)) {
			return true;
		}

		$tables =  implode("\" \"",$tables);

		$this->exec_mysqldump($tables);
	}

	private function exec_mysqldump($tables, $structure_only = ''){
		$file 	 = $this->get_file();
		$paths   = $this->check_mysql_paths();
		$brace   = (substr(PHP_OS, 0, 3) == 'WIN') ? '"' : '';

		reset_last_backup_request_wptc();

		$comments = '';
		if (file_exists($file) && filesize($file) > 0) {
			$comments = '--skip-comments'; //assume already comments are dumped
		}

		$command = $brace . $paths['mysqldump'] . $brace . ' --force ' . $comments . ' ' . $structure_only . ' --host="' . DB_HOST . '" --user="' . DB_USER . '" --password="' . DB_PASSWORD . '" --add-drop-table --skip-lock-tables --extended-insert=FALSE "' . DB_NAME . '" "' . $tables . '" --triggers=false >> ' . $brace . $file . $brace;

		wptc_log($command, '---------------$command-----------------');
		return $this->wptc_exec($command);
	}

	### Function: Auto Detect MYSQL and MYSQL Dump Paths
	private function check_mysql_paths() {
		global $wpdb;
		$paths = array(
			'mysql' => '',
			'mysqldump' => ''
		);
		if (substr(PHP_OS, 0, 3) == 'WIN') {
			$mysql_install = $wpdb->get_row("SHOW VARIABLES LIKE 'basedir'");
			if ($mysql_install) {
				$install_path       = str_replace('\\', '/', $mysql_install->Value);
				$paths['mysql']     = $install_path . 'bin/mysql.exe';
				$paths['mysqldump'] = $install_path . 'bin/mysqldump.exe';
			} else {
				$paths['mysql']     = 'mysql.exe';
				$paths['mysqldump'] = 'mysqldump.exe';
			}
		} else {
			$paths['mysql'] = $this->wptc_exec('which mysql', true);
			if (empty($paths['mysql']))
				$paths['mysql'] = 'mysql'; // try anyway

			$paths['mysqldump'] = $this->wptc_exec('which mysqldump', true);
			if (empty($paths['mysqldump']))
				$paths['mysqldump'] = 'mysqldump'; // try anyway

		}
		return $paths;
	}

	private function wptc_exec($command, $string = false, $rawreturn = false) {
		if ($command == '')
			return false;

		if (function_exists('exec')) {
			$log = @exec($command, $output, $return);
			wptc_log($log, '---------------$log-----------------');
			wptc_log($output, '---------------$output-----------------');
			if ($string)
				return $log;
			if ($rawreturn)
				return $return;

			return $return ? false : true;
		} elseif (function_exists('system')) {
			$log = @system($command, $return);
			wptc_log($log, '---------------$log-----------------');

			if ($string)
				return $log;

			if ($rawreturn)
				return $return;

			return $return ? false : true;
		} else if (function_exists('passthru')) {
			$log = passthru($command, $return);
			wptc_log($log, '---------------$log-----------------');

			if ($rawreturn)
				return $return;

			return $return ? false : true;
		}

		if ($rawreturn)
			return -1;

		return false;
	}

	public function is_shell_exec_available() {
		if (in_array(strtolower(ini_get('safe_mode')), array('on', '1'), true) || (!function_exists('exec'))) {
			return false;
		}
		$disabled_functions = explode(',', ini_get('disable_functions'));
		$exec_enabled = !in_array('exec', $disabled_functions);
		return ($exec_enabled) ? true : false;
	}

	private function modify_table_description($table_data){
		$temp_table = array();
		foreach ($table_data as $key => $value) {
			$temp = $table_data[$key];
			$temp_table[$value['Field']] = $table_data[$key];
		}
		return $temp_table;
	}

	private function write_to_temp($out) {
		if (!$this->temp) {
			$this->temp = fopen('php://memory', 'rw');
		}

		if (fwrite($this->temp, $out) === false) {
			throw new Exception(__('Sql Backup : Error writing to php://memory.', 'wptc'));
		}
	}

	private function persist() {

		$file = $this->get_file();

		// wptc_log($file,'-----------$file----------------');
		
		if (file_exists($file)) {
			$fh = fopen($file, 'a');
		} else {
			$fh = fopen($file, 'w');
		}

		if (!$fh) {
			throw new Exception(__('Sql Backup : Error creating sql dump file.', 'wptc'));
		}

		fseek($this->temp, 0);

		fwrite($fh, stream_get_contents($this->temp));

		if (!fclose($fh)) {
			throw new Exception(__(' Sql Backup : Error closing sql dump file.', 'wptc'));
		}

		if (!fclose($this->temp)) {
			throw new Exception(__(' Sql Backup : Error closing php://memory.', 'wptc'));
		}

		$this->temp = null;
	}


	public function compress(){
		wptc_log(func_get_args(), "--------" . __FUNCTION__ . "--------");
		if(!wptc_function_exist('gzwrite') || !wptc_function_exist('gzopen') || !wptc_function_exist('gzclose') ){
			wptc_log(array(), '--------ZGIP not available--------');
			$this->config->set_option('sql_gz_compression', true);
			return ;
		}

		$offset = $this->config->get_option('sql_gz_compression_offset');
		$offset = empty($offset) ? 0 : $offset;

		wptc_log($offset, '--------$offset--------');

		$file = $this->get_file();

		wptc_log($file, '--------$file--------');

		if (!file_exists($file)) {
			$this->config->set_option('sql_gz_compression', true);
			return ;
		}

		$this->gz_compress_file($file, $offset);
	}

	private function gz_compress_file($source, $offset, $level = 9){

		if (filesize($source) < 5 ) {
			wptc_log(array(),'-----------FILE contains nothing so delete it and skip compression----------------');
			return @unlink($source);
		}

		wptc_log(func_get_args(), "--------" . __FUNCTION__ . "--------");
		wptc_log(filesize($source),'-----------filesize($source)----------------');

		$dest = $source . '.gz';
		$mode = 'ab' . $level;

		$break = false;

		$fp_out = gzopen($dest, $mode);

		if (empty($fp_out)) {
			return false;
		}

		$fp_in = fopen($source,'rb');

		if (empty($fp_in)) {
			return false;
		}

		fseek($fp_in, $offset);

		while (!feof($fp_in)){

			gzwrite($fp_out, fread($fp_in, 1024 * 1024 * 5)); //read 5MB chunk

			wptc_manual_debug('', 'during_compress_db', 10);

			if($this->app_functions->is_backup_request_timeout($return = true)){
				$break = true;
				$offset = ftell($fp_in);
				break;
			}
		}

		fclose($fp_in);
		gzclose($fp_out);

		if ($break) {
			$this->config->set_option('sql_gz_compression_offset', $offset);
			send_response_wptc('Compressing database file, Offset : ' .$offset );
		}

		wptc_log(array(), '--------Done--------');
		$this->config->set_option('sql_gz_compression', true);
		@unlink($source);
		return ;
	}

	public function encrypt($fullpath = false) {
		wptc_log(func_get_args(), "--------" . __FUNCTION__ . "--------");

		$is_enabled = $this->config->get_database_encryption_settings('status');

		wptc_log($is_enabled,'-----------$is_enabled----------------');

		if ($is_enabled !== true) {
			return ;
		}

		$key = $this->config->get_database_encryption_settings('key');

		wptc_log($key,'-----------$key----------------');

		if (empty($fullpath)) {
			$fullpath = $this->get_file();
		}

		wptc_log($fullpath,'-----------$fullpath----------------');

		if (!function_exists('mcrypt_encrypt') && !extension_loaded('openssl')) {
			WPTC_Factory::get('logger')->log('Your web-server does not have the PHP/mcrypt / PHP/OpenSSL module installed. Without it, encryption will be a lot slower.', 'backups', wptc_get_cookie('backupID'));
		}

		// include Rijndael library from phpseclib
		$ensure_phpseclib = wptc_ensure_phpseclib('Crypt_Rijndael', 'Crypt/Rijndael');

		if (is_wp_error($ensure_phpseclib)) {
			WPTC_Factory::get('logger')->log("Failed to load phpseclib classes (" . $ensure_phpseclib->get_error_code() . "): " . $ensure_phpseclib->get_error_message(), 'backups', wptc_get_cookie('backupID'));
			return false;
		}

		// open file to read
		if (false === ($file_handle = fopen($fullpath, 'rb'))) {
			WPTC_Factory::get('logger')->log("Failed to open file for read access: $fullpath", 'backups', wptc_get_cookie('backupID'));
			return false;
		}

		// encrypted path name. The trailing .tmp ensures that it will be cleaned up by the temporary file reaper eventually, if needs be.
		$encrypted_path = dirname($fullpath) . '/encrypt_' . basename($fullpath) . '.tmp';

		$data_encrypted = 0;

		$time_last_logged = time();

		$file_size = filesize($fullpath);

		// Set initial value to false so we can check it later and decide what to do
		$resumption = false;

		// setup encryption
		$rijndael = new Crypt_Rijndael();
		$rijndael->setKey($key);
		$rijndael->disablePadding();
		$rijndael->enableContinuousBuffer();

		// First we need to get the block length, this method returns the length in bits we need to change this back to bytes in order to use it with the file operation methods.
		$block_length = $rijndael->getBlockLength() >> 3;

		// Check if the path already exists as this could be a resumption
		if (file_exists($encrypted_path)) {
			WPTC_Factory::get('logger')->log("Temporary encryption file found, will try to resume the encryption", 'backups', wptc_get_cookie('backupID'));

			// The temp file exists so set resumption to true
			$resumption = true;

			// Get the file size as this is needed to help resume the encryption
			$data_encrypted = filesize($encrypted_path);
			// Get the true file size e.g without padding used for various resumption paths
			$true_data_encrypted = $data_encrypted - ($data_encrypted % WPTC_CRYPT_BUFFER_SIZE);

			if ($data_encrypted >= $block_length) {

				// Open existing file from the path
				if (false === ($encrypted_handle = fopen($encrypted_path, 'rb+'))) {
					WPTC_Factory::get('logger')->log("Failed to open file for write access on resumption: $encrypted_path", 'backups', wptc_get_cookie('backupID'));
					$resumption = false;
				}

				// First check if our buffer size needs padding if it does increase buffer size to length that doesn't need padding
				if (0 != WPTC_CRYPT_BUFFER_SIZE % 16) {
					$pad = 16 - (WPTC_CRYPT_BUFFER_SIZE % 16);
					$true_buffer_size = WPTC_CRYPT_BUFFER_SIZE + $pad;
				} else {
					$true_buffer_size = WPTC_CRYPT_BUFFER_SIZE;
				}

				// Now check if using modulo on data encrypted and buffer size returns 0 if it doesn't then the last block was a partial write and we need to discard that and get the last useable IV by adding this value to the block length
				$partial_data_size = $data_encrypted % $true_buffer_size;

				// We need to reconstruct the IV from the previous run in order for encryption to resume
				if (-1 === (fseek($encrypted_handle, $data_encrypted - ($block_length + $partial_data_size)))) {
					WPTC_Factory::get('logger')->log("Failed to move file pointer to correct position to get IV: $encrypted_path", 'backups', wptc_get_cookie('backupID'));
					$resumption = false;
				}

				// Read previous block length from file
				if (false === ($iv = fread($encrypted_handle, $block_length))) {
					WPTC_Factory::get('logger')->log("Failed to read from file to get IV: $encrypted_path", 'backups', wptc_get_cookie('backupID'));
					$resumption = false;
				}

				$rijndael->setIV($iv);

				// Now we need to set the file pointer for the original file to the correct position and take into account the padding added, this padding needs to be removed to get the true amount of bytes read from the original file
				if (-1 === (fseek($file_handle, $true_data_encrypted))) {
					WPTC_Factory::get('logger')->log("Failed to move file pointer to correct position to resume encryption: $fullpath", 'backups', wptc_get_cookie('backupID'));
					$resumption = false;
				}

			} else {
				// If we enter here then the temp file exists but it is either empty or has one incomplete block we may as well start again
				$resumption = false;
			}
		}
		if (!$resumption) {
			// WPTC_Factory::get('logger')->log("Could not resume the encryption will now try to start again", 'backups', wptc_get_cookie('backupID'));

			// remove the existing encrypted file as it's no good to us now
			if (file_exists($encrypted_path)) {
				@unlink($encrypted_path);
			}
			// reset the data encrypted so that the loop can be entered
			$data_encrypted = 0;
			// setup encryption to reset the IV
			$rijndael = new Crypt_Rijndael();
			$rijndael->setKey($key);
			$rijndael->disablePadding();
			$rijndael->enableContinuousBuffer();
			// reset the file pointer and then we should be able to start from fresh
			if (-1 === (fseek($file_handle, 0))) {
				WPTC_Factory::get('logger')->log("Failed to move file pointer to start position to restart encryption: $fullpath", 'backups', wptc_get_cookie('backupID'));
				$resumption = false;
			}
		}

		if (!$resumption) {
			// open new file from new path
			if (false === ($encrypted_handle = fopen($encrypted_path, 'wb+'))) {
				WPTC_Factory::get('logger')->log("Failed to open file for write access: $encrypted_path", 'backups', wptc_get_cookie('backupID'));
				return false;
			}
		}

		$break = false;

		// loop around the file
		while ($data_encrypted < $file_size) {

			// read buffer-sized amount from file
			if (false === ($file_part = fread($file_handle, WPTC_CRYPT_BUFFER_SIZE))) {
				WPTC_Factory::get('logger')->log("Failed to read from file: $fullpath", 'backups', wptc_get_cookie('backupID'));
				return false;
			}

			// check to ensure padding is needed before encryption
			$length = strlen($file_part);
			if (0 != $length % 16) {
				$pad = 16 - ($length % 16);
				$file_part = str_pad($file_part, $length + $pad, chr($pad));
			}

			$encrypted_data = $rijndael->encrypt($file_part);

			if (false === fwrite($encrypted_handle, $encrypted_data)) {
				WPTC_Factory::get('logger')->log("Failed to write to file: $encrypted_path", 'backups', wptc_get_cookie('backupID'));
				return false;
			}

			$data_encrypted += WPTC_CRYPT_BUFFER_SIZE;

			$time_since_last_logged = time() - $time_last_logged;
			if ($time_since_last_logged > 5) {
				$time_since_last_logged = time();
				WPTC_Factory::get('logger')->log("Encrypting file: completed $data_encrypted bytes", 'backups', wptc_get_cookie('backupID'));
			}

			if($this->app_functions->is_backup_request_timeout($return = true)){
				$break = true;
				$offset = ftell($encrypted_handle);
				break;
			}

		}

		// close the main file handle
		fclose($encrypted_handle);
		fclose($file_handle);

		if ($break) {
			send_response_wptc('Encrypting database file, Offset : ' . $offset );
		}

		// encrypted path
		$result_path = $fullpath.'.crypt';

		// need to replace original file with tmp file
		if (false === rename($encrypted_path, $result_path)) {
			WPTC_Factory::get('logger')->log("File rename failed: $encrypted_path -> $result_path", 'backups', wptc_get_cookie('backupID'));
			return false;
		}

		@unlink($fullpath);

		return $result_path;
	}

	public function is_wp_table($table) {
		//ignoring tables other than wordpress table
		$wp_prefix = $this->database->prefix;
		$wptc_strpos = stripos($table, $wp_prefix);

		if (false !== $wptc_strpos && $wptc_strpos === 0) {
			return true;
		}
		return false;
	}

	public function complete_all_tables(){
		$update_all_tables = "UPDATE " . $this->database->base_prefix . "wptc_processed_iterator SET offset = '-1' WHERE `name` NOT LIKE '%/%'";
		wptc_log($update_all_tables, '---------------$update_all_tables-----------------');
		$result = $this->database->query($update_all_tables);
		wptc_log($result, '---------------$result-----------------');
	}
}
