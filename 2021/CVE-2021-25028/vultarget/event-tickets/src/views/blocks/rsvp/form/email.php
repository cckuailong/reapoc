<?php
/**
 * Block: RSVP
 * Form Email
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/blocks/rsvp/form/email.php
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
 * Set the default value for the email on the RSVP form.
 *
 * @param string
 * @param Tribe__Events_Gutenberg__Template $this
 *
 * @since 4.9
 *
 */
$email = apply_filters( 'tribe_tickets_rsvp_form_email', '', $this );
?>
<input
	type="email"
	name="attendee[email]"
	class="tribe-tickets-email"
	placeholder="<?php esc_attr_e( 'Email', 'event-tickets' ); ?>"
	value="<?php echo esc_attr( $email ); ?>"
	required
/>
