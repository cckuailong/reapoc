<?php
/**
 * Block: RSVP
 * Form Name
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/blocks/rsvp/form/name.php
 *
 * See more documentation about our Blocks Editor templating system.
 *
 * @link https://evnt.is/1amp Help article for RSVP & Ticket template files.
 *
 * @since 4.9.3
 * @version 4.9.4
 *
 */
/**
 * Set the default Full Name for the RSVP form
 *
 * @param string
 * @param Tribe__Events_Gutenberg__Template $this
 *
 * @since 4.9
 */
$name = apply_filters( 'tribe_tickets_rsvp_form_full_name', '', $this );
?>
<input
	type="text"
	name="attendee[full_name]"
	class="tribe-tickets-full-name"
	placeholder="<?php esc_attr_e( 'Full Name', 'event-tickets' ); ?>"
	value="<?php echo esc_attr( $name ); ?>"
	required
/>
