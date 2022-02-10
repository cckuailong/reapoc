<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Base class for all core database objects.
 *
 */
abstract class WPBS_DB {

	/**
	 * Database table name
	 *
	 * @access public
	 * @var    string
	 *
	 */
	public $table_name;

	/**
	 * Primary key of the table
	 *
	 * @access public
	 * @var    string 
	 *
	 */
	public $primary_key;

	/**
	 * The context of the object affected
	 *
	 * @access public
	 * @var    string 
	 *
	 */
	public $context = '';


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
	 * Retrieves the list of columns for the database table
	 *
	 * @access public
	 *
	 * @return array
	 *
	 */
	public function get_columns() {

		return array();

	}

}