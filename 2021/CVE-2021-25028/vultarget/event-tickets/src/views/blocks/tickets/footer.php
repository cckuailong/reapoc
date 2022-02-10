<?php
/**
 * Block: Tickets
 * Footer
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/blocks/tickets/footer.php
 *
 * See more documentation about our Blocks Editor templating system.
 *
 * @link https://evnt.is/1amp Help article for RSVP & Ticket template files.
 *
 * @since 4.11.0
 * @since 4.12.0 Prevent potential errors when $provider_obj is not valid.
 *
 * @version 4.12.0
 */
$event_id        = $this->get( 'event_id' );
$is_modal        = $this->get( 'is_modal' );
$is_mini         = $this->get( 'is_mini' );
$tickets         = $this->get( 'tickets' );
$currency_symbol = $this->get( 'currency_symbol' );
$provider        = $this->get( 'provider' );

if ( method_exists( $provider, 'get_cart_url' ) ) {
	$cart_url = $provider->get_cart_url();
} else {
	$cart_url = '';
}

if ( method_exists( $provider, 'get_checkout_url' ) ) {
	$checkout_url = $provider->get_checkout_url();
} else {
	$checkout_url = '';
}
?>
<div class="tribe-tickets__footer" >
	<?php if ( $is_mini && strtok( $cart_url, '?' ) !== strtok( $checkout_url, '?' ) ) : ?>
		<a class="tribe-common-b2 tribe-tickets__footer__back-link" href="<?php echo esc_url( $cart_url ); ?>"><?php esc_html_e( 'Return to Cart', 'event-tickets' ); ?></a>
	<?php endif; ?>
	<?php $this->template( 'blocks/tickets/footer-quantity' ); ?>
	<?php $this->template( 'blocks/tickets/footer-total', [ 'event_id' => $event_id, 'provider' => $provider ] ); ?>
	<?php if ( ! $is_mini && ! $is_modal ) : ?>
		<?php $this->template( 'blocks/tickets/submit', [ 'tickets' => $tickets ] ); ?>
	<?php endif; ?>
</div>
