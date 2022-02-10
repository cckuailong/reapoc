<?php
if ( ! isset( $post_id ) ) {
	$post_id = get_the_ID();
}

if ( ! $post_id ) {
	$post_id = tribe_get_request_var( 'post_id', 0 );
}

// Makes sure we are dealing an int
$post_id = (int) $post_id;

if ( 0 === $post_id ) {
	$post_type = tribe_get_request_var( 'post_type', 'post' );
} else {
	$post_type = get_post_type( $post_id );
}

$modules = Tribe__Tickets__Tickets::modules();
?>

<?php if ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) : ?>
<div id="ticket_list_wrapper">
<?php endif; ?>

	<table id="tribe_ticket_list_table" class="tribe-tickets-editor-table eventtable ticket_list eventForm widefat fixed">
		<thead>
			<tr class="table-header">
				<th class="ticket_name column-primary"><?php echo esc_html( tribe_get_ticket_label_plural( basename( __FILE__ ) ) ); ?></th>
				<?php
				/**
				 * Allows for the insertion of additional columns into the ticket table header.
				 *
				 * @since 4.6
				 */
				do_action( 'tribe_events_tickets_ticket_table_add_header_column' );
				?>
				<th class="ticket_capacity"><?php esc_html_e( 'Capacity', 'event-tickets' ); ?></th>
				<th class="ticket_available"><?php esc_html_e( 'Available', 'event-tickets' ); ?></th>
				<th class="ticket_edit"></th>
			</tr>
		</thead>
		<?php

		foreach ( $tickets as $key => $ticket ) {
			if ( strpos( $ticket->provider_class, 'RSVP' ) !== false ) {
				$rsvp[] = $ticket;
				unset( $tickets[ $key ] );
				continue;
			}
		}

		/** @var Tribe__Tickets__Tickets_Handler $handler */
		$handler = tribe( 'tickets.handler' );

		$tickets = $handler->sort_tickets_by_menu_order( $tickets );

		/** @var Tribe__Tickets__Admin__Views $admin_views */
		$admin_views = tribe( 'tickets.admin.views' );
		?>
		<tbody class="tribe-tickets-editor-table-tickets-body">
		<?php
		if ( ! empty( $tickets ) ) {
			foreach ( $tickets as $ticket ) {
				$admin_views->template( [ 'editor', 'list-row' ], [ 'ticket' => $ticket ] );
			}
		}
		?>
		</tbody>

		<tbody>
			<?php
			if ( ! empty( $rsvp ) ) {
				foreach ( $rsvp as $ticket ) {
					$admin_views->template( [ 'editor', 'list-row' ], [ 'ticket' => $ticket ] );
				}
			}
			?>
		</tbody>
	</table>
<?php do_action( 'tribe_ticket_order_field', $post_id ); ?>

<?php if ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) : ?>
</div>
<?php endif;
