<?php
function pmpro_upgrade_1_5_9()
{
	global $wpdb;
	$wpdb->hide_errors();
	$wpdb->pmpro_membership_orders = $wpdb->prefix . 'pmpro_membership_orders';

	//fix firstpayment statuses
	$sqlQuery = "UPDATE " . $wpdb->pmpro_membership_orders . " SET status = 'success' WHERE status = 'firstpayment'";
	$wpdb->query($sqlQuery);

	pmpro_setOption("db_version", "1.59");
	return 1.59;
}
