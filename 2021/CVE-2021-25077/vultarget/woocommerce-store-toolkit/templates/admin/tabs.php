<div id="content">

	<h2 class="nav-tab-wrapper">
		<a data-tab-id="overview" class="nav-tab<?php woo_st_admin_active_tab( 'overview' ); ?>" href="<?php echo esc_url( add_query_arg( array( 'page' => 'woo_st', 'tab' => 'overview' ), 'admin.php' ) ); ?>"><?php _e( 'Overview', 'woocommerce-store-toolkit' ); ?></a>
		<a data-tab-id="nuke" class="nav-tab<?php woo_st_admin_active_tab( 'nuke' ); ?>" href="<?php echo esc_url( add_query_arg( array( 'page' => 'woo_st', 'tab' => 'nuke' ), 'admin.php' ) ); ?>"><?php _e( 'Nuke', 'woocommerce-store-toolkit' ); ?></a>
		<a data-tab-id="post_types" class="nav-tab<?php woo_st_admin_active_tab( 'post_types' ); ?>" href="<?php echo esc_url( add_query_arg( array( 'page' => 'woo_st', 'tab' => 'post_types' ), 'admin.php' ) ); ?>"><?php _e( 'Post Types', 'woocommerce-store-toolkit' ); ?></a>
		<a data-tab-id="tools" class="nav-tab<?php woo_st_admin_active_tab( 'tools' ); ?>" href="<?php echo esc_url( add_query_arg( array( 'page' => 'woo_st', 'tab' => 'tools' ), 'admin.php' ) ); ?>"><?php _e( 'Tools', 'woocommerce-store-toolkit' ); ?></a>
		<a data-tab-id="settings" class="nav-tab<?php woo_st_admin_active_tab( 'settings' ); ?>" href="<?php echo esc_url( add_query_arg( array( 'page' => 'woo_st', 'tab' => 'settings' ), 'admin.php' ) ); ?>"><?php _e( 'Settings', 'woocommerce-store-toolkit' ); ?></a>
	</h2>
	<?php woo_st_tab_template( $tab ); ?>

</div>
<!-- #content -->

<div id="progress" style="display:none;">
	<p><?php _e( 'Chosen WooCommerce details are being nuked, this process can take awhile. Time for a beer?', 'woocommerce-store-toolkit' ); ?></p>
	<img src="<?php echo plugins_url( '/templates/admin/images/progress.gif', WOO_ST_RELPATH ); ?>" alt="" />
	<hr />
	<h2><?php _e( 'Just to clarify...', 'woocommerce-store-toolkit' ); ?></h2>
	<p><?php _e( 'Just to clarify what\'s going on behind the progress bar in case the dredded \'white screen\' appears or a a 500 Internal Server Error is returned:', 'woocommerce-store-toolkit' ); ?></p>
	<ol class="ol-disc">
		<li><?php _e( 'First we enter a loop that checks if any records for the selected dataset exist', 'woocommerce-store-toolkit' ); ?></li>
		<li><?php _e( 'Then we ask WordPress (via WP_Query) for a list of 100 ID\'s from the selected dataset (e.g. 100 Product ID\'s/Product Category ID\'s/Order ID\'s, etc.)', 'woocommerce-store-toolkit' ); ?></li>
		<li><?php _e( 'We enter a second loop that permanently deletes each ID that WordPress gave us', 'woocommerce-store-toolkit' ); ?></li>
		<li><?php _e( 'When that first 100 records are no more we ask for the next 100, rinse and repeat, rinse and repeat, ...', 'woocommerce-store-toolkit' ); ?></li>
		<li><?php _e( 'Once we\'ve nuked every record for the selected datasets we can finally show the success screen notice <strong>:)</strong>', 'woocommerce-store-toolkit' ); ?></li>
	</ol>
	<p><?php _e( 'Where things can go wrong during this process is:', 'woocommerce-store-toolkit' ); ?></p>
	<ul class="ul-disc">
		<li><?php _e( 'We hit the 30 second server timeout configured on some hosting server\'s that kills the active process (ours), or', 'woocommerce-store-toolkit' ); ?></li>
		<li><?php _e( 'WordPress maxes out its memory allocation looping through each batch of 100 ID\'s', 'woocommerce-store-toolkit' ); ?></li>
	</ul>
	<p><?php _e( 'Re-opening Store Toolkit from the WordPress Administration and hitting continue will resolve most issues. Happy nuking! <strong>:)</strong>', 'woocommerce-store-toolkit' ); ?></p>
</div>
<!-- #progress -->