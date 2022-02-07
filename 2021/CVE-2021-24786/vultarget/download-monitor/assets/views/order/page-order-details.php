<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/** @var \Never5\DownloadMonitor\Shop\Order\Order $order */
/** @var array $customer */


$items = $order->get_items();
?>
<div class="wrap dlm-order-details">

    <h1><?php printf( __( 'Order Details #%s', 'download-monitor' ), $order->get_id() ); ?></h1>

    <div class="dlm-order-details-main">

        <div class="dlm-order-details-block dlm-order-details-order-items">
            <h2 class="dlm-order-details-block-title"><span><?php _e( 'Order Items', 'download-monitor' ); ?></span>
            </h2>
            <div class="dlm-order-details-block-inside">
                <table cellspacing="0" cellpadding="0" border="0" class="dlm-order-details-data-table">
                    <thead>
                    <tr>
                        <th><?php _e( "Product", 'download-monitor' ); ?></th>
                        <th><?php _e( "Price", 'download-monitor' ); ?></th>
                        <th><?php _e( "QTY", 'download-monitor' ); ?></th>
                        <th class="dlm-order-details-order-items-item-total"><?php _e( "Total", 'download-monitor' ); ?></th>
                    </tr>
                    </thead>
                    <tbody>
					<?php if ( ! empty( $items ) ) : ?>
						<?php foreach ( $items as $item ) : ?>
                            <tr>
                                <td><?php echo esc_html( $item->get_label() ); ?></td>
                                <td><?php echo esc_html( dlm_format_money( $item->get_subtotal(), array( 'currency' => $order->get_currency() ) ) ); ?></td>
                                <td><?php echo esc_html( $item->get_qty() ); ?></td>
                                <td class="dlm-order-details-order-items-item-total"><?php echo esc_html( dlm_format_money( $item->get_total(), array( 'currency' => $order->get_currency() ) ) ); ?></td>
                            </tr>
						<?php endforeach; ?>
					<?php endif; ?>
                    </tbody>
                </table>
                <table cellspacing="0" cellpadding="0" border="0" class="dlm-order-details-overview">
                    <tbody>
                    <tr>
                        <th><?php _e( "Total", 'download-monitor' ); ?>:</th>
                        <td><?php echo dlm_format_money( $order->get_total(), array( 'currency' => $order->get_currency() ) ); ?></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="dlm-order-details-block">
            <h2 class="dlm-order-details-block-title">
                <span><?php _e( 'Transactions', 'download-monitor' ); ?></span>
            </h2>
            <div class="dlm-order-details-block-inside">
				<?php
				$transactions = $order->get_transactions();
				if ( ! empty( $transactions ) ) :
					?>
                    <table cellspacing="0" cellpadding="0" border="0" class="dlm-order-details-data-table">
                        <thead>
                        <tr>
                            <th><?php _e( "ID", 'download-monitor' ); ?></th>
                            <th><?php _e( "Date", 'download-monitor' ); ?></th>
                            <th><?php _e( "Status", 'download-monitor' ); ?></th>
                            <th><?php _e( "Amount", 'download-monitor' ); ?></th>
                            <th><?php _e( "Processor", 'download-monitor' ); ?></th>
                            <th class="dlm-order-transaction-processor-id"><?php _e( "Processor ID", 'download-monitor' ); ?></th>
                        </tr>
                        </thead>
                        <tbody>
						<?php
                        // replace long date format vars for short ones
						$date_short_format = str_replace( "F", "M", str_replace( "Y", "y", get_option( 'date_format' ) ) );

						foreach ( $transactions as $transaction ) :

							if ( $transaction->get_date_modified() !== null ) {
								$date_obj = $transaction->get_date_modified();
							} else {
								$date_obj = $transaction->get_date_created();
							}

							$date = date_i18n( $date_short_format, $date_obj->format( 'U' ) ) . " " . $date_obj->format( 'H:i:s' );
							?>
                            <tr>
                                <td><?php echo esc_html( $transaction->get_id() ); ?></td>
                                <td><?php echo esc_html( $date ); ?></td>
                                <td><?php echo esc_html( $transaction->get_status()->get_label() ); ?></td>
                                <td><?php echo esc_html( dlm_format_money( $transaction->get_amount(), array( 'currency' => $order->get_currency() ) ) ); ?></td>
                                <td><?php echo esc_html( $transaction->get_processor_nice_name() ); ?></td>
                                <td class="dlm-order-transaction-processor-id"><?php echo esc_html( $transaction->get_processor_transaction_id() ); ?></td>
                            </tr>
							<?php
						endforeach;
						?>
                        </tbody>
                    </table>
					<?php
				else: ?>
                    <p><?php _e( "No transactions found", 'download-monitor' ); ?></p>
				<?php endif; ?>
            </div>
        </div>

    </div>

    <div class="dlm-order-details-side">

        <div class="dlm-order-details-block dlm-order-details-customer">
            <h2 class="dlm-order-details-block-title"><span><?php _e( 'Customer', 'download-monitor' ); ?></span></h2>
            <div class="dlm-order-details-block-inside">
				<?php
				if ( ! empty( $customer['email'] ) ) {
					echo "<img src='https://www.gravatar.com/avatar/" . md5( $customer['email'] ) . "?s=95&d=mp' alt='" . esc_attr( $customer['name'] ) . "' class='dlm-order-details-customer-image' />";
				}
				?>
                <ul>
					<?php
					foreach ( $customer as $key => $data ) {
						if ( ! empty( $data ) ) {

							if ( "email" === $key ) {
								echo "<li><a href='mailto:" . esc_attr( $data ) . "'>" . esc_html( $data ) . "</a></li>";
								continue;
							}

							echo "<li>" . esc_html( $data ) . "</li>";
						}
					}
					?>
                </ul>
            </div>
        </div>

        <div class="dlm-order-details-block">
            <h2 class="dlm-order-details-block-title"><span><?php _e( 'Order Details', 'download-monitor' ); ?></span>
            </h2>
            <div class="dlm-order-details-block-inside">
                <ul>
                    <li>
                        <label><?php _e( "Order Status", 'download-monitor' ); ?>:</label>
                        <select name="dlm_new_order_status" class="dlm-order-details-current-state"
                                id="dlm-order-details-current-state">
							<?php
							if ( ! empty( $statuses ) ) :
								foreach ( $statuses as $status ):
									echo "<option value='" . $status->get_key() . "' " . selected( $status->get_key(), $order->get_status()->get_key(), false ) . ">" . $status->get_label() . "</option>" . PHP_EOL;
								endforeach;
							endif;
							?>
                        </select>
                        <button class="button button-primary button-large"
                                id="dlm-order-details-button-change-state"><?php _e( "Change", 'download-montior' ); ?></button>
                    </li>
                    <li>
                        <label><?php _e( "Date created", 'download-monitor' ); ?>:</label>
                        <p><?php echo esc_html( date_i18n( get_option( 'date_format' ), $order->get_date_created()->format( 'U' ) ) . " " . $order->get_date_created()->format( 'H:i:s' ) ); ?></p>
                    </li>
                    <li>
                        <label><?php _e( "IP Address", 'download-monitor' ); ?>:</label>
                        <p><?php echo esc_html( $order->get_customer()->get_ip_address() ); ?></p>
                    </li>
					<?php if ( ! empty( $processors ) ) : ?>
                        <li>
                            <label><?php _e( "Payment Method", 'download-monitor' ); ?>:</label>
                            <p><?php echo esc_html( $processors[ count( $processors ) - 1 ] ); ?></p>
                        </li>
					<?php endif; ?>
            </div>
        </div>

    </div>

</div>