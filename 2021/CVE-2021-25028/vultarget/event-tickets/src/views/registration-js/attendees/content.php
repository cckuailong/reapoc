<?php
/**
 * This template renders the registration/purchase attendee fields
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/registration-js/attendees/content.php
 *
 * @link    https://evnt.is/1amp Help article for RSVP & Ticket template files.
 *
 * @since 4.11.0
 * @since 5.0.3 Updated template link.
 *
 * @version 5.0.3
 *
 */
if (
	! class_exists( 'Tribe__Tickets_Plus__Meta' )
	|| ! class_exists( 'Tribe__Tickets_Plus__Meta__Storage' )
) {
	return;
}

$storage = new Tribe__Tickets_Plus__Meta__Storage();
/**
* @var Tribe__Tickets_Plus__Meta $meta
*/
$meta     = tribe( 'tickets-plus.main' )->meta();
$provider = $this->get( 'provider' );
$event_id = $this->get( 'event_id' );
$tickets  = $this->get( 'tickets' );

if ( empty( $tickets ) ) {
	// Nothing to see here!
	return;
}
?>
<?php foreach ( $tickets as $ticket ) : ?>
		<?php
		// Sometimes we get an array - let's handle that.
		if ( is_array( $ticket ) ) {
			$ticket = $provider->get_ticket( $event_id, $ticket['id'] );
		}

		/** @var Tribe__Tickets__Ticket_Object $ticket */

		// Only include tickets with meta.
		if ( ! $ticket->has_meta_enabled() ) {
			continue;
		}
		?>
		<script type="text/html" class="registration-js-attendees-content" id="tmpl-tribe-registration--<?php echo esc_attr( $ticket->ID ); ?>">
			<?php
			$ticket_qty = 1;
			$post       = get_post( $ticket->ID );
			$fields     = $meta->get_meta_fields_by_ticket( $post->ID );
			$saved_meta = $storage->get_meta_data_for( $post->ID );
			?>
			<?php // go through each attendee ?>
			<?php while ( 0 < $ticket_qty ) : ?>
				<?php

					$args = [
						'event_id'   => $event_id,
						'ticket'     => $post,
						'fields'     => $fields,
						'saved_meta' => $saved_meta,
					];

					$this->template( 'registration-js/attendees/fields', $args );
					$ticket_qty--;
				?>
			<?php endwhile; ?>
		</script>
<?php
endforeach;
