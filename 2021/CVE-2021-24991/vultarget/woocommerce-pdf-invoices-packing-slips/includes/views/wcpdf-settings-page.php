<?php defined( 'ABSPATH' ) or exit; ?>
<script type="text/javascript">
	jQuery( function( $ ) {
		$("#footer-thankyou").html("If you like <strong>WooCommerce PDF Invoices & Packing Slips</strong> please leave us a <a href='https://wordpress.org/support/view/plugin-reviews/woocommerce-pdf-invoices-packing-slips?rate=5#postform'>★★★★★</a> rating. A huge thank you in advance!");
	});
</script>
<div class="wrap">
	<div class="icon32" id="icon-options-general"><br /></div>
	<h2><?php _e( 'WooCommerce PDF Invoices', 'woocommerce-pdf-invoices-packing-slips' ); ?></h2>
	<h2 class="nav-tab-wrapper">
	<?php
	foreach ($settings_tabs as $tab_slug => $tab_title ) {
		$tab_link = esc_url("?page=wpo_wcpdf_options_page&tab={$tab_slug}");
		printf('<a href="%1$s" class="nav-tab nav-tab-%2$s %3$s">%4$s</a>', $tab_link, $tab_slug, (($active_tab == $tab_slug) ? 'nav-tab-active' : ''), $tab_title);
	}
	?>
	</h2>

	<?php
	do_action( 'wpo_wcpdf_before_settings_page', $active_tab, $active_section );

	// save or check option to hide extensions ad
	if ( isset( $_GET['wpo_wcpdf_hide_extensions_ad'] ) ) {
		update_option( 'wpo_wcpdf_hide_extensions_ad', true );
		$hide_ad = true;
	} else {
		$hide_ad = get_option( 'wpo_wcpdf_hide_extensions_ad' );
	}
	
	if ( !$hide_ad && !( class_exists('WooCommerce_PDF_IPS_Pro') && class_exists('WooCommerce_PDF_IPS_Templates') && class_exists('WooCommerce_Ext_PrintOrders') ) ) {
		include('wcpdf-extensions.php');
	}

	?>
	<form method="post" action="options.php" id="wpo-wcpdf-settings" class="<?php echo "{$active_tab} {$active_section}"; ?>">
		<?php
			do_action( 'wpo_wcpdf_before_settings', $active_tab, $active_section );
			if ( has_action( 'wpo_wcpdf_settings_output_'.$active_tab ) ) {
				do_action( 'wpo_wcpdf_settings_output_'.$active_tab, $active_section );
			} else {
				// legacy settings
				settings_fields( "wpo_wcpdf_{$active_tab}_settings" );
				do_settings_sections( "wpo_wcpdf_{$active_tab}_settings" );

				submit_button();
			}
			do_action( 'wpo_wcpdf_after_settings', $active_tab, $active_section );
		?>

	</form>
	<?php do_action( 'wpo_wcpdf_after_settings_page', $active_tab, $active_section ); ?>
</div>
