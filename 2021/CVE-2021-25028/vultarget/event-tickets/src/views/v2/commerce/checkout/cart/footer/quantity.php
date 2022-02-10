<?php
/**
 * Tickets Commerce: Checkout Cart Footer Quantity
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/v2/commerce/checkout/cart/footer/quantity.php
 *
 * See more documentation about our views templating system.
 *
 * @link    https://evnt.is/1amp Help article for RSVP & Ticket template files.
 *
 * @since   5.1.9
 *
 * @version 5.1.9
 *
 * @var \Tribe__Template $this                  [Global] Template object.
 * @var Module           $provider              [Global] The tickets provider instance.
 * @var string           $provider_id           [Global] The tickets provider class name.
 * @var array[]          $items                 [Global] List of Items on the cart to be checked out.
 * @var array[]          $gateways              [Global] An array with the gateways.
 * @var int              $gateways_active       [Global] The number of active gateways.
 */

?>
<div class="tribe-tickets__commerce-checkout-cart-footer-quantity">
	<?php
	echo wp_kses_post(
		sprintf(
			// Translators: %1$s: Opening span for "Quantity:" string; %2$s: Closing span for "Quantity:" string; %3$s: Opening span for the quantity; %4$s: The quantity; %5$s: Closing span for the quantity.
			__( '%1$sQuantity: %2$s%3$s%4$s%5$s', 'event-tickets' ),
			'<span class="tribe-tickets__commerce-checkout-cart-footer-quantity-label">',
			'</span>',
			'<span class="tribe-tickets__commerce-checkout-cart-footer-quantity-number">',
			array_sum( wp_list_pluck( $items, 'quantity' ) ),
			'</span>'
		)
	);
	?>
</div>
