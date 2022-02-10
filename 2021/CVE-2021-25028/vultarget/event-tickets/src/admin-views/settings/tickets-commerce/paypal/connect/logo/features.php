<?php
/**
 * The Template for displaying the Tickets Commerce PayPal features.
 *
 * @version 5.1.10
 *
 * @since 5.1.10
 *
 * @var Tribe__Tickets__Admin__Views                  $this               [Global] Template object.
 * @var string                                        $plugin_url         [Global] The plugin URL.
 * @var TEC\Tickets\Commerce\Gateways\PayPal\Merchant $merchant           [Global] The merchant class.
 * @var TEC\Tickets\Commerce\Gateways\PayPal\Signup   $signup             [Global] The Signup class.
 * @var bool                                          $is_merchant_active [Global] Whether the merchant is active or not.
 */

?>
<ul>
	<li>
		<?php esc_html_e( 'Credit and debit card payments', 'event-tickets' ); ?>
	</li>
	<li>
		<?php esc_html_e( 'Easy no-API key connection', 'event-tickets' ); ?>
	</li>
	<li>
		<?php esc_html_e( 'Accept payments from around the world', 'event-tickets' ); ?>
	</li>
	<li>
		<?php esc_html_e( 'Supports 3D Secure payments', 'event-tickets' ); ?>
	</li>
</ul>
