<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Class that handles database queries for the Forms
 *
 */
Class WPBS_Object_DB_Forms extends WPBS_Object_DB {

	/**
	 * Construct
	 *
	 */
	public function __construct() {

		global $wpdb;

		$this->table_name 		 = $wpdb->prefix . 'wpbs_forms';
		$this->primary_key 		 = 'id';
		$this->context 	  		 = 'form';
		$this->query_object_type = 'WPBS_Form';

	}


	/**
	 * Return the table columns 
	 *
	 */
	public function get_columns() {

		return array(
			'id' 		    => '%d',
			'name' 		    => '%s',
			'date_created' 	=> '%s',
			'date_modified' => '%s',
			'status'		=> '%s',
			'fields'		=> '%s'
		);

	}


	/**
	 * Returns an array of WPBS_Form objects from the database
	 *
	 * @param array $args
	 * @param bool  $count - whether to return just the count for the query or not
	 *
	 * @return mixed array|int
	 *
	 */
	public function get_forms( $args = array(), $count = false ) {

		$defaults = array(
			'number'    => -1,
			'offset'    => 0,
			'orderby'   => 'id',
			'order'     => 'DESC',
			'include'   => array(),
			'search'	=> ''
		);

		$args = wp_parse_args( $args, $defaults );

		/**
		 * Filter the query arguments just before making the db call
		 *
		 * @param array $args
		 *
		 */
		$args = apply_filters( 'wpbs_get_forms_args', $args );

		// Number args
		if( $args['number'] < 1 )
			$args['number'] = 999999;

		// Where clause
		$where = "WHERE 1=1";

		// Status where clause
		if( ! empty( $args['status'] ) ) {

			$status = sanitize_text_field( $args['status'] );
			$where .= " AND status = '{$status}'";

		}

		
		// Include where clause
		if( ! empty( $args['include'] ) ) {

			$include = implode( ',', $args['include'] );
			$where  .= " AND id IN({$include})";

		}

		// Include search
		if( ! empty( $args['search'] ) ) {

			$search = sanitize_text_field( $args['search'] );
			$where  .= " AND name LIKE '%%{$search}%%'";

		}

		// Orderby
		$orderby = sanitize_text_field( $args['orderby'] );

		// Order
		$order = ( 'DESC' === strtoupper( $args['order'] ) ? 'DESC' : 'ASC' );

		$clauses = compact( 'where', 'orderby', 'order', 'count' );

		$results = $this->get_results( $clauses, $args, 'wpbs_get_form' );

		return $results;

	}


	/**
	 * Creates and updates the database table for the forms
	 *
	 */
	public function create_table() {

		global $wpdb;

		$table_name 	 = $this->table_name;
		$charset_collate = $wpdb->get_charset_collate();

		$query = "CREATE TABLE {$table_name} (
			id bigint(10) NOT NULL AUTO_INCREMENT,
			name text NOT NULL,
			date_created datetime NOT NULL,
			date_modified datetime NOT NULL,
			status text NOT NULL,
			fields longtext NOT NULL,
			PRIMARY KEY  id (id)
		) {$charset_collate};";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $query );

	}

}