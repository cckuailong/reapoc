<?php
/* "Copyright 2012 a3 Revolution Web Design" This software is distributed under the terms of GNU GENERAL PUBLIC LICENSE Version 3, 29 June 2007 */

namespace A3Rev\PageViewsCount\FrameWork\Pages {

use A3Rev\PageViewsCount\FrameWork;

// File Security Check
if ( ! defined( 'ABSPATH' ) ) exit;

/*-----------------------------------------------------------------------------------
WP PVC Admin Page

TABLE OF CONTENTS

- var menu_slug
- var page_data

- __construct()
- page_init()
- page_data()
- add_admin_menu()
- tabs_include()
- admin_settings_page()

-----------------------------------------------------------------------------------*/

class Settings extends FrameWork\Admin_UI
{
	/**
	 * @var string
	 */
	private $menu_slug = 'a3-pvc';
	
	/**
	 * @var array
	 */
	private $page_data;
	
	/*-----------------------------------------------------------------------------------*/
	/* __construct() */
	/* Settings Constructor */
	/*-----------------------------------------------------------------------------------*/
	public function __construct() {
		$this->page_init();
		$this->tabs_include();
	}
	
	/*-----------------------------------------------------------------------------------*/
	/* page_init() */
	/* Page Init */
	/*-----------------------------------------------------------------------------------*/
	public function page_init() {
		
		add_filter( $this->plugin_name . '_add_admin_menu', array( $this, 'add_admin_menu' ) );
	}
	
	/*-----------------------------------------------------------------------------------*/
	/* page_data() */
	/* Get Page Data */
	/*-----------------------------------------------------------------------------------*/
	public function page_data() {
		
		$page_data = array(
			'type'				=> 'submenu',
			'parent_slug'		=> 'options-general.php',
			'page_title'		=> __( 'Page Views Count', 'page-views-count' ),
			'menu_title'		=> __( 'Page Views Count', 'page-views-count' ),
			'capability'		=> 'manage_options',
			'menu_slug'			=> $this->menu_slug,
			'function'			=> 'wp_pvc_admin_page_show',
			'admin_url'			=> 'options-general.php',
			'callback_function' => 'wp_pvc_callback_settings_page_show',
			'script_function' 	=> '',
			'view_doc'			=> '',
		);
		
		if ( $this->page_data ) return $this->page_data;
		return $this->page_data = $page_data;
		
	}
	
	/*-----------------------------------------------------------------------------------*/
	/* add_admin_menu() */
	/* Add This page to menu on left sidebar */
	/*-----------------------------------------------------------------------------------*/
	public function add_admin_menu( $admin_menu ) {
		
		if ( ! is_array( $admin_menu ) ) $admin_menu = array();
		$admin_menu[] = $this->page_data();
		
		return $admin_menu;
	}
	
	/*-----------------------------------------------------------------------------------*/
	/* tabs_include() */
	/* Include all tabs into this page
	/*-----------------------------------------------------------------------------------*/
	public function tabs_include() {
		global $wp_pvc_general_tab;
		$wp_pvc_general_tab = new FrameWork\Tabs\Global_Settings();
	}
	
	/*-----------------------------------------------------------------------------------*/
	/* admin_settings_page() */
	/* Show Settings Page */
	/*-----------------------------------------------------------------------------------*/
	public function admin_settings_page() {		
		$GLOBALS[$this->plugin_prefix.'admin_init']->admin_settings_page( $this->page_data() );
	}
	
	/*-----------------------------------------------------------------------------------*/
	/* admin_settings_page() */
	/* Show Settings Page */
	/*-----------------------------------------------------------------------------------*/
	public function callback_admin_settings_page() {
		global $wp_pvc_general_settings;
		
		$this->plugin_extension_start();
		$wp_pvc_general_settings->settings_form();
		$this->plugin_extension_end();
	}
}

}

// global code
namespace {

/** 
 * wp_pvc_admin_page_show()
 * Define the callback function to show page content
 */
function wp_pvc_admin_page_show() {
	global $wp_pvc_admin_page;
	$wp_pvc_admin_page->admin_settings_page();
}

function wp_pvc_callback_settings_page_show() {
	global $wp_pvc_admin_page;
	$wp_pvc_admin_page->callback_admin_settings_page();
}

}
