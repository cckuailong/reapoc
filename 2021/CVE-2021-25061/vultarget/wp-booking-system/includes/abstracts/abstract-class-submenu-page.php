<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Base class to add custom submenu pages
 *
 */
abstract class WPBS_Submenu_Page {

	/**
	 * The menu page under which the submenu page should be added
	 *
	 * @access protected
	 * @var    string
	 *
	 */
	protected $parent_slug;


	/**
	 * The title of the submenu page
	 *
	 * @access protected
	 * @var    string
	 *
	 */
	protected $page_title;


	/**
	 * The title that should appear in the menu 
	 *
	 * @access protected
	 * @var    string
	 *
	 */
	protected $menu_title;


	/**
	 * The user capability required to view this page
	 *
	 * @access protected
	 * @var    string
	 *
	 */
	protected $capability;


	/**
	 * The menu
	 *
	 * @access protected
	 * @var    string
	 *
	 */
	protected $menu_slug;

	/**
	 * The current subpage url query arg
	 *
	 * @access protected
	 * @var    string
	 *
	 */
	protected $current_subpage;

	/**
	 * The current tab url query arg
	 *
	 * @access protected
	 * @var    string
	 *
	 */
	protected $current_tab;

	/**
	 * The admin path to the page, used in admin_url
	 *
	 * @access protected
	 * @var    string
	 *
	 */
	protected $admin_url;

	/**
	 * A list with admin notices to be printed on the page
	 *
	 * @access protected
	 * @var    string
	 *
	 */
	protected $admin_notices = array();


	/**
	 * Constructor
	 *
	 */
	public function __construct( $page_title = '', $menu_title = '', $capability = '', $menu_slug = '' ) {

		$this->parent_slug = 'wp-booking-system';
		$this->page_title  = $page_title;
		$this->menu_title  = $menu_title;
		$this->capability  = $capability;
		$this->menu_slug   = $menu_slug;

		$this->current_subpage = ( ! empty( $_GET['subpage'] ) ? $_GET['subpage'] : '' );
		$this->current_tab     = ( ! empty( $_GET['tab'] ) ? $_GET['tab'] : '' );

		add_action( 'admin_menu', array( $this, 'add_submenu_page' ) );

		$this->init();

	}


	/**
	 * Helper init method to avoid rewriting of the __construct method by subclasses
	 *
	 */
	protected function init() {}


	/**
	 * Getter
	 *
	 * @param string $property
	 *
	 */
	public function get( $property = '' ) {

		if( method_exists( $this, 'get_' . $property ) )
			return $this->{'get_' . $property}();
		else
			return $this->$property;

	}
	

	/**
	 * Callback to add the submenu page
	 *
	 */
	public function add_submenu_page() {

		$hook_sufix = add_submenu_page( $this->parent_slug, $this->page_title, $this->menu_title, $this->capability, $this->menu_slug, array( $this, 'output' ) );

		if( $hook_sufix ) {

			$this->admin_url = add_query_arg( array( 'page' => $this->menu_slug ), 'admin.php' );

		}

	}


	/**
	 * Callback for the HTML output for the page
	 *
	 */
	public function output() {}

}