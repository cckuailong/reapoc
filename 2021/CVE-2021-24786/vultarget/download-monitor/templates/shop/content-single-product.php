<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/** @var \Never5\DownloadMonitor\Shop\Product\Product $product */

/**
 * dlm_before_single_product hook
 */
do_action( 'dlm_before_single_product', $product );
?>
    <div class="dlm-product">
		<?php if ( $product->get_content() != "" ) : ?>
            <p><?php echo esc_html( $product->get_content() ); ?></p>
			<?php echo do_shortcode( sprintf( '[dlm_buy id="%s"]', intval( $product->get_id() ) ) ); ?>
		<?php endif; ?>
    </div>
<?php do_action( 'dlm_after_single_product', $product ); ?>