<?php
/**
 * This template renders the RSVP ticket form quantity input.
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/v2/rsvp/form/fields/quantity.php
 *
 * @since 4.12.3
 * @since 5.0.0 Updated the input name used for submitting.
 *
 * @version 5.0.0
 */

/** @var Tribe__Tickets__Tickets_Handler $tickets_handler */
$tickets_handler = tribe( 'tickets.handler' );

$max_at_a_time = $tickets_handler->get_ticket_max_purchase( $rsvp->ID );
$field_label   = 'going' === $going ? __( 'Number of Guests', 'event-tickets' ) : __( 'Number of Guests Not Attending', 'event-tickets' );
?>
<div class="tribe-common-b1 tribe-tickets__form-field tribe-tickets__form-field--required">
	<label
		class="tribe-common-b2--min-medium tribe-tickets__form-field-label"
		for="quantity_<?php echo absint( $rsvp->ID ); ?>"
	>
		<?php echo esc_html( $field_label ); ?><span class="screen-reader-text">(<?php esc_html_e( 'required', 'event-tickets' ); ?>)</span>
		<span class="tribe-required" aria-hidden="true" role="presentation">*</span>
	</label>
	<input
		type="number"
		name="tribe_tickets[<?php echo esc_attr( absint( $rsvp->ID ) ); ?>][quantity]"
		id="quantity_<?php echo esc_attr( absint( $rsvp->ID ) ); ?>"
		class="tribe-common-form-control-text__input tribe-tickets__form-field-input tribe-tickets__rsvp-form-input-number tribe-tickets__rsvp-form-field-quantity"
		value="1"
		required
		min="1"
		max="<?php echo esc_attr( $max_at_a_time ); ?>"
	>
</div>
