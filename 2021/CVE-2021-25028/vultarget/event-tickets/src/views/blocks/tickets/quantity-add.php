<?php
/**
 * Block: Tickets
 * Quantity Add
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/blocks/tickets/quantity-add.php
 *
 * See more documentation about our Blocks Editor templating system.
 *
 * @link https://evnt.is/1amp Help article for RSVP & Ticket template files.
 *
 * @since 4.9.3
 * @since 4.11.3 Updated the button to include a type - helps avoid submitting forms unintentionally.
 * @since 4.11.4 Added accessibility classes to screen reader text element.
 * @since 4.12.0    Removed duplicate `type="button"` from button element.
 *
 * @version 4.12.0
 *
 * @var $this Tribe__Tickets__Editor__Template
 */

$ticket = $this->get( 'ticket' );
$button_title = sprintf(
	// translators: %s: ticket name.
	_x( 'Increase ticket quantity for %s', '%s: ticket name.', 'event-tickets' ),
	$ticket->name
);
?>
<button
	class="tribe-tickets__item__quantity__add"
	title="<?php echo esc_attr( $button_title ); ?>"
	type="button"
>
	<span class="screen-reader-text tribe-common-a11y-visual-hide"><?php echo esc_html( $button_title ); ?></span>
	<?php echo esc_html_x( '+', 'A plus sign, add ticket.', 'event-tickets' ); ?>
</button>
