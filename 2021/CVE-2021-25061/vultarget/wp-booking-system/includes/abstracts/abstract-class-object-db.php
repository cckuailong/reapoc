<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Base class for all core database objects
 *
 */
abstract class WPBS_Object_DB extends WPBS_DB {

	/**
	 * Object type to query for
	 *
	 * @access public
	 * @var    string
	 *
	 */
	public $query_object_type = 'stdClass';


	/**
	 * Constructor
	 *
	 * Subclasses should set the $table_name, $primary_key, $context and $query_object_type
	 *
	 * @access public
	 *
	 */
	public function __construct() {}


	/**
	 * Returns a table row for the given row id
	 *
	 * @param int $row_id
	 *
	 * @return mixed object|null
	 *
	 */
	public function get( $row_id ) {

		global $wpdb;

		$row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$this->table_name} WHERE {$this->primary_key} = %s LIMIT 1", $row_id ) );

		// json decode
		if( is_object( $row ) ) {

			foreach( array_keys( get_object_vars( $row ) ) as $key )
				$row->$key = $this->json_decode( $row->$key );

		}

		return $row;

	}


	/**
	 * Retrieves results for the given query clauses
	 *
	 * @param array  $clauses  - an array with SQL ready clauses
	 * @param array  $args 	   - the query args
	 * @param string $callback - a callback to be run against every returned result
	 *
	 * @return mixed array|int|null
	 *
	 */
	protected function get_results( $clauses, $args, $callback = '' ) {

		global $wpdb;

		if( true === $clauses['count'] ) {
 
			$results = $wpdb->get_var( "SELECT COUNT({$this->primary_key}) FROM {$this->table_name} {$clauses['where']}" );

			return absint( $results );

		} else {

			$results = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$this->table_name} {$clauses['where']} ORDER BY {$clauses['orderby']} {$clauses['order']} LIMIT %d, %d", absint( $args['offset'] ), absint( $args['number'] ) ) );
			
		}

		// json decode
		if( ! empty( $results ) ) {

			foreach( $results as $result_key => $row ) {

				if( is_object( $row ) ) {

					foreach( array_keys( get_object_vars( $row ) ) as $key )
						$row->$key = $this->json_decode( $row->$key );

				}

				$results[$result_key] = $row;

			}

		}


		if( ! empty( $callback ) && is_callable( $callback ) )
			$results = array_map( $callback, $results );

		return $results;

	}


	/**
	 * Inserts a new row into the database table
	 *
	 * @param array $data
	 *
	 * @return mixed int|false
	 *
	 */
	public function insert( $data ) {

		global $wpdb;

		/**
		 * Modify the data to be added into a new row just before
		 * the insert procedure
		 *
		 * @param array $data
		 *
		 */
		$data = apply_filters( "wpbs_pre_insert_{$this->context}_data", $data );

		if( empty( $data ) )
			return false;

		$column_formats = $this->get_columns();

		// Encodes the values of the array that are arrays
		foreach( $data as $key => $val ) {
			$data[$key] = $this->json_encode( $val );
		}

		// Make array keys lowercase
		$data = array_change_key_case( $data );

		// Filter out unwanted keys
		$data = array_intersect_key( $data, $column_formats );

		/**
		 * Fires just before a new row is to be inserted into the table
		 *
		 * @param array $data
		 *
		 */
		do_action( 'wpbs_pre_insert_' . $this->context, $data );

		// Arrange column formats to match data elements
		$data_keys 		= array_keys( $data );
		$column_formats = array_merge( array_flip( $data_keys ), $column_formats );

		// Insert the new row
		$inserted = $wpdb->insert( $this->table_name, $data, $column_formats );

		if( ! $inserted )
			return false;

		$insert_id = $wpdb->insert_id;

		/**
		 * Fires right after a new row has been inserted
		 *
		 * @param int   $insert_id
		 * @param array $data
		 *
		 */
		do_action( 'wpbs_insert_' . $this->context, $insert_id, $data );

		return $insert_id;

	}


	/**
	 * Updates a row from the database table
	 *
	 * @param int $row_id
	 *
	 * @return bool
	 *
	 */
	public function update( $row_id, $data ) {

		global $wpdb;

		$row_id = absint( $row_id );

		$column_formats = $this->get_columns();

		// Encodes the values of the array that are arrays
		foreach( $data as $key => $val ) {
			$data[$key] = $this->json_encode( $val );
		}

		// Make array keys lowercase
		$data = array_change_key_case( $data );

		// Filter out unwanted keys
		$data = array_intersect_key( $data, $column_formats );

		// Make sure the primary key is not included
		if( isset( $data[ $this->primary_key ] ) )
			unset( $data[ $this->primary_key ] );

		$data_keys 		= array_keys( $data );
		$column_formats = array_merge( array_flip( $data_keys ), $column_formats );

		// Update
		$updated = $wpdb->update( $this->table_name, $data, array( $this->primary_key => $row_id ), $column_formats );

		if( false === $updated )
			return false;

		/**
		 * Fires right after a row has been updated
		 *
		 * @param int   $inserted
		 * @param array $data
		 *
		 */
		do_action( 'wpbs_update_' . $this->context, $row_id, $data );

		return true;

	}


	/**
	 * Removes a row from the database table
	 *
	 * @param int $row_id
	 *
	 * @return bool
	 *
	 */
	public function delete( $row_id ) {

		global $wpdb;

		$row_id = absint( $row_id );

		/**
		 * Fires right before a row is removed from the database table
		 *
		 * @param int $row_id
		 *
		 */
		do_action( 'wpbs_pre_delete_' . $this->context, $row_id );

		// Delete the row
		$deleted = $wpdb->query( $wpdb->prepare( "DELETE FROM {$this->table_name} WHERE {$this->primary_key} = %d", $row_id ) );

		if( false === $deleted )
			return false;

		/**
		 * Fires right after the row is removed from the database table
		 *
		 * @param int $row_id
		 *
		 */
		do_action( 'wpbs_delete_' . $this->context, $row_id );

		return true;

	}


	/**
	 * Returns an instance of the $query_object_type given an object or an id
	 *
	 * @param mixed int|object
	 *
	 * @return mixed object|null
	 *
	 */
	public function get_object( $object ) {

		if( ! class_exists( $this->query_object_type ) )
			return null;

		if( $object instanceof $this->query_object_type )
			$_object = $object;

		elseif( is_object( $object ) )
			$_object = new $this->query_object_type( $object );

		else {

			$object = $this->get( $object );

			if( is_null( $object ) )
				$_object = null;
			else
				$_object = new $this->query_object_type( $object );

		}
		
		return $_object;

	}


	/**
	 * Encodes each value of a given array to json
	 *
	 * @param array $val
	 *
	 * @return string
	 *
	 */
	protected function json_encode( $val ) {

		if( is_array( $val ) || is_object( $val ) )
			$val = json_encode( $val, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );

		return $val;

	}


	/**
	 * Decodes a given json string to array
	 *
	 */
	protected function json_decode( $str ) {

		$arr = json_decode( $str, true );

		return ( json_last_error() == JSON_ERROR_NONE ? $arr : $str );

	}

}