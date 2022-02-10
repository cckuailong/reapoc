<?php
/**
 * Block: Tickets
 * Registration Attendee Submit
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/blocks/tickets/registration/attendee/submit.php
 *
 * See more documentation about our Blocks Editor templating system.
 *
 * @link https://evnt.is/1amp Help article for RSVP & Ticket template files.
 *
 * @since 4.11.0
 *
 * @version 4.11.0
 *
 */

?>
<button
	class="tribe-common-c-btn tribe-common-c-btn--small tribe-tickets__item__registration__submit"
	type="submit"
>
	<?php echo esc_html_x( 'Save & Checkout', 'Save attendee meta and proceed to checkout.', 'event-tickets' ); ?>
</button>
