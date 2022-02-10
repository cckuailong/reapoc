<?php
/**
 * The Template for displaying the Tickets Commerce PayPal help links.
 *
 * @version 5.2.0
 *
 * @since 5.2.0
 *
 * @var Tribe__Tickets__Admin__Views                  $this               [Global] Template object.
 * @var string                                        $plugin_url         [Global] The plugin URL.
 * @var TEC\Tickets\Commerce\Gateways\PayPal\Merchant $merchant           [Global] The merchant class.
 * @var TEC\Tickets\Commerce\Gateways\PayPal\Signup   $signup             [Global] The Signup class.
 * @var bool                                          $is_merchant_active [Global] Whether the merchant is active or not.
 */

?>

<div class="tec-tickets__admin-settings-tickets-commerce-paypal-help-links">

	<?php $this->template( 'settings/tickets-commerce/paypal/connect/help-links/configuring' ); ?>

	<?php $this->template( 'settings/tickets-commerce/paypal/connect/help-links/troubleshooting' ); ?>

</div>
