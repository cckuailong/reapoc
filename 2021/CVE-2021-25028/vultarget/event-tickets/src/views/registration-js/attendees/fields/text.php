<?php
/**
 * This template renders a Single Ticket content
 * composed by Title and Description currently
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/registration-js/attendees/fields/text.php
 *
 * @since 4.11.0
 *
 * @version 4.12.1 Updated phpdoc for which field class to reference.
 *
 * @see Tribe__Tickets_Plus__Meta__Field__Text
 */

$required    = isset( $field->required ) && 'on' === $field->required ? true : false;
$option_id   = "tribe-tickets-meta_{$field->slug}_{$ticket->ID}{{data.attendee_id}}";
$field       = (array) $field;
$multiline   = isset( $field['extra'] ) && isset( $field['extra']['multiline'] ) ? $field['extra']['multiline'] : '';
$field_name  = 'tribe-tickets-meta[' . $ticket->ID . '][{{data.attendee_id}}][' . esc_attr( $field['slug'] ) . ']';
$disabled    = false;
$classes = [ 'tribe-common-b1', 'tribe-field', 'tribe-tickets__item__attendee__field__text' ];
if ( $required ) {
	$classes[] = 'tribe-tickets-meta-required';
}

if ( $multiline ) {
	$classes[] = 'tribe-tickets__item__attendee__field__textarea';
}
?>
<div <?php tribe_classes( $classes ); ?> >
	<label
		class="tribe-common-b2--min-medium tribe-tickets-meta-label"
		for="<?php echo esc_attr( $option_id ); ?>"
	><?php echo wp_kses_post( $field['label'] ); ?><?php tribe_required_label( $required ); ?></label>
	<?php if ( $multiline ) : ?>
		<textarea
			id="<?php echo esc_attr( $option_id ); ?>"
			class="tribe-common-form-control-text__input ticket-meta"
			name="<?php echo esc_attr( $field_name ); ?>"
			<?php tribe_required( $required ); ?>
			<?php tribe_disabled( $disabled ); ?>
		><?php echo esc_textarea( $value ); ?></textarea>
	<?php else : ?>
		<input
			type="text"
			id="<?php echo esc_attr( $option_id ); ?>"
			class="tribe-common-form-control-text__input ticket-meta"
			name="<?php echo esc_attr( $field_name ); ?>"
			value="<?php echo esc_attr( $value ); ?>"
			<?php tribe_required( $required ); ?>
			<?php tribe_disabled( $disabled ); ?>
		/>
	<?php endif; ?>
</div>
