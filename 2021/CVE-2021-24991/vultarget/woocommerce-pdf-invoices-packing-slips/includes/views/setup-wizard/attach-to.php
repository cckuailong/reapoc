<?php defined( 'ABSPATH' ) or exit; ?>
<div class="wpo-step-description">
	<h2><?php _e( 'Attach too...', 'woocommerce-pdf-invoices-packing-slips' ); ?></h2>
	<p><?php _e( 'Select to which emails you would like to attach your invoice.', 'woocommerce-pdf-invoices-packing-slips' ); ?></p>
</div>
<div class="wpo-setup-input">
	<table>
	<?php 
	$current_settings = get_option( 'wpo_wcpdf_documents_settings_invoice', array() );
	// echo "<pre>".var_dump($current_settings)."</pre>";
	// load invoice to reuse method to get wc emails
	$invoice = wcpdf_get_invoice( null );
	$wc_emails = $invoice->get_wc_emails();
	foreach ($wc_emails as $email_id => $name) {
		if (!empty($current_settings['attach_to_email_ids'][$email_id])) {
			$checked = 'checked';
		} else {
			$checked = '';
		}
		printf('<tr>
					<th>
						<input type="hidden" value="" name="wcpdf_settings[wpo_wcpdf_documents_settings_invoice][attach_to_email_ids][%1$s]">
						<input type="checkbox" %3$s name="wcpdf_settings[wpo_wcpdf_documents_settings_invoice][attach_to_email_ids][%1$s]" value="1">
					</th>
					<td>
						<span class="checkbox">%2$s</span><br>
					</td>
				</tr>', $email_id, $name, $checked);

	}
	?>
	</table>
</div>
