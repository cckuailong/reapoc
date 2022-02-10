<?php 
	
	/**
		* Include data param for Wow Plugin
		*
		* @package     Wow_Plugin
		* @copyright   Copyright (c) 2018, Dmytro Lobov
		* @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
		* @since       1.0
	*/
	if ( ! defined( 'ABSPATH' ) ) exit;
	
	$act = ( isset( $_REQUEST["act"] ) ) ? sanitize_text_field( $_REQUEST["act"] ) : '';
	if ($act == "update") {
		$recid = absint( $_REQUEST["id"] );
		$result = $wpdb->get_row("SELECT * FROM $data WHERE id=$recid");
		if ($result){
			$id = $result->id;
			$title = $result->title;
			$param = unserialize( $result->param );
			$tool_id = $id;
			$hidval = 2;
			$btn = __( 'Update', 'wpcoder' );
		}
	}
	else if ($act == "duplicate") {
		$recid = $_REQUEST["id"];
		$result = $wpdb->get_row("SELECT * FROM $data WHERE id=$recid");
		if ($result){
			$id = "";
			$title = "";
			$param = unserialize($result->param);		
			$last  = $wpdb->get_col( "SELECT id FROM $data" );;
			$tool_id    = max( $last ) + 1;		
			$hidval = 1;
			$btn = __( 'Save', 'wpcoder' );
		}
	}
	else {    
    $id = "";
    $title = "";
		$last  = $wpdb->get_col( "SELECT id FROM $data" );
		$tool_id    = !empty($last) ?  max( $last ) + 1 : 1;		
		$param = '';
		$hidval = 1;		
		$btn = __( 'Save', 'wpcoder' );
	}
	
?>