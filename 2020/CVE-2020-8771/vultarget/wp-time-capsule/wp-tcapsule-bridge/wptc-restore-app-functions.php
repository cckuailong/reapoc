<?php

class WPTC_Restore_App_Functions {

	private $config;
	private $fs;
	private $wpdb;
	private $is_restore_to_staging;
	private	$live_db_prefix;
	private	$replace_links_obj;
	private $old_url;
	private $new_url;
	private $old_dir;
	private $new_dir;
	private $restore_id;
	private $is_migration;
	private $is_meta_restore;
	private $wptc_db_prefix;
	private $site_db_prefix;
	private $is_multisite;
	private $multisite_config;

	const SECRET_HEAD = '<wptc_head>';
	const SECRET_TAIL = '</wptc_head>';

	public function __construct(){
	}

	public function set_default_timezone(){
		if (function_exists('date_default_timezone_set')) {
			date_default_timezone_set('UTC');
		}
	}

	public function verify_request(){
		if (!empty( $_POST['wptc_request'] ) || !empty( $_POST['data']['wptc_request'] ) ) {
			return true;
		}

		$this->die_with_msg(array('error' => 'Not wptc request'));
	}

	public function define( $name, $value ) {
		if ( ! defined( $name ) ) {
			define( $name, $value );
		}
	}

	public function enable_php_errors(){
		error_reporting(E_ERROR | E_PARSE);
		ini_set('display_errors', 'On');
	}

	public function init_other_functions(){
		wptc_set_time_limit(0); //to stay in safe side (30 + 5) secs
	}

	public function start_request_time($type = false){

		if ($type === 'iterator') {
			return ;
		}

		global $start_time_tc;
		$start_time_tc = time();
	}

	public function set_fs(){
		global $wp_filesystem;
		$this->fs = $wp_filesystem;

		return $this->fs;
	}

	public function define_constants($enable_bridge_alone = true){
		//used in wptc-constants.php
		$this->define('WPTC_BRIDGE', true);

		if ($enable_bridge_alone) {
			return;
		}

		$this->define('FS_CHMOD_FILE', 0644);

		$this->define('FS_CHMOD_DIR', 0755);
	}

	public function init_db_connection(){
		//initialize wpdb since we are using it independently
		global $wpdb;
		$wpdb = new wpdb(DB_USER, DB_PASSWORD, DB_NAME, DB_HOST);

		//setting the prefix from post value;
		$wpdb->prefix = $wpdb->base_prefix = DB_PREFIX_WPTC;

		$this->wpdb = $wpdb;

		return $wpdb;
	}

	public function init_other_objects(){
		if (!empty($this->config)) {
			return ;
		}

		$this->config 	  = WPTC_Factory::get('config');
		$this->restore_id = $this->config->get_option('restore_action_id');
	}

	public function init_file_system(){
		$credentials = request_filesystem_credentials("", "", false, false, null);
		if (false === $credentials) {
			$this->die_with_msg(array('error' => 'Filesystem error: Could not get filesystem credentials.'));
		}

		if (!WP_Filesystem($credentials)) {
			$this->die_with_msg(array('error' => 'Filesystem error: Could not initiate filesystem.'));
		}

		return $this->set_fs();
	}

	public function decode_request(){
		return $_POST;
	}

	public function die_with_msg($msg, $option = false){
		if (!$option) {
			$json_encoded_msg = json_encode($msg);
		} else if($option === 'unescape_slashes'){
			$json_encoded_msg = json_encode($msg, JSON_UNESCAPED_SLASHES);
		}

		$msg_with_secret = self::SECRET_HEAD . $json_encoded_msg . self::SECRET_TAIL;
		die($msg_with_secret);
	}

	public function init_log_files(){
		$tcapsule_path = wptc_get_tmp_dir();
		$tcapsule_restore_path = $tcapsule_path . '/wptc_restore_logs/';
		$this->create_folder_by_path($tcapsule_restore_path);
		$this->create_golden_file($tcapsule_restore_path);
		$this->create_log_file($tcapsule_restore_path, $type = 'queries');
		$this->create_log_file($tcapsule_restore_path, $type = 'files');
	}

	public function create_folder_by_path($path){
		$base = new Utils_Base();
		$base->createRecursiveFileSystemFolder($path);
	}

	public function create_golden_file($dir_path){
		$this->fs->put_contents($dir_path. 'index.php', '<?php //silence is golden'); // to create a file
	}

	public function create_log_file($dir_path, $type){
		if (empty($dir_path) || empty($type)) {
			return false; //we cannot handle if anyone is false
		}

		if ($type === 'queries') {
			$is_restore_failed_queries_file_created = $this->config->get_option('is_restore_failed_queries_file_created');
			$file_path = $restore_failed_queries_file_path = $this->config->get_option('restore_failed_queries_file_path');

			if(!$is_restore_failed_queries_file_created || !$restore_failed_queries_file_path || !$this->fs->exists($restore_failed_queries_file_path)) {
				$file_name = WPTC_Factory::secret('failed_queries');
				$file_path = $dir_path . $file_name . '.sql';
				$this->fs->put_contents($file_path, ''); // to create a file
				$this->config->set_option('is_restore_failed_queries_file_created', true);
				$this->config->set_option('restore_failed_queries_file_path', $file_path);
			}

		} else if($type === 'files'){
			$is_restore_failed_downloads_file_created = $this->config->get_option('is_restore_failed_downloads_file_created');
			$file_path = $restore_failed_downloads_file_path = $this->config->get_option('restore_failed_downloads_file_path');

			if(!$is_restore_failed_downloads_file_created || !$restore_failed_downloads_file_path || ($restore_failed_downloads_file_path && !$this->fs->exists($restore_failed_downloads_file_path)) ) {
				$file_name = WPTC_Factory::secret('failed_downloads');
				$file_path = $dir_path . $file_name . '.txt';
				$this->fs->put_contents($file_path, ''); // to create a file
				$this->config->set_option('is_restore_failed_downloads_file_created', true);
				$this->config->set_option('restore_failed_downloads_file_path', $file_path);
			}
		}

		return $file_path;
	}

	public function log_data($type, $data){
		if (empty($type) || empty($data)) {
			return false;
		}

		if ($type === 'files') {
			$file_path = $this->config->get_option('restore_failed_downloads_file_path');
		} else if($type === 'queries') {
			$file_path = $this->config->get_option('restore_failed_queries_file_path');
		}

		if (empty($file_path) || !file_exists($file_path)) {
			wptc_log($file_path, '--------$file_path not exist so cannot log--------');
			return false;
		}

		if ($type === 'files') {
			foreach ($data as $key => $value) {
				file_put_contents($file_path, $key . " : " . $value . "\n", FILE_APPEND);
			}
			file_put_contents($file_path, "\n", FILE_APPEND);
		} else if($type === 'queries') {
			file_put_contents($file_path, $data . "\n", FILE_APPEND);
		}

	}

	public function get_failure_data(){
		$restore_failed_queries_file_path =  $this->config->get_option('restore_failed_queries_file_path');
		$restore_failed_downloads_file_path = $this->config->get_option('restore_failed_downloads_file_path');

		$result = array();

		if (!empty($restore_failed_downloads_file_path) && file_exists($restore_failed_downloads_file_path) && filesize($restore_failed_downloads_file_path) > 0) {
			$result['failed_files'] = str_replace(ABSPATH, $this->config->get_option('site_url_wptc'). '/', $restore_failed_downloads_file_path);
		}

		if (!empty($restore_failed_queries_file_path) && file_exists($restore_failed_queries_file_path) && filesize($restore_failed_queries_file_path) > 0) {
			$result['failed_queries'] = str_replace(ABSPATH, $this->config->get_option('site_url_wptc'). '/', $restore_failed_queries_file_path);
		}

		return $result;
	}

	public function check_and_record_not_safe_for_write($this_file) {

		$this_file = $this->config->wp_filesystem_safe_abspath_replace($this_file);

		if($this->fs->is_dir($this_file)){
			return true;
		}

		$this_file = rtrim($this_file, '/');

		if (!$this->fs->exists($this_file) ){
			return true;
		}

		if($this->fs->is_writable($this_file)){
			return true;
		}

		$chmod_result = $this->fs->chmod($this_file, 0644);
		if (!$chmod_result || !$this->fs->is_writable($this_file)) {
			$this->config->save_encoded_not_safe_for_write_files($this_file);
			return false;
		}

		return true;
	}

	public function maybe_call_again_tc($return = false) {
		global $start_time_tc;

		$this->define('WPTC_TIMEOUT', 21);

		if ((time() - $start_time_tc) >= WPTC_TIMEOUT) {

			if ($return) return true;

			$this->die_with_msg("wptcs_callagain_wptce");

		}

		return false;
	}

	public function is_file_hash_same($file_path, $prev_file_hash ,$prev_file_size, $prev_file_mtime = 0) {
		$file_path = wptc_add_fullpath($file_path);

		$this->init_other_objects();

		$file_path = $this->config->wp_filesystem_safe_abspath_replace($file_path);
		$file_path = rtrim($file_path, '/');

		if (!file_exists($file_path)) {
			return false;
		}

		if(!wptc_is_hash_required($file_path) || empty($prev_file_hash)){
			return $this->is_same_size_and_same_mtime($file_path, $prev_file_size, $prev_file_mtime);
		}

		$new_file_hash = wptc_get_hash($file_path);
		if ($prev_file_hash != $new_file_hash) {
			return false;
		}

		return true;

	}

	private function is_same_size_and_same_mtime($file_path, $prev_file_size, $prev_file_mtime){
		$new_file_size = @filesize($file_path);

		$this_file_m_time = @filemtime($file_path);

		if (($new_file_size == $prev_file_size) && ($this_file_m_time == $prev_file_mtime)) {
			return true;
		}

		return false;
	}

	public function enable_maintenance_mode() {
		$path = $this->config->wp_filesystem_safe_abspath_replace(ABSPATH);
		$this->config->enable_maintenance_mode($path);
	}

	public function disable_maintenance_mode() {
		$path = $this->config->wp_filesystem_safe_abspath_replace(ABSPATH);
		$this->config->disable_maintenance_mode($path);
	}

	public function remove_gz_ext_from_file($file){
		if (strstr($file, '.gz.crypt') !== false){
			return str_replace('.gz.crypt', '', $file);
		}

		if (strstr($file, '.gz') !== false){
			return str_replace('.gz', '', $file);
		}

		return $file;
	}

	public function is_gzip_available(){
		if(!wptc_function_exist('gzwrite') || !wptc_function_exist('gzopen') || !wptc_function_exist('gzclose') ){
			wptc_log(array(), '--------ZGIP not available--------');
			return false;
		}

		return true;
	}

	public function gz_uncompress_file($source, $offset = 0){

		$dest =  str_replace('.gz', '', $source);

		$fp_in = gzopen($source, 'rb');

		if (empty($fp_in)) {

			wptc_log(error_get_last(),'-----------error_get_last()----------------');
			
			$this->die_with_msg(array('error' => "Cannot open gzfile to uncompress sql. Give 644 permission to the file $source and resume again."));
		}

		$fp_out = ($offset === 0) ? fopen($dest, 'wb') : fopen($dest, 'ab');

		if (empty($fp_out)) {
			fclose($fp_out);
			$this->die_with_msg(array('error' => 'Cannot open temp file to uncompress sql'));
		}

		gzseek($fp_in, $offset);

		$emptimes = 0;

		while (!gzeof($fp_in)){

			$chunk_data = gzread($fp_in, 1024 * 1024 * 5); //read 5MB per chunk

			wptc_log(strlen($chunk_data), '---------------strlen($chunk_data)-----------------');

			if (empty($chunk_data)) {

				$emptimes++;

				wptc_log(array(), "---------------Got empty gzread ($emptimes times)---------------");

				if ($emptimes > 3){
					$this->die_with_msg(array('error' => "Got empty gzread ($emptimes times). Give 644 permission to the file $source and resume again."));
				}

			} else {
				@fwrite($fp_out, $chunk_data);
			}

			wptc_manual_debug('', 'during_uncompress_db', 2);

			$current_offset = gztell($fp_in);

			wptc_log($current_offset, '---------------$current_offset-----------------');

			//Clearning to save memory
			unset($chunk_data);
		}

		fclose($fp_out);
		gzclose($fp_in);

		wptc_log(array(), '--------Un compression done--------');

		@unlink($source);

		wptc_manual_debug('', 'end_uncompress_db');

		return $dest;
	}

	public function set_wptc_sql_mode_variables() {
		wptc_log('', "--------altering foreign key mode--------");
		// $this->wpdb->query("SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0");
		$this->wpdb->query("SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=OFF;");
		wptc_log($this->wpdb->last_error,'-----------$last_error----set_wptc_sql_mode_variables------------');
		$this->wpdb->query("SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO';");

	}

	public function reset_wptc_sql_mode_variables() {
		wptc_log('', "--------resetting foreign key mode--------");
		// $this->wpdb->query("SET UNIQUE_CHECKS=@@OLD_UNIQUE_CHECKS");
		$this->wpdb->query("SET FOREIGN_KEY_CHECKS=@@OLD_FOREIGN_KEY_CHECKS");
		$this->wpdb->query("SET SQL_MODE=@@OLD_SQL_MODE");
	}

	public function import_sql_file($file_name, $prev_index, $replace_collation = false){
		wptc_log(func_get_args(), "--------------" . __FUNCTION__ . "------------------");
		wptc_log($this->is_restore_to_staging,'-----------$this->is_restore_to_staging----------------');

		if (!$replace_collation) {
			$replace_collation = $this->get_collation_replacement_status();
		}

		wptc_log($replace_collation,'-----------$replace_collation----------------');

		// $foreign_key_variable = $this->wpdb->get_results('SHOW SESSION variables LIKE "%foreign%"');
		// wptc_log($foreign_key_variable, "--------foreign_key_variable--------");

		$handle = fopen($file_name, "rb");

		if (empty($handle)) {
			return array('status' => 'error', 'msg' => 'Cannot open database file');
		}

		$prev_index = empty($prev_index) ? 0 : $prev_index;

		$current_query = '';
		$tempQuery = '';

		$this_lines_count = $loop_iteration = 0;

		$wptc_php_dump_file = false;
		
		if($this->is_restore_to_staging){
			$this->init_necessary_things_R2S();
		}

		if($this->is_migration){
			$this->init_necessary_things_migration();
		}

		// exit;

		while ( ( $line = fgets( $handle ) ) !== false ) {

			if($loop_iteration == 0 && stripos($line, '-- WP Time Capsule SQL Dump') !== false){
				$wptc_php_dump_file = true;

				wptc_log('', "--------wptc_php_dump_file--during restore------");
			}

			$loop_iteration++;

			if ($loop_iteration <= $prev_index ) {
				continue; //check index; if it is previously written ; then continue;
			}

			$this_lines_count++;

			if (substr($line, 0, 2) == '--' || $line == '' || substr($line, 0, 3) == '/*!') {
				continue; // Skip it if it's a comment
			}

			$current_query .= $line;

			// If it does not have a semicolon at the end, then it's not the end of the query
			if (substr(trim($line), -1, 1) != ';') {
				continue;
			}

			if ($this->is_multisite) {
				if($this->skip_this_query_for_multisite($current_query)){
					// wptc_log(array(),'-----------Query skipped----------------');
					$current_query = '';
					continue;
				}
			}

			// wptc_log($this->old_url, "--------old_url--------");
			// wptc_log($this->new_url, "--------new_url--------");

			if ($this->is_restore_to_staging || $this->is_meta_restore || $this->is_migration ) {
				$current_query = $this->search_and_replace_db_name($current_query);
				$current_query = $this->search_and_replace_prefix($current_query);
				$replaceQuery = $this->search_and_replace_urls_new($current_query, $this->old_url, $this->new_url, $this->live_db_prefix, $wptc_php_dump_file);
			} else {
				$replaceQuery = $this->search_and_replace_urls_new($current_query, null, null, $this->live_db_prefix, $wptc_php_dump_file);
			}

			wptc_manual_debug('', 'during_db_restore', 1000);

			if(!empty($replaceQuery['prevExec']) && !empty($tempQuery)) {

				// wptc_log(strlen($tempQuery), "--------tempQuery--length---exec---");
				// wptc_log($tempQuery, "--------tempQuerying---q-----");

				// wptc_log($tempQuery, "--------running query--------");

				// exit;

				if(stripos($tempQuery, 'insert into') === false){
					//wptc_log($tempQuery, "--------tempQuery--------");
				}

				$result = $this->wpdb->query($tempQuery);
				if($result===false) {
					wptc_log($this->wpdb->last_error,'-----------$last_error----------------');
					wptc_log($this->wpdb->last_query,'-----------$last_query----------------');

					// wptc_log($tempQuery, "--------tempQuery--------");

					$string = substr($tempQuery,0,2500).'...';
					$this->log_data('queries', $string);

					$tempQuery ='';

					if( !$replace_collation && $this->is_collation_issue($this->wpdb->last_error) ){

						wptc_log(array(),'-----------Collation issue----------------');

						$this->wpdb->query('UNLOCK TABLES;');
						fclose($handle);

						return array('status' => 'continue', 'offset' => $loop_iteration, 'replace_collation' => true);
					}
				}
				$tempQuery ='';
            }

            $tempQuery .= $replaceQuery['q'];

            if(!empty($replaceQuery['exec']) && !empty($tempQuery)) {

				// wptc_log(strlen($tempQuery), "--------tempQuery--length---exec---");
				// wptc_log($tempQuery, "--------tempQuerying---q-----");

				// wptc_log($tempQuery, "--------running query--------");

				if(stripos($tempQuery, 'insert into') === false){
					//wptc_log($tempQuery, "--------tempQuery2--------");
				}

				$result = $this->wpdb->query($tempQuery);
				if($result===false) {
					wptc_log($this->wpdb->last_error,'-----------$last_error----------------');
					wptc_log($this->wpdb->last_query,'-----------$last_query----------------');

					// wptc_log($tempQuery, "--------tempQuery--------");

					$string = substr($tempQuery,0,2500).'...';
					$this->log_data('queries', $string);

					$tempQuery ='';

					if( !$replace_collation && $this->is_collation_issue($this->wpdb->last_error) ){

						wptc_log(array(),'-----------Collation issue----------------');

						$this->wpdb->query('UNLOCK TABLES;');
						fclose($handle);

						return array('status' => 'continue', 'offset' => $loop_iteration, 'replace_collation' => true);
					}
				}
				$tempQuery ='';
            }

			$current_query = $line = '';

			//check timeout after every 10 queries executed
			if ($this_lines_count <= 10) {
				continue;
			}

			$this_lines_count = 0;

			if(!$this->maybe_call_again_tc($return = true)){
				continue;
			}

			$this->wpdb->query('UNLOCK TABLES;');
			fclose($handle);
			return array('status' => 'continue', 'offset' => $loop_iteration, 'replace_collation' => false);
		}

		$this->wpdb->query('UNLOCK TABLES;');

		return array('status' => 'completed');
	}

	public function search_and_replace_urls_new($haystack, $from = NULL, $to = NULL, $table_prefix = NULL, $wptc_php_dump_file = false) {
		// wptc_log('', "--------search_and_replace_urls_new--------");
		// wptc_log($from, "-----search_and_replace_urls_new---fromURL--------");
		// wptc_log($to, "--------toURL--------");

		// wptc_log(func_get_args(), "--------search_and_replace_urls_new--------");

		// wptc_log($table_prefix, "--------table_prefix--------");
		// wptc_log(DB_PREFIX_WPTC, "--------DB_PREFIX_WPTC--------");

		if(!empty($from)){
			$fromURL = parse_url($from);
		}

		if(!empty($to)){
			$toURL = parse_url($to);
		}

		$retArray = array();

		$ignore_create_db_queries = false;

		if(stripos($haystack, 'CREATE DATABASE IF NOT EXISTS') !== false || stripos($haystack, 'USE ') === 0){
			$ignore_create_db_queries = true;

			wptc_log($from, "-----search_and_replace_urls_new---fromURL--------");
			wptc_log($to, "--------toURL--------");
			wptc_log($table_prefix, "--------table_prefix--------");
			wptc_log(DB_PREFIX_WPTC, "--------DB_PREFIX_WPTC--------");

			wptc_log($this->live_db_prefix, '--------$this->live_db_prefix--------');
			wptc_log($this->wptc_db_prefix, '--------$this->wptc_db_prefix--------');
			wptc_log($this->site_db_prefix, '--------$this->site_db_prefix--------');

			wptc_log($this->is_migration, '--------$this->is_migration--------');
			wptc_log($this->is_meta_restore, '--------$this->is_meta_restore--------');
			wptc_log($this->is_restore_to_staging, '--------$this->is_restore_to_staging--------');

			$haystack = '';
			// if($this->is_migration || $this->is_meta_restore){
			// 	$ignore_create_db_queries = false;
			// }
		}

		$table_name_from_query = wptc_get_table_from_query($haystack);

		if( !empty($table_prefix) && !$ignore_create_db_queries ){
			if ( stripos($haystack, $table_prefix . 'user_roles') === false
				 && stripos($haystack, $table_prefix . 'usermeta') === false ) {
				if(!$this->is_meta_restore){

					if( stripos($haystack, 'CREATE TABLE') !== false ){

						$new_table_name_from_query = preg_replace("/$table_prefix/i", DB_PREFIX_WPTC, $table_name_from_query, 1);
						$haystack = str_ireplace($table_name_from_query, $new_table_name_from_query, $haystack);

					} else {
						$queryArray = explode("(", $haystack);

						$new_table_name_from_query = preg_replace("/$table_prefix/i", DB_PREFIX_WPTC, $table_name_from_query, 1);

						$queryArray[0] = str_ireplace($table_name_from_query, $new_table_name_from_query, $queryArray[0]);

						$haystack = implode("(", $queryArray);
					}


				}
			} else {
				if(!$this->is_meta_restore){
					// $new_table_name_from_query = str_ireplace($table_prefix, DB_PREFIX_WPTC, $table_name_from_query);

					$new_table_name_from_query = preg_replace("/$table_prefix/i", DB_PREFIX_WPTC, $table_name_from_query, 1);

					$haystack = str_ireplace($table_name_from_query, $new_table_name_from_query, $haystack);
				}
			}
		}

		$table_name_from_query = wptc_get_table_from_query($haystack);

		if( !empty($from) 
			&& !empty($to) 
			&& stripos($haystack, "insert into") !== false 
			&& stripos($haystack, $fromURL['host']) !== false ){

			if($wptc_php_dump_file){
				$match = explode(", '", $haystack);
			} else {
				$match = explode(",'", $haystack);
			}

			// wptc_log($match, "--------exploded match--------");

			$incrementor = 0;
			foreach($match as $matchDat => $val) {
				$val = str_replace("\',", "**||**||-lcsync,", $val);
				if($wptc_php_dump_file){
					$val = explode("', ", $val);
				} else {
					$val = explode("',", $val);
				}
				$val = $val[0];
				$replaceEndQuote = 0;
				$replaceStartQuote = 0;
				$replaceEndBraces = 0;
				$val = str_replace("**||**||-lcsync,", "\',", $val);
				$val = trim($val, ");\n");
				$val = trim($val, "'");
				$oldval = $val;
				$val = $this->stripallslashes($val);

				// wptc_log($val, "--------to replace val--------");

				if ($this->is_multisite) { //revisit
					$replace = $this->findAndReplace($fromURL['host'], $toURL['host'], $val);
					$replace = $this->findAndReplace($fromURL['path'], $toURL['path'], $replace);
				} else {
					$urlPort = '';
					$urlPath = '';
					if (isset($fromURL['port']) && $fromURL['port'] != '')
						$urlPort = ":".$fromURL['port'];
					if (isset($fromURL['path']) && $fromURL['path'] != '')
						$urlPath = $fromURL['path'];
					$fromHTTPS = "https://".$fromURL['host'].$urlPort.$urlPath;
					$fromHTTP = "http://".$fromURL['host'].$urlPort.$urlPath;
					$withoutProtocolFrom = "//".$fromURL['host'].$urlPort.$urlPath;
					$replace = $this->findAndReplace(array($fromHTTPS, $fromHTTP, $withoutProtocolFrom), $to, $val);
				}

				if ($incrementor == 0 && stripos($replace, "'") !== false) {

					$replace = str_replace("'", "**||**||-lcsync", $replace);
					$escapedSQL = $this->wpdb->_real_escape($replace);
					$escapedSQL = str_replace("**||**||-lcsync", "'", $escapedSQL);
				} else {
					$escapedSQL = $this->wpdb->_real_escape($replace);
				}

				$haystack = str_replace($oldval, $escapedSQL, $haystack);
				$incrementor++;
			}
		}

		// wptc_log($haystack, "--------haystack--------");

		if (stripos($haystack, "insert into") !== false) {
			if ($this->tempQueryCount > 0) {
				if ($this->tempQueryCount > 1000 || $this->tempQuerySize > 100000) {
					$sql = ",".$this->replaceInsertQuery($haystack, $table_name_from_query). ";\n";
					$retArray['q'] = $sql;
					$retArray['exec'] = 1;
					$this->resetTempQuery(-1);
				} else {
					$sql = ",".$this->replaceInsertQuery($haystack, $table_name_from_query);
					$retArray['q'] = $sql;
				}
			} else {
				$sql = substr($haystack, 0, -2);
				$retArray['q'] = $sql;
			}

			$this->tempQueryCount = $this->tempQueryCount + 1;
			$this->tempQuerySize = $this->tempQuerySize + strlen($sql);

			// wptc_log($retArray['q'], "-----modified---haystack--------");

			// exit;

		} else {

			// wptc_log('', "--------else haystack--------");

			// if($this->tempQueryCount > 0){
			// 	$haystack = "; \n " . $haystack;
			// }

			$retArray['q'] = $haystack;
			$retArray['exec'] = 1;
			$retArray['prevExec'] = 1;
			$this->resetTempQuery();
		}

		// wptc_log($retArray['q'], "-----modified---haystack--------");

		return $retArray;
	}

	public function stripallslashes($string) {
        $string = str_ireplace(array('\"',"\'",'\r','\n',"\\\\"),array('"',"'","\r","\n","\\"),$string);

		return $string;
    }

    public function findAndReplace( $from = '', $to = '', $data = '', $serialised = false) {

    	// wptc_log(func_get_args(), "--------findAndReplace--------");

        try {
            if ( is_string( $data ) && ( $unserialized = @unserialize( $data ) ) !== false ) {

                $data = $this->findAndReplace( $from, $to, $unserialized, true );
            }

            elseif ( is_array( $data ) ) {
                $_tmp = array( );
                foreach ( $data as $key => $value ) {
                    $_tmp[ $key ] = $this->findAndReplace( $from, $to, $value, false );
                }

                $data = $_tmp;
                unset( $_tmp );
            }

            elseif ( is_object( $data ) ) {
                $_tmp = $data;
                $props = get_object_vars( $data );
                foreach ( $props as $key => $value ) {
                    $_tmp->$key = $this->findAndReplace( $from, $to, $value, false );
                }

                $data = $_tmp;
                unset( $_tmp );
            }

            else {
                if ( is_string( $data ) ) {
                    $data = str_replace( $from, $to, $data );
                }
            }
            //file_put_contents(dirname(__FILE__)."/__debugger1.php",$tableName.'-'.var_export($data,1)."\n<br><br>\n",FILE_APPEND );
            if ( $serialised )
                return serialize( $data );

        } catch( Exception $error ) {}

        return $data;
    }

	public function resetTempQuery($val=0) {
        $this->tempQueryCount=$val;
	    $this->tempQuerySize=0;
    }

	public function replaceInsertQuery($query, $table_name_from_query) {
        if(stripos($query,"INSERT INTO")!==false) {
	        // $newTable = str_ireplace($table_prefix, DB_PREFIX_WPTC, $this->old_table_name);

	        $query = str_ireplace("INSERT INTO `".$table_name_from_query."` VALUES", '', $query);

	        $query = substr($query, 0, -2);

	        // wptc_log($query, "--------replaceInsertQuery--------");
        }

        return $query;
    }

	private function is_collation_issue($error){
		wptc_log(func_get_args(), "--------" . __FUNCTION__ . "--------");

		if (!$error) {
			return false;
		}

		if (strstr($error, 'Unknown collation') === false) {
			return false;
		}

		$this->config->set_option('replace_collation_for_this_restore', true);

		return true;

	}

	private function get_collation_replacement_status(){
		return $this->config->get_option('replace_collation_for_this_restore');
	}

	private function replace_collation($current_query){
		if (strstr($current_query,'utf8mb4_unicode_520_ci') === false) {
			return $current_query;
		}

		return str_replace('utf8mb4_unicode_520_ci','utf8mb4_unicode_ci', $current_query);
	}

	public function set_additional_flags(){
		$this->is_restore_to_staging = $this->config->get_option('is_restore_to_staging');
		$this->is_meta_restore       = $this->config->get_option('is_meta_restore_running');
		$this->is_migration          = $this->config->get_option('migration_url') ? true : false;
		$this->is_multisite          = $this->config->get_option('restore_is_multisite') ? true : false;
		if ($this->is_multisite) {
			$this->multisite_config['base_prefix']    = $this->config->get_option('restore_multisite_base_prefix');
			$this->multisite_config['current_prefix'] = $this->config->get_option('restore_multisite_current_prefix');
			$this->multisite_config['upload_dir']     = $this->config->get_option('restore_multisite_upload_dir');
		}

		wptc_log($this->is_multisite,'-----------$this->is_migration sql----------------');
		wptc_log($this->multisite_config,'-----------$this->multisite_config sql----------------');
	}

	public function set_old_prefix_restore_to_staging(){
		$this->live_db_prefix = $this->config->get_option('s2l_live_db_prefix');
		$this->site_db_prefix = $this->config->get_option('site_db_prefix');
		wptc_log($this->site_db_prefix,'-----------$this->site_db_prefix----------------');
	}

	private function skip_this_query_for_multisite($current_query){

		$wildcards = array('UNLOCK TABLES', 'LOCK TABLES');

		foreach ($wildcards as $wildcard) {
			if (strstr($current_query, $wildcard) !== false) {
				return false;
			}
		}

		$table_name = wptc_get_table_from_query($current_query);

		// wptc_log($table_name,'-----------$table_name----------------');

		// wptc_log($table_name,'-----------$table_name-----suring skip query-----------');

		preg_match("/^".$this->multisite_config['base_prefix']."._/", $table_name, $output_array);

		// wptc_log($output_array, "--------skip this query output_array--------");

		if (empty($output_array)) {
			return true;
		}

		if($output_array[0] === $this->multisite_config['current_prefix']){
			return false;
		}

		return true;
	}

	public function search_and_replace_db_name($query){
		if(!$this->is_migration){

			return $query;
		}

		if(!$this->is_restore_to_staging){

			return $query;
		}

		$stripos1 = stripos($query, 'CREATE DATABASE IF NOT EXISTS');
		$stripos2 = stripos($query, 'USE ');
		if($stripos1 !== false){

			wptc_log('', "--------search_and_replace_db_name--1--success----");

			return 'CREATE DATABASE IF NOT EXISTS `' . DB_NAME . '`';
		}

		if($stripos2 !== false && $stripos2 == 0){

			wptc_log('', "--------search_and_replace_db_name--2--success----");

			return 'USE `' . DB_NAME . '`';
		}

		return $query;
	}

	public function search_and_replace_prefix($query){
		$old_table_name = wptc_get_table_from_query($query);
		$this->old_table_name = $old_table_name;

		// wptc_log($old_table_name, "--------old_table_name----wptc_get_table_from_query----");

		$this->get_table_prefix_wptc_tables($old_table_name);

		if ($this->is_restore_to_staging) {
			$old_prefix = $this->live_db_prefix;
		} else if($this->is_meta_restore) {
			$old_prefix = $this->wptc_db_prefix;
		} else if($this->is_migration) {
			$old_prefix = $this->site_db_prefix;
		}

		// wptc_log($old_prefix, "--------old_prefix--------");
		// wptc_log($old_table_name, "--------old_table_name--------");
		// wptc_log($new_table_name, "--------new_table_name----search_and_replace_prefix----");

		if (!empty($this->is_meta_restore)) {
			$new_table_name = preg_replace("/$old_prefix/i", DB_PREFIX_WPTC, $old_table_name, 1);

			return str_replace($old_table_name, $new_table_name, $query);
		} else {

			return $query;
		}
	}

	public function get_table_prefix_wptc_tables($table){


		if ($this->wptc_db_prefix) {
			return ;
		}

		$wptc_tables = array(
			'wptc_activity_log',
			'wptc_auto_backup_record',
			'wptc_backup_names',
			'wptc_backups',
			'wptc_current_process',
			'wptc_debug_log',
			'wptc_excluded_files',
			'wptc_excluded_tables',
			'wptc_inc_exc_contents',
			'wptc_included_files',
			'wptc_included_tables',
			'wptc_options',
			'wptc_processed_files',
			'wptc_processed_iterator',
			'wptc_processed_restored_files',
		);

		foreach ($wptc_tables as $wptc_table) {
			if (stristr($table, $wptc_table) !== false) {
				$this->wptc_db_prefix = substr($table, 0, stripos($table, $wptc_table));
				break;
			}
		}
	}

	public function restore_to_staging_replace_links(){

		if($this->config->get_option('R2S_replace_links')){
			wptc_log(array(),'----------replace links done already----------------');
			return ;
		}

		if(!$this->config->get_option('is_restore_to_staging')){
			wptc_log(array(),'----------not is_restore_to_staging----------------');
			return ;
		}

		$this->init_necessary_things_R2S();

		// $replace_db_links = $this->config->get_option('restore_deep_links_completed');
		// wptc_log($replace_db_links , '-------------$replace_db_links -------------------');

		// if(empty($replace_db_links)){

		// 	wptc_manual_debug('', 'start_replace_old_url_R2S');
		// 	$this->replace_db_links();
		// 	wptc_manual_debug('', 'end_replace_old_url_R2S');

		// }

		$this->create_default_htaccess();


		$this->replace_links_obj->discourage_search_engine(DB_PREFIX_WPTC, $reset_permalink = true);

		$this->replace_links_obj->update_site_and_home_url(DB_PREFIX_WPTC, $this->new_url);

		$this->replace_links_obj->rewrite_rules(DB_PREFIX_WPTC);

		$this->replace_links_obj->update_user_roles(DB_PREFIX_WPTC, $this->live_db_prefix);

			//Replace new prefix
		$this->replace_links_obj->replace_prefix(DB_PREFIX_WPTC, $this->live_db_prefix);

		//multisite changes
		if (is_multisite()) {
			$this->replace_links_obj->multi_site_db_changes(DB_PREFIX_WPTC, $this->new_url, $this->old_url);
		}

		//replace $table_prefix in wp-config.php
		$this->replace_links_obj->modify_wp_config(
			array(
				'old_url' =>  $this->old_url,
				'new_url' =>  $this->new_url,
				'new_path' => $this->new_dir,
				'old_path' => $this->old_dir,
				'new_prefix' =>  DB_PREFIX_WPTC,
			), 'RESTORE_TO_STAGING'
		);

		wptc_log(array(),'-----------8-----------------');


		//Deactivate WP Time Capsule on staging site
		WPTC_Base_Factory::get('Wptc_App_Functions')->run_deactivate_plugin('wp-time-capsule/wp-time-capsule.php', DB_PREFIX_WPTC);

		//Activate WP Time Capsule Staging plugin on staging site
		WPTC_Base_Factory::get('Wptc_App_Functions')->run_activate_plugin('wp-time-capsule-staging/wp-time-capsule-staging.php', DB_PREFIX_WPTC);

		$this->config->set_option('R2S_replace_links', true);
	}

	public function migration_replace_links(){

		wptc_log(func_get_args(), "--------" . __FUNCTION__ . "--------");

		if($this->config->get_option('migration_replaced_links')){
			wptc_log(array(),'----------replace links done already----------------');
			return ;
		}

		if(!$this->config->get_option('migration_url')){
			wptc_log(array(),'----------not migration_url----------------');
			return ;
		}

		$this->config->set_option('is_migration_running', true);

		$this->init_necessary_things_migration();

		// $replace_db_links = $this->config->get_option('restore_deep_links_completed');
		// wptc_log($replace_db_links , '-------------$replace_db_links -------------------');

		// if(empty($replace_db_links)){

		// 	wptc_manual_debug('', 'start_replace_old_url');
		// 	$this->replace_db_links($is_migration = true);
		// 	wptc_manual_debug('', 'end_replace_old_url');

		// }

		$this->replace_links_obj->update_site_and_home_url(DB_PREFIX_WPTC, $this->new_url);

		$this->replace_links_obj->rewrite_rules(DB_PREFIX_WPTC);

		$this->replace_links_obj->update_user_roles(DB_PREFIX_WPTC, $this->site_db_prefix);

			//Replace new prefix
		$this->replace_links_obj->replace_prefix(DB_PREFIX_WPTC, $this->site_db_prefix);

		//multisite changes
		if (is_multisite()) {
			$this->replace_links_obj->multi_site_db_changes(DB_PREFIX_WPTC, $this->new_url, $this->old_url);
		}

		//replace $table_prefix in wp-config.php
		$this->replace_links_obj->modify_wp_config(
			array(
				'old_url'    => $this->old_url,
				'new_url'    => $this->new_url,
				'new_path'   => $this->new_dir,
				'old_path'   => $this->old_dir,
				'new_prefix' => DB_PREFIX_WPTC,
			), 'MIGRATION'
		);

		$this->replace_links_obj->replace_htaccess(
			array(
				'new_url'    => $this->new_url,
				'new_path'   => $this->new_dir,
				'old_path'   => $this->old_dir,
			)
		);

		$this->config->set_option('is_migration_running', false);
		$this->config->set_option('migration_replaced_links', true);
	}

	private function create_default_htaccess(){
		if (is_multisite()) {
			return $this->multi_site_default_htaccess();
		}

		return $this->normal_site_default_htaccess();
	}

	private function multi_site_default_htaccess(){
		$this->replace_links_obj->create_htaccess($this->new_url, $this->new_dir, 'multisite');
	}

	private function normal_site_default_htaccess(){
		$this->replace_links_obj->create_htaccess($this->new_url, $this->new_dir, 'normal');
	}

	public function replace_db_links($is_migration = false){
		if (!$this->is_restore_to_staging && !$is_migration) {

			return ;
		}

		$restore_deep_links_completed = $this->config->get_option('restore_deep_links_completed');

		wptc_log($restore_deep_links_completed,'-----------$restore_deep_links_completed----------------');
		if ($restore_deep_links_completed) {

			return ;
		}

		$raw_result = $this->config->get_option('same_server_replace_old_url_data');

		wptc_log($raw_result,'-----------$raw_result----------------');
		
		$tables = false;
		if (!empty($raw_result)) {
			$tables = @unserialize($raw_result);
		}

		$new_site_url = $this->config->get_option('site_url_wptc');

		$this->replace_links_obj->replace_uri($this->old_url, $this->new_url, $this->old_dir, $this->new_dir, DB_PREFIX_WPTC, $tables, $new_site_url, 'restore_in_staging');

		$this->config->set_option('restore_deep_links_completed', true);
	}

	private function init_necessary_things_R2S(){
		$this->get_replace_db_link_obj();
		$this->old_url = $this->config->get_option('s2l_live_url');
		$this->new_url = $this->config->get_option('site_url_wptc');
		$this->old_dir = $this->config->get_option('s2l_live_path');
		$this->new_dir = $this->config->get_option('site_abspath');

		wptc_log($this->old_url, '---------------$this->old_url-----------------');
		wptc_log($this->new_url, '---------------$this->new_url-----------------');
		wptc_log($this->old_dir, '---------------$this->old_dir-----------------');
		wptc_log($this->new_dir, '---------------$this->new_dir-----------------');
	}

	private function init_necessary_things_migration(){
		$this->get_replace_db_link_obj();
		$this->old_url = $this->config->get_option('site_url_wptc');
		$this->new_url = $this->config->get_option('migration_url');
		$this->old_dir = $this->config->get_option('site_abspath');
		$this->new_dir = wptc_add_trailing_slash(dirname(dirname(__FILE__)));

		$this->config->set_option('setup_fresh_site_coz_migration', true);

		wptc_log($this->old_url, '---------------$this->old_url-----------------');
		wptc_log($this->new_url, '---------------$this->new_url-----------------');
		wptc_log($this->old_dir, '---------------$this->old_dir-----------------');
		wptc_log($this->new_dir, '---------------$this->new_dir-----------------');
	}

	private function get_replace_db_link_obj(){
		$this->replace_links_obj = new WPTC_Replace_DB_Links();
	}

	public function send_report_data($id, $status){
		if ($this->is_restore_to_staging) {
			return WPTC_Base_Factory::get('Wptc_Backup_Analytics')->send_report_data($id, 'RESTORE_TO_STAGING', $status);
		}

		WPTC_Base_Factory::get('Wptc_Backup_Analytics')->send_report_data($id, 'RESTORE', $status);
	}

	public function delete_state_files_from_download_list($processed_files_obj) {

		$restore_temp_folder = $this->config->get_backup_dir(true) . '/' . WPTC_TEMP_DIR_BASENAME;

		wptc_log($restore_temp_folder,'-----------$restore_temp_folder----------------');

		$temp_dir = $this->convert_abspath_dir_to_temp_dir_path($restore_temp_folder);

		wptc_log($temp_dir,'-----------$temp_dir----------------');

		$state_file = $temp_dir . '/backups/wptc_current_files_state.txt';

		wptc_log($state_file, '---------------$state_file-----------------');

		if (!file_exists($state_file)) {
			wptc_log(array(), '----------------File not exists----------------');
			return true;
		}

		$handle = fopen($state_file, "rb");

		if (empty($handle)) {
			wptc_log(array(), '----------------cannot open state file----------------');
			return false;
		}

		$prev_offset = $this->config->get_option('delete_state_files_from_download_list_offset');
		$prev_offset = ($prev_offset) ? $prev_offset : 0 ;

		wptc_log($prev_offset, '---------------$prev_offset-----------------');

		$current_offset = 0;

		$processsed_files_count_on_this_request = 0;

		$bulk_delete = '';

		while (($file = fgets($handle)) !== false) {

			$current_offset++;

			if ($current_offset <= $prev_offset ) {
				continue; //check offset; if it already processed ; then continue;
			}

			wptc_manual_debug('', 'during_delete_state_files_from_download_list', 100);

			$file = str_replace("\n", '', $file);

			if (empty($file)) {
				continue;
			}

			wptc_log($file, '---------------$file-----------------');

			$processsed_files_count_on_this_request++;

			if ($processsed_files_count_on_this_request < 500) {
				if (empty($bulk_delete)) {
					$bulk_delete .= "('" . $file . "', ";
				} else {
					$bulk_delete .= "'" . $file . "', ";
				}

				continue;
			}

			$this->delete_state_files_as_bulk_query($bulk_delete);

			$bulk_delete = '';

			$processsed_files_count_on_this_request = 0;

			// $processed_files_obj->delete_file_from_download_list($file);

			if(!$this->maybe_call_again_tc($return = true)){
				continue;
			}

			$this->config->set_option('delete_state_files_from_download_list_offset', $current_offset);
			$this->die_with_msg("wptcs_callagain_wptce");
		}

		if (!empty($bulk_delete)) {
			$this->delete_state_files_as_bulk_query($bulk_delete);
		}

		return true;
	}

	private function delete_state_files_as_bulk_query($bulk_delete){
		$bulk_delete  = rtrim($bulk_delete, ', ');
		$bulk_delete = "DELETE FROM {$this->wpdb->prefix}wptc_processed_restored_files WHERE (file) IN " . $bulk_delete . ")";
		$delete_state_files_as_bulk_query = $this->wpdb->query($bulk_delete);
		wptc_log($delete_state_files_as_bulk_query,'-----------$delete_state_files_as_bulk_query----------------');
		if ($delete_state_files_as_bulk_query === false) {
			wptc_log($bulk_delete,'-----------$bulk_delete----------------');
		}

	}

	public function convert_abspath_dir_to_temp_dir_path($path){
		wptc_log(func_get_args(), "--------" . __FUNCTION__ . "--------");
		$path = wptc_remove_trailing_slash($path);

		// wptc_log($path,'-----------$path----------------');
		
		if (wptc_is_wp_content_path($path) && wptc_is_wp_content_dir_moved_outside_root($this->config)) {
			wptc_log(array(),'-----------Coming 1----------------');
			return  $path . '/' . str_replace(dirname(WPTC_ABSPATH) . '/', '', $path);
		}

		return  $path . '/' . str_replace(WPTC_ABSPATH, '', $path);
	}

	public function recreate_triggers(){

		if ($this->config->get_option('tried_to_create_triggers_after_restore')) {
			wptc_log(array(),'----------recreate_triggers already done-----------------');
			return ;
		}

		if (!$this->allowed_to_create_triggers()) {
			$this->config->set_option('tried_to_create_triggers_after_restore', true);
			return false;
		}

		$trigger = WPTC_Base_Factory::get('Trigger_Init');

		if(!$this->config->get_option('refresh_triggers_on_restore') ){
			$trigger->drop_trigger_for_all_tables();
			$this->config->set_option('refresh_triggers_on_restore', true);
		}

		$error = $trigger->create_trigger_for_all_tables($dont_create_table = true);

		if (!empty($error)) {
			$this->config->set_option('backup_slot', 'daily');
			//wptc_modify_schedule_backup();
			set_admin_notices_wptc('WPTC cannot create triggers so backup schedule fallback to daily. If you want to enable real time backups go to Settings -> Backups -> modify Backup Schedule', 'error', false, false);
			wptc_log(array(),'-----------FAiled to recreate triggers----------------');
		}

		$this->config->set_option('tried_to_create_triggers_after_restore', true);
	}

	private function allowed_to_create_triggers(){
		if ($this->config->get_option('is_not_eligible_for_partial_backup') ) {
			$this->config->set_option('tried_to_create_triggers_after_restore', true);
			return false;
		}

		$current_backup_slot = $this->config->get_option('backup_slot');
		$auto_backup_slots 	 = wptc_get_auto_backup_slots();

		if (empty($auto_backup_slots)) {
			// $this->config->set_option('backup_slot', 'daily');
			//wptc_modify_schedule_backup();
			// set_admin_notices_wptc('WPTC cannot create triggers so backup schedule fallback to daily. If you want to enable real time backups go to Settings -> Backups -> modify Backup Schedule', 'error', false, false);
			// wptc_log(array(),'-----------slots data are empty----------------');
			return false;
		}

		//Current slot not in the slot of auto backup
		if(empty( $auto_backup_slots[$current_backup_slot] )){
			// $this->config->set_option('tried_to_create_triggers_after_restore', true);
			return false;
		}

		return $auto_backup_slots[$current_backup_slot]['partial_db_backup'];
	}

	public function is_multisite_skip_file($file){
		if (!$this->is_multisite) {

			// wptc_log($file, "--------is_multisite_skip_file---false-----");

			return false;
		}

		if ( $this->multisite_config['base_prefix'] != $this->multisite_config['current_prefix']  ){

			return false;
		}

		$file = wptc_remove_fullpath($file);

		wptc_log($file, "--------" . __FUNCTION__ . "--------");

		if ( strstr($file, $this->multisite_config['upload_dir']) === false ) {

			wptc_log($file,'-----------Skiped file on restore----by multisite restriction------------');
		
			return true;
		}

		return false;
	}

	public function is_parent_multisite_site()
	{
		return ($this->multisite_config['base_prefix'] == $this->multisite_config['current_prefix']);
	}

	public function is_ms_and_ms_upload_dir($file)
	{
		if ( $this->multisite_config['base_prefix'] != $this->multisite_config['current_prefix'] 
			&& strstr($file, $this->multisite_config['upload_dir']) !== false ) {

			wptc_log($file,'-----------is_ms_and_ms_upload_dir------------');
		
			return true;
		}

		return false;	
	}
}
