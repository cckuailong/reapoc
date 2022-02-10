<?php
/*
	Upgrade to 1.9.4
	Update for div layout.
*/
function pmpro_upgrade_1_9_4() {

	$parent_theme_template = get_template_directory() . "/paid-memberships-pro/pages/checkout.php";
	$child_theme_template = get_stylesheet_directory() . "/paid-memberships-pro/pages/checkout.php";

	$pmpro_hide_notice = get_option( 'pmpro_hide_div_notice', 0 );
		
		// Show admin notice if the user has a custom checkout page template.
		if( ( file_exists( $parent_theme_template ) || file_exists( $child_theme_template ) ) && empty( $pmpro_hide_notice ) && empty( $_REQUEST['pmpro_div_notice_hide'] ) ) {
			add_action( 'admin_notices', 'pmpro_upgrade_1_9_4_show_div_notice' );
		}

		pmpro_setOption( 'db_version', '1.94' );
		return 1.94;
}

// Code to handle the admin notice.
function pmpro_upgrade_1_9_4_show_div_notice() {
 ?>
    <div class="notice notice-warning">
        <p><?php _e( 'We have detected that you are using a custom checkout page template for Paid Memberships Pro. This was recently changed and may need to be updated in order to display correctly.', 'paid-memberships-pro')?>
        	<?php _e('If you notice UI issues after upgrading, <a href="https://www.paidmembershipspro.com/add-ons/table-layout-plugin-pages/">see this free add on to temporarily roll back to the table-based layout while you resolve the issues</a>.', 'paid-memberships-pro' ); ?> <a href="<?php echo add_query_arg('pmpro_div_notice_hide', '1', $_SERVER['REQUEST_URI']);?>"><?php _e( 'Dismiss', 'paid-memberships-pro' );?></a></p>
    </div>
<?php
}

function pmpro_update_1_9_4_notice_dismiss() {

	// check if query arg is available.
	if( !empty( $_REQUEST['pmpro_div_notice_hide'] ) ) {
		update_option( 'pmpro_hide_div_notice', 1 );
	}
}

add_action( 'admin_init', 'pmpro_update_1_9_4_notice_dismiss' );