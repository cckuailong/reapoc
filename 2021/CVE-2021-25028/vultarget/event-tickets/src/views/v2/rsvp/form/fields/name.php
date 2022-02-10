<?php
/**
 * Block: RSVP
 * Form Name
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/v2/rsvp/form/fields/name.php
 *
 * See more documentation about our Blocks Editor templating system.
 *
 * @link https://evnt.is/1amp Help article for RSVP & Ticket template files.
 *
 * @since 4.12.3
 * @since 5.0.0 Updated the input name used for submitting.
 *
 * @version 5.0.0
 */

/**
 * Set the default Full Name for the RSVP form
 *
 * @param string
 * @param Tribe__Tickets__Editor__Template $this
 *
 * @since 4.9
 */
$name = apply_filters( 'tribe_tickets_rsvp_form_full_name', '', $this );
?>
<div class="tribe-common-b1 tribe-common-b2--min-medium tribe-tickets__form-field tribe-tickets__form-field--required">
	<label
		class="tribe-tickets__form-field-label"
		for="tribe-tickets-rsvp-name-<?php echo esc_attr( $rsvp->ID ); ?>"
	>
		<?php esc_html_e( 'Name', 'event-tickets' ); ?><span class="screen-reader-text"><?php esc_html_e( 'required', 'event-tickets' ); ?></span>
		<span class="tribe-required" aria-hidden="true" role="presentation">*</span>
	</label>
	<input
		type="text"
		class="tribe-common-form-control-text__input tribe-tickets__form-field-input tribe-tickets__rsvp-form-field-name"
		name="tribe_tickets[<?php echo esc_attr( absint( $rsvp->ID ) ); ?>][attendees][0][full_name]"
		id="tribe-tickets-rsvp-name-<?php echo esc_attr( $rsvp->ID ); ?>"
		value="<?php echo esc_attr( $name ); ?>"
		required
		placeholder="<?php esc_attr_e( 'Your Name', 'event-tickets' ); ?>"
	>
</div>
