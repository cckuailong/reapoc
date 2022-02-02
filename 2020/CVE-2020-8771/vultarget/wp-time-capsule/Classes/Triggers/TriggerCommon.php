<?php

class Trigger_Common{
	private $wpdb;

	public function __construct(){
		$this->init_db();
	}

	private function init_db(){
		global $wpdb;
		$this->wpdb = $wpdb;
	}

	public function get_query_recorder_table() {
		return  $this->wpdb->base_prefix . 'wptc_query_recorder';
	}

	public function get_columns_detail($table_name) {
		return  $this->wpdb->get_results("SHOW columns FROM `$table_name`; ", ARRAY_A);
	}

}
