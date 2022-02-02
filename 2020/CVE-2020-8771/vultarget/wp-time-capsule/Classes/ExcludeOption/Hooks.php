<?php

class Wptc_Exclude_Hooks extends Wptc_Base_Hooks {
	public $hooks_handler_obj;

	public function __construct() {
		$supposed_hooks_hanlder_class = get_class($this) . '_Handler';
		$this->hooks_handler_obj = WPTC_Base_Factory::get($supposed_hooks_hanlder_class);
	}

	public function register_hooks() {
		if (is_admin()) {
			$this->register_actions();
		}
		$this->register_filters();
		$this->register_wptc_actions();
		$this->register_wptc_filters();
	}

	protected function register_actions() {
		add_action('wp_ajax_wptc_get_root_files', array($this->hooks_handler_obj, 'wptc_get_root_files'));
		add_action('wp_ajax_wptc_get_init_root_files', array($this->hooks_handler_obj, 'wptc_get_init_root_files'));
		add_action('wp_ajax_wptc_get_init_files_by_key', array($this->hooks_handler_obj, 'wptc_get_init_files_by_key'));
		add_action('wp_ajax_wptc_get_files_by_key', array($this->hooks_handler_obj, 'wptc_get_files_by_key'));
		add_action('wp_ajax_wptc_get_tables', array($this->hooks_handler_obj, 'wptc_get_tables'));
		add_action('wp_ajax_wptc_get_init_tables', array($this->hooks_handler_obj, 'wptc_get_init_tables'));
		add_action('wp_ajax_exclude_file_list_wptc', array($this->hooks_handler_obj, 'exclude_file_list'));
		add_action('wp_ajax_include_file_list_wptc', array($this->hooks_handler_obj, 'include_file_list'));
		add_action('wp_ajax_exclude_table_list_wptc', array($this->hooks_handler_obj, 'exclude_table_list'));
		add_action('wp_ajax_include_table_list_wptc', array($this->hooks_handler_obj, 'include_table_list'));
		add_action('wp_ajax_include_table_structure_only_wptc', array($this->hooks_handler_obj, 'include_table_structure_only'));
		add_action('wp_ajax_analyze_inc_exc_lists_wptc', array($this->hooks_handler_obj, 'analyze_inc_exc'));
		add_action('wp_ajax_exclude_all_suggested_items_wptc', array($this->hooks_handler_obj, 'exclude_all_suggested_items'));
		add_action('wp_ajax_get_all_excluded_files_wptc', array($this->hooks_handler_obj, 'get_all_excluded_files'));
	}

	protected function register_filters() {
	}

	protected function register_wptc_actions() {

	}

	protected function register_wptc_filters() {
	}

}