<?php
function pmpro_upgrade_1_5()
{
	/*
		Add the id and status fields to pmpro_memberships_users, change primary key to id instead of user_id
	*/

	global $wpdb;
	$wpdb->hide_errors();
	$wpdb->pmpro_memberships_users = $wpdb->prefix . 'pmpro_memberships_users';

	//remove primary key
	$sqlQuery = "ALTER TABLE `" . $wpdb->pmpro_memberships_users . "` DROP PRIMARY KEY";
	$wpdb->query($sqlQuery);

	//id
	$sqlQuery = "ALTER TABLE `" . $wpdb->pmpro_memberships_users . "` ADD  `id` BIGINT( 20 ) UNSIGNED AUTO_INCREMENT FIRST, ADD PRIMARY KEY(id)";
	$wpdb->query($sqlQuery);

	//status
	$sqlQuery = "ALTER TABLE `" . $wpdb->pmpro_memberships_users . "` ADD  `status` varchar( 20 ) NOT NULL DEFAULT 'active' AFTER `trial_limit`";
	$wpdb->query($sqlQuery);

	pmpro_setOption("db_version", "1.5");
	return 1.5;
}
