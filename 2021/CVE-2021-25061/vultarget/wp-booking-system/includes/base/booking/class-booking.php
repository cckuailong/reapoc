<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * The main class for the Booking
 *
 */
class WPBS_Booking extends WPBS_Base_Object {

	/**
	 * The ID of the booking
	 *
	 * @access protected
	 * @var    int
	 *
	 */
	protected $id;

	/**
	 * The ID of the calendar
	 *
	 * @access protected
	 * @var    int
	 *
	 */
	protected $calendar_id;

	/**
	 * The ID of the form
	 *
	 * @access protected
	 * @var    int
	 *
	 */
	protected $form_id;

	/**
	 * The starting date of the booking
	 *
	 * @access protected
	 * @var    int
	 *
	 */
	protected $start_date;

	/**
	 * The ending date of the booking
	 *
	 * @access protected
	 * @var    int
	 *
	 */
	protected $end_date;

	/**
	 * The booking fields
	 *
	 * @access protected
	 * @var    array
	 *
	 */
	protected $fields;

	/**
	 * The status of the bookng
	 *
	 * @access protected
	 * @var    string
	 *
	 */
	protected $status;

	/**
	 * Wether or not the booking is read
	 *
	 * @access protected
	 * @var    int
	 *
	 */
	protected $is_read;

	/**
	 * The date the booking was created
	 *
	 * @access protected
	 * @var    int
	 *
	 */
	protected $date_created;

	/**
	 * The date the booking was modified
	 *
	 * @access protected
	 * @var    int
	 *
	 */
	protected $date_modified;

}