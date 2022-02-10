<?php
/**
 * Tickets Commerce: Checkout Page Footer > Gateway error
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/v2/commerce/checkout/footer/gateway-error.php
 *
 * See more documentation about our views templating system.
 *
 * @link    https://evnt.is/1amp Help article for RSVP & Ticket template files.
 *
 * @since   5.1.10
 *
 * @version 5.1.10
 *
 * @var \Tribe__Template $this               [Global] Template object.
 * @var Module           $provider           [Global] The tickets provider instance.
 * @var string           $provider_id        [Global] The tickets provider class name.
 * @var array[]          $items              [Global] List of Items on the cart to be checked out.
 * @var bool             $must_login         [Global] Whether login is required to buy tickets or not.
 * @var string           $login_url          [Global] The site's login URL.
 * @var string           $registration_url   [Global] The site's registration URL.
 * @var bool             $is_tec_active      [Global] Whether `The Events Calendar` is active or not.
 * @var array[]          $gateways           [Global] An array with the gateways.
 * @var int              $gateways_active    [Global] The number of active gateways.
 * @var int              $gateways_connected [Global] The number of connected gateways.
 */

// Bail if the cart is empty or if there's active gateways.
if ( empty( $items ) || tribe_is_truthy( $gateways_active ) ) {
	return;
}

tribe( 'tickets.editor.template' )->template(
	'components/notice',
	[
		'id'             => 'tribe-tickets__commerce-checkout-footer-notice-error--no-gateway',
		'notice_classes' => [
			'tribe-tickets__notice--error',
			'tribe-tickets__commerce-checkout-footer-notice-error--no-gateway',
		],
		'title'          => __( 'Checkout Unavailable!', 'event-tickets' ),
		'content'        => __( 'Checkout is not available at this time because a payment method has not been set up. Please notify the site administrator.', 'event-tickets' ),
	]
);
