<?php
function pmpro_upgrade_1_4()
{
	global $wpdb;
	$wpdb->hide_errors();
	$wpdb->pmpro_membership_levels = $wpdb->prefix . 'pmpro_membership_levels';

	//confirmation message
	$sqlQuery = "
		ALTER TABLE  `" . $wpdb->pmpro_membership_levels . "` ADD  `confirmation` LONGTEXT NOT NULL AFTER  `description`
	";
	$wpdb->query($sqlQuery);

	pmpro_setOption("db_version", "1.4");
	return 1.4;
}
