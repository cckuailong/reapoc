<?php
/**
 * Block: Tickets
 * Registration Summary Ticket Price
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/blocks/tickets/registration/summary/ticket-price.php
 *
 * See more documentation about our Blocks Editor templating system.
 *
 * @link https://evnt.is/1amp Help article for RSVP & Ticket template files.
 *
 * @since   4.9
 * @since   5.0.2 Fix template path in documentation block.
 * @version 5.0.2
 *
 */
?>

<div class="tribe-tickets__registration__tickets__item__price">
	<?php echo $ticket->get_provider()->get_price_html( $ticket->ID ); ?>
</div>
