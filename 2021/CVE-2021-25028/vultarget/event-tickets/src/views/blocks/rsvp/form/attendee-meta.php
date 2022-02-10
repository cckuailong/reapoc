<?php
/**
 * Block: RSVP
 * Attendee Meta
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/blocks/rsvp/form/attendee-meta.php
 *
 * See more documentation about our Blocks Editor templating system.
 *
 * @link https://evnt.is/1amp Help article for RSVP & Ticket template files.
 *
 * @since 4.9.3
 * @since 4.11.5 Corrected the template override instructions in template comments.
 * @since 5.0.3 Abstracted the ticket meta enabled check to use the Ticket object method instead.
 *
 * @version 5.0.3
 *
 * @var Tribe__Tickets__Ticket_Object $ticket The ticket object.
 */

if ( ! $ticket->has_meta_enabled() ) {
	return;
}
?>

<table class="tribe-block__rsvp__form__attendee-meta">
	<?php
		/**
		 * Allows injection of HTML after an RSVP ticket table row
		 *
		 * @var bool|WP_Post
		 * @var Tribe__Tickets__Ticket_Object
		 */
		do_action( 'event_tickets_rsvp_after_ticket_row', tribe_events_get_ticket_event( $ticket->ID ), $ticket );
	?>
</table>
