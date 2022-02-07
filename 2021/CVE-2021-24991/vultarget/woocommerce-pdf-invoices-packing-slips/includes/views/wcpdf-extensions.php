<?php defined( 'ABSPATH' ) or exit; ?>
<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery('.extensions .more').hide();

	jQuery('.extensions > li').on('click',function() {
		jQuery(this).toggleClass('expanded');
		jQuery(this).find('.more').slideToggle();
	});
});
</script>

<div class="wcpdf-extensions-ad">
	<?php $no_pro = !class_exists('WooCommerce_PDF_IPS_Pro') && !class_exists('WPO_WCPDF_Templates'); ?>
	<img src="<?php echo WPO_WCPDF()->plugin_url() . '/assets/images/wpo-helper.png'; ?>" class="wpo-helper">
	<h3><?php _e( 'Check out these premium extensions!', 'woocommerce-pdf-invoices-packing-slips' ); ?></h3>
	<i>(<?php _e( 'click items to read more', 'woocommerce-pdf-invoices-packing-slips' ); ?>)</i>
	<ul class="extensions">
		<?php if ( $no_pro ): ?>
			<!-- No Pro extensions: Ad for PDF bundle -->
			<li>
				<?php _e('Premium PDF Invoice bundle: Everything you need for a perfect invoicing system', 'woocommerce-pdf-invoices-packing-slips' )?>
				<div class="more" style="display:none;">
				<h4><?php _e( 'Supercharge WooCommerce PDF Invoices & Packing Slips with the all our premium extensions:', 'woocommerce-pdf-invoices-packing-slips' ); ?></h4>
				<?php _e( 'Professional features:', 'woocommerce-pdf-invoices-packing-slips' ); ?>
				<ul>
					<li><?php _e( 'Email/print/download <b>PDF Credit Notes & Proforma invoices</b>', 'woocommerce-pdf-invoices-packing-slips' ); ?></li>
					<li><?php _e( 'Send out a separate <b>notification email</b> with (or without) PDF invoices/packing slips, for example to a drop-shipper or a supplier.', 'woocommerce-pdf-invoices-packing-slips' ); ?></li>
					<li><?php _e( 'Attach <b>up to 3 static files</b> (for example a terms & conditions document) to the WooCommerce emails of your choice.', 'woocommerce-pdf-invoices-packing-slips' ); ?></li>
					<li><?php _e( 'Use <b>separate numbering systems</b> and/or format for proforma invoices and credit notes or utilize the main invoice numbering system', 'woocommerce-pdf-invoices-packing-slips' ); ?></li>
					<li><?php _e( '<b>Customize</b> the <b>shipping & billing address</b> format to include additional custom fields, font sizes etc. without the need to create a custom template.', 'woocommerce-pdf-invoices-packing-slips' ); ?></li>
					<li><?php _e( 'Use the plugin in multilingual <b>WPML</b> setups', 'woocommerce-pdf-invoices-packing-slips' ); ?></li>
				</ul>
				<?php _e('Advanced, customizable templates', 'woocommerce-pdf-invoices-packing-slips' )?>
				<ul>
					<li><?php _e( 'Completely customize the invoice contents (prices, taxes, thumbnails) to your needs with a drag & drop customizer', 'woocommerce-pdf-invoices-packing-slips' ); ?></li>
					<li><?php _e( 'Two extra stylish premade templates (Modern & Business)', 'woocommerce-pdf-invoices-packing-slips' ); ?></li>
				</ul>
				<?php _e('Upload automatically to dropbox', 'woocommerce-pdf-invoices-packing-slips' )?>
				<ul>
					<li><?php _e( 'This extension conveniently uploads all the invoices (and other pdf documents from the professional extension) that are emailed to your customers to Dropbox. The best way to keep your invoice administration up to date!', 'woocommerce-pdf-invoices-packing-slips' ); ?></li>
				</ul>
				<br>
				<a href="https://wpovernight.com/downloads/woocommerce-pdf-invoices-packing-slips-bundle/" target="_blank"><?php _e("Get WooCommerce PDF Invoices & Packing Slips Bundle", 'woocommerce-pdf-invoices-packing-slips' ); ?></a>
				</div>
			</li>
		<?php endif; ?>
		<?php
		// NO BUNDLE: separate ads
		if (!class_exists('WooCommerce_PDF_IPS_Pro') && !$no_pro) {
			?>
			<li>
				<?php _e('Go Pro: Proforma invoices, credit notes (=refunds) & more!', 'woocommerce-pdf-invoices-packing-slips' )?>
				<div class="more" style="display:none;">
				<?php _e( 'Supercharge WooCommerce PDF Invoices & Packing Slips with the following features:', 'woocommerce-pdf-invoices-packing-slips' ); ?>
				<ul>
					<li><?php _e( 'Email/print/download <b>PDF Credit Notes & Proforma invoices</b>', 'woocommerce-pdf-invoices-packing-slips' ); ?></li>
					<li><?php _e( 'Send out a separate <b>notification email</b> with (or without) PDF invoices/packing slips, for example to a drop-shipper or a supplier.', 'woocommerce-pdf-invoices-packing-slips' ); ?></li>
					<li><?php _e( 'Attach <b>up to 3 static files</b> (for example a terms & conditions document) to the WooCommerce emails of your choice.', 'woocommerce-pdf-invoices-packing-slips' ); ?></li>
					<li><?php _e( 'Use <b>separate numbering systems</b> and/or format for proforma invoices and credit notes or utilize the main invoice numbering system', 'woocommerce-pdf-invoices-packing-slips' ); ?></li>
					<li><?php _e( '<b>Customize</b> the <b>shipping & billing address</b> format to include additional custom fields, font sizes etc. without the need to create a custom template.', 'woocommerce-pdf-invoices-packing-slips' ); ?></li>
					<li><?php _e( 'Use the plugin in multilingual <b>WPML</b> setups', 'woocommerce-pdf-invoices-packing-slips' ); ?></li>
					<li><?php _e( 'Upload automatically to dropbox', 'woocommerce-pdf-invoices-packing-slips' )?></li>
				</ul>
				<a href="https://wpovernight.com/downloads/woocommerce-pdf-invoices-packing-slips-professional/" target="_blank"><?php _e("Get WooCommerce PDF Invoices & Packing Slips Professional!", 'woocommerce-pdf-invoices-packing-slips' ); ?></a>
			</li>
		<?php } ?>

		<?php
		if (!class_exists('WPO_WC_Smart_Reminder_Emails')) {
			?>
			<li>
				<?php _e('Automatically send payment reminders to your customers', 'woocommerce-pdf-invoices-packing-slips' )?>
				<div class="more" style="display:none;">
				<?php _e('WooCommerce Smart Reminder emails', 'woocommerce-pdf-invoices-packing-slips' )?>
				<ul>
					<li><?php _e( '<b>Completely automatic</b> scheduled emails', 'woocommerce-pdf-invoices-packing-slips' ); ?></li>
					<li><?php _e( '<b>Rich text editor</b> for the email text, including placeholders for data from the order (name, order total, etc)', 'woocommerce-pdf-invoices-packing-slips' ); ?></li>
					<li><?php _e( 'Configure the exact requirements for sending an email (time after order, order status, payment method)', 'woocommerce-pdf-invoices-packing-slips' ); ?></li>
					<li><?php _e( 'Fully <b>WPML Compatible</b> – emails will be automatically sent in the order language.', 'woocommerce-pdf-invoices-packing-slips' ); ?></li>
					<li><?php _e( '<b>Super versatile!</b> Can be used for any kind of reminder email (review reminders, repeat purchases)', 'woocommerce-pdf-invoices-packing-slips' ); ?></li>
					<li><b><?php _e( 'Integrates seamlessly with the PDF Invoices & Packing Slips plugin', 'woocommerce-pdf-invoices-packing-slips' ); ?></b></li>
				</ul>
				<a href="https://wpovernight.com/downloads/woocommerce-reminder-emails-payment-reminders/" target="_blank"><?php _e("Get WooCommerce Smart Reminder Emails", 'woocommerce-pdf-invoices-packing-slips' ); ?></a>
				</div>
			</li>
		<?php } ?>

		<?php
		if (!class_exists('WooCommerce_Ext_PrintOrders')) {
			?>
			<li>
				<?php _e('Automatically send new orders or packing slips to your printer, as soon as the customer orders!', 'woocommerce-pdf-invoices-packing-slips' )?>
				<div class="more" style="display:none;">
				<table>
					<tr>
						<td><img src="<?php echo WPO_WCPDF()->plugin_url() . '/assets/images/cloud-print.png'; ?>" class="cloud-logo"></td>
						<td>
						<?php _e( 'Check out the WooCommerce Automatic Order Printing extension from our partners at Simba Hosting', 'woocommerce-pdf-invoices-packing-slips' ); ?><br/>
						<a href="https://www.simbahosting.co.uk/s3/product/woocommerce-printnode-automatic-order-printing/?affiliates=2" target="_blank"><?php _e("WooCommerce Automatic Order Printing", 'woocommerce-pdf-invoices-packing-slips' ); ?></a>
						</td>
					</tr>
				</table>
				</div>
			</li>
		<?php } ?>

		<?php
		if (!class_exists('WooCommerce_PDF_IPS_Templates') && !class_exists('WPO_WCPDF_Templates') && !$no_pro) {
			$template_link = '<a href="https://wpovernight.com/downloads/woocommerce-pdf-invoices-packing-slips-premium-templates/" target="_blank">wpovernight.com</a>';
			$email_link = '<a href="mailto:support@wpovernight.com">support@wpovernight.com</a>'
			?>
			<li>
				<?php _e('Advanced, customizable templates', 'woocommerce-pdf-invoices-packing-slips' )?>
				<div class="more" style="display:none;">
				<ul>
					<li><?php _e( 'Completely customize the invoice contents (prices, taxes, thumbnails) to your needs with a drag & drop customizer', 'woocommerce-pdf-invoices-packing-slips' ); ?></li>
					<li><?php _e( 'Two extra stylish premade templates (Modern & Business)', 'woocommerce-pdf-invoices-packing-slips' ); ?></li>
					<?php /* translators: Premium Templates link */?>
					<li><?php printf( __("Check out the Premium PDF Invoice & Packing Slips templates at %s.", 'woocommerce-pdf-invoices-packing-slips' ), $template_link );?></li>
					<?php /* translators: email link */?>
					<li><?php printf( __("For custom templates, contact us at %s.", 'woocommerce-pdf-invoices-packing-slips' ), $email_link );?></li>
				</ul>
				</div>
			</li>
		<?php } ?>
	</ul>
	<?php
	// link to hide message when one of the premium extensions is installed
	if ( class_exists('WooCommerce_PDF_IPS_Pro') || class_exists('WPO_WCPDF_Templates') || class_exists('WooCommerce_PDF_IPS_Templates') || class_exists('WooCommerce_Ext_PrintOrders') || class_exists('WPO_WC_Smart_Reminder_Emails') ) {
		printf('<a href="%s" style="display:inline-block; margin-top: 10px;">%s</a>', add_query_arg( 'wpo_wcpdf_hide_extensions_ad', 'true' ), __( 'Hide this message', 'woocommerce-pdf-invoices-packing-slips' ) );
	}
	?>
</div>