<?php


/**
 * Class Tribe__Tickets__RSVP__Status_Manager
 *
 * @since 4.10
 *
 */
class Tribe__Tickets__RSVP__Status_Manager extends Tribe__Tickets__Status__Abstract_Commerce {

	public $status_names = array(
		'Going',
		'Not_Going',
	);

	public $statuses = array();

	public function __construct() {

		$this->initialize_status_classes();
	}

	/**
	 * Initialize Commerce Status Class and Get all Statuses
	 */
	public function initialize_status_classes() {

		foreach ( $this->status_names as $name ) {

			$class_name = 'Tribe__Tickets__RSVP__Status__' . $name;

			$this->statuses[ $name ] = new $class_name();
		}
	}
}