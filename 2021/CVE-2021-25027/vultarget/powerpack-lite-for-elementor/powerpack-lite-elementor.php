<?php
/**
 * Plugin Name: PowerPack Lite for Elementor
 * Plugin URI: https://powerpackelements.com
 * Description: Extend Elementor Page Builder with 30+ Creative Widgets and exciting extensions.
 * Version: 2.6.1
 * Author: IdeaBox Creations
 * Author URI: http://ideabox.io/
 * License: GNU General Public License v2.0
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: powerpack
 * Domain Path: /languages
 * Elementor tested up to: 3.4.8
 * Elementor Pro tested up to: 3.5.1
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( defined( 'POWERPACK_ELEMENTS_VER' ) ) {
	return;
}

define( 'POWERPACK_ELEMENTS_LITE_VER', '2.6.1' );
define( 'POWERPACK_ELEMENTS_LITE_PATH', plugin_dir_path( __FILE__ ) );
define( 'POWERPACK_ELEMENTS_LITE_BASE', plugin_basename( __FILE__ ) );
define( 'POWERPACK_ELEMENTS_LITE_URL', plugins_url( '/', __FILE__ ) );
define( 'POWERPACK_ELEMENTS_LITE_ELEMENTOR_VERSION_REQUIRED', '3.0.0' );
define( 'POWERPACK_ELEMENTS_LITE_PHP_VERSION_REQUIRED', '5.6' );

require_once POWERPACK_ELEMENTS_LITE_PATH . 'includes/helper-functions.php';
require_once POWERPACK_ELEMENTS_LITE_PATH . 'classes/class-pp-tracking.php';
require_once POWERPACK_ELEMENTS_LITE_PATH . 'classes/class-pp-admin-settings.php';
require_once POWERPACK_ELEMENTS_LITE_PATH . 'classes/class-pp-config.php';
require_once POWERPACK_ELEMENTS_LITE_PATH . 'classes/class-pp-helper.php';
require_once POWERPACK_ELEMENTS_LITE_PATH . 'classes/class-pp-posts-helper.php';
require_once POWERPACK_ELEMENTS_LITE_PATH . 'classes/class-pp-wpml.php';
require_once POWERPACK_ELEMENTS_LITE_PATH . 'plugin.php';
if ( did_action( 'elementor/loaded' ) ) {
	require_once POWERPACK_ELEMENTS_LITE_PATH . 'classes/class-pp-templates-lib.php';
}

/**
 * Check if Elementor is installed
 *
 * @since 1.0
 */
function pp_elements_lite_is_elementor_installed() {
	$file_path = 'elementor/elementor.php';
	$installed_plugins = get_plugins();
	return isset( $installed_plugins[ $file_path ] );
}

/**
 * Shows notice to user if Elementor plugin
 * is not installed or activated or both
 *
 * @since 1.0
 */
function pp_elements_lite_fail_load() {
    $plugin = 'elementor/elementor.php';

	if ( pp_elements_lite_is_elementor_installed() ) {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}

		$activation_url = wp_nonce_url( 'plugins.php?action=activate&amp;plugin=' . $plugin . '&amp;plugin_status=all&amp;paged=1&amp;s', 'activate-plugin_' . $plugin );
        $message = __( 'PowerPack requires Elementor plugin to be active. Please activate Elementor to continue.', 'powerpack' );
		$button_text = __( 'Activate Elementor', 'powerpack' );

	} else {
		if ( ! current_user_can( 'install_plugins' ) ) {
			return;
		}

		$activation_url = wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=elementor' ), 'install-plugin_elementor' );
        $message = sprintf( __( 'PowerPack requires %1$s"Elementor"%2$s plugin to be installed and activated. Please install Elementor to continue.', 'powerpack' ), '<strong>', '</strong>' );
		$button_text = __( 'Install Elementor', 'powerpack' );
	}

	$button = '<p><a href="' . $activation_url . '" class="button-primary">' . $button_text . '</a></p>';
    
    printf( '<div class="error"><p>%1$s</p>%2$s</div>', esc_html( $message ), $button );
}

/**
 * Shows notice to user if
 * Elementor version if outdated
 *
 * @since 1.0
 *
 */
function pp_elements_lite_fail_load_out_of_date() {
    if ( ! current_user_can( 'update_plugins' ) ) {
		return;
	}
    
	$message = __( 'PowerPack requires Elementor version at least ' . POWERPACK_ELEMENTS_LITE_ELEMENTOR_VERSION_REQUIRED . '. Please update Elementor to continue.', 'powerpack' );

	printf( '<div class="error"><p>%1$s</p></div>', esc_html( $message ) );
}

/**
 * Shows notice to user if minimum PHP
 * version requirement is not met
 *
 * @since 1.0
 *
 */
function pp_elements_lite_fail_php() {
	$message = __( 'PowerPack requires PHP version ' . POWERPACK_ELEMENTS_LITE_PHP_VERSION_REQUIRED .'+ to work properly. The plugins is deactivated for now.', 'powerpack' );

	printf( '<div class="error"><p>%1$s</p></div>', esc_html( $message ) );

	if ( isset( $_GET['activate'] ) ) 
		unset( $_GET['activate'] );
}

/**
 * Deactivates the plugin
 *
 * @since 1.0
 */
function pp_elements_lite_deactivate() {
	deactivate_plugins( plugin_basename( __FILE__ ) );
}

/**
 * Load theme textdomain
 *
 * @since 1.0
 *
 */
function pp_elements_lite_load_plugin_textdomain() {
	load_plugin_textdomain( 'powerpack', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}

add_action( 'plugins_loaded', 'pp_elements_lite_init' );

function pp_elements_lite_init() {
    if ( class_exists( 'Caldera_Forms' ) ) {
        add_filter( 'caldera_forms_force_enqueue_styles_early', '__return_true' );
    }

    // Notice if the Elementor is not active
	if ( ! did_action( 'elementor/loaded' ) ) {
		add_action( 'admin_notices', 'pp_elements_lite_fail_load' );
		return;
	}

	// Check for required Elementor version
	if ( ! version_compare( ELEMENTOR_VERSION, POWERPACK_ELEMENTS_LITE_ELEMENTOR_VERSION_REQUIRED, '>=' ) ) {
		add_action( 'admin_notices', 'pp_elements_lite_fail_load_out_of_date' );
		add_action( 'admin_init', 'pp_elements_lite_deactivate' );
		return;
	}
    
    // Check for required PHP version
	if ( ! version_compare( PHP_VERSION, POWERPACK_ELEMENTS_LITE_PHP_VERSION_REQUIRED, '>=' ) ) {
		add_action( 'admin_notices', 'pp_elements_lite_fail_php' );
		add_action( 'admin_init', 'pp_elements_lite_deactivate' );
		return;
	}
    
    add_action( 'init', 'pp_elements_lite_load_plugin_textdomain' );

	$is_plugin_activated = get_option( 'pp_plugin_activated' );
	if ( current_user_can('activate_plugins') && 'yes' !== $is_plugin_activated ) {
		update_option( 'pp_install_date', current_time( 'mysql' ) );
		update_option( 'pp_plugin_activated', 'yes' );
	}
}

/**
 * Check if PowerPack Elements is active
 *
 * @since 1.2.9.4
 *
 */
if ( ! function_exists( 'is_pp_elements_active' ) ) {
	function is_pp_elements_active() {
		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

		$plugin = 'powerpack-elements/powerpack-elements.php';

		return is_plugin_active( $plugin ) || function_exists( 'pp_init' );
	}
}

/**
 * Add settings page link to plugin page
 *
 * @since 1.4.4
 */
function pp_elements_lite_add_plugin_page_settings_link( $links ) {
	$links[] = '<a href="' . admin_url( 'admin.php?page=powerpack-settings' ) . '">' . __('Settings', 'powerpack') . '</a>';
	return $links;
}
add_filter('plugin_action_links_' . POWERPACK_ELEMENTS_LITE_BASE, 'pp_elements_lite_add_plugin_page_settings_link');

 
function pp_add_description_links( $plugin_meta, $plugin_file ) {

	if ( POWERPACK_ELEMENTS_LITE_BASE === $plugin_file ) {
		$row_meta = [
			'docs' => '<a href="https://powerpackelements.com/docs/?utm_source=doclink&utm_medium=widget&utm_campaign=lite" aria-label="' . esc_attr( __( 'View PowerPack Documentation', 'powerpack' ) ) . '" target="_blank">' . __( 'Docs & FAQs', 'powerpack' ) . '</a>',
			'ideo' => '<a href="https://powerpackelements.com/?utm_source=plugin&utm_medium=list&utm_campaign=lite" aria-label="' . esc_attr( __( 'Go Pro', 'powerpack' ) ) . '" target="_blank" style="font-weight:bold;">' . __( 'Go Pro', 'powerpack' ) . '</a>',
		];

		$plugin_meta = array_merge( $plugin_meta, $row_meta );
	}

	return $plugin_meta;
}

add_filter( 'plugin_row_meta', 'pp_add_description_links', 10, 4 );