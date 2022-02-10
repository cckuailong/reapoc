<?php
/**
 * This template renders a Single Ticket content
 * composed by Title and Description currently
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/registration-js/attendees/fields/radio.php
 *
 * @since 4.11.0
 * @since 4.12.1 Make sure label/input IDs don't conflict with other ticket fields.
 *
 * @version 4.12.1 Updated phpdoc for which field class to reference.
 *
 * @see Tribe__Tickets_Plus__Meta__Field__Radio
 */

$field         = $this->get( 'field' );
$required      = isset( $field->required ) && 'on' === $field->required ? true : false;
$field         = (array) $field;

$field    = $this->get( 'field' );
$required = isset( $field->required ) && 'on' === $field->required ? true : false;
$field    = (array) $field;

$options = null;

if ( isset( $field['extra'] ) && ! empty( $field['extra']['options'] ) ) {
	$options = $field['extra']['options'];
}

if ( ! $options ) {
	return;
}

$value      = '';
$disabled   = false;
$slug       = $field['slug'];
$field_name = 'tribe-tickets-meta[' . $ticket->ID . '][{{data.attendee_id}}][' . esc_attr( $slug ) . ']';
?>
<div class="tribe-field tribe-tickets-meta-fieldset tribe-tickets-meta-fieldset__checkbox-radio <?php echo $required ? 'tribe-tickets-meta-required' : ''; ?>">
	<header class="tribe-tickets-meta-label">
		<h3 class="tribe-common-b1 tribe-common-b2--min-medium"><?php echo wp_kses_post( $field['label'] ); ?><?php tribe_required_label( $required ); ?></h3>
	</header>

	<div class="tribe-common-form-control-checkbox-radio-group">
		<?php
		foreach ( $options as $option ) :
			$option_slug = md5( sanitize_title( $option ) );
			$field_slug  = $field['slug'];
			$option_id   = "tribe-tickets-meta_{$ticket->ID}_{$field_slug}{{data.attendee_id}}_{$option_slug}";
			$slug        = $field_slug . '_' . $option_slug;
			$value       = [];
		?>

		<div class="tribe-common-form-control-radio">
			<label
				class="tribe-common-form-control-radio__label"
				for="<?php echo esc_attr( $option_id ); ?>"
			>
				<input
					class="tribe-common-form-control-radio__input ticket-meta"
					id="<?php echo esc_attr( $option_id ); ?>"
					name="<?php echo esc_attr( $field_name ); ?>"
					type="radio"
					value="<?php echo esc_attr( $option ); ?>"
					<?php checked( true, in_array( $slug, $value ) ); ?>
					<?php tribe_disabled( $disabled ); ?>
					<?php tribe_required( $required ); ?>
				/>
				<?php echo wp_kses_post( $option ); ?>
			</label>
		</div>
		<?php endforeach; ?>
	</div>
</div>
