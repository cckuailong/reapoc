<?php
/**
 * The Template for displaying the Tickets Commerce PayPal help links (configuring).
 *
 * @version 5.2.0
 *
 * @since   5.2.0
 *
 * @var Tribe__Tickets__Admin__Views                  $this                  [Global] Template object.
 * @var string                                        $plugin_url            [Global] The plugin URL.
 * @var TEC\Tickets\Commerce\Gateways\PayPal\Merchant $merchant              [Global] The merchant class.
 * @var TEC\Tickets\Commerce\Gateways\PayPal\Signup   $signup                [Global] The Signup class.
 * @var bool                                          $is_merchant_active    [Global] Whether the merchant is active or not.
 * @var bool                                          $is_merchant_connected [Global] Whether the merchant is connected or not.
 */

if ( ! empty( $is_merchant_connected ) ) {
	return;
}

?>
<div class="tec-tickets__admin-settings-tickets-commerce-paypal-help-link">
	<?php $this->template( 'components/icons/lightbulb' ); ?>
	<a
		href="https://evnt.is/1axt"
		target="_blank"
		rel="noopener noreferrer"
		class="tec-tickets__admin-settings-tickets-commerce-paypal-help-link-url"
	><?php esc_html_e( 'Learn more about configuring PayPal payments', 'event-tickets' ); ?></a>
</div>
