<?php
/**
 * Tickets Commerce: Success Order Page Details > PayPal capture ID
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/v2/commerce/gateway/paypal/order/details/capture-id.php
 *
 * See more documentation about our views templating system.
 *
 * @link    https://evnt.is/1amp Help article for RSVP & Ticket template files.
 *
 * @since   5.2.0
 *
 * @version 5.2.0
 *
 * @var \Tribe__Template $this          [Global] Template object.
 * @var Module           $provider      [Global] The tickets provider instance.
 * @var string           $provider_id   [Global] The tickets provider class name.
 * @var \WP_Post         $order         [Global] The order object.
 * @var int              $order_id      [Global] The order ID.
 * @var bool             $is_tec_active [Global] Whether `The Events Calendar` is active or not.
 * @var string           $capture_id    PayPal Capture ID for this order.
 */

if (
	empty( $order->gateway )
	&& Gateway::get_key() !== $order->gateway
) {
	return;
}

// Couldn't find a valid Capture ID.
if ( empty( $capture_id ) ) {
	return;
}

?>
<div class="tribe-tickets__commerce-order-details-row">
	<div class="tribe-tickets__commerce-order-details-col1">
		<?php esc_html_e( 'PayPal Capture ID:', 'event-tickets' ); ?>
	</div>
	<div class="tribe-tickets__commerce-order-details-col2">
		<?php echo esc_html( $capture_id ); ?>
	</div>
</div>
