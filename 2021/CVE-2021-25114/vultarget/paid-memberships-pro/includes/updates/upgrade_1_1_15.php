<?php
function pmpro_upgrade_1_1_15()
{
	/*
		DB table setup	
	*/
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

	/*
		Changing some id columns to unsigned.			
	*/
	$sqlQuery = "
		ALTER TABLE  `" . $wpdb->pmpro_membership_levels . "` CHANGE  `id`  `id` INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT
	";
	$wpdb->query($sqlQuery);

	$sqlQuery = "
		ALTER TABLE  `" . $wpdb->pmpro_memberships_categories . "` CHANGE  `membership_id`  `membership_id` INT( 11 ) UNSIGNED NOT NULL
	";
	$wpdb->query($sqlQuery);

	$sqlQuery = "
		ALTER TABLE  `" . $wpdb->pmpro_memberships_categories . "` CHANGE  `category_id`  `category_id` INT( 11 ) UNSIGNED NOT NULL
	";
	$wpdb->query($sqlQuery);

	$sqlQuery = "
		ALTER TABLE  `" . $wpdb->pmpro_memberships_pages . "` CHANGE  `membership_id`  `membership_id` INT( 11 ) UNSIGNED NOT NULL
	";
	$wpdb->query($sqlQuery);

	$sqlQuery = "
		ALTER TABLE  `" . $wpdb->pmpro_memberships_pages . "` CHANGE  `page_id`  `page_id` INT( 11 ) UNSIGNED NOT NULL
	";
	$wpdb->query($sqlQuery);

	$sqlQuery = "
		ALTER TABLE  `" . $wpdb->pmpro_memberships_users . "` CHANGE  `user_id`  `user_id`  INT( 11 ) UNSIGNED NOT NULL
	";
	$wpdb->query($sqlQuery);

	$sqlQuery = "
		ALTER TABLE  `" . $wpdb->pmpro_memberships_users . "` CHANGE  `membership_id`  `membership_id` INT( 11 ) UNSIGNED NOT NULL
	";
	$wpdb->query($sqlQuery);

	$sqlQuery = "
		ALTER TABLE  `" . $wpdb->pmpro_membership_orders . "` CHANGE  `id`  `id` INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT
	";
	$wpdb->query($sqlQuery);

	$sqlQuery = "
		ALTER TABLE  `" . $wpdb->pmpro_membership_orders . "` CHANGE  `user_id`  `user_id` INT( 11 ) UNSIGNED NOT NULL DEFAULT  '0'
	";
	$wpdb->query($sqlQuery);

	$sqlQuery = "
		ALTER TABLE  `" . $wpdb->pmpro_membership_orders . "` CHANGE  `membership_id`  `membership_id` INT( 11 ) UNSIGNED NOT NULL DEFAULT  '0'
	";
	$wpdb->query($sqlQuery);

	$sqlQuery = "
		ALTER TABLE  `" . $wpdb->pmpro_memberships_users . "` ADD  `code_id` INT UNSIGNED NOT NULL AFTER  `membership_id` ;
	";
	$wpdb->query($sqlQuery);

	$sqlQuery = "
		ALTER TABLE  `" . $wpdb->pmpro_memberships_users . "` ADD INDEX (  `code_id` )
	";
	$wpdb->query($sqlQuery);

	/*
		New tables for discount codes
	*/

	//wp_pmpro_discount_codes
	$sqlQuery = "		
		CREATE TABLE `" . $wpdb->pmpro_discount_codes . "` (
		  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
		  `code` varchar(32) NOT NULL,
		  `starts` date NOT NULL,
		  `expires` date NOT NULL,
		  `uses` int(11) NOT NULL,
		  PRIMARY KEY (`id`),
		  UNIQUE KEY `code` (`code`),
		  KEY `starts` (`starts`),
		  KEY `expires` (`expires`)
		);
	";
	$wpdb->query($sqlQuery);

	//wp_pmpro_discount_codes_levels
	$sqlQuery = "		
		CREATE TABLE `" . $wpdb->pmpro_discount_codes_levels . "` (
		  `code_id` int(11) unsigned NOT NULL,
		  `level_id` int(11) unsigned  NOT NULL,
		  `initial_payment` decimal(10,2) NOT NULL DEFAULT '0.00',
		  `billing_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
		  `cycle_number` int(11) NOT NULL DEFAULT '0',
		  `cycle_period` enum('Day','Week','Month','Year') DEFAULT 'Month',
		  `billing_limit` int(11) NOT NULL COMMENT 'After how many cycles should billing stop?',
		  `trial_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
		  `trial_limit` int(11) NOT NULL DEFAULT '0',
		  PRIMARY KEY (`code_id`,`level_id`),
		  KEY `initial_payment` (`initial_payment`)
		);
	";
	$wpdb->query($sqlQuery);

	//wp_pmpro_discount_codes_uses
	$sqlQuery = "		
		CREATE TABLE `" . $wpdb->pmpro_discount_codes_uses . "` (
		  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
		  `code_id` int(10) unsigned NOT NULL,
		  `user_id` int(10) unsigned NOT NULL,
		  `order_id` int(10) unsigned NOT NULL,
		  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		  PRIMARY KEY (`id`),
		  KEY `user_id` (`user_id`),
		  KEY `timestamp` (`timestamp`)
		);
	";
	$wpdb->query($sqlQuery);

	pmpro_setOption("db_version", "1.115");

	//do the next update
	return 1.115;
}
