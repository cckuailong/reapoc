<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * @var Never5\DownloadMonitor\Shop\Cart\Cart $cart
 * @var string $url_cart
 * @var string $url_checkout
 */

?>
<div class="dlm-cart">
    <div class="dlm-cart-table-items">
        <table cellpadding="0" cellspacing="0" border="0">
            <thead>
            <tr>
                <th>&nbsp;</th>
                <th><?php _e( 'Name', 'download-monitor' ); ?></th>
                <th><?php _e( 'Price', 'download-monitor' ); ?></th>
                <th><?php _e( 'Quantity', 'download-monitor' ); ?></th>
                <th><?php _e( 'Total', 'download-monitor' ); ?></th>
            </tr>
            </thead>
            <tbody>
			<?php
			$items = $cart->get_items();
			if ( ! empty( $items ) ) {
				/** @var \Never5\DownloadMonitor\Shop\Cart\Item $item */
				foreach ( $items as $item ) {
					download_monitor()->service( 'template_handler' )->get_template_part( 'shop/cart/item', '', '', array(
						'item'     => $item,
						'url_cart' => $url_cart
					) );
				}
			}
			?>
            </tbody>
        </table>
    </div>
    <div class="dlm-cart-bottom">
        <div class="dlm-cart-coupons">

        </div>
        <div class="dlm-cart-bottom-right">
            <div class="dlm-cart-totals">
                <h2><?php _e( 'Cart Totals', 'download-monitor' ); ?></h2>
				<?php
				download_monitor()->service( 'template_handler' )->get_template_part( 'shop/cart/totals', '', '', array(
					'cart' => $cart
				) );
				?>
            </div>
            <div class="dlm-proceed-to-checkout">
                <a href="<?php echo $url_checkout; ?>"
                   class="dlm-button-checkout"><?php _e( 'Proceed to checkout', 'download-monitor' ); ?> »</a>
            </div>
        </div>
    </div>

</div>