<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * The main class for the Event
 *
 */
class WPBS_Event extends WPBS_Base_Object {

	/**
	 * The Id of the event
	 *
	 * @access protected
	 * @var    int
	 *
	 */
	protected $id;

	/**
	 * The year
	 *
	 * @access protected
	 * @var    int
	 *
	 */
	protected $date_year;

	/**
	 * The int number representing the month
	 *
	 * @access protected
	 * @var    int
	 *
	 */
	protected $date_month;

	/**
	 * The int number representing the day
	 *
	 * @access protected
	 * @var    int
	 *
	 */
	protected $date_day;

	/**
	 * The ID of the calendar in which the event should appear
	 *
	 * @access protected
	 * @var    int
	 *
	 */
	protected $calendar_id;

	/**
	 * The ID of the booking associated with the event
	 *
	 * @access protected
	 * @var    int
	 *
	 */
	protected $booking_id;

	/**
	 * The ID of the legend item attached to the event
	 *
	 * @access protected
	 * @var    int
	 *
	 */
	protected $legend_item_id;

	/**
	 * The admin only description for the event
	 *
	 * @access protected
	 * @var    string
	 *
	 */
	protected $description;

	/**
	 * The optional tooltip to show in the calendar
	 *
	 * @access protected
	 * @var    string
	 *
	 */
	protected $tooltip;

	/**
	 * The price for an event
	 * 
	 * Only used when a pricing Add-on is installed
	 *
	 * @access protected
	 * @var    string
	 *
	 */
	protected $price;

}