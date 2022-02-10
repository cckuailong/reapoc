<?php
/**
 * Tickets Commerce: Checkout Buttons for PayPal
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/v2/commerce/gateway/paypal/buttons.php
 *
 * See more documentation about our views templating system.
 *
 * @link    https://evnt.is/1amp Help article for RSVP & Ticket template files.
 *
 * @since   5.1.9
 *
 * @version 5.1.9
 * @var bool $must_login [Global] Whether login is required to buy tickets or not.
 */

if ( $must_login ) {
	return;
}
?>

<div id="tec-tc-gateway-paypal-checkout-buttons" class="tribe-tickets__commerce-checkout-paypal-buttons"></div>
