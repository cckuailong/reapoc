<?php


namespace Tribe\Tickets\Promoter\Triggers\Builders;


use Tribe\Tickets\Promoter\Triggers\Contracts\Attendee_Model;
use Tribe\Tickets\Promoter\Triggers\Director;
use Tribe__Tickets__Tickets;

/**
 * Class Attendee_Trigger
 *
 * @since 4.12.3
 */
class Attendee_Trigger extends Director {
	/**
	 * @var Tribe__Tickets__Tickets
	 */
	private $provider;
	/**
	 * @var string
	 */
	private $type;

	/**
	 * Attendee constructor.
	 *
	 * @param string                  $trigger_name
	 * @param Attendee_Model          $attendee
	 * @param Tribe__Tickets__Tickets $provider
	 */
	public function __construct( $trigger_name, Attendee_Model $attendee, Tribe__Tickets__Tickets $provider ) {
		$this->type     = $trigger_name;
		$this->attendee = $attendee;
		$this->provider = $provider;
	}

	/**
	 * @inheritDoc
	 */
	public function create_attendee() {
		$this->attendee->build();
	}

	/**
	 * @inheritDoc
	 */
	public function find_ticket() {
		$this->ticket = $this->provider->get_ticket( $this->attendee->event_id(), $this->attendee->product_id() );
	}

	/**
	 * @inheritDoc
	 */
	public function find_event() {
		$this->event = get_post( $this->attendee->event_id() );
	}

	/**
	 * @inheritDoc
	 */
	public function type() {
		return $this->type;
	}
}