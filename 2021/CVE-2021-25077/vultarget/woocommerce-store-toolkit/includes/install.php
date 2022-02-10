<?php
function woo_st_install() {

	woo_st_create_options();

}

// Trigger the creation of Admin options for this Plugin
function woo_st_create_options() {

	$prefix = 'woo_st';

	// Generate a unique CRON secret key for each new installation
	if( !get_option( $prefix . '_secret_key' ) )
		add_option( $prefix . '_secret_key', wp_generate_password( 64, false ) );

}
?>