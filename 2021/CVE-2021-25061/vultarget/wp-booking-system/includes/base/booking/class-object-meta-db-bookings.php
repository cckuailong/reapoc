<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Class that handles database queries for the Bookings
 *
 */
Class WPBS_Object_Meta_DB_Bookings extends WPBS_Object_Meta_DB {

	/**
	 * Construct
	 *
	 */
	public function __construct() {

		global $wpdb;

		$this->table_name 		 = $wpdb->prefix . 'wpbs_booking_meta';
		$this->primary_key 		 = 'booking_id';
		$this->context 	  		 = 'booking';

		add_action( 'plugins_loaded', array( $this, 'register_wpdb_column' ) );

	}


	/**
	 * Register the meta table for the bookings with the $wpdb global
	 *
	 */
	public function register_wpdb_column() {

		global $wpdb;

		$meta_table_name 		  = $this->context . 'meta';
		$wpdb->{$meta_table_name} = $this->table_name;

	}


	/**
	 * Return the table columns 
	 *
	 */
	public function get_columns() {

		return array(
			'meta_id' 	  => '%d',
			'booking_id' => '%d',
			'meta_key' 	  => '%s',
			'meta_value'  => '%s'
		);

	}


	/**
	 * Creates and updates the database table for the bookings
	 *
	 */
	public function create_table() {

		global $wpdb;

		$table_name 	 = $this->table_name;
		$charset_collate = $wpdb->get_charset_collate();

		$query = "CREATE TABLE {$table_name} (
			meta_id bigint(20) NOT NULL AUTO_INCREMENT,
			booking_id bigint(20) NOT NULL DEFAULT '0',
			meta_key varchar(255) DEFAULT NULL,
			meta_value longtext,
			PRIMARY KEY  (meta_id),
			KEY booking_id (booking_id),
			KEY meta_key (meta_key(191))
		) {$charset_collate};";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $query );

	}

}