<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * @var string $form_data_str
 * @var Never5\DownloadMonitor\Shop\Cart\Cart $cart
 * @var string $url_cart
 * @var string $url_checkout
 * @var array $field_values
 * @var array $items
 * @var string $subtotal
 * @var string $total
 * @var array $errors
 */

?>
<div class="dlm-checkout">
    <noscript><?php
		download_monitor()->service( 'template_handler' )->get_template_part( 'shop/checkout/error', '', '', array(
			'error' => __( "Your browser does not support JavaScript which our checkout page requires to function properly.", 'download-monitor' )
		) );
		?></noscript>
    <form method="post" action="<?php echo $url_checkout; ?>" id="dlm-form-checkout" <?php echo $form_data_str; ?>>
		<?php
		if ( ! empty( $errors ) ):
			foreach ( $errors as $error ):
				download_monitor()->service( 'template_handler' )->get_template_part( 'shop/checkout/error', '', '', array(
					'error' => $error
				) );
			endforeach;
		endif;
		?>
        <div class="dlm-checkout-billing">
            <h2><?php _e( 'Billing details', 'download-monitor' ); ?></h2>
			<?php dlm_checkout_fields( $field_values ); ?>
        </div>
        <div class="dlm-checkout-order-review">
            <h2><?php _e( 'Your order', 'download-monitor' ); ?></h2>
			<?php
			download_monitor()->service( 'template_handler' )->get_template_part( 'shop/checkout/order-review', '', '', array(
				'cart'         => $cart,
				'url_checkout' => $url_checkout,
				'items'        => $items,
				'subtotal'     => $subtotal,
				'total'        => $total
			) );
			?>

            <div class="dlm-checkout-payment">
				<?php
				download_monitor()->service( 'template_handler' )->get_template_part( 'shop/checkout/payment', '', '', array(
					'cart'         => $cart,
					'url_checkout' => $url_checkout
				) );
				?>
            </div>

            <div class="dlm-checkout-submit">
				<?php
				download_monitor()->service( 'template_handler' )->get_template_part( 'shop/checkout/submit-button', '', '', array(
					'cart'         => $cart,
					'url_checkout' => $url_checkout
				) );
				?>
            </div>
        </div>
    </form>
</div>