<?php
/**
 * The Template for displaying the Tickets Commerce PayPal help links (troubleshooting).
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
<div class="tec-tickets__admin-settings-tickets-commerce-paypal-help-link">
	<?php $this->template( 'components/icons/lightbulb' ); ?>
	<a
		href="https://evnt.is/1axw"
		target="_blank"
		rel="noopener noreferrer"
		class="tec-tickets__admin-settings-tickets-commerce-paypal-help-link-url"
	><?php esc_html_e( 'Get troubleshooting help', 'event-tickets' ); ?></a>
</div>
