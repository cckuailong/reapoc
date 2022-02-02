<?php

class Wptc_Exclude_Hooks_Handler extends Wptc_Base_Hooks_Handler {
	protected $config;

	public function __construct() {
		$this->backup_obj = WPTC_Base_Factory::get('Wptc_Backup');
		$this->ExcludeOption = WPTC_Base_Factory::get('Wptc_ExcludeOption');
	}

	//WPTC's specific hooks start

	public function wptc_get_root_files($args) {

		WPTC_Base_Factory::get('Wptc_App_Functions')->verify_ajax_requests();

		$this->ExcludeOption->get_root_files();
	}

	public function wptc_get_init_root_files($args) {

		WPTC_Base_Factory::get('Wptc_App_Functions')->verify_ajax_requests();

		$this->ExcludeOption->get_root_files($exc_wp_files = true);
	}

	public function wptc_get_init_files_by_key($args) {

		WPTC_Base_Factory::get('Wptc_App_Functions')->verify_ajax_requests();

		$key = $_REQUEST['key'];
		$this->ExcludeOption->get_files_by_key($key);
	}

	public function wptc_get_tables($args) {

		WPTC_Base_Factory::get('Wptc_App_Functions')->verify_ajax_requests();

		$this->ExcludeOption->get_tables();
	}

	public function wptc_get_init_tables($args) {

		WPTC_Base_Factory::get('Wptc_App_Functions')->verify_ajax_requests();

		$this->ExcludeOption->get_tables($exc_wp_tables = true);
	}

	public function wptc_get_files_by_key() {

		WPTC_Base_Factory::get('Wptc_App_Functions')->verify_ajax_requests();

		$key = $_REQUEST['key'];
		$this->ExcludeOption->get_files_by_key($key);
	}

	public function include_file_list() {

		WPTC_Base_Factory::get('Wptc_App_Functions')->verify_ajax_requests();

		if (!isset($_POST['data'])) {
			wptc_die_with_json_encode( array('status' => 'no data found') );
		}
		$this->ExcludeOption->include_file_list($_POST['data']);
	}

	public function exclude_file_list() {

		WPTC_Base_Factory::get('Wptc_App_Functions')->verify_ajax_requests();

		if (!isset($_POST['data'])) {
			wptc_die_with_json_encode( array('status' => 'no data found') );
		}
		$this->ExcludeOption->exclude_file_list($_POST['data']);
	}

	public function include_table_list() {

		WPTC_Base_Factory::get('Wptc_App_Functions')->verify_ajax_requests();

		if (!isset($_POST['data'])) {
			wptc_die_with_json_encode( array('status' => 'no data found') );
		}
		$this->ExcludeOption->include_table_list($_POST['data']);
	}

	public function include_table_structure_only() {

		WPTC_Base_Factory::get('Wptc_App_Functions')->verify_ajax_requests();

		if (!isset($_POST['data'])) {
			wptc_die_with_json_encode( array('status' => 'no data found') );
		}
		$this->ExcludeOption->include_table_structure_only($_POST['data']);
	}

	public function exclude_table_list() {

		WPTC_Base_Factory::get('Wptc_App_Functions')->verify_ajax_requests();

		if (!isset($_POST['data'])) {
			wptc_die_with_json_encode( array('status' => 'no data found') );
		}
		$this->ExcludeOption->exclude_table_list($_POST['data']);
	}

	public function analyze_inc_exc() {

		WPTC_Base_Factory::get('Wptc_App_Functions')->verify_ajax_requests();

		$this->ExcludeOption->analyze_inc_exc();
	}

	public function exclude_all_suggested_items() {

		WPTC_Base_Factory::get('Wptc_App_Functions')->verify_ajax_requests();

		$this->ExcludeOption->exclude_all_suggested_items($_POST);
	}

	public function get_all_excluded_files() {

		WPTC_Base_Factory::get('Wptc_App_Functions')->verify_ajax_requests();

		$this->ExcludeOption->get_all_excluded_files();
	}

}