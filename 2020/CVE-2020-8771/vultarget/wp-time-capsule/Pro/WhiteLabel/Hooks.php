<?php

class Wptc_White_Label_Hooks extends Wptc_Base_Hooks {
	public $hooks_handler_obj;
	private $config;
	private $WhiteLabel_obj;

	public function __construct() {
		$supposed_hooks_hanlder_class = get_class($this) . '_Hanlder';
		$this->hooks_handler_obj = WPTC_Pro_Factory::get($supposed_hooks_hanlder_class);
		$this->config = WPTC_Pro_Factory::get('Wptc_White_Label_Config');
		$this->whitelabel_obj = WPTC_Pro_Factory::get('Wptc_White_Label');
	}

	public function register_hooks() {
		$this->register_actions();
		$this->register_filters();
		$this->register_wptc_filters();
	}

	protected function register_actions() {
		//general settings
		add_action('admin_init', array($this,'admin_actions'));

		//hide all updates
		add_action('admin_menu', array($this,'admin_menu_actions'), 999, 1);

		add_filter('show_advanced_plugins', array($this->hooks_handler_obj, 'mu_plugin_list_filter'), 10, 2);

		//update settings from service
		add_action('update_white_labling_settings_wptc', array($this->hooks_handler_obj, 'update_settings'), 10, 1);

		add_action('set_user_to_access_wl_wptc', array($this->hooks_handler_obj, 'set_user_to_access'), 10, 1);

		add_action('add_pages_wl_wptc', array($this->hooks_handler_obj, 'add_pages'), 10, 1);

		add_filter('modify_settings_to_server_wptc', array($this->hooks_handler_obj, 'modify_settings'), 1, 1);
	}

	public function admin_actions(){
		$settings = $this->hooks_handler_obj->get_settings();

		// wptc_log($settings,'-----------$admin_actions----------------');

		if(empty($settings) || empty($settings->wl_select_action)){
			return false;
		}

		if ($settings->wl_select_action == 'normal') {
			return false;
		}

		if($this->hooks_handler_obj->validate_users_access_wptc() === 'authorized') {
			// wptc_log(array(),'-----------authorized----------------');
			return false;
		}

		//Hiding the view details alone.
		add_filter('plugin_row_meta', array($this->hooks_handler_obj, 'replace_row_meta'), 10, 2);

		//Hiding the wptc update details.
		add_filter('site_transient_update_plugins', array($this->hooks_handler_obj, 'site_transient_update_plugins'), 10, 2);

		//Modifying the link available in plugin's view version details link.
		add_filter('admin_url', array($this->hooks_handler_obj, 'user_admin_url'), 10, 2);

		//Replacing name and other details.
		add_filter('all_plugins', array($this->hooks_handler_obj, 'replace_details'));
	}

	public function admin_menu_actions(){
		$settings = $this->hooks_handler_obj->get_settings();

		if(empty($settings)){
			wptc_log(array(),'-----------athorized 6----------------');
			return false;
		}

		if($this->hooks_handler_obj->validate_users_access_wptc() === 'authorized') {
			wptc_log(array(),'-----------athorized 5----------------');
			return false;
		}

		if(!empty($settings->hide_updates)){
			$page = remove_submenu_page( 'index.php', 'update-core.php' );
			add_filter('site_transient_update_plugins', array($this->hooks_handler_obj, 'remove_updates'), 10, 1);
		}

		if(!empty($settings->hide_edit)){
			remove_submenu_page('themes.php','theme-editor.php');
			remove_submenu_page('plugins.php','plugin-editor.php');
			add_filter('plugin_action_links', array($this->hooks_handler_obj, 'replace_action_links'), 10, 2);
		}
	}

	protected function register_filters() {
		add_filter('validate_users_access_wptc', array($this->hooks_handler_obj, 'validate_users_access_wptc'));

	}

	protected function register_wptc_filters() {
		add_filter('is_whitelabling_override_wptc', array($this->hooks_handler_obj, 'is_whitelabling_override'), 10);
		add_filter('is_whitelabling_active_wptc', array($this->hooks_handler_obj, 'is_whitelabling_active'), 10);
		add_filter('hide_this_option_wl_wptc', array($this->hooks_handler_obj, 'hide_this_option'), 10, 1);
		add_filter('is_general_tab_allowed_wptc', array($this->hooks_handler_obj, 'is_general_tab_allowed'), 10, 1);
		add_filter('is_staging_tab_allowed_wptc', array($this->hooks_handler_obj, 'is_staging_tab_allowed'), 10, 1);
		add_filter('is_backup_tab_allowed_wptc', array($this->hooks_handler_obj, 'is_backup_tab_allowed'), 10, 1);
	}
}
