<?php
/**
 * The Template for displaying the Tickets Commerce refresh connection action button.
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

$resync_connection_url = Tribe__Settings::instance()->get_url( [ 'tab' => 'payments', 'tc-action' => 'paypal-resync-connection' ] );
?>
<div class="tec-tickets__admin-settings-tickets-commerce-paypal-connected-row">
	<div class="tec-tickets__admin-settings-tickets-commerce-paypal-connected-col1"></div>
	<div class="tec-tickets__admin-settings-tickets-commerce-paypal-connected-col2">
	<a
		href="<?php echo esc_url( $resync_connection_url ); ?>"
		class="tec-tickets__admin-settings-tickets-commerce-paypal-connected-resync-button"
	>
		<?php tribe( 'tickets.editor.template' )->template( 'v2/components/icons/reset', [ 'classes' => [ 'tec-tickets__admin-settings-tickets-commerce-paypal-connected-resync-button-icon' ] ] ); ?>
		<?php esc_html_e( 'Resync payment connection', 'event-tickets' ); ?>
	</a>
	</div>
</div>

