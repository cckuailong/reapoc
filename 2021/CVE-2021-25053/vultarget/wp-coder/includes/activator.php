<?php 
	/**
		* Activator function
		*
		* @package     Wow_Plugin
		* @subpackage  
		* @copyright   Copyright (c) 2018, Dmytro Lobov
		* @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
		* @since       1.0
	*/
	
	if ( ! defined( 'ABSPATH' ) ) exit; 
	global $wpdb;
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');	
	$table = $wpdb->prefix . 'wow_' . self::PREF;	
	$sql = "CREATE TABLE " . $table." (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		title VARCHAR(200) NOT NULL,  
		param LONGTEXT,
		UNIQUE KEY id (id)
	) DEFAULT CHARSET=utf8;";
	dbDelta( $sql );
	
	$upload = wp_upload_dir();
	$upload_dir = $upload['basedir'] . '/' . $field;
	wp_mkdir_p( $upload_dir );