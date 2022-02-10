<?php
/**
 * Block: RSVP
 * Details Availability - Remaining
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/v2/rsvp/details/availability/remaining.php
 *
 * See more documentation about our Blocks Editor templating system.
 *
 * @link  https://evnt.is/1amp Help article for RSVP & Ticket template files.
 *
 * @var Tribe__Tickets__Ticket_Object $rsvp The rsvp ticket object.
 * @var int $threshold The threshold.
 *
 * @since 4.12.3
 *
 * @version 4.12.3
 */

$remaining_tickets = $rsvp->remaining();

if ( 0 !== $threshold && $threshold < $remaining_tickets ) {
	return;
}

echo wp_kses_post(
	sprintf(
		// Translators: 1: opening span. 2: the number of remaining tickets to RSVP. 3: Closing span.
		_x(
			'%1$s %2$s %3$s remaining',
			'Remaining RSVP quantity',
			'event-tickets'
		),
		'<span class="tribe-tickets__rsvp-availability-quantity tribe-common-b2--bold">',
		$remaining_tickets,
		'</span>'
	) . ( false !== $days_to_rsvp ? ',' : '' )
);
