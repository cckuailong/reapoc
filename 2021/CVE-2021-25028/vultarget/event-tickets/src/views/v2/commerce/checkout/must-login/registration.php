<?php
/**
 * Tickets Commerce: Checkout Page Must Login Registration link
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/v2/commerce/checkout/must-login/registration.php
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
 */

// Bail if WordPress is not open to user registration.
if ( empty( get_option( 'users_can_register' ) ) ) {
	return;
}

?>
<div class="tribe-common-b1 tribe-tickets__commerce-checkout-must-login-registration">
	<?php
	echo wp_kses_post(
		_x( 'or ', 'or <- create a new account', 'event-tickets' ) .
		'<a class="tribe-common-cta tribe-common-cta--alt tribe-common-b2 tribe-tickets__commerce-checkout-must-login-registration-link">' .
		__( 'create a new account', 'event-tickets' ) .
		'</a>'
	);
	?>
</div>
