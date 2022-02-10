<?php 
	/**
		* DataBase Update
		*
		* @package     Wow_Plugin
		* @copyright   Copyright (c) 2018, Dmytro Lobov
		* @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
		* @since       1.0
	*/
	
	namespace wpcoder;
	
	if ( ! defined( 'ABSPATH' ) ) exit; 
	
	class Wow_DB_Update {	
		
		function __construct( $plugin_dir, $plugin_slug ) {		
			$this->plugin_dir  = $plugin_dir;
			$upload = wp_upload_dir();
			$this->basedir = $upload['basedir'] . '/' . $plugin_slug . '/';			
		}
		
		function addNewItem( $tblname, $info ) {
			global $wpdb;
			$fields = $wpdb->get_col( "DESC " . $tblname, 0);			
			$data = array();            
			foreach ( $fields as $key ) {
				if ( is_array( $info[$key] ) == true ){
					$data[$key] = serialize( $info[$key] );
				}
				else {
					$data[$key] = $info[$key];
				}				                
			}
			$wpdb->insert( $tblname, $data );			
			$lastid = $wpdb->insert_id; 
			$result = $wpdb->get_results( "SELECT * FROM " . $tblname . " WHERE id = " . $lastid );			
			if ( count( $result ) > 0 ) {
				foreach ( $result as $key => $val ) {
					$param = unserialize( $val->param );					
					$file_script = $this->plugin_dir . 'admin/partials/generator/script.php';
					if ( file_exists ( $file_script ) ) {
						$path_script = $this->basedir . 'script-' . $lastid . '.js';
						ob_start();
						include ( $file_script );
						$content_script = ob_get_contents();
						ob_end_clean();
						file_put_contents( $path_script, $content_script );
					}
					$file_style = $this->plugin_dir . 'admin/partials/generator/style.php';
					if ( file_exists ( $file_style ) ) {
						$path_style = $this->basedir . 'style-' . $lastid . '.css';
						ob_start();
						include ( $file_style );
						$content_style = ob_get_contents();										
						ob_end_clean();
						file_put_contents( $path_style, $content_style );
					}				
				}
			}		
		}
		function updItem( $tblname, $info ) {
			global $wpdb;		
			$fields = $wpdb->get_col( "DESC " . $tblname, 0);			
			$data = array();            
			foreach ( $fields as $key ) {
				if (is_array( $info[$key] ) == true ) {
					$data[$key] = serialize( $info[$key] );
				}
				else {
					$data[$key] = $info[$key];
				}				                
			}
			$where = array( 'id' => $info["id"] );
			$id = $info["id"];			
			$wpdb->update( $tblname, $data, array('id' => $id ), $format = null, $where_format = null );			
			$result = $wpdb->get_results( "SELECT * FROM ".$tblname." WHERE id = " . $id );	
			if (count( $result ) > 0 ) {
				foreach ( $result as $key => $val ) {
					$param = unserialize($val->param);
					$file_script = $this->plugin_dir . 'admin/partials/generator/script.php';
					if ( file_exists ( $file_script ) ) {
						$path_script = $this->basedir . 'script-' . $id . '.js';
						ob_start();
						include ( $file_script );
						$content_script = ob_get_contents();
						ob_end_clean();
						file_put_contents( $path_script, $content_script );
					}
					$file_style = $this->plugin_dir . 'admin/partials/generator/style.php';
					if ( file_exists ( $file_style ) ) {
						$path_style = $this->basedir . '/style-' . $id . '.css';
						ob_start();
						include ( $file_style );
						$content_style = ob_get_contents();										
						ob_end_clean();
						file_put_contents( $path_style, $content_style );
					}				
				}			
			}
		}		
	}
