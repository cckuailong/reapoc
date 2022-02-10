<?php
/**
 * Template for Print Invoices
 *
 * @since 1.8.6
 */
?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<style>
		.main, .header {
			display: block;
		}
		.right {
			display: inline-block;
			float: right;
		}
		.alignright {
			text-align: right;
		}
		.aligncenter {
			text-align: center;
		}
		.invoice, .invoice tr, .invoice th, .invoice td {
			border: 1px solid;
			border-collapse: collapse;
			padding: 4px;
		}
		.invoice {
			width: 100%;
		}
		@media screen {
			body {
				max-width: 50%;
				margin: 0 auto;
			}
		}
	</style>
</head>
<body>
	<header class="header">
		<div>
			<h2><?php bloginfo( 'sitename' ); ?></h2>
		</div>
		<div class="right">
			<table>
				<tr>
					<td><?php echo __('Invoice #: ', 'paid-memberships-pro' ) . '&nbsp;' . $order->code; ?></td>
				</tr>
				<tr>
					<td>
						<?php echo __( 'Date:', 'paid-memberships-pro' ) . '&nbsp;' . date_i18n( get_option( 'date_format' ), $order->getTimestamp() ); ?>
					</td>
				</tr>
			</table>
		</div>
	</header>
	<main class="main">
		<p>
			<?php echo pmpro_formatAddress(
				$order->billing->name,
				$order->billing->street,
				'',
				$order->billing->city,
				$order->billing->state,
				$order->billing->zip,
				$order->billing->country,
				$order->billing->phone
			); ?>
		</p>
		<table class="invoice" style="border-width:0px;border-collapse:collapse;">
			<tr>
				<th><?php _e('ID', 'paid-memberships-pro' ); ?></th>
				<th><?php _e('Item', 'paid-memberships-pro' ); ?></th>
				<th><?php _e('Price', 'paid-memberships-pro' ); ?></th>
			</tr>
			<tr>
				<td class="aligncenter"><?php echo $level->id; ?></td>
				<td><?php echo $level->name; ?></td>
				<td class="alignright"><?php echo pmpro_escape_price( pmpro_formatPrice( $order->subtotal ) ); ?></td>
			</tr>
			<?php
				if ( (float)$order->total > 0 ) {
					$pmpro_price_parts = pmpro_get_price_parts( $order, 'array' );
					foreach ( $pmpro_price_parts as $pmpro_price_part ) { ?>
						<tr style="border-width:1px;border-style:solid;border-collapse:collapse;">
							<th colspan="2" style="text-align:right;border-width:1px;border-style:solid;border-collapse:collapse;">
								<?php esc_html_e( $pmpro_price_part['label'] ); ?>
							</th>
							<td style="text-align:right;border-width:1px;border-style:solid;border-collapse:collapse;">
								<?php esc_html_e( $pmpro_price_part['value'] ); ?>
							</td>
						</tr>
						<?php
					}
				}
			?>
		</table>
	</main>
</body>
</html>
