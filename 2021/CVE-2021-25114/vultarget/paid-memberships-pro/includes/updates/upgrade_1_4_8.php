<?php
function pmpro_upgrade_1_4_8()
{
	/*
		Adding a billing_country field to the orders table.		
	*/

	global $wpdb;
	$wpdb->hide_errors();
	$wpdb->pmpro_membership_orders = $wpdb->prefix . 'pmpro_membership_orders';

	//billing_country
	$sqlQuery = "
		ALTER TABLE  `" . $wpdb->pmpro_membership_orders . "` ADD  `billing_country` VARCHAR( 128 ) NOT NULL AFTER  `billing_zip`
	";
	$wpdb->query($sqlQuery);

	pmpro_setOption("db_version", "1.48");
	return 1.48;
}
