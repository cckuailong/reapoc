<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * The main class for the Form
 *
 */
class WPBS_Form extends WPBS_Base_Object {

	/**
	 * The Id of the legend item
	 *
	 * @access protected
	 * @var    int
	 *
	 */
	protected $id;

	/**
	 * The legend item name
	 *
	 * @access protected
	 * @var    string
	 *
	 */
	protected $name;

	/**
	 * The date when the form was created
	 *
	 * @access protected
	 * @var    string
	 *
	 */
	protected $date_created;

	/**
	 * The date when the form was last modified
	 *
	 * @access protected
	 * @var    string
	 *
	 */
	protected $date_modified;

	/**
	 * The status of the form
	 *
	 * @access protected
	 * @var    string
	 *
	 */
	protected $status;

	/**
	 * The form fields
	 *
	 * @access protected
	 * @var    string
	 *
	 */
	protected $fields;

}