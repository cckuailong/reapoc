<?php

/**
 * The ORM/Repository class for RSVP tickets.
 *
 * @since 4.10.6
 */
class Tribe__Tickets__Repositories__Ticket__RSVP extends Tribe__Tickets__Ticket_Repository {

	/**
	 * {@inheritdoc}
	 */
	public function ticket_types() {
		$types = parent::ticket_types();

		$types = [
			'rsvp' => $types['rsvp'],
		];

		return $types;
	}

	/**
	 * {@inheritdoc}
	 */
	public function ticket_to_event_keys() {
		$keys = parent::ticket_to_event_keys();

		$keys = [
			'rsvp' => $keys['rsvp'],
		];

		return $keys;
	}

}
