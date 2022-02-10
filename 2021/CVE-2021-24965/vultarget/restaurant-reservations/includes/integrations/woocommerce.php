<?php if ( !defined( 'ABSPATH' ) ) exit;
/**
 * WooCommerce: Allow booking managers to access the backend
 *
 * This overrides code in WooCommerce which blocks all non-WooCommerce users
 * from accessing the WordPress admin. It allows bookings managers to view the
 * backend.
 *
 * @param bool $block Whether or not to block the user
 * @since 1.6
 */
function rtb_woocommerce_allow_booking_managers_access( $block ) {

	if ( current_user_can( 'manage_bookings' ) ) {
		return false;
	}

	return $block;
}
add_filter( 'woocommerce_prevent_admin_access', 'rtb_woocommerce_allow_booking_managers_access' );
