<?php
/**
 * Block: Tickets
 * Quantity Unavailable
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/blocks/tickets/quantity-unavailable.php
 *
 * See more documentation about our Blocks Editor templating system.
 *
 * @link https://evnt.is/1amp Help article for RSVP & Ticket template files.
 *
 * @since 4.9.3
 * @version 4.11.0
 *
 */

$ticket = $this->get( 'ticket' );
?>
<div
	class="tribe-common-b2 tribe-common-b2--bold tribe-tickets__item__quantity__unavailable"
>
	<?php echo esc_html_x( 'Sold Out', 'Tickets are sold out.', 'event-tickets' ); ?>
</div>
