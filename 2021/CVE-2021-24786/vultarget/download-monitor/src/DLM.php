<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

use \Never5\DownloadMonitor\Util;

/**
 * WP_DLM class.
 *
 * Main plugin class
 */
class WP_DLM {

	private $services = null;

	/**
	 * Get the plugin file
	 *
	 * @return String
	 */
	public function get_plugin_file() {
		return DLM_PLUGIN_FILE;
	}

	/**
	 * Get plugin path
	 *
	 * @return string
	 */
	public function get_plugin_path() {
		return plugin_dir_path( $this->get_plugin_file() );
	}

	/**
	 * Get plugin URL
	 *
	 * @return string
	 */
	public function get_plugin_url() {
		return plugins_url( basename( plugin_dir_path( $this->get_plugin_file() ) ), basename( $this->get_plugin_file() ) );
	}

	/**
	 * Return requested service
	 *
	 * @param string $key
	 *
	 * @return mixed
	 */
	public function service( $key ) {
		return $this->services->get( $key );
	}

	/**
	 * __construct function.
	 *
	 * @access public
	 */
	public function __construct() {
		global $wpdb;

		// Setup Services
		$this->services = new DLM_Services();

		// Load plugin text domain
		load_textdomain( 'download-monitor', WP_LANG_DIR . '/download-monitor/download_monitor-' . get_locale() . '.mo' );
		load_plugin_textdomain( 'download-monitor', false, dirname( plugin_basename( DLM_PLUGIN_FILE ) ) . '/languages' );

		// Table for logs
		$wpdb->download_log = $wpdb->prefix . 'download_log';

		// Setup admin classes
		if ( is_admin() ) {

			// check if multisite and needs to create DB table

			// Setup admin scripts
			$admin_scripts = new DLM_Admin_Scripts();
			$admin_scripts->setup();

			// Setup Main Admin Class
			$dlm_admin = new DLM_Admin();
			$dlm_admin->setup();

			// setup custom labels
			$custom_labels = new DLM_Custom_Labels();
			$custom_labels->setup();

			// setup custom columns
			$custom_columns = new DLM_Custom_Columns();
			$custom_columns->setup();

			// setup custom actions
			$custom_actions = new DLM_Custom_Actions();
			$custom_actions->setup();

			// Admin Write Panels
			new DLM_Admin_Writepanels();

			// Admin Media Browser
			new DLM_Admin_Media_Browser();

			// Admin Media Insert
			new DLM_Admin_Media_Insert();

			// Upgrade Manager
			$upgrade_manager = new DLM_Upgrade_Manager();
			$upgrade_manager->setup();

			// Legacy Upgrader
			$lu_page = new DLM_LU_Page();
			$lu_page->setup();

			$lu_ajax = new DLM_LU_Ajax();
			$lu_ajax->setup();

			// Onboarding
			$onboarding = new Util\Onboarding();
			$onboarding->setup();
		}

		// Setup AJAX handler if doing AJAX
		if ( defined( 'DOING_AJAX' ) ) {
			new DLM_Ajax_Handler();
		}

		// Setup new AJAX handler
		$ajax_manager = new DLM_Ajax_Manager();
		$ajax_manager->setup();

		// Functions
		require_once( $this->get_plugin_path() . 'includes/download-functions.php' );

		// Deprecated
		require_once( $this->get_plugin_path() . 'includes/deprecated.php' );

		// Setup DLM Download Handler
		$download_handler = new DLM_Download_Handler();
		$download_handler->setup();

		// setup no access page endpoints
		$no_access_page_endpoint = new DLM_Download_No_Access_Page_Endpoint();
		$no_access_page_endpoint->setup();

		// Setup shortcodes
		$dlm_shortcodes = new DLM_Shortcodes();
		$dlm_shortcodes->setup();

		// Setup Widgets
		$widget_manager = new DLM_Widget_Manager();
		$widget_manager->setup();

		// Setup Taxonomies
		$taxonomy_manager = new DLM_Taxonomy_Manager();
		$taxonomy_manager->setup();

		// Setup Post Types
		$post_type_manager = new DLM_Post_Type_Manager();
		$post_type_manager->setup();

		// Setup Log Filters
		$log_filters = new DLM_Log_Filters();
		$log_filters->setup();

		// Setup actions
		$this->setup_actions();

		// Setup Search support
		$search = new DLM_Search();
		$search->setup();

		// Setup Gutenberg
		$gutenberg = new DLM_Gutenberg();
		$gutenberg->setup();

		// Setup Gutenberg Download Preview
		$gb_download_preview = new DLM_DownloadPreview_Preview();
		$gb_download_preview->setup();

		// Setup integrations
		$this->setup_integrations();

		// check if we need to bootstrap E-Commerce
		if ( apply_filters( 'dlm_shop_load_bootstrap', true ) ) {
			require_once( $this->get_plugin_path() . 'src/Shop/bootstrap.php' );
		}

	}

	/**
	 * Setup actions
	 */
	private function setup_actions() {
		add_filter( 'plugin_action_links_' . plugin_basename( DLM_PLUGIN_FILE ), array( $this, 'plugin_links' ) );
		add_action( 'init', array( $this, 'register_globals' ) );
		add_action( 'after_setup_theme', array( $this, 'compatibility' ), 20 );
		add_action( 'the_post', array( $this, 'setup_download_data' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'frontend_scripts' ) );

		// setup product manager
		DLM_Product_Manager::get()->setup();
	}

	/**
	 * Setup 3rd party integrations
	 */
	private function setup_integrations() {
		$yoast = new DLM_Integrations_YoastSEO();
		$yoast->setup();

		$post_types_order = new DLM_Integrations_PostTypesOrder();
		$post_types_order->setup();
	}

	/**
	 * Add Theme Compatibility
	 *
	 * @access public
	 * @return void
	 */
	public function compatibility() {

		// Post thumbnail support
		if ( ! current_theme_supports( 'post-thumbnails' ) ) {
			add_theme_support( 'post-thumbnails' );
			remove_post_type_support( 'post', 'thumbnail' );
			remove_post_type_support( 'page', 'thumbnail' );
		} else {

			// Get current supported
			$current_support = get_theme_support( 'post-thumbnails' );

			// fix current support for some themes
			if ( is_array( $current_support ) && is_array( $current_support[0] ) ) {
				$current_support = $current_support[0];
			}

			// This can be a bool or array. If array we merge our post type in, if bool ignore because it's like a global theme setting.
			if ( is_array( $current_support ) ) {
				add_theme_support( 'post-thumbnails', array_merge( $current_support, array( 'dlm_download' ) ) );
			}

			add_post_type_support( 'download', 'thumbnail' );
		}
	}

	/**
	 * Add links to admin plugins page.
	 *
	 * @param  array $links
	 *
	 * @return array
	 */
	public function plugin_links( $links ) {
		$plugin_links = array(
			'<a href="' . DLM_Admin_Settings::get_url() . '">' . __( 'Settings', 'download-monitor' ) . '</a>',
			'<a href="https://www.download-monitor.com/extensions/?utm_source=plugin&utm_medium=plugins-page&utm_campaign=plugin-link-extensions">' . __( 'Extensions', 'download-monitor' ) . '</a>',
			'<a href="https://www.download-monitor.com/kb/?utm_source=plugin&utm_medium=plugins-page&utm_campaign=plugin-link-documentation">' . __( 'Documentation', 'download-monitor' ) . '</a>',
		);

		return array_merge( $plugin_links, $links );
	}

	/**
	 * frontend_scripts function.
	 *
	 * @access public
	 * @return void
	 */
	public function frontend_scripts() {
		if ( apply_filters( 'dlm_frontend_scripts', true ) ) {
			wp_enqueue_style( 'dlm-frontend', $this->get_plugin_url() . '/assets/css/frontend.css' );
		}

		// only enqueue preview stylesheet when we're in the preview
		if ( isset( $_GET['dlm_gutenberg_download_preview'] ) ) {
			// Enqueue admin css
			wp_enqueue_style(
				'dlm_preview',
				plugins_url( '/assets/css/preview.css', $this->get_plugin_file() ),
				array(),
				DLM_VERSION
			);
		}

		do_action( 'dlm_frontend_scripts_after' );

	}

	/**
	 * Register environment globals
	 *
	 * @access private
	 * @return void
	 */
	public function register_globals() {
		$GLOBALS['dlm_download'] = null;
	}

	/**
	 * When the_post is called, get download data too
	 *
	 * @access public
	 *
	 * @param mixed $post
	 *
	 * @return void
	 */
	public function setup_download_data( $post ) {
		if ( is_int( $post ) ) {
			$post = get_post( $post );
		}

		if ( $post->post_type !== 'dlm_download' ) {
			return;
		}

		try {
			$download                = $this->service( 'download_repository' )->retrieve_single( $post->ID );
			$GLOBALS['dlm_download'] = $download;
		} catch ( Exception $e ) {

		}
	}

	/** Deprecated methods **************************************************/

	/**
	 * get_template_part function.
	 *
	 * @deprecated 1.6.0
	 *
	 * @access public
	 *
	 * @param mixed $slug
	 * @param string $name (default: '')
	 *
	 * @return void
	 */
	public function get_template_part( $slug, $name = '', $custom_dir = '' ) {

		// Deprecated
		DLM_Debug_Logger::deprecated( __METHOD__ );

		// Load template part
		$template_handler = new DLM_Template_Handler();
		$template_handler->get_template_part( $slug, $name, $custom_dir );
	}

	/**
	 * Get the plugin url
	 *
	 * @deprecated 1.6.0
	 *
	 * @access public
	 * @return string
	 */
	public function plugin_url() {

		// Deprecated
		DLM_Debug_Logger::deprecated( __METHOD__ );

		return $this->get_plugin_url();
	}

	/**
	 * Get the plugin path
	 *
	 * @deprecated 1.6.0
	 *
	 * @access public
	 * @return string
	 */
	public function plugin_path() {

		// Deprecated
		DLM_Debug_Logger::deprecated( __METHOD__ );

		return $this->get_plugin_path();
	}

	/**
	 * Returns a listing of all files in the specified folder and all subdirectories up to 100 levels deep.
	 * The depth of the recursiveness can be controlled by the $levels param.
	 *
	 * @deprecated 1.6.0
	 *
	 * @access public
	 *
	 * @param string $folder (default: '')
	 *
	 * @return array|bool
	 */
	public function list_files( $folder = '' ) {

		// Deprecated
		DLM_Debug_Logger::deprecated( __METHOD__ );

		// File Manger
		$file_manager = new DLM_File_Manager();

		// Return files
		return $file_manager->list_files( $folder );
	}

	/**
	 * Parse a file path and return the new path and whether or not it's remote
	 *
	 * @deprecated 1.6.0
	 *
	 * @param  string $file_path
	 *
	 * @return array
	 */
	public function parse_file_path( $file_path ) {

		// Deprecated
		DLM_Debug_Logger::deprecated( __METHOD__ );

		// File Manger
		$file_manager = new DLM_File_Manager();

		// Return files
		return $file_manager->parse_file_path( $file_path );
	}

	/**
	 * Gets the filesize of a path or URL.
	 *
	 * @deprecated 1.6.0
	 *
	 * @param string $file_path
	 *
	 * @access public
	 * @return string size on success, -1 on failure
	 */
	public function get_filesize( $file_path ) {

		// Deprecated
		DLM_Debug_Logger::deprecated( __METHOD__ );

		// File Manger
		$file_manager = new DLM_File_Manager();

		// Return files
		return $file_manager->get_file_size( $file_path );
	}

	/**
	 * Gets md5, sha1 and crc32 hashes for a file and store it.
	 *
	 * @deprecated 1.6.0
	 *
	 * @string $file_path
	 *
	 * @access public
	 * @return array of sizes
	 */
	public function get_file_hashes( $file_path ) {

		// Deprecated
		DLM_Debug_Logger::deprecated( __METHOD__ );

		// Return files
		return $this->service( 'hasher' )->get_file_hashes( $file_path );
	}

	/**
	 * Encode files for storage
	 *
	 * @deprecated 1.6.0
	 *
	 * @param  array $files
	 *
	 * @return string
	 */
	public function json_encode_files( $files ) {

		// Deprecated
		DLM_Debug_Logger::deprecated( __METHOD__ );

		// File Manger
		$file_manager = new DLM_File_Manager();

		// Return files
		return $file_manager->json_encode_files( $files );
	}

	/**
	 * Fallback for PHP < 5.4 where JSON_UNESCAPED_UNICODE does not exist.
	 *
	 * @deprecated 1.6.0
	 *
	 * @param  array $matches
	 *
	 * @return string
	 */
	public function json_unscaped_unicode_fallback( $matches ) {

		// Deprecated
		DLM_Debug_Logger::deprecated( __METHOD__ );

		// File Manger
		$file_manager = new DLM_File_Manager();

		// Return files
		return $file_manager->json_unscaped_unicode_fallback( $matches );
	}

}