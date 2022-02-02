<?php
class Wptc_Staging_Hooks extends Wptc_Base_Hooks {
	public $hooks_handler_obj;
	public $wp_filter_id;

	public function __construct() {
		$this->hooks_handler_obj = WPTC_Pro_Factory::get('Wptc_Staging_Hooks_Hanlder');
	}

	public function register_hooks() {
		$this->register_actions();
		$this->register_filters();
		$this->register_wptc_actions();
		$this->register_wptc_filters();
	}

	public function register_actions() {
		add_action('wp_ajax_start_fresh_staging_wptc', array($this->hooks_handler_obj, 'start_fresh_staging'));
		add_action('wp_ajax_copy_staging_wptc', array($this->hooks_handler_obj, 'copy_staging'));
		add_action('wp_ajax_continue_staging_wptc', array($this->hooks_handler_obj, 'continue_staging'));
		add_action('wp_ajax_delete_staging_wptc', array($this->hooks_handler_obj, 'delete_staging_wptc'));
		add_action('wp_ajax_get_staging_details_wptc', array($this->hooks_handler_obj, 'get_staging_details'));
		add_action('wp_ajax_stop_staging_wptc', array($this->hooks_handler_obj, 'stop_staging_wptc'));
		add_action('wp_ajax_is_staging_need_request_wptc', array($this->hooks_handler_obj, 'is_staging_need_request'));

		add_action('wp_ajax_get_staging_url_wptc', array($this->hooks_handler_obj, 'get_staging_url_wptc'));
		add_action('wp_ajax_save_upgrade_meta_in_staging_wptc', array($this->hooks_handler_obj, 'save_stage_n_update'));
		add_action('wp_ajax_save_staging_settings_wptc', array($this->hooks_handler_obj, 'save_staging_settings'));
		add_action('wp_ajax_force_update_in_staging_wptc', array($this->hooks_handler_obj, 'force_update_in_staging'));
		add_action('wp_ajax_get_staging_current_status_key_wptc', array($this->hooks_handler_obj, 'get_staging_current_status_key'));
	}

	public function register_filters() {

	}

	public function register_filters_may_be_prevent_auto_update() {

	}

	public function register_wptc_actions() {
		add_action('add_additional_sub_menus_wptc_h', array($this->hooks_handler_obj, 'add_additional_sub_menus_wptc_h'), 10, 2);
		add_action('init_staging_wptc_h', array($this->hooks_handler_obj, 'init_staging_wptc_h'));
		add_action('add_staging_req_h', array($this->hooks_handler_obj, 'add_staging_req_h'));
		add_action('send_response_node_staging_wptc_h', array($this->hooks_handler_obj, 'send_response_node_staging_wptc_h'));
		add_action('admin_enqueue_scripts', array($this->hooks_handler_obj, 'enque_js_files'));
		add_action('staging_view_wptc', array($this->hooks_handler_obj, 'staging_view'));
		add_action('is_staging_taken_wptc', array($this->hooks_handler_obj, 'is_staging_taken'));
		add_action('upgrade_our_staging_plugin_wptc', array($this->hooks_handler_obj, 'upgrade_our_staging_plugin_wptc'));
	}

	public function register_wptc_filters() {
		add_filter('is_any_staging_process_going_on', array($this->hooks_handler_obj, 'is_any_staging_process_going_on'), 10);
		add_filter('get_internal_staging_db_prefix', array($this->hooks_handler_obj, 'get_internal_staging_db_prefix'), 10);
		add_filter('page_settings_tab_wptc', array($this->hooks_handler_obj, 'page_settings_tab'), 10);
		add_filter('page_settings_content_wptc', array($this->hooks_handler_obj, 'page_settings_content'), 10);
		add_filter('process_staging_details_hook_wptc', array($this->hooks_handler_obj, 'process_staging_details_hook'), 10, 1);
		add_filter('set_options_to_staging_site_wptc', array($this->hooks_handler_obj, 'set_options_to_staging_site'), 10, 2);
	}

}
