<div id="content">

	<h2 class="nav-tab-wrapper">
		<a data-tab-id="overview" class="nav-tab<?php woo_ce_admin_active_tab( 'overview' ); ?>" href="<?php echo add_query_arg( array( 'page' => 'woo_ce', 'tab' => 'overview' ), 'admin.php' ); ?>"><?php _e( 'Overview', 'woo_ce' ); ?></a>
		<a data-tab-id="export" class="nav-tab<?php woo_ce_admin_active_tab( 'export' ); ?>" href="<?php echo add_query_arg( array( 'page' => 'woo_ce', 'tab' => 'export' ), 'admin.php' ); ?>"><?php _e( 'Export', 'woo_ce' ); ?></a>
		<a data-tab-id="archive" class="nav-tab<?php woo_ce_admin_active_tab( 'archive' ); ?>" href="<?php echo add_query_arg( array( 'page' => 'woo_ce', 'tab' => 'archive' ), 'admin.php' ); ?>"><?php _e( 'Archives', 'woo_ce' ); ?></a>
		<a data-tab-id="settings" class="nav-tab<?php woo_ce_admin_active_tab( 'settings' ); ?>" href="<?php echo add_query_arg( array( 'page' => 'woo_ce', 'tab' => 'settings' ), 'admin.php' ); ?>"><?php _e( 'Settings', 'woo_ce' ); ?></a>
		<a data-tab-id="tools" class="nav-tab<?php woo_ce_admin_active_tab( 'tools' ); ?>" href="<?php echo add_query_arg( array( 'page' => 'woo_ce', 'tab' => 'tools' ), 'admin.php' ); ?>"><?php _e( 'Tools', 'woo_ce' ); ?></a>
	</h2>
	<?php woo_ce_tab_template( $tab ); ?>

</div>
<!-- #content -->