<div class="overview-left">

	<h3><div class="dashicons dashicons-migrate"></div>&nbsp;<a href="<?php echo esc_url( add_query_arg( 'tab', 'export' ) ); ?>"><?php _e( 'Quick Export', 'woocommerce-exporter' ); ?></a></h3>
	<p><?php _e( 'Export store details out of WooCommerce into common export files (e.g. CSV, TSV, XLS, XLSX, XML, etc.).', 'woocommerce-exporter' ); ?></p>
	<ul class="ul-disc">
		<li>
			<a href="<?php echo esc_url( add_query_arg( 'tab', 'export' ) ); ?>#export-product"><?php _e( 'Export Products', 'woocommerce-exporter' ); ?></a>
		</li>
		<li>
			<a href="<?php echo esc_url( add_query_arg( 'tab', 'export' ) ); ?>#export-category"><?php _e( 'Export Categories', 'woocommerce-exporter' ); ?></a>
		</li>
		<li>
			<a href="<?php echo esc_url( add_query_arg( 'tab', 'export' ) ); ?>#export-tag"><?php _e( 'Export Tags', 'woocommerce-exporter' ); ?></a>
		</li>
		<li>
			<a href="<?php echo esc_url( add_query_arg( 'tab', 'export' ) ); ?>#export-brand"><?php _e( 'Export Brands', 'woocommerce-exporter' ); ?></a>
			<span class="description">(<?php printf( __( 'available in %s', 'woocommerce-exporter' ), $woo_cd_link ); ?>)</span>
		</li>
		<li>
			<a href="<?php echo esc_url( add_query_arg( 'tab', 'export' ) ); ?>#export-order"><?php _e( 'Export Orders', 'woocommerce-exporter' ); ?></a>
		</li>
		<li>
			<a href="<?php echo esc_url( add_query_arg( 'tab', 'export' ) ); ?>#export-customer"><?php _e( 'Export Customers', 'woocommerce-exporter' ); ?></a>
			<span class="description">(<?php printf( __( 'available in %s', 'woocommerce-exporter' ), $woo_cd_link ); ?>)</span>
		</li>
		<li>
			<a href="<?php echo esc_url( add_query_arg( 'tab', 'export' ) ); ?>#export-user"><?php _e( 'Export Users', 'woocommerce-exporter' ); ?></a>
		</li>
		<li>
			<a href="<?php echo esc_url( add_query_arg( 'tab', 'export' ) ); ?>#export-user"><?php _e( 'Export Reviews', 'woocommerce-exporter' ); ?></a>
			<span class="description">(<?php printf( __( 'available in %s', 'woocommerce-exporter' ), $woo_cd_link ); ?>)</span>
		</li>
		<li>
			<a href="<?php echo esc_url( add_query_arg( 'tab', 'export' ) ); ?>#export-coupon"><?php _e( 'Export Coupons', 'woocommerce-exporter' ); ?></a>
			<span class="description">(<?php printf( __( 'available in %s', 'woocommerce-exporter' ), $woo_cd_link ); ?>)</span>
		</li>
		<li>
			<a href="<?php echo esc_url( add_query_arg( 'tab', 'export' ) ); ?>#export-subscription"><?php _e( 'Export Subscriptions', 'woocommerce-exporter' ); ?></a>
			<span class="description">(<?php printf( __( 'available in %s', 'woocommerce-exporter' ), $woo_cd_link ); ?>)</span>
		</li>
		<li>
			<a href="<?php echo esc_url( add_query_arg( 'tab', 'export' ) ); ?>#export-product_vendor"><?php _e( 'Export Product Vendors', 'woocommerce-exporter' ); ?></a>
			<span class="description">(<?php printf( __( 'available in %s', 'woocommerce-exporter' ), $woo_cd_link ); ?>)</span>
		</li>
		<li>
			<a href="<?php echo esc_url( add_query_arg( 'tab', 'export' ) ); ?>#export-commission"><?php _e( 'Export Commissions', 'woocommerce-exporter' ); ?></a>
			<span class="description">(<?php printf( __( 'available in %s', 'woocommerce-exporter' ), $woo_cd_link ); ?>)</span>
		</li>
		<li>
			<a href="<?php echo esc_url( add_query_arg( 'tab', 'export' ) ); ?>#export-shipping_class"><?php _e( 'Export Shipping Classes', 'woocommerce-exporter' ); ?></a>
			<span class="description">(<?php printf( __( 'available in %s', 'woocommerce-exporter' ), $woo_cd_link ); ?>)</span>
		</li>
		<li>
			<a href="<?php echo esc_url( add_query_arg( 'tab', 'export' ) ); ?>#export-ticket"><?php _e( 'Export Tickets', 'woocommerce-exporter' ); ?></a>
			<span class="description">(<?php printf( __( 'available in %s', 'woocommerce-exporter' ), $woo_cd_link ); ?>)</span>
		</li>
		<li>
			<a href="<?php echo esc_url( add_query_arg( 'tab', 'export' ) ); ?>#export-booking"><?php _e( 'Export Bookings', 'woocommerce-exporter' ); ?></a>
			<span class="description">(<?php printf( __( 'available in %s', 'woocommerce-exporter' ), $woo_cd_link ); ?>)</span>
		</li>
		<li>
			<a href="<?php echo esc_url( add_query_arg( 'tab', 'export' ) ); ?>#export-attribute"><?php _e( 'Export Attributes', 'woocommerce-exporter' ); ?></a>
			<span class="description">(<?php printf( __( 'available in %s', 'woocommerce-exporter' ), $woo_cd_link ); ?>)</span>
		</li>
	</ul>

	<h3>
		<div class="dashicons dashicons-calendar"></div>&nbsp;<a href="<?php echo esc_url( add_query_arg( 'tab', 'scheduled_export' ) ); ?>"><?php _e( 'Scheduled Exports', 'woocommerce-exporter' ); ?></a>
		<span class="description">(<?php printf( __( 'available in %s', 'woocommerce-exporter' ), $woo_cd_link ); ?>)</span>
	</h3>
	<p><?php _e( 'Automatically generate exports and apply filters to export just what you need.', 'woocommerce-exporter' ); ?></p>

	<h3>
		<div class="dashicons dashicons-list-view"></div>&nbsp;<a href="<?php echo esc_url( add_query_arg( 'tab', 'export_template' ) ); ?>"><?php _e( 'Export Templates', 'woocommerce-exporter' ); ?></a>
		<span class="description">(<?php printf( __( 'available in %s', 'woocommerce-exporter' ), $woo_cd_link ); ?>)</span>
	</h3>
	<p><?php _e( 'Create lists of pre-defined fields which can be applied to exports.', 'woocommerce-exporter' ); ?></p>

	<h3><div class="dashicons dashicons-list-view"></div>&nbsp;<a href="<?php echo esc_url( add_query_arg( 'tab', 'archive' ) ); ?>"><?php _e( 'Archives', 'woocommerce-exporter' ); ?></a></h3>
	<p><?php _e( 'Download copies of prior store exports.', 'woocommerce-exporter' ); ?></p>

	<h3><div class="dashicons dashicons-admin-settings"></div>&nbsp;<a href="<?php echo esc_url( add_query_arg( 'tab', 'settings' ) ); ?>"><?php _e( 'Settings', 'woocommerce-exporter' ); ?></a></h3>
	<p><?php _e( 'Manage export options from a single detailed screen.', 'woocommerce-exporter' ); ?></p>
	<ul class="ul-disc">
		<li>
			<a href="<?php echo esc_url( add_query_arg( 'tab', 'settings' ) ); ?>#general-settings"><?php _e( 'General Settings', 'woocommerce-exporter' ); ?></a>
		</li>
		<li>
			<a href="<?php echo esc_url( add_query_arg( 'tab', 'settings' ) ); ?>#csv-settings"><?php _e( 'CSV Settings', 'woocommerce-exporter' ); ?></a>
		</li>
		<li>
			<a href="<?php echo esc_url( add_query_arg( 'tab', 'settings' ) ); ?>#xml-settings"><?php _e( 'XML Settings', 'woocommerce-exporter' ); ?></a>
			<span class="description">(<?php printf( __( 'available in %s', 'woocommerce-exporter' ), $woo_cd_link ); ?>)</span>
		</li>
		<li>
			<a href="<?php echo esc_url( add_query_arg( 'tab', 'settings' ) ); ?>#scheduled-exports"><?php _e( 'Scheduled Exports', 'woocommerce-exporter' ); ?></a>
			<span class="description">(<?php printf( __( 'available in %s', 'woocommerce-exporter' ), $woo_cd_link ); ?>)</span>
		</li>
		<li>
			<a href="<?php echo esc_url( add_query_arg( 'tab', 'settings' ) ); ?>#cron-exports"><?php _e( 'CRON Exports', 'woocommerce-exporter' ); ?></a>
			<span class="description">(<?php printf( __( 'available in %s', 'woocommerce-exporter' ), $woo_cd_link ); ?>)</span>
		</li>
		<li>
			<a href="<?php echo esc_url( add_query_arg( 'tab', 'settings' ) ); ?>#orders-screen"><?php _e( 'Orders Screen', 'woocommerce-exporter' ); ?></a>
			<span class="description">(<?php printf( __( 'available in %s', 'woocommerce-exporter' ), $woo_cd_link ); ?>)</span>
		</li>
		<li>
			<a href="<?php echo esc_url( add_query_arg( 'tab', 'settings' ) ); ?>#export-triggers"><?php _e( 'Export Triggers', 'woocommerce-exporter' ); ?></a>
			<span class="description">(<?php printf( __( 'available in %s', 'woocommerce-exporter' ), $woo_cd_link ); ?>)</span>
		</li>
	</ul>

	<h3><div class="dashicons dashicons-hammer"></div>&nbsp;<a href="<?php echo esc_url( add_query_arg( 'tab', 'tools' ) ); ?>"><?php _e( 'Tools', 'woocommerce-exporter' ); ?></a></h3>
	<p><?php _e( 'Export tools for WooCommerce.', 'woocommerce-exporter' ); ?></p>

	<hr />
	<label class="description">
		<input type="checkbox" disabled="disabled" /> <?php _e( 'Jump to Export screen in the future', 'woocommerce-exporter' ); ?>
		<span class="description"> - <?php printf( __( 'available in %s', 'woocommerce-exporter' ), $woo_cd_link ); ?></span>
	</label>

</div>
<!-- .overview-left -->
<div class="welcome-panel overview-right">
	<h3>
		<?php _e( 'Unlock business focused WooCommerce exports.', 'woocommerce-exporter' ); ?>
	</h3>
	<p class="clear"><strong><?php _e( 'Upgrade to Store Exporter Deluxe today!', 'woocommerce-exporter' ); ?></strong></p>
	<ul class="ul-disc">
		<li><?php _e( 'Native export support for 125+ Plugins', 'woocommerce-exporter' ); ?></li>
		<li><?php _e( 'Premium Support', 'woocommerce-exporter' ); ?></li>
		<li><?php _e( 'Select export date ranges', 'woocommerce-exporter' ); ?></li>
		<li><?php _e( 'Export Templates', 'woocommerce-exporter' ); ?></li>
		<li><?php _e( 'Filter exports by multiple filter options', 'woocommerce-exporter' ); ?></li>
		<li><?php _e( 'Export Orders, Customers, Coupons, Subscriptions, Product Vendors, Shippping Classes, Attributes, Bookings, Tickets and more', 'woocommerce-exporter' ); ?></li>
		<li><?php _e( 'Export custom Order, Order Item, Customer, Subscription, Booking, User meta and more', 'woocommerce-exporter' ); ?></li>
		<li><?php _e( 'CRON export engine', 'woocommerce-exporter' ); ?></li>
		<li><?php _e( 'WP-CLI export engine', 'woocommerce-exporter' ); ?></li>
		<li><?php _e( 'Schedule automatic exports with filtering options', 'woocommerce-exporter' ); ?></li>
		<li><?php _e( 'Export automatically on new Order received', 'woocommerce-exporter' ); ?></li>
		<li><?php _e( 'Export to remote POST', 'woocommerce-exporter' ); ?></li>
		<li><?php _e( 'Export to e-mail addresses', 'woocommerce-exporter' ); ?></li>
		<li><?php _e( 'Export to remote FTP/FTPS/SFTP', 'woocommerce-exporter' ); ?></li>
		<li><?php _e( 'Export to Excel 2007-2013 (XLSX) file', 'woocommerce-exporter' ); ?></li>
		<li><?php _e( 'Export to Excel 97-2003 (XLS) file', 'woocommerce-exporter' ); ?></li>
		<li><?php _e( 'Export to XML, JSON and RSS file formats', 'woocommerce-exporter' ); ?></li>
		<li><?php _e( '...and more.', 'woocommerce-exporter' ); ?></li>
	</ul>
	<p>
		<a href="<?php echo $woo_cd_url; ?>" target="_blank" class="button button-primary"><?php _e( 'Upgrade Now', 'woocommerce-exporter' ); ?></a>
		<a href="<?php echo $woo_cd_url; ?>" target="_blank" class="button"><?php _e( 'Tell me more', 'woocommerce-exporter' ); ?></a>&nbsp;
	</p>
</div>
<!-- .overview-right -->