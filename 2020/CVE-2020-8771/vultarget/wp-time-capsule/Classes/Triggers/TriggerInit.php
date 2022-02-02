<?php

class Trigger_Init{
	private $processed_files,
			$exclude_class_obj,
			$config,
			$schema_in_tmp_memory,
			$failed_tables = array(),
			$wpdb;

	const MAX_FAILED_COUNT        = 10;

	public function __construct() {
		$this->init_db();
		$this->processed_files   = WPTC_Factory::get('processed-files');
		$this->exclude_class_obj = WPTC_Base_Factory::get('Wptc_ExcludeOption');
		$this->config = WPTC_Factory::get('config');
	}

	private function init_db(){
		global $wpdb;
		$this->wpdb = $wpdb;
	}

	public function create_trigger_for_all_tables($dont_create_table = false) {

		wptc_log(func_get_args(), "--------" . __FUNCTION__ . "--------");

		wptc_set_time_limit(0);

		$error = $this->create_table($dont_create_table);

		wptc_log($error,'-----------$error----------------');

		if (!empty($error['message'])) {
			return $error;
		}

		$tables = $this->processed_files->get_all_tables();

		wptc_log($tables,'-----------$tables----------------');

		$failed_count = 0;

		$error = false;

		foreach ($tables as $table) {

			wptc_log($table,'--------create_trigger_for_all_tables---$table----------------');

			if ($this->exclude_class_obj->is_excluded_table($table) !== 'table_included') {
				continue;
			}

			if ($this->exclude_class_obj->is_log_table($table)) {
				continue;
			}

			if(!$this->create_trigger($table, $is_modified = false, $add_to_tmp_memory = true)){

				wptc_log($table, "-------pushing 3--------");

				$this->push_failed_tables($table);
				$failed_count++;
			} else {
				continue;
			}

			$response = $this->is_max_trigger_creation_failed($failed_count);

			if ($response === false) {
				continue;
			}
			$error = array(
				'title' => 'Failed to create triggers',
				'message' => 'WPTC cannot create the triggers to backup the queries, Please contact hosting provide to enable triggers.' . FALL_BACK_TO_6_HOUR_MSG_WPTC,
				'type' => 'error'
			);

			$this->set_disabled();
			return $error;
		}

		$this->save_schema_in_tmp_memory();
		$this->save_failed_tables();
		$this->set_enabled();
		return false;
	}

	public function enabled(){
		return $this->config->get_option('is_triggers_enabled');
	}

	private function set_enabled(){
		return $this->config->set_option('is_triggers_enabled', true);
	}

	private function set_disabled(){
		return $this->config->set_option('is_triggers_enabled', false);
	}

	private function is_max_trigger_creation_failed($failed_count){
		if ($failed_count >= self::MAX_FAILED_COUNT ) {
			return true;
		}

		return false;
	}

	public function create_trigger($table, $is_modified = false , $add_to_tmp_memory = false){
		WPTC_Base_Factory::get('Insert_Trigger_WPTC')->add($table);
		WPTC_Base_Factory::get('Update_Trigger_WPTC')->add($table);
		WPTC_Base_Factory::get('Delete_Trigger_WPTC')->add($table);

		if (!$this->all_triggers_exist($table)) {
			if (!$add_to_tmp_memory) {

				wptc_log('', "-------pushing 1--------");

				$this->push_failed_tables($table);
			}

			return false;
		}
		if (!$this->upsert_schema($table, $is_modified = $is_modified, $add_to_tmp_memory)) {
			if (!$add_to_tmp_memory) {

				wptc_log('', "-------pushing 2--------");

				$this->push_failed_tables($table);
			}

			return false;
		}

		return true;
	}

	private function push_failed_tables($table){

		wptc_log($table, "--------push_failed_tables--------");

		array_push($this->failed_tables, $table);
	}

	private function save_failed_tables(){
		$this->config->set_option('triggers_failed_tables', serialize($this->failed_tables));
	}

	public function is_trigger_exist($trigger_name){
		wptc_log(func_get_args(), "--------" . __FUNCTION__ . "--------");

		$result = $this->wpdb->get_var("SHOW TRIGGERS WHERE `Trigger` = '" . $trigger_name . "'");

		// wptc_log($result,'-----------$result----------------');

		return !empty($result) ? true : false;
	}

	public function all_triggers_exist($table){
		wptc_log(func_get_args(), "--------" . __FUNCTION__ . "--------");

		$query = "SHOW TRIGGERS WHERE `Trigger` LIKE '%" . md5($table) . "%'";

		$result = $this->wpdb->get_results($query);

		// wptc_log($result,'-----------$result----------------');

		if(empty($result)){
			wptc_log($query, "--------trigger failed for---$table-----");
		}

		return count($result) == 3 ? true : false;
	}

	public function drop_trigger_for_all_tables() {

		$triggers_meta = $this->wpdb->get_results("SHOW TRIGGERS WHERE `Trigger` LIKE '%_wptc%'");

		if (empty($triggers_meta)) {
			return ;
		}

		foreach ($triggers_meta as $trigger_meta) {

			if (empty($trigger_meta->Trigger)) {
				continue;
			}

			$this->drop_trigger_by_name($trigger_meta->Trigger);
		}
	}

	private function drop_trigger_by_name($name){
		wptc_log(func_get_args(), "--------" . __FUNCTION__ . "--------");
		$this->wpdb->query('DROP TRIGGER IF EXISTS ' . $name);
	}

	public function drop_tiggers($table){
		WPTC_Base_Factory::get('Insert_Trigger_WPTC')->drop($table);
		WPTC_Base_Factory::get('Update_Trigger_WPTC')->drop($table);
		WPTC_Base_Factory::get('Delete_Trigger_WPTC')->drop($table);

		$this->remove_schema($table);
	}

	public function update_all_schema(){

		$tables = $this->processed_files->get_all_tables();

		wptc_log($tables,'-----------$tables----------------');
		foreach ($tables as $key => $table) {

			if ($this->exclude_class_obj->is_excluded_table($table) !== 'table_included') {
				continue;
			}

			$this->upsert_schema($table);
		}
	}

	public function upsert_schema($table, $is_modified = false, $add_to_tmp_memory = false){
		$schema  = $this->get_live_schema($table);

		if (empty($schema)) {

			wptc_log($table, "--------upsert_schema--empty--for----");

			return false;
		}

		$encoded = base64_encode($schema);
		$this->add_schema( $table, array( 'schema' => $encoded, 'is_modified' => $is_modified ), $add_to_tmp_memory );

		return true;
	}

	private function save_schema_in_tmp_memory(){
		$this->config->set_option('tables_schema', serialize($this->schema_in_tmp_memory));
	}

	private function add_schema($table, $meta, $add_to_tmp_memory){

		if (!$add_to_tmp_memory) {
			$tables_schema = $this->get_all_schema();
			$tables_schema[$table] = $meta;
			return $this->config->set_option('tables_schema', serialize($tables_schema));
		}

		return $this->schema_in_tmp_memory[$table] = $meta;
	}

	private function remove_schema($table){
		$tables_schema = $this->get_all_schema();

		if (!empty($tables_schema[$table])) {
			unset($tables_schema[$table]);
		}

		$this->config->set_option('tables_schema', serialize($tables_schema));
	}

	public function get_all_schema(){
		$tables_schema = $this->config->get_option('tables_schema');

		if (empty($tables_schema)) {
			return array();
		}

		return unserialize($tables_schema);
	}

	private function get_live_schema($table){
		$schema_meta = $this->wpdb->get_results("SHOW CREATE TABLE `$table`", ARRAY_N);

		if (empty($schema_meta)) {
			return false;
		}

		$current_schema = $schema_meta[0][1];

		return substr($current_schema, 0, strpos($current_schema, "ENGINE="));;
	}

	public function get_stored_schema($table){
		$tables_schema = $this->get_all_schema();

		if (empty($tables_schema[$table])) {
			return false;
		}

		return array(
			'schema'      => base64_decode( $tables_schema[$table]['schema']),
			'is_modified' => $tables_schema[$table]['is_modified']
			);
	}

	public function is_schema_changed($table){
		$stored_schema = $this->get_stored_schema($table);

		if ($stored_schema['is_modified'] == 1) {
			$this->upsert_schema($table, $is_modified = false);
			wptc_log(array(), '---------Schema changed------------');
			return true;
		}

		$current_schema = $this->get_live_schema($table);

		if ($current_schema === $stored_schema['schema']) {
			wptc_log(array(), '---------is_schema_changed false------------');
			return false;
		} else{
			wptc_log(array(), '---------is_schema_changed true------------');
			$this->upsert_schema($table, $is_modified = false);
			return true;
		}
	}

	public function full_backup_needed_tables(){
		$schema_changed_tables = $this->get_schema_changed_tables();

		wptc_log($schema_changed_tables,'-----------$schema_changed_tables----------------');

		$trigger_failed_tables = $this->get_trigger_failed_tables();

		wptc_log($trigger_failed_tables,'-----------$trigger_failed_tables----------------');

		$table = $this->get_trigger_query_tablename();

		wptc_log($table,'-----------$table----------------');
		wptc_log(array_unique(array_merge(array($table), $schema_changed_tables, $trigger_failed_tables ), SORT_REGULAR),'-----------All tables----------------');
		
		return array_unique(array_merge(array($table), $schema_changed_tables, $trigger_failed_tables ), SORT_REGULAR);
	}

	private function get_trigger_failed_tables(){
		$failed_tables = $this->config->get_option('triggers_failed_tables');
		return unserialize($failed_tables);
	}

	public function get_schema_changed_tables(){
		// $prev_records = $this->config->get_option('triggers_schema_changed_tables');
		// if (!empty($prev_records)) {
			// return unserialize($prev_records);
		// }

		$tables = $this->processed_files->get_all_tables();

		$schema_changed = array();
		foreach ($tables as $key => $table) {

			if ($this->exclude_class_obj->is_excluded_table($table) !== 'table_included') {
				continue;
			}

			if(!$this->is_schema_changed($table)){
				continue;
			}

			array_push($schema_changed, $table);
		}

		//remove after backup
		// $this->config->set_option('triggers_schema_changed_tables', serialize($schema_changed));

		return $schema_changed;
	}

	public function get_trigger_query_tablename(){
		return $this->wpdb->base_prefix . 'wptc_query_recorder';
	}

	private function create_table($dont_create_table){

		$table 	= $this->get_trigger_query_tablename();

		wptc_log($table,'-----------$table----------------');

		if ( $this->table_exist($table) ) {
			wptc_log($table,'-----------Table exist----------------');
			return true;
		}

		if ($dont_create_table) {
			return array(
					'title' => 'Failed!',
					'message' => 'WPTC Query table is missing',
					'type' => 'error'
				);
		}

		include_once ( ABSPATH . 'wp-admin/includes/upgrade.php' );

		$cachecollation = wptc_get_collation();

		dbDelta("CREATE TABLE IF NOT EXISTS $table (
			`id` int(11) NOT NULL AUTO_INCREMENT,
			`query` longtext NOT NULL,
			`table_name` text  NOT NULL,
			PRIMARY KEY (`id`)
		) ENGINE=InnoDB " . $cachecollation . " ;");

		if ( $this->table_exist($table) ) {
			wptc_log($table,'-----------Table created----------------');
			return true;
		}

		global $EZSQL_ERROR;

		if (!$EZSQL_ERROR) {
			return array(
					'title' => 'Failed!',
					'message' => 'WPTC cannot create the table to log queries' . FALL_BACK_TO_6_HOUR_MSG_WPTC,
					'type' => 'error'
				);
		}

		foreach ($EZSQL_ERROR as $error) {
			if (preg_match("/^CREATE TABLE IF NOT EXISTS {$wpdb->base_prefix}wptc_/", $error['query'])) {
				return array(
					'title' => 'Failed!',
					'message' => 'WPTC cannot create the table to log queries' . ( (string) $error['error_str'] ) . FALL_BACK_TO_6_HOUR_MSG_WPTC,
					'type' => 'error'
				); ;
			}
		}

		return array(
					'title' => 'Failed!',
					'message' => 'WPTC cannot create the table to log queries' . FALL_BACK_TO_6_HOUR_MSG_WPTC,
					'type' => 'error'
				);
	}

	private function table_exist($table){
		$small_letters_table = strtolower($table);

		if( $this->wpdb->get_var("SHOW TABLES LIKE '$small_letters_table'") == $small_letters_table ){
			
			return true;
		}

		if( $this->wpdb->get_var("SHOW TABLES LIKE '$table'") == $table ){

			return true;
		}

		return false;
	}

	public function truncate_table(){
		$table = $this->get_trigger_query_tablename();

		if (!$this->table_exist($table)) {
			return ;
		}

		$result = $this->wpdb->query('TRUNCATE TABLE `'. $table . "`");
	}

	public function drop_table(){
		$this->wpdb->query('DROP TABLE IF EXISTS `'. $this->get_trigger_query_tablename() . "`");
	}
}
