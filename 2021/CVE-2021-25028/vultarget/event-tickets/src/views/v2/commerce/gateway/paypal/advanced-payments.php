<?php
/**
 * Tickets Commerce: Checkout Advanced Payments for PayPal
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/v2/commerce/gateway/paypal/advanced-payments.php
 *
 * See more documentation about our views templating system.
 *
 * @link    https://evnt.is/1amp Help article for RSVP & Ticket template files.
 *
 * @since   5.2.0
 *
 * @version 5.2.0
 *
 * @var bool $must_login              [Global] Whether login is required to buy tickets or not.
 * @var bool $supports_custom_payments [Global] Determines if this site supports custom payments.
 * @var bool $active_custom_payments  [Global] Determines if this site supports custom payments.
 */

if ( $must_login || ! $active_custom_payments ) {
	return;
}

?>

<!-- Advanced credit and debit card payments form -->
<div class="tribe-tickets__commerce-checkout-paypal-advanced-payments-container">

	<?php $this->template( 'gateway/paypal/advanced-payments/separator' ); ?>

	<?php $this->template( 'gateway/paypal/advanced-payments/form' ); ?>

</div>
