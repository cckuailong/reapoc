<?php
/**
 * Include data param for Wow Plugin
 *
 * @package     Wow_Plugin
 * @subpackage  Admin/Data_Item
 * @author      Dmytro Lobov <i@wpbiker.com>
 * @copyright   2019 Wow-Company
 * @license     GNU Public License
 * @version     1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$act        = ( isset( $_REQUEST["act"] ) ) ? sanitize_text_field( $_REQUEST["act"] ) : '';
$param      = '';
$tool_id    = '';
$btn        = esc_attr__( 'Save', 'modal-window' );
$add_action = '1';

if ( $act == "update" ) {
	$id = absint( $_REQUEST["id"] );
	$result = $wpdb->get_row( "SELECT * FROM $data WHERE id = $id" );
	if ( $result ) {
		$id         = $result->id;
		$title      = $result->title;
		$param      = unserialize( $result->param );
		$status  = $result->status;
		$tool_id    = $id;
		$add_action = 2;
		$btn        = esc_attr__( 'Update', 'modal-window' );
	}
} elseif ( $act == "duplicate" ) {
	$id = absint( $_REQUEST["id"] );
	$result = $wpdb->get_row( "SELECT * FROM $data WHERE id = $id" );
	if ( $result ) {
		$id    = "";
		$title = "";
		$param = unserialize( $result->param );
		$last  = $wpdb->get_col( "SELECT id FROM $data" );;
		$tool_id    = max( $last ) + 1;
		$status  = '1';
	}
} else {
	$id         = "";
	$title      = "";
	$last       = $wpdb->get_col( "SELECT id FROM $data" );
	$tool_id    = ! empty( $last ) ? max( $last ) + 1 : 1;
	$param      = '';
	$status  = '1';
}
