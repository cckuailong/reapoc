<?php
/**
 * The Template for displaying the Tickets Commerce PayPal logo and features.
 *
 * @version 5.1.10
 *
 * @since   5.1.10
 *
 * @var Tribe__Tickets__Admin__Views                  $this                  [Global] Template object.
 * @var string                                        $plugin_url            [Global] The plugin URL.
 * @var TEC\Tickets\Commerce\Gateways\PayPal\Merchant $merchant              [Global] The merchant class.
 * @var TEC\Tickets\Commerce\Gateways\PayPal\Signup   $signup                [Global] The Signup class.
 * @var bool                                          $is_merchant_active    [Global] Whether the merchant is active or not.
 * @var bool                                          $is_merchant_connected [Global] Whether the merchant is connected or not.
 */

?>

<div class="tec-tickets__admin-settings-tickets-commerce-paypal-logo">

	<?php $this->template( 'settings/tickets-commerce/paypal/connect/logo/image' ); ?>

	<?php $this->template( 'settings/tickets-commerce/paypal/connect/logo/features' ); ?>

</div>
