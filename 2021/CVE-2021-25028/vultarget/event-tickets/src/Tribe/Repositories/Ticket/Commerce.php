<?php

/**
 * The ORM/Repository class for Tribe Commerce (PayPal) tickets.
 *
 * @since 4.10.6
 */
class Tribe__Tickets__Repositories__Ticket__Commerce extends Tribe__Tickets__Ticket_Repository {

	/**
	 * {@inheritdoc}
	 */
	public function ticket_types() {
		$types = parent::ticket_types();

		$types = [
			'tribe-commerce' => $types['tribe-commerce'],
		];

		return $types;
	}

	/**
	 * {@inheritdoc}
	 */
	public function ticket_to_event_keys() {
		$keys = parent::ticket_to_event_keys();

		$keys = [
			'tribe-commerce' => $keys['tribe-commerce'],
		];

		return $keys;
	}

}
