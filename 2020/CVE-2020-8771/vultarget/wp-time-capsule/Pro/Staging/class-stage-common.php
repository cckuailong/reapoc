<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class WPTC_Stage_Common{
	private $fs,
			$config,
			$processed_db,
			$staging_id,
			$logger;

	public function __construct(){
		$this->config = WPTC_Factory::get('config');
		$this->processed_db = new WPTC_Processed_iterator();
		$this->logger = WPTC_Factory::get('logger');
	}

	public function init_fs(){
		global $wp_filesystem;
		if (!$wp_filesystem) {
			initiate_filesystem_wptc();
			if (empty($wp_filesystem)) {
				// $this->app_functions->die_with_json_encode(array("status" => "error", 'msg' => 'Could not initiate File system'));
				return false;
			}
		}
		$this->fs = $wp_filesystem;
		return $wp_filesystem;
	}

	public function init_db(){
		global $wpdb;
		$this->wpdb = $wpdb;
		return $wpdb;
	}

	public function get_table_data($table){
		$table_data = $this->processed_db->get_table($table);

		if ($table_data) {
			return array('offset' => $table_data->offset, 'is_new' => false);
		}

		return array('offset' => 0, 'is_new' => true);
	}

	public function clone_table_structure($table, $new_table){
		$this->wpdb->query("DROP TABLE IF EXISTS `$new_table`");

		$sql = "CREATE TABLE `$new_table` LIKE `$table`";

		$is_cloned = $this->wpdb->query($sql);

		if ($is_cloned === false) {
			wptc_log($sql,'-----------$sql----------------');
			$this->log(__('Creating table ' . $table . ' has been failed', 'wptc') , 'staging', $this->staging_id);
			wptc_log('Creating table ' . $this->wpdb->last_error . ' has been failed.', '--------Failed-------------');
			return false;
		}

		$this->log(__("Created table " . $table, 'wptc'), 'staging', $this->staging_id);
		return true;
	}

	public function clone_table_content($table, $new_table, $limit, $offset){
		while(true){
			$inserted_rows = 0;

			wptc_manual_debug('', 'during_clone_table_staging_common_' .$table, 100);

			$inserted_rows = $this->wpdb->query(
				"insert `$new_table` select * from `$table` limit $offset, $limit"
			);

			wptc_log($inserted_rows, '---------$inserted_rows------------');

			if ($inserted_rows !== false) {
				if ($offset != 0) {
					$this->log(__( 'Copy database table: ' . $table . ' DB rows: ' . $offset, 'wptc') , 'staging', $this->staging_id); //create staging id
				}
				$offset = $offset + $inserted_rows;
				if ($inserted_rows < $limit) {
					$this->processed_db->update_iterator($table, -1); //Done
					break;
				}
				if(is_wptc_timeout_cut()){
					$this->processed_db->update_iterator($table, $offset);
					wptc_die_with_json_encode( array('status' => 'continue', 'msg' => 'Cloning ' . $table . '(' . $offset . ')' , 'percentage' => 20) );
				}
			} else {
				$this->processed_db->update_iterator($table, -1); //Done
				wptc_log('Error: '.$this->wpdb->error.'Table ' . $new_table . ' has been created, but inserting rows failed! Rows will be skipped. Offset: ' . $offset , '--------Failed-------------');
				break;
				$this->log(__('Error: '.$this->wpdb->error.'inserting rows failed! Rows will be skipped. Offset: ' . $offset, 'wptc') , 'staging', $this->staging_id);
			}
		}
	}

	public function init_staging_id(){
		$this->staging_id = $this->config->get_option('staging_id',true);
		if (empty($this->staging_id)) {
			$this->config->set_option('staging_id', time());
			$this->staging_id = $this->config->get_option('staging_id');
		}

		return $this->staging_id;
	}

	public function log($msg, $name, $id){
		// return false;
		$this->logger->log($msg, $name, $id);
	}

}
