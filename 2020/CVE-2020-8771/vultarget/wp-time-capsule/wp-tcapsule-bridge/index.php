<?php


class WPTC_Bridge_Index{

	private $bridge_core;

	public function __construct(){
		$this->include_bridge_core();
		$this->choose_action();
	}

	private function include_bridge_core(){
		require_once "class-wptc-bridge-core.php";
		$this->bridge_core = new WPTC_Bridge_Core();
	}

	private function choose_action(){

		if (isset($_GET['continue']) && $_GET['continue'] == true ) {
			return $this->continue_restore();
		}

		if (isset($_POST['data']['cur_res_b_id'])) {
			return $this->start_restore();
		}

		if (isset($_POST['action']) && $_POST['action'] == 'check_db_creds') {
			return $this->bridge_core->check_db_creds();
		}

		if (isset($_POST['action']) && $_POST['action'] == 'import_meta_file') {
			return $this->bridge_core->import_meta_file();
		}

		if (isset($_POST['action']) && $_POST['action'] == 'get_migration_html') {
			return $this->bridge_core->get_migration_data($_POST['data']['url']);
		}

		//No action specified
		if (empty($_GET) || !is_array($_GET)) {
			header("Location: index.php?step=connect_db");
		}

		return $this->core_choose_step();
	}

	private function continue_restore(){
		$this->bridge_core->include_config();
		$this->bridge_core->include_files();
		$this->bridge_core->initiate_database();
		$this->bridge_core->load_header();
		$this->bridge_core->include_js_vars();

		if (isset($_GET['position']) && $_GET['position'] == 'beginning' ){
			$this->bridge_core->start_from_beginning();
		} else {
			$this->bridge_core->continue_restore();
		}

		$this->bridge_core->load_footer();
	}

	private function start_restore(){
		$this->bridge_core->include_config();
		$this->bridge_core->include_files();
		$this->bridge_core->initiate_database();
		$this->bridge_core->initiate_filesystem();
		$this->bridge_core->start_restore_tc_callback_bridge();
	}

	private function core_choose_step(){
		$this->bridge_core->define_constants();
		$this->bridge_core->wptc_choose_functions();
		$this->bridge_core->load_header();
		$this->bridge_core->load_footer();
	}
}

new WPTC_Bridge_Index();