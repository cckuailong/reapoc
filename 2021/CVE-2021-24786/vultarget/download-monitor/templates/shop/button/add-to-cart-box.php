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
<aside class="download-box">

	<?php echo $product->get_image(); ?>

    <div class="download-count"><?php echo dlm_format_money( $product->get_price() ); ?></div>

    <div class="download-box-content">

        <h1><?php echo $product->get_title(); ?></h1>

		<p><?php echo $product->get_excerpt(); ?></p>

        <a class="download-button" title="<?php _e( 'Purchase Now', 'download-monitor' ); ?>" href="<?php echo $atc_url; ?>"
           rel="nofollow">
			<?php _e( 'Purchase Now', 'download-monitor' ); ?>
        </a>

    </div>
</aside>