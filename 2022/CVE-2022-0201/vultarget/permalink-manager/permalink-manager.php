<?php

/**
* Plugin Name:       Permalink Manager Lite
* Plugin URI:        https://permalinkmanager.pro?utm_source=plugin
* Description:       Advanced plugin that allows to set-up custom permalinks (bulk editors included), slugs and permastructures (WooCommerce compatible).
* Version:           2.2.14
* Author:            Maciej Bis
* Author URI:        http://maciejbis.net/
* License:           GPL-2.0+
* License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
* Text Domain:       permalink-manager
* Domain Path:       /languages
* WC requires at least: 3.0.0
* WC tested up to:      5.6.0
*/

// If this file is called directly or plugin is already defined, abort.
if (!defined('WPINC')) {
	die;
}

if(!class_exists('Permalink_Manager_Class')) {

	// Define the directories used to load plugin files.
	define( 'PERMALINK_MANAGER_PLUGIN_NAME', 'Permalink Manager' );
	define( 'PERMALINK_MANAGER_PLUGIN_SLUG', 'permalink-manager' );
	define( 'PERMALINK_MANAGER_VERSION', '2.2.14' );
	define( 'PERMALINK_MANAGER_FILE', __FILE__ );
	define( 'PERMALINK_MANAGER_DIR', untrailingslashit(dirname(__FILE__)) );
	define( 'PERMALINK_MANAGER_BASENAME', plugin_basename(__FILE__));
	define( 'PERMALINK_MANAGER_URL', untrailingslashit( plugins_url('', __FILE__) ) );
	define( 'PERMALINK_MANAGER_WEBSITE', 'http://permalinkmanager.pro?utm_source=plugin' );
	define( 'PERMALINK_MANAGER_DONATE', 'https://www.paypal.me/Bismit' );

	class Permalink_Manager_Class {

		public $permalink_manager_options_page, $permalink_manager_options;
		public $sections, $functions, $permalink_manager_before_sections_html, $permalink_manager_after_sections_html;

		/**
		* Get options from DB, load subclasses & hooks
		*/
		public function __construct() {
			$this->include_subclassess();
			$this->register_init_hooks();
		}

		/**
		* Include back-end classess and set their instances
		*/
		function include_subclassess() {
			// WP_List_Table needed for post types & taxnomies editors
			if( ! class_exists( 'WP_List_Table' ) ) {
				require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
			}

			$classes = array(
				'core' => array(
					'helper-functions' => 'Permalink_Manager_Helper_Functions',
					'uri-functions' => 'Permalink_Manager_URI_Functions',
					'uri-functions-post' => 'Permalink_Manager_URI_Functions_Post',
					'uri-functions-tax' => 'Permalink_Manager_URI_Functions_Tax',
					'admin-functions' => 'Permalink_Manager_Admin_Functions',
					'actions' => 'Permalink_Manager_Actions',
					'third-parties' => 'Permalink_Manager_Third_Parties',
					'language-plugins' => 'Permalink_Manager_Language_Plugins',
					'core-functions' => 'Permalink_Manager_Core_Functions',
					'gutenberg' => 'Permalink_Manager_Gutenberg',
					'debug' => 'Permalink_Manager_Debug_Functions',
					'pro-functions' => 'Permalink_Manager_Pro_Functions'
				),
				'views' => array(
					'uri-editor' => 'Permalink_Manager_Uri_Editor',
					'tools' => 'Permalink_Manager_Tools',
					'permastructs' => 'Permalink_Manager_Permastructs',
					'settings' => 'Permalink_Manager_Settings',
					'debug' => 'Permalink_Manager_Debug',
					'pro-addons' => 'Permalink_Manager_Pro_Addons',
					'help' => 'Permalink_Manager_Help',
					'uri-editor-tax' => false,
					'uri-editor-post' => false
				)
			);

			// Load classes and set-up their instances
			foreach($classes as $class_type => $classes_array) {
				foreach($classes_array as $class => $class_name) {
					$filename = PERMALINK_MANAGER_DIR . "/includes/{$class_type}/permalink-manager-{$class}.php";

					if(file_exists($filename)) {
						require_once $filename;
						if($class_name) { $this->functions[$class] = new $class_name(); }
					}
				}
			}
		}

		/**
		* Register general hooks
		*/
		public function register_init_hooks() {
			// Localize plugin
			add_action( 'init', array($this, 'localize_me'), 1 );

			// Support deprecated hooks
			add_action( 'plugins_loaded', array($this, 'deprecated_hooks'), 9 );

			// Deactivate free version if Permalink Manager Pro is activated
			add_action( 'plugins_loaded', array($this, 'is_pro_activated'), 9 );

			// Load globals & options
			add_action( 'plugins_loaded', array($this, 'get_options_and_globals'), 9 );

			// Legacy support
			add_action( 'init', array($this, 'legacy_support'), 2 );

			// Default settings & alerts
			add_filter( 'permalink_manager_options', array($this, 'default_settings'), 1 );
			add_filter( 'permalink_manager_alerts', array($this, 'default_alerts'), 1 );
		}

		/**
		* Localize this plugin
		*/
		function localize_me() {
			load_plugin_textdomain( 'permalink-manager', false, basename(dirname(__FILE__)) . "/languages" );
		}

		/**
		* Get options values & set global
		*/
		public function get_options_and_globals() {
			// 1. Globals with data stored in DB
			global $permalink_manager_options, $permalink_manager_uris, $permalink_manager_permastructs, $permalink_manager_redirects, $permalink_manager_external_redirects;

			$this->permalink_manager_options = $permalink_manager_options = (array) apply_filters('permalink_manager_options', get_option('permalink-manager', array()));
			$this->permalink_manager_uris = $permalink_manager_uris = (array) apply_filters('permalink_manager_uris', get_option('permalink-manager-uris', array()));
			$this->permalink_manager_permastructs = $permalink_manager_permastructs = (array) apply_filters('permalink_manager_permastructs', get_option('permalink-manager-permastructs', array()));
			$this->permalink_manager_redirects = $permalink_manager_redirects = (array) apply_filters('permalink_manager_redirects', get_option('permalink-manager-redirects', array()));
			$this->permalink_manager_external_redirects = $permalink_manager_external_redirects = (array) apply_filters('permalink_manager_external_redirects', get_option('permalink-manager-external-redirects', array()));

			// 2. Globals used to display additional content (eg. alerts)
			global $permalink_manager_alerts, $permalink_manager_before_sections_html, $permalink_manager_after_sections_html;

			$this->permalink_manager_alerts = $permalink_manager_alerts = apply_filters('permalink_manager_alerts', get_option('permalink-manager-alerts', array()));
			$this->permalink_manager_before_sections_html = $permalink_manager_before_sections_html = apply_filters('permalink_manager_before_sections', '');
			$this->permalink_manager_after_sections_html = $permalink_manager_after_sections_html = apply_filters('permalink_manager_after_sections', '');
		}

		/**
		* Set the initial/default settings (including "Screen Options")
		*/
		public function default_settings($settings) {
			$default_settings = apply_filters('permalink_manager_default_options', array(
				'screen-options' => array(
					'per_page' => 20,
					'post_statuses' => array('publish'),
					'group' => false,
				),
				'general' => array(
					'auto_update_uris' => 0,
					'show_native_slug_field' => 0,
					'pagination_redirect' => 0,
					'sslwww_redirect' => 0,
					'canonical_redirect' => 1,
					'old_slug_redirect' => 0,
					'setup_redirects' => 0,
					'redirect' => '301',
					'extra_redirects' => 1,
					'copy_query_redirect' => 1,
					'trailing_slashes' => 0,
					'trailing_slash_redirect' => 0,
					'auto_fix_duplicates' => 0,
					'fix_language_mismatch' => 1,
					'pmxi_support' => 1,
					'um_support' => 1,
					'yoast_breadcrumbs' => 0,
					'primary_category' => 1,
					'force_custom_slugs' => 0,
					'disable_slug_sanitization' => 0,
					'keep_accents' => 0,
					'partial_disable' => array(
						'post_types' => array('attachment', 'tribe_events')
					),
					'deep_detect' => 1,
					'ignore_drafts' => 1,
					'edit_uris_cap' => 'publish_posts',
				),
				'licence' => array()
			));

			// Apply the default settings (if empty values) in all settings sections
			foreach($default_settings as $group_name => $fields) {
				foreach($fields as $field_name => $field) {
					if(!isset($settings[$group_name][$field_name]) && $field_name !== 'partial_disable') {
						$settings[$group_name][$field_name] = $field;
					}
				}
			}

			return $settings;
		}

		/**
		* Set the initial/default admin notices
		*/
		public function default_alerts($alerts) {
			$default_alerts = apply_filters('permalink_manager_default_alerts', array(
				'jan21' => array(
					'txt' => sprintf(
						__("Get access to extra features: full taxonomy and WooCommerce support, possibility to use custom fields inside the permalinks and more!<br /><strong>Buy Permalink Manager Pro <a href=\"%s\" target=\"_blank\">here</a> and save %s using \"%s\" coupon code!</strong> Valid until %s!", "permalink-manager"),
						PERMALINK_MANAGER_WEBSITE . "&utm_campaign=discount_code",
						'20&#37;',
						'NEWYEAR21',
						'09.01'
					),
					'type' => 'notice-info',
					'show' => 'pro_hide',
					'plugin_only' => true,
					'until' => '2021-01-09'
				)
			));

			// Apply the default settings (if empty values) in all settings sections
			return (array) $alerts + (array) $default_alerts;
		}

		/**
		 * Temporary hook
		 */
		function legacy_support() {
			global $permalink_manager_permastructs, $permalink_manager_options;

			if(isset($permalink_manager_options['base-editor'])) {
				$new_options['post_types'] = $permalink_manager_options['base-editor'];
				update_option('permalink-manager-permastructs', $new_options);
			}
			else if(empty($permalink_manager_permastructs['post_types']) && empty($permalink_manager_permastructs['taxonomies']) && count($permalink_manager_permastructs) > 0) {
				$new_options['post_types'] = $permalink_manager_permastructs;
				update_option('permalink-manager-permastructs', $new_options);
			}

			// Adjust options structure
			if(!empty($permalink_manager_options['miscellaneous'])) {
				// Combine general & miscellaneous options
				$permalink_manager_unfiltered_options['general'] = array_merge($permalink_manager_unfiltered_options['general'], $permalink_manager_unfiltered_options['miscellaneous']);

				// Move licence key to different section
				$permalink_manager_unfiltered_options['licence']['licence_key'] = (!empty($permalink_manager_unfiltered_options['miscellaneous']['license_key'])) ? $permalink_manager_unfiltered_options['miscellaneous']['license_key'] : "";
			}

			// Separate "Trailing slashes" & "Trailing slashes redirect" setting fields
			if(!empty($permalink_manager_options['general']['trailing_slashes']) && $permalink_manager_options['general']['trailing_slashes'] >= 10) {
				$permalink_manager_unfiltered_options = (!empty($permalink_manager_unfiltered_options)) ? $permalink_manager_unfiltered_options : $permalink_manager_options;

				$permalink_manager_unfiltered_options['general']['trailing_slashes_redirect'] = 1;
				$permalink_manager_unfiltered_options['general']['trailing_slashes'] = ($permalink_manager_options['general']['trailing_slashes'] == 10) ? 1 : 2;
			}

			// Adjust WP All Import suport mode
			if(isset($permalink_manager_options['general']['pmxi_import_support'])) {
				$permalink_manager_unfiltered_options = (!empty($permalink_manager_unfiltered_options)) ? $permalink_manager_unfiltered_options : $permalink_manager_options;

				$permalink_manager_unfiltered_options['general']['pmxi_support'] = (empty($permalink_manager_options['general']['pmxi_import_support'])) ? 1 : 0;
				unset($permalink_manager_unfiltered_options['general']['pmxi_import_support']);
			}

			// Save the settings in database
			if(!empty($permalink_manager_unfiltered_options)) {
				update_option('permalink-manager', $permalink_manager_unfiltered_options);
			}
		}

		/**
		 * Support deprecated hooks
		 */
		function deprecated_hooks_list($filters = true) {
			$deprecated_filters = array(
				'permalink_manager_default_options' => 'permalink-manager-default-options',
				'permalink_manager_options' => 'permalink-manager-options',
				'permalink_manager_uris' => 'permalink-manager-uris',
				'permalink_manager_alerts' => 'permalink-manager-alerts',
				'permalink_manager_redirects' => 'permalink-manager-redirects',
				'permalink_manager_external_redirects' => 'permalink-manager-external-redirects',
				'permalink_manager_permastructs' => 'permalink-manager-permastructs',

				'permalink_manager_alerts' => 'permalink-manager-alerts',
				'permalink_manager_before_sections' => 'permalink-manager-before-sections',
				'permalink_manager_sections' => 'permalink-manager-sections',
				'permalink_manager_after_sections' => 'permalink-manager-after-sections',

				'permalink_manager_field_args' => 'permalink-manager-field-args',
				'permalink_manager_field_output' => 'permalink-manager-field-output',

				'permalink_manager_deep_uri_detect' => 'permalink-manager-deep-uri-detect',
				'permalink_manager_detect_uri' => 'permalink-manager-detect-uri',
				'permalink_manager_detected_element_id' => 'permalink-manager-detected-initial-id',
				'permalink_manager_detected_term_id' => 'permalink-manager-detected-term-id',
				'permalink_manager_detected_post_id' => 'permalink-manager-detected-post-id',

				'permalink_manager_primary_term' => 'permalink-manager-primary-term',
				'permalink_manager_disabled_post_types' => 'permalink-manager-disabled-post-types',
				'permalink_manager_disabled_taxonomies' => 'permalink-manager-disabled-taxonomies',
				'permalink_manager_endpoints' => 'permalink-manager-endpoints',
				'permalink_manager_filter_permalink_base' => 'permalink_manager-filter-permalink-base',
				'permalink_manager_force_lowercase_uris' => 'permalink-manager-force-lowercase-uris',

				'permalink_manager_uri_editor_extra_info' => 'permalink-manager-uri-editor-extra-info',
				'permalink_manager_debug_fields' => 'permalink-manager-debug-fields',
				'permalink_manager_permastructs_fields' => 'permalink-manager-permastructs-fields',
				'permalink_manager_settings_fields' => 'permalink-manager-settings-fields',
				'permalink_manager_tools_fields' => 'permalink-manager-tools-fields',

				'permalink_manager_uri_editor_columns' => 'permalink-manager-uri-editor-columns',
				'permalink_manager_uri_editor_column_content' => 'permalink-manager-uri-editor-column-content',

				'permalink_manager_redirect_shop_archive' => 'permalink-manager-redirect-shop-archive'
			);

			return ($filters) ? $deprecated_filters : array();
		}

		function deprecated_hooks() {
			$deprecated_filters = (array) $this->deprecated_hooks_list(true);
			foreach($deprecated_filters as $new => $old) {
				add_filter($new, array($this, 'deprecated_hooks_mapping'), -1000, 8);
			}
		}

		function deprecated_hooks_mapping($data) {
			$deprecated_filters = $this->deprecated_hooks_list(true);
			$filter = current_filter();

			if(isset($deprecated_filters[$filter])) {
				if(has_filter($deprecated_filters[$filter])) {
					$args = func_get_args();
					$data = apply_filters_ref_array($deprecated_filters[$filter], $args);
				}
			}

			return $data;
		}

		/**
		 * Deactivate Permalink Manager Lite if Permalink Manager Pro is enabled
		 */
		function is_pro_activated() {
			if(function_exists('is_plugin_active') && is_plugin_active('permalink-manager/permalink-manager.php') && is_plugin_active('permalink-manager-pro/permalink-manager.php')) {
				deactivate_plugins('permalink-manager/permalink-manager.php');
			}
		}

	}

	/**
	* Begins execution of the plugin.
	*/
	function run_permalink_manager() {
		global $permalink_manager;

		// Do not run when Elementor is opened
		if((!empty($_REQUEST['action']) && strpos($_REQUEST['action'], 'elementor') !== false) || isset($_REQUEST['elementor-preview'])) { return; }

		$permalink_manager = new Permalink_Manager_Class();
	}

	run_permalink_manager();
}
