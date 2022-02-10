<?php
/*
	These functions below handle DB upgrades, etc
*/
function pmpro_checkForUpgrades()
{
	$pmpro_db_version = pmpro_getOption("db_version");

	//if we can't find the DB tables, reset db_version to 0
	global $wpdb;
	$wpdb->hide_errors();
	$wpdb->pmpro_membership_levels = $wpdb->prefix . 'pmpro_membership_levels';
	$table_exists = $wpdb->query("SHOW TABLES LIKE '" . $wpdb->pmpro_membership_levels . "'");
	if(!$table_exists)
		$pmpro_db_version = 0;

	//default options
	if(!$pmpro_db_version) {
		require_once(PMPRO_DIR . "/includes/updates/upgrade_1.php");
		$pmpro_db_version = pmpro_upgrade_1();
	}

	//upgrading from early early versions of PMPro
	if($pmpro_db_version < 1.115) {
		require_once(PMPRO_DIR . "/includes/updates/upgrade_1_1_15.php");
		$pmpro_db_version = pmpro_upgrade_1_1_15();
	}

	//upgrading from early early versions of PMPro
	if($pmpro_db_version < 1.23) {
		require_once(PMPRO_DIR . "/includes/updates/upgrade_1_2_3.php");
		$pmpro_db_version = pmpro_upgrade_1_2_3();
	}

	//upgrading from early early versions of PMPro
	if($pmpro_db_version < 1.318) {
		require_once(PMPRO_DIR . "/includes/updates/upgrade_1_3_18.php");
		$pmpro_db_version = pmpro_upgrade_1_3_18();
	}

	//upgrading from early early versions of PMPro
	if($pmpro_db_version < 1.4) {
		require_once(PMPRO_DIR . "/includes/updates/upgrade_1_4.php");
		$pmpro_db_version = pmpro_upgrade_1_4();
	}

	//upgrading from early early versions of PMPro
	if($pmpro_db_version < 1.42) {
		require_once(PMPRO_DIR . "/includes/updates/upgrade_1_4_2.php");
		$pmpro_db_version = pmpro_upgrade_1_4_2();
	}

	//upgrading from early early versions of PMPro
	if($pmpro_db_version < 1.48) {
		require_once(PMPRO_DIR . "/includes/updates/upgrade_1_4_8.php");
		$pmpro_db_version = pmpro_upgrade_1_4_8();
	}

	//upgrading from early early versions of PMPro
	if($pmpro_db_version < 1.5) {
		require_once(PMPRO_DIR . "/includes/updates/upgrade_1_5.php");
		$pmpro_db_version = pmpro_upgrade_1_5();
	}

	//upgrading from early early versions of PMPro
	if($pmpro_db_version < 1.59) {
		require_once(PMPRO_DIR . "/includes/updates/upgrade_1_5_9.php");
		$pmpro_db_version = pmpro_upgrade_1_5_9();
	}

	//upgrading from early early versions of PMPro
	if($pmpro_db_version < 1.6) {
		require_once(PMPRO_DIR . "/includes/updates/upgrade_1_6.php");
		$pmpro_db_version = pmpro_upgrade_1_6();
	}

	//fix for fresh 1.7 installs
	if($pmpro_db_version == 1.7)
	{
		//check if we have an id column in the memberships_users table
		$wpdb->pmpro_memberships_users = $wpdb->prefix . 'pmpro_memberships_users';
		$col = $wpdb->get_var("SELECT id FROM $wpdb->pmpro_memberships_users LIMIT 1");
		if($wpdb->last_error == "Unknown column 'id' in 'field list'")
		{
			//redo 1.5 fix
			require_once(PMPRO_DIR . "/includes/updates/upgrade_1_5.php");
			pmpro_upgrade_1_5();
		}

		pmpro_db_delta();

		pmpro_setOption("db_version", "1.703");
		$pmpro_db_version = 1.703;
	}

	//updates from this point on should be like this if DB only
	if($pmpro_db_version < 1.71)
	{
		pmpro_db_delta();
		pmpro_setOption("db_version", "1.71");
		$pmpro_db_version = 1.71;
	}

	//schedule the credit card expiring cron
	if($pmpro_db_version < 1.72)
	{
		//schedule the credit card expiring cron
		pmpro_maybe_schedule_event(current_time('timestamp'), 'monthly', 'pmpro_cron_credit_card_expiring_warnings');

		pmpro_setOption("db_version", "1.72");
		$pmpro_db_version = 1.72;
	}

	//register capabilities required for menus now
	if($pmpro_db_version < 1.79)
	{
		//need to register caps for menu
		pmpro_activation();

		pmpro_setOption("db_version", "1.79");
		$pmpro_db_version = 1.79;
	}

	//set default filter_queries setting
	if($pmpro_db_version < 1.791)
	{
		if(!pmpro_getOption("showexcerpts"))
			pmpro_setOption("filterqueries", 1);
		else
			pmpro_SetOption("filterqueries", 0);

		pmpro_setOption("db_version", "1.791");
		$pmpro_db_version = 1.791;
	}

	//fix subscription ids on stripe orders
	require_once(PMPRO_DIR . "/includes/updates/upgrade_1_8_6_9.php");	//need to include this for AJAX calls
	if($pmpro_db_version < 1.869) {		
		$pmpro_db_version = pmpro_upgrade_1_8_6_9();
	}

	//Remove extra cron jobs inserted in version 1.8.7 and 1.8.7.1
	if($pmpro_db_version < 1.87) {
		require_once(PMPRO_DIR . "/includes/updates/upgrade_1_8_7.php");
		$pmpro_db_version = pmpro_upgrade_1_8_7();
	}
	
	/*
		v1.8.8
		* Running the cron job cleanup again.
		* Fixing old $0 Stripe orders.
		* Fixing old Authorize.net orders with empty status.
	*/	
	require_once(PMPRO_DIR . "/includes/updates/upgrade_1_8_8.php");
	if($pmpro_db_version < 1.88) {		
		$pmpro_db_version = pmpro_upgrade_1_8_8();			
	}
	
	/*
		v1.8.9.1
		* Fixing Stripe orders where user_id/membership_id = 0
		* Updated in v1.9.2.2 to check for namespace compatibility first,
		  since the Stripe class isn't loaded for PHP < 5.3.29
	*/	
	if (version_compare( PHP_VERSION, '5.3.29', '>=' )) {
		require_once(PMPRO_DIR . "/includes/updates/upgrade_1_8_9_1.php");
		if($pmpro_db_version < 1.891) {			
			$pmpro_db_version = pmpro_upgrade_1_8_9_1();
		}
	} elseif($pmpro_db_version < 1.891) {
		$pmpro_db_version = 1.891;		  //skipping this update because Stripe is not supported
	}

	/*
		v1.8.9.2 (db v1.9)
		* Changed 'code' column of pmpro_membership_orders table to 32 characters.
	*/
	if($pmpro_db_version < 1.892) {
		pmpro_db_delta();
		
		$pmpro_db_version = 1.892;
		pmpro_setOption("db_version", "1.892");
	}

	/*
		v1.8.9.3 (db v1.91)
		* Fixing incorrect start and end dates.	
	*/
	require_once(PMPRO_DIR . "/includes/updates/upgrade_1_8_9_3.php");
	if($pmpro_db_version < 1.91) {
		$pmpro_db_version = pmpro_upgrade_1_8_9_3();			
	}

	/*
		v1.8.10 (db v1.92)

		Added checkout_id column to pmpro_membership_orders
	*/
	if($pmpro_db_version < 1.92) {
		pmpro_db_delta();
		
		$pmpro_db_version = 1.92;
		pmpro_setOption("db_version", "1.92");
	}

	/*
		v1.8.10.2 (db v1.93)

		Run dbDelta again to fix broken/missing orders tables.
	*/
	if($pmpro_db_version < 1.93) {
		pmpro_db_delta();
		
		$pmpro_db_version = 1.93;
		pmpro_setOption("db_version", "1.93");
	}

	require_once( PMPRO_DIR . "/includes/updates/upgrade_1_9_4.php" );	
	if($pmpro_db_version < 1.94) {
		$pmpro_db_version = pmpro_upgrade_1_9_4();
	}
	
	if($pmpro_db_version < 1.944) {
		pmpro_cleanup_memberships_users_table();
		$pmpro_db_version = '1.944';
		pmpro_setOption('db_version', '1.944');
	}

	if ( $pmpro_db_version < 2.1 ) {
		pmpro_db_delta();

		$pmpro_db_version = 2.1;
		pmpro_setOption( 'db_version', '2.1' );
	}
	
	if ( $pmpro_db_version < 2.3 ) {
		pmpro_maybe_schedule_event( strtotime( '10:30:00' ) - ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS ), 'daily', 'pmpro_cron_admin_activity_email' );
		pmpro_setOption( 'db_version', '2.3' );
	}
	
	/**
	 * Version 2.4
	 * Fixing subscription_transaction_id
	 * for orders created through a Stripe Update.
	 */
	require_once( PMPRO_DIR . "/includes/updates/upgrade_2_4.php" );	
 	if($pmpro_db_version < 2.4) {
 		$pmpro_db_version = pmpro_upgrade_2_4();
 	}
	
	/**
	 * Version 2.5
	 * Running pmpro_db_delta to install the ordermeta table.
	 */
	if( $pmpro_db_version < 2.5 ) {
		pmpro_db_delta();
		$pmpro_db_version = 2.5;
		pmpro_setOption( 'db_version', '2.5' );
	}

	/**
	 * Version 2.6
	 * Running pmpro_db_delta to update column types to bigint/etc
	 */
	require_once( PMPRO_DIR . "/includes/updates/upgrade_2_6.php" );
	if( $pmpro_db_version < 2.6 ) {
		pmpro_db_delta();		
		$pmpro_db_version = pmpro_upgrade_2_6();
		pmpro_setOption( 'db_version', '2.6' );
	}
}

function pmpro_db_delta()
{
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

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
	$wpdb->pmpro_membership_levelmeta = $wpdb->prefix . 'pmpro_membership_levelmeta';
	$wpdb->pmpro_membership_ordermeta = $wpdb->prefix . 'pmpro_membership_ordermeta';

	//wp_pmpro_membership_levels
	$sqlQuery = "
		CREATE TABLE `" . $wpdb->pmpro_membership_levels . "` (
		  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
		  `name` varchar(255) NOT NULL,
		  `description` longtext NOT NULL,
		  `confirmation` longtext NOT NULL,
		  `initial_payment` decimal(18,8) NOT NULL DEFAULT '0.00',
		  `billing_amount` decimal(18,8) NOT NULL DEFAULT '0.00',
		  `cycle_number` int(11) NOT NULL DEFAULT '0',
		  `cycle_period` enum('Day','Week','Month','Year') DEFAULT 'Month',
		  `billing_limit` int(11) NOT NULL COMMENT 'After how many cycles should billing stop?',
		  `trial_amount` decimal(18,8) NOT NULL DEFAULT '0.00',
		  `trial_limit` int(11) NOT NULL DEFAULT '0',
		  `allow_signups` tinyint(4) NOT NULL DEFAULT '1',
		  `expiration_number` int(10) unsigned NOT NULL,
		  `expiration_period` enum('Hour','Day','Week','Month','Year') NOT NULL,
		  PRIMARY KEY  (`id`),
		  KEY `allow_signups` (`allow_signups`),
		  KEY `initial_payment` (`initial_payment`),
		  KEY `name` (`name`)
		);
	";
	dbDelta($sqlQuery);

	//wp_pmpro_membership_orders
	$sqlQuery = "
		CREATE TABLE `" . $wpdb->pmpro_membership_orders . "` (
		  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		  `code` varchar(32) NOT NULL,
		  `session_id` varchar(64) NOT NULL DEFAULT '',
		  `user_id` bigint(20) unsigned NOT NULL DEFAULT '0',
		  `membership_id` int(11) unsigned NOT NULL DEFAULT '0',
		  `paypal_token` varchar(64) NOT NULL DEFAULT '',
		  `billing_name` varchar(128) NOT NULL DEFAULT '',
		  `billing_street` varchar(128) NOT NULL DEFAULT '',
		  `billing_city` varchar(128) NOT NULL DEFAULT '',
		  `billing_state` varchar(32) NOT NULL DEFAULT '',
		  `billing_zip` varchar(16) NOT NULL DEFAULT '',
		  `billing_country` varchar(128) NOT NULL,
		  `billing_phone` varchar(32) NOT NULL,
		  `subtotal` varchar(16) NOT NULL DEFAULT '',
		  `tax` varchar(16) NOT NULL DEFAULT '',
		  `couponamount` varchar(16) NOT NULL DEFAULT '',
		  `checkout_id` bigint(20) NOT NULL DEFAULT '0',
		  `certificate_id` int(11) NOT NULL DEFAULT '0',
		  `certificateamount` varchar(16) NOT NULL DEFAULT '',
		  `total` varchar(16) NOT NULL DEFAULT '',
		  `payment_type` varchar(64) NOT NULL DEFAULT '',
		  `cardtype` varchar(32) NOT NULL DEFAULT '',
		  `accountnumber` varchar(32) NOT NULL DEFAULT '',
		  `expirationmonth` char(2) NOT NULL DEFAULT '',
		  `expirationyear` varchar(4) NOT NULL DEFAULT '',
		  `status` varchar(32) NOT NULL DEFAULT '',
		  `gateway` varchar(64) NOT NULL,
		  `gateway_environment` varchar(64) NOT NULL,
		  `payment_transaction_id` varchar(64) NOT NULL,
		  `subscription_transaction_id` varchar(32) NOT NULL,
		  `timestamp` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
		  `affiliate_id` varchar(32) NOT NULL,
		  `affiliate_subid` varchar(32) NOT NULL,
		  `notes` TEXT NOT NULL,
		  PRIMARY KEY  (`id`),
		  UNIQUE KEY `code` (`code`),
		  KEY `session_id` (`session_id`),
		  KEY `user_id` (`user_id`),
		  KEY `membership_id` (`membership_id`),
		  KEY `status` (`status`),
		  KEY `timestamp` (`timestamp`),
		  KEY `gateway` (`gateway`),
		  KEY `gateway_environment` (`gateway_environment`),
		  KEY `payment_transaction_id` (`payment_transaction_id`),
		  KEY `subscription_transaction_id` (`subscription_transaction_id`),
		  KEY `affiliate_id` (`affiliate_id`),
		  KEY `affiliate_subid` (`affiliate_subid`),
		  KEY `checkout_id` (`checkout_id`)
		);
	";
	dbDelta($sqlQuery);

	//wp_pmpro_memberships_categories
	$sqlQuery = "
		CREATE TABLE `" . $wpdb->pmpro_memberships_categories . "` (
		  `membership_id` int(11) unsigned NOT NULL,
		  `category_id` bigint(20) unsigned NOT NULL,
		  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		  UNIQUE KEY `membership_category` (`membership_id`,`category_id`),
		  UNIQUE KEY `category_membership` (`category_id`,`membership_id`)
		);
	";
	dbDelta($sqlQuery);

	//wp_pmpro_memberships_pages
	$sqlQuery = "
		CREATE TABLE `" . $wpdb->pmpro_memberships_pages . "` (
		  `membership_id` int(11) unsigned NOT NULL,
		  `page_id` bigint(20) unsigned NOT NULL,
		  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		  UNIQUE KEY `category_membership` (`page_id`,`membership_id`),
		  UNIQUE KEY `membership_page` (`membership_id`,`page_id`)
		);
	";
	dbDelta($sqlQuery);

	//wp_pmpro_memberships_users
	$sqlQuery = "
		CREATE TABLE `" . $wpdb->pmpro_memberships_users . "` (
		   `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		   `user_id` bigint(20) unsigned NOT NULL,
		   `membership_id` int(11) unsigned NOT NULL,
		   `code_id` bigint(20) unsigned NOT NULL,
		   `initial_payment` decimal(18,8) NOT NULL,
		   `billing_amount` decimal(18,8) NOT NULL,
		   `cycle_number` int(11) NOT NULL,
		   `cycle_period` enum('Day','Week','Month','Year') NOT NULL DEFAULT 'Month',
		   `billing_limit` int(11) NOT NULL,
		   `trial_amount` decimal(18,8) NOT NULL,
		   `trial_limit` int(11) NOT NULL,
		   `status` varchar(20) NOT NULL DEFAULT 'active',
		   `startdate` datetime NOT NULL,
		   `enddate` datetime DEFAULT NULL,
		   `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		   PRIMARY KEY  (`id`),
		   KEY `membership_id` (`membership_id`),
		   KEY `modified` (`modified`),
		   KEY `code_id` (`code_id`),
		   KEY `enddate` (`enddate`),
		   KEY `user_id` (`user_id`),
		   KEY `status` (`status`)
		);
	";
	dbDelta($sqlQuery);

	//wp_pmpro_discount_codes
	$sqlQuery = "		
		CREATE TABLE `" . $wpdb->pmpro_discount_codes . "` (
		  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		  `code` varchar(32) NOT NULL,
		  `starts` date NOT NULL,
		  `expires` date NOT NULL,
		  `uses` int(11) NOT NULL,
		  PRIMARY KEY  (`id`),
		  UNIQUE KEY `code` (`code`),
		  KEY `starts` (`starts`),
		  KEY `expires` (`expires`)
		);
	";
	dbDelta($sqlQuery);

	//wp_pmpro_discount_codes_levels
	$sqlQuery = "		
		CREATE TABLE `" . $wpdb->pmpro_discount_codes_levels . "` (
		  `code_id` bigint(20) unsigned NOT NULL,
		  `level_id` int(11) unsigned NOT NULL,
		  `initial_payment` decimal(18,8) NOT NULL DEFAULT '0.00',
		  `billing_amount` decimal(18,8) NOT NULL DEFAULT '0.00',
		  `cycle_number` int(11) NOT NULL DEFAULT '0',
		  `cycle_period` enum('Day','Week','Month','Year') DEFAULT 'Month',
		  `billing_limit` int(11) NOT NULL COMMENT 'After how many cycles should billing stop?',
		  `trial_amount` decimal(18,8) NOT NULL DEFAULT '0.00',
		  `trial_limit` int(11) NOT NULL DEFAULT '0',
		  `expiration_number` int(10) unsigned NOT NULL,
		  `expiration_period` enum('Hour','Day','Week','Month','Year') NOT NULL,
		  PRIMARY KEY  (`code_id`,`level_id`),
		  KEY `initial_payment` (`initial_payment`)
		);
	";
	dbDelta($sqlQuery);

	//wp_pmpro_discount_codes_uses
	$sqlQuery = "		
		CREATE TABLE `" . $wpdb->pmpro_discount_codes_uses . "` (		  
		  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		  `code_id` bigint(20) unsigned NOT NULL,
		  `user_id` bigint(20) unsigned NOT NULL,
		  `order_id` bigint(20) unsigned NOT NULL,
		  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		  PRIMARY KEY  (`id`),
		  KEY `user_id` (`user_id`),
		  KEY `timestamp` (`timestamp`)
		);
	";
	dbDelta($sqlQuery);

	//pmpro_membership_levelmeta
	$sqlQuery = "
		CREATE TABLE `" . $wpdb->pmpro_membership_levelmeta . "` (
		  `meta_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		  `pmpro_membership_level_id` int(11) unsigned NOT NULL,
		  `meta_key` varchar(255) NOT NULL,
		  `meta_value` longtext,
		  PRIMARY KEY (`meta_id`),
		  KEY `pmpro_membership_level_id` (`pmpro_membership_level_id`),
		  KEY `meta_key` (`meta_key`)
		);
	";
	dbDelta($sqlQuery);

	//pmpro_membership_ordermeta
	$sqlQuery = "
		CREATE TABLE `" . $wpdb->pmpro_membership_ordermeta . "` (
		  `meta_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		  `pmpro_membership_order_id` int(11) unsigned NOT NULL,
		  `meta_key` varchar(255) NOT NULL,
		  `meta_value` longtext,
		  PRIMARY KEY (`meta_id`),
		  KEY `pmpro_membership_order_id` (`pmpro_membership_order_id`),
		  KEY `meta_key` (`meta_key`)
		);
	";
	dbDelta($sqlQuery);
}
