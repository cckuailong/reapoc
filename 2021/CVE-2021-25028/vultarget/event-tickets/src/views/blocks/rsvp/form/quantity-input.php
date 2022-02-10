<?php
/**
 * Block: RSVP
 * Form Quantity Input
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/blocks/rsvp/form/quantity-input.php
 *
 * See more documentation about our Blocks Editor templating system.
 *
 * @link    https://evnt.is/1amp Help article for RSVP & Ticket template files.
 *
 * @since   4.9
 * @since   4.11.1 Corrected amount of available/remaining tickets. Removed unused `data-remaining` attribute.
 * @since   4.11.5 The input's "max" is now always set. The unused `data-remaining` attribute actually didn't get removed
 *                 in the previous change, above, so it got removed in this version.
 * @since   5.0.3 Add vars to docblock and removed duplicative vars.
 * @since   5.1.5 Add label to the quantity input to improve accessibility.
 *
 * @version 5.1.5
 *
 * @var Tribe__Tickets__Editor__Template $this    Template object.
 * @var int                              $post_id [Global] The current Post ID to which RSVPs are attached.
 * @var Tribe__Tickets__Ticket_Object    $ticket  The ticket object with provider set to RSVP.
 * @var string                           $going   The RSVP status at time of add/edit (e.g. 'yes'), or empty if not in that context.
 */

/** @var Tribe__Tickets__Ticket_Object $ticket */
if ( empty( $ticket->ID ) ) {
	return;
}

/** @var Tribe__Tickets__Tickets_Handler $tickets_handler */
$tickets_handler = tribe( 'tickets.handler' );

$max_at_a_time = $tickets_handler->get_ticket_max_purchase( $ticket->ID );
?>
<label
	class="screen-reader-text"
	for="quantity_<?php echo esc_attr( absint( $ticket->ID ) ); ?>"
>
	<?php esc_html_e( 'Quantity', 'event-tickets' ); ?>
</label>
<input
	type="number"
	id="quantity_<?php echo esc_attr( absint( $ticket->ID ) ); ?>"
	name="quantity_<?php echo esc_attr( absint( $ticket->ID ) ); ?>"
	class="tribe-tickets-quantity"
	step="1"
	min="1"
	value="1"
	required
	max="<?php echo esc_attr( $max_at_a_time ); ?>"
	<?php disabled( $must_login ); ?>
/>
