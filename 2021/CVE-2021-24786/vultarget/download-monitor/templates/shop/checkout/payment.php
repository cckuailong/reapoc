<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


/** @var \Never5\DownloadMonitor\Shop\Checkout\PaymentGateway\Manager $pgm Payment Gateway Manager */
$pgm              = \Never5\DownloadMonitor\Shop\Services\Services::get()->service( 'payment_gateway' );
$payment_gateways = $pgm->get_enabled_gateways();
$default_gateway  = download_monitor()->service( 'settings' )->get_option( 'default_gateway' );

if ( ! empty( $payment_gateways ) ) {
	?>
    <ul>
		<?php
		foreach ( $payment_gateways as $gateway ) {
			download_monitor()->service( 'template_handler' )->get_template_part( 'shop/checkout/payment-gateway', '', '', array(
				'cart'            => $cart,
				'gateway'         => $gateway,
				'default_gateway' => $default_gateway
			) );
		}
		?>
    </ul>
	<?php
}