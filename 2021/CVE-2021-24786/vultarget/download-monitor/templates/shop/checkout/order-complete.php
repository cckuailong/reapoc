<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/** @var \Never5\DownloadMonitor\Shop\Order\Order $order */
?>
<div class="dlm-checkout dlm-checkout-complete">
    <p><?php _e( 'Thank you for your order. Please find your order details below.', 'download-monitor' ); ?></p>

	<?php
	if ( $order != null ) :
		?>

		<?php
		/**
		 * Order details table
		 */
		?>
        <div class="dlm-checkout-complete-order-details">
            <h2><?php _e( "Order Details", 'download-monitor' ); ?></h2>
            <table cellpadding="0" cellspacing="0" border="0">
                <tbody>
                <tr>
                    <th><?php _e( "Order ID", 'download-monitor' ); ?></th>
                    <td><?php echo $order->get_id(); ?></td>
                </tr>
                <tr>
                    <th><?php _e( "Order Status", 'download-monitor' ); ?></th>
                    <td><?php echo $order->get_status()->get_label(); ?></td>
                </tr>
                <tr>
                    <th><?php _e( "Order Date", 'download-monitor' ); ?></th>
                    <td><?php echo $order->get_date_created()->format( 'Y-h-d H:i:s' ); ?></td>
                </tr>
                </tbody>
            </table>
        </div>

		<?php
		/**
		 * Downloadable files table
		 */

		if ( $order->get_status()->get_key() === 'completed' ) :

			?>
            <div class="dlm-checkout-complete-files">
                <h2>Your Products</h2>
				<?php
				$order_items = $order->get_items();

				if ( count( $order_items ) > 0 ) : ?>

					<?php foreach ( $order_items as $order_item ) : ?>

						<?php
						try {
							$product = \Never5\DownloadMonitor\Shop\Services\Services::get()->service( 'product_repository' )->retrieve_single( $order_item->get_product_id() );
						} catch ( \Exception $exception ) {
							continue;
						}

						?>

                        <h3><?php echo esc_html( $product->get_title() ); ?></h3>
						<?php
						$downloads = $product->get_downloads();
						if ( ! empty( $downloads ) ) :
							?>
                            <table cellpadding="0" cellspacing="0" border="0">
                                <thead>
                                <tr>
                                    <th><?php _e( "Download name", 'download-monitor' ); ?></th>
                                    <th><?php _e( "Download version", 'download-monitor' ); ?></th>
                                    <th>&nbsp;</th>
                                </tr>
                                </thead>
                                <tbody>
								<?php foreach ( $downloads as $download ) : ?>
									<?php
									$download_title       = "-";
									$version_label        = "-";
									$download_button_html = __( 'Download is no longer available', 'download-monitor' );

									if ( $download->exists() ) {
										$download_title       = $download->get_title();
										$version_label        = $download->get_version()->get_version();
										$download_button_html = "<a href='" . $product->get_secure_download_link( $order, $download ) . "' class='dlm-checkout-download-button'>" . __( 'Download File', 'download-monitor' ) . "</a>";
									}

									?>
                                    <tr>
                                        <td><?php echo $download_title; ?></td>
                                        <td><?php echo $version_label; ?></td>
                                        <td><?php echo $download_button_html; ?></td>
                                    </tr>
								<?php endforeach; ?>
                                </tbody>
                            </table>
						<?php endif; ?>
					<?php endforeach; ?>


				<?php else: ?>
                    <p>No items found.</p>
				<?php endif; ?>
            </div>

		<?php endif; ?>

	<?php endif; ?>
</div>


