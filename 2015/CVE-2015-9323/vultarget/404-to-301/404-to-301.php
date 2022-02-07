<?php
/**
 * Plugin Name:       404 to 301
 * Plugin URI:        http://iscode.co/product/404-to-301/
 * Description:       Automatically redirect all <strong>404 errors</strong> to any page using <strong>301 redirect for SEO</strong>. You can <strong>redirect and log</strong> every 404 errors. No more 404 errors in Webmaster tool.
 * Version:           2.0.2
 * Author:            Joel James
 * Author URI:        http://iscode.co/
 * Donate link:		  https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=XUVWY8HUBUXY4
 * License:           GPL-2.0+
 * License URI:		  http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       404-to-301
 * Domain Path:       /languages
 *
 * 404 to 301 is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * 404 to 301 is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Easy Digital Downloads. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package I4T3
 * @category Core
 * @author Joel James
 * @version 2.0.0
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die('Damn it.! Dude you are looking for what?');
}

if(!defined('I4T3_PATH')){
	define( 'I4T3_PATH', home_url( PLUGINDIR . '/404-to-301/' ) );
}
if(!defined('I4T3_PLUGIN_DIR')) {
	define( 'I4T3_PLUGIN_DIR', __FILE__ );
}
if(!defined('I4T3_SETTINGS_PAGE')) {
	define( 'I4T3_SETTINGS_PAGE', admin_url( 'admin.php?page=i4t3-settings' ) );
}
if(!defined('I4T3_LOGS_PAGE')) {
	define( 'I4T3_LOGS_PAGE', admin_url( 'admin.php?page=i4t3-logs' ) );
}
if(!defined('I4T3_DB_VERSION')) {
	define( 'I4T3_DB_VERSION', '2' );
}
if(!defined('I4T3_VERSION')) {
	define( 'I4T3_VERSION', '2.0.2' );
}
// Set who all can access 404 settings. You can change this if you want to give others access.
if(!defined('I4T3_ADMIN_PERMISSION')) {
	define( 'I4T3_ADMIN_PERMISSION', 'manage_options' );
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-dcl-activator.php
 */
function activate_i4t3() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-404-to-301-activator.php';
	_404_To_301_Activator::activate();
}
register_activation_hook( __FILE__, 'activate_i4t3' );

/**
 * The core plugin class that is used to define
 * dashboard-specific hooks, and public-facing site hooks.
 */
require_once plugin_dir_path( __FILE__ ) . 'includes/class-404-to-301.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    2.0.0
 */
function run_i4t3() {

	$plugin = new _404_To_301();
	$plugin->run();

}
run_i4t3();

//*** Thank you for your interest in 404 to 301 - Developed and managed by Joel James ***// 