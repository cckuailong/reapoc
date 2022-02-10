<?php
/**
 * This template renders the attendee registration block for each ticket
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/registration-js/attendees/ticket.php
 *
 * @since 4.11.0
 * @since 4.11.3 Require Event Tickets Plus for the template.
 *
 * @version 4.11.3
 *
 */
 if (
	! class_exists( 'Tribe__Tickets_Plus__Meta' )
	|| ! class_exists( 'Tribe__Tickets_Plus__Meta__Storage' )
) {
	return;
}
/**
* @var Tribe__Tickets_Plus__Meta $meta
*/
$meta    = tribe( 'tickets-plus.main' )->meta();
?>
<script type="text/html" id="tmpl-tribe-registration--<?php echo esc_attr( $ticket['id'] ); ?>">
	<?php
	$ticket_qty = $ticket['qty'];
	$post       = get_post( $ticket['id'] );
	$fields     = $meta->get_meta_fields_by_ticket( $post->ID );
	$saved_meta = $storage->get_meta_data_for( $post->ID );
	?>
	<h3 class="tribe-common-h5 tribe-common-h5--min-medium tribe-common-h--alt tribe-ticket__heading"><?php echo esc_html( get_the_title( $post->ID ) ); ?></h3>
	<?php // go through each attendee ?>
	<?php while ( 0 < $ticket_qty ) : ?>
		<?php
			$args = array(
				'event_id'   => $event_id,
				'ticket'     => $post,
				'fields'     => $fields,
				'saved_meta' => $saved_meta,
			);

			$this->template( 'registration-js/attendees/fields', $args );
			$ticket_qty--;
		?>
	<?php endwhile; ?>
</script>
