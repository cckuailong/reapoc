<?php
/**
 * This template renders the Birth Date field for Attendee Information.
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/registration-js/attendees/fields/birth.php
 *
 * @since 4.12.1 Introduced template.
 * @since 4.12.2 Fix PHP notice for undefined variable. Wrap select fields in sub-container.
 * @since 4.12.3 Add a separate label per input for screen readers.
 *
 * @version 4.12.3
 *
 * @see     Tribe__Tickets_Plus__Meta__Field__Birth
 */

$required    = isset( $field->required ) && 'on' === $field->required;
$option_id   = "tribe-tickets-meta_{$field->slug}_{$ticket->ID}{{data.attendee_id}}";
/** @var Tribe__Tickets_Plus__Meta__Field__Birth $birth_field */
$birth_field = $field;
$field       = (array) $field;
$field_name  = 'tribe-tickets-meta[' . $ticket->ID . '][{{data.attendee_id}}][' . esc_attr( $field['slug'] ) . ']';
$disabled    = false;
$classes     = [
	'tribe-common-b1',
	'tribe-field',
	'tribe-tickets__item__attendee__field__birth',
	'tribe-tickets-meta-required' => $required,
];
?>
<div class="tribe_horizontal_datepicker__container">
	<div <?php tribe_classes( $classes ); ?> >
		<label
			class="tribe-common-b2--min-medium tribe-tickets-meta-label"
		>
			<?php echo wp_kses_post( $field['label'] ) . ' ' . tribe_required_label( $required ); ?>
		</label>

		<!-- Group of Month, Day, Year fields -->
		<div class="tribe_horizontal_datepicker__field_group">
			<!-- Month -->
			<div class="tribe_horizontal_datepicker">
				<label
					for="<?php echo esc_attr( $option_id . '-month' ); ?>"
					class="tribe-common-a11y-hidden"
				>
					<?php echo wp_kses_post( $field['label'] ) . esc_html_x( ' Month', 'birthdate field', 'event-tickets-plus' ); ?>
				</label>
				<select
					id="<?php echo esc_attr( $option_id . '-month' ); ?>"
					<?php tribe_disabled( $disabled ); ?>
					<?php tribe_required( $required ); ?>
					class="tribe_horizontal_datepicker__month"
				>
					<option value="" disabled selected><?php esc_html_e( 'Month', 'tribe-event-plus' ); ?></option>
					<?php foreach ( $birth_field->get_months() as $month_number => $month_name ) : ?>
						<option value="<?php echo esc_attr( $month_number ); ?>"><?php echo esc_html( $month_name ); ?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<!-- Day -->
			<div class="tribe_horizontal_datepicker">
				<label
					for="<?php echo esc_attr( $option_id . '-day' ); ?>"
					class="tribe-common-a11y-hidden"
				>
					<?php echo wp_kses_post( $field['label'] ) . esc_html_x( ' Day', 'birthdate field', 'event-tickets-plus' ); ?>
				</label>
				<select
					id="<?php echo esc_attr( $option_id . '-day' ); ?>"
					<?php tribe_disabled( $disabled ); ?>
					<?php tribe_required( $required ); ?>
					class="tribe_horizontal_datepicker__day"
				>
					<option value="" disabled selected><?php esc_html_e( 'Day', 'tribe-event-plus' ); ?></option>
					<?php foreach ( $birth_field->get_days() as $birth_day ) : ?>
						<option value="<?php echo esc_attr( $birth_day ); ?>"><?php echo esc_html( $birth_day ); ?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<!-- Year -->
			<div class="tribe_horizontal_datepicker">
				<label
					for="<?php echo esc_attr( $option_id . '-year' ); ?>"
					class="tribe-common-a11y-hidden"
				>
					<?php echo wp_kses_post( $field['label'] ) . esc_html_x( ' Year', 'birthdate field', 'event-tickets-plus' ); ?>
				</label>
				<select
					id="<?php echo esc_attr( $option_id . '-year' ); ?>"
					<?php tribe_disabled( $disabled ); ?>
					<?php tribe_required( $required ); ?>
					class="tribe_horizontal_datepicker__year"
				>
					<option value="" disabled selected><?php esc_html_e( 'Year', 'tribe-event-plus' ); ?></option>
					<?php foreach ( $birth_field->get_years() as $birth_year ) : ?>
						<option value="<?php echo esc_attr( $birth_year ); ?>"><?php echo esc_html( $birth_year ); ?></option>
					<?php endforeach; ?>
				</select>
			</div>
		</div>
	</div>
	<div>
		<input
			type="hidden"
			class="ticket-meta tribe_horizontal_datepicker__value"
			name="<?php echo esc_attr( $field_name ); ?>"
			value="<?php echo esc_attr( $value ); ?>"
			<?php tribe_disabled( $disabled ); ?>
			<?php tribe_required( $required ); ?>
		/>
	</div>
</div>
