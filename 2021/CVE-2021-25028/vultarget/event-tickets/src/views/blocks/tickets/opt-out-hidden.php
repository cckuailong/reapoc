<?php
/**
 * Block: Tickets
 * Form Opt-Out
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/blocks/rsvp/form/opt-out.php
 *
 * See more documentation about our Blocks Editor templating system.
 *
 * @link https://evnt.is/1amp Help article for RSVP & Ticket template files.
 *
 * @since 4.11.0
 * @since 4.11.3 Ensure we always show the optout by default.
 *
 * @version 4.11.3
 *
 */
$ticket   = $this->get( 'ticket' );

/**
 * Use this filter to hide the Attendees List Optout
 *
 * @since 4.9
 *
 * @param bool
 */
$hide_attendee_list_optout = apply_filters( 'tribe_tickets_plus_hide_attendees_list_optout', false );

if ( $hide_attendee_list_optout ) {
	// Force optout.
	?>
	<input name="attendee[optout]" value="1" type="hidden" />
	<?php
	return;
}
?>

<input id="tribe-tickets-attendees-list-optout-<?php echo esc_attr( $ticket->ID ); ?>-modal" class="tribe-tickets__item__quantity" name="attendee[optout]" value="1" type="hidden" />
