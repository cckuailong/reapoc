<?php
/**
 * Block: Tickets
 * Quantity
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/blocks/tickets/quantity.php
 *
 * See more documentation about our Blocks Editor templating system.
 *
 * @link https://evnt.is/1amp Help article for RSVP & Ticket template files.
 *
 * @since 4.9
 * @since 4.11.1 Corrected amount of available/remaining tickets.
 *
 * @version 4.11.1
 */

/** @var Tribe__Tickets__Ticket_Object $ticket */
$ticket = $this->get( 'ticket' );

if ( empty( $ticket->ID ) ) {
	return;
}

/** @var Tribe__Tickets__Tickets_Handler $tickets_handler */
$tickets_handler = tribe( 'tickets.handler' );

$available = $tickets_handler->get_ticket_max_purchase( $ticket->ID );

$context = [
	'ticket' => $ticket,
	'key' => $this->get( 'key' ),
];

$classes = [
	'tribe-common-h4',
	'tribe-tickets__item__quantity',
];
?>
<div
	<?php tribe_classes( $classes ); ?>
>
	<?php if ( 0 !== $available ) : ?>
		<?php $this->template( 'blocks/tickets/quantity-remove', $context ); ?>
		<?php $this->template( 'blocks/tickets/quantity-number', $context ); ?>
		<?php $this->template( 'blocks/tickets/quantity-add', $context ); ?>
	<?php else : ?>
		<?php $this->template( 'blocks/tickets/quantity-unavailable', $context ); ?>
	<?php endif; ?>
</div>