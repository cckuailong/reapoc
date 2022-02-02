<?php
/*
Plugin Name: WP Time Capsule Staging
Plugin URI: https://wptimecapsule.com
Description: WP Time Capsule Staging plugin.
Author: Revmakx
Version: 1.0.0
Author URI: http://www.revmakx.com
Tested up to: 4.8
/************************************************************
 * This plugin was modified by Revmakx
 * Copyright (c) 2017 Revmakx
 * www.revmakx.com
 ************************************************************/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class WP_Time_Capsule_Staging{

	private $stage_to_live;

	public function __construct(){
		$this->include_constants_file();
		$this->include_files();
		$this->include_primary_files_wptc();
		$this->create_objects();
		$this->init_hooks();
	}

	/**
	 * Define WPTC Staging Constants.
	*/
	private function include_constants_file() {
		$this->define('WPTC_IS_STAGING_SITE', true);
		require_once dirname(__FILE__).  DIRECTORY_SEPARATOR  .'wptc-constants.php';
		$constants = new WPTC_Constants();
		$constants->init_staging_plugin();
	}

	private function include_files(){
		include_once ( WPTC_PLUGIN_DIR . 'includes/class-file-iterator.php' );
		include_once ( WPTC_PLUGIN_DIR . 'includes/class-stage-to-live.php' );
		include_once ( WPTC_PLUGIN_DIR . 'includes/class-stage-common.php' );
		include_once ( WPTC_PLUGIN_DIR . 'includes/common-functions.php' );
		include_once ( WPTC_PLUGIN_DIR . 'includes/class-staging-white-label.php' );
		include_once ( WPTC_PLUGIN_DIR . 'utils/g-wrapper-utils.php' );
		include_once ( WPTC_CLASSES_DIR . 'Extension/Base.php' );
		include_once ( WPTC_CLASSES_DIR . 'Extension/Manager.php' );
		include_once ( WPTC_CLASSES_DIR . 'Extension/DefaultOutput.php' );
		include_once ( WPTC_CLASSES_DIR . 'Processed/Base.php' );
		include_once ( WPTC_CLASSES_DIR . 'Processed/Files.php' );
		include_once ( WPTC_CLASSES_DIR . 'Processed/Restoredfiles.php' );
		include_once ( WPTC_CLASSES_DIR . 'Processed/iterator.php' );
		include_once ( WPTC_CLASSES_DIR . 'DatabaseBackup.php' );
		include_once ( WPTC_CLASSES_DIR . 'FileList.php' );
		include_once ( WPTC_CLASSES_DIR . 'Config.php' );
		include_once ( WPTC_CLASSES_DIR . 'Logger.php' );
		include_once ( WPTC_CLASSES_DIR . 'Factory.php' );
	}

	private function include_primary_files_wptc() {

		include_once( WPTC_PLUGIN_DIR.'Base/Factory.php' );

		include_once( WPTC_PLUGIN_DIR.'Base/init.php' );
		include_once( WPTC_PLUGIN_DIR.'Base/Hooks.php' );
		include_once( WPTC_PLUGIN_DIR.'Base/HooksHandler.php' );
		include_once( WPTC_PLUGIN_DIR.'Base/Config.php' );

		include_once( WPTC_PLUGIN_DIR.'Base/CurlWrapper.php' );

		include_once( WPTC_CLASSES_DIR.'CronServer/Config.php' );
		include_once( WPTC_CLASSES_DIR.'CronServer/CurlWrapper.php' );

		include_once( WPTC_CLASSES_DIR.'WptcBackup/init.php' );
		include_once( WPTC_CLASSES_DIR.'WptcBackup/Hooks.php' );
		include_once( WPTC_CLASSES_DIR.'WptcBackup/HooksHandler.php' );
		include_once( WPTC_CLASSES_DIR.'WptcBackup/Config.php' );

		include_once( WPTC_CLASSES_DIR.'Common/init.php' );
		include_once( WPTC_CLASSES_DIR.'Common/Hooks.php' );
		include_once( WPTC_CLASSES_DIR.'Common/HooksHandler.php' );
		include_once( WPTC_CLASSES_DIR.'Common/Config.php' );

		include_once( WPTC_CLASSES_DIR.'Analytics/init.php' );
		include_once( WPTC_CLASSES_DIR.'Analytics/Hooks.php' );
		include_once( WPTC_CLASSES_DIR.'Analytics/HooksHandler.php' );
		include_once( WPTC_CLASSES_DIR.'Analytics/Config.php' );
		include_once( WPTC_CLASSES_DIR.'Analytics/BackupAnalytics.php' );

		include_once( WPTC_CLASSES_DIR.'ExcludeOption/init.php' );
		include_once( WPTC_CLASSES_DIR.'ExcludeOption/Hooks.php' );
		include_once( WPTC_CLASSES_DIR.'ExcludeOption/HooksHandler.php' );
		include_once( WPTC_CLASSES_DIR.'ExcludeOption/Config.php' );
		include_once( WPTC_CLASSES_DIR.'ExcludeOption/ExcludeOption.php' );

		include_once( WPTC_CLASSES_DIR.'Settings/init.php' );
		include_once( WPTC_CLASSES_DIR.'Settings/Hooks.php' );
		include_once( WPTC_CLASSES_DIR.'Settings/HooksHandler.php' );
		include_once( WPTC_CLASSES_DIR.'Settings/Config.php' );
		include_once( WPTC_CLASSES_DIR.'Settings/Settings.php' );

		include_once( WPTC_CLASSES_DIR.'AppFunctions/init.php' );
		include_once( WPTC_CLASSES_DIR.'AppFunctions/Hooks.php' );
		include_once( WPTC_CLASSES_DIR.'AppFunctions/HooksHandler.php' );
		include_once( WPTC_CLASSES_DIR.'AppFunctions/Config.php' );
		include_once( WPTC_CLASSES_DIR.'AppFunctions/AppFunctions.php' );

		include_once( WPTC_CLASSES_DIR.'InitialSetup/init.php' );
		include_once( WPTC_CLASSES_DIR.'InitialSetup/Hooks.php' );
		include_once( WPTC_CLASSES_DIR.'InitialSetup/HooksHandler.php' );
		include_once( WPTC_CLASSES_DIR.'InitialSetup/Config.php' );
		include_once( WPTC_CLASSES_DIR.'InitialSetup/InitialSetup.php' );

		if(is_wptc_server_req() || is_admin()) {
			WPTC_Base_Factory::get('Wptc_Base')->init();
		}
	}

	private function create_objects(){
		$this->stage_to_live = new WPTC_Stage_To_Live();
	}

	private function init_hooks(){
		add_action('wp_enqueue_scripts',           array($this,                'add_frontend_scripts'));
		add_action('admin_enqueue_scripts',           array($this,                'add_scripts'));
		add_action('wp_before_admin_bar_render',      array($this->stage_to_live, 'change_sitename'));
		add_action('init',                            array($this->stage_to_live, 'check_permissions'));
		add_action('wp_ajax_wptc_copy_stage_to_live', array($this->stage_to_live, 'to_live'));

		$exclude_class_obj = new Wptc_Exclude_Hooks_Handler($category = 'staging');

		add_action('wp_ajax_wptc_get_root_files',               array($exclude_class_obj, 'wptc_get_root_files'));
		add_action('wp_ajax_wptc_get_files_by_key',             array($exclude_class_obj, 'wptc_get_files_by_key'));
		add_action('wp_ajax_wptc_get_tables',                   array($exclude_class_obj, 'wptc_get_tables'));
		add_action('wp_ajax_exclude_file_list_wptc',            array($exclude_class_obj, 'exclude_file_list'));
		add_action('wp_ajax_include_file_list_wptc',            array($exclude_class_obj, 'include_file_list'));
		add_action('wp_ajax_exclude_table_list_wptc',           array($exclude_class_obj, 'exclude_table_list'));
		add_action('wp_ajax_include_table_list_wptc',           array($exclude_class_obj, 'include_table_list'));
		add_action('wp_ajax_include_table_structure_only_wptc', array($exclude_class_obj, 'include_table_structure_only'));

		$this->add_admin_menu_hook();

		$white_label_staging = new Wptc_White_Label_Staging();
		add_action('admin_init', array($white_label_staging,'admin_actions'));
	}

	private function add_admin_menu_hook(){

		$white_label_staging = new Wptc_White_Label_Staging();

		if ( is_multisite() ) {
			add_action('network_admin_menu', array($this, 'add_admin_menu_new'));
		} else{
			// add_action('admin_menu', array($this, 'add_admin_menu'));
			add_action('admin_menu', array($this, 'add_admin_menu_new'));
		}
	}

	public function add_frontend_scripts()	{

		if(is_windows_machine_wptc()){
			$site_url = site_url();
			$wp_content = basename(WPTC_WP_CONTENT_DIR);
			$plugin_dir = $site_url . '/' . $wp_content . '/' . 'plugins';
		} else {
			$plugin_dir = plugins_url();
		}

		wptc_log('', "--------add_frontend_scripts--------");
		wp_enqueue_style('wptc-s2l-css',              $plugin_dir . '/' . basename(dirname(__FILE__)) . '/css/wptc-s2l.css',                  array(), WPTC_VERSION);
	}

	public function add_scripts(){

		if(is_windows_machine_wptc()){
			$site_url = site_url();
			$wp_content = basename(WPTC_WP_CONTENT_DIR);
			$plugin_dir = $site_url . '/' . $wp_content . '/' . 'plugins';
		} else {
			$plugin_dir = plugins_url();
		}

		wp_enqueue_script('wptc-staging-js',          $plugin_dir . '/' . basename(dirname(__FILE__)) . '/js/wptc-staging.js',                array(), WPTC_VERSION);
		wp_enqueue_script('wptc-jquery-ui-custom-js', $plugin_dir . '/' . basename(dirname(__FILE__)) . '/treeView/jquery-ui.custom.js',   array(), WPTC_VERSION);
		wp_enqueue_script('wptc-fancytree-js',        $plugin_dir . '/' . basename(dirname(__FILE__)) . '/treeView/jquery.fancytree.js',   array(), WPTC_VERSION);
		wp_enqueue_style('wptc-fancytree-css',        $plugin_dir . '/' . basename(dirname(__FILE__)) . '/treeView/skin/ui.fancytree.css', array(), WPTC_VERSION);
		wp_enqueue_script('wptc-filetree-common-js',  $plugin_dir . '/' . basename(dirname(__FILE__)) . '/treeView/common.js',              array(), WPTC_VERSION);
		wp_enqueue_style('wptc-s2l-css',              $plugin_dir . '/' . basename(dirname(__FILE__)) . '/css/wptc-s2l.css',                  array(), WPTC_VERSION);
		wp_enqueue_style('wptc-css',                  $plugin_dir . '/' . basename(dirname(__FILE__)) . '/wp-time-capsule.css',               array(), WPTC_VERSION);
		wp_enqueue_style('wptc-ui-css',               $plugin_dir . '/' . basename(dirname(__FILE__)) . '/tc-ui.css',                         array(), WPTC_VERSION);
		$this->add_nonce();
	}

	public function add_nonce(){
		$params = array(
			'ajax_nonce' => wp_create_nonce('wptc_nonce'),
			'admin_url'  => network_admin_url(),
		);
		wp_localize_script( 'wptc-staging-js', 'wptc_ajax_object', $params );
	}

	public function add_admin_menu_with_this_name($name = 'WPTC') {
		$text = __($name . ' Staging', 'wp-time-capsule-staging');

		if($name == 'WPTC'){
			add_menu_page($text, $text, 'activate_plugins', 'wp-time-capsule-staging', array($this, 'staging_page'), 'dashicons-wptc', '80.0564');
		} else {
			add_menu_page($text, $text, 'activate_plugins', 'wp-time-capsule-staging', array($this, 'staging_page'), 'dashicons-cloud', '80.0564');
		}

	}

	public function add_admin_menu() {
		$text = __('WPTC Staging', 'wp-time-capsule-staging');
		add_menu_page($text, $text, 'activate_plugins', 'wp-time-capsule-staging', array($this, 'staging_page'), 'dashicons-wptc', '80.0564');
	}

	public function add_admin_menu_new(){
		$white_label_staging = new Wptc_White_Label_Staging();
		$settings = $white_label_staging->get_settings();

		if(empty($settings)){

			$this->add_admin_menu_with_this_name();

			return false;
		}

		if($white_label_staging->validate_users_access_wptc() === 'authorized') {

			wptc_log(array(),'-----------athorized 5----------------');

			$this->add_admin_menu_with_this_name();

			return false;
		}

		if ( !empty($settings->wl_select_action) && 
			( $settings->wl_select_action == 'hide_details' || $settings->wl_select_action == 'normal') ) {
			$this->add_admin_menu_with_this_name();
		} else if ( $settings->wl_select_action == 'change_details' && !empty($settings->wl_select_action) ) {
			$this->add_admin_menu_with_this_name($settings->plugin_name);
		}
	}

	public function staging_page() {
		$stage_to_live = $this->stage_to_live;
		include_once 'views/wp-time-capsule-staging.php';
	}

	private function define( $name, $value ) {
		if ( ! defined( $name ) ) {
			define( $name, $value );
		}
	}
}

new WP_Time_Capsule_Staging();
