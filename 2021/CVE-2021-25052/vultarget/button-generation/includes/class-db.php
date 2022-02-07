<?php
/**
 * DataBase Update
 *
 * @package     Wow_Plugin
 * @subpackage  Includes/Database
 * @author      Wow-Company <support@wow-company.com>
 * @copyright   2019 Wow-Company
 * @license     GNU Public License
 * @version     1.0

 */

namespace button_generator;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Creates and updates a record in the database
 *
 * @since 1.0
 *
 * @property string plugin_dir - filesystem directory path for the plugin
 * @property string basedir    - URL to the file which saving CSS and JS files
 */
class Wow_DB_Update {
	
	/**
	 * Setup folder fo saving CSS and JS files
	 *
	 * @param string $plugin_dir  filesystem directory path for the plugin
	 * @param string $plugin_slug URL directory path for the plugin
	 *
	 * @since 1.0
	 */
	function __construct( $plugin_dir, $plugin_slug ) {
		$this->plugin_dir = $plugin_dir;
		$upload           = wp_upload_dir();
		$this->basedir    = $upload['basedir'] . '/' . $plugin_slug . '/';
	}
	
	/**
	 * Create a new Item in the database
	 *
	 * @param string $table name of the datatable
	 * @param string $info  array of parameters to save to the database
	 *
	 * @since 1.0
	 */
	function create_item( $table, $info ) {
		global $wpdb;
		$fields = $wpdb->get_col( "DESC " . $table, 0 );
		// Collect all passed parameters
		$data = array();
		foreach ( $fields as $key ) {
			if ( is_array( $info[ $key ] ) == true ) {
				$data[ $key ] = serialize( $info[ $key ] );
			} else {
				$data[ $key ] = $info[ $key ];
			}
		}
		// Insert in database
		$wpdb->insert( $table, $data );
		// Get information for creating JS and CSS files
		$last_id = $wpdb->insert_id;
		$result  = $wpdb->get_results( "SELECT * FROM " . $table . " WHERE id = " . $last_id );
		if ( count( $result ) > 0 ) {
			foreach ( $result as $key => $val ) {
				$param = unserialize( $val->param );
				// Create the JS file in the plugin folder in uploads
				$file_script = $this->plugin_dir . 'admin/partials/generator/script.php';
				if ( file_exists( $file_script ) ) {
					$path_script = $this->basedir . 'script-' . $last_id . '.js';
					ob_start();
					include( $file_script );
					$content_script = ob_get_contents();
					ob_end_clean();
					file_put_contents( $path_script, $content_script );
				}
				
				// Create the CSS file in the plugin folder in uploads
				$file_style = $this->plugin_dir . 'admin/partials/generator/style.php';
				if ( file_exists( $file_style ) ) {
					$path_style = $this->basedir . 'style-' . $last_id . '.css';
					ob_start();
					include( $file_style );
					$content_style = ob_get_contents();
					ob_end_clean();
					file_put_contents( $path_style, $content_style );
				}
			}
		}
	}
	
	/**
	 * Update an existing item in the database
	 *
	 * @param string $table name of the datatable
	 * @param string $info  array of parameters to save to the database
	 *
	 * @since 1.0
	 */
	function update_item( $table, $info ) {
		global $wpdb;
		$fields = $wpdb->get_col( "DESC " . $table, 0 );
		// Collect all passed parameters
		$data = array();
		foreach ( $fields as $key ) {
			if ( is_array( $info[ $key ] ) == true ) {
				$data[ $key ] = serialize( $info[ $key ] );
			} else {
				$data[ $key ] = $info[ $key ];
			}
		}
		
		$id = absint( $info["id"] );
		$wpdb->update( $table, $data, array( 'id' => $id ), $format = null, $where_format = null );
		// Get information for creating JS and CSS files
		$result = $wpdb->get_results( "SELECT * FROM " . $table . " WHERE id = " . $id );
		if ( count( $result ) > 0 ) {
			foreach ( $result as $key => $val ) {
				$param       = unserialize( $val->param );
				$file_script = $this->plugin_dir . 'admin/partials/generator/script.php';
				if ( file_exists( $file_script ) ) {
					$path_script = $this->basedir . 'script-' . $id . '.js';
					ob_start();
					include( $file_script );
					$content_script = ob_get_contents();
					ob_end_clean();
					file_put_contents( $path_script, $content_script );
				}
				$file_style = $this->plugin_dir . 'admin/partials/generator/style.php';
				if ( file_exists( $file_style ) ) {
					$path_style = $this->basedir . '/style-' . $id . '.css';
					ob_start();
					include( $file_style );
					$content_style = ob_get_contents();
					ob_end_clean();
					file_put_contents( $path_style, $content_style );
				}
			}
		}
	}
}
