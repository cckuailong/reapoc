<?php


namespace Tribe\Tickets\Promoter\Triggers;


use RuntimeException;
use Tribe\Tickets\Promoter\Triggers\Contracts\Attendee_Model;
use Tribe\Tickets\Promoter\Triggers\Contracts\Builder;
use Tribe\Tickets\Promoter\Triggers\Contracts\Triggered;
use Tribe__Tickets__Ticket_Object;
use Tribe__Tickets__Tickets;
use WP_Post;

abstract class Director implements Triggered, Builder {
	/**
	 * @since 4.12.3
	 *
	 * @var Tribe__Tickets__Tickets $ticket
	 */
	protected $ticket;
	/**
	 * @since 4.12.3
	 *
	 * @var WP_Post $event
	 */
	protected $event;
	/**
	 * @since 4.12.3
	 *
	 * @var Attendee_Model $attendee
	 */
	protected $attendee;

	/**
	 * @inheritDoc
	 */
	public function build() {
		$this->create_attendee();
		$this->find_ticket();

		if ( ! $this->ticket instanceof Tribe__Tickets__Ticket_Object ) {
			throw new RunTimeException( "The ticket, is not of the right type." );
		}

		$this->find_event();

		if ( ! $this->event instanceof WP_Post ) {
			throw new RuntimeException( "The event, is not a valid WP Post object." );
		}
	}

	/**
	 * @inheritDoc
	 */
	public function attendee() {
		return $this->attendee;
	}

	/**
	 * @inheritDoc
	 */
	public function ticket() {
		return $this->ticket;
	}

	/**
	 * @inheritDoc
	 */
	public function post() {
		return $this->event;
	}
}