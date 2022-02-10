<?php
/**
 * Block: Tickets
 * Registration Attendee Fields Text
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/blocks/tickets/registration/attendee/fields/text.php
 *
 * See more documentation about our Blocks Editor templating system.
 *
 * @link https://evnt.is/1amp Help article for RSVP & Ticket template files.
 *
 * @since 4.9
 * @version 4.11.0
 *
 */

$attendee_id   = null;
$required      = isset( $field->required ) && 'on' === $field->required ? true : false;
$option_id     = "tribe-tickets-meta_{$field->slug}" . ( $attendee_id ? '_' . $attendee_id : '' );
$field         = (array) $field;
$multiline     = isset( $field['extra'] ) && isset( $field['extra']['multiline'] ) ? $field['extra']['multiline'] : '';
$value         = '';
$is_restricted = false;
$field_name    = 'tribe-tickets-meta[' . $attendee_id . '][' . esc_attr( $field['slug'] ) . ']';
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
			name="<?php echo esc_attr( $field_name ); ?>"
			class="ticket-metatribe-common-form-control-text__input"
			<?php tribe_required( $required ); ?>
			<?php disabled( $is_restricted ); ?>
		><?php echo esc_textarea( $value ); ?></textarea>
	<?php else : ?>
		<input
			type="text"
			id="<?php echo esc_attr( $option_id ); ?>"
			name="<?php echo esc_attr( $field_name ); ?>"
			value="<?php echo esc_attr( $value ); ?>"
			<?php tribe_required( $required ); ?>
			<?php disabled( $is_restricted ); ?>
		>
	<?php endif; ?>
</div>
