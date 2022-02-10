<?php
/**
 * Block: Tickets
 * Extra column
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/blocks/tickets/extra.php
 *
 * See more documentation about our Blocks Editor templating system.
 *
 * @link https://evnt.is/1amp Help article for RSVP & Ticket template files.
 *
 * @since 4.9
 * @since 4.11.1 Changed some HTML class names.
 * @since 4.11.3 Updated the button to include a type - helps avoid submitting forms unintentionally. Updated comments and array formatting.
 * @since 4.11.4 Added accessibility classes to screen reader text elements.
 * @since 4.12.0    Added implementation for the price suffix.
 *
 * @version 4.12.0
 *
 * @var Tribe__Tickets__Editor__Template $this
 */
$classes = [ 'tribe-tickets__item__extra' ];

/** @var Tribe__Tickets__Ticket_Object $ticket */
$ticket     = $this->get( 'ticket' );
$has_suffix = ! empty( $ticket->price_suffix );

$classes['tribe-tickets__item__extra--price-suffix'] = $has_suffix;

$is_mini = $this->get( 'is_mini' );

$context = [
	'ticket' => $ticket,
	'key' => $this->get( 'key' ),
	'provider' => $this->get( 'provider' ),
	'post_id' => $this->get( 'post_id' ),
];

$modal = $this->get( 'is_modal' );

$id = 'tribe__details__content--' . $ticket->ID;
?>
<div <?php tribe_classes( $classes ); ?>>
	<?php $this->template( 'blocks/tickets/extra-price', $context ); ?>
	<?php if ( true !== $is_mini ) : ?>
		<?php $this->template( 'blocks/tickets/extra-available', $context ); ?>
	<?php endif; ?>
	<?php if ( true !== $modal && true !== $is_mini && $ticket->show_description() && ! empty( $ticket->description ) ) : ?>
		<div class="tribe-tickets__item__details__summary">
			<button
				type="button"
				class="tribe-common-b3 tribe-tickets__item__details__summary--more"
				aria-controls="<?php echo esc_attr( $id ); ?>"
				tabindex="0"
			>
				<span class="screen-reader-text tribe-common-a11y-visual-hide"><?php esc_html_e( 'Open the ticket description.', 'event-tickets' ); ?></span>
				<?php echo esc_html_x( 'More', 'Opens the ticket description', 'event-tickets' ); ?>
			</button>
			<button
				type="button"
				class="tribe-common-b3 tribe-tickets__item__details__summary--less"
				aria-controls="<?php echo esc_attr( $id ); ?>"
				tabindex="0"
			>
				<span class="screen-reader-text tribe-common-a11y-visual-hide"><?php esc_html_e( 'Close the ticket description.', 'event-tickets' ); ?></span>
				<?php echo esc_html_x( 'Less', 'Closes the ticket description', 'event-tickets' ); ?>
			</button>
	</div>
	<?php endif; ?>
</div>
