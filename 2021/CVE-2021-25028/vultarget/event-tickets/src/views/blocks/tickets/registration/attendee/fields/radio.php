<?php
/**
 * Block: Tickets
 * Registration Attendee Fields Radio
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/blocks/tickets/registration/attendee/fields/radio.php
 *
 * See more documentation about our Blocks Editor templating system.
 *
 * @link https://evnt.is/1amp Help article for RSVP & Ticket template files.
 *
 * @since 4.9
 * @since 4.10.2 Use md5() for field name slugs
 * @version 4.11.0
 *
 */
$field         = $this->get( 'field' );
$required      = isset( $field->required ) && 'on' === $field->required ? true : false;
$field         = (array) $field;

$options = null;

if ( isset( $field['extra'] ) && ! empty( $field['extra']['options'] ) ) {
	$options = $field['extra']['options'];
}

if ( ! $options ) {
	return;
}

$attendee_id   = null;
$value         = '';
$is_restricted = false;
$slug          = $field['slug'];
?>
<div class="tribe-field tribe-tickets-meta-fieldset tribe-tickets-meta-fieldset__checkbox-radio">
	<header class="tribe-tickets-meta-label">
		<h3 class="tribe-common-b1 tribe-common-b2--min-medium" ><?php echo wp_kses_post( $field['label'] ); ?></h3>
	</header>

	<div class="tribe-common-form-control-checkbox-radio-group">
		<?php
		foreach ( $options as $option ) {
			$option_slug = md5( sanitize_title( $option ) );
			$field_slug  = $field['slug'];
			$field_name    = 'tribe-tickets-meta[' . $ticket->ID . '][' . $attendee_id . '][' . esc_attr( $slug ) . ']';
			$option_id   = "tribe-tickets-meta_{$field_slug}" . ( $attendee_id ? '_' . $attendee_id : '' ) . "_{$option_slug}";
			$slug        = $field_slug . '_' . $option_slug;
			$value       = isset( $saved_meta[ $ticket->ID ][ $attendee_id ][ $slug ] ) ? $saved_meta[ $ticket->ID ][ $attendee_id ][ $slug ] : [];
		?>

		<div class="tribe-common-form-control-radio">
			<label
				class="tribe-common-form-control-radio__label"
				for="<?php echo esc_attr( $option_id ); ?>"
			>
				<input
					class="tribe-common-form-control-radio__input"
					id="<?php echo esc_attr( $option_id ); ?>"
					name="<?php echo esc_attr( $field_name ); ?>"
					type="radio"
					value="<?php echo esc_attr( $option ); ?>"
					<?php checked( true, in_array( $slug, $value ) ); ?>
					<?php disabled( $is_restricted ); ?>
				/>
				<?php echo wp_kses_post( $option ); ?>
			</label>
		</div>
		<?php }?>
	</div>
</div>
