<?php

/**
 * Class Tribe__Tickets__Promoter__Observer
 *
 * Class used to observe hooks and actions happening to notify promoter of those actions.
 *
 * @since 4.10.1.1
 */
class Tribe__Tickets__Promoter__Observer {

	/**
	 * Holding the reference to the event post type
	 *
	 * @since 4.10.9
	 *
	 * @var string
	 */
	protected $event_type = 'tribe_events';

	/**
	 * Hooks on which this observer notifies promoter.
	 *
	 * @since 4.10.1.1
	 */
	public function hook() {
		/** @var Tribe__Promoter__PUE $pue */
		$pue = tribe( 'promoter.pue' );

		if ( ! $pue->has_license_key() ) {
			return;
		}

		/**
		 * In case the class for TEC is defined we use the value defined there
		 */
		if ( class_exists( 'Tribe__Events__Main' ) ) {
			$this->event_type = Tribe__Events__Main::POSTTYPE;
		}

		$this->registered_types();

		// Listen for changes on RSVP as Gutenberg Uses the post_type API to update RSVP's
		add_action( 'save_post_tribe_rsvp_tickets', [ $this, 'notify_ticket_event' ], 10, 1 );

		// RSVP
		add_action( 'tickets_rsvp_ticket_deleted', [ $this, 'notify_event_id' ], 10, 2 );
		add_action( 'event_tickets_rsvp_tickets_generated', [ $this, 'notify_event_id' ], 10, 2 );

		// Moved tickets
		add_action( 'tribe_tickets_ticket_type_moved', [ $this, 'ticket_moved_type' ], 10, 4 );
		add_action( 'tribe_tickets_ticket_moved', [ $this, 'ticket_moved' ], 10, 6 );

		// PayPal
		add_action( 'save_post_tribe_tpp_attendees', [ $this, 'notify_ticket_event' ], 10, 1 );
		add_action( 'tickets_tpp_ticket_deleted', [ $this, 'notify_event_id' ], 10, 2 );
		add_action( 'event_tickets_tpp_tickets_generated', [ $this, 'notify_event_id' ], 10, 2 );
		add_action( 'event_tickets_tpp_attendee_updated', [ $this, 'tpp_attendee_updated' ], 10, 5 );
		add_action( 'event_tickets_tpp_tickets_generated_for_product', [ $this, 'tpp_tickets_generated_for_product' ], 10, 3 );

		// All tickets
		add_action( 'event_tickets_after_save_ticket', [ $this, 'notify' ], 10, 1 );
	}

	/**
	 * Notify to the parent Event when an attendee has changes via REST API.
	 *
	 * @since 4.10.1.2
	 *
	 * @param $attendee_id
	 *
	 * @deprecated 4.11.5
	 *
	 */
	public function notify_rsvp_event( $attendee_id ) {
		_deprecated_function( __METHOD__, '4.11.5', __CLASS__ . '::notify_ticket_event' );

		$this->notify_ticket_event( $attendee_id );
	}

	/**
	 * Notify to the parent Event of the ticket.
	 *
	 * @since 4.11.5
	 *
	 * @param $ticket_id int|null The Ticket ID where to look for the Event.
	 */
	public function notify_ticket_event( $ticket_id ) {
		/** @var Tribe__Tickets__Tickets $provider */
		$provider = tribe_tickets_get_ticket_provider( $ticket_id );

		if ( empty( $provider ) ) {
			return;
		}

		$this->notify( $provider->get_event_for_ticket( $ticket_id ) );
	}

	/**
	 * Attach hooks only if events has support for tickets, to the following actions:
	 *
	 * - `save_post_tribe_events`
	 * - `delete_post`
	 *
	 * @since 4.10.1.1
	 */
	public function registered_types() {

		if ( ! $this->event_support_tickets() ) {
			return;
		}

		add_action( 'save_post_' . $this->event_type, [ $this, 'notify' ], 10, 1 );
		add_action( 'delete_post', [ $this, 'on_event_deleted' ], 10, 1 );
	}

	/**
	 * Check if the Event post type has support for tickets
	 *
	 * @since 4.10.9
	 *
	 * @return bool
	 */
	private function event_support_tickets() {
		$post_types = (array) tribe_get_option( 'ticket-enabled-post-types', [] );

		return in_array( $this->event_type, $post_types, true );
	}

	/**
	 * Wrapper when the $post_id is passed as second argument of the hook.
	 *
	 * @since 4.10.1.1
	 *
	 * @param $ticket_id int The ID of the ticket.
	 * @param $event_id  int The ID of the post/event.
	 */
	public function notify_event_id( $ticket_id, $event_id ) {
		$this->notify( $event_id );
	}

	/**
	 * Action attached to tribe_tickets_ticket_type_moved to notify promoter when a ticket is moved.
	 *
	 * @since 4.10.1.2
	 *
	 * @param int $ticket_type_id the ticket type which has been moved
	 * @param int $destination_id the post to which the ticket type has been moved
	 * @param int $source_id the post which previously hosted the ticket type
	 * @param int $instigator_id the user who initiated the change
	 */
	public function ticket_moved_type( $ticket_type_id, $destination_id, $source_id, $instigator_id ) {
		$this->notify( $source_id );
		// Prevent to send the same response twice if the ID's are the same.
		if ( $source_id !== $destination_id ) {
			$this->notify( $destination_id );
		}
	}

	/**
	 * Observer when an attendee is moved from a post to another and notify Promoter about changes on both events
	 *
	 * @since 4.11.5
	 *
	 * @param int $ticket_id the ticket which has been moved
	 * @param int $source_ticket_type_id the ticket type it belonged to originally
	 * @param int $target_ticket_type_id the ticket type it now belongs to
	 * @param int $source_event_id the event/post which the ticket originally belonged to
	 * @param int $target_event_id the event/post which the ticket now belongs to
	 * @param int $instigator_id the user who initiated the change
	 *
	 * @return void Action hook with no return.
	 */
	public function ticket_moved( $ticket_id, $source_ticket_type_id, $target_ticket_type_id, $source_event_id, $target_event_id, $instigator_id ) {
		$this->notify( $source_event_id );
		// Prevent to send the same response twice if the ID's are the same.
		if ( $source_event_id !== $target_event_id ) {
			$this->notify( $target_event_id );
		}
	}

	/**
	 * Action fired when an PayPal attendee ticket is updated.
	 *
	 * @since 4.11.5
	 *
	 * @param int    $attendee_id           Attendee post ID.
	 * @param string $order_id              PayPal Order ID.
	 * @param int    $product_id            PayPal ticket post ID.
	 * @param int    $order_attendee_id     Attendee number in submitted order.
	 * @param string $attendee_order_status The order status for the attendee.
	 *
	 * @return void
	 */
	public function tpp_attendee_updated( $attendee_id, $order_id, $product_id, $order_attendee_id, $attendee_order_status ) {
		$this->notify_ticket_event( $product_id );
	}

	/**
	 * Action fired when a PayPal has had attendee tickets generated for it.
	 *
	 * @since 4.11.5
	 *
	 * @param int    $product_id PayPal ticket post ID.
	 * @param string $order_id   ID of the PayPal order.
	 * @param int    $qty        Quantity ordered.
	 *
	 * @return void
	 */
	public function tpp_tickets_generated_for_product( $product_id, $order_id, $qty ) {
		$this->notify_ticket_event( $product_id );
	}

	/**
	 * Notify the connector of changes when the event was deleted
	 *
	 * @since 4.10.9
	 *
	 * @param $post_id
	 *
	 * @deprecated 4.11.5
	 *
	 */
	public function delete_post( $post_id ) {
		_deprecated_function( __METHOD__, '4.11.5', __CLASS__ . "::on_event_deleted" );

		$this->on_event_deleted( $post_id );
	}

	/**
	 * Notify the connector of changes when the event was deleted
	 *
	 * @since 4.11.5
	 *
	 * @param $post_id
	 */
	public function on_event_deleted( $post_id ) {
		if ( $this->event_type === get_post_type( $post_id ) ) {
			$this->notify( $post_id );
		}
	}

	/**
	 * Function used to notify the promoter endpoint of a new change on an event.
	 *
	 * @since 4.10.1.1
	 *
	 * @param $post_id int The ID of the post.
	 */
	public function notify( $post_id ) {

		// The $post_id is a falsy value, avoid a non required call.
		if ( ! $post_id ) {
			return;
		}

		try {
			/** @var Tribe__Promoter__Connector $connector */
			$connector = tribe( 'promoter.connector' );
			$connector->notify_promoter_of_changes( $post_id );
		} catch ( RuntimeException $exception ) {
			// TODO: Report this to the logger
			return;
		}
	}
}
