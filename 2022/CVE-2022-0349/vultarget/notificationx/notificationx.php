<?php
/**
 * Plugin Name:       NotificationX
 * Plugin URI:        https://notificationx.com
 * Description:       Social Proof & Recent Sales Popup, Comment Notification, Subscription Notification, Notification Bar and many more.
 * Version:           2.3.8
 * Author:            WPDeveloper
 * Author URI:        https://wpdeveloper.com
 * License:           GPL-3.0+
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:       notificationx
 * Domain Path:       /languages
 *
 * @package           NotificationX
 * @link              https://wpdeveloper.com
 * @since             1.0.0
 */

/**
 * If this file is called directly, abort.
 */
if ( ! defined( 'WPINC' ) ) {
	die;
}
/**
 * Defines CONSTANTS for Whole plugins.
 */
define( 'NOTIFICATIONX_FILE', __FILE__ );
define( 'NOTIFICATIONX_VERSION', '2.3.8' );
define( 'NOTIFICATIONX_URL', plugins_url( '/', __FILE__ ) );
define( 'NOTIFICATIONX_PATH', plugin_dir_path( __FILE__ ) );
define( 'NOTIFICATIONX_BASENAME', plugin_basename( __FILE__ ) );

define( 'NOTIFICATIONX_ASSETS', NOTIFICATIONX_URL . 'assets/' );
define( 'NOTIFICATIONX_ASSETS_PATH', NOTIFICATIONX_PATH . 'assets/' );
define( 'NOTIFICATIONX_DEV_ASSETS', NOTIFICATIONX_URL . 'nxbuild/' );
define( 'NOTIFICATIONX_DEV_ASSETS_PATH', NOTIFICATIONX_PATH . 'nxbuild/' );
define( 'NOTIFICATIONX_INCLUDES', NOTIFICATIONX_PATH . 'includes/' );


define( 'NOTIFICATIONX_PLUGIN_URL', 'https://notificationx.com' );
define( 'NOTIFICATIONX_ADMIN_URL', NOTIFICATIONX_ASSETS . 'admin/' );
define( 'NOTIFICATIONX_PUBLIC_URL', NOTIFICATIONX_ASSETS . 'public/' );

/**
 * The Core Engine of the Plugin
 */
if ( ! class_exists( '\NotificationX\NotificationX' ) ) {
    require_once NOTIFICATIONX_PATH . 'vendor/autoload.php';
    if(nx_is_plugin_active( 'notificationx-pro/notificationx-pro.php' )){
        add_action( 'admin_notices', 'nx_free_compatibility_notice' );
        if( file_exists(dirname(NOTIFICATIONX_PATH) . '/notificationx-pro/vendor/autoload.php') ) {
            require_once dirname(NOTIFICATIONX_PATH) . '/notificationx-pro/vendor/autoload.php';
        } else {
            add_action('plugins_loaded', function(){
                remove_action( 'admin_notices', 'notificationx_install_core_notice' );
                \NotificationX\Core\Helper::remove_old_notice();
            });
        }
    }

    function activate_notificationx() {
        \NotificationX\NotificationX::get_instance()->activator();
    }
    /**
     * Plugin Activator
     */
    register_activation_hook( NOTIFICATIONX_FILE, 'activate_notificationx' );
    \NotificationX\NotificationX::get_instance();
}

function nx_free_compatibility_notice(){
    if ( ! function_exists( 'get_plugins' ) ) {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }
    $plugins = get_plugins();
    if( isset( $plugins['notificationx-pro/notificationx-pro.php'], $plugins['notificationx-pro/notificationx-pro.php']['Version'] ) && version_compare( $plugins['notificationx-pro/notificationx-pro.php']['Version'], '2.1.0', '>=' ) ) {
        return;
    }
    ?>
        <div class="notice notice-warning is-dismissible">
            <p>
            <?php echo sprintf(__("<strong>Recommended: </strong> Seems like you haven't updated the NotificationX Pro version. Please make sure to update NotificationX Pro plugin from <a href='%s'><strong>wp-admin -> Plugins</strong></a>.", 'notificationx' ), esc_url( admin_url('plugins.php' )));?></p>
        </div>
    <?php
}


function nx_is_plugin_active( $plugin ) {
    return in_array( $plugin, (array) get_option( 'active_plugins', array() ), true ) || nx_is_plugin_active_for_network( $plugin );
}

function nx_is_plugin_active_for_network( $plugin ) {
    if ( ! is_multisite() ) {
        return false;
    }

    $plugins = get_site_option( 'active_sitewide_plugins' );
    if ( isset( $plugins[ $plugin ] ) ) {
        return true;
    }

    return false;
}