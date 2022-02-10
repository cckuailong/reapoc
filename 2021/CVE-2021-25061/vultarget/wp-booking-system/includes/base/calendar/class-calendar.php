<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * The main class for the Calendar
 *
 */
class WPBS_Calendar extends WPBS_Base_Object {

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
	 * The date when the calendar was created
	 *
	 * @access protected
	 * @var    string
	 *
	 */
	protected $date_created;

	/**
	 * The date when the calendar was last modified
	 *
	 * @access protected
	 * @var    string
	 *
	 */
	protected $date_modified;

	/**
	 * The status of the calendar
	 *
	 * @access protected
	 * @var    string
	 *
	 */
	protected $status;

	/**
	 * The random ical hash
	 *
	 * @access protected
	 * @var    string
	 *
	 */
	protected $ical_hash;

}