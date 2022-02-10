<?php


/**
 * Class Tribe__Tickets__RSVP__Status__Not_Going
 *
 * @since 4.10
 *
 */
class Tribe__Tickets__RSVP__Status__Not_Going extends Tribe__Tickets__Status__Abstract {

	//Cancelled by an admin or the customer – no further action required (Cancelling an order does not affect stock quantity by default)
	public $name;
	public $provider_name = 'no';
	public $post_type     = 'tribe_rsvp_attendees';

	public $count_not_going = true;

	/**
	 * Tribe__Tickets__RSVP__Status__Not_Going constructor.
	 *
	 * @since 5.1.3
	 */
	public function __construct() {
		$this->name = __( 'Not going', 'event-tickets' );
	}

}
