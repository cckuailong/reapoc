<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/** @var string $error */
?>

<div class="dlm-checkout-error">
    <img src="<?php echo download_monitor()->get_plugin_url(); ?>/assets/images/shop/icon-error.svg"
         alt="<?php _e( "Checkout error", 'download-monitor' ); ?>" class="dlm-checkout-error-icon">
    <p><?php echo esc_html( $error ); ?></p>
</div>