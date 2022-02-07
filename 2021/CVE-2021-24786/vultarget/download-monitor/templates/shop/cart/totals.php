<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/** @var Never5\DownloadMonitor\Shop\Cart\Cart $cart */
?>
<table cellspacing="0" cellpadding="0" border="0">
    <tbody>
    <tr>
        <th><?php _e( 'Subtotal', 'download-monitor' ); ?></th>
        <td><?php echo dlm_format_money( $cart->get_subtotal() ); ?></td>
    </tr>
	<?php
	/**
	 * @todo [TAX] Implement taxes
	 */
	?>
	<?php
	/**
	 * @todo [COUPONS] Implement coupons
	 */
	?>
    <tr class="dlm-totals-last-row">
        <th><?php _e( 'Total', 'download-monitor' ); ?></th>
        <td><?php echo dlm_format_money( $cart->get_total() ); ?></td>
    </tr>
    </tbody>
</table>
