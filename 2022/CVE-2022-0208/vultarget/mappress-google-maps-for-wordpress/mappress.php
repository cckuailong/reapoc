<?php
/*
Plugin Name: MapPress Maps for WordPress
Plugin URI: https://www.mappresspro.com
Author URI: https://www.mappresspro.com
Description: MapPress makes it easy to add Google and Leaflet Maps to WordPress
Version: 2.73.3
Author: Chris Richardson
Text Domain: mappress-google-maps-for-wordpress
Thanks to all the translators and to Scott DeJonge for his wonderful icons
*/

/*
	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the license.txt file for details.
*/

require_once dirname( __FILE__ ) . '/mappress_obj.php';
require_once dirname( __FILE__ ) . '/mappress_poi.php';
require_once dirname( __FILE__ ) . '/mappress_map.php';
require_once dirname( __FILE__ ) . '/mappress_settings.php';
include_once dirname( __FILE__ ) . '/mappress_template.php';
include_once dirname( __FILE__ ) . '/mappress_wpml.php';

if (is_dir(dirname( __FILE__ ) . '/pro')) {
	include_once dirname( __FILE__ ) . '/pro/mappress_filter.php';
	include_once dirname( __FILE__ ) . '/pro/mappress_frontend.php';
	include_once dirname( __FILE__ ) . '/pro/mappress_geocoder.php';
	include_once dirname( __FILE__ ) . '/pro/mappress_icons.php';
	include_once dirname( __FILE__ ) . '/pro/mappress_meta.php';
	include_once dirname( __FILE__ ) . '/pro/mappress_query.php';
	include_once dirname( __FILE__ ) . '/pro/mappress_updater.php';
	include_once dirname( __FILE__ ) . '/pro/mappress_widget.php';
	include_once dirname( __FILE__ ) . '/pro/mappress_widget_map.php';
}

class Mappress {
	const VERSION = '2.73.3';

	static
		$baseurl,
		$basename,
		$basedir,
		$block_category = 'text',
		$debug,
		$loaded,
		$options,
		$notices,
		$pages,
		$pro,
		$updater,
		$version
		;

	function __construct()  {
		global $wp_version;
		self::$basedir = dirname(__FILE__);
		self::$basename = plugin_basename(__FILE__);
		self::$baseurl = plugins_url('', __FILE__);
		self::$options = Mappress_Options::get();
		self::$pro = is_dir(dirname( __FILE__ ) . '/pro');
		self::$version = (self::$pro) ? self::VERSION . "PRO" : self::VERSION;
		self::$version = (defined('MAPPRESS_DEV') && MAPPRESS_DEV) ? self::$version . '-' . rand(0,99999) : self::$version;

		self::debugging();

		// Pro updater
		if (self::$pro)
			self::$updater = new Mappress_Updater(self::$basename, 'mappress', self::VERSION, self::$options->license, self::$options->betas);

		add_action('admin_menu', array(__CLASS__, 'admin_menu'));
		add_action('init', array(__CLASS__, 'init'), 0);	// Priority 0 required for widgets_init hook
		add_action('plugins_loaded', array(__CLASS__, 'plugins_loaded'));

		add_shortcode('mappress', array(__CLASS__, 'shortcode_map'));
		add_action('admin_notices', array(__CLASS__, 'admin_notices'));

		// Filter to automatically add maps to post/page content
		add_filter('the_content', array(__CLASS__, 'the_content'), 2);

		// Namespace
		add_action('wp_head', array(__CLASS__, 'wp_head'), 0);
		add_action('admin_head', array(__CLASS__, 'wp_head'), 0);

		// Scripts and stylesheets
		add_action('wp_enqueue_scripts', array(__CLASS__, 'wp_enqueue_scripts'));
		add_action('admin_enqueue_scripts', array(__CLASS__, 'admin_enqueue_scripts'));

		// Plugin action links
		add_filter("plugin_action_links_" . self::$basename, array(__CLASS__, 'plugin_action_links'), 10, 2);

		if (self::$pro)
			add_shortcode('mashup', array(__CLASS__, 'shortcode_mashup'));

		// Remove API loaded by other plugins
		add_filter('script_loader_tag', array(__CLASS__, 'script_loader_tag'), PHP_INT_MAX, 3);

		// Slow heartbeat
		if (self::$debug)
			add_filter( 'heartbeat_settings', array(__CLASS__, 'heartbeat_settings'));

		// Dismissible notices
		add_action('wp_ajax_mapp_dismiss', array(__CLASS__, 'ajax_dismiss' ));

		// Add block category
		if ( version_compare( $wp_version, '5.8-RC4', '>=' ) )
			add_filter( 'block_categories_all', array(__CLASS__, 'block_categories'), 10, 2);
		else
			add_filter( 'block_categories', array(__CLASS__, 'block_categories'), 10, 2 );

		// AO
		add_filter('autoptimize_filter_js_exclude', array(__CLASS__, 'autoptimize_filter_js_exclude'), 10, 2);
		add_filter('autoptimize_filter_css_exclude', array(__CLASS__, 'autoptimize_filter_css_exclude'), 10, 2);
		add_filter('autoptimize_filter_css_minify_excluded', array(__CLASS__, 'autoptimize_filter_css_minify_excluded'));
		add_filter('autoptimize_filter_js_minify_excluded', array(__CLASS__, 'autoptimize_filter_js_minify_excluded'));
		add_filter('autoptimize_js_include_inline', array(__CLASS__, 'autoptimize_js_include_inline'));

		add_filter('mime_types', array(__CLASS__, 'mime_types'));
		add_action('deactivate_' . self::$basename, array(__CLASS__, 'deactivate'));

		// Welcome
		add_action('activate_' . self::$basename, array(__CLASS__, 'activate'), 10, 2);
		add_action('admin_init', array(__CLASS__, 'admin_init'), 10, 2);

		// Iframes
		if (isset($_REQUEST['mapp_iframe']))
			add_action('template_redirect', array(__CLASS__, 'template_redirect'));
	}

	static function activate($network_wide = false) {
		$current_version = get_option('mappress_version');
		if (!$current_version)
			set_transient('_mappress_activation_redirect', 'wizard', 30);
		else
			set_transient('_mappress_activation_redirect', true, 30);
	}

	static function admin_init() {
		$redirect = get_transient('_mappress_activation_redirect');
		if ($redirect) {
			delete_transient('_mappress_activation_redirect');
			if (is_network_admin() || isset( $_GET['activate-multi'])) {
				return;
			} else {
				$args = array('page' => 'mappress_support', 'wizard' => ($redirect == 'wizard') ? 1 : 0);
				wp_safe_redirect(add_query_arg($args, admin_url('admin.php')));
			}
		}
	}

	// Scripts & styles for admin
	// CSS is always loaded from the plugin directory
	static function admin_enqueue_scripts($hook) {
		global $postid;

		// Some plugins call this without setting $hook
		if (empty($hook))
			return;

		else if (empty(self::$pages))				// Network admin has no pages
			return;

		$page = null;
		if (in_array($hook, array('post.php', 'post-new.php')))
			$page = 'editor';
		else if ($hook == self::$pages[0])
			$page = 'settings';
		else if ($hook == self::$pages[2])
			$page = 'library';
		else if ($hook == self::$pages[3])
			$page = 'support';
		else if (in_array($hook, array('customize.php', 'appearance_page_gutenberg-widgets', 'appearance_page_gutenberg-edit-site')))
			$page = 'customizer';
		else if (in_array($hook, array('plugins.php')))
			$page = 'plugins';
		else if ($hook == 'widgets.php')
			$page = 'widgets';

		if ($page) {
			if ($page == 'settings') {
				self::scripts_enqueue('settings');
				self::styles_enqueue('settings');
			} else {
				self::scripts_enqueue('backend');
				self::styles_enqueue('backend');
			}
		}
	}

	static function admin_menu() {
		// Settings
		self::$pages[] = add_menu_page('MapPress', 'MapPress', 'manage_options', 'mappress', array('Mappress_Settings', 'options_page'), 'dashicons-location');
		self::$pages[] = add_submenu_page('mappress', __('Settings', 'mappress-google-maps-for-wordpress'), __('Settings', 'mappress-google-maps-for-wordpress'), 'manage_options', 'mappress', array('Mappress_Settings', 'options_page'));
		self::$pages[] = add_submenu_page('mappress', __('Maps', 'mappress-google-maps-for-wordpress'), __('Maps', 'mappress-google-maps-for-wordpress'), 'manage_options', 'mappress_maps', array(__CLASS__, 'map_library'));
		self::$pages[] = add_submenu_page('mappress', __('Support', 'mappress-google-maps-for-wordpress'), __('Support', 'mappress-google-maps-for-wordpress'), 'manage_options', 'mappress_support', array('Mappress_Settings', 'support_page'));
	}

	static function admin_notices() {
		global $wpdb;
		$error =  "<div class='notice notice-error'><p>%s</p></div>";
		$map_table = $wpdb->prefix . "mappress_maps";
		$exists = $wpdb->get_var("show tables like '$map_table'");

		// Non-dismissible notices
		if (!$exists) {
			printf($error, __("MapPress database tables are missing.  Please deactivate the plugin and activate it again to fix this.", 'mappress-google-maps-for-wordpress'));
			return;
		}

		if (self::$options->engine != 'leaflet' && !self::get_api_keys()->browser)
			printf($error, sprintf("%s. %s <a href='%s'>%s</a>.", __("A Google Maps API key is required", 'mappress-google-maps-for-wordpress'), __("Please update your", 'mappress-google-maps-for-wordpress'), admin_url('admin.php?page=mappress'), __('MapPress Settings', 'mappress-google-maps-for-wordpress')));

		// Dismissibles
		if (is_super_admin()) {
			$content =  "<div class='notice notice-%s is-dismissible' data-mapp-dismiss='%s'><p>%s</p></div>";
			$dismissed = array_filter( explode( ',', (string) get_user_meta( get_current_user_id(), 'mappress_dismissed', true ) ) );
			$notices = (self::$notices) ? array_diff_key(self::$notices, array_flip($dismissed)) : array();

			foreach($notices as $key => $notice)
				printf($content, $notice[0], $key, $notice[1]);

			if ($notices) {
				echo Mappress::script("jQuery('[data-mapp-dismiss]').on('click', '.notice-dismiss, .mapp-dismiss', function(e) {
					var key = jQuery(this).closest('.notice').attr('data-mapp-dismiss');
					jQuery(this).closest('[data-mapp-dismiss]').remove();
					jQuery.post(ajaxurl, { action : 'mapp_dismiss', key : key });
				});");
			}
		}
	}

	/**
	* Dismiss/undismiss admin notices
	*
	* @param mixed $key - notice to dismiss/undismiss
	* @param mixed $dismiss - true to dismiss, false to undismiss
	* @return mixed
	*/
	static function admin_notices_dismiss($key, $dismiss) {
		if (!$key)
			return;

		$dismissed = array_filter( explode( ',', (string) get_user_meta( get_current_user_id(), 'mappress_dismissed', true ) ) );
		if ($dismiss)
			$dismissed[] = $key;
		else
			unset($dismissed[$key]);
		update_user_meta( get_current_user_id(), 'mappress_dismissed', implode( ',', $dismissed ));
	}

	static function ajax_dismiss() {
		$key = isset($_POST['key']) ? $_POST['key'] : null;

		if (!$key || sanitize_key( $key) != $key)
			wp_die( 0 );
		self::admin_notices_dismiss($key, true);
		self::ajax_response('OK');
	}

	static function ajax_response($status, $data=null) {
		$output = trim(ob_get_clean());		// Ignore whitespace, any other output is an error
		header( "Content-Type: application/json" );

		// WP bug: when zlib active, warning messages are generated, which corrupt JSON output
		// Ticket has been open for 9 years.  Workaround is to disable flush when providing json response - may cause other conflicts!
		// https://core.trac.wordpress.org/ticket/22430, https://core.trac.wordpress.org/ticket/18525
		if (ini_get('zlib.output_compression'))
			remove_action( 'shutdown', 'wp_ob_end_flush_all', 1 );

		$response = json_encode(array('status' => $status, 'output' => $output, 'data' => $data));
		die ($response);
	}

	static function autoptimize_filter_css_exclude($exclude_css, $content) {
		if ($exclude_css)
			$exclude_css .= ',';
		$exclude_css .= "mappress.css,mappress_admin.css,leaflet.css,MarkerCluster.Default.css,Leaflet.markercluster/MarkerCluster.css";
		return $exclude_css;
	}

	// Exclude JS from AO
	static function autoptimize_filter_js_exclude($exclude_js, $content) {
		if ($exclude_js)
			$exclude_js .= ',';
		$exclude_js .= "underscore.js,underscore.min,js,index_mappress.js,index_mappress_admin.js";
		return $exclude_js;
	}

	// Without this AO will still minify excluded CSS, causing display problems
	static function autoptimize_filter_css_minify_excluded($excluded) { return false; }

	// Without this AO will still minify excluded JS, causing JS errors
	static function autoptimize_filter_js_minify_excluded($excluded) { return false; }

	// Without this AO will remove the wp_head() output, causing JS errors
	static function autoptimize_js_include_inline($include_inline) { return false; }

	// 5.8 version of block_categories hook
	// Older GT versions send ($categories, $post) instead of ($categories, $context)
	static function block_categories($categories, $context) {
		self::$block_category = 'mappress';
		return array_merge(
			$categories,
			array(
				array(
					'slug' => 'mappress',
					'title' => 'MapPress'
				),
			)
		);
	}

	static function deactivate() {
		$reason = (isset($_REQUEST['mapp_reason'])) ? $_REQUEST['mapp_reason'] : null;
		$reason_text = (isset($_REQUEST['mapp_reason_text'])) ? $_REQUEST['mapp_reason_text'] : null;

		if (!$reason || $reason == 'private' || $reason == 'temporary')
			return;

		// Call API (static functions can't use api_call())
		$args = array(
			'api_action' => 'feedback',
			'network_url' => (is_multisite()) ? trim(network_home_url()) : trim(home_url()),
			'plugin' => 'mappress',
			'reason' => $reason,
			'reason_text' => $reason_text,
			'url' => trim(home_url()),
		);
		$response = wp_remote_post('https://mappresspro.com', array('timeout' => 15, 'sslverify' => false, 'body' => (array) $args));
	}

	static function debugging() {
		global $wpdb;

		if (isset($_GET['mp_info'])) {
			echo "<b>Plugin</b> " . self::$version;
			$posts_table = $wpdb->prefix . 'mappress_posts';
			$results = $wpdb->get_results("SELECT postid, mapid FROM $posts_table");
			echo "<br/>postid => mapid<br/>";
			foreach($results as $i => $result) {
				if ($i > 50)
					break;
				echo "<br/>$result->postid => $result->mapid";
			}
			$options = Mappress_Options::get();
			unset($options->mapbox, $options->license, $options->apiKey, $options->apiKeyServer);
			echo str_replace(array("\r", "\n"), array('<br/>', '<br/>'), print_r($options, true));
			die();
		}

		if (isset($_REQUEST['mp_debug']))
			self::$debug = max(1, (int) $_REQUEST['mp_debug']);
		else if (defined('MAPPRESS_DEBUG'))
			self::$debug = MAPPRESS_DEBUG;

		if (self::$debug) {
			error_reporting(E_ALL);
			ini_set('error_reporting', E_ALL);
			ini_set('display_errors','On');
			$wpdb->show_errors();
		}
	}

	static function get_api_keys() {
		$results = (object) array('browser' => self::$options->apiKey, 'server' => self::$options->apiKeyServer, 'mapbox' => self::$options->mapbox);
		if (empty($results->browser) && defined('MAPPRESS_APIKEY'))
			$results->browser = MAPPRESS_APIKEY;
		if (empty($results->server) && defined('MAPPRESS_APIKEY_SERVER'))
			$results->server = MAPPRESS_APIKEY_SERVER;
		if (empty($results->mapbox) && defined('MAPPRESS_APIKEY_MAPBOX'))
			$result->mapbox = MAPPRESS_APIKEY_MAPBOX;
		return $results;
	}

	/**
	* Get language using settings/WPML/qTrans
	*
	*/
	static function get_language() {
		// WPML
		if (defined('ICL_LANGUAGE_CODE'))
			$lang = ICL_LANGUAGE_CODE;

		// qTranslate
		else if (function_exists('qtrans_getLanguage'))
			$lang = qtrans_getLanguage();

		else
			$lang = self::$options->language;

		return ($lang) ? $lang : null;
	}

	/**
	* Get a mashup - used by shortcode and widget
	*
	* @param mixed $atts
	*/
	static function get_mashup($atts) {
		global $wp_query;

		$mashup = new Mappress_Map($atts);
		$mashup->query = Mappress_Query::parse_query($atts);

		// If parameter test="true", output the query result (or global query) without using a map
		if (isset($_GET['mp_test']) || (isset($atts['test']) && $atts['test'])) {
			$wpq = ($mashup->query) ? new WP_Query($mashup->query) : $wp_query;
			return "<pre>" . print_r($wpq, true) . "</pre>";
		}

		// If 'hideEmpty' is set, try to suppress the map if there are no POIs
		if ($mashup->hideEmpty) {
			if (Mappress_Query::is_empty($mashup->query))
				return "";
		}
		return $mashup->display();
	}

	static function heartbeat_settings( $settings ) {
		$settings['minimalInterval'] = 600;
		return $settings;
	}

	/**
	* There are several WP bugs that prevent correct activation in multisitie:
	*   http://core.trac.wordpress.org/ticket/14170
	*   http://core.trac.wordpress.org/ticket/14718)
	*
	*/
	static function init() {
		Mappress_Map::register();
		Mappress_Settings::register();
		Mappress_Template::register();
		Mappress_WPML::register();

		if (self::$pro) {
			Mappress_Frontend::register();
			Mappress_Icons::register();
			Mappress_Meta::register();
			Mappress_Query::register();
			Mappress_Widget::register();
			Mappress_Widget_Map::register();
		}

		self::scripts_register();
		self::styles_register();

		// Register Gutenberg block types and load GT scripts
		if (function_exists('register_block_type')) {
			register_block_type('mappress/map', array(
				'render_callback' => array(__CLASS__, 'shortcode_map'),
				'editor_script' => array('mappress_admin'),

			));
			if (self::$pro) {
				register_block_type('mappress/mashup', array(
					'render_callback' => array(__CLASS__, 'shortcode_mashup'),
					'editor_script' => array('mappress_admin'),
				));
			}
		}

		// Check if upgrade is needed
		$current_version = get_option('mappress_version');

		if (empty($current_version)) {
			$args = array(
				'api_action' => 'feedback',
				'network_url' => (is_multisite()) ? trim(network_home_url()) : trim(home_url()),
				'plugin' => 'mappress',
				'reason' => 'new',
				'reason_text' => '',
				'url' => trim(home_url()),
			);
			$response = wp_remote_post('https://mappresspro.com', array('timeout' => 15, 'sslverify' => false, 'body' => (array) $args));
		}

		// Algolia geocoder discontinued since 2.69.3
		if (empty(self::$options->geocoder)) {
			self::$options->geocoder = 'nominatim';
			self::$options->save();
		}

		// Convert meta key settings
		if ($current_version < '2.45') {
			$old = (object) get_option('mappress_options');
			foreach(array('address1', 'address2', 'address3', 'address4', 'address5', 'address6', 'lat', 'lng', 'iconid', 'title', 'body', 'zoom') as $i => $key) {
				if ($i < 6) {
					$value = (isset($old->metaKeyAddress[$i])) ? $old->metaKeyAddress[$i] : null;
				} else {
					$old_key = 'metaKey' . ucfirst($key);
					$value = (isset($old->$old_key)) ? $old->$old_key : null;
				}
				if ($value)
					Mappress::$options->metaKeys[$key] = $value;
			}
			Mappress::$options->save();
		}

		// Check for license expired
		if (self::$pro && self::$options->license) {
			$last_check = get_option('mappress_license_check');
			if (!$last_check || time() > $last_check + (60 * 60 * 24 * 7)) {
				$status = Mappress::$updater->get_status();
				if ($status == 'inactive') {
					$renew_link = sprintf("<a target='_blank' href='http://mappresspro.com/account'>%s</a>", __('Renew your license', 'mappress-google-maps-for-wordpress'));
					self::admin_notices_dismiss('expiredlicense', false);
					self::$notices['expiredlicense'] = sprintf(__('Your MapPress license has expired.  %s to get the latest updates and prevent errors.', 'mappress-google-maps-for-wordpress'), $renew_link);
				}
				update_option('mappress_license_check', time());
				return;
			}
		}

		// Missing license
		if (self::$pro && !trim(self::$options->license) && (!is_multisite() || (is_super_admin() && is_main_site())))
			self::$notices['nolicense'] = array('warning', __('Please enter your MapPress license key to enable plugin updates', 'mappress-google-maps-for-wordpress'));

		// WP minimum version
		if (self::VERSION >= '2.55' && version_compare(get_bloginfo('version'),'5.3', '<') )
			self::$notices['255_min_version'] = array('error', __('MapPress Gutenberg blocks require WordPress 5.3 or the latest Gutenberg Plugin. Please update if using the block editor.', 'mappress-google-maps-for-wordpress'));

		// New features
		if ($current_version && $current_version < '2.55' && self::VERSION >= '2.55')
			self::$notices['255_whats_new'] = array('info', sprintf(__('MapPress has many new features!  %s.', 'mappress-google-maps-for-wordpress'), '<a target="_blank" href="https://mappresspro.com/whats-new">' . __("Learn more", 'mappress-google-maps-for-wordpress') . '</a>'));

		// New popup templates
		if ($current_version && $current_version < '2.60' && self::VERSION >= '2.60')
			self::$notices['260_whats_new'] = array('warning', sprintf(__('MapPress popup templates have changed!  Please update custom templates to the new format. %s.', 'mappress-google-maps-for-wordpress'), '<a target="_blank" href="https://mappresspro.com/whats-new">' . __("Learn more", 'mappress-google-maps-for-wordpress') . '</a>'));

		if ($current_version && $current_version < '2.63' && self::VERSION >= '2.63') {
			// New list templates
			self::$notices['263_whats_new'] = array('warning', sprintf(__('MapPress templates and filters have changed.  Please update custom templates and filters. %s.', 'mappress-google-maps-for-wordpress'), '<a target="_blank" href="https://mappresspro.com/whats-new">' . __("Learn more", 'mappress-google-maps-for-wordpress') . '</a>'));

			// Convert filters to array
			if (self::$options->filter) {
				self::$options->filters = array(array('key' => self::$options->filter));
				self::$options->save();
			}

			// Convert styles to indexed arrays
			if (self::$options->styles && is_array(self::$options->styles)) {
				self::$options->stylesGoogle = array();
				self::$options->stylesMapbox = array();
				foreach(self::$options->styles as $name => $json)
					self::$options->stylesGoogle[] = array('id' => $name, 'name' => $name, 'url' => null, 'json' => $json, 'imageUrl' => self::$baseurl . '/images/roadmap.png');
				foreach(self::$options->mapboxStyles as $name => $url) {
					$parts = explode('?', strtolower($url));
					$short_url = str_ireplace(array('.html', 'https://api.mapbox.com/styles/v1/', 'mapbox://styles/'), '', $parts[0]);
					$parts = explode('/', $short_url);
					if (count($parts) == 2)
						self::$options->stylesMapbox[] = array('url' => $url, 'provider' => 'mapbox', 'user' => $parts[0], 'id' => $name, 'mapboxid' => $parts[1], 'name' => $name);
				}
				self::$options->save();
			}
		}

		// Add a type to all filters
		if ($current_version && $current_version < '2.73') {
			foreach(self::$options->filters as &$filter) {
				if (empty($filter['type']))
					$filter['type'] = 'tax';
			}
			self::$options->save();
		}

		update_option('mappress_version', self::VERSION);
	}

	// Prevent shortcodes on admin screens
	static function is_admin() {
		$ajax = defined('DOING_AJAX') && DOING_AJAX;
		$rest = defined('REST_REQUEST') && REST_REQUEST;
		return (is_admin() && !$ajax) || $rest;
	}

	static function is_dev() {
		if (defined('MAPPRESS_DEV') && MAPPRESS_DEV)
			return MAPPRESS_DEV;
		else if (isset($_REQUEST['mp_dev']))
			return ($_REQUEST['mp_dev']) ? $_REQUEST['mp_dev'] : 'dev';
		else
			return false;
	}

	static function is_footer() {
		if (class_exists( 'Jetpack' ) && Jetpack::is_module_active( 'infinite-scroll' ))
			return false;
		if (defined('DOING_AJAX') && DOING_AJAX)
			return false;
		if (defined('REST_REQUEST') && REST_REQUEST)
			return true;
		if (is_admin())
			return true;
		return self::$options->footer;
	}

	static function is_localhost() {
		return !filter_var($_SERVER['SERVER_ADDR'], FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);
	}

	static function is_ssl() {
		return (is_ssl() || !filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE));
	}

	static function l10n() {
		global $post, $is_IE;

		$l10n = array(
			'delete_prompt' => __('Are you sure you want to delete?', 'mappress-google-maps-for-wordpress'),
			'delete_map_prompt' => __('Permanently delete this map?', 'mappress-google-maps-for-wordpress'),
			'kml_error' => __('Error reading KML file', 'mappress-google-maps-for-wordpress'),
			'layer' => __('URL for KML file', 'mappress-google-maps-for-wordpress'),
			'need_classic' => __('Please select an editor to insert into.', 'mappress-google-maps-for-wordpress'),
			'no_geolocate' => __('Unable to get your location', 'mappress-google-maps-for-wordpress'),
			'no_results' => __('No results', 'mappress-google-maps-for-wordpress'),
			'save' => __('Save changes?', 'mappress-google-maps-for-wordpress'),
			'shape' => __('Shape', 'mappress-google-maps-for-wordpress'),
		);

		// Globals
		$l10n['options'] = array(
			'admin' => current_user_can('administrator'),
			'adminurl' => admin_url(),
			'ajaxurl' => admin_url('admin-ajax.php'),
			'apikey' => self::get_api_keys()->browser,
			'baseurl' => self::$baseurl,
			'blockCategory' => self::$block_category,
			'debug' => self::$debug,
			'editurl' => admin_url('post.php'),
			'iconsUrl' => (self::$pro) ? Mappress_Icons::$icons_url : null,
			'isIE' => $is_IE,
			'language' => self::get_language(),
			'mapbox' => self::get_api_keys()->mapbox,
			'nonce' => wp_create_nonce('mappress'),
			'postid' => ($post) ? $post->ID : null,	// Note: GT => numeric, classic => string
			'pro' => self::$pro,
			'ssl' => self::is_ssl(),                // SSL is needed for 'your location' in directions
			'standardIcons' => (self::$pro) ? Mappress_Icons::$standard_icons : null,
			'standardIconsUrl' => (self::$pro) ? Mappress_Icons::$standard_icons_url : null,
			'userStyles' => (self::$options->engine == 'leaflet') ? self::$options->stylesMapbox : self::$options->stylesGoogle,
			'userIcons' => (self::$pro) ? Mappress_Icons::get_user_icons() : null,
			'version' => self::$version
		);

		// Default styles
		if (Mappress::$options->engine == 'leaflet') {
			if (Mappress::get_api_keys()->mapbox) {
				$styles = array(
					array('id' => 'streets', 'type' => 'standard', 'provider' => 'mapbox', 'user' => 'mapbox', 'mapboxid' => 'streets-v11', 'name' => __('Streets', 'mappress-google-maps-for-wordpress')),
					array('id' => 'outdoors', 'type' => 'standard', 'provider' => 'mapbox', 'user' => 'mapbox', 'mapboxid' => 'outdoors-v11', 'name' => __('Outdoors', 'mappress-google-maps-for-wordpress')),
					array('id' => 'light', 'type' => 'standard', 'provider' => 'mapbox', 'user' => 'mapbox', 'mapboxid' => 'light-v10', 'name' => __('Light', 'mappress-google-maps-for-wordpress')),
					array('id' => 'dark', 'type' => 'standard', 'provider' => 'mapbox', 'user' => 'mapbox', 'mapboxid' => 'dark-v10', 'name' => __('Dark', 'mappress-google-maps-for-wordpress')),
					array('id' => 'satellite', 'type' => 'standard', 'provider' => 'mapbox', 'user' => 'mapbox', 'mapboxid' => 'satellite-v9', 'name' => __('Satellite', 'mappress-google-maps-for-wordpress')),
					array('id' => 'satellite-streets', 'type' => 'standard', 'provider' => 'mapbox', 'user' => 'mapbox', 'mapboxid' => 'satellite-streets-v11', 'name' => __('Satellite Streets', 'mappress-google-maps-for-wordpress'))
				);
			} else {
				$styles = array(
					array('id' => 'osm', 'type' => 'standard', 'provider' => 'osm', 'name' => __('Streets', 'mappress-google-maps-for-wordpress'))
				);
			}
		} else {
			// Google styles
			$styles = array(
				array( 'id' => 'roadmap', 'type' => 'standard', 'name' => __('Roadmap', 'mappress-google-maps-for-wordpress'), 'imageUrl' => Mappress::$baseurl . '/images/roadmap.png'),
				array( 'id' => 'terrain', 'type' => 'standard', 'name' => __('Terrain', 'mappress-google-maps-for-wordpress'), 'imageUrl' => Mappress::$baseurl . '/images/terrain.png'),
				array( 'id' => 'satellite', 'type' => 'standard', 'name' => __('Satellite', 'mappress-google-maps-for-wordpress'), 'imageUrl' => Mappress::$baseurl . '/images/satellite.png'),
				array( 'id' => 'hybrid', 'type' => 'standard', 'name' => __('Hybrid', 'mappress-google-maps-for-wordpress'), 'imageUrl' => Mappress::$baseurl . '/images/hybrid.png'),
			);
		}
		$l10n['options']['standardStyles'] = $styles;

		// Global settings
		$options = array('alignment', 'clustering', 'clusteringOptions', 'country', 'defaultIcon', 'directions', 'directionsServer',
		'engine', 'filters', 'filtersPos', 'geocoder', 'highlight', 'highlightIcon', 'iconScale', 'initialOpenInfo', 'layout',
		'mashupClick', 'mini', 'poiList', 'poiListOpen', 'poiListPageSize', 'poiZoom', 'radius', 'scrollWheel', 'search', 'searchBox', 'size', 'sizes', 'style', 'thumbs', 'thumbsList', 'thumbsPopup', 'tooltips', 'userLocation');

		foreach($options as $option) {
			if (isset(self::$options->$option)) {
				$l10n['options'][$option] = self::$options->$option;
			}
		}

		return apply_filters('mappress_options', $l10n);
	}

	static function map_library() {
		self::scripts_enqueue('backend');
		echo '<div id="mapp-library" class="mapp-library"></div>';
		wp_editor('', 'mapp-library-tinymce');
	}

	/**
	* Add KML/KMZ as valid mime types
	*
	* @param mixed $mimes
	*/
	static function mime_types($mimes) {
		// Additional entries must match WP, which use finfo_file(), e.g. KML => text/xml
		$mimes['kml'] = 'text/xml';			// Real type: 'application/vnd.google-earth.kml+xml';
		$mimes['kmz'] = 'application/zip';	// Real type: 'application/vnd.google-earth.kmz';
		return $mimes;
	}

	static function script_loader_tag($tag, $handle, $src) {
		// Deregister
		if ( self::$options->deregister && self::$options->engine == 'google' && self::$loaded && ($handle != 'mappress-google' && (stripos($src, 'maps.googleapis.com') !== false || stripos($src, 'maps.google.com'))))
			return '';
		// Re-register
		else if ($handle == 'mappress-google' && empty($tag))
			return sprintf("<script src='%s' id='mappress-google-js-fixed'></script>\n", self::scripts_google_tag());
		else
			return $tag;
	}

	static function scripts_enqueue($type = 'frontend') {
		if (self::$loaded)
			return;
		else
			self::$loaded = true;

		wp_enqueue_script('mappress');

		// L10N - can't be done at register because $post global isn't set
		wp_localize_script('mappress', 'mappl10n', self::l10n());

		if ($type == 'backend' || $type == 'settings')
			wp_enqueue_script('mappress_admin');

		if ($type == 'settings') {
			if (function_exists('wp_enqueue_code_editor'))
				wp_enqueue_code_editor(array( 'type' => 'php' ));
		}

		// Templates
		$templates = array('map', 'map-directions', 'map-filters', 'map-item', 'map-loop', 'map-popup', 'mashup-popup', 'mashup-loop', 'mashup-item');
		if ($type == 'backend')
			$templates = array_merge($templates, array('editor', 'mce'));

		foreach($templates as $template_name)
			Mappress_Template::enqueue_template($template_name, self::is_footer());
	}

	static function scripts_register() {
		$dev = self::is_dev();
		$footer = self::is_footer();

		// Directories
		$lib = self::$baseurl . '/lib';
		$js = ($dev) ? "http://localhost/$dev/wp-content/plugins/mappress-google-maps-for-wordpress/build" : self::$baseurl . '/build';

		// Leaflet
		wp_register_script("mappress-leaflet", 'https://unpkg.com/leaflet@1.7.1/dist/leaflet.js', null, '1.7.1', $footer);
		wp_register_script("mappress-omnivore", $lib . "/leaflet-omnivore.min.js", null, '0.3.1', $footer);

		// Google
		wp_register_script("mappress-google", self::scripts_google_tag(), null, null, $footer);

		// Clustering ( https://github.com/googlemaps/js-markerclustererplus | https://github.com/Leaflet/Leaflet.markercluster )
		wp_register_script('mappress-markerclustererplus', "https://unpkg.com/@googlemaps/markerclustererplus/dist/index.min.js", null, '1.2.0', $footer);
		wp_register_script('mappress-leaflet-markercluster', $lib . "/Leaflet.markercluster/leaflet.markercluster.js", null, '1.4.1', $footer);

		// Dependencies
		$deps = array('jquery', 'jquery-ui-autocomplete', 'underscore', 'wp-element', 'wp-i18n');
		if (self::$options->engine == 'leaflet')
			$deps = array_merge(array('mappress-leaflet', 'mappress-omnivore'), $deps);
		if (self::$options->engine != 'leaflet' || self::$options->geocoder == 'google')
			$deps[] = 'mappress-google';
		if (self::$options->clustering)
			$deps[] = (self::$options->engine == 'leaflet') ? 'mappress-leaflet-markercluster' : 'mappress-markerclustererplus';

		wp_register_script('mappress', $js . "/index_mappress.js", $deps, self::$version, $footer);
		wp_register_script('mappress_admin', $js . "/index_mappress_admin.js", array('mappress', 'jquery-ui-position', 'wp-blocks', 'wp-components', 'wp-compose', 'wp-element', 'wp-notices', 'wp-url', 'wp-core-data', 'wp-i18n'), self::$version, $footer);

		// I18N
		if (function_exists('wp_set_script_translations')) {
			wp_set_script_translations('mappress', 'mappress-google-maps-for-wordpress', self::$basedir . '/languages');
			wp_set_script_translations('mappress_admin', 'mappress-google-maps-for-wordpress', self::$basedir . '/languages');
		}
	}

	static function scripts_google_tag() {
		$dev = self::is_dev();
		$language = self::get_language();
		$language = ($language) ? "&language=$language" : '';
		$apiversion = ($dev) ? 'v=beta' : 'v=3';
		$apikey = "&key=" . self::get_api_keys()->browser;
		$libs = '&libraries=places,drawing';
		return "https://maps.googleapis.com/maps/api/js?{$apiversion}{$language}{$libs}{$apikey}";
	}

	static function plugin_action_links($links, $file) {
		$settings_link = "<a href='" . admin_url("admin.php?page=mappress") . "'>" . __('Settings', 'mappress-google-maps-for-wordpress') . "</a>";
		$whatsnew_link = "<a href='https://mappresspro.com/whats-new/' target='_blank'>" . __("What's new", 'mappress-google-maps-for-wordpress') . "</a>";
		array_unshift( $links, $whatsnew_link );
		array_unshift( $links, $settings_link);
		return $links;
	}

	static function plugins_loaded() {
		load_plugin_textdomain('mappress-google-maps-for-wordpress', false, dirname(self::$basename) . '/languages');
	}

	static function script($script, $ready = false) {
		$html = "\r\n<script type='text/javascript'>\r\n";
		$html .= ($ready) ? "jQuery(function() { $script });" : $script;
		$html .= "\r\n</script>";
		return $html;
	}

	static function script_template($template, $id = null) {
		$id = ($id) ? "id='mapp-tmpl-{$id}'" : '';
		$html = "\r\n<script type='text/template' $id>\r\n{$template}\r\n</script>\r\n";
		return $html;
	}

	/**
	* Scrub attributes
	* The WordPress shortcode API passes shortcode attributes in lowercase and with boolean values as strings (e.g. "true")
	* Converts atts to lowercase, replaces boolean strings with booleans, and creates arrays from comma-separated attributes
	*
	* Returns empty array if $atts is empty or not an array
	*/
	static function scrub_atts($atts=null) {
		if (!$atts || !is_array($atts))
			return array();

		$atts = self::string_to_boolean($atts);

		// Shortcode attributes are lowercase so convert everything to lowercase
		$atts = array_change_key_case($atts);

		// Map options - includes both leaflet and Google
		foreach(array('disableDefaultUI', 'disableDoubleClickZoom', 'draggable', 'dragging', 'fullscreenControl', 'keyboard',
			'keyboardShortcuts', 'mapTypeControl', 'maxZoom', 'minZoom', 'panControl', 'rotateControl', 'scaleControl',
			'scrollwheel', 'scrollWheelZoom', 'streetViewControl', 'zoomControl') as $opt) {
			$lcopt = strtolower($opt);
			if (isset($atts[$lcopt])) {
				$atts['mapopts'][$opt] = $atts[$lcopt];
				unset($atts[$lcopt]);
			}
		}

		// Explode layers
		if (isset($atts['layers'])) {
			$atts['layers'] = explode(',', $atts['layers']);
			foreach($atts['layers'] as &$layer)
				$layer = trim($layer);
		}

		// Search = 'post', replace with post's location
		if (isset($atts['center']) && $atts['center'] == 'post') {
			global $post;
			$maps = Mappress_Map::get_list($post->ID, 'ids');
			$map = ($maps) ? Mappress_Map::get($maps[0]) : null;
			$atts['center'] = ($map && $map->pois) ? $map->pois[0]->point['lat'] . ',' . $map->pois[0]->point['lng'] : null;
		}

		// Conver GT 'align' to 'alignment'
		if (isset($atts['align']))
			$atts['alignment'] = $atts['align'];

		return $atts;
	}

	/**
	* Switch WPML language during AJAX calls
	*/
	static function set_language() {
		global $sitepress;
		if ($sitepress && method_exists($sitepress, 'switch_lang')) {
			$language = self::get_language();
			$sitepress->switch_lang($language);
		}
	}

	/**
	* Map shortcode
	*
	*/
	static function shortcode_map($atts='') {
		global $post;

		if (self::is_admin() || is_feed())
			return;

		$atts = self::scrub_atts($atts);

		// Determine what to show
		$mapid = (isset($atts['mapid'])) ? $atts['mapid'] : null;

		// On archive pages, $post isn't set
		if (!$mapid && !$post)
			return;

		if ($mapid) {
			// Show map by mapid
			$map = Mappress_Map::get($mapid);
		} else {
			// Get the first map attached to the post
			$maps = Mappress_Map::get_list($post->ID);
			$map = (isset ($maps[0]) ? $maps[0] : false);
		}

		if (!$map)
			return;

		return $map->display($atts);
	}

	/**
	* Mashup shortcode
	*
	*/
	static function shortcode_mashup($atts='') {
		if (self::is_admin() || is_feed())
			return;

		$atts = self::scrub_atts($atts);
		return self::get_mashup($atts);
	}

	static function string_to_boolean($data) {
		if ($data === 'false')
			return false;

		if ($data === 'true')
			return true;

		if (is_array($data)) {
			foreach($data as &$datum)
				$datum = self::string_to_boolean($datum);
		}

		return $data;
	}

	static function styles_enqueue($type = 'frontend') {
		wp_enqueue_style('mappress-leaflet');
		wp_enqueue_style('mappress-leaflet-markercluster-default');
		wp_enqueue_style('mappress-leaflet-markercluster');
		wp_enqueue_style('mappress');

		if ($type == 'frontend')
			wp_enqueue_style('mappress-custom');
		else if ($type == 'backend' || $type == 'settings')
			wp_enqueue_style('mappress-admin');
	}

	static function styles_register() {
		// Leaflet CSS
		if (self::$options->engine == 'leaflet') {
			wp_register_style('mappress-leaflet', 'https://unpkg.com/leaflet@1.7.1/dist/leaflet.css', null, '1.7.1');
			if (self::$options->clustering) {
				wp_register_style('mappress-leaflet-markercluster-default', self::$baseurl . "/lib/Leaflet.markercluster/MarkerCluster.Default.css", null, '1.4.1');
				wp_register_style('mappress-leaflet-markercluster', self::$baseurl . "/lib/Leaflet.markercluster/MarkerCluster.css", null, '1.4.1');
			}
		}

		// Mappress CSS from plugin directory
		wp_register_style('mappress', self::$baseurl . '/css/mappress.css', null, self::$version);

		// Mappress CSS from theme directory
		if ( @file_exists( get_stylesheet_directory() . '/mappress.css' ) )
			$file = get_stylesheet_directory_uri() . '/mappress.css';
		elseif ( @file_exists( get_template_directory() . '/mappress.css' ) )
			$file = get_template_directory_uri() . '/mappress.css';
		if (isset($file))
			wp_register_style('mappress-custom', $file, array('mappress'), self::$version);

		// Admin CSS
		wp_register_style('mappress-admin', self::$baseurl . '/css/mappress_admin.css', array('wp-edit-blocks'), self::$version);
	}

	static function template_redirect() {
		$atts = $_REQUEST;		// Copy request to modify it later
		$query = (isset($atts['query'])) ? $atts['query'] : null;
		$mapid = (isset($atts['mapid'])) ? $atts['mapid'] : null;
		$transient = (isset($atts['transient'])) ? $atts['transient'] : null;

		// URL booleans are string integers, e.g. "0" so correct them
		foreach(array('clustering', 'editable', 'embed', 'initialOpenDirections', 'initialOpenInfo', 'poiList') as $key) {
			if (isset($atts[$key]))
				$atts[$key] = ($atts[$key] == "1") ? true : false;
		}

		if ($mapid) {
			$map = Mappress_Map::get($mapid);
			if ($map)
				$map->update($atts);
			else
				die("<html><body><!-- Bad mapid: $mapid --></body></html>");
		} else {
			$map = new Mappress_Map($atts);
		}

		// Programmtic pois
		if ($transient)
			$map->pois = get_transient($transient);

		$map->embed = true;

		ob_start();
		Mappress::$options->footer = false;
		Mappress::scripts_register();
		Mappress::styles_enqueue('frontend');
		Mappress::scripts_enqueue('frontend');

		header( "Content-Type: text/html" );

		?>
		<!doctype html>
		<html class='mapp-iframe-html' <?php language_attributes(); ?>>
		<head>
			<title>MapPress</title>
			<?php
				//wp_head();
				Mappress::wp_head();
				wp_print_scripts();
				wp_print_styles();
			?>
		</head>
		<body class='mapp-iframe-body'>
			<?php echo $map->display(null, true); ?>
		</body>
		</html>
		<?php

		die();
	}

	/**
	* Automatic map display.
	* If set, the [mappress] shortcode will be prepended/appended to the post body, once for each map
	* The shortcode is used so it can be filtered - for example WordPress will remove it in excerpts by default.
	*
	* @param mixed $content
	*/
	static function the_content($content="") {
		global $post;
		global $wp_current_filter;

		$autodisplay = self::$options->autodisplay;

		// No auto display
		if (!$autodisplay || $autodisplay == 'none')
			return $content;

		// Check if in the loop, to prevent conflicts with JetPack - see http://wordpress.org/support/topic/easy-adsense-lite-and-jetpack
		if (!in_the_loop())
			return $content;

		// Don't add the shortcode for feeds or admin screens
		if (is_feed() || self::is_admin())
			return $content;

		// No shortcode if post is password protected
		if (post_password_required())
			return $content;

		// If this is an excerpt don't attempt to add the map to it
		if (in_array('get_the_excerpt', $wp_current_filter))
			return $content;

		// Don't auto display if the post already contains a MapPress shortcode
		if (stristr($content, '[mappress') !== false || stristr($content, '[mashup') !== false)
			return $content;

		// Don't auto display if the post already contains GT block
		if (stristr($content, 'wp:mappress/map') !== false)
			return $content;

		// Get maps associated with post
		$mapids = Mappress_Map::get_list($post->ID, 'ids');
		if (empty($mapids))
			return $content;

		// Add the shortcode once for each map
		$shortcodes = "";
		foreach($mapids as $mapid)
			$shortcodes .= '<p>[mappress mapid="' . $mapid . '"]</p>';

		if ($autodisplay == 'top')
			return $shortcodes . $content;
		else
			return $content . $shortcodes;
	}

	/**
	* Scripts & styles for frontend
	* CSS is loaded from: child theme, theme, or plugin directory
	*/
	static function wp_enqueue_scripts() {
		self::styles_enqueue('frontend');

		// Load scripts in header if needed
		if (!self::is_footer())
			self::scripts_enqueue();
	}

	static function wp_head() {
		echo "\r\n<!-- MapPress Easy Google Maps " . __('Version', 'mappress-google-maps-for-wordpress') . ':' . self::$version . " (http://www.mappresspro.com) -->\r\n";
	}
}

$mappress = new Mappress();
?>