<?php
/**
 * Tickets Commerce: Checkout Cart Item title
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/v2/commerce/checkout/cart/item/details/toggle.php
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
 * @var bool             $must_login            [Global] Whether login is required to buy tickets or not.
 * @var string           $login_url             [Global] The site's login URL.
 * @var string           $registration_url      [Global] The site's registration URL.
 * @var bool             $is_tec_active         [Global] Whether `The Events Calendar` is active or not.
 * @var array[]          $gateways              [Global] An array with the gateways.
 * @var int              $gateways_active       [Global] The number of active gateways.
 * @var array            $item                  Which item this row will be for.
 */

$aria_controls = 'tribe-tickets__commerce-checkout-cart-item-details-description--' . $item['ticket_id'];
?>
<div class="tribe-tickets__commerce-checkout-cart-item-details-toggle">
	<button
		type="button"
		class="tribe-common-b2 tribe-common-b3--min-medium tribe-tickets__commerce-checkout-cart-item-details-button--more"
		aria-controls="<?php echo esc_attr( $aria_controls ); ?>"
	>
		<span class="screen-reader-text tribe-common-a11y-visual-hide">
			<?php esc_html_e( 'Open the ticket description in checkout.', 'event-tickets' ); ?>
		</span>
		<span class="tribe-tickets__commerce-checkout-cart-item-details-button-text">
			<?php echo esc_html_x( 'More info', 'Opens the ticket description', 'event-tickets' ); ?>
		</span>
	</button>
	<button
		type="button"
		class="tribe-common-b2 tribe-common-b3--min-medium tribe-tickets__commerce-checkout-cart-item-details-button--less"
		aria-controls="<?php echo esc_attr( $aria_controls ); ?>"
	>
		<span class="screen-reader-text tribe-common-a11y-visual-hide">
			<?php esc_html_e( 'Close the ticket description in checkout.', 'event-tickets' ); ?>
		</span>
		<span class="tribe-tickets__commerce-checkout-cart-item-details-button-text">
			<?php echo esc_html_x( 'Less info', 'Closes the ticket description', 'event-tickets' ); ?>
		</span>
	</button>
</div>
