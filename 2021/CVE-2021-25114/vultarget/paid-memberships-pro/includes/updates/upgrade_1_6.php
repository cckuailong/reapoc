<?php
function pmpro_upgrade_1_6()
{
	global $wpdb;
	$wpdb->hide_errors();
	$wpdb->pmpro_membership_orders = $wpdb->prefix . 'pmpro_membership_orders';

	//add notes column to orders
	$sqlQuery = "ALTER TABLE  `" . $wpdb->pmpro_membership_orders . "` ADD  `notes` TEXT NOT NULL";
	$wpdb->query($sqlQuery);

	pmpro_setOption("db_version", "1.6");
	return 1.6;
}
