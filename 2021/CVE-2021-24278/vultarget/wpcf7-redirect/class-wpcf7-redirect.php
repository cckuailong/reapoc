<?php
/**
 * Main Contact Form 7 Redirect Class
 */
class Wpcf7_Redirect {
	public function init() {
		$this->define();
		$this->load_dependencies();
		$this->cf7_redirect_base = new WPCF7R_Base();

		add_action( 'plugins_loaded', array( $this, 'notice_to_remove_old_plugin' ) );
	}

	/**
	 * Load dependencies
	 */
	public function load_dependencies() {
		// Load all actions
		foreach ( glob( WPCF7_PRO_REDIRECT_BASE_PATH . 'modules/*.php' ) as $filename ) {
			require_once( $filename );
		}
		require_once( WPCF7_PRO_REDIRECT_CLASSES_PATH . 'class-wpcf7r-base.php' );
	}

	/**
	 * Notice to remove old plugin
	 */
	public function notice_to_remove_old_plugin() {
		if ( ! function_exists( 'is_plugin_active' ) ) {
			include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}
		if ( is_plugin_active( 'cf7-to-api/cf7-to-api.php' ) ) {
			add_action( 'admin_notices', 'wpcf7_remove_contact_form_7_to_api' );
		}
	}

	/**
	 * Defines
	 */
	public function define() {
		$base_url = 'https://redirection-for-contact-form7.com/';

		define( 'WPCF7_PRO_REDIRECT_PLUGIN_PAGE_URL', $base_url );
		define( 'WPCF7_PRO_REDIRECT_DEBUG_URL', $base_url . 'wp-json/api-v1/debug' );
		define( 'WPCF7_PRO_REDIRECT_PLUGIN_ACTIVATION_URL', $base_url . 'wp-admin/admin-ajax.php' );
		define( 'WPCF7_PRO_REDIRECT_PLUGIN_CHANGELOG_URL', $base_url . 'wp-json/api-v1/get_changelog/' );
		define( 'WPCF7_PRO_REDIRECT_PLUGIN_UPDATES', $base_url . 'wp-json/api-v1/check-for-updates/' );
		define( 'WPCF7_PRO_REDIRECT_EXTENSION_UPDATES', $base_url . 'wp-json/api-v1/get-extension-update/' );
		define( 'WPCF7_PRO_REDIRECT_PLUGIN_EXTENSIONS_LIST_URL', $base_url . 'wp-json/api-v1/extensions_list/' );
		define( 'WPCF7_PRO_REDIRECT_PLUGIN_PROMOTIONS_URL', $base_url . 'wp-json/api-v1/promotions/' );
		define( 'ACCESSIBE_API_URI', $base_url . 'wp-json/accesibe-api/activate' );

		define( 'WPCF7_PRO_REDIRECT_WP_REQUIRES', '5.3' );
		define( 'WPCF7_PRO_REDIRECT_WP_TESTED', '5.6' );
		define( 'WPCF7_PRO_REDIRECT_PLUGIN_ID', '6XpU046EOVs7v6O' );
		define( 'WPCF7_PRO_REDIRECT_PLUGIN_SKU', 'wpcfr-pro' );
		define( 'WPCF7_PRO_REDIRECT_BASE_NAME', plugin_basename( __FILE__ ) );
		define( 'WPCF7_PRO_REDIRECT_BASE_PATH', plugin_dir_path( __FILE__ ) );
		define( 'WPCF7_PRO_REDIRECT_BASE_URL', plugin_dir_url( __FILE__ ) );
		define( 'WPCF7_PRO_REDIRECT_PLUGINS_PATH', plugin_dir_path( dirname( __FILE__ ) ) );
		define( 'WPCF7_PRO_REDIRECT_TEMPLATE_PATH', WPCF7_PRO_REDIRECT_BASE_PATH . 'templates/' );
		define( 'WPCF7_PRO_REDIRECT_ACTIONS_TEMPLATE_PATH', WPCF7_PRO_REDIRECT_CLASSES_PATH . 'actions/html/' );
		define( 'WPCF7_PRO_REDIRECT_ADDONS_PATH', WPCF7_PRO_REDIRECT_PLUGINS_PATH . 'wpcf7r-addons/' );
		define( 'WPCF7_PRO_REDIRECT_ACTIONS_PATH', WPCF7_PRO_REDIRECT_CLASSES_PATH . 'actions/' );
		define( 'WPCF7_PRO_REDIRECT_FIELDS_PATH', WPCF7_PRO_REDIRECT_TEMPLATE_PATH . 'fields/' );
		define( 'WPCF7_PRO_REDIRECT_POPUP_TEMPLATES_PATH', WPCF7_PRO_REDIRECT_TEMPLATE_PATH . 'popups/' );
		define( 'WPCF7_PRO_REDIRECT_POPUP_TEMPLATES_URL', WPCF7_PRO_REDIRECT_BASE_URL . '/templates/popups/' );
		define( 'WPCF7_PRO_REDIRECT_ASSETS_PATH', WPCF7_PRO_REDIRECT_BASE_URL . 'assets/' );
		define( 'WPCF7_PRO_REDIRECT_BUILD_PATH', WPCF7_PRO_REDIRECT_BASE_URL . 'build/' );

		define( 'QFORM_BASE', WPCF7_PRO_REDIRECT_BASE_PATH . 'form/' );
	}
}
