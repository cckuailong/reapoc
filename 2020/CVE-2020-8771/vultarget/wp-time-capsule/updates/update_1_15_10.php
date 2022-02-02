<?php

Class Wptc_Update_1_15_10{
	const DELETE_ROWS_LIMIT = 5000;
	private $wpdb;
	private $config;

	public function __construct(&$app_functions, &$wpdb, &$config){
		$this->wpdb = $wpdb;
		$this->config = $config;
		$this->init();
	}

	public function init(){

		$default_repo = $this->config->get_option('default_repo');
		if ($default_repo == 'dropbox' || $default_repo == 's3') {
			$this->config->set_option('update_prev_backups_1_15_10', true);
			wptc_log(array(),'-----------This is not g_drive so no upgrade required----------------');
			return ;
		}

		$this->delete_process();

		$this->config->set_option('update_prev_backups_1_15_10', true);
	}

	private function get_pointer(){
		$pointer = $this->config->get_option('update_prev_backups_1_15_10_pointer');
		return empty($pointer) ? 0 : $pointer;;
	}

	private function get_total_rows(){
		$total_rows = $this->config->get_option('update_prev_backups_1_15_10_total_rows');

		if (!empty($total_rows)) {
			return $total_rows;
		}

		$total_rows = $this->wpdb->get_var("SELECT MAX(file_id) FROM {$this->wpdb->base_prefix}wptc_processed_files");
		wptc_log($total_rows,'-----------$total_rows refreshed----------------');
		$this->config->set_option('update_prev_backups_1_15_10_total_rows', $total_rows);
		return $total_rows;
	}

	private function delete_process(){

		$from 	= $this->get_pointer();
		$to = $from + self::DELETE_ROWS_LIMIT;

		$total_rows = $this->get_total_rows();

		while( true ) {
			$query = " DELETE FROM {$this->wpdb->base_prefix}wptc_processed_files
						WHERE `is_dir` IS NULL AND `parent_dir` IS NULL AND `cloud_type` IS NULL AND file_id BETWEEN " . $from . " AND " .  $to;

			wptc_log($query,'-----------$query----------------');

			$result = $this->wpdb->query($query);
			wptc_log($result,'-----------delete_process update_prev_backups_1_15_10_total_rows----------------');

			if ($to > $total_rows) {
				wptc_log(array(),'-----------All rows has been processed----------------');
				break;
			}

			$from 	+= self::DELETE_ROWS_LIMIT;
			$to 	+= self::DELETE_ROWS_LIMIT;

			if (!is_wptc_timeout_cut()) {
				$this->config->set_option( 'update_prev_backups_1_15_10_pointer',  $from);
				continue;
			}

			$this->config->set_option( 'update_prev_backups_1_15_10_pointer', $from );
			send_response_wptc( 'updating_prev_backups_1_15_10_pointer : ' . $from );
		}
	}
}