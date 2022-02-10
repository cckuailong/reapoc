<?php
/**
 * This template renders a the fields for a ticket
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/registration/attendees/fields.php
 *
 * @since 4.9
 * @since 4.10.1 Update template paths to add the "registration/" prefix
 * @version 4.11.0
 *
 */
?>
<div class="tribe-common-h7 tribe-common-h6--min-medium tribe-common-h--alt tribe-ticket">
	<h4 class="tribe-common-b1"><?php esc_html_e( 'Attendee', 'event-tickets' ); ?> <?php echo esc_html( $key + 1 ); ?></h4>
	<?php foreach ( $fields as $field ) : ?>
		<?php
			$value = ! empty( $saved_meta[ $ticket->ID ][ $key ][ $field->slug ] ) ? $saved_meta[ $ticket->ID ][ $key ][ $field->slug ] : null;

			$args = array(
				'event_id'   => $event_id,
				'ticket'     => $ticket,
				'field'      => $field,
				'value'      => $value,
				'key'        => $key,
				'saved_meta' => $saved_meta,
			);

			$this->template( 'registration/attendees/fields/' . $field->type, $args );
		?>
	<?php endforeach; ?>
</div>
