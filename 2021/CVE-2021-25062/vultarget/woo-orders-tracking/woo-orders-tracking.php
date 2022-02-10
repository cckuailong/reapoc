<?php
/**
 * Plugin Name: Orders Tracking for WooCommerce
 * Plugin URI: https://villatheme.com/extensions/woo-orders-tracking
 * Description: Import orders tracking number and send tracking info to customers
 * Version: 1.1.8.7
 * Author: VillaTheme
 * Author URI: https://villatheme.com
 * Text Domain: woo-orders-tracking
 * Domain Path: /languages
 * Copyright 2019-2021 VillaTheme.com. All rights reserved.
 * Tested up to: 5.8
 * WC tested up to: 5.6
 * Requires PHP: 7.4
 **/
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
define( 'VI_WOO_ORDERS_TRACKING_VERSION', '1.1.8.7' );
define( 'VI_WOO_ORDERS_TRACKING_PATH_FILE', __FILE__ );
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
if ( is_plugin_active( 'woocommerce-orders-tracking/woocommerce-orders-tracking.php' ) ) {
	return;
}
if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
	$init_file = WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . "woo-orders-tracking" . DIRECTORY_SEPARATOR . "includes" . DIRECTORY_SEPARATOR . "define.php";
	require_once $init_file;
}

if ( ! class_exists( 'WOO_ORDERS_TRACKING' ) ) {
	class WOO_ORDERS_TRACKING {
		public function __construct() {
			add_action( 'admin_notices', array( $this, 'global_note' ) );
			register_activation_hook( VI_WOO_ORDERS_TRACKING_PATH_FILE, array( __CLASS__, 'install' ) );
		}

		/**
		 * Notify if WooCommerce is not activated
		 */
		public function global_note() {
			if ( ! is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
				?>
                <div id="message" class="error">
                    <p><?php _e( 'Please install and activate WooCommerce to use Orders Tracking for WooCommerce plugin.', 'woo-orders-tracking' ); ?></p>
                </div>
				<?php
			}
		}

		public static function install() {

			if ( ! is_blog_installed() ) {
				return;
			}

			global $wpdb;

			$wotv_track_info_table_name = $wpdb->prefix . 'wotv_woo_track_info';

			$charset_collate = $wpdb->get_charset_collate();
			$sql             = "CREATE TABLE IF NOT EXISTS {$wotv_track_info_table_name} ( 
                id int NOT NULL AUTO_INCREMENT,
                order_id int ,
                tracking_number VARCHAR(50) NOT NULL ,
                status VARCHAR(50)  ,
                carrier_id VARCHAR(50)  ,
                carrier_name VARCHAR(50)  ,
                shipping_country_code VARCHAR(50)  ,
                track_info LONGTEXT  ,
                last_event LONGTEXT  ,
                create_at  DATETIME ,
                modified_at  DATETIME ,
                PRIMARY KEY  (id)
            )$charset_collate;";

			$wpdb->query( $sql );
		}
	}
}
new WOO_ORDERS_TRACKING();