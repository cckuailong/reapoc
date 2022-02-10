<?php
/**
 * Block: Tickets
 * Registration Attendee Fields Select
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/blocks/tickets/registration/attendee/fields/select.php
 *
 * See more documentation about our Blocks Editor templating system.
 *
 * @link https://evnt.is/1amp Help article for RSVP & Ticket template files.
 *
 * @since 4.9.3
 * @version 4.11.0
 *
 */

$required      = isset( $field->required ) && 'on' === $field->required ? true : false;
$field         = (array) $field;
$attendee_id   = null;
$value         = '';
$is_restricted = false;
$slug          = $field['slug'];
$options       = null;
$field_name    = 'tribe-tickets-meta[' . $attendee_id . '][' .esc_attr( $slug ) . ']';

if ( isset( $field['extra'] ) && ! empty( $field['extra']['options'] ) ) {
	$options = $field['extra']['options'];
}

if ( ! $options ) {
	return;
}

$option_id = "tribe-tickets-meta_{$slug}" . ( $attendee_id ? '_' . $attendee_id : '' );
?>
<div
	class="tribe-field tribe-tickets__item__attendee__field__select <?php echo $required ? 'tribe-tickets-meta-required' : ''; ?>"
>
	<label class="tribe-common-b1 tribe-common-b2--min-medium" for="<?php echo esc_attr( $option_id ); ?>"><?php echo wp_kses_post( $field['label'] ); ?><?php tribe_required_label( $required ); ?></label>
	<select	<?php disabled( $is_restricted ); ?>
		id="<?php echo esc_attr( $option_id ); ?>"
		class="ticket-meta ticket-metatribe-common-form-control-select__input"
		name="<?php echo esc_attr( $field_name ); ?>"
		<?php tribe_required( $required ); ?>
	>
		<option><?php esc_html_e( 'Select an option', 'event-tickets' ); ?></option>
		<?php foreach ( $options as $option ) : ?>
			<option <?php selected( $option, $value ); ?>><?php echo esc_html( $option ); ?></option>
		<?php endforeach; ?>
	</select>
</div>
