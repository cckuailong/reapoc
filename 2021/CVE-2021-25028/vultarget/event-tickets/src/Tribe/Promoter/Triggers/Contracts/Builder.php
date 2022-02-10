<?php


namespace Tribe\Tickets\Promoter\Triggers\Contracts;

/**
 * Interface Builder
 *
 * @since 4.12.3
 */
interface Builder {
	/**
	 * Build an attendee.
	 *
	 * @since 4.12.3
	 *
	 * @return void
	 */
	public function create_attendee();

	/**
	 * Find the ticket instance.
	 *
	 * @since 4.12.3
	 *
	 * @return void
	 */
	public function find_ticket();

	/**
	 * Find an event instance for this trigger message.
	 *
	 * @since 4.12.3
	 *
	 * @return void
	 */
	public function find_event();
}