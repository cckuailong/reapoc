<?php
/**
 * Tickets Commerce: Checkout Script for the PayPal gateway.
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/tickets/v2/commerce/gateway/paypal/checkout-script.php
 *
 * See more documentation about our views templating system.
 *
 * @link    https://evnt.is/1amp Help article for RSVP & Ticket template files.
 *
 * @since   5.1.9
 *
 * @version 5.1.9
 *
 * @var \Tribe__Template $this                    [Global] Template object.
 * @var string           $url                     [Global] Script URL.
 * @var string           $client_token            [Global] One time use client Token.
 * @var string           $client_token_expires_in [Global] How much time to when the Token in this script will take to expire.
 * @var string           $attribution_id          [Global] What is our PayPal Attribution ID.
 * @var bool             $must_login              [Global] Whether login is required to buy tickets or not.
 */

if ( $must_login ) {
	return;
}
?>
<script
	class="tec-tc-gateway-paypal-checkout-script"
	src="<?php echo esc_url( $url ); ?>"
	data-partner-attribution-id="<?php echo esc_attr( $attribution_id ); ?>"
	<?php if ( ! empty( $client_token ) ) : ?>
		data-client-token="<?php echo esc_attr( $client_token ); ?>"
	<?php endif; ?>
	<?php if ( ! empty( $client_token_expires_in ) ) : ?>
		data-client-token-expires-in="<?php echo esc_attr( $client_token_expires_in ); ?>"
	<?php endif; ?>
></script>