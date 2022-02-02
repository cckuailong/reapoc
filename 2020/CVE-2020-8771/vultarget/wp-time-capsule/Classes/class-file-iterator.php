<?php

class WPTC_File_Iterator{

	private $dir;
	private $wpdb;
	private $current_iterator_table;
	private $fs;
	private $file_base;
	private $staging_common;
	private $deep_dirs;
	private $iterator_common;

	public  function __construct(){
		$this->init_db();
		$this->current_iterator_table = new WPTC_Processed_iterator();
		$this->iterator_common = new WPTC_Iterator_Common();
		$this->file_base = new Utils_Base();

		if (!defined('WPTC_BRIDGE') || !WPTC_BRIDGE) {
			$this->staging_common = new WPTC_Stage_Common();
		}

		$this->deep_dirs = array(
			WPTC_RELATIVE_ABSPATH,
			WPTC_RELATIVE_WP_CONTENT_DIR,
			WPTC_RELATIVE_WP_CONTENT_DIR . '/' . WPTC_TEMP_DIR_BASENAME,
			WPTC_RELATIVE_UPLOADS_DIR,
			WPTC_RELATIVE_UPLOADS_DIR . '/'. WPTC_TEMP_DIR_BASENAME,
		);
	}

	private function init_db(){
		global $wpdb;
		$this->wpdb =$wpdb;
	}

	private function init_fs(){
		$this->fs = $this->staging_common->init_fs();
	}

	public function get_deep_dirs(){
		return $this->deep_dirs;
	}

	private function get_db_backup_file(){
		if (!is_any_ongoing_wptc_backup_process()) {
			return ;
		}

		$files_obj = $this->get_files_obj_by_path( WPTC_RELATIVE_UPLOADS_DIR . '/'. WPTC_TEMP_DIR_BASENAME );
		$this->add_dir_list($files_obj);
	}

	public function get_folders(){

		if (defined('WPTC_BRIDGE') || !apply_filters('is_auto_backup_running_wptc', '')) {

			wptc_log('', "--------get_folders--FIRST_METHOD------");

			return $this->scan_entire_site();
		}

		if( defined('WPTC_BRIDGE') || $this->get_auto_update_folders() === 'normal_method'){

			wptc_log('', "--------get_folders--SECOND_METHOD------");

			return $this->scan_entire_site();
		}

		wptc_log('', "--------get_folders--THIRD_METHOD------");

		$this->get_db_backup_file();
		$this->save_dir_list();
	}

	private function scan_entire_site(){

		wptc_log('', "--------scanning_entire_site--------");

		$this->get_root_dir_folders();

		// wptc_log($this->dir, "--------all dirs--------");

		$this->get_wp_content_dir_folders();
		$this->get_uploads_dir_folders();
		$this->get_db_backup_file();
		$this->save_dir_list();
		$this->save_deep_dir_list();
	}

	private function get_auto_update_folders(){
		//As of now ,we have improved iterator faster so even real time backups scans entire sites
		return 'normal_method';
	}

	public function get_root_dir_folders(){
		$files_obj = $this->get_files_obj_by_path(WPTC_RELATIVE_ABSPATH);

		// wptc_log($files_obj, "--------get_root_dir_folders--------");

		$this->add_dir_list($files_obj);
	}

	public function get_wp_content_dir_folders(){
		$files_obj = $this->get_files_obj_by_path(WPTC_RELATIVE_WP_CONTENT_DIR);

		// wptc_log($files_obj, "--------get_wp_content_dir_folders--------");

		$this->add_dir_list($files_obj);
	}

	public function get_uploads_dir_folders(){
		$files_obj = $this->get_files_obj_by_path(WPTC_RELATIVE_UPLOADS_DIR);

		// wptc_log($files_obj, "--------get_uploads_dir_folders--------");

		$this->add_dir_list($files_obj);
	}

	private function add_dir_list($files_obj){
		foreach ($files_obj as $key => $file_obj) {

			$file = $file_obj->getPathname();

			$file = wp_normalize_path($file);

			if (!wptc_is_dir($file)) {
				/// $this->files[] = $file;
			} else {
				$file = wptc_remove_fullpath($file);
				$this->dir[] = $file;
			}

			// wptc_log($file, "--------added_to_dir_list--------");

		}

		wptc_manual_debug('', 'after_adding_to_dir_list');

	}

	public function save_dir_list(){
		$qry = '';

		foreach ($this->dir as $dir) {
			if (in_array($dir, $this->deep_dirs)) {
				continue;
			}


			$qry .= empty($qry) ? "('" : ",('" ;
			$qry .= wp_normalize_path($dir) . "', '0')";

		}

		wptc_manual_debug('', 'before_insert_into_iterator_process_1');

		// wptc_log($qry, "--------insert into iterator query--------");

		$this->insert_into_iterator_process($qry);

		wptc_manual_debug('', 'after_insert_into_iterator_process_1');
	}

	public function save_deep_dir_list(){

		$qry = '';
		foreach ($this->deep_dirs as $dir) {
			$qry .= empty($qry) ? "('" : ",('" ;
			$qry .= wp_normalize_path($dir) . "', '0')";

		}

		wptc_manual_debug('', 'before_insert_into_iterator_process_2');

		$this->insert_into_iterator_process($qry);

		wptc_manual_debug('', 'after_insert_into_iterator_process_2');
	}

	private function insert_into_iterator_process($qry){
		$sql = "insert into " . $this->wpdb->base_prefix . "wptc_processed_iterator ( name, offset  ) values $qry";
		$result = $this->wpdb->query($sql);
	}

	public function get_files_obj_by_path($path, $recursive = false){

		$path = wptc_add_fullpath($path);

		$path = $this->iterator_common->is_valid_path($path);

		if( is_array($path) ) {
			return $path;
		}

		if($recursive){
			return new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path , RecursiveDirectoryIterator::SKIP_DOTS), RecursiveIteratorIterator::SELF_FIRST, RecursiveIteratorIterator::CATCH_GET_CHILD);
		}

		return new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path , RecursiveDirectoryIterator::SKIP_DOTS), RecursiveIteratorIterator::CATCH_GET_CHILD);
	}

	public function copy_dir($from, $to){
		$this->init_fs();
		$files = $this->get_files_obj_by_path($from, true);
		foreach ($files as $key => $file) {
			$pathname = $files->getPathname();

			$pathname = wp_normalize_path($pathname);

			if (wptc_is_dir($pathname)) {
				continue;
			}

			$to_file = str_replace($from, $to, $pathname);
			$to_file = wp_normalize_path($to_file);

			if (!$this->fs->exists(dirname($to_file))) {
				$this->same_server_mkdir(dirname($to_file));
			}
			$this->fs->copy($pathname, $to_file, true, FS_CHMOD_FILE);
		}
	}

	public function same_server_mkdir($path, $recursive = true){
		$path = wptc_add_fullpath($path);
		$path = wp_normalize_path($path);
		$this->file_base->createRecursiveFileSystemFolder($path, false, false);
	}

	public function is_empty_folder($path){
		$path = wptc_add_fullpath($path);
		$path = wp_normalize_path($path);
		$obj = $this->get_files_obj_by_path($path, true);

		foreach ($obj as $file) {
			$pathname = $file->getPathname();
			$pathname = wp_normalize_path($pathname);

			if (!wptc_is_dir($pathname)) {
				return false;
			}
		}

		return true;
	}
}


class WPTC_Seek_Iterator{

	private $iterator_common;
	private $external_obj;
	private	$iterator_loop_limit;
	private	$path;
	private	$type;
	private	$query;
	private	$processed_files;
	private	$app_functions;
	private	$is_recursive;
	private	$exclude_class_obj;

	public function __construct($object = false, $type = false, $iterator_loop_limit = 100, $category = 'backup'){
		$this->iterator_common = new WPTC_Iterator_Common();
		$this->processed_files = WPTC_Factory::get('processed-files');
		$this->app_functions = WPTC_Base_Factory::get('Wptc_App_Functions');
		$this->exclude_class_obj = new Wptc_ExcludeOption($category);
		$this->type = $type;
		$this->iterator_loop_limit = $iterator_loop_limit;
		$this->external_obj = $object;
	}

	public function get_seekable_files_obj($path){

		$temp_path = $path;

		$path = wptc_add_fullpath($path);

		$path = $this->iterator_common->is_valid_path($path);

		if( is_array($path) ) {
			return $path;
		}

		$this->path = $temp_path;

		return new DirectoryIterator($path);
	}

	public function process_iterator($path, $offset = false, $is_recursive = false){

		$iterator = $this->get_seekable_files_obj($path);

		if (empty($iterator)) {
			return ;
		}

		$this->seek = empty($offset) ? array() : explode('-', $offset);

		$this->counter = 0;
		$this->is_recursive = $is_recursive;

		if ($is_recursive) {
			$this->recursive_iterator($iterator, false);
		} else {
			$this->iterator($iterator);
		}
	}

	public function process_file($iterator, $key){
		switch ($this->type) {
			case 'BACKUP':
				$this->external_obj->process_file($iterator, $this->path, $this->counter, $this->iterator_loop_limit, $this->query, $key);
				break;

			case 'LIVE_TO_STAGING':
				$this->external_obj->process_file($iterator, $this->is_recursive, $this->path, $key, $this->counter, $this->iterator_loop_limit);
				break;

			case 'STAGING_TO_LIVE':
				$this->external_obj->process_file($iterator, $this->is_recursive, $this->path, $this->counter, $this->iterator_loop_limit, $this->query, $key);
				break;

			case 'RESTORE':
				$this->external_obj->process_file($iterator, $this->is_recursive, $this->path, $this->counter, $key);
				break;

			case 'DEV_TEST':
				$this->external_obj->process_file($iterator, $this->is_recursive, $this->path, $this->counter, $this->iterator_loop_limit, $this->query, $key);
				break;
		}
	}

	private function extra_check_query(){
		if (!empty($this->query)) {
			$this->app_functions->insert_into_current_process($this->query);
			$this->query = '';
		}
	}

	public function iterator($iterator){
		//Moving satelite into position.
		$this->seek_offset($iterator);

		while ($iterator->valid()) {

			$this->counter++;

			$recursive_path = $iterator->getPathname();

			$recursive_path = wp_normalize_path($recursive_path);

			//Dont recursive iterator if its a dir or dot
			if ($iterator->isDot() || !$iterator->isReadable()  || $iterator->isDir()) {

				//move to next file
				$iterator->next();

				continue;
			}

			$key = $iterator->key();

			$this->process_file( $iterator, $key );

			//move to next file
			$iterator->next();
		}

		$this->extra_check_query();
	}


	public function recursive_iterator($iterator, $key_recursive) {

		$this->seek_offset($iterator);

		while ($iterator->valid()) {

			//Forming current path from iterator
			$recursive_path = $iterator->getPathname();

			$recursive_path = wp_normalize_path($recursive_path);

			//Mapping keys
			$key = ($key_recursive !== false ) ? $key_recursive . '-' . $iterator->key() : $iterator->key() ;

			//Do recursive iterator if its a dir
			if (!$iterator->isDot() && $iterator->isReadable() && $iterator->isDir() ) {

				if (!$this->exclude_class_obj->is_excluded_file($recursive_path) ) {
					//create new object for new dir
					$sub_iterator = new DirectoryIterator($recursive_path);

					$this->recursive_iterator($sub_iterator, $key);

				} else{
					// wptc_log($recursive_path,'-----------$recursive_path excluded----------------');
				}

			}

			//Ignore dots paths
			if(!$iterator->isDot()){
				$this->process_file( $iterator, $key );
			}

			//move to next file
			$iterator->next();
		}

		$this->extra_check_query();
	}

	private function seek_offset(&$iterator){

		if(!count($this->seek)){
			return false;
		}

		//Moving satelite into position.
		$iterator->seek($this->seek[0]);

		//remove positions from the array after moved satelite
		unset($this->seek[0]);

		//reset array index
		$this->seek = array_values($this->seek);

	}
}


Class WPTC_Iterator_Common{

	public function is_valid_path($path){
		$default = array();

		if (empty($path)) {
			return $default;
		}

		$path = rtrim($path, '/');

		$path = wp_normalize_path($path);

		if (empty($path)) {
			return $default;
		}

		$basename = basename($path);

		if ($basename == '..' || $basename == '.') {
			return $default;
		}

		if (!is_readable($path)) {
			return $default;
		}

		return $path;
	}
}
