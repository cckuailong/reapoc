<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

$prefix = 'woo_st';

delete_option( $prefix . '_secret_key' );
?>