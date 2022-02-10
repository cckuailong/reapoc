<?php
/**
 * Block: Tickets
 * Footer Quantity
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/blocks/tickets/footer-quantity.php
 *
 * See more documentation about our Blocks Editor templating system.
 *
 * @link https://evnt.is/1amp Help article for RSVP & Ticket template files.
 *
 * @since 4.11.0
 * @version 4.11.0
 *
 */
?>
<div class="tribe-common-b2 tribe-tickets__footer__quantity" >
	<span class="tribe-tickets__footer__quantity__label">
		<?php echo esc_html_x( 'Quantity:', 'Total selected tickets count.', 'event-tickets' ); ?>
	</span>
	<span class="tribe-tickets__footer__quantity__number">0</span>
</div>
