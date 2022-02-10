<?php
/**
 * Register classes, actions and filters that Event Tickets uses to manage "Events".
 */

namespace Tribe\Tickets\Events;

/**
 * Class Service_Provider
 *
 * @since 4.12.0
 *
 * @package Tribe\Tickets\Events
 */
class Service_Provider extends \tad_DI52_ServiceProvider {

	/**
	 * Register classes in the container that Event Tickets uses to manage Events.
	 *
	 * @since 4.12.0
	 */
	public function register() {
		tribe_singleton( 'tickets.events.attendees-list', Attendees_List::class );

		$this->hooks();
	}

	/**
	 * Actions and filters that Event Tickets uses to manage Events.
	 *
	 * @since 4.12.0
	 */
	protected function hooks() {
		/** @var Attendees_List $attendees_list */
		$attendees_list = tribe( 'tickets.events.attendees-list' );

		add_action( 'save_post', tribe_callback( $attendees_list, 'maybe_update_attendee_list_hide_meta' ), 10 );
		add_filter( 'tribe_tickets_plus_hide_attendees_list_optout', tribe_callback( $attendees_list, 'should_hide_optout' ), 1, 2 );
	}

}
