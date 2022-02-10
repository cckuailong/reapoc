<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://ays-pro.com/
 * @since      1.0.0
 *
 * @package    Secure_Copy_Content_Protection
 * @subpackage Secure_Copy_Content_Protection/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Secure_Copy_Content_Protection
 * @subpackage Secure_Copy_Content_Protection/includes
 * @author     Security Team <info@ays-pro.com>
 */
class Secure_Copy_Content_Protection {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Secure_Copy_Content_Protection_Loader $loader Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $plugin_name The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $version The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if (defined('SCCP_NAME_VERSION')) {
			$this->version = SCCP_NAME_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'secure-copy-content-protection';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Secure_Copy_Content_Protection_Loader. Orchestrates the hooks of the plugin.
	 * - Secure_Copy_Content_Protection_i18n. Defines internationalization functionality.
	 * - Secure_Copy_Content_Protection_Admin. Defines all hooks for the admin area.
	 * - Secure_Copy_Content_Protection_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {
		if (!class_exists('WP_List_Table')) {
            require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
        }
		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-secure-copy-content-protection-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-secure-copy-content-protection-i18n.php';

		/*
         * The class is responsible for showing sccp results in wordpress default WP_LIST_TABLE style
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/lists/class-secure-copy-content-protection-results-list-table.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-secure-copy-content-protection-admin.php';
		require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-secure-copy-content-protection-actions.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/subscribe/actions/secure-copy-content-protection-subscribe-actions.php';

		/**
         * The class is responsible for showing poll settings
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/settings/secure-copy-content-protection-settings-actions.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-secure-copy-content-protection-public.php';

		$this->loader = new Secure_Copy_Content_Protection_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Secure_Copy_Content_Protection_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Secure_Copy_Content_Protection_i18n();

		$this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Secure_Copy_Content_Protection_Admin($this->get_plugin_name(), $this->get_version());

		// Add menu item
		$this->loader->add_action('admin_menu', $plugin_admin, 'add_plugin_admin_menu');

		$this->loader->add_action( 'admin_head', $plugin_admin, 'admin_menu_styles' );
		$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
		$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');

		$this->loader->add_action('wp_ajax_deactivate_sccp_option_sccp', $plugin_admin, 'deactivate_sccp_option');
		$this->loader->add_action('wp_ajax_nopriv_deactivate_sccp_option_sccp', $plugin_admin, 'deactivate_sccp_option');

		// EXPORT FILTERS
        $this->loader->add_action( 'wp_ajax_ays_sccp_show_filters', $plugin_admin, 'ays_sccp_show_filters' );
        $this->loader->add_action( 'wp_ajax_nopriv_ays_sccp_show_filters', $plugin_admin, 'ays_sccp_show_filters' );

		$this->loader->add_action( 'wp_ajax_ays_sccp_results_export_file', $plugin_admin, 'ays_sccp_results_export_file' );
        $this->loader->add_action( 'wp_ajax_nopriv_ays_sccp_results_export_file', $plugin_admin, 'ays_sccp_results_export_file' );

        $this->loader->add_action( 'wp_ajax_ays_sccp_results_export_filter', $plugin_admin, 'ays_sccp_results_export_filter' );
        $this->loader->add_action( 'wp_ajax_nopriv_ays_sccp_results_export_filter', $plugin_admin, 'ays_sccp_results_export_filter' );

		// Add Settings link to the plugin
		$plugin_basename = plugin_basename(plugin_dir_path(__DIR__) . $this->plugin_name . '.php');
		$this->loader->add_filter('plugin_action_links_' . $plugin_basename, $plugin_admin, 'add_action_links');

		// Add Row meta link to the plugin
		$this->loader->add_filter( 'plugin_row_meta', $plugin_admin, 'add_plugin_row_meta',10 ,2 );

		$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'codemirror_enqueue_scripts');

		$this->loader->add_action( 'in_admin_footer', $plugin_admin, 'sccp_admin_footer', 1 );
		
		// $this->loader->add_action( 'admin_notices', $plugin_admin, 'ays_sccp_sale_baner', 1 );
	}



	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Secure_Copy_Content_Protection_Public($this->get_plugin_name(), $this->get_version());


		$this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
		$this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');
		$this->loader->add_action('wp_footer', $plugin_public, 'ays_get_notification_text');
		// $this->loader->add_action('wp_loaded', $plugin_public, 'ays_block_all_page');

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @return    string    The name of the plugin.
	 * @since     1.0.0
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @return    Secure_Copy_Content_Protection_Loader    Orchestrates the hooks of the plugin.
	 * @since     1.0.0
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @return    string    The version number of the plugin.
	 * @since     1.0.0
	 */
	public function get_version() {
		return $this->version;
	}

}
