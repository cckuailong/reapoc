<?php
/**
 * Block: RSVP
 * Form Quantity Input
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/v2/rsvp/ari/sidebar/quantity/input.php
 *
 * See more documentation about our Blocks Editor templating system.
 *
 * @link    https://evnt.is/1amp Help article for RSVP & Ticket template files.
 *
 * @var bool $must_login Whether the user has to login to RSVP or not.
 * @var Tribe__Tickets__Ticket_Object $rsvp The rsvp ticket object.
 *
 * @since   4.12.3
 * @since   5.1.5 Add label to the input to improve accessibility.
 *
 * @version 5.1.5
 */

/** @var Tribe__Tickets__Ticket_Object $rsvp */
if ( empty( $rsvp->ID ) ) {
	return;
}

/** @var Tribe__Tickets__Tickets_Handler $tickets_handler */
$tickets_handler = tribe( 'tickets.handler' );

$max_at_a_time = $tickets_handler->get_ticket_max_purchase( $rsvp->ID );
?>
<label
	class="tribe-common-a11y-visual-hide"
	for="tribe-tickets__rsvp-ar-quantity-number--<?php echo esc_attr( absint( $rsvp->ID ) ); ?>"
>
	<?php esc_html_e( 'Quantity', 'event-tickets' ); ?>
</label>
<input
	type="number"
	id="tribe-tickets__rsvp-ar-quantity-number--<?php echo esc_attr( absint( $rsvp->ID ) ); ?>"
	name="tribe_tickets[<?php echo esc_attr( absint( $rsvp->ID ) ); ?>][quantity]"
	class="tribe-common-h4"
	step="1"
	min="1"
	value="1"
	required
	max="<?php echo esc_attr( $max_at_a_time ); ?>"
	<?php disabled( $must_login ); ?>
	autocomplete="off"
/>
