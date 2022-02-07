<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/** @var \Never5\DownloadMonitor\Shop\Checkout\PaymentGateway\PaymentGateway $gateway */
?>
<li>
    <label for="dlm_gateway_<?php echo $gateway->get_id(); ?>">
        <input type="radio" name="dlm_gateway" id="dlm_gateway_<?php echo $gateway->get_id(); ?>"
               value="<?php echo $gateway->get_id(); ?>" <?php checked( $default_gateway, $gateway->get_id() ); ?>/>
		<?php echo $gateway->get_title(); ?>
    </label>
    <div class="dlm_gateway_details">
		<?php
		$description = $gateway->get_description();
		if ( ! empty( $description ) ) {
			printf( "<p>%s</p>", esc_html( $description ) );
		}
		?>
    </div>
</li>