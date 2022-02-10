<?php
/**
 * The template for the select input
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/registration-js/attendees/fields/select.php
 *
 * @since 4.11.0
 *
 * @version 4.12.1 Updated phpdoc for which field class to reference.
 *
 * @see Tribe__Tickets_Plus__Meta__Field__Select
 */

$required    = isset( $field->required ) && 'on' === $field->required ? true : false;
$field       = (array) $field;
$disabled    = false;
$slug        = $field['slug'];
$options     = null;
$field_name  = 'tribe-tickets-meta[' . $ticket->ID . '][{{data.attendee_id}}][' . esc_attr( $field['slug'] ) . ']';

if ( isset( $field['extra'] ) && ! empty( $field['extra']['options'] ) ) {
	$options = $field['extra']['options'];
}

if ( ! $options ) {
	return;
}

$option_id = "tribe-tickets-meta_{$slug}_{$ticket->ID}{{data.attendee_id}}";
?>
<div class="tribe-field tribe-tickets__item__attendee__field__select <?php echo $required ? 'tribe-tickets-meta-required' : ''; ?>">
	<label class="tribe-common-b1 tribe-common-b2--min-medium tribe-tickets-meta-label" for="<?php echo esc_attr( $option_id ); ?>"><?php echo wp_kses_post( $field['label'] ); ?><?php tribe_required_label( $required ); ?></label>
	<select
		<?php tribe_disabled( $disabled ); ?>
		id="<?php echo esc_attr( $option_id ); ?>"
		class="tribe-common-form-control-select__input ticket-meta"
		name="<?php echo esc_attr( $field_name ); ?>"
		<?php tribe_required( $required ); ?>
		>
		<option value=""><?php esc_html_e( 'Select an option', 'event-tickets' ); ?></option>
		<?php foreach ( $options as $option => $label ) : ?>
			<option <?php selected( $label, $value ); ?> value="<?php echo esc_attr( $label ); ?>"><?php echo esc_html( $label ); ?></option>
		<?php endforeach; ?>
	</select>
</div>
