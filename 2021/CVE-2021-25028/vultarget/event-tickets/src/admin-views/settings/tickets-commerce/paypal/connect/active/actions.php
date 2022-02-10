<?php
/**
 * The Template for displaying the Tickets Commerce PayPal connection details.
 *
 * @version 5.2.0
 *
 * @since 5.2.0
 *
 * @var Tribe__Tickets__Admin__Views                  $this               [Global] Template object.
 * @var string                                        $plugin_url         [Global] The plugin URL.
 * @var TEC\Tickets\Commerce\Gateways\PayPal\Merchant $merchant           [Global] The merchant class.
 * @var TEC\Tickets\Commerce\Gateways\PayPal\Signup   $signup             [Global] The Signup class.
 * @var bool                                          $is_merchant_active    [Global] Whether the merchant is active or not.
 * @var bool                                          $is_merchant_connected [Global] Whether the merchant is connected or not.
 */

if ( empty( $is_merchant_connected ) ) {
	return;
}
?>

<div class="tec-tickets__admin-settings-tickets-commerce-paypal-connected-actions">
	<?php $this->template( 'settings/tickets-commerce/paypal/connect/active/actions/refresh-connection' ); ?>

	<div class="tec-tickets__admin-settings-tickets-commerce-paypal-connected-actions-debug">
		<?php $this->template( 'settings/tickets-commerce/paypal/connect/active/actions/refresh-access-token' ); ?>

		<?php $this->template( 'settings/tickets-commerce/paypal/connect/active/actions/refresh-user-info' ); ?>

		<?php $this->template( 'settings/tickets-commerce/paypal/connect/active/actions/refresh-webhook' ); ?>
	</div>
</div>
