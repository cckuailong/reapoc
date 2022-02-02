<?php

class Delete_Trigger_WPTC{

	private $wpdb,
			$trigger_common,
			$trigger_init;

	public function __construct($foo = null) {
		$this->init_db();
		$this->trigger_init   = WPTC_Base_Factory::get('Trigger_Init');
		$this->trigger_common = new Trigger_Common();
	}

	private function init_db(){
		global $wpdb;
		$this->wpdb = $wpdb;
	}

	public function get_query_string($table_name) {
		$columns_arr = $this->trigger_common->get_columns_detail($table_name);

		$where_stmt_str = '';

		foreach($columns_arr as $k => $single_column){
			$where_stmt_str .= '"`' . $single_column['Field'] . '`" , "=", QUOTE(OLD.`' . $single_column['Field'] . '`), " AND ", ';
		}

		// remove extra chars
		$where_stmt_str = rtrim($where_stmt_str, ', " AND ", ');

		return array(
			'where_stmt_str' => $where_stmt_str,
		);
	}

	public function add($table) {

		if($this->trigger_init->is_trigger_exist( $this->get_trigger_name_from_table($table) ) ){
			return false;
		}

		$col_dets = $this->get_query_string($table);

		$trigger_query = ' CREATE TRIGGER ' . $this->get_trigger_name_from_table($table) . ' AFTER DELETE ON `' . $table . '` FOR EACH ROW
							BEGIN
								SET @cur_query = CONCAT("DELETE FROM `' . $table . '`", " WHERE ", ' . $col_dets["where_stmt_str"] . ', ";");
								INSERT INTO ' . $this->trigger_common->get_query_recorder_table()  . ' (id, query, table_name) VALUES (NULL, @cur_query, "' . $table . '");
							END ';

		$trigger_result = $this->wpdb->query($trigger_query);

		if($trigger_result === false){
			wptc_log($this->wpdb->last_error, "----------mysqli_error---delete------");
		}
	}

	public function drop($table) {
		$this->wpdb->query('DROP TRIGGER IF EXISTS ' . $this->get_trigger_name_from_table($table));
	}

	private function get_trigger_name_from_table($table) {
		return 'after_' . md5($table) . '_delete_wptc';
	}
}
