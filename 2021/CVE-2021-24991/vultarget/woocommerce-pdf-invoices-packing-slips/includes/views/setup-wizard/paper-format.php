<?php defined( 'ABSPATH' ) or exit; ?>
<div class="wpo-step-description">
	<h2><?php _e( 'Paper format', 'woocommerce-pdf-invoices-packing-slips' ); ?></h2>
	<p><?php _e( 'Select the paper format for your invoice.', 'woocommerce-pdf-invoices-packing-slips' ); ?></p>
</div>
<div class="wpo-setup-input">
	<?php
	$current_settings = get_option( 'wpo_wcpdf_settings_general', array() );
	?>

	<select name="wcpdf_settings[wpo_wcpdf_settings_general][paper_size]">
		<option <?php echo $current_settings['paper_size'] == 'a4' ? 'selected' : ''; ?> value="a4"><?php _e( 'A4', 'woocommerce-pdf-invoices-packing-slips' ); ?></option>
		<option <?php echo $current_settings['paper_size'] == 'letter' ? 'selected' : ''; ?> value="letter"><?php _e( 'Letter', 'woocommerce-pdf-invoices-packing-slips' ); ?></option>
	</select>
</div>