<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
define( 'VI_WOO_ORDERS_TRACKING_DIR', WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . 'woo-orders-tracking' . DIRECTORY_SEPARATOR );
define( 'VI_WOO_ORDERS_TRACKING_LANGUAGES', VI_WOO_ORDERS_TRACKING_DIR . 'languages' . DIRECTORY_SEPARATOR );
define( 'VI_WOO_ORDERS_TRACKING_INCLUDES', VI_WOO_ORDERS_TRACKING_DIR . 'includes' . DIRECTORY_SEPARATOR );
define( 'VI_WOO_ORDERS_TRACKING_ADMIN', VI_WOO_ORDERS_TRACKING_INCLUDES . 'admin' . DIRECTORY_SEPARATOR );
define( 'VI_WOO_ORDERS_TRACKING_FRONTEND', VI_WOO_ORDERS_TRACKING_INCLUDES . 'frontend' . DIRECTORY_SEPARATOR );
define( 'VI_WOO_ORDERS_TRACKING_TEMPLATES', VI_WOO_ORDERS_TRACKING_INCLUDES . 'templates' . DIRECTORY_SEPARATOR );
define( 'VI_WOO_ORDERS_TRACKING_CACHE', WP_CONTENT_DIR . '/cache/woo-orders-tracking/' );
$plugin_url = plugins_url( '', __FILE__ );
$plugin_url = str_replace( '/includes', '/assets', $plugin_url );
define( 'VI_WOO_ORDERS_TRACKING_CSS', $plugin_url . '/css/' );
define( 'VI_WOO_ORDERS_TRACKING_CSS_DIR', VI_WOO_ORDERS_TRACKING_DIR . 'css' . DIRECTORY_SEPARATOR );
define( 'VI_WOO_ORDERS_TRACKING_JS', $plugin_url . '/js/' );
define( 'VI_WOO_ORDERS_TRACKING_JS_DIR', VI_WOO_ORDERS_TRACKING_DIR . 'js' . DIRECTORY_SEPARATOR );
define( 'VI_WOO_ORDERS_TRACKING_IMAGES', $plugin_url . '/images/' );
define( 'VI_WOO_ORDERS_TRACKING_PAYPAL_IMAGE', VI_WOO_ORDERS_TRACKING_IMAGES . 'paypal.png' );
define( 'VI_WOO_ORDERS_TRACKING_LOADING_IMAGE', VI_WOO_ORDERS_TRACKING_IMAGES . 'loading.gif' );

if ( is_file( VI_WOO_ORDERS_TRACKING_INCLUDES . 'data.php' ) ) {
	require_once VI_WOO_ORDERS_TRACKING_INCLUDES . 'data.php';
}
if ( is_file( VI_WOO_ORDERS_TRACKING_INCLUDES . 'track-info-table.php' ) ) {
	require_once VI_WOO_ORDERS_TRACKING_INCLUDES . 'track-info-table.php';
}

if ( is_file( VI_WOO_ORDERS_TRACKING_INCLUDES . 'functions.php' ) ) {
	require_once VI_WOO_ORDERS_TRACKING_INCLUDES . 'functions.php';
}
if ( is_file( VI_WOO_ORDERS_TRACKING_INCLUDES . 'custom-controls.php' ) ) {
	require_once VI_WOO_ORDERS_TRACKING_INCLUDES . 'custom-controls.php';
}
if ( is_file( VI_WOO_ORDERS_TRACKING_INCLUDES . 'support.php' ) ) {
	require_once VI_WOO_ORDERS_TRACKING_INCLUDES . 'support.php';
}
if ( is_file( VI_WOO_ORDERS_TRACKING_INCLUDES . 'schedule.php' ) ) {
	require_once VI_WOO_ORDERS_TRACKING_INCLUDES . 'schedule.php';
}
vi_include_folder( VI_WOO_ORDERS_TRACKING_ADMIN, 'VI_WOO_ORDERS_TRACKING_ADMIN_' );
vi_include_folder( VI_WOO_ORDERS_TRACKING_FRONTEND, 'VI_WOO_ORDERS_TRACKING_FRONTEND_' );