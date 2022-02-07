<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://wordpress.org/plugins/simple-job-board
 * @since             1.0.0
 * @package           Simple_Job_Board
 *
 * @wordpress-plugin
 * Plugin Name:       Simple Job Board
 * Plugin URI:        https://wordpress.org/plugins/simple-job-board/simple-job-board-uri
 * Description:       Powerful & Robust plugin to create a Job Board on your website in simple & elegant way.
 * Version:           2.9.3
 * Author:            PressTigers
 * Author URI:        http://pressTigers.com
 * License:           GPL-3.0+
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain:       simple-job-board
 * Domain Path:       /languages
 */
// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

// Define Plugin Contant
if (!defined('SJB_PLUGIN_VERSION')) {
    define('SJB_PLUGIN_VERSION', '2.9.3');
}

update_option('sjb_version', SJB_PLUGIN_VERSION);

/**
 *  Show SJB Upgrade Notice
 */
function sjb_showUpgradeNotification($currentPluginMetadata, $newPluginMetadata)
{

    // check "upgrade_notice"
    if (isset($newPluginMetadata->upgrade_notice) && strlen(trim($newPluginMetadata->upgrade_notice)) > 0) {
        echo '<br><br><strong>Important Upgrade Notice:</strong> ' . strip_tags($newPluginMetadata->upgrade_notice) . '';
    }
}

// Show SJB Upgrade Notice
add_action('in_plugin_update_message-simple-job-board/simple-job-board.php', 'sjb_showUpgradeNotification', 10, 2);

/**
 * The code that runs duringin plugin activation.
 * This action is documented in includes/class-simple-job-board-activator.php
 */
function activate_simple_job_board()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-simple-job-board-activator.php';
    Simple_Job_Board_Activator::activate();
}

function sjb_wp_upe_upgrade_completed( $upgrader_object, $options ) {

 // The path to our plugin's main file
 $our_plugin = 'simple-job-board/simple-job-board.php';


 // If an update has tken place and the updated type is plugins and the plugins element exists
 if( $options['action'] == 'update' && $options['type'] == 'plugin' && isset( $options['plugins'] ) ) {
  // Iterate through the plugins being updated and check if ours is there
  foreach( $options['plugins'] as $plugin ) {
   if( $plugin == $our_plugin ) {

    update_option( 'sjb_update_ui_notice', 'Versi0n 2.4 is Updated' );
   }
  }
 }
}
add_action( 'upgrader_process_complete', 'sjb_wp_upe_upgrade_completed', 10, 2 );


/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-simple-job-board-deactivator.php
 */
function deactivate_simple_job_board()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-simple-job-board-deactivator.php';
    Simple_Job_Board_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_simple_job_board');
register_deactivation_hook(__FILE__, 'deactivate_simple_job_board');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-simple-job-board.php';

/*
 * Initialize Job Board Shortcode Block
 */
require_once plugin_dir_path(__FILE__) . 'sjb-block/src/init.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_simple_job_board()
{
    $plugin = new Simple_Job_Board();
    $plugin->run();
}

run_simple_job_board();