<?php
/**
 * Tickets Commerce: Checkout Advanced Payments for PayPal - Expiration date field
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/v2/commerce/gateway/paypal/advanced-payments/fields/expiration-date.php
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

$label_classes = [
	'tribe-common-b3',
	'tribe-tickets__commerce-checkout-paypal-advanced-payments-form-field-label',
];

$field_classes = [
	'card_field',
	'tribe-tickets__commerce-checkout-paypal-advanced-payments-form-field',
	'tribe-tickets__commerce-checkout-paypal-advanced-payments-form-field--expiration-date',
];

?>
<div class="tribe-tickets__commerce-checkout-paypal-advanced-payments-form-field-wrapper">
	<label for="tec-tc-expiration-date" <?php tribe_classes( $label_classes ); ?>>
		<?php esc_html_e( 'Expiration Date', 'event-tickets' ); ?>
	</label>
	<div
		id="tec-tc-expiration-date"
		<?php tribe_classes( $field_classes ); ?>
	></div>
</div>
