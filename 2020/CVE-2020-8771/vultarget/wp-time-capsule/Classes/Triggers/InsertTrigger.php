<?php

class Insert_Trigger_WPTC{

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

		$column_names_str = $column_values_str = '';

		foreach($columns_arr as $k => $single_column){
			$column_names_str  .= '`' . $single_column['Field'] . "` , ";
			$column_values_str .= 'QUOTE(NEW.`' . $single_column['Field'] . '`), ",", ';
		}

		//Remove extra chars
		$column_names_str  = rtrim($column_names_str, " , ");
		$column_values_str = rtrim($column_values_str, ", \",\", ");

		return array(
			'column_names_str'  => $column_names_str,
			'column_values_str' => $column_values_str,
		);
	}

	public function add($table) {

		if($this->trigger_init->is_trigger_exist( $this->get_trigger_name_from_table($table) ) ){
			return false;
		}

		$col_dets = $this->get_query_string($table);

		$trigger_query = ' CREATE TRIGGER ' . $this->get_trigger_name_from_table($table) . ' AFTER INSERT ON `' . $table . '` FOR EACH ROW
							BEGIN
								SET @col_names_str = "' . $col_dets['column_names_str'] . '";
								SET @col_vals_str = CONCAT(' . $col_dets['column_values_str'] . ');
								SET @cur_query = CONCAT("INSERT INTO `' . $table . '` ", "(", @col_names_str, ")", " VALUES ", "(", @col_vals_str, ")", ";");
								INSERT INTO ' . $this->trigger_common->get_query_recorder_table()  . ' (id, query, table_name) VALUES (NULL, @cur_query, "' . $table . '");
							END ';

		// wptc_log($trigger_query,'-----------$trigger_query----------------');

		$trigger_result = $this->wpdb->query($trigger_query);

		if($trigger_result === false){
			wptc_log($col_dets, "--------col_dets--------");
			wptc_log($trigger_query,'-----------$trigger_query----------------');
			wptc_log($this->wpdb->last_error, "----------mysqli_error-----insert--trigger--");
		}
	}

	public function drop($table) {
		$this->wpdb->query('DROP TRIGGER IF EXISTS ' . $this->get_trigger_name_from_table($table));
	}

	private function get_trigger_name_from_table($table) {
		return 'after_' . md5($table) . '_insert_wptc';
	}
}
