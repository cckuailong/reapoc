<?php defined( 'ABSPATH' ) or exit; ?>
<?php
$invoice_settings_url = add_query_arg( array(
		'tab' => 'documents',
		'section' => 'invoice',
	) );
?>
<style>
.wcpdf-attachment-settings-hint {
	display: inline-block;
	background: #fff;
	border-left: 4px solid #cc99c2 !important;
	-webkit-box-shadow: 0 1px 1px 0 rgba( 0, 0, 0, 0.1 );
	box-shadow: 0 1px 1px 0 rgba( 0, 0, 0, 0.1 );
	padding: 15px;
	margin-top: 15px;
	font-size: 120%;
}
</style>
<!-- <div id="message" class="updated woocommerce-message"> -->
<div class="wcpdf-attachment-settings-hint">
<?php /* translators: <a> tags */ ?>
	<?php printf(__( 'It looks like you haven\'t setup any email attachments yet, check the settings under <b>%1$sDocuments > Invoice%2$s</b>', 'woocommerce-pdf-invoices-packing-slips' ), '<a href="'.$invoice_settings_url.'">', '</a>'); ?><br>
	<?php printf('<a href="%s" style="font">%s</a>', add_query_arg( 'wpo_wcpdf_hide_attachments_hint', 'true' ), __( 'Hide this message', 'woocommerce-pdf-invoices-packing-slips' ) ); ?>
</div>
