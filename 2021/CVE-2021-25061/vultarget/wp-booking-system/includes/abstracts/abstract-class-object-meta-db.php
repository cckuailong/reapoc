<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Base class for all core database objects.
 *
 */
abstract class WPBS_Object_Meta_DB extends WPBS_DB {


	/**
	 * Constructor
	 *
	 * Subclasses should set the $table_name, $primary_key, $context
	 *
	 * @access public
	 *
	 */
	public function __construct() {}


	/**
	 * Inserts a new meta entry for the object
	 *
	 * @param int    $object_id
	 * @param string $meta_key
	 * @param string $meta_value
	 * @param bool   $unique
	 *
	 * @return mixed int|false
	 *
	 */
	public function add( $object_id, $meta_key, $meta_value, $unique = false ) {

		return add_metadata( $this->context, $object_id, $meta_key, $meta_value, $unique );

	}


	/**
	 * Updates a meta entry for the object
	 *
	 * @param int    $object_id
	 * @param string $meta_key
	 * @param string $meta_value
	 * @param bool   $prev_value
	 *
	 * @return bool
	 *
	 */
	public function update( $object_id, $meta_key, $meta_value, $prev_value = '' ) {

		return update_metadata( $this->context, $object_id, $meta_key, $meta_value, $prev_value );

	}


	/**
	 * Returns a meta entry for the object
	 *
	 * @param int    $object_id
	 * @param string $meta_key
	 * @param bool   $single
	 *
	 * @return mixed
	 *
	 */
	public function get( $object_id, $meta_key = '', $single = false ) {

		return get_metadata( $this->context, $object_id, $meta_key, $single );

	}


	/**
	 * Removes a meta entry for the object
	 *
	 * @param int    $object_id
	 * @param string $meta_key
	 * @param string $meta_value
	 * @param bool   $delete_all
	 *
	 * @return bool
	 *
	 */
	public function delete( $object_id, $meta_key, $meta_value = '', $delete_all = '' ) {

		return delete_metadata( $this->context, $object_id, $meta_key, $meta_value, $delete_all );

	}

}