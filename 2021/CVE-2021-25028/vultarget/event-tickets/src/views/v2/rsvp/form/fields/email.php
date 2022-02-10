<?php
/**
 * Block: RSVP
 * Form Email
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/v2/rsvp/form/fields/email.php
 *
 * See more documentation about our Blocks Editor templating system.
 *
 * @link https://evnt.is/1amp Help article for RSVP & Ticket template files.
 *
 * @since 4.12.3
 * @since 5.0.0 Updated the input name used for submitting data.
 *
 * @version 5.0.0
 */

/**
 * Set the default value for the email on the RSVP form.
 *
 * @param string
 * @param Tribe__Tickets__Editor__Template $this
 *
 * @since 4.9
 */
$email = apply_filters( 'tribe_tickets_rsvp_form_email', '', $this );
?>
<div class="tribe-common-b1 tribe-common-b2--min-medium tribe-tickets__form-field tribe-tickets__form-field--required">
	<label
		class="tribe-tickets__form-field-label"
		for="tribe-tickets-rsvp-email-<?php echo esc_attr( $rsvp->ID ); ?>"
	>
		<?php esc_html_e( 'Email', 'event-tickets' ); ?><span class="screen-reader-text"><?php esc_html_e( 'required', 'event-tickets' ); ?></span>
		<span class="tribe-required" aria-hidden="true" role="presentation">*</span>
	</label>
	<input
		type="email"
		class="tribe-common-form-control-text__input tribe-tickets__form-field-input tribe-tickets__rsvp-form-field-email"
		name="tribe_tickets[<?php echo esc_attr( absint( $rsvp->ID ) ); ?>][attendees][0][email]"
		id="tribe-tickets-rsvp-email-<?php echo esc_attr( $rsvp->ID ); ?>"
		value="<?php echo esc_attr( $email ); ?>"
		required
		placeholder="<?php esc_attr_e( 'your@email.com', 'event-tickets' ); ?>"
	>
</div>
