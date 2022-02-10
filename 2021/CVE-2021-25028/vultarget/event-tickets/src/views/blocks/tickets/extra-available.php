<?php
/**
 * Block: Tickets
 * Extra column, available
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/blocks/tickets/extra-available.php
 *
 * See more documentation about our Blocks Editor templating system.
 *
 * @link    https://evnt.is/1amp Help article for RSVP & Ticket template files.
 *
 * @since   4.9.3
 * @since   4.11.1 Corrected amount of available/remaining tickets.
 * @since   4.11.5 Corrected the way to get the actual stock available.
 *
 * @version 4.11.5
 *
 * @var Tribe__Tickets__Editor__Template $this
 */

/** @var Tribe__Tickets__Ticket_Object $ticket */
$ticket = $this->get( 'ticket' );

if ( empty( $ticket->ID ) ) {
	return;
}

$available = $ticket->available();
$post_id   = $this->get( 'post_id' );

/** @var Tribe__Settings_Manager $settings_manager */
$settings_manager = tribe( 'settings.manager' );

$threshold = $settings_manager::get_option( 'ticket-display-tickets-left-threshold', null );

/**
 * Overwrites the threshold to display "# tickets left".
 *
 * @since 4.11.1
 *
 * @param int   $threshold Stock threshold to trigger display of "# tickets left"
 * @param int   $post_id   WP_Post/Event ID.
 */
$threshold = absint( apply_filters( 'tribe_display_tickets_block_tickets_left_threshold', $threshold, $post_id ) );

/**
 * Allows hiding of "unlimited" to be toggled on/off conditionally.
 *
 * @since 4.11.1
 *
 * @param int $show_unlimited allow showing of "unlimited".
 */
$show_unlimited = apply_filters( 'tribe_tickets_block_show_unlimited_availability', true, $available );
?>
<div
	class="tribe-common-b3 tribe-tickets__item__extra__available"
>
	<?php if ( $show_unlimited && - 1 === $available ) : ?>
		<?php
		$this->template(
			'blocks/tickets/extra-available-unlimited',
			[
				'ticket' => $ticket,
				'key'    => $key,
			]
		);
		?>
	<?php elseif ( 0 === $threshold || $available <= $threshold ) : ?>
		<?php
		$this->template(
			'blocks/tickets/extra-available-quantity',
			[
				'ticket'    => $ticket,
				'available' => $available,
			]
		);
		?>
	<?php endif; ?>
</div>
