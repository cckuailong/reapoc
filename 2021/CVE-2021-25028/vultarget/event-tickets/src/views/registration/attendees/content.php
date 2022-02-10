<?php
/**
 * This template renders the registration/purchase attendee fields
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/registration/attendees/content.php
 *
 * @link    https://evnt.is/1amp Help article for RSVP & Ticket template files.
 *
 * @since 4.9
 * @since 4.10.1 Update template paths to add the "registration/" prefix
 * @since 5.0.3 Abstract calls for whether a ticket has meta enabled.
 *
 * @version 5.0.3
 *
 * @var int   $event_id The post/event ID.
 * @var array $tickets  The list of ticket config arrays.
 */
if (
	! class_exists( 'Tribe__Tickets_Plus__Meta' )
	|| ! class_exists( 'Tribe__Tickets_Plus__Meta__Storage' )
) {
	return;
}

$storage = new Tribe__Tickets_Plus__Meta__Storage();

/** @var Tribe__Tickets_Plus__Meta $meta */
$meta = tribe( 'tickets-plus.meta' );
?>

<?php foreach ( $tickets as $ticket ) : ?>
	<?php
	// Only include those who have meta.
	if ( ! $meta->ticket_has_meta( $ticket['id'] ) ) {
		continue;
	}

	$attendee_count = 0;
	$post           = get_post( $ticket['id'] );
	?>
	<h3 class="tribe-common-h5 tribe-common-h5--min-medium tribe-common-h--alt tribe-ticket__heading"><?php echo get_the_title( $post->ID ); ?></h3>
	<?php // go through each attendee ?>
	<?php while ( $attendee_count < $ticket['qty'] ) : ?>
		<?php
			$fields     = $meta->get_meta_fields_by_ticket( $post->ID );
			$saved_meta = $storage->get_meta_data_for( $post->ID );

			$args = array(
				'event_id'   => $event_id,
				'ticket'     => $post,
				'key'        => $attendee_count,
				'fields'     => $fields,
				'saved_meta' => $saved_meta,
			);


			$this->template( 'registration/attendees/fields', $args );
			$attendee_count++;
		?>
	<?php endwhile; ?>
<?php endforeach;
