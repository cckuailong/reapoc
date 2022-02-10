<?php
/**
 * This template renders the attendee registration back to cart button
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/registration/button-cart.php
 *
 * @since 4.9
 * @since 4.10.1 Update template paths to add the "registration/" prefix
 * @since 4.11.0 Add docblock for `$this`.
 * @since 4.12.0 Prevent potential errors when $provider_obj is not valid.
 *
 * @version 4.12.0
 *
 * @var Tribe__Tickets__Attendee_Registration__View $this
 */
$provider     = $this->get( 'provider' );
$cart_url     = $this->get_cart_url( $provider );
$provider_obj = $this->get_cart_provider( $provider );

if ( method_exists( $provider_obj, 'get_checkout_url' ) ) {
	$checkout_url = $provider_obj->get_checkout_url();
} else {
	$checkout_url = '';
}

// If the cart and checkout urls are the same, don't display.
if ( strtok( $cart_url, '?' ) === strtok( $checkout_url, '?' ) ) {
	return;
}

?>
<?php if ( $cart_url ) : ?>
	<a
		href="<?php echo esc_url( $cart_url ); ?>"
		class="tribe-tickets__registration__back__to__cart"
	><?php esc_html_e( 'Back to cart', 'event-tickets' ); ?></a>
<?php endif;
