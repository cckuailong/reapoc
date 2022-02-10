<h3><a href="<?php echo esc_url( add_query_arg( 'tab', 'nuke' ) ); ?>"><?php _e( 'Nuke', 'woocommerce-store-toolkit' ); ?></a></h3>
<p><?php _e( 'Permanently remove WooCommerce generated details from your WordPress database.', 'woocommerce-store-toolkit' ); ?></p>
<ul class="ul-disc">
	<li><a href="<?php echo esc_url( add_query_arg( 'tab', 'nuke' ) ); ?>#empty-woocommerce-tables"><?php _e( 'Empty WooCommerce Tables', 'woocommerce-store-toolkit' ); ?></a></li>
	<li><a href="<?php echo esc_url( add_query_arg( 'tab', 'nuke' ) ); ?>#empty-product-by-status"><?php _e( 'Delete Products by Product Status', 'woocommerce-store-toolkit' ); ?></a></li>
	<li><a href="<?php echo esc_url( add_query_arg( 'tab', 'nuke' ) ); ?>#empty-product-by-category"><?php _e( 'Delete Products by Product Category', 'woocommerce-store-toolkit' ); ?></a></li>
	<li><a href="<?php echo esc_url( add_query_arg( 'tab', 'nuke' ) ); ?>#delete-sales-by-sale-status"><?php _e( 'Delete Orders by Order Status', 'woocommerce-store-toolkit' ); ?></a></li>
	<li><a href="<?php echo esc_url( add_query_arg( 'tab', 'nuke' ) ); ?>#delete-sales-by-date"><?php _e( 'Delete Orders by Order Date', 'woocommerce-store-toolkit' ); ?></a></li>
	<li><a href="<?php echo esc_url( add_query_arg( 'tab', 'nuke' ) ); ?>#empty-wordpress-tables"><?php _e( 'Empty WordPress Tables', 'woocommerce-store-toolkit' ); ?></a></li>
</ul>

<h3><a href="<?php echo esc_url( add_query_arg( 'tab', 'post_types' ) ); ?>"><?php _e( 'Post Types', 'woocommerce-store-toolkit' ); ?></a></h3>
<p><?php _e( 'View a summary of all Post Types acrosss this WordPress site.', 'woocommerce-store-toolkit' ); ?></p>

<h3><a href="<?php echo esc_url( add_query_arg( 'tab', 'tools' ) ); ?>"><?php _e( 'Tools', 'woocommerce-store-toolkit' ); ?></a></h3>
<p><?php _e( 'Other WooCommerce actions for store and database maintenance.', 'woocommerce-store-toolkit' ); ?></p>
<ul class="ul-disc">
	<li><a href="<?php echo esc_url( add_query_arg( 'tab', 'tools' ) ); ?>"><?php _e( 'Re-link rogue Products to Simple Product Type', 'woocommerce-store-toolkit' ); ?></a></li>
	<li><a href="<?php echo esc_url( add_query_arg( 'tab', 'tools' ) ); ?>"><?php _e( 'Scan for corrupt Variations and set to Simple Product Type', 'woocommerce-store-toolkit' ); ?></a></li>
	<li><a href="<?php echo esc_url( add_query_arg( 'tab', 'tools' ) ); ?>"><?php _e( 'Refresh Product Transients', 'woocommerce-store-toolkit' ); ?></a></li>
<!--
	<li><a href="<?php echo esc_url( add_query_arg( 'tab', 'tools' ) ); ?>"><?php _e( 'Generate Sample Products', 'woocommerce-store-toolkit' ); ?></a></li>
-->
	<li><a href="<?php echo esc_url( add_query_arg( 'tab', 'tools' ) ); ?>"><?php _e( 'Generate Sample Orders', 'woocommerce-store-toolkit' ); ?></a></li>
	<li><a href="<?php echo esc_url( add_query_arg( 'tab', 'tools' ) ); ?>"><?php _e( 'Auto-complete Orders with 0 totals', 'woocommerce-store-toolkit' ); ?></a></li>
	<li><a href="<?php echo esc_url( add_query_arg( 'tab', 'tools' ) ); ?>"><?php _e( 'Unlock the Edit Product screen for Variations', 'woocommerce-store-toolkit' ); ?></a></li>
</ul>

<h3><div class="dashicons dashicons-admin-settings"></div>&nbsp;<a href="<?php echo esc_url( add_query_arg( 'tab', 'settings' ) ); ?>"><?php _e( 'Settings', 'woocommerce-store-toolkit' ); ?></a></h3>
<p><?php _e( 'Manage Store Toolkit options from a single detailed screen.', 'woocommerce-exporter' ); ?></p>
<ul class="ul-disc">
	<li>
		<a href="<?php echo esc_url( add_query_arg( 'tab', 'settings' ) ); ?>#cron-nuke"><?php _e( 'CRON Nuke', 'woocommerce-store-toolkit' ); ?></a>
	</li>
</ul>