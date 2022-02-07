<?php
/**
 * Add to cart button
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/** @var \Never5\DownloadMonitor\Shop\Product\Product $product */
/** @var string $atc_url */
?>
<p><a class="aligncenter download-button" href="<?php echo $atc_url; ?>" rel="nofollow">
		<?php printf( __( 'Purchase &ldquo;%s&rdquo;', 'download-monitor' ), $product->get_title() ); ?>
        <small><?php echo dlm_format_money( $product->get_price() ); ?>
            - <?php echo esc_html( $product->get_excerpt() ); ?></small>
    </a></p>