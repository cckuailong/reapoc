<?php
/**
 * Block: RSVP
 * Status Not Going
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/blocks/rsvp/status/not-going.php
 *
 * See more documentation about our Blocks Editor templating system.
 *
 * @link https://evnt.is/1amp Help article for RSVP & Ticket template files.
 *
 * @since 4.9.3
 * @version 4.10.4
 *
 */

/**
 * @todo: Create a hook for the get_ticket method in order to set dynamic or custom properties into
 * the instance variable so we can set a new one called $ticket->show_not_going.
 *
 * Method is located on:
 * - https://github.com/the-events-calendar/event-tickets/blob/9e77f61f191bbc86ee9ec9a0277ed7dde66ba0d8/src/Tribe/RSVP.php#L1130
 *
 * For now we need to access directly the value of the meta field in order to render this field.
 */
$show_not_going = tribe_is_truthy(
	get_post_meta( $ticket->ID, '_tribe_ticket_show_not_going', true )
);

if ( ! $show_not_going ) {
    return;
}
?>
<span>
	<button
		class="tribe-block__rsvp__status-button tribe-block__rsvp__status-button--not-going<?php if ( 'no' === $going ) { echo ' tribe-active'; }?>"
		<?php echo disabled( 'no', $going, false ); ?>
	>
		<span><?php echo esc_html_x( 'Not going', 'Label for the RSVP not going button', 'event-tickets' ); ?></span>
		<?php $this->template( 'blocks/rsvp/status/not-going-icon' ); ?>
	</button>
</span>
