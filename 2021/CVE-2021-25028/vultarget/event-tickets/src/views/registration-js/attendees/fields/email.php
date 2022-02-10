<?php
/**
 * This template renders a Single Ticket content
 * composed by Title and Description currently
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/registration-js/attendees/fields/email.php
 *
 * @since  4.12.1
 *
 * @see Tribe__Tickets_Plus__Meta__Field__Email
 */

$required   = isset( $field->required ) && 'on' === $field->required ? true : false;
$option_id  = "tribe-tickets-meta_{$field->slug}_{$ticket->ID}{{data.attendee_id}}";
$field      = (array) $field;
$field_name = 'tribe-tickets-meta[' . $ticket->ID . '][{{data.attendee_id}}][' . esc_attr( $field['slug'] ) . ']';
$disabled   = false;
$classes    = [
	'tribe-common-b1',
	'tribe-field',
	'tribe-tickets__item__attendee__field__email',
	'tribe-tickets-meta-required' => $required,
];
?>
<div <?php tribe_classes( $classes ); ?> >
	<label
		class="tribe-common-b2--min-medium tribe-tickets-meta-label"
		for="<?php echo esc_attr( $option_id ); ?>"
	><?php echo wp_kses_post( $field['label'] ); ?><?php tribe_required_label( $required ); ?></label>
	<input
		type="email"
		id="<?php echo esc_attr( $option_id ); ?>"
		class="tribe-common-form-control-email__input ticket-meta"
		name="<?php echo esc_attr( $field_name ); ?>"
		value="<?php echo esc_attr( $value ); ?>"
		<?php tribe_required( $required ); ?>
		<?php tribe_disabled( $disabled ); ?>
	/>
</div>
