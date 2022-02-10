<?php
function pmpro_upgrade_1_2_3()
{
	global $wpdb;
	$wpdb->hide_errors();
	$wpdb->pmpro_membership_levels = $wpdb->prefix . 'pmpro_membership_levels';
	$wpdb->pmpro_memberships_users = $wpdb->prefix . 'pmpro_memberships_users';
	$wpdb->pmpro_memberships_categories = $wpdb->prefix . 'pmpro_memberships_categories';
	$wpdb->pmpro_memberships_pages = $wpdb->prefix . 'pmpro_memberships_pages';
	$wpdb->pmpro_membership_orders = $wpdb->prefix . 'pmpro_membership_orders';
	$wpdb->pmpro_discount_codes = $wpdb->prefix . 'pmpro_discount_codes';
	$wpdb->pmpro_discount_codes_levels = $wpdb->prefix . 'pmpro_discount_codes_levels';
	$wpdb->pmpro_discount_codes_uses = $wpdb->prefix . 'pmpro_discount_codes_uses';

	//expiration number and period for levels
	$sqlQuery = "
		ALTER TABLE  `" . $wpdb->pmpro_membership_levels . "` ADD  `expiration_number` INT UNSIGNED NOT NULL ,
ADD  `expiration_period` ENUM(  'Day',  'Week',  'Month',  'Year' ) NOT NULL
	";
	$wpdb->query($sqlQuery);

	//expiration number and period for discount code levels
	$sqlQuery = "
		ALTER TABLE  `" . $wpdb->pmpro_discount_codes_levels . "` ADD  `expiration_number` INT UNSIGNED NOT NULL ,
ADD  `expiration_period` ENUM(  'Day',  'Week',  'Month',  'Year' ) NOT NULL
	";
	$wpdb->query($sqlQuery);

	//end date for members
	$sqlQuery = "
		ALTER TABLE  `" . $wpdb->pmpro_memberships_users . "` ADD  `enddate` DATETIME NULL AFTER  `startdate`
	";
	$wpdb->query($sqlQuery);

	$sqlQuery = "
		ALTER TABLE  `" . $wpdb->pmpro_memberships_users . "` ADD INDEX (  `enddate` )
	";
	$wpdb->query($sqlQuery);

	pmpro_setOption("db_version", "1.23");
	return 1.23;
}
