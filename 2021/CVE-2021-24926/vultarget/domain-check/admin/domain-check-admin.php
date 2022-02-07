<?php

//wp-plugin can be a url, no no!
if (!defined('ABSPATH') && php_sapi_name() !== 'cli') {
	die();
}

if (!function_exists('is_admin') || !is_admin()) {
	die();
}

require_once( dirname(__FILE__) . '/domain-check-admin-ajax.php' );
require_once( dirname(__FILE__) . '/domain-check-admin-coupons.php' );
require_once( dirname(__FILE__) . '/domain-check-admin-dashboard.php' );
require_once( dirname(__FILE__) . '/domain-check-admin-header.php' );
require_once( dirname(__FILE__) . '/domain-check-admin-help.php' );
require_once( dirname(__FILE__) . '/domain-check-admin-import-export.php' );
require_once( dirname(__FILE__) . '/domain-check-admin-profile.php' );
require_once( dirname(__FILE__) . '/domain-check-admin-search.php' );
require_once( dirname(__FILE__) . '/domain-check-admin-settings.php' );
require_once( dirname(__FILE__) . '/domain-check-admin-ssl-profile.php' );
require_once( dirname(__FILE__) . '/domain-check-admin-ssl-search.php' );
require_once( dirname(__FILE__) . '/domain-check-admin-ssl-watch.php' );
require_once( dirname(__FILE__) . '/domain-check-admin-watch.php' );
require_once( dirname(__FILE__) . '/domain-check-admin-your-domains.php' );


if (php_sapi_name() !== 'cli') {
	require_once( dirname(__FILE__) . '/domain-check-admin-search-list.php' );
	require_once( dirname(__FILE__) . '/domain-check-admin-ssl-search-list.php' );
	require_once( dirname(__FILE__) . '/domain-check-admin-ssl-watch-list.php' );
	require_once( dirname(__FILE__) . '/domain-check-admin-watch-list.php' );
	require_once( dirname(__FILE__) . '/domain-check-admin-your-domains-list.php' );
}

//define wp-plugin class, must be self contained!
if(!class_exists('DomainCheckAdmin')) {
	class DomainCheckAdmin
	{

		const PLUGIN_CLASSNAME = 'DomainCheck';
		const PLUGIN_NAME = 'domain-check';
		const PLUGIN_OPTION_PREFIX = 'domain_check';
		const PLUGIN_TEXTNAME = 'Domain Check';

		private $m_pluginData = array();

		public $domains_obj;
		public $your_domains_obj;

		public $customers_obj;

		static $instance;

		static $admin_notices = array();

		static $ajax_nonce = null;

		static $scheduled_event_name = 'domain_check_cron';

		static $admin_icon = 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4NCjwhLS0gR2VuZXJhdG9yOiBBZG9iZSBJbGx1c3RyYXRvciAxOS4wLjAsIFNWRyBFeHBvcnQgUGx1Zy1JbiAuIFNWRyBWZXJzaW9uOiA2LjAwIEJ1aWxkIDApICAtLT4NCjxzdmcgdmVyc2lvbj0iMS4xIiBpZD0iTGF5ZXJfMSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgeD0iMHB4IiB5PSIwcHgiDQoJIHZpZXdCb3g9Ii0yOTUgMjk3IDggOCIgc3R5bGU9ImVuYWJsZS1iYWNrZ3JvdW5kOm5ldyAtMjk1IDI5NyA4IDg7IiB4bWw6c3BhY2U9InByZXNlcnZlIj4NCjxwYXRoIGQ9Ik0tMjkxLDI5Ny4xYy0yLjIsMC0zLjksMS44LTMuOSwzLjlzMS44LDMuOSwzLjksMy45czMuOS0xLjgsMy45LTMuOVMtMjg4LjgsMjk3LjEtMjkxLDI5Ny4xeiBNLTI5Mi44LDMwMS45aC0wLjRsLTAuMy0xLjENCglsLTAuMywxLjFoLTAuNGwtMC41LTEuOGgwLjRsMC4zLDEuMmwwLjMtMS4yaDAuNGwwLjMsMS4ybDAuMy0xLjJoMC40TC0yOTIuOCwzMDEuOXogTS0yOTAuMywzMDEuOWgtMC40bC0wLjMtMS4xbC0wLjMsMS4xaC0wLjQNCglsLTAuNS0xLjhoMC40bDAuMywxLjJsMC4zLTEuMmgwLjRsMC4zLDEuMmwwLjMtMS4yaDAuNEwtMjkwLjMsMzAxLjl6IE0tMjg4LjIsMzAxLjlsLTAuMy0xLjFsLTAuMywxLjFoLTAuNGwtMC41LTEuOGgwLjRsMC4zLDEuMg0KCWwwLjMtMS4yaDAuNGwwLjMsMS4ybDAuMy0xLjJoMC40bC0wLjUsMS44SC0yODguMnoiLz4NCjwvc3ZnPg0K';

		public function __construct()
		{
			//********** WORDPRESS ADMIN HOOKS *******************
			if (function_exists('add_action')) {
				add_action( 'admin_init', array(&$this, 'admin_init') );
				add_action( 'admin_enqueue_scripts', array(&$this, 'admin_enqueue_scripts') );
				add_action( 'admin_menu', array(&$this, 'admin_menu') );
				add_action( 'admin_notices', array(&$this, 'admin_notices') );


				add_action( 'wp_ajax_watch_trigger', array(&$this, 'watch_trigger' ) );

				add_action( 'wp_ajax_status_trigger', array(&$this, 'status_trigger' ) );

				add_action( 'wp_ajax_autorenew_trigger', array(&$this, 'autorenew_trigger' ) );

				add_action( 'wp_ajax_ssl_watch_trigger', array(&$this, 'ssl_watch_trigger' ) );

				add_action( 'wp_ajax_domain_search', array(&$this, 'ajax_domain_search' ) );

				add_action( 'wp_ajax_settings', array(&$this, 'ajax_settings' ) );
			}

			//filters
			if (function_exists('add_filter')) {
				add_filter( 'set-screen-option', array( __CLASS__, 'set_screen' ), 10, 3 );
				add_filter( 'plugin_action_links_' . DomainCheck::$basename, array( $this, 'add_action_links' ), 10, 2 );

				//add_filter( 'plugin_action_links_domaincheck', array( $this, 'add_action_link' ), 10, 2 );
				//add_filter( 'plugin_action_links_domain-check', array( $this, 'add_action_link' ), 10, 2 );
			}
		}

		public function add_action_links( $links ) {
			$faq_link = '<a title="Help" href="' . esc_url( admin_url( 'admin.php?page=domain-check-help' ) ) . '">Help</a>';
			array_unshift( $links, $faq_link );

			$settings_link = '<a href="' . esc_url( admin_url( 'admin.php?page=domain-check-import-export' ) ) . '">Import</a>';
			array_unshift( $links, $settings_link );

			$settings_link = '<a href="' . esc_url( admin_url( 'admin.php?page=domain-check-search' ) ) . '">Search</a>';
			array_unshift( $links, $settings_link );

			$dashboard_link = '<a href="' . esc_url( admin_url( 'admin.php?page=domain-check' ) ) . '">Dashboard</a>';
			array_unshift( $links, $dashboard_link );

			return $links;
		}

		//includes the necessarry css and js files
		public function admin_enqueue_scripts($hook) {

		}

		//updated, error, update-nag
		public static function admin_notices_add($message = '', $type = 'updated', $options = null, $icon = null) {
			if ($message) {
				if ($icon) {
					$image = '<img src="' . plugins_url('/images/icons/color/' . $icon . '.svg', __FILE__) .'" class="svg svg-icon-admin-notice svg-fill-' . $type . '">';
					$message = $image . $message;
					$message = '<h2>' . $message . '</h2>';
				}
				if ($options && is_array($options) && count($options)) {
					$message .= '<span class="hidden-mobile"><br><br></span>' . "\n";
					$first = true;
					foreach($options as $option_name => $option_url) {
						if (!$first) {
							$message .= ' | ';
						}
						if ($option_name == 'Launch [&raquo]') {
							$message .= '<a href="http://'.$option_url.'" target="_blank">'.$option_name.'</a>';
						} else if ($option_name == '<img src="' . plugins_url('domain-check/images/icons/external-link.svg') . '" class="svg svg-h2 svg-fill-gray">') {
							$message .= '<a href="http://'.$option_url.'" target="_blank">'.$option_name.'</a>';
						} else {
							$message .= '<a href="'.$option_url.'">'.$option_name.'</a>';
						}
						if ($first) {
							$first = false;
						}
					}
				}
				self::$admin_notices[] = array('message' => $message, 'type' => $type);
			}
		}

		public function admin_notices() {
			foreach (self::$admin_notices as $admin_notice_idx => $admin_notice_data) {
				if ($admin_notice_data['type'] !== 'updated'
					&& $admin_notice_data['type'] !== 'error'
					&& $admin_notice_data['type'] !== 'update-nag'
				) {
					$admin_notice_data['type'] = 'updated notice-' . $admin_notice_data['type'];
				}
				?>
			<div class="<?php echo $admin_notice_data['type']; ?> domain-check-notice">
				<p><?php echo $admin_notice_data['message']; ?></p>
			</div>
		<?php
			}
		}

		public function admin_header($nav = true) {
			DomainCheckAdminHeader::admin_header($nav);
		}

		function admin_header_nav() {
			DomainCheckAdminHeader::admin_header_nav();
		}

		public function admin_init() {
			global $wpdb;

			self::$ajax_nonce = wp_create_nonce('domain_check_ajax_nonce');

			//capability
			$role = get_role( 'administrator' );
			$role->add_cap( DomainCheck::CAPABILITY );

			$domain_data = null;
			$domain_extension = null;

			//search
			if ( isset( $_GET['domain_check_search'] ) ) {
				DomainCheckSearch::domain_search($_GET['domain_check_search']);
			}

			if ( isset( $_GET['domain_check_your_domains'] ) ) {
				DomainCheckSearch::domain_search( $_GET['domain_check_your_domains'], false, true, true);
			}

			if ( isset( $_GET['domain_check_watch'] ) ) {
				DomainCheckSearch::domain_search( $_GET['domain_check_watch'], false, false, true);
				DomainCheckAdmin::admin_notices_add('Started watching <strong>' . $_GET['domain_check_watch'] . '</strong>!', 'updated', null, '208-eye-plus');
			}

			//SSL CHECK!!!
			if ( isset( $_GET['domain_check_ssl_search'] ) && strpos( $_GET['domain_check_ssl_search'], '.' ) !== false ) {
				$this->ssl_check_init();
			}

			if ( isset( $_GET['domain_check_ssl_watch'] ) && strpos( $_GET['domain_check_ssl_watch'], '.' ) !== false ) {
				DomainCheckSearch::ssl_search( $_GET['domain_check_ssl_watch'], true );
				DomainCheckAdmin::admin_notices_add('Started watching SSL expiration for <strong>' . $_GET['domain_check_ssl_watch'] . '</strong>!', 'updated', null, '208-eye-plus');
			}

			//domain delete
			if ( isset($_GET['domain_check_delete']) && strpos( $_GET['domain_check_delete'], '.' ) !== false ) {
				$this->delete_init();
			}

			//domain status
			if ( isset($_GET['domain_check_status_owned']) && strpos( $_GET['domain_check_status_owned'], '.' ) !== false ) {
				$this->status_owned($_GET['domain_check_status_owned']);
			}
			if ( isset($_GET['domain_check_status_taken']) && strpos( $_GET['domain_check_status_taken'], '.' ) !== false ) {
				$this->status_taken($_GET['domain_check_status_taken']);
			}

			//domain watch
			if ( isset($_GET['domain_check_watch_start']) && strpos( $_GET['domain_check_watch_start'], '.' ) !== false ) {
				$this->watch_start($_GET['domain_check_watch_start']);
			}
			if ( isset($_GET['domain_check_watch_stop']) && strpos( $_GET['domain_check_watch_stop'], '.' ) !== false ) {
				$this->watch_stop($_GET['domain_check_watch_stop']);
			}
			if ( isset($_POST['watch_email_add']) ) {
				$this->watch_email_add($_GET['domain'], $_POST['watch_email_add']);
			}
			if ( isset($_POST['profile_settings_update']) ) {
				$this->profile_settings_update($_GET['domain']);
			}

			//ssl delete
			if ( isset($_GET['domain_check_ssl_delete']) && strpos( $_GET['domain_check_ssl_delete'], '.' ) !== false ) {
				$this->ssl_delete_init();
			}

			//ssl watch
			if ( isset($_GET['domain_check_ssl_watch_start']) && strpos( $_GET['domain_check_ssl_watch_start'], '.' ) !== false ) {
				$this->ssl_watch_start($_GET['domain_check_ssl_watch_start']);
			}
			if ( isset($_GET['domain_check_ssl_watch_stop']) && strpos( $_GET['domain_check_ssl_watch_stop'], '.' ) !== false ) {
				$this->ssl_watch_stop($_GET['domain_check_ssl_watch_stop']);
			}
			if ( isset($_POST['ssl_watch_email_add']) ) {
				$this->ssl_watch_email_add($_GET['domain'], $_POST['ssl_watch_email_add']);
			}
			if ( isset($_POST['ssl_profile_settings_update']) ) {
				$this->ssl_profile_settings_update($_GET['domain']);
			}

			//coupons
			if ( isset($_GET['domain_check_coupons_search']) && $_GET['domain_check_coupons_search']) {
				DomainCheckAdminCoupons::coupons_init($_GET['domain_check_coupons_search']);
			}
			if ( isset($_GET['domain_check_coupons_update']) && $_GET['domain_check_coupons_update']) {
				DomainCheckAdminCoupons::coupons_init();
			}

		}

		public function admin_menu() {
			$hook = add_menu_page(
				'Domains',
				'Domains',
				'manage_options',
				'domain-check',
				array(
					$this,
					'dashboard'
				),
				DomainCheckAdmin::$admin_icon, //'dashicons-admin-site'
				'81.9987654321'
			);
			//add_action( 'load-' . $hook, array( $this, 'screen_option' ) );

			//add submenu items
			$hook = add_submenu_page(
				'domain-check',
				'Your Domains', //__('Fancy Products', MyStyleWpPlugin::PLUGIN_NAME),
				'Your Domains', //__('Fancy Products', MyStyleWpPlugin::PLUGIN_NAME),
				DomainCheck::CAPABILITY,
				'domain-check-your-domains',
				array(
					$this,
					'your_domains'
				)
			);
			add_action( 'load-' . $hook, array( $this, 'your_domains_screen_option' ) );

			//add submenu items
			$hook = add_submenu_page(
				'domain-check',
				'Domain Search', //__('Fancy Products', MyStyleWpPlugin::PLUGIN_NAME),
				'Domain Search', //__('Fancy Products', MyStyleWpPlugin::PLUGIN_NAME),
				DomainCheck::CAPABILITY,
				'domain-check-search',
				array(
					$this,
					'search'
				)
			);
			add_action( 'load-' . $hook, array( $this, 'search_screen_option' ) );

			//add fancy designs sub menu page to products menu
			$hook = add_submenu_page(
				'domain-check',
				'Domain Watch',
				'Domain Watch',
				DomainCheck::CAPABILITY,
				'domain-check-watch',
				array(
					$this,
					'watch'
				)
			);
			add_action( 'load-' . $hook, array( $this, 'watch_screen_option' ) );

			//add fancy designs sub menu page to products menu
			$hook = add_submenu_page(
				'domain-check',
				'SSL Check',
				'SSL Check',
				DomainCheck::CAPABILITY,
				'domain-check-ssl-check',
				array(
					$this,
					'ssl_check'
				)
			);
			add_action( 'load-' . $hook, array( $this, 'ssl_check_screen_option' ) );

			//add fancy designs sub menu page to products menu
			$hook = add_submenu_page(
				'domain-check',
				'SSL Expiration Alerts',
				'SSL Expiration Alerts',
				DomainCheck::CAPABILITY,
				'domain-check-ssl-watch',
				array(
					$this,
					'ssl_watch'
				)
			);
			add_action( 'load-' . $hook, array( $this, 'ssl_watch_screen_option' ) );

			/*
			//add fancy designs sub menu page to products menu
			add_submenu_page(
				'domain-check',
				'Hosting Check',
				'Hosting Check',
				DomainCheck::CAPABILITY,
				'domain-check-hosting-check',
				array(
					$this,
					'hosting_check'
				)
			);

			//add fancy designs sub menu page to products menu
			add_submenu_page(
				'domain-check',
				'Hosting Alerts',
				'Hosting Alerts',
				DomainCheck::CAPABILITY,
				'domain-check-hosting-alerts',
				array(
					$this,
					'hosting_alerts'
				)
			);
			*/

			//add fancy designs sub menu page to products menu
			add_submenu_page(
				'domain-check',
				'Import / Export',
				'Import / Export',
				DomainCheck::CAPABILITY,
				'domain-check-import-export',
				array(
					$this,
					'import_export'
				)
			);

			//add fancy designs sub menu page to products menu
			add_submenu_page(
				'domain-check',
				'Settings',
				'Settings',
				DomainCheck::CAPABILITY,
				'domain-check-settings',
				array(
					$this,
					'settings'
				)
			);

			//add fancy designs sub menu page to products menu
			add_submenu_page(
				'domain-check',
				'Help',
				'Help',
				DomainCheck::CAPABILITY,
				'domain-check-help',
				array(
					$this,
					'help'
				)
			);

			//add fancy designs sub menu page to products menu
			add_submenu_page(
				'domain-check',
				'Coupons & Deals',
				'Coupons & Deals',
				DomainCheck::CAPABILITY,
				'domain-check-coupons',
				array(
					$this,
					'coupons'
				)
			);

			if (class_exists('DomainCheckProAdmin')) {
				DomainCheckProAdmin::admin_menu_static();
			}

			//pages without a submenu link (domain profile mostly...)
			//domain profile
			add_submenu_page(
				'domain-check',
				'',
				'',
				DomainCheck::CAPABILITY,
				'domain-check-profile',
				array(
					$this,
					'profile'
				)
			);

			//ssl profile
			add_submenu_page(
				'domain-check',
				'',
				'',
				DomainCheck::CAPABILITY,
				'domain-check-ssl-profile',
				array(
					$this,
					'ssl_profile'
				)
			);

			//ssl profile
			/*
			add_submenu_page(
				'domain-check',
				'',
				'',
				DomainCheck::CAPABILITY,
				'domain-check-watch-profile',
				array(
					$this,
					'watch_profile'
				)
			);

			global $submenu;
			if ( isset( $submenu['domain-check'] ) ) {
				$submenu['domain-check'][0][0] = 'General';
			}
			*/
		}

		public function ajax_domain_search($action = null, $domain = null) {
			DomainCheckAdminAjax::ajax_domain_search($action, $domain);
		}

		public function ajax_settings() {
			DomainCheckAdminAjax::ajax_settings();
		}

		public static function ajax_success($data) {
			DomainCheckAdminAjax::ajax_success($data);
		}

		public static function ajax_error($message, $code = 0) {
			DomainCheckAdminAjax::ajax_error($message, $code);
		}

		public function autorenew_trigger( $domain, $ajax = 0 ) {
			DomainCheckAdminProfile::autorenew_trigger($domain, $ajax);
		}

		public function bulk_domain_delete($domain_urls) {
			global $wpdb;
			foreach ($domain_urls as $domain) {
				$wpdb->delete(
					DomainCheck::$db_prefix . '_domains',
					array(
						'domain_url' => $domain
					)
				);
			}
			$message = 'Success! You deleted <strong>' . count($domain_urls) . '</strong> domains!';
			DomainCheckAdmin::admin_notices_add($message, 'updated', null, '174-bin2');
		}

		public function bulk_domain_watch($domain_urls, $watch = 1) {
			global $wpdb;
			foreach ($domain_urls as $domain) {
				$wpdb->update(
					DomainCheck::$db_prefix . '_domains',
					array(
						'domain_watch' => $watch
					),
					array(
						'domain_url' => $domain
					)
				);
			}
			$watch_text = 'started';
			$icon = '208-eye-plus';
			if (!$watch) {
				$watch_text = 'stopped';
				$icon = '209-eye-minus';
			}
			$message = 'Success! You ' . $watch_text . ' watching <strong>' . count($domain_urls) . '</strong> domains!';
			DomainCheckAdmin::admin_notices_add($message, 'updated', null, $icon);
		}

		public function bulk_domain_watch_stop($domain_urls) {
			$this->bulk_domain_watch($domain_urls, 0);
		}

		public function bulk_ssl_delete($domain_urls) {
			global $wpdb;
			foreach ($domain_urls as $domain) {
				$wpdb->delete(
					DomainCheck::$db_prefix . '_ssl',
					array(
						'domain_url' => $domain
					)
				);
			}
			$message = 'Success! You deleted <strong>' . count($domain_urls) . '</strong> SSL certificates!';
			DomainCheckAdmin::admin_notices_add($message, 'updated', null, '174-bin2');
		}

		public function bulk_ssl_watch($domain_urls, $watch = 1) {
			global $wpdb;
			foreach ($domain_urls as $domain) {
				$wpdb->update(
					DomainCheck::$db_prefix . '_ssl',
					array(
						'domain_watch' => $watch
					),
					array(
						'domain_url' => $domain
					)
				);
			}
			$watch_text = 'started';
			$icon = '208-eye-plus';
			if (!$watch) {
				$watch_text = 'stopped';
				$icon = '209-eye-minus';
			}
			$message = 'Success! You ' . $watch_text . ' watching <strong>' . count($domain_urls) . '</strong> SSL certificates!';
			DomainCheckAdmin::admin_notices_add($message, 'updated', null, $icon);
		}

		public function bulk_ssl_watch_stop($domain_urls) {
			$this->bulk_ssl_watch($domain_urls, 0);
		}

		public static function callInstance($method, $args) {
			self::$instance->{$method}($args);
		}

		public function coupons() {
			DomainCheckAdminCoupons::coupons();
		}

		public function dashboard() {
			DomainCheckAdminDashboard::dashboard();
		}

		public function delete_init() {
			global $wpdb;

			$domain = strtolower($_GET['domain_check_delete']);

			if (!isset($_GET['domain_check_delete_confirm'])) {
				$message = 'Are you sure you want to delete <strong> ' . $domain . ' </strong>? It will no longer be watched and may expire! This cannot be undone.';
				$message_options = array(
					'Delete' => '?page=domain-check-search&domain_check_delete=' . $domain . '&domain_check_delete_confirm=' . $domain,
					'Cancel' => '?page=domain-check-search'
				);
				DomainCheckAdmin::admin_notices_add($message, 'error', $message_options, '174-bin2');
			} else {
				if ($_GET['domain_check_delete_confirm'] == $_GET['domain_check_delete']) {
					$wpdb->delete(
						DomainCheck::$db_prefix . '_domains',
						array(
							'domain_url' => $domain
						)
					);
					$message = 'Success! You deleted <strong>' . $domain . '</strong>!';
					DomainCheckAdmin::admin_notices_add($message, 'updated', null, '174-bin2');
				}
			}
		}

		public function domain_check() {
			$this->dashboard();
		}

		public function domain_search($domain, $use_cache = false, $force_owned = false, $force_watch = false, $ajax = false) {
			DomainCheckSearch::domain_search($domain, $use_cache, $force_owned, $force_watch, $ajax);
		}

		public function domain_url_extension($url) {
			return DomainCheckWhois::dolookup($url);
		}

		public function domain_url_validate($url) {}

		/** Singleton instance */
		public static function get_instance() {
			if ( ! isset( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		public function help() {
			DomainCheckAdminHelp::help();
		}

		public function import_export() {
			DomainCheckAdminImportExport::import_export();
		}

		function profile() {
			DomainCheckAdminProfile::profile();
		}

		function profile_settings_update($domain) {
			DomainCheckAdminProfile::profile_settings_update($domain);
		}

		public function search() {
			DomainCheckAdminSearch::search();
		}

		public function search_box() {
			DomainCheckAdminSearch::search_box();
		}

		public function search_init() {
			DomainCheckAdminSearch::search_init();
		}

		/**
		 * Screen options
		 */
		public function search_screen_option() {
			DomainCheckAdminSearch::search_screen_option();
		}

		/**
		 * Screen options
		 */
		public function screen_option() {
			$option = 'per_page';
			$args   = array(
				'label'   => 'Domains',
				'default' => 100,
				'option'  => 'domains_per_page'
			);

			add_screen_option( $option, $args );

			$this->domains_obj = new DomainCheck_Domains_List();
		}

		public static function set_screen( $status, $option, $value ) {
			return $value;
		}

		public function settings() {
			DomainCheckAdminSettings::settings();
		}

		public function ssl_check_init() {
			DomainCheckAdminSslSearch::ssl_check_init();
		}

		public function ssl_check() {
			DomainCheckAdminSslSearch::ssl_check();
		}

		/**
		 * Screen options
		 */
		public static function ssl_check_screen_option() {
			DomainCheckAdminSslSearch::ssl_check_screen_option();
		}

		public function ssl_delete_init() {
			DomainCheckAdminSslSearch::ssl_delete_init();
		}

		public static function ssl_search_box($dashboard = false) {
			DomainCheckAdminSslSearch::ssl_search_box($dashboard);
		}

		public function ssl_profile() {
			DomainCheckAdminSslProfile::ssl_profile();
		}

		function ssl_profile_settings_update($domain) {
			DomainCheckAdminSslProfile::ssl_profile_settings_update($domain);
		}

		function ssl_watch() {
			DomainCheckAdminSslWatch::ssl_watch();
		}

		public function ssl_watch_email_add($domain, $watch_email_content) {
			DomainCheckAdminSslWatch::ssl_watch_email_add($domain, $watch_email_content);
		}

		/**
		 * Screen options
		 */
		public function ssl_watch_screen_option() {
			DomainCheckAdminSslWatch::ssl_watch_screen_option();
		}

		public function ssl_watch_start($domain) {
			DomainCheckAdminSslWatch::ssl_watch_start($domain);
		}

		public function ssl_watch_stop($domain) {
			DomainCheckAdminSslWatch::ssl_watch_stop($domain);
		}

		public function ssl_watch_trigger($domain, $ajax = 0) {
			DomainCheckAdminSslWatch::ssl_watch_trigger($domain, $ajax);
		}

		public function watch() {
			DomainCheckAdminWatch::watch();
		}

		public function watch_email_add($domain, $watch_email_content) {
			DomainCheckAdminWatch::watch_email_add($domain, $watch_email_content);
		}

		public function watch_profile() {
			DomainCheckAdminWatch::watch_profile();
		}

		public function watch_start($domain) {
			DomainCheckAdminWatch::watch_start($domain);
		}

		public function watch_stop($domain) {
			DomainCheckAdminWatch::watch_stop($domain);
		}

		public function watch_trigger($domain, $ajax = 0) {
			DomainCheckAdminWatch::watch_trigger($domain, $ajax);
		}

		/**
				 * Screen options
				 */
		public function watch_screen_option() {
			DomainCheckAdminWatch::watch_screen_option();
		}

		public function status_owned($domain) {
			$this->status_update($domain, 'owned');
		}

		public function status_taken($domain) {
			$this->status_update($domain, 'taken');
		}

		public function status_trigger($domain) {
			global $wpdb;

			if (isset($_POST['domain'])) {
				$ajax = 1;
				$domain = strtolower($_POST['domain']);
			}

			$domain = strtolower($domain);

			$sql = 'SELECT * FROM ' . DomainCheck::$db_prefix . '_domains WHERE domain_url = "' . strtolower($domain) . '"';
			$result = $wpdb->get_results( $sql, 'ARRAY_A' );
			if ( count ( $result ) ) {
				$result = array_pop($result);
				$new_status = 0;
				if ($result['status'] == 1) {
					$new_status = 2;
				} else if ($result['status'] == 2) {
					$new_status = 1;
				}
				if ($new_status == 1 || $new_status == 2) {
					$wpdb->update(
						DomainCheck::$db_prefix . '_domains',
						array(
							'status' => $new_status
						),
						array(
							'domain_url' => $domain
						)
					);

					$message_start = '<strong>' . $domain . '</strong> marked as Owned!';
					$message_stop = '<strong>' . $domain . '</strong> marked as Taken!';

					if (!$ajax) {
						if ($new_status) {
							DomainCheckAdmin::admin_notices_add($message_start, 'owned', null, 'flag');
						} else {
							DomainCheckAdmin::admin_notices_add($message_stop, 'error', null, 'taken');
						}
					} else {
						DomainCheckAdmin::ajax_success(
							$data = array(
								'status' => $new_status,
								'message' => ($new_status == 2 ? $message_start : $message_stop),
								'domain' => $domain
							)
						);
					}
				}
			}
		}

		public function status_update($domain, $new_status) {
			global $wpdb;

			$status_name = ucfirst($new_status);
			$notice_type = 'owned';
			$icon = 'flag';

			switch ($new_status) {
				case 'owned':
				case 2:
					$new_status = 2;
					break;
				case 'registered':
				case 'taken':
				case 1:
					$new_status = 1;
					$notice_type = 'error';
					$icon = 'ban';
					break;
				default:
					DomainCheckAdmin::admin_notices_add('Status not allowed.', 'error', null, 'circle-x');
					return;
			}

			$domain = strtolower($domain);

			$wpdb->update(
				DomainCheck::$db_prefix . '_domains',
				array(
					'status' => $new_status
				),
				array (
					'domain_url' => $domain
				)
			);

			$message = 'Updated <strong>' . $domain . '</strong> status to <strong>' . $status_name . '</strong>!';
			DomainCheckAdmin::admin_notices_add(
				$message,
				$notice_type,
				null,
				$icon
			);
		}

		public function your_domains() {
			DomainCheckAdminYourDomains::your_domains();
		}

		/**
		 * Screen options
		 */
		public function your_domains_screen_option() {
			DomainCheckAdminYourDomains::your_domains_screen_option();
		}

		public function your_domains_search_box() {
			DomainCheckAdminYourDomains::your_domains_search_box();
		}

	}

	//fire things up bruh...
	if (function_exists('add_action')) {
		add_action(
			'plugins_loaded',
			function () {
				DomainCheckAdmin::$instance = new DomainCheckAdmin();
			}
		);
	}
}