<?php


namespace Tribe\Tickets\Promoter\Triggers\Observers;

use Tribe\Tickets\Promoter\Triggers\Contracts\Attendee_Model;
use Tribe\Tickets\Promoter\Triggers\Models\Attendee;
use Tribe__Tickets__Commerce__PayPal__Main;
use Tribe__Tickets__Tickets;

class Commerce {
	/**
	 * Attach hooks for trigger messages.
	 *
	 * @since 4.12.3
	 */
	public function hook() {
		add_action( 'event_tickets_tpp_attendee_created', [ $this, 'attendee_created' ], 10, 5 );
		add_action( 'event_tickets_checkin', [ $this, 'checkin' ], 10, 2 );
	}

	/**
	 * Action fired when an PayPal attendee ticket is created
	 *
	 * @since 4.12.3
	 *
	 * @param int    $attendee_id           Attendee post ID
	 * @param string $order_id              PayPal Order ID
	 * @param int    $product_id            PayPal ticket post ID
	 * @param int    $order_attendee_id     Attendee number in submitted order
	 * @param string $attendee_order_status The order status for the attendee.
	 */
	public function attendee_created( $attendee_id, $order_id, $product_id, $order_attendee_id, $attendee_order_status ) {
		$this->trigger( 'ticket_purchased', $attendee_id );
	}

	/**
	 * Responds to a checkin action.
	 *
	 * @since 4.12.3
	 *
	 * @param int       $attendee_id The ID of the attendee utilized.
	 * @param bool|null $qr          Whether it's from a QR scan.
	 */
	public function checkin( $attendee_id, $qr ) {
		$this->trigger( 'checkin', $attendee_id );
	}

	/**
	 * Fire an trigger action using the tribe commerce as main source of the ticket data.
	 *
	 * @since 4.12.3
	 *
	 * @param string $type        The trigger type.
	 * @param int    $attendee_id The ID of the attendee utilized.
	 */
	private function trigger( $type, $attendee_id ) {
		/** @var Tribe__Tickets__Commerce__PayPal__Main $ticket */
		$ticket   = tribe( 'tickets.commerce.paypal' );
		$attendee = new Attendee( $ticket->get_attendee( $attendee_id ) );

		/**
		 * Create a new action to listen for a trigger associated with an attendee.
		 *
		 * @since 4.12.3
		 *
		 * @param string                  $type     The type of trigger fired.
		 * @param Attendee_Model          $attendee The attendee associated with the trigger.
		 * @param Tribe__Tickets__Tickets $ticket   The ticket where the attendee was created.
		 */
		do_action( 'tribe_tickets_promoter_trigger_attendee', $type, $attendee, $ticket );
	}
}
