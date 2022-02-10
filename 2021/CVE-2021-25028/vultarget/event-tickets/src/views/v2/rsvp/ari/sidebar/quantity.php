<?php
/**
 * This template renders the RSVP ticket form quantity input.
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/v2/rsvp/ari/sidebar/quantity.php
 *
 * @var Tribe__Tickets__Ticket_Object $rsvp The rsvp ticket object.
 *
 * @since 4.12.3
 *
 * @version 4.12.3
 */

?>
<div class="tribe-tickets__rsvp-ar-quantity">
	<span class="tribe-common-h7 tribe-common-h--alt">
		<?php
		echo esc_html(
			sprintf(
				/* Translators: %s Guest label for RSVP attendee registration sidebar title. */
				__( 'Total %s', 'event-tickets' ),
				tribe_get_guest_label_plural( 'RSVP attendee registration sidebar title' )
			)
		);
		?>
	</span>

	<div class="tribe-tickets__rsvp-ar-quantity-input">
		<?php $this->template( 'v2/rsvp/ari/sidebar/quantity/minus' ); ?>

		<?php $this->template( 'v2/rsvp/ari/sidebar/quantity/input', [ 'rsvp' => $rsvp ] ); ?>

		<?php $this->template( 'v2/rsvp/ari/sidebar/quantity/plus' ); ?>
	</div>

</div>
