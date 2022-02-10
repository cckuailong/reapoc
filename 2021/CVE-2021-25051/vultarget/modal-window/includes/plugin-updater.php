<?php
/**
 * Update plugin data to new version
 *
 * @package     Wow_Plugin
 * @copyright   Copyright (c) 2018, Dmytro Lobov
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

namespace modal_window;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$info = self::information();

if ( get_option( 'wow_' . $info['plugin']['prefix'] . '_updater_5.0' ) === false ) {

	global $wpdb;
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	$table = $wpdb->prefix . 'wow_' . $info['plugin']['prefix'];

	$sql = "CREATE TABLE " . $table . " (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		title VARCHAR(200) NOT NULL,
		param TEXT,
		script TEXT,
		style TEXT,
		status INT,
		UNIQUE KEY id (id)
	) DEFAULT CHARSET=utf8;";
	dbDelta( $sql );

	$result = $wpdb->get_results( "SELECT * FROM " . $table . " order by id asc" );
	if ( count( $result ) > 0 ) {

		foreach ( $result as $key => $val ) {
			$id = $val->id;
			$param = unserialize( $val->param );
			$path = $info['plugin']['dir'] . 'admin/generate/';

			$script = array();
			include( $path . 'script.php' );
			$in_script =  wp_json_encode( $script );

			$css = '';
			include( $path . 'style.php' );
			$in_style =  $css;

			$wpdb->update( $table, array( 'script' => $in_script, 'style' => $in_style, 'status' => 1 ), array( 'id' => $id ) );
		}


	}

	update_option( 'wow_' . $info['plugin']['prefix'] . '_updater_5.0', '5.0' );
}
