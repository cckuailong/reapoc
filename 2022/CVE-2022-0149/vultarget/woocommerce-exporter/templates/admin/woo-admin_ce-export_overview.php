<div class="overview-left">

	<h3><a href="<?php echo add_query_arg( 'tab', 'export' ); ?>"><?php _e( 'Export', 'woo_ce' ); ?></a></h3>
	<p><?php _e( 'Export store details out of WooCommerce into a CSV-formatted file.', 'woo_ce' ); ?></p>
	<ul class="ul-disc">
		<li>
			<a href="<?php echo add_query_arg( 'tab', 'export' ); ?>#export-products"><?php _e( 'Export Products', 'woo_ce' ); ?></a>
		</li>
		<li>
			<a href="<?php echo add_query_arg( 'tab', 'export' ); ?>#export-categories"><?php _e( 'Export Categories', 'woo_ce' ); ?></a>
		</li>
		<li>
			<a href="<?php echo add_query_arg( 'tab', 'export' ); ?>#export-tags"><?php _e( 'Export Tags', 'woo_ce' ); ?></a>
		</li>
		<li>
			<a href="<?php echo add_query_arg( 'tab', 'export' ); ?>#export-orders"><?php _e( 'Export Orders', 'woo_ce' ); ?></a>
<?php if( !function_exists( 'woo_cd_admin_init' ) ) { ?>
			<span class="description">(<?php printf( __( 'available in %s', 'woo_ce' ), $woo_cd_link ); ?>)</span>
<?php } ?>
		</li>
		<li>
			<a href="<?php echo add_query_arg( 'tab', 'export' ); ?>#export-customers"><?php _e( 'Export Customers', 'woo_ce' ); ?></a>
<?php if( !function_exists( 'woo_cd_admin_init' ) ) { ?>
			<span class="description">(<?php printf( __( 'available in %s', 'woo_ce' ), $woo_cd_link ); ?>)</span>
<?php } ?>
		</li>
		<li>
			<a href="<?php echo add_query_arg( 'tab', 'export' ); ?>#export-coupons"><?php _e( 'Export Coupons', 'woo_ce' ); ?></a>
<?php if( !function_exists( 'woo_cd_admin_init' ) ) { ?>
			<span class="description">(<?php printf( __( 'available in %s', 'woo_ce' ), $woo_cd_link ); ?>)</span>
<?php } ?>
		</li>
	</ul>

	<h3><a href="<?php echo add_query_arg( 'tab', 'archive' ); ?>"><?php _e( 'Archives', 'woo_ce' ); ?></a></h3>
	<p><?php _e( 'Download copies of prior store exports.', 'woo_ce' ); ?></p>

	<h3><a href="<?php echo add_query_arg( 'tab', 'settings' ); ?>"><?php _e( 'Settings', 'woo_ce' ); ?></a></h3>
	<p><?php _e( 'Manage CSV export options from a single detailed screen.', 'woo_ce' ); ?></p>

	<h3><a href="<?php echo add_query_arg( 'tab', 'tools' ); ?>"><?php _e( 'Tools', 'woo_ce' ); ?></a></h3>
	<p><?php _e( 'Export tools for WooCommerce.', 'woo_ce' ); ?></p>

	<hr />
<?php if( !function_exists( 'woo_cd_admin_init' ) ) { ?>
	<label class="description">
		<input type="checkbox" disabled="disabled" /> <?php _e( 'Jump to Export screen in the future', 'woo_ce' ); ?>
		<span class="description"> - <?php printf( __( 'available in %s', 'woo_ce' ), $woo_cd_link ); ?></span>
	</label>
<?php } else { ?>
	<form id="skip_overview_form" method="post">
		<label><input type="checkbox" id="skip_overview" name="skip_overview"<?php checked( $skip_overview ); ?> /> <?php _e( 'Jump to Export screen in the future', 'woo_ce' ); ?></label>
		<input type="hidden" name="action" value="skip_overview" />
	</form>
<?php } ?>

</div>
<!-- .overview-left -->
<?php if( !function_exists( 'woo_cd_admin_init' ) ) { ?>
<div class="welcome-panel overview-right">
	<h3>
		<!-- <span><a href="#"><attr title="<?php _e( 'Dismiss this message', 'woo_ce' ); ?>"><?php _e( 'Dismiss', 'woo_ce' ); ?></attr></a></span> -->
		<?php _e( 'Upgrade to Pro', 'woo_ce' ); ?>
	</h3>
	<p class="clear"><?php _e( 'Upgrade to Store Exporter Deluxe to unlock business focused e-commerce features within Store Exporter, including:', 'woo_ce' ); ?></p>
	<ul class="ul-disc">
		<li><?php _e( 'Select export date ranges', 'woo_ce' ); ?></li>
		<li><?php _e( 'Export Orders', 'woo_ce' ); ?></li>
		<li><?php _e( 'Select Order fields to export', 'woo_ce' ); ?></li>
		<li><?php _e( 'Export custom Order and Order Item meta', 'woo_ce' ); ?></li>
		<li><?php _e( 'Export Customers', 'woo_ce' ); ?></li>
		<li><?php _e( 'Select Customer fields to export', 'woo_ce' ); ?></li>
		<li><?php _e( 'Export Coupons', 'woo_ce' ); ?></li>
		<li><?php _e( 'Select Coupon fields to export', 'woo_ce' ); ?></li>
		<li><?php _e( 'CRON / Scheduled Exports', 'woo_ce' ); ?></li>
		<li><?php _e( 'Export to XML', 'woo_ce' ); ?></li>
		<li><?php _e( 'Premium Support', 'woo_ce' ); ?></li>
	</ul>
	<p>
		<a href="<?php echo $woo_cd_url; ?>" target="_blank" class="button"><?php _e( 'More Features', 'woo_ce' ); ?></a>&nbsp;
		<a href="<?php echo $woo_cd_url; ?>" target="_blank" class="button button-primary"><?php _e( 'Buy Now', 'woo_ce' ); ?></a>
	</p>
</div>
<!-- .overview-right -->
<?php } ?>