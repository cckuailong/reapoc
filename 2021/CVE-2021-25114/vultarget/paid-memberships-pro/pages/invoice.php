<?php 
/**
 * Template: Invoice
 *
 * See documentation for how to override the PMPro templates.
 * @link https://www.paidmembershipspro.com/documentation/templates/
 *
 * @version 2.0
 *
 * @author Paid Memberships Pro
 */
?>
<div class="<?php echo pmpro_get_element_class( 'pmpro_invoice_wrap' ); ?>">
	<?php
	global $wpdb, $pmpro_invoice, $pmpro_msg, $pmpro_msgt, $current_user;

	if($pmpro_msg)
	{
	?>
	<div class="<?php echo pmpro_get_element_class( 'pmpro_message ' . $pmpro_msgt, $pmpro_msgt ); ?>"><?php echo $pmpro_msg?></div>
	<?php
	}
?>

<?php
	if($pmpro_invoice)
	{
		?>
		<?php
			$pmpro_invoice->getUser();
			$pmpro_invoice->getMembershipLevel();
		?>
		<h3><?php printf(__('Invoice #%s on %s', 'paid-memberships-pro' ), $pmpro_invoice->code, date_i18n(get_option('date_format'), $pmpro_invoice->getTimestamp()));?></h3>
		<a class="<?php echo pmpro_get_element_class( 'pmpro_a-print' ); ?>" href="javascript:window.print()"><?php _e('Print', 'paid-memberships-pro' ); ?></a>
		<ul>
			<?php do_action("pmpro_invoice_bullets_top", $pmpro_invoice); ?>
			<li><strong><?php _e('Account', 'paid-memberships-pro' );?>:</strong> <?php echo $pmpro_invoice->user->display_name?> (<?php echo $pmpro_invoice->user->user_email?>)</li>
			<li><strong><?php _e('Membership Level', 'paid-memberships-pro' );?>:</strong> <?php echo $pmpro_invoice->membership_level->name?></li>
			<?php if ( ! empty( $pmpro_invoice->status ) ) { ?>
				<li><strong><?php _e('Status', 'paid-memberships-pro' ); ?>:</strong>
				<?php
					if ( in_array( $pmpro_invoice->status, array( '', 'success', 'cancelled' ) ) ) {
						$display_status = __( 'Paid', 'paid-memberships-pro' );
					} else {
						$display_status = ucwords( $pmpro_invoice->status );
					}
					esc_html_e( $display_status );
				?>
				</li>
			<?php } ?>
			<?php if($pmpro_invoice->getDiscountCode()) { ?>
				<li><strong><?php _e('Discount Code', 'paid-memberships-pro' );?>:</strong> <?php echo $pmpro_invoice->discount_code->code?></li>
			<?php } ?>
			<?php do_action("pmpro_invoice_bullets_bottom", $pmpro_invoice); ?>
		</ul>

		<?php
			// Check instructions
			if ( $pmpro_invoice->gateway == "check" && ! pmpro_isLevelFree( $pmpro_invoice->membership_level ) ) {
				echo '<div class="' . pmpro_get_element_class( 'pmpro_payment_instructions' ) . '">' . wpautop( wp_unslash( pmpro_getOption("instructions") ) ) . '</div>';
			}
		?>

		<hr />
		<div class="<?php echo pmpro_get_element_class( 'pmpro_invoice_details' ); ?>">
			<?php if(!empty($pmpro_invoice->billing->street)) { ?>
				<div class="<?php echo pmpro_get_element_class( 'pmpro_invoice-billing-address' ); ?>">
					<strong><?php _e('Billing Address', 'paid-memberships-pro' );?></strong>
					<p>
						<span class="<?php echo pmpro_get_element_class( 'pmpro_invoice-field-billing_name' ); ?>"><?php echo $pmpro_invoice->billing->name; ?></span>
						<span class="<?php echo pmpro_get_element_class( 'pmpro_invoice-field-billing_street' ); ?>"><?php echo $pmpro_invoice->billing->street; ?></span>
						<?php if($pmpro_invoice->billing->city && $pmpro_invoice->billing->state) { ?>
							<span class="<?php echo pmpro_get_element_class( 'pmpro_invoice-field-billing_city' ); ?>"><?php echo $pmpro_invoice->billing->city; ?></span>
							<span class="<?php echo pmpro_get_element_class( 'pmpro_invoice-field-billing_state' ); ?>"><?php echo $pmpro_invoice->billing->state; ?></span>
							<span class="<?php echo pmpro_get_element_class( 'pmpro_invoice-field-billing_zip' ); ?>"><?php echo $pmpro_invoice->billing->zip; ?></span>
							<span class="<?php echo pmpro_get_element_class( 'pmpro_invoice-field-billing_country' ); ?>"><?php echo $pmpro_invoice->billing->country; ?></span>
						<?php } ?>
						<span class="<?php echo pmpro_get_element_class( 'pmpro_invoice-field-billing_phone' ); ?>"><?php echo formatPhone($pmpro_invoice->billing->phone); ?></span>
					</p>
				</div> <!-- end pmpro_invoice-billing-address -->
			<?php } ?>

			<?php if ( ! empty( $pmpro_invoice->accountnumber ) || ! empty( $pmpro_invoice->payment_type ) ) { ?>
				<div class="<?php echo pmpro_get_element_class( 'pmpro_invoice-payment-method' ); ?>">
					<strong><?php _e('Payment Method', 'paid-memberships-pro' );?></strong>
					<?php if($pmpro_invoice->accountnumber) { ?>
						<p><?php echo ucwords( $pmpro_invoice->cardtype ); ?> <?php _e('ending in', 'paid-memberships-pro' );?> <?php echo last4($pmpro_invoice->accountnumber)?>
						<br />
						<?php _e('Expiration', 'paid-memberships-pro' );?>: <?php echo $pmpro_invoice->expirationmonth?>/<?php echo $pmpro_invoice->expirationyear?></p>
					<?php } else { ?>
						<p><?php echo $pmpro_invoice->payment_type; ?></p>
					<?php } ?>
				</div> <!-- end pmpro_invoice-payment-method -->
			<?php } ?>

			<div class="<?php echo pmpro_get_element_class( 'pmpro_invoice-total' ); ?>">
				<strong><?php _e('Total Billed', 'paid-memberships-pro' );?></strong>
				<p>
					<?php
						if ( (float)$pmpro_invoice->total > 0 ) {
							echo pmpro_get_price_parts( $pmpro_invoice, 'span' );
						} else {
							echo pmpro_escape_price( pmpro_formatPrice(0) );
						}
					?>
				</p>
			</div> <!-- end pmpro_invoice-total -->
		</div> <!-- end pmpro_invoice_details -->
		<hr />
		<?php
	}
	else
	{
		//Show all invoices for user if no invoice ID is passed
		$invoices = $wpdb->get_results("SELECT o.*, UNIX_TIMESTAMP(CONVERT_TZ(o.timestamp, '+00:00', @@global.time_zone)) as timestamp, l.name as membership_level_name FROM $wpdb->pmpro_membership_orders o LEFT JOIN $wpdb->pmpro_membership_levels l ON o.membership_id = l.id WHERE o.user_id = '$current_user->ID' AND o.status NOT IN('review', 'token', 'error') ORDER BY timestamp DESC");
		if($invoices)
		{
			?>
			<table id="pmpro_invoices_table" class="<?php echo pmpro_get_element_class( 'pmpro_table pmpro_invoice', 'pmpro_invoices_table' ); ?>" width="100%" cellpadding="0" cellspacing="0" border="0">
			<thead>
				<tr>
					<th><?php _e('Date', 'paid-memberships-pro' ); ?></th>
					<th><?php _e('Invoice #', 'paid-memberships-pro' ); ?></th>
					<th><?php _e('Level', 'paid-memberships-pro' ); ?></th>
					<th><?php _e('Total Billed', 'paid-memberships-pro' ); ?></th>
				</tr>
			</thead>
			<tbody>
			<?php
				foreach($invoices as $invoice)
				{
					?>
					<tr>
						<td><a href="<?php echo pmpro_url("invoice", "?invoice=" . $invoice->code)?>"><?php echo date_i18n( get_option("date_format"), strtotime( get_date_from_gmt( date( 'Y-m-d H:i:s', $invoice->timestamp ) ) ) )?></a></td>
						<td><a href="<?php echo pmpro_url("invoice", "?invoice=" . $invoice->code)?>"><?php echo $invoice->code; ?></a></td>
						<td><?php echo $invoice->membership_level_name;?></td>
						<td><?php echo pmpro_formatPrice($invoice->total);?></td>
					</tr>
					<?php
				}
			?>
			</tbody>
			</table>
			<?php
		}
		else
		{
			?>
			<p><?php _e('No invoices found.', 'paid-memberships-pro' );?></p>
			<?php
		}
	}
?>
<p class="<?php echo pmpro_get_element_class( 'pmpro_actions_nav' ); ?>">
	<span class="<?php echo pmpro_get_element_class( 'pmpro_actions_nav-right' ); ?>"><a href="<?php echo pmpro_url("account")?>"><?php _e('View Your Membership Account &rarr;', 'paid-memberships-pro' );?></a></span>
	<?php if ( $pmpro_invoice ) { ?>
		<span class="<?php echo pmpro_get_element_class( 'pmpro_actions_nav-left' ); ?>"><a href="<?php echo pmpro_url("invoice")?>"><?php _e('&larr; View All Invoices', 'paid-memberships-pro' );?></a></span>
	<?php } ?>
</p> <!-- end pmpro_actions_nav -->
</div> <!-- end pmpro_invoice_wrap -->
