<?php
/**
 * Code Snippets - An easy, clean and simple way to add code snippets to your site.
 *
 * If you're interested in helping to develop Code Snippets, or perhaps contribute
 * to the localization, please see https://github.com/sheabunge/code-snippets
 *
 * @package   Code_Snippets
 * @author    Shea Bunge <shea@sheabunge.com>
 * @copyright 2012-2021 Shea Bunge
 * @license   MIT http://opensource.org/licenses/MIT
 * @version   2.14.2
 * @link      https://github.com/sheabunge/code-snippets
 */

/*
Plugin Name: Code Snippets
Plugin URI:  https://github.com/sheabunge/code-snippets
Description: An easy, clean and simple way to run code snippets on your site. No need to edit to your theme's functions.php file again!
Author:       Code Snippets Pro
Author URI:   https://codesnippets.pro
Version:      2.14.2
License:      MIT
License URI:  license.txt
Text Domain:  code-snippets
Domain Path:  /languages
Requires PHP: 5.2
Requires at least: 3.6
*/

/* Exit if accessed directly */
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/* If a version of code snippets has already been loaded, then deactivate this plugin. */
if ( defined( 'CODE_SNIPPETS_FILE' ) ) {
	require_once ABSPATH . 'wp-admin/includes/plugin.php';
	deactivate_plugins( array( 'code-snippets/code-snippets.php' ), true );

	if ( ! function_exists( 'code_snippets_deactivated_old_version_notice' ) ) {
		function code_snippets_deactivated_old_version_notice() {
			echo '<div class="error fade"><p>',
			esc_html__( 'Another version of Code Snippets appears to be installed. Deactivating this version.', 'code-snippets' ),
			'</p></div>';
		}
	}

	add_action( 'admin_notices', 'code_snippets_deactivated_old_version_notice', 11 );
	return;
}

/**
 * The full path to the main file of this plugin
 *
 * This can later be passed to functions such as
 * plugin_dir_path(), plugins_url() and plugin_basename()
 * to retrieve information about plugin paths
 *
 * @since 2.0
 * @var string
 */
define( 'CODE_SNIPPETS_FILE', __FILE__ );

/**
 * Enable autoloading of plugin classes
 * @param $class_name
 */
function code_snippets_autoload( $class_name ) {

	/* Only autoload classes from this plugin */
	if ( 'Code_Snippet' !== $class_name && 'Code_Snippets' !== substr( $class_name, 0, 13 ) ) {
		return;
	}

	/* Remove namespace from class name */
	$class_file = str_replace( 'Code_Snippets_', '', $class_name );

	if ( 'Code_Snippet' === $class_name ) {
		$class_file = 'Snippet';
	}

	/* Convert class name format to file name format */
	$class_file = strtolower( $class_file );
	$class_file = str_replace( '_', '-', $class_file );

	$class_path = dirname( __FILE__ ) . '/php/';

	if ( 'Menu' === substr( $class_name, -4, 4 ) ) {
		$class_path .= 'admin-menus/';
	}

	/* Load the class */
	require_once $class_path . "class-{$class_file}.php";
}

try {
	spl_autoload_register( 'code_snippets_autoload' );
} catch ( Exception $e ) {
	new WP_Error( $e->getCode(), $e->getMessage() );
}

/**
 * Retrieve the instance of the main plugin class
 *
 * @since 2.6.0
 * @return Code_Snippets
 */
function code_snippets() {
	static $plugin;

	if ( is_null( $plugin ) ) {
		$plugin = new Code_Snippets( '2.14.2', __FILE__ );
	}

	return $plugin;
}

code_snippets()->load_plugin();

/* Execute the snippets once the plugins are loaded */
add_action( 'plugins_loaded', 'execute_active_snippets', 1 );
