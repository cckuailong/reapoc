<?php


/**
 * Class Tribe__Tickets__RSVP__Status__Going
 *
 * @since 4.10
 *
 */
class Tribe__Tickets__RSVP__Status__Going extends Tribe__Tickets__Status__Abstract {

	//Order fulfilled and complete – requires no further action
	public $name;
	public $provider_name = 'yes';
	public $post_type     = 'tribe_rsvp_attendees';

	public $trigger_option      = true;
	public $attendee_generation = true;
	public $attendee_dispatch   = true;
	public $stock_reduced       = true;
	public $count_attendee      = true;
	public $count_sales         = true;
	public $count_completed     = true;

	/**
	 * Tribe__Tickets__RSVP__Status__Going constructor.
	 *
	 * @since 5.1.3
	 */
	public function __construct() {
		$this->name = __( 'Going', 'event-tickets' );
	}
}