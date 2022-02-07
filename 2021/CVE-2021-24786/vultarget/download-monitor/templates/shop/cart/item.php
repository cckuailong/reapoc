<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/** @var Never5\DownloadMonitor\Shop\Cart\Item\Item $item */
?>
<tr>
    <td><a href="<?php echo add_query_arg( array( 'dlm-remove-from-cart' => $item->get_product_id() ), $url_cart ); ?>"
           class="dlm-cart-remove-item"
           aria-label="<?php _e( 'Remove this item from your cart', 'download-monitor' ); ?>">x</a></td>
    <td><?php echo $item->get_label(); ?></td>
    <td><?php echo dlm_format_money( $item->get_subtotal() ); ?></td>
    <td><?php echo $item->get_qty(); ?></td>
    <td><?php echo dlm_format_money( $item->get_total() ); ?></td>
</tr>