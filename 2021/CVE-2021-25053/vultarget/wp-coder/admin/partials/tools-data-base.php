<?php 
	/**
		* Database Result
		*
		* @package     Wow Plugin
		* @copyright   Copyright (c) 2018, Dmytro Lobov
		* @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
		* @since       1.0
	*/
	
	if ( ! defined( 'ABSPATH' ) ) exit;
	global $wpdb;
	$data = $wpdb->prefix . 'wow_' . $this->plugin_pref;
	$info = ( isset( $_REQUEST['info'] ) ) ? sanitize_text_field( $_REQUEST['info'] ) : '';
	if ( $info == 'saved' ) {
		echo '<div class="updated" id="message"><p><strong>' . __( 'Record Added', 'wpcoder' ) . '</strong>.</p></div>';
	}
	elseif ( $info == 'update' ) {
		echo '<div class="updated" id="message"><p><strong>' . __( 'Record Updated', 'wpcoder' ) . '</strong>.</p></div>';
	}
	elseif ( $info == 'del' ) {
		$delid = absint( $_GET['did'] );
		$page = ( isset( $_REQUEST['page'] ) ) ? sanitize_text_field( $_REQUEST['page'] ) : '';
		$page_tool = explode('-', $page);		
		$wpdb->query( 'delete from ' . $data . ' where id=' . $delid );	
		echo '<div class="updated" id="message"><p><strong>' . __( 'Record Deleted', 'wpcoder' ) . '</strong>.</p></div>';
	}
$resultat = $wpdb->get_results( 'SELECT * FROM ' . $data . ' order by id asc' );