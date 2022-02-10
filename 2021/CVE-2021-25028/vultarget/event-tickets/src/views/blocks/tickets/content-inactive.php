<?php
/**
 * Block: Tickets
 * Inactive Content
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/blocks/tickets/content-inactive.php
 *
 * See more documentation about our Blocks Editor templating system.
 *
 * @link https://evnt.is/1amp Help article for RSVP & Ticket template files.
 *
 * @since 4.12.1 Updated message to use tribe_get_ticket_label_plural() for "Tickets" string
 * @version 4.12.1
 *
 */

/* translators: %s: Tickets label */
$message = $this->get( 'is_sale_past' ) ? sprintf( __( '%s are no longer available', 'event-tickets' ), tribe_get_ticket_label_plural( 'event-tickets' ) ) : sprintf( __( '%s are not yet available', 'event-tickets' ), tribe_get_ticket_label_plural( 'event-tickets' ) );
?>
<div
	class="tribe-tickets__item__content tribe-tickets__item__content--inactive"
>
	<?php echo esc_html( $message ) ?>
</div>
