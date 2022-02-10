<?php

namespace TEC\Tickets\Commerce\Status;

use TEC\Tickets\Commerce\Module;
use TEC\Tickets\Commerce\Ticket;
use TEC\Tickets\Event;
use Tribe__Date_Utils as Dates;

use WP_Error;

/**
 * Class Pending.
 *
 * This is a payment that has begun, but is not complete.  An example of this is someone who has filled out the checkout
 * form and then gone to Gateway for payment.  We have the record of sale, but they haven't completed their payment yet.
 *
 * @since   5.1.9
 *
 * @package TEC\Tickets\Commerce\Status
 */
class Pending extends Status_Abstract {
	/**
	 * Slug for this Status.
	 *
	 * @since 5.1.9
	 *
	 * @var string
	 */
	const SLUG = 'pending';

	/**
	 * {@inheritdoc}
	 */
	public function get_name() {
		return __( 'Pending', 'event-tickets' );
	}

	/**
	 * {@inheritdoc}
	 */
	protected $flags = [
		'backfill_purchaser',
		'count_attendee',
		'count_incomplete',
		'count_sales',
	];

	/**
	 * {@inheritdoc}
	 */
	protected $wp_arguments = [
		'public'                    => true,
		'exclude_from_search'       => false,
		'show_in_admin_all_list'    => true,
		'show_in_admin_status_list' => true,
	];

	/**
	 * {@inheritdoc}
	 */
	public function can_apply_to( $order ) {
		$status = parent::can_apply_to( $order );

		// If the parent status or abstract has an error already we dont even run.
		if ( is_wp_error( $status ) ) {
			return $status;
		}

		$order = tec_tc_get_order( $order );

		// Since there are no cart items we can do anything.
		if ( empty( $order->items ) || ! is_array( $order->items ) ) {
			return true;
		}

		foreach ( $order->items as $item ) {
			// Skip if we dont have a ticket id.
			if ( empty( $item['ticket_id'] ) ) {
				continue;
			}

			// If item quantity is empty, continue
			if ( empty( $item['quantity'] ) ) {
				continue;
			}

			/** @var \Tribe__Tickets__Ticket_Object $ticket */
			$ticket = tribe( Ticket::class )->get_ticket( $item['ticket_id'] );

			if ( null === $ticket ) {
				return new WP_Error(
					'tec-tc-invalid-ticket-id',
					sprintf( __( 'This order contained an invalid Ticket (ID: %1$d)', 'event-tickets' ), $item['ticket_id'] ),
					[
						'ticket'     => $item['ticket_id'],
						'order'      => $order,
						'new_status' => $this
					]
				);
			}

			// Get the event this tickets is for
			$post = $ticket->get_event();

			if ( empty( $post ) ) {
				return new WP_Error(
					'tec-tc-invalid-event-id',
					sprintf( __( 'This order contained a Ticket with an invalid Event (Event ID: %1$d)', 'event-tickets' ), $item['ticket_id'] ),
					[
						'ticket'     => $item['ticket_id'],
						'order'      => $order,
						'new_status' => $this
					]
				);
			}

			$qty = max( (int) $item['quantity'], 0 );

			if ( $qty === 0 ) {
				return new WP_Error(
					'tec-tc-cannot-purchase-zero',
					sprintf( __( 'Cannot purchase zero of "%1$s"', 'event-tickets' ), $ticket->name ),
					[
						'ticket'     => $item['ticket_id'],
						'order'      => $order,
						'new_status' => $this
					]
				);
			}

			// Throw an error if Qty is bigger then Remaining
			if ( $ticket->managing_stock() ) {
				$inventory                  = (int) $ticket->inventory();
				$inventory_is_not_unlimited = - 1 !== $inventory;

				if ( $inventory_is_not_unlimited && $qty > $inventory ) {
					return new WP_Error(
						'tec-tc-ticket-insufficient-stock',
						sprintf( __( 'Insufficient stock for "%1$s"', 'event-tickets' ), $ticket->name ),
						[
							'ticket'     => $item['ticket_id'],
							'order'      => $order,
							'new_status' => $this
						]
					);
				}
			}

			if ( ! $ticket->date_in_range( Dates::build_date_object() ) ) {
				$now             = Dates::build_date_object();
				$start_sale_date = $ticket->start_date;
				$start_sale_time = Dates::reformat( $ticket->start_time, tribe_get_time_format() );

				if ( $ticket->date_is_earlier( $now ) ) {
					$message = sprintf( __( '%s will be available on %s at %s', 'event-tickets' ), tribe_get_ticket_label_plural( 'unavailable_future_display_date' ), $start_sale_date, $start_sale_time );
				} elseif ( $ticket->date_is_later( $now ) ) {
					$message = sprintf( __( '%s are no longer available.', 'event-tickets' ), tribe_get_ticket_label_plural( 'unavailable_past' ) );
				} else {
					$message = sprintf( __( 'There are no %s available at this time.', 'event-tickets' ), tribe_get_ticket_label_plural( 'unavailable_mixed' ) );
				}

				return new WP_Error(
					'tec-tc-ticket-unavailable',
					$message,
					[
						'ticket'     => $item['ticket_id'],
						'order'      => $order,
						'new_status' => $this
					]
				);
			}
		}

		return true;
	}
}