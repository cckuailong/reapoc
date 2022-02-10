<?php
/**
 * Order - Print View
 *
 * Gets the order and displays the print view template.
 *
 * @since 1.8.6
 */

//only admins can get this
if ( ! function_exists( "current_user_can" ) || ( ! current_user_can( "manage_options" ) && ! current_user_can( "pmpro_ordersprint" ) ) ) {
	die( __( "You do not have permissions to perform this action.", 'paid-memberships-pro' ) );
}

// Do we have an order ID?
if ( empty( $_REQUEST['order'] ) ) {
	wp_redirect( admin_url( 'admin.php?page=pmpro-orders' ) );
	exit;
}

// Get order and membership level.
$order = new MemberOrder($_REQUEST['order']);
$level = pmpro_getLevel($order->membership_id);

// Load template
if ( file_exists( get_stylesheet_directory() . '/paid-memberships-pro/pages/orders-print.php' ) ) {
	$template = get_stylesheet_directory() . '/paid-memberships-pro/pages/orders-print.php';
} elseif ( file_exists( get_template_directory() . '/paid-memberships-pro/pages/orders-print.php' ) ) {
	$template = get_template_directory() . '/paid-memberships-pro/pages/orders-print.php';
} else {
	$template = PMPRO_DIR . '/adminpages/templates/orders-print.php';
}

require_once( $template );
?>
<script>
	window.print();
</script>

