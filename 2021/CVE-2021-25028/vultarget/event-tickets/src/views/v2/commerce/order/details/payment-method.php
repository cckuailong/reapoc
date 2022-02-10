<?php
/**
 * Tickets Commerce: Success Order Page Details > Payment Method
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/v2/commerce/order/details/payment-method.php
 *
 * See more documentation about our views templating system.
 *
 * @link    https://evnt.is/1amp Help article for RSVP & Ticket template files.
 *
 * @since   5.1.10
 *
 * @since 5.2.0 Added Payment method label.
 *
 * @version 5.1.10
 *
 * @var \Tribe__Template $this                  [Global] Template object.
 * @var Module           $provider              [Global] The tickets provider instance.
 * @var string           $provider_id           [Global] The tickets provider class name.
 * @var \WP_Post         $order                 [Global] The order object.
 * @var int              $order_id              [Global] The order ID.
 * @var bool             $is_tec_active         [Global] Whether `The Events Calendar` is active or not.
 * @var string           $payment_method        [Global] The payment method label.
 */

if ( empty( $order->gateway ) ) {
	return;
}

?>
<div class="tribe-tickets__commerce-order-details-row">
	<div class="tribe-tickets__commerce-order-details-col1">
		<?php esc_html_e( 'Payment method:', 'event-tickets' ); ?>
	</div>
	<div class="tribe-tickets__commerce-order-details-col2">
		<?php echo esc_html( $payment_method ); ?>
	</div>
</div>
