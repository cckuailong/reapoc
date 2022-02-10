<?php
/**
 * Block: RSVP
 * Details Availability
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/blocks/rsvp/details/availability.php
 *
 * See more documentation about our Blocks Editor templating system.
 *
 * @link  https://evnt.is/1amp Help article for RSVP & Ticket template files.
 *
 * @since 4.9.3
 * @since 4.11.1 Corrected amount of available/remaining tickets.
 *
 * @version 4.11.1
 */

/** @var Tribe__Settings_Manager $settings_manager */
$settings_manager = tribe( 'settings.manager' );

$threshold = $settings_manager::get_option( 'ticket-display-tickets-left-threshold', 0 );

/**
 * Overwrites the threshold to display "# tickets left".
 *
 * @param int   $threshold Stock threshold to trigger display of "# tickets left"
 * @param array $data      Ticket data.
 * @param int   $event_id  Event ID.
 *
 * @since 4.11.1
 */
$threshold = absint( apply_filters( 'tribe_display_rsvp_block_tickets_left_threshold', $threshold, tribe_events_get_ticket_event( $ticket ) ) );

$remaining_tickets = $ticket->remaining();
$is_unlimited = -1 === $remaining_tickets;

/** @var Tribe__Tickets__Tickets_Handler $handler */
$handler = tribe( 'tickets.handler' );

/**
 * Allows hiding of "unlimited" to be toggled on/off conditionally.
 *
 * @param int   $show_unlimited allow showing of "unlimited".
 *
 * @since 4.11.1
 */
$show_unlimited = apply_filters( 'tribe_rsvp_block_show_unlimited_availability', false, $is_unlimited );
?>
<div class="tribe-block__rsvp__availability">
	<?php if ( ! $ticket->is_in_stock() ) : ?>
		<span class="tribe-block__rsvp__no-stock"><?php esc_html_e( 'Out of stock!', 'event-tickets' ); ?></span>
	<?php elseif ( $is_unlimited ) : ?>
		<?php if ( $show_unlimited) : ?>
			<span class="tribe-block__rsvp__unlimited"><?php echo esc_html( $handler->unlimited_term ); ?></span>
		<?php endif; ?>
	<?php elseif ( 0 === $threshold || $remaining_tickets <= $threshold ) : ?>
		<span class="tribe-block__rsvp__quantity"><?php echo esc_html( $remaining_tickets ); ?> </span>
		<?php esc_html_e( 'remaining', 'event-tickets' ) ?>
	<?php endif; ?>
</div>
