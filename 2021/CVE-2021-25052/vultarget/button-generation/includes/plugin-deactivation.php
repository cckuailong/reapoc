<?php
/**
 * Deactivation function
 *
 * @package     Wow_Plugin
 * @subpackage  Includes/Dectivation
 * @author      Wow-Company <support@wow-company.com>
 * @copyright   2019 Wow-Company
 * @license     GNU Public License
 * @version     1.0

 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get the plugin folder in 'uploads'
 */
$upload  = wp_upload_dir();
$field   = dirname( plugin_basename( __FILE__ ), 2 );
$basedir = $upload['basedir'] . '/' . $field . '/';

/**
 * Delete the plugin folder and files from wp-upload
 */
if ( is_dir( $basedir ) ) {
	
	$is_empty = count( glob( $basedir . '*' ) ) ? true : false;
	if ( $is_empty === true ) {
		$handle = opendir( $basedir );
		
		while ( false !== ( $file = readdir( $handle ) ) ) {
			
			if ( $file != "." && $file != ".." ) {
				wp_delete_file( $basedir . $file );
			}
			
		}
		closedir( $basedir );
	}
	rmdir( $basedir );
}
