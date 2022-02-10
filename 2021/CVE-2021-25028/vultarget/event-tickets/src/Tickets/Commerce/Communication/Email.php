<?php

namespace TEC\Tickets\Commerce\Communication;

use TEC\Tickets\Commerce\Module;

/**
 * Class Email
 *
 * @since 5.1.9
 *
 * @package TEC\Tickets\Commerce\Communication
 */
class Email {
	/**
	 * Sends ticket email
	 *
	 * @since 5.2.0
	 *
	 * @param string $order_id Order post ID
	 * @param int    $post_id  Parent post ID (optional)
	 */
	public function send_tickets_email( $order_id, $post_id = null ) {
		$all_attendees = tribe( Module::class )->get_attendees_by_order_id( $order_id );

		$to_send = array();

		if ( empty( $all_attendees ) ) {
			return;
		}

		// Look at each attendee and check if a ticket was sent: in each case where a ticket
		// has not yet been sent we should a) send the ticket out by email and b) record the
		// fact it was sent
		foreach ( $all_attendees as $single_attendee ) {
			// Only add those attendees/tickets that haven't already been sent
			if ( ! empty( $single_attendee['ticket_sent'] ) ) {
				continue;
			}

			$to_send[] = $single_attendee;
		}

		/**
		 * Controls the list of tickets which will be emailed out.
		 *
		 * @since 5.2.0
		 *
		 * @param array  $to_send       list of tickets to be sent out by email
		 * @param array  $all_attendees list of all attendees/tickets, including those already sent out
		 * @param int    $post_id
		 * @param string $order_id
		 *
		 */
		$to_send = (array) apply_filters( 'tec_tickets_commerce_legacy_email_tickets_to_send', $to_send, $all_attendees, $post_id, $order_id );

		if ( empty( $to_send ) ) {
			return;
		}

		$send_args = [
			'post_id'            => $post_id,
			'order_id'           => $order_id,
			'send_purchaser_all' => true,
		];

		// Send the emails.
		tribe( Module::class )->send_tickets_email_for_attendees( $to_send, $send_args );
	}
}