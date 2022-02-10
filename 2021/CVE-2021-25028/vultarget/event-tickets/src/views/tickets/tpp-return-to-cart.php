<?php
/**
 * This template renders the "Return to Cart" text.
 *
 * Override this template in your own theme by creating a file at:
 *
 * [your-theme]/tribe-events/tickets/tpp-return-to-cart.php
 *
 * @link    https://evnt.is/1amp Help article for RSVP & Ticket template files.
 *
 * @version 5.1.3
 */

$link = tribe( 'tickets.commerce.paypal.links' )->return_to_cart();
?>

<a href="<?php echo esc_url( $link ) ?>" target="_self" class="tribe-commerce return-to-cart">
	<?php esc_html_e( 'Return to Cart', 'event-tickets' ) ?>
</a>
