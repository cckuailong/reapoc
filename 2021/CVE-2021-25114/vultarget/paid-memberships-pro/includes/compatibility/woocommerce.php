<?php
/**
 * WooCommerce Compatibility
 *
 * This code resolves common conflicts between PMPro and WooCommerce.
 * For more advanced integration, see the PMPro WooCommerce Add-On.
 * https://www.paidmembershipspro.com/add-ons/pmpro-woocommerce/
 *
 * @since 2.3
 */
 
/**
 * Make sure the PMPro lost password form
 * doesn't submit to the WC lost password form.
 *
 * @since 2.3
 */
function pmpro_maybe_remove_wc_lostpassword_url_filter() {
	global $pmpro_pages;
	
	if ( ! empty( $pmpro_pages ) && ! empty( $pmpro_pages['login'] ) && is_page( $pmpro_pages['login'] ) ) {
		remove_filter( 'lostpassword_url', 'wc_lostpassword_url', 10, 1 );		
	}
}	
add_action( 'wp', 'pmpro_maybe_remove_wc_lostpassword_url_filter' );
