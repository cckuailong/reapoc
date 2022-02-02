<?php

Class Wptc_Update_1_14_10{
	const UPDATE_ROWS_LIMIT = 1000;
	private $app_functions;
	private $wpdb;
	private $config;

	public function __construct(&$app_functions, &$wpdb, &$config){
		$this->app_functions = $app_functions;
		$this->wpdb = $wpdb;
		$this->config = $config;
		$this->init();
	}

	public function init(){

		if ($this->config->get_option('current_stage') === 'completed') {
			$this->config->set_option('update_prev_backups_1_14_10', true);
			wptc_log(array(),'-----------update process already completed----------------');
			return false;
		}

		wptc_set_time_limit(30);

		$this->update_process('stage-1');
		$this->update_process('stage-2');
		$this->update_process('stage-3');
		$this->replace_empty_backup_types();

		$this->config->set_option('update_prev_backups_1_14_10', true);
	}

	private function get_stage(){
		$current_stage = $this->config->get_option('update_prev_backups_1_14_10_current_stage');
		return empty($current_stage) ? 'stage-1' : $current_stage ;
	}

	private function reset_counts(){
		$this->config->set_option('update_prev_backups_1_14_10_total_rows', 0);
		$this->config->set_option('update_prev_backups_1_14_10_pointer', 0);
	}

	private function get_pointer(){
		$pointer = $this->config->get_option('update_prev_backups_1_14_10_pointer');
		return empty($pointer) ? 0 : $pointer;;
	}

	private function get_total_rows(){
		$total_rows = $this->config->get_option('update_prev_backups_1_14_10_total_rows');

		if (!empty($total_rows)) {
			return $total_rows;
		}

		$total_rows = $this->wpdb->get_var("SELECT MAX(file_id) FROM {$this->wpdb->base_prefix}wptc_processed_files");
		wptc_log($total_rows,'-----------$total_rows refreshed----------------');
		$this->config->set_option('update_prev_backups_1_14_10_total_rows', $total_rows);
		return $total_rows;
	}

	private function update_process($requested_stage){
		wptc_log(func_get_args(), "--------" . __FUNCTION__ . "--------");
		$current_stage = $this->get_stage();

		if ($current_stage !== $requested_stage) {
			wptc_log($current_stage,'-----------$current_stage does not match----------------');
			return ;
		}

		$from 	= $this->get_pointer();
		$to = $from + self::UPDATE_ROWS_LIMIT;

		$total_rows = $this->get_total_rows();

		switch ($current_stage) {
			case 'stage-1':
				$search = WPTC_ABSPATH;
				$column = 'parent_dir';
				$next_stage = 'stage-2';
				$replace = '/';
				break;
			case 'stage-2':
				$search = wptc_remove_trailing_slash(WPTC_ABSPATH);
				$column = 'parent_dir';
				$next_stage = 'stage-3';
				$replace = '/';
				break;
			case 'stage-3':
				$search = wptc_remove_trailing_slash(WPTC_ABSPATH);
				$column = 'file';
				$next_stage = 'completed';
				$replace = '';
				break;
		}

		while( true ) {
			$query = " UPDATE {$this->wpdb->base_prefix}wptc_processed_files
						SET " . $column . "  = replace( " . $column . " , '" . $search . "', '" . $replace . "')
						WHERE file_id BETWEEN " . $from . " AND " .  $to;

			wptc_log($query,'-----------$query----------------');

			$result = $this->wpdb->query($query);
			wptc_log($result,'-----------$result----------------');

			if($this->is_stage_over($to, $total_rows, $next_stage )){
				break;
			}

			$from += self::UPDATE_ROWS_LIMIT;
			$to += self::UPDATE_ROWS_LIMIT;

			if (!is_wptc_timeout_cut()) {
				$this->config->set_option( 'update_prev_backups_1_14_10_pointer',  $from);
				continue;
			}

			$this->config->set_option( 'update_prev_backups_1_14_10_pointer', $from );
			send_response_wptc( 'updating_prev_backups_1_14_10_pointer : ' . $from . ' - current_stage: '.$current_stage );
		}

		$this->reset_counts();
		wptc_log($current_stage,'-----------$current_stage is over----------------');
	}

	private function is_stage_over($pointer, $total_rows, $next_stage){
		wptc_log(func_get_args(), "--------" . __FUNCTION__ . "--------");
		if ($pointer < $total_rows) {
			return false;
		}

		$this->config->set_option('update_prev_backups_1_14_10_current_stage', $next_stage);
		return true;
	}

	private function replace_empty_backup_types(){
		$this->wpdb->query("UPDATE {$this->wpdb->base_prefix}wptc_backups SET `backup_type` = 'M' WHERE `backup_type` = '0'");
	}
}