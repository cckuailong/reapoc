<?php
/**
 * Activation function
 *
 * @package     Wow_Plugin
 * @subpackage  Includes/Activation
 * @author      Dmytro Lobov <i@wpbiker.com>
 * @copyright   2019 Wow-Company
 * @license     GNU Public License
 * @version     1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $wpdb;
require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
// Create the database for plugin
$table = $wpdb->prefix . 'wow_' . $prefix;
$sql   = "CREATE TABLE " . $table . " (
	id mediumint(9) NOT NULL AUTO_INCREMENT,
	title VARCHAR(200) NOT NULL,
	param TEXT,
	script TEXT,
	style TEXT,
	status INT,
	UNIQUE KEY id (id)
) DEFAULT CHARSET=utf8;";
dbDelta( $sql );

deactivate_plugins( 'wow-modal-windows-pro/wow-modal-windows-pro.php' );
