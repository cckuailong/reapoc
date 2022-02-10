<?php
/**
 * Tickets Commerce: Checkout Advanced Payments for PayPal - Submit
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/v2/commerce/gateway/paypal/advanced-payments/fields/submit.php
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
 */

$classes = [
	'tribe-common-c-btn',
	'tribe-tickets__commerce-checkout-paypal-advanced-payments-form-submit-button',
];

?>
<button value="submit" id="submit" <?php tribe_classes( $classes ); ?>>
	<?php esc_html_e( 'Purchase Tickets', 'event-tickets' ); ?>
</button>
