<?php
/**
 * The Template for displaying the Tickets Commerce PayPal connection details.
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

use TEC\Tickets\Commerce\Gateways\PayPal\Webhooks;
use TEC\Tickets\Commerce\Gateways\PayPal\Webhooks\Events;

if ( empty( $is_merchant_connected ) ) {
	return;
}

$webhooks_events    = tribe( Events::class );
$webhook_data       = tribe( Webhooks::class )->get_settings();
$event_types_active = [];
if ( ! empty( $webhook_data['event_types'] ) ) {
	$event_types_active = wp_list_pluck( $webhook_data['event_types'], 'name' );
}

?>
<div class="tec-tickets__admin-settings-tickets-commerce-paypal-connected-row">
	<div class="tec-tickets__admin-settings-tickets-commerce-paypal-connected-col1">
		<?php esc_html_e( 'Webhooks:', 'event-tickets' ); ?>
	</div>
	<div class="tec-tickets__admin-settings-tickets-commerce-paypal-connected-col2">
		<?php
		foreach ( $webhooks_events->get_registered_events() as $event_name ) :
			$webhook_label = $webhooks_events->get_webhook_label( $event_name );
			$is_active = in_array( $event_name, $event_types_active, true );
			$classes = [
				'tec-tickets__admin-settings-tickets-commerce-paypal-connected-webhook',
				'tec-tickets__admin-settings-tickets-commerce-paypal-connected-webhook--active' => $is_active,
			]
			?>
			<div <?php tribe_classes( $classes ); ?>>
				<span class="tec-tickets__admin-settings-tickets-commerce-paypal-connected-webhook-name">
					<?php echo esc_html( $webhook_label ); ?>
				</span>
				<span class="tec-tickets__admin-settings-tickets-commerce-paypal-connected-webhook-error">
					<?php esc_html_e( 'payment connection error', 'event-tickets' ); ?>
				</span>
			</div>
		<?php endforeach; ?>
	</div>
</div>
