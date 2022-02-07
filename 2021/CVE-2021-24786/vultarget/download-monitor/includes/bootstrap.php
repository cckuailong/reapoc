<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


function download_monitor() {
	static $instance;
	if ( is_null( $instance ) ) {
		$instance = new WP_DLM();
	}
	return $instance;
}

function _load_download_monitor() {
	// fetch instance and store in global
	$GLOBALS['download_monitor'] = download_monitor();
}

// require autoloader
require_once dirname( DLM_PLUGIN_FILE ) . '/vendor/autoload.php';

// Init plugin
add_action( 'plugins_loaded', '_load_download_monitor', 10 );

if ( is_admin() && ( false === defined( 'DOING_AJAX' ) || false === DOING_AJAX ) ) {

	// set installer file constant
	define( 'DLM_PLUGIN_FILE_INSTALLER', DLM_PLUGIN_FILE );

	// include installer functions
	require_once( 'installer-functions.php' );

	// Activation hook
	register_activation_hook( DLM_PLUGIN_FILE_INSTALLER, '_download_monitor_install' );

	// Multisite new blog hook
	add_action( 'wpmu_new_blog', '_download_monitor_mu_new_blog', 10, 6 );

	// Multisite blog delete
	add_filter( 'wpmu_drop_tables', '_download_monitor_mu_delete_blog' );
}
