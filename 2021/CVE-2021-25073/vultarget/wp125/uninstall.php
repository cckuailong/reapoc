<?php

if (!defined('WP_UNINSTALL_PLUGIN'))
    exit();


function wp125_uninstall() {

	//Remove database tables
	global $wpdb;
	$table = $wpdb->prefix . "wp125_ads";
	$wpdb->query("DROP TABLE {$table}; ");

	//Remove options
	delete_option("wp125_num_slots");
	delete_option("wp125_ad_order");
	delete_option("wp125_buyad_url");
	delete_option("wp125_disable_default_style");
	delete_option("wp125_dofollow");
	delete_option("wp125_emailonexp");
	delete_option("wp125_daysbeforeexp");
	delete_option("wp125_defaultad");
	delete_option("wp125_db_version");

}

wp125_uninstall();