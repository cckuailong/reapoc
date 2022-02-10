<?php
/**
 * Tickets Commerce: Checkout Cart Items
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/v2/commerce/checkout/cart/items.php
 *
 * See more documentation about our views templating system.
 *
 * @link    https://evnt.is/1amp Help article for RSVP & Ticket template files.
 *
 * @since   5.1.10
 *
 * @version 5.1.10
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
 * @var int              $section               Which Section that we are going to render for this table.
 * @var \WP_Post         $post                  Which Section that we are going to render for this table.
 */

if ( empty( $items ) ) {
	return;
}

?>
<div class="tribe-tickets__commerce-checkout-cart-items">
	<?php foreach ( $items as $item ) : ?>
		<?php
		if ( $item['event_id'] !== $section ) {
			continue;
		}
		?>
		<?php $this->template( 'checkout/cart/item', [ 'section' => $section, 'post' => $post, 'item' => $item ] ); ?>
	<?php endforeach; ?>
</div>
