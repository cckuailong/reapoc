<?php
/**
 * Leave no trace...
 * Use this file to remove all elements added by plugin, including database table
 */

// exit if uninstall/delete not called
if (!defined('ABSPATH') && !defined('WP_UNINSTALL_PLUGIN'))
    exit();

if ( get_option( 'pmpro_uninstall', 0 ) ) {
	// otherwise remove pages
	$pmpro_pages = array(
		'account' => get_option( 'pmpro_account_page_id' ),
		'billing' => get_option( 'pmpro_billing_page_id' ),
		'cancel' =>get_option( 'pmpro_cancel_page_id' ),
		'checkout' => get_option( 'pmpro_checkout_page_id' ),
		'confirmation' => get_option( 'pmpro_confirmation_page_id' ),
		'invoice' => get_option( 'pmpro_invoice_page_id' ),
		'levels' => get_option( 'pmpro_levels_page_id' ),
	  'login' => get_option( 'pmpro_login_page_id' ),
	  'member_profile_edit' => get_option( 'pmpro_member_profile_edit_page_id' )
	);

	foreach ( $pmpro_pages as $pmpro_page_id => $pmpro_page ) {
		$shortcode_prefix = 'pmpro_';
		$shortcode = '[' . $shortcode_prefix . $pmpro_page_id . ']';
		$post = get_post( $pmpro_page );

		// If shortcode is found at the beginning of the page content and it is the only content that exists, remove the page
		if ( strpos( $post->post_content, $shortcode ) === 0 && strcmp( $post->post_content, $shortcode ) === 0 )
			wp_delete_post( $post->ID, true ); // Force delete (no trash)
	}

	// otherwise remove db tables
	global $wpdb;

	$tables = array(
	    'pmpro_discount_codes',
	    'pmpro_discount_codes_levels',
	    'pmpro_discount_codes_uses',
	    'pmpro_memberships_categories',
	    'pmpro_memberships_pages',
	    'pmpro_memberships_users',
	    'pmpro_membership_levels',
	    'pmpro_membership_orders',
	    'pmpro_membership_levelmeta',
	    'pmpro_membership_ordermeta'
	);

	foreach($tables as $table){
	    $delete_table = $wpdb->prefix . $table;
	    // setup sql query
	    $sql = "DROP TABLE `$delete_table`";
	    // run the query
	    $wpdb->query($sql);
	}

	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	dbDelta($sql);

	//delete options
	global $wpdb;
	$sqlQuery = "DELETE FROM $wpdb->options WHERE option_name LIKE 'pmpro_%'";
	$wpdb->query($sqlQuery);
}
