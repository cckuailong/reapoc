<?php
/**
 * Update Datatable
 *
 * @package     Wow_Plugin
 * @subpackage  Update Database to version 2.0
 * @author      Wow-Company <support@wow-company.com>
 * @copyright   2019 Wow-Company
 * @license     GNU Public License
 * @version     1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( get_option( 'wow_button_data' ) === false ) {
	global $wpdb;
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	$table = $wpdb->prefix . 'wow_' . self::PREF;
	$sql   = "CREATE TABLE " . $table . " (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		title VARCHAR(200) NOT NULL,  
		param TEXT,
		UNIQUE KEY id (id)
		) DEFAULT CHARSET=utf8;";
	dbDelta( $sql );
	$old_data  = $wpdb->prefix . 'lg_tools';
	$arrresult = $wpdb->get_results( "select * from " . $old_data );
	$columns   = count( $arrresult );
	for ( $i = 0; $i < $columns; $i ++ ) {
		$arr = array();
		foreach ( $arrresult[ $i ] as $key => $val ) {
			if ( $key == 'title' ) {
				$title = $val;
			} elseif ( $key == 'id' ) {
				$id = $val;
			} elseif ( $key == 'param' ) {
				$param = $val;
			}
		}
		$data = array(
			'id'    => $id,
			'title' => $title,
			'param' => $param,
		);

		$tool_view   = get_option( '_lg_tool_button_view_counter_' . $id, '0' );
		$tool_action = get_option( '_lg_tool_button_action_counter_' . $id, '0' );

		$prefix = self::PREF;
		$option_name_view   = '_' . $prefix . '_view_counter_' . $id;
		$option_name_action = '_' . $prefix . '_action_counter_' . $id;

		update_option( $option_name_view, $tool_view );
		update_option( $option_name_action, $tool_action );

		$wpdb->insert( $table, $data );
	}
	update_option( 'wow_button_data', '2.0' );
}
