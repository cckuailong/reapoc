<?php

function woo_ce_admin_scheduled_exports_recent_scheduled_exports() {

	$size = 0;
	$pagination_links = '';

	$template = 'scheduled_exports-recent_scheduled_exports.php';
	if( file_exists( WOO_CE_PATH . 'templates/admin/' . $template ) ) {
		include_once( WOO_CE_PATH . 'templates/admin/' . $template );
	} else {
		$message = sprintf( __( 'We couldn\'t load the widget template file <code>%s</code> within <code>%s</code>, this file should be present.', 'woocommerce-exporter' ), $template, WOO_CE_PATH . 'templates/admin/...' );

		ob_start(); ?>
<p><strong><?php echo $message; ?></strong></p>
<p><?php _e( 'You can see this error for one of a few common reasons', 'woocommerce-exporter' ); ?>:</p>
<ul class="ul-disc">
	<li><?php _e( 'WordPress was unable to create this file when the Plugin was installed or updated', 'woocommerce-exporter' ); ?></li>
	<li><?php _e( 'The Plugin files have been recently changed and there has been a file conflict', 'woocommerce-exporter' ); ?></li>
	<li><?php _e( 'The Plugin file has been locked and cannot be opened by WordPress', 'woocommerce-exporter' ); ?></li>
</ul>
<p><?php _e( 'Jump onto our website and download a fresh copy of this Plugin as it might be enough to fix this issue. If this persists get in touch with us.', 'woocommerce-exporter' ); ?></p>
<?php
		ob_end_flush();
	}

}
add_action( 'woo_ce_after_scheduled_exports', 'woo_ce_admin_scheduled_exports_recent_scheduled_exports' );
?>