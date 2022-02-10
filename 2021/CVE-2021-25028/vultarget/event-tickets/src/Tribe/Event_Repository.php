<?php
/**
 * A decorator of the Event repository to add and replace some tickets related functions.
 *
 * @since 4.10.4
 */

use Tribe\Tickets\Repositories\Traits\Post_Attendees;
use Tribe\Tickets\Repositories\Traits\Post_Tickets;

/**
 * Class Tribe__Tickets__Event_Repository
 *
 * @since 4.10.4
 */
class Tribe__Tickets__Event_Repository extends Tribe__Repository__Decorator {

	use Post_Attendees;
	use Post_Tickets;

	/**
	 * Tribe__Tickets__Event_Repository constructor.
	 *
	 * Gets the current event repository instance to add or replace some filters in it.
	 *
	 * @since 4.10.4
	 */
	public function __construct() {
		$this->decorated = $this->get_previously_decorated_repository();

		// These filter methods are added by the Post_Tickets trait.
		$this->decorated->add_schema_entry( 'cost', [ $this, 'filter_by_cost' ] );
		$this->decorated->add_schema_entry( 'cost_currency_symbol', [ $this, 'filter_by_cost_currency_symbol' ] );
		$this->decorated->add_schema_entry( 'has_tickets', [ $this, 'filter_by_has_tickets' ] );
		$this->decorated->add_schema_entry( 'has_rsvp', [ $this, 'filter_by_has_rsvp' ] );
		$this->decorated->add_schema_entry( 'has_rsvp_or_tickets', [ $this, 'filter_by_has_rsvp_or_tickets' ] );

		// These filter methods are added by the Post_Attendees trait.
		$this->decorated->add_schema_entry( 'has_attendees', [ $this, 'filter_by_has_attendees' ] );
		$this->decorated->add_schema_entry( 'attendee', [ $this, 'filter_by_attendee' ] );
		$this->decorated->add_schema_entry( 'attendee__not_in', [ $this, 'filter_by_attendee_not_in' ] );
		$this->decorated->add_schema_entry( 'attendee_user', [ $this, 'filter_by_attendee_user' ] );

		// This is not yet working, it needs more debugging to determine why it's not functional yet.
		//$this->decorated->add_schema_entry( 'attendee_user__not_in', [ $this, 'filter_by_attendee_user_not_in' ] );
	}

	/**
	 * Get the previously declared event repository so that we can decorate it.
	 *
	 * @since 5.2.0
	 *
	 * @return Tribe__Repository
	 */
	protected function get_previously_decorated_repository() {
		/**
		 * Filters the map relating event repository slugs to service container bindings.
		 *
		 * @see tribe_events()
		 *
		 * @since 5.2.0
		 *
		 * @param array  $map        A map in the shape [ <repository_slug> => <service_name> ]
		 * @param string $repository The currently requested implementation.
		 * @param array $args        An array of additional call arguments used to call the function beside the
		 *                           repository slug.
		 */
		$map = apply_filters(
			'tribe_events_event_repository_map',
			[
				'default' => 'events.event-repository',
			],
			'default',
			[]
		);

		return tribe( $map['tickets_event_previous'] );
	}

	/**
	 * Returns an array of the attendee types handled by this repository.
	 *
	 * Extending repository classes should override this to add more attendee types.
	 *
	 * @since 4.12.1
	 *
	 * @return array
	 */
	public function attendee_types() {
		return [
			'rsvp'                          => 'tribe_rsvp_attendees',
			'tribe-commerce'                => 'tribe_tpp_attendees',
			\TEC\Tickets\Commerce::PROVIDER => \TEC\Tickets\Commerce\Attendee::POSTTYPE,
		];
	}

	/**
	 * Returns the list of meta keys relating an Attendee to a Post (Event).
	 *
	 * Extending repository classes should override this to add more keys.
	 *
	 * @since 4.12.1
	 *
	 * @return array
	 */
	public function attendee_to_event_keys() {
		return [
			'rsvp'                          => '_tribe_rsvp_event',
			'tribe-commerce'                => '_tribe_tpp_event',
			\TEC\Tickets\Commerce::PROVIDER => \TEC\Tickets\Commerce\Attendee::$event_relation_meta_key,
		];
	}

	/**
	 * Returns the meta key relating an Attendee to a User.
	 *
	 * @since 4.12.1
	 *
	 * @return string
	 */
	public function attendee_to_user_key() {
		return '_tribe_tickets_attendee_user_id';
	}
}
