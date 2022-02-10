<?php


namespace Tribe\Tickets\Promoter\Triggers\Observers;

use Tribe\Tickets\Promoter\Triggers\Contracts\Attendee_Model;
use Tribe\Tickets\Promoter\Triggers\Models\Attendee;
use Tribe__Tickets__RSVP;
use Tribe__Tickets__Tickets;

/**
 * Class RSVP
 *
 * @since 4.12.3
 */
class RSVP {
	/**
	 * Attach hooks for trigger messages.
	 *
	 * @since 4.12.3
	 */
	public function hook() {
		add_action( 'rsvp_checkin', [ $this, 'rsvp_checkin' ], 10, 2 );
		add_action( 'event_tickets_rsvp_attendee_created', [ $this, 'attendee_created' ], 10, 3 );
		add_action( 'updated_postmeta', [ $this, 'attendee_updated' ], 10, 4 );
	}

	/**
	 * Fires a checkin action.
	 *
	 * @param int       $attendee_id The ID of the attendee utilized.
	 * @param bool|null $qr          Whether it's from a QR scan.
	 */
	public function rsvp_checkin( $attendee_id, $qr ) {
		$this->trigger( 'checkin', $attendee_id );
	}

	/**
	 * RSVP specific action fired when a RSVP-driven attendee ticket for an event is generated.
	 * Used to assign a unique ID to the attendee.
	 *
	 * @param int    $attendee_id ID of attendee ticket
	 * @param int    $post_id     ID of event
	 * @param string $order_id    RSVP order ID (hash)
	 */
	public function attendee_created( $attendee_id, $post_id, $order_id ) {
		$value = get_post_meta( $attendee_id, Tribe__Tickets__RSVP::ATTENDEE_RSVP_KEY, true );
		$type  = $value === 'yes' ? 'rsvp_going' : 'rsvp_not_going';
		$this->trigger( $type, $attendee_id );
	}

	/**
	 * Fires immediately after updating a post's metadata.
	 *
	 * @since 4.12.3
	 *
	 * @param int    $meta_id    ID of updated metadata entry.
	 * @param int    $object_id  Post ID.
	 * @param string $meta_key   Metadata key.
	 * @param mixed  $meta_value Metadata value. This will be a PHP-serialized string representation of the value
	 *                           if the value is an array, an object, or itself a PHP-serialized string.
	 */
	public function attendee_updated( $meta_id, $object_id, $meta_key, $meta_value ) {
		if ( $meta_key !== Tribe__Tickets__RSVP::ATTENDEE_RSVP_KEY ) {
			return;
		}

		$type = $meta_value === 'yes' ? 'rsvp_going' : 'rsvp_not_going';
		$this->trigger( $type, $object_id );
	}

	/**
	 * @since 4.12.3
	 *
	 * @param $type
	 * @param $attendee_id
	 */
	private function trigger( $type, $attendee_id ) {
		/** @var Tribe__Tickets__RSVP $ticket */
		$ticket   = tribe( 'tickets.rsvp' );
		$attendee = new Attendee( $ticket->get_attendee( $attendee_id ) );

		/**
		 * Create a new action to listen for a trigger associated with an attendee.
		 *
		 * @since 4.12.3
		 *
		 * @param string                  $type     The type of trigger fired.
		 * @param Attendee_Model          $attendee The attendee associated with the trigger.
		 * @param Tribe__Tickets__Tickets $ticket   The ticket where the attendee was created or updated.
		 */
		do_action( 'tribe_tickets_promoter_trigger_attendee', $type, $attendee, $ticket );
	}
}
