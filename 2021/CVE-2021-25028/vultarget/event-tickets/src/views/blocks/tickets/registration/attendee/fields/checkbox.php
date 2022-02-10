<?php
/**
 * Block: Tickets
 * Registration Attendee Fields Checkbox
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/blocks/tickets/registration/attendee/fields/checkbox.php
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
$required      = isset( $field->required ) && 'on' === $field->required ? true : false;
$field         = (array) $field;
$attendee_id   = $key;
$options       = Tribe__Utils__Array::get( $field, [ 'extra', 'options' ], null );
$field_name    = 'tribe-tickets-meta[' . $ticket->ID . '][' . $attendee_id . ']';
$is_restricted = false;

if ( ! $options ) {
	return;
}
?>

<fieldset>
	<div class="tribe-tickets-meta-fieldset tribe-tickets-meta-fieldset__checkbox-radio">
		<header class="tribe-tickets-meta-label">
			<h3 class="tribe-common-b1 tribe-common-b2--min-medium"><?php echo wp_kses_post( $field['label'] ); ?></h3>
		</header>

		<div class="tribe-common-form-control-checkbox-radio-group">
			<?php
			foreach ( $options as $option ) :
				$option_slug = md5( sanitize_title( $option ) );
				$field_slug  = $field['slug'];
				$option_id   = "tribe-tickets-meta_{$field_slug}" . ( $attendee_id ? '_' . $attendee_id : '' ) . "_{$option_slug}";
				$slug        = $field_slug . '_' . $option_slug;
				$value       = isset( $saved_meta[ $ticket->ID ][ $attendee_id ][ $slug ] ) ? $saved_meta[ $ticket->ID ][ $attendee_id ][ $slug ] : [];
			?>

			<div class="tribe-common-form-control-checkbox">
				<label
					class="tribe-common-form-control-checkbox__label"
					for="<?php echo esc_attr( $option_id ); ?>"
				>
					<input
						class="tribe-common-form-control-checkbox__input"
						id="<?php echo esc_attr( $option_id ); ?>"
						name="tribe-tickets-meta[<?php echo esc_attr( $attendee_id ); ?>][<?php echo esc_attr( $slug ); ?>]"
						type="checkbox"
						value="<?php echo esc_attr( $option ); ?>"
						<?php checked( true, in_array( $slug, $value ) ); ?>
						<?php disabled( $is_restricted ); ?>
					/>
					<?php echo wp_kses_post( $option ); ?>
				</label>
			</div>
			<?php endforeach; ?>
		</div>
		<input
			type="hidden"
			name="<?php echo esc_attr( $field_name . '[0]' ); ?>"
			value=""
		>
	</div>
</fieldset>
