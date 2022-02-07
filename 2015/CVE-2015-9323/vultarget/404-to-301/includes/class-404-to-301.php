<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die('Damn it.! Dude you are looking for what?');
}
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the dashboard.
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @link       http://iscode.co/product/404-to-301/
 * @since      2.0.0
 * @package    I4T3
 * @subpackage I4T3/includes
 * @author     Joel James <me@joelsays.com>
 */
class _404_To_301 {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    2.0.0
	 * @access   protected
	 * @var      _404_To_301_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    2.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    2.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;
	
	/**
	 * The database table of plugin.
	 *
	 * @since    2.0.0
	 * @access   protected
	 * @var      string    $table    The plugin table name in db.
	 */
	protected $table;
	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name, plugin version and the plugin table name that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the Dashboard and
	 * the public-facing side of the site.
	 *
	 * @since    2.0.0
	 */
	public function __construct() {

		$this->plugin_name = '404-to-301';
		$this->version = '2.0.2';
		$this->table = $GLOBALS['wpdb']->prefix . '404_to_301';
		$this->load_dependencies();
		$this->define_admin_hooks();
		$this->define_handler_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - _404_To_301_Loader. Orchestrates the hooks of the plugin.
	 * - _404_To_301_Admin. Defines all hooks for the dashboard.
	 * - _404_To_301_Public. Defines all hooks for the public functions.
	 * - _404_To_301_Logs. Defines all hooks for listing logs.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    2.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-404-to-301-loader.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-404-to-301-admin.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-404-to-301-logs.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-404-to-301-public.php';
		
		$this->loader = new _404_To_301_Loader();
	}


	/**
	 * Register all of the hooks related to the dashboard functionality
	 * of the plugin.
	 * This function is used to register all styles and JavaScripts for admin side.
	 *
	 * @since    2.0.0
	 * @access   private
	 * @uses 	add_action() and add_filter()
	 */
	private function define_admin_hooks() {

		$plugin_admin = new _404_To_301_Admin( $this->get_plugin_name(), $this->get_version(), $this->get_table() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'i4t3_create_404_to_301_menu');
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'i4t3_rename_plugin_menu' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'i4t3_options_register' );
		$this->loader->add_filter( 'admin_footer_text', $plugin_admin, 'i4t3_dashboard_footer');
		$this->loader->add_filter( 'plugin_action_links', $plugin_admin, 'i4t3_plugin_action_links', 10, 5 );
		$this->loader->add_action( 'plugins_loaded', $plugin_admin, 'i4t3_upgrade_if_new' );
	}

	
	/**
	 * Register all of the hooks related to handle 404 actions of the plugin.
	 *
	 * @since   2.0.0
	 * @access  private
	 * @uses 	add_filter()
	 */
	private function define_handler_hooks() {
		
		$plugin_public = new _404_To_301_Public( $this->get_plugin_name(), $this->get_version(), $this->get_table() );
		// Main Hook to perform redirections on 404s
		$this->loader->add_filter( 'template_redirect', $plugin_public, 'i4t3_redirect_404' );
	}
	
	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    2.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     2.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     2.0.0
	 * @return    i4t3_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     2.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * Retrieve the table name of the plugin.
	 *
	 * @since     2.0.0
	 * @return    string    The table name of the plugin.
	 */
	public function get_table() {
		return $this->table;
	}
}